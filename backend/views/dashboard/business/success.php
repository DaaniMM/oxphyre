<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Negocio creado — Oxphyre</title>
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
      <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
      <a href="/dashboard/admin"         class="db-nav-item">
        <i data-lucide="shield"          width="18" height="18" aria-hidden="true"></i>
        <span>Admin</span>
      </a>
      <?php endif; ?>
    </nav>
    <div class="db-sidebar-footer">
      <div class="db-plan-badge">
        <span class="db-plan-label">Plan</span>
        <span class="db-plan-name"><?= htmlspecialchars($planLabel) ?></span>
        <?php if ($planLabel !== 'Business' && $planLabel !== 'Admin'): ?>
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
    <h1 class="db-topbar-title">Negocio creado</h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido: paso 3 éxito ── -->
  <main class="db-main">
    <div class="db-page">

      <!-- Indicador de pasos (paso 3 activo) -->
      <div class="wizard-header">
        <h2 class="wizard-title">Crea tu <em>negocio.</em></h2>
        <p class="wizard-subtitle">Solo necesitas un par de datos para empezar.</p>

        <div class="wizard-steps" role="list" aria-label="Pasos del wizard">
          <div class="wizard-step is-done" id="step-indicator-1" role="listitem">
            <div class="step-bubble">
              <i data-lucide="check" width="14" height="14" aria-hidden="true"></i>
            </div>
            <span class="step-label">Tu negocio</span>
          </div>
          <div class="wizard-connector is-done"></div>
          <div class="wizard-step is-done" id="step-indicator-2" role="listitem">
            <div class="step-bubble">
              <i data-lucide="check" width="14" height="14" aria-hidden="true"></i>
            </div>
            <span class="step-label">Tu plan</span>
          </div>
          <div class="wizard-connector is-done"></div>
          <div class="wizard-step is-active" id="step-indicator-3" role="listitem">
            <div class="step-bubble">3</div>
            <span class="step-label">Listo</span>
          </div>
        </div>
      </div>

      <!-- Card de éxito -->
      <div class="wizard-card">
        <div class="wizard-success">

          <div class="wizard-success-icon" aria-hidden="true">
            <i data-lucide="check" width="32" height="32"></i>
          </div>

          <h3 class="wizard-success-title">
            <em><?= htmlspecialchars($business['name']) ?></em> está listo.
          </h3>

          <p class="wizard-success-meta">Tu negocio ya está creado en Oxphyre.</p>
          <p class="wizard-success-url">oxphyre.com/<?= htmlspecialchars($business['slug']) ?></p>

          <div class="wizard-success-actions">
            <a href="/dashboard/tours/nuevo?negocio=<?= htmlspecialchars($business['slug']) ?>" class="wizard-btn-submit" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;">
              <i data-lucide="plus" width="16" height="16" aria-hidden="true"></i>
              Crear mi primer tour
            </a>
            <a href="/dashboard" class="wizard-btn-back" style="text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;">
              <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
              Volver al dashboard
            </a>
          </div>

        </div>
      </div>

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
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSidebar(); });
});
</script>

</body>
</html>
