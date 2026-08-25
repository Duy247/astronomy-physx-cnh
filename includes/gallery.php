<main class="astro-page astro-collection" id="main-content">
    <section class="astro-section">
        <div class="astro-collection-heading">
            <h1><?= astro_escape($heading) ?></h1>
        </div>
        <div class="astro-gallery">
            <?php foreach ($gallery as $item): ?>
                <a href="<?= astro_escape(astro_url((string) ($item['link'] ?? ''))) ?>" class="astro-gallery-item">
                    <img src="<?= astro_escape(astro_url((string) ($item['thumb'] ?? ''))) ?>" alt="<?= astro_escape((string) ($item['alt'] ?? '')) ?>" class="astro-thumb" loading="lazy">
                    <strong><?= astro_escape((string) (($item['title'] ?? '') ?: ($item['subtitle'] ?? 'Untitled object'))) ?></strong>
                    <?php if (($item['subtitle'] ?? '') !== ($item['title'] ?? '')): ?><span><?= astro_escape((string) ($item['subtitle'] ?? '')) ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>
