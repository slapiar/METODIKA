<?php

declare(strict_types=1);

// Hostinger fallback bootstrap when website root cannot be changed.
// Redirect root requests to /codei/public while preserving path and query string.
// Use 307 to preserve HTTP method for POST/PUT requests.

$uri = $_SERVER['REQUEST_URI'] ?? '/';

if (str_starts_with($uri, '/codei/public/')) {
    // Avoid redirect loops.
    require __DIR__ . '/codei/public/index.php';
    exit;
}

$target = '/codei/public' . ($uri === '/' ? '/' : $uri);
header('Location: ' . $target, true, 307);
exit;
