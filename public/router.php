<?php

/**
 * Router script for PHP built-in server
 * This file routes all requests to index.php for Symfony
 */

if (file_exists(__DIR__ . '/' . $_SERVER['REQUEST_URI'])) {
    // Serve the requested resource as-is.
    return false;
}

// Route everything else to index.php
require_once __DIR__ . '/index.php';
