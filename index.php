<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
$pageTitle = 'Misti Mountain Observatory — AstroGallery';
require __DIR__ . '/includes/header.php';
?>
<main class="astro-page astro-home">
    <section class="astro-hero">
        <div class="astro-hero-copy">
            <p class="astro-eyebrow">A historical astrophotography archive</p>
            <h1>The night sky, observed from Arizona.</h1>
            <p>Explore decades of deep-sky and Solar System photography from Misti Mountain Observatory, with original observing notes, equipment records, and raw data.</p>
            <div class="astro-hero-actions">
                <a class="astro-button" href="<?= astro_escape(astro_url('/Galaxies.php')) ?>">Explore the galleries</a>
                <a class="astro-button astro-button-secondary" href="<?= astro_escape(astro_url('/index_fits.php')) ?>">Browse FITS data</a>
            </div>
        </div>
        <figure class="astro-hero-image">
            <img src="<?= astro_escape(astro_url('/Images/m51_050414_2000.jpg')) ?>" alt="The Whirlpool Galaxy, M51">
            <figcaption>M51 · Whirlpool Galaxy</figcaption>
        </figure>
    </section>
    <section class="astro-section astro-featured">
        <div class="astro-section-heading">
            <div><p class="astro-eyebrow">Browse the collection</p><h2>Featured galleries</h2></div>
            <p>From neighboring planets to galaxies millions of light-years away.</p>
        </div>
        <div class="astro-gallery">
            <a href="<?= astro_escape(astro_url('/Galaxies.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/PerseusCluster_041008_041214_200.jpg')) ?>" alt="A field of distant galaxies" class="astro-thumb"><span class="astro-gallery-meta">Deep sky</span><strong>Galaxies</strong></a>
            <a href="<?= astro_escape(astro_url('/Nebulae.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ngc2359_041212_200.jpg')) ?>" alt="Thor's Helmet nebula" class="astro-thumb"><span class="astro-gallery-meta">Deep sky</span><strong>Nebulae</strong></a>
            <a href="<?= astro_escape(astro_url('/Clusters.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m22_040914_200.jpg')) ?>" alt="M22 star cluster" class="astro-thumb"><span class="astro-gallery-meta">Deep sky</span><strong>Star clusters</strong></a>
            <a href="<?= astro_escape(astro_url('/SolarSystem.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/FullMoon_200.jpg')) ?>" alt="The full Moon" class="astro-thumb"><span class="astro-gallery-meta">Nearby worlds</span><strong>Solar System</strong></a>
            <a href="<?= astro_escape(astro_url('/178ED.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ic5067_041013_200.jpg')) ?>" alt="IC 5067 photographed with a refractor" class="astro-thumb"><span class="astro-gallery-meta">By instrument</span><strong>7-inch refractor</strong></a>
            <a href="<?= astro_escape(astro_url('/500mm.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m31_040723_200.jpg')) ?>" alt="Andromeda photographed with a 500mm lens" class="astro-thumb"><span class="astro-gallery-meta">By instrument</span><strong>500mm lens</strong></a>
        </div>
    </section>
    <section class="astro-section astro-home-grid">
        <article class="astro-info-card"><p class="astro-eyebrow">Original data</p><h2>Work with the raw light</h2><p>Download unprocessed FITS exposures and study the source material behind selected photographs.</p><a href="<?= astro_escape(astro_url('/index_fits.php')) ?>">Browse FITS files <span aria-hidden="true">→</span></a></article>
        <article class="astro-info-card"><p class="astro-eyebrow">The observatory</p><h2>See how the images were made</h2><p>Explore the telescopes, mounts, cameras, and desert observing site used throughout the archive.</p><p><a href="<?= astro_escape(astro_url('/Equipment.php')) ?>">View equipment <span aria-hidden="true">→</span></a><br><a href="<?= astro_escape(astro_url('/Site.php')) ?>">Read the site history <span aria-hidden="true">→</span></a></p></article>
        <article class="astro-info-card astro-about-card"><p class="astro-eyebrow">About the archive</p><h2>More than 50 years under the stars</h2><p>Jim Misti built and used telescopes from a 3-inch refractor to a 32-inch Ritchey–Chrétien. AstroGallery preserves his historical observatory website in a modern, browsable form.</p></article>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
