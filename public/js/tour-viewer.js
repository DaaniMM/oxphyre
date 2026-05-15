// Visor público Oxphyre: panorámica principal obligatoria + fotos detalle opcionales.
// Depende de: Three.js CDN y TOUR_DATA inyectado por PHP.

'use strict';

let mainState = null;
let currentPositionIdx = 0;
let currentPosition = null;
let gyroActive = false;
let roomState = null;

const MAIN_PITCH_LIMIT_DEG = 6;
const MAIN_DEFAULT_FOV = 62;
// La BD conserva N/S/E/O por compatibilidad. En el visor se traducen a
// fotos detalle 1-4 y se renderizan solo las que existan para cada posición.
const DETAIL_DIRECTIONS = ['N', 'S', 'E', 'O'];
const DETAIL_LABELS = {
  N: 'Foto detalle 1',
  S: 'Foto detalle 2',
  E: 'Foto detalle 3',
  O: 'Foto detalle 4',
};
const ROOM_TARGET_YAW = {
  N: 0,
  E: -Math.PI / 2,
  S: Math.PI,
  O: Math.PI / 2,
};
const ROOM_PANEL_ANGLES = {
  N: 0,
  E: Math.PI / 2,
  S: Math.PI,
  O: -Math.PI / 2,
};
const ROOM_PITCH_LIMIT_DEG = 24;

function configureNeutralRenderer(renderer) {
  if ('outputColorSpace' in renderer && THREE.SRGBColorSpace) {
    renderer.outputColorSpace = THREE.SRGBColorSpace;
  }
  if ('outputEncoding' in renderer && THREE.sRGBEncoding) {
    renderer.outputEncoding = THREE.sRGBEncoding;
  }
  if (THREE.NoToneMapping !== undefined) {
    renderer.toneMapping = THREE.NoToneMapping;
  }
  renderer.toneMappingExposure = 1;
}

function configureNeutralTexture(texture) {
  if ('colorSpace' in texture && THREE.SRGBColorSpace) {
    texture.colorSpace = THREE.SRGBColorSpace;
  }
  if ('encoding' in texture && THREE.sRGBEncoding) {
    texture.encoding = THREE.sRGBEncoding;
  }
}

function getPositions() {
  if (typeof TOUR_DATA === 'undefined') return [];
  return Array.isArray(TOUR_DATA?.positions) ? TOUR_DATA.positions : [];
}

function getPanoramaUrl(position) {
  return position?.photos?.['360']?.url || null;
}

function getDetailSlots(position) {
  return DETAIL_DIRECTIONS
    .filter(dir => Boolean(position?.photos?.[dir]?.url))
    .map(dir => ({
      dir,
      label: DETAIL_LABELS[dir],
      url: position.photos[dir].url,
    }));
}

function getInitialPositionIndex(positions) {
  const requestedId = new URLSearchParams(window.location.search).get('position');
  if (!requestedId) return 0;

  const requestedIdx = positions.findIndex(position => {
    return String(position?.id) === requestedId && Boolean(getPanoramaUrl(position));
  });

  return requestedIdx >= 0 ? requestedIdx : 0;
}

function hasRoom(position) {
  return getDetailSlots(position).length > 0;
}

function showUnavailable() {
  const unavailable = document.getElementById('tour-unavailable');
  const viewerEl = document.getElementById('psv-viewer');
  const gyroBtn = document.getElementById('tour-gyro-btn');
  const detailsBtn = document.getElementById('tour-details-btn');
  const bar = document.getElementById('tour-positions-bar');
  const loadingEl = document.getElementById('psv-loading');

  if (viewerEl) viewerEl.style.display = 'none';
  if (gyroBtn) gyroBtn.hidden = true;
  if (detailsBtn) detailsBtn.hidden = true;
  if (bar) bar.hidden = true;
  if (unavailable) unavailable.hidden = false;
  if (loadingEl) loadingEl.hidden = true;
}

