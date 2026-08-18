<?php $pageTitle='Payments'; $currentTab='payments'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<h2 class="mb-3" style="font-size:1.25rem;">Payments</h2>
<div class="bank-details-box mb-3">
    <h4>💳 Payment via Bank Transfer</h4>
    <p style="font-size:0.75rem;color:#666;margin-bottom:12px;">Transfer to any of the accounts below and include your invoice number as reference.</p>
    <div id="bankDetails"><div class="spinner spinner-sm"></div></div>
</div>
<h3 class="mb-2" style="font-size:1rem;">Invoice History</h3>
<div id="invoicesList"><div class="spinner mx-auto mt-3"></div></div>
<script>$(function(){ Parent.loadPayments(); });</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
