<?php
require __DIR__ . "/enums.php";

if (verifySession($_COOKIE['token'] ?? '') != false) {
    redirect('/~dobiapa2/profile');
}
initCsrf('login');

$viewState = ViewData::getInstance();
$viewState->set('form-username','');