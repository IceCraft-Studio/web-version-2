<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';
require __DIR__ . "/enums.php";

const TWO_DAYS_IN_SECONDS = 60*60*24*2;

$username = $_POST['username'];
$password = $_POST['password'];
$passwordConfirm = $_POST['confirm-password'];

$viewState = ViewData::getInstance();

$viewState->set('form-username',$username);

// Validate CSRF
$csrfLegit = validateCsrf();
if (!$csrfLegit) {
    $viewState->set('register-error',RegisterFormError::CsrfInvalid);
    return;
}
// Check if username is valid
$usernameValid = validateUsername($username);
if (!$usernameValid) {
    $viewState->set('register-error',RegisterFormError::UsernameInvalid);
    return;
}
// Check if username is taken
$userData = getUserData($username);
if ($userData !== false) {
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
    $token = createSession($username,$password);
    updateSessionCookie($token);
    redirect('/~dobiapa2/profile');
} else {
    $viewState->set('register-error',RegisterFormError::ServerDatabase);
}
