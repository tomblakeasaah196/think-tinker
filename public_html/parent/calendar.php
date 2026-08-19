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
        if(!sessions.length){$('#calSessions').html('<div class="empty-state"><div class="icon-badge"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16" rx="2" ry="2"/><line x1="16" y1="2.5" x2="16" y2="6.5"/><line x1="8" y1="2.5" x2="8" y2="6.5"/><line x1="3" y1="9.5" x2="21" y2="9.5"/></svg></div><p>No sessions this month.</p></div>');return;}
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
