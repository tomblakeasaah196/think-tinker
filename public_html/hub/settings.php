<?php
$pageTitle = 'Settings';
$currentModule = 'settings';
require_once __DIR__ . '/../templates/header-hub.php';
?>

<div class="tabs">
    <div class="tab active" data-tab="tabBusiness">Business Setup</div>
    <div class="tab" data-tab="tabBank">Bank Accounts</div>
    <div class="tab" data-tab="tabRoles">Roles & Permissions</div>
</div>

<!-- Business Settings Tab -->
<div class="tab-content active" id="tabBusiness">
    <div class="hub-card">
        <div class="hub-card-header"><h3>Business Information</h3></div>
        <div class="hub-card-body">
            <form id="settingsForm">
                <div id="settingsFields"><div class="spinner mx-auto"></div></div>
                <div style="margin-top: 16px;">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bank Accounts Tab -->
<div class="tab-content" id="tabBank">
    <div class="hub-card">
        <div class="hub-card-header">
            <h3>Bank Accounts</h3>
            <button class="btn btn-primary btn-sm" onclick="showBankModal()">+ Add Account</button>
        </div>
        <div class="hub-card-body no-pad">
            <table class="hub-table"><thead><tr><th>Bank</th><th>Account Name</th><th>Number</th><th>Type</th><th>Status</th><th></th></tr></thead>
            <tbody id="bankTable"><tr><td colspan="6" style="text-align:center; padding:24px;"><div class="spinner mx-auto"></div></td></tr></tbody></table>
        </div>
    </div>
</div>

<!-- Roles Tab -->
<div class="tab-content" id="tabRoles">
    <div class="hub-card">
        <div class="hub-card-header"><h3>Role Permissions</h3></div>
        <div class="hub-card-body">
            <div class="form-group" style="max-width: 300px;">
                <label class="form-label">Select Role</label>
                <select class="form-select" id="roleSelect" onchange="loadRolePerms(this.value)">
                    <option value="">Choose role...</option>
                </select>
            </div>
            <div id="permissionsGrid" style="margin-top: 16px;"></div>
        </div>
    </div>
</div>

<!-- Bank Account Modal -->
<div class="modal-overlay" id="bankModal">
    <div class="modal">
        <div class="modal-header"><h3 id="bankModalTitle">Add Bank Account</h3><span class="modal-close">&times;</span></div>
        <div class="modal-body">
            <form id="bankForm">
                <input type="hidden" name="action" value="add_bank_account">
                <input type="hidden" name="id" value="">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Bank Name <span class="required">*</span></label><input type="text" name="bank_name" class="form-input" required></div>
                    <div class="form-group"><label class="form-label">Account Type</label><select name="account_type" class="form-select"><option value="current">Current</option><option value="savings">Savings</option></select></div>
                </div>
                <div class="form-group"><label class="form-label">Account Name <span class="required">*</span></label><input type="text" name="account_name" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Account Number <span class="required">*</span></label><input type="text" name="account_number" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Notes</label><input type="text" name="notes" class="form-input"></div>
                <div class="checkbox-group"><input type="checkbox" name="is_primary" value="1" id="isPrimary"><label for="isPrimary">Set as primary account</label></div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" data-modal-close>Cancel</button>
            <button class="btn btn-primary" onclick="saveBank()">Save</button>
        </div>
    </div>
</div>

<script>
// Load settings
function loadSettings() {
    TT.get('SettingsController.php', { action: 'get_settings_form' }).done(function(r) {
        if (!r.success) return;
        let html = '';
        Object.entries(r.data.groups).forEach(([group, fields]) => {
            html += `<h4 style="margin: 20px 0 12px; color: var(--tinker-teal); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px;">${group.replace(/_/g, ' ')}</h4>`;
            fields.forEach(f => {
                const inputType = f.setting_type === 'textarea' ? 'textarea' : 'input';
                if (inputType === 'textarea') {
                    html += `<div class="form-group"><label class="form-label">${TT.escHtml(f.setting_label)}</label><textarea name="settings[${f.setting_key}]" class="form-textarea" rows="2">${TT.escHtml(f.setting_value)}</textarea></div>`;
                } else {
                    html += `<div class="form-group"><label class="form-label">${TT.escHtml(f.setting_label)}</label><input type="${f.setting_type || 'text'}" name="settings[${f.setting_key}]" class="form-input" value="${TT.escHtml(f.setting_value)}"></div>`;
                }
            });
        });
        $('#settingsFields').html(html);
    });
}

$('#settingsForm').on('submit', function(e) {
    e.preventDefault();
    const data = { action: 'update_settings', _token: TT.config.csrfToken };
    $(this).find('[name^="settings["]').each(function() {
        const key = $(this).attr('name');
        data[key] = $(this).val();
    });
    TT.api('SettingsController.php', data).done(r => { if (r.success) TT.toast(r.message, 'success'); });
});

