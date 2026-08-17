<?php $pageTitle='Checkout'; require_once __DIR__.'/includes/config.php'; require_once __DIR__.'/includes/auth.php';
$user=getCurrentUser();if(!$user){header('Location:'.APP_URL.'/parent/login.php');exit;}
include __DIR__.'/templates/header.php'; ?>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/shop.css">
<section class="section" style="padding-top:24px;"><div class="container" style="max-width:640px;">
    <h1 class="mb-3">Checkout</h1>
    <form id="checkoutForm">
        <input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
        <div class="card mb-3"><div class="card-body">
            <h3 style="font-size:1rem;margin-bottom:16px;">Delivery Details</h3>
            <div class="form-group"><label class="form-label">Delivery Address *</label><textarea name="delivery_address" class="form-textarea" rows="3" required placeholder="Full address including landmark"></textarea></div>
            <div class="form-group"><label class="form-label">Phone Number *</label><input type="tel" name="delivery_phone" class="form-input" value="<?= htmlspecialchars($user['phone']) ?>" required></div>
            <div class="form-group"><label class="form-label">Delivery Notes</label><input type="text" name="delivery_notes" class="form-input" placeholder="e.g. Call before delivery"></div>
        </div></div>
        <button type="submit" class="btn btn-primary btn-block btn-lg">Place Order</button>
        <p class="text-center text-sm text-muted mt-2">You'll pay via bank transfer after placing the order.</p>
    </form>
    <div id="checkoutResult" style="display:none;" class="card mt-3"><div class="card-body"></div></div>
</div></section>
<script src="<?= APP_URL ?>/assets/js/shop.js"></script>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';TT.config.siteUrl='<?= APP_URL ?>';
$('#checkoutForm').on('submit',function(e){e.preventDefault();Shop.checkout();});
</script>
<?php include __DIR__.'/templates/footer.php'; ?>
