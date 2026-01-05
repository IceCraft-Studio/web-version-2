<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/user.php";

const ORDER_ASCENDING = 'asc';
const ORDER_DESCENDING = 'desc';
const SORT_USERNAME = 'username';
const SORT_DISPLAY_NAME = 'display_name';
const SORT_ROLE = 'role';
const SORT_CREATED = 'created';

$viewState = ViewData::getInstance();

$username = $viewState->get('verified-username');
$role = $viewState->get('verified-role');

if ($username === '') {
    redirect('/~dobiapa2/login');
}

$isAdmin = $role == UserRole::Admin->value || $role  == UserRole::Owner->value;

// Disallow Regular Users
if (!$isAdmin) {
    http_response_code(401);
    return;
}

// Ensure `page` is a number higher than 1
$page = $_GET['page'] ?? 1;
$page = is_numeric($page) ? $page : 1;
if ($page < 1) {
    $page = 1;
}
// Ensure `size` is a number between 10 and 500
$size = $_GET['size'] ?? 10;
$size = is_numeric($size) ? $size : 10;
if ($size < 10) {
    $size = 10;
}
if ($size > 500) {
    $size = 500;
}
// Ensure order and sort is set correctly
$order = $_GET['order'] ?? ORDER_DESCENDING;

switch ($_GET['sort'] ?? SORT_CREATED) {
    case SORT_USERNAME:
        $sort = UserSort::Username;
        break;
    case SORT_DISPLAY_NAME:
        $sort = UserSort::DisplayName;
        break;
    case SORT_ROLE:
        $sort = UserSort::Role;
        break;
    case SORT_CREATED:
    default:
        $sort = UserSort::Created;
        break;
}

$roleFilter = $_GET['role'] ?? '';

$viewState->set('users-list', getUserList($page, $size, ['role' => $roleFilter], $sort, $order == ORDER_ASCENDING));

