<?php $pageTitle='Join STEM Club'; require_once __DIR__.'/../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join STEM Club — Think & Tinker</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/parent.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>body{background:linear-gradient(135deg,var(--spark-orange),#e06810);min-height:100vh;padding:20px;display:flex;align-items:center;justify-content:center;}.register-card{background:#FFF;border-radius:var(--radius-lg);padding:40px;width:100%;max-width:560px;box-shadow:0 20px 60px rgba(0,0,0,0.2);}</style>
</head>
<body>
<div class="register-card fade-in">
    <div style="text-align:center;margin-bottom:24px;">
        <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think & Tinker Logo" style="height:42px;margin-bottom:12px;">
        <h2 style="color:var(--deep-navy);">Join STEM & Reading Club</h2>
        <p style="color:#666;font-size:0.875rem;margin-bottom:8px;">Saturdays at Think & Tinker — ages 4 to 14</p>
        <p style="color:#555;font-size:0.8125rem;margin-bottom:8px;">Our club provides engaging, hands-on science and engineering projects, combined with reading sessions to foster curiosity and build a lifelong love for learning.</p>
        <a href="<?= APP_URL ?>/services/stem-club.php" style="color:var(--tinker-teal);font-size:0.8125rem;font-weight:700;text-decoration:underline;">Learn more about our STEM Club</a>
    </div>
    <form id="clubRegForm">
        <input type="hidden" name="action" value="register_club"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <h4 class="mb-2" style="color:var(--tinker-teal);">Your Details</h4>
        <div class="form-row"><div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-input" required></div></div>
        <div class="form-row"><div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div><div class="form-group"><label class="form-label">Phone *</label><input type="tel" name="phone" class="form-input" required></div></div>
        <div class="form-group"><label class="form-label">Password *</label><input type="password" name="password" class="form-input" required minlength="8"></div>
        <h4 class="mt-3 mb-2" style="color:var(--tinker-teal);">Child's Details</h4>
        <div class="form-row"><div class="form-group"><label class="form-label">Child First Name *</label><input type="text" name="child_first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Child Last Name</label><input type="text" name="child_last_name" class="form-input"></div></div>
        <div class="form-row"><div class="form-group"><label class="form-label">Date of Birth *</label><input type="date" name="child_dob" class="form-input" required></div><div class="form-group"><label class="form-label">Gender</label><select name="child_gender" class="form-select"><option value="male">Male</option><option value="female">Female</option></select></div></div>
        <div class="form-group"><label class="form-label">Medical Notes / Allergies</label><textarea name="medical_notes" class="form-textarea" rows="2" placeholder="Optional"></textarea></div>
        <h4 class="mt-3 mb-2" style="color:var(--tinker-teal);">Choose a Plan</h4>
        <div class="form-group">
            <div class="radio-group"><input type="radio" name="plan" value="trial" id="planTrial" checked><label for="planTrial">Trial — 1 Saturday (₦8,000)</label></div>
            <div class="radio-group"><input type="radio" name="plan" value="monthly" id="planMonthly"><label for="planMonthly">Monthly — 4 Saturdays (₦30,000)</label></div>
            <div class="radio-group"><input type="radio" name="plan" value="quarterly" id="planQuarterly"><label for="planQuarterly">Quarterly — 12 Saturdays (₦85,000)</label></div>
            <div class="radio-group"><input type="radio" name="plan" value="biannual" id="planBiannual"><label for="planBiannual">Bi-Annual — 24 Saturdays (₦165,000)</label></div>
        </div>
        <button type="submit" class="btn btn-secondary btn-block btn-lg" style="margin-top:8px;">Register & Join Club</button>
    </form>
    <div style="text-align:center;margin-top:16px;">
        <p style="font-size:0.8125rem;">Already registered? <a href="<?= APP_URL ?>/parent/login.php" style="color:var(--tinker-teal);font-weight:700;text-decoration:underline;">Log In</a></p>
    </div>
</div>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';
$('#clubRegForm').on('submit',function(e){e.preventDefault();TT.submitForm('#clubRegForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast(r.message,'success');setTimeout(()=>location.href=r.data.redirect||'<?= APP_URL ?>/parent/payments.php',1200);}}});});
</script>
</body></html>
