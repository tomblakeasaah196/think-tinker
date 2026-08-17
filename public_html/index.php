<?php
$pageTitle = 'Home';
$pageDescription = 'Think & Tinker Kids Club — Building curious minds through play, creativity, and exploration. Home tutorials, STEM club, educational consulting, and online bookstore in Lagos.';
require_once __DIR__ . '/includes/config.php';
include __DIR__ . '/templates/header.php';
?>

<section class="hero">
    <div class="hero-bg"><div class="hero-orb orb-1"></div><div class="hero-orb orb-2"></div><div class="hero-orb orb-3"></div></div>
    <div class="container hero-inner">
        <div class="hero-text">
            <div class="hero-badge slide-in" style="animation-delay:0.2s;">Ages 1 — 14 · Lagos, Nigeria</div>
            <h1 class="hero-title slide-in" style="animation-delay:0.4s;">Building <span class="text-gradient">Curious Minds</span><br>Through Play & Creativity</h1>
            <p class="hero-sub slide-in" style="animation-delay:0.6s;">Home tutorials, STEM & Reading Club, educational consulting, and a bookstore full of learning adventures.</p>
            <div class="hero-ctas slide-in" style="animation-delay:0.8s;">
                <a href="<?= APP_URL ?>/parent/register-club.php" class="btn btn-secondary btn-lg">Join STEM Club 🎪</a>
                <a href="<?= APP_URL ?>/services.php" class="btn btn-outline btn-lg" style="border-color:#FFF;color:#FFF;">Explore Services</a>
            </div>
            <div class="hero-trust slide-in" style="animation-delay:1s;"><span>✓ Experienced tutors</span><span>✓ Personalized learning</span><span>✓ Flexible scheduling</span></div>
        </div>
        <div class="hero-visual slide-in" style="animation-delay:0.6s;">
            <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80" alt="Children learning" class="hero-img">
            <div class="hero-float-card float-1"><span style="font-size:1.5rem;">📚</span><div><strong>500+</strong><br><small>Books & Resources</small></div></div>
            <div class="hero-float-card float-2"><span style="font-size:1.5rem;">🧪</span><div><strong>STEM</strong><br><small>Every Saturday</small></div></div>
        </div>
    </div>
</section>

<section class="section" id="services"><div class="container">
    <div class="section-header reveal"><div class="section-tag">What We Do</div><h2>Five Pillars of Learning</h2><p>A complete educational ecosystem designed to nurture every child's potential.</p></div>
    <div class="pillars-grid">
        <a href="<?= APP_URL ?>/services/tutorials.php" class="pillar-card reveal" style="--accent:#1AAFA0;"><div class="pillar-icon">🏠</div><h3>Home Tutorials</h3><p>1-on-1 personalized sessions at your home. All subjects, all levels.</p><span class="pillar-link">Learn more →</span></a>
        <a href="<?= APP_URL ?>/services/stem-club.php" class="pillar-card reveal" style="--accent:#F47B20;"><div class="pillar-icon">🧪</div><h3>STEM & Reading Club</h3><p>Saturday sessions: experiments, coding, robotics, and story adventures.</p><span class="pillar-link">Learn more →</span></a>
        <a href="<?= APP_URL ?>/services/consulting.php" class="pillar-card reveal" style="--accent:#1B2A4A;"><div class="pillar-icon">🎯</div><h3>Educational Consulting</h3><p>Expert guidance on curriculum, school selection, and child development.</p><span class="pillar-link">Learn more →</span></a>
        <a href="<?= APP_URL ?>/services/consult-booklets.php" class="pillar-card reveal" style="--accent:#27AE60;"><div class="pillar-icon">📖</div><h3>Consult Booklets</h3><p>Curated workbooks and activity sheets for self-paced study.</p><span class="pillar-link">Learn more →</span></a>
        <a href="<?= APP_URL ?>/shop.php" class="pillar-card reveal" style="--accent:#3498DB;"><div class="pillar-icon">🛒</div><h3>Online Bookstore</h3><p>Textbooks, STEM kits, flashcards — delivered across Lagos.</p><span class="pillar-link">Shop now →</span></a>
    </div>
</div></section>

