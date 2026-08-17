/**
 * assets/js/shop.js — Bookstore cart and checkout
 */
'use strict';
const Shop = window.Shop || {};

Shop.loadProducts = function(filters = {}) {
    const params = Object.assign({ action: 'get_products' }, filters);
    TT.get('ShopController.php', params).done(function(r) {
        if (!r.success) return;
        let html = '';
        (r.data.products || []).forEach(p => {
            const img = p.image_url || 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&q=80';
            html += `<a href="${TT.config.siteUrl}/shop-product.php?slug=${p.slug}" class="product-card">
                ${p.is_featured == 1 ? '<span class="prod-badge">⭐ Featured</span>' : ''}
                <img src="${img}" alt="${TT.escHtml(p.name)}" class="prod-img" loading="lazy">
                <div class="prod-body">
                    <div class="prod-category">${TT.escHtml(p.category)}</div>
                    <div class="prod-name">${TT.escHtml(p.name)}</div>
                    <span class="prod-price">${p.formatted_price}</span>
                    ${p.compare_price ? `<span class="prod-old-price">${TT.formatNaira(p.compare_price)}</span>` : ''}
                </div>
            </a>`;
        });
        $('#productGrid').html(html || '<div class="empty-state"><div class="icon">📚</div><h3>No products found</h3><p>Try a different category.</p></div>');
    });
};

Shop.addToCart = function(productId, qty) {
    qty = qty || 1;
    TT.api('ShopController.php', { action: 'add_to_cart', product_id: productId, quantity: qty }).done(function(r) {
        if (r.success) {
            TT.toast('Added to cart!', 'success', 2000);
            Shop.updateCartCount(r.data.cart_count);
        }
    });
};

Shop.loadCart = function() {
    TT.get('ShopController.php', { action: 'get_cart' }).done(function(r) {
        if (!r.success) return;
        const cart = r.data.cart || [];
        if (!cart.length) {
            $('#cartItems').html('<div class="empty-state"><div class="icon">🛒</div><h3>Your cart is empty</h3><p>Browse our bookstore to find great learning materials.</p><a href="' + TT.config.siteUrl + '/shop.php" class="btn btn-primary mt-2">Browse Shop</a></div>');
            $('#cartSummary').hide();
            return;
        }
        let html = '', subtotal = 0;
        cart.forEach(item => {
            const lineTotal = item.price * item.quantity;
            subtotal += lineTotal;
            const img = item.image ? (TT.config.siteUrl + '/' + item.image) : 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=200&q=80';
            html += `<div class="cart-item">
                <img src="${img}" class="ci-img" alt="">
                <div class="ci-info">
                    <div class="ci-name">${TT.escHtml(item.name)}</div>
                    <div class="ci-price">${TT.formatNaira(item.price)} × ${item.quantity}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:700;">${TT.formatNaira(lineTotal)}</div>
                    <span class="ci-remove" onclick="Shop.removeItem(${item.product_id})">Remove</span>
                </div>
            </div>`;
        });
        $('#cartItems').html(html);
        $('#cartSubtotal').text(TT.formatNaira(subtotal));
        $('#cartTotal').text(TT.formatNaira(subtotal));
        $('#cartSummary').show();
    });
};

Shop.removeItem = function(productId) {
    TT.api('ShopController.php', { action: 'remove_from_cart', product_id: productId }).done(function(r) {
        if (r.success) { Shop.loadCart(); TT.toast('Item removed', 'info', 1500); }
    });
};

Shop.checkout = function() {
    const data = TT.formData('#checkoutForm');
    data.action = 'checkout';
    TT.api('ShopController.php', data).done(function(r) {
        if (r.success) {
            TT.toast(r.message, 'success', 5000);
            $('#checkoutForm').hide();
            let bankHtml = '<h3 class="mb-2">Complete Your Payment</h3><p>Transfer <strong>' + TT.formatNaira(r.data.total) + '</strong> to any account below, using <strong>' + r.data.order_number + '</strong> as reference.</p>';
            (r.data.bank_accounts || []).forEach(b => {
                bankHtml += `<div class="bank-item mt-2"><div class="bank-name">${TT.escHtml(b.bank_name)}</div><div class="acct-num" onclick="TT.copyText('${b.account_number}')">${b.account_number}</div><div class="acct-name">${TT.escHtml(b.account_name)}</div></div>`;
            });
            $('#checkoutResult').html(bankHtml).show();
        }
    });
};

Shop.updateCartCount = function(count) {
    const $badge = $('#cartCount');
    if (count > 0) { $badge.text(count).show(); } else { $badge.hide(); }
};

Shop.filterCategory = function(cat, el) {
    $('.filter-chip').removeClass('active');
    $(el).addClass('active');
    Shop.loadProducts({ category: cat });
};

window.Shop = Shop;
