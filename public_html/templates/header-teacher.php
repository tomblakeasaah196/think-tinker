<?php
/**
 * templates/header-teacher.php — Teacher Portal header
 * For public teacher pages (no auth needed) and dashboard (auth needed).
 */
$pageTitle = ($pageTitle ?? 'For Teachers') . ' — Think & Tinker';
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
$isAuth = isset($teacherUser);
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
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/teacher.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="teacher-portal">

<nav class="teacher-nav no-print">
    <div class="container flex items-center justify-between" style="height: 56px;">
        <a href="<?= APP_URL ?>/teacher/" class="topbar-logo" style="color: var(--deep-navy);">
            Think & Tinker <span style="color: var(--spark-orange); font-size: 0.75rem; font-weight: 600;">FOR TEACHERS</span>
        </a>
        <div class="flex items-center gap-2">
            <?php if ($isAuth): ?>
                <?php include __DIR__ . '/notification-bell.php'; ?>
                <span style="font-size: 0.875rem; font-weight: 600;"><?= htmlspecialchars($teacherUser['first_name']) ?></span>
                <a href="#" onclick="TT.api('AuthController.php',{action:'logout'}).done(()=>location.href='<?= APP_URL ?>/teacher/')" class="btn btn-ghost btn-sm">Log Out</a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/teacher/login.php" class="btn btn-outline btn-sm">Log In</a>
                <a href="<?= APP_URL ?>/teacher/apply.php" class="btn btn-navy btn-sm">Apply as Tutor</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.teacher-nav { background: #FFF; border-bottom: 2px solid var(--deep-navy); position: sticky; top: 0; z-index: 200; }
.topbar-logo { font-family: 'Quicksand', sans-serif; font-weight: 800; font-size: 1.125rem; }
</style>

<main class="container" style="padding: var(--space-3) var(--space-2);">
