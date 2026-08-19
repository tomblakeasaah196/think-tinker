<?php $pageTitle='My Profile'; $currentModule='profile'; require_once __DIR__.'/../templates/header-hub.php'; ?>
<div class="page-header"><h1>My Profile</h1></div>

<div class="hub-card" id="incompleteBanner" style="display:none; border-left:4px solid var(--spark-orange); margin-bottom:16px;">
    <div class="hub-card-body" style="display:flex; align-items:center; gap:14px;">
        <span class="icon-badge" style="background:rgba(244,123,32,0.12); color:var(--spark-orange); flex-shrink:0;"><?= ttIcon('star', 'tt-icon', 18) ?></span>
        <div><strong>Your profile is missing a few things.</strong><div id="incompleteList" style="font-size:0.8125rem; color:#666; margin-top:2px;"></div></div>
    </div>
</div>

<div class="hub-card mb-3">
    <div class="hub-card-header"><h3><?= ttIcon('camera', 'tt-icon', 16) ?> Profile</h3></div>
    <div class="hub-card-body">
        <div id="profilePhotoPreview" style="margin-bottom:18px;"><div class="spinner"></div></div>
        <form id="profileForm" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
            <div class="form-row">
                <div class="form-group"><label class="form-label">First Name</label><input type="text" name="first_name" id="fFirstName" class="form-input"></div>
                <div class="form-group"><label class="form-label">Last Name</label><input type="text" name="last_name" id="fLastName" class="form-input"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="fEmail" class="form-input" disabled style="opacity:0.6;">
                    <div style="font-size:0.75rem; color:#999; margin-top:4px;">This is your login email — ask an admin in Hub &gt; Staff to change it.</div>
                </div>
                <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" id="fPhone" class="form-input" placeholder="+234..."></div>
            </div>
            <div class="form-group"><label class="form-label">Profile Photo</label><input type="file" name="profile_photo" class="form-input" accept="image/*"></div>
            <button type="submit" class="btn btn-primary btn-sm">Save Profile</button>
        </form>
    </div>
</div>

<div class="hub-card mb-3">
    <div class="hub-card-header"><h3><?= ttIcon('lock', 'tt-icon', 16) ?> Change Password</h3></div>
    <div class="hub-card-body">
        <form id="passwordForm">
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
            <div class="form-group"><label class="form-label">Current Password <span class="required">*</span></label><input type="password" name="current_password" class="form-input" required></div>
            <div class="form-row">
                <div class="form-group"><label class="form-label">New Password <span class="required">*</span></label><input type="password" name="new_password" id="fNewPw" class="form-input" required minlength="8"></div>
                <div class="form-group"><label class="form-label">Confirm New Password <span class="required">*</span></label><input type="password" name="new_password_confirm" id="fNewPwConfirm" class="form-input" required minlength="8"></div>
            </div>
            <div style="font-size:0.75rem; color:#999; margin:-8px 0 14px;">At least 8 characters.</div>
            <button type="submit" class="btn btn-primary btn-sm">Change Password</button>
        </form>
    </div>
</div>

<div class="hub-card" id="employmentCard" style="display:none;">
    <div class="hub-card-header"><h3><?= ttIcon('id-badge', 'tt-icon', 16) ?> Employment</h3></div>
    <div class="hub-card-body">
        <div class="form-row">
            <div><div style="font-size:0.75rem; color:#999;">Staff ID</div><div id="eStaffId" style="font-weight:700; margin-top:2px;">—</div></div>
            <div><div style="font-size:0.75rem; color:#999;">Job Title</div><div id="eJobTitle" style="font-weight:700; margin-top:2px;">—</div></div>
        </div>
        <div class="form-row" style="margin-top:14px;">
            <div><div style="font-size:0.75rem; color:#999;">Employment Type</div><div id="eEmpType" style="font-weight:700; margin-top:2px;">—</div></div>
            <div><div style="font-size:0.75rem; color:#999;">Hire Date</div><div id="eHireDate" style="font-weight:700; margin-top:2px;">—</div></div>
        </div>
        <div style="font-size:0.75rem; color:#999; margin-top:16px;">These details are set by an admin in Hub &gt; Staff. Let one know if anything here needs correcting.</div>
    </div>
</div>

<script>
function loadMyProfile() {
    TT.get('AuthController.php', { action: 'get_current_user' }).done(function(r) {
        if (!r.success) return;
        const d = r.data;
        $('#fFirstName').val(d.first_name);
        $('#fLastName').val(d.last_name);
        $('#fEmail').val(d.email);
        $('#fPhone').val(d.phone || '');

        const initials = TT.escHtml(((d.first_name || ' ')[0] + (d.last_name || ' ')[0]).toUpperCase());
        const photoHtml = d.photo
            ? `<img src="${d.photo}" alt="" style="width:80px; height:80px; border-radius:50%; object-fit:cover; display:block;">`
            : `<div class="avatar avatar-xl avatar-teal">${initials}</div>`;
        $('#profilePhotoPreview').html(photoHtml);

        const missing = [];
        if (!d.phone) missing.push('phone number');
        if (!d.photo) missing.push('profile photo');
        if (missing.length) {
            $('#incompleteList').text('Add your ' + missing.join(' and ') + ' below to finish setting up your account.');
            $('#incompleteBanner').show();
        } else {
            $('#incompleteBanner').hide();
        }
    });

    TT.get('StaffController.php', { action: 'get_my_staff_info' }).done(function(r) {
        if (!r.success || !r.data.staff_record) return;
        const s = r.data.staff_record;
        $('#eStaffId').text(s.staff_id_number || '—');
        $('#eJobTitle').text(s.job_title || '—');
        $('#eEmpType').text(s.employment_type ? s.employment_type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()) : '—');
        $('#eHireDate').text(s.hire_date || '—');
        $('#employmentCard').show();
    });
}

$('#profileForm').on('submit', function(e) {
    e.preventDefault();
    TT.submitForm('#profileForm', 'AuthController.php', {
        onSuccess: r => {
            if (r.success) { TT.toast('Profile updated!', 'success'); loadMyProfile(); }
        }
    });
});

$('#passwordForm').on('submit', function(e) {
    e.preventDefault();
    if ($('#fNewPw').val() !== $('#fNewPwConfirm').val()) {
        TT.toast('New passwords do not match.', 'error');
        return;
    }
    TT.submitForm('#passwordForm', 'AuthController.php', {
        onSuccess: r => {
            if (r.success) { TT.toast(r.message, 'success'); TT.clearForm('#passwordForm'); }
        }
    });
});

$(function() { loadMyProfile(); });
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
