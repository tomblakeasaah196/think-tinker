<?php
/**
 * pdf-templates/admission-form.php
 * Variables: $child (includes parent data via JOIN), + common business vars
 */
$age = floor((time() - strtotime($child['date_of_birth'])) / (365.25 * 86400));
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 11px; color: #2C3E50; line-height: 1.5; }
    .header { background: #1AAFA0; color: #FFF; padding: 20px 32px; text-align: center; }
    .header h1 { font-size: 18px; }
    .header small { opacity: 0.8; font-size: 10px; }
    .accent { background: #F47B20; height: 4px; }
    .body { padding: 24px 32px; }
    .title { font-size: 18px; color: #1B2A4A; font-weight: 700; text-align: center; margin-bottom: 16px; }
    .section { margin-bottom: 16px; }
    .section h3 { color: #1AAFA0; font-size: 11px; text-transform: uppercase; border-bottom: 1px solid #EEE; padding-bottom: 4px; margin-bottom: 8px; }
    .field-grid { width: 100%; border-collapse: collapse; }
    .field-grid td { padding: 6px 8px; border: 1px solid #DDD; font-size: 10px; }
    .field-grid td:first-child { background: #F8F8F8; color: #666; width: 35%; font-weight: 600; }
    .write-line { border-bottom: 1px dotted #999; min-height: 20px; padding: 4px 0; margin: 4px 0; }
    .checkbox { display: inline-block; width: 12px; height: 12px; border: 1px solid #999; margin-right: 6px; vertical-align: middle; }
    .terms { font-size: 9px; color: #666; line-height: 1.6; margin-top: 12px; }
    .sig-row { width: 100%; margin-top: 24px; }
    .sig-row td { width: 50%; padding: 8px; vertical-align: bottom; }
    .sig-line { border-top: 1px solid #333; margin-top: 36px; padding-top: 4px; font-size: 10px; }
    .footer { background: #1B2A4A; color: rgba(255,255,255,0.7); padding: 12px 32px; font-size: 8px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
</style>
</head>
<body>
<div class="header">
    <h1><?= htmlspecialchars($business_name) ?></h1>
    <small><?= htmlspecialchars($business_address) ?> · <?= htmlspecialchars($business_phone) ?></small>
</div>
<div class="accent"></div>
<div class="body">
    <div class="title">ADMISSION FORM</div>

    <div class="section">
        <h3>Child Information</h3>
        <table class="field-grid">
            <tr><td>Full Name</td><td><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></td></tr>
            <tr><td>Date of Birth</td><td><?= date('M j, Y', strtotime($child['date_of_birth'])) ?> (Age: <?= $age ?>)</td></tr>
            <tr><td>Gender</td><td><?= ucfirst($child['gender'] ?? 'N/A') ?></td></tr>
            <tr><td>Current School</td><td><?= htmlspecialchars($child['current_school'] ?? 'N/A') ?></td></tr>
            <tr><td>Grade Level</td><td><?= htmlspecialchars($child['grade_level'] ?? 'N/A') ?></td></tr>
            <tr><td>Medical Notes / Allergies</td><td><?= htmlspecialchars($child['medical_notes'] ?? 'None') ?></td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Parent / Guardian Information</h3>
        <table class="field-grid">
            <tr><td>Full Name</td><td><?= htmlspecialchars(($child['parent_first'] ?? '') . ' ' . ($child['parent_last'] ?? '')) ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($child['parent_email'] ?? '') ?></td></tr>
            <tr><td>Phone</td><td><?= htmlspecialchars($child['parent_phone'] ?? '') ?></td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Emergency Contact</h3>
        <table class="field-grid">
            <tr><td>Name</td><td><div class="write-line"></div></td></tr>
            <tr><td>Phone</td><td><div class="write-line"></div></td></tr>
            <tr><td>Relationship</td><td><div class="write-line"></div></td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Services Requested</h3>
        <p style="font-size: 10px; margin-bottom: 8px;">Please tick all that apply:</p>
        <div><span class="checkbox"></span> Home Tutorials (1-on-1)</div>
        <div><span class="checkbox"></span> STEM & Reading Club (Saturdays)</div>
        <div><span class="checkbox"></span> Both Tutorials + Club</div>
    </div>

    <div class="terms">
        <strong>Declaration:</strong> I confirm that the information provided is accurate. I authorize Think & Tinker Kids Club to provide educational services to my child as described. I have read and agree to the terms and conditions of service.
    </div>

    <table class="sig-row">
        <tr>
            <td><div class="sig-line">Parent/Guardian Signature</div></td>
            <td><div class="sig-line">Date</div></td>
        </tr>
    </table>
</div>
<div class="footer">
    <?= htmlspecialchars($business_name) ?> · RC <?= htmlspecialchars($business_rc) ?> · <?= htmlspecialchars($business_email) ?> · Ref: ADM-<?= $child['id'] ?>
</div>
</body>
</html>
