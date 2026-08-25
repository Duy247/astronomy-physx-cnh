<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';

$dataPath = astro_object_data_path((string) ($_GET['id'] ?? ''));
if ($dataPath === null) {
    astro_not_found();
}

astro_render_object($dataPath);
