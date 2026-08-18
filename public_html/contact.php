<?php $pageTitle='Contact Us'; require_once __DIR__.'/includes/config.php'; include __DIR__.'/templates/header.php'; ?>
<section class="page-hero page-hero--teal" style="padding:56px 0;">
    <div class="container"><h1>Get in Touch</h1><p>We'd love to hear from you. Reach out anytime.</p></div>
</section>
<section class="section"><div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;">
    <div>
        <h2 class="mb-3">Send Us a Message</h2>
        <div class="card"><div class="card-body">
            <form id="contactForm"><input type="hidden" name="action" value="submit_inquiry"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <div class="form-group"><label class="form-label">Your Name *</label><input type="text" name="name" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input"></div>
                <div class="form-group"><label class="form-label">I'm interested in</label><select name="inquiry_type" class="form-select"><option value="general">General Inquiry</option><option value="tutorials">Home Tutorials</option><option value="stem_club">STEM & Reading Club</option><option value="consulting">Educational Consulting</option><option value="school_partnership">School Partnership</option><option value="bookstore">Bookstore</option></select></div>
                <div class="form-group"><label class="form-label">Message *</label><textarea name="message" class="form-textarea" rows="5" required></textarea></div>
                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        </div></div>
    </div>
    <div>
        <h2 class="mb-3">Other Ways to Reach Us</h2>
        <div class="card mb-2"><div class="card-body"><h3 style="font-size:1rem;margin-bottom:8px;color:var(--text-heading);">📍 Visit Us</h3><p style="font-size:.9375rem;color:var(--text-muted-c);"><?= htmlspecialchars(getSetting('business_address','Lagos, Nigeria')) ?></p></div></div>
        <div class="card mb-2"><div class="card-body"><h3 style="font-size:1rem;margin-bottom:8px;color:var(--text-heading);">📞 Call Us</h3><p style="font-size:.9375rem;"><a href="tel:<?= htmlspecialchars(getSetting('business_phone','')) ?>"><?= htmlspecialchars(getSetting('business_phone','')) ?></a></p></div></div>
        <div class="card mb-2"><div class="card-body"><h3 style="font-size:1rem;margin-bottom:8px;color:var(--text-heading);">✉️ Email</h3><p style="font-size:.9375rem;"><a href="mailto:<?= htmlspecialchars(getSetting('business_email','')) ?>"><?= htmlspecialchars(getSetting('business_email','')) ?></a></p></div></div>
        <a href="<?= whatsappLink("Hello! I'd like to learn more about Think & Tinker.") ?>" target="_blank" class="btn btn-success btn-block btn-lg" style="margin-top:12px;">💬 Chat on WhatsApp</a>
    </div>
</div></section>
<style>@media(max-width:768px){.section > .container[style*="grid-template-columns"]{grid-template-columns:1fr !important;}}</style>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#contactForm').on('submit',function(e){e.preventDefault();TT.submitForm('#contactForm','ContactController.php',{onSuccess:r=>{if(r.success){TT.toast(r.message,'success',5000);TT.clearForm('#contactForm');}}});});
</script>
<?php include __DIR__.'/templates/footer.php'; ?>
