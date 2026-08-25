<?php

declare(strict_types=1);

require_once __DIR__ . '/app/weather.php';

[$status, $payload] = astro_weather_response(strtolower(trim((string) ($_GET['city'] ?? 'hanoi'))));
http_response_code($status);
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: public, max-age=300, stale-if-error=21600');
echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

