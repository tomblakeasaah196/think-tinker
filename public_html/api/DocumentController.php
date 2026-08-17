<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../includes/pdf.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'generate_invoice':     genInvoice(); break;
    case 'generate_receipt':     genReceipt(); break;
    case 'generate_contract':    genContract(); break;
    case 'generate_admission':   genAdmission(); break;
    case 'generate_progress':    genProgress(); break;
    case 'generate_calendar':    genCalendar(); break;
    case 'generate_certificate': genCertificate(); break;
    case 'generate_badge':       genBadge(); break;
    case 'generate_quote':       genQuote(); break;
    case 'stream_pdf':           streamPdfAction(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function genInvoice(): void {
    $user = requireAuth(); requirePermission($user, 'documents', 'generate_all'); validateCsrf();
    $invoiceId = postInt('invoice_id'); if (!$invoiceId) jsonResponse(false, 'Invoice ID required.');
    $invoice = dbFetchOne("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);
    $items = dbFetchAll("SELECT * FROM invoice_items WHERE invoice_id = ?", [$invoiceId]);
    $parent = dbFetchOne("SELECT * FROM users WHERE id = ?", [$invoice['parent_id']]);
    $result = generateInvoicePDF($invoice, $items, $parent);
    if ($result['success']) dbUpdate('invoices', ['pdf_path' => $result['path']], 'id = ?', [$invoiceId]);
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genReceipt(): void {
    $user = requireAuth(); requirePermission($user, 'documents', 'generate_finance'); validateCsrf();
    $receiptId = postInt('receipt_id'); if (!$receiptId) jsonResponse(false, 'Receipt ID required.');
    $receipt = dbFetchOne("SELECT * FROM receipts WHERE id = ?", [$receiptId]);
    $invoice = dbFetchOne("SELECT * FROM invoices WHERE id = ?", [$receipt['invoice_id']]);
    $parent = dbFetchOne("SELECT * FROM users WHERE id = ?", [$invoice['parent_id']]);
    $result = generateReceiptPDF($receipt, $invoice, $parent);
    if ($result['success']) dbUpdate('receipts', ['pdf_path' => $result['path']], 'id = ?', [$receiptId]);
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genContract(): void {
    $user = requireAuth(); requirePermission($user, 'documents', 'generate_all'); validateCsrf();
    $parentId = postInt('parent_id'); $childId = postInt('child_id');
    if (!$parentId || !$childId) jsonResponse(false, 'Parent and child ID required.');
    $parent = dbFetchOne("SELECT * FROM users WHERE id = ?", [$parentId]);
    $child = dbFetchOne("SELECT * FROM children WHERE id = ?", [$childId]);
    $contractNumber = generateDocNumber('CON', 'contracts', 'contract_number');
    $contractId = dbInsert('contracts', ['parent_id' => $parentId, 'child_id' => $childId,
        'contract_number' => $contractNumber, 'service_type' => post('service_type') ?: 'tutorials',
        'terms_text' => $_POST['terms_text'] ?? 'Standard service provision contract.', 'status' => 'sent']);
    $contract = dbFetchOne("SELECT * FROM contracts WHERE id = ?", [$contractId]);
    $result = generateContractPDF($contract, $parent, $child);
    if ($result['success']) dbUpdate('contracts', ['pdf_path' => $result['path']], 'id = ?', [$contractId]);
    jsonResponse($result['success'], $result['message'], ['contract_id' => $contractId, 'number' => $contractNumber, 'path' => $result['path'] ?? '']);
}

function genAdmission(): void {
    $user = requireAuth(); requirePermission($user, 'documents', 'generate_all'); validateCsrf();
    $childId = postInt('child_id'); if (!$childId) jsonResponse(false, 'Child ID required.');
    $child = dbFetchOne("SELECT c.*, u.first_name as parent_first, u.last_name as parent_last, u.email as parent_email, u.phone as parent_phone FROM children c JOIN users u ON c.parent_id = u.id WHERE c.id = ?", [$childId]);
    if (!$child) jsonResponse(false, 'Child not found.');
    $result = generatePDF('admission-form', ['child' => $child], 'TTK-ADM-'.$childId.'-'.date('Ymd'), 'contracts');
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genProgress(): void {
    $user = requireAuth(); validateCsrf();
    $childId = postInt('child_id'); if (!$childId) jsonResponse(false, 'Child ID required.');
    $child = dbFetchOne("SELECT * FROM children WHERE id = ?", [$childId]);
    $progress = dbFetchAll("SELECT * FROM progress_records WHERE child_id = ? ORDER BY subject, topic", [$childId]);
    $notes = dbFetchAll("SELECT sn.*, s.session_date FROM session_notes sn JOIN sessions s ON sn.session_id = s.id WHERE s.child_id = ? AND sn.status = 'submitted' ORDER BY s.session_date DESC LIMIT 10", [$childId]);
    $result = generateProgressReportPDF($child, $progress, $notes, post('period') ?: date('F Y'));
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genCalendar(): void {
    $user = requireAuth(); validateCsrf();
    $childId = postInt('child_id'); $month = postInt('month') ?: (int)date('n'); $year = postInt('year') ?: (int)date('Y');
    if (!$childId) jsonResponse(false, 'Child ID required.');
    $child = dbFetchOne("SELECT * FROM children WHERE id = ?", [$childId]);
    $startDate = sprintf('%04d-%02d-01', $year, $month); $endDate = date('Y-m-t', strtotime($startDate));
    $sessions = dbFetchAll("SELECT s.*, u.first_name as tutor_name FROM sessions s JOIN users u ON s.tutor_id = u.id WHERE s.child_id = ? AND s.session_date BETWEEN ? AND ? ORDER BY s.session_date", [$childId, $startDate, $endDate]);
    $result = generateMonthlyCalendarPDF($child, $sessions, $month, $year);
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genCertificate(): void {
    $user = requireAuth(); requirePermission($user, 'club', 'edit'); validateCsrf();
    $membershipId = postInt('membership_id'); if (!$membershipId) jsonResponse(false, 'Membership ID required.');
    $membership = dbFetchOne("SELECT * FROM club_memberships WHERE id = ?", [$membershipId]);
    $child = dbFetchOne("SELECT * FROM children WHERE id = ?", [$membership['child_id'] ?? 0]);
    $result = generateClubCertificatePDF($child, $membership);
    if ($result['success']) dbUpdate('club_memberships', ['certificate_pdf' => $result['path']], 'id = ?', [$membershipId]);
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genBadge(): void {
    $user = requireAuth(); requirePermission($user, 'staff', 'edit'); validateCsrf();
    $userId = postInt('user_id'); if (!$userId) jsonResponse(false, 'User ID required.');
    $member = dbFetchOne("SELECT u.*, sr.* FROM users u JOIN staff_records sr ON u.id = sr.user_id WHERE u.id = ?", [$userId]);
    if (!$member) jsonResponse(false, 'Staff not found.');
    $result = generateStaffBadgePDF($member, $member);
    if ($result['success']) dbUpdate('staff_records', ['id_badge_pdf' => $result['path']], 'user_id = ?', [$userId]);
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function genQuote(): void {
    $user = requireAuth(); requirePermission($user, 'documents', 'generate_all'); validateCsrf();
    $result = generateQuotePDF(['title' => post('title'), 'school_name' => post('school_name'),
        'scope' => post('scope'), 'items' => json_decode($_POST['items'] ?? '[]', true), 'notes' => post('notes')]);
    jsonResponse($result['success'], $result['message'], ['path' => $result['path'] ?? '']);
}

function streamPdfAction(): void {
    $user = requireAuth();
    $path = $_GET['path'] ?? '';
    if (!$path || !file_exists(ROOT_PATH.'/'.$path)) { http_response_code(404); exit('File not found.'); }
    if (!str_contains($path, 'uploads/')) { http_response_code(403); exit('Access denied.'); }
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="'.basename($path).'"');
    readfile(ROOT_PATH.'/'.$path);
    exit;
}
