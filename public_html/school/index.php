<?php $pageTitle='For Schools'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="section" style="background:linear-gradient(135deg,#1B2A4A,#243758);color:#FFF;padding:80px 0;">
    <div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;">
        <div class="reveal">
            <div class="section-tag" style="color:var(--idea-gold);">For Schools</div>
            <h1 style="color:#FFF;font-size:2.25rem;">Partner With Think & Tinker</h1>
            <p style="opacity:.85;font-size:1.0625rem;line-height:1.7;margin:16px 0 24px;">Bring STEM workshops, after-school programs, and educational resources to your students. We work with schools across Lagos to design programs that inspire curiosity and build confidence.</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <a href="<?= APP_URL ?>/school/inquiry.php" class="btn btn-secondary btn-lg">Get a Proposal</a>
                <a href="<?= APP_URL ?>/school/case-studies.php" class="btn btn-outline btn-lg" style="border-color:#FFF;color:#FFF;">See Our Work</a>
            </div>
        </div>
        <div class="reveal"><img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=600&q=80" alt="School partnership" style="border-radius:var(--radius-lg);width:100%;box-shadow:0 24px 64px rgba(0,0,0,.3);"></div>
    </div>
</section>
<section class="section"><div class="container" style="text-align:center;">
    <div class="section-header reveal"><div class="section-tag">What We Offer Schools</div><h2>Tailored Programs</h2></div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
        <div class="pillar-card reveal" style="--accent:var(--tinker-teal);"><div class="pillar-icon">🔬</div><h3>STEM Workshops</h3><p>Half-day or full-day workshops for students — science experiments, coding, robotics, and engineering challenges.</p></div>
        <div class="pillar-card reveal" style="--accent:var(--spark-orange);"><div class="pillar-icon">📚</div><h3>After-School Programs</h3><p>Structured weekly programs that complement your curriculum with enrichment activities and homework support.</p></div>
        <div class="pillar-card reveal" style="--accent:var(--deep-navy);"><div class="pillar-icon">🎓</div><h3>Teacher Training</h3><p>Professional development sessions for your teaching staff on modern pedagogy, STEM integration, and classroom management.</p></div>
    </div>
</div></section>
<section class="section cta-section"><div class="container" style="text-align:center;"><div class="reveal"><h2 style="color:#FFF;">Let's Build Something Together</h2><p style="color:rgba(255,255,255,.8);max-width:480px;margin:8px auto 20px;">Every school partnership is custom-designed. Tell us about your needs and we'll create a proposal within 48 hours.</p><a href="<?= APP_URL ?>/school/inquiry.php" class="btn btn-lg" style="background:#FFF;color:var(--deep-navy);">Request a Proposal</a></div></div></section>
<style>.reveal{opacity:0;transform:translateY(32px);transition:opacity .7s,transform .7s;}.reveal.visible{opacity:1;transform:translateY(0);}.section-tag{display:inline-block;font-size:.6875rem;font-weight:800;text-transform:uppercase;letter-spacing:2px;color:var(--spark-orange);margin-bottom:8px;}.pillar-card{background:#FFF;border-radius:var(--radius-lg);padding:28px;text-align:center;box-shadow:var(--shadow-sm);border-top:4px solid var(--accent);transition:transform .3s;}.pillar-card:hover{transform:translateY(-6px);}.pillar-icon{font-size:2.5rem;margin-bottom:12px;}.pillar-card h3{color:var(--deep-navy);margin-bottom:8px;font-size:1rem;}.pillar-card p{font-size:.8125rem;color:#666;line-height:1.6;}.cta-section{background:linear-gradient(135deg,var(--tinker-teal),var(--deep-navy));padding:80px 0;}@media(max-width:768px){.section>.container[style*="grid-template-columns"]{grid-template-columns:1fr !important;}div[style*="grid-template-columns:repeat(3"]{grid-template-columns:1fr !important;}}</style>
<script>document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.reveal').forEach(el=>{new IntersectionObserver(e=>{e.forEach(en=>{if(en.isIntersecting)en.target.classList.add('visible');});},{threshold:.1}).observe(el);});});</script>
<?php include __DIR__.'/../templates/footer.php'; ?>
