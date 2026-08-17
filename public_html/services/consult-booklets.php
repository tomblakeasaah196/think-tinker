<?php $pageTitle='Consult Booklets'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="page-hero" style="padding:56px 0;background:linear-gradient(135deg,#27AE60,#1e8449);">
    <div class="container"><div style="font-size:3rem;margin-bottom:12px;">📖</div><h1 style="font-size:1.875rem;">Consult Booklets</h1></div>
</section>
<section class="section"><div class="container" style="max-width:860px;">
    <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=860&q=80" alt="Consult Booklets" style="width:100%;border-radius:var(--radius-lg);margin-bottom:32px;box-shadow:var(--shadow-md);">
    <div style="font-size:1rem;line-height:1.8;color:#444;">Our Consult Booklets are carefully designed learning resources for self-paced study at home. Each booklet is age-appropriate, curriculum-aligned, and packed with engaging activities that make learning fun.<br><br><strong>Categories:</strong> Early Years (ages 1–4), Foundation (ages 4–6), Primary (ages 6–10), Pre-Teen (ages 10–14). Subjects include literacy, numeracy, science, critical thinking, and creative arts.<br><br>Available in print from our bookstore or as digital downloads.</div>
    <div style="text-align:center;margin-top:32px;">
        <a href="<?= APP_URL ?>/parent/register.php" class="btn btn-primary btn-lg">Browse Booklets →</a>
        <a href="<?= whatsappLink("Hi! I'm interested in Consult Booklets at Think & Tinker.") ?>" target="_blank" class="btn btn-outline btn-lg" style="margin-left:8px;">💬 Ask Us</a>
    </div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
