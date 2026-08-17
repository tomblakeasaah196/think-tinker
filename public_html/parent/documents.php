<?php $pageTitle='Documents'; $currentTab='home'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<h2 class="mb-3" style="font-size:1.25rem;">My Documents</h2>
<p class="text-muted text-sm mb-3">All your contracts, invoices, receipts, and certificates in one place.</p>
<div id="docsList"><div class="spinner mx-auto mt-4"></div></div>
<script>$(function(){ Parent.loadDocuments(); });</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
