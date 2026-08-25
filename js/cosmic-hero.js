import * as THREE from "../assets/vendor/three/three.module.min.js";

const hero = document.querySelector("[data-cosmic-hero]");
const canvas = hero?.querySelector("[data-cosmic-canvas]");
const stage = canvas?.parentElement;
const interfaceLayer = hero?.querySelector("[data-cosmic-interface]");
const surpriseButton = hero?.querySelector("[data-cosmic-surprise]");
const catalogElement = hero?.querySelector("[data-cosmic-catalog]");
const discoveriesLayer = hero?.querySelector("[data-cosmic-discoveries]");
const leadersLayer = hero?.querySelector("[data-cosmic-leaders]");
const announcement = hero?.querySelector("[data-cosmic-announcement]");
let archiveCatalog = [];

try {
  archiveCatalog = JSON.parse(catalogElement?.textContent || "[]");
} catch {
  archiveCatalog = [];
}

if (hero && canvas && stage && interfaceLayer && surpriseButton && discoveriesLayer && leadersLayer) {
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const compactQuery = window.matchMedia("(max-width: 760px), (pointer: coarse)");
  const finePointer = window.matchMedia("(hover: hover) and (pointer: fine)").matches;

  const useFallback = () => {
    hero.classList.remove("is-cosmic-loading", "is-cosmic-ready");
    hero.classList.add("is-cosmic-fallback");
  };

  hero.classList.add("is-cosmic-loading");

  try {
    const renderer = new THREE.WebGLRenderer({
      canvas,
      alpha: true,
      antialias: !compactQuery.matches,
      powerPreference: "high-performance",
    });
    renderer.setClearColor(0x000000, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 80);
    const world = new THREE.Group();
    const solar = new THREE.Group();
    const boundary = new THREE.Group();
    const deepField = new THREE.Group();
    scene.add(world);
    world.add(solar, boundary, deepField);

    const random = (() => {
      let seed = 0x51a7c0de;
      return () => {
        seed |= 0;
        seed = (seed + 0x6d2b79f5) | 0;
        let value = Math.imul(seed ^ (seed >>> 15), 1 | seed);
        value = (value + Math.imul(value ^ (value >>> 7), 61 | value)) ^ value;
        return ((value ^ (value >>> 14)) >>> 0) / 4294967296;
      };
    })();

    const makeGlowTexture = () => {
      const sprite = document.createElement("canvas");
      sprite.width = 64;
      sprite.height = 64;
      const context = sprite.getContext("2d");
      const glow = context.createRadialGradient(32, 32, 0, 32, 32, 31);
      glow.addColorStop(0, "rgba(255,255,255,1)");
      glow.addColorStop(0.15, "rgba(213,240,255,.9)");
      glow.addColorStop(0.45, "rgba(111,188,255,.28)");
      glow.addColorStop(1, "rgba(0,0,0,0)");
      context.fillStyle = glow;
      context.fillRect(0, 0, 64, 64);
      return new THREE.CanvasTexture(sprite);
    };

    const glowTexture = makeGlowTexture();
    const fadingMaterials = [];

    const meteorGroup = new THREE.Group();
    const meteorTrailMaterial = new THREE.SpriteMaterial({
      map: glowTexture,
      color: 0xa9eaf5,
      transparent: true,
      opacity: 0,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
      rotation: -0.3,
    });
    const meteorTrail = new THREE.Sprite(meteorTrailMaterial);
    meteorTrail.center.set(1, 0.5);
    const meteorHeadMaterial = new THREE.SpriteMaterial({
      map: glowTexture,
      color: 0xf4fbff,
      transparent: true,
      opacity: 0,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });
    const meteorHead = new THREE.Sprite(meteorHeadMaterial);
    meteorGroup.add(meteorTrail, meteorHead);
    meteorGroup.visible = false;
    world.add(meteorGroup);
    const meteorState = {
      active: false,
      next: performance.now() * 0.001 + 3 + random() * 5,
      start: new THREE.Vector3(),
      end: new THREE.Vector3(),
      startTime: 0,
      duration: 1,
    };

    const sunMaterial = new THREE.SpriteMaterial({
      map: glowTexture,
      color: 0xf2c66d,
      transparent: true,
      opacity: 0.68,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });
    sunMaterial.userData.baseOpacity = sunMaterial.opacity;
    fadingMaterials.push(sunMaterial);
    const sun = new THREE.Sprite(sunMaterial);
    sun.scale.set(1.1, 1.1, 1);
    solar.add(sun);

    [0.72, 1.06, 1.48, 1.94, 2.46, 3.05].forEach((radius, index) => {
      const points = [];
      for (let step = 0; step <= 96; step += 1) {
        const angle = (step / 96) * Math.PI * 2;
        points.push(new THREE.Vector3(Math.cos(angle) * radius, Math.sin(angle) * radius * 0.38, 0));
      }
      const material = new THREE.LineBasicMaterial({
        color: index < 2 ? 0xe9cb76 : 0x6aa9bd,
        transparent: true,
        opacity: Math.max(0.08, 0.23 - index * 0.024),
        depthWrite: false,
      });
      material.userData.baseOpacity = material.opacity;
      fadingMaterials.push(material);
      const ring = new THREE.LineLoop(new THREE.BufferGeometry().setFromPoints(points), material);
      ring.rotation.z = 0.18;
      ring.rotation.x = 0.12 + index * 0.025;
      solar.add(ring);
    });

    [5.8, 6.02, 6.28].forEach((radius, index) => {
      const points = [];
      for (let step = 0; step <= 72; step += 1) {
        const angle = -0.9 + (step / 72) * 1.8;
        points.push(new THREE.Vector3(-5.1 + Math.cos(angle) * radius, Math.sin(angle) * radius, -0.5 - index * 0.08));
      }
      const material = new THREE.LineBasicMaterial({
        color: index === 1 ? 0xa798ff : 0x65e6f2,
        transparent: true,
        opacity: index === 1 ? 0.18 : 0.12,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
      });
      boundary.add(new THREE.Line(new THREE.BufferGeometry().setFromPoints(points), material));
    });

    const makeStarField = (count) => {
      const positions = new Float32Array(count * 3);
      const colors = new Float32Array(count * 3);
      const palette = [new THREE.Color(0x8eb9cc), new THREE.Color(0xdbeeff), new THREE.Color(0xa798ff)];
      for (let index = 0; index < count; index += 1) {
        const offset = index * 3;
        positions[offset] = -6 + random() * 14;
        positions[offset + 1] = -4.8 + random() * 9.6;
        positions[offset + 2] = -5 + random() * 7;
        const color = palette[Math.floor(random() * palette.length)].clone().multiplyScalar(0.45 + random() * 0.5);
        colors[offset] = color.r;
        colors[offset + 1] = color.g;
        colors[offset + 2] = color.b;
      }
      const geometry = new THREE.BufferGeometry();
      geometry.setAttribute("position", new THREE.BufferAttribute(positions, 3));
      geometry.setAttribute("color", new THREE.BufferAttribute(colors, 3));
      const material = new THREE.PointsMaterial({
        map: glowTexture,
        size: compactQuery.matches ? 0.075 : 0.058,
        sizeAttenuation: true,
        transparent: true,
        opacity: 0.58,
        vertexColors: true,
        blending: THREE.AdditiveBlending,
        depthWrite: false,
      });
      return new THREE.Points(geometry, material);
    };

    const stars = makeStarField(compactQuery.matches ? 360 : 960);
    world.add(stars);

    const galaxyCount = compactQuery.matches ? 2600 : 5400;
    const galaxyPositions = new Float32Array(galaxyCount * 3);
    const galaxyColors = new Float32Array(galaxyCount * 3);
    const galaxySizes = new Float32Array(galaxyCount);
    const galaxyPhases = new Float32Array(galaxyCount);
    const cool = new THREE.Color(0x77d4e5);
    const violet = new THREE.Color(0x8379d8);
    const warm = new THREE.Color(0xffe6ad);
    const pale = new THREE.Color(0xe7f6ff);
    const signedScatter = (power = 2.4) => (random() < 0.5 ? -1 : 1) * Math.pow(random(), power);
    const diskEnd = Math.floor(galaxyCount * 0.68);
    const bulgeEnd = Math.floor(galaxyCount * 0.82);
    const companionEnd = Math.floor(galaxyCount * 0.93);

    const writeGalaxyParticle = (index, x, y, z, color, size) => {
      const offset = index * 3;
      galaxyPositions[offset] = x;
      galaxyPositions[offset + 1] = y;
      galaxyPositions[offset + 2] = z;
      galaxyColors[offset] = color.r;
      galaxyColors[offset + 1] = color.g;
      galaxyColors[offset + 2] = color.b;
      galaxySizes[index] = size;
      galaxyPhases[index] = random() * Math.PI * 2;
    };

    for (let index = 0; index < galaxyCount; index += 1) {
      if (index < diskEnd) {
        const radiusRatio = Math.pow(random(), 0.58);
        const radius = radiusRatio * 2.72;
        const inArm = random() < 0.82;
        const arm = random() < 0.5 ? 0 : 1;
        const baseAngle = arm * Math.PI + radius * 2.12;
        const angle = inArm
          ? baseAngle + signedScatter(2.2) * (0.18 + radius * 0.18)
          : random() * Math.PI * 2;
        const radialNoise = signedScatter(2.1) * (inArm ? 0.12 : 0.3);
        const finalRadius = Math.max(0.03, radius + radialNoise);
        const x = Math.cos(angle) * finalRadius;
        const y = Math.sin(angle) * finalRadius * 0.48 + signedScatter(2.5) * 0.1;
        const z = signedScatter(2.3) * (0.08 + radiusRatio * 0.14);
        const color = warm.clone().lerp(arm === 0 ? cool : violet, Math.min(1, radiusRatio * 1.15));
        color.multiplyScalar((inArm ? 0.42 : 0.24) + random() * (inArm ? 0.42 : 0.2));
        writeGalaxyParticle(index, x, y, z, color, (inArm ? 0.72 : 0.48) + random() * (inArm ? 1.15 : 0.55));
      } else if (index < bulgeEnd) {
        const radius = Math.pow(random(), 2.35) * 0.92;
        const angle = random() * Math.PI * 2;
        const color = warm.clone().lerp(pale, random() * 0.42).multiplyScalar(0.55 + random() * 0.25);
        writeGalaxyParticle(
          index,
          Math.cos(angle) * radius,
          Math.sin(angle) * radius * 0.65,
          signedScatter(2.1) * 0.24,
          color,
          0.8 + random() * 1.65,
        );
      } else if (index < companionEnd) {
        const radius = Math.pow(random(), 1.65) * 0.67;
        const angle = random() * Math.PI * 2;
        const localX = Math.cos(angle) * radius;
        const localY = Math.sin(angle) * radius * 0.58;
        const rotation = -0.32;
        const color = warm.clone().lerp(cool, random() * 0.7).multiplyScalar(0.45 + random() * 0.35);
        writeGalaxyParticle(
          index,
          -1.86 + localX * Math.cos(rotation) - localY * Math.sin(rotation),
          0.82 + localX * Math.sin(rotation) + localY * Math.cos(rotation),
          signedScatter(2.2) * 0.15,
          color,
          0.66 + random() * 1.3,
        );
      } else {
        const progress = random();
        const curve = Math.sin(progress * Math.PI);
        const x = -1.72 + progress * 1.5 + signedScatter(2.4) * 0.12;
        const y = 0.72 - progress * 0.55 + curve * 0.2 + signedScatter(2.4) * 0.1;
        const color = cool.clone().lerp(warm, 1 - progress).multiplyScalar(0.35 + random() * 0.32);
        writeGalaxyParticle(index, x, y, signedScatter(2.5) * 0.09, color, 0.58 + random() * 0.95);
      }
    }
    const galaxyGeometry = new THREE.BufferGeometry();
    galaxyGeometry.setAttribute("position", new THREE.BufferAttribute(galaxyPositions, 3));
    galaxyGeometry.setAttribute("color", new THREE.BufferAttribute(galaxyColors, 3));
    galaxyGeometry.setAttribute("aSize", new THREE.BufferAttribute(galaxySizes, 1));
    galaxyGeometry.setAttribute("aPhase", new THREE.BufferAttribute(galaxyPhases, 1));
    const galaxyMaterial = new THREE.ShaderMaterial({
      uniforms: {
        uTime: { value: 0 },
        uPixelRatio: { value: 1 },
        uScale: { value: compactQuery.matches ? 23 : 19 },
        uOpacity: { value: compactQuery.matches ? 0.98 : 0.84 },
      },
      vertexShader: `
        uniform float uTime;
        uniform float uPixelRatio;
        uniform float uScale;
        attribute float aSize;
        attribute float aPhase;
        varying vec3 vColor;
        varying float vPulse;

        void main() {
          vec4 viewPosition = modelViewMatrix * vec4(position, 1.0);
          float pulse = 1.0 + sin(uTime * 0.55 + aPhase) * 0.1;
          gl_Position = projectionMatrix * viewPosition;
          gl_PointSize = max(1.0, aSize * uPixelRatio * uScale * pulse / max(1.0, -viewPosition.z));
          vColor = color;
          vPulse = pulse;
        }
      `,
      fragmentShader: `
        uniform float uOpacity;
        varying vec3 vColor;
        varying float vPulse;

        void main() {
          float distanceToCenter = length(gl_PointCoord - vec2(0.5));
          if (distanceToCenter > 0.5) discard;
          float softDisc = smoothstep(0.5, 0.04, distanceToCenter);
          float hotCore = pow(smoothstep(0.5, 0.0, distanceToCenter), 3.0);
          float alpha = (softDisc * 0.48 + hotCore * 0.62) * uOpacity;
          gl_FragColor = vec4(vColor * (0.82 + hotCore * 0.18) * vPulse, alpha);
          #include <tonemapping_fragment>
          #include <colorspace_fragment>
        }
      `,
      transparent: true,
      vertexColors: true,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });
    const galaxy = new THREE.Points(galaxyGeometry, galaxyMaterial);
    galaxy.rotation.z = -0.2;
    galaxy.rotation.x = -0.15;
    deepField.add(galaxy);

    const galaxyHaloMaterial = new THREE.SpriteMaterial({
      map: glowTexture,
      color: 0x65d7e7,
      transparent: true,
      opacity: compactQuery.matches ? 0.16 : 0.09,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });
    const galaxyHalo = new THREE.Sprite(galaxyHaloMaterial);
    galaxyHalo.scale.set(4.4, 2.15, 1);
    galaxyHalo.position.z = -0.12;
    deepField.add(galaxyHalo);

    const galaxyCoreMaterial = new THREE.SpriteMaterial({
      map: glowTexture,
      color: 0xe9cb76,
      transparent: true,
      opacity: compactQuery.matches ? 0.58 : 0.38,
      blending: THREE.AdditiveBlending,
      depthWrite: false,
    });
    const galaxyCore = new THREE.Sprite(galaxyCoreMaterial);
    galaxyCore.scale.set(0.72, 0.72, 1);
    galaxyCore.position.z = 0.08;
    deepField.add(galaxyCore);

    const companionCoreMaterial = galaxyCoreMaterial.clone();
    companionCoreMaterial.opacity *= 0.52;
    const companionCore = new THREE.Sprite(companionCoreMaterial);
    companionCore.scale.set(0.34, 0.34, 1);
    companionCore.position.set(-1.86, 0.82, 0.05);
    deepField.add(companionCore);

    const discoveryGroup = new THREE.Group();
    deepField.add(discoveryGroup);
    const discoveryColors = [0x65e6f2, 0xe9cb76, 0xa798ff, 0x86d6ff, 0xf0dca2];
    let activeDiscoveries = [];

    const shuffled = (items) => {
      const copy = [...items];
      for (let index = copy.length - 1; index > 0; index -= 1) {
        const swap = Math.floor(Math.random() * (index + 1));
        [copy[index], copy[swap]] = [copy[swap], copy[index]];
      }
      return copy;
    };

    const generateDiscoveryPositions = () => {
      const rotation = Math.random() * Math.PI * 2;
      return shuffled([0, 1, 2, 3, 4]).map((sector) => {
        const angle = rotation + sector * (Math.PI * 2 / 5) + (Math.random() - 0.5) * 0.42;
        const radius = compact ? 0.68 + Math.random() * 0.38 : 1 + Math.random() * 0.68;
        return {
          x: Math.cos(angle) * radius * (compact ? 1.08 : 1.55),
          y: Math.sin(angle) * radius * (compact ? 0.95 : 0.85),
        };
      });
    };

    const pickDiscoveries = () => {
      const byCategory = new Map();
      archiveCatalog.forEach((item) => {
        if (!byCategory.has(item.category)) byCategory.set(item.category, []);
        byCategory.get(item.category).push(item);
      });
      const picks = [];
      shuffled([...byCategory.values()]).forEach((items) => {
        if (picks.length < 5 && items.length) picks.push(items[Math.floor(Math.random() * items.length)]);
      });
      const remaining = shuffled(archiveCatalog.filter((item) => !picks.includes(item)));
      while (picks.length < 5 && remaining.length) picks.push(remaining.pop());
      return shuffled(picks);
    };

    const revealDiscoveries = () => {
      activeDiscoveries.forEach(({ marker }) => marker.material.dispose());
      discoveryGroup.clear();
      discoveriesLayer.replaceChildren();
      leadersLayer.replaceChildren();
      activeDiscoveries = [];
      const discoveryPositions = generateDiscoveryPositions();

      pickDiscoveries().forEach((item, index) => {
        const position = discoveryPositions[index];
        const material = new THREE.SpriteMaterial({
          map: glowTexture,
          color: discoveryColors[index],
          transparent: true,
          opacity: 0.92,
          blending: THREE.AdditiveBlending,
          depthWrite: false,
        });
        const marker = new THREE.Sprite(material);
        marker.position.set(position.x, position.y, 0.34);
        marker.userData.baseScale = (index === 1 ? 0.24 : 0.19) * (0.9 + Math.random() * 0.2);
        marker.scale.setScalar(marker.userData.baseScale);
        discoveryGroup.add(marker);

        const label = document.createElement("a");
        label.className = "astro-discovery";
        label.href = item.url;
        label.style.animationDelay = `${index * 70}ms`;
        const category = document.createElement("span");
        category.textContent = item.category;
        const title = document.createElement("strong");
        title.textContent = item.title;
        label.append(category, title);
        discoveriesLayer.append(label);

        const line = document.createElementNS("http://www.w3.org/2000/svg", "line");
        leadersLayer.append(line);
        activeDiscoveries.push({ marker, label, line, position, placementOrder: shuffled([0, 1, 2, 3, 4, 5, 6, 7]), placementChoice: null, index });
      });

      if (announcement) {
        announcement.textContent = `Five archive objects revealed: ${activeDiscoveries.map(({ label }) => label.querySelector("strong")?.textContent).join(", ")}.`;
      }
      requestAnimationFrame(() => updateInterface(performance.now() * 0.001));
    };

    surpriseButton.disabled = archiveCatalog.length < 5;
    surpriseButton.addEventListener("click", revealDiscoveries);

    const pointer = { x: 0, y: 0, targetX: 0, targetY: 0 };
    let scrollProgress = 0;
    let frame = 0;
    let visible = !document.hidden;
    let intersecting = true;
    let compact = compactQuery.matches;
    let layout = { solarX: -5.15, solarY: 0.55, galaxyX: 3.05, galaxyY: 0.25 };
    const projectedPosition = new THREE.Vector3();

    const projectToInterface = (object) => {
      object.getWorldPosition(projectedPosition);
      projectedPosition.project(camera);
      return {
        x: (projectedPosition.x * 0.5 + 0.5) * interfaceLayer.clientWidth,
        y: (-projectedPosition.y * 0.5 + 0.5) * interfaceLayer.clientHeight,
      };
    };

    const updateInterface = (time) => {
      const center = projectToInterface(deepField);
      surpriseButton.style.left = `${center.x}px`;
      surpriseButton.style.top = `${center.y}px`;
      leadersLayer.setAttribute("viewBox", `0 0 ${interfaceLayer.clientWidth} ${interfaceLayer.clientHeight}`);
      const labelWidth = compact ? 112 : 144;
      const labelHeight = compact ? 44 : 48;
      const visibleLeft = Math.max(6, -interfaceLayer.offsetLeft + 6);
      const visibleRight = Math.min(interfaceLayer.clientWidth - 6, visibleLeft + hero.clientWidth - 12);
      const regionHalfWidth = compact ? Math.min(164, hero.clientWidth * 0.43) : Math.min(340, hero.clientWidth * 0.3);
      const regionLeft = Math.max(visibleLeft, center.x - regionHalfWidth);
      const regionRight = Math.min(visibleRight, center.x + regionHalfWidth);
      const regionTop = compact
        ? Math.max(interfaceLayer.clientHeight * 0.38, center.y - 175)
        : Math.max(12, center.y - 220);
      const regionBottom = Math.min(interfaceLayer.clientHeight - 12, center.y + (compact ? 260 : 220));
      const placedRects = [];
      const buttonRect = {
        left: center.x - (compact ? 58 : 68),
        right: center.x + (compact ? 58 : 68),
        top: center.y - 24,
        bottom: center.y + 24,
      };

      const overlapArea = (first, second) => {
        const width = Math.max(0, Math.min(first.right, second.right) - Math.max(first.left, second.left));
        const height = Math.max(0, Math.min(first.bottom, second.bottom) - Math.max(first.top, second.top));
        return width * height;
      };

      const closestPlacement = (point, placementOrder, lockedChoice) => {
        const gap = 18;
        const rawCandidates = [
          { x: point.x + gap, y: point.y - labelHeight * 0.5 },
          { x: point.x - labelWidth - gap, y: point.y - labelHeight * 0.5 },
          { x: point.x - labelWidth * 0.5, y: point.y - labelHeight - gap },
          { x: point.x - labelWidth * 0.5, y: point.y + gap },
          { x: point.x + gap, y: point.y - labelHeight - gap },
          { x: point.x - labelWidth - gap, y: point.y + gap },
          { x: point.x + gap, y: point.y + gap },
          { x: point.x - labelWidth - gap, y: point.y - labelHeight - gap },
        ];
        const candidateIndexes = lockedChoice === null ? placementOrder : [lockedChoice];
        const candidates = candidateIndexes.map((candidateIndex) => ({
          candidateIndex,
          x: Math.max(regionLeft, Math.min(regionRight - labelWidth, rawCandidates[candidateIndex].x)),
          y: Math.max(regionTop, Math.min(regionBottom - labelHeight, rawCandidates[candidateIndex].y)),
        }));

        return candidates.reduce((best, candidate) => {
          const rect = {
            left: candidate.x,
            right: candidate.x + labelWidth,
            top: candidate.y,
            bottom: candidate.y + labelHeight,
          };
          const distanceX = Math.max(rect.left - point.x, 0, point.x - rect.right);
          const distanceY = Math.max(rect.top - point.y, 0, point.y - rect.bottom);
          let score = Math.hypot(distanceX, distanceY);
          if (score < 10) score += 50000;
          const buttonOverlap = overlapArea(rect, buttonRect);
          if (buttonOverlap > 0) score += 100000 + buttonOverlap * 100;
          placedRects.forEach((placed) => {
            const overlap = overlapArea(rect, placed);
            if (overlap > 0) score += 100000 + overlap * 100;
          });
          return !best || score < best.score ? { ...candidate, rect, score } : best;
        }, null);
      };

      activeDiscoveries.forEach((discovery) => {
        const { marker, label, line, placementOrder, index } = discovery;
        const point = projectToInterface(marker);
        const placement = closestPlacement(point, placementOrder, discovery.placementChoice);
        if (discovery.placementChoice === null) discovery.placementChoice = placement.candidateIndex;
        let labelX = placement.x;
        let labelY = placement.y;
        labelX = Math.max(regionLeft, Math.min(regionRight - labelWidth, labelX));
        labelY = Math.max(regionTop, Math.min(regionBottom - labelHeight, labelY));
        placedRects.push({ left: labelX, right: labelX + labelWidth, top: labelY, bottom: labelY + labelHeight });
        label.style.left = `${labelX}px`;
        label.style.top = `${labelY}px`;
        const lineEndX = Math.max(labelX, Math.min(labelX + labelWidth, point.x));
        const lineEndY = Math.max(labelY, Math.min(labelY + labelHeight, point.y));
        line.setAttribute("x1", point.x.toFixed(1));
        line.setAttribute("y1", point.y.toFixed(1));
        line.setAttribute("x2", lineEndX.toFixed(1));
        line.setAttribute("y2", lineEndY.toFixed(1));
        const pulse = marker.userData.baseScale * (1 + Math.sin(time * 2.1 + index * 1.7) * 0.2);
        marker.scale.setScalar(pulse);
        marker.material.opacity = 0.78 + Math.sin(time * 1.7 + index) * 0.18;
      });
    };

    const updateMeteor = (time) => {
      if (!meteorState.active && time >= meteorState.next) {
        const direction = random() < 0.5 ? 1 : -1;
        const horizontalExtent = compact ? 3.3 : 6.2;
        meteorState.start.set(-direction * horizontalExtent, 2.5 - random() * 2.4, 1.2);
        meteorState.end.set(direction * horizontalExtent, meteorState.start.y - 2.0 - random() * 1.5, 1.2);
        meteorState.startTime = time;
        meteorState.duration = 0.85 + random() * 0.45;
        meteorState.active = true;
        meteorGroup.visible = true;
        const angle = Math.atan2(meteorState.end.y - meteorState.start.y, meteorState.end.x - meteorState.start.x);
        meteorTrailMaterial.rotation = angle;
        meteorTrail.scale.set(compact ? 1.05 : 1.55, compact ? 0.06 : 0.075, 1);
        meteorHead.scale.setScalar(compact ? 0.13 : 0.16);
      }

      if (!meteorState.active) return;
      const progress = (time - meteorState.startTime) / meteorState.duration;
      if (progress >= 1) {
        meteorState.active = false;
        meteorState.next = time + 7 + random() * 12;
        meteorGroup.visible = false;
        return;
      }
      const eased = progress * progress * (3 - 2 * progress);
      meteorGroup.position.lerpVectors(meteorState.start, meteorState.end, eased);
      const brightness = Math.sin(progress * Math.PI);
      meteorTrailMaterial.opacity = brightness * 0.58;
      meteorHeadMaterial.opacity = brightness * 0.95;
    };

    const applyResponsiveLayout = () => {
      compact = compactQuery.matches;
      layout = compact
        ? { solarX: -2.15, solarY: 1.35, galaxyX: 0.2, galaxyY: -1.22 }
        : { solarX: -5.15, solarY: 0.55, galaxyX: 3.05, galaxyY: 0.25 };
      solar.scale.setScalar(compact ? 0.68 : 1);
      boundary.scale.setScalar(compact ? 0.64 : 1);
      deepField.scale.setScalar(compact ? 1.32 : 1);
      galaxyMaterial.uniforms.uScale.value = compact ? 23 : 19;
      galaxyMaterial.uniforms.uOpacity.value = compact ? 0.98 : 0.84;
      galaxyHaloMaterial.opacity = compact ? 0.16 : 0.09;
      galaxyCoreMaterial.opacity = compact ? 0.58 : 0.38;
      companionCoreMaterial.opacity = galaxyCoreMaterial.opacity * 0.52;
    };

    const updateScroll = () => {
      const rect = hero.getBoundingClientRect();
      scrollProgress = Math.max(0, Math.min(1, -rect.top / Math.max(1, rect.height * 0.72)));
    };

    const resize = () => {
      const width = Math.max(1, stage.clientWidth);
      const height = Math.max(1, stage.clientHeight);
      applyResponsiveLayout();
      renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, compact ? 1.2 : 1.55));
      galaxyMaterial.uniforms.uPixelRatio.value = renderer.getPixelRatio();
      renderer.setSize(width, height, false);
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      render(performance.now() * 0.001);
    };

    const render = (time) => {
      pointer.x += (pointer.targetX - pointer.x) * 0.045;
      pointer.y += (pointer.targetY - pointer.y) * 0.045;
      solar.position.set(layout.solarX - scrollProgress * 1.25, layout.solarY, -0.5);
      boundary.position.x = -scrollProgress * 0.48;
      deepField.position.set(layout.galaxyX + scrollProgress * 0.18, layout.galaxyY, 0.15);
      camera.position.set(pointer.x * 0.22 + scrollProgress * 0.18, pointer.y * 0.15, 10.7 - scrollProgress * 0.62);
      camera.lookAt(0, 0, 0);
      fadingMaterials.forEach((material) => {
        material.opacity = material.userData.baseOpacity * (1 - scrollProgress * 0.72);
      });
      galaxyMaterial.uniforms.uTime.value = reducedMotion ? 0 : time;
      if (!reducedMotion) {
        solar.rotation.z = time * 0.014;
        boundary.rotation.z = Math.sin(time * 0.09) * 0.008;
        galaxy.rotation.z = -0.2 + time * 0.006;
        stars.rotation.y = time * 0.0018;
        updateMeteor(time);
      }
      renderer.render(scene, camera);
      updateInterface(time);
    };

    const animate = (timestamp) => {
      frame = 0;
      if (!visible || !intersecting || reducedMotion) return;
      render(timestamp * 0.001);
      frame = requestAnimationFrame(animate);
    };

    const start = () => {
      if (!frame && visible && intersecting && !reducedMotion) frame = requestAnimationFrame(animate);
    };

    const stop = () => {
      if (frame) cancelAnimationFrame(frame);
      frame = 0;
    };

    canvas.addEventListener("webglcontextlost", (event) => {
      event.preventDefault();
      stop();
      useFallback();
    }, { once: true });

    if (finePointer && !reducedMotion) {
      hero.addEventListener("pointermove", (event) => {
        const rect = hero.getBoundingClientRect();
        pointer.targetX = ((event.clientX - rect.left) / rect.width - 0.5) * 2;
        pointer.targetY = -((event.clientY - rect.top) / rect.height - 0.5) * 2;
      }, { passive: true });
      hero.addEventListener("pointerleave", () => {
        pointer.targetX = 0;
        pointer.targetY = 0;
      });
    }

    window.addEventListener("scroll", updateScroll, { passive: true });
    document.addEventListener("visibilitychange", () => {
      visible = !document.hidden;
      if (visible) start(); else stop();
    });

    const observer = new IntersectionObserver(([entry]) => {
      intersecting = entry.isIntersecting;
      if (intersecting) start(); else stop();
    }, { rootMargin: "100px" });
    observer.observe(hero);

    if ("ResizeObserver" in window) {
      new ResizeObserver(resize).observe(hero);
    } else {
      window.addEventListener("resize", resize, { passive: true });
    }

    updateScroll();
    resize();
    hero.classList.remove("is-cosmic-loading");
    hero.classList.add("is-cosmic-ready");
    start();
  } catch (error) {
    console.warn("Cosmic hero unavailable; using the image fallback.", error);
    useFallback();
  }
}
