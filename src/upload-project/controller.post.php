<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/image.php';
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/storage.php';
require_once __DIR__ . '/enums.php';


/**
 * Maximum file upload size for images in MB.
 * @var int
 */
const MAX_ALLOWED_IMAGE_SIZE_MB = 15;
/**
 * Maximum file upload size for files in MB.
 * @var int
 */
const MAX_ALLOWED_UPLOAD_SIZE_MB = 30;
/**
 * Maximum amount of uploaded gallery images.
 * @var int
 */
const MAX_GALLERY_UPLOADS = 12;
/**
 * Maximum amount of uploaded files.
 * @var int
 */
const MAX_FILE_UPLOADS = 5;
/**
 * Maximum amount of uploaded links.
 * @var int
 */
const MAX_LINK_UPLOADS = 5;

//## Functions

/**
 * Validates all basic project data.
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
 * Validates the thumbnail for the project. Must be 16:9 aspect ratio, format of PNG, JPEG or WEBP and no bigger than 15 MB.
 * @param string $filePath Where the thumbnail is saved as a file.
 * @return bool `true` when valid, `false` otherwise.
 */
function validateProjectThumbnail($filePath) {
    return (
        validateImageType($filePath,ALLOWED_THUMBNAIL_IMG_TYPES) && 
        validateImageAspectRatio($filePath,16/9) &&
        (filesize($filePath) / (1024**2) <= MAX_ALLOWED_IMAGE_SIZE_MB)
    );
}

/**
 * Validates gallery images in numerous ways.
 * @param array $fileArray Array where the file paths for the images are stored.
 * @param array $uuidArray Array where the UUIDs for the images are stored.
 * @param int $existingNumber How many gallery images have been previously uploaded.
 * @return bool `true` when all are valid, `false` otherwise.
 */
function validateGalleryUploads($fileArray,$uuidArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if (($filePath ?? '' ) == '') {
            continue;
        }
        if ($uploadsNumber >= MAX_GALLERY_UPLOADS) {
            break;
        }
        if (filesize($filePath) / (1024**2) > MAX_ALLOWED_IMAGE_SIZE_MB) {
            return false;
        }
        if (!validateImageAspectRatio($filePath,16/9)) {
            return false;
        }
        if (!validateImageType($filePath,ALLOWED_GALLERY_IMG_TYPES)) {
            return false;
        }
        $uuid = strtolower($uuidArray[$index] ?? '');
        if (strlen($uuid) > 36 || strlen($uuid) < 24 || !isStringSafeUrl($uuid)) {
            return false;
        }
        $uploadsNumber++;
    }
    return $uploadsNumber - $existingNumber;
}

/**
 * Validates link uploads in numerous ways.
 * @param array $urlArray Array where the URLs for the links are stored.
 * @param array $nameArray Array where the display names for the links are stored.
 * @param int $existingNumber How manlinks have been previously uploaded.
 * @return bool `true` when all are valid, `false` otherwise.
 */
