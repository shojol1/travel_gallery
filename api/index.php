<?php
/**
 * Vercel Serverless Function Entrypoint & Router
 * Maps incoming HTTP requests to corresponding PHP pages
 */

// Parse requested URI path
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = urldecode($requestUri);

// Root path -> index.php
if ($requestUri === '/' || $requestUri === '' || $requestUri === '/index.php') {
    require __DIR__ . '/../index.php';
    exit;
}

// Clean trip URL route e.g. /trip/coxs-bazar-coastal-expedition
if (preg_match('#^/trip/([^/]+)/?#', $requestUri, $matches)) {
    $_GET['slug'] = $matches[1];
    require __DIR__ . '/../trip.php';
    exit;
}

// Admin index route e.g. /admin or /admin/
if ($requestUri === '/admin' || $requestUri === '/admin/') {
    require __DIR__ . '/../admin/index.php';
    exit;
}

// Remove trailing slash if present
$cleanPath = rtrim($requestUri, '/');
$targetFile = __DIR__ . '/..' . $cleanPath;

// If exact PHP file exists
if (file_exists($targetFile) && is_file($targetFile) && str_ends_with($targetFile, '.php')) {
    require $targetFile;
    exit;
}

// If path without .php extension exists
if (file_exists($targetFile . '.php') && is_file($targetFile . '.php')) {
    require $targetFile . '.php';
    exit;
}

// Fallback to main index.php
require __DIR__ . '/../index.php';
