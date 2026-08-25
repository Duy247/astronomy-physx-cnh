<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

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

if ($failures) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo "PHP smoke tests passed.\n";
