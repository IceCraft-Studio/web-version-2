<?php
require_once __DIR__ . '/enums.php';

$viewState = ViewData::getInstance();

$csrfLegit = validateCsrf();

if (!$csrfLegit) {
    $viewState->set('profile-update-state',ProfileUpdateState::CsrfInvalid);
    include __DIR__ . '/controller.get.php';
    return;
}

$username = verifySession($_COOKIE['token'] ?? '');
if ($username == null) {
    updateSessionCookie('',-99999);
    redirect('/~dobiapa2/login');
}

$displayName = $_POST['display-name'] ?? '';
$email = $_POST['email'] ?? '';
$socialsWebsite = $_POST['socials-website'] ?? '';
$socialsReddit = $_POST['socials-reddit'] ?? '';
$socialsTwitter = $_POST['socials-twitter'] ?? '';
$socialsInstagram = $_POST['socials-instagram'] ?? '';
$socialsDiscord = $_POST['socials-discord'] ?? '';
$password = $_POST['password'] ?? '';
$passwordNew = $_POST['password-new'] ?? '';
$passwordConfirm = $_POST['password-confirm'] ?? '';

// Profile Change
$profileUpdated = false;
$profileUpdateSuccess = true;
$originalUserData = getUserData($username);

if ($originalUserData['display_name'] != $displayName) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserDisplayName($username,$displayName) : false;
}
if ($originalUserData['email'] != $email) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserEmail($username,$email) : false;
}
if ($originalUserData['social_website'] != $socialsWebsite) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocials($username,UserSocial::Website,$socialsWebsite) : false;
}
if ($originalUserData['social_reddit'] != $socialsReddit) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocials($username,UserSocial::Reddit,$socialsReddit) : false;
}
if ($originalUserData['social_twitter'] != $socialsTwitter) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocials($username,UserSocial::Twitter,$socialsTwitter) : false;
}
if ($originalUserData['social_instagram'] != $socialsInstagram) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocials($username,UserSocial::Instagram,$socialsInstagram) : false;
}
if ($originalUserData['social_discord'] != $socialsDiscord) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocials($username,UserSocial::Discord,$socialsDiscord) : false;
}

if ($profileUpdated) {

} else {
    
}

// Password Update
$viewState->set('profile-update-state',PasswordUpdateState::NoUpdate);
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
    $viewState->set('profile-update-state',PasswordUpdateState::Success);
}


include __DIR__ . '/controller.get.php';
