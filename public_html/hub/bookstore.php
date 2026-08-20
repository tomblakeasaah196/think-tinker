<?php $pageTitle='Bookstore'; $currentModule='bookstore'; require_once __DIR__.'/../templates/header-hub.php'; ?>
<div class="page-header"><h1>Bookstore Management</h1><div class="actions"><button class="btn btn-primary btn-sm" onclick="addProduct()">+ Add Product</button></div></div>
<div class="tabs"><div class="tab active" data-tab="tabProducts">Products</div><div class="tab" data-tab="tabOrders">Orders</div></div>

<div class="tab-content active" id="tabProducts">
<div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th></th></tr></thead><tbody id="productsTable"></tbody></table></div></div>
</div>

<div class="tab-content" id="tabOrders">
<div class="filter-bar"><select class="form-select" id="orderStatus" onchange="loadOrders()"><option value="">All</option><option value="pending_payment">Pending Payment</option><option value="processing">Processing</option><option value="shipped">Shipped</option><option value="delivered">Delivered</option></select></div>
<div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Order #</th><th>Client</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody id="ordersTable"></tbody></table></div></div>
</div>

<div class="modal-overlay" id="productModal"><div class="modal modal-lg">
<div class="modal-header"><h3 id="prodModalTitle">Add Product</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
<div class="modal-body"><form id="productForm" enctype="multipart/form-data"><input type="hidden" name="action" value="add_product"><input type="hidden" name="id" value="">
    <div class="form-group"><label class="form-label">Product Name *</label><input type="text" name="name" class="form-input" required></div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-textarea" rows="3"></textarea></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Category</label><select name="category" class="form-select"><option value="workbooks">Workbooks</option><option value="exercise_books">Exercise Books</option><option value="textbooks">Textbooks</option><option value="stationery">Stationery</option><option value="educational_toys">Educational Toys</option><option value="recommended_reading">Recommended Reading</option><option value="consult_booklets">Consultation Booklets</option></select></div><div class="form-group"><label class="form-label">Grade Level</label><input type="text" name="grade_level" class="form-input" placeholder="e.g. Nursery, Primary 1-3"></div></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Subject</label><input type="text" name="subject" class="form-input" placeholder="e.g. Mathematics"></div><div class="form-group"><label class="form-label">Series</label><input type="text" name="series" class="form-input" placeholder="e.g. Collins Mental Maths"></div></div>
    <div class="form-row"><div class="form-group"><label class="form-label">Price (₦) *</label><input type="number" name="price" class="form-input" required></div><div class="form-group"><label class="form-label">Stock Quantity</label><input type="number" name="stock_quantity" class="form-input" value="0"></div></div>
    <div class="form-group"><label class="form-label">Cover Image</label><input type="file" name="cover_image" class="form-input" accept="image/*"></div>
    <div class="checkbox-group"><input type="checkbox" name="is_featured" value="1" id="isFeatured"><label for="isFeatured">Featured product</label></div>
</form></div>
<div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="saveProduct()">Save</button></div>
</div></div>

<script>
function loadProducts(){TT.get('ShopController.php',{action:'get_products',admin:1}).done(function(r){if(!r.success)return;let h='';r.data.products.forEach(p=>{h+=`<tr><td><strong>${TT.escHtml(p.name)}</strong></td><td><span class="badge badge-navy">${p.category}</span></td><td class="fw-700">${TT.formatNaira(p.price)}</td><td>${p.stock_quantity}</td><td>${p.is_featured==1?'⭐':'-'}</td><td class="row-actions"><button class="btn btn-ghost btn-sm" onclick="editProduct(${p.id})">Edit</button></td></tr>`;});$('#productsTable').html(h||'<tr><td colspan="6"><div class="empty-state"><p>No products yet.</p></div></td></tr>');});}
function loadOrders(){TT.get('ShopController.php',{action:'get_orders',status:$('#orderStatus').val()}).done(function(r){if(!r.success)return;let h='';r.data.orders.forEach(o=>{const sc={pending_payment:'badge-orange',processing:'badge-blue',shipped:'badge-teal',delivered:'badge-green',cancelled:'badge-red'}[o.status]||'badge-gray';h+=`<tr><td class="fw-600">${TT.escHtml(o.order_number)}</td><td>${TT.escHtml((o.first_name||'')+' '+(o.last_name||''))}</td><td class="fw-700">${o.formatted_total}</td><td><span class="badge ${sc}">${o.status.replace('_',' ')}</span></td><td class="text-sm">${o.formatted_date}</td><td class="row-actions"><select class="form-select" style="height:32px;width:auto;font-size:0.75rem;" onchange="updateOrder(${o.id},this.value)"><option value="">Change...</option><option value="processing">Processing</option><option value="shipped">Shipped</option><option value="delivered">Delivered</option></select></td></tr>`;});$('#ordersTable').html(h||'<tr><td colspan="6"><div class="empty-state"><p>No orders.</p></div></td></tr>');});}
function addProduct(){TT.clearForm('#productForm');$('#prodModalTitle').text('Add Product');$('[name=action]','#productForm').val('add_product');$('[name=id]','#productForm').val('');TT.modal.open('#productModal');}
function editProduct(id){TT.get('ShopController.php',{action:'get_product',id}).done(r=>{if(!r.success)return;const p=r.data.product;$('#prodModalTitle').text('Edit Product');$('[name=action]','#productForm').val('update_product');$('[name=id]','#productForm').val(p.id);Object.keys(p).forEach(k=>{if(k==='cover_image')return;const $f=$(`[name="${k}"]`,'#productForm');if($f.length)$f.val(p[k]);});$('#isFeatured').prop('checked',p.is_featured==1);TT.modal.open('#productModal');});}
function saveProduct(){TT.submitForm('#productForm','ShopController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#productModal');loadProducts();TT.toast(r.message,'success');}}});}
function updateOrder(id,status){if(!status)return;TT.api('ShopController.php',{action:'update_order_status',id,status}).done(r=>{if(r.success){loadOrders();TT.toast(r.message,'success');}});}
$(function(){loadProducts();loadOrders();});
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