function initViewer() {
  const positions = getPositions();
  currentPositionIdx = getInitialPositionIndex(positions);
  currentPosition = positions[currentPositionIdx] || null;
  const initialUrl = getPanoramaUrl(currentPosition);

  if (!initialUrl) {
    showUnavailable();
    return;
  }

  initMainPanorama(initialUrl);
  updateActiveBtn();
  updateDetailsButton();
}

function loadPosition(idx) {
  const positions = getPositions();
  if (idx === currentPositionIdx || !positions[idx]) return;

  closeRoom();

  currentPositionIdx = idx;
  currentPosition = positions[idx];

  const url = getPanoramaUrl(currentPosition);
  if (!url) return;

  initMainPanorama(url);
  updateActiveBtn();
  updateDetailsButton();
}

function initMainPanorama(url) {
  disposeMainPanorama();

  const container = document.getElementById('psv-viewer');
  if (!container || typeof THREE === 'undefined') {
    showUnavailable();
    return;
  }

  const loadingEl = document.getElementById('psv-loading');
  if (loadingEl) loadingEl.hidden = false;

  container.innerHTML = '';
  mainState = {
    container,
    renderer: null,
    scene: null,
    camera: null,
    texture: null,
    material: null,
    geometry: null,
    mesh: null,
    frameId: null,
    listeners: [],
    yaw: 0,
    targetYaw: 0,
    pitch: 0,
    targetPitch: 0,
    yawLimit: 0,
    dragging: false,
    lastX: 0,
    lastY: 0,
    disposed: false,
  };

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0x050505);

  const camera = new THREE.PerspectiveCamera(MAIN_DEFAULT_FOV, 1, 0.05, 80);
  camera.rotation.order = 'YXZ';

  const renderer = new THREE.WebGLRenderer({
    antialias: true,
    alpha: false,
    powerPreference: 'high-performance',
  });
  configureNeutralRenderer(renderer);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.setClearColor(0x050505, 1);
  container.appendChild(renderer.domElement);

  mainState.scene = scene;
  mainState.camera = camera;
  mainState.renderer = renderer;

  const state = mainState;
  const loader = new THREE.TextureLoader();
  loader.load(
    url,
    texture => {
      if (!state || state.disposed || mainState !== state) {
        texture.dispose();
        return;
      }

      if (loadingEl) loadingEl.hidden = true;

      configureNeutralTexture(texture);
      texture.anisotropy = renderer.capabilities.getMaxAnisotropy?.() || 1;
      texture.minFilter = THREE.LinearFilter;
      texture.magFilter = THREE.LinearFilter;

      const image = texture.image || {};
      const aspect = image.width && image.height ? image.width / image.height : 2.6;
      const coverageDeg = THREE.MathUtils.clamp(aspect * 55, 110, 240);
      const diagnosticRadius = 5.2;
      const diagnosticHeight = 4.8;

      state.texture = texture;
      state.geometry = createMainPanoramaGeometry(THREE.MathUtils.degToRad(coverageDeg));
      state.material = new THREE.MeshBasicMaterial({
        map: texture,
        side: THREE.DoubleSide,
      });
      state.mesh = new THREE.Mesh(state.geometry, state.material);
      scene.add(state.mesh);

      updateMainLimits();
      resizeMainRenderer();
      console.info('[Oxphyre panorama diagnostics]', {
        imageWidth: image.width || null,
        imageHeight: image.height || null,
        imageAspectRatio: aspect,
        windowWidth: window.innerWidth,
        windowHeight: window.innerHeight,
        rendererWidth: renderer.domElement.width,
        rendererHeight: renderer.domElement.height,
        devicePixelRatio: window.devicePixelRatio || 1,
        fov: MAIN_DEFAULT_FOV,
        cylinderHeight: diagnosticHeight,
        cylinderRadius: diagnosticRadius,
        coverageDeg,
        yawLimit: state.yawLimit,
      });
      animateMainPanorama();
    },
    undefined,
    () => {
      if (mainState === state) showUnavailable();
    }
  );

  const resize = () => {
    resizeMainRenderer();
    updateMainLimits();
  };
  addMainListener(window, 'resize', resize);
  addMainPointerListeners(container);
  resizeMainRenderer();
}

