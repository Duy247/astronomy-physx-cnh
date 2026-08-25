<?php

declare(strict_types=1);

$requestPath = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$localPath = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $requestPath);
$resolvedPath = realpath($localPath);
$projectRoot = realpath(__DIR__);

if (
    $requestPath !== '/'
    && $resolvedPath !== false
    && $projectRoot !== false
    && str_starts_with($resolvedPath, $projectRoot . DIRECTORY_SEPARATOR)
    && is_file($resolvedPath)
) {
    return false;
}

if (preg_match('~^/image_card/([A-Za-z0-9_-]+)\.php$~', $requestPath, $match)) {
    $_GET['id'] = $match[1];
    require __DIR__ . '/object.php';
    return true;
}

if (preg_match('~^/([A-Za-z0-9_-]+)\.(?:php|htm)$~', $requestPath, $match)) {
    $_GET['page'] = $match[1];
    require __DIR__ . '/legacy_page.php';
    return true;
}

return false;
