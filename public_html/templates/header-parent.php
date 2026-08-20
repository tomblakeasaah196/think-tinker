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
    <!-- `user-scalable=no` is deliberately absent here: on several Android
         browsers / WebViews it caused the layout viewport to fall back to a
         wide (~980px) "desktop" width, which broke other width-dependent
         layout in the portal. viewport-fit=cover keeps the composer flush
         against the safe-area on notched devices. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="theme-color" content="#1AAFA0">
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/assets/img/logo/favicon.png">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/parent.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Expose the API config EARLY (before any page script or messenger.js
        // runs). messenger.js historically raced the footer, which only set
        // TT.config.apiBase / csrfToken at the very end of the document — a
        // send fired before that pointed at the wrong URL and the bubble
        // "vanished". main.js merges (never clobbers) this config when it
        // loads, so both orderings are now safe.
        window.TT = window.TT || {};
        TT.config = Object.assign(TT.config || {}, {
            apiBase: '<?= APP_URL ?>/api',
            csrfToken: '<?= CSRF_TOKEN ?>'
        });
    </script>
</head>
<body class="has-bottom-nav parent-portal<?= !empty($bodyClass) ? ' ' . htmlspecialchars($bodyClass) : '' ?>">

<!-- Top Bar -->
<header class="portal-topbar no-print">
    <div class="container flex items-center justify-between" style="height: 56px;">
        <div class="flex items-center gap-2">
            <a href="<?= APP_URL ?>/parent/" class="topbar-logo">
                <img src="<?= APP_URL ?>/assets/img/logo/logo-full.png" alt="Think & Tinker" class="topbar-logo-img">
            </a>
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

<!-- Navigation for the portal is a SINGLE bottom tab bar (footer-parent.php),
     shown at every screen size — see assets/css/main.css's .bottom-nav rules.
     There used to also be a second, near-identical "desktop sub-nav" strip
     rendered here between the top bar and the page content; on top of being
     one more copy of the same 5 links to keep in sync, a mis-detected
     viewport width could make both bars render at once. Removed rather than
     patched — one nav, one source of truth. -->

<style>
/* Frosted glass top bar — echoes the public-site glass tokens (variables.css)
   rather than the old flat white strip. A translucent teal-tinted surface +
   blur over the portal's soft mesh background. */
.portal-topbar {
    background: linear-gradient(180deg, rgba(255,255,255,0.72), rgba(255,255,255,0.55));
    -webkit-backdrop-filter: blur(14px) saturate(1.4);
    backdrop-filter: blur(14px) saturate(1.4);
    border-bottom: 1px solid rgba(26,175,160,0.18);
    box-shadow: 0 4px 24px rgba(15,23,42,0.06);
    position: sticky; top: 0; z-index: 200;
}
.topbar-logo { display: flex; align-items: center; }
.topbar-logo-img { height: 30px; width: auto; display: block; }
.avatar-photo { width: 100%; height: 100%; border-radius: inherit; object-fit: cover; display: block; }
</style>

<div class="portal-content container" style="padding-top: var(--space-3); padding-bottom: var(--space-3);">
<!-- This tag is closed by footer content below, at end of each page just close </div> then include bottom nav -->
