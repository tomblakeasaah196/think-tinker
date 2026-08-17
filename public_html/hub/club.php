<?php $pageTitle='STEM Club'; $currentModule='club'; require_once __DIR__.'/../templates/header-hub.php'; ?>
<div class="page-header"><h1>STEM & Reading Club</h1><div class="actions"><button class="btn btn-primary btn-sm" onclick="TT.modal.open('#memberModal')">+ Add Membership</button></div></div>

<div class="tabs">
    <div class="tab active" data-tab="tabMembers">Memberships</div>
    <div class="tab" data-tab="tabAttendance">Saturday Attendance</div>
    <div class="tab" data-tab="tabExpiring">Expiring Soon</div>
</div>

<div class="tab-content active" id="tabMembers">
    <div class="filter-bar">
        <select class="form-select" id="memberStatus" onchange="loadMemberships()"><option value="active">Active</option><option value="expired">Expired</option><option value="">All</option></select>
    </div>
    <div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Child</th><th>Parent</th><th>Plan</th><th>Price</th><th>Ends</th><th>Days Left</th><th></th></tr></thead><tbody id="membersTable"></tbody></table></div></div>
</div>

<div class="tab-content" id="tabAttendance">
    <div class="filter-bar">
        <input type="date" class="form-input" id="clubAttDate" value="<?= date('Y-m-d', strtotime('next Saturday')) ?>" style="width:auto;">
        <button class="btn btn-primary btn-sm" onclick="loadClubAttendance()">Load</button>
        <button class="btn btn-success btn-sm" onclick="saveClubAttendance()">Save Attendance</button>
    </div>
    <div id="clubAttStats" class="mb-2"></div>
    <div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Child</th><th>Status</th><th>Check In</th><th>Check Out</th></tr></thead><tbody id="clubAttTable"></tbody></table></div></div>
</div>

<div class="tab-content" id="tabExpiring">
    <div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Child</th><th>Plan</th><th>Expires</th><th>Parent Phone</th><th></th></tr></thead><tbody id="expiringTable"></tbody></table></div></div>
</div>

<!-- Add Membership Modal -->
<div class="modal-overlay" id="memberModal"><div class="modal">
    <div class="modal-header"><h3>Add Club Membership</h3><span class="modal-close">&times;</span></div>
    <div class="modal-body"><form id="memberForm"><input type="hidden" name="action" value="add_membership">
        <div class="form-group"><label class="form-label">Student *</label><select name="child_id" class="form-select" id="clubChildSelect" required><option value="">Select...</option></select></div>
        <div class="form-group"><label class="form-label">Plan *</label><select name="plan" class="form-select" required>
            <option value="trial">Trial — 1 Saturday (₦<?= number_format((float)getSetting('club_plan_trial','8000'),0) ?>)</option>
            <option value="monthly">Monthly — 4 Saturdays (₦<?= number_format((float)getSetting('club_plan_monthly','30000'),0) ?>)</option>
            <option value="quarterly">Quarterly — 12 Saturdays (₦<?= number_format((float)getSetting('club_plan_quarterly','85000'),0) ?>)</option>
            <option value="biannual">Bi-Annual — 24 Saturdays (₦<?= number_format((float)getSetting('club_plan_biannual','165000'),0) ?>)</option>
        </select></div>
    </form></div>
    <div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="TT.submitForm('#memberForm','ClubController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#memberModal');loadMemberships();TT.toast(r.message,'success');}}})">Create</button></div>
</div></div>

