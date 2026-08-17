<?php
$pageTitle = 'Dashboard';
$currentModule = 'dashboard';
require_once __DIR__ . '/../templates/header-hub.php';
?>

<div id="statsGrid" class="stats-grid">
    <div class="stat-card"><div class="stat-label">Loading...</div><div class="stat-value"><div class="spinner spinner-sm"></div></div></div>
</div>

<div id="clientStatsGrid" class="stats-grid" style="margin-top: -8px;"></div>
<div id="personalStats"></div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
    <div class="activity-feed">
        <div class="feed-header"><h3>Recent Activity</h3></div>
        <div id="activityFeed">
            <div style="padding: 24px; text-align: center;"><div class="spinner mx-auto"></div></div>
        </div>
    </div>

    <div class="hub-card">
        <div class="hub-card-header"><h3>Quick Actions</h3></div>
        <div class="hub-card-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <a href="<?= HUB_URL ?>/clients.php?action=add" class="btn btn-outline btn-sm" style="justify-content: flex-start;">👥 Add Client</a>
            <a href="<?= HUB_URL ?>/sessions.php?action=add" class="btn btn-outline btn-sm" style="justify-content: flex-start;">📅 Schedule Session</a>
            <a href="<?= HUB_URL ?>/finance.php?action=invoice" class="btn btn-outline btn-sm" style="justify-content: flex-start;">💰 Create Invoice</a>
            <a href="<?= HUB_URL ?>/bookstore.php?action=add" class="btn btn-outline btn-sm" style="justify-content: flex-start;">📚 Add Product</a>
            <a href="<?= HUB_URL ?>/club.php" class="btn btn-outline btn-sm" style="justify-content: flex-start;">🎪 Club Attendance</a>
            <a href="<?= HUB_URL ?>/blog.php?action=add" class="btn btn-outline btn-sm" style="justify-content: flex-start;">✍️ New Blog Post</a>
        </div>
    </div>
</div>

<script>
$(function() {
    Hub.loadDashboard();
});
</script>

<style>
@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr; gap: 20px"] { grid-template-columns: 1fr !important; }
}
</style>

<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
