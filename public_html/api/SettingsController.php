<?php
/**
 * api/SettingsController.php
 * Business Setup, RBAC management, bank accounts, user roles.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    // === BUSINESS SETTINGS ===
    case 'get_settings':         getSettings(); break;
    case 'get_settings_form':    getSettingsForm(); break;
    case 'update_settings':      updateSettings(); break;

    // === BANK ACCOUNTS ===
    case 'get_bank_accounts':    getBankAccounts(); break;
    case 'add_bank_account':     addBankAccount(); break;
    case 'update_bank_account':  updateBankAccount(); break;
    case 'toggle_bank_account':  toggleBankAccount(); break;
    case 'set_primary_bank':     setPrimaryBank(); break;
    case 'delete_bank_account':  deleteBankAccount(); break;

    // === RBAC: ROLES ===
    case 'get_roles':            getRoles(); break;
    case 'get_role_permissions': getRolePerms(); break;
    case 'update_role_permissions': updateRolePerms(); break;

    // === RBAC: USER ASSIGNMENTS ===
    case 'get_user_roles':       getUserRolesAction(); break;
    case 'update_user_roles':    updateUserRolesAction(); break;
    case 'get_user_permissions': getUserPermsAction(); break;
    case 'get_user_modules':     getUserModulesAction(); break;

    // === ALL PERMISSIONS (for RBAC UI) ===
    case 'get_all_permissions':  getAllPerms(); break;

    default:
        jsonResponse(false, 'Invalid action.');
}

// ============================================================
// BUSINESS SETTINGS
// ============================================================

function getSettings(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'view');

    $group = get('group');
    $settings = getAllSettings($group);

    jsonResponse(true, '', ['settings' => $settings]);
}

function getSettingsForm(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'view');

    $rows = getSettingsForForm();

    // Group by setting_group for tab rendering
    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['setting_group']][] = $row;
    }

    jsonResponse(true, '', ['groups' => $grouped]);
}

function updateSettings(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'edit');
    validateCsrf();

    $settings = $_POST['settings'] ?? [];

    if (!is_array($settings) || empty($settings)) {
        jsonResponse(false, 'No settings provided.');
    }

    $updated = 0;
    foreach ($settings as $key => $value) {
        $key = sanitize($key);
        $value = sanitize($value);
        if (updateSetting($key, $value)) {
            $updated++;
        }
    }

    jsonResponse(true, "{$updated} setting(s) updated successfully.");
}

// ============================================================
// BANK ACCOUNTS
// ============================================================

function getBankAccounts(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'view');

    $accounts = dbFetchAll("SELECT * FROM `bank_accounts` ORDER BY `is_primary` DESC, `is_active` DESC, `bank_name` ASC");

    jsonResponse(true, '', ['accounts' => $accounts]);
}

function addBankAccount(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'edit');
    validateCsrf();

    $bankName      = post('bank_name');
    $accountName   = post('account_name');
    $accountNumber = post('account_number');
    $accountType   = post('account_type');
    $isPrimary     = postInt('is_primary');
    $notes         = post('notes');

    if (!$bankName || !$accountName || !$accountNumber) {
        jsonResponse(false, 'Bank name, account name, and account number are required.');
    }

    // If setting as primary, unset all others
    if ($isPrimary) {
        dbExecute("UPDATE `bank_accounts` SET `is_primary` = 0");
    }

    $id = dbInsert('bank_accounts', [
        'bank_name'      => $bankName,
        'account_name'   => $accountName,
        'account_number' => $accountNumber,
        'account_type'   => in_array($accountType, ['savings', 'current']) ? $accountType : 'current',
        'is_primary'     => $isPrimary ? 1 : 0,
        'is_active'      => 1,
        'notes'          => $notes,
    ]);

    jsonResponse(true, 'Bank account added.', ['id' => $id]);
}

function updateBankAccount(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'edit');
    validateCsrf();

    $id            = postInt('id');
    $bankName      = post('bank_name');
    $accountName   = post('account_name');
    $accountNumber = post('account_number');
    $accountType   = post('account_type');
    $notes         = post('notes');

    if (!$id || !$bankName || !$accountName || !$accountNumber) {
        jsonResponse(false, 'All fields are required.');
    }

    dbUpdate('bank_accounts', [
        'bank_name'      => $bankName,
        'account_name'   => $accountName,
        'account_number' => $accountNumber,
        'account_type'   => in_array($accountType, ['savings', 'current']) ? $accountType : 'current',
        'notes'          => $notes,
    ], 'id = ?', [$id]);

    jsonResponse(true, 'Bank account updated.');
}

function toggleBankAccount(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'edit');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Account ID required.');

    $account = dbFetchOne("SELECT * FROM `bank_accounts` WHERE `id` = ?", [$id]);
    if (!$account) jsonResponse(false, 'Account not found.');

    $newStatus = $account['is_active'] ? 0 : 1;
    dbUpdate('bank_accounts', ['is_active' => $newStatus], 'id = ?', [$id]);

    $label = $newStatus ? 'activated' : 'deactivated';
    jsonResponse(true, "Bank account {$label}.");
}

function setPrimaryBank(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'edit');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Account ID required.');

    // Unset all, then set this one
    dbExecute("UPDATE `bank_accounts` SET `is_primary` = 0");
    dbUpdate('bank_accounts', ['is_primary' => 1, 'is_active' => 1], 'id = ?', [$id]);

    jsonResponse(true, 'Primary bank account updated.');
}

function deleteBankAccount(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'edit');
    validateCsrf();

    $id = postInt('id');
    if (!$id) jsonResponse(false, 'Account ID required.');

    // Don't delete the last account
    $count = dbCount('bank_accounts');
    if ($count <= 1) {
        jsonResponse(false, 'Cannot delete the last bank account. Add another one first.');
    }

    dbExecute("DELETE FROM `bank_accounts` WHERE `id` = ?", [$id]);
    jsonResponse(true, 'Bank account deleted.');
}

// ============================================================
// RBAC: ROLES
// ============================================================

function getRoles(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'manage_roles');

    $roles = getAllRoles();
    jsonResponse(true, '', ['roles' => $roles]);
}

function getRolePerms(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'manage_roles');

    $roleId = (int) ($_GET['role_id'] ?? 0);
    if (!$roleId) jsonResponse(false, 'Role ID required.');

    $permissionIds = getRolePermissionIds($roleId);
    jsonResponse(true, '', ['permission_ids' => $permissionIds]);
}

function updateRolePerms(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'manage_roles');
    validateCsrf();

    $roleId = postInt('role_id');
    $permissionIds = $_POST['permission_ids'] ?? [];

    if (!$roleId) jsonResponse(false, 'Role ID required.');

    // Prevent modifying super_admin role
    $role = dbFetchOne("SELECT * FROM `roles` WHERE `id` = ?", [$roleId]);
    if ($role && $role['name'] === 'super_admin') {
        jsonResponse(false, 'Cannot modify super admin permissions.');
    }

    updateRolePermissions($roleId, array_map('intval', $permissionIds));
    jsonResponse(true, 'Role permissions updated.');
}

// ============================================================
// RBAC: USER ROLE ASSIGNMENTS
// ============================================================

function getUserRolesAction(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'manage_roles');

    $userId = (int) ($_GET['user_id'] ?? 0);
    if (!$userId) jsonResponse(false, 'User ID required.');

    $roleIds = getUserRoleIds($userId);
    jsonResponse(true, '', ['role_ids' => $roleIds]);
}

function updateUserRolesAction(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'manage_roles');
    validateCsrf();

    $userId  = postInt('user_id');
    $roleIds = $_POST['role_ids'] ?? [];

    if (!$userId) jsonResponse(false, 'User ID required.');

    updateUserRoles($userId, array_map('intval', $roleIds));
    jsonResponse(true, 'User roles updated.');
}

function getUserPermsAction(): void
{
    $user = requireAuth();

    // Users can check their own permissions, admin can check anyone's
    $userId = (int) ($_GET['user_id'] ?? $user['id']);
    if ($userId !== $user['id']) {
        requirePermission($user, 'settings', 'view');
    }

    $permissions = getUserPermissions($userId);
    jsonResponse(true, '', ['permissions' => $permissions]);
}

function getUserModulesAction(): void
{
    $user = requireAuth();

    $modules = getUserModules($user);
    jsonResponse(true, '', [
        'modules'    => $modules,
        'csrf_token' => CSRF_TOKEN,
    ]);
}

// ============================================================
// ALL PERMISSIONS (for RBAC management UI)
// ============================================================

function getAllPerms(): void
{
    $user = requireAuth();
    requirePermission($user, 'settings', 'manage_roles');

    $grouped = getAllPermissionsGrouped();
    jsonResponse(true, '', ['permissions' => $grouped]);
}
