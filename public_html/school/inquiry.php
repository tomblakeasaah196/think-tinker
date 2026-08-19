<?php $pageTitle='School Inquiry'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="section" style="background:var(--tinker-teal);color:#FFF;padding:60px 0;text-align:center;">
    <div class="container"><h1 style="color:#FFF;">Partner With Us</h1><p style="opacity:.8;">Tell us about your school and we'll design a custom proposal within 48 hours.</p></div>
</section>
<section class="section"><div class="container" style="max-width:640px;">
    <div class="card"><div class="card-body">
        <form id="schoolForm">
            <input type="hidden" name="action" value="submit_inquiry"><input type="hidden" name="inquiry_type" value="school_proposal"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
            <div class="form-group"><label class="form-label">Contact Name *</label><input type="text" name="name" class="form-input" required></div>
            <div class="form-group"><label class="form-label">School Name *</label><input type="text" name="school_name" class="form-input" required></div>
            <div class="form-row"><div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div><div class="form-group"><label class="form-label">Phone *</label><input type="tel" name="phone" class="form-input" required></div></div>
            <div class="form-group"><label class="form-label">What are you looking for? *</label><textarea name="message" class="form-textarea" rows="5" placeholder="e.g. STEM workshop for 100 Primary 4-6 students, teacher training on modern pedagogy, after-school program setup..." required></textarea></div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Submit Inquiry</button>
        </form>
    </div></div>
</div></section>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#schoolForm').on('submit',function(e){e.preventDefault();TT.submitForm('#schoolForm','ContactController.php',{onSuccess:r=>{if(r.success){TT.toast('Inquiry submitted! We\'ll be in touch within 48 hours.','success',6000);TT.clearForm('#schoolForm');}}});});
</script>
<?php include __DIR__.'/../templates/footer.php'; ?>