function createMainPanoramaGeometry(widthAngle, radius = 5.2, height = 4.8, widthSegments = 96, heightSegments = 16) {
  const positions = [];
  const uvs = [];
  const indices = [];

  for (let y = 0; y <= heightSegments; y++) {
    const v = y / heightSegments;
    const py = (0.5 - v) * height;

    for (let x = 0; x <= widthSegments; x++) {
      const u = x / widthSegments;
      const angle = (u - 0.5) * widthAngle;
      positions.push(Math.sin(angle) * radius, py, -Math.cos(angle) * radius);
      uvs.push(u, 1 - v);
    }
  }

  for (let y = 0; y < heightSegments; y++) {
    for (let x = 0; x < widthSegments; x++) {
      const a = y * (widthSegments + 1) + x;
      const b = a + 1;
      const c = a + widthSegments + 1;
      const d = c + 1;
      indices.push(a, c, b, b, c, d);
    }
  }

  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
  geometry.setAttribute('uv', new THREE.Float32BufferAttribute(uvs, 2));
  geometry.setIndex(indices);
  geometry.computeVertexNormals();
  geometry.userData.widthAngle = widthAngle;
  return geometry;
}

function addMainListener(target, type, handler, options) {
  target.addEventListener(type, handler, options);
  mainState.listeners.push({ target, type, handler, options });
}

function resizeMainRenderer() {
  if (!mainState?.renderer || !mainState.camera) return;

  const width = Math.max(1, mainState.container.clientWidth);
  const height = Math.max(1, mainState.container.clientHeight);
  mainState.camera.aspect = width / height;
  mainState.camera.updateProjectionMatrix();
  mainState.renderer.setSize(width, height, false);
}

function updateMainLimits() {
  if (!mainState?.geometry || !mainState.camera) return;

  const visibleAngle = THREE.MathUtils.degToRad(mainState.camera.fov * Math.max(1, mainState.camera.aspect));
  const halfCoverage = mainState.geometry.userData.widthAngle / 2;
  mainState.yawLimit = Math.max(0, halfCoverage - visibleAngle * 0.54);
  mainState.targetYaw = THREE.MathUtils.clamp(mainState.targetYaw, -mainState.yawLimit, mainState.yawLimit);
  mainState.yaw = THREE.MathUtils.clamp(mainState.yaw, -mainState.yawLimit, mainState.yawLimit);
}

function addMainPointerListeners(container) {
  const pitchLimit = THREE.MathUtils.degToRad(MAIN_PITCH_LIMIT_DEG);

  addMainListener(container, 'pointerdown', event => {
    if (event.target.closest('button')) return;
    mainState.dragging = true;
    mainState.lastX = event.clientX;
    mainState.lastY = event.clientY;
    container.classList.add('is-dragging');
    container.setPointerCapture(event.pointerId);
  });

  addMainListener(container, 'pointermove', event => {
    if (!mainState?.dragging) return;

    const dx = event.clientX - mainState.lastX;
    const dy = event.clientY - mainState.lastY;
    mainState.lastX = event.clientX;
    mainState.lastY = event.clientY;

    mainState.targetYaw = THREE.MathUtils.clamp(
      mainState.targetYaw + dx * 0.0032,
      -mainState.yawLimit,
      mainState.yawLimit
    );
    mainState.targetPitch = THREE.MathUtils.clamp(
      mainState.targetPitch + dy * 0.0012,
      -pitchLimit,
      pitchLimit
    );
  });

  const stopDrag = event => {
    if (!mainState?.dragging) return;
    mainState.dragging = false;
    container.classList.remove('is-dragging');
    try {
      container.releasePointerCapture(event.pointerId);
    } catch {
      // Algunos navegadores mÃ³viles liberan el puntero antes del pointerup.
    }
  };

  addMainListener(container, 'pointerup', stopDrag);
  addMainListener(container, 'pointercancel', stopDrag);
}

