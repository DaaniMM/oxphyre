<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title><?= htmlspecialchars($tour['title']) ?> — <?= htmlspecialchars($business['name']) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <!-- Estilos base del visor -->
  <link rel="stylesheet" href="/css/tour.css">
  <!-- PSV: CSS del núcleo -->
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core@5.4.4/index.min.css">
</head>
<body>

<!-- Contenedor principal de PSV — ocupa 100vw × 100vh -->
<div id="psv-viewer"></div>

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
  <i data-lucide="compass" width="20" height="20" aria-hidden="true"></i>
</button>

<!-- Datos del tour inyectados como variable JS global para tour-viewer.js -->
<script>
  const TOUR_DATA = <?= json_encode($tourData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- PSV standalone (incluye Three.js internamente — no cargar Three.js por separado) -->
<script src="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.standalone.min.js"></script>
<!-- Lógica del visor (debe ir después de PSV, sin defer) -->
<script src="/js/tour-viewer.js"></script>

</body>
</html>
