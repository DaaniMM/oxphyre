// Visor 360° de tours virtuales Oxphyre
// Depende de: Three.js 0.160.0 (cargado antes en el HTML), TOUR_DATA (inyectado por PHP)

'use strict';

// ── Estado global del visor ───────────────────────────────────────────────────

let renderer, scene, camera, sphere;
let standardMat, midasMat;

let currentPositionIdx = 0;
let lon = 0;        // rotación horizontal (0–360°)
let lat = 0;        // rotación vertical (−85° a +85°)
let isDragging = false;
let gyroActive = false;

// Delta suavizado para el shader MiDaS (EMA)
let prevLon = 0;
let prevLat = 0;
let smoothDeltaLon = 0;
let smoothDeltaLat = 0;

let dragStart = { x: 0, y: 0, lon: 0, lat: 0 };

const LAT_LIMIT = 85;

// ── Estado del cambio de dirección (modo 4 fotos) ─────────────────────────────

let currentDir = 'N';          // dirección actualmente visible
let isSwitchingDir = false;    // evita llamadas paralelas mientras carga
let lastDirSwitch = 0;         // timestamp del último cambio (debounce)
const DIR_COOLDOWN_MS = 800;   // ms mínimos entre cambios de dirección


// ── Inicialización ────────────────────────────────────────────────────────────

function init() {
  const canvas = document.getElementById('tour-canvas');

  renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(window.innerWidth, window.innerHeight);

  scene  = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1100);
  camera.target = new THREE.Vector3(0, 0, 0);

  // Esfera invertida: cámara dentro mirando la textura en la cara interna
  const geo  = new THREE.SphereGeometry(500, 60, 40);
  standardMat = new THREE.MeshBasicMaterial({ side: THREE.BackSide });
  sphere      = new THREE.Mesh(geo, standardMat);
  scene.add(sphere);

  midasMat = buildMiDaSMaterial();

  setupDrag(canvas);
  setupTouch(canvas);
  setupGyro();
  setupHotspots();
  setupResize();

  loadPosition(0).then(() => {
    document.getElementById('tour-loading').style.display = 'none';
  });

  animate();
}


// ── Material ShaderMaterial con efecto parallax MiDaS ────────────────────────

function buildMiDaSMaterial() {
  return new THREE.ShaderMaterial({
    uniforms: {
      u_texture: { value: null },
      u_depth:   { value: null },
      u_shift:   { value: new THREE.Vector2(0, 0) },
    },
    vertexShader: `
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
      }
    `,
    fragmentShader: `
      uniform sampler2D u_texture;
      uniform sampler2D u_depth;
      uniform vec2 u_shift;
      varying vec2 vUv;
      void main() {
        // Pixeles más cercanos (d alto) se desplazan más → efecto parallax 3D
        float d  = texture2D(u_depth, vUv).r;
        vec2 uv2 = vUv - u_shift * d * 0.035;
        gl_FragColor = texture2D(u_texture, uv2);
      }
    `,
    side: THREE.BackSide,
  });
}


// ── Carga de posición con fade ────────────────────────────────────────────────

async function loadPosition(idx) {
  if (idx < 0 || idx >= TOUR_DATA.positions.length) return;

  const fade = document.getElementById('tour-fade');
  const pos  = TOUR_DATA.positions[idx];

  fade.classList.add('visible');
  await sleep(300);

  const activeMode = pos.activeMode || '4photos';

  if (activeMode === 'panoramic' && pos.photos['360']) {
    // Modo panorámica: foto equirectangular cubre la esfera completa
    await applyPhoto(pos.photos['360']);
    currentDir = '360';
  } else {
    // Modo 4 fotos: carga foto N (Frente) como vista inicial
    const photo = pos.photos['N'] || null;
    if (photo) await applyPhoto(photo);
    currentDir = 'N';
  }

  currentPositionIdx = idx;
  updateHotspotButtons();

  fade.classList.remove('visible');
}

// Aplica una foto a la esfera — usa ShaderMaterial si hay depth map MiDaS disponible
async function applyPhoto(photo) {
  try {
    // La textura de la esfera es SIEMPRE photo.url (foto original)
    // photo.depthUrl solo entra como uniform u_depth en el shader, nunca como textura visible
    const tex = await loadTexture(photo.url);
    tex.colorSpace = THREE.SRGBColorSpace;

    const useMiDaS = TOUR_DATA.features.midas && photo.processed && photo.depthUrl;

    if (useMiDaS) {
      const depthTex = await loadTexture(photo.depthUrl);
      midasMat.uniforms.u_texture.value = tex;       // foto original → esfera visible
      midasMat.uniforms.u_depth.value   = depthTex;  // depth map → parallax interno
      sphere.material = midasMat;
    } else {
      standardMat.map = tex;
      standardMat.needsUpdate = true;
      sphere.material = standardMat;
    }
  } catch (err) {
    console.warn('[tour-viewer] Error cargando textura:', err);
  }
}


// ── Cambio de dirección en modo 4 fotos ───────────────────────────────────────

// Determina qué foto cargar según el ángulo horizontal actual
function getLonDirection(lon) {
  const l = ((lon % 360) + 360) % 360;
  if (l >= 315 || l < 45)  return 'N';  // Frente
  if (l >= 45  && l < 135) return 'E';  // Izquierda (mapeado según convención N/S/E/O)
  if (l >= 135 && l < 225) return 'S';  // Fondo
  return 'O';                           // Derecha (225–315)
}

