<?php $pageTitle='Messages'; $currentModule='messages'; require_once __DIR__.'/../templates/header-hub.php'; ?>

<div class="page-header"><h1>Messages</h1></div>

<div style="display:grid; grid-template-columns: 320px 1fr; gap:0; height: calc(100vh - 180px); border-radius: var(--radius-lg); overflow:hidden; box-shadow: var(--shadow-sm);">

    <!-- Conversation List -->
    <div style="background:#FFF; border-right:1px solid var(--cloud-gray); display:flex; flex-direction:column;">
        <div style="padding:12px; border-bottom:1px solid var(--cloud-gray);">
            <div class="search-bar" style="max-width:100%;"><span class="search-icon"><?= ttIcon('search', 'tt-icon', 16) ?></span><input type="text" placeholder="Search conversations..." id="convoSearch"></div>
        </div>
        <div id="convoList" style="flex:1; overflow-y:auto;"></div>
    </div>

    <!-- Chat Area -->
    <div style="background:#F8F8F8; display:flex; flex-direction:column;">
        <div id="chatHeader" style="padding:14px 20px; background:#FFF; border-bottom:1px solid var(--cloud-gray); font-weight:700; font-size:0.9375rem;">
            Select a conversation
        </div>
        <div id="chatMessages" style="flex:1; overflow-y:auto; padding:20px; display:flex; flex-direction:column; gap:8px;">
            <div class="empty-state" style="margin:auto;"><div class="icon-badge"><?= ttIcon('message', 'tt-icon', 26) ?></div><p>Select a conversation to view messages.</p></div>
        </div>
        <div id="chatInput" style="padding:12px 20px; background:#FFF; border-top:1px solid var(--cloud-gray); display:none;">
            <div style="display:flex; gap:8px;">
                <input type="text" id="msgInput" class="form-input" placeholder="Type a message..." style="flex:1;" onkeydown="if(event.key==='Enter')sendMsg()">
                <button class="btn btn-primary btn-sm" onclick="sendMsg()">Send</button>
            </div>
        </div>
    </div>
</div>

<style>
.convo-item { display:flex; align-items:center; gap:12px; padding:14px 16px; border-bottom:1px solid #F0F0F0; cursor:pointer; transition:background 0.15s; }
.convo-item:hover { background:#FAFAFA; }
.convo-item.active { background:rgba(26,175,160,0.08); border-left:3px solid var(--tinker-teal); }
.convo-name { font-weight:700; font-size:0.8125rem; }
.convo-preview { font-size:0.75rem; color:#999; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }
.convo-time { font-size:0.625rem; color:#999; margin-left:auto; white-space:nowrap; }
.convo-unread { background:var(--story-coral); color:#FFF; font-size:0.5625rem; font-weight:700; min-width:18px; height:18px; border-radius:9px; display:flex; align-items:center; justify-content:center; padding:0 5px; }
.msg-bubble { max-width:70%; padding:10px 14px; border-radius:16px; font-size:0.8125rem; line-height:1.5; position:relative; }
.msg-mine { background:var(--tinker-teal); color:#FFF; border-bottom-right-radius:4px; align-self:flex-end; }
.msg-theirs { background:#FFF; color:var(--charcoal); border-bottom-left-radius:4px; align-self:flex-start; box-shadow:0 1px 3px rgba(0,0,0,0.08); }
.msg-time { font-size:0.625rem; opacity:0.7; margin-top:4px; }
.msg-sender { font-size:0.625rem; font-weight:700; color:var(--tinker-teal); margin-bottom:2px; }
@media(max-width:768px){ div[style*="grid-template-columns: 320px"]{ grid-template-columns:1fr!important; } }
</style>

<script>
let currentConvoId = null;
let pollTimer = null;

function loadConversations(){
    TT.get('MessageController.php',{action:'get_conversations'}).done(function(r){
        if(!r.success)return;
        if(!r.data.conversations.length){$('#convoList').html('<div style="padding:24px;text-align:center;color:#999;font-size:0.875rem;">No conversations yet.</div>');return;}
        let h='';r.data.conversations.forEach(c=>{
            const active=c.conversation_id===currentConvoId?'active':'';
            const name=c.parent_name||c.sender_name||'Unknown';
            h+=`<div class="convo-item ${active}" onclick="openConvo('${c.conversation_id}','${TT.escHtml(name)}')">
                <div class="avatar avatar-sm avatar-teal">${TT.initials(name)}</div>
                <div style="flex:1;min-width:0;"><div class="convo-name">${TT.escHtml(name)}</div><div class="convo-preview">${TT.escHtml(c.last_message||'')}</div></div>
                <div style="text-align:right;"><div class="convo-time">${c.time_ago}</div>${c.unread>0?`<div class="convo-unread">${c.unread}</div>`:''}</div>
            </div>`;
        });$('#convoList').html(h);
    });
}

function openConvo(id,name){
    currentConvoId=id;
    $('#chatHeader').html(`<strong>${TT.escHtml(name)}</strong> <span style="font-weight:400;font-size:0.75rem;color:#999;">— replies show as "Think & Tinker"</span>`);
    $('#chatInput').show();
    loadMessages();
    // Highlight active
    $('.convo-item').removeClass('active');
    $(`.convo-item`).each(function(){if($(this).attr('onclick')?.includes(id))$(this).addClass('active');});
    // Start polling
    if(pollTimer)clearInterval(pollTimer);
    pollTimer=setInterval(pollNewMessages,10000);
}

function loadMessages(){
    TT.get('MessageController.php',{action:'get_messages',conversation_id:currentConvoId}).done(function(r){
        if(!r.success)return;renderMessages(r.data.messages);
    });
}

function renderMessages(messages){
    if(!messages.length){$('#chatMessages').html('<div class="empty-state" style="margin:auto;"><p>No messages yet. Start the conversation.</p></div>');return;}
    let h='',lastDate='';
    messages.forEach(m=>{
        if(m.date!==lastDate){h+=`<div style="text-align:center;font-size:0.6875rem;color:#999;margin:12px 0;">${m.date}</div>`;lastDate=m.date;}
        const cls=m.is_mine?'msg-mine':'msg-theirs';
        const readReceipt = m.is_mine ? (m.is_read ? '<span style="color:var(--tinker-teal);margin-left:4px;">✓✓</span>' : '<span style="color:#999;margin-left:4px;">✓</span>') : '';
        h+=`<div class="msg-bubble ${cls}">`;
        if(!m.is_mine)h+=`<div class="msg-sender">${TT.escHtml(m.display_as)}</div>`;
        h+=`${TT.escHtml(m.message_text)}<div class="msg-time">${m.time}${readReceipt}</div></div>`;
    });
    $('#chatMessages').html(h);
    const el=document.getElementById('chatMessages');el.scrollTop=el.scrollHeight;
}

function sendMsg(){
    const text=$('#msgInput').val().trim();if(!text||!currentConvoId)return;
    TT.api('MessageController.php',{action:'send_message',conversation_id:currentConvoId,message_text:text},{silent:true}).done(function(r){
        if(r.success){$('#msgInput').val('');loadMessages();loadConversations();}
    });
}

function pollNewMessages(){
    if(!currentConvoId)return;
    TT.get('MessageController.php',{action:'get_messages',conversation_id:currentConvoId}).done(function(r){
        if(r.success)renderMessages(r.data.messages);
    });
}

$(function(){loadConversations();});
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
