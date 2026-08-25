<main class="astro-card">
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
        <h2>Sky Map</h2>
        <div id="aladin-lite-div" class="astro-skymap" role="img" aria-label="Interactive sky map for <?= astro_escape($object) ?>"></div>
        <p class="astro-skymap-fallback"><a href="https://aladin.cds.unistra.fr/AladinLite/" rel="noopener">Open Aladin Lite</a> if the interactive map is unavailable.</p>
    </section>
</main>
<link rel="stylesheet" href="https://aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.css">
<script src="https://aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.js" defer></script>
<script src="<?= astro_escape(astro_url('/js/sky-map.js')) ?>" defer></script>
