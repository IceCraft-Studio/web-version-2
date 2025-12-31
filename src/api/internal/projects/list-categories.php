<?php
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

// TODO Save in SQL database
$categories = [
    ["id" => "bedrock-addon", "displayName" => "MC Bedrock - Add-on"],
    ["id" => "bedrock-map", "displayName" => "MC Bedrock - Map"],
    ["id" => "java-map", "displayName" => "MC Java - Map"],
    ["id" => "java-mod", "displayName" => "MC Java - Mod"],
    ["id" => "java-datapack", "displayName" => "MC Java - Datapack"],
    ["id" => "vscode-extension", "displayName" => "VSCode - Extension"],
    ["id" => "steam-workshop", "displayName" => "Steam - Workshop Item"]
];

echo json_encode(["categories" => $categories]);