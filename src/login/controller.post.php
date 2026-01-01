<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';
require __DIR__ . "/enums.php";

$csrfLegit = validateCsrf();
$userPasswordLegit = verifyUserPassword(trim($_POST['username']),$_POST['password']);

if ($userPasswordLegit && $csrfLegit) {
    $token = createSession($_POST['username'],$_POST['password']);
    updateSessionCookie($token);
    redirect('/~dobiapa2/profile');
}
// Resending
$viewState = ViewData::getInstance();
$viewState->set('form-username',$_POST['username'] ?? '');
if ($csrfLegit) {
    $viewState->set('login-error',LoginFormError::CsrfInvalid);
} else {
    $viewState->set('login-error',LoginFormError::WrongCredentials);
}