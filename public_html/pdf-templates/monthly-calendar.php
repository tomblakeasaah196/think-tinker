<?php
/**
 * pdf-templates/monthly-calendar.php
 * Variables: $child, $sessions (array), $month (int), $year (int), + common business vars
 */
$monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
$daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
$firstDayOfWeek = (int) date('N', mktime(0, 0, 0, $month, 1, $year)); // 1=Mon
// Index sessions by date
$sessionsByDate = [];
foreach ($sessions as $s) {
    $day = (int) date('j', strtotime($s['session_date']));
    $sessionsByDate[$day][] = $s;
}
$statusColors = ['scheduled' => '#3498DB', 'completed' => '#27AE60', 'cancelled' => '#E8614D', 'rescheduled' => '#F47B20'];
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 10px; color: #2C3E50; }
    .header { background: #1AAFA0; color: #FFF; padding: 16px 24px; }
    .header h1 { font-size: 16px; }
    .accent { background: #F47B20; height: 3px; }
    .body { padding: 16px 24px; }
    .cal-title { font-size: 22px; color: #1B2A4A; font-weight: 700; text-align: center; margin: 8px 0; }
    .cal-child { text-align: center; color: #666; font-size: 11px; margin-bottom: 12px; }
    .calendar { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .calendar th { background: #1B2A4A; color: #FFF; padding: 6px; text-align: center; font-size: 9px; }
    .calendar td { border: 1px solid #DDD; height: 70px; vertical-align: top; padding: 3px 4px; }
    .day-num { font-weight: 700; font-size: 11px; color: #999; }
    .session-dot { display: inline-block; padding: 2px 5px; border-radius: 3px; color: #FFF; font-size: 8px; margin-top: 2px; }
    .empty { background: #F8F8F8; }
    .legend { margin-top: 12px; font-size: 9px; }
    .legend span { margin-right: 12px; }
    .legend .dot { display: inline-block; width: 8px; height: 8px; border-radius: 2px; margin-right: 3px; vertical-align: middle; }
    .footer { background: #1B2A4A; color: rgba(255,255,255,0.7); padding: 10px 24px; font-size: 8px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
</style>
</head>
<body>
<div class="header"><h1><?= htmlspecialchars($business_name) ?></h1></div>
<div class="accent"></div>
<div class="body">
    <div class="cal-title"><?= $monthName ?> <?= $year ?></div>
    <div class="cal-child"><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?> · Session Calendar</div>

    <table class="calendar">
        <thead><tr><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th></tr></thead>
        <tbody>
        <?php
        $dayCounter = 1;
        for ($row = 0; $row < 6; $row++):
            if ($dayCounter > $daysInMonth) break;
        ?>
        <tr>
            <?php for ($col = 1; $col <= 7; $col++):
                if (($row === 0 && $col < $firstDayOfWeek) || $dayCounter > $daysInMonth):
            ?>
                <td class="empty"></td>
            <?php else: ?>
                <td>
                    <div class="day-num"><?= $dayCounter ?></div>
                    <?php if (isset($sessionsByDate[$dayCounter])):
                        foreach ($sessionsByDate[$dayCounter] as $sess):
                            $bg = $statusColors[$sess['status']] ?? '#999';
                    ?>
                    <div class="session-dot" style="background: <?= $bg ?>;">
                        <?= $sess['start_time'] ? date('g:iA', strtotime($sess['start_time'])) : '' ?>
                        <?= htmlspecialchars($sess['tutor_name'] ?? '') ?>
                    </div>
                    <?php endforeach; endif; ?>
                </td>
                <?php $dayCounter++; ?>
            <?php endif; endfor; ?>
        </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <div class="legend">
        <span><span class="dot" style="background: #3498DB;"></span> Scheduled</span>
        <span><span class="dot" style="background: #27AE60;"></span> Completed</span>
        <span><span class="dot" style="background: #E8614D;"></span> Cancelled</span>
        <span><span class="dot" style="background: #F47B20;"></span> Rescheduled</span>
    </div>
</div>
<div class="footer"><?= htmlspecialchars($business_name) ?> · Generated <?= $generated_at ?></div>
</body>
</html>