function animateMainPanorama() {
  if (!mainState || mainState.disposed || !mainState.mesh) return;

  mainState.yaw += (mainState.targetYaw - mainState.yaw) * 0.14;
  mainState.pitch += (mainState.targetPitch - mainState.pitch) * 0.12;
  mainState.camera.rotation.set(mainState.pitch, mainState.yaw, 0);
  mainState.renderer.render(mainState.scene, mainState.camera);
  mainState.frameId = requestAnimationFrame(animateMainPanorama);
}

function disposeMainPanorama() {
  if (!mainState) return;

  mainState.disposed = true;
  if (mainState.frameId) cancelAnimationFrame(mainState.frameId);
  mainState.listeners.forEach(({ target, type, handler, options }) => {
    target.removeEventListener(type, handler, options);
  });
  mainState.geometry?.dispose();
  mainState.material?.dispose();
  mainState.texture?.dispose();
  mainState.renderer?.dispose();
  mainState.renderer?.domElement?.remove();
  mainState.container.classList.remove('is-dragging');
  mainState.container.innerHTML = '';
  mainState = null;
}

function updateActiveBtn() {
  document.querySelectorAll('.tour-pos-btn').forEach((btn, i) => {
    const active = i === currentPositionIdx;
    btn.classList.toggle('active', active);
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
  });
}

function updateDetailsButton() {
  const btn = document.getElementById('tour-details-btn');
  if (!btn) return;

  btn.hidden = !hasRoom(currentPosition) || document.body.classList.contains('room-is-open');
}

function createCurvedPanelGeometry(centerAngle, radius = 4.6, widthAngle = THREE.MathUtils.degToRad(82), height = 3.05, widthSegments = 36, heightSegments = 10) {
  const positions = [];
  const uvs = [];
  const indices = [];

  for (let y = 0; y <= heightSegments; y++) {
    const v = y / heightSegments;
    const py = (0.5 - v) * height;

    for (let x = 0; x <= widthSegments; x++) {
      const u = x / widthSegments;
      const angle = centerAngle + (u - 0.5) * widthAngle;
      positions.push(Math.sin(angle) * radius, py, -Math.cos(angle) * radius);
      uvs.push(u, 1 - v);
    }
  }

  for (let y = 0; y < heightSegments; y++) {
    for (let x = 0; x < widthSegments; x++) {
      const a = y * (widthSegments + 1) + x;
      const b = a + 1;
      const c = a + widthSegments + 1;
      const d = c + 1;
      indices.push(a, c, b, b, c, d);
    }
  }

  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
  geometry.setAttribute('uv', new THREE.Float32BufferAttribute(uvs, 2));
  geometry.setIndex(indices);
  geometry.computeVertexNormals();
  return geometry;
}

function createRoomMaterial(texture) {
  return new THREE.ShaderMaterial({
    uniforms: {
      map: { value: texture },
      glowColor: { value: new THREE.Color(0xfeb354) },
    },
    vertexShader: `
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
      }
    `,
    fragmentShader: `
      uniform sampler2D map;
      uniform vec3 glowColor;
      varying vec2 vUv;
      void main() {
        vec4 texel = texture2D(map, vUv);
        float edgeX = smoothstep(0.0, 0.12, vUv.x) * (1.0 - smoothstep(0.88, 1.0, vUv.x));
        float edgeY = smoothstep(0.0, 0.08, vUv.y) * (1.0 - smoothstep(0.92, 1.0, vUv.y));
        float fade = edgeX * edgeY;
        vec3 dark = vec3(0.012, 0.009, 0.006);
        vec3 color = mix(dark, texel.rgb, fade);
        float rim = 1.0 - smoothstep(0.0, 0.16, min(min(vUv.x, 1.0 - vUv.x), min(vUv.y, 1.0 - vUv.y)));
        color += glowColor * rim * 0.055;
        gl_FragColor = vec4(color, 1.0);
      }
    `,
    side: THREE.DoubleSide,
  });
}

