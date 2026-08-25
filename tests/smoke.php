<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';
require_once dirname(__DIR__) . '/app/weather.php';

$failures = [];
$assert = static function (bool $condition, string $message) use (&$failures): void {
    if (!$condition) {
        $failures[] = $message;
    }
};

$assert(astro_url('/Images/example.jpg') === '/Images/example.jpg', 'Root base-path URL failed.');
$data = astro_load_json(dirname(__DIR__) . '/image_card/card_data/site_data_Galaxies_m33.json');
$assert(($data['id'] ?? '') === 'Galaxies_m33', 'Object ID was not normalized.');
$assert(is_file(dirname(__DIR__) . ($data['image']['large'] ?? '')), 'Sample large image is missing.');
$assert(
    realpath((string) astro_object_data_path('Galaxies_m33')) === realpath(dirname(__DIR__) . '/image_card/card_data/site_data_Galaxies_m33.json'),
    'Shared object route did not resolve its content record.'
);
$assert(astro_object_data_path('../config/site') === null, 'Unsafe object route was accepted.');
$assert(strpos(astro_safe_html('<script>alert(1)</script><b>safe</b>'), '<script') === false, 'Unsafe script survived sanitization.');
$assert(astro_observing_city('hanoi')['name'] === 'Hà Nội', 'Default observing city is unavailable.');
$assert(astro_observing_city('unknown') === null, 'Unknown observing city was accepted.');
$assert(is_file(dirname(__DIR__) . '/Tonight.php'), 'Tonight sky page is missing.');
$assert(is_file(dirname(__DIR__) . '/gallery/observable_targets.json'), 'Observable target catalogue is missing.');
$observableTargets = astro_load_json(dirname(__DIR__) . '/gallery/observable_targets.json');
$assert(count($observableTargets) >= 20, 'Observable target catalogue is unexpectedly small.');

$weatherFixture = [
    'hourly' => [
        'time' => ['2026-08-26T20:00', '2026-08-26T21:00'],
        'cloud_cover' => [12, 104],
        'visibility' => [13500, 9000],
        'precipitation_probability' => [5, -4],
        'wind_speed_10m' => [7.24, 9.86],
    ],
];
$weatherCache = sys_get_temp_dir() . '/astro-weather-smoke-' . getmypid() . '.json';
@unlink($weatherCache);
[$weatherStatus, $weatherData] = astro_weather_response(
    'hanoi',
    static fn (string $url): string => json_encode($weatherFixture, JSON_THROW_ON_ERROR),
    1_787_782_400,
    $weatherCache
);
$assert($weatherStatus === 200 && ($weatherData['available'] ?? false), 'Weather fixture did not normalize.');
$assert(($weatherData['hours'][1]['cloudCover'] ?? null) === 100, 'Weather percentages were not clamped.');
$assert(($weatherData['hours'][1]['precipitationChance'] ?? null) === 0, 'Negative weather percentage was not clamped.');
[$staleStatus, $staleData] = astro_weather_response(
    'hanoi',
    static fn (string $url): never => throw new RuntimeException('fixture outage'),
    1_787_784_301,
    $weatherCache
);
$assert($staleStatus === 200 && ($staleData['stale'] ?? false), 'Stale weather cache was not used during an outage.');
[$invalidWeatherStatus] = astro_weather_response('not-a-city', null, 1_787_782_400, $weatherCache);
$assert($invalidWeatherStatus === 400, 'Invalid weather city was not rejected.');
@unlink($weatherCache);

if ($failures) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "PHP smoke tests passed.\n";
