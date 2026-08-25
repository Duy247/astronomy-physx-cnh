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
        ['href' => '/178ED.php', 'label' => '7\" Refractor'],
        ['href' => '/500mm.php', 'label' => '500mm Lens'],
        ['href' => '/index_fits.php', 'label' => 'FITS Files'],
        ['href' => '/index.php', 'label' => 'Home'],
    ],
];
