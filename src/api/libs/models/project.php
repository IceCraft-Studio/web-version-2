<?php
enum ProjectSort: string{
    case Modified = 'datetime_modified';
    case Title = 'title';
    case Created = 'datetime_created';
}

function createProject() {
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
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
 * @param string $category Filter specific category. Empty string means no filtering.
 * @param ProjectSort $sortBy How to sort the projects.
 * @param bool $isAscending When `true` 'ASC' is used in the SQL query.
 * @return array
 */
function getProjectList($listNumber, $listItems, $category = '', $sortBy = ProjectSort::Modified, $isAscending = false) { 
    $dbConnection = DbConnect::getConnection(getDbAccessObject());
    $order = $isAscending ? 'ASC' : 'DESC';
    $offset = ($listNumber - 1) * $listItems;
    $sortColumn = $sortBy->value;
    if ($category == '') {
        return dbQuery($dbConnection,"SELECT * FROM `project` ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","ii",[$listItems,$offset]);
    }
    return dbQuery($dbConnection,"SELECT * FROM `project` WHERE `category` = ? ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?","iis",[$category,$listItems,$offset]);
}

