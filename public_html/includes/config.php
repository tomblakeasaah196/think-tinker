<?php
/**
 * includes/config.php
 * Core configuration — loads .env, starts session, sets constants.
 * This file is required FIRST by every page and controller.
 */

// Prevent direct access
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'config.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

// ============================================================
// 1. LOAD .ENV
// ============================================================

/**
 * Find and parse the .env file.
 * Walks up from this file's directory to find .env in public_html root.
 */
function loadEnv(): array
{
    // .env sits in public_html root (one level above includes/)
    $envPath = __DIR__ . '/../.env';

    if (!file_exists($envPath)) {
        http_response_code(500);
        exit('Environment file not found. Copy .env.example to .env and configure it.');
    }

    $env = [];
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Remove surrounding quotes
        if (strlen($value) >= 2) {
            if (($value[0] === '"' && $value[-1] === '"') ||
                ($value[0] === "'" && $value[-1] === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $env[$key] = $value;
    }

    return $env;
}

$env = loadEnv();

// ============================================================
// 2. DATABASE CONSTANTS
// ============================================================

define('DB_HOST',    $env['DB_HOST']    ?? 'localhost');
define('DB_PORT',    $env['DB_PORT']    ?? '3306');
define('DB_NAME',    $env['DB_NAME']    ?? '');
define('DB_USER',    $env['DB_USER']    ?? '');
define('DB_PASS',    $env['DB_PASS']    ?? '');
define('DB_CHARSET', $env['DB_CHARSET'] ?? 'utf8mb4');

// ============================================================
// 3. SMTP CONSTANTS
// ============================================================

define('SMTP_HOST',       $env['SMTP_HOST']       ?? '');
define('SMTP_PORT',       (int)($env['SMTP_PORT'] ?? 465));
define('SMTP_ENCRYPTION', $env['SMTP_ENCRYPTION'] ?? 'ssl');
define('SMTP_USER',       $env['SMTP_USER']       ?? '');
define('SMTP_PASS',       $env['SMTP_PASS']       ?? '');

// ============================================================
// 4. APP CONSTANTS
// ============================================================

define('APP_URL',   $env['APP_URL']   ?? 'https://think-tinker.com');
define('HUB_URL',   $env['HUB_URL']   ?? 'https://hub.think-tinker.com');
define('APP_ENV',   $env['APP_ENV']   ?? 'production');
define('APP_DEBUG', ($env['APP_DEBUG'] ?? 'false') === 'true');

// Root path (public_html directory)
define('ROOT_PATH', realpath(__DIR__ . '/..'));

// ============================================================
// 5. TIMEZONE & ERROR REPORTING
// ============================================================

date_default_timezone_set('Africa/Lagos');

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT_PATH . '/logs/php-errors.log');
}

// ============================================================
// 6. SESSION CONFIGURATION
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    // Cookie domain: .think-tinker.com (leading dot) allows session
    // sharing between think-tinker.com and hub.think-tinker.com
    $cookieDomain = '.think-tinker.com';

    // In local dev, don't set domain
    if (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'])) {
        $cookieDomain = '';
    }

    session_set_cookie_params([
        'lifetime' => 1800,         // 30 minutes
        'path'     => '/',
        'domain'   => $cookieDomain,
        'secure'   => !APP_DEBUG,   // HTTPS only in production
        'httponly'  => true,        // No JS access
        'samesite'  => 'Lax',      // CSRF protection
    ]);

    session_start();
}

// ============================================================
// 7. CSRF TOKEN
// ============================================================

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

define('CSRF_TOKEN', $_SESSION['csrf_token']);

/**
 * Validate CSRF token from POST/header.
 * Call this at the top of every state-changing API action.
 */
function validateCsrf(): void
{
    $token = $_POST['_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? '';

    if (!hash_equals(CSRF_TOKEN, $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
        exit;
    }
}

// ============================================================
// 8. AUTOLOAD VENDOR (DomPDF + PHPMailer)
// ============================================================

$autoloadPath = ROOT_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

// ============================================================
// 9. LOAD CORE INCLUDES
// ============================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
