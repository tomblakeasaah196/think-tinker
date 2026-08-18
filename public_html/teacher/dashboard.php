<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
$teacherUser = requireAuth();
// Tutors use the Hub for their dashboard
header('Location: ' . HUB_URL . '/dashboard.php');
exit;
