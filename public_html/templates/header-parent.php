<?php
/**
 * templates/header-parent.php — Parent Portal header + bottom tab nav
 * Requires auth. Include at top of every parent/ page.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../includes/icons.php';

$user = requireAuth();
requireUserType($user, 'parent');

$pageTitle = ($pageTitle ?? 'Parent Portal') . ' — Think & Tinker';
$currentTab = $currentTab ?? 'home';
$unreadMessages = dbCount('messages',
    "conversation_id IN (SELECT DISTINCT conversation_id FROM messages WHERE sender_id = ?) AND sender_type != 'parent' AND is_read = 0",
    [$user['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="theme-color" content="#1AAFA0">
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/img/logo/favicon.png">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/parent.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="has-bottom-nav parent-portal<?= !empty($bodyClass) ? ' ' . htmlspecialchars($bodyClass) : '' ?>">

<!-- Top Bar -->
<header class="portal-topbar no-print">
    <div class="container flex items-center justify-between" style="height: 56px;">
        <div class="flex items-center gap-2">
            <a href="<?= APP_URL ?>/parent/" class="topbar-logo">Think & Tinker</a>
        </div>
        <div class="flex items-center gap-1">
            <a href="<?= APP_URL ?>/" class="btn-back-home"><?= ttIcon('arrow-left', 'tt-icon', 14) ?> Home</a>
            <?php include __DIR__ . '/notification-bell.php'; ?>
            <div class="dropdown">
                <button data-dropdown class="avatar avatar-sm avatar-teal">
                    <?php if (!empty($user['profile_photo'])): ?>
                        <img src="<?= htmlspecialchars(getUploadUrl($user['profile_photo'])) ?>" alt="" class="avatar-photo">
                    <?php else: ?>
                        <?= strtoupper(substr($user['first_name'],0,1) . substr($user['last_name'],0,1)) ?>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu">
                    <div style="padding: 12px 16px; border-bottom: 1px solid var(--cloud-gray);">
                        <strong style="font-size: 0.875rem;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                        <div style="font-size: 0.75rem; color: #999;"><?= htmlspecialchars($user['email']) ?></div>
                    </div>
                    <a href="<?= APP_URL ?>/parent/settings.php" class="dropdown-item"><?= ttIcon('sliders', 'tt-icon', 16) ?> Settings</a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" onclick="TT.api('AuthController.php',{action:'logout'}).done(()=>location.href='<?= APP_URL ?>')"><?= ttIcon('logout', 'tt-icon', 16) ?> Log Out</a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Desktop sub-nav: same 5 tabs as the mobile bottom nav below (footer-parent.php),
     which is hidden at desktop widths. Keeps a way back to the dashboard visible
     from Payments/Messages/My Child on any non-mobile screen. -->
<nav class="portal-subnav no-print">
    <a href="<?= APP_URL ?>/parent/" class="subnav-item <?= $currentTab === 'home' ? 'active' : '' ?>">
        <span class="subnav-icon"><?= ttIcon('home', 'tt-icon', 17) ?></span> Home
    </a>
    <a href="<?= APP_URL ?>/parent/calendar.php" class="subnav-item <?= $currentTab === 'calendar' ? 'active' : '' ?>">
        <span class="subnav-icon"><?= ttIcon('calendar', 'tt-icon', 17) ?></span> Calendar
    </a>
    <a href="<?= APP_URL ?>/parent/messages.php" class="subnav-item <?= $currentTab === 'messages' ? 'active' : '' ?>">
        <span class="subnav-icon"><?= ttIcon('message', 'tt-icon', 17) ?></span> Messages
        <?php if (($unreadMessages ?? 0) > 0): ?><span class="subnav-badge"><?= $unreadMessages ?></span><?php endif; ?>
    </a>
    <a href="<?= APP_URL ?>/parent/payments.php" class="subnav-item <?= $currentTab === 'payments' ? 'active' : '' ?>">
        <span class="subnav-icon"><?= ttIcon('card', 'tt-icon', 17) ?></span> Payments
    </a>
    <a href="<?= APP_URL ?>/parent/child.php" class="subnav-item <?= $currentTab === 'child' ? 'active' : '' ?>">
        <span class="subnav-icon"><?= ttIcon('smile', 'tt-icon', 17) ?></span> My Child
    </a>
</nav>

<style>
.portal-topbar { background: #FFF; border-bottom: 1px solid var(--cloud-gray); position: sticky; top: 0; z-index: 200; }
.topbar-logo { font-family: 'Quicksand', sans-serif; font-weight: 800; font-size: 1.125rem; color: var(--tinker-teal); }
.avatar-photo { width: 100%; height: 100%; border-radius: inherit; object-fit: cover; display: block; }
</style>

<div class="portal-content container" style="padding-top: var(--space-3); padding-bottom: var(--space-3);">
<!-- This tag is closed by footer content below, at end of each page just close </div> then include bottom nav -->
