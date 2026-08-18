<?php $pageTitle='Calendar'; $currentTab='calendar'; require_once __DIR__.'/../templates/header-parent.php'; ?>
<h2 class="mb-2" style="font-size:1.25rem;">Session Calendar</h2>
<div class="flex items-center justify-between mb-3">
    <button class="btn btn-ghost btn-sm" onclick="changeMonth(-1)">← Prev</button>
    <h3 id="calMonth" style="font-size:1rem;"></h3>
    <button class="btn btn-ghost btn-sm" onclick="changeMonth(1)">Next →</button>
</div>
<div id="calSessions"><div class="spinner mx-auto mt-4"></div></div>
<script>
let calMonth=<?= (int)date('n') ?>, calYear=<?= (int)date('Y') ?>;
function loadCal(){
    const months=['','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $('#calMonth').text(months[calMonth]+' '+calYear);
    TT.get('SessionController.php',{action:'get_calendar',month:calMonth,year:calYear}).done(function(r){
        if(!r.success)return;
        const sessions=r.data.sessions||[];
        if(!sessions.length){$('#calSessions').html('<div class="empty-state"><div class="icon">📅</div><p>No sessions this month.</p></div>');return;}
        let html='';sessions.forEach(s=>{
            const dt=new Date(s.session_date);
            const cls={scheduled:'',completed:'completed',cancelled:'cancelled'}[s.status]||'';
            html+=`<div class="session-card ${cls}"><div class="session-date-box"><div class="day">${dt.getDate()}</div><div class="month">${dt.toLocaleString('en',{weekday:'short'})}</div></div><div class="session-info"><div class="tutor">${TT.escHtml(s.child_name)} with ${TT.escHtml(s.tutor_name)}</div><div class="time">${s.start_time||'Time TBD'} · <span class="badge ${s.status==='completed'?'badge-green':s.status==='cancelled'?'badge-red':'badge-blue'}">${s.status}</span></div>${s.has_note>0?'<div class="note-badge">📝 Note available</div>':''}</div></div>`;
        });$('#calSessions').html(html);
    });
}
function changeMonth(d){calMonth+=d;if(calMonth>12){calMonth=1;calYear++;}if(calMonth<1){calMonth=12;calYear--;}loadCal();}
$(function(){loadCal();});
</script>
<?php require_once __DIR__.'/../templates/footer-parent.php'; ?>
