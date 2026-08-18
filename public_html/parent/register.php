<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
if (getCurrentUser()) { header('Location: ' . APP_URL . '/parent/'); exit; }
$pageTitle = 'Register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Think & Tinker</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>body{background:linear-gradient(135deg,#1AAFA0,#138D80);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}.reg-card{background:#FFF;border-radius:var(--radius-lg);padding:40px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.2);}</style>
</head>
<body>
<div class="reg-card fade-in">
    <div style="text-align:center;margin-bottom:24px;">
        <h2 style="color:var(--deep-navy);">Create Your Account</h2>
        <p style="color:#999;font-size:0.8125rem;">Join Think & Tinker to manage your child's education</p>
    </div>
    <form id="regForm">
        <input type="hidden" name="action" value="register"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="form-row"><div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-input" required></div></div>
        <div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" placeholder="+234..."></div>
        <div class="form-row"><div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" class="form-input" required minlength="8"></div><div class="form-group"><label class="form-label">Confirm *</label><input type="password" name="password_confirm" class="form-input" required></div></div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Create Account</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:0.8125rem;">Already have an account? <a href="<?= APP_URL ?>/parent/login.php" style="color:var(--tinker-teal);font-weight:700;">Log In</a></p>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#regForm').on('submit',function(e){e.preventDefault();TT.submitForm('#regForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast('Welcome!','success');setTimeout(()=>location.href=r.data.redirect||'<?= APP_URL ?>/parent/',800);}}});});
</script>
</body></html>
