<?php

declare(strict_types=1);

// Shared-hosting shim for installations where web root points to /codei.
// It works both for:
// - https://subdomain.tld/      (codei as document root)
// - https://domain.tld/codei/   (codei as URL subpath)

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';

$scriptDir = str_replace('\\', '/', dirname($scriptName));
if ($scriptDir === '/' || $scriptDir === '.') {
    $scriptDir = '';
}

$path = parse_url($requestUri, PHP_URL_PATH);
$query = parse_url($requestUri, PHP_URL_QUERY);
$path = is_string($path) ? $path : '/';
$queryString = is_string($query) && $query !== '' ? '?' . $query : '';

$publicPrefix = ($scriptDir === '' ? '' : $scriptDir) . '/public';

if ($path === $publicPrefix || str_starts_with($path, $publicPrefix . '/')) {
    require __DIR__ . '/public/index.php';
    exit;
}

$relativePath = $path;
if ($scriptDir !== '' && str_starts_with($relativePath, $scriptDir)) {
    $relativePath = substr($relativePath, strlen($scriptDir));
    $relativePath = $relativePath === '' ? '/' : $relativePath;
}

$target = $publicPrefix . ($relativePath === '/' ? '/' : $relativePath) . $queryString;
header('Location: ' . $target, true, 307);
exit;
