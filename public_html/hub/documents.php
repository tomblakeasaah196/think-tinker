<?php $pageTitle='Documents'; $currentModule='documents'; require_once __DIR__.'/../templates/header-hub.php'; ?>

<div class="page-header"><h1>Document Generator</h1></div>
<p class="text-muted mb-3" style="font-size:0.875rem;">Generate any branded PDF document on demand. All documents include your business details, logo, and bank accounts automatically.</p>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <!-- Invoice -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px;"><?= ttIcon('card', 'tt-icon', 26) ?></div>
            <div class="card-title">Invoice</div>
            <p class="card-text mb-2">Bill a client with line items and bank transfer details.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="genInvoicePrompt()">Generate</button>
        </div>
    </div>
    <!-- Receipt -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px; background:rgba(39,174,96,0.1); color:var(--leaf-green);"><?= ttIcon('check', 'tt-icon', 26) ?></div>
            <div class="card-title">Receipt</div>
            <p class="card-text mb-2">Payment confirmation for a paid invoice.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="genReceiptPrompt()">Generate</button>
        </div>
    </div>
    <!-- Contract -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px;"><?= ttIcon('book', 'tt-icon', 26) ?></div>
            <div class="card-title">Service Contract</div>
            <p class="card-text mb-2">Service agreement for a parent-child enrollment.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#contractModal')">Generate</button>
        </div>
    </div>
    <!-- Admission Form -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px;"><?= ttIcon('clipboard', 'tt-icon', 26) ?></div>
            <div class="card-title">Admission Form</div>
            <p class="card-text mb-2">Pre-filled form with child and parent data.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#admissionModal')">Generate</button>
        </div>
    </div>
    <!-- Progress Report -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px; background:rgba(244,123,32,0.1); color:var(--spark-orange);"><?= ttIcon('star', 'tt-icon', 26) ?></div>
            <div class="card-title">Progress Report</div>
            <p class="card-text mb-2">Student progress with topics and tutor notes.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#progressModal')">Generate</button>
        </div>
    </div>
    <!-- Monthly Calendar -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px;"><?= ttIcon('calendar', 'tt-icon', 26) ?></div>
            <div class="card-title">Monthly Calendar</div>
            <p class="card-text mb-2">Session calendar for a student in a given month.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#calendarModal')">Generate</button>
        </div>
    </div>
    <!-- Club Certificate -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px; background:rgba(244,123,32,0.1); color:var(--spark-orange);"><?= ttIcon('award', 'tt-icon', 26) ?></div>
            <div class="card-title">Club Certificate</div>
            <p class="card-text mb-2">Participation certificate (landscape, ornamental).</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#certModal')">Generate</button>
        </div>
    </div>
    <!-- Staff Badge -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px;"><?= ttIcon('id-badge', 'tt-icon', 26) ?></div>
            <div class="card-title">Staff ID Badge</div>
            <p class="card-text mb-2">Front/back printable staff identification card.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#badgeModal')">Generate</button>
        </div>
    </div>
    <!-- Quote / Proposal -->
    <div class="card card-flat">
        <div class="card-body" style="text-align:center;">
            <div class="icon-badge" style="margin-bottom:8px;"><?= ttIcon('briefcase', 'tt-icon', 26) ?></div>
            <div class="card-title">Quote / Proposal</div>
            <p class="card-text mb-2">Custom quote for school partnerships.</p>
            <button class="btn btn-outline btn-sm btn-block" onclick="TT.modal.open('#quoteModal')">Generate</button>
        </div>
    </div>
</div>

