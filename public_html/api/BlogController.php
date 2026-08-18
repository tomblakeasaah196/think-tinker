<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'get_posts':       getPosts(); break;
    case 'get_post':        getPost(); break;
    case 'create_post':     createPost(); break;
    case 'update_post':     updatePost(); break;
    case 'delete_post':     deletePost(); break;
    case 'publish_post':    publishPost(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function getPosts(): void {
    $category = $_GET['category'] ?? ''; $status = $_GET['status'] ?? '';
    $page = max(1, (int)($_GET['page'] ?? 1)); $perPage = 10;
    $where = "1=1"; $params = [];
    // Public view shows only published
    if (!isset($_GET['admin'])) { $where .= " AND bp.status = 'published'"; }
    elseif ($status) { $where .= " AND bp.status = ?"; $params[] = $status; }
    if ($category) { $where .= " AND bp.category = ?"; $params[] = $category; }
    $total = dbFetchColumn("SELECT COUNT(*) FROM blog_posts bp WHERE $where", $params);
    $posts = dbFetchAll(
        "SELECT bp.*, u.first_name as author_name FROM blog_posts bp JOIN users u ON bp.author_id = u.id
         WHERE $where ORDER BY bp.published_at DESC, bp.created_at DESC LIMIT ? OFFSET ?",
        array_merge($params, [$perPage, ($page-1)*$perPage]));
    foreach ($posts as &$p) { $p['image_url'] = $p['featured_image'] ? getUploadUrl($p['featured_image']) : null;
        $p['formatted_date'] = $p['published_at'] ? formatDate($p['published_at']) : 'Draft'; }
    jsonResponse(true, '', ['posts' => $posts, 'total' => (int)$total, 'page' => $page, 'pages' => ceil($total/$perPage)]);
}

function getPost(): void {
    $slug = $_GET['slug'] ?? ''; $id = (int)($_GET['id'] ?? 0);
    if ($slug) $post = dbFetchOne("SELECT bp.*, u.first_name as author_name, u.profile_photo as author_photo FROM blog_posts bp JOIN users u ON bp.author_id = u.id WHERE bp.slug = ?", [$slug]);
    elseif ($id) $post = dbFetchOne("SELECT bp.*, u.first_name as author_name FROM blog_posts bp JOIN users u ON bp.author_id = u.id WHERE bp.id = ?", [$id]);
    else jsonResponse(false, 'Post slug or ID required.');
    if (!$post) jsonResponse(false, 'Post not found.');
    $post['image_url'] = $post['featured_image'] ? getUploadUrl($post['featured_image']) : null;
    $post['tags_array'] = $post['tags'] ? json_decode($post['tags'], true) : [];
    jsonResponse(true, '', ['post' => $post]);
}

function createPost(): void {
    $user = requireAuth(); requirePermission($user, 'blog', 'create'); validateCsrf();
    require_once __DIR__ . '/../includes/upload.php';
    $title = post('title'); if (!$title) jsonResponse(false, 'Title required.');
    $slug = slugify($title);
    $existing = dbFetchOne("SELECT id FROM blog_posts WHERE slug = ?", [$slug]);
    if ($existing) $slug .= '-' . time();
    $data = ['title' => $title, 'slug' => $slug, 'body' => $_POST['body'] ?? '', 'category' => post('category') ?: 'announcements',
        'excerpt' => post('excerpt') ?: mb_substr(strip_tags($_POST['body'] ?? ''), 0, 160), 'tags' => post('tags') ? json_encode(explode(',', post('tags'))) : null,
        'author_id' => $user['id'], 'status' => post('status') ?: 'draft'];
    if ($data['status'] === 'published') $data['published_at'] = date('Y-m-d H:i:s');
    if (!empty($_FILES['featured_image']['name'])) { $r = uploadBlogImage('featured_image'); if ($r['success']) $data['featured_image'] = $r['path']; }
    $id = dbInsert('blog_posts', $data);
    jsonResponse(true, 'Post created.', ['id' => $id, 'slug' => $slug]);
}

function updatePost(): void {
    $user = requireAuth(); requirePermission($user, 'blog', 'edit'); validateCsrf();
    require_once __DIR__ . '/../includes/upload.php';
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Post ID required.');
    $data = [];
    if (post('title')) { $data['title'] = post('title'); $data['slug'] = slugify(post('title')); }
    if (isset($_POST['body'])) $data['body'] = $_POST['body'];
    if (post('category')) $data['category'] = post('category');
    if (post('excerpt')) $data['excerpt'] = post('excerpt');
    if (post('tags')) $data['tags'] = json_encode(explode(',', post('tags')));
    if (post('status')) { $data['status'] = post('status'); if (post('status') === 'published') $data['published_at'] = date('Y-m-d H:i:s'); }
    if (!empty($_FILES['featured_image']['name'])) { $r = uploadBlogImage('featured_image'); if ($r['success']) $data['featured_image'] = $r['path']; }
    if (!empty($data)) dbUpdate('blog_posts', $data, 'id = ?', [$id]);
    jsonResponse(true, 'Post updated.');
}

function deletePost(): void {
    $user = requireAuth(); requirePermission($user, 'blog', 'delete'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Post ID required.');
    dbExecute("DELETE FROM blog_posts WHERE id = ?", [$id]);
    jsonResponse(true, 'Post deleted.');
}

function publishPost(): void {
    $user = requireAuth(); requirePermission($user, 'blog', 'edit'); validateCsrf();
    $id = postInt('id'); if (!$id) jsonResponse(false, 'Post ID required.');
    dbUpdate('blog_posts', ['status' => 'published', 'published_at' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
    jsonResponse(true, 'Post published.');
}
