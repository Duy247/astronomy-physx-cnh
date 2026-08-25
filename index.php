<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
$pageTitle = 'Misti Mountain Observatory — AstroGallery';
$surpriseCatalog = [];
$surpriseSeen = [];
$surpriseSources = [
    ['Galaxy', __DIR__ . '/gallery/gallery_galaxies.json'],
    ['Nebula', __DIR__ . '/gallery/gallery_nebulae.json'],
    ['Star cluster', __DIR__ . '/gallery/gallery_clusters.json'],
    ['Solar System', __DIR__ . '/gallery/gallery_solarsystem.json'],
    ['Solar System', __DIR__ . '/gallery/gallery_solarsystem_moon.json'],
];
foreach ($surpriseSources as [$category, $source]) {
    foreach (astro_load_json($source) as $item) {
        $link = (string) ($item['link'] ?? '');
        $title = trim((string) ($item['title'] ?? ''));
        if ($link === '' || $title === '' || isset($surpriseSeen[$link])) {
            continue;
        }
        $surpriseSeen[$link] = true;
        $surpriseCatalog[] = [
            'title' => $title,
            'subtitle' => trim((string) ($item['subtitle'] ?? '')),
            'category' => $category,
            'url' => astro_url($link),
        ];
    }
}
require __DIR__ . '/includes/header.php';
?>
<main class="astro-page astro-home" id="main-content">
    <section class="astro-hero" aria-labelledby="home-title" data-cosmic-hero>
        <div class="astro-cosmic-stage" aria-hidden="true">
            <canvas data-cosmic-canvas></canvas>
        </div>
        <div class="astro-cosmic-interface" data-cosmic-interface>
            <svg class="astro-cosmic-leaders" data-cosmic-leaders aria-hidden="true"></svg>
            <div class="astro-cosmic-discoveries" data-cosmic-discoveries></div>
            <button class="astro-surprise" type="button" data-cosmic-surprise aria-label="Reveal five random objects from the archive">Surprise me</button>
            <span class="astro-visually-hidden" data-cosmic-announcement aria-live="polite"></span>
        </div>
        <script type="application/json" data-cosmic-catalog><?= json_encode($surpriseCatalog, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) ?></script>
        <div class="astro-hero-meta" aria-hidden="true"><span>PHYSX-CNH / ASTRO ARCHIVE</span><span>35.2° N · 113.7° W</span></div>
        <div class="astro-hero-copy">
            <h1 id="home-title"><em>Beyond</em> the local system.</h1>
            <div class="astro-hero-actions">
                <a class="astro-button" href="#observation-fields">Enter the archive <span aria-hidden="true">↓</span></a>
            </div>
        </div>
        <a class="astro-tonight-gateway" href="<?= astro_escape(astro_url('/Tonight.php')) ?>" data-tonight-gateway
           data-weather-url="<?= astro_escape(astro_url('/weather.php?city=hanoi')) ?>"
           data-d3-url="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/lib/d3.min.js')) ?>"
           data-projection-url="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/lib/d3.geo.projection.min.js')) ?>"
           data-celestial-url="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/celestial.min.js')) ?>"
           data-astronomy-url="<?= astro_escape(astro_url('/assets/vendor/astronomy-engine/astronomy.browser.min.js')) ?>"
           data-core-url="<?= astro_escape(astro_url('/js/tonight-core.js?v=20260826a')) ?>"
           data-sky-data-path="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/data/')) ?>">
            <div class="astro-tonight-gateway-map" id="tonight-teaser-map" aria-hidden="true"></div>
            <div class="astro-tonight-gateway-shade" aria-hidden="true"></div>
            <div class="astro-tonight-gateway-copy">
                <span class="astro-tonight-kicker">Live observing window · Hà Nội</span>
                <strong>Tonight over Vietnam.</strong>
                <span class="astro-tonight-gateway-status" data-gateway-status>Calculating the evening sky…</span>
            </div>
            <span class="astro-tonight-gateway-action">Explore tonight’s sky <span aria-hidden="true">↗</span></span>
        </a>
        <figure class="astro-hero-image">
            <img src="<?= astro_escape(astro_url('/Images/m51_050414_2000.jpg')) ?>" alt="The Whirlpool Galaxy, M51">
            <figcaption><span>M51</span><span>13h 29m 53s / +47° 11′ 43″</span></figcaption>
        </figure>
        <div class="astro-hero-coordinate" aria-hidden="true"><span>FIELD / 001</span><strong>DEEP SKY</strong></div>
    </section>

    <section class="astro-section astro-featured" id="observation-fields">
        <div class="astro-section-heading">
            <h2>Observation fields.</h2>
        </div>
        <div class="astro-gallery astro-home-gallery">
            <a href="<?= astro_escape(astro_url('/Galaxies.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/PerseusCluster_041008_041214_200.jpg')) ?>" alt="A field of distant galaxies" class="astro-thumb"><strong>Galaxies</strong></a>
            <a href="<?= astro_escape(astro_url('/Nebulae.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ngc2359_041212_200.jpg')) ?>" alt="Thor's Helmet nebula" class="astro-thumb"><strong>Nebulae</strong></a>
            <a href="<?= astro_escape(astro_url('/Clusters.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m22_040914_200.jpg')) ?>" alt="M22 star cluster" class="astro-thumb"><strong>Star clusters</strong></a>
            <a href="<?= astro_escape(astro_url('/SolarSystem.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/FullMoon_200.jpg')) ?>" alt="The full Moon" class="astro-thumb"><strong>Solar System</strong></a>
        </div>
    </section>
</main>
<script type="module" src="<?= astro_escape(astro_url('/js/cosmic-hero.js?v=20260825n')) ?>"></script>
<script src="<?= astro_escape(astro_url('/js/tonight-gateway.js?v=20260826a')) ?>" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
