<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/image.php';
require_once __DIR__ . '/enums.php';

const MAX_ALLOWED_IMAGE_SIZE_MB = 8;

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

if (isset($_POST['delete-profile-picture'])) {
    if(saveUserProfilePicture($username,'')) {
        $viewState->set('picture-update-state',PictureUpdateState::Success);        
    } else {
        $viewState->set('picture-update-state',PictureUpdateState::Failure);        
    }
} else if (isset($_FILES['profile-picture'])) {
    $imgTempPath = $_FILES['profile-picture']['tmp_name'];
    if (filesize($imgTempPath)/(1000 ** 2) > 8) {
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

$displayName = trim($_POST['display-name'] ?? '');
$email = trim($_POST['email'] ?? '');
$socialWebsite = trim($_POST['social-website'] ?? '');
$socialReddit = trim($_POST['social-reddit'] ?? '');
$socialTwitter = trim($_POST['social-twitter'] ?? '');
$socialInstagram = trim($_POST['social-instagram'] ?? '');
$socialDiscord = trim($_POST['social-discord'] ?? '');
$password = $_POST['password'] ?? '';
$passwordNew = $_POST['password-new'] ?? '';
$passwordConfirm = $_POST['password-confirm'] ?? '';

// Profile Change
$profileUpdated = false;
$profileUpdateSuccess = true;
$originalUserData = getUserData($username);

if ($originalUserData['display_name'] != $displayName) {
    $changeSuccess = changeUserDisplayName($username,$displayName);
    if ($changeSuccess) {
        $viewState->set('user-display-name', $displayName);
    }
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
