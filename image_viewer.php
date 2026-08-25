<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
$requested = rawurldecode((string) ($_GET['img'] ?? ''));
$relative = ltrim(str_replace('\\', '/', $requested), '/');
$extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
$imagesRoot = realpath(__DIR__ . '/Images');
$resolved = $relative !== '' ? realpath(__DIR__ . '/' . $relative) : false;
if (!$imagesRoot || !$resolved || !str_starts_with($relative, 'Images/') || !str_starts_with(strtolower($resolved), strtolower($imagesRoot . DIRECTORY_SEPARATOR)) || !in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
    astro_not_found('The requested image is unavailable or is not an approved image type.');
}
$imageUrl = astro_url('/' . $relative);
$imageLabel = trim(str_replace(['_', '-'], ' ', pathinfo($relative, PATHINFO_FILENAME)));
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Image Viewer — <?= astro_escape((string) astro_config('site_name')) ?></title>
<link rel="icon" href="<?= astro_escape(astro_url('/assets/branding/favicon.ico')) ?>" sizes="any">
<link rel="stylesheet" href="<?= astro_escape(astro_url('/css/image-viewer.css')) ?>">
</head><body class="iv-page">
<main class="iv-workspace">
    <header class="iv-header">
        <button class="iv-back" type="button" data-viewer-back data-home="<?= astro_escape(astro_url('/index.php')) ?>" aria-label="Return to the previous page"><span aria-hidden="true">←</span> Back</button>
        <div class="iv-title"><strong><?= astro_escape($imageLabel ?: 'Astronomy image') ?></strong><small>Pinch or scroll to zoom · drag to explore</small></div>
        <a class="iv-download" href="<?= astro_escape($imageUrl) ?>" download>Download</a>
    </header>
    <div id="iv-viewer" class="iv-viewer" data-source="<?= astro_escape($imageUrl) ?>" data-prefix="<?= astro_escape(astro_url('/assets/vendor/openseadragon/images/')) ?>" aria-label="Zoomable astronomy image viewer">
        <p class="iv-status" data-viewer-status>Loading full-resolution image…</p>
    </div>
    <noscript><p class="iv-noscript"><a href="<?= astro_escape($imageUrl) ?>">Open the original image</a></p></noscript>
</main>
<script src="<?= astro_escape(astro_url('/assets/vendor/openseadragon/openseadragon.min.js')) ?>"></script>
<script src="<?= astro_escape(astro_url('/js/image-viewer.js')) ?>"></script>
</body></html>
