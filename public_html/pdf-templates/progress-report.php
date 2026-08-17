<?php
/**
 * pdf-templates/progress-report.php
 * Variables: $child, $progress (array), $notes (array), $period (string), + common business vars
 */
$age = floor((time() - strtotime($child['date_of_birth'])) / (365.25 * 86400));
$statusEmoji = ['mastered' => '●', 'in_progress' => '◐', 'not_started' => '○'];
$statusLabel = ['mastered' => 'Mastered', 'in_progress' => 'In Progress', 'not_started' => 'Not Started'];
$statusColor = ['mastered' => '#27AE60', 'in_progress' => '#F47B20', 'not_started' => '#999'];
// Group progress by subject
$grouped = [];
foreach ($progress as $p) { $grouped[$p['subject']][] = $p; }
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; font-size: 11px; color: #2C3E50; line-height: 1.5; }
    .header { background: #1AAFA0; color: #FFF; padding: 20px 32px; }
    .header h1 { font-size: 18px; margin-bottom: 2px; }
    .accent { background: #F47B20; height: 4px; }
    .body { padding: 24px 32px; }
    .title { font-size: 20px; color: #1B2A4A; font-weight: 700; margin-bottom: 4px; }
    .period { color: #666; font-size: 11px; margin-bottom: 16px; }
    .child-info { background: #F2F3F4; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; }
    .child-info strong { color: #1AAFA0; }
    .subject-section { margin-bottom: 14px; }
    .subject-title { background: #1B2A4A; color: #FFF; padding: 6px 12px; font-size: 11px; font-weight: 600; border-radius: 4px 4px 0 0; }
    .topic-table { width: 100%; border-collapse: collapse; }
    .topic-table td { padding: 6px 12px; border-bottom: 1px solid #EEE; font-size: 10px; }
    .topic-table td:last-child { width: 100px; text-align: center; font-weight: 600; }
    .legend { margin: 12px 0; font-size: 9px; color: #666; }
    .legend span { margin-right: 14px; }
    .notes-section { margin-top: 16px; }
    .notes-section h3 { color: #1AAFA0; font-size: 12px; margin-bottom: 8px; }
    .note-item { background: #FEF5E7; border-radius: 4px; padding: 10px 12px; margin-bottom: 8px; }
    .note-date { font-size: 9px; color: #999; }
    .footer { background: #1B2A4A; color: rgba(255,255,255,0.7); padding: 12px 32px; font-size: 8px; text-align: center; position: fixed; bottom: 0; left: 0; right: 0; }
</style>
</head>
<body>
<div class="header">
    <h1><?= htmlspecialchars($business_name) ?></h1>
    <small><?= htmlspecialchars($business_address) ?> · <?= htmlspecialchars($business_phone) ?></small>
</div>
<div class="accent"></div>
<div class="body">
    <div class="title">Progress Report</div>
    <div class="period"><?= htmlspecialchars($period) ?></div>

    <div class="child-info">
        <strong><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></strong> · Age <?= $age ?> ·
        <?= htmlspecialchars($child['current_school'] ?? 'N/A') ?> · <?= htmlspecialchars($child['grade_level'] ?? 'N/A') ?>
    </div>

    <div class="legend">
        <span style="color: #27AE60;">● Mastered</span>
        <span style="color: #F47B20;">◐ In Progress</span>
        <span style="color: #999;">○ Not Started</span>
    </div>

    <?php foreach ($grouped as $subject => $topics): ?>
    <div class="subject-section">
        <div class="subject-title"><?= htmlspecialchars(ucfirst($subject)) ?></div>
        <table class="topic-table">
            <?php foreach ($topics as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['topic']) ?></td>
                <td style="color: <?= $statusColor[$t['status']] ?? '#999' ?>;">
                    <?= $statusEmoji[$t['status']] ?? '○' ?> <?= $statusLabel[$t['status']] ?? $t['status'] ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($notes)): ?>
    <div class="notes-section">
        <h3>Recent Tutor Notes</h3>
        <?php foreach (array_slice($notes, 0, 5) as $note): ?>
        <div class="note-item">
            <div class="note-date"><?= date('M j, Y', strtotime($note['session_date'])) ?></div>
            <?= nl2br(htmlspecialchars(mb_substr($note['note_text'], 0, 300))) ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<div class="footer">
    <?= htmlspecialchars($business_name) ?> · <?= htmlspecialchars($business_email) ?> · Generated <?= $generated_at ?>
</div>
</body>
</html>
