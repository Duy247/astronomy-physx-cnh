<main class="astro-page astro-object-page" id="main-content">
    <div class="astro-flex-row">
        <section class="astro-section astro-image-section">
            <h1><?= astro_escape($object) ?></h1>
            <a href="<?= astro_escape(astro_url('/image_viewer.php?img=' . rawurlencode((string) ($image['large'] ?? '')))) ?>">
                <img src="<?= astro_escape(astro_url((string) ($image['thumb'] ?? ''))) ?>" alt="<?= astro_escape((string) ($image['alt'] ?? $object)) ?>">
            </a>
            <p class="astro-hint"><em>Click the photo for a larger image.</em></p>
        </section>
        <section class="astro-details-section" aria-label="Observation details">
            <table class="astro-details"><tbody>
                <?php foreach ($details as $row): ?>
                    <tr><th scope="row"><?= astro_escape((string) ($row['label'] ?? 'Detail')) ?></th><td><?= astro_render_detail($row) ?></td></tr>
                <?php endforeach; ?>
            </tbody></table>
        </section>
    </div>
    <section class="astro-skymap-section" data-sky-map data-survey="<?= astro_escape((string) ($image['survey'] ?? 'P/DSS2/color')) ?>" data-fov="<?= astro_escape((string) ($image['fov'] ?? 1)) ?>" data-target="<?= astro_escape((string) ($image['target'] ?? '')) ?>">
        <div class="astro-section-heading"><div><p class="astro-eyebrow">Interactive atlas</p><h2>Locate this object</h2></div><p>Pan, zoom, and compare this field with the Digitized Sky Survey.</p></div>
        <div id="aladin-lite-div" class="astro-skymap" role="img" aria-label="Interactive sky map for <?= astro_escape($object) ?>"></div>
        <p class="astro-skymap-status" data-sky-map-status>Loading the sky atlas…</p>
        <p class="astro-skymap-fallback"><a href="https://aladin.cds.unistra.fr/AladinLite/" rel="noopener">Open Aladin Lite</a> if the interactive map is unavailable.</p>
    </section>
</main>
<script src="<?= astro_escape(astro_url('/assets/vendor/aladin-lite/aladin.js')) ?>" defer></script>
<script src="<?= astro_escape(astro_url('/js/sky-map.js')) ?>" defer></script>
