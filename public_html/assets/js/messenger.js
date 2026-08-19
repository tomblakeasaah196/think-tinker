/**
 * assets/js/messenger.js — WhatsApp-style chat for parent portal
 */
'use strict';
const Messenger = window.Messenger || {};

Messenger.convoId = null;
Messenger.pollTimer = null;

Messenger.init = function() {
    Messenger.loadConversations();
    $(document).on('click', '.msg-convo-item', function() {
        Messenger.convoId = $(this).data('id');
        Messenger.loadMessages();
        $('.msg-convo-item').removeClass('active');
        $(this).addClass('active');
        $('#msgChatArea').show();
        if (Messenger.pollTimer) clearInterval(Messenger.pollTimer);
        Messenger.pollTimer = setInterval(() => Messenger.loadMessages(), 10000);
    });
    $(document).on('keydown', '#parentMsgInput', function(e) { if (e.key === 'Enter') Messenger.send(); });
};

Messenger.loadConversations = function() {
    TT.get('MessageController.php', { action: 'get_conversations' }).done(function(r) {
        if (!r.success) return;
        let html = '';
        if (!r.data.conversations.length) {
            html = '<div style="padding:20px; text-align:center; color:#999;">No messages yet. Start a conversation below.</div>';
        } else {
            r.data.conversations.forEach(c => {
                // The thread is always titled "Think & Tinker" (there's only
                // one, with the business) but the preview line used to show
                // your own last message with no clue you were the sender —
                // "Think & Tinker: Can I have a session Sunday?" read as if
                // the business had asked you that. Prefix "You:" when the
                // parent sent the most recent message.
                const youSent = c.last_sender_type === 'parent';
                const preview = `${youSent ? '<strong>You:</strong> ' : ''}${TT.escHtml(c.last_message||'')}`;
                html += `<div class="msg-convo-item ${c.conversation_id===Messenger.convoId?'active':''}" data-id="${c.conversation_id}">
                    <div class="avatar avatar-sm avatar-teal">TT</div>
                    <div style="flex:1;min-width:0;"><div style="font-weight:700;font-size:0.8125rem;">Think & Tinker</div><div style="font-size:0.75rem;color:#999;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${preview}</div></div>
                    <div style="text-align:right;"><div style="font-size:0.625rem;color:#999;">${c.time_ago}</div>${c.unread>0?`<div class="convo-unread" style="margin-top:4px;">${c.unread}</div>`:''}</div></div>`;
            });
        }
        $('#parentConvoList').html(html);
        // Parents only ever have this one thread. If it's the only row and
        // nothing is open yet, open it automatically instead of making them
        // click into the "obvious" single conversation to find out whether
        // there's a reply — that extra click is what made replies feel like
        // they'd vanished.
        if (r.data.conversations.length === 1 && !Messenger.convoId) {
            $(`.msg-convo-item[data-id="${r.data.conversations[0].conversation_id}"]`).trigger('click');
        }
    });
};

Messenger.loadMessages = function() {
    if (!Messenger.convoId) return;
    TT.get('MessageController.php', { action: 'get_messages', conversation_id: Messenger.convoId }).done(function(r) {
        if (!r.success) return;
        let html = '';
        (r.data.messages || []).forEach(m => {
            const cls = m.is_mine ? 'msg-mine' : 'msg-theirs';
            html += `<div class="msg-bubble ${cls}">${TT.escHtml(m.message_text)}<div class="msg-time">${m.time}</div></div>`;
        });
        $('#parentMsgList').html(html || '<div style="text-align:center;color:#999;margin:auto;">No messages in this conversation.</div>');
        const el = document.getElementById('parentMsgList');
        if (el) el.scrollTop = el.scrollHeight;
    });
};

Messenger.send = function() {
    const text = $('#parentMsgInput').val().trim();
    if (!text) return;
    const convoId = Messenger.convoId || '';
    TT.api('MessageController.php', { action: 'send_message', conversation_id: convoId, message_text: text }, { silent: true }).done(function(r) {
        if (r.success) {
            $('#parentMsgInput').val('');
            if (!Messenger.convoId) Messenger.convoId = r.data.conversation_id;
            Messenger.loadMessages();
            Messenger.loadConversations();
        }
    });
};

Messenger.startNew = function() {
    Messenger.convoId = '';
    $('#msgChatArea').show();
    $('#parentMsgList').html('<div style="text-align:center;color:#999;margin:auto;">Type your first message below.</div>');
    $('#parentMsgInput').focus();
};

window.Messenger = Messenger;
