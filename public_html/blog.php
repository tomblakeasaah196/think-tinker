<?php $pageTitle='Blog'; require_once __DIR__.'/includes/config.php'; include __DIR__.'/templates/header.php'; ?>
<section class="page-hero page-hero--navy" style="padding:56px 0;">
    <div class="container"><h1>Blog</h1><p>Tips, stories, and insights on parenting, education, and raising curious kids.</p></div>
</section>
<section class="section"><div class="container">
    <div id="blogGrid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;"><div class="spinner mx-auto" style="grid-column:1/-1;"></div></div>
    <div class="pagination mt-3" id="blogPagination"></div>
</div></section>
<style>@media(max-width:768px){#blogGrid{grid-template-columns:1fr !important;}}</style>
<script>
TT.config.apiBase='<?= APP_URL ?>/api';TT.config.csrfToken='<?= CSRF_TOKEN ?>';TT.config.siteUrl='<?= APP_URL ?>';
function loadBlog(page){
    TT.get('BlogController.php',{action:'get_posts',page:page||1}).done(function(r){
        if(!r.success)return;const posts=r.data.posts||[];
        if(!posts.length){$('#blogGrid').html('<div style="grid-column:1/-1;" class="empty-state"><div class="icon">✍️</div><h3>Coming Soon</h3><p>We\'re working on great content. Check back soon!</p></div>');return;}
        let html='';posts.forEach(p=>{
            const img=p.image_url||'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&q=80';
            html+=`<a href="<?= APP_URL ?>/blog-post.php?slug=${p.slug}" class="card card-clickable" style="text-decoration:none;color:var(--charcoal);">
                <img src="${img}" class="card-img" alt="" loading="lazy">
                <div class="card-body"><span class="badge badge-teal" style="margin-bottom:8px;">${TT.escHtml(p.category||'')}</span><h3 class="card-title" style="font-size:1rem;">${TT.escHtml(p.title)}</h3><p class="card-text">${TT.escHtml((p.excerpt||'').substring(0,120))}</p><span style="font-size:.75rem;color:#999;">${p.formatted_date} · ${TT.escHtml(p.author_name)}</span></div></a>`;
        });
        $('#blogGrid').html(html);
        if(r.data.pages>1)TT.buildPagination($('#blogPagination'),r.data.page,r.data.pages,loadBlog);
    });
}
$(function(){loadBlog(1);});
</script>
<?php include __DIR__.'/templates/footer.php'; ?>
