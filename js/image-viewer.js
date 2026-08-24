// Image Viewer/Editor Core
let img = new window.Image();
let zoom = 1, brightness = 0, contrast = 0;
let tool = 'pen', drawColor = '#e53935';
let drawing = false, startX = 0, startY = 0;
let imgLayer, imgCtx;
let drawHistory = [];
let currentStroke = null;
let panX = null, panY = null;
let isPanning = false;
let panStartX = 0, panStartY = 0;

function fitImageToCenter() {
    if (!img.src) return;
    const wrap = document.getElementById('iv-canvas-wrap');
    const canvasW = wrap.clientWidth;
    const canvasH = wrap.clientHeight;
    // Fit image to canvas while preserving aspect ratio
    const scale = Math.min(canvasW / img.naturalWidth, canvasH / img.naturalHeight, 1);
    zoom = scale;
    panX = (canvasW - img.naturalWidth * zoom) / 2;
    panY = (canvasH - img.naturalHeight * zoom) / 2;
}

function ensurePanInitialized() {
    if (panX === null || panY === null) fitImageToCenter();
}

function updateCanvasSize() {
    if (!img.src) return;
    // Always fill the container
    const wrap = document.getElementById('iv-canvas-wrap');
    imgLayer.width = wrap.clientWidth;
    imgLayer.height = wrap.clientHeight;
}

function redrawAll() {
    if (!img.src) return;
    updateCanvasSize();
    imgCtx.save();
    imgCtx.clearRect(0, 0, imgLayer.width, imgLayer.height);
    imgCtx.setTransform(zoom, 0, 0, zoom, panX, panY);
    imgCtx.filter = `brightness(${100 + +brightness}%) contrast(${100 + +contrast}%)`;
    imgCtx.drawImage(img, 0, 0);
    // Draw all actions, scaling from image coordinates to canvas
    for (const action of drawHistory) {
        imgCtx.save();
        imgCtx.strokeStyle = action.color;
        imgCtx.lineWidth = 2 / zoom;
        if (action.type === 'pen') {
            imgCtx.beginPath();
            imgCtx.moveTo(action.points[0].x, action.points[0].y);
            for (let i = 1; i < action.points.length; i++) {
                imgCtx.lineTo(action.points[i].x, action.points[i].y);
            }
            imgCtx.stroke();
        } else if (action.type === 'rect') {
            const p0 = action.points[0], p1 = action.points[1];
            imgCtx.strokeRect(p0.x, p0.y, p1.x - p0.x, p1.y - p0.y);
        } else if (action.type === 'circle') {
            const p0 = action.points[0], p1 = action.points[1];
            let r = Math.sqrt(Math.pow(p1.x - p0.x, 2) + Math.pow(p1.y - p0.y, 2));
            imgCtx.beginPath();
            imgCtx.arc(p0.x, p0.y, r, 0, 2 * Math.PI);
            imgCtx.stroke();
        }
        imgCtx.restore();
    }
    imgCtx.setTransform(1, 0, 0, 1, 0, 0);
    imgCtx.restore();
}

function clearDrawing() {
    drawHistory = [];
    redrawAll();
}

function resetAll() {
    zoom = 1; brightness = 0; contrast = 0;
    document.getElementById('iv-brightness').value = 0;
    document.getElementById('iv-contrast').value = 0;
    drawHistory = [];
    redrawAll();
}

function setTool(newTool) {
    tool = newTool;
    imgLayer.style.pointerEvents = (tool === 'none') ? 'none' : 'auto';
}


