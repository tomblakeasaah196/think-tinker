/**
 * assets/js/parent.js — Parent Portal JavaScript
 */
'use strict';
const Parent = window.Parent || {};

Parent.currentChildId = null;

Parent.loadDashboard = function() {
    const params = { action: 'get_dashboard' };
    if (Parent.currentChildId) params.child_id = Parent.currentChildId;
    TT.get('ParentController.php', params).done(function(r) {
        if (!r.success) return;
        const d = r.data;
        // Render child switcher
        if (d.children && d.children.length > 1) {
            let chips = '';
            d.children.forEach(c => {
                const active = c.id == d.selected_child_id ? 'active' : '';
                chips += `<div class="child-chip ${active}" onclick="Parent.switchChild(${c.id})"><div class="chip-avatar">${TT.initials(c.first_name+' '+c.last_name)}</div>${TT.escHtml(c.first_name)}</div>`;
            });
            $('#childSwitcher').html(chips).show();
        }
        Parent.currentChildId = d.selected_child_id;
        // Stats
        const prog = d.progress || {};
        // Each tile links to the page it summarises. Previously these were
        // inert numbers, so a parent reading "0 Messages" concluded there was
        // nothing there rather than that it was somewhere to tap.
        $('#parentStats').html(`
            <a class="parent-stat" href="calendar.php"><div class="stat-num">${(d.upcoming_sessions||[]).length}</div><div class="stat-lbl">Upcoming Sessions</div></a>
            <a class="parent-stat orange" href="messages.php"><div class="stat-num">${d.unread_messages||0}</div><div class="stat-lbl">Messages</div></a>
            <a class="parent-stat green" href="child.php"><div class="stat-num">${prog.mastered||0}</div><div class="stat-lbl">Topics Mastered</div></a>
            <a class="parent-stat coral" href="payments.php"><div class="stat-num">${(d.pending_invoices||[]).length}</div><div class="stat-lbl">Pending Payments</div></a>
        `);
        // Upcoming sessions
        let sessHtml = '';
        (d.upcoming_sessions || []).forEach(s => {
            const dt = new Date(s.session_date);
            sessHtml += `<div class="session-card"><div class="session-date-box"><div class="day">${dt.getDate()}</div><div class="month">${dt.toLocaleString('en',{month:'short'})}</div></div><div class="session-info"><div class="tutor">With ${TT.escHtml(s.tutor_name)}</div><div class="time">${s.start_time||'Time TBD'}</div></div></div>`;
        });
        $('#upcomingSessions').html(sessHtml || '<p class="text-muted text-sm">No upcoming sessions scheduled.</p>');
        // Latest note
        if (d.latest_note) {
            const n = d.latest_note;
            $('#latestNote').html(`<div class="note-card"><div class="note-header"><span class="note-tutor">${TT.escHtml(n.tutor_name)}</span><span class="note-date">${n.session_date}</span></div><div class="note-text">${TT.escHtml((n.note_text||'').substring(0,200))}${n.note_text?.length>200?'...':''}</div>${n.topics_covered?`<div class="note-topics">Topics: ${TT.escHtml(n.topics_covered)}</div>`:''}</div>`);
        }
        // Club card
        if (d.club_membership) {
            const cm = d.club_membership;
            const days = Math.max(0, Math.floor((new Date(cm.end_date) - Date.now()) / 86400000));
            $('#clubCard').html(`<div class="club-card"><div class="plan">${cm.plan} Plan</div><div class="status">Active Membership</div><div class="expires">Expires ${cm.end_date} · ${days} days remaining</div></div>`).show();
        }
    });
};

Parent.switchChild = function(childId) {
    Parent.currentChildId = childId;
    Parent.loadDashboard();
};

Parent.loadPayments = function() {
    TT.get('ParentController.php', { action: 'get_payment_history' }).done(function(r) {
        if (!r.success) return;
        // Bank details
        let bankHtml = '';
        (r.data.bank_accounts || []).forEach(b => {
            bankHtml += `<div class="bank-item"><div class="bank-name">${TT.escHtml(b.bank_name)} ${b.is_primary==1?'<span class="badge badge-green" style="font-size:0.5rem;">Primary</span>':''}</div><div class="acct-num" onclick="TT.copyText('${b.account_number}')">${b.account_number}</div><div class="acct-name">${TT.escHtml(b.account_name)}</div><div class="copy-hint">Tap to copy</div></div>`;
        });
        $('#bankDetails').html(bankHtml);
        // Invoices
        let invHtml = '';
        (r.data.invoices || []).forEach(inv => {
            const statusCls = {paid:'badge-green',sent:'badge-blue',overdue:'badge-red'}[inv.status]||'badge-gray';
            invHtml += `<div class="invoice-card"><div class="inv-amount">${inv.formatted_total}</div><div class="inv-detail"><div class="inv-number">${TT.escHtml(inv.invoice_number)} <span class="badge ${statusCls}">${inv.status}</span></div><div class="inv-due">${inv.child_name?TT.escHtml(inv.child_name)+' · ':''}Due: ${inv.formatted_due}</div></div>
            ${inv.pdf_url?`<a href="${inv.pdf_url}" target="_blank" class="btn btn-ghost btn-sm">📄</a>`:''}
            ${inv.receipt_url?`<a href="${inv.receipt_url}" target="_blank" class="btn btn-ghost btn-sm">🧾</a>`:''}</div>`;
        });
        $('#invoicesList').html(invHtml || '<div class="empty-state"><div class="icon">💳</div><p>No invoices yet.</p></div>');
    });
};

Parent.loadDocuments = function() {
    TT.get('ParentController.php', { action: 'get_documents' }).done(function(r) {
        if (!r.success) return;
        const icons = {invoice:'🧾',receipt:'✅',contract:'📝',certificate:'🎓'};
        let html = '';
        (r.data.documents || []).forEach(d => {
            html += `<a href="${d.url}" target="_blank" class="doc-card"><span class="doc-icon">${icons[d.doc_type]||'📄'}</span><div class="doc-info"><div class="doc-name">${TT.escHtml(d.doc_number)}</div><div class="doc-date">${d.date} · <span class="badge ${d.status==='paid'?'badge-green':'badge-teal'}">${d.status}</span></div></div><span class="doc-dl">View ›</span></a>`;
        });
        $('#docsList').html(html || '<div class="empty-state"><div class="icon">📁</div><p>No documents yet.</p></div>');
    });
};

Parent.loadNotes = function() {
    const params = { action: 'get_notes' };
    if (Parent.currentChildId) params.child_id = Parent.currentChildId;
    TT.get('SessionController.php', params).done(function(r) {
        if (!r.success) return;
        let html = '';
        (r.data.notes || []).forEach(n => {
            html += `<div class="note-card"><div class="note-header"><span class="note-tutor">${TT.escHtml(n.tutor_name)}</span><span class="note-date">${n.session_date}</span></div><div class="note-text">${TT.escHtml(n.note_text)}</div>${n.topics_covered?`<div class="note-topics">Topics: ${TT.escHtml(n.topics_covered)}</div>`:''}</div>`;
        });
        $('#notesList').html(html || '<div class="empty-state"><div class="icon">📝</div><p>No session notes yet.</p></div>');
    });
};

window.Parent = Parent;
