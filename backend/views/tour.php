<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title><?= htmlspecialchars($tour['title']) ?> — <?= htmlspecialchars($business['name']) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/tour.css">
</head>
<body>

<!-- Canvas Three.js (full screen) -->
<canvas id="tour-canvas"></canvas>

<!-- Pantalla de carga inicial -->
<div id="tour-loading" role="status" aria-live="polite">
  <div class="tour-loading-spinner" aria-hidden="true"></div>
  <p>Cargando visor...</p>
</div>

<!-- Overlay negro para transiciones entre posiciones (fade) -->
<div id="tour-fade" aria-hidden="true"></div>

<!-- Nombre del negocio y tour (esquina superior izquierda) -->
<div id="tour-header" aria-label="Información del tour">
  <p class="tour-header-biz"><?= htmlspecialchars($business['name']) ?></p>
  <p class="tour-header-title"><?= htmlspecialchars($tour['title']) ?></p>
</div>

<?php if (!empty($tourPositions)): ?>
<!-- Navegación entre posiciones (barra inferior centrada) -->
<nav id="tour-positions" aria-label="Posiciones del tour">
  <?php foreach ($tourPositions as $i => $pos): ?>
    <button class="tour-pos-btn <?= $i === 0 ? 'active' : '' ?>"
            type="button"
            data-pos-index="<?= $i ?>"
            aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"
            title="<?= htmlspecialchars($pos['name']) ?>">
      <span class="tour-pos-dot" aria-hidden="true"></span>
      <span class="tour-pos-name"><?= htmlspecialchars($pos['name']) ?></span>
    </button>
  <?php endforeach; ?>
</nav>
<?php endif; ?>

<!-- Botón giroscopio (solo visible en dispositivos táctiles) -->
<button id="tour-gyro-btn" type="button" aria-label="Activar giroscopio" title="Giroscopio">
  <i data-lucide="compass" width="20" height="20" aria-hidden="true"></i>
</button>

<?php if ($hasWatermark): ?>
<!-- Marca de agua plan Free: visible pero no intrusiva -->
<a href="https://oxphyre.com" target="_blank" rel="noopener noreferrer"
   id="tour-watermark" aria-label="Tour creado con Oxphyre">
  Powered by <strong>Oxphyre</strong>
</a>
<?php endif; ?>

<?php if ($hasMinimapa): ?>
<!-- Minimapa (Pro/Business) — placeholder, implementación completa pendiente -->
<div id="tour-minimap" aria-label="Minimapa del tour" role="img">
  <div id="tour-minimap-inner"></div>
</div>
<?php endif; ?>

<!-- Datos del tour inyectados como variable JS global para tour-viewer.js -->
<script>
  const TOUR_DATA = <?= json_encode($tourData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="https://unpkg.com/three@0.160.0/build/three.min.js"></script>
<script src="/js/tour-viewer.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>

</body>
</html>
