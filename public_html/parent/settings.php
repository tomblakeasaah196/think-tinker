<?php $pageTitle='Settings'; $currentTab='home'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<h2 class="mb-3" style="font-size:1.25rem;">Account Settings</h2>
<div class="card mb-3"><div class="card-body">
    <h3 class="mb-2" style="font-size:1rem;">Profile</h3>
    <form id="profileForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="form-row"><div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" class="form-input" value="<?= htmlspecialchars($user['first_name']) ?>"></div><div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" class="form-input" value="<?= htmlspecialchars($user['last_name']) ?>"></div></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($user['phone']) ?>"></div>
        <div class="form-group">
            <label class="form-label">Profile Photo</label>
            <div class="flex items-center gap-2" style="margin-bottom:8px;">
                <div id="profilePhotoPreview" class="avatar avatar-lg avatar-teal">
                    <?php if (!empty($user['profile_photo'])): ?>
                        <img src="<?= htmlspecialchars(getUploadUrl($user['profile_photo'])) ?>" alt="" class="avatar-photo">
                    <?php else: ?>
                        <?= strtoupper(substr($user['first_name'],0,1) . substr($user['last_name'],0,1)) ?>
                    <?php endif; ?>
                </div>
                <input type="file" name="profile_photo" id="profilePhotoInput" class="form-input" accept="image/*" style="flex:1;">
            </div>
            <p class="text-muted text-sm">JPEG, PNG or WebP, up to 8MB.</p>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Save Profile</button>
    </form>
</div></div>
<div class="card"><div class="card-body">
    <h3 class="mb-2" style="font-size:1rem;">Change Password</h3>
    <form id="passwordForm">
        <input type="hidden" name="action" value="change_password"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="form-group"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-input" required></div>
        <div class="form-row"><div class="form-group"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-input" required minlength="8"></div><div class="form-group"><label class="form-label">Confirm</label><input type="password" name="new_password_confirm" class="form-input" required></div></div>
        <button type="submit" class="btn btn-primary btn-sm">Change Password</button>
    </form>
</div></div>
<script>
// Instant preview the moment a photo is picked — before it's even
// uploaded — so choosing a file always visibly does something.
$('#profilePhotoInput').on('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => $('#profilePhotoPreview').html(`<img src="${e.target.result}" class="avatar-photo" alt="">`);
    reader.readAsDataURL(file);
});
$('#profileForm').on('submit',function(e){
    e.preventDefault();
    TT.submitForm('#profileForm','ParentController.php',{onSuccess:r=>{
        // r.success can be false here specifically when the photo failed
        // to upload (see ParentController::updateProfile) — TT.api already
        // shows that error as a toast, so there's nothing more to do here.
        if (r.success) { TT.toast('Profile updated!','success'); setTimeout(()=>location.reload(), 900); }
    }});
});
$('#passwordForm').on('submit',function(e){e.preventDefault();TT.submitForm('#passwordForm','AuthController.php',{onSuccess:r=>{if(r.success){TT.toast(r.message,'success');TT.clearForm('#passwordForm');}}});});
</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
