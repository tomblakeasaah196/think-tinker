<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_staff':           getStaff(); break;
    case 'get_staff_member':    getStaffMember(); break;
    case 'add_staff':           addStaff(); break;
    case 'update_staff':        updateStaff(); break;
    case 'deactivate_staff':    deactivateStaff(); break;
    case 'get_attendance':      getAttendance(); break;
    case 'mark_attendance':     markAttendance(); break;
    case 'get_tutors':          getTutors(); break;
    case 'generate_badge':      generateBadge(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getStaff(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'view');
    $staff = dbFetchAll(
        "SELECT u.*, sr.staff_id_number, sr.job_title, sr.employment_type, sr.salary, sr.hire_date
         FROM users u LEFT JOIN staff_records sr ON u.id = sr.user_id
         WHERE u.user_type IN ('tutor','staff','admin','super_admin') AND u.deleted_at IS NULL ORDER BY u.first_name");
    jsonResponse(true, '', ['staff' => $staff]);
}

function getStaffMember(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'view');
    $id = (int)($_GET['id'] ?? 0); if (!$id) jsonResponse(false, 'Staff ID required.');
    $member = dbFetchOne("SELECT u.*, sr.* FROM users u LEFT JOIN staff_records sr ON u.id = sr.user_id WHERE u.id = ?", [$id]);
    if (!$member) jsonResponse(false, 'Staff not found.');
    $assigned = dbFetchAll("SELECT cs.*, c.first_name, c.last_name FROM child_services cs JOIN children c ON cs.child_id = c.id WHERE cs.assigned_tutor_id = ? AND cs.status = 'active'", [$id]);
    $recentAttendance = dbFetchAll("SELECT * FROM staff_attendance WHERE user_id = ? ORDER BY attendance_date DESC LIMIT 30", [$id]);
    jsonResponse(true, '', ['member' => $member, 'assigned_students' => $assigned, 'attendance' => $recentAttendance]);
}

function addStaff(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'create'); validateCsrf();
    require_once __DIR__ . '/../includes/mailer.php';
    $tempPassword = bin2hex(random_bytes(4));
    $userType = post('user_type') ?: 'tutor';
    if (!in_array($userType, ['tutor','staff','admin'])) $userType = 'tutor';
    $result = createUser(['first_name' => post('first_name'), 'last_name' => post('last_name'),
        'email' => post('email'), 'phone' => post('phone'), 'password' => $tempPassword, 'user_type' => $userType]);
    if (!$result['success']) jsonResponse(false, $result['message']);
    $userId = $result['user_id'];
    dbExecute("UPDATE users SET email_verified_at = NOW() WHERE id = ?", [$userId]);
    $staffIdNumber = date('Ym') . str_pad($userId, 3, '0', STR_PAD_LEFT);
    dbInsert('staff_records', [
        'user_id' => $userId, 'staff_id_number' => $staffIdNumber, 'job_title' => post('job_title') ?: ucfirst($userType),
        'employment_type' => post('employment_type') ?: 'contract', 'salary' => postFloat('salary') ?: null,
        'bank_name' => post('bank_name'), 'bank_account' => post('bank_account'),
        'guarantor_name' => post('guarantor_name'), 'guarantor_phone' => post('guarantor_phone'),
        'guarantor_address' => post('guarantor_address'), 'hire_date' => post('hire_date') ?: date('Y-m-d'), 'location_id' => 1,
    ]);
    $staffUser = dbFetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    sendNotificationEmail($staffUser['email'], $staffUser['first_name'], 'Your Think & Tinker Staff Account',
        "Your staff account has been created. Log in at ".HUB_URL." with your email and temporary password: <strong>{$tempPassword}</strong>",
        HUB_URL, 'Log In');
    jsonResponse(true, 'Staff member added. Activation email sent.', ['user_id' => $userId, 'staff_id' => $staffIdNumber]);
}

function updateStaff(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'edit'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Staff ID required.');
    $data = [];
    if (post('job_title')) $data['job_title'] = post('job_title');
    if (post('employment_type')) $data['employment_type'] = post('employment_type');
    if (isset($_POST['salary'])) $data['salary'] = postFloat('salary') ?: null;
    if (post('bank_name')) $data['bank_name'] = post('bank_name');
    if (post('bank_account')) $data['bank_account'] = post('bank_account');
    if (!empty($data)) dbUpdate('staff_records', $data, 'user_id = ?', [$id]);
    if (post('first_name') || post('last_name') || post('phone')) {
        $userData = [];
        if (post('first_name')) $userData['first_name'] = post('first_name');
        if (post('last_name')) $userData['last_name'] = post('last_name');
        if (post('phone')) $userData['phone'] = post('phone');
        if (!empty($userData)) dbUpdate('users', $userData, 'id = ?', [$id]);
    }
    jsonResponse(true, 'Staff record updated.');
}

function deactivateStaff(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'delete'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Staff ID required.');
    dbUpdate('users', ['is_active' => 0], 'id = ?', [$id]);
    jsonResponse(true, 'Staff member deactivated.');
}

function getAttendance(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'view');
    $date = $_GET['date'] ?? date('Y-m-d');
    $records = dbFetchAll(
        "SELECT sa.*, u.first_name, u.last_name FROM staff_attendance sa JOIN users u ON sa.user_id = u.id WHERE sa.attendance_date = ?", [$date]);
    $allStaff = dbFetchAll("SELECT u.id, u.first_name, u.last_name, sr.job_title FROM users u JOIN staff_records sr ON u.id = sr.user_id WHERE u.is_active = 1 AND u.deleted_at IS NULL");
    jsonResponse(true, '', ['records' => $records, 'staff' => $allStaff, 'date' => $date]);
}

function markAttendance(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'edit'); validateCsrf();
    $entries = json_decode($_POST['entries'] ?? '[]', true);
    $date = post('date') ?: date('Y-m-d');
    $count = 0;
    foreach ($entries as $entry) {
        $userId = (int)($entry['user_id'] ?? 0); $status = $entry['status'] ?? 'present';
        if (!$userId) continue;
        $existing = dbFetchOne("SELECT id FROM staff_attendance WHERE user_id = ? AND attendance_date = ?", [$userId, $date]);
        if ($existing) { dbUpdate('staff_attendance', ['status' => $status, 'clock_in' => $entry['clock_in'] ?? null, 'clock_out' => $entry['clock_out'] ?? null], 'id = ?', [$existing['id']]); }
        else { dbInsert('staff_attendance', ['user_id' => $userId, 'attendance_date' => $date, 'status' => $status, 'clock_in' => $entry['clock_in'] ?? null, 'clock_out' => $entry['clock_out'] ?? null]); }
        $count++;
    }
    jsonResponse(true, "{$count} attendance record(s) saved.");
}

function getTutors(): void {
    $user = requireAuth();
    $tutors = dbFetchAll("SELECT id, first_name, last_name FROM users WHERE user_type = 'tutor' AND is_active = 1 AND deleted_at IS NULL ORDER BY first_name");
    jsonResponse(true, '', ['tutors' => $tutors]);
}

function generateBadge(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'edit'); validateCsrf();
    require_once __DIR__ . '/../includes/pdf.php';
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Staff ID required.');
    $member = dbFetchOne("SELECT u.*, sr.* FROM users u JOIN staff_records sr ON u.id = sr.user_id WHERE u.id = ?", [$id]);
    if (!$member) jsonResponse(false, 'Staff not found.');
    $result = generateStaffBadgePDF($member, $member);
    if ($result['success']) { dbUpdate('staff_records', ['id_badge_pdf' => $result['path']], 'user_id = ?', [$id]); }
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}
