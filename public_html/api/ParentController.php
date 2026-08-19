<?php
/**
 * api/ParentController.php
 * Parent profile CRUD, onboarding, dashboard data.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    // === PARENT PORTAL ===
    case 'get_dashboard':        getParentDashboard(); break;
    case 'get_profile':          getProfile(); break;
    case 'update_profile':       updateProfile(); break;
    case 'get_documents':        getDocuments(); break;
    case 'get_payment_history':  getPaymentHistory(); break;

    // === HUB: CLIENT MANAGEMENT ===
    case 'get_clients':          getClients(); break;
    case 'get_client':           getClient(); break;
    case 'add_client':           addClient(); break;
    case 'update_client':        updateClient(); break;
    case 'deactivate_client':    deactivateClient(); break;
    case 'activate_client':      activateClient(); break;

    default:
        jsonResponse(false, 'Invalid action.');
}

// ============================================================
// PARENT PORTAL — DASHBOARD
// ============================================================

function getParentDashboard(): void
{
    $user = requireAuth();
    requireUserType($user, 'parent');

    $parentId = $user['id'];
    $today = date('Y-m-d');

    // Get children
    $children = dbFetchAll(
        "SELECT * FROM `children` WHERE `parent_id` = ? AND `deleted_at` IS NULL ORDER BY `first_name`",
        [$parentId]
    );

    // Selected child (default first)
    $childId = (int) ($_GET['child_id'] ?? ($children[0]['id'] ?? 0));

    // Upcoming sessions
    $upcomingSessions = [];
    if ($childId) {
        $upcomingSessions = dbFetchAll(
            "SELECT s.*, u.first_name as tutor_name 
             FROM `sessions` s 
             JOIN `users` u ON s.tutor_id = u.id 
             WHERE s.child_id = ? AND s.session_date >= ? AND s.status = 'scheduled' 
             ORDER BY s.session_date, s.start_time LIMIT 5",
            [$childId, $today]
        );
    }

    // Latest session note
    $latestNote = null;
    if ($childId) {
        $latestNote = dbFetchOne(
            "SELECT sn.*, s.session_date, u.first_name as tutor_name 
             FROM `session_notes` sn 
             JOIN `sessions` s ON sn.session_id = s.id 
             JOIN `users` u ON sn.tutor_id = u.id 
             WHERE s.child_id = ? AND sn.status = 'submitted' 
             ORDER BY sn.submitted_at DESC LIMIT 1",
            [$childId]
        );
    }

    // Progress snapshot
    $progress = ['mastered' => 0, 'in_progress' => 0, 'not_started' => 0];
    if ($childId) {
        $progressRows = dbFetchAll(
            "SELECT `status`, COUNT(*) as cnt FROM `progress_records` WHERE `child_id` = ? GROUP BY `status`",
            [$childId]
        );
        foreach ($progressRows as $row) {
            $progress[$row['status']] = (int) $row['cnt'];
        }
    }

    // Club status — include pending_payment too (not just active) so an
    // unpaid registration shows an honest "payment pending" card instead of
    // silently disappearing from the dashboard.
    $clubMembership = null;
    if ($childId) {
        $clubMembership = dbFetchOne(
            "SELECT * FROM `club_memberships` WHERE `child_id` = ? AND `status` IN ('active','pending_payment')
             ORDER BY FIELD(`status`,'active','pending_payment') ASC, `end_date` DESC LIMIT 1",
            [$childId]
        );
    }

    // Unread messages
    $unreadMessages = dbCount('messages',
        "conversation_id IN (SELECT DISTINCT conversation_id FROM messages WHERE sender_id = ?) AND sender_type != 'parent' AND is_read = 0",
        [$parentId]
    );

    // Pending invoices
    $pendingInvoices = dbFetchAll(
        "SELECT * FROM `invoices` WHERE `parent_id` = ? AND `status` IN ('sent', 'overdue') ORDER BY `due_date`",
        [$parentId]
    );

    jsonResponse(true, '', [
        'children'          => $children,
        'selected_child_id' => $childId,
        'upcoming_sessions' => $upcomingSessions,
        'latest_note'       => $latestNote,
        'progress'          => $progress,
        'club_membership'   => $clubMembership,
        'unread_messages'   => $unreadMessages,
        'pending_invoices'  => $pendingInvoices,
        'notifications'     => getUnreadNotificationCount($parentId),
    ]);
}

// ============================================================
// PARENT PORTAL — PROFILE
// ============================================================

function getProfile(): void
{
    $user = requireAuth();

    jsonResponse(true, '', [
        'id'         => $user['id'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'email'      => $user['email'],
        'phone'      => $user['phone'],
        'photo'      => $user['profile_photo'] ? getUploadUrl($user['profile_photo']) : null,
    ]);
}

function updateProfile(): void
{
    $user = requireAuth();
    validateCsrf();

    $data = [
        'first_name' => post('first_name') ?: $user['first_name'],
        'last_name'  => post('last_name') ?: $user['last_name'],
        'phone'      => post('phone') ?: $user['phone'],
    ];

    // Handle photo upload — a failure here used to be swallowed silently:
    // the field was just skipped and the response still said "Profile
    // updated." with no indication the photo never saved. Now name/phone
    // still save, but a real photo failure is reported instead of hidden.
    $photoError = '';
    if (!empty($_FILES['profile_photo']['name'])) {
        require_once __DIR__ . '/../includes/upload.php';
        $result = uploadProfilePhoto('profile_photo');
        if ($result['success']) {
            $data['profile_photo'] = $result['path'];
        } else {
            $photoError = $result['message'];
        }
    }

    dbUpdate('users', $data, 'id = ?', [$user['id']]);
    $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];

    if ($photoError) {
        jsonResponse(false, "Name and phone saved, but the photo didn't upload: {$photoError}");
    }

    jsonResponse(true, 'Profile updated.');
}

// ============================================================
// PARENT PORTAL — DOCUMENTS
// ============================================================

function getDocuments(): void
{
    $user = requireAuth();
    requireUserType($user, 'parent');

    $parentId = $user['id'];

    $documents = [];

    // Contracts
    $contracts = dbFetchAll(
        "SELECT 'contract' as doc_type, contract_number as doc_number, pdf_path, status, created_at 
         FROM `contracts` WHERE `parent_id` = ? AND `pdf_path` IS NOT NULL ORDER BY `created_at` DESC",
        [$parentId]
    );
    $documents = array_merge($documents, $contracts);

    // Invoices
    $invoices = dbFetchAll(
        "SELECT 'invoice' as doc_type, invoice_number as doc_number, pdf_path, status, created_at 
         FROM `invoices` WHERE `parent_id` = ? AND `pdf_path` IS NOT NULL ORDER BY `created_at` DESC",
        [$parentId]
    );
    $documents = array_merge($documents, $invoices);

    // Receipts
    $receipts = dbFetchAll(
        "SELECT 'receipt' as doc_type, r.receipt_number as doc_number, r.pdf_path, 'paid' as status, r.created_at 
         FROM `receipts` r JOIN `invoices` i ON r.invoice_id = i.id 
         WHERE i.parent_id = ? AND r.pdf_path IS NOT NULL ORDER BY r.created_at DESC",
        [$parentId]
    );
    $documents = array_merge($documents, $receipts);

    // Certificates
    $certs = dbFetchAll(
        "SELECT 'certificate' as doc_type, CONCAT('CERT-', cm.id) as doc_number, cm.certificate_pdf as pdf_path, cm.status, cm.created_at 
         FROM `club_memberships` cm WHERE cm.parent_id = ? AND cm.certificate_pdf IS NOT NULL ORDER BY cm.created_at DESC",
        [$parentId]
    );
    $documents = array_merge($documents, $certs);

    // Sort by date descending
    usort($documents, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

    // Add URLs
    foreach ($documents as &$doc) {
        $doc['url'] = $doc['pdf_path'] ? getUploadUrl($doc['pdf_path']) : null;
        $doc['date'] = formatDate($doc['created_at']);
    }

    jsonResponse(true, '', ['documents' => $documents]);
}

// ============================================================
// PARENT PORTAL — PAYMENT HISTORY
// ============================================================

function getPaymentHistory(): void
{
    $user = requireAuth();
    requireUserType($user, 'parent');

    $invoices = dbFetchAll(
        "SELECT i.*, c.first_name as child_name 
         FROM `invoices` i 
         LEFT JOIN `children` c ON i.child_id = c.id 
         WHERE i.parent_id = ? 
         ORDER BY i.created_at DESC",
        [$user['id']]
    );

    foreach ($invoices as &$inv) {
        $inv['formatted_total'] = formatNaira($inv['total']);
        $inv['formatted_date'] = formatDate($inv['created_at']);
        $inv['formatted_due'] = formatDate($inv['due_date']);
        $inv['pdf_url'] = $inv['pdf_path'] ? getUploadUrl($inv['pdf_path']) : null;

        // Get receipt if paid
        if ($inv['status'] === 'paid') {
            $receipt = dbFetchOne("SELECT * FROM `receipts` WHERE `invoice_id` = ?", [$inv['id']]);
            $inv['receipt'] = $receipt;
            $inv['receipt_url'] = $receipt && $receipt['pdf_path'] ? getUploadUrl($receipt['pdf_path']) : null;
        }
    }

    // Bank accounts for pending payments
    $bankAccounts = getActiveBankAccounts();

    jsonResponse(true, '', [
        'invoices'      => $invoices,
        'bank_accounts' => $bankAccounts,
    ]);
}

// ============================================================
// HUB — CLIENT MANAGEMENT
// ============================================================

function getClients(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'view');

    $search = get('search');
    $status = get('status');
    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = 20;
    $offset = ($page - 1) * $perPage;

    $where = "u.user_type = 'parent' AND u.deleted_at IS NULL";
    $params = [];

    if ($search) {
        $where .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
        $like = "%{$search}%";
        $params = array_merge($params, [$like, $like, $like, $like]);
    }

    if ($status === 'active') {
        $where .= " AND u.is_active = 1";
    } elseif ($status === 'inactive') {
        $where .= " AND u.is_active = 0";
    }

    $total = dbFetchColumn("SELECT COUNT(*) FROM `users` u WHERE $where", $params);

    $clients = dbFetchAll(
        "SELECT u.*, 
                (SELECT COUNT(*) FROM `children` c WHERE c.parent_id = u.id AND c.deleted_at IS NULL) as child_count,
                (SELECT GROUP_CONCAT(DISTINCT cs.service_type) FROM `child_services` cs 
                 JOIN `children` c2 ON cs.child_id = c2.id WHERE c2.parent_id = u.id AND cs.status = 'active') as services
         FROM `users` u WHERE $where 
         ORDER BY u.created_at DESC LIMIT ? OFFSET ?",
        array_merge($params, [$perPage, $offset])
    );

    foreach ($clients as &$c) {
        $c['name'] = $c['first_name'] . ' ' . $c['last_name'];
        $c['last_activity'] = $c['last_login_at'] ? timeAgo($c['last_login_at']) : 'Never';
    }

    jsonResponse(true, '', [
        'clients'  => $clients,
        'total'    => (int) $total,
        'page'     => $page,
        'pages'    => ceil($total / $perPage),
    ]);
}

function getClient(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'view');

    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) jsonResponse(false, 'Client ID required.');

    $client = dbFetchOne("SELECT * FROM `users` WHERE `id` = ? AND `user_type` = 'parent'", [$id]);
    if (!$client) jsonResponse(false, 'Client not found.');

    $children = dbFetchAll(
        "SELECT c.*, cs.service_type, cs.frequency, cs.session_rate, cs.status as service_status,
                u.first_name as tutor_first_name, u.last_name as tutor_last_name
         FROM `children` c
         LEFT JOIN `child_services` cs ON c.id = cs.child_id AND cs.status = 'active'
         LEFT JOIN `users` u ON cs.assigned_tutor_id = u.id
         WHERE c.parent_id = ? AND c.deleted_at IS NULL ORDER BY c.first_name",
        [$id]
    );

    $invoices = dbFetchAll(
        "SELECT * FROM `invoices` WHERE `parent_id` = ? ORDER BY `created_at` DESC LIMIT 10",
        [$id]
    );

    $contracts = dbFetchAll(
        "SELECT * FROM `contracts` WHERE `parent_id` = ? ORDER BY `created_at` DESC",
        [$id]
    );

    jsonResponse(true, '', [
        'client'    => $client,
        'children'  => $children,
        'invoices'  => $invoices,
        'contracts' => $contracts,
    ]);
}

function addClient(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'create');
    validateCsrf();

    require_once __DIR__ . '/../includes/mailer.php';

    // Create parent account
    $tempPassword = bin2hex(random_bytes(4)); // 8 char temp password

    $result = createUser([
        'first_name' => post('first_name'),
        'last_name'  => post('last_name'),
        'email'      => post('email'),
        'phone'      => post('phone'),
        'password'   => $tempPassword,
        'user_type'  => 'parent',
    ]);

    if (!$result['success']) {
        jsonResponse(false, $result['message']);
    }

    $parentId = $result['user_id'];

    // Mark email verified (admin created the account)
    dbExecute("UPDATE `users` SET `email_verified_at` = NOW() WHERE `id` = ?", [$parentId]);

    // Send activation email with temp password
    $parentUser = dbFetchOne("SELECT * FROM `users` WHERE `id` = ?", [$parentId]);
    sendNotificationEmail(
        $parentUser['email'],
        $parentUser['first_name'],
        'Your Think & Tinker Account',
        "Your account has been created. Log in at " . APP_URL . "/parent/login.php with your email and this temporary password: <strong>{$tempPassword}</strong><br><br>Please change your password after your first login.",
        APP_URL . '/parent/login.php',
        'Log In Now'
    );

    // Create child if provided
    if (post('child_first_name')) {
        $childId = dbInsert('children', [
            'parent_id'     => $parentId,
            'first_name'    => post('child_first_name'),
            'last_name'     => post('child_last_name') ?: post('last_name'),
            'date_of_birth' => post('child_dob'),
            'gender'        => in_array(post('child_gender'), ['male', 'female']) ? post('child_gender') : 'male',
            'current_school' => post('child_school'),
            'grade_level'   => post('child_grade'),
            'medical_notes' => post('child_medical'),
            'status'        => 'active',
            'location_id'   => 1,
        ]);

        // Create service record if specified
        $serviceType = post('service_type');
        if ($serviceType && in_array($serviceType, ['tutorials', 'stem_club', 'both'])) {
            dbInsert('child_services', [
                'child_id'          => $childId,
                'service_type'      => $serviceType,
                'frequency'         => post('frequency'),
                'assigned_tutor_id' => postInt('tutor_id') ?: null,
                'session_rate'      => postFloat('session_rate') ?: null,
                'start_date'        => date('Y-m-d'),
                'status'            => 'active',
            ]);
        }
    }

    jsonResponse(true, 'Client created. Activation email sent.', ['parent_id' => $parentId]);
}

function updateClient(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'edit');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Client ID required.');

    dbUpdate('users', [
        'first_name' => post('first_name'),
        'last_name'  => post('last_name'),
        'phone'      => post('phone'),
    ], 'id = ? AND user_type = ?', [$id, 'parent']);

    jsonResponse(true, 'Client updated.');
}

function deactivateClient(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'delete');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Client ID required.');

    dbUpdate('users', ['is_active' => 0], 'id = ?', [$id]);
    jsonResponse(true, 'Client deactivated. Their session has been terminated.');
}

function activateClient(): void
{
    $user = requireAuth();
    requirePermission($user, 'clients', 'edit');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Client ID required.');

    dbUpdate('users', ['is_active' => 1], 'id = ?', [$id]);
    jsonResponse(true, 'Client reactivated.');
}
