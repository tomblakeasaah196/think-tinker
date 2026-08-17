<?php
/**
 * templates/notification-bell.php — In-app notification bell dropdown
 * Include in any authenticated header. Requires notification.js.
 */
$unreadCount = getUnreadNotificationCount($_SESSION['user_id'] ?? 0);
?>
<div class="notif-wrapper" style="position: relative;">
    <button class="notif-bell" aria-label="Notifications">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        <span class="notif-count" <?= $unreadCount > 0 ? '' : 'style="display:none;"' ?>><?= $unreadCount > 99 ? '99+' : $unreadCount ?></span>
    </button>

    <div class="notif-dropdown">
        <div class="notif-header">
            <strong>Notifications</strong>
            <a href="#" class="notif-mark-all">Mark all read</a>
        </div>
        <div class="notif-list">
            <div style="padding: 24px; text-align: center; color: #999; font-size: 0.875rem;">Loading...</div>
        </div>
    </div>
</div>

<style>
.notif-wrapper { display: inline-block; }
.notif-bell { position: relative; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-full); cursor: pointer; color: var(--charcoal); transition: background 0.2s; }
.notif-bell:hover { background: var(--cloud-gray); }
.notif-count { position: absolute; top: 2px; right: 2px; min-width: 18px; height: 18px; border-radius: 9px; background: var(--story-coral); color: #FFF; font-size: 0.5625rem; font-weight: 700; display: flex; align-items: center; justify-content: center; padding: 0 4px; }
.notif-dropdown { position: absolute; top: calc(100% + 8px); right: -8px; width: 360px; max-height: 440px; background: #FFF; border-radius: var(--radius-lg); box-shadow: 0 12px 40px rgba(0,0,0,0.18); z-index: 600; opacity: 0; visibility: hidden; transform: translateY(-8px); transition: all 0.2s; overflow: hidden; }
.notif-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0); }
.notif-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--cloud-gray); }
.notif-header strong { font-size: 0.9375rem; }
.notif-mark-all { font-size: 0.75rem; color: var(--tinker-teal); }
.notif-list { max-height: 360px; overflow-y: auto; }
.notif-item { display: flex; gap: 10px; padding: 12px 16px; cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #F8F8F8; }
.notif-item:hover { background: #FAFAFA; }
.notif-item.unread { background: rgba(26,175,160,0.04); }
.notif-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 6px; flex-shrink: 0; }
.notif-content { flex: 1; min-width: 0; }
.notif-title { font-size: 0.8125rem; font-weight: 700; color: var(--deep-navy); }
.notif-body { font-size: 0.75rem; color: #666; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.notif-time { font-size: 0.6875rem; color: #999; margin-top: 4px; }
@media (max-width: 640px) { .notif-dropdown { width: calc(100vw - 24px); right: -60px; } }
</style>
