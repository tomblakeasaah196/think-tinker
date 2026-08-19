<?php $pageTitle='My Child'; $currentTab='child'; require_once __DIR__.'/../templates/header-parent.php'; ?>

<div id="childSwitcher" class="child-switcher" style="display:none;"></div>
<div id="childProfile"><div class="spinner mx-auto mt-4"></div></div>

<script>
$(function(){
    TT.get('ParentController.php',{action:'get_dashboard'}).done(function(r){
        if(!r.success) return;
        const children = r.data.children || [];
        if (!children.length) { $('#childProfile').html('<div class="empty-state"><div class="icon">👶</div><h3>No Children Yet</h3><p>Your children will appear here once enrolled.</p></div>'); return; }
        // Switcher
        if (children.length > 1) {
            let chips = '';
            children.forEach((c,i) => { chips += `<div class="child-chip ${i===0?'active':''}" onclick="loadChild(${c.id},this)">${TT.escHtml(c.first_name)}</div>`; });
            $('#childSwitcher').html(chips).show();
        }
        loadChild(children[0].id);
    });
});

function loadChild(id, chip) {
    if (chip) { $('.child-chip').removeClass('active'); $(chip).addClass('active'); }
    TT.get('ChildController.php', { action: 'get_child', id }).done(function(r) {
        if (!r.success) return;
        const c = r.data.child;
        let html = `<div class="card mb-3"><div class="card-body" style="text-align:center;">
            <div class="child-photo-wrap mx-auto mb-2">
                ${c.photo_url
                    ? `<img src="${c.photo_url}" alt="${TT.escHtml(c.first_name)}" class="child-photo">`
                    : `<div class="avatar avatar-xl avatar-teal">${TT.initials(c.first_name+' '+c.last_name)}</div>`}
                <button type="button" class="child-photo-btn" onclick="ChildPhoto.pick(${c.id})" title="Change photo" aria-label="Change photo">&#128247;</button>
            </div>
            <input type="file" id="childPhotoInput" accept="image/*" style="display:none;">
            <h2>${TT.escHtml(c.first_name+' '+c.last_name)}</h2>
            <p class="text-muted text-sm">Age ${c.age} · ${TT.escHtml(c.current_school||'School not set')} · ${TT.escHtml(c.grade_level||'Grade not set')}</p>
            ${c.medical_notes?`<div class="alert alert-warning mt-2" style="text-align:left;"><strong>Medical Notes:</strong> ${TT.escHtml(c.medical_notes)}</div>`:''}
        </div></div>`;
        // Services
        if (r.data.services && r.data.services.length) {
            html += '<h3 class="mb-2" style="font-size:1rem;">Active Services</h3>';
            r.data.services.forEach(s => {
                html += `<div class="session-card" style="border-left-color:var(--tinker-teal);"><div class="session-info"><div class="tutor">${s.service_type.replace('_',' ')} ${s.frequency?'— '+s.frequency:''}</div><div class="time">Tutor: ${s.tutor_name||'Unassigned'} · Since ${s.start_date}</div></div><span class="badge ${s.status==='active'?'badge-green':'badge-gray'}">${s.status}</span></div>`;
            });
        }
        // Club — a pending_payment membership gets an honest "payment
        // pending" card instead of counting down before payment clears.
        if (r.data.club_membership) {
            const cm = r.data.club_membership;
            if (cm.status === 'pending_payment') {
                html += `<a class="club-card club-card--pending mt-3" href="payments.php"><div class="plan">${TT.escHtml(cm.plan)} Plan</div><div class="status">⏳ Payment Pending</div><div class="expires">Pay your invoice to activate the STEM & Reading Club.</div></a>`;
            } else {
                const days = Math.max(0, Math.floor((new Date(cm.end_date) - Date.now()) / 86400000));
                html += `<div class="club-card mt-3"><div class="plan">${TT.escHtml(cm.plan)} Plan</div><div class="status">STEM & Reading Club</div><div class="expires">${cm.start_date} → ${cm.end_date} · ${days} days left</div></div>`;
            }
        }
        // Progress
        html += '<h3 class="mt-3 mb-2" style="font-size:1rem;">Progress</h3><div id="childProgress"><div class="spinner mx-auto"></div></div>';
        $('#childProfile').html(html);
        // Load progress
        TT.get('ChildController.php', { action: 'get_progress', child_id: id }).done(function(pr) {
            if (!pr.success) return;
            const p = pr.data.progress;
            if (!Object.keys(p).length) { $('#childProgress').html('<p class="text-muted text-sm">No progress records yet.</p>'); return; }
            let ph = '';
            Object.entries(p).forEach(([subj, topics]) => {
                ph += `<div class="card card-flat mb-2"><div class="card-body"><h4 style="font-size:0.875rem;margin-bottom:8px;">${subj}</h4>`;
                topics.forEach(t => {
                    const colors = {mastered:'var(--leaf-green)',in_progress:'var(--spark-orange)',not_started:'#CCC'};
                    const icons = {mastered:'●',in_progress:'◐',not_started:'○'};
                    ph += `<div style="display:flex;justify-content:space-between;padding:4px 0;font-size:0.8125rem;border-bottom:1px solid #F0F0F0;"><span>${TT.escHtml(t.topic)}</span><span style="color:${colors[t.status]||'#CCC'};font-weight:700;">${icons[t.status]||'○'}</span></div>`;
                });
                ph += '</div></div>';
            });
            $('#childProgress').html(ph);
        });
    });
}
</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>

<style>
/* Child photo — the API has always returned photo_url and the children table
   has always had a photo column; this page just never used them. */
.child-photo-wrap { position: relative; width: 96px; height: 96px; }
.child-photo {
    width: 96px; height: 96px; border-radius: 50%;
    object-fit: cover; display: block;
    border: 3px solid var(--tinker-teal);
}
.child-photo-wrap .avatar-xl { width: 96px; height: 96px; font-size: 2rem; }
.child-photo-btn {
    position: absolute; right: -2px; bottom: -2px;
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--tinker-teal); color: #FFF;
    border: 2px solid #FFF; cursor: pointer;
    font-size: 14px; line-height: 1;
    display: flex; align-items: center; justify-content: center;
}
.child-photo-btn:hover { background: #0F6F66; }
</style>
<script>
/* Parents may set their own child's photo; ChildController.updateChild()
   already enforces that a parent can only touch a child whose parent_id
   matches their own user id, and already accepts $_FILES['photo']. */
const ChildPhoto = {
    pick(childId) {
        const input = document.getElementById('childPhotoInput');
        input.value = '';
        input.onchange = () => ChildPhoto.upload(childId, input.files[0]);
        input.click();
    },
    upload(childId, file) {
        if (!file) return;
        if (!file.type.startsWith('image/')) { TT.toast('Please choose an image file.', 'error'); return; }
        if (file.size > 10 * 1024 * 1024) { TT.toast('Image must be under 10MB.', 'error'); return; }
        const fd = new FormData();
        fd.append('action', 'update_child');
        fd.append('id', childId);
        fd.append('photo', file);
        TT.toast('Uploading photo...', 'info');
        TT.api('ChildController.php', fd).done(function(r) {
            if (r.success) { TT.toast('Photo updated.', 'success'); loadChild(childId); }
            else { TT.toast(r.message || 'Upload failed.', 'error'); }
        }).fail(function() { TT.toast('Upload failed. Please try again.', 'error'); });
    }
};
</script>
