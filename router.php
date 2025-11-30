<?php
// Development router for PHP built-in server
// Place this file in the project root (next to index.php) and start
// the server with: php -S localhost:8080 router.php

// Decode URI and map to real file if exists
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$requested = __DIR__ . $uri;

// If the requested resource is a real file (css, js, images, etc.), let the server serve it
if ($uri !== '/' && file_exists($requested) && is_file($requested)) {
    return false; // serve the requested resource as-is
}

// Otherwise route through front controller
require_once __DIR__ . '/index.php';
