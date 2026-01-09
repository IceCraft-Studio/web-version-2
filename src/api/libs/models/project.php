<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/github.php";
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/storage.php";

/**
 * The starting portion of the URL for downloading a project's download file.
 * @var string
 */
const FILE_DOWNLOAD_URL_START = '/~dobiapa2/api/internal/projects/file-download.php';
/**
 * The starting portion of the URL for downloading a gallery image file.
 * @var string
 */
const GALLERY_URL_START = '/~dobiapa2/api/internal/projects/gallery.php';
/**
 * The starting portion of the URL for downloading the project's thumbnail.
 * @var string
 */
const THUMBNAIL_URL_START = '/~dobiapa2/api/internal/projects/thumbnail.php';
/**
 * The starting portion of the URL for the project's page.
 * @var string
 */
const PROJECT_URL_START = '/~dobiapa2/projects/';
/**
 * The starting portion of the URL for the project's page.
 * @var string
 */
const PROJECT_EDIT_URL_START = '/~dobiapa2/upload-project/';

/**
 * Types of image file allowed in the project's gallery.
 * @var array
 */
const ALLOWED_GALLERY_IMG_TYPES = [
    IMAGETYPE_GIF,
    IMAGETYPE_PNG,
    IMAGETYPE_JPEG,
    IMAGETYPE_WEBP
];
/**
 * Types of image files allowed as a thumbnail.
 * @var array
 */
const ALLOWED_THUMBNAIL_IMG_TYPES = [
    IMAGETYPE_PNG,
    IMAGETYPE_JPEG,
    IMAGETYPE_WEBP
];
/**
 * Sort values for the SQL query used to get list of projects.
 */
enum ProjectSort: string
{
    /**
     * Sort by date modified.
     */
    case Modified = 'datetime_modified';
    /**
     * Sort by title.
     */
    case Title = 'title';
    /**
     * Sort by date created.
     */
    case Created = 'datetime_created';
}

/**
 * Returns the URL for the thumbnail of the given project.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @param bool $full If `true`, return the URL for the full resolution thumbnail.
 * @return string The result URL.
 */
function getProjectThumbnailLink($category, $slug, $full = false)
{
    return THUMBNAIL_URL_START . '?category=' . $category . '&project=' . $slug . ($full ? '' : '&variant=preview');
}

/**
 * Returns the URL for the page of the given project.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @return string The result URL.
 */
function getProjectLink($category, $slug)
{
    return PROJECT_URL_START . $category . '/' . $slug;
}

/**
 * Returns the URL for editing of the given project.
 * @param string $category The project's category.
 * @param string $slug The project's slug.
 * @return string The result URL.
 */
function getProjectEditLink($category, $slug)
{
    return PROJECT_EDIT_URL_START . '?edit-category=' . $category . '&edit-slug=' . $slug;
}

/**
 * Retuns the data directory for a given project and ensures it exists. (Optionally with subdirectories.)
 * @param string $category The project category.
 * @param string $slug The project slug.
 * @param string $subdir Optional subdirectory inside the project folder.
 * @return string|bool The directory, `false` on failure.
 */
function getProjectDirectory($category, $slug, $subdir = '')
{
    $projectDirectory = resolveDataPath('project/' . $category . '/' . $slug);
    if ($subdir != '') {
        $projectDirectory .= '/' . $subdir;
    }
    if (!is_dir($projectDirectory)) {
        if (!mkdir($projectDirectory, 0777, true)) {
            return false;
        }
    }
    return $projectDirectory;
}

//## Creating and Deleting Project
/**
 * Creates a new project with the given parameters.
 * @param string $category The new project's category.
 * @param string $slug The new project's slug.
 * @param string $username The username of the creator of the new project.
 * @param string $title The new project's title.
 * @param string $description The new project's description.
 * @return bool `true` on success, `false` on failure.
 */
function createProject($category, $slug, $username, $title, $description)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $timestamp = date('Y-m-d H:i:s');
    $result = dbQuery($dbConnection, 'INSERT INTO `project` (`category`, `slug`, `username`,`title`,`description`,`datetime_created`, `datetime_modified`) VALUES (?, ?, ?, ?, ?, ?, ?)', "sssssss", [$category, $slug, $username, $title, $description, $timestamp, $timestamp]);
    if ($result === 1) {
        return true;
    }
    return false;
}

