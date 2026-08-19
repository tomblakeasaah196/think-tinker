<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

// Columns shared by every staff-detail query below. Deliberately NOT
// `u.*` — that would also pull password_hash, activation_token, and
// reset_token straight into a JSON response the browser can read. Also
// deliberately not `sr.*` alongside it — both `users` and `staff_records`
// have their own `id` column, and `SELECT u.*, sr.*` lets sr.id silently
// overwrite u.id in the result array, so anything keyed off that "id"
// (e.g. an edit form's hidden id field) would quietly act on the wrong
// row. Listing columns explicitly avoids both problems at once.
// (Declared before the switch below — a top-level `const` is NOT hoisted
// the way `function` declarations are, so it must exist before anything
// that references it actually runs.)
const STAFF_DETAIL_COLUMNS = "u.id, u.first_name, u.last_name, u.email, u.phone, u.user_type,
         u.profile_photo, u.is_active, u.location_id,
         sr.staff_id_number, sr.job_title, sr.employment_type, sr.salary, sr.bank_name, sr.bank_account,
         sr.guarantor_name, sr.guarantor_phone, sr.guarantor_address, sr.hire_date, sr.end_date, sr.id_badge_pdf";

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_staff':           getStaff(); break;
    case 'get_staff_member':    getStaffMember(); break;
    case 'get_my_staff_info':   getMyStaffInfo(); break;
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
        "SELECT " . STAFF_DETAIL_COLUMNS . "
         FROM users u LEFT JOIN staff_records sr ON u.id = sr.user_id
         WHERE u.user_type IN ('tutor','staff','admin','super_admin') AND u.deleted_at IS NULL ORDER BY u.first_name");
    jsonResponse(true, '', ['staff' => $staff]);
}

function getStaffMember(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'view');
    $id = (int)($_GET['id'] ?? 0); if (!$id) jsonResponse(false, 'Staff ID required.');
    $member = dbFetchOne("SELECT " . STAFF_DETAIL_COLUMNS . " FROM users u LEFT JOIN staff_records sr ON u.id = sr.user_id WHERE u.id = ?", [$id]);
    if (!$member) jsonResponse(false, 'Staff not found.');
    $assigned = dbFetchAll("SELECT cs.*, c.first_name, c.last_name FROM child_services cs JOIN children c ON cs.child_id = c.id WHERE cs.assigned_tutor_id = ? AND cs.status = 'active'", [$id]);
    $recentAttendance = dbFetchAll("SELECT * FROM staff_attendance WHERE user_id = ? ORDER BY attendance_date DESC LIMIT 30", [$id]);
    jsonResponse(true, '', ['member' => $member, 'assigned_students' => $assigned, 'attendance' => $recentAttendance]);
}

/**
 * A staff member's OWN employment info — job title, staff ID, hire date.
 * Deliberately not gated behind requirePermission('staff','view'): that
 * permission controls seeing OTHER people's records in Hub > Staff, but
 * everyone should be able to see what's on file for themselves (used by
 * the read-only "Employment" panel on Hub > My Profile). Always scoped to
 * the logged-in user's own id — never accepts an id from the request.
 */
function getMyStaffInfo(): void {
    $user = requireAuth();
    $record = dbFetchOne(
        "SELECT staff_id_number, job_title, employment_type, hire_date FROM staff_records WHERE user_id = ?",
        [$user['id']]
    );
    jsonResponse(true, '', ['staff_record' => $record]);
}

function addStaff(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'create'); validateCsrf();
    require_once __DIR__ . '/../includes/mailer.php';
    $tempPassword = bin2hex(random_bytes(4));
    $userType = post('user_type') ?: 'tutor';
    if (!in_array($userType, ['tutor','staff','admin','super_admin'])) $userType = 'tutor';
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
    if (isset($_POST['guarantor_name'])) $data['guarantor_name'] = post('guarantor_name');
    if (isset($_POST['guarantor_phone'])) $data['guarantor_phone'] = post('guarantor_phone');
    if (isset($_POST['guarantor_address'])) $data['guarantor_address'] = post('guarantor_address');
    if (!empty($data)) dbUpdate('staff_records', $data, 'user_id = ?', [$id]);

    $userData = [];
    if (post('first_name')) $userData['first_name'] = post('first_name');
    if (post('last_name')) $userData['last_name'] = post('last_name');
    if (post('phone')) $userData['phone'] = post('phone');

    // Role change — same allowed set as Add Staff, including super_admin
    // (this is the one place, other than direct database access, that a
    // full-permission account can be granted after the fact).
    if (post('user_type')) {
        $userType = post('user_type');
        if (!in_array($userType, ['tutor','staff','admin','super_admin'])) {
            jsonResponse(false, 'Invalid role.');
        }
        $userData['user_type'] = $userType;
    }

    // Email change — `users.email` is UNIQUE, so check for a collision
    // with a DIFFERENT user first rather than letting it fail as a raw,
    // unhandled database error.
    if (post('email')) {
        $email = strtolower(trim(post('email')));
        if (!isValidEmail($email)) jsonResponse(false, 'Please enter a valid email address.');
        $existing = dbFetchOne("SELECT id FROM users WHERE LOWER(email) = ? AND id != ?", [$email, $id]);
        if ($existing) jsonResponse(false, 'Another account already uses this email address.');
        $userData['email'] = $email;
    }

    if (!empty($userData)) dbUpdate('users', $userData, 'id = ?', [$id]);
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
    $member = dbFetchOne("SELECT " . STAFF_DETAIL_COLUMNS . " FROM users u JOIN staff_records sr ON u.id = sr.user_id WHERE u.id = ?", [$id]);
    if (!$member) jsonResponse(false, 'Staff not found.');
    $result = generateStaffBadgePDF($member, $member);
    if ($result['success']) { dbUpdate('staff_records', ['id_badge_pdf' => $result['path']], 'user_id = ?', [$id]); }
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}
