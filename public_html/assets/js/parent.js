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
        // Small icon per tile so the row reads as four distinct shortcuts
        // rather than four anonymous numbers. Paths match includes/icons.php
        // (kept as inline SVG here since this file has no PHP context).
        const statIcon = paths => `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${paths}</svg>`;
        const ICON_CALENDAR = '<rect x="3" y="4.5" width="18" height="16" rx="2" ry="2"/><line x1="16" y1="2.5" x2="16" y2="6.5"/><line x1="8" y1="2.5" x2="8" y2="6.5"/><line x1="3" y1="9.5" x2="21" y2="9.5"/>';
        const ICON_MESSAGE = '<rect x="2.5" y="4" width="19" height="13" rx="3" ry="3"/><polygon points="7,17 7,21 11,17"/>';
        const ICON_STAR = '<polygon points="12,2.5 15,8.9 22,9.8 17,14.7 18.2,21.6 12,18.3 5.8,21.6 7,14.7 2,9.8 9,8.9"/>';
        const ICON_CARD = '<rect x="1.5" y="5" width="21" height="14" rx="2" ry="2"/><line x1="1.5" y1="10" x2="22.5" y2="10"/>';
        // Each tile links to the page it summarises. Previously these were
        // inert numbers, so a parent reading "0 Messages" concluded there was
        // nothing there rather than that it was somewhere to tap.
        $('#parentStats').html(`
            <a class="parent-stat" href="calendar.php"><div class="stat-icon">${statIcon(ICON_CALENDAR)}</div><div class="stat-num">${(d.upcoming_sessions||[]).length}</div><div class="stat-lbl">Upcoming Sessions</div></a>
            <a class="parent-stat orange" href="messages.php"><div class="stat-icon">${statIcon(ICON_MESSAGE)}</div><div class="stat-num">${d.unread_messages||0}</div><div class="stat-lbl">Messages</div></a>
            <a class="parent-stat green" href="child.php"><div class="stat-icon">${statIcon(ICON_STAR)}</div><div class="stat-num">${prog.mastered||0}</div><div class="stat-lbl">Topics Mastered</div></a>
            <a class="parent-stat coral" href="payments.php"><div class="stat-icon">${statIcon(ICON_CARD)}</div><div class="stat-num">${(d.pending_invoices||[]).length}</div><div class="stat-lbl">Pending Payments</div></a>
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
        // Club card — a pending_payment membership gets its own honest
        // state instead of the "Active Membership … days remaining" card,
        // which used to show (and silently count down) before payment had
        // even been confirmed.
        if (d.club_membership) {
            const cm = d.club_membership;
            if (cm.status === 'pending_payment') {
                $('#clubCard').html(`<a class="club-card club-card--pending" href="payments.php"><div class="plan">${TT.escHtml(cm.plan)} Plan</div><div class="status">⏳ Payment Pending</div><div class="expires">Pay your invoice to activate — tap to view Payments.</div></a>`).show();
            } else {
                const days = Math.max(0, Math.floor((new Date(cm.end_date) - Date.now()) / 86400000));
                $('#clubCard').html(`<div class="club-card"><div class="plan">${TT.escHtml(cm.plan)} Plan</div><div class="status">Active Membership</div><div class="expires">Expires ${cm.end_date} · ${days} days remaining</div></div>`).show();
            }
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
        $('#invoicesList').html(invHtml || '<div class="empty-state"><div class="icon-badge"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1.5" y="5" width="21" height="14" rx="2" ry="2"/><line x1="1.5" y1="10" x2="22.5" y2="10"/></svg></div><p>No invoices yet.</p></div>');
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
