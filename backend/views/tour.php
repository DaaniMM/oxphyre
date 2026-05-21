<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title><?= htmlspecialchars($tour['title']) ?> — <?= htmlspecialchars($business['name']) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <!-- Estilos base del visor — versión automática vía filemtime para evitar caché vieja -->
  <link rel="stylesheet" href="<?= asset('/css/tour.css') ?>">
<?php if ($businessLocation['hasCoords']): ?>
  <!-- Schema.org LocalBusiness: SEO estructurado con dirección y coordenadas del negocio -->
  <script type="application/ld+json">
<?php
$schema = ['@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => $business['name']];
$addr   = array_filter([
    'streetAddress'   => $businessLocation['address']    ?? null,
    'addressLocality' => $businessLocation['city']       ?? null,
    'postalCode'      => $businessLocation['postalCode'] ?? null,
    'addressCountry'  => $businessLocation['country']   ?? null,
]);
if ($addr) { $addr['@type'] = 'PostalAddress'; $schema['address'] = $addr; }
$schema['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => $businessLocation['lat'], 'longitude' => $businessLocation['lng']];
echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP);
?>
  </script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
<?php endif; ?>
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

<div class="tour-unavailable" id="tour-position-unavailable" hidden>
  <h1>Esta zona no está disponible en el tour</h1>
  <p>Es posible que el enlace esté desactualizado o que esta parte del local aún no esté lista para visitar.</p>
  <button type="button" id="tour-start-btn" class="tour-unavailable-action">
    Ver el tour desde el principio
  </button>
</div>

<?php if ($hasWatermark): ?>
<!-- Marca de agua Free: el overlay es decorativo y el badge lleva a la comparativa de planes -->
<div class="tour-watermark" aria-hidden="true"></div>
<a class="tour-watermark-badge"
   href="/precios"
   aria-label="Ver planes de Oxphyre: tour creado con Oxphyre">
  Creado con Oxphyre
</a>
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

<?php if ($businessLocation['hasCoords']): ?>
<!-- Botón ubicación: solo si el negocio tiene coordenadas geocodificadas -->
<button id="tour-location-btn" type="button" aria-label="Ver ubicación del negocio">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
    <circle cx="12" cy="10" r="3"/>
  </svg>
  Dónde estamos
</button>

<div id="tour-location-backdrop" class="tour-location-backdrop" aria-hidden="true"></div>
<div id="tour-location-sheet" class="tour-location-sheet" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="tour-location-title">
  <div class="tour-location-header">
    <h2 id="tour-location-title" class="tour-location-title">Dónde estamos</h2>
    <button id="tour-location-close" type="button" aria-label="Cerrar mapa de ubicación">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  </div>
  <div class="tour-location-body">
    <p class="tour-location-biz-name"><?= htmlspecialchars($business['name']) ?></p>
    <?php
    $addrParts = array_filter([
        $businessLocation['address'],
        trim(($businessLocation['postalCode'] ?? '') . ' ' . ($businessLocation['city'] ?? '')) ?: null,
        $businessLocation['country'],
    ]);
    ?>
    <?php if ($addrParts): ?>
    <address class="tour-location-address">
      <?= implode('<br>', array_map('htmlspecialchars', $addrParts)) ?>
    </address>
    <?php endif; ?>
    <div id="tour-location-map" class="tour-location-map" role="application" aria-label="Mapa de ubicación del negocio"></div>
    <a href="https://www.openstreetmap.org/directions?to=<?= urlencode($businessLocation['lat'] . ',' . $businessLocation['lng']) ?>"
       target="_blank" rel="noopener noreferrer"
       class="tour-location-directions">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M5 12h14M12 5l7 7-7 7"/>
      </svg>
      Cómo llegar
    </a>
  </div>
</div>
<?php endif; ?>

<!-- Datos del tour inyectados como variable JS global para tour-viewer.js -->
<script>
  const TOUR_DATA = <?= json_encode($tourData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>

<!-- Three.js para panorámica adaptativa y Oxphyre Room -->
<script src="https://cdn.jsdelivr.net/npm/three@0.147/build/three.min.js"></script>
<!-- Lógica del visor (después de Three.js, sin defer) — versión automática vía filemtime -->
<script src="<?= asset('/js/tour-viewer.js') ?>"></script>
<?php if ($businessLocation['hasCoords']): ?>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?= asset('/js/tour-location.js') ?>"></script>
<?php endif; ?>

</body>
</html>
