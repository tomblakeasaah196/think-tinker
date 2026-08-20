<?php
/**
 * api/AuthController.php
 * Authentication — login, logout, register, activate, password reset.
 * Used by: Parent Portal, Teacher Portal, Hub login.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/mailer.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'login':               handleLogin(); break;
    case 'logout':              handleLogout(); break;
    case 'register':            handleRegister(); break;
    case 'register_club':       handleRegisterClub(); break;
    case 'activate':            handleActivate(); break;
    case 'forgot_password':     handleForgotPassword(); break;
    case 'reset_password':      handleResetPassword(); break;
    case 'change_password':     handleChangePassword(); break;
    case 'update_profile':      handleUpdateProfile(); break;
    case 'get_current_user':    handleGetCurrentUser(); break;
    case 'get_csrf_token':      handleGetCsrfToken(); break;
    default:
        jsonResponse(false, 'Invalid action.');
}

// ============================================================
// LOGIN
// ============================================================

function handleLogin(): void
{
    validateCsrf();
    $email    = post('email');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        jsonResponse(false, 'Email and password are required.');
    }

    $result = login($email, $password);

    if ($result['success']) {
        $user = $result['user'];

        // Determine redirect URL based on user type
        $redirect = match ($user['user_type']) {
            'super_admin', 'admin', 'staff' => HUB_URL . '/dashboard.php',
            'finance_admin'                 => HUB_URL . '/finance.php',
            'education_admin'               => HUB_URL . '/clients.php',
            'tutor'                         => HUB_URL . '/sessions.php',
            'teacher'                       => APP_URL . '/teacher/dashboard.php',
            'parent'                        => APP_URL . '/parent/index.php',
            default                         => APP_URL,
        };

        jsonResponse(true, 'Login successful.', [
            'redirect'  => $redirect,
            'user_type' => $user['user_type'],
            'name'      => $user['first_name'] . ' ' . $user['last_name'],
        ]);
    }

    jsonResponse(false, $result['message']);
}

// ============================================================
// LOGOUT
// ============================================================

function handleLogout(): void
{
    logout();
    jsonResponse(true, 'Logged out successfully.', [
        'redirect' => APP_URL,
    ]);
}

// ============================================================
// REGISTER (Full tutorial onboarding — parent)
// ============================================================

function handleRegister(): void
{
    validateCsrf();

    $firstName = post('first_name');
    $lastName  = post('last_name');
    $email     = post('email');
    $phone     = post('phone');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['password_confirm'] ?? '';

    // Validation
    if (!$firstName || !$lastName || !$email || !$password) {
        jsonResponse(false, 'All fields are required.');
    }

    if (!isValidEmail($email)) {
        jsonResponse(false, 'Please enter a valid email address.');
    }

    if ($phone && !isValidPhone($phone)) {
        jsonResponse(false, 'Please enter a valid Nigerian phone number.');
    }

    if ($password !== $confirm) {
        jsonResponse(false, 'Passwords do not match.');
    }

    $result = createUser([
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
        'phone'      => $phone,
        'password'   => $password,
        'user_type'  => 'parent',
    ]);

    if ($result['success']) {
        // Send welcome/activation email
        $user = dbFetchOne("SELECT * FROM `users` WHERE `id` = ?", [$result['user_id']]);
        sendWelcomeEmail($user, $result['activation_token']);

        // Auto-login after registration
        $_SESSION['user_id']   = $result['user_id'];
        $_SESSION['user_type'] = 'parent';
        $_SESSION['user_name'] = "$firstName $lastName";

        // Mark as verified immediately (self-registration)
        dbExecute("UPDATE `users` SET `email_verified_at` = NOW() WHERE `id` = ?", [$result['user_id']]);

        jsonResponse(true, 'Account created! Redirecting to onboarding...', [
            'user_id'  => $result['user_id'],
            'redirect' => APP_URL . '/parent/onboard.php',
        ]);
    }

    jsonResponse(false, $result['message']);
}

// ============================================================
// REGISTER CLUB (Express club-only registration)
// ============================================================

function handleRegisterClub(): void
{
    validateCsrf();

    // Parent info
    $firstName = post('first_name');
    $lastName  = post('last_name');
    $email     = post('email');
    $phone     = post('phone');
    $password  = $_POST['password'] ?? '';

    // Child info
    $childFirstName = post('child_first_name');
    $childLastName  = post('child_last_name');
    $childDob       = post('child_dob');
    $childGender    = post('child_gender');
    $medicalNotes   = post('medical_notes');
    $plan           = post('plan');

    // Validate
    if (!$firstName || !$lastName || !$email || !$password || !$childFirstName || !$childDob || !$plan) {
        jsonResponse(false, 'Please fill in all required fields.');
    }

    if (!isValidEmail($email)) {
        jsonResponse(false, 'Please enter a valid email address.');
    }

    // Create parent account
    $result = createUser([
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
        'phone'      => $phone,
        'password'   => $password,
        'user_type'  => 'parent',
    ]);

    if (!$result['success']) {
        jsonResponse(false, $result['message']);
    }

    $parentId = $result['user_id'];

    // Mark email verified (self-registration)
    dbExecute("UPDATE `users` SET `email_verified_at` = NOW() WHERE `id` = ?", [$parentId]);

    // Create child profile
    $childId = dbInsert('children', [
        'parent_id'     => $parentId,
        'first_name'    => sanitize($childFirstName),
        'last_name'     => sanitize($childLastName ?: $lastName),
        'date_of_birth' => $childDob,
        'gender'        => in_array($childGender, ['male', 'female']) ? $childGender : 'male',
        'medical_notes' => sanitize($medicalNotes),
        'status'        => 'active',
        'location_id'   => 1,
    ]);

    // Create child service record
    dbInsert('child_services', [
        'child_id'     => $childId,
        'service_type'  => 'stem_club',
        'start_date'   => date('Y-m-d'),
        'status'       => 'active',
    ]);

    // Get plan price from settings
    $planPrices = [
        'trial'     => (float) getSetting('club_plan_trial', '8000'),
        'monthly'   => (float) getSetting('club_plan_monthly', '30000'),
        'quarterly' => (float) getSetting('club_plan_quarterly', '85000'),
        'biannual'  => (float) getSetting('club_plan_biannual', '165000'),
    ];
    $planPrice = $planPrices[$plan] ?? $planPrices['trial'];

    // Provisional dates only — recalculated from the real payment date in
    // FinanceController::markInvoicePaid() once the invoice below clears.
    $startDate = date('Y-m-d');
    $endDate = clubPlanEndDate($plan, $startDate);

    // Create invoice
    $invoiceNumber = generateDocNumber('INV', 'invoices', 'invoice_number');
    $invoiceId = dbInsert('invoices', [
        'invoice_number' => $invoiceNumber,
        'parent_id'      => $parentId,
        'child_id'       => $childId,
        'invoice_type'   => 'club_membership',
        'subtotal'       => $planPrice,
        'discount'       => 0,
        'total'          => $planPrice,
        'status'         => 'sent',
        'due_date'       => date('Y-m-d', strtotime('+7 days')),
        'location_id'    => 1,
    ]);

    // Add invoice line item
    $planLabels = [
        'trial' => 'STEM & Reading Club — Trial (1 Saturday)',
        'monthly' => 'STEM & Reading Club — Monthly (4 Saturdays)',
        'quarterly' => 'STEM & Reading Club — Quarterly (12 Saturdays)',
        'biannual' => 'STEM & Reading Club — Bi-Annual (24 Saturdays)',
    ];
    dbInsert('invoice_items', [
        'invoice_id'  => $invoiceId,
        'description' => $planLabels[$plan] ?? 'STEM & Reading Club Membership',
        'quantity'     => 1,
        'unit_price'   => $planPrice,
        'total'        => $planPrice,
    ]);

    // Create club membership as pending — it only flips to 'active' (with
    // start_date reset to the actual payment date) once the invoice above
    // is confirmed paid. Previously this was inserted 'active' immediately,
    // so the membership's countdown started — and days silently ticked away
    // — before any payment had actually been received.
    dbInsert('club_memberships', [
        'child_id'   => $childId,
        'parent_id'  => $parentId,
        'plan'       => $plan,
        'plan_price' => $planPrice,
        'start_date' => $startDate,
        'end_date'   => $endDate,
        'status'     => 'pending_payment',
        'invoice_id' => $invoiceId,
        'location_id' => 1,
    ]);

    // Auto-login
    session_regenerate_id(true);
    $_SESSION['user_id']   = $parentId;
    $_SESSION['user_type'] = 'parent';
    $_SESSION['user_name'] = "$firstName $lastName";

    // Send welcome email
    $user = dbFetchOne("SELECT * FROM `users` WHERE `id` = ?", [$parentId]);
    sendWelcomeEmail($user, $result['activation_token'] ?? '');

    // Notify admin
    $admins = dbFetchAll("SELECT `id` FROM `users` WHERE `user_type` IN ('super_admin','admin') AND `is_active` = 1");
    foreach ($admins as $admin) {
        createNotification(
            $admin['id'],
            'New Club Registration',
            "$firstName $lastName registered {$childFirstName} for the {$plan} STEM & Reading Club plan.",
            HUB_URL . '/club.php'
        );
    }

    jsonResponse(true, 'Welcome to Think & Tinker! Your club registration is complete.', [
        'redirect'       => APP_URL . '/parent/payments.php',
        'invoice_number' => $invoiceNumber,
        'total'          => $planPrice,
    ]);
}

// ============================================================
// ACTIVATE ACCOUNT
// ============================================================

function handleActivate(): void
{
    $token = $_GET['token'] ?? post('token');

    if (!$token) {
        jsonResponse(false, 'Activation token is required.');
    }

    if (activateAccount($token)) {
        jsonResponse(true, 'Account activated! You can now log in.');
    }

    jsonResponse(false, 'Invalid or expired activation link.');
}

// ============================================================
// FORGOT PASSWORD
// ============================================================

function handleForgotPassword(): void
{
    validateCsrf();

    $email = post('email');
    if (!$email || !isValidEmail($email)) {
        jsonResponse(false, 'Please enter a valid email address.');
    }

    $token = createPasswordResetToken($email);

    // Always show success message (don't reveal if email exists)
    if ($token) {
        $user = dbFetchOne("SELECT `first_name`, `last_name` FROM `users` WHERE LOWER(`email`) = ?", [strtolower($email)]);
        $name = $user ? $user['first_name'] : 'User';
        sendPasswordResetEmail($email, $name, $token);
    }

    jsonResponse(true, 'If an account exists with that email, a password reset link has been sent.');
}

// ============================================================
// RESET PASSWORD
// ============================================================

function handleResetPassword(): void
{
    validateCsrf();

    $email       = post('email');
    $token       = post('token');
    $newPassword = $_POST['new_password'] ?? '';
    $confirm     = $_POST['new_password_confirm'] ?? '';

    if (!$email || !$token || !$newPassword) {
        jsonResponse(false, 'All fields are required.');
    }

    if ($newPassword !== $confirm) {
        jsonResponse(false, 'Passwords do not match.');
    }

    $result = resetPassword($email, $token, $newPassword);
    jsonResponse($result['success'], $result['message']);
}

// ============================================================
// CHANGE PASSWORD (logged-in user)
// ============================================================

function handleChangePassword(): void
{
    validateCsrf();
    $user = requireAuth();

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirm         = $_POST['new_password_confirm'] ?? '';

    if (!$currentPassword || !$newPassword) {
        jsonResponse(false, 'Current and new password are required.');
    }

    if ($newPassword !== $confirm) {
        jsonResponse(false, 'New passwords do not match.');
    }

    if (strlen($newPassword) < 8) {
        jsonResponse(false, 'New password must be at least 8 characters.');
    }

    // Verify current password
    if (!password_verify($currentPassword, $user['password_hash'])) {
        jsonResponse(false, 'Current password is incorrect.');
    }

    dbExecute(
        "UPDATE `users` SET `password_hash` = ? WHERE `id` = ?",
        [password_hash($newPassword, PASSWORD_BCRYPT), $user['id']]
    );

    jsonResponse(true, 'Password changed successfully.');
}

// ============================================================
// UPDATE PROFILE (logged-in user, any portal — self-service)
// ============================================================

function handleUpdateProfile(): void
{
    $user = requireAuth();
    validateCsrf();

    $phone = post('phone');
    if ($phone && !isValidPhone($phone)) {
        jsonResponse(false, 'Please enter a valid Nigerian phone number.');
    }

    $data = [
        'first_name' => post('first_name') ?: $user['first_name'],
        'last_name'  => post('last_name') ?: $user['last_name'],
        'phone'      => $phone ?: $user['phone'],
    ];

    // Handle photo upload
    if (!empty($_FILES['profile_photo']['name'])) {
        require_once __DIR__ . '/../includes/upload.php';
        $result = uploadProfilePhoto('profile_photo');
        if (!$result['success']) {
            jsonResponse(false, $result['message']);
        }
        // Clean up the old photo so uploads/profiles doesn't accumulate
        // an orphaned file every time someone updates their picture.
        if (!empty($user['profile_photo'])) {
            deleteUploadedFile($user['profile_photo']);
        }
        $data['profile_photo'] = $result['path'];
    }

    dbUpdate('users', $data, 'id = ?', [$user['id']]);
    $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];

    jsonResponse(true, 'Profile updated.', [
        'photo' => isset($data['profile_photo']) ? getUploadUrl($data['profile_photo']) : null,
    ]);
}

// ============================================================
// GET CURRENT USER
// ============================================================

function handleGetCurrentUser(): void
{
    $user = getCurrentUser();

    if (!$user) {
        jsonResponse(false, 'Not authenticated.', [], 401);
    }

    jsonResponse(true, '', [
        'id'         => $user['id'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'email'      => $user['email'],
        'phone'      => $user['phone'],
        'user_type'  => $user['user_type'],
        'photo'      => isset($user['profile_photo']) && $user['profile_photo'] ? getUploadUrl($user['profile_photo']) : null,
        'csrf_token' => CSRF_TOKEN,
    ]);
}

// ============================================================
// GET CSRF TOKEN (for AJAX forms)
// ============================================================

function handleGetCsrfToken(): void
{
    jsonResponse(true, '', ['csrf_token' => CSRF_TOKEN]);
}
