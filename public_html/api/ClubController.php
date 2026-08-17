<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_memberships':     getMemberships(); break;
    case 'add_membership':      addMembership(); break;
    case 'update_membership':   updateMembership(); break;
    case 'get_attendance':      getClubAttendance(); break;
    case 'mark_attendance':     markClubAttendance(); break;
    case 'generate_certificate': generateCert(); break;
    case 'get_expiring':        getExpiring(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getMemberships(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'view');
    $status = $_GET['status'] ?? 'active';
    $where = "1=1"; $params = [];
    if ($status) { $where .= " AND cm.status = ?"; $params[] = $status; }
    $members = dbFetchAll(
        "SELECT cm.*, c.first_name as child_name, c.last_name as child_last, c.date_of_birth,
                u.first_name as parent_name, u.last_name as parent_last, u.phone as parent_phone
         FROM club_memberships cm JOIN children c ON cm.child_id = c.id JOIN users u ON cm.parent_id = u.id
         WHERE $where ORDER BY cm.end_date ASC", $params);
    foreach ($members as &$m) { $m['formatted_price'] = formatNaira($m['plan_price']); $m['days_remaining'] = max(0, (int)((strtotime($m['end_date']) - time()) / 86400)); }
    jsonResponse(true, '', ['memberships' => $members]);
}

function addMembership(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'create'); validateCsrf();
    $childId = postInt('child_id'); $plan = post('plan');
    if (!$childId || !$plan) jsonResponse(false, 'Child and plan required.');
    $child = dbFetchOne("SELECT * FROM children WHERE id = ?", [$childId]);
    if (!$child) jsonResponse(false, 'Child not found.');
    $prices = ['trial' => (float)getSetting('club_plan_trial','8000'), 'monthly' => (float)getSetting('club_plan_monthly','30000'),
        'quarterly' => (float)getSetting('club_plan_quarterly','85000'), 'biannual' => (float)getSetting('club_plan_biannual','165000')];
    $price = $prices[$plan] ?? $prices['trial'];
    $start = date('Y-m-d');
    $end = match($plan) { 'trial' => date('Y-m-d', strtotime('+1 week')), 'monthly' => date('Y-m-d', strtotime('+1 month')),
        'quarterly' => date('Y-m-d', strtotime('+3 months')), 'biannual' => date('Y-m-d', strtotime('+6 months')), default => date('Y-m-d', strtotime('+1 week'))};
    $id = dbInsert('club_memberships', ['child_id' => $childId, 'parent_id' => $child['parent_id'], 'plan' => $plan,
        'plan_price' => $price, 'start_date' => $start, 'end_date' => $end, 'status' => 'active', 'location_id' => 1]);
    jsonResponse(true, 'Membership created.', ['id' => $id, 'price' => $price]);
}

function updateMembership(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'edit'); validateCsrf();
    $id = postInt('id'); $status = post('status');
    if (!$id) jsonResponse(false, 'Membership ID required.');
    $data = [];
    if ($status) $data['status'] = $status;
    if (post('end_date')) $data['end_date'] = post('end_date');
    if (!empty($data)) dbUpdate('club_memberships', $data, 'id = ?', [$id]);
    jsonResponse(true, 'Membership updated.');
}

function getClubAttendance(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'view');
    $date = $_GET['date'] ?? date('Y-m-d', strtotime('next Saturday'));
    $members = dbFetchAll(
        "SELECT cm.id as membership_id, c.id as child_id, c.first_name, c.last_name,
                ca.status as att_status, ca.checked_in_at, ca.checked_out_at
         FROM club_memberships cm JOIN children c ON cm.child_id = c.id
         LEFT JOIN club_attendance ca ON ca.membership_id = cm.id AND ca.attendance_date = ?
         WHERE cm.status = 'active' AND cm.end_date >= ? ORDER BY c.first_name", [$date, $date]);
    $stats = dbFetchOne("SELECT COUNT(*) as total, SUM(CASE WHEN ca.status = 'present' THEN 1 ELSE 0 END) as present
         FROM club_memberships cm LEFT JOIN club_attendance ca ON ca.membership_id = cm.id AND ca.attendance_date = ?
         WHERE cm.status = 'active' AND cm.end_date >= ?", [$date, $date]);
    jsonResponse(true, '', ['members' => $members, 'date' => $date, 'stats' => $stats]);
}

function markClubAttendance(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'edit'); validateCsrf();
    $date = post('date') ?: date('Y-m-d'); $entries = json_decode($_POST['entries'] ?? '[]', true);
    $count = 0;
    foreach ($entries as $e) {
        $membershipId = (int)($e['membership_id'] ?? 0); $childId = (int)($e['child_id'] ?? 0);
        $status = $e['status'] ?? 'present';
        if (!$membershipId || !$childId) continue;
        $existing = dbFetchOne("SELECT id FROM club_attendance WHERE membership_id = ? AND attendance_date = ?", [$membershipId, $date]);
        if ($existing) { dbUpdate('club_attendance', ['status' => $status], 'id = ?', [$existing['id']]); }
        else { dbInsert('club_attendance', ['membership_id' => $membershipId, 'child_id' => $childId, 'attendance_date' => $date,
            'status' => $status, 'checked_in_at' => $e['check_in'] ?? null, 'checked_out_at' => $e['check_out'] ?? null, 'marked_by' => $user['id']]); }
        $count++;
    }
    jsonResponse(true, "{$count} attendance records saved.");
}

function generateCert(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'edit'); validateCsrf();
    require_once __DIR__ . '/../includes/pdf.php';
    $membershipId = postInt('membership_id'); if (!$membershipId) jsonResponse(false, 'Membership ID required.');
    $membership = dbFetchOne("SELECT * FROM club_memberships WHERE id = ?", [$membershipId]);
    $child = dbFetchOne("SELECT * FROM children WHERE id = ?", [$membership['child_id'] ?? 0]);
    if (!$membership || !$child) jsonResponse(false, 'Not found.');
    $result = generateClubCertificatePDF($child, $membership);
    if ($result['success']) { dbUpdate('club_memberships', ['certificate_pdf' => $result['path']], 'id = ?', [$membershipId]); }
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function getExpiring(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'view');
    $cutoff = date('Y-m-d', strtotime('+7 days'));
    $expiring = dbFetchAll(
        "SELECT cm.*, c.first_name, c.last_name, u.phone as parent_phone
         FROM club_memberships cm JOIN children c ON cm.child_id = c.id JOIN users u ON cm.parent_id = u.id
         WHERE cm.status = 'active' AND cm.end_date <= ? ORDER BY cm.end_date", [$cutoff]);
    jsonResponse(true, '', ['expiring' => $expiring]);
}
