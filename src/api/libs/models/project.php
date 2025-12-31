<?php
enum ProjectSort: string{
    case Modified = 'datetime_modified';
    case Title = 'title';
    case Created = 'datetime_created';
}

function createProject() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

/**
 * Takes a specified file on the server and sets it as the profile picture for the given user. If empty deleted the file.
 * @param mixed $username
 * @param mixed $fileLocation
 * @return void
 */
function saveProjectThumbnail($username,$fileLocation) {

}


function getProjectPreview() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

function getProjectFull() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
}

/**
 * Returns list of projects from the database defined by page number, items per page and filtering & sorting parameters.
 * @param int $listNumber The page number.
 * @param int $listItems Amount of items per page.
 * @param array<string> $filters Array with 2 indexes, `username` and `category`. If the string isn't empty, it is used to filter out results.
 * @param string $filterUsername Filter specific username. Empty string means no filtering.
 * @param ProjectSort $sortBy How to sort the projects.
 * @param bool $sortAscending When `true` 'ASC' is used in the SQL query.
 * @return array
 */
function getProjectList($listNumber, $listItems, $filters = ['category' => '', 'username' => ''], $sortBy = ProjectSort::Modified, $sortAscending = false) { 
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $order = $sortAscending ? 'ASC' : 'DESC';
    $offset = ($listNumber - 1) * $listItems;
    $sortColumn = $sortBy->value;
    if ($filters['category'] == '' && $filters['username'] == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","ii",[$listItems,$offset]);
    }
    if ($filters['category'] == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `username` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","sii",[$filters['username'],$listItems,$offset]);
    }
    if ($filters['username'] == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `category` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","sii",[$filters['category'],$listItems,$offset]);
    }
    return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `category` = ? AND `username` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","ssii",[$filters['category'],$filters['username'],$listItems,$offset]);
}

