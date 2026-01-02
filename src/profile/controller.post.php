<?php
require_once __DIR__ . '/enums.php';

$viewState = ViewData::getInstance();

$csrfLegit = validateCsrf();

if (!$csrfLegit) {
    $viewState->set('profile-update-state',ProfileUpdateState::CsrfInvalid);
    include __DIR__ . '/controller.get.php';
    return;
}

$username = $viewState->get('verified-username','');
if ($username === '') {
    redirect('/~dobiapa2/login');
}

$displayName = $_POST['display-name'] ?? '';
$email = $_POST['email'] ?? '';
$socialWebsite = $_POST['social-website'] ?? '';
$socialReddit = $_POST['social-reddit'] ?? '';
$socialTwitter = $_POST['social-twitter'] ?? '';
$socialInstagram = $_POST['social-instagram'] ?? '';
$socialDiscord = $_POST['social-discord'] ?? '';
$password = $_POST['password'] ?? '';
$passwordNew = $_POST['password-new'] ?? '';
$passwordConfirm = $_POST['password-confirm'] ?? '';

// Profile Change
$profileUpdated = false;
$profileUpdateSuccess = true;
$originalUserData = getUserData($username);

if ($originalUserData['display_name'] != $displayName) {
    $changeSuccess = changeUserDisplayName($username,$displayName);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['email'] != $email) {
    $changeSuccess = changeUserEmail($username,$email);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_website'] != $socialWebsite) {
    $changeSuccess = changeUserSocial($username,UserSocial::Website,$socialWebsite);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_reddit'] != $socialReddit) {
    $changeSuccess = changeUserSocial($username,UserSocial::Reddit,$socialReddit);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_twitter'] != $socialTwitter) {
    $changeSuccess = changeUserSocial($username,UserSocial::Twitter,$socialTwitter);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_instagram'] != $socialInstagram) {
    $changeSuccess = changeUserSocial($username,UserSocial::Instagram,$socialInstagram);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_discord'] != $socialDiscord) {
    $changeSuccess = changeUserSocial($username,UserSocial::Discord,$socialDiscord);
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}

if ($profileUpdated) {
    if ($profileUpdateSuccess) {
        $viewState->set('profile-update-state',ProfileUpdateState::Success);
    } else {
        $viewState->set('profile-update-state',ProfileUpdateState::Failure);
    }
} else {
    $viewState->set('profile-update-state',ProfileUpdateState::NoUpdate);
}

// Password Update
$viewState->set('password-update-state',PasswordUpdateState::NoUpdate);
if ($passwordNew != '') {
    if (!validatePassword($passwordNew)) {
        $viewState->set('password-update-state',PasswordUpdateState::PasswordInvalid);
        include __DIR__ . '/controller.get.php';
        return;
    }
    if ($passwordNew != $passwordConfirm) {
        $viewState->set('password-update-state',PasswordUpdateState::PasswordMismatch);
        include __DIR__ . '/controller.get.php';
        return;
    }
    if (!changeUserPassword($username,$password,$passwordNew)) {
        $viewState->set('password-update-state',PasswordUpdateState::PasswordWrong);
        include __DIR__ . '/controller.get.php';
        return;        
    }
    destroyUserSessions($username);
    $token = createSession($username,$passwordNew);
    updateSessionCookie($token);
    $viewState->set('password-update-state',PasswordUpdateState::Success);
}


include __DIR__ . '/controller.get.php';
