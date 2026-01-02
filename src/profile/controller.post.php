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
if ($username ?? '' === '') {
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
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserDisplayName($username,$displayName) : false;
}
if ($originalUserData['email'] != $email) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserEmail($username,$email) : false;
}
if ($originalUserData['social_website'] != $socialWebsite) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocial($username,UserSocial::Website,$socialWebsite) : false;
}
if ($originalUserData['social_reddit'] != $socialReddit) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocial($username,UserSocial::Reddit,$socialReddit) : false;
}
if ($originalUserData['social_twitter'] != $socialTwitter) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocial($username,UserSocial::Twitter,$socialTwitter) : false;
}
if ($originalUserData['social_instagram'] != $socialInstagram) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocial($username,UserSocial::Instagram,$socialInstagram) : false;
}
if ($originalUserData['social_discord'] != $socialDiscord) {
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? changeUserSocial($username,UserSocial::Discord,$socialDiscord) : false;
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
