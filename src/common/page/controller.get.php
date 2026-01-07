<?php
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/session.php';
require $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/api/libs/models/user.php';

$viewState = ViewData::getInstance();

// Current Page Logic
$currentRoute = normalizeUriRoute($_SERVER['REQUEST_URI']);
$viewState->set('normalized-route',$currentRoute);

if (str_starts_with($currentRoute, 'home')) {
    $viewState->set('current-page', 'home');
} elseif (str_starts_with($currentRoute, 'projects') || str_starts_with($currentRoute, 'users/')) {
    $viewState->set('current-page', "projects");
} elseif (str_starts_with($currentRoute, 'about')) {
    $viewState->set('current-page', 'about');
} elseif (str_starts_with($currentRoute, 'login') || str_starts_with($currentRoute, 'register') || str_starts_with($currentRoute, 'profile')) {
    $viewState->set('current-page', 'user');
}

// User Display Logic
$username = verifySession($_COOKIE['token'] ?? '');
if ($username != null) {
    $userData = getUserData($username);
    if (($userData['display_name'] ?? '') === '') {
        $displayName = $username;
    } else {
        $displayName = $userData['display_name'];
    }

    $viewState->set('verified-display-name',$displayName);
    $viewState->set('verified-profile-link', '/~dobiapa2/profile');
    $viewState->set('verified-profile-picture', '/~dobiapa2/api/internal/users/profile-picture.php?variant=preview&username=' . $username);
} else {
    $viewState->set('verified-display-name', 'Login');
    $viewState->set('verified-profile-link', '/~dobiapa2/login');
    $viewState->set('verified-profile-picture', '/~dobiapa2/assets/icons/steve.webp');
    if (isset($_COOKIE['token'])) {
        updateSessionCookie('',-99999);
    }
}

// Sets verified data for all future controllers
$viewState->set('verified-username', $username ?? '');
$viewState->set('verified-role', $userData['role'] ?? '');