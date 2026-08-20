<?php $pageTitle='Messages'; $currentTab='messages'; $bodyClass='wa-messages wa-single-thread'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/messenger.css">

<!-- Parent messaging: exactly ONE thread with the business. There is no
     conversation list, no search, no "new chat" FAB — this page IS the chat.
     It loads/creates the Think & Tinker thread immediately and fills the
     viewport (WhatsApp-style header, wallpaper, bubbles, ticks, composer). -->
<div class="wa-app wa-app--single" id="waApp">
    <div class="wa-pane" id="waChatPane">
        <div class="wa-chat-header">
            <a href="<?= APP_URL ?>/parent/" class="wa-back" aria-label="Back to home">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
            </a>
            <div class="wa-avatar sm">TT</div>
            <div class="wa-chat-heading">
                <div class="wa-chat-title" id="waChatName">Think &amp; Tinker</div>
                <div class="wa-chat-sub">Typically replies today</div>
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
            <textarea id="waInput" class="wa-input" rows="1" placeholder="Message"></textarea>
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
    Messenger.init({ role: 'parent', single: true });
    // "Ask a Question" (?new=1) opens this same thread with the composer focused.
    if (new URLSearchParams(location.search).get('new')) { Messenger.focusComposer(); }
});
</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
