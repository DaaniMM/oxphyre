<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nuevo tour — <?= htmlspecialchars($business['name']) ?> — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <link rel="stylesheet" href="<?= asset('/css/dashboard.css') ?>">
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
      Nuevo tour
    </h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido ── -->
  <main class="db-main">
    <div class="db-page">

      <?php if ($flash && $flash['type'] === 'error'): ?>
        <div role="alert" style="background:oklch(0.35 0.12 25/0.2);border:1px solid oklch(0.55 0.15 25/0.4);color:oklch(0.80 0.10 25);padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.5rem;">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <div class="wizard-header">
        <h2 class="wizard-title">Nuevo <em>tour.</em></h2>
        <p class="wizard-subtitle">Para <strong><?= htmlspecialchars($business['name']) ?></strong> · oxphyre.com/<?= htmlspecialchars($business['slug']) ?>/…</p>
      </div>

      <form action="/dashboard/tours/store" method="POST" novalidate id="tour-form">
        <input type="hidden" name="csrf_token"    value="<?= $csrfToken ?>">
        <input type="hidden" name="business_slug" value="<?= htmlspecialchars($business['slug']) ?>">

        <div class="wizard-card">

          <div class="db-form-group">
            <label class="db-form-label" for="tour-title">
              Título del tour<span class="required" aria-hidden="true">*</span>
            </label>
            <input class="db-form-input" type="text" id="tour-title" name="title"
              maxlength="100" placeholder="Ej. Tour virtual del local"
              autocomplete="off" required>
            <div class="char-counter" id="title-counter" aria-live="polite">0 / 100</div>
            <span class="db-form-error" id="title-error" aria-live="polite"></span>
          </div>

          <div class="db-form-group">
            <label class="db-form-label" for="tour-slug">URL del tour</label>
            <div class="slug-row">
              <span class="slug-prefix">oxphyre.com/<?= htmlspecialchars($business['slug']) ?>/</span>
              <input class="db-form-input" type="text" id="tour-slug" name="slug"
                maxlength="80" placeholder="tour-virtual"
                pattern="[a-z0-9][a-z0-9\-]*"
                aria-describedby="tour-slug-hint">
            </div>
            <span id="tour-slug-hint" style="font-size:11px;color:var(--ox-text-dim);margin-top:2px;">
              Se genera automáticamente desde el título. Solo letras minúsculas, números y guiones.
            </span>
          </div>

          <div class="db-form-group" style="margin-bottom:0;">
            <label class="db-form-label" for="tour-desc">Descripción</label>
            <textarea class="db-form-textarea" id="tour-desc" name="description"
              maxlength="500" rows="3"
              placeholder="Describe brevemente este tour virtual..."></textarea>
            <div class="char-counter" id="desc-counter" aria-live="polite">0 / 500</div>
          </div>

        </div>

        <p style="display:flex;align-items:flex-start;gap:0.5rem;font-size:0.8125rem;color:var(--ox-text-muted);margin-top:1rem;">
          <i data-lucide="info" width="15" height="15" style="flex-shrink:0;margin-top:1px;color:var(--ox-text-dim);" aria-hidden="true"></i>
          Una vez creado el tour podrás añadir posiciones, subir fotos 360°, configurar hotspots y mucho más.
        </p>

        <div class="wizard-nav">
          <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>"
             class="wizard-btn-back">
            <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
            Cancelar
          </a>
          <button type="submit" class="wizard-btn-submit">
            <i data-lucide="plus" width="16" height="16" aria-hidden="true"></i>
            Crear tour
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

  // ── Formulario ────────────────────────────────────────────────────────────
  const titleInput = document.getElementById('tour-title');
  const slugInput  = document.getElementById('tour-slug');
  const descInput  = document.getElementById('tour-desc');
  const titleCtr   = document.getElementById('title-counter');
  const descCtr    = document.getElementById('desc-counter');
  const titleError = document.getElementById('title-error');

  let slugManuallyEdited = false;

  function slugify(str) {
    return str
      .toLowerCase()
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 80);
  }

  titleInput.addEventListener('input', () => {
    titleCtr.textContent = `${titleInput.value.length} / 100`;
    if (!slugManuallyEdited) slugInput.value = slugify(titleInput.value);
    if (titleInput.value.trim()) { titleInput.classList.remove('is-error'); titleError.textContent = ''; }
  });

  slugInput.addEventListener('input', () => {
    slugManuallyEdited = true;
    slugInput.value = slugInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
  });

  descInput.addEventListener('input', () => {
    descCtr.textContent = `${descInput.value.length} / 500`;
  });

  document.getElementById('tour-form').addEventListener('submit', e => {
    if (!titleInput.value.trim()) {
      e.preventDefault();
      titleInput.classList.add('is-error');
      titleError.textContent = 'El título es obligatorio.';
      titleInput.focus();
    }
  });
});
</script>

</body>
</html>
