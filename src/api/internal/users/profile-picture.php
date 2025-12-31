<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

# Validation
if (!isset($_GET['username']) || $_GET['username'] === '') {
    http_response_code(400);
    exit;
}

$username = $_GET['username'];
$variant = $_GET['variant'] ?? 'full'; //  'full' or 'preview'

$image_path = resolveDataPath('user/' . $username . '/profile-picture.webp');

if (file_exists($image_path)) {
    header('Content-Type: image/webp',true);
    readfile($image_path);
} else {
    header('Location: /~dobiapa2/assets/icons/default-steve.webp', true, 302);
}
