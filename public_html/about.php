<?php $pageTitle='About Us'; require_once __DIR__.'/includes/config.php'; include __DIR__.'/templates/header.php'; ?>
<section class="section" style="background:linear-gradient(135deg,#1B2A4A,#243758);color:#FFF;padding:80px 0 60px;text-align:center;">
    <div class="container reveal"><div class="section-tag" style="color:var(--idea-gold);">Our Story</div><h1 style="color:#FFF;font-size:2.25rem;">Think. Tinker. Shine.</h1><p style="opacity:.8;max-width:600px;margin:12px auto 0;font-size:1.0625rem;">We believe every child is born curious. Our job is to keep that fire burning.</p></div>
</section>
<section class="section"><div class="container"><div class="why-grid">
    <div class="reveal"><img src="https://images.unsplash.com/photo-1577896851231-70ef18881754?w=600&q=80" alt="Our team" style="border-radius:var(--radius-lg);width:100%;box-shadow:var(--shadow-md);"></div>
    <div class="reveal">
        <div class="section-tag">Who We Are</div><h2>Think & Tinker Kids Club</h2>
        <p style="color:#666;line-height:1.8;margin:16px 0;">Think & Tinker Kids LTD (RC <?= htmlspecialchars(getSetting('business_rc','9030989')) ?>) is a Lagos-based educational services company founded by Janeth Joseph with a mission to make learning joyful, practical, and accessible for every child aged 1–14.</p>
        <p style="color:#666;line-height:1.8;">We bring education to life through five integrated pillars: personalized home tutorials, a vibrant Saturday STEM & Reading Club, expert educational consulting, carefully crafted consult booklets, and a curated online bookstore. Every service is designed to nurture curiosity, build confidence, and develop real-world skills.</p>
    </div>
</div></div></section>
<section class="section section-cream"><div class="container" style="text-align:center;">
    <div class="section-header reveal"><div class="section-tag">Our Values</div><h2>What Drives Us</h2></div>
    <div class="pillars-grid" style="grid-template-columns:repeat(4,1fr);">
        <div class="pillar-card reveal" style="--accent:#1AAFA0;"><div class="pillar-icon">💡</div><h3>Curiosity</h3><p>We ask "what if?" before "why not?" — and teach children to do the same.</p></div>
        <div class="pillar-card reveal" style="--accent:#F47B20;"><div class="pillar-icon">🤝</div><h3>Inclusion</h3><p>Every child deserves access to quality education, regardless of background.</p></div>
        <div class="pillar-card reveal" style="--accent:#1B2A4A;"><div class="pillar-icon">🌱</div><h3>Growth</h3><p>We celebrate progress over perfection — small steps build big futures.</p></div>
        <div class="pillar-card reveal" style="--accent:#27AE60;"><div class="pillar-icon">🎯</div><h3>Excellence</h3><p>Professional, accountable, and always improving our craft.</p></div>
    </div>
</div></section>
<section class="section cta-section"><div class="container" style="text-align:center;"><div class="reveal"><h2 style="color:#FFF;">Join the Think & Tinker Family</h2><p style="color:rgba(255,255,255,.8);margin:8px auto 20px;max-width:400px;">Whether you're a parent, teacher, or school — we'd love to work with you.</p><a href="<?= APP_URL ?>/contact.php" class="btn btn-lg" style="background:#FFF;color:var(--deep-navy);">Get in Touch</a></div></div></section>
<style>.reveal{opacity:0;transform:translateY(32px);transition:opacity .7s,transform .7s;}.reveal.visible{opacity:1;transform:translateY(0);}.cta-section{background:linear-gradient(135deg,var(--tinker-teal),var(--deep-navy));padding:80px 0;}.section-tag{display:inline-block;font-size:.6875rem;font-weight:800;text-transform:uppercase;letter-spacing:2px;color:var(--spark-orange);margin-bottom:8px;}.why-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;}.pillars-grid{display:grid;gap:16px;}.pillar-card{background:#FFF;border-radius:var(--radius-lg);padding:28px 20px;text-align:center;box-shadow:var(--shadow-sm);border-top:4px solid var(--accent);transition:transform .3s;}.pillar-card:hover{transform:translateY(-6px);}.pillar-icon{font-size:2.5rem;margin-bottom:12px;}.pillar-card h3{font-size:1rem;margin-bottom:8px;color:var(--deep-navy);}.pillar-card p{font-size:.8125rem;color:#666;line-height:1.6;}@media(max-width:768px){.why-grid{grid-template-columns:1fr;}.pillars-grid{grid-template-columns:1fr 1fr !important;}}</style>
<script>document.addEventListener('DOMContentLoaded',function(){const r=document.querySelectorAll('.reveal');const o=new IntersectionObserver(function(e){e.forEach(function(en,i){if(en.isIntersecting){setTimeout(()=>en.target.classList.add('visible'),i*80);o.unobserve(en.target);}});},{threshold:.1});r.forEach(el=>o.observe(el));});</script>
<?php include __DIR__.'/templates/footer.php'; ?>
