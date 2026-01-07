<?php

$viewState = ViewData::getInstance();

// Get basic user data
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
// Get user data for the page

if ($userData['display_name'] ?? '' === '') {
    $userDisplayName = $userUsername;
} else { 
    $userDisplayName = $userData['display_name'];
}

$viewState->set('page-display-name',$userDisplayName);
$viewState->set('page-social-website',$userData['social_website'] ?? '');
$viewState->set('page-social-reddit',$userData['social_reddit'] ?? '');
$viewState->set('page-social-twitter',$userData['social_twitter'] ?? '');
$viewState->set('page-social-instagram',$userData['social_instagram'] ?? '');
$viewState->set('page-social-discord',$userData['social_discord'] ?? '');
$viewState->set('page-picture-link',getUserPictureLink($userUsername));

// Project Paging logic
//$userProjects = getProjectList();