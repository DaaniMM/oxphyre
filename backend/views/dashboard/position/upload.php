<?php
  // Por compatibilidad, la BD mantiene N/S/E/O como direcciones internas.
  // La UI las presenta como fotos detalle 1-4 para que Oxphyre Room no
  // parezca un requisito tecnico de cuatro angulos obligatorios.
  $detailSlots = [
    'N' => 'Foto detalle 1',
    'S' => 'Foto detalle 2',
    'E' => 'Foto detalle 3',
    'O' => 'Foto detalle 4',
  ];
  $orientations = $detailSlots;
  $roomPhotoCount = $roomPhotoCount ?? 0;
  $hasPanorama = $hasPanorama ?? ($photo360 !== null);
  $hasDetailPhotos = $roomPhotoCount > 0;
  $hasOxphyreRoom = $hasDetailPhotos;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subir fotos — <?= htmlspecialchars($position['name']) ?> — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <!-- Versión automática vía filemtime para evitar caché vieja tras despliegues -->
  <link rel="stylesheet" href="<?= asset('/css/dashboard.css') ?>">
</head>
<body>

<div class="upload-tip-overlay" id="upload-tip-overlay" role="dialog"
     aria-modal="true" aria-labelledby="tip-title" style="display:none;">
  <div class="upload-tip-modal">
    <button class="upload-tip-close" id="tip-close" aria-label="Cerrar ayuda">
      <i data-lucide="x" width="16" height="16" aria-hidden="true"></i>
    </button>

    <h2 class="upload-tip-title" id="tip-title">Cómo completar una posición</h2>

    <ol class="upload-tip-steps">
      <li>Sube una panorámica principal. Es obligatoria.</li>
      <li>Añade de 1 a 4 fotos detalle para destacar partes clave de esta zona.</li>
      <li>Las flechas de navegación servirán para conectar esta zona con otras posiciones.</li>
    </ol>

    <p class="upload-tip-footer">
      <i data-lucide="info" width="14" height="14" style="flex-shrink:0;color:var(--ox-amber);" aria-hidden="true"></i>
      Consejo: sube las fotos originales desde el móvil y evita WhatsApp para conservar calidad.
    </p>

    <div class="upload-tip-actions">
      <button type="button" class="wizard-btn-submit" id="tip-understood">Entendido</button>
      <button type="button" class="db-btn-ghost" id="tip-never">No volver a mostrar</button>
    </div>
  </div>
</div>

<div class="db-overlay" id="db-overlay" aria-hidden="true"></div>

