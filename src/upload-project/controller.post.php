<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
// Validation
$csrfLegit = validateCsrf();
if (!$csrfLegit) {
    
}
$viewState = ViewData::getInstance();

$username = $viewState->get('verified-username','');

if ($username === '') {
    http_response_code(401);
    return;
}

$projectSlug = $_POST['slug'] ?? '';
$projectCategory = $_POST['category'] ?? '';
$projectEditing = $_POST['editing'] ?? '0';
$checkExistingProject = getProjectData($projectCategory,$projectSlug);
if ($projectEditing === '1') {
    if ($checkExistingProject === false) {
        http_response_code(400);
        return;
    }
    // failed validating gives control to get controller and sets the edit parameters in viewData

} else if ($projectEditing === '0') {
    if ($checkExistingProject !== false) {
        //handle slug taken
    }
    //failed validating gives control to get controller
} else {
    http_response_code(400);
    return;
}


// Processing

redirect('/~dobiapa2/projects/' . $category . '/' . $slug);