<?php

declare(strict_types=1);

return [
    'site_name' => 'AstroGallery',
    // Set to '/astronomy' when the folder is served below a domain root.
    'base_path' => getenv('ASTRO_BASE_PATH') ?: '',
    'storage_path' => dirname(__DIR__) . '/storage',
    'observing_cities' => [
        'hanoi' => [
            'name' => 'Hà Nội',
            'latitude' => 21.0285,
            'longitude' => 105.8542,
            'elevation' => 12,
        ],
        'hochiminh' => [
            'name' => 'Hồ Chí Minh City',
            'latitude' => 10.8231,
            'longitude' => 106.6297,
            'elevation' => 19,
        ],
        'danang' => [
            'name' => 'Đà Nẵng',
            'latitude' => 16.0544,
            'longitude' => 108.2022,
            'elevation' => 7,
        ],
    ],
    'navigation' => [
        ['href' => '/Galaxies.php', 'label' => 'Galaxies'],
        ['href' => '/Nebulae.php', 'label' => 'Nebulae'],
        ['href' => '/Clusters.php', 'label' => 'Star Clusters'],
        ['href' => '/SolarSystem.php', 'label' => 'Solar System'],
    ],
];
