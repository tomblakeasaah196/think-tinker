/**
 * assets/js/notification.js — Notification bell polling + dropdown
 * Polls for unread count, renders notification dropdown.
 */
'use strict';

TT.notifications = {
    pollTimer: null,
    lastCount: 0,

    /**
     * Start polling for notifications.
     * Call this on any authenticated page.
     */
    init: function() {
        this.fetch();
        this.pollTimer = setInterval(() => this.fetchCount(), TT.config.pollInterval);

        // Toggle dropdown
        $(document).on('click', '.notif-bell', function(e) {
            e.stopPropagation();
            const $dropdown = $(this).siblings('.notif-dropdown');
            const wasOpen = $dropdown.hasClass('open');
            $('.notif-dropdown').removeClass('open');
            if (!wasOpen) {
                $dropdown.addClass('open');
                TT.notifications.fetch();
            }
        });

        // Close on outside click
        $(document).on('click', function() {
            $('.notif-dropdown').removeClass('open');
        });
        $('.notif-dropdown').on('click', function(e) { e.stopPropagation(); });

        // Mark single as read
        $(document).on('click', '.notif-item[data-id]', function() {
            const id = $(this).data('id');
            const link = $(this).attr('data-link');
            TT.api('NotificationController.php', { action: 'mark_read', id: id }, { silent: true }).done(function() {
                if (link && link !== 'null' && link !== 'undefined') window.location.href = link;
            });
            $(this).removeClass('unread');
        });

        // Mark all read
        $(document).on('click', '.notif-mark-all', function(e) {
            e.preventDefault();
            TT.api('NotificationController.php', { action: 'mark_all_read' }, { silent: true })
                .done(function() {
                    $('.notif-item').removeClass('unread');
                    TT.notifications.updateBadge(0);
                });
        });
    },

    /**
     * Fetch full notification list and render dropdown.
     */
    fetch: function() {
        TT.get('NotificationController.php', { action: 'get_notifications', limit: 10 })
            .done(function(r) {
                if (!r.success) return;
                TT.notifications.render(r.data.notifications);
                TT.notifications.updateBadge(r.data.unread);
            });
    },

    /**
     * Lightweight count-only poll.
     */
    fetchCount: function() {
        TT.get('NotificationController.php', { action: 'get_unread_count' })
            .done(function(r) {
                if (r.success) {
                    TT.notifications.updateBadge(r.data.count);
                }
            });
    },

    /**
     * Render notification items in dropdown.
     */
    render: function(notifications) {
        const $list = $('.notif-list');
        if (!$list.length) return;

        if (!notifications || notifications.length === 0) {
            $list.html(`
                <div style="padding: 24px; text-align: center; color: #999; font-size: 0.875rem;">
                    No notifications yet
                </div>
            `);
            return;
        }

        let html = '';
        notifications.forEach(function(n) {
            const unread = n.is_read == 0 ? 'unread' : '';
            const typeColors = { info: 'var(--sky-blue)', success: 'var(--leaf-green)', warning: 'var(--spark-orange)', error: 'var(--story-coral)' };
            const dotColor = typeColors[n.type] || typeColors.info;

            html += `
                <div class="notif-item ${unread}" data-id="${n.id}" data-link="${TT.escHtml(n.link || '')}">
                    <span class="notif-dot" style="background: ${dotColor};"></span>
                    <div class="notif-content">
                        <div class="notif-title">${TT.escHtml(n.title)}</div>
                        ${n.body ? `<div class="notif-body">${TT.escHtml(n.body)}</div>` : ''}
                        <div class="notif-time">${n.time_ago || TT.timeAgo(n.created_at)}</div>
                    </div>
                </div>
            `;
        });

        $list.html(html);
    },

    /**
     * Update the badge count on the bell icon.
     */
    updateBadge: function(count) {
        count = parseInt(count) || 0;
        const $badge = $('.notif-count');

        if (count > 0) {
            $badge.text(count > 99 ? '99+' : count).show();
        } else {
            $badge.hide();
        }

        // Also update bottom nav badge if present
        const $navBadge = $('.bottom-nav .nav-badge[data-type="notif"]');
        if (count > 0) {
            if (!$navBadge.length) {
                $('.bottom-nav .nav-item[data-tab="notifications"]').append(
                    `<span class="nav-badge" data-type="notif">${count}</span>`
                );
            } else {
                $navBadge.text(count);
            }
        } else {
            $navBadge.remove();
        }

        this.lastCount = count;
    },

    /**
     * Stop polling (call on page unload if needed).
     */
    destroy: function() {
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
    }
};
