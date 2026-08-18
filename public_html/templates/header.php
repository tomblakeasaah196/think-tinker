<?php
/**
 * templates/header.php — Public website header + navigation
 * Include at top of every public page.
 * Sets $pageTitle and $pageDescription before including.
 */
$pageTitle = ($pageTitle ?? 'Think & Tinker Kids Club') . ' — Think. Tinker. Shine.';
$pageDescription = $pageDescription ?? 'Building curious minds through play, creativity, and exploration. Home tutorials, STEM club, educational consulting, and bookstore in Lagos, Nigeria.';
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
    (function(){
        try {
            var saved = localStorage.getItem('tt-theme');
            var theme = saved || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
        } catch (e) {}
    })();
    </script>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php include __DIR__ . '/og-meta.php'; ?>
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#1AAFA0">
    <link rel="icon" href="<?= APP_URL ?>/favicon.ico" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_URL ?>/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= APP_URL ?>/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= APP_URL ?>/apple-touch-icon.png">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- main.js defines window.TT and must run BEFORE the per-page inline
         blocks that assign TT.config.*. It used to load from footer.php,
         after those blocks, so every page threw "TT is not defined" and the
         loaders never registered (shop/blog spun forever). All of its DOM
         work is inside $(function(){…}), so loading it here is safe. -->
    <script src="<?= APP_URL ?>/assets/js/main.js"></script>
</head>
<body>

<nav class="site-nav no-print" id="siteNav">
    <div class="container">
        <div class="nav-inner">
            <a href="<?= APP_URL ?>/" class="nav-logo" aria-label="Think &amp; Tinker — home">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think &amp; Tinker" class="logo-img logo-light">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full-dark.png" alt="" aria-hidden="true" class="logo-img logo-dark">
            </a>

            <div class="nav-links" id="navLinks">
                <a href="<?= APP_URL ?>/" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
                <a href="<?= APP_URL ?>/about.php" class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
                <div class="nav-dropdown">
                    <a href="<?= APP_URL ?>/services.php" class="nav-link <?= in_array($currentPage, ['services','tutorials','stem-club','consulting','consult-booklets']) ? 'active' : '' ?>">
                        Services ▾
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="<?= APP_URL ?>/services/tutorials.php">Home Tutorials</a>
                        <a href="<?= APP_URL ?>/services/stem-club.php">STEM & Reading Club</a>
                        <a href="<?= APP_URL ?>/services/consulting.php">Educational Consulting</a>
                        <a href="<?= APP_URL ?>/services/consult-booklets.php">Consult Booklets</a>
                    </div>
                </div>
                <a href="<?= APP_URL ?>/shop.php" class="nav-link <?= in_array($currentPage, ['shop','shop-product','cart','checkout']) ? 'active' : '' ?>">Bookstore</a>
                <a href="<?= APP_URL ?>/blog.php" class="nav-link <?= in_array($currentPage, ['blog','blog-post']) ? 'active' : '' ?>">Blog</a>
                <a href="<?= APP_URL ?>/contact.php" class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a>

                <?php /* Mobile only: .nav-actions .btn is hidden under 768px, so the
                         auth buttons are re-rendered inside the slide-out panel.
                         The .mobile-auth styles already existed; the markup did not,
                         which left mobile visitors with no way to log in or join. */ ?>
                <div class="mobile-auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= $_SESSION['user_type'] === 'parent' ? APP_URL . '/parent/' : HUB_URL . '/dashboard.php' ?>" class="btn btn-primary btn-block">My Portal</a>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/parent/login.php" class="btn btn-outline btn-block">Log In</a>
                        <a href="<?= APP_URL ?>/parent/register-club.php" class="btn btn-primary btn-block">Join Club</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="nav-actions">
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" type="button">
                    <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $_SESSION['user_type'] === 'parent' ? APP_URL . '/parent/' : HUB_URL . '/dashboard.php' ?>" class="btn btn-primary btn-sm">My Portal</a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/parent/login.php" class="btn btn-outline btn-sm">Log In</a>
                    <a href="<?= APP_URL ?>/parent/register-club.php" class="btn btn-primary btn-sm">Join Club</a>
                <?php endif; ?>
                <button class="nav-toggle" id="navToggle" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>
