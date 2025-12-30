<?php
$viewState = ViewData::getState();

// Current Page Logic
$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

if (str_starts_with($route,'home')) {
    $viewState['current-page'] = "home";
} elseif (str_starts_with($route,'projects') || str_starts_with($route,'users/')) {
    $viewState['current-page'] = "projects";
} elseif (str_starts_with($route,'about')) {
    $viewState['current-page'] = "about";
} elseif (str_starts_with($route,'login') || str_starts_with($route,'register')  || str_starts_with($route,'profile')  ) {
    $viewState['current-page'] = "user";
}

// User Display Logic
$viewState['username'] = "Login";
$viewState['user-link'] = "/~dobiapa2/login";
$viewState['user-profile-picture'] = "/~dobiapa2/assets/icons/steve.webp";
