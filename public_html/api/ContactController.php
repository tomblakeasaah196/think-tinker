<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'submit_inquiry':    submitInquiry(); break;
    case 'get_inquiries':     getInquiries(); break;
    case 'get_inquiry':       getInquiry(); break;
    case 'respond_inquiry':   respondInquiry(); break;
    case 'close_inquiry':     closeInquiry(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function submitInquiry(): void {
    validateCsrf();
    $name = post('name'); $email = post('email'); $message = post('message');
    if (!$name || !$email || !$message) jsonResponse(false, 'Name, email, and message are required.');
    if (!isValidEmail($email)) jsonResponse(false, 'Please enter a valid email.');
    $id = dbInsert('contact_inquiries', ['inquiry_type' => post('inquiry_type') ?: 'general', 'name' => $name,
        'email' => $email, 'phone' => post('phone'), 'school_name' => post('school_name'), 'message' => $message]);
    // Notify admins
    $admins = dbFetchAll("SELECT id FROM users WHERE user_type IN ('super_admin','admin') AND is_active = 1");
    foreach ($admins as $a) { createNotification($a['id'], "New inquiry from {$name}", substr($message, 0, 100), HUB_URL.'/clients.php'); }
    // Send confirmation to inquirer
    require_once __DIR__ . '/../includes/mailer.php';
    sendNotificationEmail($email, $name, 'We received your inquiry',
        'Thank you for reaching out to Think & Tinker Kids Club. We\'ll respond within 24–48 hours.', APP_URL, 'Visit Our Website');
    jsonResponse(true, 'Thank you! We\'ll be in touch soon.', ['id' => $id]);
}

function getInquiries(): void {
    $user = requireAuth(); requirePermission($user, 'clients', 'view');
    $status = $_GET['status'] ?? '';
    $where = "1=1"; $params = [];
    if ($status) { $where .= " AND status = ?"; $params[] = $status; }
    $inquiries = dbFetchAll("SELECT * FROM contact_inquiries WHERE $where ORDER BY created_at DESC", $params);
    foreach ($inquiries as &$q) { $q['time_ago'] = timeAgo($q['created_at']); }
    jsonResponse(true, '', ['inquiries' => $inquiries]);
}

function getInquiry(): void {
    $user = requireAuth(); requirePermission($user, 'clients', 'view');
    $id = (int)($_GET['id'] ?? 0);
    $inquiry = dbFetchOne("SELECT * FROM contact_inquiries WHERE id = ?", [$id]);
    if (!$inquiry) jsonResponse(false, 'Inquiry not found.');
    jsonResponse(true, '', ['inquiry' => $inquiry]);
}

function respondInquiry(): void {
    $user = requireAuth(); requirePermission($user, 'clients', 'edit'); validateCsrf();
    require_once __DIR__ . '/../includes/mailer.php';
    $id = postInt('id'); $response = post('response');
    if (!$id || !$response) jsonResponse(false, 'ID and response required.');
    $inquiry = dbFetchOne("SELECT * FROM contact_inquiries WHERE id = ?", [$id]);
    if (!$inquiry) jsonResponse(false, 'Inquiry not found.');
    dbUpdate('contact_inquiries', ['status' => 'responded', 'responded_by' => $user['id'], 'responded_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
    sendNotificationEmail($inquiry['email'], $inquiry['name'], 'Response from Think & Tinker', nl2br(sanitize($response)), APP_URL, 'Visit Our Website');
    jsonResponse(true, 'Response sent.');
}

function closeInquiry(): void {
    $user = requireAuth(); requirePermission($user, 'clients', 'edit'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'ID required.');
    dbUpdate('contact_inquiries', ['status' => 'closed'], 'id = ?', [$id]);
    jsonResponse(true, 'Inquiry closed.');
}
