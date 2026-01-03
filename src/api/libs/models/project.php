<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/github.php";
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/storage.php";

const ALLOWED_GALLERY_IMG_TYPES = [
    IMAGETYPE_GIF,
    IMAGETYPE_PNG,
    IMAGETYPE_JPEG,
    IMAGETYPE_WEBP
];
const ALLOWED_THUMBNAIL_IMG_TYPES =  [
    IMAGETYPE_PNG,
    IMAGETYPE_JPEG,
    IMAGETYPE_WEBP  
];
enum ProjectSort: string{
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

// Creating Project
function createProject($category,$slug,$username,$title,$description) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $timestamp = date('Y-m-d H:i:s');
    $result = dbQuery($dbConnection, 'INSERT INTO `project` (`category`, `slug`, `username`,`title`,`description`,`datetime_created`, `datetime_modified`) VALUES (?, ?, ?, ?, ?, ?, ?)', "sssssss", [$category,$slug,$username,$title,$description,$timestamp,$timestamp]);
    if ($result === 1) {
        return true;
    }
    return false;
}

function validateProjectSlug($slug) {
    if (6 > strlen($slug) || strlen($slug) > 64) {
        return false;
    }
    return isStringSafeUrl($slug);
}

function validateProjectTitle($title) {
    return strlen($title) > 6 && strlen($title) < 128;
}

function validateProjectDescription($description) {
    return strlen($description) > 24 && strlen($description) < 256;
}

// Basic Edits
function updateProjectDateModified($category,$slug) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $timestamp = date('Y-m-d H:i:s');
    $result = dbQuery($dbConnection, "UPDATE `project` SET `datetime_modified` = ? WHERE `category` = ? AND `slug` = ? ", "sss", [$timestamp, $category, $slug]);
    return ($result !== false && $result !== 0);
}
function changeProjectTitle($category,$slug,$newTitle) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `project` SET `title` = ? WHERE `category` = ? AND `slug` = ? ", "sss", [$newTitle, $category, $slug]);
    $sucess = ($result !== false && $result !== 0);
    if ($sucess) {
        updateProjectDateModified($category,$slug);
    }
    return $sucess;
}

function changeProjectDescription($category,$slug,$newDescription) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "UPDATE `project` SET `description` = ? WHERE `category` = ? AND `slug` = ? ", "sss", [$newDescription, $category, $slug]);
    $sucess = ($result !== false && $result !== 0);
    if ($sucess) {
        updateProjectDateModified($category,$slug);
    }
    return $sucess;
}

/**
 * Retuns the data directory for a given project and ensures it exists. (Optionally with subdirectories.)
 * @param string $category The project category.
 * @param string $slug The project slug.
 * @param string $subdir Optional subdirectory inside the project folder.
 * @return string|bool The directory, `false` on failure.
 */
function getProjectDirectory($category,$slug,$subdir = '') {
    $projectDirectory = resolveDataPath('project/' . $category . '/' . $slug);
    if ($subdir !== '') {
        $projectDirectory .= '/' . $subdir;
    }
    if (!is_dir($projectDirectory)) {
        if (!mkdir($projectDirectory,0777,true)) {
            return false;
        }
    }
    return $projectDirectory;
}

/**
 * Takes a specified file on the server and sets it as the profile picture for the given user. If empty deleted the file.
 * @param mixed $category
 * @param mixed $slug
 * @param mixed $fileLocation
 * @return bool
 */
function saveProjectThumbnail($category,$slug,$fileLocation) {
    $projectDirectory = getProjectDirectory($category,$slug);
    if ($projectDirectory === false) {
        return false;
    }
    $thumbnailFullPath = $projectDirectory . '/thumbnail.webp';
    $thumbnailPreviewPath = $projectDirectory . '/thumbnail-preview.webp';

    if ($fileLocation === '') {
        if (file_exists($thumbnailFullPath)) {
            unlink(($thumbnailFullPath));
        }
        if (file_exists($thumbnailPreviewPath)) {
            unlink(($thumbnailPreviewPath));
        }
        return true;
    }

    [$width,$height] = getimagesize($fileLocation);
    return (
        saveImageAsWebP($fileLocation,$thumbnailFullPath) &&
        saveImageAsWebP($fileLocation,$thumbnailPreviewPath,min($width,320),min($height,180))
    );
}

