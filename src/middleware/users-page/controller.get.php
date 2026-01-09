<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/project.php';

const ORDER_ASCENDING = 'asc';
const ORDER_DESCENDING = 'desc';
const SORT_TITLE = 'title';
const SORT_MODIFIED = 'modified';
const SORT_CREATED = 'created';

$viewState = ViewData::getInstance();

initCsrf('users-page');

//## Basic User Data
$userUsername = explode('/',$viewState->get('normalized-route'))[1];
$viewState->set('page-username',$userUsername);
$userData = getUserData($userUsername);
if ($userData === false) {
    http_response_code(404);
    return;
}
// Check if the viewer of the page is admin
$verifiedRole = $viewState->get('verified-role');
$viewerIsAdmin = $verifiedRole === UserRole::Admin->value || $verifiedRole === UserRole::Owner;
$viewState->set('viewer-admin',$viewerIsAdmin);

// Banned users can only be seen by admins
if ($userData['role'] === UserRole::Banned->value && !$viewerIsAdmin) {
    http_response_code(401);
    return;
}

if (($userData['display_name'] ?? '') == '') {
    $userDisplayName = $userUsername;
} else { 
    $userDisplayName = $userData['display_name'];
}

$viewState->set('page-user-role',$userData['role']);
$viewState->set('page-display-name',$userDisplayName);
$viewState->set('page-social-website',$userData['social_website'] ?? '');
$viewState->set('page-social-reddit',$userData['social_reddit'] ?? '');
$viewState->set('page-social-twitter',$userData['social_twitter'] ?? '');
$viewState->set('page-social-instagram',$userData['social_instagram'] ?? '');
$viewState->set('page-social-discord',$userData['social_discord'] ?? '');
$viewState->set('page-picture-link',getUserPictureLink($userUsername,true));

//## Project Paging logic
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
$amount = getProjectCount(['category' => '', 'username' => $userUsername]);
if ($amount === false) {
    http_response_code(500);
    return;
}
$lastPage = ceil($amount/$size);
$viewState->set('paging-last-page',$lastPage);

$projectsList = getProjectList($page, $size, ['category' => '', 'username' => $userUsername], $sort, $order != ORDER_DESCENDING);
if ($projectsList === false) {
    http_response_code(500);
    return;
}
$viewState->set('projects-list', $projectsList);