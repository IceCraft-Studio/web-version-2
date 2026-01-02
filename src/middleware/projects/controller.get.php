<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";

const ORDER_ASCENDING = 'asc';
const ORDER_DESCENDING = 'desc';
const SORT_TITLE = 'title';
const SORT_MODIFIED = 'modified';
const SORT_CREATED = 'created';

$viewState = ViewData::getInstance();

$currentRoute = normalizeUriRoute($_SERVER['REQUEST_URI']);
if ($currentRoute == 'projects') {
    $viewState->set('projects-category','');
} else {
    $allCategories = getCategories();
    if ($allCategories === false) {
        http_response_code(500);
        return;
    }
    $category = substr($currentRoute,9);
    $categoryExists = false;
    foreach ($allCategories as $someCategory) {
        if ($someCategory['id'] == $category) {
            $categoryExists = true;
            break;
        }
    }
    if (!$categoryExists) {
        http_response_code(404);
        return;
    }
    $viewState->set('projects-category',$category);
}

// Ensure `page` is a number higher than 1
$page = $_GET['page'] ?? 1;
$page = is_numeric($page) ? $page : 1;
if ($page < 1) {
    $page = 1;
}
// Ensure `size` is a number between 6 and 200
$size = $_GET['size'] ?? 10;
$size = is_numeric($size) ? $size : 10;
if ($size < 6) {
    $size = 6;
}
if ($size > 200) {
    $size = 200;
}
// Ensure order and sort is set correctly
$order = $_GET['order'] ?? ORDER_DESCENDING;

switch ($_GET['sort'] ?? SORT_MODIFIED) {
    case SORT_TITLE:
        $sort = ProjectSort::Title;
        break;
    case SORT_CREATED:
        $sort = ProjectSort::Created;
        break;
    case SORT_MODIFIED:
    default:
        $sort = ProjectSort::Modified;
        break;
}

$viewState->set('projects-list', getProjectList($page, $size, ['category' => '', 'username' => ''], $sort, $order == ORDER_ASCENDING));
