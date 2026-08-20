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
</script>
</body>
</html>
