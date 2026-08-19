<?php
$pageTitle = 'Finance';
$currentModule = 'finance';
require_once __DIR__ . '/../templates/header-hub.php';
?>
<div class="page-header"><h1>Finance</h1><div class="actions"><button class="btn btn-primary btn-sm" onclick="showInvoiceModal()">+ Create Invoice</button><button class="btn btn-outline btn-sm" onclick="showExpenseModal()">+ Add Expense</button></div></div>

<div class="tabs">
    <div class="tab active" data-tab="tabInvoices">Invoices</div>
    <div class="tab" data-tab="tabExpenses">Expenses</div>
    <div class="tab" data-tab="tabPL">P&L Report</div>
</div>

<div class="tab-content active" id="tabInvoices">
    <div class="filter-bar">
        <select class="form-select" id="invStatus" onchange="loadInvoices()"><option value="">All</option><option value="sent">Sent</option><option value="paid">Paid</option><option value="overdue">Overdue</option><option value="cancelled">Cancelled</option></select>
    </div>
    <div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Invoice #</th><th>Client</th><th>Amount</th><th>Status</th><th>Due</th><th></th></tr></thead><tbody id="invoicesTable"></tbody></table></div></div>
    <div class="pagination" id="invoicesPagination"></div>
</div>

<div class="tab-content" id="tabExpenses">
    <div class="filter-bar">
        <input type="date" class="form-input" id="expFrom" value="<?= date('Y-m-01') ?>" onchange="loadExpenses()" style="width:auto;">
        <input type="date" class="form-input" id="expTo" value="<?= date('Y-m-t') ?>" onchange="loadExpenses()" style="width:auto;">
    </div>
    <div id="expenseSummary" class="mb-2"></div>
    <div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th><th></th></tr></thead><tbody id="expensesTable"></tbody></table></div></div>
</div>

<div class="tab-content" id="tabPL">
    <div class="filter-bar">
        <input type="date" class="form-input" id="plFrom" value="<?= date('Y-01-01') ?>" style="width:auto;">
        <input type="date" class="form-input" id="plTo" value="<?= date('Y-12-31') ?>" style="width:auto;">
        <button class="btn btn-primary btn-sm" onclick="loadPL()">Generate</button>
    </div>
    <div id="plReport"></div>
</div>

<!-- Invoice Modal -->
<div class="modal-overlay" id="invoiceModal"><div class="modal modal-lg">
    <div class="modal-header"><h3>Create Invoice</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
    <div class="modal-body">
        <form id="invoiceForm">
            <input type="hidden" name="action" value="create_invoice">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Client <span class="required">*</span></label><select name="parent_id" class="form-select" id="invClientSelect" required><option value="">Select...</option></select></div>
                <div class="form-group"><label class="form-label">Due Date</label><input type="date" name="due_date" class="form-input" value="<?= date('Y-m-d', strtotime('+7 days')) ?>"></div>
            </div>
            <div class="form-group"><label class="form-label">Type</label><select name="invoice_type" class="form-select"><option value="tutorials">Tutorials</option><option value="club_membership">Club Membership</option><option value="bookstore">Bookstore</option><option value="consulting">Consulting</option><option value="custom">Custom</option></select></div>
            <h4 class="mt-3 mb-2" style="color: var(--tinker-teal);">Line Items</h4>
            <div id="invoiceItems">
                <div class="form-row inv-item"><div class="form-group" style="flex:3;"><input type="text" name="desc" class="form-input" placeholder="Description" required></div><div class="form-group" style="flex:1;"><input type="number" name="qty" class="form-input" placeholder="Qty" value="1" min="1"></div><div class="form-group" style="flex:1;"><input type="number" name="price" class="form-input" placeholder="Price" required></div><div style="padding-top:28px;"><button type="button" class="btn btn-ghost btn-sm" onclick="$(this).closest('.inv-item').remove()">✕</button></div></div>
            </div>
            <button type="button" class="btn btn-ghost btn-sm mt-1" onclick="addInvItem()">+ Add Item</button>
            <div class="form-group mt-2"><label class="form-label">Discount (₦)</label><input type="number" name="discount" class="form-input" value="0" min="0"></div>
            <div class="form-group"><label class="form-label">Notes</label><textarea name="notes" class="form-textarea" rows="2"></textarea></div>
        </form>
    </div>
    <div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="saveInvoice()">Create & Send</button></div>
</div></div>

<!-- Expense Modal -->
<div class="modal-overlay" id="expenseModal"><div class="modal">
    <div class="modal-header"><h3>Add Expense</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
    <div class="modal-body">
        <form id="expenseForm">
            <input type="hidden" name="action" value="add_expense">
            <div class="form-row">
                <div class="form-group"><label class="form-label">Amount (₦) <span class="required">*</span></label><input type="number" name="amount" class="form-input" required></div>
                <div class="form-group"><label class="form-label">Date</label><input type="date" name="expense_date" class="form-input" value="<?= date('Y-m-d') ?>"></div>
            </div>
            <div class="form-group"><label class="form-label">Category <span class="required">*</span></label><select name="category" class="form-select" required><option value="">Select...</option><option value="transport">Transport</option><option value="materials">Materials</option><option value="salary">Salary</option><option value="utilities">Utilities</option><option value="marketing">Marketing</option><option value="office">Office</option><option value="other">Other</option></select></div>
            <div class="form-group"><label class="form-label">Description <span class="required">*</span></label><input type="text" name="description" class="form-input" required></div>
        </form>
    </div>
    <div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="TT.submitForm('#expenseForm','FinanceController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#expenseModal');loadExpenses();TT.toast(r.message,'success');}}})">Save</button></div>
