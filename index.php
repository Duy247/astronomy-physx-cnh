<?php /* Modern Astronomy Home Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Misti Mountain Observatory - Astronomy</title>
    <meta name="description" content="Astrophotos gallery of Mr Misti.">
    <link rel="stylesheet" href="css/astro-modern.css">
    <!-- Social Media Thumbnail -->
    <meta property="og:image" content="https://www.physx-cnh.com/newyear/img.png">
    <meta property="og:image:alt" content="PhysxCNH AstroGallery">
    <meta property="og:type" content="website">
    <meta property="og:title" content="PhysxCNH AstroGallery">
    <meta property="og:description" content="Astrophotos gallery of Mr Misti">
    <meta property="og:url" content="https://www.mistisoftware.com/astronomy/">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="https://www.physx-cnh.com/newyear/img.png">
    <meta name="twitter:title" content="PhysxCNH AstroGallery">
    <meta name="twitter:description" content="Astrophotos gallery of Mr Misti.">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>
    <main class="astro-card">
        <section class="astro-section">
            <h3>Misti Mountain Observatory</h3>
            <p>Astrophotography, equipment, and resources from an amateur observatory in Arizona. Explore deep sky images, solar system photos, and more, all taken with large aperture telescopes and advanced CCD cameras.</p>
            <p>Website remade by Duy-Physx-CNH</p>
        </section>
        <section class="astro-section">
            <h3>Featured Galleries</h3>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;">
                <a href="Galaxies.php"><img src="Images/PerseusCluster_041008_041214_200.jpg" alt="Galaxies" style="width:180px;"><br>Galaxies</a>
                <a href="Nebulae.php"><img src="Images/ngc2359_041212_200.jpg" alt="Nebulae" style="width:180px;"><br>Nebulae</a>
                <a href="Clusters.php"><img src="Images/m22_040914_200.jpg" alt="Star Clusters" style="width:180px;"><br>Star Clusters</a>
                <a href="SolarSystem.php"><img src="Images/FullMoon_200.jpg" alt="Solar System" style="width:120px;"><br>Solar System</a>
                <a href="178ED.php"><img src="Images/ic5067_041013_200.jpg" alt="178ED" style="width:180px;"><br>7'' Refractor Images</a>
                <a href="500mm.php"><img src="Images/m31_040723_200.jpg" alt="500mm" style="width:180px;"><br>Nikon Lens Images</a>
            </div>
        </section>
        <section class="astro-section">
            <h3>Latest Images</h3>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem;">
                <a href="index_newCCD.php"><img src="Images/RhoOph_060527_100.jpg" alt="Rho Ophiuchi Region" style="width:120px;"><br>Rho Ophiuchi</a>
                <a href="index_newCCD.php"><img src="Images/m8Region_060527_100.jpg" alt="m8 Region" style="width:120px;"><br>m8 Region</a>
                <a href="index_newCCD.php"><img src="Images/m24_060530_100.jpg" alt="m24" style="width:120px;"><br>m24</a>
                <a href="index_newCCD.php"><img src="Images/b72_060531_100.jpg" alt="Barnard 72" style="width:120px;"><br>Barnard 72</a>
            </div>
        </section>
        <section class="astro-section">
            <h3>Special Resources</h3>
            <ul style="list-style:none; padding:0;">
                <li><a href="index_fits.php">Unprocessed FITS Image Files</a> for advanced image processing.</li>
            </ul>
        </section>
        <section class="astro-section">
            <h3>About</h3>
            <p>Jim Misti has enjoyed amateur astronomy for over 50 years, building and using telescopes from a 3" refractor to a 32" Ritchey-Chretien. This site shares images, equipment details, and resources for fellow astronomy enthusiasts.</p>
            <p>Contact: astronomy1 {at} mistisoftware.com</p>
        </section>
    </main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>
