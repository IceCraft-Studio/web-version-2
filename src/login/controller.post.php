<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';
require __DIR__ . "/enums.php";


$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$csrfLegit = validateCsrf();
$userPasswordLegit = verifyUserPassword($username,$password);

$userBanned = false;
if ($userPasswordLegit) {
    $userData = getUserData($username);
    $userBanned = isset($userData['role']) ? $userData['role'] == UserRole::Banned->value : false;
}


if ($userPasswordLegit && $csrfLegit && !$userBanned) {
    $token = createSession($username,$password);
    updateSessionCookie($token);
    redirect('/~dobiapa2/profile');
}
// Resending
$viewState = ViewData::getInstance();
$viewState->set('form-username',$username);
if ($csrfLegit) {
    $viewState->set('login-error',LoginFormError::CsrfInvalid);
} else if ($userBanned) {
    $viewState->set('login-error',LoginFormError::WrongCredentials);
} else {
    $viewState->set('login-error',LoginFormError::WrongCredentials);
}