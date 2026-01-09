<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/db-setup.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/helpers.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';

# Validation
if (($_POST['slug'] ?? '') === '' || ($_POST['category'] ?? '') === '') {
    http_response_code(400);
    exit;
}

$slug = $_POST['slug'];
$category = $_POST['category'];

$projectData = getProjectData($category,$project);

if ($projectData === false) {
    http_response_code(404);
    exit;
}

$username = verifySession($_COOKIE['token'] ?? '');

if ($username == false) {
    http_response_code(401);
    exit;
}

if ($username != $projectData['username']) {
    $userData = getUserData($username);
    if (!($userData['role'] == UserRole::Admin->value || $userData['role'] == UserRole::Owner->value)) {
        http_response_code(401);
        exit;
    }
}

if (deleteProject($category,$slug)) {
    http_response_code(500);
};
