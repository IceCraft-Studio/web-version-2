<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
require_once __DIR__ . '/enums.php';

const MAX_GALLERY_UPLOADS = 12;
const MAX_FILE_UPLOADS = 5;
const MAX_LINK_UPLOADS = 5;
//## Functions
/**
 * Summary of validateProjectData
 * @param string $title The project's title.
 * @param string $description The project's description.
 * @param string $category The project's category.
 * @param string $slug The project's title.
 * @param string $markdownArticle The project's article.
 * @return bool|ProjectUploadState `true` when validation succeeds, otherwise the project upload state.
 */
function validateProjectData($title,$description,$category,$slug,$markdownArticle) {
    if (!validateProjectTitle($title)) {
        return ProjectUploadState::TitleInvalid;
    }
    if (!validateProjectDescription($description)) {
        return ProjectUploadState::DescriptionInvalid;
    }
    if (!validateProjectCategory($category)) {
        return ProjectUploadState::CategoryInvalid;
    }
    if (!validateProjectSlug($slug)) {
        return ProjectUploadState::SlugInvalid;
    }
    if (!validateProjectArticle($markdownArticle)) {
        return ProjectUploadState::ArticleInvalid;
    }
    return true;
}
function validateGalleryUploads($fileArray,$uuidArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $filePath) {

    }
}

function validateLinkUploads($urlArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
}

function validateFileUploads($fileArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
}



/**
 * Prefills `upload-project` form fields used when returning the form cause of an error.
 * @param ViewData $viewState Valid `ViewData` instance to set the values.
 * @return void
 */
function prefillProjectFormValues($viewState) {
    $viewState->set('form-editing', $_POST['editing'] ?? '0');
    $viewState->set('form-title', $_POST['title'] ?? '');
    $viewState->set('form-description', $_POST['description'] ??'');
    $viewState->set('form-slug', $_POST['slug'] ?? '');
    $viewState->set('form-category', $_POST['category'] ?? '');
    $viewState->set('form-markdown-article', $_POST['markdown-article'] ?? '');
}

$viewState = ViewData::getInstance();

$csrfLegit = validateCsrf('upload-project');
if (!$csrfLegit) {
    $viewState->set('upload-project-state', ProjectUploadState::CsrfInvalid);
    prefillProjectFormValues($viewState);
    return;
}

$username = $viewState->get('verified-username','');
if ($username === '') {
    http_response_code(401);
    return;
}

$projectSlug = $_POST['slug'] ?? '';
$projectCategory = $_POST['category'] ?? '';
$projectIsEditing = ($_POST['editing'] ?? '0') === '1';

$checkExistingProject = getProjectData($projectCategory,$projectSlug);
//## Editing the project
if ($projectIsEditing) {
    if ($checkExistingProject === false) {
        http_response_code(400);
        return;
    }
    // failed validating gives control to 

}
//## Uploading a new project
if (!$projectIsEditing) {
    if ($checkExistingProject !== false) {
        $viewState->set('upload-project-state', ProjectUploadState::SlugTaken);
        prefillProjectFormValues($viewState);
        return;
    }
    //failed validating gives control to get controller
}



redirect('/~dobiapa2/projects/' . $category . '/' . $slug);