</nav>

<style>
.site-nav { background: #FFF; height: var(--nav-height-desktop); position: sticky; top: 0; z-index: 200; box-shadow: var(--shadow-sm); }
.nav-inner { display: flex; align-items: center; justify-content: space-between; height: var(--nav-height-desktop); }
.nav-logo { display: flex; align-items: center; flex: 0 0 auto; }
.logo-img { height: 42px; width: auto; display: block; }
.logo-dark { display: none; }
[data-theme="dark"] .logo-light { display: none; }
[data-theme="dark"] .logo-dark  { display: block; }
@media (max-width: 900px) { .logo-img { height: 34px; } }
.logo-text { font-family: 'Quicksand', sans-serif; font-weight: 800; font-size: 1.25rem; color: var(--tinker-teal); }
.nav-links { display: flex; align-items: center; gap: 4px; }
.nav-link { padding: 8px 14px; font-size: 0.875rem; font-weight: 700; color: var(--text-body); border-radius: var(--radius-md); transition: all 0.2s; }
.nav-link:hover { color: var(--tinker-teal); background: rgba(26,175,160,0.06); }
.nav-link.active { color: var(--tinker-teal); }
.nav-actions { display: flex; align-items: center; gap: 8px; }
.nav-toggle { display: none; width: 36px; height: 36px; flex-direction: column; align-items: center; justify-content: center; gap: 5px; cursor: pointer; }
.nav-toggle span { display: block; width: 22px; height: 2px; background: var(--charcoal); border-radius: 2px; transition: all 0.3s; }
.nav-dropdown { position: relative; }
.nav-dropdown-menu { position: absolute; top: 100%; left: 0; background: #FFF; border-radius: var(--radius-md); box-shadow: 0 8px 24px rgba(0,0,0,0.12); min-width: 220px; opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s; z-index: 300; overflow: hidden; }
.nav-dropdown:hover .nav-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
.nav-dropdown-menu a { display: block; padding: 10px 16px; font-size: 0.875rem; color: var(--text-body); transition: background 0.15s; }
.nav-dropdown-menu a:hover { background: var(--cloud-gray); color: var(--tinker-teal); }

@media (max-width: 768px) {
    .site-nav { height: var(--nav-height-mobile); }
    .nav-inner { height: var(--nav-height-mobile); }
    .nav-toggle { display: flex; }
    .nav-links { position: fixed; top: var(--nav-height-mobile); left: 0; right: 0; bottom: 0; background: #FFF; flex-direction: column; align-items: stretch; padding: var(--space-3); gap: 0; transform: translateX(100%); transition: transform 0.3s ease; z-index: 199; overflow-y: auto; }
    .nav-links.open { transform: translateX(0); }
    .nav-link { padding: 14px 16px; font-size: 1rem; border-bottom: 1px solid var(--cloud-gray); border-radius: 0; }
    .nav-dropdown-menu { position: static; box-shadow: none; opacity: 1; visibility: visible; transform: none; padding-left: 16px; }
    .nav-actions .btn { display: none; }
    .nav-links .mobile-auth { display: flex; flex-direction: column; gap: 8px; padding-top: 16px; margin-top: 16px; border-top: 2px solid var(--cloud-gray); }
}
@media (min-width: 769px) {
    .mobile-auth { display: none !important; }
}
</style>

<script>
document.getElementById('navToggle')?.addEventListener('click', function() {
    document.getElementById('navLinks').classList.toggle('open');
    this.classList.toggle('active');
});
document.getElementById('themeToggle')?.addEventListener('click', function() {
    var root = document.documentElement;
    var isDark = root.getAttribute('data-theme') === 'dark';
    if (isDark) {
        root.removeAttribute('data-theme');
        try { localStorage.setItem('tt-theme', 'light'); } catch (e) {}
    } else {
        root.setAttribute('data-theme', 'dark');
        try { localStorage.setItem('tt-theme', 'dark'); } catch (e) {}
    }
});
</script>

<main>
