<?php
/**
 * includes/helpers.php
 * Utility functions used across the entire application.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'helpers.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

// ============================================================
// SETTINGS (from database)
// ============================================================

/**
 * Get a setting value from the settings table.
 * Results are cached in $_SESSION for the duration of the request cycle.
 *
 * @param string $key     The setting_key
 * @param string $default Fallback value if not found
 * @return string
 */
function getSetting(string $key, string $default = ''): string
{
    // Cache in a static variable for this request
    static $cache = [];

    if (isset($cache[$key])) {
        return $cache[$key];
    }

    $row = dbFetchOne(
        "SELECT `setting_value` FROM `settings` WHERE `setting_key` = ?",
        [$key]
    );

    $value = $row['setting_value'] ?? $default;
    $cache[$key] = $value;

    return $value;
}

/**
 * Get all settings as key => value array.
 * Optionally filtered by group.
 */
function getAllSettings(string $group = ''): array
{
    if ($group) {
        $rows = dbFetchAll(
            "SELECT `setting_key`, `setting_value` FROM `settings` WHERE `setting_group` = ? ORDER BY `sort_order`",
            [$group]
        );
    } else {
        $rows = dbFetchAll(
            "SELECT `setting_key`, `setting_value` FROM `settings` ORDER BY `setting_group`, `sort_order`"
        );
    }

    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

/**
 * Update a setting value.
 */
function updateSetting(string $key, string $value): bool
{
    $affected = dbExecute(
        "UPDATE `settings` SET `setting_value` = ? WHERE `setting_key` = ?",
        [$value, $key]
    );
    return $affected > 0;
}

/**
 * Get all settings rows for the Business Setup form (with labels, types, groups).
 */
function getSettingsForForm(): array
{
    return dbFetchAll(
        "SELECT * FROM `settings` ORDER BY `setting_group`, `sort_order`"
    );
}

// ============================================================
// BANK ACCOUNTS
// ============================================================

/**
 * Get all active bank accounts, primary first.
 * Used on checkout pages, invoices, and PDFs.
 */
function getActiveBankAccounts(): array
{
    return dbFetchAll(
        "SELECT * FROM `bank_accounts` WHERE `is_active` = 1 ORDER BY `is_primary` DESC, `bank_name` ASC"
    );
}

/**
 * Get the primary bank account.
 */
function getPrimaryBankAccount(): ?array
{
    return dbFetchOne(
        "SELECT * FROM `bank_accounts` WHERE `is_active` = 1 AND `is_primary` = 1 LIMIT 1"
    );
}

// ============================================================
// CURRENCY FORMATTING
// ============================================================

/**
 * Format a number as Nigerian Naira.
 * e.g., 20000 → "₦20,000" or 85000.50 → "₦85,000.50"
 */
function formatNaira(float|int|string $amount): string
{
    $amount = (float) $amount;
    if (floor($amount) == $amount) {
        return '₦' . number_format($amount, 0);
    }
    return '₦' . number_format($amount, 2);
}

// ============================================================
// DOCUMENT NUMBER GENERATION
// ============================================================

/**
 * Generate a unique document number.
 * Pattern: TTK-{TYPE}-{YYYYMMDD}-{XXXX}
 *
 * @param string $type    Type code: INV, RCT, CON, ORD, QUO
 * @param string $table   Table to check for uniqueness
 * @param string $column  Column name for the document number
 * @return string
 */
function generateDocNumber(string $type, string $table, string $column): string
{
    $date = date('Ymd');
    $prefix = "TTK-{$type}-{$date}-";

    // Find the highest existing number for today
    $last = dbFetchColumn(
        "SELECT `$column` FROM `$table` WHERE `$column` LIKE ? ORDER BY `$column` DESC LIMIT 1",
        [$prefix . '%']
    );

    if ($last) {
        $lastNum = (int) substr($last, -4);
        $nextNum = $lastNum + 1;
    } else {
        $nextNum = 1;
    }

    return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
}

// ============================================================
// JSON RESPONSE
// ============================================================

/**
 * Send a JSON response and exit.
 * Every API controller uses this.
 */
function jsonResponse(bool $success, string $message = '', array $data = [], int $httpCode = 200): void
{
    http_response_code($httpCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============================================================
// SANITIZATION & VALIDATION
// ============================================================

/**
 * Sanitize a string input for safe display.
 */
function sanitize(mixed $input): string
{
    if ($input === null) {
        return '';
    }
    return htmlspecialchars(trim((string) $input), ENT_QUOTES, 'UTF-8');
}

/**
 * Get a POST parameter, sanitized.
 */
function post(string $key, mixed $default = ''): string
{
    return sanitize($_POST[$key] ?? $default);
}

/**
 * Get a GET parameter, sanitized.
 */
function get(string $key, mixed $default = ''): string
{
    return sanitize($_GET[$key] ?? $default);
}

/**
 * Get a POST parameter as integer.
 */
function postInt(string $key, int $default = 0): int
{
    return (int) ($_POST[$key] ?? $default);
}

/**
 * Get a POST parameter as float.
 */
function postFloat(string $key, float $default = 0.0): float
{
    return (float) ($_POST[$key] ?? $default);
}

/**
 * Validate an email address.
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate a Nigerian phone number (basic).
 * Accepts: +234..., 0..., 234...
 */
function isValidPhone(string $phone): bool
{
    $cleaned = preg_replace('/[\s\-\(\)]/', '', $phone);
    return (bool) preg_match('/^(\+?234|0)[789]\d{9}$/', $cleaned);
}

// ============================================================
// STRING UTILITIES
// ============================================================

/**
 * Generate a URL-friendly slug from a string.
 */
function slugify(string $text): string
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Generate a random token (for activation links, password resets, etc.).
 */
function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Convert a timestamp to a human-readable "time ago" string.
 */
function timeAgo(string $datetime): string
{
    $now = new DateTime();
    $then = new DateTime($datetime);
    $diff = $now->diff($then);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 6) return floor($diff->d / 7) . ' week' . (floor($diff->d / 7) > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

/**
 * Format a date for display.
 * e.g., "2026-06-01" → "Mon, Jun 1st 2026"
 */
function formatDate(string $date, string $format = 'D, M jS Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Format a datetime for display.
 * e.g., "2026-06-01 14:30:00" → "Mon, Jun 1st 2026 · 2:30 PM"
 */
function formatDateTime(string $datetime): string
{
    return date('D, M jS Y · g:i A', strtotime($datetime));
}

// ============================================================
// REDIRECT
// ============================================================

/**
 * Redirect to a URL and exit.
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

// ============================================================
// NOTIFICATIONS (in-app)
// ============================================================

/**
 * Create an in-app notification for a user.
 */
function createNotification(int $userId, string $title, string $body = '', string $link = '', string $type = 'info'): int
{
    return dbInsert('notifications', [
        'user_id' => $userId,
        'title'   => $title,
        'body'    => $body,
        'link'    => $link,
        'type'    => $type,
    ]);
}

/**
 * Get unread notification count for a user.
 */
function getUnreadNotificationCount(int $userId): int
{
    return dbCount('notifications', 'user_id = ? AND is_read = 0', [$userId]);
}

/**
 * Get recent notifications for a user.
 */
function getRecentNotifications(int $userId, int $limit = 10): array
{
    return dbFetchAll(
        "SELECT * FROM `notifications` WHERE `user_id` = ? ORDER BY `created_at` DESC LIMIT ?",
        [$userId, $limit]
    );
}

// ============================================================
// MISCELLANEOUS
// ============================================================

/**
 * Check if the current request is an AJAX request.
 */
function isAjax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get the client's IP address.
 */
function getClientIP(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';
}

/**
 * Generate a SHA-256 hash for contract signature verification.
 */
function generateSignatureHash(string $signerName, string $timestamp, string $ip, string $contractNumber): string
{
    return hash('sha256', $signerName . '|' . $timestamp . '|' . $ip . '|' . $contractNumber);
}

/**
 * Get the WhatsApp chat link with a pre-filled message.
 */
function whatsappLink(string $message = ''): string
{
    $phone = getSetting('business_whatsapp', '2349042636948');
    $url = "https://wa.me/{$phone}";
    if ($message) {
        $url .= '?text=' . urlencode($message);
    }
    return $url;
}
