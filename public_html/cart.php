<?php $pageTitle='Shopping Cart'; require_once __DIR__.'/includes/config.php'; include __DIR__.'/templates/header.php'; ?>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/shop.css">
<section class="section" style="padding-top:24px;"><div class="container" style="max-width:760px;">
    <a href="<?= APP_URL ?>/shop.php" style="font-size:.8125rem;color:var(--tinker-teal);font-weight:700;">← Continue Shopping</a>
    <h1 class="mt-2 mb-3">Your Cart</h1>
    <div id="cartItems"><div class="spinner mx-auto mt-4"></div></div>
    <div id="cartSummary" class="cart-summary" style="display:none;">
        <div class="cs-row"><span>Subtotal</span><span id="cartSubtotal">₦0</span></div>
        <div class="cs-row cs-total"><span>Total</span><span id="cartTotal">₦0</span></div>
        <a href="<?= APP_URL ?>/checkout.php" class="btn btn-primary btn-block mt-2">Proceed to Checkout →</a>
    </div>
</div></section>
<script src="<?= APP_URL ?>/assets/js/shop.js"></script>
<script>TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';TT.config.siteUrl='<?= APP_URL ?>';$(function(){Shop.loadCart();});</script>
<?php include __DIR__.'/templates/footer.php'; ?>
