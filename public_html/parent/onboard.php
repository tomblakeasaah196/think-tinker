<?php $pageTitle='Welcome'; $currentTab='home'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<div style="max-width:560px;margin:0 auto;">
    <div class="wizard-steps"><div class="wizard-step active" data-step="1">1</div><div class="wizard-step" data-step="2">2</div><div class="wizard-step" data-step="3">3</div></div>

    <div class="wizard-panel active" id="step1">
        <div class="card"><div class="card-body" style="text-align:center;">
            <h2 class="mb-2">Welcome to Think & Tinker! 🎉</h2>
            <p class="text-muted mb-3">Let's set up your child's profile so we can start their learning journey.</p>
            <button class="btn btn-primary" onclick="goStep(2)">Get Started →</button>
        </div></div>
    </div>

    <div class="wizard-panel" id="step2">
        <div class="card"><div class="card-body">
            <h3 class="mb-3">Add Your Child</h3>
            <form id="childForm">
                <input type="hidden" name="action" value="add_child"><input type="hidden" name="_token" value="<?= CSRF_TOKEN ?>">
                <div class="form-row"><div class="form-group"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-input" required></div><div class="form-group"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-input" required></div></div>
                <div class="form-row"><div class="form-group"><label class="form-label">Date of Birth *</label><input type="date" name="date_of_birth" class="form-input" required></div><div class="form-group"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="male">Male</option><option value="female">Female</option></select></div></div>
                <div class="form-group"><label class="form-label">Current School</label><input type="text" name="current_school" class="form-input"></div>
                <div class="form-group"><label class="form-label">Grade / Class</label><input type="text" name="grade_level" class="form-input" placeholder="e.g. Primary 3"></div>
                <div class="form-group"><label class="form-label">Medical Notes / Allergies</label><textarea name="medical_notes" class="form-textarea" rows="2"></textarea></div>
                <button type="submit" class="btn btn-primary btn-block">Save & Continue →</button>
            </form>
        </div></div>
    </div>

    <div class="wizard-panel" id="step3">
        <div class="card"><div class="card-body" style="text-align:center;">
            <div class="icon-badge icon-badge--lg" style="background:rgba(39,174,96,0.1);color:var(--leaf-green);"><?= ttIcon('check', 'tt-icon', 44) ?></div>
            <h2 class="mb-2">You're All Set!</h2>
            <p class="text-muted mb-3">Your child's profile is ready. You'll receive session schedules, progress notes, and invoices right here.</p>
            <a href="<?= APP_URL ?>/parent/" class="btn btn-primary">Go to Dashboard</a>
            <p class="mt-3 text-sm"><a href="<?= whatsappLink("Hi! I just registered on Think & Tinker. I'd like to discuss services for my child.") ?>" target="_blank" style="color:var(--leaf-green);">💬 Chat with us on WhatsApp</a></p>
        </div></div>
    </div>
</div>

<script>
function goStep(n){$('.wizard-panel').removeClass('active');$('#step'+n).addClass('active');$('.wizard-step').each(function(){const s=$(this).data('step');$(this).toggleClass('active',s==n).toggleClass('done',s<n);});}
$('#childForm').on('submit',function(e){e.preventDefault();TT.submitForm('#childForm','ChildController.php',{onSuccess:r=>{if(r.success){TT.toast('Child profile saved!','success');goStep(3);}}});});
</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
