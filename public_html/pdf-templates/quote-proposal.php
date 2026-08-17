<?php
/**
 * pdf-templates/quote-proposal.php
 * Variables: $title, $school_name, $scope, $items (array), $notes, + common business vars
 */
$items = $items ?? [];
$subtotal = 0;
foreach ($items as $item) { $subtotal += ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0); }
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 11px; color: #2C3E50; line-height: 1.5; }
    .header { background: #1B2A4A; color: #FFF; padding: 24px 32px; }
    .header h1 { font-size: 18px; margin-bottom: 2px; }
    .header small { opacity: 0.7; font-size: 10px; }
    .accent { background: #1AAFA0; height: 4px; }
    .body { padding: 28px 32px; }
    .title { font-size: 24px; color: #1AAFA0; font-weight: 700; margin-bottom: 4px; }
    .subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
    .section { margin-bottom: 16px; }
    .section h3 { color: #1AAFA0; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #EEE; padding-bottom: 4px; margin-bottom: 8px; }
    .info-grid td { padding: 4px 8px; font-size: 11px; }
    .info-grid td:first-child { color: #999; width: 30%; }
    table.items { width: 100%; border-collapse: collapse; margin: 12px 0; }
    table.items th { background: #F2F3F4; padding: 8px 12px; text-align: left; font-size: 9px; text-transform: uppercase; color: #666; border-bottom: 2px solid #DDD; }
    table.items td { padding: 8px 12px; border-bottom: 1px solid #EEE; }
    table.items td.amount { text-align: right; font-weight: 600; }
    table.items th.amount { text-align: right; }
    .total-row { text-align: right; font-size: 18px; color: #1AAFA0; font-weight: 700; margin: 12px 0; }
    .scope-text { font-size: 10px; line-height: 1.7; color: #444; background: #F8F8F8; padding: 12px; border-radius: 4px; }
    .bank-box { background: #FEF5E7; border: 1px solid #F47B20; border-radius: 6px; padding: 14px 16px; margin-top: 20px; }
    .bank-box h4 { color: #F47B20; font-size: 11px; margin-bottom: 6px; }
    .validity { margin-top: 16px; font-size: 10px; color: #999; text-align: center; }
    .footer { background: #1B2A4A; color: rgba(255,255,255,0.7); padding: 12px 32px; font-size: 8px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
</style>
</head>
<body>
<div class="header">
    <h1><?= htmlspecialchars($business_name) ?></h1>
    <small>RC: <?= htmlspecialchars($business_rc) ?> · <?= htmlspecialchars($business_address) ?></small>
</div>
<div class="accent"></div>
<div class="body">
    <div class="title">QUOTE / PROPOSAL</div>
    <div class="subtitle">Date: <?= date('M j, Y') ?></div>

    <div class="section">
        <h3>Prepared For</h3>
        <table class="info-grid">
            <?php if (!empty($title)): ?><tr><td>Title</td><td><strong><?= htmlspecialchars($title) ?></strong></td></tr><?php endif; ?>
            <?php if (!empty($school_name)): ?><tr><td>School/Organization</td><td><?= htmlspecialchars($school_name) ?></td></tr><?php endif; ?>
        </table>
    </div>

    <?php if (!empty($scope)): ?>
    <div class="section">
        <h3>Scope of Work</h3>
        <div class="scope-text"><?= nl2br(htmlspecialchars($scope)) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
    <div class="section">
        <h3>Pricing</h3>
        <table class="items">
            <thead><tr><th>Description</th><th>Qty</th><th class="amount">Rate</th><th class="amount">Total</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): $lineTotal = ($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0); ?>
            <tr>
                <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
                <td><?= (int)($item['quantity'] ?? 1) ?></td>
                <td class="amount">₦<?= number_format($item['unit_price'] ?? 0, 0) ?></td>
                <td class="amount">₦<?= number_format($lineTotal, 0) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total-row">Total: ₦<?= number_format($subtotal, 0) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($notes)): ?>
    <div class="section">
        <h3>Notes</h3>
        <div class="scope-text"><?= nl2br(htmlspecialchars($notes)) ?></div>
    </div>
    <?php endif; ?>

    <div class="bank-box">
        <h4>Payment Details</h4>
        <?php foreach ($bank_accounts as $bank): ?>
        <div style="margin-bottom: 4px;">
            <strong><?= htmlspecialchars($bank['bank_name']) ?></strong> · <?= htmlspecialchars($bank['account_name']) ?> ·
            <strong style="font-size: 14px;"><?= htmlspecialchars($bank['account_number']) ?></strong>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="validity">This quote is valid for 30 days from the date issued.</div>
</div>
<div class="footer"><?= htmlspecialchars($business_name) ?> · <?= htmlspecialchars($business_email) ?> · <?= htmlspecialchars($business_phone) ?> · Generated <?= $generated_at ?></div>
</body>
</html>
