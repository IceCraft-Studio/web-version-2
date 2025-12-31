<?php
if (verifySession($_COOKIE['token'] ?? '') != null) {
    header('Location: /~dobiapa2/profile',true,302);
    exit;
}
initCsrf();

$viewState = ViewData::getInstance();
$viewState->set('login-error',0);
$viewState->set('csrf-error',0);
$viewState->set('login-form-username','');