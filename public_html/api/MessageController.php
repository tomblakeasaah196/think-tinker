<?php
/**
 * api/MessageController.php
 * WhatsApp-style messaging: parent ↔ admin, tutor loop-in.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_conversations':   getConversations(); break;
    case 'get_messages':        getMessages(); break;
    case 'send_message':        sendMessage(); break;
    case 'mark_read':           markRead(); break;
    case 'assign_to_tutor':     assignToTutor(); break;
    case 'get_new_messages':    getNewMessages(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getConversations(): void {
    $user = requireAuth();
    if ($user['user_type'] === 'parent') {
        $convos = dbFetchAll(
            "SELECT m.conversation_id, MAX(m.created_at) as last_activity,
                    (SELECT message_text FROM messages WHERE conversation_id = m.conversation_id ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT display_as FROM messages WHERE conversation_id = m.conversation_id ORDER BY created_at DESC LIMIT 1) as last_sender,
                    (SELECT sender_type FROM messages WHERE conversation_id = m.conversation_id ORDER BY created_at DESC LIMIT 1) as last_sender_type,
                    SUM(CASE WHEN m.sender_type != 'parent' AND m.is_read = 0 THEN 1 ELSE 0 END) as unread
             FROM messages m WHERE m.conversation_id IN (SELECT DISTINCT conversation_id FROM messages WHERE sender_id = ?)
             GROUP BY m.conversation_id ORDER BY last_activity DESC", [$user['id']]);
    } elseif ($user['user_type'] === 'tutor') {
        $convos = dbFetchAll(
            "SELECT m.conversation_id, MAX(m.created_at) as last_activity,
                    (SELECT message_text FROM messages WHERE conversation_id = m.conversation_id ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT display_as FROM messages WHERE conversation_id = m.conversation_id AND sender_type = 'parent' LIMIT 1) as parent_name,
                    SUM(CASE WHEN m.is_read = 0 AND m.assigned_to = ? THEN 1 ELSE 0 END) as unread
             FROM messages m WHERE m.assigned_to = ? GROUP BY m.conversation_id ORDER BY last_activity DESC",
            [$user['id'], $user['id']]);
    } else {
        requirePermission($user, 'messages', 'view');
        $convos = dbFetchAll(
            "SELECT m.conversation_id, MAX(m.created_at) as last_activity,
                    (SELECT message_text FROM messages WHERE conversation_id = m.conversation_id ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT display_as FROM messages WHERE conversation_id = m.conversation_id AND sender_type = 'parent' LIMIT 1) as parent_name,
                    (SELECT first_name FROM users u JOIN messages m2 ON u.id = m2.sender_id WHERE m2.conversation_id = m.conversation_id AND m2.sender_type = 'parent' LIMIT 1) as sender_name,
                    (SELECT sender_type FROM messages WHERE conversation_id = m.conversation_id ORDER BY created_at DESC LIMIT 1) as last_sender_type,
                    SUM(CASE WHEN m.sender_type = 'parent' AND m.is_read = 0 THEN 1 ELSE 0 END) as unread,
                    m.child_id, (SELECT first_name FROM children WHERE id = m.child_id LIMIT 1) as child_name
             FROM messages m GROUP BY m.conversation_id ORDER BY last_activity DESC");
    }
    foreach ($convos as &$c) {
        $c['time_ago'] = timeAgo($c['last_activity']);
        $preview = decodeMessagePayload((string) ($c['last_message'] ?? ''));
        $c['last_message'] = $preview['preview'];
        $c['last_is_attachment'] = $preview['attachment'] ? 1 : 0;
    }
    jsonResponse(true, '', ['conversations' => $convos]);
}

function getMessages(): void {
    $user = requireAuth();
    $conversationId = $_GET['conversation_id'] ?? '';
    if (!$conversationId) jsonResponse(false, 'Conversation ID required.');
    $messages = dbFetchAll("SELECT * FROM messages WHERE conversation_id = ? ORDER BY created_at ASC", [$conversationId]);
    // Mark as read
    if ($user['user_type'] === 'parent') {
        dbExecute("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_type != 'parent'", [$conversationId]);
    } else {
        dbExecute("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_type = 'parent'", [$conversationId]);
    }
    foreach ($messages as &$m) {
        decorateMessage($m, $user);
    }
    jsonResponse(true, '', ['messages' => $messages]);
}

function sendMessage(): void {
    $user = requireAuth(); validateCsrf();
    $text = trim($_POST['message_text'] ?? '');
    $attachment = null;
    if (!empty($_FILES['attachment']['name'])) {
        require_once __DIR__ . '/../includes/upload.php';
        $up = uploadMessageAttachment('attachment');
        if (!$up['success']) {
            jsonResponse(false, $up['message']);
        }
        $attachment = [
            'path' => $up['path'],
            'name' => $up['filename'] ?? basename($up['path']),
            'mime' => $up['mime'] ?? '',
            'url'  => getUploadUrl($up['path']),
        ];
    }
    if ($text === '' && !$attachment) jsonResponse(false, 'Message cannot be empty.');
    $conversationId = post('conversation_id');
    // New conversation if none provided
    if (!$conversationId) {
        $conversationId = bin2hex(random_bytes(18));
    }
    $senderType = match($user['user_type']) {
        'parent' => 'parent', 'tutor' => 'tutor', default => 'admin',
    };
    $displayAs = ($senderType === 'parent') ? $user['first_name'].' '.$user['last_name'] : getSetting('business_name', 'Think & Tinker');
    $stored = encodeMessagePayload(sanitize($text), $attachment);
    $id = dbInsert('messages', [
        'conversation_id' => $conversationId, 'sender_id' => $user['id'],
        'sender_type' => $senderType, 'display_as' => $displayAs,
        'message_text' => $stored, 'child_id' => postInt('child_id') ?: null,
    ]);
    // Notify
    if ($senderType === 'parent') {
        $admins = dbFetchAll("SELECT id FROM users WHERE user_type IN ('super_admin','admin') AND is_active = 1");
        foreach ($admins as $a) { createNotification($a['id'], 'New message from '.$displayAs, substr($text,0,100), HUB_URL.'/messages.php'); }
    } else {
        // Find parent in conversation
        $parentMsg = dbFetchOne("SELECT sender_id FROM messages WHERE conversation_id = ? AND sender_type = 'parent' LIMIT 1", [$conversationId]);
        if ($parentMsg) {
            createNotification($parentMsg['sender_id'], 'New message from Think & Tinker', substr($text,0,100), APP_URL.'/parent/messages.php');
        }
    }
    jsonResponse(true, 'Message sent.', ['id' => $id, 'conversation_id' => $conversationId]);
}

function markRead(): void {
    $user = requireAuth();
    $conversationId = post('conversation_id');
    if (!$conversationId) jsonResponse(false, 'Conversation ID required.');
    if ($user['user_type'] === 'parent') {
        dbExecute("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_type != 'parent'", [$conversationId]);
    } else {
        dbExecute("UPDATE messages SET is_read = 1 WHERE conversation_id = ? AND sender_type = 'parent'", [$conversationId]);
    }
    jsonResponse(true, 'Marked as read.');
}

function assignToTutor(): void {
    $user = requireAuth(); requirePermission($user, 'messages', 'assign'); validateCsrf();
    $conversationId = post('conversation_id'); $tutorId = postInt('tutor_id');
    if (!$conversationId || !$tutorId) jsonResponse(false, 'Conversation and tutor ID required.');
    dbExecute("UPDATE messages SET assigned_to = ? WHERE conversation_id = ?", [$tutorId, $conversationId]);
    createNotification($tutorId, 'Message assigned to you', 'Please respond to a parent message.', HUB_URL.'/messages.php');
    jsonResponse(true, 'Conversation assigned to tutor.');
}

function getNewMessages(): void {
    $user = requireAuth();
    $conversationId = $_GET['conversation_id'] ?? '';
    $after = $_GET['after'] ?? '';
    if (!$conversationId) jsonResponse(false, 'Conversation ID required.');
    $where = "conversation_id = ?"; $params = [$conversationId];
    if ($after) { $where .= " AND created_at > ?"; $params[] = $after; }
    $messages = dbFetchAll("SELECT * FROM messages WHERE $where ORDER BY created_at ASC", $params);
    foreach ($messages as &$m) {
        decorateMessage($m, $user);
    }
    jsonResponse(true, '', ['messages' => $messages]);
}

const MSG_ATT_PREFIX = '__TTATT__';

function encodeMessagePayload(string $text, ?array $attachment): string
{
    if (!$attachment) {
        return $text;
    }
    return MSG_ATT_PREFIX . json_encode([
        't'   => $text,
        'att' => $attachment,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function decodeMessagePayload(string $raw): array
{
    $empty = ['text' => $raw, 'attachment' => null, 'preview' => $raw];
    if ($raw === '' || !str_starts_with($raw, MSG_ATT_PREFIX)) {
        return $empty;
    }
    $decoded = json_decode(substr($raw, strlen(MSG_ATT_PREFIX)), true);
    if (!is_array($decoded)) {
        return $empty;
    }
    $text = (string) ($decoded['t'] ?? '');
    $att  = is_array($decoded['att'] ?? null) ? $decoded['att'] : null;
    if ($att && empty($att['url']) && !empty($att['path'])) {
        require_once __DIR__ . '/../includes/upload.php';
        $att['url'] = getUploadUrl($att['path']);
    }
    $preview = $text !== '' ? $text : (($att && str_starts_with((string) ($att['mime'] ?? ''), 'image/')) ? 'Photo' : 'Attachment');
    return ['text' => $text, 'attachment' => $att, 'preview' => $preview];
}

function decorateMessage(array &$m, array $user): void
{
    $payload = decodeMessagePayload((string) ($m['message_text'] ?? ''));
    $m['message_text'] = $payload['text'];
    $m['attachment'] = $payload['attachment'];
    $m['is_mine'] = ((int) $m['sender_id'] === (int) $user['id']);
    $m['time'] = date('g:i A', strtotime($m['created_at']));
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $day = date('Y-m-d', strtotime($m['created_at']));
    if ($day === $today) {
        $m['date'] = 'Today';
    } elseif ($day === $yesterday) {
        $m['date'] = 'Yesterday';
    } else {
        $m['date'] = formatDate($m['created_at'], 'D, M j');
    }
}
