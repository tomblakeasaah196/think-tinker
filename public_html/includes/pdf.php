<?php
/**
 * includes/pdf.php
 * DomPDF wrapper — generates all branded PDF documents.
 * Templates live in /pdf-templates/ as PHP/HTML files with inline CSS.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'pdf.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Generate a PDF from a template and return the saved file path.
 *
 * @param string $template    Template name (without .php), e.g., 'invoice', 'receipt', 'contract'
 * @param array  $data        Data to pass to the template (extracted as variables)
 * @param string $filename    Output filename (without .pdf extension)
 * @param string $subDir      Subdirectory under uploads/ (e.g., 'invoices', 'receipts', 'contracts')
 * @param string $orientation 'portrait' or 'landscape'
 * @param string $paperSize   'A4', 'Letter', etc.
 * @return array              ['success' => bool, 'path' => string, 'message' => string]
 */
function generatePDF(
    string $template,
    array  $data,
    string $filename,
    string $subDir = '',
    string $orientation = 'portrait',
    string $paperSize = 'A4'
): array {
    $templatePath = ROOT_PATH . "/pdf-templates/{$template}.php";

    if (!file_exists($templatePath)) {
        return [
            'success' => false,
            'path'    => '',
            'message' => "PDF template '{$template}' not found.",
        ];
    }

    try {
        // Configure DomPDF options
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        $options->set('defaultMediaType', 'print');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('tempDir', ROOT_PATH . '/logs');
        $options->set('logOutputFile', ROOT_PATH . '/logs/dompdf.log');

        // Font directory (DomPDF built-in fonts)
        $options->set('fontDir', ROOT_PATH . '/vendor/dompdf/dompdf/lib/fonts');
        $options->set('fontCache', ROOT_PATH . '/vendor/dompdf/dompdf/lib/fonts');

        $dompdf = new Dompdf($options);

        // Inject common data that every template needs
        $data['business_name']    = getSetting('business_name', 'Think & Tinker Kids Club');
        $data['business_address'] = getSetting('business_address', '');
        $data['business_phone']   = getSetting('business_phone', '');
        $data['business_email']   = getSetting('business_email', '');
        $data['business_rc']      = getSetting('business_rc', '');
        $data['business_social']  = getSetting('business_social', '');
        $data['bank_accounts']    = getActiveBankAccounts();
        $data['generated_at']     = date('D, M jS Y · g:i A');

        // Render the template to HTML
        ob_start();
        extract($data, EXTR_SKIP);
        include $templatePath;
        $html = ob_get_clean();

        // Load HTML and render
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paperSize, $orientation);
        $dompdf->render();

        // Build output path with year/month subdirectories
        $yearMonth = date('Y/m');
        if ($subDir) {
            $outputDir = ROOT_PATH . "/uploads/{$subDir}/{$yearMonth}";
        } else {
            $outputDir = ROOT_PATH . "/uploads/{$yearMonth}";
        }

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . '/' . $filename . '.pdf';

        // Save to disk
        file_put_contents($outputPath, $dompdf->output());

        // Return the relative path (from public_html root)
        $relativePath = str_replace(ROOT_PATH . '/', '', $outputPath);

        return [
            'success' => true,
            'path'    => $relativePath,
            'message' => 'PDF generated successfully.',
        ];

    } catch (\Exception $e) {
        error_log("PDF generation failed ({$template}): " . $e->getMessage());
        return [
            'success' => false,
            'path'    => '',
            'message' => APP_DEBUG ? $e->getMessage() : 'PDF generation failed. Please try again.',
        ];
    }
}

/**
 * Generate a PDF and stream it directly to the browser (inline view or download).
 *
 * @param string $template    Template name
 * @param array  $data        Data for the template
 * @param string $filename    Download filename (with .pdf)
 * @param string $disposition 'inline' (view in browser) or 'attachment' (download)
 * @param string $orientation 'portrait' or 'landscape'
 */
