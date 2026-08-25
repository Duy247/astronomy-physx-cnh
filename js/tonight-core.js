(function () {
  'use strict';

  const TZ_OFFSET_MINUTES = 420;
  const HOUR = 3600000;
  const DAY = 24 * HOUR;

  const astroDate = (value) => value && value.date instanceof Date ? value.date : null;
  const pad = (value) => String(value).padStart(2, '0');

  function vietnamParts(date) {
    const local = new Date(date.getTime() + TZ_OFFSET_MINUTES * 60000);
    return {
      year: local.getUTCFullYear(),
      month: local.getUTCMonth(),
      day: local.getUTCDate(),
      hour: local.getUTCHours(),
      minute: local.getUTCMinutes(),
    };
  }

  function vietnamDate(year, month, day, hour = 0, minute = 0) {
    return new Date(Date.UTC(year, month, day, hour, minute) - TZ_OFFSET_MINUTES * 60000);
  }

  function shiftLocalDay(parts, days) {
    const shifted = new Date(Date.UTC(parts.year, parts.month, parts.day + days));
    return {year: shifted.getUTCFullYear(), month: shifted.getUTCMonth(), day: shifted.getUTCDate()};
  }

  function observer(city) {
    return new Astronomy.Observer(Number(city.latitude), Number(city.longitude), Number(city.elevation || 0));
  }

  function calculateNight(city, localDay) {
    const obs = observer(city);
    const noon = vietnamDate(localDay.year, localDay.month, localDay.day, 12);
    const sunset = astroDate(Astronomy.SearchRiseSet(Astronomy.Body.Sun, obs, -1, noon, 1));
    if (!sunset) throw new Error('Unable to calculate sunset.');
    const dusk = astroDate(Astronomy.SearchAltitude(Astronomy.Body.Sun, obs, -1, sunset, 0.5, -18)) || sunset;
    const dawn = astroDate(Astronomy.SearchAltitude(Astronomy.Body.Sun, obs, +1, dusk, 1, -18));
    const sunrise = astroDate(Astronomy.SearchRiseSet(Astronomy.Body.Sun, obs, +1, dusk, 1));
    if (!dawn || !sunrise) throw new Error('Unable to calculate sunrise.');
    return {sunset, dusk, dawn, sunrise, observer: obs, localDay};
  }

  function observingWindow(city, now = new Date()) {
    const today = vietnamParts(now);
    const previousNight = calculateNight(city, shiftLocalDay(today, -1));
    if (now >= previousNight.sunset && now <= previousNight.sunrise) {
      return {...previousNight, selected: now, isLive: true};
    }
    const tonight = calculateNight(city, today);
    const ninePm = vietnamDate(today.year, today.month, today.day, 21);
    const selected = new Date(Math.max(tonight.sunset.getTime(), Math.min(ninePm.getTime(), tonight.sunrise.getTime())));
    return {...tonight, selected, isLive: now >= tonight.sunset && now <= tonight.sunrise};
  }

  function formatTime(date) {
    return new Intl.DateTimeFormat('en-GB', {
      timeZone: 'Asia/Ho_Chi_Minh', hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
    }).format(date);
  }

  function formatDateTime(date) {
    return new Intl.DateTimeFormat('en-GB', {
      timeZone: 'Asia/Ho_Chi_Minh', weekday: 'short', day: 'numeric', month: 'short',
      hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
    }).format(date);
  }

  function compass(azimuth) {
    const names = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
    return names[Math.round(((azimuth % 360) + 360) % 360 / 45) % 8];
  }

  function horizontalForBody(body, date, obs) {
    const equatorial = Astronomy.Equator(body, date, obs, true, true);
    const horizontal = Astronomy.Horizon(date, obs, equatorial.ra, equatorial.dec, 'normal');
    return {ra: equatorial.ra, dec: equatorial.dec, altitude: horizontal.altitude, azimuth: horizontal.azimuth};
  }

  function moonInfo(date, obs) {
    const angle = Astronomy.MoonPhase(date);
    const illumination = Astronomy.Illumination(Astronomy.Body.Moon, date);
    const position = horizontalForBody(Astronomy.Body.Moon, date, obs);
    let phase = 'Waning crescent';
    if (angle < 22.5 || angle >= 337.5) phase = 'New Moon';
    else if (angle < 67.5) phase = 'Waxing crescent';
    else if (angle < 112.5) phase = 'First quarter';
    else if (angle < 157.5) phase = 'Waxing gibbous';
    else if (angle < 202.5) phase = 'Full Moon';
    else if (angle < 247.5) phase = 'Waning gibbous';
    else if (angle < 292.5) phase = 'Third quarter';
    return {...position, phase, illumination: Math.round(illumination.phase_fraction * 100)};
  }

  const PLANETS = [
    ['Mercury', 'Mercury'], ['Venus', 'Venus'], ['Mars', 'Mars'],
    ['Jupiter', 'Jupiter'], ['Saturn', 'Saturn'], ['Uranus', 'Uranus'], ['Neptune', 'Neptune']
  ];

  function planetInfo(date, obs) {
    return PLANETS.map(([name, key]) => {
      const body = Astronomy.Body[key];
      const position = horizontalForBody(body, date, obs);
      const light = Astronomy.Illumination(body, date);
      const rise = astroDate(Astronomy.SearchRiseSet(body, obs, +1, date, 1.5));
      const set = astroDate(Astronomy.SearchRiseSet(body, obs, -1, date, 1.5));
      return {...position, name, magnitude: light.mag, rise, set};
    });
  }

  function targetPosition(target, date, obs) {
    return Astronomy.Horizon(date, obs, Number(target.raHours), Number(target.dec), 'normal');
  }

  function targetCrossings(target, start, end, obs) {
    let previousDate = start;
    let previousAltitude = targetPosition(target, previousDate, obs).altitude;
    let rise = null;
    let set = null;
    for (let time = start.getTime() + 10 * 60000; time <= end.getTime() + DAY; time += 10 * 60000) {
      const date = new Date(time);
      const altitude = targetPosition(target, date, obs).altitude;
      if (previousAltitude < 0 && altitude >= 0 && !rise) rise = new Date((previousDate.getTime() + date.getTime()) / 2);
      if (previousAltitude >= 0 && altitude < 0 && !set) set = new Date((previousDate.getTime() + date.getTime()) / 2);
      if (rise && set) break;
      previousDate = date;
      previousAltitude = altitude;
    }
    return {rise, set};
  }

  function moonSeparation(target, date, obs) {
    const moon = Astronomy.Equator(Astronomy.Body.Moon, date, obs, true, true);
    const ra1 = Number(target.raHours) * 15 * Math.PI / 180;
    const ra2 = moon.ra * 15 * Math.PI / 180;
    const dec1 = Number(target.dec) * Math.PI / 180;
    const dec2 = moon.dec * Math.PI / 180;
    const cosine = Math.sin(dec1) * Math.sin(dec2) + Math.cos(dec1) * Math.cos(dec2) * Math.cos(ra1 - ra2);
    return Math.acos(Math.max(-1, Math.min(1, cosine))) * 180 / Math.PI;
  }

  function rankTargets(targets, window, date) {
    return targets.map((target) => {
      let maximumAltitude = -90;
      let bestTime = window.dusk;
      for (let time = window.dusk.getTime(); time <= window.dawn.getTime(); time += 30 * 60000) {
        const sampleDate = new Date(time);
        const altitude = targetPosition(target, sampleDate, window.observer).altitude;
        if (altitude > maximumAltitude) {
          maximumAltitude = altitude;
          bestTime = sampleDate;
        }
      }
      const current = targetPosition(target, date, window.observer);
      const separation = moonSeparation(target, bestTime, window.observer);
      const magnitude = Number.isFinite(Number(target.magnitude)) ? Number(target.magnitude) : 9;
      const score = maximumAltitude - Math.min(magnitude, 12) * 2 + Math.min(separation, 60) * 0.08;
      return {...target, maximumAltitude, bestTime, currentAltitude: current.altitude, currentAzimuth: current.azimuth, moonSeparation: separation, score};
    }).filter((target) => target.maximumAltitude >= 20).sort((a, b) => b.score - a.score).slice(0, 5);
  }

  function nearestWeather(weather, date) {
    if (!weather || !weather.available || !Array.isArray(weather.hours) || !weather.hours.length) return null;
    let best = weather.hours[0];
    let distance = Infinity;
    weather.hours.forEach((hour) => {
      const parsed = new Date(`${hour.time}:00+07:00`);
      const candidate = Math.abs(parsed.getTime() - date.getTime());
      if (candidate < distance) { best = hour; distance = candidate; }
    });
    return best;
  }

  function observingScore(hour) {
    if (!hour) return {label: 'Forecast unavailable', tone: 'unknown'};
    const penalty = hour.cloudCover + hour.precipitationChance * 0.75 + Math.max(0, 10000 - hour.visibilityMetres) / 250;
    if (penalty < 30) return {label: 'Excellent', tone: 'excellent'};
    if (penalty < 60) return {label: 'Good', tone: 'good'};
    if (penalty < 95) return {label: 'Fair', tone: 'fair'};
    return {label: 'Poor', tone: 'poor'};
  }

  window.AstroTonight = {
    TZ_OFFSET_MINUTES, HOUR, DAY, observer, observingWindow, formatTime, formatDateTime,
    compass, moonInfo, planetInfo, targetPosition, targetCrossings, rankTargets,
    nearestWeather, observingScore, vietnamParts, vietnamDate
  };
})();

