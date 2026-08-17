<?php
/**
 * templates/og-meta.php — Open Graph + Twitter Card meta tags
 * Set $ogTitle, $ogDescription, $ogImage, $ogType before including.
 */
$ogTitle = $ogTitle ?? ($pageTitle ?? 'Think & Tinker Kids Club');
$ogDescription = $ogDescription ?? ($pageDescription ?? 'Building curious minds through play, creativity, and exploration.');
$ogImage = $ogImage ?? (APP_URL . '/assets/img/og/default-og.jpg');
$ogType = $ogType ?? 'website';
$ogUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'think-tinker.com') . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!-- Open Graph -->
<meta property="og:title" content="<?= htmlspecialchars($ogTitle) ?>">
<meta property="og:description" content="<?= htmlspecialchars($ogDescription) ?>">
<meta property="og:image" content="<?= htmlspecialchars($ogImage) ?>">
<meta property="og:url" content="<?= htmlspecialchars($ogUrl) ?>">
<meta property="og:type" content="<?= $ogType ?>">
<meta property="og:site_name" content="Think & Tinker Kids Club">
<meta property="og:locale" content="en_NG">
<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($ogTitle) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($ogDescription) ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($ogImage) ?>">
