<?php require_once __DIR__.'/includes/config.php'; $slug=$_GET['slug']??''; if(!$slug){header('Location:'.APP_URL.'/shop.php');exit;}
$product=dbFetchOne("SELECT * FROM products WHERE slug=? AND is_active=1 AND deleted_at IS NULL",[$slug]);
if(!$product){$pageTitle='Not Found';include __DIR__.'/templates/header.php';echo '<section class="section"><div class="container"><div class="empty-state"><h3>Product Not Found</h3><a href="'.APP_URL.'/shop.php" class="btn btn-primary mt-2">Back to Shop</a></div></div></section>';include __DIR__.'/templates/footer.php';exit;}
$pageTitle=$product['name'];include __DIR__.'/templates/header.php';
$imgUrl=$product['cover_image']?APP_URL.'/'.$product['cover_image']:'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&q=80';
?>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/shop.css">
<section class="section" style="padding-top:24px;"><div class="container">
    <a href="<?= APP_URL ?>/shop.php" style="font-size:.8125rem;color:var(--tinker-teal);font-weight:700;">← Back to Shop</a>
    <div class="product-detail mt-3">
        <div class="pd-image"><img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($product['name']) ?>"></div>
        <div class="pd-info">
            <span class="badge badge-teal mb-1"><?= htmlspecialchars($product['category']) ?></span>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <div style="margin:12px 0;"><span class="price price-lg"><?= formatNaira($product['price']) ?></span>
            <?php if($product['compare_price']): ?><span class="price-old" style="margin-left:8px;"><?= formatNaira($product['compare_price']) ?></span><?php endif; ?></div>
            <div class="pd-meta"><?php if($product['grade_level']): ?>Grade: <?= htmlspecialchars($product['grade_level']) ?> · <?php endif; ?>
            <?= $product['stock_quantity']>0?'<span style="color:var(--leaf-green);font-weight:700;">In Stock ('.$product['stock_quantity'].')</span>':'<span style="color:var(--story-coral);font-weight:700;">Out of Stock</span>' ?></div>
            <div class="pd-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></div>
            <?php if($product['stock_quantity']>0): ?>
            <div class="pd-actions">
                <div class="qty-control"><button onclick="let i=document.getElementById('qty');i.value=Math.max(1,parseInt(i.value)-1);">−</button><input type="number" id="qty" value="1" min="1" max="<?= $product['stock_quantity'] ?>"><button onclick="let i=document.getElementById('qty');i.value=Math.min(<?= $product['stock_quantity'] ?>,parseInt(i.value)+1);">+</button></div>
                <button class="btn btn-primary btn-lg" onclick="Shop.addToCart(<?= $product['id'] ?>,parseInt(document.getElementById('qty').value))">Add to Cart 🛒</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div></section>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/assets/js/shop.js"></script>
<script>TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';TT.config.siteUrl='<?= APP_URL ?>';</script>
<?php include __DIR__.'/templates/footer.php'; ?>
