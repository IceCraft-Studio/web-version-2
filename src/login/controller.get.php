<?php
initCsrf();

$viewState = ViewData::getInstance();
$viewState->set('login-error',0);
$viewState->set('csrf-error',0);
$viewState->set('login-form-username','');