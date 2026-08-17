<?php $pageTitle='Case Studies'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="section" style="background:var(--deep-navy);color:#FFF;padding:60px 0;text-align:center;">
    <div class="container"><h1 style="color:#FFF;">Case Studies</h1><p style="opacity:.8;">How Think & Tinker has transformed learning across Lagos schools.</p></div>
</section>
<section class="section"><div class="container" style="max-width:860px;">
    <div class="card mb-3" style="overflow:hidden;"><div style="display:grid;grid-template-columns:1fr 1.5fr;">
        <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&q=80" alt="Workshop" style="width:100%;height:100%;object-fit:cover;">
        <div class="card-body"><span class="badge badge-teal mb-1">STEM Workshop</span><h3>Bright Stars Academy — 3-Day STEM Bootcamp</h3><p class="text-sm text-muted mt-1 mb-2">Ikeja · 120 students · Ages 8–12</p><p style="color:#666;font-size:.9375rem;line-height:1.7;">We ran a 3-day intensive STEM bootcamp covering robotics, basic circuitry, and environmental science. Students built working LED circuits, programmed simple robots, and presented their findings to parents on the final day. Teacher feedback rated the program 4.9/5.</p></div>
    </div></div>
    <div class="card mb-3" style="overflow:hidden;"><div style="display:grid;grid-template-columns:1fr 1.5fr;">
        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400&q=80" alt="After-school" style="width:100%;height:100%;object-fit:cover;">
        <div class="card-body"><span class="badge badge-orange mb-1">After-School Program</span><h3>Grace International School — Weekly STEM Club</h3><p class="text-sm text-muted mt-1 mb-2">Lekki · 45 students · Ongoing since 2025</p><p style="color:#666;font-size:.9375rem;line-height:1.7;">A weekly after-school STEM enrichment program running every Wednesday. Activities rotate between science experiments, coding with Scratch, creative building challenges, and reading circles. The school reported a 30% increase in student interest in STEM subjects.</p></div>
    </div></div>
    <div class="card mb-3" style="overflow:hidden;"><div style="display:grid;grid-template-columns:1fr 1.5fr;">
        <img src="https://images.unsplash.com/photo-1544717305-2782549b5136?w=400&q=80" alt="Training" style="width:100%;height:100%;object-fit:cover;">
        <div class="card-body"><span class="badge badge-navy mb-1">Teacher Training</span><h3>Lagos State Education Board — Teacher PD Workshop</h3><p class="text-sm text-muted mt-1 mb-2">Maryland · 60 teachers · 2 days</p><p style="color:#666;font-size:.9375rem;line-height:1.7;">A 2-day professional development workshop for primary school teachers on integrating STEM into the existing curriculum. Sessions covered practical classroom experiments, low-cost teaching materials, and student engagement strategies.</p></div>
    </div></div>
    <div style="text-align:center;margin-top:32px;"><a href="<?= APP_URL ?>/school/inquiry.php" class="btn btn-primary btn-lg">Start Your School's Journey →</a></div>
</div></section>
<style>@media(max-width:768px){.card>div[style*="grid-template-columns"]{grid-template-columns:1fr !important;}}</style>
<?php include __DIR__.'/../templates/footer.php'; ?>
