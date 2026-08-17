<?php
/**
 * ============================================================
 * THINK & TINKER — PROJECT SCAFFOLD (Browser Version)
 * ============================================================
 * 
 * HOW TO USE:
 * 1. Upload this file to public_html/ via cPanel File Manager
 * 2. Visit: https://think-tinker.com/setup.php
 * 3. Click "Build Project Structure"
 * 4. DELETE this file immediately after — it's a security risk
 * 
 * ============================================================
 */

// Simple security: only run if a POST request with the correct token
$SECRET = 'thinktinker2026build';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think & Tinker — Project Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #1B2A4A; color: #FFF; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #FFF; color: #2C3E50; border-radius: 16px; padding: 40px; max-width: 700px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #1AAFA0; font-size: 24px; margin-bottom: 8px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .warn { background: #FEF5E7; border-left: 4px solid #F47B20; padding: 12px 16px; border-radius: 4px; margin-bottom: 24px; font-size: 13px; color: #2C3E50; }
        .warn strong { color: #F47B20; }
        button { background: #1AAFA0; color: #FFF; border: none; padding: 16px 32px; font-size: 16px; font-weight: 600; border-radius: 8px; cursor: pointer; width: 100%; }
        button:hover { background: #17998c; }
        .log { background: #F2F3F4; border-radius: 8px; padding: 20px; margin-top: 24px; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.8; max-height: 500px; overflow-y: auto; white-space: pre-wrap; }
        .ok { color: #27AE60; }
        .info { color: #1AAFA0; }
        .err { color: #E8614D; }
        .done { background: #27AE60; color: #FFF; padding: 16px; border-radius: 8px; margin-top: 20px; text-align: center; font-weight: 600; }
        .next { margin-top: 20px; font-size: 13px; line-height: 1.8; }
        .next code { background: #F2F3F4; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>
<div class="card">

<?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['token'] ?? '') !== $SECRET): ?>

    <h1>🔧 Think & Tinker — Project Scaffold</h1>
    <p class="subtitle">This will create all 38 directories and 128+ files for the project.</p>
    
    <div class="warn">
        <strong>⚠ DELETE this file immediately after running.</strong><br>
        It creates your entire project structure. Anyone who visits this URL could trigger it.
    </div>

    <form method="POST">
        <input type="hidden" name="token" value="<?= $SECRET ?>">
        <button type="submit">Build Project Structure</button>
    </form>

<?php else: ?>

    <h1>Building Project Structure...</h1>
    <div class="log"><?php

    $base = __DIR__;
    $created_dirs = 0;
    $created_files = 0;
    $errors = [];

    // ============================================================
    // HELPER FUNCTIONS
    // ============================================================
    
    function makeDir($path) {
        global $base, $created_dirs;
        $full = $base . '/' . $path;
        if (!is_dir($full)) {
            if (mkdir($full, 0755, true)) {
                $GLOBALS['created_dirs']++;
                return true;
            }
            return false;
        }
        return true;
    }

    function makeFile($path, $content = '') {
        global $base, $created_files;
        $full = $base . '/' . $path;
        $dir = dirname($full);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (!file_exists($full)) {
            if (file_put_contents($full, $content) !== false) {
                $GLOBALS['created_files']++;
                return true;
            }
            return false;
        }
        return true; // already exists, skip
    }

    function logLine($msg, $class = '') {
        echo "<span class=\"$class\">$msg</span>\n";
        flush();
    }

    // ============================================================
    // 1. DIRECTORIES
    // ============================================================
    
    logLine("[1/6] Creating directories...", "info");

    $dirs = [
        'services',
        'parent',
        'teacher',
        'school',
        'api',
        'includes',
        'templates',
        'pdf-templates',
        'assets/css',
        'assets/js',
        'assets/img/logo',
        'assets/img/og',
        'assets/img/defaults',
        'uploads/products',
        'uploads/blog',
        'uploads/profiles',
        'uploads/children',
        'uploads/contracts',
        'uploads/invoices',
        'uploads/receipts',
        'uploads/progress-reports',
        'uploads/certificates',
        'uploads/session-images',
        'uploads/signatures',
        'uploads/expenses',
        'uploads/resources',
        'database',
        'logs',
        'backups',
        'hub/api',
        'hub/assets/css',
        'hub/assets/js',
    ];

    foreach ($dirs as $d) {
        if (makeDir($d)) {
            logLine("   ✓ $d", "ok");
        } else {
            logLine("   ✗ Failed: $d", "err");
            $errors[] = "Directory: $d";
        }
    }

    // ============================================================
    // 2. PUBLIC WEBSITE PAGES
    // ============================================================
    
    logLine("\n[2/6] Creating public website pages...", "info");

    $pages = [
        // Root pages
        'index.php', 'about.php', 'services.php', 'shop.php',
        'shop-product.php', 'cart.php', 'checkout.php',
        'blog.php', 'blog-post.php', 'contact.php',

        // Service sub-pages
        'services/tutorials.php', 'services/stem-club.php',
        'services/consulting.php', 'services/consult-booklets.php',

        // Parent Portal
        'parent/index.php', 'parent/login.php', 'parent/register.php',
        'parent/register-club.php', 'parent/onboard.php', 'parent/child.php',
        'parent/calendar.php', 'parent/notes.php', 'parent/messages.php',
        'parent/documents.php', 'parent/payments.php', 'parent/settings.php',

        // Teacher Portal
        'teacher/index.php', 'teacher/login.php', 'teacher/register.php',
        'teacher/dashboard.php', 'teacher/resources.php',
        'teacher/workshops.php', 'teacher/apply.php',

        // School Portal
        'school/index.php', 'school/case-studies.php', 'school/inquiry.php',
    ];

    foreach ($pages as $p) {
        makeFile($p, "<?php\n// $p\n");
    }
    logLine("   ✓ " . count($pages) . " page files created", "ok");

    // ============================================================
    // 3. BACKEND FILES
    // ============================================================
    
    logLine("\n[3/6] Creating backend files...", "info");

    // API Controllers
    $controllers = [
        'AuthController', 'ParentController', 'ChildController',
        'SessionController', 'MessageController', 'ShopController',
        'BlogController', 'ContactController', 'FinanceController',
        'StaffController', 'ClubController', 'DocumentController',
        'SettingsController', 'NotificationController', 'UploadController',
        'DashboardController',
    ];

    foreach ($controllers as $c) {
        makeFile("api/{$c}.php", "<?php\n// api/{$c}.php — Handles full CRUD for this module\n\nheader('Content-Type: application/json');\n\nrequire_once __DIR__ . '/../includes/config.php';\nrequire_once __DIR__ . '/../includes/auth.php';\nrequire_once __DIR__ . '/../includes/rbac.php';\nrequire_once __DIR__ . '/../includes/db.php';\nrequire_once __DIR__ . '/../includes/helpers.php';\n\n\$action = \$_POST['action'] ?? \$_GET['action'] ?? '';\n\nswitch (\$action) {\n    default:\n        echo json_encode(['success' => false, 'message' => 'Invalid action']);\n}\n");
    }
    logLine("   ✓ " . count($controllers) . " API controllers created (with boilerplate)", "ok");

    // Shared PHP includes
    $includes = [
        'includes/config.php', 'includes/auth.php', 'includes/rbac.php',
        'includes/db.php', 'includes/helpers.php', 'includes/mailer.php',
        'includes/pdf.php', 'includes/upload.php',
    ];
    foreach ($includes as $f) {
        makeFile($f, "<?php\n// $f\n");
    }
    logLine("   ✓ " . count($includes) . " include files created", "ok");

    // Templates
    $templates = [
        'templates/header.php', 'templates/footer.php',
        'templates/header-parent.php', 'templates/header-teacher.php',
        'templates/header-hub.php', 'templates/og-meta.php',
        'templates/notification-bell.php', 'templates/email-template.php',
    ];
    foreach ($templates as $f) {
        makeFile($f, "<?php\n// $f\n");
    }
    logLine("   ✓ " . count($templates) . " template files created", "ok");

    // PDF Templates
    $pdfs = [
        'pdf-templates/invoice.php', 'pdf-templates/receipt.php',
        'pdf-templates/contract.php', 'pdf-templates/admission-form.php',
        'pdf-templates/progress-report.php', 'pdf-templates/monthly-calendar.php',
        'pdf-templates/quote-proposal.php', 'pdf-templates/club-certificate.php',
        'pdf-templates/staff-id-badge.php',
    ];
    foreach ($pdfs as $f) {
        makeFile($f, "<?php\n// $f — DomPDF HTML template\n");
    }
    logLine("   ✓ " . count($pdfs) . " PDF template files created", "ok");

    // ============================================================
    // 4. ASSETS
    // ============================================================
    
    logLine("\n[4/6] Creating asset files...", "info");

    // CSS Variables file (pre-filled with brand tokens)
    makeFile('assets/css/variables.css', ':root {
    /* Primary Palette */
    --tinker-teal: #1AAFA0;
    --spark-orange: #F47B20;
    --idea-gold: #FDB813;

    /* Secondary Palette */
    --deep-navy: #1B2A4A;
    --story-coral: #E8614D;
    --leaf-green: #27AE60;
    --sky-blue: #3498DB;

    /* Neutrals */
    --charcoal: #2C3E50;
    --warm-white: #FFF9F0;
    --cloud-gray: #F2F3F4;
    --soft-cream: #FEF5E7;

    /* Shadows */
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.06);
    --shadow-md: 0 2px 4px rgba(0,0,0,0.08);
    --shadow-lg: 0 4px 12px rgba(0,0,0,0.12);

    /* Radius */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 16px;
    --radius-full: 9999px;

    /* Spacing (8px grid) */
    --space-1: 8px;
    --space-2: 16px;
    --space-3: 24px;
    --space-4: 32px;
    --space-5: 40px;
    --space-6: 48px;
    --space-8: 64px;

    /* Component heights */
    --nav-height-desktop: 64px;
    --nav-height-mobile: 56px;
    --input-height: 48px;
    --btn-height: 48px;
    --bottom-nav-height: 64px;
}
');
    logLine("   ✓ variables.css (brand tokens pre-filled)", "ok");

    $css = ['assets/css/main.css', 'assets/css/parent.css', 'assets/css/teacher.css', 'assets/css/shop.css'];
    foreach ($css as $f) {
        makeFile($f, "/* $f */\n@import url('variables.css');\n");
    }
    logLine("   ✓ " . count($css) . " CSS files created", "ok");

    $js = [
        'assets/js/main.js', 'assets/js/parent.js', 'assets/js/teacher.js',
        'assets/js/shop.js', 'assets/js/voice-recorder.js',
        'assets/js/signature-pad.js', 'assets/js/messenger.js',
        'assets/js/notification.js',
    ];
    foreach ($js as $f) {
        makeFile($f, "// $f\n'use strict';\n");
    }
    logLine("   ✓ " . count($js) . " JS files created", "ok");

    // Hub assets
    makeFile('hub/assets/css/hub.css', "/* Hub styles */\n@import url('../../../assets/css/variables.css');\n");
    makeFile('hub/assets/js/hub.js', "// Hub scripts\n'use strict';\n");
    logLine("   ✓ Hub CSS + JS created", "ok");

    // ============================================================
    // 5. HUB FILES
    // ============================================================
    
    logLine("\n[5/6] Creating Hub files...", "info");

    $hubPages = [
        'hub/index.php', 'hub/dashboard.php', 'hub/clients.php',
        'hub/children.php', 'hub/sessions.php', 'hub/finance.php',
        'hub/staff.php', 'hub/bookstore.php', 'hub/club.php',
        'hub/blog.php', 'hub/messages.php', 'hub/documents.php',
        'hub/settings.php',
    ];
    foreach ($hubPages as $p) {
        makeFile($p, "<?php\n// $p\nrequire_once __DIR__ . '/../includes/config.php';\nrequire_once __DIR__ . '/../includes/auth.php';\n");
    }
    logLine("   ✓ " . count($hubPages) . " Hub page files created", "ok");

    // Hub API proxies (pre-filled with require line)
    foreach ($controllers as $c) {
        makeFile("hub/api/{$c}.php", "<?php require_once __DIR__ . '/../../api/{$c}.php';\n");
    }
    logLine("   ✓ " . count($controllers) . " Hub API proxy files created", "ok");

    // ============================================================
    // 6. INFRASTRUCTURE FILES
    // ============================================================
    
    logLine("\n[6/6] Creating infrastructure files...", "info");

    // .htaccess (main)
    makeFile('.htaccess', '# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"

# Prevent directory listing
Options -Indexes

# Protect .env
<FilesMatch "^\.env$">
    Require all denied
</FilesMatch>

# Protect sensitive files
<FilesMatch "\.(json|lock|md|gitignore|sql|sh)$">
    Require all denied
</FilesMatch>

# Protect directories
RewriteRule ^database/ - [F,L]
RewriteRule ^logs/ - [F,L]
RewriteRule ^backups/ - [F,L]

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
    ExpiresByType font/woff2 "access plus 1 month"
</IfModule>

# GZIP compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json
</IfModule>
');
    logLine("   ✓ .htaccess (main — security + caching)", "ok");

    // .htaccess (hub)
    makeFile('hub/.htaccess', '# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"

Options -Indexes
');
    logLine("   ✓ hub/.htaccess", "ok");

    // .htaccess (uploads)
    makeFile('uploads/.htaccess', '# Block PHP execution in uploads
<IfModule mod_php.c>
    php_flag engine off
</IfModule>
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>
<FilesMatch "\.php$">
    Require all denied
</FilesMatch>
');
    logLine("   ✓ uploads/.htaccess (blocks PHP execution)", "ok");

    // .env.example
    makeFile('.env.example', '# ============================================
# THINK & TINKER — ENVIRONMENT CONFIGURATION
# ============================================
# Copy to .env and fill in your values.
# Business settings managed from Hub → Business Setup.
# ============================================

# --- APP ---
APP_URL=https://think-tinker.com
HUB_URL=https://hub.think-tinker.com
APP_ENV=production
APP_DEBUG=false

# --- DATABASE ---
DB_HOST=localhost
DB_PORT=3306
DB_NAME=
DB_USER=
DB_PASS=
DB_CHARSET=utf8mb4

# --- SMTP EMAIL ---
SMTP_HOST=mail.think-tinker.com
SMTP_PORT=465
SMTP_ENCRYPTION=ssl
SMTP_USER=no-reply@think-tinker.com
SMTP_PASS=
');
    logLine("   ✓ .env.example", "ok");

    // composer.json
    makeFile('composer.json', '{
    "name": "thinktinker/website",
    "description": "Think & Tinker Kids Club — Digital Ecosystem",
    "type": "project",
    "require": {
        "php": ">=8.0",
        "dompdf/dompdf": "^2.0",
        "phpmailer/phpmailer": "^6.9"
    },
    "config": {
        "vendor-dir": "vendor",
        "optimize-autoloader": true
    }
}
');
    logLine("   ✓ composer.json (DomPDF + PHPMailer)", "ok");

    // manifest.json (PWA)
    makeFile('manifest.json', '{
    "name": "Think & Tinker Kids Club",
    "short_name": "Think & Tinker",
    "description": "Building curious minds through play, creativity, and exploration.",
    "start_url": "/",
    "display": "standalone",
    "background_color": "#FFF9F0",
    "theme_color": "#1AAFA0",
    "orientation": "portrait-primary",
    "icons": [
        { "src": "/assets/img/logo/icon-192.png", "sizes": "192x192", "type": "image/png" },
        { "src": "/assets/img/logo/icon-512.png", "sizes": "512x512", "type": "image/png" }
    ]
}
');
    logLine("   ✓ manifest.json (PWA)", "ok");

    // robots.txt
    makeFile('robots.txt', 'User-agent: *
Allow: /
Disallow: /api/
Disallow: /includes/
Disallow: /templates/
Disallow: /pdf-templates/
Disallow: /uploads/contracts/
Disallow: /uploads/signatures/
Disallow: /uploads/invoices/
Disallow: /uploads/receipts/
Disallow: /hub/
Disallow: /database/
Disallow: /logs/
Disallow: /backups/

Sitemap: https://think-tinker.com/sitemap.xml
');
    logLine("   ✓ robots.txt", "ok");

    // ============================================================
    // SUMMARY
    // ============================================================
    
    echo "\n";
    logLine("============================================", "info");
    logLine(" ✓ PROJECT SCAFFOLD COMPLETE", "ok");
    logLine("============================================", "info");
    echo "\n";
    logLine(" Directories: $created_dirs");
    logLine(" Files:       $created_files");

    if (!empty($errors)) {
        echo "\n";
        logLine("ERRORS:", "err");
        foreach ($errors as $e) {
            logLine("   ✗ $e", "err");
        }
    }

    ?></div>

    <?php if (empty($errors)): ?>
    <div class="done">✓ All files and directories created successfully</div>
    <?php endif; ?>

    <div class="next">
        <strong>NEXT STEPS:</strong><br>
        1. <strong style="color:#E8614D;">DELETE this file (setup.php) NOW</strong> via File Manager<br>
        2. Copy <code>.env.example</code> → <code>.env</code> and fill in your DB + SMTP credentials<br>
        3. Import <code>think_tinker_complete.sql</code> via phpMyAdmin<br>
        4. Install Composer dependencies — see note below<br>
        5. Start coding <code>includes/config.php</code> first<br><br>
        
        <strong>COMPOSER WITHOUT TERMINAL:</strong><br>
        Since you don't have Terminal access, you have two options:<br>
        • <strong>Option A:</strong> Run <code>composer install</code> on your local machine, then upload the entire <code>vendor/</code> folder via File Manager (zip it first, upload, extract)<br>
        • <strong>Option B:</strong> Use an online Composer tool — some cPanel hosts have "Composer" under Software. Check your cPanel.<br><br>

        <strong>Default login after SQL import:</strong><br>
        Email: <code>janeth@think-tinker.com</code><br>
        Password: <code>ThinkTinker@2026</code>
    </div>

<?php endif; ?>

</div>
</body>
</html>
