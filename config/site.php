<?php

declare(strict_types=1);

return [
    'site_name' => 'AstroGallery',
    // Set to '/astronomy' when the folder is served below a domain root.
    'base_path' => getenv('ASTRO_BASE_PATH') ?: '',
    'storage_path' => dirname(__DIR__) . '/storage',
    'navigation' => [
        ['href' => '/Galaxies.php', 'label' => 'Galaxies'],
        ['href' => '/Nebulae.php', 'label' => 'Nebulae'],
        ['href' => '/Clusters.php', 'label' => 'Star Clusters'],
        ['href' => '/SolarSystem.php', 'label' => 'Solar System'],
        ['href' => '/index_fits.php', 'label' => 'FITS Data'],
    ],
];