<div class="db-layout">

  <aside class="db-sidebar" id="db-sidebar" role="navigation" aria-label="Navegación principal">
    <div class="db-sidebar-header">
      <a href="/" class="db-logo" aria-label="Oxphyre inicio">Oxphyre</a>
      <button class="db-sidebar-close" id="db-sidebar-close" aria-label="Cerrar menú">
        <i data-lucide="x" width="18" height="18"></i>
      </button>
    </div>
    <nav class="db-nav">
      <a href="/dashboard" class="db-nav-item">
        <i data-lucide="home" width="18" height="18" aria-hidden="true"></i>
        <span>Inicio</span>
      </a>
      <a href="/dashboard/tours" class="db-nav-item active" aria-current="page">
        <i data-lucide="play-circle" width="18" height="18" aria-hidden="true"></i>
        <span>Mis tours</span>
      </a>
      <a href="/dashboard/negocios" class="db-nav-item">
        <i data-lucide="building-2" width="18" height="18" aria-hidden="true"></i>
        <span>Negocios</span>
      </a>
      <a href="/dashboard/analiticas" class="db-nav-item">
        <i data-lucide="bar-chart-2" width="18" height="18" aria-hidden="true"></i>
        <span>Analíticas</span>
      </a>
      <a href="/dashboard/configuracion" class="db-nav-item">
        <i data-lucide="settings" width="18" height="18" aria-hidden="true"></i>
        <span>Configuración</span>
      </a>
    </nav>
    <div class="db-sidebar-footer">
      <div class="db-plan-badge" aria-label="Plan actual: <?= htmlspecialchars($planLabel) ?>">
        <span class="db-plan-label">Plan</span>
        <span class="db-plan-name"><?= htmlspecialchars($planLabel) ?></span>
        <?php if ($planLabel !== 'Business'): ?>
          <a href="/precios" class="db-upgrade-link">Mejorar →</a>
        <?php endif; ?>
      </div>
      <form action="/logout" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <button type="submit" class="db-logout-btn">
          <i data-lucide="log-out" width="16" height="16" aria-hidden="true"></i>
          <span>Cerrar sesión</span>
        </button>
      </form>
    </div>
  </aside>

  <header class="db-topbar">
    <button class="db-hamburger" id="db-hamburger" aria-label="Abrir menú" aria-expanded="false" aria-controls="db-sidebar">
      <i data-lucide="menu" width="20" height="20" aria-hidden="true"></i>
    </button>
    <h1 class="db-topbar-title">
      <a href="/dashboard/negocios" style="color:var(--ox-text-muted);font-weight:400;">Negocios</a>
      <span style="margin:0 0.5rem;color:var(--ox-text-dim);">/</span>
      <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>"
         style="color:var(--ox-text-muted);font-weight:400;"><?= htmlspecialchars($business['name']) ?></a>
      <span style="margin:0 0.5rem;color:var(--ox-text-dim);">/</span>
      <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>"
         style="color:var(--ox-text-muted);font-weight:400;"><?= htmlspecialchars($tour['title']) ?></a>
      <span style="margin:0 0.5rem;color:var(--ox-text-dim);">/</span>
      <?= htmlspecialchars($position['name']) ?>
    </h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <main class="db-main">
    <div class="db-page">

      <?php if ($flash): ?>
        <div role="alert" style="padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.25rem;
          <?= $flash['type'] === 'success'
            ? 'background:oklch(0.35 0.10 145/0.2);border:1px solid oklch(0.55 0.12 145/0.4);color:oklch(0.80 0.14 145);'
            : 'background:oklch(0.35 0.12 25/0.2);border:1px solid oklch(0.55 0.15 25/0.4);color:oklch(0.80 0.10 25);'
          ?>">
          <?= htmlspecialchars($flash['message']) ?>
          <?php if (!empty($flash['secondary'])): ?>
            <div style="margin-top:0.45rem;font-size:0.78rem;line-height:1.5;color:var(--ox-text-muted);">
              <?= htmlspecialchars($flash['secondary']) ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="db-manage-header" style="margin-bottom:1.25rem;">
        <div class="db-manage-header-left">
          <div class="db-manage-header-top">
            <h2 class="db-manage-name"><?= htmlspecialchars($position['name']) ?></h2>
            <span class="db-badge db-badge--draft">Posición #<?= (int) $position['order_index'] ?></span>
          </div>
          <p class="db-manage-desc" style="margin-top:0.25rem;">
            Completa esta posición con una panorámica principal y, si quieres, añade fotos detalle para destacar zonas concretas.
          </p>
        </div>
        <div class="position-header-actions">
          <?php if ((bool) $tour['is_published']): ?>
            <?php
              // La panoramica principal activa la experiencia visitable.
              // Las fotos detalle 1-4 son opcionales y pueden completarse despues.
              $positionPreviewTooltip = 'Sube una panorámica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales.';
            ?>
            <?php if ($hasPanorama): ?>
              <a class="db-preview-link"
                 href="/tour/<?= htmlspecialchars($business['slug']) ?>/<?= htmlspecialchars($tour['slug']) ?>?position=<?= (int) $position['id'] ?>"
                 target="_blank"
                 rel="noopener">
                <i data-lucide="external-link" width="15" height="15" aria-hidden="true"></i>
                Ver esta posición
              </a>
            <?php else: ?>
              <span class="db-preview-link"
                    role="button"
                    aria-disabled="true"
                    title="<?= htmlspecialchars($positionPreviewTooltip) ?>"
                    style="opacity:0.55;cursor:not-allowed;filter:grayscale(0.35);">
                <i data-lucide="external-link" width="15" height="15" aria-hidden="true"></i>
                Ver esta posición
              </span>
            <?php endif; ?>
          <?php endif; ?>
        <button type="button" class="db-help-icon" id="reopen-tip" aria-label="Cómo completar una posición">
          <i data-lucide="circle-help" width="18" height="18" aria-hidden="true"></i>
          <span class="db-help-tooltip">Ver instrucciones de subida</span>
        </button>
        </div>
      </div>

      <div style="display:flex;align-items:flex-start;gap:0.5rem;font-size:0.8125rem;color:var(--ox-text-muted);
                  background:var(--ox-bg-elevated);border:1px solid var(--ox-border);border-radius:10px;
                  padding:0.875rem 1rem;margin-bottom:1.5rem;">
        <i data-lucide="cpu" width="16" height="16" style="flex-shrink:0;margin-top:1px;color:var(--ox-amber);" aria-hidden="true"></i>
        <span>El procesado con IA puede tardar <strong>30-60 segundos por foto</strong>. Las fotos sin procesar se guardan igualmente.</span>
      </div>

      <form action="/dashboard/posicion/upload" method="POST"
            enctype="multipart/form-data" id="upload-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="biz_slug" value="<?= htmlspecialchars($business['slug']) ?>">
        <input type="hidden" name="tour_slug" value="<?= htmlspecialchars($tour['slug']) ?>">
        <input type="hidden" name="position_id" value="<?= (int) $position['id'] ?>">

        <section class="upload-flow-card upload-flow-card--required" aria-labelledby="panorama-title">
          <div class="upload-flow-card-header">
            <div>
              <p class="upload-flow-kicker">Panorámica principal</p>
              <h3 class="upload-flow-title" id="panorama-title">Obligatoria</h3>
            </div>
            <span class="db-badge <?= $hasPanorama ? 'db-badge--published' : 'db-badge--draft' ?>">
              <?= $hasPanorama ? 'Completada' : 'Pendiente' ?>
            </span>
          </div>

          <p class="upload-flow-copy">
            Será la vista principal que verán tus clientes al entrar en esta posición.
          </p>
          <p class="upload-flow-help">
            Usa una panorámica hecha con tu móvil. No pasa nada si no cubre los 360º completos: Oxphyre limitará la vista para evitar zonas vacías.
          </p>

          <div class="db-upload-zone-360 <?= $photo360 ? 'has-file' : '' ?>" id="zone-360"
               role="region" aria-label="Foto panorámica principal">

            <?php if ($photo360): ?>
              <?php $photo360Url = $photo360['resolved_url'] ?? ('/uploads/' . (int) $position['id'] . '/' . $photo360['filename']); ?>
              <img src="<?= htmlspecialchars($photo360Url) ?>"
                   alt="Foto panorámica principal" class="db-upload-zone-360-preview" id="preview-360">
              <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
                <span class="db-upload-zone-dir">Panorámica</span>
                <?php if ($photo360['processed']): ?>
                  <span class="db-badge db-badge--published" style="font-size:9px;">Procesada</span>
                <?php else: ?>
                  <span class="db-badge db-badge--draft" style="font-size:9px;">Sin IA</span>
                <?php endif; ?>
                <button type="submit"
                        class="db-upload-delete-btn"
                        form="delete-photo-360"
                        title="Eliminar panorámica"
                        aria-label="Eliminar panorámica principal">
                  <i data-lucide="trash-2" width="13" height="13" aria-hidden="true"></i>
                  Eliminar
                </button>
              </div>
            <?php else: ?>
              <img alt="" class="db-upload-zone-360-preview" id="preview-360" style="display:none;">
              <div data-empty-360 class="upload-flow-empty">
                <i data-lucide="panorama" width="32" height="32" aria-hidden="true"></i>
                <span>Sin panorámica principal</span>
              </div>
            <?php endif; ?>

            <input type="file" name="photo_360" id="input-360"
                   class="db-upload-input" accept="image/jpeg,image/png,image/webp,image/heic,image/heif"
                   aria-label="Seleccionar panorámica principal">
            <button type="button" class="db-upload-btn" style="margin-top:0.5rem;"
                    onclick="document.getElementById('input-360').click()">
              <i data-lucide="upload" width="14" height="14" aria-hidden="true"></i>
              <?= $photo360 ? 'Cambiar panorámica' : 'Subir panorámica' ?>
            </button>
          </div>
        </section>

        <section class="upload-flow-card" aria-labelledby="room-title">
          <div class="upload-flow-card-header">
            <div>
              <p class="upload-flow-kicker">Fotos detalle</p>
              <h3 class="upload-flow-title" id="room-title">Opcionales</h3>
            </div>
            <span class="db-badge <?= $hasOxphyreRoom ? 'db-badge--published' : 'db-badge--draft' ?>">
              <?= $hasDetailPhotos ? $roomPhotoCount . '/4 detalles' : '0/4 detalles' ?>
            </span>
          </div>

          <p class="upload-flow-copy">
            Añade de 1 a 4 fotos detalle para destacar partes clave de esta zona: barra, mesa, escaparate, producto, decoración o un rincón especial.
          </p>
          <p class="upload-flow-help">
            Recomendamos fotos horizontales, con buena luz y subidas directamente desde el móvil.
          </p>

          <div class="db-upload-grid">
            <?php foreach ($orientations as $dir => $label): ?>
              <?php $existing = $photosByDir[$dir] ?? null; ?>
              <div class="db-upload-zone <?= $existing ? 'has-file' : '' ?>"
                   id="zone-<?= $dir ?>" role="region" aria-label="Foto <?= htmlspecialchars($label) ?>">

                <div style="display:flex;align-items:center;justify-content:space-between;">
                  <span class="db-upload-zone-dir"><?= htmlspecialchars($label) ?></span>
                  <?php if ($existing && $existing['processed']): ?>
                    <span class="db-badge db-badge--published" style="font-size:9px;">Procesada</span>
                  <?php elseif ($existing): ?>
                    <span class="db-badge db-badge--draft" style="font-size:9px;">Sin IA</span>
                  <?php endif; ?>
                  <?php if ($existing): ?>
                    <button type="submit"
                            class="db-upload-delete-icon"
                            form="delete-photo-<?= $dir ?>"
                            title="Eliminar foto <?= htmlspecialchars($label) ?>"
                            aria-label="Eliminar foto <?= htmlspecialchars($label) ?>">
                      <i data-lucide="trash-2" width="13" height="13" aria-hidden="true"></i>
                    </button>
                  <?php endif; ?>
                </div>

                <div class="db-upload-preview" id="preview-<?= $dir ?>">
                  <?php if ($existing): ?>
                    <?php $existingUrl = $existing['resolved_url'] ?? ('/uploads/' . (int) $position['id'] . '/' . $existing['filename']); ?>
                    <img src="<?= htmlspecialchars($existingUrl) ?>"
                         alt="Foto <?= htmlspecialchars($label) ?>"
                         style="display:block;width:100%;height:100%;object-fit:cover;">
                  <?php else: ?>
                    <img alt="" style="display:none;width:100%;height:100%;object-fit:cover;">
                    <div class="db-upload-preview-placeholder">
                      <i data-lucide="image" width="24" height="24" aria-hidden="true"></i>
                      <span>Sin foto</span>
                    </div>
                  <?php endif; ?>
                </div>

                <input type="file" name="photo_<?= $dir ?>" id="input-<?= $dir ?>"
                       class="db-upload-input" accept="image/jpeg,image/png,image/webp,image/heic,image/heif"
                       aria-label="Seleccionar foto <?= htmlspecialchars($label) ?>">
                <button type="button" class="db-upload-btn"
                        onclick="document.getElementById('input-<?= $dir ?>').click()">
                  <i data-lucide="upload" width="14" height="14" aria-hidden="true"></i>
                  <?= $existing ? 'Cambiar foto' : 'Seleccionar foto' ?>
                </button>
              </div>
            <?php endforeach; ?>
          </div>

          <p class="upload-flow-cta-note">
            <?= $roomPhotoCount > 0 ? 'Puedes actualizar los detalles disponibles.' : 'La panorámica funciona sola; añade detalles si quieres destacar algo.' ?>
          </p>
        </section>

        <section class="upload-flow-card <?= $canEditNavigationArrows ? '' : 'upload-flow-card--locked' ?>"
                 aria-labelledby="navigation-arrows-title"
                 id="navigation-arrows-panel"
                 data-navigation-arrows-ready="<?= $canEditNavigationArrows ? '1' : '0' ?>">
          <div class="upload-flow-card-header">
            <div>
              <p class="upload-flow-kicker">Flechas de navegación</p>
              <h3 class="upload-flow-title" id="navigation-arrows-title">Conecta esta zona con otras zonas del tour</h3>
            </div>
            <span class="db-badge <?= $canEditNavigationArrows ? 'db-badge--published' : 'db-badge--draft' ?>">
              <?= $canEditNavigationArrows ? 'Disponible' : 'Pendiente' ?>
            </span>
          </div>
          <p class="upload-flow-help">
            Las flechas aparecerán sobre la panorámica y permitirán que tus clientes avancen por el recorrido.
          </p>

          <?php if (!$hasPanorama): ?>
            <div class="upload-flow-locked-row">
              <i data-lucide="lock" width="16" height="16" aria-hidden="true"></i>
              <span>Para añadir flechas de navegación, primero sube la foto panorámica de esta zona.</span>
            </div>
          <?php elseif ($navigationTargetCount < 1): ?>
            <div class="upload-flow-locked-row">
              <i data-lucide="map" width="16" height="16" aria-hidden="true"></i>
              <span>Aún no hay más zonas a las que navegar. Añade al menos una zona más con panorámica para crear flechas de navegación.</span>
            </div>
          <?php else: ?>
            <div class="navigation-arrows-actions">
              <button type="button" class="wizard-btn-submit" id="navigation-arrows-open">
                <i data-lucide="navigation" width="16" height="16" aria-hidden="true"></i>
                Editar flechas de navegación
              </button>
              <span class="navigation-arrows-status" id="navigation-arrows-status" aria-live="polite">
                Preparado para editar.
              </span>
            </div>
            <div class="navigation-arrows-editor" id="navigation-arrows-editor" hidden>
              <p class="navigation-arrows-instructions">
                Listado de zonas disponibles. Pulsa una zona para añadir o editar su flecha de navegación.
              </p>
              <div class="navigation-arrows-list" id="navigation-arrows-list"></div>
            </div>
          <?php endif; ?>
        </section>

        <div class="wizard-nav" style="margin-top:1.75rem;">
          <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>"
             class="wizard-btn-back">
            <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
            Cancelar
          </a>
          <button type="submit" class="wizard-btn-submit" id="btn-submit-upload">
            <i data-lucide="cpu" width="16" height="16" aria-hidden="true"></i>
            Guardar y procesar fotos →
          </button>
        </div>
      </form>

      <?php if ($photo360): ?>
        <form action="/dashboard/posicion/photo/delete" method="POST" id="delete-photo-360" class="js-delete-photo-form">
          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
          <input type="hidden" name="biz_slug" value="<?= htmlspecialchars($business['slug']) ?>">
          <input type="hidden" name="tour_slug" value="<?= htmlspecialchars($tour['slug']) ?>">
          <input type="hidden" name="position_id" value="<?= (int) $position['id'] ?>">
          <input type="hidden" name="direction" value="360">
        </form>
      <?php endif; ?>

      <?php foreach ($orientations as $dir => $label): ?>
        <?php if (!empty($photosByDir[$dir])): ?>
          <form action="/dashboard/posicion/photo/delete" method="POST" id="delete-photo-<?= $dir ?>" class="js-delete-photo-form">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="biz_slug" value="<?= htmlspecialchars($business['slug']) ?>">
            <input type="hidden" name="tour_slug" value="<?= htmlspecialchars($tour['slug']) ?>">
            <input type="hidden" name="position_id" value="<?= (int) $position['id'] ?>">
            <input type="hidden" name="direction" value="<?= $dir ?>">
          </form>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php if ($canEditNavigationArrows): ?>
      <div class="navigation-arrows-modal" id="navigation-arrows-modal" hidden
           role="dialog" aria-modal="true" aria-labelledby="nar-modal-title">
        <div class="navigation-arrows-modal-overlay" id="navigation-arrows-modal-overlay"></div>
        <div class="navigation-arrows-modal-box">
          <p class="navigation-arrows-modal-title" id="nar-modal-title">Colocar flecha</p>
          <p class="navigation-arrows-modal-hint">Haz clic en la imagen donde quieres que aparezca la flecha.</p>
          <div class="navigation-arrows-stage" id="navigation-arrows-stage">
            <img src="<?= htmlspecialchars((string) $panoramaUrl) ?>"
                 alt="Panorámica de esta zona"
                 class="navigation-arrows-image"
                 id="navigation-arrows-image">
            <span class="navigation-arrows-marker" id="navigation-arrows-marker" hidden></span>
          </div>
          <input type="hidden" id="navigation-arrows-target" value="">
          <div class="navigation-arrows-modal-actions">
            <button type="button" class="wizard-btn-submit" id="navigation-arrows-save">
              Guardar flecha
            </button>
            <button type="button" class="db-btn-ghost" id="navigation-arrows-cancel">
              Cancelar
            </button>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<script>