</div></div>

<!-- Mark Paid Modal -->
<div class="modal-overlay" id="paidModal"><div class="modal">
    <div class="modal-header"><h3>Confirm Payment</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
    <div class="modal-body">
        <form id="paidForm">
            <input type="hidden" name="action" value="mark_paid"><input type="hidden" name="id" id="paidInvId">
            <div class="form-group"><label class="form-label">Payment Reference</label><input type="text" name="payment_reference" class="form-input" placeholder="Bank transfer ref"></div>
        </form>
    </div>
    <div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-success" onclick="TT.submitForm('#paidForm','FinanceController.php',{onSuccess:r=>{if(r.success){TT.modal.close('#paidModal');loadInvoices();TT.toast(r.message,'success');}}})">Confirm Payment</button></div>
</div></div>

<script>
function loadInvoices() {
    Hub.loadTable('FinanceController.php','get_invoices',{status:$('#invStatus').val(),page:1},function(i){
        const sc={paid:'badge-green',sent:'badge-blue',overdue:'badge-red',cancelled:'badge-gray'}[i.status]||'badge-gray';
        return `<tr><td class="fw-600">${TT.escHtml(i.invoice_number)}</td><td>${TT.escHtml(i.first_name+' '+i.last_name)}</td><td class="fw-700">${i.formatted_total}</td><td><span class="badge ${sc}">${i.status}</span></td><td class="text-sm">${i.formatted_due}</td>
        <td class="row-actions">${i.status==='sent'||i.status==='overdue'?`<button class="btn btn-success btn-sm" onclick="markPaid(${i.id})">Mark Paid</button>`:''}</td></tr>`;
    },'#invoicesTable','#invoicesPagination');
}
function loadExpenses() {
    TT.get('FinanceController.php',{action:'get_expenses',from:$('#expFrom').val(),to:$('#expTo').val()}).done(function(r){
        if(!r.success) return;
        $('#expenseSummary').html(`<div class="stat-card stat-red" style="display:inline-block;"><div class="stat-label">Total Expenses</div><div class="stat-value">${r.data.formatted_total}</div></div>`);
        if(!r.data.expenses.length){$('#expensesTable').html('<tr><td colspan="5"><div class="empty-state"><p>No expenses found.</p></div></td></tr>');return;}
        let html='';r.data.expenses.forEach(e=>{html+=`<tr><td>${e.formatted_date}</td><td><span class="badge badge-navy">${e.category}</span></td><td>${TT.escHtml(e.description)}</td><td class="fw-700">${e.formatted_amount}</td><td><button class="btn btn-ghost btn-sm" onclick="deleteExpense(${e.id})">Delete</button></td></tr>`;});
        $('#expensesTable').html(html);
    });
}
function loadPL(){
    TT.get('FinanceController.php',{action:'get_pl_report',from:$('#plFrom').val(),to:$('#plTo').val()}).done(function(r){
        if(!r.success) return;const d=r.data;
        $('#plReport').html(`<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);"><div class="stat-card"><div class="stat-label">Revenue</div><div class="stat-value">${d.formatted.revenue}</div></div><div class="stat-card stat-red"><div class="stat-label">Expenses</div><div class="stat-value">${d.formatted.expenses}</div></div><div class="stat-card ${d.net_profit>=0?'stat-green':'stat-red'}"><div class="stat-label">Net Profit</div><div class="stat-value">${d.formatted.net_profit}</div><div class="stat-hint">Margin: ${d.margin}%</div></div></div>`);
    });
}
function markPaid(id){$('#paidInvId').val(id);TT.modal.open('#paidModal');}
function deleteExpense(id){TT.modal.confirm('Delete this expense?',()=>{TT.api('FinanceController.php',{action:'delete_expense',id}).done(r=>{if(r.success){loadExpenses();TT.toast(r.message,'success');}});});}
function showInvoiceModal(){TT.clearForm('#invoiceForm');TT.get('ParentController.php',{action:'get_clients',status:'active'}).done(r=>{let o='<option value="">Select client...</option>';if(r.data?.clients)r.data.clients.forEach(c=>{o+=`<option value="${c.id}">${TT.escHtml(c.first_name+' '+c.last_name)}</option>`;});$('#invClientSelect').html(o);});TT.modal.open('#invoiceModal');}
function showExpenseModal(){TT.clearForm('#expenseForm');TT.modal.open('#expenseModal');}
function addInvItem(){$('#invoiceItems').append('<div class="form-row inv-item"><div class="form-group" style="flex:3;"><input type="text" name="desc" class="form-input" placeholder="Description" required></div><div class="form-group" style="flex:1;"><input type="number" name="qty" class="form-input" placeholder="Qty" value="1" min="1"></div><div class="form-group" style="flex:1;"><input type="number" name="price" class="form-input" placeholder="Price" required></div><div style="padding-top:28px;"><button type="button" class="btn btn-ghost btn-sm" onclick="$(this).closest(\'.inv-item\').remove()">✕</button></div></div>');}
function saveInvoice(){const items=[];$('#invoiceItems .inv-item').each(function(){items.push({description:$(this).find('[name=desc]').val(),quantity:$(this).find('[name=qty]').val()||1,unit_price:$(this).find('[name=price]').val()||0});});const d=TT.formData('#invoiceForm');d.items=JSON.stringify(items);d.action='create_invoice';TT.api('FinanceController.php',d).done(r=>{if(r.success){TT.modal.close('#invoiceModal');loadInvoices();TT.toast(r.message,'success');}});}

$(function(){loadInvoices();loadExpenses();});
</script>
<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
