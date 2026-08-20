/**
 * WhatsApp-style messenger (parent + hub).
 *
 * Two modes:
 *   - parent (`role: 'parent'`, `single: true`): exactly ONE thread with the
 *     business ("Think & Tinker"). No conversation list, no search, no FAB.
 *     The thread is loaded/created immediately and fills the viewport.
 *   - staff  (`role: 'staff'`): an inbox of many parent conversations — list
 *     → thread, one pane at a time on phone.
 *
 * Send path is optimistic: the bubble is appended the instant the user taps
 * send, then confirmed by the API. Failures are marked in-place and toasted —
 * never silently dropped.
 */
'use strict';
const Messenger = window.Messenger || {};

Messenger.convoId = null;
Messenger.convoName = '';
Messenger.pollTimer = null;
Messenger.role = 'parent';
Messenger.single = false;           // parent: single full-screen thread, no list
Messenger.pendingFile = null;
Messenger.pending = [];             // optimistic bubbles awaiting server confirm
Messenger._lastServerMessages = []; // last known-good server messages (for re-render)
Messenger._hasRendered = false;     // whether any bubble has been drawn yet
Messenger._pollSeq = 0;             // monotonic counter to drop stale responses

const TICK_SVG = `<svg viewBox="0 0 16 11" fill="currentColor" aria-hidden="true"><path d="M11.07.9 4.86 7.3 2.4 4.9 1.2 6.1l3.66 3.55L12.3.9z"/></svg><svg viewBox="0 0 16 11" fill="currentColor" aria-hidden="true"><path d="M11.07.9 4.86 7.3 2.4 4.9 1.2 6.1l3.66 3.55L12.3.9z"/></svg>`;

Messenger.init = function(opts) {
    opts = opts || {};
    Messenger.role = opts.role || 'parent';
    Messenger.single = opts.single === true || Messenger.role === 'parent';

    Messenger.bindEvents();

    if (Messenger.single) {
        Messenger.openSingleThread();
    } else {
        Messenger.loadConversations();
    }
};

Messenger.bindEvents = function() {
    $(document).on('click', '.wa-convo', function() {
        Messenger.openConvo($(this).data('id'), $(this).data('name') || '');
    });
    // Staff back arrow → back to list. Parent uses a real <a> to /parent/.
    if (!Messenger.single) {
        $(document).on('click', '.wa-back', function() { Messenger.showList(); });
    }
    $(document).on('keydown', '#waInput', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); Messenger.send(); }
    });
    $(document).on('input', '#waInput', function() {
        // Auto-grow the composer (WhatsApp-style), clamped to ~4 lines.
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    $(document).on('input', '#waSearch', function() {
        const q = $(this).val().toLowerCase();
        $('.wa-convo').each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
        });
    });
    $(document).on('change', '#waFile', function() {
        const f = this.files && this.files[0];
        Messenger.pendingFile = f || null;
        if (f) {
            $('#waPreviewName').text(f.name);
            if (f.type.indexOf('image/') === 0) {
                const url = URL.createObjectURL(f);
                $('#waPreviewThumb').attr('src', url).show();
            } else {
                $('#waPreviewThumb').hide();
            }
            $('#waPreview').addClass('is-on');
        } else {
            Messenger.clearAttach();
        }
    });
    $(document).on('click', '#waPreviewClear', function() { Messenger.clearAttach(); });
    $(document).on('click', '.wa-att-img', function() {
        $('#waLightboxImg').attr('src', $(this).attr('src'));
        $('#waLightbox').addClass('open');
    });
    $(document).on('click', '#waLightbox, #waLightboxClose', function(e) {
        if (e.target === this || $(e.target).is('#waLightboxClose')) {
            $('#waLightbox').removeClass('open');
        }
    });
    // Retry a failed optimistic bubble.
    $(document).on('click', '.wa-bubble.wa-failed .wa-retry', function() {
        const tempId = $(this).closest('.wa-bubble').data('temp');
        Messenger.retry(tempId);
    });
};

Messenger.clearAttach = function() {
    Messenger.pendingFile = null;
    const input = document.getElementById('waFile');
    if (input) input.value = '';
    $('#waPreview').removeClass('is-on');
};

Messenger.emptyText = function() {
    return Messenger.single
        ? 'No messages yet. Say hello 👋'
        : 'No messages yet.';
};

