<?php
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';

const TWO_DAYS_IN_SECONDS = 60*60*24*2;
$csrfLegit = validateCsrf();
$userPasswordLegit = verifyUserPassword(trim($_POST['username']),$_POST['password']);

if ($userPasswordLegit && $csrfLegit) {
    $token = createSession($_POST['username'],$_POST['password'],TWO_DAYS_IN_SECONDS);
    setcookie('token',$token,expires_or_options:time()+TWO_DAYS_IN_SECONDS,path:'/~dobiapa2',secure:true);
    header('Location: /~dobiapa2/profile',true,302);
    exit;
}
// Resending
$viewState = ViewData::getInstance();
$viewState->set('login-form-username',$_POST['username'] ?? '');
if ($csrfLegit) {
    $viewState->set('login-error',1);
    $viewState->set('csrf-error',0);
} else {
    $viewState->set('login-error',0);
    $viewState->set('csrf-error',1);
}