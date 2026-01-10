<?php

$viewState = ViewData::getInstance();

$projectsList = getProjectList(1, 2, ['category' => '', 'username' => ''], ProjectSort::Created, false);
if ($projectsList === false) {
    $projectsList = [];
}
$viewState->set('projects-list', $projectsList);