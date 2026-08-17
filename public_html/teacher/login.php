<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
if (getCurrentUser()) { header('Location: ' . HUB_URL . '/sessions.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutor Login — Think & Tinker</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>body{background:var(--deep-navy);display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}.login-card{background:#FFF;border-radius:var(--radius-lg);padding:40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.3);}</style>
</head>
<body>
<div class="login-card fade-in">
    <div style="text-align:center;margin-bottom:24px;">
        <h2 style="color:var(--deep-navy);">Tutor Login</h2>
        <p style="color:#999;font-size:0.8125rem;">Access your sessions and notes</p>
    </div>
    <form id="loginForm">
        <input type="hidden" name="action" value="login"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required autofocus></div>
        <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" required></div>
        <button type="submit" class="btn btn-navy btn-block">Log In</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:0.8125rem;">Want to join? <a href="<?= APP_URL ?>/teacher/apply.php" style="color:var(--spark-orange);font-weight:700;">Apply as Tutor</a></p>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#loginForm').on('submit',function(e){e.preventDefault();TT.submitForm('#loginForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast('Welcome!','success');setTimeout(()=>location.href=r.data.redirect||'<?= HUB_URL ?>/sessions.php',800);}}});});
</script>
</body></html>
