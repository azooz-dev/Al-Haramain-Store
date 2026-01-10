<?php
/**
 * Laravel Development Server Router
 * 
 * This router script handles static files correctly when using PHP's built-in server
 * Static files are served directly, all other requests go to index.php
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Check if the request is for a static file that exists
$publicPath = __DIR__ . $uri;

if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    // Get the file extension
    $extension = pathinfo($publicPath, PATHINFO_EXTENSION);
    
    // Set proper content type for common file types
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ];
    
    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }
    
    // Return false to let PHP's built-in server handle the static file
    return false;
}

// Route everything else through Laravel's index.php
require_once __DIR__ . '/index.php';
