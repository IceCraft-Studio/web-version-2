<?php
$viewState = ViewData::getInstance();

// Current Page Logic
$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

if (str_starts_with($route,'home')) {
    $viewState->set('current-page', 'home');
} elseif (str_starts_with($route,'projects') || str_starts_with($route,'users/')) {
    $viewState->set('current-page', "projects");
} elseif (str_starts_with($route,'about')) {
    $viewState->set('current-page', 'about');
} elseif (str_starts_with($route,'login') || str_starts_with($route,'register')  || str_starts_with($route,'profile')  ) {
    $viewState->set('current-page', 'user');
}

// User Display Logic
$viewState->set('username', 'Login');
$viewState->set('user-link', '/~dobiapa2/login');
$viewState->set('user-profile-picture', '/~dobiapa2/assets/icons/steve.webp');