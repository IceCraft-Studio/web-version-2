<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';

enum RegisterFormError {
    case CsrfInvalid;
    case UsernameInvalid;
    case UsernameTaken;
    case PasswordInvalid;
    case PasswordMismatch;
    case ServerDatabase;

}
const TWO_DAYS_IN_SECONDS = 60*60*24*2;

$viewState = ViewData::getInstance();

$csrfLegit = validateCsrf();
if (!$csrfLegit) {
    $viewState->set('register-error',RegisterFormError::CsrfInvalid);
    return;
}

$username = $_POST['username'];
$password = $_POST['password'];
$passwordConfirm = $_POST['confirm-password'];

$viewState->set('username',$username);

// Check if username is valid
$usernameValid = validateUsername($username);
if (!$usernameValid) {
    $viewState->set('register-error',RegisterFormError::UsernameInvalid);
    return;
}
// Check if username is taken
$userData = getUserData($username);
if ($userData != false) {
    $usernameAvailable = (count($userData) > 0);
} else {
    $usernameAvailable = false;
}
if (!$usernameAvailable) {
    $viewState->set('register-error',RegisterFormError::UsernameTaken);
    return;
}
// Check if password is valid
$passwordValid = validatePassword($password);
if (!$passwordValid) {
    $viewState->set('register-error',RegisterFormError::PasswordInvalid);
    return;
}
// Check if passwords match
if ($password != $passwordConfirm) {
    $viewState->set('register-error',RegisterFormError::PasswordMismatch);
    return;
}
// Create user and session
$created = createUser($username,$password);
if ($created) {
    $token = createSession($username,$password,TWO_DAYS_IN_SECONDS);
    setcookie('token',$token,expires_or_options:time()+TWO_DAYS_IN_SECONDS,secure:true);
    header('Location: /~dobiapa2/profile',true,302);
    exit;
} else {
    $viewState->set('register-error',RegisterFormError::ServerDatabase);
}
