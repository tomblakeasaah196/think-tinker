<?php
/**
 * includes/upload.php
 * File upload handler — image compression via GD, MIME validation, secure naming.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'upload.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

/**
 * Upload and process a file.
 *
 * @param string $fieldName  The form field name (e.g., 'cover_image', 'profile_photo')
 * @param string $subDir     Subdirectory under uploads/ (e.g., 'products', 'profiles', 'blog')
 * @param array  $options    Optional overrides:
 *                           - 'maxWidth'  => int    (default: 1200)
 *                           - 'maxSize'   => int    (max file size in bytes, default: 10MB)
 *                           - 'quality'   => int    (JPEG quality 1-100, default: 85)
 *                           - 'allowedTypes' => array (default: ['image/jpeg','image/png','image/webp'])
 *                           - 'skipResize' => bool  (skip image resizing, default: false)
 * @return array             ['success' => bool, 'path' => string, 'message' => string]
 */
function uploadFile(string $fieldName, string $subDir, array $options = []): array
{
    // Check if file was uploaded
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'path' => '', 'message' => 'No file selected.'];
    }

    $file = $_FILES[$fieldName];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds the server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temporary directory not found.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by server extension.',
        ];
        $msg = $errorMessages[$file['error']] ?? 'Unknown upload error.';
        return ['success' => false, 'path' => '', 'message' => $msg];
    }

    // Options with defaults
    $maxWidth     = $options['maxWidth']  ?? 1200;
    $maxSize      = $options['maxSize']   ?? (10 * 1024 * 1024); // 10MB
    $quality      = $options['quality']   ?? 85;
    $skipResize   = $options['skipResize'] ?? false;
    $allowedTypes = $options['allowedTypes'] ?? [
        'image/jpeg', 'image/png', 'image/webp',
    ];

    // Check file size
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1024 / 1024, 1);
        return ['success' => false, 'path' => '', 'message' => "File is too large. Maximum size is {$maxMB}MB."];
    }

    // Validate MIME type (server-side, not extension-based)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    // Also allow document types if specified
    $docTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedTypes) && !in_array($mimeType, $docTypes)) {
        return ['success' => false, 'path' => '', 'message' => 'File type not allowed. Accepted: JPEG, PNG, WebP.'];
    }

    // Generate secure filename: {hash}_{timestamp}.{ext}
    $extension = getExtensionFromMime($mimeType);
    $hash = substr(md5(uniqid(mt_rand(), true)), 0, 12);
    $timestamp = time();
    $newFilename = "{$hash}_{$timestamp}.{$extension}";

    // Build output path with year/month subdirectories
    $yearMonth = date('Y/m');
    $outputDir = ROOT_PATH . "/uploads/{$subDir}/{$yearMonth}";

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    $outputPath = $outputDir . '/' . $newFilename;

    // Process based on type
    $isImage = in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp']);

    if ($isImage && !$skipResize) {
        // Resize and compress image via GD
        $result = processImage($file['tmp_name'], $outputPath, $mimeType, $maxWidth, $quality);
        if (!$result) {
            return ['success' => false, 'path' => '', 'message' => 'Image processing failed.'];
        }
    } else {
        // Move file as-is (PDFs, documents, etc.)
        if (!move_uploaded_file($file['tmp_name'], $outputPath)) {
            return ['success' => false, 'path' => '', 'message' => 'Failed to save file.'];
        }
    }

    // Return relative path from public_html root
    $relativePath = "uploads/{$subDir}/{$yearMonth}/{$newFilename}";

    return [
        'success'   => true,
        'path'      => $relativePath,
        'filename'  => $newFilename,
        'mime'      => $mimeType,
        'size'      => filesize($outputPath),
        'message'   => 'File uploaded successfully.',
    ];
}

/**
 * Upload a profile photo with stricter size limits.
 */
function uploadProfilePhoto(string $fieldName, string $subDir = 'profiles'): array
{
    return uploadFile($fieldName, $subDir, [
        'maxWidth' => 400,
        'maxSize'  => 2 * 1024 * 1024, // 2MB
        'quality'  => 80,
    ]);
}

/**
 * Upload a product image for the bookstore.
 */
function uploadProductImage(string $fieldName): array
{
    return uploadFile($fieldName, 'products', [
        'maxWidth' => 800,
        'maxSize'  => 5 * 1024 * 1024, // 5MB
        'quality'  => 85,
    ]);
}

/**
 * Upload a blog featured image.
 */
function uploadBlogImage(string $fieldName): array
{
    return uploadFile($fieldName, 'blog', [
        'maxWidth' => 1200,
        'maxSize'  => 5 * 1024 * 1024, // 5MB
        'quality'  => 85,
    ]);
}

/**
 * Upload an expense receipt (photo of receipt).
 */
function uploadExpenseReceipt(string $fieldName): array
{
    return uploadFile($fieldName, 'expenses', [
        'maxWidth' => 1200,
        'maxSize'  => 5 * 1024 * 1024,
        'quality'  => 80,
    ]);
}

/**
 * Upload session images (photos from tutorial sessions).
 */
function uploadSessionImage(string $fieldName): array
{
    return uploadFile($fieldName, 'session-images', [
        'maxWidth' => 1200,
        'maxSize'  => 5 * 1024 * 1024,
        'quality'  => 85,
    ]);
}

/**
 * Upload a document file (PDF, DOCX — no image processing).
 */
