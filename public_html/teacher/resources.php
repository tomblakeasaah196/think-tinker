<?php
$pageTitle = 'Teaching Resources';
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../templates/header-teacher.php';
?>
<div style="text-align:center;margin:var(--space-6) 0;">
    <div class="icon-badge icon-badge--lg"><?= ttIcon('book', 'tt-icon', 40) ?></div>
    <h2 class="mb-2">Teaching Resources</h2>
    <p class="text-muted" style="max-width:480px;margin:0 auto;">Downloadable lesson plans, worksheets, activity guides, and STEM project ideas — curated for Think & Tinker tutors.</p>
    <p class="mt-3 text-sm text-muted">Resources are available to active tutors via the <a href="<?= HUB_URL ?>" style="color:var(--tinker-teal);">Operations Hub</a>.</p>
</div>
<?php include __DIR__ . '/../templates/footer-teacher.php'; ?>
