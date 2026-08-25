<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
$pageTitle = 'Tonight over Vietnam — AstroGallery';
$cities = astro_observing_cities();
require __DIR__ . '/includes/header.php';
?>
<main class="astro-page astro-tonight-page" id="main-content" data-tonight-app
      data-base-path="<?= astro_escape(astro_base_path()) ?>"
      data-weather-base="<?= astro_escape(astro_url('/weather.php')) ?>"
      data-targets-url="<?= astro_escape(astro_url('/gallery/observable_targets.json')) ?>"
      data-sky-data-path="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/data/')) ?>">
    <section class="astro-tonight-intro" aria-labelledby="tonight-title">
        <p class="astro-eyebrow">Vietnam observing desk / live model</p>
        <h1 id="tonight-title">Tonight over <em>Vietnam.</em></h1>
        <p>A calculated horizon view of the stars, Moon, planets, and archive objects visible from your selected city. Weather is forecast guidance, not a live camera feed.</p>
        <div class="astro-tonight-city-picker" role="group" aria-label="Observing city">
            <?php foreach ($cities as $id => $city): ?>
                <button type="button" data-city="<?= astro_escape($id) ?>" aria-pressed="<?= $id === 'hanoi' ? 'true' : 'false' ?>"><?= astro_escape((string) $city['name']) ?></button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="astro-tonight-dashboard" aria-live="polite">
        <article><span>Observing from</span><strong data-city-name>Hà Nội</strong><small data-coordinates>21.03° N · 105.85° E</small></article>
        <article><span>Dark sky</span><strong data-darkness>Calculating…</strong><small data-sun-window>Sunset and sunrise</small></article>
        <article><span>Moon</span><strong data-moon-phase>Calculating…</strong><small data-moon-detail>Illumination and altitude</small></article>
        <article><span>Conditions</span><strong data-observing-score>Loading forecast…</strong><small data-weather-detail>Cloud and visibility</small></article>
    </section>

    <section class="astro-tonight-map-section" aria-labelledby="sky-map-title">
        <div class="astro-section-heading">
            <h2 id="sky-map-title">Your horizon.</h2>
            <p>Drag to look around, pinch or scroll to zoom, and move through the night with the timeline.</p>
        </div>
        <div class="astro-tonight-map-shell">
            <div id="tonight-sky-map" class="astro-tonight-map" role="img" aria-label="Interactive horizon map of tonight's sky from Hà Nội"></div>
            <span class="astro-compass astro-compass-n" aria-hidden="true">N</span>
            <span class="astro-compass astro-compass-e" aria-hidden="true">E</span>
            <span class="astro-compass astro-compass-s" aria-hidden="true">S</span>
            <span class="astro-compass astro-compass-w" aria-hidden="true">W</span>
            <p class="astro-tonight-loading" data-map-status>Building the local sky…</p>
        </div>
        <div class="astro-time-control">
            <div><span>Sunset</span><output data-selected-time>—</output><span>Sunrise</span></div>
            <input type="range" min="0" max="100" value="50" step="1" data-time-slider aria-label="Time between sunset and sunrise">
            <button type="button" data-now-button>Now</button>
        </div>
    </section>

    <section class="astro-tonight-details" aria-labelledby="solar-system-title">
        <div class="astro-section-heading">
            <h2 id="solar-system-title">Solar system.</h2>
            <p>Positions update with the selected city and time.</p>
        </div>
        <div class="astro-planet-grid" data-planet-list></div>
    </section>

    <section class="astro-tonight-targets" aria-labelledby="targets-title">
        <div class="astro-section-heading">
            <h2 id="targets-title">Best archive targets.</h2>
            <p>Objects photographed in this archive that climb at least 20° above your horizon during the selected night.</p>
        </div>
        <div class="astro-target-grid" data-target-list><p>Matching the archive to tonight’s sky…</p></div>
    </section>

    <section class="astro-weather-timeline" aria-labelledby="weather-title">
        <div class="astro-section-heading">
            <h2 id="weather-title">Observing weather.</h2>
            <p>Hourly forecast nearest the selected time. Astronomy remains available if the forecast service is offline.</p>
        </div>
        <div class="astro-weather-hours" data-weather-hours></div>
        <p class="astro-weather-credit"><a href="https://open-meteo.com/" rel="noopener">Weather data by Open-Meteo.com</a></p>
    </section>

    <noscript><p class="astro-card">JavaScript is required to calculate the interactive local sky. The photography archive remains available from the main navigation.</p></noscript>
</main>
<script type="application/json" data-city-config><?= json_encode($cities, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<script src="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/lib/d3.min.js')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/lib/d3.geo.projection.min.js')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/assets/vendor/d3-celestial/celestial.min.js')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/assets/vendor/astronomy-engine/astronomy.browser.min.js')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/js/tonight-core.js?v=20260826a')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/js/tonight.js?v=20260826a')) ?>" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
