<?php /** templates/footer-teacher.php */ ?>
</main>
<footer class="site-footer" style="margin-top: var(--space-6);">
    <div class="container" style="padding: var(--space-4) 0; text-align: center;">
        <p style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">&copy; <?= date('Y') ?> <?= htmlspecialchars(getSetting('business_name', 'Think & Tinker Kids Club')) ?>. All Rights Reserved.</p>
    </div>
</footer>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<?php if (isset($teacherUser)): ?>
<script src="<?= APP_URL ?>/assets/js/notification.js"></script>
<script src="<?= APP_URL ?>/assets/js/teacher.js"></script>
<?php endif; ?>
<script>
    TT.config.apiBase = '<?= APP_URL ?>/api';
    TT.config.csrfToken = '<?= CSRF_TOKEN ?>';
    <?php if (isset($teacherUser)): ?>TT.notifications.init();<?php endif; ?>
</script>
</body>
</html>
