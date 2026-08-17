<?php
/**
 * pdf-templates/club-certificate.php (Landscape A4)
 * Variables: $child, $membership, + common business vars
 */
$planLabels = ['trial' => 'Trial', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'biannual' => 'Bi-Annual'];
$planLabel = $planLabels[$membership['plan']] ?? ucfirst($membership['plan']);
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">
<style>
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: sans-serif; color: #2C3E50; }
    .certificate { width: 100%; min-height: 595px; position: relative; padding: 0; }
    .border-frame { border: 8px solid #1AAFA0; margin: 16px; padding: 24px; min-height: 555px; position: relative; }
    .inner-border { border: 2px solid #F47B20; padding: 32px 40px; min-height: 505px; }
    .corner { position: absolute; width: 30px; height: 30px; }
    .cert-header { text-align: center; margin-bottom: 12px; }
    .cert-header h1 { font-size: 16px; color: #1AAFA0; letter-spacing: 2px; margin-bottom: 4px; }
    .cert-header .divider { width: 80px; height: 3px; background: #F47B20; margin: 6px auto; }
    .cert-title { text-align: center; margin: 16px 0; }
    .cert-title h2 { font-size: 32px; color: #1B2A4A; letter-spacing: 3px; }
    .cert-subtitle { color: #666; font-size: 12px; margin-top: 6px; }
    .cert-name { text-align: center; margin: 20px 0; }
    .cert-name .name { font-size: 36px; color: #1AAFA0; font-weight: 700; font-style: italic; border-bottom: 2px solid #F47B20; display: inline-block; padding: 0 20px 4px; }
    .cert-body { text-align: center; font-size: 13px; line-height: 1.8; color: #444; margin: 16px 40px; }
    .cert-details { text-align: center; margin: 16px 0; font-size: 11px; color: #666; }
    .cert-footer { width: 100%; margin-top: 20px; }
    .cert-footer td { width: 33%; text-align: center; vertical-align: bottom; padding: 0 16px; }
    .cert-footer .line { border-top: 1px solid #999; margin-top: 30px; padding-top: 4px; font-size: 10px; color: #666; }
    .seal { width: 60px; height: 60px; border: 3px solid #1AAFA0; border-radius: 50%; text-align: center; line-height: 54px; font-size: 8px; color: #1AAFA0; font-weight: 700; margin: 0 auto; letter-spacing: 1px; }
</style>
</head>
<body>
<div class="certificate">
    <div class="border-frame">
        <div class="inner-border">

            <div class="cert-header">
                <h1><?= strtoupper(htmlspecialchars($business_name)) ?></h1>
                <div class="divider"></div>
            </div>

            <div class="cert-title">
                <h2>CERTIFICATE</h2>
                <div class="cert-subtitle">of Participation</div>
            </div>

            <div class="cert-body">This is proudly presented to</div>

            <div class="cert-name">
                <span class="name"><?= htmlspecialchars($child['first_name'] . ' ' . $child['last_name']) ?></span>
            </div>

            <div class="cert-body">
                for successful participation in the <strong>STEM & Reading Club</strong> (<?= $planLabel ?> Plan)<br>
                from <?= date('M j, Y', strtotime($membership['start_date'])) ?> to <?= date('M j, Y', strtotime($membership['end_date'])) ?>
            </div>

            <div class="cert-details">
                Think. Tinker. Shine. — Building curious minds through play, creativity, and exploration.
            </div>

            <table class="cert-footer">
                <tr>
                    <td>
                        <div class="line">Date Issued<br><?= date('M j, Y') ?></div>
                    </td>
                    <td>
                        <div class="seal">THINK &<br>TINKER</div>
                    </td>
                    <td>
                        <div class="line">Director<br><?= htmlspecialchars($business_name) ?></div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>
</body>
</html>
