<?php
require __DIR__ . "/enums.php";

if (verifySession($_COOKIE['token'] ?? '') != null) {
    redirect('/~dobiapa2/profile');
}
initCsrf();

$viewState = ViewData::getInstance();
$viewState->set('form-username','');