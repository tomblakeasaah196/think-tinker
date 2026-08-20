<?php $pageTitle='Messages'; $currentModule='messages'; require_once __DIR__.'/../templates/header-hub.php'; ?>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/messenger.css">
<script>document.querySelector('.hub-content')?.classList.add('wa-hub-content');</script>

<div class="wa-app" id="waApp">
    <div class="wa-pane wa-list-pane" id="waListPane">
        <div class="wa-list-header">
            <h2>Chats</h2>
        </div>
        <div class="wa-search-wrap"><input type="search" class="wa-search" id="waSearch" placeholder="Search conversations" autocomplete="off"></div>
        <div class="wa-convo-list" id="waConvoList"></div>
    </div>
    <div class="wa-pane is-hidden" id="waChatPane">
        <div class="wa-chat-header">
            <button type="button" class="wa-back" aria-label="Back">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <div class="wa-avatar sm" id="waChatAvatar">P</div>
            <div>
                <div class="wa-chat-title" id="waChatName">Select a chat</div>
                <div class="wa-chat-sub">Replies show as Think &amp; Tinker</div>
            </div>
        </div>
        <div class="wa-thread" id="waThread"></div>
        <div class="wa-preview" id="waPreview">
            <img id="waPreviewThumb" alt="">
            <span class="wa-preview-name" id="waPreviewName"></span>
            <button type="button" class="wa-preview-x" id="waPreviewClear" aria-label="Remove">&times;</button>
        </div>
        <div class="wa-composer">
            <label class="wa-attach" title="Attach">
                <input type="file" id="waFile" accept="image/jpeg,image/png,image/webp,application/pdf" hidden>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
            </label>
            <textarea id="waInput" class="wa-input" rows="1" placeholder="Type a message"></textarea>
            <button type="button" class="wa-send" id="waSend" onclick="Messenger.send()" aria-label="Send">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/></svg>
            </button>
        </div>
    </div>
</div>
<div class="wa-lightbox" id="waLightbox"><button type="button" class="wa-lightbox-close" id="waLightboxClose">&times;</button><img id="waLightboxImg" alt=""></div>

<script src="<?= APP_URL ?>/assets/js/messenger.js"></script>
<script>
$(function(){
    Messenger.init({ role: 'staff' });
});
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
