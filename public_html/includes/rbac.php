<?php
/**
 * includes/rbac.php
 * Role-Based Access Control — permission checking and module visibility.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'rbac.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

/**
 * Check if a user has permission for a module+action.
 * Does NOT exit — returns true/false.
 *
 * @param array  $user   The authenticated user array
 * @param string $module Module name (e.g., 'finance', 'clients', 'sessions')
 * @param string $action Action (e.g., 'view', 'create', 'edit', 'delete')
 * @return bool
 */
function hasPermission(array $user, string $module, string $action = 'view'): bool
{
    // Super admin bypasses all checks
    if ($user['user_type'] === 'super_admin') {
        return true;
    }

    $count = dbFetchColumn(
        "SELECT COUNT(*) FROM `user_roles` ur
         JOIN `role_permissions` rp ON ur.role_id = rp.role_id
         JOIN `permissions` p ON rp.permission_id = p.id
         WHERE ur.user_id = ? AND p.module = ? AND p.name = ?",
        [$user['id'], $module, "$module.$action"]
    );

    return (int) $count > 0;
}

/**
 * Require permission — exits with 403 if denied.
 * Use this at the top of API controller actions.
 *
 * @param array  $user   The authenticated user
 * @param string $module Module name
 * @param string $action Action (default: 'view')
 */
function requirePermission(array $user, string $module, string $action = 'view'): void
{
    if (!hasPermission($user, $module, $action)) {
        jsonResponse(false, 'Access denied. You do not have permission for this action.', [], 403);
    }
}

/**
 * Get all permissions for a user, grouped by module.
 * Used to determine which sidebar items to show in the Hub.
 *
 * @param int $userId
 * @return array e.g., ['finance' => ['finance.view', 'finance.create'], 'clients' => ['clients.view']]
 */
function getUserPermissions(int $userId): array
{
    $rows = dbFetchAll(
        "SELECT DISTINCT p.name, p.module FROM `user_roles` ur
         JOIN `role_permissions` rp ON ur.role_id = rp.role_id
         JOIN `permissions` p ON rp.permission_id = p.id
         WHERE ur.user_id = ?
         ORDER BY p.module, p.name",
        [$userId]
    );

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['module']][] = $row['name'];
    }

    return $grouped;
}

/**
 * Get the list of modules a user can access.
 * Used to render the Hub sidebar/bottom nav — hidden modules don't appear at all.
 *
 * @param array $user The authenticated user
 * @return array List of module names the user can see
 */
function getUserModules(array $user): array
{
    // Super admin sees everything
    if ($user['user_type'] === 'super_admin') {
        return [
            'dashboard', 'clients', 'sessions', 'finance', 'staff',
            'bookstore', 'club', 'blog', 'messages', 'documents', 'settings',
        ];
    }

    $permissions = getUserPermissions($user['id']);
    return array_keys($permissions);
}

/**
 * Get all roles in the system.
 */
function getAllRoles(): array
{
    return dbFetchAll("SELECT * FROM `roles` ORDER BY `id`");
}

/**
 * Get a user's assigned role IDs.
 */
function getUserRoleIds(int $userId): array
{
    $rows = dbFetchAll(
        "SELECT `role_id` FROM `user_roles` WHERE `user_id` = ?",
        [$userId]
    );
    return array_column($rows, 'role_id');
}

/**
 * Get all permissions in the system, grouped by module.
 * Used in the RBAC management UI.
 */
function getAllPermissionsGrouped(): array
{
    $rows = dbFetchAll("SELECT * FROM `permissions` ORDER BY `module`, `id`");

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[$row['module']][] = $row;
    }

    return $grouped;
}

/**
 * Get permission IDs assigned to a role.
 */
function getRolePermissionIds(int $roleId): array
{
    $rows = dbFetchAll(
        "SELECT `permission_id` FROM `role_permissions` WHERE `role_id` = ?",
        [$roleId]
    );
    return array_column($rows, 'permission_id');
}

/**
 * Update role permissions (replace all).
 */
function updateRolePermissions(int $roleId, array $permissionIds): void
{
    dbExecute("DELETE FROM `role_permissions` WHERE `role_id` = ?", [$roleId]);

    foreach ($permissionIds as $permId) {
        dbExecute(
            "INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES (?, ?)",
            [$roleId, (int) $permId]
        );
    }
}

/**
 * Update a user's role assignments (replace all).
 */
function updateUserRoles(int $userId, array $roleIds): void
{
    dbExecute("DELETE FROM `user_roles` WHERE `user_id` = ?", [$userId]);

    foreach ($roleIds as $roleId) {
        dbExecute(
            "INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES (?, ?)",
            [$userId, (int) $roleId]
        );
    }
}
