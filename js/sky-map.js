window.addEventListener('load', () => {
  const section = document.querySelector('[data-sky-map]');
  if (!section || typeof window.A?.aladin !== 'function') return;
  window.A.aladin('#aladin-lite-div', {
    survey: section.dataset.survey || 'P/DSS2/color',
    fov: Number(section.dataset.fov || 1),
    target: section.dataset.target || '',
    reticleColor: '#4fb3ff'
  });
  section.classList.add('is-loaded');
});
