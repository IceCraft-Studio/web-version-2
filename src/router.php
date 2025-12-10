<?php
//# Libs
require __DIR__ . "api/libs/helpers.php";

//# Functions
/**
 * Primitive middleware that works by rerouting the request based on the route.
 * @param string $currentRoute Current **normalized** route URI.
 * @return string The new nornalized route URI, it returns the input one if no rerouting occurs.
 */
function rerouteMiddleware($currentRoute) {
    $newRoute = $currentRoute;
    if (str_starts_with($currentRoute,"projects/")) {
        $newRoute = "home";
    }
    return $newRoute;
}

function includeTemplateFile($basePath) {
    $file = null;
    if (file_exists("$basePath.php")) {
        $file = "$basePath.php";
    } elseif (file_exists("$basePath.html")) {
        $file = "$basePath.html";
    }
    include $file;
}

//# Script
$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

// Redirects
if ($route == '') {
    header("Location: ./home", true, 301);
    exit;
}

// These are files defining parts of the HTML common to all pages
$commonHead = __DIR__ . '/common/page/head.php';
$commonBodyStart = __DIR__ . '/common/page/body-start.php';
$commonBodyEnd = __DIR__ . '/common/page/body-end.php';

$route = rerouteMiddleware($route);

// These are specific templates files for the route, can be .html or .php
// If they are missing, 404 is used instead
$routeHead  = __DIR__ . '/' . $route . '/head';
$routeBody  = __DIR__ . '/' . $route . '/body';

// GET pre-processor (optional)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $routeGet  = __DIR__ . '/' . $route . '/get.php';
    include $routeGet;
}

// POST pre-processor (required)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $routePost  = __DIR__ . '/' . $route . '/post.php';
    $postExists = file_exists($routePost);
    if (!$postExists) {
        http_response_code(405);
        echo "POST is not allowed on this URL!";
        exit; // `post.php` is required to allow POST on a URL, hence the `exit` function
    }
    include $routePost;
}

// Checks from existing path, otherwise sends a 404 page
$headExists = file_exists($routeHead . '.php') || file_exists($routeHead . '.html');
$bodyExists = file_exists($routeBody . '.php') || file_exists($routeBody . '.html');

if (!is_dir(__DIR__ . '/' . $route) || !$headExists || !$bodyExists) {
    http_response_code(404);
    $routeHead = __DIR__ . '/404/head';
    $routeBody = __DIR__ . '/404/body';
}

// Constructor of the HTML response
echo '<!DOCTYPE html>';
echo '<html data-theme="dark">';
echo '<head>';
include $commonHead;
includeTemplateFile($routeHead);
echo '</head>';
echo '<body>';
include $commonBodyStart;
includeTemplateFile($routeBody);
include $commonBodyEnd;
echo '</body>';
echo '</html>';
