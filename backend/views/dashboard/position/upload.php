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
      <div class="db-manage-header" style="margin-bottom:1.5rem;">
        <div class="db-manage-header-left">
          <div class="db-manage-header-top">
            <h2 class="db-manage-name"><?= htmlspecialchars($position['name']) ?></h2>
            <span class="db-badge db-badge--draft">Posición #<?= (int) $position['order_index'] ?></span>
          </div>
          <p class="db-manage-desc" style="margin-top:0.25rem;">
            Sube las fotos de cada orientación de tu local (imagen normal o 360°).
          </p>
        </div>
      </div>

      <!-- Mensaje informativo sobre el tiempo de procesado -->
      <div style="display:flex;align-items:flex-start;gap:0.5rem;font-size:0.8125rem;color:var(--ox-text-muted);
                  background:var(--ox-bg-elevated);border:1px solid var(--ox-border);border-radius:10px;
                  padding:0.875rem 1rem;margin-bottom:1.5rem;">
        <i data-lucide="cpu" width="16" height="16" style="flex-shrink:0;margin-top:1px;color:var(--ox-amber);" aria-hidden="true"></i>
        <span>
          El procesado con IA puede tardar <strong>30-60 segundos por foto</strong>.
          Puedes rellenar los datos de la posición mientras espera.
          Las fotos sin procesar se guardan igualmente y pueden reprocesarse después.
        </span>
      </div>

      <!-- Formulario de subida — enctype obligatorio para archivos -->
      <form action="/dashboard/posicion/upload" method="POST"
        enctype="multipart/form-data" id="upload-form" novalidate>
        <input type="hidden" name="csrf_token"   value="<?= $csrfToken ?>">
        <input type="hidden" name="biz_slug"     value="<?= htmlspecialchars($business['slug']) ?>">
        <input type="hidden" name="tour_slug"    value="<?= htmlspecialchars($tour['slug']) ?>">
        <input type="hidden" name="position_id"  value="<?= (int) $position['id'] ?>">

        <?php
          // Mapa de orientaciones: clave BD → etiqueta visible para el usuario
          // Las claves (N/S/E/O) se guardan en BD; los nombres son solo UI
          $orientations = [
            'N' => ['label' => 'Frente',    'name' => 'Frente'],
            'S' => ['label' => 'Fondo',     'name' => 'Fondo'],
            'E' => ['label' => 'Izquierda', 'name' => 'Izquierda'],
            'O' => ['label' => 'Derecha',   'name' => 'Derecha'],
          ];
        ?>

        <!-- Grid 2x2 de zonas de subida -->
        <div class="db-upload-grid">
          <?php foreach ($orientations as $dir => $info): ?>
            <?php $existing = $photosByDir[$dir] ?? null; ?>
            <div class="db-upload-zone <?= $existing ? 'has-file' : '' ?>"
              id="zone-<?= $dir ?>" role="region" aria-label="Foto <?= $info['name'] ?>">

              <div style="display:flex;align-items:center;justify-content:space-between;">
                <span class="db-upload-zone-dir"><?= $info['label'] ?></span>
                <span class="db-upload-zone-name"><?= $info['name'] ?></span>
                <?php if ($existing && $existing['processed']): ?>
                  <span class="db-badge db-badge--published" style="font-size:9px;">IA ✓</span>
                <?php elseif ($existing): ?>
                  <span class="db-badge db-badge--draft" style="font-size:9px;">Sin IA</span>
                <?php endif; ?>
              </div>

              <!-- Área de previsualización — muestra la foto existente o un placeholder -->
              <div class="db-upload-preview" id="preview-<?= $dir ?>">
                <?php if ($existing): ?>
                  <img src="/uploads/<?= (int) $position['id'] ?>/<?= htmlspecialchars($existing['filename']) ?>"
                    alt="Foto <?= $info['name'] ?>"
                    style="display:block;width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                  <img alt="" style="display:none;width:100%;height:100%;object-fit:cover;">
                  <div class="db-upload-preview-placeholder">
                    <i data-lucide="image" width="24" height="24" aria-hidden="true"></i>
                    <span>Sin foto</span>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Input oculto + botón de selección visible -->
              <input type="file" name="photo_<?= $dir ?>" id="input-<?= $dir ?>"
                class="db-upload-input" accept="image/jpeg,image/png,image/webp"
                aria-label="Seleccionar foto <?= $info['name'] ?>">
              <button type="button" class="db-upload-btn" onclick="document.getElementById('input-<?= $dir ?>').click()">
                <i data-lucide="upload" width="14" height="14" aria-hidden="true"></i>
                <?= $existing ? 'Cambiar foto' : 'Seleccionar foto' ?>
              </button>

            </div>
          <?php endforeach; ?>
        </div>

        <div class="wizard-nav" style="margin-top:1.5rem;">
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

  // ── Preview de imágenes seleccionadas ──────────────────────────────────────
  // Para cada input de archivo, cuando cambia mostramos la imagen con FileReader
  ['N', 'S', 'E', 'O'].forEach(dir => {
    const input   = document.getElementById(`input-${dir}`);
    const zone    = document.getElementById(`zone-${dir}`);
    const preview = document.getElementById(`preview-${dir}`);
    const img     = preview.querySelector('img');
    const placeholder = preview.querySelector('.db-upload-preview-placeholder');

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = e => {
        img.src           = e.target.result;
        img.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        zone.classList.add('has-file');
      };
      reader.readAsDataURL(file);
    });
  });

  // ── Feedback visual al enviar el formulario ────────────────────────────────
  // El procesado MiDaS tarda hasta 60s — indicamos al usuario que está trabajando
  const form       = document.getElementById('upload-form');
  const btnSubmit  = document.getElementById('btn-submit-upload');

  form.addEventListener('submit', () => {
    btnSubmit.disabled    = true;
    btnSubmit.textContent = 'Procesando con IA...';
  });
});
</script>

</body>
</html>
