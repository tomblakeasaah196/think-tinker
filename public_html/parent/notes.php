<?php $pageTitle='Session Notes'; $currentTab='home'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<h2 class="mb-3" style="font-size:1.25rem;">Session Notes</h2>
<div id="notesList"><div class="spinner mx-auto mt-4"></div></div>
<script>$(function(){ Parent.loadNotes(); });</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
