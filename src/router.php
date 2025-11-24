<?php

$uri = trim(parse_url(substr($_SERVER['REQUEST_URI'],11), PHP_URL_PATH),'/');
$fsPath = __DIR__ . '/' . $uri;

// If the requested path is a real file then serve it directly
if (is_file($fsPath)) {
    $mime = mime_content_type($fsPath);
    header("Content-Type: $mime");
    readfile($fsPath);
    exit;
}

$route = $uri === '' ? 'home' : $uri;

// These files have to exist because they are included without checking
$commonHead = __DIR__ . '/common/page/head.html';
$commonBodyStart = __DIR__ . '/common/page/body-start.html';
$commonBodyEnd = __DIR__ . '/common/page/body-end.html';

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
        return;
    }

    include $file;
}

$headExists = file_exists($routeHead . '.php') || file_exists($routeHead . '.html');
$bodyExists = file_exists($routeBody . '.php') || file_exists($routeBody . '.html');
header("Test: $route $headExists $bodyExists " . !is_dir(__DIR__ . '/' . $route));

if (!is_dir(__DIR__ . '/' . $route) || !$headExists || !$bodyExists) {
    http_response_code(404);

    $routeHead = __DIR__ . '/404/head';
    $routeBody = __DIR__ . '/404/body';
}

// Constructor of all output documents
echo '<!DOCTYPE html><html>';
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
