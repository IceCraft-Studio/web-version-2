<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/classes/db-connect.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';
/*
    API Endpoint - /api/internal/users/verify-username
    Request Method - POST
    Input Parameters:
    username {string} - The username to check for its availability.
    Response Parameters:
    available {boolean} - `true` if the username is available , else `false`.
    Example: {"username": "john-doe"} checks for availability of username `john-doe`.
*/

//Common Headers
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'API Requests have to be made with the POST method!']);
    exit;
}

$rawJsonPostData = file_get_contents('php://input');
$postData = json_decode($rawJsonPostData, true);

if (isset($postData['username'])) {
    $available = (getUserData($username) === false) ? true : false;
    
    echo json_encode(['available' => $available]);
} else {
    http_response_code(400); 
    echo json_encode(['error' => 'Format of this request is `{"username": string}` The string is the username.']);
}