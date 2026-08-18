<?php
/**
 * includes/auth.php
 * Authentication — login, logout, session management, rate limiting.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'auth.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

// ============================================================
// SESSION / CURRENT USER
// ============================================================

/**
 * Get the currently logged-in user, or null if not authenticated.
 * Also checks if the user account is still active (admin can "kill" sessions).
 */
function getCurrentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    // Re-check from database to enforce instant session kill
    $user = dbFetchOne(
        "SELECT * FROM `users` WHERE `id` = ? AND `deleted_at` IS NULL",
        [$_SESSION['user_id']]
    );

    if (!$user || !$user['is_active']) {
        // Account deactivated or deleted — destroy session
        logout();
        return null;
    }

    return $user;
}

/**
 * Check if a user is currently logged in.
 */
function isLoggedIn(): bool
{
    return getCurrentUser() !== null;
}

/**
 * Require authentication. Returns the user array.
 * If not authenticated:
 *   - AJAX requests get a 401 JSON response
 *   - Page requests get redirected to login
 *
 * @param string $loginUrl URL to redirect to (defaults based on context)
 * @return array The authenticated user
 */
function requireAuth(string $loginUrl = ''): array
{
    $user = getCurrentUser();

    if (!$user) {
        if (isAjax()) {
            jsonResponse(false, 'Authentication required. Please log in.', [], 401);
        }

        // Determine login URL based on current path
        if (!$loginUrl) {
            $path = $_SERVER['REQUEST_URI'] ?? '';
            if (str_contains($path, '/hub')) {
                $loginUrl = HUB_URL;
            } elseif (str_contains($path, '/teacher')) {
                $loginUrl = APP_URL . '/teacher/login.php';
            } else {
                $loginUrl = APP_URL . '/parent/login.php';
            }
        }

        redirect($loginUrl);
    }

    return $user;
}

/**
 * Require a specific user type. Must be called after requireAuth().
 *
 * @param array        $user  The authenticated user
 * @param string|array $types Allowed user type(s)
 */
function requireUserType(array $user, string|array $types): void
{
    $allowed = is_array($types) ? $types : [$types];

    if (!in_array($user['user_type'], $allowed)) {
        if (isAjax()) {
            jsonResponse(false, 'Access denied. Insufficient privileges.', [], 403);
        }
        http_response_code(403);
        exit('Access denied.');
    }
}

// ============================================================
// LOGIN
// ============================================================

/**
 * Attempt to log in a user.
 *
 * @param string $email    Email address
 * @param string $password Plain-text password
 * @return array           ['success' => bool, 'message' => string, 'user' => array|null]
 */
function login(string $email, string $password): array
{
    $email = strtolower(trim($email));
    $ip = getClientIP();

    // Check rate limiting
    if (isLoginLocked($ip, $email)) {
        return [
            'success' => false,
            'message' => 'Too many login attempts. Please wait 15 minutes before trying again.',
            'user'    => null,
        ];
    }

    // Find user
    $user = dbFetchOne(
        "SELECT * FROM `users` WHERE LOWER(`email`) = ? AND `deleted_at` IS NULL",
        [$email]
    );

    if (!$user) {
        recordLoginAttempt($ip, $email);
        return [
            'success' => false,
            'message' => 'Invalid email or password.',
            'user'    => null,
        ];
    }

    // Check if account is active
    if (!$user['is_active']) {
        return [
            'success' => false,
            'message' => 'Your account has been deactivated. Please contact administration.',
            'user'    => null,
        ];
    }

    // Check if email is verified (skip for super_admin)
    if ($user['user_type'] !== 'super_admin' && empty($user['email_verified_at'])) {
        return [
            'success' => false,
            'message' => 'Please verify your email address. Check your inbox for the activation link.',
            'user'    => null,
        ];
    }

    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        recordLoginAttempt($ip, $email);
        return [
            'success' => false,
            'message' => 'Invalid email or password.',
            'user'    => null,
        ];
    }

    // Success — create session
    clearLoginAttempts($ip, $email);

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_type']  = $user['user_type'];
    $_SESSION['user_name']  = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['logged_in_at'] = time();

    // Update last login timestamp
    dbExecute("UPDATE `users` SET `last_login_at` = NOW() WHERE `id` = ?", [$user['id']]);

    return [
        'success' => true,
        'message' => 'Login successful.',
        'user'    => $user,
    ];
}

// ============================================================
// LOGOUT
// ============================================================

/**
 * Destroy the current session and log out.
 */
function logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

// ============================================================
// REGISTRATION
// ============================================================

/**
 * Create a new user account.
 *
 * @param array $data User data (first_name, last_name, email, phone, password, user_type)
 * @return array      ['success' => bool, 'message' => string, 'user_id' => int|null]
 */
