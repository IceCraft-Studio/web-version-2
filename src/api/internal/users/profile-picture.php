<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

$defaultPicture = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/assets/icons/default-steve.webp';

# Validation
if (($_GET['username'] ?? '') == '') {
    http_response_code(400);
    exit;
}

$username = $_GET['username'];
$variant = $_GET['variant'] ?? 'full'; //  'full' or 'preview'

$variantPart = $variant == 'preview' ? '-preview' : '';

$image_path = resolveDataPath('user/' . $username . '/profile-picture' . $variantPart . '.webp');

header('Content-Type: image/webp', true);
if (file_exists($image_path)) {
    readfile($image_path);
} else {
    readfile($defaultPicture);
}
