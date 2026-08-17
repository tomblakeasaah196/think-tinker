<?php
$pageTitle = 'Workshops & Training';
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../templates/header-teacher.php';
?>
<div style="text-align:center;margin:var(--space-6) 0;">
    <div style="font-size:3rem;">🎓</div>
    <h2 class="mb-2">Workshops & Training</h2>
    <p class="text-muted" style="max-width:480px;margin:0 auto;">Professional development sessions for Think & Tinker educators. Coming soon.</p>
    <a href="<?= whatsappLink('Hi! I\'d like to learn about upcoming Think & Tinker teacher workshops.') ?>" target="_blank" class="btn btn-outline mt-3">💬 Ask About Workshops</a>
</div>
<?php include __DIR__ . '/../templates/footer-teacher.php'; ?>
