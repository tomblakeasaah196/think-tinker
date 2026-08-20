<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
if (getCurrentUser()) { header('Location: ' . APP_URL . '/parent/'); exit; }

// Handle activation
if (!empty($_GET['activate'])) {
    if (activateAccount($_GET['activate'])) { $activateMsg = 'Account activated! You can now log in.'; }
    else { $activateErr = 'Invalid or expired activation link.'; }
}
$pageTitle = 'Parent Login';
$pageDescription = "Log in to the Think & Tinker Parent Portal to track sessions, chat with tutors, view invoices, and follow your child's learning journey.";
require_once __DIR__ . '/../templates/header.php';
?>
<section class="page-hero page-hero--teal auth-hero">
    <div class="container">
        <div class="section-tag">Parent Portal</div>
        <h1>Welcome Back</h1>
        <p>Log in to track sessions, chat with tutors, and follow your child's learning journey.</p>

        <div class="auth-card fade-in">
            <?php if (!empty($activateMsg)): ?><div class="alert alert-success mb-2"><?= $activateMsg ?></div><?php endif; ?>
            <?php if (!empty($activateErr)): ?><div class="alert alert-danger mb-2"><?= $activateErr ?></div><?php endif; ?>

            <?php if (!empty($_GET['reset']) && !empty($_GET['email'])): ?>
            <!-- Password Reset Form -->
            <h2 class="auth-card-title">Reset Your Password</h2>
            <form id="newPasswordForm">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['reset']) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']) ?>">

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-input" required autofocus minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirm" class="form-input" required minlength="8">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
            <div class="auth-card-footer">
                <a href="<?= APP_URL ?>/parent/login.php">Back to Log In</a>
            </div>
            <?php else: ?>
            <!-- Standard Login Form -->
            <div class="auth-card-brand">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think & Tinker" class="auth-card-logo logo-light">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full-dark.png" alt="" aria-hidden="true" class="auth-card-logo logo-dark">
            </div>
            <form id="loginForm">
                <input type="hidden" name="action" value="login"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required autofocus></div>
                <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" required></div>
                <button type="submit" class="btn btn-primary btn-block">Log In</button>
            </form>
            <div class="auth-card-footer">
                <a href="#" id="forgotLink">Forgot password?</a><br>
                <span class="auth-card-muted">Don't have an account?</span> <a href="<?= APP_URL ?>/parent/register.php" class="auth-card-strong">Register</a>
                <span class="auth-card-muted" style="margin: 0 8px;">or</span><a href="<?= APP_URL ?>/parent/register-club.php" class="auth-card-strong auth-card-strong--orange">Join Club</a>
            </div>
            <div id="forgotForm" class="auth-card-forgot">
                <form id="resetForm"><input type="hidden" name="action" value="forgot_password"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <div class="form-group"><input type="email" name="email" class="form-input" placeholder="your@email.com" required></div>
                <button type="submit" class="btn btn-outline btn-block">Send Reset Link</button></form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#loginForm').on('submit',function(e){e.preventDefault();TT.submitForm('#loginForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast('Welcome back!','success');setTimeout(()=>location.href=r.data.redirect||'<?= APP_URL ?>/parent/',800);}}});});
$('#forgotLink').on('click',function(e){e.preventDefault();$('#forgotForm').slideToggle(200);});
$('#resetForm').on('submit',function(e){e.preventDefault();TT.submitForm('#resetForm','AuthController.php',{onSuccess:r=>{if(r.success)TT.toast(r.message,'success',6000);}});});
$('#newPasswordForm').on('submit',function(e){e.preventDefault();TT.submitForm('#newPasswordForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast(r.message,'success',6000);setTimeout(()=>location.href='<?= APP_URL ?>/parent/login.php',1500);}}});});
</script>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
