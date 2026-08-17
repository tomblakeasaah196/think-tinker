<?php
/**
 * api/SessionController.php
 * Session scheduling, calendar, notes (voice-to-text), rescheduling.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../includes/upload.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_calendar':        getCalendar(); break;
    case 'get_sessions':        getSessions(); break;
    case 'get_session':         getSession(); break;
    case 'add_session':         addSession(); break;
    case 'bulk_schedule':       bulkSchedule(); break;
    case 'update_session':      updateSession(); break;
    case 'reschedule':          rescheduleSession(); break;
    case 'cancel_session':      cancelSession(); break;
    case 'complete_session':    completeSession(); break;
    case 'get_notes':           getNotes(); break;
    case 'get_session_note':    getSessionNote(); break;
    case 'add_note':            addNote(); break;
    case 'update_note':         updateNote(); break;
    case 'submit_note':         submitNote(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getCalendar(): void {
    $user = requireAuth();
    $childId = (int)($_GET['child_id'] ?? 0);
    $month = (int)($_GET['month'] ?? date('n'));
    $year = (int)($_GET['year'] ?? date('Y'));

    $startDate = sprintf('%04d-%02d-01', $year, $month);
    $endDate = date('Y-m-t', strtotime($startDate));

    $where = "s.session_date BETWEEN ? AND ?";
    $params = [$startDate, $endDate];

    if ($user['user_type'] === 'parent') {
        $childIds = array_column(dbFetchAll("SELECT id FROM children WHERE parent_id = ?", [$user['id']]), 'id');
        if (empty($childIds)) { jsonResponse(true, '', ['sessions' => []]); return; }
        if ($childId && in_array($childId, $childIds)) {
            $where .= " AND s.child_id = ?";
            $params[] = $childId;
        } else {
            $where .= " AND s.child_id IN (" . implode(',', array_map('intval', $childIds)) . ")";
        }
    } elseif ($user['user_type'] === 'tutor') {
        $where .= " AND s.tutor_id = ?";
        $params[] = $user['id'];
    } elseif ($childId) {
        $where .= " AND s.child_id = ?";
        $params[] = $childId;
    }

    $sessions = dbFetchAll(
        "SELECT s.*, c.first_name as child_name, c.last_name as child_last, u.first_name as tutor_name,
                (SELECT COUNT(*) FROM session_notes sn WHERE sn.session_id = s.id AND sn.status = 'submitted') as has_note
         FROM sessions s JOIN children c ON s.child_id = c.id JOIN users u ON s.tutor_id = u.id
         WHERE $where ORDER BY s.session_date, s.start_time", $params
    );

    jsonResponse(true, '', ['sessions' => $sessions, 'month' => $month, 'year' => $year]);
}

function getSessions(): void {
    $user = requireAuth();
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = 20;
    $status = $_GET['status'] ?? '';
    $tutorId = (int)($_GET['tutor_id'] ?? 0);
    $childId = (int)($_GET['child_id'] ?? 0);

    $where = "1=1"; $params = [];

    if ($user['user_type'] === 'tutor') { $where .= " AND s.tutor_id = ?"; $params[] = $user['id']; }
    if ($status) { $where .= " AND s.status = ?"; $params[] = $status; }
    if ($tutorId) { $where .= " AND s.tutor_id = ?"; $params[] = $tutorId; }
    if ($childId) { $where .= " AND s.child_id = ?"; $params[] = $childId; }

    $total = dbFetchColumn("SELECT COUNT(*) FROM sessions s WHERE $where", $params);
    $sessions = dbFetchAll(
        "SELECT s.*, c.first_name as child_name, c.last_name as child_last, u.first_name as tutor_name
         FROM sessions s JOIN children c ON s.child_id = c.id JOIN users u ON s.tutor_id = u.id
         WHERE $where ORDER BY s.session_date DESC LIMIT ? OFFSET ?",
        array_merge($params, [$perPage, ($page-1)*$perPage])
    );
    jsonResponse(true, '', ['sessions' => $sessions, 'total' => (int)$total, 'page' => $page]);
}

function getSession(): void {
    $user = requireAuth();
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonResponse(false, 'Session ID required.');
    $session = dbFetchOne(
        "SELECT s.*, c.first_name as child_name, c.last_name as child_last, c.parent_id,
                u.first_name as tutor_name, u.last_name as tutor_last
         FROM sessions s JOIN children c ON s.child_id = c.id JOIN users u ON s.tutor_id = u.id WHERE s.id = ?", [$id]);
    if (!$session) jsonResponse(false, 'Session not found.');
    $note = dbFetchOne("SELECT * FROM session_notes WHERE session_id = ? ORDER BY created_at DESC LIMIT 1", [$id]);
    jsonResponse(true, '', ['session' => $session, 'note' => $note]);
}

function addSession(): void {
    $user = requireAuth(); requirePermission($user, 'sessions', 'create'); validateCsrf();
    $childId = postInt('child_id'); $tutorId = postInt('tutor_id'); $date = post('session_date');
    if (!$childId || !$tutorId || !$date) jsonResponse(false, 'Child, tutor, and date are required.');
    $id = dbInsert('sessions', [
        'child_id' => $childId, 'tutor_id' => $tutorId, 'session_date' => $date,
        'start_time' => post('start_time') ?: null, 'end_time' => post('end_time') ?: null,
        'status' => 'scheduled', 'location_id' => 1,
    ]);
    jsonResponse(true, 'Session scheduled.', ['id' => $id]);
}

function bulkSchedule(): void {
    $user = requireAuth(); requirePermission($user, 'sessions', 'create'); validateCsrf();
    $childId = postInt('child_id'); $tutorId = postInt('tutor_id');
    $days = $_POST['days'] ?? []; // ['mon','wed','fri']
    $startDate = post('start_date'); $endDate = post('end_date');
    $startTime = post('start_time'); $endTime = post('end_time');
    if (!$childId || !$tutorId || empty($days) || !$startDate || !$endDate) jsonResponse(false, 'All fields required.');
    $dayMap = ['mon'=>1,'tue'=>2,'wed'=>3,'thu'=>4,'fri'=>5,'sat'=>6,'sun'=>7];
    $targetDays = array_map(fn($d) => $dayMap[strtolower($d)] ?? 0, $days);
    $current = new DateTime($startDate); $end = new DateTime($endDate); $count = 0;
    while ($current <= $end) {
        if (in_array((int)$current->format('N'), $targetDays)) {
            dbInsert('sessions', [
                'child_id' => $childId, 'tutor_id' => $tutorId, 'session_date' => $current->format('Y-m-d'),
                'start_time' => $startTime ?: null, 'end_time' => $endTime ?: null,
                'status' => 'scheduled', 'location_id' => 1,
            ]);
            $count++;
        }
        $current->modify('+1 day');
    }
    jsonResponse(true, "{$count} sessions scheduled.", ['count' => $count]);
}

function updateSession(): void {
    $user = requireAuth(); requirePermission($user, 'sessions', 'edit'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Session ID required.');
    $data = [];
    if (post('session_date')) $data['session_date'] = post('session_date');
    if (post('start_time')) $data['start_time'] = post('start_time');
    if (post('end_time')) $data['end_time'] = post('end_time');
    if (post('status')) $data['status'] = post('status');
    if (postInt('tutor_id')) $data['tutor_id'] = postInt('tutor_id');
    if (!empty($data)) dbUpdate('sessions', $data, 'id = ?', [$id]);
    jsonResponse(true, 'Session updated.');
}

function rescheduleSession(): void {
    $user = requireAuth(); requirePermission($user, 'sessions', 'edit'); validateCsrf();
    $id = postInt('id'); $newDate = post('new_date'); $reason = post('reason');
    if (!$id || !$newDate) jsonResponse(false, 'Session ID and new date required.');
    $session = dbFetchOne("SELECT * FROM sessions WHERE id = ?", [$id]);
    if (!$session) jsonResponse(false, 'Session not found.');
    dbUpdate('sessions', [
        'session_date' => $newDate, 'status' => 'rescheduled',
        'rescheduled_from' => $session['session_date'], 'rescheduled_reason' => $reason,
    ], 'id = ?', [$id]);
    jsonResponse(true, 'Session rescheduled.');
}

function cancelSession(): void {
    $user = requireAuth(); requirePermission($user, 'sessions', 'delete'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Session ID required.');
    dbUpdate('sessions', ['status' => 'cancelled'], 'id = ?', [$id]);
    jsonResponse(true, 'Session cancelled.');
}

function completeSession(): void {
    $user = requireAuth(); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Session ID required.');
    dbUpdate('sessions', ['status' => 'completed'], 'id = ?', [$id]);
    jsonResponse(true, 'Session marked as completed.');
}

function getNotes(): void {
    $user = requireAuth();
    $childId = (int)($_GET['child_id'] ?? 0);
    $where = "sn.status = 'submitted'"; $params = [];
    if ($childId) { $where .= " AND s.child_id = ?"; $params[] = $childId; }
    if ($user['user_type'] === 'parent') {
        $childIds = array_column(dbFetchAll("SELECT id FROM children WHERE parent_id = ?", [$user['id']]), 'id');
        if (empty($childIds)) { jsonResponse(true, '', ['notes' => []]); return; }
        $where .= " AND s.child_id IN (" . implode(',', array_map('intval', $childIds)) . ")";
    } elseif ($user['user_type'] === 'tutor') {
        $where .= " AND sn.tutor_id = ?"; $params[] = $user['id'];
    }
    $notes = dbFetchAll(
        "SELECT sn.*, s.session_date, c.first_name as child_name, u.first_name as tutor_name
         FROM session_notes sn JOIN sessions s ON sn.session_id = s.id
         JOIN children c ON s.child_id = c.id JOIN users u ON sn.tutor_id = u.id
         WHERE $where ORDER BY sn.submitted_at DESC LIMIT 50", $params);
    jsonResponse(true, '', ['notes' => $notes]);
}

function getSessionNote(): void {
    $user = requireAuth();
    $id = (int)($_GET['id'] ?? 0);
    $note = dbFetchOne(
        "SELECT sn.*, s.session_date, c.first_name as child_name, u.first_name as tutor_name
         FROM session_notes sn JOIN sessions s ON sn.session_id = s.id
         JOIN children c ON s.child_id = c.id JOIN users u ON sn.tutor_id = u.id WHERE sn.id = ?", [$id]);
    if (!$note) jsonResponse(false, 'Note not found.');
    jsonResponse(true, '', ['note' => $note]);
}

function addNote(): void {
    $user = requireAuth(); validateCsrf();
    $sessionId = postInt('session_id');
    if (!$sessionId) jsonResponse(false, 'Session ID required.');
    $images = null;
    if (!empty($_FILES['images'])) {
        $uploaded = [];
        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                $_FILES['session_img'] = [
                    'name' => $_FILES['images']['name'][$i], 'tmp_name' => $tmp,
                    'size' => $_FILES['images']['size'][$i], 'error' => 0,
                    'type' => $_FILES['images']['type'][$i],
                ];
                $r = uploadSessionImage('session_img');
                if ($r['success']) $uploaded[] = $r['path'];
            }
        }
        if ($uploaded) $images = json_encode($uploaded);
    }
    $id = dbInsert('session_notes', [
        'session_id' => $sessionId, 'tutor_id' => $user['id'],
        'note_text' => $_POST['note_text'] ?? '', 'topics_covered' => post('topics_covered'),
        'input_method' => in_array(post('input_method'), ['voice','typed']) ? post('input_method') : 'typed',
        'images' => $images, 'status' => 'draft',
    ]);
    jsonResponse(true, 'Note saved as draft.', ['id' => $id]);
}

function updateNote(): void {
    $user = requireAuth(); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Note ID required.');
    $data = [];
    if (isset($_POST['note_text'])) $data['note_text'] = $_POST['note_text'];
    if (post('topics_covered')) $data['topics_covered'] = post('topics_covered');
    if (!empty($data)) dbUpdate('session_notes', $data, 'id = ? AND tutor_id = ?', [$id, $user['id']]);
    jsonResponse(true, 'Note updated.');
}

function submitNote(): void {
    $user = requireAuth(); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Note ID required.');
    dbUpdate('session_notes', ['status' => 'submitted', 'submitted_at' => date('Y-m-d H:i:s')],
        'id = ? AND tutor_id = ?', [$id, $user['id']]);
    // Mark session as completed
    $note = dbFetchOne("SELECT session_id FROM session_notes WHERE id = ?", [$id]);
    if ($note) dbUpdate('sessions', ['status' => 'completed'], 'id = ?', [$note['session_id']]);
    // Notify parent
    $session = dbFetchOne(
        "SELECT s.*, c.first_name as child_name, c.parent_id FROM sessions s JOIN children c ON s.child_id = c.id WHERE s.id = ?",
        [$note['session_id'] ?? 0]);
    if ($session) {
        createNotification($session['parent_id'], "New session note for {$session['child_name']}",
            'Your child\'s tutor has submitted a session note. View it in your portal.',
            APP_URL . '/parent/notes.php');
    }
    jsonResponse(true, 'Note submitted and published to parent.');
}
