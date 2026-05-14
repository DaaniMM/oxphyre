<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title><?= htmlspecialchars($tour['title']) ?> — <?= htmlspecialchars($business['name']) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <!-- Estilos base del visor -->
  <link rel="stylesheet" href="/css/tour.css">
</head>
<body>

<!-- Contenedor principal de la panorámica adaptativa -->
<div id="psv-viewer"></div>

<!-- Loading overlay: visible mientras carga la textura panorámica -->
<div id="psv-loading" class="tour-loading" aria-live="polite" aria-label="Cargando panorámica">
  <div class="tour-loading-spinner"></div>
  <p class="tour-loading-text">Cargando vista…</p>
</div>

<div class="tour-unavailable" id="tour-unavailable" hidden>
  <h1>Tour no disponible</h1>
  <p>Este tour todavía no está listo.</p>
</div>

<?php if ($hasWatermark): ?>
<!-- Marca de agua: solo visible en plan Free -->
<div class="tour-watermark">
  Powered by <a href="https://oxphyre.com" target="_blank" rel="noopener noreferrer">Oxphyre</a>
</div>
<?php endif; ?>

<?php if (count($tourPositions) > 1): ?>
<!-- Barra de puntos para navegar entre posiciones (solo si hay más de una) -->
<div class="tour-positions-bar" id="tour-positions-bar" role="navigation" aria-label="Posiciones del tour">
  <?php foreach ($tourPositions as $i => $pos): ?>
    <button class="tour-pos-btn <?= $i === 0 ? 'active' : '' ?>"
            type="button"
            data-idx="<?= $i ?>"
            data-name="<?= htmlspecialchars($pos['name']) ?>"
            aria-label="<?= htmlspecialchars($pos['name']) ?>"
            aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
            title="<?= htmlspecialchars($pos['name']) ?>">
    </button>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Botón giroscopio (solo visible en móvil via CSS pointer:coarse) -->
<button id="tour-gyro-btn" type="button" aria-label="Activar giroscopio">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <circle cx="12" cy="12" r="10"/>
    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
  </svg>
</button>

<button id="tour-details-btn" class="tour-details-btn" type="button" hidden>
  Ver detalles
</button>

<div id="room-viewer" class="room-viewer" hidden aria-hidden="true">
  <div class="room-hud">
    <div>
      <strong>Oxphyre Room</strong>
      <span>Arrastra para mirar alrededor</span>
    </div>
    <button id="room-back-btn" type="button">Volver a vista principal</button>
  </div>
  <div id="room-track" class="room-track" aria-live="polite"></div>
</div>

<!-- Datos del tour inyectados como variable JS global para tour-viewer.js -->
<script>
  const TOUR_DATA = <?= json_encode($tourData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Three.js para panorámica adaptativa y Oxphyre Room -->
<script src="https://cdn.jsdelivr.net/npm/three@0.147/build/three.min.js"></script>
<!-- Lógica del visor (después de Three.js, sin defer) -->
<script src="/js/tour-viewer.js?v=20260514-6"></script>

</body>
</html>