/**
 * Runs SQL commands to delete all data associated with a project and recursively deletes the project's data in the file system.
 * @param string $category The project category.
 * @param string $slug The project slug.
 * @return bool
 */
function deleteProject($category, $slug)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $resultProject = dbQuery($dbConnection, "DELETE FROM `project` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
    $resultGallery = dbQuery($dbConnection, "DELETE FROM `project_gallery` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
    $resultUpload = dbQuery($dbConnection, "DELETE FROM `project_upload` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
    $resultLink = dbQuery($dbConnection, "DELETE FROM `project_link` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
    removeDirRecursive(getProjectDirectory($category, $slug));
    return true;
}

//## Validations
/**
 * Validation for project's slug. Requirements: Between 6 and 64 characters (inclusive) and safe for use URL.
 * @param string $slug The slug text data.
 * @return bool Result of the validation.
 */
function validateProjectSlug($slug)
{
    if (6 > strlen($slug) || strlen($slug) > 64) {
        return false;
    }
    return isStringSafeUrl($slug);
}

/**
 * Validation for project's title. Requirements: Between 6 and 96 characters (inclusive).
 * @param string $title The title text data.
 * @return bool Result of the validation.
 */
function validateProjectTitle($title)
{
    return strlen($title) >= 6 && strlen($title) <= 96;
}

/**
 * Validation for project's description. Requirements: Between 24 and 320 characters (inclusive).
 * @param string $description The description text data.
 * @return bool Result of the validation.
 */
function validateProjectDescription($description)
{
    return strlen($description) >= 24 && strlen($description) <= 320;
}

/**
 * Validation for project's article. Requirements: Between 128 and 6144 characters (inclusive).
 * @param string $article The article text data.
 * @return bool Result of the validation.
 */
function validateProjectArticle($article)
{
    return strlen($article) >= 128 && strlen($article) <= 6144;
}

/**
 * validation for project's category.
 * @param string $category The category to validate.
 * @return bool `true` when category can be used, `false` otherwise.
 */
function validateProjectCategory($category)
{
    foreach (getCategories() as $someCategory) {
        if ($someCategory['id'] === $category) {
            return true;
        }
    }
    return false;
}



//## Basic Edits
/**
 * Updates the project's date modified to the current datetime.
 * @param string $category The project category.
 * @param string $slug The project slug.
 * @return bool `true` on success, `false` on failure.
 */
function updateProjectDateModified($category, $slug)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $timestamp = date('Y-m-d H:i:s');
    $result = dbQuery($dbConnection, "UPDATE `project` SET `datetime_modified` = ? WHERE `category` = ? AND `slug` = ? ", "sss", [$timestamp, $category, $slug]);
    return ($result !== false && $result !== 0);
}

/**
 * Updates the project's title.
 * @param string $category The project category.
 * @param string $slug The project slug.
 * @param string $newTitle new title for the project.
 * @return bool `true` on success, `false` on failure.
 */
function changeProjectTitle($category, $slug, $newTitle)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `project` SET `title` = ? WHERE `category` = ? AND `slug` = ? ", "sss", [$newTitle, $category, $slug]);
    $success = ($result !== false && $result !== 0);
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

/**
 * Updates the project's description.
 * @param string $category The project category.
 * @param string $slug The project slug.
 * @param string $newDescription The new description for the project.
 * @return bool `true` on success, `false` on failure.
 */
function changeProjectDescription($category, $slug, $newDescription)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `project` SET `description` = ? WHERE `category` = ? AND `slug` = ? ", "sss", [$newDescription, $category, $slug]);
    $success = ($result !== false && $result !== 0);
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

/**
 * Takes a specified file on the server and sets it as the profile picture for the given user. If empty deleted the file.
 * @param mixed $category
 * @param mixed $slug
 * @param mixed $fileLocation
 * @return bool
 */
function saveProjectThumbnail($category, $slug, $fileLocation)
{
    $projectDirectory = getProjectDirectory($category, $slug);
    if ($projectDirectory === false) {
        return false;
    }
    $thumbnailFullPath = $projectDirectory . '/thumbnail';
    $thumbnailPreviewPath = $projectDirectory . '/thumbnail-preview';

    if ($fileLocation == '') {
        if (file_exists($thumbnailFullPath)) {
            unlink(($thumbnailFullPath));
        }
        if (file_exists($thumbnailPreviewPath)) {
            unlink(($thumbnailPreviewPath));
        }
        return true;
    }

    return (
        (saveImageAsWebpOrGif($fileLocation, $thumbnailFullPath, 960, 540) !== false) &&
        (saveImageAsWebpOrGif($fileLocation, $thumbnailPreviewPath, 480, 270) !== false)
    );
}

//## Working with Project Article
/**
 * Loads project article as markdown and HTML.
 * @param string $category The category of the project.
 * @param string $slug The unique slug of the project.
 * @return array|bool `false` when it isn't found, otherwise array with 2 index - `markdown` for Markdown of the article, `html` for HTML of the article.
 */
function loadProjectArticle($category, $slug)
{
    $projectDirectory = getProjectDirectory($category, $slug);
    if ($projectDirectory === false) {
        return false;
    }
    $projectArticleHtmlPath = $projectDirectory . '/article.html';
    $projectArticleMdPath = $projectDirectory . '/article.md';
    if (!file_exists($projectArticleHtmlPath) || !file_exists($projectArticleMdPath)) {
        return false;
    }
    $markdownData = file_get_contents($projectArticleMdPath);
    $htmlData = file_get_contents($projectArticleHtmlPath);
    return [
        'markdown' => $markdownData,
        'html' => $htmlData
    ];
}

/**
 * Saves project article as markdown and also HTML.
 * @param mixed $category The category of the project.
 * @param mixed $slug The unique slug of the project.
 * @return bool `false` on failure, `true` on success.
 */
function saveProjectArticle($category, $slug, $markdownData)
{
    $projectDirectory = getProjectDirectory($category, $slug);
    if ($projectDirectory === false) {
        return false;
    }
    $projectArticleHtmlPath = $projectDirectory . '/article.html';
    $projectArticleMdPath = $projectDirectory . '/article.md';

    if (file_put_contents($projectArticleMdPath, $markdownData) === false) {
        return false;
    }
    $htmlData = markdownGithub($markdownData);
    if ($htmlData === false) {
        return false;
    }
    if (file_put_contents($projectArticleHtmlPath, $htmlData) === false) {
        return false;
    }
    return true;
}

//## Working with Project Gallery

function addProjectGalleryImage($category, $slug, $fileLocation, $imageUuid)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $projectGalleryDirectory = getProjectDirectory($category, $slug, 'gallery');
    if ($projectGalleryDirectory === false) {
        return false;
    }
    $projectGalleryImagePath = $projectGalleryDirectory . '/' . $imageUuid;
    $saveResult = saveImageAsWebpOrGif($fileLocation, $projectGalleryImagePath, 960, 540,true);

    if ($saveResult === false) {
        return false;
    }

    $result = dbQuery($dbConnection, "INSERT INTO `project_gallery` (`category`,`slug`,`file_name`) VALUES (?, ?, ?, ?)  ", "ssss", [$category, $slug, $imageUuid]);
    $success = ($result !== false && $result !== 0);
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}
function removeProjectGalleryImage($category, $slug, $fileName)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $projectGalleryDirectory = getProjectDirectory($category, $slug, 'gallery');
    if ($projectGalleryDirectory === false) {
        return false;
    }
    $projectGalleryImagePath = $projectGalleryDirectory . '/' . $fileName;

    $result = dbQuery($dbConnection, "DELETE FROM `project_gallery` WHERE `category` = ? AND `slug` = ? AND `file_name` = ? ", "sss", [$category, $slug, $fileName]);
    $success = ($result !== false && $result !== 0);
    if ($success && file_exists($projectGalleryImagePath)) {
        $success = unlink($projectGalleryImagePath);
    } else {
        return false;
    }
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

function loadProjectGalleryImages($category, $slug)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT * FROM `project_gallery` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
    if ($result === false) {
        return false;
    }
    foreach ($result as $galleryRow) {
        $fileName = $galleryRow['file_name'] ?? '';
        $linkToImage = GALLERY_URL_START . '?category=' . $category . '&project=' . $slug . '&file_name=' . $fileName;
        $finalArray[] = ['link' => $linkToImage, 'file_name' => $fileName];
    }
    return $finalArray;

}