// ---------------------------------------------------------------------------
// PARENT — single thread
// ---------------------------------------------------------------------------
Messenger.openSingleThread = function() {
    $('body').addClass('wa-chat-open wa-single');
    $('#waListPane').addClass('is-hidden');
    $('#waChatPane').removeClass('is-hidden');
    Messenger.convoName = 'Think & Tinker';
    const nameEl = document.getElementById('waChatName');
    if (nameEl) nameEl.textContent = Messenger.convoName;

    // Paint the empty thread immediately so there's no blank flash while the
    // conversation lookup is in flight.
    Messenger.renderThread();

    TT.get('MessageController.php', { action: 'get_conversations' }).done(function(r) {
        if (!r.success) { Messenger.renderThread(); return; }
        const convos = r.data.conversations || [];
        if (convos.length) {
            // A parent has exactly one counterparty — use the most recent thread.
            Messenger.convoId = convos[0].conversation_id;
            Messenger.loadMessages();
        } else {
            // No thread yet. Show the empty thread UI; the first send will
            // create the conversation and return its id.
            Messenger.convoId = null;
            Messenger._lastServerMessages = [];
            Messenger.renderThread();
        }
    });
    Messenger.startPolling();
};

// ---------------------------------------------------------------------------
// STAFF — inbox of parent conversations
// ---------------------------------------------------------------------------
Messenger.showList = function() {
    Messenger.convoId = null;
    $('body').removeClass('wa-chat-open');
    $('#waListPane').removeClass('is-hidden');
    $('#waChatPane').addClass('is-hidden');
    Messenger.stopPolling();
    Messenger.loadConversations();
};

Messenger.openConvo = function(id, name) {
    Messenger.convoId = id;
    Messenger.convoName = name || 'Chat';
    $('body').addClass('wa-chat-open');
    $('#waListPane').addClass('is-hidden');
    $('#waChatPane').removeClass('is-hidden');
    $('#waChatName').text(Messenger.convoName);
    $('#waChatAvatar').text(TT.initials(Messenger.convoName));
    Messenger._lastServerMessages = [];
    Messenger._hasRendered = false;
    Messenger.loadMessages();
    Messenger.startPolling();
    $('#waInput').focus();
};

Messenger.loadConversations = function() {
    TT.get('MessageController.php', { action: 'get_conversations' }).done(function(r) {
        if (!r.success) return;
        const convos = r.data.conversations || [];
        const list = document.getElementById('waConvoList');
        if (!list) return;
        if (!convos.length) {
            list.innerHTML = '<div class="wa-empty">' +
                (Messenger.single ? 'No chats yet.' : 'No conversations yet. Parents who message you will appear here.') +
                '</div>';
            return;
        }
        let html = '';
        convos.forEach(c => {
            const name = Messenger.role === 'parent'
                ? 'Think & Tinker'
                : (c.parent_name || c.sender_name || 'Parent');
            const youSent = Messenger.role === 'parent'
                ? c.last_sender_type === 'parent'
                : (c.last_sender_type && c.last_sender_type !== 'parent');
            const preview = `${youSent ? 'You: ' : ''}${TT.escHtml(c.last_message || '')}`;
            const unread = parseInt(c.unread, 10) || 0;
            html += `<div class="wa-convo ${c.conversation_id === Messenger.convoId ? 'active' : ''}" data-id="${TT.escHtml(c.conversation_id)}" data-name="${TT.escHtml(name)}">
                <div class="wa-avatar">${TT.escHtml(TT.initials(name))}</div>
                <div class="wa-convo-body">
                    <div class="wa-convo-top"><span class="wa-convo-name">${TT.escHtml(name)}</span><span class="wa-convo-time">${TT.escHtml(c.time_ago || '')}</span></div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <div class="wa-convo-preview">${preview}</div>
                        ${unread > 0 ? `<span class="wa-unread">${unread}</span>` : ''}
                    </div>
                </div>
            </div>`;
        });
        list.innerHTML = html;
    });
};

// ---------------------------------------------------------------------------
// RENDERING
// ---------------------------------------------------------------------------
Messenger.ticks = function(m) {
    if (!m.is_mine) return '';
    const read = parseInt(m.is_read, 10) === 1;
    return `<span class="wa-ticks ${read ? 'read' : 'unread single'}" title="${read ? 'Read' : 'Sent'}">${TICK_SVG}</span>`;
};

