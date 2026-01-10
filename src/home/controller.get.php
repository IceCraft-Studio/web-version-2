<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";

$viewState = ViewData::getInstance();

$projectsList = getProjectList(1, 2, ['category' => '', 'username' => ''], ProjectSort::Created, false);
if ($projectsList === false) {
    $projectsList = [];
}
$viewState->set('projects-list', $projectsList);