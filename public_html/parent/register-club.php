<?php
$pageTitle = 'Join STEM Club';
$pageDescription = 'Register your child for the Think & Tinker Saturday STEM & Reading Club — hands-on science, coding, robotics, and story time for ages 4 to 14.';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../templates/header.php';
?>
<section class="page-hero page-hero--sunset auth-hero">
    <div class="container">
        <div class="section-tag">STEM & Reading Club</div>
        <h1>Join STEM & Reading Club</h1>
        <p>Saturdays at Think & Tinker — ages 4 to 14. Hands-on science and engineering projects, combined with reading sessions to foster curiosity and build a lifelong love for learning.</p>
        <a href="<?= APP_URL ?>/services/stem-club.php" class="auth-hero-link">Learn more about our STEM Club →</a>

        <div class="auth-card auth-card--wide fade-in">
            <div class="auth-card-brand">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think & Tinker" class="auth-card-logo logo-light">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full-dark.png" alt="" aria-hidden="true" class="auth-card-logo logo-dark">
            </div>
            <form id="clubRegForm">
                <input type="hidden" name="action" value="register_club"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <h4 class="mb-2 auth-card-section-title">Your Details</h4>
                <div class="form-row"><div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-input" required></div></div>
                <div class="form-row"><div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div><div class="form-group"><label class="form-label">Phone *</label><input type="tel" name="phone" class="form-input" required></div></div>
                <div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" class="form-input" required minlength="8"></div>
                <h4 class="mt-3 mb-2 auth-card-section-title">Child's Details</h4>
                <div class="form-row"><div class="form-group"><label class="form-label">Child First Name *</label><input type="text" name="child_first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Child Last Name</label><input type="text" name="child_last_name" class="form-input"></div></div>
                <div class="form-row"><div class="form-group"><label class="form-label">Date of Birth *</label><input type="date" name="child_dob" class="form-input" required></div><div class="form-group"><label class="form-label">Gender</label><select name="child_gender" class="form-select"><option value="male">Male</option><option value="female">Female</option></select></div></div>
                <div class="form-group"><label class="form-label">Medical Notes / Allergies</label><textarea name="medical_notes" class="form-textarea" rows="2" placeholder="Optional"></textarea></div>
                <h4 class="mt-3 mb-2 auth-card-section-title">Choose a Plan</h4>
                <div class="form-group">
                    <div class="radio-group"><input type="radio" name="plan" value="trial" id="planTrial" checked><label for="planTrial">Trial — 1 Saturday (₦8,000)</label></div>
                    <div class="radio-group"><input type="radio" name="plan" value="monthly" id="planMonthly"><label for="planMonthly">Monthly — 4 Saturdays (₦30,000)</label></div>
                    <div class="radio-group"><input type="radio" name="plan" value="quarterly" id="planQuarterly"><label for="planQuarterly">Quarterly — 12 Saturdays (₦85,000)</label></div>
                    <div class="radio-group"><input type="radio" name="plan" value="biannual" id="planBiannual"><label for="planBiannual">Bi-Annual — 24 Saturdays (₦165,000)</label></div>
                </div>
                <button type="submit" class="btn btn-secondary btn-block btn-lg" style="margin-top:8px;">Register & Join Club</button>
            </form>
            <div class="auth-card-footer">Already registered? <a href="<?= APP_URL ?>/parent/login.php" class="auth-card-strong">Log In</a></div>
        </div>
    </div>
</section>

<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#clubRegForm').on('submit',function(e){e.preventDefault();TT.submitForm('#clubRegForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast(r.message,'success');setTimeout(()=>location.href=r.data.redirect||'<?= APP_URL ?>/parent/payments.php',1200);}}});});
</script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
