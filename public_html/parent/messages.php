<?php $pageTitle='Messages'; $currentTab='messages'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<div class="flex items-center justify-between mb-3">
    <h2 style="font-size:1.25rem;">Messages</h2>
    <button class="btn btn-primary btn-sm" onclick="Messenger.startNew()">+ New Message</button>
</div>
<div id="parentConvoList" class="mb-3" style="border-radius:var(--radius-md);overflow:hidden;box-shadow:var(--shadow-sm);background:#FFF;"></div>
<div id="msgChatArea" style="display:none;">
    <div style="background:#FFF;border-radius:var(--radius-md);box-shadow:var(--shadow-sm);overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:1px solid var(--cloud-gray);font-weight:700;font-size:0.875rem;display:flex;align-items:center;justify-content:space-between;">
            Think & Tinker <button class="btn btn-ghost btn-sm" onclick="$('#msgChatArea').hide()">Close</button>
        </div>
        <div id="parentMsgList" style="height:300px;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:8px;background:#F8F8F8;"></div>
        <div style="padding:12px;display:flex;gap:8px;border-top:1px solid var(--cloud-gray);">
            <input type="text" id="parentMsgInput" class="form-input" placeholder="Type a message..." style="flex:1;">
            <button class="btn btn-primary btn-sm" onclick="Messenger.send()">Send</button>
        </div>
    </div>
</div>
<style>.msg-convo-item{display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid #F0F0F0;cursor:pointer;transition:background 0.15s;}.msg-convo-item:hover{background:#FAFAFA;}.msg-convo-item.active{background:rgba(26,175,160,0.08);}</style>
<script src="<?= APP_URL ?>/assets/js/messenger.js"></script>
<script>$(function(){ Messenger.init(); });</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
