<?php $pageTitle='Home Tutorials'; require_once __DIR__.'/../includes/config.php'; include __DIR__.'/../templates/header.php'; ?>
<section class="section" style="background:#1AAFA0;color:#FFF;padding:60px 0;text-align:center;">
    <div class="container"><div style="font-size:3rem;margin-bottom:12px;">🏠</div><h1 style="color:#FFF;">Home Tutorials</h1></div>
</section>
<section class="section"><div class="container" style="max-width:860px;">
    <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=860&q=80" alt="Home Tutorials" style="width:100%;border-radius:var(--radius-lg);margin-bottom:32px;box-shadow:var(--shadow-md);">
    <div style="font-size:1rem;line-height:1.8;color:#444;">Personalized 1-on-1 home tutoring for children aged 1–14. Our experienced tutors travel to your home across Lagos, covering all subjects from nursery to secondary school — English, Mathematics, Science, Social Studies, Yoruba, French, and more.<br><br>Sessions are tailored to your child's pace, learning style, and curriculum. Whether your child needs help catching up, staying ahead, or preparing for exams, we design a learning plan that works.<br><br><strong>What's included:</strong> personalized lesson plans, progress tracking via Parent Portal, session notes after every visit, flexible scheduling (mornings, afternoons, weekends), and regular progress reports.</div>
    <div style="text-align:center;margin-top:32px;">
        <a href="<?= APP_URL ?>/parent/register.php" class="btn btn-primary btn-lg">Register Your Child →</a>
        <a href="<?= whatsappLink("Hi! I'm interested in Home Tutorials at Think & Tinker.") ?>" target="_blank" class="btn btn-outline btn-lg" style="margin-left:8px;">💬 Ask Us</a>
    </div>
</div></section>
<?php include __DIR__.'/../templates/footer.php'; ?>
