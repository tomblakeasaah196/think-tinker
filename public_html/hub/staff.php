<?php $pageTitle='Staff'; $currentModule='staff'; require_once __DIR__.'/../templates/header-hub.php'; ?>
<div class="page-header"><h1>Staff Management</h1><div class="actions"><button class="btn btn-primary btn-sm" onclick="TT.modal.open('#staffModal')">+ Add Staff</button></div></div>
<div class="tabs"><div class="tab active" data-tab="tabStaff">Staff List</div><div class="tab" data-tab="tabAttendance">Attendance</div></div>

<div class="tab-content active" id="tabStaff">
<div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Name</th><th>Role</th><th>Title</th><th>Type</th><th>Phone</th><th></th></tr></thead><tbody id="staffTable"></tbody></table></div></div>
</div>

<div class="tab-content" id="tabAttendance">
<div class="filter-bar"><input type="date" class="form-input" id="attDate" value="<?= date('Y-m-d') ?>" onchange="loadAttendance()" style="width:auto;"><button class="btn btn-primary btn-sm" onclick="saveAttendance()">Save Attendance</button></div>
<div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Staff</th><th>Title</th><th>Status</th><th>Clock In</th><th>Clock Out</th></tr></thead><tbody id="attTable"></tbody></table></div></div>
</div>

<div class="modal-overlay" id="staffModal"><div class="modal modal-lg">
<div class="modal-header"><h3>Add Staff Member</h3><span class="modal-close">&times;</span></div>
<div class="modal-body"><form id="staffForm"><input type="hidden" name="action" value="add_staff">
    <div class="form-row"><div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-input" required></div></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Email *</label><input type="email" name="email" class="form-input" required></div><div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input"></div></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Role</label><select name="user_type" class="form-select"><option value="tutor">Tutor</option><option value="staff">Staff</option><option value="admin">Admin</option></select></div><div class="form-group"><label class="form-label">Job Title</label><input type="text" name="job_title" class="form-input"></div></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Employment Type</label><select name="employment_type" class="form-select"><option value="contract">Contract</option><option value="full_time">Full Time</option><option value="part_time">Part Time</option></select></div><div class="form-group"><label class="form-label">Hire Date</label><input type="date" name="hire_date" class="form-input" value="<?= date('Y-m-d') ?>"></div></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Guarantor Name</label><input type="text" name="guarantor_name" class="form-input"></div><div class="form-group"><label class="form-label">Guarantor Phone</label><input type="tel" name="guarantor_phone" class="form-input"></div></div>
</form></div>
<div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="TT.submitForm('#staffForm','StaffController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#staffModal');loadStaff();TT.toast(r.message,'success');}}})">Save</button></div>
</div></div>

<script>
function loadStaff(){TT.get('StaffController.php',{action:'get_staff'}).done(function(r){if(!r.success)return;let h='';r.data.staff.forEach(s=>{h+=`<tr><td><strong>${TT.escHtml(s.first_name+' '+s.last_name)}</strong></td><td>${s.user_type}</td><td>${TT.escHtml(s.job_title||'-')}</td><td>${s.employment_type||'-'}</td><td class="text-sm">${TT.escHtml(s.phone||'-')}</td><td class="row-actions"><button class="btn btn-ghost btn-sm" onclick="genBadge(${s.id})">Badge</button>${s.is_active==1?`<button class="btn btn-ghost btn-sm" onclick="TT.api('StaffController.php',{action:'deactivate_staff',id:${s.id}}).done(r=>{if(r.success){loadStaff();TT.toast(r.message,'success');}})">Deactivate</button>`:''}</td></tr>`;});$('#staffTable').html(h||'<tr><td colspan="6"><div class="empty-state"><p>No staff yet.</p></div></td></tr>');});}
function loadAttendance(){TT.get('StaffController.php',{action:'get_attendance',date:$('#attDate').val()}).done(function(r){if(!r.success)return;let h='';const records={};r.data.records.forEach(rc=>{records[rc.user_id]=rc;});r.data.staff.forEach(s=>{const rc=records[s.id]||{};h+=`<tr><td>${TT.escHtml(s.first_name+' '+s.last_name)}</td><td>${TT.escHtml(s.job_title||'-')}</td><td><select class="form-select" data-uid="${s.id}" data-field="status" style="height:36px;width:auto;font-size:0.8125rem;"><option value="present" ${rc.status==='present'?'selected':''}>Present</option><option value="absent" ${rc.status==='absent'?'selected':''}>Absent</option><option value="late" ${rc.status==='late'?'selected':''}>Late</option><option value="leave" ${rc.status==='leave'?'selected':''}>Leave</option></select></td><td><input type="time" class="form-input" data-uid="${s.id}" data-field="clock_in" value="${rc.clock_in||''}" style="height:36px;width:auto;"></td><td><input type="time" class="form-input" data-uid="${s.id}" data-field="clock_out" value="${rc.clock_out||''}" style="height:36px;width:auto;"></td></tr>`;});$('#attTable').html(h);});}
function saveAttendance(){const entries=[];$('#attTable tr').each(function(){const uid=$(this).find('[data-uid]').first().data('uid');if(!uid)return;entries.push({user_id:uid,status:$(this).find('[data-field=status]').val(),clock_in:$(this).find('[data-field=clock_in]').val(),clock_out:$(this).find('[data-field=clock_out]').val()});});TT.api('StaffController.php',{action:'mark_attendance',date:$('#attDate').val(),entries:JSON.stringify(entries)}).done(r=>{if(r.success)TT.toast(r.message,'success');});}
function genBadge(id){TT.api('StaffController.php',{action:'generate_badge',id}).done(r=>{if(r.success){TT.toast('Badge generated','success');if(r.data.path)window.open('<?= APP_URL ?>/'+r.data.path,'_blank');}});}
$(function(){loadStaff();loadAttendance();});
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