window.OXPHYRE_HOTSPOT_EDITOR = <?= json_encode($hotspotEditorConfig, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;

document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();

  const sidebar = document.getElementById('db-sidebar');
  const overlay = document.getElementById('db-overlay');
  const hamburger = document.getElementById('db-hamburger');
  const closeBtn = document.getElementById('db-sidebar-close');

  const openSidebar = () => {
    sidebar.classList.add('is-open');
    overlay.classList.add('is-visible');
    hamburger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  };
  const closeSidebar = () => {
    sidebar.classList.remove('is-open');
    overlay.classList.remove('is-visible');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };

  hamburger.addEventListener('click', openSidebar);
  closeBtn.addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && sidebar.classList.contains('is-open')) closeSidebar();
  });

  const tipOverlay = document.getElementById('upload-tip-overlay');
  const tipClose = document.getElementById('tip-close');
  const tipUnderstood = document.getElementById('tip-understood');
  const tipNever = document.getElementById('tip-never');
  const reopenTip = document.getElementById('reopen-tip');
  const TIP_KEY = 'oxphyre_room_free_flow_tip_seen';

  function showTip() {
    tipOverlay.style.display = 'flex';
    tipOverlay.removeAttribute('aria-hidden');
    lucide.createIcons();
  }

  function hideTip(remember = false) {
    if (remember) localStorage.setItem(TIP_KEY, '1');
    tipOverlay.style.display = 'none';
    tipOverlay.setAttribute('aria-hidden', 'true');
  }

  if (!localStorage.getItem(TIP_KEY)) showTip();

  tipClose.addEventListener('click', () => hideTip(false));
  tipUnderstood.addEventListener('click', () => hideTip(false));
  tipNever.addEventListener('click', () => hideTip(true));
  reopenTip.addEventListener('click', showTip);

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && tipOverlay.style.display !== 'none') hideTip(false);
  });

  ['N', 'S', 'E', 'O'].forEach(dir => {
    const input = document.getElementById(`input-${dir}`);
    const zone = document.getElementById(`zone-${dir}`);
    const preview = document.getElementById(`preview-${dir}`);
    const img = preview?.querySelector('img');
    const placeholder = preview?.querySelector('.db-upload-preview-placeholder');

    input?.addEventListener('change', () => {
      const file = input.files[0];
      if (!file || !img) return;
      const reader = new FileReader();
      reader.onload = ev => {
        img.src = ev.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        zone.classList.add('has-file');
      };
      reader.readAsDataURL(file);
    });
  });

  const input360 = document.getElementById('input-360');
  const zone360 = document.getElementById('zone-360');
  const img360 = document.getElementById('preview-360');
  const empty360 = zone360?.querySelector('[data-empty-360]');

  input360?.addEventListener('change', () => {
    const file = input360.files[0];
    if (!file || !img360) return;
    const reader = new FileReader();
    reader.onload = ev => {
      img360.src = ev.target.result;
      img360.style.display = 'block';
      if (empty360) empty360.style.display = 'none';
      zone360.classList.add('has-file');
    };
    reader.readAsDataURL(file);
  });

  const form = document.getElementById('upload-form');
  const btnSubmit = document.getElementById('btn-submit-upload');

  form.addEventListener('submit', () => {
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Procesando con IA...';
  });

  document.querySelectorAll('.js-delete-photo-form').forEach(deleteForm => {
    deleteForm.addEventListener('submit', event => {
      const direction = deleteForm.querySelector('[name="direction"]')?.value || '';
      const photoLabels = {
        '360': 'la panorámica principal',
        N: 'la foto detalle 1',
        S: 'la foto detalle 2',
        E: 'la foto detalle 3',
        O: 'la foto detalle 4',
      };
      const label = photoLabels[direction] || 'esta foto';
      if (!confirm(`¿Eliminar ${label}?`)) {
        event.preventDefault();
      }
    });
  });
});
</script>
<script src="<?= asset('/js/hotspot-editor.js') ?>"></script>

</body>
</html>
