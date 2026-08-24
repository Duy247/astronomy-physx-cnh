<?php include 'includes/header.php'; ?>

<style>
.astro-card {
    max-width: 950px;
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
.astro-subtitle {
    color: var(--accent, #0055aa);
    font-size: 1.05em;
    margin-bottom: 1.5em;
}
.astro-compare-table {
    width: 100%;
    border-collapse: separate;
    margin: 0 auto 2em auto;
    background: none;
}
.astro-compare-table th, .astro-compare-table td {
    background: #f7f7fa;
    border-radius: 8px;
    padding: 0.7em 0.3em;
    font-size: 0.98em;
    color: #003366;
    vertical-align: top;
}
.astro-compare-table th {
    background: var(--accent, #e3eaff);
    color: #003366;
    font-weight: 600;
}
.astro-compare-table a {
    color: var(--accent, #0055aa);
    text-decoration: none;
    font-weight: 500;
}
.astro-compare-table a:hover {
    text-decoration: underline;
}
.astro-compare-thumbs {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1.2em;
    justify-items: center;
    margin: 2em 0 0 0;
}
.astro-thumb {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 2px 12px #0002;
    background: #fff;
    transition: transform 0.15s;
}
.astro-thumb:hover {
    transform: scale(1.04);
}
@media (max-width: 700px) {
    .astro-card { padding: 1.2rem 0.3rem; }
    .astro-compare-table th, .astro-compare-table td { font-size: 0.93em; }
    .astro-compare-thumbs { grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)); }
}
</style>

<main class="astro-card">
    <section class="astro-section">
        <h2 style="margin-bottom:0.5em;">m13 (Hercules Globular Cluster) - Image Comparison</h2>
        <p class="astro-subtitle"><i>Comparison of processing by various imagers using the same data.<br>Click a name for their website, or Medium/Large for their version. "Composite" shows a tiled version.</i></p>
        <div style="margin-bottom:2em;">
            <a href="/index_fits.php" class="astro-btn">&larr; Back to FITS Gallery</a>
        </div>
        <table class="astro-compare-table">
            <tr>
                <th>Imager</th>
                <th>Links</th>
                <th>Imager</th>
                <th>Links</th>
                <th>Imager</th>
                <th>Links</th>
            </tr>
            <tr>
                <td><a href="http://www.mistisoftware.com/astronomy">Jim Misti</a></td>
                <td><a href="/Images/m13_JimMisti_800.jpg">Medium</a> / <a href="/Images/m13_JimMisti_2000.jpg">Large</a></td>
                <td><a href="http://www.robgendlerastropics.com/">Rob Gendler</a></td>
                <td><a href="/Images/m13_RobGendler_800.jpg">Medium</a> / <a href="/Images/m13_RobGendler_2000.jpg">Large</a></td>
                <td><a href="http://www.starrywonders.com/">Steve Cannistra</a></td>
                <td><a href="/Images/m13_SteveCannistra_800.jpg">Medium</a> / <a href="/Images/m13_SteveCannistra_2000.jpg">Large</a></td>
            </tr>
            <tr>
                <td><a href="http://www.tamanti.it/astronomy.htm">Andrea Tamanti</a></td>
                <td><a href="/Images/m13_AndreaTamanti_800.jpg">Medium</a> / <a href="/Images/m13_AndreaTamanti_2000.jpg">Large</a></td>
                <td><a href="http://www.teamgs.org/astronomy.htm">Gary Blevins</a></td>
                <td><a href="/Images/m13_GaryBlevins_800.jpg">Medium</a> / <a href="/Images/m13_GaryBlevins_2000.jpg">Large</a></td>
                <td><a href="http://pk.darkhorizons.org/">Paul K</a></td>
                <td><a href="/Images/m13_PaulK_800.jpg">Medium</a> / <a href="/Images/m13_PaulK_2000.jpg">Large</a></td>
            </tr>
            <tr>
                <td>Paul Phelps</td>
                <td><a href="/Images/m13_PaulPhelps_800.jpg">Medium</a> / <a href="/Images/m13_PaulPhelps_2000.jpg">Large</a></td>
                <td>Brian Richardson</td>
                <td><a href="/Images/m13_BrianRichardson_800.jpg">Medium</a> / <a href="/Images/m13_BrianRichardson_2000.jpg">Large</a></td>
                <td><a href="Compare_m13_Med.htm">Composite</a></td>
                <td></td>
            </tr>
            <tr>
                <td><a href="http://www.manoprietoobservatory.com/">Tom Harrison</a></td>
                <td><a href="/Images/m13_TomHarrison_800.jpg">Medium</a> / <a href="/Images/m13_TomHarrison_2000.jpg">Large</a></td>
                <td><a href="http://starryforge.com/">Larry Citro</a></td>
                <td><a href="/Images/m13_LarryCitro_800.jpg">Medium</a> / <a href="/Images/m13_LarryCitro_2000.jpg">Large</a></td>
                <td><a href="http://www.luluobservatorium.de/">Guido Rettig</a></td>
                <td><a href="/Images/m13_GuidoRettig_800.jpg">Medium</a> / <a href="/Images/m13_GuidoRettig_2000.jpg">Large</a></td>
            </tr>
            <tr>
                <td>Darrell Hilde</td>
                <td><a href="/Images/m13_DarrellHilde_800.jpg">Medium</a> / <a href="/Images/m13_DarrellHilde_2000.jpg">Large</a></td>
                <td>Steve Timmons</td>
                <td><a href="/Images/m13_SteveTimmons_800.jpg">Medium</a> / <a href="/Images/m13_SteveTimmons_2000.jpg">Large</a></td>
                <td><a href="http://ncarboni.home.att.net/Astrophotography.html">Noel Carboni</a></td>
                <td><a href="/Images/m13_NoelCarboni_800.jpg">Medium</a> / <a href="/Images/m13_NoelCarboni_2000.jpg">Large</a></td>
            </tr>
            <tr>
                <td><a href="http://paginas.terra.com.br/arte/astrophotography3/RC32/">Marcos Mataratzis<br>& Vivek Hira</a></td>
                <td><a href="/Images/m13_MarcosMataratzisVivekHira_800.jpg">Medium</a> / <a href="/Images/m13_MarcosMataratzisVivekHira_2000.jpg">Large</a></td>
                <td><a href="http://www.highdesertsky.com/">Jimmy Stewart</a></td>
                <td><a href="/Images/m13_JimStewart_800.jpg">Medium</a> / <a href="/Images/m13_JimStewart_2000.jpg">Large</a></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <div class="astro-compare-thumbs">
            <a href="/Images/m13_JimMisti_800.jpg"><img src="Images/m13_JimMisti_200.jpg" alt="Jim Misti" class="astro-thumb"></a>
            <a href="/Images/m13_RobGendler_800.jpg"><img src="Images/m13_RobGendler_200.jpg" alt="Rob Gendler" class="astro-thumb"></a>
            <a href="/Images/m13_SteveCannistra_800.jpg"><img src="Images/m13_SteveCannistra_200.jpg" alt="Steve Cannistra" class="astro-thumb"></a>
            <a href="/Images/m13_AndreaTamanti_800.jpg"><img src="Images/m13_AndreaTamanti_200.jpg" alt="Andrea Tamanti" class="astro-thumb"></a>
            <a href="/Images/m13_GaryBlevins_800.jpg"><img src="Images/m13_GaryBlevins_200.jpg" alt="Gary Blevins" class="astro-thumb"></a>
            <a href="/Images/m13_PaulK_800.jpg"><img src="Images/m13_PaulK_200.jpg" alt="Paul K" class="astro-thumb"></a>
            <a href="/Images/m13_PaulPhelps_800.jpg"><img src="Images/m13_PaulPhelps_200.jpg" alt="Paul Phelps" class="astro-thumb"></a>
            <a href="/Images/m13_BrianRichardson_800.jpg"><img src="Images/m13_BrianRichardson_200.jpg" alt="Brian Richardson" class="astro-thumb"></a>
            <a href="/Images/m13_TomHarrison_800.jpg"><img src="Images/m13_TomHarrison_200.jpg" alt="Tom Harrison" class="astro-thumb"></a>
            <a href="/Images/m13_LarryCitro_800.jpg"><img src="Images/m13_LarryCitro_200.jpg" alt="Larry Citro" class="astro-thumb"></a>
            <a href="/Images/m13_GuidoRettig_800.jpg"><img src="Images/m13_GuidoRettig_200.jpg" alt="Guido Rettig" class="astro-thumb"></a>
            <a href="/Images/m13_DarrellHilde_800.jpg"><img src="Images/m13_DarrellHilde_200.jpg" alt="Darrell Hilde" class="astro-thumb"></a>
            <a href="/Images/m13_SteveTimmons_800.jpg"><img src="Images/m13_SteveTimmons_200.jpg" alt="Steve Timmons" class="astro-thumb"></a>
            <a href="/Images/m13_NoelCarboni_800.jpg"><img src="Images/m13_NoelCarboni_200.jpg" alt="Noel Carboni" class="astro-thumb"></a>
            <a href="/Images/m13_MarcosMataratzisVivekHira_800.jpg"><img src="Images/m13_MarcosMataratzisVivekHira_200.jpg" alt="Marcos Mataratzis & Vivek Hira" class="astro-thumb"></a>
            <a href="/Images/m13_JimStewart_800.jpg"><img src="Images/m13_JimStewart_200.jpg" alt="Jimmy Stewart" class="astro-thumb"></a>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
