<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva posición — <?= htmlspecialchars($tour['title']) ?> — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="alternate icon" href="/favicon.ico">
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
      <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>"
         style="color:var(--ox-text-muted);font-weight:400;"><?= htmlspecialchars($tour['title']) ?></a>
      <span style="margin:0 0.5rem;color:var(--ox-text-dim);">/</span>
      Nueva posición
    </h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido ── -->
  <main class="db-main">
    <div class="db-page">

      <div class="wizard-header">
        <h2 class="wizard-title">Nueva <em>posición.</em></h2>
        <p class="wizard-subtitle">
          Para el tour <strong><?= htmlspecialchars($tour['title']) ?></strong>
          · <?= htmlspecialchars($business['name']) ?>
        </p>
        <p style="display:flex;align-items:flex-start;gap:0.375rem;font-size:0.8125rem;color:var(--ox-text-muted);margin-top:0.625rem;">
          <i data-lucide="info" width="14" height="14" style="flex-shrink:0;margin-top:2px;color:var(--ox-text-dim);" aria-hidden="true"></i>
          Una posición es un punto de tu local desde el que el cliente podrá mirar a su alrededor. Por ejemplo: la entrada, la barra o la terraza.
        </p>
      </div>

      <form action="/dashboard/posicion/store" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <input type="hidden" name="biz_slug"   value="<?= htmlspecialchars($business['slug']) ?>">
        <input type="hidden" name="tour_slug"  value="<?= htmlspecialchars($tour['slug']) ?>">

        <div class="wizard-card">
          <div class="db-form-group">
            <label class="db-form-label" for="pos-name">
              Nombre de la posición<span class="required" aria-hidden="true">*</span>
            </label>
            <input class="db-form-input" type="text" id="pos-name" name="name"
              maxlength="100" placeholder="Ej. Entrada, Sala principal, Terraza"
              autocomplete="off" required>
          </div>
        </div>

        <p style="display:flex;align-items:flex-start;gap:0.5rem;font-size:0.8125rem;color:var(--ox-text-muted);margin-top:1rem;">
          <i data-lucide="info" width="15" height="15" style="flex-shrink:0;margin-top:1px;color:var(--ox-text-dim);" aria-hidden="true"></i>
          Después de crear la posición podrás subir las fotos 360° para cada orientación (Norte, Sur, Este, Oeste).
        </p>

        <div class="wizard-nav">
          <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>"
             class="wizard-btn-back">
            <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
            Cancelar
          </a>
          <button type="submit" class="wizard-btn-submit">
            <i data-lucide="plus" width="16" height="16" aria-hidden="true"></i>
            Crear posición
          </button>
        </div>
      </form>

    </div>
  </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();

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
});
</script>

</body>
</html>
