window.addEventListener('load', async () => {
  const section = document.querySelector('[data-sky-map]');
  const status = section?.querySelector('[data-sky-map-status]');
  if (!section) return;

  const fail = (message) => {
    section.classList.add('has-error');
    if (status) status.textContent = message;
  };

  try {
    if (!window.A?.init || typeof window.A?.aladin !== 'function') {
      throw new Error('Aladin Lite did not load');
    }
    await window.A.init;
    const configuredSurvey = section.dataset.survey || 'P/DSS2/color';
    // The primary CDS DSS host is not reachable from every network. Use the
    // official CDS mirror directly so the viewer does not stall during lookup.
    const survey = configuredSurvey === 'P/DSS2/color'
      ? 'https://alaskybis.cds.unistra.fr/DSS/DSSColor'
      : configuredSurvey;
    window.A.aladin('#aladin-lite-div', {
      survey,
      fov: Number(section.dataset.fov || 1),
      target: section.dataset.target || '',
      reticleColor: '#77d5ff',
      showCooGridControl: true,
      showSimbadPointerControl: true,
      showFullscreenControl: true
    });
    section.classList.add('is-loaded');
    if (status) status.textContent = 'Interactive sky atlas loaded.';
  } catch (error) {
    console.error('Unable to initialize the sky atlas.', error);
    fail('The interactive atlas could not be loaded on this device.');
  }
});