Messenger.attachmentHtml = function(att) {
    if (!att || !att.url) return '';
    const mime = att.mime || '';
    const name = TT.escHtml(att.name || 'File');
    if (mime.indexOf('image/') === 0) {
        return `<img class="wa-att-img" src="${TT.escHtml(att.url)}" alt="${name}">`;
    }
    return `<a class="wa-att-file" href="${TT.escHtml(att.url)}" target="_blank" rel="noopener">
        <span style="font-size:1.4rem">📄</span>
        <span><strong>${name}</strong><span>Tap to open</span></span>
    </a>`;
};

Messenger.bubbleHtml = function(m) {
    const mine = !!m.is_mine;
    let html = `<div class="wa-bubble ${mine ? 'wa-mine' : 'wa-theirs'}">`;
    if (!mine && Messenger.role !== 'parent') {
        html += `<div class="wa-sender">${TT.escHtml(m.display_as || '')}</div>`;
    }
    html += Messenger.attachmentHtml(m.attachment);
    if (m.message_text) {
        html += `<div class="wa-caption">${TT.escHtml(m.message_text)}</div>`;
    }
    html += `<div class="wa-meta-row"><span class="wa-time">${TT.escHtml(m.time || '')}</span>${Messenger.ticks(m)}</div></div>`;
    return html;
};

Messenger.localAttachmentPreview = function(p) {
    if (!p.file) return '';
    const name = TT.escHtml(p.file.name || 'File');
    if (p.previewUrl && p.file.type.indexOf('image/') === 0) {
        return `<img class="wa-att-img" src="${p.previewUrl}" alt="${name}">`;
    }
    return `<span class="wa-att-file wa-att-file--pending"><span style="font-size:1.4rem">📄</span><span><strong>${name}</strong><span>Sending…</span></span></span>`;
};

Messenger.pendingHtml = function(p) {
    let html = `<div class="wa-bubble wa-mine ${p.failed ? 'wa-failed' : 'wa-pending'}" data-temp="${TT.escHtml(p.tempId)}">`;
    html += Messenger.localAttachmentPreview(p);
    if (p.text) html += `<div class="wa-caption">${TT.escHtml(p.text)}</div>`;
    html += `<div class="wa-meta-row"><span class="wa-time">${TT.escHtml(p.time)}</span>`;
    if (p.failed) {
        html += `<span class="wa-failed-mark" title="${TT.escHtml(p.error || 'Not sent')}">!</span>`;
        html += `<button type="button" class="wa-retry" title="Retry">↻</button>`;
    } else if (p.sent) {
        // Confirmed by the server — one grey tick until the peer reads it.
        html += `<span class="wa-ticks unread single" title="Sent">${TICK_SVG}</span>`;
    } else {
        html += `<span class="wa-ticks pending" title="Sending…"><span class="wa-spinner"></span></span>`;
    }
    html += `</div></div>`;
    return html;
};

Messenger.dateChip = function(date) {
    return `<div class="wa-date-chip">${TT.escHtml(date)}</div>`;
};

/**
 * Rebuild the thread from server messages + any optimistic (pending) bubbles.
 * Single source of truth for the DOM, so a slow/empty poll can never wipe a
 * just-sent bubble: pending items are always re-appended until confirmed.
 */
Messenger.renderThread = function() {
    const el = document.getElementById('waThread');
    if (!el) return;

    const messages = Messenger._lastServerMessages || [];
    // Drop optimistic items the server has already confirmed (they render as
    // real bubbles via `messages`), so a sent message never double-renders.
    const serverIds = new Set(messages.map(m => String(m.id)));
    const visiblePending = Messenger.pending.filter(p => !(p.serverId && serverIds.has(String(p.serverId))));

    let html = '';
    let lastDate = '';

    messages.forEach(m => {
        if (m.date !== lastDate) { html += Messenger.dateChip(m.date); lastDate = m.date; }
        html += Messenger.bubbleHtml(m);
    });
    visiblePending.forEach(p => {
        if (p.date !== lastDate) { html += Messenger.dateChip(p.date); lastDate = p.date; }
        html += Messenger.pendingHtml(p);
    });

    const isEmpty = messages.length === 0 && visiblePending.length === 0;
    if (isEmpty) {
        // Never blank a thread that already has content because a GET came
        // back empty (slow write / race) — only draw the empty state once.
        if (Messenger._hasRendered) return;
        el.innerHTML = '<div class="wa-empty">' + Messenger.emptyText() + '</div>';
        Messenger._hasRendered = false;
        return;
    }

    el.innerHTML = html;
    Messenger._hasRendered = true;
    el.scrollTop = el.scrollHeight;
};

