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
  <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>

<!-- Modal de ayuda — aparece la primera vez (controlado por localStorage) -->
<div class="upload-tip-overlay" id="upload-tip-overlay" role="dialog"
     aria-modal="true" aria-labelledby="tip-title" style="display:none;">
  <div class="upload-tip-modal">
    <button class="upload-tip-close" id="tip-close" aria-label="Cerrar ayuda">
      <i data-lucide="x" width="16" height="16" aria-hidden="true"></i>
    </button>

    <h2 class="upload-tip-title" id="tip-title">¿Cómo funciona la subida de fotos?</h2>

    <div class="upload-tip-cols">
      <div class="upload-tip-col">
        <h4>Opción 1 — 4 Fotos</h4>
        <p style="margin-bottom:0.5rem;">Haz 4 fotos en el mismo punto de tu local, girando sobre ti mismo:</p>
        <ol>
          <li>Apunta hacia la entrada <strong>(Frente)</strong></li>
          <li>Gira 180° y apunta al fondo <strong>(Fondo)</strong></li>
          <li>Apunta a tu derecha <strong>(Derecha)</strong></li>
          <li>Apunta a tu izquierda <strong>(Izquierda)</strong></li>
        </ol>
        <p style="margin-top:0.5rem;color:var(--ox-text-dim);">
          <i data-lucide="lightbulb" width="12" height="12" style="vertical-align:middle;"></i>
          No te muevas del sitio, solo gira.
        </p>
      </div>
      <div class="upload-tip-col">
        <h4>Opción 2 — Panorámica 360°</h4>
        <p>Con tu móvil en horizontal, usa el <strong>modo Panorama</strong> de la cámara.</p>
        <p style="margin-top:0.5rem;">Gira lentamente sobre ti mismo de izquierda a derecha hasta completar el giro.</p>
        <p style="margin-top:0.5rem;color:var(--ox-text-dim);">
          <i data-lucide="lightbulb" width="12" height="12" style="vertical-align:middle;"></i>
          La foto debe ser muy ancha (proporción 2:1 o más). Gira despacio y mantén el móvil nivelado.
        </p>
      </div>
    </div>

    <p class="upload-tip-footer">
      <i data-lucide="info" width="14" height="14" style="flex-shrink:0;color:var(--ox-amber);" aria-hidden="true"></i>
      Puedes subir ambas opciones y elegir cuál usa el visor con el botón "Usar en el visor".
    </p>
  </div>
</div>

<div class="db-overlay" id="db-overlay" aria-hidden="true"></div>

