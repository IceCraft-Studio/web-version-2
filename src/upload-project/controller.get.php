<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";
require_once __DIR__ . '/enums.php';

$viewState = ViewData::getInstance();

$username = $viewState->get('verified-username', '');
if ($username == '') {
    redirect('/~dobiapa2/login');
}

initCsrf('upload-project');

// Check for editing query
$editCategory = $_GET['edit-category'] ?? '';
$editSlug = $_GET['edit-slug'] ?? '';
if ($editCategory == '' || $editSlug == '') {
    return;
}
// When editing get data
$projectData = getProjectData($editCategory,$editSlug);
if ($projectData === false) {
    http_response_code(400);
    return;
}

if ($projectData['username'] != $username) {
    http_response_code(401);
    return;
}

$viewState->set('form-category',$editCategory);
$viewState->set('form-slug',$editSlug);
$viewState->set('form-editing','1');
$viewState->set('form-title',$projectData['title'] ?? '');
$viewState->set('form-description',$projectData['description'] ?? '');

$projectArticleData = loadProjectArticle($editCategory,$editSlug);
if ($projectArticleData !== false) {
    $viewState->set('form-markdown-article',$projectArticleData['markdown'] ?? '');
}


$viewState->set('form-previous-gallery',loadProjectGalleryImages($editCategory,$editSlug) ?: []);
$viewState->set('form-previous-links',loadProjectLinks($editCategory,$editSlug) ?: []);
$viewState->set('form-previous-files',loadProjectFiles($editCategory,$editSlug) ?: []);

