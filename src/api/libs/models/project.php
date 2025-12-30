<?php
const DB_ACCESS = getDbAccessObject();

function createProject() {

}

function getProjectPreview() {

}

function getProjectFull() {

}

/**
 * Returns list of projects from the database defined by page number, items per page and filtering & sorting parameters.
 * @param int $listNumber The page number.
 * @param int $listItems Amount of items per page.
 * @param string $category Filter specific category. Empty string means no filtering.
 * @param string $sortBy How to sort the projects. Valid is 'title', 'modified' or 'created'.
 * @param bool $isAscending When `true` 'ASC' is used in the SQL query.
 * @return array
 */
function getProjectList($listNumber, $listItems, $category = '', $sortBy = 'modified', $isAscending = false) { 
    $dbConnection = DbConnect::getConnection(DB_ACCESS);
    // Prevent SQL Injection but keep the statement dynamic
    $order = $isAscending ? 'ASC' : 'DESC';
    switch ($sortBy) {
        case 'title':
            $sortColumn = 'title';
            break;
        case 'created':
            $sortColumn = 'datetime_created';
            break;
        case 'modified':
        default:
            $sortColumn = 'datetime_modified';
    }
    if ($category == '') {
        $dbConnection->prepare("SELECT * FROM `project` ORDER BY `$sortColumn` $order LIMIT ? OFFSET ?");
    } else {

    }

    return [];
}

