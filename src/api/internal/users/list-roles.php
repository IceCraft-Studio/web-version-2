<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/db-setup.php";
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';

/*
    API Endpoint - /api/internal/user/list-roles
    Request Method - POST
    Input Parameters:
    None
    Response Parameters:
    roles {string[]} - String array of all available roles.
*/

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'This API Request requires the POST method!']);
    exit;
}

$rawJsonPostData = file_get_contents('php://input');
$postData = json_decode($rawJsonPostData, true);

$roles = getRoles();
if ($roles === false) {
    http_response_code(500);
    json_encode(["error" => "Database Error"]);
    exit;
}

echo json_encode(["roles" => $roles]);