// Cambia la textura de la esfera al girar a otra zona de dirección (con fade suave)
async function switchDirection(newDir, pos) {
  if (isSwitchingDir) return;
  const photo = pos.photos[newDir];
  if (!photo) return;

  isSwitchingDir = true;
  currentDir     = newDir;
  lastDirSwitch  = Date.now();

  const fade = document.getElementById('tour-fade');
  fade.classList.add('visible');
  await sleep(200);

  await applyPhoto(photo);

  fade.classList.remove('visible');
  isSwitchingDir = false;
}


// ── Helpers de textura ────────────────────────────────────────────────────────

// repeat.x = -1 + offset.x = 1 corrige el espejo horizontal de BackSide en esferas
function loadTexture(url) {
  return new Promise((resolve, reject) => {
    new THREE.TextureLoader().load(url, texture => {
      texture.repeat.x = -1;
      texture.offset.x =  1;
      resolve(texture);
    }, undefined, reject);
  });
}

function sleep(ms) {
  return new Promise(r => setTimeout(r, ms));
}


// ── Controles de arrastre (mouse) ─────────────────────────────────────────────

function setupDrag(canvas) {
  canvas.addEventListener('mousedown', e => {
    isDragging = true;
    dragStart = { x: e.clientX, y: e.clientY, lon, lat };
  });

  document.addEventListener('mousemove', e => {
    if (!isDragging) return;
    lon = (dragStart.x - e.clientX) * 0.25 + dragStart.lon;
    lat = (e.clientY - dragStart.y) * 0.25 + dragStart.lat;
  });

  document.addEventListener('mouseup', () => { isDragging = false; });
}


// ── Controles táctiles (móvil) ────────────────────────────────────────────────

function setupTouch(canvas) {
  let touchStart = { x: 0, y: 0, lon: 0, lat: 0 };

  canvas.addEventListener('touchstart', e => {
    e.preventDefault();
    const t = e.touches[0];
    touchStart = { x: t.clientX, y: t.clientY, lon, lat };
    isDragging = true;
  }, { passive: false });

  canvas.addEventListener('touchmove', e => {
    e.preventDefault();
    if (!isDragging || gyroActive) return;
    const t = e.touches[0];
    lon = (touchStart.x - t.clientX) * 0.25 + touchStart.lon;
    lat = (t.clientY - touchStart.y) * 0.25 + touchStart.lat;
  }, { passive: false });

  canvas.addEventListener('touchend', () => { isDragging = false; });
}


// ── Giroscopio ────────────────────────────────────────────────────────────────

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
      return;
    }

    // iOS 13+ requiere permiso explícito del usuario
    if (typeof DeviceOrientationEvent.requestPermission === 'function') {
      try {
        const perm = await DeviceOrientationEvent.requestPermission();
        if (perm !== 'granted') return;
      } catch {
        return;
      }
    }

    window.addEventListener('deviceorientation', handleGyroEvent);
    gyroActive = true;
    btn.classList.add('active');
    btn.setAttribute('aria-label', 'Desactivar giroscopio');
  });
}

function handleGyroEvent(e) {
  if (!gyroActive) return;
  if (e.alpha !== null) lon = -e.alpha;
  if (e.beta  !== null) lat = e.beta - 90;
}


// ── Hotspots de navegación entre posiciones ───────────────────────────────────

function setupHotspots() {
  document.querySelectorAll('.tour-pos-btn').forEach((btn, i) => {
    btn.addEventListener('click', () => {
      if (i !== currentPositionIdx) loadPosition(i);
    });
  });
}

function updateHotspotButtons() {
  document.querySelectorAll('.tour-pos-btn').forEach((btn, i) => {
    const isActive = i === currentPositionIdx;
    btn.classList.toggle('active', isActive);
    btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
  });
}


// ── Resize ────────────────────────────────────────────────────────────────────

function setupResize() {
  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });
}


// ── Loop de animación ─────────────────────────────────────────────────────────

function animate() {
  requestAnimationFrame(animate);

  // Auto-rotación cuando el usuario no interactúa
  if (!isDragging && !gyroActive) {
    lon += 0.03;
  }

  lat = Math.max(-LAT_LIMIT, Math.min(LAT_LIMIT, lat));

  // Delta suavizado (EMA) para el shader MiDaS
  const deltaLon = lon - prevLon;
  const deltaLat = lat - prevLat;
  smoothDeltaLon = smoothDeltaLon * 0.85 + deltaLon * 0.15;
  smoothDeltaLat = smoothDeltaLat * 0.85 + deltaLat * 0.15;

  if (sphere.material === midasMat) {
    midasMat.uniforms.u_shift.value.set(
      smoothDeltaLon * 0.0018,
      smoothDeltaLat * 0.0018
    );
  }

  prevLon = lon;
  prevLat = lat;

  // Cambio de textura por dirección solo en modo 4 fotos
  const pos = TOUR_DATA.positions[currentPositionIdx];
  if (pos && (pos.activeMode || '4photos') === '4photos' && !isSwitchingDir) {
    const newDir = getLonDirection(lon);
    if (newDir !== currentDir && Date.now() - lastDirSwitch > DIR_COOLDOWN_MS) {
      switchDirection(newDir, pos);
    }
  }

  // Posicionar la cámara según lon/lat en coordenadas esféricas
  const phi   = THREE.MathUtils.degToRad(90 - lat);
  const theta = THREE.MathUtils.degToRad(lon);

  camera.target.x = 500 * Math.sin(phi) * Math.cos(theta);
  camera.target.y = 500 * Math.cos(phi);
  camera.target.z = 500 * Math.sin(phi) * Math.sin(theta);

  camera.lookAt(camera.target);
  renderer.render(scene, camera);
}


// ── Arrancar cuando el DOM esté listo ─────────────────────────────────────────

document.addEventListener('DOMContentLoaded', init);
