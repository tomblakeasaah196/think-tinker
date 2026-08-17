<?php
/**
 * pdf-templates/receipt.php
 * Variables: $receipt, $invoice, $parent, + common business vars
 */
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 11px; color: #2C3E50; line-height: 1.5; }
    .header { background: #27AE60; color: #FFF; padding: 24px 32px; }
    .header h1 { font-size: 20px; margin-bottom: 2px; }
    .header small { opacity: 0.8; font-size: 10px; }
    .accent { background: #1AAFA0; height: 4px; }
    .body { padding: 28px 32px; }
    .row { width: 100%; overflow: hidden; margin-bottom: 20px; }
    .col-left { float: left; width: 50%; }
    .col-right { float: right; width: 45%; text-align: right; }
    .label { color: #999; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .value { font-size: 12px; font-weight: bold; }
    .receipt-title { font-size: 28px; color: #27AE60; font-weight: 700; margin-bottom: 4px; }
    .paid-badge { display: inline-block; padding: 4px 14px; border-radius: 12px; color: #FFF; font-size: 10px; font-weight: 600; background: #27AE60; }
    .amount-box { background: #F0FFF4; border: 2px solid #27AE60; border-radius: 8px; padding: 20px; text-align: center; margin: 24px 0; }
    .amount-box .label { color: #27AE60; }
    .amount-box .big { font-size: 32px; color: #27AE60; font-weight: 700; }
    .detail-table { width: 100%; margin: 16px 0; }
    .detail-table td { padding: 8px 0; border-bottom: 1px solid #EEE; }
    .detail-table td:first-child { color: #999; width: 40%; }
    .detail-table td:last-child { font-weight: 600; }
    .thank-you { background: #FEF5E7; border-radius: 8px; padding: 16px; text-align: center; margin-top: 24px; }
    .thank-you h3 { color: #F47B20; font-size: 14px; margin-bottom: 4px; }
    .footer { background: #1B2A4A; color: rgba(255,255,255,0.7); padding: 16px 32px; font-size: 9px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
</style>
</head>
<body>

<div class="header">
    <h1><?= htmlspecialchars($business_name) ?></h1>
    <small><?= htmlspecialchars($business_address) ?> · <?= htmlspecialchars($business_phone) ?></small>
</div>
<div class="accent"></div>

<div class="body">
    <div class="row">
        <div class="col-left">
            <div class="receipt-title">RECEIPT</div>
            <span class="paid-badge">✓ PAID</span>
        </div>
        <div class="col-right">
            <div class="label">Receipt Number</div>
            <div class="value"><?= htmlspecialchars($receipt['receipt_number']) ?></div>
            <br>
            <div class="label">Payment Date</div>
            <div class="value"><?= date('M j, Y', strtotime($receipt['created_at'])) ?></div>
        </div>
    </div>

    <div class="amount-box">
        <div class="label">Amount Received</div>
        <div class="big">₦<?= number_format($receipt['amount'], 0) ?></div>
    </div>

    <table class="detail-table">
        <tr><td>Received From</td><td><?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?></td></tr>
        <tr><td>Email</td><td><?= htmlspecialchars($parent['email']) ?></td></tr>
        <tr><td>Invoice Reference</td><td><?= htmlspecialchars($invoice['invoice_number']) ?></td></tr>
        <tr><td>Invoice Type</td><td><?= ucfirst(str_replace('_', ' ', $invoice['invoice_type'])) ?></td></tr>
        <tr><td>Payment Method</td><td><?= ucfirst(str_replace('_', ' ', $receipt['payment_method'] ?? 'Bank Transfer')) ?></td></tr>
        <?php if ($receipt['payment_reference']): ?>
        <tr><td>Payment Reference</td><td><?= htmlspecialchars($receipt['payment_reference']) ?></td></tr>
        <?php endif; ?>
        <tr><td>Invoice Date</td><td><?= date('M j, Y', strtotime($invoice['created_at'])) ?></td></tr>
        <tr><td>Date Paid</td><td><?= date('M j, Y g:i A', strtotime($receipt['created_at'])) ?></td></tr>
    </table>

    <div class="thank-you">
        <h3>Thank You!</h3>
        <p style="font-size: 11px; color: #666;">This receipt confirms your payment has been received and processed. Please keep this for your records.</p>
    </div>
</div>

<div class="footer">
    <?= htmlspecialchars($business_name) ?> · RC <?= htmlspecialchars($business_rc) ?> · <?= htmlspecialchars($business_address) ?><br>
    Generated on <?= $generated_at ?>
</div>

</body>
</html>
