window.addEventListener('load', async () => {
  'use strict';
  const app = document.querySelector('[data-tonight-app]');
  const configNode = document.querySelector('[data-city-config]');
  if (!app || !configNode || !window.Astronomy || !window.Celestial || !window.AstroTonight) return;

  const T = window.AstroTonight;
  const cities = JSON.parse(configNode.textContent || '{}');
  let cityId = localStorage.getItem('astro-observing-city');
  if (!cities[cityId]) cityId = 'hanoi';
  let city = {id: cityId, ...cities[cityId]};
  let windowInfo;
  let selectedDate;
  let weather = null;
  let targets = [];
  let mapReady = false;

  const $ = (selector) => app.querySelector(selector);
  const cityButtons = [...app.querySelectorAll('[data-city]')];
  const slider = $('[data-time-slider]');
  const mapStatus = $('[data-map-status]');

  function mapConfig() {
    const compact = window.matchMedia('(max-width: 760px)').matches;
    return {
      container: 'tonight-sky-map',
      datapath: app.dataset.skyDataPath,
      width: 0,
      projection: 'airy',
      projectionRatio: 1,
      interactive: true,
      controls: false,
      form: false,
      location: false,
      settimezone: false,
      geopos: [city.latitude, city.longitude],
      follow: 'zenith',
      zoomlevel: 1,
      zoomextend: 3,
      adaptable: true,
      stars: {show: true, limit: compact ? 5.2 : 5.8, colors: false, style: {fill: '#e9f7ff', opacity: 0.88}, designation: false, propername: false, data: 'stars.6.json', size: 5, exponent: -0.28},
      dsos: {show: false},
      constellations: {show: true, names: true, namesType: compact ? 'iau' : 'en', nameStyle: {fill: '#78919f', opacity: 0.72, font: ["11px 'Geist Mono Local', monospace", "10px 'Geist Mono Local', monospace", "9px 'Geist Mono Local', monospace"], align: 'center', baseline: 'middle'}, lines: true, lineStyle: {stroke: '#5a8091', width: 0.75, opacity: 0.42}, bounds: false},
      mw: {show: true, style: {fill: '#7994c8', opacity: 0.055}},
      lines: {graticule: {show: false}, equatorial: {show: false}, ecliptic: {show: true, stroke: '#8a7cca', width: 0.8, opacity: 0.35}, galactic: {show: false}, supergalactic: {show: false}},
      background: {fill: '#02050b', opacity: 1, stroke: '#65e6f2', width: 0.7},
      horizon: {show: true, stroke: '#65e6f2', width: 1.1, fill: '#020409', opacity: 0.78},
      daylight: {show: true},
      planets: {show: true, which: ['sol','mer','ven','lun','mar','jup','sat','ura','nep'], symbolType: 'disk', names: true, namesType: 'en', nameStyle: {fill: '#e9cb76', font: "11px 'Geist Mono Local', monospace", align: 'right', baseline: 'top'}}
    };
  }

  function setMapView() {
    if (!mapReady) return;
    Celestial.skyview({date: selectedDate, location: [city.latitude, city.longitude], timezone: T.TZ_OFFSET_MINUTES});
  }

  function updateTimeline() {
    const total = windowInfo.sunrise.getTime() - windowInfo.sunset.getTime();
    const elapsed = selectedDate.getTime() - windowInfo.sunset.getTime();
    slider.value = String(Math.max(0, Math.min(100, Math.round(elapsed / total * 100))));
    $('[data-selected-time]').textContent = T.formatDateTime(selectedDate);
  }

  function updateSummary() {
    const moon = T.moonInfo(selectedDate, windowInfo.observer);
    $('[data-city-name]').textContent = city.name;
    $('[data-coordinates]').textContent = `${Number(city.latitude).toFixed(2)}° N · ${Number(city.longitude).toFixed(2)}° E`;
    $('[data-darkness]').textContent = `${T.formatTime(windowInfo.dusk)}–${T.formatTime(windowInfo.dawn)}`;
    $('[data-sun-window]').textContent = `Sunset ${T.formatTime(windowInfo.sunset)} · Sunrise ${T.formatTime(windowInfo.sunrise)}`;
    $('[data-moon-phase]').textContent = moon.phase;
    $('[data-moon-detail]').textContent = `${moon.illumination}% illuminated · ${moon.altitude >= 0 ? `${Math.round(moon.altitude)}° ${T.compass(moon.azimuth)}` : 'below horizon'}`;
    const hour = T.nearestWeather(weather, selectedDate);
    const score = T.observingScore(hour);
    $('[data-observing-score]').textContent = score.label;
    $('[data-observing-score]').dataset.tone = score.tone;
    $('[data-weather-detail]').textContent = hour ? `${hour.cloudCover}% cloud · ${(hour.visibilityMetres / 1000).toFixed(1)} km visibility` : 'Astronomy view remains available';
  }

  function updatePlanets() {
    const planets = T.planetInfo(selectedDate, windowInfo.observer);
    $('[data-planet-list]').innerHTML = planets.map((planet) => {
      const visible = planet.altitude > 0;
      return `<article class="astro-planet-card ${visible ? 'is-visible' : ''}">
        <span>${visible ? 'Above horizon' : 'Below horizon'}</span><strong>${planet.name}</strong>
        <p>${visible ? `${Math.round(planet.altitude)}° high · ${T.compass(planet.azimuth)}` : `Rises ${planet.rise ? T.formatTime(planet.rise) : '—'}`}</p>
        <small>Magnitude ${planet.magnitude.toFixed(1)} · sets ${planet.set ? T.formatTime(planet.set) : '—'}</small>
      </article>`;
    }).join('');
  }

  function updateTargets() {
    const ranked = T.rankTargets(targets, windowInfo, selectedDate);
    const container = $('[data-target-list]');
    if (!ranked.length) {
      container.innerHTML = '<p>No matched archive targets reach 20° during this observing window.</p>';
      return;
    }
    container.innerHTML = ranked.map((target, index) => {
      const events = T.targetCrossings(target, windowInfo.sunset, windowInfo.sunrise, windowInfo.observer);
      const current = target.currentAltitude >= 0 ? `${Math.round(target.currentAltitude)}° ${T.compass(target.currentAzimuth)} now` : 'Below horizon now';
      return `<a class="astro-target-card" href="${app.dataset.basePath || ''}${target.url}">
        <span>Target ${String(index + 1).padStart(2, '0')} · ${target.typeLabel}</span>
        <strong>${target.name}</strong><p>${current}</p>
        <small>Best ${T.formatTime(target.bestTime)} at ${Math.round(target.maximumAltitude)}° · Moon ${Math.round(target.moonSeparation)}° away</small>
        <em>${events.rise ? `Rises ${T.formatTime(events.rise)}` : 'Already risen'} · ${events.set ? `sets ${T.formatTime(events.set)}` : 'up past dawn'} ↗</em>
      </a>`;
    }).join('');
  }

  function updateWeatherHours() {
    const container = $('[data-weather-hours]');
    if (!weather?.available) {
      container.innerHTML = '<p class="astro-weather-unavailable">Forecast unavailable. The calculated sky and object positions are unaffected.</p>';
      return;
    }
    const start = windowInfo.sunset.getTime() - T.HOUR;
    const end = windowInfo.sunrise.getTime() + T.HOUR;
    const hours = weather.hours.filter((hour) => {
      const time = new Date(`${hour.time}:00+07:00`).getTime();
      return time >= start && time <= end;
    });
    container.innerHTML = hours.map((hour) => {
      const date = new Date(`${hour.time}:00+07:00`);
      const active = Math.abs(date.getTime() - selectedDate.getTime()) < 31 * 60000;
      return `<button type="button" class="astro-weather-hour ${active ? 'is-active' : ''}" data-weather-time="${date.toISOString()}">
        <span>${T.formatTime(date)}</span><strong>${hour.cloudCover}%</strong><small>cloud</small>
        <em>${hour.precipitationChance}% rain · ${hour.windKmh} km/h</em>
      </button>`;
    }).join('');
    container.querySelectorAll('[data-weather-time]').forEach((button) => button.addEventListener('click', () => {
      selectedDate = new Date(button.dataset.weatherTime);
      refresh(false);
    }));
  }

  function refresh(updateWeatherTimeline = true) {
    updateTimeline();
    updateSummary();
    updatePlanets();
    updateTargets();
    if (updateWeatherTimeline) updateWeatherHours();
    else updateWeatherHours();
    setMapView();
  }

  async function loadWeather() {
    weather = null;
    updateSummary();
    try {
      const response = await fetch(`${app.dataset.weatherBase}?city=${encodeURIComponent(city.id)}`, {headers: {'Accept': 'application/json'}});
      weather = await response.json();
    } catch (error) {
      console.warn('Weather forecast unavailable.', error);
    }
    updateSummary();
    updateWeatherHours();
  }

  async function selectCity(nextId) {
    if (!cities[nextId]) return;
    cityId = nextId;
    city = {id: cityId, ...cities[cityId]};
    localStorage.setItem('astro-observing-city', cityId);
    cityButtons.forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.city === cityId)));
    windowInfo = T.observingWindow(city);
    selectedDate = windowInfo.selected;
    weather = null;
    refresh();
    await loadWeather();
  }

  slider.addEventListener('input', () => {
    const ratio = Number(slider.value) / 100;
    selectedDate = new Date(windowInfo.sunset.getTime() + (windowInfo.sunrise.getTime() - windowInfo.sunset.getTime()) * ratio);
    refresh(false);
  });
  $('[data-now-button]').addEventListener('click', () => {
    const now = new Date();
    selectedDate = now >= windowInfo.sunset && now <= windowInfo.sunrise ? now : windowInfo.selected;
    refresh(false);
  });
  cityButtons.forEach((button) => button.addEventListener('click', () => selectCity(button.dataset.city)));

  try {
    targets = await fetch(app.dataset.targetsUrl).then((response) => response.json());
    windowInfo = T.observingWindow(city);
    selectedDate = windowInfo.selected;
    Celestial.display(mapConfig());
    Celestial.skyview({date: selectedDate, location: [city.latitude, city.longitude], timezone: T.TZ_OFFSET_MINUTES});
    mapReady = true;
    mapStatus.textContent = 'Sky model ready.';
    mapStatus.classList.add('is-ready');
    refresh();
    loadWeather();
  } catch (error) {
    console.error('Unable to initialize tonight’s sky.', error);
    mapStatus.textContent = 'The interactive map could not be loaded. Planet and archive calculations remain available when possible.';
    mapStatus.classList.add('has-error');
  }
});