function createUser(array $data): array
{
    $email = strtolower(trim($data['email']));

    // Check if email already exists
    $exists = dbFetchOne(
        "SELECT `id` FROM `users` WHERE LOWER(`email`) = ?",
        [$email]
    );

    if ($exists) {
        return [
            'success' => false,
            'message' => 'An account with this email already exists.',
            'user_id' => null,
        ];
    }

    // Validate password strength
    $password = $data['password'] ?? '';
    if (strlen($password) < 8) {
        return [
            'success' => false,
            'message' => 'Password must be at least 8 characters.',
            'user_id' => null,
        ];
    }

    $activationToken = generateToken();
    $userType = $data['user_type'] ?? 'parent';

    $userId = dbInsert('users', [
        'first_name'       => sanitize($data['first_name']),
        'last_name'        => sanitize($data['last_name']),
        'email'            => $email,
        'phone'            => sanitize($data['phone'] ?? ''),
        'password_hash'    => password_hash($password, PASSWORD_BCRYPT),
        'user_type'        => $userType,
        'is_active'        => 1,
        'activation_token' => $activationToken,
        'location_id'      => 1,
    ]);

    // Assign default role
    $roleMap = [
        'parent'      => 5,
        'teacher'     => 6,
        'tutor'       => 4,
        'staff'       => 3,
        'admin'       => 3,
        'super_admin' => 1,
    ];

    $roleId = $roleMap[$userType] ?? 5;
    dbExecute(
        "INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES (?, ?)",
        [$userId, $roleId]
    );

    return [
        'success'          => true,
        'message'          => 'Account created successfully.',
        'user_id'          => $userId,
        'activation_token' => $activationToken,
    ];
}

/**
 * Activate a user account via activation token.
 */
function activateAccount(string $token): bool
{
    $affected = dbExecute(
        "UPDATE `users` SET `email_verified_at` = NOW(), `activation_token` = NULL 
         WHERE `activation_token` = ? AND `email_verified_at` IS NULL",
        [$token]
    );
    return $affected > 0;
}

// ============================================================
// PASSWORD RESET
// ============================================================

/**
 * Generate a password reset token and store it.
 */
function createPasswordResetToken(string $email): ?string
{
    $user = dbFetchOne(
        "SELECT `id` FROM `users` WHERE LOWER(`email`) = ? AND `deleted_at` IS NULL",
        [strtolower(trim($email))]
    );

    if (!$user) {
        return null; // Don't reveal if email exists
    }

    $token = generateToken();
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    dbExecute(
        "UPDATE `users` SET `reset_token` = ?, `reset_token_expires` = ? WHERE `id` = ?",
        [password_hash($token, PASSWORD_BCRYPT), $expires, $user['id']]
    );

    return $token;
}

/**
 * Reset password using a valid token.
 */
function resetPassword(string $email, string $token, string $newPassword): array
{
    $user = dbFetchOne(
        "SELECT `id`, `reset_token`, `reset_token_expires` FROM `users` 
         WHERE LOWER(`email`) = ? AND `deleted_at` IS NULL",
        [strtolower(trim($email))]
    );

    if (!$user || !$user['reset_token'] || !password_verify($token, $user['reset_token'])) {
        return ['success' => false, 'message' => 'Invalid or expired reset link.'];
    }

    if (strtotime($user['reset_token_expires']) < time()) {
        return ['success' => false, 'message' => 'Reset link has expired. Please request a new one.'];
    }

    if (strlen($newPassword) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
    }

    dbExecute(
        "UPDATE `users` SET `password_hash` = ?, `reset_token` = NULL, `reset_token_expires` = NULL WHERE `id` = ?",
        [password_hash($newPassword, PASSWORD_BCRYPT), $user['id']]
    );

    return ['success' => true, 'message' => 'Password has been reset. You can now log in.'];
}

// ============================================================
// LOGIN RATE LIMITING
// ============================================================

/**
 * Check if login is locked for this IP/email combination.
 * Locked after 5 failed attempts within 15 minutes.
 */
function isLoginLocked(string $ip, string $email): bool
{
    $cutoff = date('Y-m-d H:i:s', strtotime('-15 minutes'));

    $count = dbCount(
        'login_attempts',
        '(ip_address = ? OR email = ?) AND attempted_at > ?',
        [$ip, $email, $cutoff]
    );

    return $count >= 5;
}

/**
 * Record a failed login attempt.
 */
function recordLoginAttempt(string $ip, string $email): void
{
    dbInsert('login_attempts', [
        'ip_address' => $ip,
        'email'      => $email,
    ]);
}

/**
 * Clear login attempts after successful login.
 */
function clearLoginAttempts(string $ip, string $email): void
{
    dbExecute(
        "DELETE FROM `login_attempts` WHERE `ip_address` = ? OR `email` = ?",
        [$ip, $email]
    );
}
