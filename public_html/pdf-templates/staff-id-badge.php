<?php
/**
 * pdf-templates/staff-id-badge.php
 * Variables: $user, $staff, + common business vars
 * Generates two badges per page: front and back (for double-sided printing)
 */
$photoUrl = (!empty($user['profile_photo'])) ? ROOT_PATH . '/' . $user['profile_photo'] : '';
$hasPhoto = $photoUrl && file_exists($photoUrl);
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    @page { margin: 20mm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 10px; color: #2C3E50; }
    .badge { width: 240px; height: 380px; border: 2px solid #1AAFA0; border-radius: 12px; overflow: hidden; margin: 0 auto 20px; }
    .badge-header { background: #1AAFA0; color: #FFF; padding: 12px 16px; text-align: center; }
    .badge-header h2 { font-size: 14px; margin-bottom: 2px; }
    .badge-header small { font-size: 8px; opacity: 0.8; }
    .badge-accent { background: #F47B20; height: 3px; }
    .badge-body { padding: 16px; text-align: center; }
    .photo-circle { width: 90px; height: 90px; border-radius: 50%; border: 3px solid #1AAFA0; margin: 0 auto 12px; overflow: hidden; background: #F2F3F4; }
    .photo-circle img { width: 100%; height: 100%; object-fit: cover; }
    .photo-placeholder { width: 90px; height: 90px; border-radius: 50%; border: 3px solid #1AAFA0; margin: 0 auto 12px; background: #F2F3F4; line-height: 84px; font-size: 32px; color: #1AAFA0; text-align: center; }
    .staff-name { font-size: 16px; font-weight: 700; color: #1B2A4A; margin-bottom: 2px; }
    .staff-title { font-size: 11px; color: #1AAFA0; font-weight: 600; margin-bottom: 8px; }
    .staff-id { font-size: 18px; font-weight: 700; color: #1B2A4A; letter-spacing: 2px; margin: 8px 0; }
    .staff-detail { font-size: 9px; color: #666; margin: 2px 0; }
    .badge-footer { background: #1B2A4A; color: #FFF; padding: 8px; text-align: center; font-size: 8px; position: absolute; bottom: 0; left: 0; right: 0; }

    .back-badge { width: 240px; height: 380px; border: 2px solid #1AAFA0; border-radius: 12px; overflow: hidden; margin: 0 auto; padding: 20px; }
    .back-badge h3 { color: #1AAFA0; font-size: 12px; text-align: center; margin-bottom: 12px; }
    .back-info { font-size: 9px; line-height: 1.8; color: #444; }
    .back-info strong { color: #1B2A4A; }
    .emergency-box { background: #FEF5E7; border-radius: 4px; padding: 8px; margin-top: 12px; font-size: 9px; }
    .emergency-box strong { color: #F47B20; }
    .back-footer { text-align: center; margin-top: 16px; font-size: 8px; color: #999; }
</style>
</head>
<body>

<!-- FRONT -->
<div class="badge" style="position: relative;">
    <div class="badge-header">
        <h2><?= htmlspecialchars($business_name) ?></h2>
        <small>Staff Identification</small>
    </div>
    <div class="badge-accent"></div>
    <div class="badge-body">
        <?php if ($hasPhoto): ?>
        <div class="photo-circle"><img src="<?= $photoUrl ?>"></div>
        <?php else: ?>
        <div class="photo-placeholder"><?= strtoupper(substr($user['first_name'],0,1) . substr($user['last_name'],0,1)) ?></div>
        <?php endif; ?>
        <div class="staff-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
        <div class="staff-title"><?= htmlspecialchars($staff['job_title'] ?? ucfirst($user['user_type'])) ?></div>
        <div class="staff-id"><?= htmlspecialchars($staff['staff_id_number'] ?? 'N/A') ?></div>
        <div class="staff-detail"><?= htmlspecialchars($user['email']) ?></div>
        <div class="staff-detail"><?= htmlspecialchars($user['phone']) ?></div>
        <div class="staff-detail">Since: <?= date('M Y', strtotime($staff['hire_date'] ?? $user['created_at'])) ?></div>
    </div>
    <div class="badge-footer"><?= htmlspecialchars($business_address) ?></div>
</div>

<div style="page-break-before: always;"></div>

<!-- BACK -->
<div class="back-badge">
    <h3><?= htmlspecialchars($business_name) ?></h3>
    <div class="back-info">
        <strong>ID Number:</strong> <?= htmlspecialchars($staff['staff_id_number'] ?? 'N/A') ?><br>
        <strong>Role:</strong> <?= htmlspecialchars($staff['job_title'] ?? ucfirst($user['user_type'])) ?><br>
        <strong>Employment:</strong> <?= ucfirst($staff['employment_type'] ?? 'Contract') ?><br>
        <strong>Issued:</strong> <?= date('M j, Y') ?><br>
    </div>

    <div class="emergency-box">
        <strong>In Case of Emergency</strong><br>
        <?php if (!empty($staff['guarantor_name'])): ?>
        Contact: <?= htmlspecialchars($staff['guarantor_name']) ?><br>
        Phone: <?= htmlspecialchars($staff['guarantor_phone'] ?? 'N/A') ?>
        <?php else: ?>
        Contact the office: <?= htmlspecialchars($business_phone) ?>
        <?php endif; ?>
    </div>

    <div class="back-info" style="margin-top: 12px;">
        <strong>Office:</strong> <?= htmlspecialchars($business_address) ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($business_phone) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($business_email) ?><br>
        <strong>RC:</strong> <?= htmlspecialchars($business_rc) ?>
    </div>

    <div class="back-footer">
        If found, please return to the address above.<br>
        This badge remains the property of <?= htmlspecialchars($business_name) ?>.
    </div>
</div>

</body>
</html>
