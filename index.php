<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
$pageTitle = 'Misti Mountain Observatory — AstroGallery';
require __DIR__ . '/includes/header.php';
?>
<main class="astro-page astro-home" id="main-content">
    <section class="astro-hero" aria-labelledby="home-title">
        <div class="astro-hero-meta" aria-hidden="true"><span>PHYSX-CNH / ASTRO ARCHIVE</span><span>35.2° N · 113.7° W</span></div>
        <div class="astro-hero-copy">
            <h1 id="home-title"><em>Beyond</em> the local system.</h1>
            <div class="astro-hero-actions">
                <a class="astro-button" href="#observation-fields">Enter the archive <span aria-hidden="true">↓</span></a>
                <a class="astro-text-link" href="<?= astro_escape(astro_url('/index_fits.php')) ?>">Access raw FITS data <span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <figure class="astro-hero-image">
            <img src="<?= astro_escape(astro_url('/Images/m51_050414_2000.jpg')) ?>" alt="The Whirlpool Galaxy, M51">
            <figcaption><span>M51</span><span>13h 29m 53s / +47° 11′ 43″</span></figcaption>
        </figure>
        <div class="astro-hero-coordinate" aria-hidden="true"><span>FIELD / 001</span><strong>DEEP SKY</strong></div>
    </section>

    <section class="astro-section astro-featured" id="observation-fields">
        <div class="astro-section-heading">
            <h2>Observation fields.</h2>
        </div>
        <div class="astro-gallery astro-home-gallery">
            <a href="<?= astro_escape(astro_url('/Galaxies.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/PerseusCluster_041008_041214_200.jpg')) ?>" alt="A field of distant galaxies" class="astro-thumb"><strong>Galaxies</strong></a>
            <a href="<?= astro_escape(astro_url('/Nebulae.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ngc2359_041212_200.jpg')) ?>" alt="Thor's Helmet nebula" class="astro-thumb"><strong>Nebulae</strong></a>
            <a href="<?= astro_escape(astro_url('/Clusters.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m22_040914_200.jpg')) ?>" alt="M22 star cluster" class="astro-thumb"><strong>Star clusters</strong></a>
            <a href="<?= astro_escape(astro_url('/SolarSystem.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/FullMoon_200.jpg')) ?>" alt="The full Moon" class="astro-thumb"><strong>Solar System</strong></a>
            <a href="<?= astro_escape(astro_url('/178ED.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ic5067_041013_200.jpg')) ?>" alt="IC 5067 photographed with a refractor" class="astro-thumb"><strong>7-inch refractor</strong></a>
            <a href="<?= astro_escape(astro_url('/500mm.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m31_040723_200.jpg')) ?>" alt="Andromeda photographed with a 500mm lens" class="astro-thumb"><strong>500mm lens</strong></a>
        </div>
    </section>

    <section class="astro-section astro-observatory-panel">
        <div><h2>Misti Mountain Observatory.</h2></div>
        <nav aria-label="Observatory resources"><a href="<?= astro_escape(astro_url('/Equipment.php')) ?>">Equipment <span>↗</span></a><a href="<?= astro_escape(astro_url('/Site.php')) ?>">Site history <span>↗</span></a><a href="<?= astro_escape(astro_url('/178ED.php')) ?>">7-inch refractor <span>↗</span></a><a href="<?= astro_escape(astro_url('/500mm.php')) ?>">500mm lens <span>↗</span></a></nav>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
