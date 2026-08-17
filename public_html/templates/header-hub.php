<?php
/**
 * templates/header-hub.php — Operations Hub sidebar + topbar
 * Include at top of every hub/ page.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$user = requireAuth();
$modules = getUserModules($user);
$pageTitle = ($pageTitle ?? 'Hub') . ' — Think & Tinker Hub';
$currentModule = $currentModule ?? '';
$userName = $user['first_name'] . ' ' . $user['last_name'];

// Module definitions: slug => [icon, label]
$moduleList = [
    'dashboard'  => ['📊', 'Dashboard'],
    'clients'    => ['👥', 'Clients'],
    'sessions'   => ['📅', 'Sessions'],
    'finance'    => ['💰', 'Finance'],
    'staff'      => ['🏢', 'Staff'],
    'bookstore'  => ['📚', 'Bookstore'],
    'club'       => ['🎪', 'STEM Club'],
    'blog'       => ['✍️', 'Blog'],
    'messages'   => ['💬', 'Messages'],
    'documents'  => ['📄', 'Documents'],
    'settings'   => ['⚙️', 'Settings'],
    'help'       => ['❓', 'Help Centre'],
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
            <span class="logo-icon">⚡</span>
            <span class="logo-text">Think & Tinker</span>
        </a>
        <span class="sidebar-badge">HUB</span>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($moduleList as $slug => [$icon, $label]):
            if ($slug !== 'help' && !in_array($slug, $modules)) continue;
            $isActive = ($currentModule === $slug) ? 'active' : '';
        ?>
        <a href="<?= HUB_URL ?>/<?= $slug === 'dashboard' ? 'dashboard' : $slug ?>.php" class="sidebar-link <?= $isActive ?>">
            <span class="sidebar-icon"><?= $icon ?></span>
            <span class="sidebar-label"><?= $label ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar avatar-sm avatar-teal"><?= strtoupper(substr($user['first_name'],0,1) . substr($user['last_name'],0,1)) ?></div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= htmlspecialchars($user['first_name']) ?></div>
                <div class="sidebar-user-role"><?= ucfirst(str_replace('_', ' ', $user['user_type'])) ?></div>
            </div>
        </div>
        <a href="#" class="sidebar-link" onclick="TT.api('AuthController.php',{action:'logout'}).done(()=>location.href='<?= APP_URL ?>')">
            <span class="sidebar-icon">🚪</span>
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