//## Working with Project Links

function addProjectLink($category, $slug, $url, $displayName)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "INSERT INTO `project_link` (`category`,`slug`,`url`,`display_name`) VALUES (?, ?, ?, ?)  ", "ssss", [$category, $slug, $url, $displayName]);
    $success = ($result !== false && $result !== 0);
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

function removeProjectLink($category, $slug, $url)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "DELETE FROM `project_link` WHERE `category` = ? AND `slug` = ? AND `url` = ? ", "sss", [$category, $slug, $url]);
    $success = ($result !== false && $result !== 0);
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

function loadProjectLinks($category, $slug)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    return dbQuery($dbConnection, "SELECT `url`, `display_name` FROM `project_link` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
}

//## Working with Project Files

function addProjectFile($category, $slug, $fileLocation, $fileName, $displayName)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $projectUploadDirectory = getProjectDirectory($category, $slug, 'upload');
    if ($projectUploadDirectory === false) {
        return false;
    }
    $projectUploadPath = $projectUploadDirectory . '/' . $fileName;
    if (!rename($fileLocation, $projectUploadPath)) {
        return false;
    }
    $result = dbQuery($dbConnection, "INSERT INTO `project_upload` (`category`,`slug`,`file_name`,`display_name`) VALUES (?, ?, ?, ?)  ", "ssss", [$category, $slug, $fileName, $displayName]);
    $success = ($result !== false && $result !== 0);
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

