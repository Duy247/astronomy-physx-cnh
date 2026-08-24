// Add Photoshop-like zoom and fix brightness/contrast
(function(){
    let zoom = 1, brightness = 0, contrast = 0;
    let imgLayer = document.getElementById('iv-image');
    let drawLayer = document.getElementById('iv-draw');
    let imgCtx = imgLayer.getContext('2d');
    let drawCtx = drawLayer.getContext('2d');
    let img = new window.Image();
    let imgSrc = null;
    let lastImgData = null;

    // Get image from canvas or preload
    function loadImage(src) {
        imgSrc = src;
        img.src = src;
        img.onload = function() {
            zoom = 1; brightness = 0; contrast = 0;
            imgLayer.width = img.naturalWidth;
            imgLayer.height = img.naturalHeight;
            drawLayer.width = img.naturalWidth;
            drawLayer.height = img.naturalHeight;
            renderImage();
            clearDrawing();
        };
    }

    function renderImage() {
        imgLayer.width = img.naturalWidth * zoom;
        imgLayer.height = img.naturalHeight * zoom;
        drawLayer.width = imgLayer.width;
        drawLayer.height = imgLayer.height;
        imgCtx.save();
        imgCtx.clearRect(0, 0, imgLayer.width, imgLayer.height);
        imgCtx.filter = `brightness(${100 + +brightness}%) contrast(${100 + +contrast}%)`;
        imgCtx.drawImage(img, 0, 0, imgLayer.width, imgLayer.height);
        imgCtx.restore();
    }

    function clearDrawing() {
        drawCtx.clearRect(0, 0, drawLayer.width, drawLayer.height);
    }

    // Zoom handlers
    function handleZoom(delta) {
        zoom *= (delta > 0) ? 1.1 : 0.9;
        if (zoom < 0.1) zoom = 0.1;
        if (zoom > 10) zoom = 10;
        renderImage();
        clearDrawing();
    }
    document.addEventListener('wheel', function(e) {
        if (e.ctrlKey) {
            e.preventDefault();
            handleZoom(-e.deltaY);
        }
    }, { passive: false });
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && (e.key === '+' || e.key === '=')) {
            handleZoom(1);
        } else if (e.ctrlKey && (e.key === '-' || e.key === '_')) {
            handleZoom(-1);
        }
    });

    // Brightness/contrast
    document.getElementById('iv-brightness').oninput = function(e) {
        brightness = e.target.value;
        renderImage();
    };
    document.getElementById('iv-contrast').oninput = function(e) {
        contrast = e.target.value;
        renderImage();
    };

    // Drawing tool, color, clear, etc. (reuse from previous js)
    // ...existing drawing logic from image-viewer.js...

    // Auto-load image if present
    let imgParam = (new URLSearchParams(window.location.search)).get('img');
    if(imgParam) loadImage(imgParam);
})();