// Project Article
/**
 * Loads project article as markdown and HTML.
 * @param mixed $category The category of the project.
 * @param mixed $slug The unique slug of the project.
 * @return array|bool `false` when it isn't found, otherwise array with 2 index - `markdown` for Markdown of the article, `html` for HTML of the article.
 */
function loadProjectArticle($category,$slug) {
    $projectDirectory = getProjectDirectory($category,$slug);
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
function saveProjectArticle($category,$slug,$markdownData) {
    $projectDirectory = getProjectDirectory($category,$slug);
    if ($projectDirectory === false) {
        return false;
    }
    $projectArticleHtmlPath = $projectDirectory . '/article.html';
    $projectArticleMdPath = $projectDirectory . '/article.md';

    if (file_put_contents($projectArticleMdPath,$markdownData) === false) {
        return false;
    }
    $htmlData = markdownGithub($markdownData);
    if ($htmlData === false) {
        return false;
    }
    if (file_put_contents($projectArticleHtmlPath,$htmlData) === false) {
        return false;
    }
    return true;
}
// Project Gallery
function addProjectGalleryImage() {

}
function removeProjectGalleryImage() {

}

// Project Links
function addProjectLink($category,$slug,$displayName,$url) {

}

function removeProjectLinks($category,$slug,$displayName,$url) {

}

// Project Files
function addProjectFile($category,$slug,$displayName,$fileName) {

}

function removeProjectFiles($category,$slug) {

}

function loadProjectFiles($category,$slug) {

}

function deleteProject($category,$slug) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $resultProject = dbQuery($dbConnection, "DELETE FROM `project` WHERE `category` = ? AND `slug` = ? ", "ss", [$category,$slug]);
    $resultGallery = dbQuery($dbConnection, "DELETE FROM `project_gallery` WHERE `category` = ? AND `slug` = ? ", "ss", [$category,$slug]);
    $resultUpload = dbQuery($dbConnection, "DELETE FROM `project_upload` WHERE `category` = ? AND `slug` = ? ", "ss", [$category,$slug]);
    $resultLink = dbQuery($dbConnection, "DELETE FROM `project_link` WHERE `category` = ? AND `slug` = ? ", "ss", [$category,$slug]);
    removeDirRecursive(getProjectDirectory($category,$slug));
}

function getProjectData($category,$slug) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT * FROM `project` WHERE `category` = ? AND `slug` = ? LIMIT 1", "ss", [$category,$slug]);
    if (!$result || count($result) === 0) {
        return false;
    }
    return $result[0];
}

/**
 * Returns list of projects from the database defined by page number, items per page and filtering & sorting parameters.
 * @param int $listNumber The page number.
 * @param int $listItems Amount of items per page.
 * @param array<string> $filters Array with 2 indexes, `username` and `category`. If the string isn't empty, it is used to filter out results.
 * @param ProjectSort $sortBy How to sort the projects.
 * @param bool $sortAscending When `true` 'ASC' is used in the SQL query.
 * @return array
 */
function getProjectList($listNumber, $listItems, $filters = ['category' => '', 'username' => ''], $sortBy = ProjectSort::Modified, $sortAscending = false) { 
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $order = $sortAscending ? 'ASC' : 'DESC';
    $offset = ($listNumber - 1) * $listItems;
    $sortColumn = $sortBy->value;
    if (($filters['category'] ?? '') == '' && ($filters['username'] ?? '') == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","ii",[$listItems,$offset]);
    }
    if (($filters['category'] ?? '') == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `username` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","sii",[$filters['username'],$listItems,$offset]);
    }
    if (($filters['username'] ?? '') == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `category` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","sii",[$filters['category'],$listItems,$offset]);
    }
    return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `category` = ? AND `username` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","ssii",[$filters['category'],$filters['username'],$listItems,$offset]);
}

/**
 * Retrieves the list of all categories from the database.
 * @return array|bool Array of all catagories or `false` if the query fails.
 */
function getCategories() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    return dbQuery($dbConnection,'SELECT * FROM `category`');
}

