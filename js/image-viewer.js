document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('iv-viewer');
  const status = container?.querySelector('[data-viewer-status]');
  const back = document.querySelector('[data-viewer-back]');

  back?.addEventListener('click', () => {
    if (window.history.length > 1) {
      window.history.back();
      return;
    }
    window.location.href = back.dataset.home || '/';
  });

  if (!container || typeof window.OpenSeadragon !== 'function') {
    if (status) status.textContent = 'The image viewer could not be loaded.';
    container?.classList.add('has-error');
    return;
  }

  const viewer = window.OpenSeadragon({
    id: container.id,
    prefixUrl: container.dataset.prefix,
    tileSources: {
      type: 'image',
      url: container.dataset.source
    },
    showNavigationControl: true,
    showZoomControl: true,
    showHomeControl: true,
    showFullPageControl: true,
    showRotationControl: false,
    showNavigator: false,
    animationTime: 0.35,
    blendTime: 0.1,
    constrainDuringPan: true,
    visibilityRatio: 0.35,
    minZoomImageRatio: 0.8,
    maxZoomPixelRatio: 5,
    zoomPerClick: 1.8,
    zoomPerScroll: 1.25,
    gestureSettingsTouch: {
      pinchToZoom: true,
      flickEnabled: true,
      clickToZoom: false,
      dblClickToZoom: true,
      dragToPan: true
    },
    gestureSettingsMouse: {
      clickToZoom: false,
      dblClickToZoom: true,
      dragToPan: true,
      scrollToZoom: true
    }
  });

  viewer.addHandler('open', () => {
    container.classList.add('is-ready');
    if (status) status.textContent = 'Image ready';
  });

  viewer.addHandler('open-failed', () => {
    container.classList.add('has-error');
    if (status) status.textContent = 'The full-resolution image could not be opened.';
  });
});
