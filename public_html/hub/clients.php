<?php
$pageTitle = 'Clients';
$currentModule = 'clients';
require_once __DIR__ . '/../templates/header-hub.php';
?>

<div class="page-header">
    <h1>Client Management</h1>
    <div class="actions"><button class="btn btn-primary btn-sm" onclick="showAddClient()">+ Add Client</button></div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="search-bar"><span class="search-icon"><?= ttIcon('search', 'tt-icon', 16) ?></span><input type="text" placeholder="Search clients..." id="clientSearch"></div>
    <select class="form-select" id="statusFilter" onchange="loadClients()">
        <option value="">All Statuses</option><option value="active">Active</option><option value="inactive">Inactive</option>
    </select>
</div>

<!-- List View -->
<div id="clientListView">
    <div class="hub-card"><div class="hub-card-body no-pad">
        <div class="table-responsive"><table class="hub-table"><thead><tr><th>Client</th><th>Email</th><th>Phone</th><th>Children</th><th>Services</th><th>Last Active</th><th></th></tr></thead>
        <tbody id="clientsTable"></tbody></table></div>
    </div></div>
    <div class="pagination" id="clientsPagination"></div>
</div>

<!-- Detail View (hidden by default) -->
<div id="clientDetailView" style="display: none;">
    <button class="btn btn-ghost btn-sm mb-3" onclick="showListView()"><?= ttIcon('arrow-left', 'tt-icon', 14) ?> Back to list</button>
    <div id="clientDetail"></div>
</div>

<!-- Add/Edit Client Modal -->
<div class="modal-overlay" id="clientModal">
    <div class="modal modal-lg">
        <div class="modal-header"><h3>Add New Client</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
        <div class="modal-body">
            <form id="clientForm">
                <input type="hidden" name="action" value="add_client">
                <h4 class="mb-2" style="color: var(--tinker-teal);">Parent Information</h4>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">First Name <span class="required">*</span></label><input type="text" name="first_name" class="form-input" required></div>
                    <div class="form-group"><label class="form-label">Last Name <span class="required">*</span></label><input type="text" name="last_name" class="form-input" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Email <span class="required">*</span></label><input type="email" name="email" class="form-input" required></div>
                    <div class="form-group"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-input"></div>
                </div>
                <h4 class="mt-3 mb-2" style="color: var(--tinker-teal);">Child Information (optional)</h4>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Child First Name</label><input type="text" name="child_first_name" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Child Last Name</label><input type="text" name="child_last_name" class="form-input"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" name="child_dob" class="form-input"></div>
                    <div class="form-group"><label class="form-label">Gender</label><select name="child_gender" class="form-select"><option value="male">Male</option><option value="female">Female</option></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Service</label><select name="service_type" class="form-select"><option value="">None yet</option><option value="tutorials">Tutorials</option><option value="stem_club">STEM Club</option><option value="both">Both</option></select></div>
                    <div class="form-group"><label class="form-label">Tutor</label><select name="tutor_id" class="form-select" id="tutorSelect"><option value="">Assign later</option></select></div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" data-modal-close>Cancel</button>
            <button class="btn btn-primary" onclick="saveClient()">Save Client</button>
        </div>
    </div>
</div>

<script>
let clientFilters = { page: 1 };

function loadClients() {
    clientFilters.search = $('#clientSearch').val();
    clientFilters.status = $('#statusFilter').val();

    Hub.loadTable('ParentController.php', 'get_clients', clientFilters, function(c) {
        const services = c.services ? c.services.split(',').map(s => `<span class="badge badge-teal">${s.replace('_',' ')}</span>`).join(' ') : '<span class="text-muted text-xs">None</span>';
        return `<tr>
            <td><strong style="cursor:pointer; color: var(--tinker-teal);" onclick="viewClient(${c.id})">${TT.escHtml(c.name)}</strong>
                ${c.is_active == 0 ? '<span class="badge badge-red" style="margin-left:4px;">Inactive</span>' : ''}</td>
            <td class="text-sm">${TT.escHtml(c.email)}</td>
            <td class="text-sm">${TT.escHtml(c.phone || '-')}</td>
            <td>${c.child_count}</td>
            <td>${services}</td>
            <td class="text-sm text-muted">${TT.escHtml(c.last_activity)}</td>
            <td class="row-actions">
                <button class="btn btn-ghost btn-sm" onclick="viewClient(${c.id})">View</button>
                ${c.is_active == 1 ? `<button class="btn btn-ghost btn-sm" onclick="deactivateClient(${c.id})">Deactivate</button>` : `<button class="btn btn-ghost btn-sm" onclick="activateClient(${c.id})">Activate</button>`}
            </td>
        </tr>`;
    }, '#clientsTable', '#clientsPagination');
}

