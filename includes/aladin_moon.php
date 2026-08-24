<link rel="stylesheet" href="https://aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.css" />
<script src="https://code.jquery.com/jquery-1.9.1.min.js" charset="utf-8"></script>
<script type='text/javascript' src="https://aladin.u-strasbg.fr/AladinLite/api/v2/latest/aladin.min.js" charset="utf-8"></script>
<main class="astro-card" style="margin-top:0">
    <section class="astro-skymap-section">
        <div id="aladin-lite-div" style="width:100%;height:400px;margin:auto;"></div>
        <script>
        // Aladin Lite for the Moon
        var aladin = A.aladin('#aladin-lite-div', {
            target: '0 0',
            cooFrame: 'j2000d',
            fov: 90,
            showFrame: false,
            showLayersControl: false,
            showGotoControl: false,
        });
        var moon = aladin.createImageSurvey('Moon', 'Moon', 'https://alasky.cds.unistra.fr/Planets/CDS_P_Moon_LROC-WAC-100m/', 'j2000', 5, {longitudeReversed: true});
        aladin.setImageSurvey(moon);

        // Example: Add some named features (coordinates are approximate)
        var c = document.createElement('canvas'); c.width = c.height = 11; var ctx = c.getContext('2d'); ctx.beginPath(); ctx.arc(5, 5, 4, 0, 2 * Math.PI, false); ctx.closePath(); ctx.strokeStyle = '#ccc'; ctx.lineWidth = 2; ctx.stroke();
        var geoFeatures = A.catalog({shape: c, labelColumn: 'name', displayLabel: true, labelColor: '#fff', labelFont: '14px sans-serif'});
        aladin.addCatalog(geoFeatures);
        </script>
    </section>
</main>
