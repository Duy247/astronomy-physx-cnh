<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
$pageTitle ??= (string) astro_config('site_name');
$navigation = astro_config('navigation');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Astronomy images, observing details, processing resources, and raw FITS data from Misti Mountain Observatory.">
    <title><?= astro_escape($pageTitle) ?></title>
    <link rel="icon" href="<?= astro_escape(astro_url('/assets/branding/favicon.ico')) ?>" sizes="any">
    <link rel="stylesheet" href="<?= astro_escape(astro_url('/css/astro-modern.css?v=20260825m')) ?>">
    <script src="<?= astro_escape(astro_url('/js/site.js?v=20260825m')) ?>" defer></script>
</head>
<body>
<a class="astro-skip-link" href="#main-content">Skip to content</a>
<div class="astro-dim-overlay" data-nav-overlay></div>
<header class="astro-header">
    <div class="astro-header-inner">
        <a class="astro-header-brand" href="<?= astro_escape(astro_url('/index.php')) ?>">
            <img src="<?= astro_escape(astro_url('/assets/branding/physx-cnh-logo.png')) ?>" alt="PhysX-CNH" class="astro-logo">
            <span class="astro-brand-context">Astro<br>Archive</span>
        </a>
        <nav class="astro-nav" id="site-navigation" data-nav aria-label="Primary navigation">
            <?php foreach ($navigation as $index => $link): ?>
                <a href="<?= astro_escape(astro_url((string) $link['href'])) ?>"><span class="astro-nav-index" aria-hidden="true"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span><span class="astro-nav-label"><?= astro_escape((string) $link['label']) ?></span><span class="astro-nav-arrow" aria-hidden="true">↗</span></a>
            <?php endforeach; ?>
        </nav>
        <a class="astro-hub-link" href="https://physx-cnh.com" rel="noopener">Study Hub <span aria-hidden="true">↗</span></a>
        <button class="astro-nav-toggle" type="button" data-nav-toggle aria-label="Open navigation" aria-expanded="false" aria-controls="site-navigation"><span class="astro-nav-toggle-label">Explore</span><span class="astro-nav-toggle-icon" aria-hidden="true"></span></button>
    </div>
</header>