function uploadDocument(string $fieldName, string $subDir = 'resources'): array
{
    return uploadFile($fieldName, $subDir, [
        'skipResize'   => true,
        'maxSize'      => 10 * 1024 * 1024, // 10MB
        'allowedTypes' => ['application/pdf'],
    ]);
}

/**
 * Save a canvas-captured signature image (base64 PNG from draw-to-sign).
 *
 * @param string $base64Data The base64-encoded PNG data (from canvas.toDataURL())
 * @return array             ['success' => bool, 'path' => string]
 */
function saveSignatureImage(string $base64Data): array
{
    // Strip the data URI prefix if present
    if (str_contains($base64Data, ',')) {
        $base64Data = explode(',', $base64Data, 2)[1];
    }

    $imageData = base64_decode($base64Data);
    if ($imageData === false) {
        return ['success' => false, 'path' => '', 'message' => 'Invalid signature data.'];
    }

    // Verify it's actually a PNG
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);

    if ($mimeType !== 'image/png') {
        return ['success' => false, 'path' => '', 'message' => 'Signature must be a PNG image.'];
    }

    // Generate filename and path
    $hash = substr(md5(uniqid(mt_rand(), true)), 0, 12);
    $filename = "sig_{$hash}_" . time() . '.png';
    $yearMonth = date('Y/m');
    $outputDir = ROOT_PATH . "/uploads/signatures/{$yearMonth}";

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    $outputPath = $outputDir . '/' . $filename;
    file_put_contents($outputPath, $imageData);

    $relativePath = "uploads/signatures/{$yearMonth}/{$filename}";

    return [
        'success' => true,
        'path'    => $relativePath,
        'message' => 'Signature saved.',
    ];
}

// ============================================================
// IMAGE PROCESSING (GD Library)
// ============================================================

/**
 * Resize and compress an image using GD library.
 *
 * @param string $sourcePath Source file path (tmp upload)
 * @param string $outputPath Destination file path
 * @param string $mimeType   MIME type of the source
 * @param int    $maxWidth   Maximum width in pixels
 * @param int    $quality    JPEG quality (1-100)
 * @return bool
 */
function processImage(string $sourcePath, string $outputPath, string $mimeType, int $maxWidth, int $quality): bool
{
    // Create GD image from source
    $source = match ($mimeType) {
        'image/jpeg' => @imagecreatefromjpeg($sourcePath),
        'image/png'  => @imagecreatefrompng($sourcePath),
        'image/webp' => @imagecreatefromwebp($sourcePath),
        default      => false,
    };

    if (!$source) {
        // GD failed — fall back to moving raw file
        return move_uploaded_file($sourcePath, $outputPath);
    }

    // Get dimensions
    $origWidth  = imagesx($source);
    $origHeight = imagesy($source);

    // Only resize if wider than maxWidth
    if ($origWidth > $maxWidth) {
        $ratio     = $maxWidth / $origWidth;
        $newWidth  = $maxWidth;
        $newHeight = (int) round($origHeight * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefill($resized, 0, 0, $transparent);
        }

        imagecopyresampled(
            $resized, $source,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $origWidth, $origHeight
        );

        imagedestroy($source);
        $source = $resized;
    }

    // Auto-rotate based on EXIF (JPEG only)
    if ($mimeType === 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($sourcePath);
        if ($exif && isset($exif['Orientation'])) {
            $source = applyExifOrientation($source, $exif['Orientation']);
        }
    }

    // Save as JPEG (always — even if source was PNG/WebP, for consistent compression)
    // Exception: keep PNG if transparency is needed
    if ($mimeType === 'image/png') {
        $result = imagepng($source, $outputPath, 8); // PNG compression level 0-9
    } else {
        // Ensure output extension is .jpg
        $outputPath = preg_replace('/\.\w+$/', '.jpg', $outputPath);
        $result = imagejpeg($source, $outputPath, $quality);
    }

    imagedestroy($source);

    // If original was a tmp file and we processed successfully, we don't need move_uploaded_file
    return $result;
}

/**
 * Apply EXIF orientation to an image (fixes rotated phone photos).
 */
function applyExifOrientation(\GdImage $image, int $orientation): \GdImage
{
    return match ($orientation) {
        3       => imagerotate($image, 180, 0),
        6       => imagerotate($image, -90, 0),
        8       => imagerotate($image, 90, 0),
        default => $image,
    };
}

/**
 * Get file extension from MIME type.
 */
function getExtensionFromMime(string $mimeType): string
{
    return match ($mimeType) {
        'image/jpeg'       => 'jpg',
        'image/png'        => 'png',
        'image/webp'       => 'webp',
        'image/gif'        => 'gif',
        'application/pdf'  => 'pdf',
        default            => 'bin',
    };
}

/**
 * Delete an uploaded file.
 *
 * @param string $relativePath Relative path from public_html (e.g., 'uploads/products/2026/06/abc123.jpg')
 * @return bool
 */
function deleteUploadedFile(string $relativePath): bool
{
    if (empty($relativePath)) {
        return false;
    }

    $fullPath = ROOT_PATH . '/' . $relativePath;

    if (file_exists($fullPath) && str_contains($fullPath, '/uploads/')) {
        return unlink($fullPath);
    }

    return false;
}

/**
 * Get the public URL for an uploaded file.
 */
function getUploadUrl(string $relativePath): string
{
    if (empty($relativePath)) {
        return APP_URL . '/assets/img/defaults/placeholder.png';
    }
    return APP_URL . '/' . $relativePath;
}

/**
 * Get human-readable file size.
 */
function formatFileSize(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' bytes';
}
