<?php
/**
 * includes/mailer.php
 * PHPMailer wrapper — all outgoing email uses this.
 * Every email is wrapped in the branded Think & Tinker HTML template.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'mailer.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send a branded email.
 *
 * @param string      $toEmail     Recipient email
 * @param string      $toName      Recipient name
 * @param string      $subject     Email subject
 * @param string      $bodyContent Inner HTML content (placed inside branded template)
 * @param array       $attachments Array of file paths to attach
 * @return array                   ['success' => bool, 'message' => string]
 */
function sendEmail(
    string $toEmail,
    string $toName,
    string $subject,
    string $bodyContent,
    array  $attachments = []
): array {
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_ENCRYPTION === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Timeout settings for shared hosting
        $mail->Timeout    = 30;
        $mail->SMTPKeepAlive = false;

        // Sender
        $fromName  = getSetting('smtp_from_name', 'Think & Tinker Kids Club');
        $fromEmail = getSetting('smtp_from_email', SMTP_USER);
        $mail->setFrom($fromEmail, $fromName);
        $mail->addReplyTo(getSetting('business_email', $fromEmail), $fromName);

        // Recipient
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = buildEmailTemplate($subject, $bodyContent);
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $bodyContent));

        // Attachments
        foreach ($attachments as $filePath) {
            if (file_exists($filePath)) {
                $mail->addAttachment($filePath);
            }
        }

        $mail->send();

        return ['success' => true, 'message' => 'Email sent successfully.'];

    } catch (Exception $e) {
        error_log("Email failed to {$toEmail}: " . $mail->ErrorInfo);
        return [
            'success' => false,
            'message' => APP_DEBUG ? $mail->ErrorInfo : 'Email could not be sent. Please try again later.',
        ];
    }
}

/**
 * Build the branded HTML email template.
 * Wraps content in Think & Tinker branded header/footer.
 */
