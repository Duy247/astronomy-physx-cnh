<?php
$surveyId = (string) ($image['survey'] ?? 'P/DSS2/color');
$isMoonSurface = str_contains($surveyId, '/Moon/');
$objectTitle = implode('/<wbr>', array_map(static fn (string $part): string => astro_escape($part), explode('/', $object)));
?>
<main class="astro-page astro-object-page" id="main-content">
    <div class="astro-flex-row">
        <section class="astro-section astro-image-section">
            <h1><?= $objectTitle ?></h1>
            <a href="<?= astro_escape(astro_url('/image_viewer.php?img=' . rawurlencode((string) ($image['large'] ?? '')))) ?>" aria-label="Open full-size image of <?= astro_escape($object) ?>">
                <img src="<?= astro_escape(astro_url((string) ($image['thumb'] ?? ''))) ?>" alt="<?= astro_escape((string) ($image['alt'] ?? $object)) ?>">
            </a>
        </section>
        <section class="astro-details-section" aria-label="Observation details">
            <table class="astro-details"><tbody>
                <?php foreach ($details as $row): ?>
                    <tr><th scope="row"><?= astro_escape((string) ($row['label'] ?? 'Detail')) ?></th><td><?= astro_render_detail($row) ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>
        </section>
    </div>
    <section class="astro-skymap-section" data-sky-map data-viewer-mode="<?= $isMoonSurface ? 'moon' : 'sky' ?>" data-survey="<?= astro_escape($surveyId) ?>" data-fov="<?= astro_escape((string) ($image['fov'] ?? 1)) ?>" data-target="<?= astro_escape((string) ($image['target'] ?? '')) ?>">
        <div class="astro-section-heading"><h2><?= $isMoonSurface ? 'Moon surface.' : 'Sky atlas.' ?></h2></div>
        <div id="aladin-lite-div" class="astro-skymap" role="img" aria-label="<?= $isMoonSurface ? 'Interactive lunar surface map' : 'Interactive sky map for ' . astro_escape($object) ?>"></div>
        <p class="astro-skymap-status" data-sky-map-status>Loading <?= $isMoonSurface ? 'the lunar surface' : 'the sky atlas' ?>…</p>
        <p class="astro-skymap-fallback"><a href="https://aladin.cds.unistra.fr/AladinLite/" rel="noopener">Open Aladin Lite</a> if the interactive map is unavailable.</p>
    </section>
</main>
<script src="<?= astro_escape(astro_url('/assets/vendor/aladin-lite/aladin.js')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/js/sky-map.js')) ?>" defer></script>
