<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/project.php";

const ORDER_ASCENDING = 'asc';
const ORDER_DESCENDING = 'asc';
const SORT_TITLE = 'title';
const SORT_MODIFIED = 'modified';
const SORT_CREATED = 'created';

$viewState = ViewData::getInstance();

$page = $_GET['page'] ?? 1;
$page = is_numeric($page) ? $page : 1;

$size = $_GET['size'] ?? 1;
$size = is_numeric($size) ? $size : 1;
$size = max(1, min($size, 5));
$sizeTable = [1 => 10,25,50,100,200];
$trueSize = $sizeTable[$size];

$order = $_GET['order'] ?? ORDER_DESCENDING;
if ($order != ORDER_ASCENDING && $order != ORDER_DESCENDING) {
    $order = ORDER_DESCENDING;
}

$sort = $_GET['sort'] ?? SORT_MODIFIED;
if ($sort != SORT_TITLE && $sort != SORT_MODIFIED ) {
    $sort = SORT_MODIFIED;
}

$viewState->set('projects-list',getProjectList($page,$size,$sort,$order == ORDER_DESCENDING));