function buildEmailTemplate(string $title, string $content): string
{
    $businessName    = sanitize(getSetting('business_name', 'Think & Tinker Kids Club'));
    $businessAddress = sanitize(getSetting('business_address', ''));
    $businessPhone   = sanitize(getSetting('business_phone', ''));
    $businessEmail   = sanitize(getSetting('business_email', ''));
    $businessSocial  = sanitize(getSetting('business_social', ''));
    $year            = date('Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="margin:0; padding:0; background-color:#F2F3F4; font-family:'Segoe UI',Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F2F3F4; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#1AAFA0; padding:24px 32px; text-align:center; border-radius:8px 8px 0 0;">
                            <h1 style="margin:0; color:#FFFFFF; font-size:22px; font-weight:700; letter-spacing:0.5px;">
                                {$businessName}
                            </h1>
                            <p style="margin:4px 0 0; color:rgba(255,255,255,0.85); font-size:13px;">
                                Think. Tinker. Shine.
                            </p>
                        </td>
                    </tr>

                    <!-- Teal accent bar -->
                    <tr>
                        <td style="background-color:#F47B20; height:4px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color:#FFFFFF; padding:32px; font-size:15px; line-height:1.6; color:#2C3E50;">
                            {$content}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#1B2A4A; padding:24px 32px; text-align:center; border-radius:0 0 8px 8px;">
                            <p style="margin:0 0 8px; color:rgba(255,255,255,0.7); font-size:12px;">
                                {$businessAddress}
                            </p>
                            <p style="margin:0 0 8px; color:rgba(255,255,255,0.7); font-size:12px;">
                                {$businessPhone} · {$businessEmail}
                            </p>
                            <p style="margin:0; color:rgba(255,255,255,0.5); font-size:11px;">
                                © {$year} {$businessName}. All Rights Reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}

// ============================================================
// CONVENIENCE FUNCTIONS (common email types)
// ============================================================

/**
 * Send a welcome email to a newly registered parent.
 */
function sendWelcomeEmail(array $user, string $activationToken = ''): array
{
    $name = sanitize($user['first_name']);
    $activationLink = APP_URL . "/parent/login.php?activate={$activationToken}";

    $content = <<<HTML
<h2 style="margin:0 0 16px; color:#1AAFA0; font-size:20px;">Welcome, {$name}!</h2>
<p>Thank you for joining Think & Tinker Kids Club. We're excited to be part of your child's learning journey.</p>
<p>Click the button below to activate your account:</p>
<p style="text-align:center; margin:24px 0;">
    <a href="{$activationLink}" style="display:inline-block; background-color:#1AAFA0; color:#FFFFFF; padding:14px 32px; text-decoration:none; border-radius:8px; font-weight:600; font-size:15px;">
        Activate My Account
    </a>
</p>
<p style="color:#999; font-size:13px;">If the button doesn't work, copy and paste this link:<br>{$activationLink}</p>
HTML;

    return sendEmail($user['email'], $user['first_name'] . ' ' . $user['last_name'], 'Welcome to Think & Tinker!', $content);
}

/**
 * Send a password reset email.
 */
function sendPasswordResetEmail(string $email, string $name, string $token): array
{
    $resetLink = APP_URL . "/parent/login.php?reset={$token}&email=" . urlencode($email);

    $content = <<<HTML
<h2 style="margin:0 0 16px; color:#1AAFA0; font-size:20px;">Password Reset</h2>
<p>Hi {$name}, we received a request to reset your password. Click below to set a new one:</p>
<p style="text-align:center; margin:24px 0;">
    <a href="{$resetLink}" style="display:inline-block; background-color:#F47B20; color:#FFFFFF; padding:14px 32px; text-decoration:none; border-radius:8px; font-weight:600; font-size:15px;">
        Reset Password
    </a>
</p>
<p style="color:#999; font-size:13px;">This link expires in 1 hour. If you didn't request this, please ignore this email.</p>
HTML;

    return sendEmail($email, $name, 'Password Reset — Think & Tinker', $content);
}

/**
 * Send an invoice email with PDF attachment.
 */
function sendInvoiceEmail(array $parent, string $invoiceNumber, string $total, string $dueDate, string $pdfPath): array
{
    $name = sanitize($parent['first_name']);
    $formattedTotal = formatNaira($total);
    $formattedDate = formatDate($dueDate);
    $portalLink = APP_URL . '/parent/payments.php';

    // Get bank accounts for the email
    $banks = getActiveBankAccounts();
    $bankHtml = '';
    foreach ($banks as $bank) {
        $badge = $bank['is_primary'] ? ' <span style="color:#27AE60; font-size:11px;">✓ Recommended</span>' : '';
        $bankHtml .= "<p style='margin:4px 0; font-size:14px;'><strong>{$bank['bank_name']}</strong>{$badge}<br>"
            . "{$bank['account_name']}<br>"
            . "<span style='font-size:18px; font-weight:700; color:#1B2A4A;'>{$bank['account_number']}</span></p>";
    }

    $content = <<<HTML
<h2 style="margin:0 0 16px; color:#1AAFA0; font-size:20px;">Invoice {$invoiceNumber}</h2>
<p>Hi {$name}, here's your invoice summary:</p>
<table style="width:100%; margin:16px 0; font-size:14px;">
    <tr><td style="padding:8px 0; color:#666;">Invoice Number:</td><td style="padding:8px 0; font-weight:600;">{$invoiceNumber}</td></tr>
    <tr><td style="padding:8px 0; color:#666;">Amount Due:</td><td style="padding:8px 0; font-weight:700; font-size:18px; color:#1AAFA0;">{$formattedTotal}</td></tr>
    <tr><td style="padding:8px 0; color:#666;">Due Date:</td><td style="padding:8px 0; font-weight:600;">{$formattedDate}</td></tr>
</table>
<div style="background:#FEF5E7; padding:16px; border-radius:8px; margin:16px 0;">
    <p style="margin:0 0 8px; font-weight:600; color:#F47B20;">Bank Transfer Details:</p>
    {$bankHtml}
    <p style="margin:8px 0 0; font-size:12px; color:#666;">Please include <strong>{$invoiceNumber}</strong> as your payment reference.</p>
</div>
<p>The full invoice PDF is attached. You can also view it in your <a href="{$portalLink}" style="color:#1AAFA0;">Parent Portal</a>.</p>
HTML;

    return sendEmail(
        $parent['email'],
        $parent['first_name'] . ' ' . $parent['last_name'],
        "Invoice {$invoiceNumber} — Think & Tinker",
        $content,
        [$pdfPath]
    );
}

/**
 * Send a generic notification email.
 */
function sendNotificationEmail(string $email, string $name, string $title, string $message, string $actionUrl = '', string $actionText = ''): array
{
    $actionHtml = '';
    if ($actionUrl && $actionText) {
        $actionHtml = <<<HTML
<p style="text-align:center; margin:24px 0;">
    <a href="{$actionUrl}" style="display:inline-block; background-color:#1AAFA0; color:#FFFFFF; padding:14px 32px; text-decoration:none; border-radius:8px; font-weight:600;">
        {$actionText}
    </a>
</p>
HTML;
    }

    $content = <<<HTML
<h2 style="margin:0 0 16px; color:#1AAFA0; font-size:20px;">{$title}</h2>
<p>Hi {$name},</p>
<p>{$message}</p>
{$actionHtml}
HTML;

    return sendEmail($email, $name, "{$title} — Think & Tinker", $content);
}
