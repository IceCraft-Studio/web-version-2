<?php

$viewState = ViewData::getInstance();

// Redirect if token is invalid
$csrfLegit = validateCsrf('users-page');
if (!$csrfLegit) {
    require __DIR__ . '/controller.get.php';
    return;
}

$username = $viewState->get('verified-username', '');
if ($username == '') {
    http_response_code(401);
    return;
}

$userUsername = explode('/',$viewState->get('normalized-route'))[1];
$userData = getUserData($userUsername);
if ($userData === false) {
    http_response_code(404);
    return;
}

$userRole = $userData['role'];
if ($userRole == UserRole::Admin->value) {
    http_response_code(401);
    return;
}

$verifiedRole = $viewState->get('verified-role');
$isAdmin = $verifiedRole == UserRole::Admin->value || $verifiedRole === UserRole::Owner;

if (!$isAdmin) {
    http_response_code(401);
    return;
}

// Run the action on user
switch ($userAction) {
    case 'ban-user':
        if ($userRole == UserRole::Banned->value) {
            changeUserRole($userUsername,UserRole::User->value);
        } else {
            changeUserRole($userUsername,UserRole::Banned->value);
        }
        break;
    case 'promote-admin':
        changeUserRole($userUsername,UserRole::Admin->value);
        break;
    case 'clear-name':
        changeUserDisplayName($userUsername,'');
        break;
    case 'delete-user':
        deleteUser($userUsername);
        redirect('/~dobiapa2/users');
}


// Change password if requested
$passwordNew = $_POST['password-new'] ?? '';
$passwordConfirm = $_POST['password-confirm'] ?? '';

if ($passwordNew != '') {
    if (!validatePassword($passwordNew)) {
        include __DIR__ . '/controller.get.php';
        return;
    }
    if ($passwordNew != $passwordConfirm) {
        include __DIR__ . '/controller.get.php';
        return;
    }
    if (!changeUserPassword($userUsername,$passwordNew)) {
        include __DIR__ . '/controller.get.php';
        return;  
    }
    destroyUserSessions($userUsername);
}

require __DIR__ . '/controller.get.php';

