<?php
$pageTitle = 'For Teachers';
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../templates/header-teacher.php';
?>

<div class="teacher-hero">
    <h1>Join Our Teaching Team</h1>
    <p>Help shape curious minds. Flexible hours, competitive pay, and the joy of watching children grow.</p>
    <div style="margin-top: 20px;">
        <a href="<?= APP_URL ?>/teacher/apply.php" class="btn btn-secondary btn-lg">Apply as Tutor</a>
        <a href="<?= APP_URL ?>/teacher/login.php" class="btn btn-outline btn-sm" style="margin-left: 8px; border-color: #FFF; color: #FFF;">Tutor Login</a>
    </div>
</div>

<div class="section-header mt-4">
    <h2>Why Teach With Us?</h2>
    <p>Think & Tinker tutors don't just teach — they inspire.</p>
</div>

<div class="teacher-grid">
    <div class="teacher-card"><div class="tc-icon"><?= ttIcon('home', 'tt-icon', 40) ?></div><h3>Home Tutorials</h3><p>Travel to families across Lagos for personalized 1-on-1 or small group sessions.</p></div>
    <div class="teacher-card orange"><div class="tc-icon"><?= ttIcon('tent', 'tt-icon', 40) ?></div><h3>Saturday Club</h3><p>Lead STEM experiments and reading circles at our Saturday Club sessions.</p></div>
    <div class="teacher-card navy"><div class="tc-icon"><?= ttIcon('smartphone', 'tt-icon', 40) ?></div><h3>Digital Tools</h3><p>Submit session notes via voice-to-text, track student progress, and communicate with parents — all from your phone.</p></div>
</div>

<div class="card card-flat" style="max-width: 640px; margin: var(--space-4) auto;">
    <div class="card-body">
        <h3 class="mb-2">What We Offer</h3>
        <ul class="benefit-list">
            <li><span class="check">✓</span> Competitive session rates paid monthly</li>
            <li><span class="check">✓</span> Flexible scheduling — you choose your availability</li>
            <li><span class="check">✓</span> Teaching materials and curriculum provided</li>
            <li><span class="check">✓</span> Professional development and training</li>
            <li><span class="check">✓</span> Official Think & Tinker staff ID badge</li>
            <li><span class="check">✓</span> Supportive team environment</li>
        </ul>
        <div style="text-align: center; margin-top: 20px;">
            <a href="<?= APP_URL ?>/teacher/apply.php" class="btn btn-primary">Apply Now</a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer-teacher.php'; ?>
