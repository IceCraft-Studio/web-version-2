<?php
//# Libs
require __DIR__ . "/api/libs/helpers.php";
require __DIR__ . "/api/libs/view-data.php";

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

/**
 * Checks if the current requested URI points to a directory, otherwise returns 404 route and set the HTTP status code.
 * @param string $requestRoute Requested URI as **normalized** route.
 * @return string Route where 404 controller exists if it's not a directory, otherwise the input route.
 */
function rerouteNotFound($requestRoute)
{
    if (!is_dir(__DIR__ . '/' . $requestRoute)) {
        http_response_code(404);
        return "middleware/404";
    }
    ;
    return $requestRoute;
}

/**
 * Loads controller for the specific page and method. Exits if the method isn't allowed.
 * The controller file should fill the static instance of `ViewData` class used by route views.
 * @param string $requestRoute Requested URI as **normalized** route.
 * @param string $requestMethod The method to load the controller for. Usually from `$_SERVER['REQUEST_METHOD']`.
 * @return void
 */
function loadMethodController($requestRoute, $requestMethod)
{
    $requestMethod = strtolower($requestMethod);
    $controllerPath = __DIR__ . '/' . $requestRoute . '/controller.' . $requestMethod . '.php';
    if (!file_exists($controllerPath)) {
        http_response_code(405);
        echo 'Method ' . strtoupper($requestMethod) . ' is not allowed on this URL!';
        exit;
    }
    include $controllerPath;
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
    echo '<html data-theme="dark">';
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
 * There are views and controllers common for all pages.
 * @var string
 */
const COMMON_ROUTE = 'common/page';

/**
 * The normalized route used to determine which controllers and views to load.
 * @var string
 */
$route = normalizeUriRoute($_SERVER['REQUEST_URI']);

// Redirects and reroutes can occur here. Redirects are HTTP 3xx, reroutes are for internal logic only. 
redirects($route);

$route = rerouteMiddleware($route);
$route = rerouteNotFound($route);

// From here on the route doesn't change and the controllers and views kick in.
loadMethodController(COMMON_ROUTE, $_SERVER['REQUEST_METHOD']);
loadMethodController($route, $_SERVER['REQUEST_METHOD']);

composeHtmlViews($route, COMMON_ROUTE);
