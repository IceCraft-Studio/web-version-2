<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

$defaultThumbnail = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/assets/empty-thumbnail.webp';

# Validation
if (($_GET['project'] ?? '') == '' || ($_GET['category'] ?? '') == '') {
    http_response_code(400);
    exit;
}

$project = $_GET['project'];
$category = $_GET['category'];
$variant = $_GET['variant'] ?? 'full'; //  'full' or 'preview'

$variantPart = $variant == 'preview' ? '-preview' : '';

$image_path = resolveDataPath('project/' . $category . '/' . $project . '/thumbnail' . $variantPart . '.webp');

header('Content-Type: image/webp');
if (file_exists($image_path)) {
    readfile($image_path);
} else {
    readfile($defaultThumbnail);
}
