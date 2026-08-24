<?php
// Get image from query param if present
$imageToLoad = '';
if (isset($_GET['img'])) {
    $imageToLoad = htmlspecialchars($_GET['img']);
}
?>
<link rel="stylesheet" href="/css/astro-modern.css">
<link rel="stylesheet" href="/css/image-viewer.css">
<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}
body {
    min-height: 100vh;
    min-width: 100vw;
    height: 100vh;
    width: 100vw;
    overflow: hidden;
    background: #181a20;
}
main.astro-card {
    height: 100vh;
    width: 100vw;
    max-width: none;
    border-radius: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    padding: 0 !important;
}
#iv-canvas-wrap {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 0;
    min-width: 0;
    height: 100%;
    width: 100%;
    margin: 0;
    background: #222;
    border-radius: 0;
    overflow: hidden;
}
.iv-menubar-ps{
    margin: 0;
}
</style>
<main class="astro-card" style="height:100vh;width:100vw;max-width:none;border-radius:0;margin:0;display:flex;flex-direction:column;">
    <!-- Photoshop-style menu bar -->
    <nav class="iv-menubar-ps">
        <ul class="iv-menu-list">
            <li class="iv-menu-item">
                <button class="iv-menu-btn">File</button>
                <ul class="iv-menu-dropdown">
                    <li><div id="iv-reset" style="width:100%; text-align:left;">Reset</div></li>
                    <li><a id="iv-download" href="<?php echo htmlspecialchars($imageToLoad); ?>" download style="display:block;width:100%;text-align:left;padding:0em;color:inherit;background:none;border:none;text-decoration:none;">Download</a></li>
                </ul>
            </li>
            <li class="iv-menu-item">
                <button class="iv-menu-btn">Edit</button>
                <ul class="iv-menu-dropdown">
                    <li><div id="iv-clear" style="width:100%; text-align:left;">Clear Drawing</div></li>
                </ul>
            </li>
            <li class="iv-menu-item">
                <button class="iv-menu-btn">Image</button>
                <ul class="iv-menu-dropdown">
                    <li>
                        <label style="width:100%; text-align:left;">Brightness
                            <input type="range" id="iv-brightness" min="-100" max="100" value="0" >
                        </label>
                    </li>
                    <li>
                        <label style="width:100%; text-align:left;">Contrast
                            <input type="range" id="iv-contrast" min="-100" max="100" value="0">
                        </label>
                    </li>
                </ul>
            </li>
            <li class="iv-menu-item">
                <button class="iv-menu-btn">Draw</button>
                <ul class="iv-menu-dropdown">
                    <li>
                        <div id="iv-tools" style="flex-direction:column;align-items:stretch;gap:0.3em;">
                            <div class="iv-tool-btn" data-tool="pen" style="width:100%; text-align:left;">Pen</div>
                            <div class="iv-tool-btn" data-tool="rect" style="width:100%; text-align:left;">Rectangle</div>
                            <div class="iv-tool-btn" data-tool="circle" style="width:100%; text-align:left;">Circle</div>
                            <div class="iv-tool-btn" data-tool="none" style="width:100%; text-align:left;">No Draw</div>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="iv-canvas-wrap" style="position:relative; width:100%; height:100%; margin:0; background:#222; border-radius:0; overflow:hidden; flex:1 1 auto; display:flex; align-items:center; justify-content:center;">
        <canvas id="iv-image" style="display:block; width:100%; height:100%; background:#111;"></canvas>
    </div>
</main>
<script src="/js/image-viewer.js"></script>
<script>
// If imageToLoad is set, load it automatically
(function(){
    var img = <?php echo json_encode($imageToLoad); ?>;
    if(img) {
        window.addEventListener('DOMContentLoaded', function() {
            // On mobile, clicking the image opens it in a new tab
            var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            var imgLayer = document.getElementById('iv-image');
            if(isMobile) {
                imgLayer.style.cursor = 'pointer';
                imgLayer.addEventListener('click', function() {
                    window.open(img, '_blank');
                });
                return; // Do not load viewer/editor logic
            }
            var image = new window.Image();
            image.src = img;
            image.onload = function() {
                var imgCtx = imgLayer.getContext('2d');
                imgLayer.width = image.naturalWidth;
                imgLayer.height = image.naturalHeight;
                imgCtx.drawImage(image, 0, 0);
            };
        });
    }
})();
</script>
