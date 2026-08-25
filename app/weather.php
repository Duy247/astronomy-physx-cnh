<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

const ASTRO_WEATHER_FRESH_SECONDS = 1800;
const ASTRO_WEATHER_STALE_SECONDS = 21600;

function astro_weather_cache_path(string $cityId): string
{
    return rtrim((string) astro_config('storage_path'), '/\\') . '/cache/weather-' . $cityId . '.json';
}

function astro_weather_fetch_url(array $city): string
{
    return 'https://api.open-meteo.com/v1/forecast?' . http_build_query([
        'latitude' => $city['latitude'],
        'longitude' => $city['longitude'],
        'hourly' => 'cloud_cover,visibility,precipitation_probability,wind_speed_10m',
        'timezone' => 'Asia/Ho_Chi_Minh',
        'forecast_days' => 3,
    ], '', '&', PHP_QUERY_RFC3986);
}

function astro_weather_http_get(string $url): string
{
    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 4,
            CURLOPT_TIMEOUT => 7,
            CURLOPT_USERAGENT => 'AstroGallery/1.0 weather preview',
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $error = curl_error($handle);
        curl_close($handle);
        if (!is_string($body) || $status !== 200) {
            throw new RuntimeException($error !== '' ? $error : 'Weather provider returned HTTP ' . $status);
        }
        return $body;
    }

    $context = stream_context_create(['http' => [
        'method' => 'GET',
        'timeout' => 7,
        'ignore_errors' => true,
        'header' => "Accept: application/json\r\nUser-Agent: AstroGallery/1.0 weather preview\r\n",
    ]]);
    $body = @file_get_contents($url, false, $context);
    if (!is_string($body)) {
        throw new RuntimeException('Weather provider is unavailable.');
    }
    return $body;
}

function astro_weather_normalize(array $source, array $city, int $fetchedAt): array
{
    $hourly = $source['hourly'] ?? null;
    $fields = ['time', 'cloud_cover', 'visibility', 'precipitation_probability', 'wind_speed_10m'];
    if (!is_array($hourly)) {
        throw new RuntimeException('Weather response is missing hourly data.');
    }
    foreach ($fields as $field) {
        if (!isset($hourly[$field]) || !is_array($hourly[$field])) {
            throw new RuntimeException('Weather response is missing ' . $field . '.');
        }
    }

    $count = min(array_map(static fn (string $field): int => count($hourly[$field]), $fields));
    if ($count < 1) {
        throw new RuntimeException('Weather response has no forecast hours.');
    }
    $hours = [];
    for ($index = 0; $index < $count; $index++) {
        $hours[] = [
            'time' => (string) $hourly['time'][$index],
            'cloudCover' => max(0, min(100, (int) round((float) $hourly['cloud_cover'][$index]))),
            'visibilityMetres' => max(0, (int) round((float) $hourly['visibility'][$index])),
            'precipitationChance' => max(0, min(100, (int) round((float) $hourly['precipitation_probability'][$index]))),
            'windKmh' => max(0, round((float) $hourly['wind_speed_10m'][$index], 1)),
        ];
    }

    return [
        'available' => true,
        'city' => ['id' => $city['id'], 'name' => $city['name']],
        'timezone' => 'Asia/Ho_Chi_Minh',
        'fetchedAt' => gmdate(DATE_ATOM, $fetchedAt),
        'stale' => false,
        'hours' => $hours,
        'attribution' => [
            'label' => 'Weather data by Open-Meteo.com',
            'url' => 'https://open-meteo.com/',
        ],
    ];
}

function astro_weather_read_cache(string $path): ?array
{
    if (!is_file($path) || !is_readable($path)) {
        return null;
    }
    try {
        $value = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        return is_array($value) && isset($value['cachedAt'], $value['data']) && is_array($value['data']) ? $value : null;
    } catch (Throwable) {
        return null;
    }
}

function astro_weather_write_cache(string $path, int $cachedAt, array $data): void
{
    $directory = dirname($path);
    if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
        return;
    }
    @file_put_contents($path, json_encode(['cachedAt' => $cachedAt, 'data' => $data], JSON_UNESCAPED_SLASHES), LOCK_EX);
}

function astro_weather_response(string $cityId, ?callable $fetcher = null, ?int $now = null, ?string $cachePath = null): array
{
    $city = astro_observing_city($cityId);
    if ($city === null) {
        return [400, ['available' => false, 'error' => 'Unknown observing city.']];
    }

    $now ??= time();
    $cachePath ??= astro_weather_cache_path($cityId);
    $cached = astro_weather_read_cache($cachePath);
    if ($cached !== null && $now - (int) $cached['cachedAt'] <= ASTRO_WEATHER_FRESH_SECONDS) {
        return [200, $cached['data']];
    }

    try {
        $fetcher ??= 'astro_weather_http_get';
        $source = json_decode((string) $fetcher(astro_weather_fetch_url($city)), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($source)) {
            throw new RuntimeException('Weather provider returned invalid data.');
        }
        $data = astro_weather_normalize($source, $city, $now);
        astro_weather_write_cache($cachePath, $now, $data);
        return [200, $data];
    } catch (Throwable $error) {
        astro_log($error);
        if ($cached !== null && $now - (int) $cached['cachedAt'] <= ASTRO_WEATHER_STALE_SECONDS) {
            $data = $cached['data'];
            $data['stale'] = true;
            return [200, $data];
        }
        return [503, [
            'available' => false,
            'city' => ['id' => $city['id'], 'name' => $city['name']],
            'error' => 'Observing weather is temporarily unavailable.',
        ]];
    }
}

