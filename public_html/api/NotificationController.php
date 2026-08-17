<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_notifications':   getNotifications(); break;
    case 'get_unread_count':    getUnreadCount(); break;
    case 'mark_read':           markNotifRead(); break;
    case 'mark_all_read':       markAllRead(); break;
    case 'delete_notification': deleteNotif(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getNotifications(): void {
    $user = requireAuth();
    $limit = min(50, (int)($_GET['limit'] ?? 20));
    $notifications = getRecentNotifications($user['id'], $limit);
    foreach ($notifications as &$n) { $n['time_ago'] = timeAgo($n['created_at']); }
    jsonResponse(true, '', ['notifications' => $notifications, 'unread' => getUnreadNotificationCount($user['id'])]);
}

function getUnreadCount(): void {
    $user = requireAuth();
    jsonResponse(true, '', ['count' => getUnreadNotificationCount($user['id'])]);
}

function markNotifRead(): void {
    $user = requireAuth();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Notification ID required.');
    dbUpdate('notifications', ['is_read' => 1], 'id = ? AND user_id = ?', [$id, $user['id']]);
    jsonResponse(true, 'Marked as read.');
}

function markAllRead(): void {
    $user = requireAuth();
    dbExecute("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$user['id']]);
    jsonResponse(true, 'All notifications marked as read.');
}

function deleteNotif(): void {
    $user = requireAuth();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Notification ID required.');
    dbExecute("DELETE FROM notifications WHERE id = ? AND user_id = ?", [$id, $user['id']]);
    jsonResponse(true, 'Notification deleted.');
}
