<?php $pageTitle='STEM & Reading Club'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="page-hero page-hero--sunset" style="padding:56px 0;">
    <div class="container"><div style="font-size:3rem;margin-bottom:12px;">🧪</div><h1 style="font-size:1.875rem;">STEM & Reading Club</h1></div>
</section>
<section class="section"><div class="container" style="max-width:860px;">
    <img src="<?= APP_URL ?>/assets/img/hero/stem-multiplication.jpg" alt="Child exploring a hands-on STEM multiplication puzzle" style="width:100%;border-radius:var(--radius-lg);margin-bottom:32px;box-shadow:var(--shadow-md);">
    <div style="font-size:1rem;line-height:1.8;color:#444;">Every Saturday, Think & Tinker transforms into a laboratory of wonder. Children aged 4–14 explore science experiments, coding, robotics, engineering challenges, creative writing, and guided reading — all in a structured, fun environment.<br><br>Our STEM Club isn't just about learning facts — it's about building the mindset of an inventor, a scientist, a storyteller. Hands-on projects, team challenges, and the joy of discovery keep children coming back week after week.<br><br><strong>Plans available:</strong> Trial (1 Saturday), Monthly (4 Saturdays), Quarterly (12 Saturdays), Bi-Annual (24 Saturdays).</div>
    <div style="text-align:center;margin-top:32px;">
        <a href="<?= APP_URL ?>/parent/register.php" class="btn btn-primary btn-lg">Join the Club →</a>
        <a href="<?= whatsappLink("Hi! I'm interested in STEM & Reading Club at Think & Tinker.") ?>" target="_blank" class="btn btn-outline btn-lg" style="margin-left:8px;">💬 Ask Us</a>
    </div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
