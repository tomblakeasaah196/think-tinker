<?php
/**
 * api/DashboardController.php
 * Hub dashboard — metrics, activity feed, quick stats.
 * Returns different data based on user role.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_dashboard':       getDashboard(); break;
    case 'get_activity_feed':   getActivityFeed(); break;
    case 'get_quick_stats':     getQuickStats(); break;
    default:
        jsonResponse(false, 'Invalid action.');
}

// ============================================================
// MAIN DASHBOARD
// ============================================================

function getDashboard(): void
{
    $user = requireAuth();
    $modules = getUserModules($user);
    $data = ['modules' => $modules, 'user_name' => $user['first_name']];

    $now = date('Y-m-d');
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    // === FINANCIAL METRICS (visible to super_admin, finance_admin) ===
    if (hasPermission($user, 'dashboard', 'view_finance') || $user['user_type'] === 'super_admin') {
        // Revenue this month (from paid invoices)
        $revenue = dbFetchColumn(
            "SELECT COALESCE(SUM(`total`), 0) FROM `invoices` WHERE `status` = 'paid' AND `paid_at` BETWEEN ? AND ?",
            [$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59']
        );

        // Expenses this month
        $expenses = dbFetchColumn(
            "SELECT COALESCE(SUM(`amount`), 0) FROM `expenses` WHERE `expense_date` BETWEEN ? AND ?",
            [$monthStart, $monthEnd]
        );

        // Pending invoices
        $pendingInvoices = dbFetchOne(
            "SELECT COUNT(*) as count, COALESCE(SUM(`total`), 0) as total FROM `invoices` WHERE `status` IN ('sent', 'overdue')"
        );

        // Pending orders
        $pendingOrders = dbCount('orders', "status IN ('pending_payment', 'awaiting_confirmation', 'processing')");

        $data['finance'] = [
            'revenue'          => (float) $revenue,
            'expenses'         => (float) $expenses,
            'net_margin'       => (float) $revenue - (float) $expenses,
            'pending_invoices' => [
                'count' => (int) $pendingInvoices['count'],
                'total' => (float) $pendingInvoices['total'],
            ],
            'pending_orders'   => $pendingOrders,
        ];

        // Revenue vs Expenses last 6 months (for chart)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mStart = date('Y-m-01', strtotime("-{$i} months"));
            $mEnd = date('Y-m-t', strtotime("-{$i} months"));
            $mLabel = date('M Y', strtotime("-{$i} months"));

            $mRevenue = dbFetchColumn(
                "SELECT COALESCE(SUM(`total`), 0) FROM `invoices` WHERE `status` = 'paid' AND `paid_at` BETWEEN ? AND ?",
                [$mStart . ' 00:00:00', $mEnd . ' 23:59:59']
            );
            $mExpenses = dbFetchColumn(
                "SELECT COALESCE(SUM(`amount`), 0) FROM `expenses` WHERE `expense_date` BETWEEN ? AND ?",
                [$mStart, $mEnd]
            );

            $chartData[] = [
                'month'    => $mLabel,
                'revenue'  => (float) $mRevenue,
                'expenses' => (float) $mExpenses,
            ];
        }
        $data['finance']['chart'] = $chartData;
    }

    // === CLIENT METRICS (visible to super_admin, education_admin) ===
    if (hasPermission($user, 'dashboard', 'view_clients') || $user['user_type'] === 'super_admin') {
        $data['clients'] = [
            'active_clients'   => dbCount('users', "user_type = 'parent' AND is_active = 1 AND deleted_at IS NULL"),
            'active_children'  => dbCount('children', "status = 'active' AND deleted_at IS NULL"),
            'upcoming_sessions' => dbCount('sessions', "session_date BETWEEN ? AND ? AND status = 'scheduled'",
                [$now, date('Y-m-d', strtotime('+7 days'))]),
            'new_inquiries'    => dbCount('contact_inquiries', "status = 'new'"),
            'active_club_members' => dbCount('club_memberships', "status = 'active' AND end_date >= ?", [$now]),
        ];
    }

    // === TUTOR PERSONAL METRICS ===
    if (hasPermission($user, 'dashboard', 'view_personal')) {
        $data['personal'] = [
            'upcoming_sessions' => dbFetchAll(
                "SELECT s.*, c.first_name as child_first_name, c.last_name as child_last_name 
                 FROM `sessions` s 
                 JOIN `children` c ON s.child_id = c.id 
                 WHERE s.tutor_id = ? AND s.session_date >= ? AND s.status = 'scheduled' 
                 ORDER BY s.session_date, s.start_time LIMIT 10",
                [$user['id'], $now]
            ),
            'pending_notes' => dbCount('sessions',
                "tutor_id = ? AND status = 'completed' AND id NOT IN (SELECT session_id FROM session_notes WHERE status = 'submitted')",
                [$user['id']]
            ),
            'assigned_students' => dbCount('child_services', "assigned_tutor_id = ? AND status = 'active'", [$user['id']]),
        ];
    }

    // === UNREAD MESSAGES ===
    if (hasPermission($user, 'messages', 'view') || hasPermission($user, 'messages', 'respond_assigned')) {
        if ($user['user_type'] === 'tutor') {
            $data['unread_messages'] = dbCount('messages', "assigned_to = ? AND is_read = 0", [$user['id']]);
        } else {
            $data['unread_messages'] = dbCount('messages', "sender_type = 'parent' AND is_read = 0");
        }
    }

    // Notification count
    $data['unread_notifications'] = getUnreadNotificationCount($user['id']);

    jsonResponse(true, '', $data);
}

// ============================================================
// ACTIVITY FEED
// ============================================================

function getActivityFeed(): void
{
    $user = requireAuth();
    $limit = min((int) ($_GET['limit'] ?? 20), 50);

    $activities = [];

    // Recent paid invoices
    if (hasPermission($user, 'finance', 'view') || $user['user_type'] === 'super_admin') {
        $recentPayments = dbFetchAll(
            "SELECT i.invoice_number, i.total, i.paid_at, u.first_name, u.last_name
             FROM `invoices` i JOIN `users` u ON i.parent_id = u.id
             WHERE i.status = 'paid' AND i.paid_at IS NOT NULL
             ORDER BY i.paid_at DESC LIMIT 5"
        );
        foreach ($recentPayments as $p) {
            $activities[] = [
                'type'    => 'payment',
                'icon'    => 'success',
                'text'    => "{$p['first_name']} {$p['last_name']} paid " . formatNaira($p['total']) . " ({$p['invoice_number']})",
                'time'    => $p['paid_at'],
                'link'    => HUB_URL . '/finance.php',
            ];
        }
    }

    // Recent session completions
    if (hasPermission($user, 'sessions', 'view') || $user['user_type'] === 'super_admin') {
        $recentSessions = dbFetchAll(
            "SELECT s.session_date, c.first_name as child_name, u.first_name as tutor_name, s.updated_at
             FROM `sessions` s
             JOIN `children` c ON s.child_id = c.id
             JOIN `users` u ON s.tutor_id = u.id
             WHERE s.status = 'completed'
             ORDER BY s.updated_at DESC LIMIT 5"
        );
        foreach ($recentSessions as $s) {
            $activities[] = [
                'type' => 'session',
                'icon' => 'info',
                'text' => "Session completed: {$s['child_name']} with {$s['tutor_name']}",
                'time' => $s['updated_at'],
                'link' => HUB_URL . '/sessions.php',
            ];
        }
    }

    // Recent inquiries
    if (hasPermission($user, 'clients', 'view') || $user['user_type'] === 'super_admin') {
        $recentInquiries = dbFetchAll(
            "SELECT name, inquiry_type, created_at FROM `contact_inquiries`
             WHERE status = 'new' ORDER BY created_at DESC LIMIT 5"
        );
        foreach ($recentInquiries as $q) {
            $typeLabel = str_replace('_', ' ', $q['inquiry_type']);
            $activities[] = [
                'type' => 'inquiry',
                'icon' => 'warning',
                'text' => "New {$typeLabel} inquiry from {$q['name']}",
                'time' => $q['created_at'],
                'link' => HUB_URL . '/clients.php',
            ];
        }
    }

    // Recent orders
    if (hasPermission($user, 'bookstore', 'view') || $user['user_type'] === 'super_admin') {
        $recentOrders = dbFetchAll(
            "SELECT o.order_number, o.total, o.status, o.created_at, u.first_name
             FROM `orders` o JOIN `users` u ON o.parent_id = u.id
             ORDER BY o.created_at DESC LIMIT 5"
        );
        foreach ($recentOrders as $o) {
            $activities[] = [
                'type' => 'order',
                'icon' => 'info',
                'text' => "Order {$o['order_number']} ({$o['status']}) from {$o['first_name']} — " . formatNaira($o['total']),
                'time' => $o['created_at'],
                'link' => HUB_URL . '/bookstore.php',
            ];
        }
    }

    // Sort all activities by time
    usort($activities, fn($a, $b) => strtotime($b['time']) - strtotime($a['time']));
    $activities = array_slice($activities, 0, $limit);

    // Add human-readable time
    foreach ($activities as &$a) {
        $a['time_ago'] = timeAgo($a['time']);
    }

    jsonResponse(true, '', ['activities' => $activities]);
}

// ============================================================
// QUICK STATS (lightweight, for polling)
// ============================================================

function getQuickStats(): void
{
    $user = requireAuth();

    $stats = [
        'unread_notifications' => getUnreadNotificationCount($user['id']),
    ];

    if (hasPermission($user, 'messages', 'view') || $user['user_type'] === 'super_admin') {
        $stats['unread_messages'] = dbCount('messages', "sender_type = 'parent' AND is_read = 0");
    }

    if (hasPermission($user, 'clients', 'view') || $user['user_type'] === 'super_admin') {
        $stats['new_inquiries'] = dbCount('contact_inquiries', "status = 'new'");
    }

    jsonResponse(true, '', $stats);
}
