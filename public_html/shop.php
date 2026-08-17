<?php $pageTitle='Bookstore'; require_once __DIR__.'/includes/config.php'; include __DIR__.'/templates/header.php'; ?>
<section class="section" style="background:linear-gradient(135deg,#3498DB,#2980B9);color:#FFF;padding:60px 0;">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div><h1 style="color:#FFF;margin-bottom:4px;">📚 Bookstore</h1><p style="opacity:.8;">Textbooks, STEM kits, flashcards, and learning tools — delivered across Lagos.</p></div>
        <a href="<?= APP_URL ?>/cart.php" class="btn btn-lg" style="background:#FFF;color:var(--deep-navy);position:relative;">🛒 Cart <span id="cartCount" class="badge badge-red" style="position:absolute;top:-6px;right:-6px;display:none;">0</span></a>
    </div>
</section>
<section class="section"><div class="container">
    <div class="shop-filters" id="shopFilters">
        <span class="filter-chip active" onclick="Shop.filterCategory('',this)">All</span>
        <span class="filter-chip" onclick="Shop.filterCategory('workbook',this)">Workbooks</span>
        <span class="filter-chip" onclick="Shop.filterCategory('textbook',this)">Textbooks</span>
        <span class="filter-chip" onclick="Shop.filterCategory('stationery',this)">Stationery</span>
        <span class="filter-chip" onclick="Shop.filterCategory('stem_kit',this)">STEM Kits</span>
        <span class="filter-chip" onclick="Shop.filterCategory('flashcard',this)">Flashcards</span>
    </div>
    <div class="product-grid" id="productGrid"><div class="spinner mx-auto" style="grid-column:1/-1;"></div></div>
</div></section>
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/shop.css">
<script src="<?= APP_URL ?>/assets/js/shop.js"></script>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';TT.config.siteUrl='<?= APP_URL ?>';
$(function(){Shop.loadProducts();TT.get('ShopController.php',{action:'get_cart'}).done(r=>{if(r.data?.cart?.length)Shop.updateCartCount(r.data.cart.length);});});
</script>
<?php include __DIR__.'/templates/footer.php'; ?>