function removeProjectFile($category, $slug, $fileName)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $projectUploadDirectory = getProjectDirectory($category, $slug, 'upload');
    if ($projectUploadDirectory === false) {
        return false;
    }
    $projectUploadPath = $projectUploadDirectory . '/' . $fileName;

    $result = dbQuery($dbConnection, "DELETE FROM `project_upload` WHERE `category` = ? AND `slug` = ? AND `file_name` = ? ", "sss", [$category, $slug, $fileName]);
    $success = ($result !== false && $result !== 0);
    if ($success && file_exists($projectUploadPath)) {
        $success = unlink($projectUploadPath);
    } else {
        return false;
    }
    if ($success) {
        updateProjectDateModified($category, $slug);
    }
    return $success;
}

function loadProjectFiles($category, $slug)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT * FROM `project_upload` WHERE `category` = ? AND `slug` = ? ", "ss", [$category, $slug]);
    if ($result === false) {
        return false;
    }
    foreach ($result as $uploadRow) {
        $fileName = $uploadRow['file_name'] ?? '';
        $linkToUpload = FILE_DOWNLOAD_URL_START . '?category=' . $category . '&project=' . $slug . '&file_name=' . $fileName;
        $finalArray[] = ['display_name' => $uploadRow['display_name'], 'link' => $linkToUpload, 'file_name' => $fileName];
    }
    return $finalArray;
}

//## Retrieving Project Information
/**
 * Retrieves all project data from the database.
 * @param string $category Category to look for in the database.
 * @param string $slug Slug to look for in the database.
 * @return array|bool All columns of the project from the database or `false` if the project isn't found.
 */
function getProjectData($category, $slug)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT * FROM `project` WHERE `category` = ? AND `slug` = ? LIMIT 1", "ss", [$category, $slug]);
    if (!$result || count($result) === 0) {
        return false;
    }
    return $result[0];
}

/**
 * Returns list of projects from the database defined by page number, items per page and filtering & sorting parameters.
 * @param int $listNumber The page number.
 * @param int $listItems Amount of items per page.
 * @param string[] $filters Array with 2 indexes, `username` and `category`. If the string isn't empty, it is used to filter out results.
 * @param ProjectSort $sortBy How to sort the projects.
 * @param bool $sortAscending When `true` 'ASC' is used in the SQL query.
 * @return array|bool Array of projects based on the parameters, `false` on failure.
 */