function streamPDF(
    string $template,
    array  $data,
    string $filename = 'document.pdf',
    string $disposition = 'inline',
    string $orientation = 'portrait'
): void {
    $templatePath = ROOT_PATH . "/pdf-templates/{$template}.php";

    if (!file_exists($templatePath)) {
        http_response_code(404);
        exit('PDF template not found.');
    }

    try {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        $options->set('isFontSubsettingEnabled', true);
        $options->set('tempDir', ROOT_PATH . '/logs');

        $options->set('fontDir', ROOT_PATH . '/vendor/dompdf/dompdf/lib/fonts');
        $options->set('fontCache', ROOT_PATH . '/vendor/dompdf/dompdf/lib/fonts');

        $dompdf = new Dompdf($options);

        // Inject common data
        $data['business_name']    = getSetting('business_name', 'Think & Tinker Kids Club');
        $data['business_address'] = getSetting('business_address', '');
        $data['business_phone']   = getSetting('business_phone', '');
        $data['business_email']   = getSetting('business_email', '');
        $data['business_rc']      = getSetting('business_rc', '');
        $data['business_social']  = getSetting('business_social', '');
        $data['bank_accounts']    = getActiveBankAccounts();
        $data['generated_at']     = date('D, M jS Y · g:i A');

        ob_start();
        extract($data, EXTR_SKIP);
        include $templatePath;
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        // Stream to browser
        $dompdf->stream($filename, ['Attachment' => ($disposition === 'attachment')]);
        exit;

    } catch (\Exception $e) {
        error_log("PDF stream failed ({$template}): " . $e->getMessage());
        http_response_code(500);
        exit('PDF generation failed.');
    }
}

// ============================================================
// CONVENIENCE FUNCTIONS (specific document types)
// ============================================================

/**
 * Generate an invoice PDF.
 */
function generateInvoicePDF(array $invoice, array $items, array $parent): array
{
    return generatePDF('invoice', [
        'invoice' => $invoice,
        'items'   => $items,
        'parent'  => $parent,
    ], $invoice['invoice_number'], 'invoices');
}

/**
 * Generate a receipt PDF.
 */
function generateReceiptPDF(array $receipt, array $invoice, array $parent): array
{
    return generatePDF('receipt', [
        'receipt' => $receipt,
        'invoice' => $invoice,
        'parent'  => $parent,
    ], $receipt['receipt_number'], 'receipts');
}

/**
 * Generate a service contract PDF.
 */
function generateContractPDF(array $contract, array $parent, array $child): array
{
    return generatePDF('contract', [
        'contract' => $contract,
        'parent'   => $parent,
        'child'    => $child,
    ], $contract['contract_number'], 'contracts');
}

/**
 * Generate a monthly calendar PDF for a child.
 */
function generateMonthlyCalendarPDF(array $child, array $sessions, int $month, int $year): array
{
    $filename = "TTK-CAL-{$child['id']}-{$year}{$month}";
    return generatePDF('monthly-calendar', [
        'child'    => $child,
        'sessions' => $sessions,
        'month'    => $month,
        'year'     => $year,
    ], $filename, 'progress-reports');
}

/**
 * Generate a progress report PDF.
 */
function generateProgressReportPDF(array $child, array $progress, array $notes, string $period): array
{
    $filename = "TTK-RPT-{$child['id']}-" . date('Ymd');
    return generatePDF('progress-report', [
        'child'    => $child,
        'progress' => $progress,
        'notes'    => $notes,
        'period'   => $period,
    ], $filename, 'progress-reports');
}

/**
 * Generate a club membership certificate PDF (landscape).
 */
function generateClubCertificatePDF(array $child, array $membership): array
{
    $filename = "TTK-CERT-{$child['id']}-" . date('Ymd');
    return generatePDF('club-certificate', [
        'child'      => $child,
        'membership' => $membership,
    ], $filename, 'certificates', 'landscape');
}

/**
 * Generate a staff ID badge PDF.
 */
function generateStaffBadgePDF(array $user, array $staffRecord): array
{
    $filename = "TTK-BADGE-{$staffRecord['staff_id_number']}";
    return generatePDF('staff-id-badge', [
        'user'  => $user,
        'staff' => $staffRecord,
    ], $filename, 'certificates');
}

/**
 * Generate a quote/proposal PDF.
 */
function generateQuotePDF(array $quoteData): array
{
    $filename = "TTK-QUO-" . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return generatePDF('quote-proposal', $quoteData, $filename, 'invoices');
}
