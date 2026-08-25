<main class="astro-card">
    <section class="astro-section">
        <h1><?= astro_escape($heading) ?></h1>
        <p class="astro-subtitle"><em><?= astro_escape($subtitle) ?></em></p>
        <div class="astro-gallery">
            <?php foreach ($gallery as $item): ?>
                <a href="<?= astro_escape(astro_url((string) ($item['link'] ?? ''))) ?>" class="astro-gallery-item">
                    <img src="<?= astro_escape(astro_url((string) ($item['thumb'] ?? ''))) ?>" alt="<?= astro_escape((string) ($item['alt'] ?? '')) ?>" class="astro-thumb" loading="lazy">
                    <strong><?= astro_escape((string) ($item['title'] ?? '')) ?></strong>
                    <span><?= astro_escape((string) ($item['subtitle'] ?? '')) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</main>
