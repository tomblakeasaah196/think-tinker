<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_products':      getProducts(); break;
    case 'get_product':       getProduct(); break;
    case 'add_product':       addProduct(); break;
    case 'update_product':    updateProduct(); break;
    case 'delete_product':    deleteProduct(); break;
    case 'get_cart':          getCart(); break;
    case 'add_to_cart':       addToCart(); break;
    case 'update_cart':       updateCart(); break;
    case 'remove_from_cart':  removeFromCart(); break;
    case 'checkout':          checkout(); break;
    case 'get_orders':        getOrders(); break;
    case 'get_order':         getOrder(); break;
    case 'update_order_status': updateOrderStatus(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getProducts(): void {
    $category = $_GET['category'] ?? ''; $grade = $_GET['grade'] ?? '';
    $sort = $_GET['sort'] ?? 'newest'; $search = $_GET['search'] ?? '';
    $where = "is_active = 1 AND deleted_at IS NULL"; $params = [];
    if ($category) { $where .= " AND category = ?"; $params[] = $category; }
    if ($grade) { $where .= " AND grade_level LIKE ?"; $params[] = "%$grade%"; }
    if ($search) { $where .= " AND (name LIKE ? OR description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
    $orderBy = match($sort) { 'price_asc' => 'price ASC', 'price_desc' => 'price DESC', 'featured' => 'is_featured DESC, created_at DESC', default => 'created_at DESC' };
    $products = dbFetchAll("SELECT id, name, slug, category, grade_level, price, compare_price, cover_image, is_featured FROM products WHERE $where ORDER BY $orderBy", $params);
    foreach ($products as &$p) { $p['formatted_price'] = formatNaira($p['price']); $p['image_url'] = $p['cover_image'] ? getUploadUrl($p['cover_image']) : null; }
    jsonResponse(true, '', ['products' => $products]);
}

function getProduct(): void {
    $slug = $_GET['slug'] ?? ''; $id = (int)($_GET['id'] ?? 0);
    if ($slug) $product = dbFetchOne("SELECT * FROM products WHERE slug = ? AND deleted_at IS NULL", [$slug]);
    elseif ($id) $product = dbFetchOne("SELECT * FROM products WHERE id = ? AND deleted_at IS NULL", [$id]);
    else jsonResponse(false, 'Product slug or ID required.');
    if (!$product) jsonResponse(false, 'Product not found.');
    $product['formatted_price'] = formatNaira($product['price']);
    $product['image_url'] = $product['cover_image'] ? getUploadUrl($product['cover_image']) : null;
    $product['gallery'] = $product['gallery_images'] ? json_decode($product['gallery_images'], true) : [];
    $related = dbFetchAll("SELECT id, name, slug, price, cover_image FROM products WHERE category = ? AND id != ? AND is_active = 1 AND deleted_at IS NULL LIMIT 4", [$product['category'], $product['id']]);
    jsonResponse(true, '', ['product' => $product, 'related' => $related]);
}

function addProduct(): void {
    $user = requireAuth(); requirePermission($user, 'bookstore', 'create'); validateCsrf();
    require_once __DIR__ . '/../includes/upload.php';
    $name = post('name'); if (!$name) jsonResponse(false, 'Product name required.');
    $data = ['name' => $name, 'slug' => slugify($name), 'description' => $_POST['description'] ?? '', 'category' => post('category'),
        'grade_level' => post('grade_level'), 'subject' => post('subject'), 'series' => post('series'),
        'price' => postFloat('price'), 'compare_price' => postFloat('compare_price') ?: null,
        'stock_quantity' => postInt('stock_quantity'), 'low_stock_threshold' => postInt('low_stock_threshold') ?: 5,
        'is_featured' => postInt('is_featured'), 'is_active' => 1, 'location_id' => 1];
    if (!empty($_FILES['cover_image']['name'])) { $r = uploadProductImage('cover_image'); if ($r['success']) $data['cover_image'] = $r['path']; }
    $id = dbInsert('products', $data);
    jsonResponse(true, 'Product added.', ['id' => $id]);
}

function updateProduct(): void {
    $user = requireAuth(); requirePermission($user, 'bookstore', 'edit'); validateCsrf();
    require_once __DIR__ . '/../includes/upload.php';
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Product ID required.');
    $data = [];
    if (post('name')) { $data['name'] = post('name'); $data['slug'] = slugify(post('name')); }
    if (isset($_POST['description'])) $data['description'] = $_POST['description'];
    if (post('category')) $data['category'] = post('category');
    if (post('grade_level')) $data['grade_level'] = post('grade_level');
    if (isset($_POST['subject'])) $data['subject'] = post('subject');
    if (isset($_POST['series'])) $data['series'] = post('series');
    if (isset($_POST['price'])) $data['price'] = postFloat('price');
    if (isset($_POST['stock_quantity'])) $data['stock_quantity'] = postInt('stock_quantity');
    if (isset($_POST['is_featured'])) $data['is_featured'] = postInt('is_featured');
    if (isset($_POST['is_active'])) $data['is_active'] = postInt('is_active');
    if (!empty($_FILES['cover_image']['name'])) { $r = uploadProductImage('cover_image'); if ($r['success']) $data['cover_image'] = $r['path']; }
    if (!empty($data)) dbUpdate('products', $data, 'id = ?', [$id]);
    jsonResponse(true, 'Product updated.');
}

function deleteProduct(): void {
    $user = requireAuth(); requirePermission($user, 'bookstore', 'delete'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Product ID required.');
    dbSoftDelete('products', $id);
    jsonResponse(true, 'Product removed.');
}

function getCart(): void { jsonResponse(true, '', ['cart' => $_SESSION['cart'] ?? []]); }

function addToCart(): void {
    $productId = postInt('product_id'); $qty = max(1, postInt('quantity', 1));
    $product = dbFetchOne("SELECT id, name, price, cover_image FROM products WHERE id = ? AND is_active = 1", [$productId]);
    if (!$product) jsonResponse(false, 'Product not found.');
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $found = false;
    foreach ($_SESSION['cart'] as &$item) { if ($item['product_id'] == $productId) { $item['quantity'] += $qty; $found = true; break; } }
    if (!$found) { $_SESSION['cart'][] = ['product_id' => $productId, 'name' => $product['name'], 'price' => (float)$product['price'], 'quantity' => $qty, 'image' => $product['cover_image']]; }
    jsonResponse(true, 'Added to cart.', ['cart_count' => count($_SESSION['cart'])]);
}

function updateCart(): void {
    $productId = postInt('product_id'); $qty = max(0, postInt('quantity'));
    if (!isset($_SESSION['cart'])) jsonResponse(true, 'Cart empty.');
    foreach ($_SESSION['cart'] as $i => &$item) {
        if ($item['product_id'] == $productId) { if ($qty <= 0) unset($_SESSION['cart'][$i]); else $item['quantity'] = $qty; break; }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart'] ?? []);
    jsonResponse(true, 'Cart updated.');
}

function removeFromCart(): void {
    $productId = postInt('product_id');
    $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'] ?? [], fn($i) => $i['product_id'] != $productId));
    jsonResponse(true, 'Item removed.');
}

function checkout(): void {
    $user = requireAuth(); requireUserType($user, 'parent'); validateCsrf();
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart)) jsonResponse(false, 'Cart is empty.');
    $address = post('delivery_address'); $phone = post('delivery_phone');
    if (!$address || !$phone) jsonResponse(false, 'Delivery address and phone required.');
    $subtotal = 0;
    foreach ($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }
    $deliveryFee = (float)getSetting('delivery_fee_default', '0');
    $total = $subtotal + $deliveryFee;
    $orderNumber = generateDocNumber('ORD', 'orders', 'order_number');
    $orderId = dbInsert('orders', [
        'order_number' => $orderNumber, 'parent_id' => $user['id'],
        'subtotal' => $subtotal, 'delivery_fee' => $deliveryFee, 'total' => $total,
        'status' => 'pending_payment', 'delivery_address' => $address, 'delivery_phone' => $phone,
        'delivery_notes' => post('delivery_notes'), 'location_id' => 1,
    ]);
    foreach ($cart as $item) {
        dbInsert('order_items', [
            'order_id' => $orderId, 'product_id' => $item['product_id'], 'product_name' => $item['name'],
            'quantity' => $item['quantity'], 'unit_price' => $item['price'], 'total' => $item['price'] * $item['quantity'],
        ]);
        // Decrease stock
        dbExecute("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?", [$item['quantity'], $item['product_id']]);
    }
    $_SESSION['cart'] = [];
    // Notify admin
    $admins = dbFetchAll("SELECT id FROM users WHERE user_type IN ('super_admin','admin') AND is_active = 1");
    foreach ($admins as $a) { createNotification($a['id'], "New order: $orderNumber", formatNaira($total)." from {$user['first_name']}", HUB_URL.'/bookstore.php'); }
    jsonResponse(true, 'Order placed!', ['order_number' => $orderNumber, 'total' => $total, 'bank_accounts' => getActiveBankAccounts()]);
}

function getOrders(): void {
    $user = requireAuth();
    if ($user['user_type'] === 'parent') {
        $orders = dbFetchAll("SELECT * FROM orders WHERE parent_id = ? ORDER BY created_at DESC", [$user['id']]);
    } else {
        requirePermission($user, 'bookstore', 'view');
        $status = $_GET['status'] ?? '';
        $where = "1=1"; $params = [];
        if ($status) { $where .= " AND o.status = ?"; $params[] = $status; }
        $orders = dbFetchAll("SELECT o.*, u.first_name, u.last_name FROM orders o JOIN users u ON o.parent_id = u.id WHERE $where ORDER BY o.created_at DESC", $params);
    }
    foreach ($orders as &$o) { $o['formatted_total'] = formatNaira($o['total']); $o['formatted_date'] = formatDate($o['created_at']); }
    jsonResponse(true, '', ['orders' => $orders]);
}

function getOrder(): void {
    $user = requireAuth();
    $id = (int)($_GET['id'] ?? 0); if (!$id) jsonResponse(false, 'Order ID required.');
    $order = dbFetchOne("SELECT o.*, u.first_name, u.last_name, u.email, u.phone FROM orders o JOIN users u ON o.parent_id = u.id WHERE o.id = ?", [$id]);
    if (!$order) jsonResponse(false, 'Order not found.');
    if ($user['user_type'] === 'parent' && $order['parent_id'] !== $user['id']) jsonResponse(false, 'Access denied.', [], 403);
    $items = dbFetchAll("SELECT * FROM order_items WHERE order_id = ?", [$id]);
    jsonResponse(true, '', ['order' => $order, 'items' => $items, 'bank_accounts' => getActiveBankAccounts()]);
}

function updateOrderStatus(): void {
    $user = requireAuth(); requirePermission($user, 'bookstore', 'edit'); validateCsrf();
    $id = postInt('id'); $status = post('status');
    if (!$id || !$status) jsonResponse(false, 'Order ID and status required.');
    $data = ['status' => $status, 'confirmed_by' => $user['id']];
    if ($status === 'shipped') $data['shipped_at'] = date('Y-m-d H:i:s');
    if ($status === 'delivered') $data['delivered_at'] = date('Y-m-d H:i:s');
    dbUpdate('orders', $data, 'id = ?', [$id]);
    $order = dbFetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
    if ($order) { createNotification($order['parent_id'], "Order {$order['order_number']} — ".ucfirst($status), "Your order status has been updated.", APP_URL.'/parent/payments.php'); }
    jsonResponse(true, 'Order status updated.');
}
