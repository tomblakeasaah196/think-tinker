<?php
/**
 * pdf-templates/contract.php
 * Variables: $contract, $parent, $child, + common business vars
 * Note for PDF Engines: Use absolute paths (ROOT_PATH) for images.
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #2C3E50; line-height: 1.6; }
        
        /* Branded Header with Logo Support */
        .header { background: #1B2A4A; color: #FFF; padding: 24px 32px; width: 100%; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo-cell { width: 60px; }
        .logo { max-height: 40px; width: auto; }
        .business-info h1 { font-size: 18px; margin-bottom: 2px; color: #FFFFFF; }
        .business-info small { opacity: 0.8; font-size: 10px; letter-spacing: 0.5px; }
        
        .accent { background: #1AAFA0; height: 4px; }
        .body { padding: 28px 32px; }
        .title { font-size: 20px; color: #1B2A4A; font-weight: 700; text-align: center; margin-bottom: 4px; letter-spacing: 1px; }
        .subtitle { text-align: center; color: #7F8C8D; font-size: 10px; margin-bottom: 24px; text-transform: uppercase; }
        
        .section { margin-bottom: 20px; }
        .section h3 { color: #1AAFA0; font-size: 12px; border-bottom: 1px solid #E2E8F0; padding-bottom: 4px; margin-bottom: 12px; text-transform: uppercase; }
        
        /* Grid Layouts for PDF */
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .info-grid td { padding: 6px 8px; font-size: 11px; border-bottom: 1px solid #F8F9F9; }
        .info-grid td:first-child { color: #7F8C8D; width: 35%; font-weight: bold; }
        .info-grid td:last-child { font-weight: 600; color: #1B2A4A; }
        
        .terms { font-size: 10px; line-height: 1.8; color: #34495E; text-align: justify; }
        .terms ol { padding-left: 24px; }
        .terms li { margin-bottom: 8px; padding-left: 4px; }
        
        /* Signatures */
        .sig-area { margin-top: 40px; width: 100%; border-collapse: collapse; }
        .sig-area td { padding: 8px 16px; width: 50%; vertical-align: bottom; }
        .sig-line { border-top: 1px solid #BDC3C7; margin-top: 40px; padding-top: 6px; font-size: 10px; color: #1B2A4A; }
        .sig-hash { font-size: 8px; color: #95A5A6; margin-top: 4px; word-break: break-all; }
        
        .footer { background: #F2F3F4; color: #7F8C8D; padding: 12px 32px; font-size: 8px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #E2E8F0; }
    </style>
</head>
<body>

<div class="header">
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <?php if (!empty($business_logo)): ?>
                    <img src="<?= ROOT_PATH . '/' . $business_logo ?>" class="logo" alt="Logo">
                <?php endif; ?>
            </td>
            <td class="business-info">
                <h1><?= htmlspecialchars($business_name) ?></h1>
                <small>RC: <?= htmlspecialchars($business_rc) ?> &nbsp;&middot;&nbsp; <?= htmlspecialchars($business_address) ?></small>
            </td>
        </tr>
    </table>
</div>
<div class="accent"></div>

<div class="body">
    <div class="title">SERVICE AGREEMENT</div>
    <div class="subtitle">Contract No: <?= htmlspecialchars($contract['contract_number']) ?> &nbsp;&middot;&nbsp; Date: <?= date('M j, Y', strtotime($contract['created_at'])) ?></div>

    <div class="section">
        <h3>1. Parties</h3>
        <table class="info-grid">
            <tr><td>Service Provider</td><td><?= htmlspecialchars($business_name) ?> (RC <?= htmlspecialchars($business_rc) ?>)</td></tr>
            <tr><td>Parent/Guardian</td><td><?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($parent['email']) ?></td></tr>
            <tr><td>Phone</td><td><?= htmlspecialchars($parent['phone']) ?></td></tr>
            <tr><td>Student</td><td><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></td></tr>
            <tr><td>Date of Birth</td><td><?= date('M j, Y', strtotime($child['date_of_birth'])) ?></td></tr>
            <tr><td>Service Type</td><td><?= ucfirst(str_replace('_', ' ', $contract['service_type'])) ?></td></tr>
        </table>
    </div>

    <div class="section">
        <h3>2. Terms & Conditions</h3>
        <div class="terms">
            <?php if (!empty($contract['terms_text'])): ?>
                <?= nl2br(htmlspecialchars($contract['terms_text'])) ?>
            <?php else: ?>
            <ol>
                <li><strong>Scope of Service:</strong> Think & Tinker will provide educational services as described in the selected service plan, tailored to the student's age and developmental needs.</li>
                <li><strong>Duration & Termination:</strong> This agreement commences on the date of execution. Either party may terminate this agreement with 14 days' written notice.</li>
                <li><strong>Payment Terms:</strong> Fees are due as per the generated invoice schedule. <strong>Payment is strictly by Bank Transfer only.</strong> Services and portal access will be suspended for accounts overdue by more than 14 days.</li>
                <li><strong>Rescheduling & Cancellations:</strong> Sessions may be rescheduled with a minimum of 24 hours' notice. No-shows or cancellations made with less than 24 hours' notice will be billed as completed sessions.</li>
                <li><strong>Digital Platform & Communication:</strong> All session notes, progress tracking, and official communications will be hosted securely on the Think & Tinker Parent Portal. Parents agree to use the portal for tracking and primary communication.</li>
                <li><strong>Child Safety & Medical Disclosure:</strong> The parent/guardian must disclose any medical conditions, allergies, or special needs. For physical club events, Think & Tinker will only release the child to authorized individuals listed in the parent portal.</li>
                <li><strong>Data Privacy (NDPR Compliance):</strong> Think & Tinker complies with the Nigerian Data Protection Regulations (NDPR). Client and student data, including voice-to-text session notes, are securely stored and processed solely for educational and administrative purposes.</li>
                <li><strong>Intellectual Property:</strong> All proprietary teaching materials, Consult Booklets, and portal resources remain the intellectual property of Think & Tinker and may not be reproduced, shared, or distributed without permission.</li>
                <li><strong>Non-Solicitation:</strong> Parents/Guardians agree not to directly hire, solicit, or engage Think & Tinker staff or tutors for private educational services outside of the Think & Tinker ecosystem during the term of this contract and for 12 months following its termination.</li>
                <li><strong>Media & Photography:</strong> Think & Tinker prioritizes child safety. We will only photograph or record the student for internal quality assurance or marketing purposes if explicit digital consent has been granted by the parent during the onboarding process.</li>
                <li><strong>Limitation of Liability:</strong> Think & Tinker will exercise reasonable care for the student's safety. However, we are not liable for the loss of personal items. Total liability under this agreement is strictly limited to the fees paid for the current active service period.</li>
            </ol>
            <?php endif; ?>
        </div>
    </div>

    <div class="section">
        <h3>3. Signatures</h3>
        <table class="sig-area">
            <tr>
                <td>
                    <?php if (!empty($contract['parent_signature'])): ?>
                        <img src="<?= ROOT_PATH . '/' . $contract['parent_signature'] ?>" style="max-width: 180px; max-height: 50px;"><br>
                    <?php endif; ?>
                    <div class="sig-line">
                        <strong><?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?></strong><br>
                        Parent / Guardian
                        <?php if (!empty($contract['signed_at'])): ?>
                            <br><span style="color: #7F8C8D;">Signed: <?= date('M j, Y g:i A', strtotime($contract['signed_at'])) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($contract['signature_hash'])): ?>
                        <div class="sig-hash">IP/Hash Auth: <?= htmlspecialchars($contract['signature_hash']) ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="sig-line">
                        <strong>For: <?= htmlspecialchars($business_name) ?></strong><br>
                        Authorized Representative
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="footer">
    <?= htmlspecialchars($business_name) ?> &nbsp;&middot;&nbsp; <?= htmlspecialchars($business_address) ?> &nbsp;&middot;&nbsp; <?= htmlspecialchars($business_email) ?> &nbsp;&middot;&nbsp; Generated <?= date('M j, Y H:i') ?>
</div>

</body>
</html>