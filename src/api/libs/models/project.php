<?php
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
}

function validateProjectSlug($username) {
    if (6 > strlen($username) || strlen($username) > 64) {
        return false;
    }
    return isStringSafeUrl($username);
}

// Basic Edits
function changeProjectTitle($category,$slug,$newTitle) {

}

function changeProjectDescription($category,$slug,$description) {
    
}

/**
 * Takes a specified file on the server and sets it as the profile picture for the given user. If empty deleted the file.
 * @param mixed $username
 * @param mixed $fileLocation
 * @return void
 */
function saveProjectThumbnail($username,$fileLocation) {

}

// Project Article
function loadProjectArticle($category,$slug) {

}

function saveProjectArticle($category,$slug,$markdown) {

}
// Project Gallery
function addProjectGalleryImage() {

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

}

function getProjectData($category,$slug) {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $result = dbQuery($dbConnection, "SELECT * FROM `user` WHERE `category` = ? AND `slug` = ? LIMIT 1", "s", [$category,$slug]);
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

