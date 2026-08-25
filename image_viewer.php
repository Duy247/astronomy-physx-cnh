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
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Image Viewer — <?= astro_escape((string) astro_config('site_name')) ?></title>
<link rel="stylesheet" href="<?= astro_escape(astro_url('/css/astro-modern.css')) ?>"><link rel="stylesheet" href="<?= astro_escape(astro_url('/css/image-viewer.css')) ?>">
</head><body class="iv-page"><main class="iv-workspace">
<nav class="iv-menubar-ps" aria-label="Image tools"><ul class="iv-menu-list">
<li class="iv-menu-item"><button class="iv-menu-btn" type="button">File</button><ul class="iv-menu-dropdown"><li><button id="iv-reset" type="button">Reset</button></li><li><a id="iv-download" href="<?= astro_escape($imageUrl) ?>" download>Download</a></li></ul></li>
<li class="iv-menu-item"><button class="iv-menu-btn" type="button">Edit</button><ul class="iv-menu-dropdown"><li><button id="iv-clear" type="button">Clear drawing</button></li></ul></li>
<li class="iv-menu-item"><button class="iv-menu-btn" type="button">Image</button><ul class="iv-menu-dropdown"><li><label>Brightness <input type="range" id="iv-brightness" min="-100" max="100" value="0"></label></li><li><label>Contrast <input type="range" id="iv-contrast" min="-100" max="100" value="0"></label></li></ul></li>
<li class="iv-menu-item"><button class="iv-menu-btn" type="button">Draw</button><ul class="iv-menu-dropdown" id="iv-tools"><li><button class="iv-tool-btn" data-tool="pen" type="button">Pen</button></li><li><button class="iv-tool-btn" data-tool="rect" type="button">Rectangle</button></li><li><button class="iv-tool-btn" data-tool="circle" type="button">Circle</button></li><li><button class="iv-tool-btn" data-tool="none" type="button">No drawing</button></li></ul></li>
</ul></nav><div id="iv-canvas-wrap"><canvas id="iv-image" data-source="<?= astro_escape($imageUrl) ?>" aria-label="Editable astronomy image"></canvas></div><noscript><p><a href="<?= astro_escape($imageUrl) ?>">Open the original image</a></p></noscript>
</main><script src="<?= astro_escape(astro_url('/js/image-viewer.js')) ?>"></script></body></html>
