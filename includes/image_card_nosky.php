<main class="astro-card" id="main-content">
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
</main>
