<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';

# Validation
if (($_GET['project'] ?? '') == '' || ($_GET['category'] ?? '') == '' || ($_GET['file_name'] ?? '') == '') {
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
    $imageInfo = getimagesize($filePath);
    if ($imageInfo === false) {
        http_response_code(404);
        exit;
    }
    $imageType = $imageInfo[2];

    switch ($imageType) {
        case IMAGETYPE_GIF:
            header('Content-Type: image/gif');
            break;
        case IMAGETYPE_WEBP:
            header('Content-Type: image/webp');
            break;
        default:
            http_response_code(404);
            exit;
    }

    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
}
