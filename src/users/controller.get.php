<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . "/api/libs/models/user.php";

/**
 * Text to indicate in GET parameter `order` to be ascending.
 * @var string
 */
const ORDER_ASCENDING = 'asc';
/**
 * Text to indicate in GET parameter `order` to be descending.
 * @var string
 */
const ORDER_DESCENDING = 'desc';
/**
 * Text to indicate in GET parameter `sort` to sort by username.
 * @var string
 */
const SORT_USERNAME = 'username';
/**
 * Text to indicate in GET parameter `sort` to sort by display name.
 * @var string
 */
const SORT_DISPLAY_NAME = 'display_name';
/**
 * Text to indicate in GET parameter `sort` to sort by role.
 * @var string
 */
const SORT_ROLE = 'role';
/**
 * Text to indicate in GET parameter `sort` to sort by date created.
 * @var string
 */
const SORT_CREATED = 'created';

$viewState = ViewData::getInstance();

$username = $viewState->get('verified-username');
$role = $viewState->get('verified-role');

if ($username == '') {
    redirect('/~dobiapa2/login');
}

$isAdmin = $role == UserRole::Admin->value || $role == UserRole::Owner->value;

// Disallow Regular Users
if (!$isAdmin) {
    http_response_code(401);
    return;
}

// Ensure `page` is a number higher than 1
$page = $_GET['page'] ?? '1';
$page = ctype_digit($page) ? (int) $page : 1;
if ($page < 1) {
    $page = 1;
}
// Ensure `size` is a number between 10 and 500
$size = $_GET['size'] ?? '25';
$size = ctype_digit($size) ? (int)  $size : 10;
if ($size < 10) {
    $size = 10;
}
if ($size > 500) {
    $size = 500;
}
// Ensure order and sort is set correctly
$order = $_GET['order'] ?? ORDER_ASCENDING;

switch ($_GET['sort'] ?? SORT_USERNAME) {

    case SORT_DISPLAY_NAME:
        $sort = UserSort::DisplayName;
        $viewState->set('paging-sort',SORT_DISPLAY_NAME);
        break;
    case SORT_ROLE:
        $sort = UserSort::Role;
        $viewState->set('paging-sort',SORT_ROLE);
        break;
    case SORT_CREATED:
        $sort = UserSort::Created;
        $viewState->set('paging-sort',SORT_CREATED);
        break;
    case SORT_USERNAME:
    default:
        $sort = UserSort::Username;
        $viewState->set('paging-sort',SORT_USERNAME);
        break;

}

$roleFilter = $_GET['role'] ?? '';

$viewState->set('paging-page',$page);
$viewState->set('paging-size',$size);
$viewState->set('paging-order',$order);
$viewState->set('paging-role',$roleFilter);

$amount = getUserCount(['role' => $roleFilter]);
if ($amount === false) {
    http_response_code(500);
    return;
}
$lastPage = ceil($amount/$size);
$viewState->set('paging-last-page',$lastPage);

$usersList = getUserList($page, $size, ['role' => $roleFilter], $sort, $order != ORDER_DESCENDING);
if ($usersList === false) {
    http_response_code(500);
    return;
}

$viewState->set('users-list', $usersList);


