<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";

initCsrf();
$viewState = ViewData::getInstance();

$editCategory = $_GET['edit-category'];
$editSlug = $_GET['edit-slug'];

$projectData = getProjectData($editCategory,$editSlug);
if ($projectData !== false) {
    $viewState->set('project-edit-category',$editCategory);
    $viewState->set('project-edit-slug',$editSlug);
}
