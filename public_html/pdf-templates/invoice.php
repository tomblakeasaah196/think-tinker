<?php
/**
 * pdf-templates/invoice.php
 * Variables: $invoice, $items, $parent, + common business vars
 */
$statusColors = ['sent' => '#3498DB', 'paid' => '#27AE60', 'overdue' => '#E8614D', 'cancelled' => '#999'];
$statusColor = $statusColors[$invoice['status']] ?? '#3498DB';
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 11px; color: #2C3E50; line-height: 1.5; }
    .header { background: #1AAFA0; color: #FFF; padding: 24px 32px; }
    .header h1 { font-size: 20px; margin-bottom: 2px; }
    .header small { opacity: 0.8; font-size: 10px; }
    .accent { background: #F47B20; height: 4px; }
    .body { padding: 28px 32px; }
    .row { width: 100%; overflow: hidden; margin-bottom: 20px; }
    .col-left { float: left; width: 50%; }
    .col-right { float: right; width: 45%; text-align: right; }
    .label { color: #999; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .value { font-size: 12px; font-weight: bold; }
    .invoice-title { font-size: 28px; color: #1B2A4A; font-weight: 700; margin-bottom: 4px; }
    .status { display: inline-block; padding: 3px 10px; border-radius: 12px; color: #FFF; font-size: 9px; font-weight: 600; text-transform: uppercase; background: <?= $statusColor ?>; }
    table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    th { background: #F2F3F4; padding: 8px 12px; text-align: left; font-size: 9px; text-transform: uppercase; color: #666; border-bottom: 2px solid #DDD; }
    td { padding: 10px 12px; border-bottom: 1px solid #EEE; font-size: 11px; }
    td.amount { text-align: right; font-weight: 600; }
    th.amount { text-align: right; }
    .totals { width: 50%; float: right; margin-top: 8px; }
    .totals tr td { border: none; padding: 4px 12px; }
    .totals .grand { font-size: 16px; color: #1AAFA0; font-weight: 700; border-top: 2px solid #1AAFA0; }
    .bank-box { background: #FEF5E7; border: 1px solid #F47B20; border-radius: 6px; padding: 14px 16px; margin-top: 24px; clear: both; }
    .bank-box h3 { color: #F47B20; font-size: 11px; margin-bottom: 6px; }
    .bank-item { margin-bottom: 6px; }
    .bank-item .acct-num { font-size: 16px; font-weight: 700; color: #1B2A4A; }
    .primary-badge { color: #27AE60; font-size: 8px; }
    .notes { margin-top: 16px; padding: 10px; background: #F2F3F4; border-radius: 4px; font-size: 10px; color: #666; }
    .footer { background: #1B2A4A; color: rgba(255,255,255,0.7); padding: 16px 32px; font-size: 9px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
</style>
</head>
<body>

<div class="header">
    <h1><?= htmlspecialchars($business_name) ?></h1>
    <small><?= htmlspecialchars($business_address) ?> · <?= htmlspecialchars($business_phone) ?> · <?= htmlspecialchars($business_email) ?></small>
</div>
<div class="accent"></div>

<div class="body">
    <div class="row">
        <div class="col-left">
            <div class="invoice-title">INVOICE</div>
            <span class="status"><?= strtoupper($invoice['status']) ?></span>
        </div>
        <div class="col-right">
            <div class="label">Invoice Number</div>
            <div class="value"><?= htmlspecialchars($invoice['invoice_number']) ?></div>
            <br>
            <div class="label">Date Issued</div>
            <div class="value"><?= date('M j, Y', strtotime($invoice['created_at'])) ?></div>
            <br>
            <div class="label">Due Date</div>
            <div class="value" style="color: <?= $statusColor ?>;"><?= date('M j, Y', strtotime($invoice['due_date'])) ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-left">
            <div class="label">Bill To</div>
            <div class="value"><?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?></div>
            <div style="font-size: 10px; color: #666;">
                <?= htmlspecialchars($parent['email']) ?><br>
                <?= htmlspecialchars($parent['phone']) ?>
            </div>
        </div>
        <div class="col-right">
            <div class="label">From</div>
            <div class="value"><?= htmlspecialchars($business_name) ?></div>
            <div style="font-size: 10px; color: #666;">
                RC: <?= htmlspecialchars($business_rc) ?><br>
                <?= htmlspecialchars($business_email) ?>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th style="width: 15%;">Qty</th>
                <th class="amount" style="width: 17%;">Unit Price</th>
                <th class="amount" style="width: 18%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td><?= (int) $item['quantity'] ?></td>
                <td class="amount">₦<?= number_format($item['unit_price'], 0) ?></td>
                <td class="amount">₦<?= number_format($item['total'], 0) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="amount">₦<?= number_format($invoice['subtotal'], 0) ?></td></tr>
        <?php if ($invoice['discount'] > 0): ?>
        <tr><td>Discount</td><td class="amount" style="color: #E8614D;">-₦<?= number_format($invoice['discount'], 0) ?></td></tr>
        <?php endif; ?>
        <tr class="grand"><td><strong>Total Due</strong></td><td class="amount">₦<?= number_format($invoice['total'], 0) ?></td></tr>
    </table>

    <div class="bank-box">
        <h3>💳 Payment Details — Bank Transfer</h3>
        <?php foreach ($bank_accounts as $bank): ?>
        <div class="bank-item">
            <strong><?= htmlspecialchars($bank['bank_name']) ?></strong>
            <?php if ($bank['is_primary']): ?><span class="primary-badge">✓ Recommended</span><?php endif; ?><br>
            <?= htmlspecialchars($bank['account_name']) ?><br>
            <span class="acct-num"><?= htmlspecialchars($bank['account_number']) ?></span>
        </div>
        <?php endforeach; ?>
        <div style="font-size: 9px; color: #666; margin-top: 6px;">
            Please use <strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong> as your payment reference.
        </div>
    </div>

    <?php if ($invoice['notes']): ?>
    <div class="notes"><strong>Notes:</strong> <?= htmlspecialchars($invoice['notes']) ?></div>
    <?php endif; ?>
</div>

<div class="footer">
    <?= htmlspecialchars($business_name) ?> · RC <?= htmlspecialchars($business_rc) ?> · <?= htmlspecialchars($business_address) ?><br>
    Generated on <?= $generated_at ?>
</div>

</body>
</html>
