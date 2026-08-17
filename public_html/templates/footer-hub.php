<?php /** templates/footer-hub.php — Closes Hub layout */ ?>
    </div><!-- /.hub-content -->
</div><!-- /.hub-main -->

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/assets/js/notification.js"></script>
<script src="<?= HUB_URL ?>/assets/js/hub.js"></script>
<script>
    TT.config.apiBase = '<?= HUB_URL ?>/api';
    TT.config.csrfToken = '<?= CSRF_TOKEN ?>';
    TT.notifications.init();
</script>
</body>
</html>
