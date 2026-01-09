<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/image.php';
require_once __DIR__ . '/enums.php';

const MAX_ALLOWED_IMAGE_SIZE_MB = 15;

$viewState = ViewData::getInstance();

//## Authentication
$csrfLegit = validateCsrf('profile');

if (!$csrfLegit) {
    $viewState->set('profile-update-state',ProfileUpdateState::CsrfInvalid);
    include __DIR__ . '/controller.get.php';
    return;
}

$username = $viewState->get('verified-username','');
if ($username == '') {
    redirect('/~dobiapa2/login');
}

//## Change Profile Picture
if (isset($_POST['delete-profile-picture'])) {
    if(saveUserProfilePicture($username,'')) {
        $viewState->set('picture-update-state',PictureUpdateState::Success);        
    } else {
        $viewState->set('picture-update-state',PictureUpdateState::Failure);        
    }
} else if (isset($_FILES['profile-picture']) && ($_FILES['profile-picture']['tmp_name'] ?? '') !== '') {
    $imgTempPath = $_FILES['profile-picture']['tmp_name'];
    if (filesize($imgTempPath)/(1000 ** 2) > MAX_ALLOWED_IMAGE_SIZE_MB) {
        $viewState->set('picture-update-state',PictureUpdateState::WrongSize);
    } else if (!validateImageType($imgTempPath,ALLOWED_PROFILE_PICTURE_IMG_TYPES)) {
        $viewState->set('picture-update-state',PictureUpdateState::WrongType);
    } else if(!validateImageAspectRatio($imgTempPath,1)) {
        $viewState->set('picture-update-state',PictureUpdateState::WrongAspectRatio);
    } else {
        if(saveUserProfilePicture($username,$imgTempPath)){
            $viewState->set('picture-update-state',PictureUpdateState::Success);
        } else {
            $viewState->set('picture-update-state',PictureUpdateState::Failure);            
        }
    }
} else {
    $viewState->set('picture-update-state',PictureUpdateState::NoUpdate);        
}

//## Profile Change
$displayName = trim($_POST['display-name'] ?? '');
$email = trim($_POST['email'] ?? '');
$socialWebsite = trim($_POST['social-website'] ?? '');
$socialReddit = trim($_POST['social-reddit'] ?? '');
$socialTwitter = trim($_POST['social-twitter'] ?? '');
$socialInstagram = trim($_POST['social-instagram'] ?? '');
$socialDiscord = trim($_POST['social-discord'] ?? '');

$profileUpdated = false;
$profileUpdateSuccess = true;
$originalUserData = getUserData($username);

if ($originalUserData['display_name'] != $displayName) {
    if (!validateUserDisplayName($displayName)) {
        $viewState->set('profile-update-state',ProfileUpdateState::Failure);
        $viewState->set('profile-update-fail-message','Profile update failed! The display name must be at most 112 characters long.');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserDisplayName($username,$displayName);
    }
    
    if ($changeSuccess && $displayName !== '') {
        $viewState->set('verified-display-name', $displayName);
    }
    if ($changeSuccess && $displayName == '') {
        $viewState->set('verified-display-name', $username);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['email'] != $email) {
    if (!validateUserEmail($email)) {
        $viewState->set('profile-update-fail-message','Profile update failed! The email must be at most 200 characters long and follow the format "name@domain.tld".');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserEmail($username,$email);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_website'] != $socialWebsite) {
    if (!validateUserSocial($socialWebsite)) {
        $viewState->set('profile-update-fail-message','Profile update failed! The social link can\'t be longer than 150 characters.');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserSocial($username,UserSocial::Website,$socialWebsite);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_reddit'] != $socialReddit) {
    if (!validateUserSocial($socialReddit)) {
        $viewState->set('profile-update-fail-message','Profile update failed! The social link can\'t be longer than 150 characters.');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserSocial($username,UserSocial::Reddit,$socialReddit);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_twitter'] != $socialTwitter) {
    if (!validateUserSocial($socialTwitter)) {
        $viewState->set('profile-update-fail-message','Profile update failed! The social link can\'t be longer than 150 characters.');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserSocial($username,UserSocial::Twitter,$socialTwitter);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_instagram'] != $socialInstagram) {
    if (!validateUserSocial($socialInstagram)) {
        $viewState->set('profile-update-fail-message','Profile update failed! The social link can\'t be longer than 150 characters.');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserSocial($username,UserSocial::Instagram,$socialInstagram);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}
if ($originalUserData['social_discord'] != $socialDiscord) {
    if (!validateUserSocial($socialDiscord)) {
        $viewState->set('profile-update-fail-message','Profile update failed! The social link can\'t be longer than 150 characters.');
        $changeSuccess = false;
    } else {
        $changeSuccess = changeUserSocial($username,UserSocial::Discord,$socialDiscord);
    }
    $profileUpdated = true;
    $profileUpdateSuccess = $profileUpdateSuccess ? $changeSuccess : false;
}

// ## Profile Update State
if ($profileUpdated) {
    if ($profileUpdateSuccess) {
        $viewState->set('profile-update-state',ProfileUpdateState::Success);
    } else {
        $viewState->set('profile-update-state',ProfileUpdateState::Failure);
    }
} else {
    $viewState->set('profile-update-state',ProfileUpdateState::NoUpdate);
}

//## Password Update
$password = $_POST['password'] ?? '';
$passwordNew = $_POST['password-new'] ?? '';
$passwordConfirm = $_POST['password-confirm'] ?? '';

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
    if (!verifyUserPassword($username, $password)) {
        $viewState->set('password-update-state',PasswordUpdateState::PasswordWrong);
        include __DIR__ . '/controller.get.php';
        return;        
    }
    if (!changeUserPassword($username,$passwordNew)) {
        $viewState->set('password-update-state',PasswordUpdateState::Failure);
        include __DIR__ . '/controller.get.php';
        return;  
    }
    destroyUserSessions($username);
    $token = createSession($username,$passwordNew);
    updateSessionCookie($token);
    $viewState->set('password-update-state',PasswordUpdateState::Success);
}

//## Prepare Display Information
include __DIR__ . '/controller.get.php';
