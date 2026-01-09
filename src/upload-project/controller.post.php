<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
require_once __DIR__ . '/enums.php';

const MAX_ALLOWED_IMAGE_SIZE_MB = 15;
const MAX_ALLOWED_UPLOAD_SIZE_MB = 30;

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

/**
 * Validates gallery images
 * @param mixed $fileArray
 * @param mixed $uuidArray
 * @param mixed $existingNumber
 * @return bool
 */
function validateGalleryUploads($fileArray,$uuidArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if ($uploadsNumber >= MAX_GALLERY_UPLOADS) {
            break;
        }
        if (filesize($filePath) / (1024**2) > MAX_ALLOWED_IMAGE_SIZE_MB) {
            return false;
        }
        if (strlen($nameArray[$index] ?? '') > 96) {
            return false;
        }
        $uuid = strtolower($uuidArray[$index] ?? '');
        if (strlen($uuid) > 36 || strlen($uuid) < 24 || !isStringSafeUrl($uuid)) {
            return false;
        }
        $uploadsNumber++;
    }
    return true;
}

function validateLinkUploads($urlArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($urlArray as $index => $url) {
        if ($uploadsNumber >= MAX_LINK_UPLOADS) {
            break;
        }
        if (strlen($url ?? '') > 200) {
            return false;
        }
        if (strlen($nameArray[$index] ?? '') > 96) {
            return false;
        }
        $uploadsNumber++;
    }
    return true;
}

function validateFileUploads($fileArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if ($uploadsNumber >= MAX_FILE_UPLOADS) {
            break;
        }
        if (filesize($filePath) / (1024**2) > MAX_ALLOWED_UPLOAD_SIZE_MB) {
            return false;
        }
        if (strlen($nameArray[$index] ?? '') > 96) {
            return false;
        }
        $uploadsNumber++;
    }
    return true;
}


function saveGalleryImages($fileArray,$uuidArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if ($uploadsNumber >= MAX_GALLERY_UPLOADS) {
            break;
        }
        
    }
}

function saveUrlLinks($urlArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($urlArray as $index => $url) {
        if ($uploadsNumber >= MAX_LINK_UPLOADS) {
            break;
        }
        
    }
}

function saveFileUploads($fileArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if ($uploadsNumber >= MAX_FILE_UPLOADS) {
            break;
        }
    }
}



function fixMarkdownLinks($markdownData,$category,$slug) {
    $prefix = 'https://zwa.toad.cz/api/internal/projects/gallery.php?category=' . $category . '&slug=' . $slug . '&file_name=';
    $markdownData = preg_replace_callback(
        '#https://zwa\.toad\.cz/([0-9a-fA-F-]{36})#',
        function ($matches) use ($prefix) {
            $uuid = $matches[1];
            return $prefix . $uuid;
        },
        $markdownData
    );
    return $markdownData;
}


/**
 * Prefills `upload-project` form fields used when returning the form cause of an error.
 * @param ViewData $viewState Valid `ViewData` instance to set the values.
 * @return void
 */
function prefillProjectFormValues($viewState) {
    $viewState->set('form-title', $_POST['title'] ?? '');
    $viewState->set('form-description', $_POST['description'] ?? '');
    $viewState->set('form-slug', $_POST['slug'] ?? '');
    $viewState->set('form-category', $_POST['category'] ?? '');
    $viewState->set('form-markdown-article', $_POST['markdown-article'] ?? '');
    $viewState->set('form-editing', ($_POST['editing'] ?? '') === '1' ? '1' : '0');
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
    if ($checkExistingProject['username'] !== $username) {
        http_response_code(401);
        return;
    }


}
//## Uploading a new project
if (!$projectIsEditing) {
    if ($checkExistingProject !== false) {
        $viewState->set('upload-project-state', ProjectUploadState::SlugTaken);
        prefillProjectFormValues($viewState);
        return;
    }

}



redirect('/~dobiapa2/projects/' . $category . '/' . $slug);