function getProjectList($listNumber, $listItems, $filters = ['category' => '', 'username' => ''], $sortBy = ProjectSort::Modified, $sortAscending = false)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $order = $sortAscending ? 'ASC' : 'DESC';
    $offset = ($listNumber - 1) * $listItems;
    $sortColumn = $sortBy->value;
    if (($filters['category'] ?? '') == '' && ($filters['username'] ?? '') == '') {
        return dbQuery($dbConnection, "SELECT * FROM `project` ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?", "ii", [$listItems, $offset]);
    }
    if (($filters['category'] ?? '') == '') {
        return dbQuery($dbConnection, "SELECT * FROM `project` WHERE `username` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?", "sii", [$filters['username'], $listItems, $offset]);
    }
    if (($filters['username'] ?? '') == '') {
        return dbQuery($dbConnection, "SELECT * FROM `project` WHERE `category` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?", "sii", [$filters['category'], $listItems, $offset]);
    }
    return dbQuery($dbConnection, "SELECT * FROM `project` WHERE `category` = ? AND `username` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?", "ssii", [$filters['category'], $filters['username'], $listItems, $offset]);
}


/**
 * Returns the amount of projects.
 * @param array $filters Array with 2 indexes, `category` and `username`. If the string isn't empty, it is used to filter out results.
 * @return int|bool `false` on failure, otherwise the amount of project records in the databse.
 */
function getProjectCount($filters = ['category' => '', 'username' => ''])
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    if (($filters['category'] ?? '') == '' && ($filters['username'] ?? '') == '') {
        $result = dbQuery($dbConnection, "SELECT COUNT(*) AS `total` FROM `project`");
    }
    if (($filters['category'] ?? '') == '') {
        $result = dbQuery($dbConnection, "SELECT COUNT(*) AS `total` FROM `project` WHERE `username` = ?", "s", [$filters['username']]);
    }
    if (($filters['username'] ?? '') == '') {
        $result = dbQuery($dbConnection, "SELECT COUNT(*) AS `total` FROM `project` WHERE `category` = ?", "s", [$filters['category']]);
    } else {
        $result = dbQuery($dbConnection, "SELECT COUNT(*) AS `total` FROM `project` WHERE `category` = ? AND `username` = ?", "ss", [$filters['category'], $filters['username']]);
    }
    if ($result === false || count($result) === 0) {
        return false;
    }
    return (int) $result[0]['total'];
}

//# Categories
/**
 * Retrieves the list of all categories from the database.
 * @return array|bool Array of all catagories or `false` if the query fails.
 */
function getCategories()
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    return dbQuery($dbConnection, 'SELECT * FROM `category`');
}

/**
 * Retrieves the category name for a given ID.
 * @param string $categoryId The category ID.
 * @return string|bool Name of the category or `false` if the category isn't found.
 */
function getCategoryName($categoryId)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, 'SELECT `name` FROM `category` WHERE `id` = ?','s', [$categoryId]);
    if ($result === false || count($result) === 0) {
        return false;
    }
    return $result[0]['name'] ?? false;
}

/**
 * Add a new category.
 * @param string $id ID of the added category.
 * @param mixed $name Name of the added category.
 * @return bool `true` on success, `false` on failure.
 */
function addCategory($id, $name)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, 'INSERT INTO `category` (`id`,`name`) VALUES (?, ?)', 'ss', [$id, $name]);
    return ($result !== false || $result !== 0);
}

/**
 * Edit the category's name.
 * @param string $id ID of the category to edit.
 * @param mixed $newName New name of the category.
 * @return bool `true` on success, `false` on failure.
 */
function editCategoryName($id, $newName)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, 'UPDATE `category` SET `name` = ? WHERE `id` = ?', 'ss', [$newName, $id]);
    return ($result !== false || $result !== 0);
}

/**
 * Remove category with replacement. MAYBE ADD LATER.
 * @param string $id ID of the category to remove.
 * @param mixed $newName New category for projects with this category.
 * @return bool `true` on success, `false` on failure.
 */
function removeCategory($id, $newCategory)
{
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, '', '', [$id, $newCategory]);
    return ($result !== false || $result !== 0);
}
