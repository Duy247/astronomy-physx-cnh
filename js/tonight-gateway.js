(function () {
  'use strict';
  const gateway = document.querySelector('[data-tonight-gateway]');
  if (!gateway) return;
  let started = false;

  function loadScript(url) {
    return new Promise((resolve, reject) => {
      const existing = document.querySelector(`script[src="${url}"]`);
      if (existing) { existing.addEventListener('load', resolve, {once: true}); return; }
      const script = document.createElement('script');
      script.src = url;
      script.onload = resolve;
      script.onerror = reject;
      document.head.appendChild(script);
    });
  }

  async function start() {
    if (started) return;
    started = true;
    const status = gateway.querySelector('[data-gateway-status]');
    try {
      await loadScript(gateway.dataset.d3Url);
      await loadScript(gateway.dataset.projectionUrl);
      await loadScript(gateway.dataset.celestialUrl);
      await loadScript(gateway.dataset.astronomyUrl);
      await loadScript(gateway.dataset.coreUrl);
      const city = {id: 'hanoi', name: 'Hà Nội', latitude: 21.0285, longitude: 105.8542, elevation: 12};
      const windowInfo = AstroTonight.observingWindow(city);
      Celestial.display({
        container: 'tonight-teaser-map', datapath: gateway.dataset.skyDataPath, width: 0,
        projection: 'airy', projectionRatio: 0.62, interactive: false, controls: false, form: false,
        location: false, settimezone: false, geopos: [city.latitude, city.longitude], follow: 'zenith',
        zoomlevel: 1.2, disableAnimations: true,
        stars: {show: true, limit: 5.2, colors: false, style: {fill: '#e9f7ff', opacity: 0.9}, designation: false, propername: false, data: 'stars.6.json', size: 5, exponent: -0.28},
        dsos: {show: false},
        constellations: {show: true, names: false, lines: true, lineStyle: {stroke: '#547b8e', width: 0.7, opacity: 0.45}, bounds: false},
        mw: {show: true, style: {fill: '#7b91c6', opacity: 0.08}},
        lines: {graticule: {show: false}, equatorial: {show: false}, ecliptic: {show: false}, galactic: {show: false}, supergalactic: {show: false}},
        background: {fill: '#02050b', opacity: 0, stroke: '#65e6f2', width: 0.6},
        horizon: {show: true, stroke: '#65e6f2', width: 0.8, fill: '#020409', opacity: 0.72},
        daylight: {show: true},
        planets: {show: true, which: ['ven','lun','mar','jup','sat'], symbolType: 'disk', names: false}
      });
      Celestial.skyview({date: windowInfo.selected, location: [city.latitude, city.longitude], timezone: AstroTonight.TZ_OFFSET_MINUTES});
      gateway.classList.add('is-ready');
      status.textContent = `${AstroTonight.formatDateTime(windowInfo.selected)} · forecast loading`;
      try {
        const weather = await fetch(gateway.dataset.weatherUrl).then((response) => response.json());
        const hour = AstroTonight.nearestWeather(weather, windowInfo.selected);
        const score = AstroTonight.observingScore(hour);
        status.textContent = hour ? `${AstroTonight.formatDateTime(windowInfo.selected)} · ${score.label.toLowerCase()} · ${hour.cloudCover}% cloud` : `${AstroTonight.formatDateTime(windowInfo.selected)} · forecast unavailable`;
      } catch (_) {
        status.textContent = `${AstroTonight.formatDateTime(windowInfo.selected)} · calculated sky ready`;
      }
    } catch (error) {
      console.warn('Tonight gateway preview unavailable.', error);
      status.textContent = 'Open the calculated sky and observing forecast';
      gateway.classList.add('has-fallback');
    }
  }

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      if (entries.some((entry) => entry.isIntersecting)) { observer.disconnect(); start(); }
    }, {rootMargin: '300px'});
    observer.observe(gateway);
  } else {
    start();
  }
})();