function createRoomParticles() {
  const count = 150;
  const positions = new Float32Array(count * 3);

  for (let i = 0; i < count; i++) {
    const angle = Math.random() * Math.PI * 2;
    const radius = 3.2 + Math.random() * 3.8;
    positions[i * 3] = Math.sin(angle) * radius;
    positions[i * 3 + 1] = -1.15 + Math.random() * 3.1;
    positions[i * 3 + 2] = -Math.cos(angle) * radius;
  }

  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

  const material = new THREE.PointsMaterial({
    color: 0xfeb354,
    size: 0.035,
    transparent: true,
    opacity: 0.36,
    blending: THREE.AdditiveBlending,
    depthWrite: false,
  });

  return new THREE.Points(geometry, material);
}

function createRoomCompass(room) {
  const compass = document.createElement('div');
  compass.className = 'room-compass';
  compass.setAttribute('aria-label', 'Fotos detalle Oxphyre Room');

  (roomState?.detailSlots || []).forEach(slot => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'room-compass-btn';
    btn.dataset.dir = slot.dir;
    btn.textContent = slot.label.replace('Foto detalle ', '');
    btn.title = slot.label;
    btn.setAttribute('aria-label', slot.label);
    btn.addEventListener('click', () => rotateRoomTo(slot.dir));
    compass.appendChild(btn);
  });

  room.appendChild(compass);
  return compass;
}

function addRoomListener(target, type, handler, options) {
  target.addEventListener(type, handler, options);
  roomState.listeners.push({ target, type, handler, options });
}

function initRoomScene(room) {
  disposeRoomScene();

  const container = document.getElementById('room-track');
  if (!container || typeof THREE === 'undefined') return false;

  container.innerHTML = '';
  const detailSlots = getDetailSlots(currentPosition);
  if (detailSlots.length === 0) return false;

  roomState = {
    room,
    container,
    detailSlots,
    renderer: null,
    scene: null,
    camera: null,
    frameId: null,
    textures: [],
    materials: [],
    geometries: [],
    listeners: [],
    compass: null,
    yaw: 0,
    targetYaw: 0,
    pitch: 0,
    targetPitch: 0,
    dragging: false,
    lastX: 0,
    lastY: 0,
    disposed: false,
    activeDir: null,
  };

  const scene = new THREE.Scene();
  scene.background = new THREE.Color(0x030201);
  scene.fog = new THREE.Fog(0x030201, 4.5, 9.5);

  const camera = new THREE.PerspectiveCamera(64, 1, 0.05, 50);
  camera.rotation.order = 'YXZ';

  const renderer = new THREE.WebGLRenderer({
    antialias: true,
    alpha: false,
    powerPreference: 'high-performance',
  });
  configureNeutralRenderer(renderer);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  renderer.setClearColor(0x030201, 1);
  container.appendChild(renderer.domElement);

  roomState.scene = scene;
  roomState.camera = camera;
  roomState.renderer = renderer;

  scene.add(new THREE.AmbientLight(0xffffff, 0.62));

  const warmLight = new THREE.PointLight(0xfeb354, 1.2, 8);
  warmLight.position.set(0, 1.3, 0);
  scene.add(warmLight);

  const loader = new THREE.TextureLoader();
  const maxAnisotropy = renderer.capabilities.getMaxAnisotropy?.() || 1;

  roomState.detailSlots.forEach(slot => {
    const texture = loader.load(slot.url, loadedTexture => {
      if (!roomState || roomState.disposed) {
        loadedTexture.dispose();
        return;
      }
      loadedTexture.needsUpdate = true;
    });

    configureNeutralTexture(texture);
    texture.anisotropy = maxAnisotropy;
    roomState.textures.push(texture);

    const geometry = createCurvedPanelGeometry(ROOM_PANEL_ANGLES[slot.dir] ?? 0);
    const material = createRoomMaterial(texture);
    roomState.geometries.push(geometry);
    roomState.materials.push(material);

    const panel = new THREE.Mesh(geometry, material);
    panel.name = `room-panel-${slot.dir}`;
    scene.add(panel);
  });

  const particles = createRoomParticles();
  roomState.geometries.push(particles.geometry);
  roomState.materials.push(particles.material);
  scene.add(particles);
  roomState.particles = particles;

  const ringGeometry = new THREE.TorusGeometry(4.15, 0.008, 8, 160);
  const ringMaterial = new THREE.MeshBasicMaterial({
    color: 0xfeb354,
    transparent: true,
    opacity: 0.18,
    blending: THREE.AdditiveBlending,
    depthWrite: false,
  });
  const ring = new THREE.Mesh(ringGeometry, ringMaterial);
  ring.rotation.x = Math.PI / 2;
  ring.position.y = -1.52;
  scene.add(ring);
  roomState.geometries.push(ringGeometry);
  roomState.materials.push(ringMaterial);

  roomState.compass = createRoomCompass(room);
  const initialDetailDir = roomState.detailSlots[0]?.dir || null;
  if (initialDetailDir) {
    roomState.yaw = ROOM_TARGET_YAW[initialDetailDir] ?? 0;
    roomState.targetYaw = roomState.yaw;
    updateRoomCompass(initialDetailDir);
  }

  const resize = () => resizeRoomRenderer();
  addRoomListener(window, 'resize', resize);
  resizeRoomRenderer();

  addRoomPointerListeners(room);
  animateRoom();
  return true;
}

