<?php /* Modern UI for FITS Image Files */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unprocessed FITS Image Files - Misti Mountain Observatory</title>
    <meta name="description" content="Download unprocessed FITS files from the 32-inch Ritchey-Chretien telescope at Misti Mountain Observatory.">
    <link rel="stylesheet" href="css/astro-modern.css">
    <style>
    /* Remove underline and color effect for links in the FITS table */
    .astro-fits-table a, .astro-fits-table a:visited, .astro-fits-table a:hover, .astro-fits-table a:focus {
        color: inherit;
        text-decoration: none;
        font-weight: 400;
    }
    </style>
</head>
<style>
.astro-card {
    max-width: 850px;
    margin: 6rem auto 2.5rem auto;
    background: var(--card-bg, #fff);
    border-radius: 10px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.10);
    padding: 2.5rem 2rem 2rem 2rem;
    font-family: 'Montserrat', Arial, Helvetica, sans-serif;
    animation: fadeIn 0.7s;
    font-weight: 300;
}
.astro-section {
    text-align: center;
}
.astro-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1.5em 1em;
    justify-items: center;
}
.astro-gallery-item {
    text-align: center;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
}
.astro-thumb {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 2px 12px #0002;
}
.astro-gallery-item div {
    margin-top: 0em;
    margin-bottom: 0.5em;
    font-weight: 600;
}
.astro-gallery-item div:last-child {
    font-size: 0.97em;
    color: var(--accent, #0055aa);
    font-weight: 400;
}
.astro-subtitle {
    color: var(--accent, #0055aa);
    font-size: 1.05em;
}
</style>
<?php
// Load gallery data from JSON
$gallery = json_decode(file_get_contents(__DIR__ . '/gallery/gallery_newCCD.json'), true);
?>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
    <main class="astro-card">
        <section class="astro-section">
            <h3>Latest CCD Images</h3>
            <p>Latest images taken by Jim Misti in 2006 and 2005</p>
        </section>        
        <section class="astro-section">
            <div class="astro-gallery">
                <?php foreach ($gallery as $item): ?>
                    <a href="<?= htmlspecialchars($item['link']) ?>" class="astro-gallery-item">
                        <img src="<?= htmlspecialchars($item['thumb']) ?>" alt="<?= htmlspecialchars($item['alt']) ?>" class="astro-thumb">
                        <div><?= htmlspecialchars($item['title']) ?></div>
                        <div><?= htmlspecialchars($item['subtitle']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

    </main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