<!-- Shared Child Select Modal Pattern -->
<?php
// Preload children + staff for dropdowns
$allChildren = dbFetchAll("SELECT c.id, c.first_name, c.last_name, u.first_name as p_first, u.id as parent_id FROM children c JOIN users u ON c.parent_id = u.id WHERE c.deleted_at IS NULL ORDER BY c.first_name");
$allStaff = dbFetchAll("SELECT u.id, u.first_name, u.last_name FROM users u JOIN staff_records sr ON u.id = sr.user_id WHERE u.is_active = 1 AND u.deleted_at IS NULL ORDER BY u.first_name");
$allMemberships = dbFetchAll("SELECT cm.id, c.first_name, c.last_name, cm.plan FROM club_memberships cm JOIN children c ON cm.child_id = c.id WHERE cm.status = 'active' ORDER BY c.first_name");

$childOpts = '<option value="">Select student...</option>';
foreach ($allChildren as $c) $childOpts .= '<option value="'.$c['id'].'" data-parent="'.$c['parent_id'].'">'.$c['first_name'].' '.$c['last_name'].' ('.$c['p_first'].')</option>';
$staffOpts = '<option value="">Select staff...</option>';
foreach ($allStaff as $s) $staffOpts .= '<option value="'.$s['id'].'">'.$s['first_name'].' '.$s['last_name'].'</option>';
$memberOpts = '<option value="">Select membership...</option>';
foreach ($allMemberships as $m) $memberOpts .= '<option value="'.$m['id'].'">'.$m['first_name'].' '.$m['last_name'].' ('.$m['plan'].')</option>';
?>

<!-- Contract Modal -->
<div class="modal-overlay" id="contractModal"><div class="modal"><div class="modal-header"><h3>Generate Contract</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="contractForm"><input type="hidden" name="action" value="generate_contract">
<div class="form-group"><label class="form-label">Student *</label><select name="child_id" class="form-select" required onchange="$('[name=parent_id]',this.form).val($(this).find(':selected').data('parent'))"><?= $childOpts ?></select></div>
<input type="hidden" name="parent_id" value="">
<div class="form-group"><label class="form-label">Service Type</label><select name="service_type" class="form-select"><option value="tutorials">Tutorials</option><option value="stem_club">STEM Club</option><option value="both">Both</option></select></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitDoc('#contractForm')">Generate</button></div></div></div>

<!-- Admission Modal -->
<div class="modal-overlay" id="admissionModal"><div class="modal"><div class="modal-header"><h3>Generate Admission Form</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="admissionForm"><input type="hidden" name="action" value="generate_admission">
<div class="form-group"><label class="form-label">Student *</label><select name="child_id" class="form-select" required><?= $childOpts ?></select></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitDoc('#admissionForm')">Generate</button></div></div></div>

<!-- Progress Report Modal -->
<div class="modal-overlay" id="progressModal"><div class="modal"><div class="modal-header"><h3>Generate Progress Report</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="progressForm"><input type="hidden" name="action" value="generate_progress">
<div class="form-group"><label class="form-label">Student *</label><select name="child_id" class="form-select" required><?= $childOpts ?></select></div>
<div class="form-group"><label class="form-label">Period</label><input type="text" name="period" class="form-input" value="<?= date('F Y') ?>" placeholder="e.g. June 2026"></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitDoc('#progressForm')">Generate</button></div></div></div>

<!-- Calendar Modal -->
<div class="modal-overlay" id="calendarModal"><div class="modal"><div class="modal-header"><h3>Generate Monthly Calendar</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="calendarForm"><input type="hidden" name="action" value="generate_calendar">
<div class="form-group"><label class="form-label">Student *</label><select name="child_id" class="form-select" required><?= $childOpts ?></select></div>
<div class="form-row"><div class="form-group"><label class="form-label">Month</label><select name="month" class="form-select"><?php for($m=1;$m<=12;$m++) echo '<option value="'.$m.'"'.($m==(int)date('n')?' selected':'').'>'.date('F',mktime(0,0,0,$m)).'</option>'; ?></select></div>
<div class="form-group"><label class="form-label">Year</label><input type="number" name="year" class="form-input" value="<?= date('Y') ?>"></div></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitDoc('#calendarForm')">Generate</button></div></div></div>

