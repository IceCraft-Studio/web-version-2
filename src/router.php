<?php

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),'/');

$route = $uri === '' ? 'home' : $uri;

$commonHead = __DIR__ . '/common/page/head';
$commonBody = __DIR__ . '/common/page/body';

$routeHead  = __DIR__ . '/' . $route . '/head';
$routeBody  = __DIR__ . '/' . $route . '/body';

function includeTemplateFile($basePath) {
    $file = null;
    if (file_exists($basePath . '.php')) {
        $file = $basePath . '.php';
    } elseif (file_exists($basePath . '.html')) {
        $file = $basePath . '.html';
    }

    if (!$file) {
        return '';
    }

    //ob_start();
    include $file;
    //return ob_get_clean();
}

// Check if route exists with at least one head and body file
$headExists = file_exists($routeHead . '.php') || file_exists($routeHead . '.html');
$bodyExists = file_exists($routeBody . '.php') || file_exists($routeBody . '.html');

if (!is_dir(__DIR__ . '/' . $route) || !$headExists || !$bodyExists) {
    // 404 fallback
    http_response_code(404);

    $routeHead = __DIR__ . '/404/head';
    $routeBody = __DIR__ . '/404/body';
}

// Constructor of all output documents
echo '<!DOCTYPE html><html>';
echo '<head>';
includeTemplateFile($commonHead);
includeTemplateFile($routeHead);
echo '</head>';
echo '<body>';
includeTemplateFile($commonBody);
includeTemplateFile($routeBody);
echo '</body>';
echo '</html>';