<script>
function loadMemberships(){
    TT.get('ClubController.php',{action:'get_memberships',status:$('#memberStatus').val()}).done(function(r){
        if(!r.success)return;
        if(!r.data.memberships.length){$('#membersTable').html('<tr><td colspan="7"><div class="empty-state"><p>No memberships found.</p></div></td></tr>');return;}
        let h='';r.data.memberships.forEach(m=>{
            const daysClass=m.days_remaining<=7?'style="color:var(--story-coral);font-weight:700;"':'';
            h+=`<tr><td><strong>${TT.escHtml(m.child_name+' '+m.child_last)}</strong></td><td class="text-sm">${TT.escHtml(m.parent_name+' '+m.parent_last)}</td>
            <td><span class="badge badge-teal">${m.plan}</span></td><td class="fw-700">${m.formatted_price}</td><td class="text-sm">${m.end_date}</td>
            <td ${daysClass}>${m.days_remaining} days</td>
            <td class="row-actions"><button class="btn btn-ghost btn-sm" onclick="genCert(${m.id})">🎓 Certificate</button></td></tr>`;
        });$('#membersTable').html(h);
    });
}
function loadClubAttendance(){
    TT.get('ClubController.php',{action:'get_attendance',date:$('#clubAttDate').val()}).done(function(r){
        if(!r.success)return;
        const s=r.data.stats;
        $('#clubAttStats').html(`<div class="flex gap-2"><div class="stat-card" style="flex:1;"><div class="stat-label">Total Members</div><div class="stat-value">${s.total||0}</div></div><div class="stat-card stat-green" style="flex:1;"><div class="stat-label">Present</div><div class="stat-value">${s.present||0}</div></div></div>`);
        if(!r.data.members.length){$('#clubAttTable').html('<tr><td colspan="4"><div class="empty-state"><p>No active members for this date.</p></div></td></tr>');return;}
        let h='';r.data.members.forEach(m=>{
            h+=`<tr><td><strong>${TT.escHtml(m.first_name+' '+m.last_name)}</strong></td>
            <td><select class="form-select" data-mid="${m.membership_id}" data-cid="${m.child_id}" data-field="status" style="height:36px;width:auto;font-size:0.8125rem;">
                <option value="present" ${m.att_status==='present'?'selected':''}>Present</option>
                <option value="absent" ${m.att_status==='absent'||!m.att_status?'selected':''}>Absent</option>
            </select></td>
            <td><input type="time" class="form-input" data-mid="${m.membership_id}" data-field="check_in" value="${m.checked_in_at||''}" style="height:36px;width:auto;"></td>
            <td><input type="time" class="form-input" data-mid="${m.membership_id}" data-field="check_out" value="${m.checked_out_at||''}" style="height:36px;width:auto;"></td></tr>`;
        });$('#clubAttTable').html(h);
    });
}
function saveClubAttendance(){
    const entries=[];$('#clubAttTable tr').each(function(){
        const mid=$(this).find('[data-mid]').first().data('mid');
        const cid=$(this).find('[data-cid]').first().data('cid');
        if(!mid)return;
        entries.push({membership_id:mid,child_id:cid,status:$(this).find('[data-field=status]').val(),check_in:$(this).find('[data-field=check_in]').val(),check_out:$(this).find('[data-field=check_out]').val()});
    });
    TT.api('ClubController.php',{action:'mark_attendance',date:$('#clubAttDate').val(),entries:JSON.stringify(entries)}).done(r=>{if(r.success)TT.toast(r.message,'success');});
}
function loadExpiring(){
    TT.get('ClubController.php',{action:'get_expiring'}).done(function(r){
        if(!r.success)return;
        if(!r.data.expiring.length){$('#expiringTable').html('<tr><td colspan="5"><div class="empty-state"><p>No memberships expiring soon. 🎉</p></div></td></tr>');return;}
        let h='';r.data.expiring.forEach(m=>{
            const waLink=`https://wa.me/${(m.parent_phone||'').replace(/\D/g,'')}?text=${encodeURIComponent('Hi! Your child\'s Think & Tinker STEM Club membership expires on '+m.end_date+'. Would you like to renew?')}`;
            h+=`<tr><td><strong>${TT.escHtml(m.first_name+' '+m.last_name)}</strong></td><td><span class="badge badge-orange">${m.plan}</span></td>
            <td style="color:var(--story-coral);font-weight:700;">${m.end_date}</td><td class="text-sm">${TT.escHtml(m.parent_phone||'-')}</td>
            <td><a href="${waLink}" target="_blank" class="btn btn-success btn-sm">💬 Remind</a></td></tr>`;
        });$('#expiringTable').html(h);
    });
}
function genCert(id){TT.api('ClubController.php',{action:'generate_certificate',membership_id:id}).done(r=>{if(r.success){TT.toast('Certificate generated','success');if(r.data.path)window.open('<?= APP_URL ?>/'+r.data.path,'_blank');}});}

$(function(){
    loadMemberships();loadClubAttendance();loadExpiring();
    TT.get('ChildController.php',{action:'get_children'}).done(r=>{let o='<option value="">Select student...</option>';if(r.data?.children)r.data.children.forEach(c=>{o+=`<option value="${c.id}">${TT.escHtml(c.first_name+' '+c.last_name)}</option>`;});$('#clubChildSelect').html(o);});
});
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
