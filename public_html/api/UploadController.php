<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
switch ($action) {
    case 'upload_image':      uploadImg(); break;
    case 'upload_document':   uploadDoc(); break;
    case 'upload_signature':  uploadSig(); break;
    case 'delete_file':       deleteFile(); break;
    default: jsonResponse(false, 'Invalid action.');
}

function uploadImg(): void {
    $user = requireAuth(); validateCsrf();
    $type = post('type') ?: 'general';
    $result = match($type) {
        'product' => uploadProductImage('file'),
        'profile' => uploadProfilePhoto('file'),
        'blog' => uploadBlogImage('file'),
        'expense' => uploadExpenseReceipt('file'),
        'session' => uploadSessionImage('file'),
        'child' => uploadProfilePhoto('file', 'children'),
        default => uploadFile('file', 'blog'),
    };
    if ($result['success']) { $result['url'] = getUploadUrl($result['path']); }
    jsonResponse($result['success'], $result['message'], $result);
}

function uploadDoc(): void {
    $user = requireAuth(); validateCsrf();
    $result = uploadDocument('file', post('subdir') ?: 'resources');
    if ($result['success']) { $result['url'] = getUploadUrl($result['path']); }
    jsonResponse($result['success'], $result['message'], $result);
}

function uploadSig(): void {
    $user = requireAuth(); validateCsrf();
    $base64 = $_POST['signature_data'] ?? '';
    if (!$base64) jsonResponse(false, 'Signature data required.');
    $result = saveSignatureImage($base64);
    if ($result['success']) { $result['url'] = getUploadUrl($result['path']); }
    jsonResponse($result['success'], $result['message'], $result);
}

function deleteFile(): void {
    $user = requireAuth(); validateCsrf();
    require_once __DIR__ . '/../includes/rbac.php';
    requirePermission($user, 'settings', 'edit');
    $path = post('path');
    if (!$path) jsonResponse(false, 'File path required.');
    $deleted = deleteUploadedFile($path);
    jsonResponse($deleted, $deleted ? 'File deleted.' : 'File not found or could not be deleted.');
}
