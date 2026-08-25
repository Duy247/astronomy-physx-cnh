<?php $objectTitle = implode('/<wbr>', array_map(static fn (string $part): string => astro_escape($part), explode('/', $object))); ?>
<main class="astro-card" id="main-content">
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
</main>