function resizeRoomRenderer() {
  if (!roomState?.renderer || !roomState.camera) return;

  const width = Math.max(1, roomState.container.clientWidth);
  const height = Math.max(1, roomState.container.clientHeight);
  roomState.camera.aspect = width / height;
  roomState.camera.updateProjectionMatrix();
  roomState.renderer.setSize(width, height, false);
}

function addRoomPointerListeners(room) {
  const pitchLimit = THREE.MathUtils.degToRad(ROOM_PITCH_LIMIT_DEG);

  addRoomListener(room, 'pointerdown', event => {
    if (event.target.closest('button')) return;
    roomState.dragging = true;
    roomState.lastX = event.clientX;
    roomState.lastY = event.clientY;
    room.classList.add('is-dragging');
    room.setPointerCapture(event.pointerId);
  });

  addRoomListener(room, 'pointermove', event => {
    if (!roomState?.dragging) return;

    const dx = event.clientX - roomState.lastX;
    const dy = event.clientY - roomState.lastY;
    roomState.lastX = event.clientX;
    roomState.lastY = event.clientY;

    roomState.targetYaw -= dx * 0.0045;
    roomState.targetPitch = THREE.MathUtils.clamp(
      roomState.targetPitch + dy * 0.0035,
      -pitchLimit,
      pitchLimit
    );
  });

  const stopDrag = event => {
    if (!roomState?.dragging) return;
    roomState.dragging = false;
    room.classList.remove('is-dragging');
    try {
      room.releasePointerCapture(event.pointerId);
    } catch {
      // Algunos navegadores móviles liberan el puntero antes del pointerup.
    }
  };

  addRoomListener(room, 'pointerup', stopDrag);
  addRoomListener(room, 'pointercancel', stopDrag);
}

function rotateRoomTo(dir) {
  if (!roomState || !(dir in ROOM_TARGET_YAW)) return;

  const desired = ROOM_TARGET_YAW[dir];
  roomState.targetYaw += shortestAngleDelta(roomState.targetYaw, desired);
  roomState.targetPitch = 0;
}

function shortestAngleDelta(from, to) {
  return Math.atan2(Math.sin(to - from), Math.cos(to - from));
}

function getActiveRoomDirection(yaw) {
  if (!roomState?.detailSlots?.length) return null;

  let nearest = roomState.detailSlots[0];
  let nearestDistance = Infinity;

  roomState.detailSlots.forEach(slot => {
    const targetYaw = ROOM_TARGET_YAW[slot.dir] ?? 0;
    const distance = Math.abs(shortestAngleDelta(yaw, targetYaw));
    if (distance < nearestDistance) {
      nearest = slot;
      nearestDistance = distance;
    }
  });

  return nearest.dir;
}

function updateRoomCompass(activeDir) {
  if (!roomState?.compass || !activeDir || roomState.activeDir === activeDir) return;
  roomState.activeDir = activeDir;
  roomState.compass.querySelectorAll('.room-compass-btn').forEach(btn => {
    const active = btn.dataset.dir === activeDir;
    btn.classList.toggle('active', active);
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
  });
}

