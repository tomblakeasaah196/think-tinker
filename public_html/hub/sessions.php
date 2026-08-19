<?php
$pageTitle = 'Sessions';
$currentModule = 'sessions';
require_once __DIR__ . '/../templates/header-hub.php';
?>

<div class="page-header">
    <h1>Session Management</h1>
    <div class="actions">
        <button class="btn btn-outline btn-sm" onclick="$('#bulkModal').length ? TT.modal.open('#bulkModal') : null"><?= ttIcon('calendar', 'tt-icon', 15) ?> Bulk Schedule</button>
        <button class="btn btn-primary btn-sm" onclick="TT.modal.open('#sessionModal')">+ Add Session</button>
    </div>
</div>

<div class="tabs">
    <div class="tab active" data-tab="tabList">Session List</div>
    <div class="tab" data-tab="tabNotes">Session Notes</div>
</div>

<div class="tab-content active" id="tabList">
    <div class="filter-bar">
        <select class="form-select" id="sessionStatus" onchange="loadSessions()">
            <option value="">All</option><option value="scheduled">Scheduled</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option><option value="rescheduled">Rescheduled</option>
        </select>
        <select class="form-select" id="sessionTutor" onchange="loadSessions()"><option value="">All Tutors</option></select>
    </div>
    <div class="hub-card"><div class="hub-card-body no-pad">
        <div class="table-responsive"><table class="hub-table"><thead><tr><th>Date</th><th>Time</th><th>Student</th><th>Tutor</th><th>Status</th><th></th></tr></thead>
        <tbody id="sessionsTable"></tbody></table></div>
    </div></div>
    <div class="pagination" id="sessionsPagination"></div>
</div>

<div class="tab-content" id="tabNotes">
    <div class="hub-card"><div class="hub-card-body no-pad">
        <div class="table-responsive"><table class="hub-table"><thead><tr><th>Date</th><th>Student</th><th>Tutor</th><th>Topics</th><th>Status</th><th></th></tr></thead>
        <tbody id="notesTable"></tbody></table></div>
    </div></div>
</div>

<!-- Add Session Modal -->
<div class="modal-overlay" id="sessionModal">
    <div class="modal">
        <div class="modal-header"><h3>Schedule Session</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
        <div class="modal-body">
            <form id="sessionForm">
                <input type="hidden" name="action" value="add_session">
                <div class="form-group"><label class="form-label">Student <span class="required">*</span></label><select name="child_id" class="form-select" id="childSelectSession" required><option value="">Select...</option></select></div>
                <div class="form-group"><label class="form-label">Tutor <span class="required">*</span></label><select name="tutor_id" class="form-select" id="tutorSelectSession" required><option value="">Select...</option></select></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Date <span class="required">*</span></label><input type="date" name="session_date" class="form-input" required></div>
                    <div class="form-group"><label class="form-label">Start Time</label><input type="time" name="start_time" class="form-input"></div>
                    <div class="form-group"><label class="form-label">End Time</label><input type="time" name="end_time" class="form-input"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="TT.submitForm('#sessionForm','SessionController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#sessionModal');loadSessions();TT.toast(r.message,'success');}}})">Save</button></div>
    </div>
</div>

<!-- Bulk Schedule Modal -->
<div class="modal-overlay" id="bulkModal">
    <div class="modal">
        <div class="modal-header"><h3>Bulk Schedule</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
        <div class="modal-body">
            <form id="bulkForm">
                <input type="hidden" name="action" value="bulk_schedule">
                <div class="form-group"><label class="form-label">Student</label><select name="child_id" class="form-select childSelect" required><option value="">Select...</option></select></div>
                <div class="form-group"><label class="form-label">Tutor</label><select name="tutor_id" class="form-select tutorSelect" required><option value="">Select...</option></select></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Start Date</label><input type="date" name="start_date" class="form-input" required></div>
                    <div class="form-group"><label class="form-label">End Date</label><input type="date" name="end_date" class="form-input" required></div>
                </div>
                <div class="form-group"><label class="form-label">Days</label>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <label class="checkbox-group"><input type="checkbox" name="days[]" value="mon"> Mon</label>
                        <label class="checkbox-group"><input type="checkbox" name="days[]" value="tue"> Tue</label>
                        <label class="checkbox-group"><input type="checkbox" name="days[]" value="wed"> Wed</label>
                        <label class="checkbox-group"><input type="checkbox" name="days[]" value="thu"> Thu</label>
                        <label class="checkbox-group"><input type="checkbox" name="days[]" value="fri"> Fri</label>
                        <label class="checkbox-group"><input type="checkbox" name="days[]" value="sat"> Sat</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Start Time</label><input type="time" name="start_time" class="form-input"></div>
                    <div class="form-group"><label class="form-label">End Time</label><input type="time" name="end_time" class="form-input"></div>
                </div>
            </form>
        </div>
        <div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="TT.submitForm('#bulkForm','SessionController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#bulkModal');loadSessions();TT.toast(r.message,'success');}}})">Schedule All</button></div>
    </div>