Messenger.loadMessages = function() {
    if (!Messenger.convoId) return;
    const seq = ++Messenger._pollSeq;
    TT.get('MessageController.php', { action: 'get_messages', conversation_id: Messenger.convoId }).done(function(r) {
        if (seq !== Messenger._pollSeq) return; // drop stale responses
        if (!r.success) return;
        Messenger._lastServerMessages = r.data.messages || [];
        Messenger.renderThread();
    });
};

// ---------------------------------------------------------------------------
// SEND (optimistic)
// ---------------------------------------------------------------------------
Messenger.send = function() {
    const text = ($('#waInput').val() || '').trim();
    const file = Messenger.pendingFile;
    if (!text && !file) return;

    // Clear composer + attach preview now (WhatsApp behaviour).
    const input = document.getElementById('waInput');
    if (input) { input.value = ''; input.style.height = 'auto'; }
    Messenger.clearAttach();

    Messenger.dispatch({ text: text, file: file });
};

/**
 * Render an optimistic bubble immediately, then POST to the API. On success
 * the bubble flips to a single grey tick; on failure it is marked in-place.
 */
Messenger.dispatch = function(item) {
    const now = new Date();
    const pending = {
        tempId: 'temp-' + Date.now() + '-' + Math.floor(Math.random() * 1000),
        text: item.text,
        file: item.file,
        previewUrl: item.file && item.file.type.indexOf('image/') === 0 ? URL.createObjectURL(item.file) : null,
        time: now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }),
        date: Messenger.dayLabel(now),
        failed: false,
        error: '',
    };

    // Optimistic UI — bubble appears immediately, before the network round-trip.
    Messenger.pending.push(pending);
    Messenger.renderThread();

    const fd = new FormData();
    fd.append('action', 'send_message');
    fd.append('message_text', item.text);
    fd.append('conversation_id', Messenger.convoId || '');
    if (item.file) fd.append('attachment', item.file);

    TT.api('MessageController.php', fd, { silent: true }).done(function(r) {
        if (r.success) {
            Messenger.confirmSend(pending.tempId, r.data);
        } else {
            Messenger.failSend(pending.tempId, r.message || 'Message failed to send.');
        }
    }).fail(function() {
        Messenger.failSend(pending.tempId, 'Could not send. Check your connection and try again.');
    });
};

Messenger.confirmSend = function(tempId, data) {
    const p = Messenger.pending.find(x => x.tempId === tempId);
    if (p) {
        p.sent = true;
        p.serverId = data && data.id ? data.id : null;
        p.failed = false;
    }
    if (data && data.conversation_id) {
        Messenger.convoId = data.conversation_id;
        Messenger.startPolling(); // ensure we poll now that a thread exists
    }
    // Flip the spinner → single grey tick immediately, then reconcile with
    // the authoritative server list (which includes the new message).
    Messenger.renderThread();
    Messenger.loadMessages();
    if (!Messenger.single) Messenger.loadConversations();
};

Messenger.failSend = function(tempId, message) {
    const p = Messenger.pending.find(x => x.tempId === tempId);
    if (p) {
        p.failed = true;
        p.error = message;
        // Note: keep the object URL alive — the failed bubble still shows the
        // image preview; it is released when the pending item is confirmed or
        // retried (re-render drops the old <img>).
    }
    Messenger.renderThread();
    TT.toast(message, 'error');
};

Messenger.retry = function(tempId) {
    const idx = Messenger.pending.findIndex(p => p.tempId === tempId);
    if (idx === -1) return;
    const p = Messenger.pending.splice(idx, 1)[0];
    // Re-queue the same content through the normal optimistic send path.
    Messenger.dispatch({ text: p.text, file: p.file });
};

Messenger.dayLabel = function(d) {
    const today = new Date();
    const yesterday = new Date(Date.now() - 86400000);
    if (d.toDateString() === today.toDateString()) return 'Today';
    if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
    return d.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short' });
};

// ---------------------------------------------------------------------------
// POLLING
// ---------------------------------------------------------------------------
Messenger.startPolling = function() {
    Messenger.stopPolling();
    Messenger.pollTimer = setInterval(function() {
        if (Messenger.convoId) Messenger.loadMessages();
        else if (!Messenger.single) Messenger.loadConversations();
    }, 8000);
};

Messenger.stopPolling = function() {
    if (Messenger.pollTimer) { clearInterval(Messenger.pollTimer); Messenger.pollTimer = null; }
};

Messenger.focusComposer = function() {
    setTimeout(function() {
        const input = document.getElementById('waInput');
        if (input) input.focus();
    }, 200);
};

window.Messenger = Messenger;
