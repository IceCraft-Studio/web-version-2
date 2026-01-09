<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";

const ORDER_ASCENDING = 'asc';
const ORDER_DESCENDING = 'desc';
const SORT_TITLE = 'title';
const SORT_MODIFIED = 'modified';
const SORT_CREATED = 'created';

$viewState = ViewData::getInstance();

$currentRoute = $viewState->get('normalized-route');

// Handle all projects page vs category specific page
if ($currentRoute == 'projects') {
    $category = '';
    $categoryName = '';
} else {
    $explodedRoute = explode('/',$currentRoute);
    $category = $explodedRoute[1] ?? '';
    $categoryName = getCategoryName($category);
    if ($categoryName === false) {
        http_response_code(404);
        return;
    }
}
$viewState->set('projects-category',$category);
$viewState->set('projects-category-name',$categoryName);

// Ensure `page` is a number higher than 1
$page = $_GET['page'] ?? '1';
$page = ctype_digit($page) ? (int) $page : 1;
if ($page < 1) {
    $page = 1;
}
// Ensure `size` is a number between 6 and 200
$size = $_GET['size'] ?? '20';
$size = ctype_digit($size) ? (int) $size : 20;
if ($size < 6) {
    $size = 6;
}
if ($size > 200) {
    $size = 200;
}

// Ensure order and sort is set correctly
$order = $_GET['order'] ?? ORDER_DESCENDING;

switch ($_GET['sort'] ?? SORT_TITLE) {
    case SORT_CREATED:
        $sort = ProjectSort::Created;
        $viewState->set('paging-sort',SORT_CREATED);
        break;
    case SORT_MODIFIED:
        $sort = ProjectSort::Modified;
        $viewState->set('paging-sort',SORT_MODIFIED);
        break;
    default:
    case SORT_TITLE:
        $sort = ProjectSort::Title;
        $viewState->set('paging-sort',SORT_TITLE);
        break;
}

$viewState->set('paging-page',$page);
$viewState->set('paging-size',$size);
$viewState->set('paging-order',$order);

// Get required data from the database
$amount = getProjectCount(['category' => $category, 'username' => '']);
if ($amount === false) {
    http_response_code(500);
    return;
}
$lastPage = ceil($amount/$size);
$viewState->set('paging-last-page',$lastPage);

$projectsList = getProjectList($page, $size, ['category' => $category, 'username' => ''], $sort, $order != ORDER_DESCENDING);
if ($projectsList === false) {
    http_response_code(500);
    return;
}
$viewState->set('projects-list', $projectsList);