<section class="section section-cream" id="why"><div class="container"><div class="why-grid">
    <div class="why-image reveal"><img src="https://images.unsplash.com/photo-1588072432836-e10032774350?w=600&q=80" alt="Kids learning" style="border-radius:var(--radius-lg);width:100%;box-shadow:var(--shadow-lg);"><div class="why-stat-float"><div class="stat-num">98%</div><div class="stat-txt">Parent Satisfaction</div></div></div>
    <div class="why-content reveal"><div class="section-tag">Why Think & Tinker</div><h2>Where Learning Meets Adventure</h2>
        <div class="why-features">
            <div class="why-feat"><span class="feat-icon">👩‍🏫</span><div><strong>Expert Tutors</strong><p>Vetted, trained, and matched to your child's style.</p></div></div>
            <div class="why-feat"><span class="feat-icon">📊</span><div><strong>Progress Tracking</strong><p>Real-time updates and detailed reports in your portal.</p></div></div>
            <div class="why-feat"><span class="feat-icon">🔬</span><div><strong>Hands-On STEM</strong><p>Real experiments, real projects, real excitement.</p></div></div>
            <div class="why-feat"><span class="feat-icon">📱</span><div><strong>Parent Portal</strong><p>Track sessions, chat, view invoices, and download reports.</p></div></div>
        </div>
    </div>
</div></div></section>

<section class="section section-navy stem-highlight"><div class="container"><div class="stem-inner reveal">
    <div class="stem-text"><div class="section-tag" style="color:var(--idea-gold);">Every Saturday</div><h2 style="color:#FFF;font-size:2rem;">STEM & Reading Club</h2>
        <p style="color:rgba(255,255,255,0.8);font-size:1.0625rem;line-height:1.7;margin:16px 0 24px;">A Saturday adventure for ages 4–14. Science, coding, robotics, creative writing, and story time — all in one magical session.</p>
        <div class="stem-plans"><div class="plan-chip">Trial · ₦<?= number_format((float)getSetting('club_plan_trial','8000'),0) ?></div><div class="plan-chip">Monthly · ₦<?= number_format((float)getSetting('club_plan_monthly','30000'),0) ?></div><div class="plan-chip">Quarterly · ₦<?= number_format((float)getSetting('club_plan_quarterly','85000'),0) ?></div></div>
        <a href="<?= APP_URL ?>/parent/register-club.php" class="btn btn-secondary btn-lg" style="margin-top:20px;">Register Your Child →</a>
    </div>
    <div class="stem-images"><img src="https://images.unsplash.com/photo-1596464716127-f2a82984de30?w=400&q=80" alt="STEM" class="stem-img img-1"><img src="https://images.unsplash.com/photo-1529390079861-591de354faf5?w=400&q=80" alt="Reading" class="stem-img img-2"></div>
</div></div></section>

<section class="section"><div class="container">
    <div class="section-header reveal"><div class="section-tag">What Parents Say</div><h2>Trusted by Lagos Families</h2></div>
    <div class="testimonial-grid">
        <div class="testimonial-card reveal"><div class="testi-stars">⭐⭐⭐⭐⭐</div><p class="testi-text">"My son went from dreading math to asking for extra practice. The tutors make learning feel like play."</p><div class="testi-author"><div class="avatar avatar-sm avatar-teal">AO</div><div><strong>Adaeze O.</strong><span>Lekki</span></div></div></div>
        <div class="testimonial-card reveal"><div class="testi-stars">⭐⭐⭐⭐⭐</div><p class="testi-text">"The Saturday STEM Club is the highlight of my daughter's week. She comes home excited about robots and volcanoes!"</p><div class="testi-author"><div class="avatar avatar-sm avatar-orange">FK</div><div><strong>Folake K.</strong><span>Ikeja</span></div></div></div>
        <div class="testimonial-card reveal"><div class="testi-stars">⭐⭐⭐⭐⭐</div><p class="testi-text">"The parent portal is a game-changer. I track everything — sessions, notes, payments. Very professional."</p><div class="testi-author"><div class="avatar avatar-sm avatar-navy">CB</div><div><strong>Chidi B.</strong><span>Victoria Island</span></div></div></div>
    </div>
</div></section>

