<?php
require "api/libs/helpers.php";

$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

// Redirects
if ($route == '') {
    header("Location: ./home", true, 301);
    exit;
}

// These files have to exist because they are included without checking
$commonHead = __DIR__ . '/common/page/head.php';
$commonBodyStart = __DIR__ . '/common/page/body-start.php';
$commonBodyEnd = __DIR__ . '/common/page/body-end.php';
// These are specific templates files for the route, can be .html or .php
// If they are missing, 404 is used instead
$routeHead  = __DIR__ . '/' . $route . '/head';
$routeBody  = __DIR__ . '/' . $route . '/body';

/**
 * 
 * @param string $currentRoute
 * @return string 
 */
function rerouteMiddleware($currentRoute) {
    $newRoute = $currentRoute;
    if (str_starts_with($currentRoute,"projects/")) {

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

// GET pre-processor (optional)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $routeGet  = __DIR__ . '/' . $route . '/get.php';
    $getExists = file_exists($routeGet);
    if ($getExists) {
        include $routeGet;
    }
}
// POST pre-processor (required)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $routePost  = __DIR__ . '/' . $route . '/post.php';
    $postExists = file_exists($routePost);
    if (!$postExists) {
        http_response_code(405);
        echo "POST is not allowed on this URL!";
        exit();
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
