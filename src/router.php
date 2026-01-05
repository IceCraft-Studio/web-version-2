<?php
//# Libs
require __DIR__ . "/db-setup.php";
require __DIR__ . "/api/libs/helpers.php";
require __DIR__ . "/api/libs/classes/view-data.php";

//# Constants
/**
 * There are views and controllers common for all pages.
 * @var string
 */
const COMMON_ROUTE = 'common/page';
/**
 * This is a fallback route for error responses.
 * @var string
 */
const ERROR_ROUTE = 'middleware/error';

//# Functions
/**
 * Sets up rules to redirect specific routes and exits the script when redirecting.
 * @param string $route Requested URI as **normalized** route to check against. Some rules may not use it.
 * @return void
 */
function redirects($route)
{
    // This router is for non-files only, so everything should be interperted as directory!!
    if (!str_ends_with($_SERVER['REQUEST_URI'], "/")) {
        echo count($_GET);
        exit;
        header("Location: {$_SERVER['REQUEST_URI']}/", true, 301);
        exit;
    }
    // Nothing goes home
    if ($route == '') {
        header("Location: ./home", true, 301);
        exit;
    }
}

/**
 * Primitive middleware that works by rerouting the request based on a regex match of the route.
 * @param string $requestRoute Requested URI as **normalized** route.
 * @return string The new normalized route URI, it returns the input one if no rerouting occurs.
 */
function rerouteMiddleware($requestRoute)
{
    // projects/ (to reuse with category specific one below)
    if (preg_match("/^projects\/$/", $requestRoute)) {
        return "middleware/projects";
    }
    // projects/* (* - category id from database)
    if (preg_match("/^projects\/[^\/]*$/", $requestRoute)) {
        return "middleware/projects";
    }
    // projects/**/* (* - project slug from database)
    if (preg_match("/^projects\/[^\/]*\/[^\/]*$/", $requestRoute)) {
        return "middleware/projects-page";
    }
    // users/* (* - username from database)
    if (preg_match("/^users\/[^\/]*$/", $requestRoute)) {
        return "middleware/users-page";
    }
    return $requestRoute;
}

//! Handle 404 here and make it work for general errors. By checking if directory is missing AFTER checking the file
//! and there setting a global response code and load an error controller instead
/**
 * Loads controller for the specific page and method. Exits if the method isn't allowed.
 * The controller file should fill the static instance of `ViewData` class used by route views.
 * @param string $requestRoute Requested URI as **normalized** route.
 * @param string $requestMethod The method to load the controller for. Usually from `$_SERVER['REQUEST_METHOD']`.
 * @return string When the controller is missing the route is replaced with the error route
 */
function loadMethodController($requestRoute, $requestMethod)
{
    $requestMethod = strtolower($requestMethod);
    // HEAD method should respond with identical headers to GET.
    if ($requestMethod === 'head') {
        $requestMethod = 'get';
    }
    // Check for exisiting controller 
    $controllerPath = __DIR__ . '/' . $requestRoute . '/controller.' . $requestMethod . '.php';
    if (!file_exists($controllerPath)) {
        if (is_dir(__DIR__ . '/' . $requestRoute)) {
            http_response_code(405); // Directory exists so method is disallowed.
        } else {
            http_response_code(404); // Directory is missing so the resource is not found.
        }
    } else {
        include $controllerPath;
    }

    // Rerouting to a designated error page occurs when there is an error. The status code may be set by this function or the controller itself.
    if (http_response_code() >= 400) {
        include __DIR__ . '/' . ERROR_ROUTE . '/controller.' . $requestMethod . '.php';
        return ERROR_ROUTE;
    }
    return $requestRoute;
}

/**
 * Loads controller for the specific route based on its name.
 * The view file should expect data in the static instance of `ViewData` class.
 * @param string $requestRoute Requested URI as **normalized** route.
 * @param string $requestMethod The method to load the controller for. Usually from `$_SERVER['REQUEST_METHOD']`.
 * @return void
 */
function loadRouteView($requestRoute, $viewName)
{
    $viewPath = __DIR__ . '/' . $requestRoute . '/view.' . $viewName . '.php';
    if (!file_exists($viewPath)) {
        return;
    }
    include $viewPath;
}

/**
 * Echo's the final HTML file composed of common and route specific components.
 * @param string $requestRoute URI for route specific components as **normalized** route.
 * @param string $commonRoute URI for common components as **normalized** route.
 * @return void
 */
function composeHtmlViews($requestRoute, $commonRoute)
{
    echo '<!DOCTYPE html>';
    echo '<html lang="en" data-theme="dark">';
    echo '<head>';
    loadRouteView($commonRoute, 'head');
    loadRouteView($requestRoute, 'head');
    echo '</head>';
    echo '<body>';
    loadRouteView($commonRoute, 'body-start');
    loadRouteView($requestRoute, 'body');
    loadRouteView($commonRoute, 'body-end');
    echo '</body>';
    echo '</html>';
}
//# Script
/**
 * The normalized route used to determine which controllers and views to load.
 * @var string
 */
$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

redirects($route);

$route = rerouteMiddleware($route);

loadMethodController(COMMON_ROUTE, $_SERVER['REQUEST_METHOD']);
$route = loadMethodController($route, $_SERVER['REQUEST_METHOD']);

// HEAD method doesn't have response contents
if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    exit;
}
composeHtmlViews($route, COMMON_ROUTE);