window.addEventListener('DOMContentLoaded', function() {
    imgLayer = document.getElementById('iv-image');
    imgCtx = imgLayer.getContext('2d');
    setTool('pen');

    // On resize, keep image centered
    window.addEventListener('resize', function() {
        fitImageToCenter();
        redrawAll();
    });

    // Zoom handlers
    imgLayer.addEventListener('wheel', function(e) {
        // Remove ctrlKey requirement: always zoom on scroll
        e.preventDefault();
        ensurePanInitialized();
        const rect = imgLayer.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        const imgX = (mouseX - panX) / zoom;
        const imgY = (mouseY - panY) / zoom;
        const oldZoom = zoom;
        zoom *= (e.deltaY < 0) ? 1.1 : 0.9;
        if (zoom < 0.1) zoom = 0.1;
        if (zoom > 10) zoom = 10;
        panX = mouseX - imgX * zoom;
        panY = mouseY - imgY * zoom;
        redrawAll();
    }, { passive: false });
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && (e.key === '+' || e.key === '=')) {
            ensurePanInitialized();
            const rect = imgLayer.getBoundingClientRect();
            const mouseX = rect.width / 2;
            const mouseY = rect.height / 2;
            const imgX = (mouseX - panX) / zoom;
            const imgY = (mouseY - panY) / zoom;
            const oldZoom = zoom;
            zoom *= 1.1;
            if (zoom > 10) zoom = 10;
            panX = mouseX - imgX * zoom;
            panY = mouseY - imgY * zoom;
            redrawAll();
        } else if (e.ctrlKey && (e.key === '-' || e.key === '_')) {
            ensurePanInitialized();
            const rect = imgLayer.getBoundingClientRect();
            const mouseX = rect.width / 2;
            const mouseY = rect.height / 2;
            const imgX = (mouseX - panX) / zoom;
            const imgY = (mouseY - panY) / zoom;
            const oldZoom = zoom;
            zoom *= 0.9;
            if (zoom < 0.1) zoom = 0.1;
            panX = mouseX - imgX * zoom;
            panY = mouseY - imgY * zoom;
            redrawAll();
        }
    });
    document.getElementById('iv-brightness').oninput = function(e) { brightness = e.target.value; redrawAll(); };
    document.getElementById('iv-contrast').oninput = function(e) { contrast = e.target.value; redrawAll(); };
    document.querySelectorAll('.iv-color').forEach(btn => btn.onclick = function() { setColor(this.dataset.color); });
    document.getElementById('iv-clear').onclick = clearDrawing;
    document.getElementById('iv-reset').onclick = resetAll;

    // Drawing logic
    imgLayer.addEventListener('mousedown', function(e) {
        if ((tool === 'none' || e.button === 1 || e.button === 2) || e.ctrlKey || e.altKey || e.metaKey || e.shiftKey) {
            // Always allow panning with right mouse or middle mouse or any modifier key
            isPanning = true;
            panStartX = e.clientX - panX;
            panStartY = e.clientY - panY;
            imgLayer.style.cursor = 'grab';
            return;
        }
        if (tool === 'none') return;
        drawing = true;
        const rect = imgLayer.getBoundingClientRect();
        // Convert mouse to image coordinates (account for pan and zoom)
        startX = (e.clientX - rect.left - panX) / zoom;
        startY = (e.clientY - rect.top - panY) / zoom;
        if (tool === 'pen') {
            currentStroke = { type: 'pen', color: drawColor, points: [{ x: startX, y: startY }] };
        }
    }, true);
    imgLayer.addEventListener('mousemove', function(e) {
        if (isPanning) {
            panX = e.clientX - panStartX;
            panY = e.clientY - panStartY;
            redrawAll();
            return;
        }
        if (!drawing) return;
        const rect = imgLayer.getBoundingClientRect();
        const x = (e.clientX - rect.left - panX) / zoom;
        const y = (e.clientY - rect.top - panY) / zoom;
        if (tool === 'pen') {
            currentStroke.points.push({ x, y });
            redrawAll();
            // Draw current stroke
            imgCtx.save();
            imgCtx.setTransform(zoom, 0, 0, zoom, panX, panY);
            imgCtx.strokeStyle = currentStroke.color;
            imgCtx.lineWidth = 2 / zoom;
            imgCtx.beginPath();
            const pts = currentStroke.points;
            imgCtx.moveTo(pts[0].x, pts[0].y);
            for (let i = 1; i < pts.length; i++) imgCtx.lineTo(pts[i].x, pts[i].y);
            imgCtx.stroke();
            imgCtx.restore();
        } else {
            redrawAll();
            imgCtx.save();
            imgCtx.setTransform(zoom, 0, 0, zoom, panX, panY);
            imgCtx.strokeStyle = drawColor;
            imgCtx.lineWidth = 2 / zoom;
            if (tool === 'rect') {
                imgCtx.strokeRect(startX, startY, x - startX, y - startY);
            } else if (tool === 'circle') {
                let r = Math.sqrt(Math.pow(x - startX, 2) + Math.pow(y - startY, 2));
                imgCtx.beginPath();
                imgCtx.arc(startX, startY, r, 0, 2 * Math.PI);
                imgCtx.stroke();
            }
            imgCtx.restore();
        }
    }, true);
    imgLayer.addEventListener('mouseup', function(e) {
        if (isPanning) {
            isPanning = false;
            imgLayer.style.cursor = '';
            return;
        }
        if (!drawing) return;
        drawing = false;
        const rect = imgLayer.getBoundingClientRect();
        const x = (e.clientX - rect.left - panX) / zoom;
        const y = (e.clientY - rect.top - panY) / zoom;
        if (tool === 'pen') {
            drawHistory.push(currentStroke);
            currentStroke = null;
            redrawAll();
        } else if (tool === 'rect' || tool === 'circle') {
            let action = {
                type: tool,
                color: drawColor,
                points: [
                    { x: startX, y: startY },
                    { x, y }
                ]
            };
            drawHistory.push(action);
            redrawAll();
        }
    }, true);
    imgLayer.addEventListener('mouseleave', function(e) {
        if (isPanning) {
            isPanning = false;
            imgLayer.style.cursor = '';
        }
        if (!drawing) return;
        drawing = false;
        currentStroke = null;
    }, true);
    // Prevent context menu on right click for the canvas
    imgLayer.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
    // Undo with Ctrl+Z
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && (e.key === 'z' || e.key === 'Z')) {
            if (drawHistory.length > 0) {
                drawHistory.pop();
                redrawAll();
            }
        }
    });
    // Auto-load image if present
    let imgParam = (new URLSearchParams(window.location.search)).get('img');
    if(imgParam) {
        img.src = imgParam;
        img.onload = function() { fitImageToCenter(); redrawAll(); };
    }
    // Menu bar dropdowns: open on hover, close on mouseleave
    document.querySelectorAll('.iv-menu-item').forEach(item => {
        const btn = item.querySelector('.iv-menu-btn');
        btn.addEventListener('mouseenter', function() {
            document.querySelectorAll('.iv-menu-item').forEach(i => i.classList.remove('open'));
            item.classList.add('open');
        });
        item.addEventListener('mouseleave', function() {
            item.classList.remove('open');
        });
    });
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.iv-menu-dropdown').forEach(dd => dd.classList.remove('open'));
    });
    // Keyboard accessibility: open with Enter/Space
    document.querySelectorAll('.iv-menu-btn').forEach(btn => {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                btn.click();
            }
        });
    });

    // Photoshop-style menu bar dropdown logic
    document.querySelectorAll('.iv-menu-item').forEach(item => {
        const btn = item.querySelector('.iv-menu-btn');
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.iv-menu-item').forEach(i => i.classList.remove('open'));
            item.classList.toggle('open');
        });
        btn.addEventListener('mouseenter', function() {
            if (document.querySelector('.iv-menu-item.open')) {
                document.querySelectorAll('.iv-menu-item').forEach(i => i.classList.remove('open'));
                item.classList.add('open');
            }
        });
    });
    // Close all menus on click outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.iv-menu-item').forEach(i => i.classList.remove('open'));
    });
    // Keyboard navigation
    document.querySelectorAll('.iv-menu-btn').forEach(btn => {
        btn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'ArrowDown') {
                e.preventDefault();
                btn.click();
            }
        });
    });

    // Tool button group logic
    document.querySelectorAll('.iv-tool-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            setTool(this.dataset.tool);
            document.querySelectorAll('.iv-tool-btn').forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    // Set initial tool button selected
    document.querySelector('.iv-tool-btn[data-tool="pen"]').classList.add('selected');
});