<div class="db-layout">

  <!-- ── Sidebar ── -->
  <aside class="db-sidebar" id="db-sidebar" role="navigation" aria-label="Navegación principal">
    <div class="db-sidebar-header">
      <a href="/" class="db-logo" aria-label="Oxphyre inicio">◉ Oxphyre</a>
      <button class="db-sidebar-close" id="db-sidebar-close" aria-label="Cerrar menú">
        <i data-lucide="x" width="18" height="18"></i>
      </button>
    </div>
    <nav class="db-nav">
      <a href="/dashboard"               class="db-nav-item">
        <i data-lucide="home"            width="18" height="18" aria-hidden="true"></i>
        <span>Inicio</span>
      </a>
      <a href="/dashboard/tours"         class="db-nav-item active" aria-current="page">
        <i data-lucide="play-circle"     width="18" height="18" aria-hidden="true"></i>
        <span>Mis tours</span>
      </a>
      <a href="/dashboard/negocios"      class="db-nav-item">
        <i data-lucide="building-2"      width="18" height="18" aria-hidden="true"></i>
        <span>Negocios</span>
      </a>
      <a href="/dashboard/analiticas"    class="db-nav-item">
        <i data-lucide="bar-chart-2"     width="18" height="18" aria-hidden="true"></i>
        <span>Analíticas</span>
      </a>
      <a href="/dashboard/configuracion" class="db-nav-item">
        <i data-lucide="settings"        width="18" height="18" aria-hidden="true"></i>
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

  <!-- ── Topbar ── -->
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

  <!-- ── Contenido ── -->
  <main class="db-main">
    <div class="db-page">

      <?php if ($flash): ?>
        <div role="alert" style="padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.25rem;
          <?= $flash['type'] === 'success'
            ? 'background:oklch(0.35 0.10 145/0.2);border:1px solid oklch(0.55 0.12 145/0.4);color:oklch(0.80 0.14 145);'
            : 'background:oklch(0.35 0.12 25/0.2);border:1px solid oklch(0.55 0.15 25/0.4);color:oklch(0.80 0.10 25);'
          ?>">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <!-- Header de la posición -->
      <div class="db-manage-header" style="margin-bottom:1.25rem;">
        <div class="db-manage-header-left">
          <div class="db-manage-header-top">
            <h2 class="db-manage-name"><?= htmlspecialchars($position['name']) ?></h2>
            <span class="db-badge db-badge--draft">Posición #<?= (int) $position['order_index'] ?></span>
          </div>
          <p class="db-manage-desc" style="margin-top:0.25rem;">
            Sube fotos de tu local para este punto del tour.
          </p>
        </div>
      </div>

      <!-- Toggle de modo + botón de ayuda -->
      <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem;">
        <div class="upload-mode-toggle" role="group" aria-label="Tipo de foto">
          <button type="button" class="upload-mode-btn <?= $activeMode === '4photos' ? 'active' : '' ?>"
                  id="toggle-4photos" aria-pressed="<?= $activeMode === '4photos' ? 'true' : 'false' ?>">
            4 Fotos
          </button>
          <button type="button" class="upload-mode-btn <?= $activeMode === 'panoramic' ? 'active' : '' ?>"
                  id="toggle-panoramic" aria-pressed="<?= $activeMode === 'panoramic' ? 'true' : 'false' ?>">
            Panorámica 360°
          </button>
        </div>
        <button type="button" class="db-help-icon" id="reopen-tip" aria-label="Cómo hacer las fotos">
          <i data-lucide="circle-help" width="16" height="16" aria-hidden="true"></i>
          <span class="db-help-tooltip">Ver instrucciones de subida</span>
        </button>
      </div>

      <!-- Aviso de tiempo de procesado -->
      <div style="display:flex;align-items:flex-start;gap:0.5rem;font-size:0.8125rem;color:var(--ox-text-muted);
                  background:var(--ox-bg-elevated);border:1px solid var(--ox-border);border-radius:10px;
                  padding:0.875rem 1rem;margin-bottom:1.5rem;">
        <i data-lucide="cpu" width="16" height="16" style="flex-shrink:0;margin-top:1px;color:var(--ox-amber);" aria-hidden="true"></i>
        <span>El procesado con IA puede tardar <strong>30–60 segundos por foto</strong>. Las fotos sin procesar se guardan igualmente.</span>
      </div>

      <!-- Formulario único con todos los campos de subida -->
      <form action="/dashboard/posicion/upload" method="POST"
            enctype="multipart/form-data" id="upload-form" novalidate>
        <input type="hidden" name="csrf_token"   value="<?= $csrfToken ?>">
        <input type="hidden" name="biz_slug"     value="<?= htmlspecialchars($business['slug']) ?>">
        <input type="hidden" name="tour_slug"    value="<?= htmlspecialchars($tour['slug']) ?>">
        <input type="hidden" name="position_id"  value="<?= (int) $position['id'] ?>">

        <!-- ── SECCIÓN 4 FOTOS ─────────────────────────────────────────── -->
        <div class="upload-section <?= $activeMode === '4photos' ? 'active' : '' ?>" id="section-4photos">

          <?php
            // Mapa de orientaciones: clave BD → etiqueta visible
            $orientations = [
              'N' => 'Frente',
              'S' => 'Fondo',
              'E' => 'Izquierda',
              'O' => 'Derecha',
            ];
          ?>

          <div class="db-upload-grid">
            <?php foreach ($orientations as $dir => $label): ?>
              <?php $existing = $photosByDir[$dir] ?? null; ?>
              <div class="db-upload-zone <?= $existing ? 'has-file' : '' ?>"
                   id="zone-<?= $dir ?>" role="region" aria-label="Foto <?= $label ?>">

                <div style="display:flex;align-items:center;justify-content:space-between;">
                  <span class="db-upload-zone-dir"><?= $label ?></span>
                  <?php if ($existing && $existing['processed']): ?>
                    <span class="db-badge db-badge--published" style="font-size:9px;">IA ✓</span>
                  <?php elseif ($existing): ?>
                    <span class="db-badge db-badge--draft" style="font-size:9px;">Sin IA</span>
                  <?php endif; ?>
                </div>

                <div class="db-upload-preview" id="preview-<?= $dir ?>">
                  <?php if ($existing): ?>
                    <img src="/uploads/<?= (int) $position['id'] ?>/<?= htmlspecialchars($existing['filename']) ?>"
                         alt="Foto <?= $label ?>"
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
                       class="db-upload-input" accept="image/jpeg,image/png,image/webp"
                       aria-label="Seleccionar foto <?= $label ?>">
                <button type="button" class="db-upload-btn"
                        onclick="document.getElementById('input-<?= $dir ?>').click()">
                  <i data-lucide="upload" width="14" height="14" aria-hidden="true"></i>
                  <?= $existing ? 'Cambiar foto' : 'Seleccionar foto' ?>
                </button>

              </div>
            <?php endforeach; ?>
          </div>

          <!-- Botón AJAX para activar modo 4 fotos en el visor -->
          <button type="button" class="btn-set-active <?= $activeMode === '4photos' ? 'is-active' : '' ?>"
                  id="btn-use-4" aria-pressed="<?= $activeMode === '4photos' ? 'true' : 'false' ?>">
            <i data-lucide="<?= $activeMode === '4photos' ? 'check-circle' : 'circle' ?>"
               width="16" height="16" aria-hidden="true"></i>
            <?= $activeMode === '4photos' ? '✓ Usando estas fotos en el visor' : 'Usar estas fotos en el visor' ?>
          </button>

        </div><!-- /section-4photos -->

        <!-- ── SECCIÓN PANORÁMICA 360° ─────────────────────────────────── -->
        <div class="upload-section <?= $activeMode === 'panoramic' ? 'active' : '' ?>" id="section-panoramic">

          <p style="font-size:0.8125rem;color:var(--ox-text-muted);margin-bottom:1rem;">
            Foto panorámica equirectangular (proporción 2:1 o más ancha).
            Obtenida con el modo Panorama del móvil o una cámara 360°.
          </p>

          <div class="db-upload-zone-360 <?= $photo360 ? 'has-file' : '' ?>" id="zone-360"
               role="region" aria-label="Foto panorámica 360°">

            <?php if ($photo360): ?>
              <img src="/uploads/<?= (int) $position['id'] ?>/<?= htmlspecialchars($photo360['filename']) ?>"
                   alt="Foto panorámica" class="db-upload-zone-360-preview" id="preview-360">
              <div style="display:flex;align-items:center;gap:0.5rem;margin-top:0.5rem;">
                <span class="db-upload-zone-dir">Panorámica</span>
                <?php if ($photo360['processed']): ?>
                  <span class="db-badge db-badge--published" style="font-size:9px;">IA ✓</span>
                <?php else: ?>
                  <span class="db-badge db-badge--draft" style="font-size:9px;">Sin IA</span>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <img alt="" class="db-upload-zone-360-preview" id="preview-360" style="display:none;">
              <i data-lucide="panorama" width="32" height="32"
                 style="color:var(--ox-text-dim);" aria-hidden="true" id="icon-360"></i>
              <span style="font-size:0.875rem;color:var(--ox-text-muted);">Sin foto panorámica</span>
            <?php endif; ?>

            <input type="file" name="photo_360" id="input-360"
                   class="db-upload-input" accept="image/jpeg,image/png,image/webp"
                   aria-label="Seleccionar foto panorámica 360°">
            <button type="button" class="db-upload-btn" style="margin-top:0.5rem;"
                    onclick="document.getElementById('input-360').click()">
              <i data-lucide="upload" width="14" height="14" aria-hidden="true"></i>
              <?= $photo360 ? 'Cambiar panorámica' : 'Seleccionar panorámica' ?>
            </button>

          </div>

          <!-- Botón AJAX para activar modo panorámica en el visor -->
          <button type="button" class="btn-set-active <?= $activeMode === 'panoramic' ? 'is-active' : '' ?>"
                  id="btn-use-360" aria-pressed="<?= $activeMode === 'panoramic' ? 'true' : 'false' ?>"
                  <?= !$photo360 ? 'disabled title="Sube primero una foto panorámica"' : '' ?>>
            <i data-lucide="<?= $activeMode === 'panoramic' ? 'check-circle' : 'circle' ?>"
               width="16" height="16" aria-hidden="true"></i>
            <?= $activeMode === 'panoramic' ? '✓ Usando panorámica en el visor' : 'Usar panorámica en el visor' ?>
          </button>

        </div><!-- /section-panoramic -->

        <!-- Botones de navegación del formulario -->
        <div class="wizard-nav" style="margin-top:1.75rem;">
          <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>"
             class="wizard-btn-back">
            <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
            Cancelar
          </a>
          <button type="submit" class="wizard-btn-submit" id="btn-submit-upload">
            <i data-lucide="cpu" width="16" height="16" aria-hidden="true"></i>
            Procesar con IA →
          </button>
        </div>

      </form>

    </div>
  </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();

  // ── Sidebar móvil ──────────────────────────────────────────────────────────
  const sidebar   = document.getElementById('db-sidebar');
  const overlay   = document.getElementById('db-overlay');
  const hamburger = document.getElementById('db-hamburger');
  const closeBtn  = document.getElementById('db-sidebar-close');

  const openSidebar  = () => { sidebar.classList.add('is-open'); overlay.classList.add('is-visible'); hamburger.setAttribute('aria-expanded', 'true'); document.body.style.overflow = 'hidden'; };
  const closeSidebar = () => { sidebar.classList.remove('is-open'); overlay.classList.remove('is-visible'); hamburger.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; };

  hamburger.addEventListener('click', openSidebar);
  closeBtn.addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && sidebar.classList.contains('is-open')) closeSidebar(); });

  // ── Modal de ayuda ─────────────────────────────────────────────────────────
  const tipOverlay = document.getElementById('upload-tip-overlay');
  const tipClose   = document.getElementById('tip-close');
  const reopenTip  = document.getElementById('reopen-tip');
  const TIP_KEY    = 'oxphyre_upload_tip_seen';

  function showTip() {
    tipOverlay.style.display = 'flex';
    tipOverlay.removeAttribute('aria-hidden');
    // Re-renderizar iconos dentro del modal por si no existían cuando se ocultó
    lucide.createIcons();
  }
  function hideTip() {
    tipOverlay.style.display = 'none';
    tipOverlay.setAttribute('aria-hidden', 'true');
  }

  // Mostrar la primera vez que el usuario entra a esta vista
  if (!localStorage.getItem(TIP_KEY)) {
    showTip();
  }

  tipClose.addEventListener('click', () => {
    localStorage.setItem(TIP_KEY, '1');
    hideTip();
  });

  // El icono ? reabre el modal manualmente
  reopenTip.addEventListener('click', () => showTip());

  // Cerrar con Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && tipOverlay.style.display !== 'none') hideTip();
  });

  // ── Toggle de secciones (4 Fotos / Panorámica) ────────────────────────────
  const toggle4     = document.getElementById('toggle-4photos');
  const toggle360   = document.getElementById('toggle-panoramic');
  const section4    = document.getElementById('section-4photos');
  const section360  = document.getElementById('section-panoramic');

  function activateSection(mode) {
    const is4 = mode === '4photos';
    toggle4.classList.toggle('active', is4);
    toggle360.classList.toggle('active', !is4);
    toggle4.setAttribute('aria-pressed', is4 ? 'true' : 'false');
    toggle360.setAttribute('aria-pressed', is4 ? 'false' : 'true');
    section4.classList.toggle('active', is4);
    section360.classList.toggle('active', !is4);
  }

  toggle4.addEventListener('click', () => activateSection('4photos'));
  toggle360.addEventListener('click', () => activateSection('panoramic'));

  // ── Preview de imágenes (4 fotos) ─────────────────────────────────────────
  ['N', 'S', 'E', 'O'].forEach(dir => {
    const input       = document.getElementById(`input-${dir}`);
    const zone        = document.getElementById(`zone-${dir}`);
    const preview     = document.getElementById(`preview-${dir}`);
    const img         = preview.querySelector('img');
    const placeholder = preview.querySelector('.db-upload-preview-placeholder');

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = ev => {
        img.src           = ev.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        zone.classList.add('has-file');
      };
      reader.readAsDataURL(file);
    });
  });

  // ── Preview de la panorámica 360° ─────────────────────────────────────────
  const input360 = document.getElementById('input-360');
  const zone360  = document.getElementById('zone-360');
  const img360   = document.getElementById('preview-360');
  const icon360  = document.getElementById('icon-360');

  input360?.addEventListener('change', () => {
    const file = input360.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
      img360.src           = ev.target.result;
      img360.style.display = 'block';
      if (icon360) icon360.style.display = 'none';
      zone360.classList.add('has-file');
      // Habilitar botón "Usar panorámica" si estaba deshabilitado
      document.getElementById('btn-use-360').disabled = false;
    };
    reader.readAsDataURL(file);
  });

  // ── Botones AJAX "Usar en el visor" ───────────────────────────────────────
  const positionId = <?= (int) $position['id'] ?>;
  const bizSlug    = '<?= addslashes($business['slug']) ?>';
  const tourSlug   = '<?= addslashes($tour['slug']) ?>';
  const csrfToken  = document.querySelector('[name=csrf_token]').value;

  async function setActiveMode(mode) {
    try {
      const resp = await fetch('/dashboard/posicion/set-mode', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          csrf_token:  csrfToken,
          biz_slug:    bizSlug,
          tour_slug:   tourSlug,
          position_id: positionId,
          mode,
        }),
      });
      const data = await resp.json();
      if (data.success) updateActiveModeUI(mode);
    } catch (err) {
      console.warn('[upload] Error al cambiar modo:', err);
    }
  }

  function updateActiveModeUI(activeMode) {
    const btn4   = document.getElementById('btn-use-4');
    const btn360 = document.getElementById('btn-use-360');
    const is4    = activeMode === '4photos';

    btn4.classList.toggle('is-active', is4);
    btn4.setAttribute('aria-pressed', is4 ? 'true' : 'false');
    btn4.innerHTML = `<i data-lucide="${is4 ? 'check-circle' : 'circle'}" width="16" height="16" aria-hidden="true"></i> ${is4 ? '✓ Usando estas fotos en el visor' : 'Usar estas fotos en el visor'}`;

    btn360.classList.toggle('is-active', !is4);
    btn360.setAttribute('aria-pressed', is4 ? 'false' : 'true');
    btn360.innerHTML = `<i data-lucide="${!is4 ? 'check-circle' : 'circle'}" width="16" height="16" aria-hidden="true"></i> ${!is4 ? '✓ Usando panorámica en el visor' : 'Usar panorámica en el visor'}`;

    lucide.createIcons();
  }

  document.getElementById('btn-use-4')  ?.addEventListener('click', () => setActiveMode('4photos'));
  document.getElementById('btn-use-360')?.addEventListener('click', () => setActiveMode('panoramic'));

  // ── Deshabilitar submit durante el procesado ───────────────────────────────
  const form      = document.getElementById('upload-form');
  const btnSubmit = document.getElementById('btn-submit-upload');

  form.addEventListener('submit', () => {
    btnSubmit.disabled    = true;
    btnSubmit.textContent = 'Procesando con IA...';
  });
});
</script>

</body>
</html>