function animateRoom() {
  if (!roomState || roomState.disposed) return;

  roomState.yaw += shortestAngleDelta(roomState.yaw, roomState.targetYaw) * 0.12;
  roomState.pitch += (roomState.targetPitch - roomState.pitch) * 0.12;
  roomState.camera.rotation.set(roomState.pitch, roomState.yaw, 0);

  if (roomState.particles) {
    roomState.particles.rotation.y += 0.0008;
  }

  updateRoomCompass(getActiveRoomDirection(roomState.yaw));
  roomState.renderer.render(roomState.scene, roomState.camera);
  roomState.frameId = requestAnimationFrame(animateRoom);
}

function disposeRoomScene() {
  if (!roomState) return;

  roomState.disposed = true;
  if (roomState.frameId) cancelAnimationFrame(roomState.frameId);

  roomState.listeners.forEach(({ target, type, handler, options }) => {
    target.removeEventListener(type, handler, options);
  });

  roomState.scene?.traverse(obj => {
    if (obj.geometry) obj.geometry.dispose();
    if (Array.isArray(obj.material)) {
      obj.material.forEach(material => material.dispose?.());
    } else if (obj.material) {
      obj.material.dispose?.();
    }
  });

  roomState.textures.forEach(texture => texture.dispose());
  roomState.renderer?.dispose();
  roomState.renderer?.domElement?.remove();
  roomState.container.innerHTML = '';
  roomState.compass?.remove();
  roomState.room?.classList.remove('is-dragging');
  roomState = null;
}

function openRoom() {
  if (!hasRoom(currentPosition)) return;

  const room = document.getElementById('room-viewer');
  const detailsBtn = document.getElementById('tour-details-btn');
  if (!room) return;

  room.hidden = false;
  room.setAttribute('aria-hidden', 'false');
  document.body.classList.add('room-is-open');
  if (detailsBtn) detailsBtn.hidden = true;

  if (!initRoomScene(room)) {
    closeRoom();
  }
}

function closeRoom() {
  const room = document.getElementById('room-viewer');
  if (!room) return;

  disposeRoomScene();
  room.hidden = true;
  room.setAttribute('aria-hidden', 'true');
  document.body.classList.remove('room-is-open');
  updateDetailsButton();
}

function setupGyro() {
  const btn = document.getElementById('tour-gyro-btn');
  if (!btn) return;

  if (typeof DeviceOrientationEvent === 'undefined') {
    btn.style.display = 'none';
    return;
  }

  btn.addEventListener('click', async () => {
    if (gyroActive) {
      gyroActive = false;
      btn.classList.remove('active');
      btn.setAttribute('aria-label', 'Activar giroscopio');
      window.removeEventListener('deviceorientation', handleGyro);
      return;
    }

    if (typeof DeviceOrientationEvent.requestPermission === 'function') {
      try {
        const perm = await DeviceOrientationEvent.requestPermission();
        if (perm !== 'granted') return;
      } catch {
        return;
      }
    }

    window.addEventListener('deviceorientation', handleGyro);
    gyroActive = true;
    btn.classList.add('active');
    btn.setAttribute('aria-label', 'Desactivar giroscopio');
  });
}

function handleGyro(e) {
  if (!gyroActive || !mainState || e.alpha === null || document.body.classList.contains('room-is-open')) return;

  mainState.targetYaw = THREE.MathUtils.clamp(
    -THREE.MathUtils.degToRad(e.alpha),
    -mainState.yawLimit,
    mainState.yawLimit
  );
  mainState.targetPitch = 0;
}

document.addEventListener('DOMContentLoaded', () => {
  if (getPositions().length === 0) {
    showUnavailable();
    return;
  }

  initViewer();

  document.querySelectorAll('.tour-pos-btn').forEach((btn, idx) => {
    btn.addEventListener('click', () => loadPosition(idx));
  });

  document.getElementById('tour-details-btn')?.addEventListener('click', openRoom);
  document.getElementById('room-back-btn')?.addEventListener('click', closeRoom);
  setupGyro();
});
