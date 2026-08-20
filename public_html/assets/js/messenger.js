/**
 * WhatsApp-style messenger (parent + hub).
 */
'use strict';
const Messenger = window.Messenger || {};

Messenger.convoId = null;
Messenger.convoName = '';
Messenger.pollTimer = null;
Messenger.role = 'parent';
Messenger.pendingFile = null;

const TICK_SVG = `<svg viewBox="0 0 16 11" fill="currentColor" aria-hidden="true"><path d="M11.07.9 4.86 7.3 2.4 4.9 1.2 6.1l3.66 3.55L12.3.9z"/></svg><svg viewBox="0 0 16 11" fill="currentColor" aria-hidden="true"><path d="M11.07.9 4.86 7.3 2.4 4.9 1.2 6.1l3.66 3.55L12.3.9z"/></svg>`;

Messenger.init = function(opts) {
    opts = opts || {};
    Messenger.role = opts.role || 'parent';
    Messenger.loadConversations();

    $(document).on('click', '.wa-convo', function() {
        Messenger.openConvo($(this).data('id'), $(this).data('name') || '');
    });
    $(document).on('click', '.wa-back', function() { Messenger.showList(); });
    $(document).on('keydown', '#waInput', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); Messenger.send(); }
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
};

Messenger.clearAttach = function() {
    Messenger.pendingFile = null;
    const input = document.getElementById('waFile');
    if (input) input.value = '';
    $('#waPreview').removeClass('is-on');
};

Messenger.showList = function() {
    Messenger.convoId = null;
    $('body').removeClass('wa-chat-open');
    $('#waListPane').removeClass('is-hidden');
    $('#waChatPane').addClass('is-hidden');
    if (Messenger.pollTimer) { clearInterval(Messenger.pollTimer); Messenger.pollTimer = null; }
    Messenger.loadConversations();
};

Messenger.openConvo = function(id, name) {
    Messenger.convoId = id;
    Messenger.convoName = name || (Messenger.role === 'parent' ? 'Think & Tinker' : 'Chat');
    $('body').addClass('wa-chat-open');
    $('#waListPane').addClass('is-hidden');
    $('#waChatPane').removeClass('is-hidden');
    $('#waChatName').text(Messenger.convoName);
    Messenger.loadMessages();
    if (Messenger.pollTimer) clearInterval(Messenger.pollTimer);
    Messenger.pollTimer = setInterval(() => Messenger.loadMessages(), 8000);
    $('#waInput').focus();
};

Messenger.loadConversations = function() {
    TT.get('MessageController.php', { action: 'get_conversations' }).done(function(r) {
        if (!r.success) return;
        const convos = r.data.conversations || [];
        if (!convos.length) {
            $('#waConvoList').html('<div class="wa-empty">No chats yet. Tap the button below to message Think &amp; Tinker.</div>');
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
        $('#waConvoList').html(html);
        if (Messenger.role === 'parent' && convos.length === 1 && !Messenger.convoId) {
            Messenger.openConvo(convos[0].conversation_id, 'Think & Tinker');
        }
    });
};

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

Messenger.loadMessages = function() {
    if (!Messenger.convoId) return;
    TT.get('MessageController.php', { action: 'get_messages', conversation_id: Messenger.convoId }).done(function(r) {
        if (!r.success) return;
        const messages = r.data.messages || [];
        let html = '';
        let lastDate = '';
        messages.forEach(m => {
            if (m.date !== lastDate) {
                html += `<div class="wa-date-chip">${TT.escHtml(m.date)}</div>`;
                lastDate = m.date;
            }
            const mine = !!m.is_mine;
            html += `<div class="wa-bubble ${mine ? 'wa-mine' : 'wa-theirs'}">`;
            if (!mine && Messenger.role !== 'parent') {
                html += `<div class="wa-sender">${TT.escHtml(m.display_as || '')}</div>`;
            }
            html += Messenger.attachmentHtml(m.attachment);
            if (m.message_text) {
                html += `<div class="wa-caption">${TT.escHtml(m.message_text)}</div>`;
            }
            html += `<div class="wa-meta-row"><span class="wa-time">${TT.escHtml(m.time || '')}</span>${Messenger.ticks(m)}</div></div>`;
        });
        const el = document.getElementById('waThread');
        const nearBottom = el && (el.scrollHeight - el.scrollTop - el.clientHeight < 80);
        $('#waThread').html(html || '<div class="wa-empty">No messages yet. Say hello 👋</div>');
        if (el && (nearBottom || true)) el.scrollTop = el.scrollHeight;
    });
};

Messenger.send = function() {
    const text = ($('#waInput').val() || '').trim();
    const file = Messenger.pendingFile;
    if (!text && !file) return;
    const fd = new FormData();
    fd.append('action', 'send_message');
    fd.append('message_text', text);
    fd.append('conversation_id', Messenger.convoId || '');
    if (file) fd.append('attachment', file);
    const $btn = $('#waSend');
    $btn.prop('disabled', true);
    TT.api('MessageController.php', fd, { silent: true }).done(function(r) {
        $btn.prop('disabled', false);
        if (r.success) {
            $('#waInput').val('');
            Messenger.clearAttach();
            if (!Messenger.convoId) Messenger.convoId = r.data.conversation_id;
            Messenger.loadMessages();
            Messenger.loadConversations();
        }
    }).fail(function() { $btn.prop('disabled', false); });
};

Messenger.startNew = function() {
    Messenger.convoId = '';
    Messenger.convoName = 'Think & Tinker';
    $('body').addClass('wa-chat-open');
    $('#waListPane').addClass('is-hidden');
    $('#waChatPane').removeClass('is-hidden');
    $('#waChatName').text(Messenger.convoName);
    $('#waThread').html('<div class="wa-empty">Type your first message below.</div>');
    $('#waInput').focus();
};

window.Messenger = Messenger;
