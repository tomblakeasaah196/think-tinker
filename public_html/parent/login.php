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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In — Think & Tinker</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>body{background:linear-gradient(135deg,#1AAFA0 0%,#138D80 100%);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}.login-card{background:#FFF;border-radius:var(--radius-lg);padding:40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.2);}.login-logo{text-align:center;margin-bottom:24px;}.login-logo h1{font-family:'Quicksand',sans-serif;color:var(--deep-navy);font-size:1.5rem;}.login-logo p{color:#999;font-size:0.8125rem;}</style>
</head>
<body>
<div class="login-card fade-in">
    <div class="login-logo">
        <img src="<?= APP_URL ?>/assets/img/logo/logo_tnt_portrait.png" alt="Think & Tinker" style="max-width:120px;margin:0 auto 16px;" onerror="this.style.display='none'">
        <h1>Parent Portal</h1>
        <p>Access your child's learning journey</p>
    </div>
    <?php if (!empty($activateMsg)): ?><div class="alert alert-success mb-2"><?= $activateMsg ?></div><?php endif; ?>
    <?php if (!empty($activateErr)): ?><div class="alert alert-danger mb-2"><?= $activateErr ?></div><?php endif; ?>

    <?php if (!empty($_GET['reset']) && !empty($_GET['email'])): ?>
    <!-- Password Reset Form -->
    <h2 style="text-align:center; margin-bottom: 20px;">Reset Your Password</h2>
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
    <div style="text-align:center;margin-top:16px;font-size:0.8125rem;">
        <a href="<?= APP_URL ?>/parent/login.php" style="color:var(--tinker-teal);">Back to Log In</a>
    </div>
    <?php else: ?>
    <!-- Standard Login Form -->
    <form id="loginForm">
        <input type="hidden" name="action" value="login"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required autofocus></div>
        <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" required></div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>
    <div style="text-align:center;margin-top:16px;font-size:0.8125rem;">
        <a href="#" id="forgotLink" style="color:var(--tinker-teal);">Forgot password?</a><br>
        <span style="color:#999;">Don't have an account?</span> <a href="<?= APP_URL ?>/parent/register.php" style="color:var(--tinker-teal);font-weight:700;">Register</a>
        <span style="color:#999;margin:0 8px;">or</span><a href="<?= APP_URL ?>/parent/register-club.php" style="color:var(--spark-orange);font-weight:700;">Join Club</a>
    </div>
    <div id="forgotForm" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--cloud-gray);">
        <form id="resetForm"><input type="hidden" name="action" value="forgot_password"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="form-group"><input type="email" name="email" class="form-input" placeholder="your@email.com" required></div>
        <button type="submit" class="btn btn-outline btn-block">Send Reset Link</button></form>
    </div>
    <?php endif; ?>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#loginForm').on('submit',function(e){e.preventDefault();TT.submitForm('#loginForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast('Welcome back!','success');setTimeout(()=>location.href=r.data.redirect||'<?= APP_URL ?>/parent/',800);}}});});
$('#forgotLink').on('click',function(e){e.preventDefault();$('#forgotForm').slideToggle(200);});
$('#resetForm').on('submit',function(e){e.preventDefault();TT.submitForm('#resetForm','AuthController.php',{onSuccess:r=>{if(r.success)TT.toast(r.message,'success',6000);}});});
$('#newPasswordForm').on('submit',function(e){e.preventDefault();TT.submitForm('#newPasswordForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast(r.message,'success',6000);setTimeout(()=>location.href='<?= APP_URL ?>/parent/login.php',1500);}}});});
</script>
</body></html>
