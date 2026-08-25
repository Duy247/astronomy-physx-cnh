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
    <link rel="icon" href="<?= astro_escape(astro_url('/assets/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= astro_escape(astro_url('/css/astro-modern.css')) ?>">
    <script src="<?= astro_escape(astro_url('/js/site.js')) ?>" defer></script>
</head>
<body>
<div class="astro-dim-overlay" data-nav-overlay></div>
<header class="astro-header">
    <div class="astro-header-inner">
        <a class="astro-header-brand" href="<?= astro_escape(astro_url('/index.php')) ?>">
            <img src="<?= astro_escape(astro_url('/assets/logo.svg')) ?>" alt="<?= astro_escape((string) astro_config('site_name')) ?>" class="astro-logo">
            <small>Misti Mountain Observatory</small>
        </a>
        <button class="astro-nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-controls="site-navigation">Explore <span aria-hidden="true">☰</span></button>
        <nav class="astro-nav" id="site-navigation" data-nav aria-label="Primary navigation">
            <?php foreach ($navigation as $link): ?>
                <a href="<?= astro_escape(astro_url((string) $link['href'])) ?>"><?= astro_escape((string) $link['label']) ?></a>
            <?php endforeach; ?>
        </nav>
        <button class="astro-dark-toggle" type="button" data-theme-toggle aria-label="Toggle color theme" title="Toggle color theme">◐</button>
    </div>
</header>
