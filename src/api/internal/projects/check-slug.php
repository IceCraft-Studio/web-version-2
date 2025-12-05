<?php
/*
    API Endpoint - /api/internal/projects/check-slug
    Request Method - POST
    Input Parameters:
    url_path {string} - Part of the URL path one level above `/projects`.
    Response Parameters:
    available {boolean} - `true` if the URL path is available, else `false`.
    Example: {"url_path": "project-title"} checks for availability of `/projects/project-title`
*/

//?? Could define it by category in the future so /projects/java-datapacks/*project-title* in the future

//Common Headers
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'API Requests have to be made with the POST method!']);
    exit;
}

$rawJsonPostData = file_get_contents('php://input');
$postData = json_decode($rawJsonPostData, true);

if (isset($postData['url_path'])) {
    $url = $postData['url_path'];

    if ($url == "something") {
        echo json_encode(['available' => false]);
    } else {
        echo json_encode(['available' => true]);
    }
} else {
    http_response_code(400); 
    echo json_encode(['error' => 'Format of this request is `{"url_path": string}` The string is a part of the URL one level above `/projects`.']);
}