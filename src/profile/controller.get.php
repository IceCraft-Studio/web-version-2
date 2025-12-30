<?php
$viewState = ViewData::getInstance();

// Redirect if token isn't in cookie
$userToken = $_COOKIE['token'];
if (!isset($userToken)) {
    header('Location: ../login', true, 302);
    exit();
}

// Redirect if token is invalid
$username = verifySession($userToken);
if ($username == null) {
    setcookie('token','',expires_or_options: time()-9999, secure: true);
    header('Location: ../login', true, 302);
    exit();
}

// CSRF Protection
session_start();
$_SESSION['csrf-token'] = bin2hex(random_bytes(32));

// Prepare fields for the page
$viewState->set('username', $username);
$viewState->set('profile-form-display-name','test');
$viewState->set('profile-form-email','mail@example.com');
$viewState->set('profile-role','admin');
$viewState->set('profile-age','2 days');