<section class="section cta-section"><div class="container" style="text-align:center;"><div class="reveal">
    <h2 style="color:#FFF;font-size:2rem;margin-bottom:8px;">Ready to Start Your Child's Journey?</h2>
    <p style="color:rgba(255,255,255,0.8);font-size:1.0625rem;max-width:500px;margin:0 auto 24px;">Join hundreds of Lagos families who trust Think & Tinker.</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="<?= APP_URL ?>/parent/register.php" class="btn btn-lg" style="background:#FFF;color:var(--deep-navy);">Get Started Free</a>
        <a href="<?= whatsappLink("Hello! I'd like to learn more about Think & Tinker.") ?>" target="_blank" class="btn btn-lg" style="background:var(--leaf-green);color:#FFF;">💬 WhatsApp Us</a>
    </div>
</div></div></section>

<style>
.hero{background:linear-gradient(135deg,#1B2A4A 0%,#243758 50%,#1AAFA0 100%);color:#FFF;padding:80px 0 60px;position:relative;overflow:hidden;min-height:90vh;display:flex;align-items:center;}
.hero-bg{position:absolute;inset:0;overflow:hidden;}
.hero-orb{position:absolute;border-radius:50%;filter:blur(100px);opacity:.3;}
.hero-orb.orb-1{width:500px;height:500px;background:var(--tinker-teal);top:-200px;right:-100px;animation:floatO 15s ease-in-out infinite alternate;}
.hero-orb.orb-2{width:400px;height:400px;background:var(--spark-orange);bottom:-150px;left:-100px;animation:floatO 18s ease-in-out infinite alternate-reverse;}
.hero-orb.orb-3{width:300px;height:300px;background:#3498DB;top:50%;left:50%;animation:floatO 12s ease-in-out infinite alternate;}
@keyframes floatO{0%{transform:translate(0,0) scale(1);}100%{transform:translate(40px,60px) scale(1.15);}}
.hero-inner{display:grid;grid-template-columns:1.1fr .9fr;gap:48px;align-items:center;position:relative;z-index:1;}
.hero-badge{display:inline-block;padding:6px 16px;border-radius:var(--radius-full);background:rgba(255,255,255,.12);backdrop-filter:blur(8px);font-size:.8125rem;font-weight:700;margin-bottom:16px;border:1px solid rgba(255,255,255,.2);}
.hero-title{font-family:'Quicksand',sans-serif;font-size:3rem;font-weight:800;line-height:1.15;margin-bottom:16px;}
.text-gradient{background:linear-gradient(90deg,#1AAFA0,#F47B20,#FDB813);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.hero-sub{font-size:1.125rem;line-height:1.7;opacity:.85;max-width:520px;margin-bottom:24px;}
.hero-ctas{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:24px;}
.hero-trust{display:flex;gap:20px;font-size:.8125rem;opacity:.7;flex-wrap:wrap;}
.hero-visual{position:relative;}
.hero-img{width:100%;border-radius:var(--radius-lg);box-shadow:0 24px 64px rgba(0,0,0,.3);}
.hero-float-card{position:absolute;background:#FFF;color:var(--deep-navy);border-radius:var(--radius-md);padding:12px 16px;box-shadow:0 8px 24px rgba(0,0,0,.15);display:flex;align-items:center;gap:10px;font-size:.8125rem;animation:floatC 4s ease-in-out infinite alternate;}
.hero-float-card strong{font-size:1.125rem;}.hero-float-card small{font-size:.6875rem;color:#999;}
.float-1{top:20%;right:-20px;}.float-2{bottom:15%;left:-20px;animation-delay:2s;}
@keyframes floatC{0%{transform:translateY(0);}100%{transform:translateY(-10px);}}
.slide-in{opacity:0;transform:translateY(24px);animation:slideU .7s ease forwards;}
@keyframes slideU{to{opacity:1;transform:translateY(0);}}
.section-tag{display:inline-block;font-size:.6875rem;font-weight:800;text-transform:uppercase;letter-spacing:2px;color:var(--spark-orange);margin-bottom:8px;}
.pillars-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;}
.pillar-card{background:#FFF;border-radius:var(--radius-lg);padding:28px 20px;text-align:center;box-shadow:var(--shadow-sm);transition:transform .3s,box-shadow .3s;text-decoration:none;color:var(--charcoal);border-top:4px solid var(--accent);}
.pillar-card:hover{transform:translateY(-8px);box-shadow:var(--shadow-lg);color:var(--charcoal);}
.pillar-icon{font-size:2.5rem;margin-bottom:12px;}
.pillar-card h3{font-size:1rem;margin-bottom:8px;color:var(--deep-navy);}
.pillar-card p{font-size:.8125rem;color:#666;line-height:1.6;margin-bottom:12px;}
.pillar-link{font-size:.75rem;font-weight:700;color:var(--tinker-teal);}
.why-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;}
.why-image{position:relative;}
.why-stat-float{position:absolute;bottom:-20px;right:-10px;background:var(--tinker-teal);color:#FFF;border-radius:var(--radius-md);padding:16px 24px;text-align:center;box-shadow:0 8px 24px rgba(26,175,160,.4);}
.why-stat-float .stat-num{font-family:'Quicksand',sans-serif;font-size:2rem;font-weight:800;}.why-stat-float .stat-txt{font-size:.6875rem;opacity:.8;}
.why-features{margin-top:24px;}
.why-feat{display:flex;gap:14px;margin-bottom:20px;}
.feat-icon{font-size:1.75rem;flex-shrink:0;margin-top:2px;}.why-feat strong{font-size:.9375rem;color:var(--deep-navy);}.why-feat p{font-size:.8125rem;color:#666;margin-top:2px;}
.stem-inner{display:grid;grid-template-columns:1.2fr .8fr;gap:40px;align-items:center;}
.stem-plans{display:flex;gap:10px;flex-wrap:wrap;}
.plan-chip{padding:8px 16px;border-radius:var(--radius-full);background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);font-size:.8125rem;font-weight:700;color:#FFF;}
.stem-images{position:relative;}.stem-img{border-radius:var(--radius-lg);box-shadow:0 16px 40px rgba(0,0,0,.3);}
.stem-img.img-1{width:85%;}.stem-img.img-2{width:55%;position:absolute;bottom:-20px;right:-10px;border:4px solid var(--deep-navy);}
.testimonial-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
.testimonial-card{background:#FFF;border-radius:var(--radius-lg);padding:28px;box-shadow:var(--shadow-sm);transition:transform .3s;}
.testimonial-card:hover{transform:translateY(-4px);}
.testi-stars{font-size:.875rem;margin-bottom:12px;}.testi-text{font-size:.9375rem;color:#444;line-height:1.7;font-style:italic;margin-bottom:16px;}
.testi-author{display:flex;align-items:center;gap:10px;}.testi-author strong{font-size:.8125rem;display:block;}.testi-author span{font-size:.6875rem;color:#999;}
.cta-section{background:linear-gradient(135deg,var(--tinker-teal),#138D80,var(--deep-navy));padding:80px 0;}
.reveal{opacity:0;transform:translateY(32px);transition:opacity .7s ease,transform .7s ease;}.reveal.visible{opacity:1;transform:translateY(0);}
@media(max-width:1024px){.pillars-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:768px){.hero{padding:60px 0 40px;min-height:auto;}.hero-inner{grid-template-columns:1fr;gap:32px;text-align:center;}.hero-title{font-size:2rem;}.hero-sub{margin:0 auto 20px;}.hero-ctas,.hero-trust{justify-content:center;}.hero-float-card{display:none;}.why-grid,.stem-inner{grid-template-columns:1fr;}.testimonial-grid{grid-template-columns:1fr;}.why-stat-float{bottom:-16px;right:10px;}.stem-img.img-2{position:static;width:60%;margin-top:12px;}}
@media(max-width:640px){.pillars-grid{grid-template-columns:1fr 1fr;}}
</style>

<script>
document.addEventListener('DOMContentLoaded',function(){
    const reveals=document.querySelectorAll('.reveal');
    const observer=new IntersectionObserver(function(entries){entries.forEach(function(e,i){if(e.isIntersecting){setTimeout(function(){e.target.classList.add('visible');},i*100);observer.unobserve(e.target);}});},{threshold:.1,rootMargin:'0px 0px -50px 0px'});
    reveals.forEach(function(el){observer.observe(el);});
});
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
