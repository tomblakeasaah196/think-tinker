<?php
$pageTitle = 'Children';
$currentModule = 'clients';
require_once __DIR__ . '/../templates/header-hub.php';
?>
<div class="page-header"><h1>Student Profiles</h1></div>

<div class="hub-card"><div class="hub-card-body no-pad">
    <table class="hub-table"><thead><tr><th>Student</th><th>Parent</th><th>Age</th><th>Service</th><th>Status</th><th></th></tr></thead>
    <tbody id="childrenTable"></tbody></table>
</div></div>

<div class="modal-overlay" id="progressModal"><div class="modal modal-lg">
    <div class="modal-header"><h3 id="progressTitle">Progress</h3><button type="button" class="modal-close" aria-label="Close">&times;</button></div>
    <div class="modal-body" id="progressBody"></div>
</div></div>

<script>
$(function(){
    TT.get('ChildController.php',{action:'get_children'}).done(function(r){
        if(!r.success)return;
        if(!r.data.children.length){$('#childrenTable').html('<tr><td colspan="6"><div class="empty-state"><p>No students yet.</p></div></td></tr>');return;}
        let html='';r.data.children.forEach(c=>{
            const age=c.date_of_birth?Math.floor((Date.now()-new Date(c.date_of_birth))/(365.25*86400000)):'?';
            html+=`<tr><td><strong>${TT.escHtml(c.first_name+' '+c.last_name)}</strong></td><td>${TT.escHtml((c.parent_first||'')+' '+(c.parent_last||''))}</td><td>${age}</td><td>${c.active_service?`<span class="badge badge-teal">${c.active_service.replace('_',' ')}</span>`:'-'}</td><td>${c.status}</td>
            <td class="row-actions"><button class="btn btn-ghost btn-sm" onclick="viewProgress(${c.id},'${TT.escHtml(c.first_name)}')">Progress</button></td></tr>`;
        });
        $('#childrenTable').html(html);
    });
});

function viewProgress(childId,name){
    $('#progressTitle').text(name+' — Progress');
    TT.get('ChildController.php',{action:'get_progress',child_id:childId}).done(function(r){
        if(!r.success)return;
        const p=r.data.progress;
        if(!Object.keys(p).length){$('#progressBody').html('<div class="empty-state"><p>No progress records yet.</p></div>');TT.modal.open('#progressModal');return;}
        let html='';
        Object.entries(p).forEach(([subj,topics])=>{
            html+=`<h4 style="margin:12px 0 8px; color:var(--deep-navy);">${subj}</h4>`;
            topics.forEach(t=>{
                const colors={mastered:'var(--leaf-green)',in_progress:'var(--spark-orange)',not_started:'#999'};
                const icons={mastered:'●',in_progress:'◐',not_started:'○'};
                html+=`<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #F0F0F0;font-size:0.875rem;"><span>${TT.escHtml(t.topic)}</span><span style="color:${colors[t.status]||'#999'};font-weight:700;">${icons[t.status]||'○'} ${t.status.replace('_',' ')}</span></div>`;
            });
        });
        $('#progressBody').html(html);
        TT.modal.open('#progressModal');
    });
}
</script>
<?php require_once __DIR__ . '/../templates/footer-hub.php'; ?>
