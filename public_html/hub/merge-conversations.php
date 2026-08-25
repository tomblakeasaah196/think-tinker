<?php $pageTitle='Merge Duplicate Conversations'; $currentModule='messages'; require_once __DIR__.'/../templates/header-hub.php';
// One-off maintenance tool, not in the sidebar — super_admin only.
if ($user['user_type'] !== 'super_admin') { redirect(HUB_URL . '/messages.php'); }
?>
<div class="page-header"><h1>Merge Duplicate Conversations</h1></div>
<p class="text-sm text-muted" style="max-width:640px; margin-bottom:16px;">
    A now-fixed bug could split one client's messages across several conversation
    threads instead of keeping them in one. This finds clients with more than one
    thread and merges them into the earliest one.
</p>

<div class="hub-card"><div class="hub-card-body no-pad">
    <table class="hub-table">
        <thead><tr><th>Client</th><th>Email</th><th>Threads</th><th>Messages</th><th>Date range</th><th></th></tr></thead>
        <tbody id="dupTable"></tbody>
    </table>
</div></div>

<script>
function loadDuplicates() {
    TT.get('MessageController.php', { action: 'find_duplicate_conversations' }).done(function(r) {
        if (!r.success) { TT.toast(r.message || 'Could not load.', 'error'); return; }
        const rows = r.data.duplicates || [];
        if (!rows.length) {
            $('#dupTable').html('<tr><td colspan="6"><div class="empty-state"><p>No duplicate conversations found.</p></div></td></tr>');
            return;
        }
        let h = '';
        rows.forEach(d => {
            const convos = d.conversations || [];
            const totalMsgs = convos.reduce((sum, c) => sum + (parseInt(c.msg_count, 10) || 0), 0);
            const first = convos[0], last = convos[convos.length - 1];
            h += `<tr>
                <td><strong>${TT.escHtml(d.parent_name)}</strong></td>
                <td class="text-sm">${TT.escHtml(d.parent_email || '-')}</td>
                <td>${convos.length}</td>
                <td>${totalMsgs}</td>
                <td class="text-sm text-muted">${TT.escHtml(first.first_at)} — ${TT.escHtml(last.last_at)}</td>
                <td><button type="button" class="btn btn-primary btn-sm" onclick="mergeClient(${d.sender_id}, this)">Merge into one</button></td>
            </tr>`;
        });
        $('#dupTable').html(h);
    });
}

function mergeClient(senderId, btn) {
    if (!confirm('Merge all duplicate threads for this client into one conversation? This cannot be undone.')) return;
    $(btn).prop('disabled', true).text('Merging…');
    TT.api('MessageController.php', { action: 'merge_conversations', sender_id: senderId }).done(function(r) {
        if (r.success) {
            TT.toast(r.message, 'success');
            loadDuplicates();
        } else {
            $(btn).prop('disabled', false).text('Merge into one');
        }
    });
}

loadDuplicates();
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
