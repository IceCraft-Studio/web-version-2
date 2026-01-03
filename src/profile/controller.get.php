<?php
require_once __DIR__ . '/enums.php';

const SECONDS_IN_MINUTE = 60;
const SECONDS_IN_HOUR = SECONDS_IN_MINUTE*60;
const SECONDS_IN_DAY = SECONDS_IN_HOUR*24;

$viewState = ViewData::getInstance();

// Redirect if token is invalid
$username = $viewState->get('verified-username','');
if ($username === '') {
    redirect('/~dobiapa2/login');
}

initCsrf();

// Retrieve user information
$userData = getUserData($username);
if ($userData === false) {
// Handle later
}

// Human Readable Account Age
$created = strtotime($userData['datetime_created']);
$now = time();
$secondsSince = $now - $created;
if ($secondsSince < SECONDS_IN_MINUTE) {
    $ageDisplay = 'less than 1 minute';
} else if ($secondsSince < SECONDS_IN_HOUR) {
    $ageDisplay = (string)(floor($secondsSince / SECONDS_IN_MINUTE)) . ' minutes';
} else if ($secondsSince < SECONDS_IN_DAY) {
    $ageDisplay = (string)(floor($secondsSince / SECONDS_IN_HOUR)) . ' hours';
} else {
    $ageDisplay = (string)(floor($secondsSince / SECONDS_IN_DAY)) . ' days';
}

// Prepare fields for the page
$viewState->set('username', $username);
$viewState->set('profile-role',$userData['role']);
$viewState->set('profile-age',$ageDisplay);
$viewState->set('form-display-name',$userData['display_name']);
$viewState->set('form-email',$userData['email']);
$viewState->set('form-social-website',$userData['social_website']);
$viewState->set('form-social-reddit',$userData['social_reddit']);
$viewState->set('form-social-twitter',$userData['social_twitter']);
$viewState->set('form-social-instagram',$userData['social_instagram']);
$viewState->set('form-social-discord',$userData['social_discord']);