// Bank accounts
function loadBanks() {
    TT.get('SettingsController.php', { action: 'get_bank_accounts' }).done(function(r) {
        if (!r.success) return;
        if (!r.data.accounts.length) { $('#bankTable').html('<tr><td colspan="6"><div class="empty-state"><p>No bank accounts yet.</p></div></td></tr>'); return; }
        let html = '';
        r.data.accounts.forEach(b => {
            html += `<tr>
                <td><strong>${TT.escHtml(b.bank_name)}</strong></td>
                <td>${TT.escHtml(b.account_name)}</td>
                <td style="font-weight: 700; font-size: 1rem;">${TT.escHtml(b.account_number)}</td>
                <td>${b.account_type}</td>
                <td>${b.is_primary == 1 ? '<span class="badge badge-green">Primary</span>' : (b.is_active == 1 ? '<span class="badge badge-gray">Active</span>' : '<span class="badge badge-red">Inactive</span>')}</td>
                <td class="row-actions">
                    <button class="btn btn-ghost btn-sm" onclick="editBank(${b.id})">Edit</button>
                    ${b.is_primary == 0 ? `<button class="btn btn-ghost btn-sm" onclick="setPrimary(${b.id})">Set Primary</button>` : ''}
                    <button class="btn btn-ghost btn-sm" onclick="toggleBank(${b.id})">${b.is_active == 1 ? 'Deactivate' : 'Activate'}</button>
                </td>
            </tr>`;
        });
        $('#bankTable').html(html);
    });
}

function showBankModal(id) {
    TT.clearForm('#bankForm');
    $('[name="action"]', '#bankForm').val(id ? 'update_bank_account' : 'add_bank_account');
    $('[name="id"]', '#bankForm').val(id || '');
    $('#bankModalTitle').text(id ? 'Edit Bank Account' : 'Add Bank Account');
    TT.modal.open('#bankModal');
}
function editBank(id) {
    TT.get('SettingsController.php', { action: 'get_bank_accounts' }).done(function(r) {
        const b = r.data.accounts.find(a => a.id == id);
        if (!b) return;
        showBankModal(id);
        Object.keys(b).forEach(k => { $(`[name="${k}"]`, '#bankForm').val(b[k]); });
        $('#isPrimary').prop('checked', b.is_primary == 1);
    });
}
function saveBank() {
    TT.submitForm('#bankForm', 'SettingsController.php', { onSuccess: r => { if (r.success) { TT.modal.close('#bankModal'); loadBanks(); TT.toast(r.message, 'success'); } } });
}
function setPrimary(id) { TT.api('SettingsController.php', { action: 'set_primary_bank', id }).done(r => { if (r.success) { loadBanks(); TT.toast(r.message, 'success'); } }); }
function toggleBank(id) { TT.api('SettingsController.php', { action: 'toggle_bank_account', id }).done(r => { if (r.success) { loadBanks(); TT.toast(r.message, 'success'); } }); }

// Roles
function loadRoles() {
    TT.get('SettingsController.php', { action: 'get_roles' }).done(function(r) {
        if (!r.success) return;
        let opts = '<option value="">Choose role...</option>';
        r.data.roles.forEach(role => { opts += `<option value="${role.id}">${TT.escHtml(role.display_name || role.name)}</option>`; });
        $('#roleSelect').html(opts);
    });
}
function loadRolePerms(roleId) {
    if (!roleId) { $('#permissionsGrid').empty(); return; }
    Promise.all([
        TT.get('SettingsController.php', { action: 'get_all_permissions' }),
        TT.get('SettingsController.php', { action: 'get_role_permissions', role_id: roleId })
    ]).then(([permResp, roleResp]) => {
        const allPerms = permResp.data.permissions;
        const assigned = roleResp.data.permission_ids.map(Number);
        let html = '<form id="rolePermForm">';
        Object.entries(allPerms).forEach(([module, perms]) => {
            html += `<div style="margin-bottom: 16px;"><h4 style="color: var(--deep-navy); font-size: 0.8125rem; margin-bottom: 8px;">${module}</h4>`;
            perms.forEach(p => {
                const checked = assigned.includes(p.id) ? 'checked' : '';
                html += `<div class="checkbox-group"><input type="checkbox" name="perm_${p.id}" value="${p.id}" ${checked} id="p${p.id}"><label for="p${p.id}">${TT.escHtml(p.name)}</label></div>`;
            });
            html += '</div>';
        });
        html += `<button type="submit" class="btn btn-primary btn-sm">Save Permissions</button></form>`;
        $('#permissionsGrid').html(html);
        $('#rolePermForm').on('submit', function(e) {
            e.preventDefault();
            const ids = [];
            $(this).find('input:checked').each(function() { ids.push($(this).val()); });
            TT.api('SettingsController.php', { action: 'update_role_permissions', role_id: roleId, 'permission_ids[]': ids })
                .done(r => { if (r.success) TT.toast('Permissions saved', 'success'); });
        });
    });
}

$(function() {
    loadSettings();
    loadBanks();
    loadRoles();
});
</script>

<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
