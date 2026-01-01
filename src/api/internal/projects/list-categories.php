<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT']. "/api/libs/secure/database-env.php";
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/classes/db-connect.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
/*
    API Endpoint - /api/internal/projects/categories
    Request Method - POST
    Input Parameters:
    None
    Response Parameters:
    categories {string[]} - String array of all available categories.
*/

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'API Requests have to be made with the POST method!']);
    exit;
}

$rawJsonPostData = file_get_contents('php://input');
$postData = json_decode($rawJsonPostData, true);

$categories = getCategories();
if ($categories === false) {
    http_response_code(500);
    json_encode(["error" => "Database Error"]);
    exit;
}

echo json_encode(["categories" => $categories]);