</div>

<script>
function loadSessions() {
    const filters = { status: $('#sessionStatus').val(), tutor_id: $('#sessionTutor').val(), page: 1 };
    Hub.loadTable('SessionController.php', 'get_sessions', filters, function(s) {
        const sc = {scheduled:'badge-blue',completed:'badge-green',cancelled:'badge-red',rescheduled:'badge-orange'}[s.status]||'badge-gray';
        return `<tr>
            <td class="fw-600">${s.session_date}</td><td>${s.start_time||'-'}</td>
            <td>${TT.escHtml(s.child_name+' '+s.child_last)}</td><td>${TT.escHtml(s.tutor_name)}</td>
            <td><span class="badge ${sc}">${s.status}</span>${s.has_note>0?' 📝':''}</td>
            <td class="row-actions">
                ${s.status==='scheduled'?`<button class="btn btn-success btn-sm" onclick="completeSession(${s.id})">Complete</button><button class="btn btn-ghost btn-sm" onclick="cancelSession(${s.id})">Cancel</button>`:''}
            </td></tr>`;
    }, '#sessionsTable', '#sessionsPagination');
}
function loadNotes() {
    TT.get('SessionController.php', { action: 'get_notes' }).done(function(r) {
        if (!r.success) return;
        const notes = r.data.notes;
        if (!notes.length) { $('#notesTable').html('<tr><td colspan="6"><div class="empty-state"><p>No session notes yet.</p></div></td></tr>'); return; }
        let html = '';
        notes.forEach(n => {
            html += `<tr><td>${n.session_date}</td><td>${TT.escHtml(n.child_name)}</td><td>${TT.escHtml(n.tutor_name)}</td>
                <td class="text-sm">${TT.escHtml((n.topics_covered||'').substring(0,60))}</td>
                <td><span class="badge ${n.status==='submitted'?'badge-green':'badge-orange'}">${n.status}</span></td>
                <td><button class="btn btn-ghost btn-sm" onclick="viewNote(${n.id})">View</button></td></tr>`;
        });
        $('#notesTable').html(html);
    });
}
function viewNote(id) { TT.get('SessionController.php', { action: 'get_session_note', id }).done(r => { if(!r.success) return; const n=r.data.note; TT.modal.confirm(`<strong>${n.child_name} — ${n.session_date}</strong><br><br>${n.note_text.replace(/\n/g,'<br>')}<br><br><em>Topics: ${n.topics_covered||'N/A'}</em>`, null, 'Session Note'); }); }
function completeSession(id) { TT.api('SessionController.php', { action: 'complete_session', id }).done(r => { if(r.success){ loadSessions(); TT.toast('Session completed','success'); }}); }
function cancelSession(id) { TT.modal.confirm('Cancel this session?', () => { TT.api('SessionController.php', { action: 'cancel_session', id }).done(r => { if(r.success){ loadSessions(); TT.toast('Session cancelled','success'); }}); }); }

function loadDropdowns() {
    TT.get('ChildController.php', { action: 'get_children' }).done(r => {
        let opts = '<option value="">Select student...</option>';
        if(r.data?.children) r.data.children.forEach(c => { opts += `<option value="${c.id}">${TT.escHtml(c.first_name+' '+c.last_name)}</option>`; });
        $('#childSelectSession, .childSelect').html(opts);
    });
    TT.get('StaffController.php', { action: 'get_tutors' }).done(r => {
        let opts = '<option value="">Select tutor...</option>';
        if(r.data?.tutors) r.data.tutors.forEach(t => { opts += `<option value="${t.id}">${TT.escHtml(t.first_name+' '+t.last_name)}</option>`; });
        $('#tutorSelectSession, .tutorSelect, #sessionTutor').append($(opts).clone());
    });
}

$(function() { loadSessions(); loadNotes(); loadDropdowns(); });
</script>

<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
