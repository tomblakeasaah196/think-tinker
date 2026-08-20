<?php $pageTitle='Dashboard'; $currentTab='home'; require_once __DIR__.'/../templates/header-parent.php'; ?>

<div id="childSwitcher" class="child-switcher" style="display:none;"></div>

<!-- Opens the single Think & Tinker thread directly with the composer
     focused. Not a second system: ?new=1 just focuses #waInput on arrival. -->
<a href="<?= APP_URL ?>/parent/messages.php?new=1" class="btn btn-primary btn-block ask-question-cta">
    <?= ttIcon('message', 'tt-icon', 18) ?> Ask a Question
</a>

<div id="parentStats" class="parent-stats"></div>

<h3 class="mb-2" style="font-size:1rem;">Upcoming Sessions</h3>
<div id="upcomingSessions"><div class="spinner mx-auto mt-3"></div></div>

<div id="clubCard" style="display:none; margin-top:var(--space-3);"></div>

<h3 class="mt-3 mb-2" style="font-size:1rem;">Latest Session Note</h3>
<div id="latestNote"><p class="text-muted text-sm">No notes yet.</p></div>

<script>$(function(){ Parent.loadDashboard(); });</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
