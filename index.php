<?php

declare(strict_types=1);

require_once __DIR__ . '/app/bootstrap.php';
$pageTitle = 'Misti Mountain Observatory — AstroGallery';
require __DIR__ . '/includes/header.php';
?>
<main class="astro-card">
    <section class="astro-section">
        <h1>Misti Mountain Observatory</h1>
        <p>Explore historical deep-sky and Solar System astrophotography, observing equipment, processing notes, and raw FITS exposures from an amateur observatory in Arizona.</p>
    </section>
    <section class="astro-section">
        <h2>Featured Galleries</h2>
        <div class="astro-gallery">
            <a href="<?= astro_escape(astro_url('/Galaxies.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/PerseusCluster_041008_041214_200.jpg')) ?>" alt="Galaxies" class="astro-thumb"><strong>Galaxies</strong></a>
            <a href="<?= astro_escape(astro_url('/Nebulae.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ngc2359_041212_200.jpg')) ?>" alt="Nebulae" class="astro-thumb"><strong>Nebulae</strong></a>
            <a href="<?= astro_escape(astro_url('/Clusters.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m22_040914_200.jpg')) ?>" alt="Star clusters" class="astro-thumb"><strong>Star Clusters</strong></a>
            <a href="<?= astro_escape(astro_url('/SolarSystem.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/FullMoon_200.jpg')) ?>" alt="Solar System" class="astro-thumb"><strong>Solar System</strong></a>
            <a href="<?= astro_escape(astro_url('/178ED.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/ic5067_041013_200.jpg')) ?>" alt="7-inch refractor" class="astro-thumb"><strong>7-inch Refractor</strong></a>
            <a href="<?= astro_escape(astro_url('/500mm.php')) ?>" class="astro-gallery-item"><img src="<?= astro_escape(astro_url('/Images/m31_040723_200.jpg')) ?>" alt="500mm lens" class="astro-thumb"><strong>500mm Lens</strong></a>
        </div>
    </section>
    <section class="astro-section">
        <h2>Resources</h2>
        <p><a href="<?= astro_escape(astro_url('/index_fits.php')) ?>">Download unprocessed FITS files</a> or explore <a href="<?= astro_escape(astro_url('/Equipment.php')) ?>">observatory equipment</a> and <a href="<?= astro_escape(astro_url('/Site.php')) ?>">site history</a>.</p>
    </section>
    <section class="astro-section">
        <h2>About</h2>
        <p>Jim Misti has enjoyed amateur astronomy for more than 50 years, building and using telescopes from a 3-inch refractor to a 32-inch Ritchey–Chrétien. This project preserves and modernizes the observatory's historical web archive.</p>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
