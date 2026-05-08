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

// Acumula la velocidad de arrastre para calcular el shift del shader MiDaS
let prevLon = 0;
let prevLat = 0;
let smoothDeltaLon = 0;
let smoothDeltaLat = 0;

// Guarda el punto de inicio del drag para calcular el desplazamiento relativo
let dragStart = { x: 0, y: 0, lon: 0, lat: 0 };

const LAT_LIMIT = 85;  // evitar gimbal lock en los polos


// ── Inicialización ────────────────────────────────────────────────────────────

function init() {
  const canvas = document.getElementById('tour-canvas');

  // Renderer con antialias — el pixel ratio limita a 2 para no saturar GPUs móviles
  renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.setSize(window.innerWidth, window.innerHeight);

  scene  = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1100);
  // camera.target es el punto hacia el que apunta la cámara en cada frame
  camera.target = new THREE.Vector3(0, 0, 0);

  // Esfera grande invertida: la cámara queda dentro mirando la textura en la cara interna
  const geo  = new THREE.SphereGeometry(500, 60, 40);
  standardMat = new THREE.MeshBasicMaterial({ side: THREE.BackSide });
  sphere      = new THREE.Mesh(geo, standardMat);
  scene.add(sphere);

  // Material con shader para el efecto de paralaje con depth map de MiDaS
  midasMat = buildMiDaSMaterial();

  setupDrag(canvas);
  setupTouch(canvas);
  setupGyro();
  setupHotspots();
  setupResize();

  // Cargar la primera posición con foto
  loadPosition(0).then(() => {
    document.getElementById('tour-loading').style.display = 'none';
  });

  animate();
}


// ── Material ShaderMaterial para efecto de profundidad MiDaS ─────────────────

function buildMiDaSMaterial() {
  return new THREE.ShaderMaterial({
    uniforms: {
      u_texture: { value: null },
      u_depth:   { value: null },
      // Desplazamiento UV basado en el movimiento de cámara, suavizado por EMA
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

  // Fade negro para ocultar la carga de la nueva textura
  fade.classList.add('visible');
  await sleep(300);

  // Usar la primera foto disponible en orden de prioridad N→S→E→O
  const photo = getFirstPhoto(pos);

  if (photo) {
    try {
      const tex = await loadTexture(photo.url);
      tex.colorSpace = THREE.SRGBColorSpace;

      // Activar shader MiDaS solo si el plan lo permite Y hay depth map para esta foto
      const useMiDaS = TOUR_DATA.features.midas && photo.processed && photo.depthUrl;

      if (useMiDaS) {
        const depthTex = await loadTexture(photo.depthUrl);
        midasMat.uniforms.u_texture.value = tex;
        midasMat.uniforms.u_depth.value   = depthTex;
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

  currentPositionIdx = idx;
  updateHotspotButtons();

  // Fade out: revelar la nueva posición
  fade.classList.remove('visible');
}

// Devuelve la primera foto disponible de una posición (N > S > E > O)
function getFirstPhoto(pos) {
  for (const dir of ['N', 'S', 'E', 'O']) {
    if (pos.photos[dir]) return pos.photos[dir];
  }
  return null;
}

// Promesa que carga una textura con THREE.TextureLoader
function loadTexture(url) {
  return new Promise((resolve, reject) => {
    new THREE.TextureLoader().load(url, resolve, undefined, reject);
  });
}

// Promesa de espera mínima
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
    // Factor 0.25 da una sensibilidad de arrastre suave
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
    // El giroscopio tiene prioridad sobre el drag táctil
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

  // Ocultar el botón si el dispositivo no tiene giroscopio
  if (typeof DeviceOrientationEvent === 'undefined') {
    btn.style.display = 'none';
    return;
  }

  btn.addEventListener('click', async () => {
    if (gyroActive) {
      // Desactivar
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
  // alpha: rotación alrededor del eje vertical (0–360°) → lon
  // beta: inclinación frontal (−180° a 180°) → lat, restamos 90° para neutro horizontal
  if (e.alpha !== null) lon = -e.alpha;
  if (e.beta  !== null) lat = e.beta - 90;
}


// ── Botones de hotspot de posición ────────────────────────────────────────────

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

  // Auto-rotación suave cuando el usuario no interactúa ni usa giroscopio
  if (!isDragging && !gyroActive) {
    lon += 0.03;
  }

  // Limitar inclinación vertical para no dar la vuelta por los polos
  lat = Math.max(-LAT_LIMIT, Math.min(LAT_LIMIT, lat));

  // Calcular delta de movimiento suavizado (EMA) para el shader MiDaS
  const deltaLon = lon - prevLon;
  const deltaLat = lat - prevLat;
  // Factor 0.15: decae rápido para que el efecto solo sea visible durante el movimiento
  smoothDeltaLon = smoothDeltaLon * 0.85 + deltaLon * 0.15;
  smoothDeltaLat = smoothDeltaLat * 0.85 + deltaLat * 0.15;

  if (sphere.material === midasMat) {
    // Multiplicar por un factor pequeño: el shift visual no debe ser perceptible como glitch
    midasMat.uniforms.u_shift.value.set(
      smoothDeltaLon * 0.0018,
      smoothDeltaLat * 0.0018
    );
  }

  prevLon = lon;
  prevLat = lat;

  // Calcular el punto de destino de la cámara en coordenadas esféricas
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
