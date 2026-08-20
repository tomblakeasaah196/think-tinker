<?php
/**
 * templates/header-hub.php — Operations Hub sidebar + topbar
 * Include at top of every hub/ page.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../includes/icons.php';

$user = requireAuth();

// Prevent parents from accessing the Hub
if ($user['user_type'] === 'parent') {
    redirect(APP_URL . '/parent/');
}

$modules = getUserModules($user);
$pageTitle = ($pageTitle ?? 'Hub') . ' — Think & Tinker Hub';
$currentModule = $currentModule ?? '';
$userName = $user['first_name'] . ' ' . $user['last_name'];

// Module definitions: slug => [icon name (see includes/icons.php), label]
// Kept in sync with the icon choices in hub/help.php's $helpContent array —
// same module, same icon, in both places.
$moduleList = [
    'dashboard'  => ['home', 'Dashboard'],
    'clients'    => ['users', 'Clients'],
    'sessions'   => ['calendar', 'Sessions'],
    'finance'    => ['card', 'Finance'],
    'staff'      => ['id-badge', 'Staff'],
    'bookstore'  => ['book', 'Bookstore'],
    'club'       => ['tent', 'STEM Club'],
    'blog'       => ['book', 'Blog'],
    'messages'   => ['message', 'Messages'],
    'documents'  => ['clipboard', 'Documents'],
    'settings'   => ['sliders', 'Settings'],
    'help'       => ['smile', 'Help Centre'],
    // Not a sidebar nav item — getUserModules() never returns 'profile', so
    // the sidebar loop's permission check below silently skips it. Listed
    // here only so the topbar title resolves to "My Profile" instead of
    // falling back to the generic "Hub" label on that page.
    'profile'    => ['id-badge', 'My Profile'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="theme-color" content="#1B2A4A">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/img/logo/favicon.png">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= HUB_URL ?>/assets/css/hub.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="hub-layout">

<!-- Sidebar -->
<aside class="hub-sidebar no-print" id="hubSidebar">
    <div class="sidebar-header">
        <a href="<?= HUB_URL ?>/dashboard.php" class="sidebar-logo">
            <span class="logo-icon"><?= ttIcon('zap', 'tt-icon', 20) ?></span>
            <span class="logo-text">Think & Tinker</span>
        </a>
        <span class="sidebar-badge">HUB</span>
    </div>

    <div style="padding: 0 var(--space-2) var(--space-2);">
        <a href="<?= APP_URL ?>/" class="btn-back-home"><?= ttIcon('arrow-left', 'tt-icon', 14) ?> Back to Home</a>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($moduleList as $slug => [$icon, $label]):
            if ($slug !== 'help' && !in_array($slug, $modules)) continue;
            $isActive = ($currentModule === $slug) ? 'active' : '';
        ?>
        <a href="<?= HUB_URL ?>/<?= $slug === 'dashboard' ? 'dashboard' : $slug ?>.php" class="sidebar-link <?= $isActive ?>">
            <span class="sidebar-icon"><?= ttIcon($icon, 'tt-icon', 18) ?></span>
            <span class="sidebar-label"><?= $label ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= HUB_URL ?>/profile.php" class="sidebar-user">
            <?php if (empty($user['phone']) || empty($user['profile_photo'])): ?>
                <span class="sidebar-user-incomplete-dot" title="Your profile is missing some info"></span>
            <?php endif; ?>
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="<?= htmlspecialchars(getUploadUrl($user['profile_photo'])) ?>" alt="" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; display: block; flex-shrink: 0;">
            <?php else: ?>
                <div class="avatar avatar-sm avatar-teal"><?= strtoupper(substr($user['first_name'],0,1) . substr($user['last_name'],0,1)) ?></div>
            <?php endif; ?>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= htmlspecialchars($user['first_name']) ?></div>
                <div class="sidebar-user-role"><?= ucfirst(str_replace('_', ' ', $user['user_type'])) ?></div>
            </div>
        </a>
        <a href="#" class="sidebar-link" onclick="TT.api('AuthController.php',{action:'logout'}).done(()=>location.href='<?= APP_URL ?>')">
            <span class="sidebar-icon"><?= ttIcon('logout', 'tt-icon', 18) ?></span>
            <span class="sidebar-label">Log Out</span>
        </a>
    </div>
</aside>

<!-- Main content area -->
<div class="hub-main">
    <!-- Topbar -->
    <header class="hub-topbar no-print">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <span></span><span></span><span></span>
        </button>
        <h1 class="topbar-title"><?= htmlspecialchars($moduleList[$currentModule][1] ?? 'Hub') ?></h1>
        <div class="topbar-actions">
            <?php include __DIR__ . '/notification-bell.php'; ?>
        </div>
    </header>

    <!-- Page content -->
    <div class="hub-content">
