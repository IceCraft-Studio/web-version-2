<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
/*
    API Endpoint - /api/internal/projects/verify-slug
    Request Method - POST
    Input Parameters:
    category {string} - The first segment of the url after `/projects`.
    slug {string} - The last segment of the url after `/projects`.
    Response Parameters:
    available {boolean} - `true` if the URL path is available, else `false`.
    Example: {"category": "apps","slug": "project-title"} checks for availability of `/projects/apps/project-title`
*/

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'API Requests have to be made with the POST method!']);
    exit;
}

$rawJsonPostData = file_get_contents('php://input');
$postData = json_decode($rawJsonPostData, true);

if (isset($postData['category']) && isset($postData['slug'])) {
    $category = $postData['category'];
    $slug = $postData['slug'];

    $available = (getProjectData($category,$slug) === false) ? true : false;
    
    echo json_encode(['available' => $available]);
} else {
    http_response_code(400); 
    echo json_encode(['error' => 'Format of this request is `{"category": string, "slug": string}`.']);
}