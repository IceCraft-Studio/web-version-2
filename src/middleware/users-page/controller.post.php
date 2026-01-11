<?php
require_once __DIR__ . "/enums.php";

$viewState = ViewData::getInstance();

// Redirect if token is invalid
$csrfLegit = validateCsrf('users-page');
if (!$csrfLegit) {
    $viewState->set('user-manage-state', UserActionState::CsrfInvalid);
    require __DIR__ . '/controller.get.php';
    return;
}

$username = $viewState->get('verified-username', '');
if ($username == '') {
    http_response_code(401);
    return;
}

$userUsername = explode('/', $viewState->get('normalized-route'))[1];
$userData = getUserData($userUsername);
if ($userData === false) {
    http_response_code(404);
    return;
}

// Page viewer role
$verifiedRole = $viewState->get('verified-role');
$isAdmin = $verifiedRole == UserRole::Admin->value || $verifiedRole === UserRole::Owner->value;
//Page user role
$userRole = $userData['role'];

if (($userRole == UserRole::Admin->value && $verifiedRole != UserRole::Owner->value) || !$isAdmin) {
    http_response_code(401);
    return;
}

$userAction = $_POST['user-action'] ?? '';

// Run the action on user
switch ($userAction) {
    case 'ban-user':
        if ($userRole == UserRole::Banned->value) {
            $actionSuccess = changeUserRole($userUsername, UserRole::User->value);
        } else {
            $actionSuccess = changeUserRole($userUsername, UserRole::Banned->value);
            if ($actionSuccess) {
                destroyUserSessions($userUsername);
            }
        }
        break;
    case 'promote-admin':
        $actionSuccess = changeUserRole($userUsername, UserRole::Admin->value);
        break;
    case 'clear-name':
        $actionSuccess = changeUserDisplayName($userUsername, '');
        break;
    case 'clear-picture':
        $actionSuccess = saveUserProfilePicture($userUsername, '');
        break;
    case 'delete-user':
        deleteUser($userUsername);
        redirect('/~dobiapa2/users');
}

if ($userAction != '') {
    if ($actionSuccess ?? false) {
        $viewState->set('user-manage-state', UserActionState::Success);
    } else {
        $viewState->set('user-manage-state', UserActionState::Failure);
    }
}

// Change password if requested
$passwordNew = $_POST['password-new'] ?? '';
$passwordConfirm = $_POST['password-confirm'] ?? '';

if ($passwordNew != '') {
    if (!validatePassword($passwordNew)) {
        $viewState->set('user-password-state', ManagePasswordState::PasswordInvalid);
        include __DIR__ . '/controller.get.php';
        return;
    }
    if ($passwordNew != $passwordConfirm) {
        $viewState->set('user-password-state', ManagePasswordState::PasswordMismatch);
        include __DIR__ . '/controller.get.php';
        return;
    }
    if (!changeUserPassword($userUsername, $passwordNew)) {
        $viewState->set('user-password-state', ManagePasswordState::Failure);
        include __DIR__ . '/controller.get.php';
        return;
    }
    $viewState->set('user-password-state', ManagePasswordState::Success);
    destroyUserSessions($userUsername);
}

require __DIR__ . '/controller.get.php';

