// Visor 360° de tours Oxphyre — basado en Photo Sphere Viewer (PSV)
// Depende de: Three.js CDN, PSV CDN, TOUR_DATA (inyectado por PHP en tour.php)

'use strict';

// ── Estado global ─────────────────────────────────────────────────────────────

let viewer           = null;
let currentPositionIdx = 0;
let currentPosition    = TOUR_DATA.positions[0];
let currentDirection   = 'N';
let gyroActive         = false;

// Evita llamadas simultáneas a setPanorama al cruzar umbrales de dirección
let isSwitchingPhoto = false;


// ── Helpers ───────────────────────────────────────────────────────────────────

// Devuelve la URL de la foto para una posición y dirección dadas.
// En modo panorámica usa siempre la foto '360'; en modo 4 fotos usa la dirección recibida.
function getPhotoUrl(position, direction) {
  if (!position) return null;
  const mode = position.activeMode || '4photos';
  if (mode === 'panoramic') {
    return position.photos['360']?.url || null;
  }
  return position.photos[direction]?.url || null;
}

// Devuelve el objeto panoData para PSV cuando la panorámica es parcial (iPhone ~270°).
// null en modo 4 fotos (PSV no necesita corrección).
function getPanoData(position) {
  if ((position.activeMode || '4photos') !== 'panoramic') return null;
  if (!position.photos['360']) return null;

  // PSV recibe panoData para tratar la imagen como equirectangular completa.
  // Para panorámicas parciales del iPhone, indicar fullWidth y fullHeight 2:1
  // evita la distorsión en techo y suelo que producía el visor Three.js anterior.
  return {
    isEquirectangular: true,
    fullWidth:         4096,
    fullHeight:        2048,  // ratio 2:1 estándar equirectangular
    croppedWidth:      4096,
    croppedHeight:     2048,
    croppedX:          0,
    croppedY:          0,
  };
}

// Convierte un ángulo yaw (grados) a la dirección de foto correspondiente.
// Los cuadrantes se reparten en 4 zonas de 90° cada una.
function getDirectionFromYaw(yawDeg) {
  const deg = ((yawDeg % 360) + 360) % 360;
  if (deg >= 315 || deg < 45)  return 'N';  // Frente       0°  ±45°
  if (deg >= 45  && deg < 135) return 'E';  // Derecha      90° ±45°
  if (deg >= 135 && deg < 225) return 'S';  // Fondo        180° ±45°
  return 'O';                               // Izquierda    270° ±45°
}


// ── Inicialización PSV ────────────────────────────────────────────────────────

function init() {
  const initialUrl = getPhotoUrl(currentPosition, 'N');

  if (!initialUrl) {
    console.warn('[tour-viewer] No hay foto disponible para la posición inicial.');
    return;
  }

  viewer = new PhotoSphereViewer.Viewer({
    container:  document.getElementById('psv-viewer'),
    panorama:   initialUrl,
    panoData:   getPanoData(currentPosition),
    defaultLong: 0,          // yaw inicial (radianes)
    defaultLat:  0,          // pitch inicial (radianes)
    navbar:      false,      // barra propia de PSV desactivada — usamos la nuestra
    loadingImg:  null,
    mousewheel:  false,      // sin zoom con rueda del ratón
  });

  // Cambio de foto al girar (solo en modo 4 fotos) — API PSV v4
  viewer.on('position-updated', (e, position) => {
    if ((currentPosition.activeMode || '4photos') !== '4photos') return;
    if (isSwitchingPhoto) return;

    // THREE.MathUtils (no THREE.Math — deprecado en Three.js 0.147)
    const yawDeg = THREE.MathUtils.radToDeg(position.longitude);
    const newDir = getDirectionFromYaw(yawDeg);

    if (newDir !== currentDirection) {
      const url = getPhotoUrl(currentPosition, newDir);
      if (url) {
        currentDirection = newDir;
        isSwitchingPhoto = true;
        // transition:false + showLoader:false para cambio instantáneo y silencioso
        viewer.setPanorama(url, { transition: false, showLoader: false })
          .then(() => { isSwitchingPhoto = false; })
          .catch(() => { isSwitchingPhoto = false; });
      }
    }
  });
}


// ── Navegación entre posiciones ───────────────────────────────────────────────

function loadPosition(idx) {
  if (idx === currentPositionIdx || !viewer) return;

  currentPositionIdx = idx;
  currentPosition    = TOUR_DATA.positions[idx];
  currentDirection   = 'N';

  const url      = getPhotoUrl(currentPosition, 'N');
  const panoData = getPanoData(currentPosition);

  if (!url) return;

  // fade: transición suave al cambiar de posición; showLoader: indicador de carga de PSV
  viewer.setPanorama(url, { transition: 'fade', showLoader: true, panoData });

  updateActiveBtn();
}

function updateActiveBtn() {
  document.querySelectorAll('.tour-pos-btn').forEach((btn, i) => {
    const active = i === currentPositionIdx;
    btn.classList.toggle('active', active);
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
  });
}


// ── Giroscopio (móvil) ────────────────────────────────────────────────────────

function setupGyro() {
  const btn = document.getElementById('tour-gyro-btn');
  if (!btn) return;

  // Ocultar si el dispositivo no expone giroscopio
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

    // iOS 13+ requiere permiso explícito del usuario
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
  if (!gyroActive || !viewer || e.alpha === null) return;
  // alpha: rotación horizontal (0–360°) → yaw de la cámara
  // PSV v4 rotate() recibe {longitude, latitude} no {yaw, pitch}
  viewer.rotate({
    longitude: -THREE.MathUtils.degToRad(e.alpha),
    latitude:  0,
  });
}


// ── Arrancar cuando el DOM esté listo ─────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
  if (!TOUR_DATA.positions || TOUR_DATA.positions.length === 0) {
    console.warn('[tour-viewer] TOUR_DATA sin posiciones.');
    return;
  }

  init();

  // Conectar botones de la barra de posiciones
  document.querySelectorAll('.tour-pos-btn').forEach((btn, idx) => {
    btn.addEventListener('click', () => loadPosition(idx));
  });

  setupGyro();
});
