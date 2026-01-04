<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";
require_once __DIR__ . '/enums.php';

$viewState = ViewData::getInstance();

$username = $viewState->get('verified-username', '');
if ($username === '') {
    redirect('/~dobiapa2/login');
}

initCsrf();

$editCategory = $_GET['edit-category'] ?? '';
$editSlug = $_GET['edit-slug'] ?? '';
if ($editCategory === '' || $editSlug === '') {
    return;
}
$projectData = getProjectData($editCategory,$editSlug);
if ($projectData !== false) {
    $viewState->set('form-category',$editCategory);
    $viewState->set('form-slug',$editSlug);
    $viewState->set('form-editing','1');
} else {
    http_response_code(400);
    return;
}
