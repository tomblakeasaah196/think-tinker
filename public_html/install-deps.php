<?php
/**
 * ============================================================
 * THINK & TINKER — DEPENDENCY INSTALLER (Browser Version)
 * ============================================================
 * 
 * HOW TO USE:
 * 1. Upload this file to public_html/ via cPanel File Manager
 * 2. Visit: https://think-tinker.com/install-deps.php
 * 3. Click "Install Dependencies"
 * 4. DELETE this file immediately after
 * 
 * Installs: DomPDF + PHPMailer via Composer
 * ============================================================
 */

set_time_limit(300); // 5 minutes max
ini_set('memory_limit', '256M');

$SECRET = 'thinktinker2026deps';
$baseDir = __DIR__;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Think & Tinker — Install Dependencies</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #1B2A4A; color: #FFF; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #FFF; color: #2C3E50; border-radius: 16px; padding: 40px; max-width: 700px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        h1 { color: #1AAFA0; font-size: 24px; margin-bottom: 8px; }
        .subtitle { color: #666; margin-bottom: 30px; font-size: 14px; }
        .warn { background: #FEF5E7; border-left: 4px solid #F47B20; padding: 12px 16px; border-radius: 4px; margin-bottom: 24px; font-size: 13px; }
        .warn strong { color: #F47B20; }
        button { background: #1AAFA0; color: #FFF; border: none; padding: 16px 32px; font-size: 16px; font-weight: 600; border-radius: 8px; cursor: pointer; width: 100%; }
        button:hover { background: #17998c; }
        .log { background: #F2F3F4; border-radius: 8px; padding: 20px; margin-top: 24px; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.8; max-height: 500px; overflow-y: auto; white-space: pre-wrap; }
        .ok { color: #27AE60; }
        .info { color: #1AAFA0; }
        .err { color: #E8614D; }
        .done { background: #27AE60; color: #FFF; padding: 16px; border-radius: 8px; margin-top: 20px; text-align: center; font-weight: 600; }
        .fail { background: #E8614D; color: #FFF; padding: 16px; border-radius: 8px; margin-top: 20px; }
        .next { margin-top: 20px; font-size: 13px; line-height: 1.8; }
        .next code { background: #F2F3F4; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>
<div class="card">

<?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['token'] ?? '') !== $SECRET): ?>

    <h1>📦 Install Dependencies</h1>
    <p class="subtitle">Downloads and installs DomPDF + PHPMailer via Composer.</p>
    
    <div class="warn">
        <strong>⚠ DELETE this file after installation.</strong><br>
        Requires: <code>composer.json</code> in public_html (created by setup.php).
    </div>

    <?php
    // Pre-flight checks
    $checks = [];
    $checks['composer.json'] = file_exists($baseDir . '/composer.json');
    $checks['PHP Version'] = version_compare(PHP_VERSION, '8.0', '>=');
    $checks['allow_url_fopen'] = ini_get('allow_url_fopen');
    $checks['Writable'] = is_writable($baseDir);
    
    $canExec = false;
    $disabledFunctions = explode(',', ini_get('disable_functions'));
    $disabledFunctions = array_map('trim', $disabledFunctions);
    
    if (!in_array('exec', $disabledFunctions) && function_exists('exec')) {
        $canExec = true;
    } elseif (!in_array('shell_exec', $disabledFunctions) && function_exists('shell_exec')) {
        $canExec = true;
    } elseif (!in_array('proc_open', $disabledFunctions) && function_exists('proc_open')) {
        $canExec = true;
    }
    $checks['Shell Access'] = $canExec;
    ?>

    <div style="margin-bottom: 24px; font-size: 13px;">
        <strong>Pre-flight checks:</strong><br>
        <?php foreach ($checks as $label => $ok): ?>
            <?= $ok ? '✅' : '❌' ?> <?= $label ?> — <?= $ok ? 'OK' : 'Missing' ?><br>
        <?php endforeach; ?>
        <?php if (!$canExec): ?>
            <br>
            <span style="color: #F47B20;">⚠ Shell functions disabled — will use fallback (direct download method).</span>
        <?php endif; ?>
    </div>

    <form method="POST">
        <input type="hidden" name="token" value="<?= $SECRET ?>">
        <button type="submit">Install Dependencies</button>
    </form>

<?php else: ?>

    <h1>Installing Dependencies...</h1>
    <div class="log"><?php

    function logLine($msg, $class = '') {
        echo "<span class=\"$class\">$msg</span>\n";
        if (ob_get_level()) ob_flush();
        flush();
    }

    $success = false;

    // ============================================================
    // Check if vendor already exists
    // ============================================================
    if (is_dir($baseDir . '/vendor/dompdf') && is_dir($baseDir . '/vendor/phpmailer')) {
        logLine("✓ vendor/ directory already contains DomPDF and PHPMailer.", "ok");
        logLine("  Nothing to install. Dependencies are already present.", "ok");
        $success = true;
    } else {

        // ============================================================
        // ATTEMPT 1: Try Composer via shell
        // ============================================================
        
        $canExec = false;
        $execFn = null;
        $disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));
        
        foreach (['exec', 'shell_exec', 'proc_open'] as $fn) {
            if (!in_array($fn, $disabledFunctions) && function_exists($fn)) {
                $canExec = true;
                $execFn = $fn;
                break;
            }
        }

        if ($canExec) {
            logLine("[Method 1] Shell access available — using Composer...", "info");

            // Download composer.phar
            logLine("  Downloading composer.phar...");
            $composerPhar = $baseDir . '/composer.phar';
            
            $downloaded = @file_put_contents(
                $composerPhar,
                file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar')
            );

            if ($downloaded) {
                logLine("  ✓ composer.phar downloaded (" . round($downloaded / 1024) . " KB)", "ok");
                chmod($composerPhar, 0755);

                // Find PHP binary
                $phpBin = PHP_BINARY ?: 'php';
                logLine("  PHP binary: $phpBin");
                logLine("  Running: composer install --no-dev --optimize-autoloader ...");

                $cmd = "cd " . escapeshellarg($baseDir) . " && " . 
                       escapeshellarg($phpBin) . " composer.phar install --no-dev --optimize-autoloader 2>&1";

                $output = '';
                if ($execFn === 'exec') {
                    exec($cmd, $outputArr, $returnCode);
                    $output = implode("\n", $outputArr);
                } elseif ($execFn === 'shell_exec') {
                    $output = shell_exec($cmd);
                    $returnCode = 0;
                } elseif ($execFn === 'proc_open') {
                    $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
                    $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);
                    $returnCode = proc_close($proc);
                }

                logLine($output);

                // Check if it worked
                if (is_dir($baseDir . '/vendor/dompdf') && is_dir($baseDir . '/vendor/phpmailer')) {
                    logLine("\n✓ Composer install succeeded!", "ok");
                    $success = true;
                } else {
                    logLine("\n✗ Composer install may have failed. Trying fallback...", "err");
                }

                // Clean up composer.phar
                @unlink($composerPhar);

            } else {
                logLine("  ✗ Could not download composer.phar. Trying fallback...", "err");
            }
        }

        // ============================================================
        // ATTEMPT 2: Direct download from GitHub (no shell needed)
        // ============================================================
        
        if (!$success) {
            logLine("\n[Method 2] Direct download — no shell required...", "info");

            if (!ini_get('allow_url_fopen')) {
                logLine("  ✗ allow_url_fopen is disabled. Cannot download files.", "err");
                logLine("  Please ask TrueHost support to enable allow_url_fopen.", "err");
            } else {

                $packages = [
                    [
                        'name' => 'PHPMailer',
                        'url'  => 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.3.zip',
                        'zip'  => 'phpmailer.zip',
                        'from' => 'PHPMailer-6.9.3',
                        'to'   => 'vendor/phpmailer/phpmailer',
                    ],
                    [
                        'name' => 'DomPDF',
                        'url'  => 'https://github.com/dompdf/dompdf/archive/refs/tags/v2.0.8.zip',
                        'zip'  => 'dompdf.zip',
                        'from' => 'dompdf-2.0.8',
                        'to'   => 'vendor/dompdf/dompdf',
                    ],
                    [
                        'name' => 'PHP CSS Parser (DomPDF dependency)',
                        'url'  => 'https://github.com/sabberworm/PHP-CSS-Parser/archive/refs/tags/v8.6.0.zip',
                        'zip'  => 'css-parser.zip',
                        'from' => 'PHP-CSS-Parser-8.6.0',
                        'to'   => 'vendor/sabberworm/php-css-parser',
                    ],
                ];

                @mkdir($baseDir . '/vendor', 0755, true);
                $allOk = true;

                foreach ($packages as $pkg) {
                    logLine("  Downloading {$pkg['name']}...");
                    
                    $zipPath = $baseDir . '/' . $pkg['zip'];
                    $zipData = @file_get_contents($pkg['url']);

                    if (!$zipData) {
                        logLine("    ✗ Failed to download {$pkg['name']}", "err");
                        $allOk = false;
                        continue;
                    }

                    file_put_contents($zipPath, $zipData);
                    logLine("    ✓ Downloaded (" . round(strlen($zipData) / 1024) . " KB)", "ok");

                    // Extract
                    $zip = new ZipArchive();
                    if ($zip->open($zipPath) === true) {
                        // Extract to temp dir
                        $tempDir = $baseDir . '/vendor/_temp_' . md5($pkg['name']);
                        @mkdir($tempDir, 0755, true);
                        $zip->extractTo($tempDir);
                        $zip->close();

                        // Move from extracted folder to target
                        $sourceDir = $tempDir . '/' . $pkg['from'];
                        $targetDir = $baseDir . '/' . $pkg['to'];
                        
                        // Create parent directories
                        @mkdir(dirname($targetDir), 0755, true);
                        
                        // Rename/move
                        if (is_dir($sourceDir)) {
                            rename($sourceDir, $targetDir);
                            logLine("    ✓ Extracted to {$pkg['to']}", "ok");
                        } else {
                            // Try finding the folder
                            $dirs = glob($tempDir . '/*', GLOB_ONLYDIR);
                            if (!empty($dirs)) {
                                rename($dirs[0], $targetDir);
                                logLine("    ✓ Extracted to {$pkg['to']}", "ok");
                            } else {
                                logLine("    ✗ Could not find extracted folder", "err");
                                $allOk = false;
                            }
                        }

                        // Clean up temp
                        @rmdir($tempDir);
                    } else {
                        logLine("    ✗ Failed to open zip", "err");
                        $allOk = false;
                    }

                    // Clean up zip
                    @unlink($zipPath);
                }

                // ============================================================
                // Generate autoload.php
                // ============================================================
                
                if ($allOk) {
                    logLine("\n  Generating autoload.php...", "info");

                    $autoload = '<?php
/**
 * Think & Tinker — Simple Autoloader
 * Generated by install-deps.php
 * Loads DomPDF + PHPMailer
 */

// PHPMailer
require_once __DIR__ . \'/phpmailer/phpmailer/src/Exception.php\';
require_once __DIR__ . \'/phpmailer/phpmailer/src/PHPMailer.php\';
require_once __DIR__ . \'/phpmailer/phpmailer/src/SMTP.php\';

// CSS Parser (DomPDF dependency)
require_once __DIR__ . \'/sabberworm/php-css-parser/lib/Sabberworm/CSS/CSSList/AtRuleBlockList.php\';

// DomPDF — use its own autoloader
require_once __DIR__ . \'/dompdf/dompdf/autoload.inc.php\';
';
                    file_put_contents($baseDir . '/vendor/autoload.php', $autoload);
                    logLine("  ✓ vendor/autoload.php created", "ok");

                    $success = true;
                }
            }
        }
    }

    // ============================================================
    // FINAL VERIFICATION
    // ============================================================
    
    echo "\n";
    logLine("============================================", "info");
    logLine("VERIFICATION", "info");
    logLine("============================================", "info");

    $checks = [
        'vendor/autoload.php'                    => 'Autoloader',
        'vendor/phpmailer/phpmailer/src/PHPMailer.php' => 'PHPMailer',
        'vendor/dompdf/dompdf/autoload.inc.php'  => 'DomPDF',
        'vendor/sabberworm/php-css-parser/lib/Sabberworm/CSS/Parser.php' => 'CSS Parser',
    ];

    $allVerified = true;
    foreach ($checks as $path => $label) {
        $exists = file_exists($baseDir . '/' . $path);
        logLine(($exists ? '  ✓' : '  ✗') . " $label ($path)", $exists ? 'ok' : 'err');
        if (!$exists) $allVerified = false;
    }

    ?></div>

    <?php if ($success && $allVerified): ?>
        <div class="done">✓ All dependencies installed and verified</div>
        <div class="next">
            <strong>NEXT STEPS:</strong><br>
            1. <strong style="color:#E8614D;">DELETE this file (install-deps.php) NOW</strong><br>
            2. Start coding <code>includes/config.php</code><br><br>
            
            <strong>Use in your PHP files:</strong><br>
            <code>require_once __DIR__ . '/../vendor/autoload.php';</code>
        </div>
    <?php elseif ($success): ?>
        <div class="done">✓ Dependencies downloaded (some files may need manual verification)</div>
    <?php else: ?>
        <div class="fail">
            <strong>Installation could not complete automatically.</strong><br><br>
            <strong>Manual fallback:</strong><br>
            1. On any computer with PHP + Composer installed, run:<br>
            <code style="background:rgba(0,0,0,0.2); padding: 4px 8px; border-radius: 4px;">composer require dompdf/dompdf phpmailer/phpmailer</code><br><br>
            2. Zip the entire <code>vendor/</code> folder<br>
            3. Upload the zip to <code>public_html/</code> via File Manager<br>
            4. Extract it in place<br>
        </div>
    <?php endif; ?>

<?php endif; ?>

</div>
</body>
</html>