function validateLinkUploads($urlArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    $usedUrls = [];
    foreach ($urlArray as $index => $url) {
        if (($url ?? '') == '') {
            continue;
        }
        if ($uploadsNumber >= MAX_LINK_UPLOADS) {
            break;
        }
        if (!preg_match('/^[a-z]*:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        if (!preg_match('/^https?:\/\//', $url)) {
            return false;
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        if (strlen($url) > 200) {
            return false;
        }
        if (in_array($url,$usedUrls)) {
            return false;
        }
        $usedUrls[] = $url;
        if (strlen($nameArray[$index] ?? '') > 96) {
            return false;
        }
        $uploadsNumber++;
    }
    return $uploadsNumber - $existingNumber;
}

/**
 * Validates file uploads in numerous ways.
 * @param array $fileArray Array where the file paths for the files are stored.
 * @param array $nameArray Array where the display names for the files are stored.
 * @param int $existingNumber How many file uploads have been previously uploaded.
 * @return bool `true` when all are valid, `false` otherwise.
 */
function validateFileUploads($fileArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if (($filePath ?? '') == '') {
            continue;
        }
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
    return $uploadsNumber - $existingNumber;
}

/**
 * Saves gallery images to the database and filesystem.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @param array $fileArray The array where the gallery image files are stored.
 * @param array $uuidArray The array where the gallery image UUIDs are stored.
 * @param int $existingNumber How many gallery images have been previously uploaded.
 * @return bool|int `false` when failed, otherwise the amount of uploaded gallery images.
 */
function saveGalleryImages($category,$slug,$fileArray,$uuidArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if (($filePath ?? '') == '') {
            continue;
        }
        if ($uploadsNumber >= MAX_GALLERY_UPLOADS) {
            break;
        }
        $result = addProjectGalleryImage($category,$slug,$filePath,$uuidArray[$index]);
        if ($result === false) {
            return false;
        }
    }
    return $uploadsNumber - $existingNumber;
}

/**
 * Saves link uploads to the database and filesystem.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @param array $urlArray The array where the link URLs are stored.
 * @param array $nameArray The array where the link display names are stored.
 * @param int $existingNumber How many link uploads have been previously uploaded.
 * @return bool|int `false` when failed, otherwise the amount of uploaded links.
 */
function saveUrlLinks($category,$slug,$urlArray,$nameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($urlArray as $index => $url) {
        if (($url ?? '') == '') {
            continue;
        }
        if ($uploadsNumber >= MAX_LINK_UPLOADS) {
            break;
        }
        if (!preg_match('/^[a-z]*:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        $result = addProjectLink($category,$slug,$url,$nameArray[$index] ?? '');
        if ($result === false) {
            return false;
        }
    }
    return $uploadsNumber - $existingNumber;
}

/**
 * Saves file uploads to the database and filesystem.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @param array $fileArray The array where the file upload locations are stored.
 * @param array $fileNameArray The array where the file upload file names are stored.
 * @param array $displayNameArray The array where the file upload display names are stored.
 * @param int $existingNumber How many file uploads have been previously uploaded.
 * @return bool|int `false` when failed, otherwise the amount of uploaded files.
 */
function saveFileUploads($category,$slug,$fileArray,$fileNameArray,$displayNameArray,$existingNumber = 0) {
    $uploadsNumber = $existingNumber;
    foreach ($fileArray as $index => $filePath) {
        if (($filePath ?? '') == '') {
            continue;
        }
        if ($uploadsNumber >= MAX_FILE_UPLOADS) {
            break;
        }
        $directory = getProjectDirectory($category,$slug,'upload');
        $fileInfo = pathinfo(createSafeFileName($fileNameArray[$index] ?? ''));
        $fileName = getAvailablePath($directory,$fileInfo['filename'] ?? '',$fileInfo['extension'] ?? '');
        $fileInfo = pathinfo($fileName);
        $fileName = ($fileInfo['filename'] ?? '') . '.' . ($fileInfo['extension'] ?? '');
        $result = addProjectFile($category,$slug,$filePath,$fileName,$displayNameArray[$index] ?? '');
        if ($result === false) {
            return false;
        }
    }
    return $uploadsNumber - $existingNumber;
}

/**
 * Replaces the UUID links generated by the browser with real links to the images inside of markdown article.
 * @param string $markdownData The markdown input data.
 * @param string $category The category of the project.
 * @param string $slug The slug of the project.
 * @return string The fixed markdown string.
 */
function fixMarkdownLinks($markdownData,$category,$slug) {
    $prefix = 'https://zwa.toad.cz/~dobiapa2/api/internal/projects/gallery.php?category=' . $category . '&project=' . $slug . '&file_name=';
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

/**
 * Prefills `upload-project` previous upload fields used when returning the edit form cause of an error.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @param ViewData $viewState Valid `ViewData` instance to set the values.
 * @return void
 */
function prefillProjectPreviousUploads($category,$slug,$viewState) {
    $viewState->set('form-previous-gallery',loadProjectGalleryImages($category,$slug) ?: []);
    $viewState->set('form-previous-links',loadProjectLinks($category,$slug) ?: []);
    $viewState->set('form-previous-files',loadProjectFiles($category,$slug) ?: []);
}

//## Script

$viewState = ViewData::getInstance();

$projectSlug = $_POST['slug'] ?? '';
$projectCategory = $_POST['category'] ?? '';
$projectIsEditing = ($_POST['editing'] ?? '0') === '1';

$csrfLegit = validateCsrf('upload-project');
if (!$csrfLegit) {
    $viewState->set('upload-project-state', ProjectUploadState::CsrfInvalid);
    prefillProjectFormValues($viewState);
    initCsrf('upload-project');
    if ($projectIsEditing) {
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
    }
    return;
}

$username = $viewState->get('verified-username','');
if ($username == '') {
    http_response_code(401);
    return;
}

$checkExistingProject = getProjectData($projectCategory,$projectSlug);

//## Editing the project
if ($projectIsEditing) {
    if ($checkExistingProject === false) {
        http_response_code(400);
        return;
    }
    if ($checkExistingProject['username'] != $username) {
        http_response_code(401);
        return;
    }
    // load and validate basic project data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $markdownArticle = fixMarkdownLinks($_POST['markdown-article'] ?? '',$projectCategory,$projectSlug);

    $projectDataValidation = validateProjectData($title,$description,$projectCategory,$projectSlug,$markdownArticle);
    if ($projectDataValidation !== true) {
        $viewState->set('upload-project-state', $projectDataValidation);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    // load and validate thumbnail
    $thumbnailFile = ($_FILES['thumbnail'] ?? [])['tmp_name'] ?? '';
    if ($thumbnailFile != '' && !validateProjectThumbnail($thumbnailFile)) {
        $viewState->set('upload-project-state', ProjectUploadState::ThumbnailInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    // delete requested uploads
    foreach (($_POST['gallery-delete-name'] ?? []) as $deleteGalleryName) {
        removeProjectGalleryImage($projectCategory,$projectSlug,$deleteGalleryName);
    }
    foreach (($_POST['link-delete-url'] ?? []) as $deleteLinkUrl) {
        removeProjectLink($projectCategory,$projectSlug,$deleteLinkUrl);
    }
    foreach (($_POST['file-delete-name'] ?? []) as $deleteFileName) {
        removeProjectFile($projectCategory,$projectSlug,$deleteFileName);
    }
    // load and validate gallery
    $galleryFilesArray = ($_FILES['gallery-upload'] ?? [])['tmp_name'] ?? [];
    $galleryUuidsArray = $_POST['gallery-uuid'] ?? [];
    $galleryExistingAmount = count(loadProjectGalleryImages($projectCategory,$projectSlug) ?: []);
    $galleryAmount = validateGalleryUploads($galleryFilesArray,$galleryUuidsArray,$galleryExistingAmount);
    if ($galleryAmount === false) {
        $viewState->set('upload-project-state', ProjectUploadState::GalleryInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    // load and validate links
    $linkUrlsArray = $_POST['link-url'] ?? [];
    $linkNamesArray = $_POST['link-name'] ?? [];
    $linksExistingAmount = count(loadProjectLinks($projectCategory,$projectSlug) ?: []);
    $linksAmount = validateLinkUploads($linkUrlsArray,$linkNamesArray,$linksExistingAmount);
    if ($linksAmount === false) {
        $viewState->set('upload-project-state', ProjectUploadState::LinkInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    // load and validate files
    $uploadFilesArray = ($_FILES['file-upload'] ?? [])['tmp_name'] ?? [];
    $uploadFileNamesArray = ($_FILES['file-upload'] ?? [])['name'] ?? [];
    $uploadDisplayNamesArray = $_POST['file-name'] ?? [];
    $uploadsExistingAmount = count(loadProjectFiles($projectCategory,$projectSlug) ?: []);
    $uploadsAmount = validateFileUploads($uploadFilesArray,$uploadDisplayNamesArray,$uploadsExistingAmount);
    if ($uploadsAmount === false) {
        $viewState->set('upload-project-state', ProjectUploadState::FileInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    // check amounts
    if (($uploadsExistingAmount + $uploadsAmount + $linksExistingAmount + $linksAmount) < 1) {
        $viewState->set('upload-project-state', ProjectUploadState::NoUploads);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    // save data
    if (changeProjectTitle($projectCategory,$projectSlug,$title) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    if (changeProjectDescription($projectCategory,$projectSlug,$description) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    }
    if ($thumbnailFile != '' && !saveProjectThumbnail($projectCategory, $projectSlug, $thumbnailFile)) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    };
    if (saveGalleryImages(
        $projectCategory, 
        $projectSlug, 
        $galleryFilesArray,
        $galleryUuidsArray,
        $galleryExistingAmount
    ) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    };
    if (saveProjectArticle($projectCategory, $projectSlug, $markdownArticle) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    };
    if (saveUrlLinks(
            $projectCategory, 
            $projectSlug, 
            $linkUrlsArray,
            $linkNamesArray,
            $linksExistingAmount
    ) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    };
    if (saveFileUploads(
        $projectCategory, 
        $projectSlug, 
        $uploadFilesArray,
        $uploadFileNamesArray,
        $uploadDisplayNamesArray,
        $uploadsExistingAmount
    ) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        prefillProjectPreviousUploads($projectCategory,$projectSlug,$viewState);
        return;
    };
    updateProjectDateModified($projectCategory,$projectSlug);

}
//## Uploading a new project
if (!$projectIsEditing) {
    if ($checkExistingProject !== false) {
        $viewState->set('upload-project-state', ProjectUploadState::SlugTaken);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // load and validate basic project data
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $markdownArticle = $_POST['markdown-article'] ?? '';

    $projectDataValidation = validateProjectData($title,$description,$projectCategory,$projectSlug,$markdownArticle);
    if ($projectDataValidation !== true) {
        $viewState->set('upload-project-state', $projectDataValidation);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // load and validate thumbnail
    $thumbnailFile = ($_FILES['thumbnail'] ?? [])['tmp_name'] ?? '';
    if ($thumbnailFile == '' || !validateProjectThumbnail($thumbnailFile)) {
        $viewState->set('upload-project-state', ProjectUploadState::ThumbnailInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // load and validate gallery
    $galleryFilesArray = ($_FILES['gallery-upload'] ?? [])['tmp_name'] ?? [];
    $galleryUuidsArray = $_POST['gallery-uuid'] ?? [];
    $galleryAmount = validateGalleryUploads($galleryFilesArray,$galleryUuidsArray);
    if ($galleryAmount === false) {
        $viewState->set('upload-project-state', ProjectUploadState::GalleryInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // load and validate links
    $linkUrlsArray = $_POST['link-url'] ?? [];
    $linkNamesArray = $_POST['link-name'] ?? [];
    $linksAmount = validateLinkUploads($linkUrlsArray,$linkNamesArray);
    if ($linksAmount === false) {
        $viewState->set('upload-project-state', ProjectUploadState::LinkInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // load and validate files
    $uploadFilesArray = ($_FILES['file-upload'] ?? [])['tmp_name'] ?? [];
    $uploadFileNamesArray = ($_FILES['file-upload'] ?? [])['name'] ?? [];
    $uploadDisplayNamesArray = $_POST['file-name'] ?? [];
    $uploadsAmount = validateFileUploads($uploadFilesArray,$uploadDisplayNamesArray);
    if ($uploadsAmount === false) {
        $viewState->set('upload-project-state', ProjectUploadState::FileInvalid);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // check amounts
    if ($uploadsAmount + $linksAmount < 1) {
        $viewState->set('upload-project-state', ProjectUploadState::NoUploads);
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    }
    // save data
    if (!createProject($projectCategory, $projectSlug, $username,$title,$description)) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        header('Error: Create Project');
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        return;
    };
    if (!saveProjectThumbnail($projectCategory, $projectSlug, $thumbnailFile)) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        header('Error: thumnbnail');
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        deleteProject($projectCategory,$projectSlug);
        return;
    };
    if (!saveProjectArticle($projectCategory, $projectSlug, $markdownArticle)) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        header('Error: article');
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        deleteProject($projectCategory,$projectSlug);
        return;
    };
    if (saveGalleryImages(
        $projectCategory, 
        $projectSlug, 
        $galleryFilesArray,
        $galleryUuidsArray
    ) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        header('Error: gallery');
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        deleteProject($projectCategory,$projectSlug);
        return;
    };
    if (saveUrlLinks(
            $projectCategory, 
            $projectSlug, 
            $linkUrlsArray,
            $linkNamesArray
    ) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        header('Error: links');
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        deleteProject($projectCategory,$projectSlug);
        return;
    };
    if (saveFileUploads(
        $projectCategory, 
        $projectSlug, 
        $uploadFilesArray,
        $uploadFileNamesArray,
        $uploadDisplayNamesArray
    ) === false) {
        $viewState->set('upload-project-state', ProjectUploadState::ServerError);
        header('Error: uplaod file');
        prefillProjectFormValues($viewState);
        initCsrf('upload-project');
        deleteProject($projectCategory,$projectSlug);
        return;
    };
}



redirect('/~dobiapa2/projects/' . $projectCategory . '/' . $projectSlug);