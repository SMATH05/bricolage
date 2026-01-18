<?php

/**
 * Router script for PHP built-in server
 * This file routes all requests to index.php for Symfony
 * Compatible with Render.com deployment
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove query string for file existence check
$filePath = __DIR__ . $requestPath;

// Serve static files directly if they exist
if ($requestPath !== '/' && file_exists($filePath) && is_file($filePath)) {
    return false;
}

// Route all other requests to index.php
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/index.php';
require_once __DIR__ . '/index.php';
