<?php
$viewState = ViewData::getInstance();

// Redirect if token isn't in cookie
$userToken = $_COOKIE['token'];
if (!isset($userToken)) {
    header('Location: /~dobiapa2/login', true, 302);
    exit;
}

// Redirect if token is invalid
$username = verifySession($userToken);
if ($username == null) {
    setcookie('token','',expires_or_options: time()-9999, secure: true);
    header('Location: /~dobiapa2/login', true, 302);
    exit;
}

// CSRF Protection
session_start();
$_SESSION['csrf-token'] = bin2hex(random_bytes(32));

// Prepare fields for the page
$viewState->set('username', $username);
$viewState->set('profile-role','admin');
$viewState->set('profile-age','2 days');
$viewState->set('profile-form-display-name','test');
$viewState->set('profile-form-email','mail@example.com');
$viewState->set('profile-form-social-website','');
$viewState->set('profile-form-social-reddit','');
$viewState->set('profile-form-social-twitter','');
$viewState->set('profile-form-social-instagram','');
$viewState->set('profile-form-social-discord','');