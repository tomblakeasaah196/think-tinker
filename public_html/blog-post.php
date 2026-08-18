<?php require_once __DIR__.'/includes/config.php'; $slug=$_GET['slug']??''; if(!$slug){header('Location:'.APP_URL.'/blog.php');exit;}
$post=dbFetchOne("SELECT bp.*,u.first_name as author_name,u.profile_photo as author_photo FROM blog_posts bp JOIN users u ON bp.author_id=u.id WHERE bp.slug=? AND bp.status='published'",[$slug]);
if(!$post){$pageTitle='Not Found';include __DIR__.'/templates/header.php';echo '<section class="section"><div class="container"><div class="empty-state"><h3>Post Not Found</h3><a href="'.APP_URL.'/blog.php" class="btn btn-primary mt-2">Back to Blog</a></div></div></section>';include __DIR__.'/templates/footer.php';exit;}
$pageTitle=$post['title'];$ogDescription=$post['excerpt'];
include __DIR__.'/templates/header.php';
$imgUrl=$post['featured_image']?APP_URL.'/'.$post['featured_image']:'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1200&q=80';
?>
<section class="section" style="padding-top:40px;"><div class="container" style="max-width:760px;">
    <a href="<?= APP_URL ?>/blog.php" style="font-size:.8125rem;color:var(--tinker-teal);font-weight:700;">← Back to Blog</a>
    <h1 style="margin:16px 0 8px;font-size:2rem;"><?= htmlspecialchars($post['title']) ?></h1>
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px;font-size:.8125rem;color:var(--text-faint);">
        <div class="avatar avatar-sm avatar-teal"><?= strtoupper(substr($post['author_name'],0,2)) ?></div>
        <span><?= htmlspecialchars($post['author_name']) ?></span>
        <span>·</span><span><?= date('M j, Y',strtotime($post['published_at'])) ?></span>
        <?php if($post['category']): ?><span>·</span><span class="badge badge-teal"><?= htmlspecialchars($post['category']) ?></span><?php endif; ?>
    </div>
    <img src="<?= $imgUrl ?>" alt="" style="width:100%;border-radius:var(--radius-lg);margin-bottom:32px;box-shadow:var(--shadow-md);">
    <article style="font-size:1.0625rem;line-height:1.8;color:#444;"><?= $post['body'] ?></article>
    <?php $tags=$post['tags']?json_decode($post['tags'],true):[]; if($tags): ?>
    <div style="margin-top:24px;display:flex;gap:8px;flex-wrap:wrap;"><?php foreach($tags as $t): ?><span class="badge badge-gray"><?= htmlspecialchars(trim($t)) ?></span><?php endforeach; ?></div>
    <?php endif; ?>
    <div style="margin-top:40px;padding-top:24px;border-top:1px solid var(--cloud-gray);text-align:center;">
        <p style="color:var(--text-faint);font-size:.875rem;margin-bottom:12px;">Enjoyed this article? Share it with other parents!</p>
        <a href="<?= whatsappLink("Check out this article from Think & Tinker: ".$post['title']." — ".APP_URL."/blog-post.php?slug=".$post['slug']) ?>" target="_blank" class="btn btn-success btn-sm">💬 Share on WhatsApp</a>
    </div>
</div></section>
<?php include __DIR__.'/templates/footer.php'; ?>
