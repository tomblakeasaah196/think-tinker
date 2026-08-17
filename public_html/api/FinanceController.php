<?php
/**
 * api/FinanceController.php
 * Income, expenses, invoices, receipts, P&L reports.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_income':        getIncome(); break;
    case 'get_expenses':      getExpenses(); break;
    case 'add_expense':       addExpense(); break;
    case 'update_expense':    updateExpense(); break;
    case 'delete_expense':    deleteExpense(); break;
    case 'get_invoices':      getInvoices(); break;
    case 'get_invoice':       getInvoice(); break;
    case 'create_invoice':    createInvoice(); break;
    case 'mark_paid':         markInvoicePaid(); break;
    case 'cancel_invoice':    cancelInvoice(); break;
    case 'get_receipts':      getReceipts(); break;
    case 'get_pl_report':     getPLReport(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getIncome(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'view');
    $from = $_GET['from'] ?? date('Y-m-01'); $to = $_GET['to'] ?? date('Y-m-t');
    $income = dbFetchAll(
        "SELECT i.*, u.first_name, u.last_name FROM invoices i JOIN users u ON i.parent_id = u.id
         WHERE i.status = 'paid' AND i.paid_at BETWEEN ? AND ? ORDER BY i.paid_at DESC",
        [$from.' 00:00:00', $to.' 23:59:59']);
    $total = dbFetchColumn("SELECT COALESCE(SUM(total),0) FROM invoices WHERE status='paid' AND paid_at BETWEEN ? AND ?",
        [$from.' 00:00:00', $to.' 23:59:59']);
    foreach ($income as &$i) { $i['formatted_total'] = formatNaira($i['total']); $i['formatted_date'] = formatDate($i['paid_at']); }
    jsonResponse(true, '', ['income' => $income, 'total' => (float)$total, 'formatted_total' => formatNaira($total)]);
}

function getExpenses(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'view');
    $from = $_GET['from'] ?? date('Y-m-01'); $to = $_GET['to'] ?? date('Y-m-t');
    $category = $_GET['category'] ?? '';
    $where = "e.expense_date BETWEEN ? AND ?"; $params = [$from, $to];
    if ($category) { $where .= " AND e.category = ?"; $params[] = $category; }
    $expenses = dbFetchAll(
        "SELECT e.*, u.first_name as created_by_name, c.first_name as child_name
         FROM expenses e JOIN users u ON e.created_by = u.id LEFT JOIN children c ON e.child_id = c.id
         WHERE $where ORDER BY e.expense_date DESC", $params);
    $total = dbFetchColumn("SELECT COALESCE(SUM(amount),0) FROM expenses e WHERE $where", $params);
    foreach ($expenses as &$e) {
        $e['formatted_amount'] = formatNaira($e['amount']); $e['formatted_date'] = formatDate($e['expense_date']);
        $e['receipt_url'] = $e['receipt_image'] ? getUploadUrl($e['receipt_image']) : null;
    }
    jsonResponse(true, '', ['expenses' => $expenses, 'total' => (float)$total, 'formatted_total' => formatNaira($total)]);
}

function addExpense(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'create'); validateCsrf();
    $amount = postFloat('amount'); $category = post('category'); $description = post('description');
    if (!$amount || !$category || !$description) jsonResponse(false, 'Amount, category, and description are required.');
    $data = ['category' => $category, 'description' => $description, 'amount' => $amount,
        'expense_date' => post('expense_date') ?: date('Y-m-d'),
        'child_id' => postInt('child_id') ?: null, 'created_by' => $user['id'], 'location_id' => 1];
    if (!empty($_FILES['receipt_image']['name'])) {
        require_once __DIR__ . '/../includes/upload.php';
        $r = uploadExpenseReceipt('receipt_image');
        if ($r['success']) $data['receipt_image'] = $r['path'];
    }
    $id = dbInsert('expenses', $data);
    jsonResponse(true, 'Expense recorded.', ['id' => $id]);
}

function updateExpense(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'edit'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Expense ID required.');
    $data = [];
    if (post('category')) $data['category'] = post('category');
    if (post('description')) $data['description'] = post('description');
    if (postFloat('amount')) $data['amount'] = postFloat('amount');
    if (post('expense_date')) $data['expense_date'] = post('expense_date');
    if (isset($_POST['child_id'])) $data['child_id'] = postInt('child_id') ?: null;
    if (!empty($data)) dbUpdate('expenses', $data, 'id = ?', [$id]);
    jsonResponse(true, 'Expense updated.');
}

function deleteExpense(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'delete'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Expense ID required.');
    dbExecute("DELETE FROM expenses WHERE id = ?", [$id]);
    jsonResponse(true, 'Expense deleted.');
}

function getInvoices(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'view');
    $status = $_GET['status'] ?? ''; $parentId = (int)($_GET['parent_id'] ?? 0);
    $page = max(1, (int)($_GET['page'] ?? 1)); $perPage = 20;
    $where = "1=1"; $params = [];
    if ($status) { $where .= " AND i.status = ?"; $params[] = $status; }
    if ($parentId) { $where .= " AND i.parent_id = ?"; $params[] = $parentId; }
    $total = dbFetchColumn("SELECT COUNT(*) FROM invoices i WHERE $where", $params);
    $invoices = dbFetchAll(
        "SELECT i.*, u.first_name, u.last_name, c.first_name as child_name
         FROM invoices i JOIN users u ON i.parent_id = u.id LEFT JOIN children c ON i.child_id = c.id
         WHERE $where ORDER BY i.created_at DESC LIMIT ? OFFSET ?",
        array_merge($params, [$perPage, ($page-1)*$perPage]));
    foreach ($invoices as &$inv) { $inv['formatted_total'] = formatNaira($inv['total']); $inv['formatted_due'] = formatDate($inv['due_date']); }
    jsonResponse(true, '', ['invoices' => $invoices, 'total' => (int)$total, 'page' => $page]);
}

function getInvoice(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'view');
    $id = (int)($_GET['id'] ?? 0); if (!$id) jsonResponse(false, 'Invoice ID required.');
    $invoice = dbFetchOne("SELECT i.*, u.first_name, u.last_name, u.email, u.phone FROM invoices i JOIN users u ON i.parent_id = u.id WHERE i.id = ?", [$id]);
    if (!$invoice) jsonResponse(false, 'Invoice not found.');
    $items = dbFetchAll("SELECT * FROM invoice_items WHERE invoice_id = ?", [$id]);
    $receipt = dbFetchOne("SELECT * FROM receipts WHERE invoice_id = ?", [$id]);
    jsonResponse(true, '', ['invoice' => $invoice, 'items' => $items, 'receipt' => $receipt, 'bank_accounts' => getActiveBankAccounts()]);
}

function createInvoice(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'create'); validateCsrf();
    require_once __DIR__ . '/../includes/pdf.php';
    require_once __DIR__ . '/../includes/mailer.php';
    $parentId = postInt('parent_id'); if (!$parentId) jsonResponse(false, 'Parent ID required.');
    $items = json_decode($_POST['items'] ?? '[]', true);
    if (empty($items)) jsonResponse(false, 'At least one line item required.');
    $subtotal = 0;
    foreach ($items as $item) { $subtotal += ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0); }
    $discount = postFloat('discount');
    $total = $subtotal - $discount;
    $invoiceNumber = generateDocNumber('INV', 'invoices', 'invoice_number');
    $invoiceId = dbInsert('invoices', [
        'invoice_number' => $invoiceNumber, 'parent_id' => $parentId,
        'child_id' => postInt('child_id') ?: null, 'invoice_type' => post('invoice_type') ?: 'custom',
        'subtotal' => $subtotal, 'discount' => $discount, 'total' => $total,
        'status' => 'sent', 'due_date' => post('due_date') ?: date('Y-m-d', strtotime('+7 days')),
        'notes' => post('notes'), 'location_id' => 1,
    ]);
    foreach ($items as $item) {
        $qty = (int)($item['quantity'] ?? 1); $price = (float)($item['unit_price'] ?? 0);
        dbInsert('invoice_items', [
            'invoice_id' => $invoiceId, 'description' => sanitize($item['description'] ?? ''),
            'quantity' => $qty, 'unit_price' => $price, 'total' => $qty * $price,
        ]);
    }
    // Generate PDF
    $parent = dbFetchOne("SELECT * FROM users WHERE id = ?", [$parentId]);
    $invoice = dbFetchOne("SELECT * FROM invoices WHERE id = ?", [$invoiceId]);
    $lineItems = dbFetchAll("SELECT * FROM invoice_items WHERE invoice_id = ?", [$invoiceId]);
    $pdfResult = generateInvoicePDF($invoice, $lineItems, $parent);
    if ($pdfResult['success']) {
        dbUpdate('invoices', ['pdf_path' => $pdfResult['path']], 'id = ?', [$invoiceId]);
        sendInvoiceEmail($parent, $invoiceNumber, $total, $invoice['due_date'], ROOT_PATH.'/'.$pdfResult['path']);
    }
    createNotification($parentId, "New invoice: $invoiceNumber", "Amount due: ".formatNaira($total), APP_URL.'/parent/payments.php');
    jsonResponse(true, 'Invoice created and sent.', ['id' => $invoiceId, 'number' => $invoiceNumber]);
}

function markInvoicePaid(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'edit'); validateCsrf();
    require_once __DIR__ . '/../includes/pdf.php';
    require_once __DIR__ . '/../includes/mailer.php';
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Invoice ID required.');
    $invoice = dbFetchOne("SELECT * FROM invoices WHERE id = ?", [$id]);
    if (!$invoice) jsonResponse(false, 'Invoice not found.');
    if ($invoice['status'] === 'paid') jsonResponse(false, 'Invoice already marked as paid.');
    dbUpdate('invoices', ['status' => 'paid', 'paid_at' => date('Y-m-d H:i:s'),
        'payment_method' => post('payment_method') ?: 'bank_transfer',
        'payment_reference' => post('payment_reference'), 'confirmed_by' => $user['id'],
    ], 'id = ?', [$id]);
    // Generate receipt
    $receiptNumber = generateDocNumber('RCT', 'receipts', 'receipt_number');
    $receiptId = dbInsert('receipts', [
        'receipt_number' => $receiptNumber, 'invoice_id' => $id, 'amount' => $invoice['total'],
        'payment_method' => post('payment_method') ?: 'bank_transfer', 'payment_reference' => post('payment_reference'),
    ]);
    $receipt = dbFetchOne("SELECT * FROM receipts WHERE id = ?", [$receiptId]);
    $parent = dbFetchOne("SELECT * FROM users WHERE id = ?", [$invoice['parent_id']]);
    $pdfResult = generateReceiptPDF($receipt, $invoice, $parent);
    if ($pdfResult['success']) {
        dbUpdate('receipts', ['pdf_path' => $pdfResult['path']], 'id = ?', [$receiptId]);
        sendNotificationEmail($parent['email'], $parent['first_name'], "Payment Received — {$invoice['invoice_number']}",
            "We've received your payment of ".formatNaira($invoice['total']).". Your receipt ({$receiptNumber}) is attached.",
            APP_URL.'/parent/payments.php', 'View Receipt');
    }
    createNotification($invoice['parent_id'], "Payment confirmed", "Receipt {$receiptNumber} for ".formatNaira($invoice['total']), APP_URL.'/parent/payments.php');
    jsonResponse(true, 'Invoice marked as paid. Receipt generated.', ['receipt_number' => $receiptNumber]);
}

function cancelInvoice(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'edit'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Invoice ID required.');
    dbUpdate('invoices', ['status' => 'cancelled'], 'id = ?', [$id]);
    jsonResponse(true, 'Invoice cancelled.');
}

function getReceipts(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'view');
    $receipts = dbFetchAll(
        "SELECT r.*, i.invoice_number, i.parent_id, u.first_name, u.last_name
         FROM receipts r JOIN invoices i ON r.invoice_id = i.id JOIN users u ON i.parent_id = u.id
         ORDER BY r.created_at DESC LIMIT 50");
    foreach ($receipts as &$r) { $r['formatted_amount'] = formatNaira($r['amount']); $r['pdf_url'] = $r['pdf_path'] ? getUploadUrl($r['pdf_path']) : null; }
    jsonResponse(true, '', ['receipts' => $receipts]);
}

function getPLReport(): void {
    $user = requireAuth(); requirePermission($user, 'finance', 'reports');
    $from = $_GET['from'] ?? date('Y-01-01'); $to = $_GET['to'] ?? date('Y-12-31');
    $revenue = (float)dbFetchColumn("SELECT COALESCE(SUM(total),0) FROM invoices WHERE status='paid' AND paid_at BETWEEN ? AND ?", [$from.' 00:00:00', $to.' 23:59:59']);
    $expenses = (float)dbFetchColumn("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN ? AND ?", [$from, $to]);
    // By category
    $revByType = dbFetchAll("SELECT invoice_type, SUM(total) as total FROM invoices WHERE status='paid' AND paid_at BETWEEN ? AND ? GROUP BY invoice_type", [$from.' 00:00:00', $to.' 23:59:59']);
    $expByCategory = dbFetchAll("SELECT category, SUM(amount) as total FROM expenses WHERE expense_date BETWEEN ? AND ? GROUP BY category", [$from, $to]);
    $netProfit = $revenue - $expenses;
    $margin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;
    jsonResponse(true, '', [
        'period' => ['from' => $from, 'to' => $to],
        'revenue' => $revenue, 'expenses' => $expenses, 'net_profit' => $netProfit, 'margin' => $margin,
        'formatted' => ['revenue' => formatNaira($revenue), 'expenses' => formatNaira($expenses), 'net_profit' => formatNaira($netProfit)],
        'revenue_by_type' => $revByType, 'expenses_by_category' => $expByCategory,
    ]);
}