function viewClient(id) {
    TT.get('ParentController.php', { action: 'get_client', id }).done(function(r) {
        if (!r.success) return;
        const c = r.data.client;
        let html = `<div class="detail-header">
            <div class="avatar avatar-lg avatar-teal">${TT.initials(c.first_name + ' ' + c.last_name)}</div>
            <div><h2>${TT.escHtml(c.first_name + ' ' + c.last_name)}</h2><div class="subtitle">${TT.escHtml(c.email)} · ${TT.escHtml(c.phone || 'No phone')}</div></div>
        </div>`;
        // Children
        html += '<div class="hub-card"><div class="hub-card-header"><h3>Children</h3></div><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Name</th><th>Age</th><th>Service</th><th>Tutor</th><th>Status</th></tr></thead><tbody>';
        if (r.data.children.length) {
            r.data.children.forEach(ch => {
                const age = ch.date_of_birth ? Math.floor((Date.now() - new Date(ch.date_of_birth)) / (365.25*86400000)) : '?';
                html += `<tr><td><strong>${TT.escHtml(ch.first_name + ' ' + ch.last_name)}</strong></td><td>${age}</td><td>${ch.service_type ? `<span class="badge badge-teal">${ch.service_type.replace('_',' ')}</span>` : '-'}</td><td>${ch.tutor_first_name ? TT.escHtml(ch.tutor_first_name + ' ' + ch.tutor_last_name) : '-'}</td><td>${ch.service_status || ch.status}</td></tr>`;
            });
        } else { html += '<tr><td colspan="5" class="text-muted text-center p-3">No children registered</td></tr>'; }
        html += '</tbody></table></div></div>';
        // Invoices
        html += '<div class="hub-card"><div class="hub-card-header"><h3>Recent Invoices</h3></div><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Invoice #</th><th>Amount</th><th>Status</th><th>Due</th></tr></thead><tbody>';
        if (r.data.invoices.length) {
            r.data.invoices.forEach(inv => {
                const statusClass = {paid:'badge-green',sent:'badge-blue',overdue:'badge-red',cancelled:'badge-gray'}[inv.status] || 'badge-gray';
                html += `<tr><td>${TT.escHtml(inv.invoice_number)}</td><td class="fw-700">${TT.formatNaira(inv.total)}</td><td><span class="badge ${statusClass}">${inv.status}</span></td><td class="text-sm">${inv.due_date}</td></tr>`;
            });
        } else { html += '<tr><td colspan="4" class="text-muted text-center p-3">No invoices</td></tr>'; }
        html += '</tbody></table></div></div>';
        $('#clientDetail').html(html);
        $('#clientListView').hide();
        $('#clientDetailView').show();
    });
}
function showListView() { $('#clientDetailView').hide(); $('#clientListView').show(); }
function showAddClient() {
    TT.clearForm('#clientForm');
    TT.modal.open('#clientModal');
    // Load tutors
    TT.get('StaffController.php', { action: 'get_tutors' }).done(r => {
        let opts = '<option value="">Assign later</option>';
        if (r.data?.tutors) r.data.tutors.forEach(t => { opts += `<option value="${t.id}">${TT.escHtml(t.first_name + ' ' + t.last_name)}</option>`; });
        $('#tutorSelect').html(opts);
    });
}
function saveClient() {
    TT.submitForm('#clientForm', 'ParentController.php', { onSuccess: r => { if (r.success) { TT.modal.close('#clientModal'); loadClients(); TT.toast(r.message, 'success'); } } });
}
function deactivateClient(id) { TT.modal.confirm('Deactivate this client? Their session will be terminated immediately.', () => { TT.api('ParentController.php', { action: 'deactivate_client', id }).done(r => { if (r.success) { loadClients(); TT.toast(r.message, 'success'); } }); }); }
function activateClient(id) { TT.api('ParentController.php', { action: 'activate_client', id }).done(r => { if (r.success) { loadClients(); TT.toast(r.message, 'success'); } }); }

$(function() {
    loadClients();
    $('#clientSearch').on('input', TT.debounce(function() { clientFilters.page = 1; loadClients(); }));
});
</script>

<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
