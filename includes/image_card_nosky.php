<link rel="stylesheet" href="https://aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.css" />
<script src="https://code.jquery.com/jquery-1.9.1.min.js" charset="utf-8"></script>
<main class="astro-card">
    <div class="astro-flex-row">
        <section class="astro-section astro-image-section">
            <h3><?= htmlspecialchars($object) ?></h3>
            <a href="/image_viewer.php?img=<?= urlencode($image['large']) ?>" id="astro-image-link">
                <img src="<?= htmlspecialchars($image['thumb']) ?>" alt="<?= htmlspecialchars($image['alt']) ?>" id="astro-image-thumb">
            </a>
            <p style="color: var(--accent); font-size: 0.97rem; margin:0"><i>Click on photo for a larger image.</i></p>
        </section>
        <section class="astro-details-section">
            <table class="astro-details">
                <?php foreach ($details as $row) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['label']) ?>:</td>
                        <td><?= $row['value'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </div>
</main>
