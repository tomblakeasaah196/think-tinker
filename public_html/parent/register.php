<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
if (getCurrentUser()) { header('Location: ' . APP_URL . '/parent/'); exit; }
$pageTitle = 'Register';
$pageDescription = 'Create your Think & Tinker parent account to manage your child\'s tutorials, STEM Club membership, and learning journey.';
require_once __DIR__ . '/../templates/header.php';
?>
<section class="page-hero page-hero--teal auth-hero">
    <div class="container">
        <div class="section-tag">Parent Portal</div>
        <h1>Create Your Account</h1>
        <p>Join Think & Tinker to manage your child's education.</p>

        <div class="auth-card fade-in">
            <div class="auth-card-brand">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think & Tinker" class="auth-card-logo logo-light">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full-dark.png" alt="" aria-hidden="true" class="auth-card-logo logo-dark">
            </div>
            <form id="regForm">
                <input type="hidden" name="action" value="register"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <div class="form-row"><div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-input" required></div></div>
                <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" placeholder="+234..."></div>
                <div class="form-row"><div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" class="form-input" required minlength="8"></div><div class="form-group"><label class="form-label">Confirm *</label><input type="password" name="password_confirm" class="form-input" required></div></div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Create Account</button>
            </form>
            <div class="auth-card-footer">Already have an account? <a href="<?= APP_URL ?>/parent/login.php" class="auth-card-strong">Log In</a></div>
        </div>
    </div>
</section>

<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#regForm').on('submit',function(e){e.preventDefault();TT.submitForm('#regForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast('Welcome!','success');setTimeout(()=>location.href=r.data.redirect||'<?= APP_URL ?>/parent/',800);}}});});
</script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
