<?php $pageTitle='Educational Consulting'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="section" style="background:#1B2A4A;color:#FFF;padding:60px 0;text-align:center;">
    <div class="container"><div style="font-size:3rem;margin-bottom:12px;">🎯</div><h1 style="color:#FFF;">Educational Consulting</h1></div>
</section>
<section class="section"><div class="container" style="max-width:860px;">
    <img src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=860&q=80" alt="Educational Consulting" style="width:100%;border-radius:var(--radius-lg);margin-bottom:32px;box-shadow:var(--shadow-md);">
    <div style="font-size:1rem;line-height:1.8;color:#444;">Making the right educational decisions for your child can be overwhelming. Our consulting service provides expert guidance from experienced educators who understand the Nigerian educational landscape.<br><br><strong>For Parents:</strong> school selection advice, curriculum guidance, learning assessments, gifted child support, special needs navigation, and exam preparation strategies.<br><br><strong>For Schools:</strong> curriculum development, teacher training, STEM program setup, after-school program design, and quality assurance consulting.</div>
    <div style="text-align:center;margin-top:32px;">
        <a href="<?= APP_URL ?>/parent/register.php" class="btn btn-primary btn-lg">Book a Consultation →</a>
        <a href="<?= whatsappLink("Hi! I'm interested in Educational Consulting at Think & Tinker.") ?>" target="_blank" class="btn btn-outline btn-lg" style="margin-left:8px;">💬 Ask Us</a>
    </div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