<!-- Certificate Modal -->
<div class="modal-overlay" id="certModal"><div class="modal"><div class="modal-header"><h3>Generate Club Certificate</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="certForm"><input type="hidden" name="action" value="generate_certificate">
<div class="form-group"><label class="form-label">Membership *</label><select name="membership_id" class="form-select" required><?= $memberOpts ?></select></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitDoc('#certForm')">Generate</button></div></div></div>

<!-- Badge Modal -->
<div class="modal-overlay" id="badgeModal"><div class="modal"><div class="modal-header"><h3>Generate Staff Badge</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="badgeForm"><input type="hidden" name="action" value="generate_badge">
<div class="form-group"><label class="form-label">Staff Member *</label><select name="user_id" class="form-select" required><?= $staffOpts ?></select></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitDoc('#badgeForm')">Generate</button></div></div></div>

<!-- Quote Modal -->
<div class="modal-overlay" id="quoteModal"><div class="modal modal-lg"><div class="modal-header"><h3>Generate Quote / Proposal</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div><div class="modal-body">
<form id="quoteForm"><input type="hidden" name="action" value="generate_quote">
<div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" class="form-input" placeholder="e.g. STEM Workshop Partnership" required></div>
<div class="form-group"><label class="form-label">School / Organization</label><input type="text" name="school_name" class="form-input"></div>
<div class="form-group"><label class="form-label">Scope of Work</label><textarea name="scope" class="form-textarea" rows="3"></textarea></div>
<h4 class="mt-2 mb-2" style="color:var(--tinker-teal);">Items</h4>
<div id="quoteItems">
    <div class="form-row q-item"><div class="form-group" style="flex:3;"><input type="text" name="q_desc" class="form-input" placeholder="Description"></div><div class="form-group" style="flex:1;"><input type="number" name="q_qty" class="form-input" placeholder="Qty" value="1"></div><div class="form-group" style="flex:1;"><input type="number" name="q_price" class="form-input" placeholder="Price"></div><div style="padding-top:28px;"><button type="button" class="btn btn-ghost btn-sm" onclick="$(this).closest('.q-item').remove()">✕</button></div></div>
</div>
<button type="button" class="btn btn-ghost btn-sm" onclick="$('#quoteItems').append($('#quoteItems .q-item:first').clone().find('input').val('').end())">+ Add Item</button>
<div class="form-group mt-2"><label class="form-label">Notes</label><textarea name="notes" class="form-textarea" rows="2"></textarea></div>
</form></div><div class="modal-footer"><button class="btn btn-ghost" data-modal-close>Cancel</button><button class="btn btn-primary" onclick="submitQuote()">Generate</button></div></div></div>

<script>
function submitDoc(formSel){
    TT.submitForm(formSel,'DocumentController.php',{onSuccess:r=>{
        if(r.success){
            TT.modal.close($(formSel).closest('.modal-overlay'));
            TT.toast('PDF generated!','success');
            if(r.data.path) window.open('<?= APP_URL ?>/'+r.data.path,'_blank');
        }
    }});
}
function genInvoicePrompt(){TT.toast('Use Finance → Create Invoice to generate invoices with auto-PDF.','info',4000);}
function genReceiptPrompt(){TT.toast('Receipts are generated automatically when you mark an invoice as paid.','info',4000);}
function submitQuote(){
    const items=[];$('#quoteItems .q-item').each(function(){const d=$(this).find('[name=q_desc]').val();if(d)items.push({description:d,quantity:$(this).find('[name=q_qty]').val()||1,unit_price:$(this).find('[name=q_price]').val()||0});});
    const d=TT.formData('#quoteForm');d.items=JSON.stringify(items);d.action='generate_quote';
    TT.api('DocumentController.php',d).done(r=>{if(r.success){TT.modal.close('#quoteModal');TT.toast('Quote generated!','success');if(r.data.path)window.open('<?= APP_URL ?>/'+r.data.path,'_blank');}});
}
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
