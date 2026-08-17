/**
 * hub/assets/js/hub.js — Operations Hub JavaScript
 * Sidebar toggle, dashboard loading, table management, chart helpers.
 */
'use strict';

const Hub = window.Hub || {};

// ============================================================
// SIDEBAR TOGGLE
// ============================================================
Hub.initSidebar = function() {
    const $toggle = $('#sidebarToggle');
    const $sidebar = $('#hubSidebar');

    $toggle.on('click', function() {
        $sidebar.toggleClass('open');
        // Add/remove overlay
        if ($sidebar.hasClass('open')) {
            if (!$('.hub-sidebar-overlay').length) {
                $('<div class="hub-sidebar-overlay"></div>').insertAfter($sidebar);
            }
            $('.hub-sidebar-overlay').on('click', function() {
                $sidebar.removeClass('open');
                $(this).remove();
            });
        } else {
            $('.hub-sidebar-overlay').remove();
        }
    });
};

// ============================================================
// DASHBOARD
// ============================================================
Hub.loadDashboard = function() {
    TT.get('DashboardController.php', { action: 'get_dashboard' })
        .done(function(r) {
            if (!r.success) return;
            const d = r.data;

            // Render stat cards if finance data present
            if (d.finance) {
                Hub.renderFinanceStats(d.finance);
            }

            // Client stats
            if (d.clients) {
                Hub.renderClientStats(d.clients);
            }

            // Personal (tutor) stats
            if (d.personal) {
                Hub.renderPersonalStats(d.personal);
            }

            // Load activity feed
            Hub.loadActivityFeed();
        });
};

Hub.renderFinanceStats = function(f) {
    const $grid = $('#statsGrid');
    if (!$grid.length) return;

    $grid.html(`
        <div class="stat-card">
            <div class="stat-label">Revenue (This Month)</div>
            <div class="stat-value">${TT.formatNaira(f.revenue)}</div>
            <div class="stat-hint">From paid invoices</div>
        </div>
        <div class="stat-card stat-red">
            <div class="stat-label">Expenses</div>
            <div class="stat-value">${TT.formatNaira(f.expenses)}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Net Margin</div>
            <div class="stat-value">${TT.formatNaira(f.net_margin)}</div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-label">Pending Invoices</div>
            <div class="stat-value">${f.pending_invoices.count}</div>
            <div class="stat-hint">${TT.formatNaira(f.pending_invoices.total)} outstanding</div>
        </div>
    `);
};

Hub.renderClientStats = function(c) {
    const $grid = $('#clientStatsGrid');
    if (!$grid.length) return;

    $grid.html(`
        <div class="stat-card">
            <div class="stat-label">Active Clients</div>
            <div class="stat-value">${c.active_clients}</div>
        </div>
        <div class="stat-card stat-orange">
            <div class="stat-label">Active Children</div>
            <div class="stat-value">${c.active_children}</div>
        </div>
        <div class="stat-card stat-green">
            <div class="stat-label">Upcoming Sessions</div>
            <div class="stat-value">${c.upcoming_sessions}</div>
            <div class="stat-hint">Next 7 days</div>
        </div>
        <div class="stat-card stat-navy">
            <div class="stat-label">Club Members</div>
            <div class="stat-value">${c.active_club_members}</div>
        </div>
    `);
};

Hub.renderPersonalStats = function(p) {
    const $el = $('#personalStats');
    if (!$el.length) return;

    let sessionHtml = '';
    if (p.upcoming_sessions && p.upcoming_sessions.length) {
        p.upcoming_sessions.forEach(function(s) {
            sessionHtml += `
                <div class="feed-item">
                    <span class="feed-dot info"></span>
                    <div class="feed-text">${TT.escHtml(s.child_first_name)} ${TT.escHtml(s.child_last_name)}</div>
                    <span class="feed-time">${s.session_date} ${s.start_time || ''}</span>
                </div>
            `;
        });
    } else {
        sessionHtml = '<div style="padding: 16px; color: #999; font-size: 0.875rem;">No upcoming sessions</div>';
    }

    $el.html(`
        <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
            <div class="stat-card">
                <div class="stat-label">Assigned Students</div>
                <div class="stat-value">${p.assigned_students}</div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-label">Pending Notes</div>
                <div class="stat-value">${p.pending_notes}</div>
            </div>
        </div>
        <div class="hub-card" style="margin-top: 16px;">
            <div class="hub-card-header"><h3>Your Upcoming Sessions</h3></div>
            <div class="hub-card-body no-pad">${sessionHtml}</div>
        </div>
    `);
};

