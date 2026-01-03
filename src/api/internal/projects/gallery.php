<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

# Validation
if (($_GET['project'] ?? '') === '' || ($_GET['category'] ?? '') === '' || ($_GET['file_name'] ?? '') === '') {
    http_response_code(400);
    exit;
}

$project = $_GET['project'];
$category = $_GET['category'];
$fileName = $_GET['file_name'];

$filePath = resolveDataPath('project/' . $category . '/' . $project . '/gallery/' . $fileName);

if (!file_exists($filePath)) {
    http_response_code(404);
} else {
    switch (getFileExt($filePath)) {
        case 'png':
            header('Content-Type: image/png');
            break;
        case 'jpeg':
        case 'jpg':
            header('Content-Type: image/jpeg');
            break;
        case 'webp':
            header('Content-Type: image/webp');
            break;
        case 'gif':
            header('Content-Type: image/gif');
            break;
    }
    readfile($filePath);
}
