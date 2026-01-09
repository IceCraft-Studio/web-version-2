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

$filePath = resolveDataPath('project/' . $category . '/' . $project . '/upload/' . $fileName);

if (!file_exists($filePath)) {
    http_response_code(404);
} else {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
    exit;
}
