<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

# Validation
if (!isset($_GET['project']) || $_GET['project'] === '' || !isset($_GET['category']) || $_GET['category'] === '') {
    http_response_code(400);
    exit;
}

$project = $_GET['project'];
$category = $_GET['category'];
$variant = $_GET['variant'] ?? 'full'; //  'full' or 'preview'

$image_path = resolveDataPath('project/' . $category . '/' . $project . '/thumbnail.webp');

if (file_exists($image_path)) {
    header('Content-Type: image/webp',true);
    readfile($image_path);
} else {
    header('Location: /~dobiapa2/assets/empty-thumbnail.webp', true, 302);
}