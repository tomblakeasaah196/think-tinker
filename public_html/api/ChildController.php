<?php
/**
 * api/ChildController.php
 * Child profile CRUD, progress tracking, service management.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../includes/upload.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_children':         getChildren(); break;
    case 'get_child':            getChild(); break;
    case 'add_child':            addChild(); break;
    case 'update_child':         updateChild(); break;
    case 'delete_child':         deleteChild(); break;
    case 'get_progress':         getProgress(); break;
    case 'update_progress':      updateProgressAction(); break;
    case 'add_service':          addService(); break;
    case 'update_service':       updateService(); break;
    default:
        jsonResponse(false, 'Invalid action.');
}

function getChildren(): void
{
    $user = requireAuth();
    $parentId = (int) ($_GET['parent_id'] ?? 0);

    // Parents can only see their own children
    if ($user['user_type'] === 'parent') {
        $parentId = $user['id'];
    } elseif (!$parentId) {
        requirePermission($user, 'clients', 'view');
        // Return all children for admin
        $children = dbFetchAll(
            "SELECT c.*, u.first_name as parent_first, u.last_name as parent_last 
             FROM `children` c JOIN `users` u ON c.parent_id = u.id 
             WHERE c.deleted_at IS NULL ORDER BY c.first_name"
        );
        jsonResponse(true, '', ['children' => $children]);
        return;
    }

    $children = dbFetchAll(
        "SELECT c.*, 
                (SELECT cs.service_type FROM child_services cs WHERE cs.child_id = c.id AND cs.status = 'active' LIMIT 1) as active_service
         FROM `children` c WHERE c.parent_id = ? AND c.deleted_at IS NULL ORDER BY c.first_name",
        [$parentId]
    );

    foreach ($children as &$child) {
        $child['age'] = floor((time() - strtotime($child['date_of_birth'])) / (365.25 * 86400));
        $child['photo_url'] = $child['photo'] ? getUploadUrl($child['photo']) : null;
    }

    jsonResponse(true, '', ['children' => $children]);
}

function getChild(): void
{
    $user = requireAuth();
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) jsonResponse(false, 'Child ID required.');

    $child = dbFetchOne("SELECT * FROM `children` WHERE `id` = ? AND `deleted_at` IS NULL", [$id]);
    if (!$child) jsonResponse(false, 'Child not found.');

    // Parents can only see their own children
    if ($user['user_type'] === 'parent' && $child['parent_id'] !== $user['id']) {
        jsonResponse(false, 'Access denied.', [], 403);
    }

    // Tutors can only see assigned children
    if ($user['user_type'] === 'tutor') {
        $assigned = dbFetchOne(
            "SELECT id FROM child_services WHERE child_id = ? AND assigned_tutor_id = ? AND status = 'active'",
            [$id, $user['id']]
        );
        if (!$assigned) jsonResponse(false, 'Access denied.', [], 403);
    }

    $child['age'] = floor((time() - strtotime($child['date_of_birth'])) / (365.25 * 86400));
    $child['photo_url'] = $child['photo'] ? getUploadUrl($child['photo']) : null;

    // Active services
    $services = dbFetchAll(
        "SELECT cs.*, u.first_name as tutor_name 
         FROM `child_services` cs 
         LEFT JOIN `users` u ON cs.assigned_tutor_id = u.id 
         WHERE cs.child_id = ? ORDER BY cs.status ASC, cs.start_date DESC",
        [$id]
    );

    // Club membership — include pending_payment too so an unpaid
    // registration shows an honest "payment pending" state instead of
    // silently disappearing from the child's page.
    $clubMembership = dbFetchOne(
        "SELECT * FROM `club_memberships` WHERE `child_id` = ? AND `status` IN ('active','pending_payment')
         ORDER BY FIELD(`status`,'active','pending_payment') ASC, `end_date` DESC LIMIT 1",
        [$id]
    );

    jsonResponse(true, '', [
        'child'           => $child,
        'services'        => $services,
        'club_membership' => $clubMembership,
    ]);
}

function addChild(): void
{
    $user = requireAuth();
    validateCsrf();

    $parentId = postInt('parent_id');
    if ($user['user_type'] === 'parent') {
        $parentId = $user['id'];
    } elseif (!$parentId) {
        jsonResponse(false, 'Parent ID required.');
    }

    if (!post('first_name') || !post('date_of_birth')) {
        jsonResponse(false, 'Child name and date of birth are required.');
    }

    $data = [
        'parent_id'      => $parentId,
        'first_name'     => post('first_name'),
        'last_name'      => post('last_name'),
        'date_of_birth'  => post('date_of_birth'),
        'gender'         => in_array(post('gender'), ['male', 'female']) ? post('gender') : 'male',
        'current_school' => post('current_school'),
        'grade_level'    => post('grade_level'),
        'medical_notes'  => post('medical_notes'),
        'status'         => 'active',
        'location_id'    => 1,
    ];

    if (!empty($_FILES['photo']['name'])) {
        $upload = uploadProfilePhoto('photo', 'children');
        if ($upload['success']) $data['photo'] = $upload['path'];
    }

    $childId = dbInsert('children', $data);
    jsonResponse(true, 'Child profile created.', ['child_id' => $childId]);
}

function updateChild(): void
{
    $user = requireAuth();
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Child ID required.');

    $child = dbFetchOne("SELECT * FROM `children` WHERE `id` = ? AND `deleted_at` IS NULL", [$id]);
    if (!$child) jsonResponse(false, 'Child not found.');

    if ($user['user_type'] === 'parent' && $child['parent_id'] !== $user['id']) {
        jsonResponse(false, 'Access denied.', [], 403);
    }

    $data = [];
    if (post('first_name'))     $data['first_name'] = post('first_name');
    if (post('last_name'))      $data['last_name'] = post('last_name');
    if (post('date_of_birth'))  $data['date_of_birth'] = post('date_of_birth');
    if (post('gender'))         $data['gender'] = post('gender');
    if (post('current_school')) $data['current_school'] = post('current_school');
    if (post('grade_level'))    $data['grade_level'] = post('grade_level');
    if (isset($_POST['medical_notes'])) $data['medical_notes'] = post('medical_notes');

    if (!empty($_FILES['photo']['name'])) {
        $upload = uploadProfilePhoto('photo', 'children');
        if ($upload['success']) $data['photo'] = $upload['path'];
    }

    if (!empty($data)) {
        dbUpdate('children', $data, 'id = ?', [$id]);
    }

    jsonResponse(true, 'Child profile updated.');
}

function deleteChild(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'delete');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Child ID required.');

    dbSoftDelete('children', $id);
    jsonResponse(true, 'Child profile removed.');
}

function getProgress(): void
{
    $user = requireAuth();
    $childId = (int) ($_GET['child_id'] ?? 0);
    if (!$childId) jsonResponse(false, 'Child ID required.');

    $records = dbFetchAll(
        "SELECT pr.*, u.first_name as updated_by_name 
         FROM `progress_records` pr 
         JOIN `users` u ON pr.updated_by = u.id 
         WHERE pr.child_id = ? ORDER BY pr.subject, pr.topic",
        [$childId]
    );

    // Group by subject
    $grouped = [];
    foreach ($records as $r) {
        $grouped[$r['subject']][] = $r;
    }

    jsonResponse(true, '', ['progress' => $grouped]);
}

function updateProgressAction(): void
{
    $user = requireAuth();
    validateCsrf();

    $childId = postInt('child_id');
    $subject = post('subject');
    $topic   = post('topic');
    $status  = post('status');

    if (!$childId || !$subject || !$topic || !$status) {
        jsonResponse(false, 'All fields are required.');
    }

    if (!in_array($status, ['not_started', 'in_progress', 'mastered'])) {
        jsonResponse(false, 'Invalid status.');
    }

    // Check if record exists
    $existing = dbFetchOne(
        "SELECT id FROM `progress_records` WHERE `child_id` = ? AND `subject` = ? AND `topic` = ?",
        [$childId, $subject, $topic]
    );

    if ($existing) {
        dbUpdate('progress_records', [
            'status'     => $status,
            'notes'      => post('notes'),
            'updated_by' => $user['id'],
        ], 'id = ?', [$existing['id']]);
    } else {
        dbInsert('progress_records', [
            'child_id'   => $childId,
            'subject'    => $subject,
            'topic'      => $topic,
            'status'     => $status,
            'notes'      => post('notes'),
            'updated_by' => $user['id'],
        ]);
    }

    jsonResponse(true, 'Progress updated.');
}

function addService(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'edit');
    validateCsrf();

    $childId = postInt('child_id');
    if (!$childId) jsonResponse(false, 'Child ID required.');

    $id = dbInsert('child_services', [
        'child_id'          => $childId,
        'service_type'      => post('service_type'),
        'frequency'         => post('frequency'),
        'assigned_tutor_id' => postInt('tutor_id') ?: null,
        'session_rate'      => postFloat('session_rate') ?: null,
        'start_date'        => post('start_date') ?: date('Y-m-d'),
        'status'            => 'active',
        'notes'             => post('notes'),
    ]);

    jsonResponse(true, 'Service added.', ['id' => $id]);
}

function updateService(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'edit');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Service ID required.');

    $data = [];
    if (post('service_type'))  $data['service_type'] = post('service_type');
    if (post('frequency'))     $data['frequency'] = post('frequency');
    if (post('status'))        $data['status'] = post('status');
    if (post('notes'))         $data['notes'] = post('notes');
    if (isset($_POST['tutor_id']))     $data['assigned_tutor_id'] = postInt('tutor_id') ?: null;
    if (isset($_POST['session_rate'])) $data['session_rate'] = postFloat('session_rate') ?: null;
    if (post('end_date'))      $data['end_date'] = post('end_date');

    if (!empty($data)) {
        dbUpdate('child_services', $data, 'id = ?', [$id]);
    }

    jsonResponse(true, 'Service updated.');
}
