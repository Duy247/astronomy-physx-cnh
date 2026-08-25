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
            <p class="astro-eyebrow"><span></span> Historical photons · preserved</p>
            <h1 id="home-title"><em>Beyond</em> the local system.</h1>
            <p>A deep-sky archive from Misti Mountain Observatory—distant galaxies, stellar nurseries, ancient clusters, and the instruments that captured their light.</p>
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
            <div><p class="astro-eyebrow">Observation fields / 01</p><h2>A survey of distant light.</h2></div>
            <p>Navigate the archive by object class or by the optical system used to record it.</p>
        </div>
        <div class="astro-gallery astro-home-gallery">
            <a href="<?= astro_escape(astro_url('/Galaxies.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/PerseusCluster_041008_041214_200.jpg')) ?>" alt="A field of distant galaxies" class="astro-thumb"><span class="astro-gallery-meta">Deep sky / 001</span><strong>Galaxies</strong></a>
            <a href="<?= astro_escape(astro_url('/Nebulae.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ngc2359_041212_200.jpg')) ?>" alt="Thor's Helmet nebula" class="astro-thumb"><span class="astro-gallery-meta">Deep sky / 002</span><strong>Nebulae</strong></a>
            <a href="<?= astro_escape(astro_url('/Clusters.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m22_040914_200.jpg')) ?>" alt="M22 star cluster" class="astro-thumb"><span class="astro-gallery-meta">Deep sky / 003</span><strong>Star clusters</strong></a>
            <a href="<?= astro_escape(astro_url('/SolarSystem.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/FullMoon_200.jpg')) ?>" alt="The full Moon" class="astro-thumb"><span class="astro-gallery-meta">Local system / 004</span><strong>Solar System</strong></a>
            <a href="<?= astro_escape(astro_url('/178ED.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ic5067_041013_200.jpg')) ?>" alt="IC 5067 photographed with a refractor" class="astro-thumb"><span class="astro-gallery-meta">Optical system / 005</span><strong>7-inch refractor</strong></a>
            <a href="<?= astro_escape(astro_url('/500mm.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m31_040723_200.jpg')) ?>" alt="Andromeda photographed with a 500mm lens" class="astro-thumb"><span class="astro-gallery-meta">Optical system / 006</span><strong>500mm lens</strong></a>
        </div>
    </section>

    <section class="astro-section astro-manifesto">
        <div class="astro-section-heading"><div><p class="astro-eyebrow">Archive protocol / 02</p><h2>Light carries memory.</h2></div><p>This collection keeps the image, its observational context, and its raw scientific data connected.</p></div>
        <div class="astro-home-grid">
            <article class="astro-info-card"><span>01</span><h3>Observe</h3><p>Browse galaxies, nebulae, clusters, planets, and wide-field regions captured from Arizona.</p><a href="<?= astro_escape(astro_url('/Galaxies.php')) ?>">Open galleries ↗</a></article>
            <article class="astro-info-card"><span>02</span><h3>Inspect</h3><p>Move through high-resolution photographs and locate deep-sky objects in the interactive atlas.</p><a href="<?= astro_escape(astro_url('/Nebulae.php')) ?>">Explore objects ↗</a></article>
            <article class="astro-info-card"><span>03</span><h3>Process</h3><p>Download original FITS exposures and study the processing notes behind finished work.</p><a href="<?= astro_escape(astro_url('/index_fits.php')) ?>">Access the data ↗</a></article>
        </div>
    </section>

    <section class="astro-section astro-observatory-panel">
        <div><p class="astro-eyebrow">Misti Mountain Observatory / 03</p><h2>Built to see farther.</h2><p>More than fifty years of amateur astronomy, from a 3-inch refractor to a 32-inch Ritchey–Chrétien under the Arizona sky.</p></div>
        <nav aria-label="Observatory resources"><a href="<?= astro_escape(astro_url('/Equipment.php')) ?>">Equipment <span>↗</span></a><a href="<?= astro_escape(astro_url('/Site.php')) ?>">Site history <span>↗</span></a><a href="<?= astro_escape(astro_url('/178ED.php')) ?>">7-inch refractor <span>↗</span></a><a href="<?= astro_escape(astro_url('/500mm.php')) ?>">500mm lens <span>↗</span></a></nav>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
