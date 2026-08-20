<?php
/**
 * templates/footer-parent.php — Closes parent portal page + bottom nav
 */
?>
</div><!-- /.portal-content -->

<nav class="bottom-nav no-print">
    <a href="<?= APP_URL ?>/parent/" class="nav-item <?= ($currentTab ?? '') === 'home' ? 'active' : '' ?>">
        <span class="nav-icon"><?= ttIcon('home', 'tt-icon', 22) ?></span>
        <span>Home</span>
    </a>
    <a href="<?= APP_URL ?>/parent/calendar.php" class="nav-item <?= ($currentTab ?? '') === 'calendar' ? 'active' : '' ?>">
        <span class="nav-icon"><?= ttIcon('calendar', 'tt-icon', 22) ?></span>
        <span>Calendar</span>
    </a>
    <a href="<?= APP_URL ?>/parent/messages.php" class="nav-item <?= ($currentTab ?? '') === 'messages' ? 'active' : '' ?>">
        <span class="nav-icon"><?= ttIcon('message', 'tt-icon', 22) ?></span>
        <span>Messages</span>
        <?php if (($unreadMessages ?? 0) > 0): ?>
            <span class="nav-badge"><?= $unreadMessages ?></span>
        <?php endif; ?>
    </a>
    <a href="<?= APP_URL ?>/parent/payments.php" class="nav-item <?= ($currentTab ?? '') === 'payments' ? 'active' : '' ?>">
        <span class="nav-icon"><?= ttIcon('card', 'tt-icon', 22) ?></span>
        <span>Payments</span>
    </a>
    <a href="<?= APP_URL ?>/parent/child.php" class="nav-item <?= ($currentTab ?? '') === 'child' ? 'active' : '' ?>">
        <span class="nav-icon"><?= ttIcon('smile', 'tt-icon', 22) ?></span>
        <span>My Child</span>
    </a>
</nav>

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/assets/js/notification.js"></script>
<script src="<?= APP_URL ?>/assets/js/parent.js"></script>
<script>
    TT.config.apiBase = '<?= APP_URL ?>/api';
    TT.config.csrfToken = '<?= CSRF_TOKEN ?>';
    TT.notifications.init();

    // Mobile-nav guard. Media queries read the LAYOUT viewport, which some
    // Android browsers report as ~980px in "Desktop site" mode (or when the
    // old `user-scalable=no` meta confused them). visualViewport.width is the
    // ACTUAL visible width in CSS px, so use it to force the desktop sub-nav
    // off whenever the real screen is phone/tablet-sized.
    (function () {
        function viewportWidth() {
            return (window.visualViewport && window.visualViewport.width) || window.innerWidth || 0;
        }
        function enforcePortalNav() {
            var sub = document.querySelector('.portal-subnav');
            if (!sub) return;
            sub.classList.toggle('is-mobile-hidden', viewportWidth() < 769);
        }
        enforcePortalNav();
        window.addEventListener('resize', enforcePortalNav);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', enforcePortalNav);
        }
    })();
</script>
</body>
</html>
