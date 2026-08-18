<?php
$pageTitle = 'Apply as Tutor';
require_once __DIR__ . '/../includes/config.php';
include __DIR__ . '/../templates/header-teacher.php';
?>

<div class="apply-form">
    <div style="text-align:center;margin-bottom:24px;">
        <h2>Apply to Join Think & Tinker</h2>
        <p class="text-muted">Fill in your details and we'll be in touch within 48 hours.</p>
    </div>

    <div class="card"><div class="card-body">
        <form id="applyForm">
            <input type="hidden" name="action" value="submit_inquiry">
            <input type="hidden" name="inquiry_type" value="tutor_application">
            <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
            <div class="form-row"><div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-input" required></div><div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div></div>
            <div class="form-group"><label class="form-label">Phone *</label><input type="tel" name="phone" class="form-input" required></div>
            <div class="form-group"><label class="form-label">Tell us about yourself *</label><textarea name="message" class="form-textarea" rows="5" placeholder="Your teaching experience, subjects you can teach, availability, and why you'd like to join Think & Tinker..." required></textarea></div>
            <button type="submit" class="btn btn-primary btn-block">Submit Application</button>
        </form>
    </div></div>
</div>

<script>
$('#applyForm').on('submit',function(e){e.preventDefault();TT.submitForm('#applyForm','ContactController.php',{onSuccess:r=>{if(r.success){TT.toast('Application submitted! We\'ll be in touch soon.','success',6000);TT.clearForm('#applyForm');}}});});
</script>
<?php include __DIR__ . '/../templates/footer-teacher.php'; ?>