Hub.loadActivityFeed = function() {
    TT.get('DashboardController.php', { action: 'get_activity_feed', limit: 15 })
        .done(function(r) {
            if (!r.success) return;
            const $feed = $('#activityFeed');
            if (!$feed.length) return;

            if (!r.data.activities || !r.data.activities.length) {
                $feed.html('<div style="padding: 20px; text-align: center; color: #999;">No recent activity</div>');
                return;
            }

            let html = '';
            r.data.activities.forEach(function(a) {
                html += `
                    <div class="feed-item" ${a.link ? `onclick="location.href='${a.link}'" style="cursor:pointer;"` : ''}>
                        <span class="feed-dot ${a.icon}"></span>
                        <div class="feed-text">${TT.escHtml(a.text)}</div>
                        <span class="feed-time">${a.time_ago}</span>
                    </div>
                `;
            });
            $feed.html(html);
        });
};

// ============================================================
// TABLE HELPERS
// ============================================================
/**
 * Load paginated data into a Hub table.
 *
 * @param {string} controller - e.g. 'ParentController.php'
 * @param {string} action - e.g. 'get_clients'
 * @param {object} filters - filter params
 * @param {function} renderRow - function(item) returns <tr> HTML
 * @param {string} tableBodySelector - e.g. '#clientsTable'
 * @param {string} paginationSelector - e.g. '#clientsPagination'
 */
Hub.loadTable = function(controller, action, filters, renderRow, tableBodySelector, paginationSelector) {
    const params = Object.assign({ action: action }, filters);
    const $table = $(tableBodySelector);
    const $pagination = $(paginationSelector);

    $table.html('<tr><td colspan="99" style="text-align:center; padding:24px;"><div class="spinner mx-auto"></div></td></tr>');

    TT.get(controller, params)
        .done(function(r) {
            if (!r.success || !r.data) return;

            const items = r.data[Object.keys(r.data).find(k => Array.isArray(r.data[k]))] || [];

            if (items.length === 0) {
                $table.html('<tr><td colspan="99"><div class="empty-state"><div class="icon">📭</div><h3>Nothing found</h3><p>Try adjusting your filters.</p></div></td></tr>');
                if ($pagination) $pagination.empty();
                return;
            }

            $table.html(items.map(renderRow).join(''));

            // Build pagination
            if ($pagination && r.data.pages > 1) {
                TT.buildPagination($pagination, r.data.page, r.data.pages, function(page) {
                    filters.page = page;
                    Hub.loadTable(controller, action, filters, renderRow, tableBodySelector, paginationSelector);
                });
            }
        });
};

// ============================================================
// QUICK STATS POLLING (lightweight)
// ============================================================
Hub.startQuickPoll = function() {
    setInterval(function() {
        TT.get('DashboardController.php', { action: 'get_quick_stats' })
            .done(function(r) {
                if (!r.success) return;
                // Update any badges
                if (r.data.unread_messages !== undefined) {
                    const $msgBadge = $('.sidebar-link[href*="messages"] .sidebar-badge');
                    if (r.data.unread_messages > 0) {
                        if (!$msgBadge.length) {
                            $('.sidebar-link[href*="messages"]').append(`<span class="sidebar-badge">${r.data.unread_messages}</span>`);
                        } else {
                            $msgBadge.text(r.data.unread_messages);
                        }
                    } else {
                        $msgBadge.remove();
                    }
                }
            });
    }, 60000); // every 60s
};

// ============================================================
// INIT
// ============================================================
$(function() {
    Hub.initSidebar();
    Hub.startQuickPoll();
});

window.Hub = Hub;
