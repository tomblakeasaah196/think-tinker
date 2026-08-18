<?php
/**
 * templates/footer.php — Public website footer
 */
$businessName = getSetting('business_name', 'Think & Tinker Kids Club');
$businessAddress = getSetting('business_address', 'Lagos, Nigeria');
$businessPhone = getSetting('business_phone', '');
$businessEmail = getSetting('business_email', '');
$businessSocial = getSetting('business_social', '');
$year = date('Y');
?>
</main>

<footer class="site-footer no-print">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col footer-brand">
                <h3>Think & Tinker</h3>
                <p class="footer-tagline">Think. Tinker. Shine.</p>
                <p class="footer-desc">Building curious minds through play, creativity, and exploration. Ages 1–14.</p>
            </div>

            <div class="footer-col">
                <h4>Services</h4>
                <a href="<?= APP_URL ?>/services/tutorials.php">Home Tutorials</a>
                <a href="<?= APP_URL ?>/services/stem-club.php">STEM & Reading Club</a>
                <a href="<?= APP_URL ?>/services/consulting.php">Educational Consulting</a>
                <a href="<?= APP_URL ?>/services/consult-booklets.php">Consult Booklets</a>
                <a href="<?= APP_URL ?>/shop.php">Bookstore</a>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <a href="<?= APP_URL ?>/about.php">About Us</a>
                <a href="<?= APP_URL ?>/blog.php">Blog</a>
                <a href="<?= APP_URL ?>/contact.php">Contact</a>
                <a href="<?= APP_URL ?>/parent/login.php">Parent Portal</a>
                <a href="<?= APP_URL ?>/teacher/">For Teachers</a>
                <a href="<?= APP_URL ?>/school/">For Schools</a>
            </div>

            <div class="footer-col">
                <h4>Contact</h4>
                <?php if ($businessAddress): ?><p><?= htmlspecialchars($businessAddress) ?></p><?php endif; ?>
                <?php if ($businessPhone): ?><p><a href="tel:<?= htmlspecialchars($businessPhone) ?>"><?= htmlspecialchars($businessPhone) ?></a></p><?php endif; ?>
                <?php if ($businessEmail): ?><p><a href="mailto:<?= htmlspecialchars($businessEmail) ?>"><?= htmlspecialchars($businessEmail) ?></a></p><?php endif; ?>
                <a href="<?= whatsappLink('Hello! I\'d like to learn more about Think & Tinker.') ?>" target="_blank" class="btn btn-sm btn-success" style="margin-top: 8px;">💬 WhatsApp Us</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= $year ?> <?= htmlspecialchars($businessName) ?>. All Rights Reserved.</p>
            <p>Think & Tinker Kids LTD · RC <?= htmlspecialchars(getSetting('business_rc', '')) ?></p>
        </div>
    </div>
</footer>

<style>
.site-footer { background: var(--deep-navy); color: rgba(255,255,255,0.7); padding: var(--space-8) 0 var(--space-4); }
.footer-grid { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1.2fr; gap: var(--space-4); margin-bottom: var(--space-5); }
.footer-col h3 { color: var(--tinker-teal); font-size: 1.375rem; margin-bottom: var(--space-1); }
.footer-col h4 { color: #FFF; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: var(--space-2); }
.footer-tagline { color: var(--spark-orange); font-weight: 700; font-size: 0.875rem; margin-bottom: var(--space-1); }
.footer-desc { font-size: 0.875rem; line-height: 1.6; }
.footer-col a { display: block; color: rgba(255,255,255,0.6); font-size: 0.875rem; padding: 4px 0; transition: color 0.2s; }
.footer-col a:hover { color: var(--tinker-teal); }
.footer-col p { font-size: 0.875rem; margin-bottom: 4px; }
.footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); padding-top: var(--space-3); text-align: center; font-size: 0.75rem; }
.footer-bottom p { margin-bottom: 4px; }
@media (max-width: 768px) {
    .footer-grid { grid-template-columns: 1fr 1fr; gap: var(--space-3); }
}
@media (max-width: 480px) {
    .footer-grid { grid-template-columns: 1fr; }
}
</style>

<!-- main.js now loads in header.php, before the per-page inline scripts
     that set TT.config.*. Loading it again here would re-run
     `TT.config = {…}` and wipe those values (and re-declaring `const TT`
     throws), so it is deliberately not included a second time. -->
<script>
    // Fallback only: per-page blocks normally set these themselves.
    TT.config.apiBase   = TT.config.apiBase   || '<?= APP_URL ?>/api';
    TT.config.csrfToken = TT.config.csrfToken || '<?= CSRF_TOKEN ?>';
</script>
</body>
</html>
