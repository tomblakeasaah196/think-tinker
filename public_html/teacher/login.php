<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
if (getCurrentUser()) { header('Location: ' . HUB_URL . '/sessions.php'); exit; }
$pageTitle = 'Tutor Login';
require_once __DIR__ . '/../templates/header-teacher.php';
?>
<div class="teacher-hero">
    <div class="auth-card fade-in">
        <div class="auth-card-brand">
            <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think & Tinker" class="auth-card-logo">
        </div>
        <div class="auth-card-heading">
            <h2>Tutor Login</h2>
            <p>Access your sessions and notes</p>
        </div>
        <form id="loginForm">
            <input type="hidden" name="action" value="login"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
            <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required autofocus></div>
            <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-input" required></div>
            <button type="submit" class="btn btn-navy btn-block">Log In</button>
        </form>
        <p class="auth-card-footer">Want to join? <a href="<?= APP_URL ?>/teacher/apply.php" class="auth-card-strong auth-card-strong--orange">Apply as Tutor</a></p>
    </div>
</div>

<!-- The teacher portal never sets data-theme="dark" (no dark-mode toggle
     of its own — see header-teacher.php), so .auth-card always renders in
     its light variant here. It sits on .teacher-hero's navy gradient
     rather than .page-hero, since this page renders inside
     <main class="container"> like every other teacher/ page, not
     full-bleed like the public site. .auth-card is already centered via
     margin:0 auto + max-width, so no extra layout is needed. -->

<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#loginForm').on('submit',function(e){e.preventDefault();TT.submitForm('#loginForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast('Welcome!','success');setTimeout(()=>location.href=r.data.redirect||'<?= HUB_URL ?>/sessions.php',800);}}});});
</script>
<?php require_once __DIR__ . '/../templates/footer-teacher.php'; ?>
