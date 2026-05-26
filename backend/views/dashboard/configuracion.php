<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Configuración — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="alternate icon" href="/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <link rel="stylesheet" href="<?= asset('/css/dashboard.css') ?>">
  <style>
    .cfg-card      { background: var(--ox-bg-elevated); border: 1px solid var(--ox-border); border-radius: 14px; padding: 1.5rem; margin-bottom: 1.5rem; }
    .cfg-card-title { font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ox-text-muted); margin-bottom: 1.25rem; }
    .cfg-info-row  { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--ox-text-muted); margin-bottom: 0.5rem; }
    .cfg-info-val  { color: var(--ox-text); font-weight: 500; }
    .cfg-divider   { height: 1px; background: var(--ox-border); margin: 1.25rem 0; }
    .cfg-action-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.875rem 0; border-top: 1px solid var(--ox-border); }
    .cfg-action-row:first-of-type { border-top: none; padding-top: 0; }
    .cfg-action-label { font-size: 0.875rem; color: var(--ox-text); }
    .cfg-action-desc  { font-size: 0.75rem; color: var(--ox-text-dim); margin-top: 2px; }
    .cfg-btn-disabled {
      display: inline-flex; align-items: center; gap: 0.375rem;
      padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.8125rem; font-weight: 500;
      border: 1px solid var(--ox-border); color: var(--ox-text-dim);
      background: transparent; opacity: 0.45; cursor: not-allowed;
    }
    .cfg-zone-danger { border-color: oklch(0.48 0.14 25 / 0.4); }
    .cfg-zone-danger .cfg-card-title { color: oklch(0.65 0.14 25); }
    .cfg-note { font-size: 0.8125rem; color: var(--ox-text-muted); line-height: 1.6; margin-top: 1rem; }
    .cfg-back  { display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.875rem; color: var(--ox-text-dim); text-decoration: none; margin-top: 0.5rem; }
    .cfg-back:hover { color: var(--ox-amber); }
  </style>
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

    <nav class="db-nav" aria-label="Secciones del dashboard">
      <a href="/dashboard"               class="db-nav-item">
        <i data-lucide="home"            width="18" height="18" aria-hidden="true"></i>
        <span>Inicio</span>
      </a>
      <a href="/dashboard/tours"         class="db-nav-item">
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
      <a href="/dashboard/configuracion" class="db-nav-item active" aria-current="page">
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
      <div class="db-plan-badge" aria-label="Plan actual: <?= htmlspecialchars($planLabel) ?>">
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
    <h1 class="db-topbar-title">Configuración</h1>
    <div class="db-avatar" aria-label="Usuario: <?= $userName ?>" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido principal ── -->
  <main class="db-main" id="db-main">
    <div class="db-page">

      <!-- Encabezado de página -->
      <div class="db-welcome">
        <h2 class="db-welcome-heading">Configuración</h2>
        <p class="db-welcome-sub">Resumen de tu cuenta. Las acciones de edición estarán disponibles próximamente.</p>
      </div>

      <!-- Resumen de cuenta -->
      <div class="cfg-card" role="region" aria-label="Resumen de cuenta">
        <p class="cfg-card-title">Tu cuenta</p>
        <div class="cfg-info-row">
          <i data-lucide="user" width="15" height="15" aria-hidden="true"></i>
          <span>Nombre:</span>
          <span class="cfg-info-val"><?= $userName ?></span>
        </div>
        <div class="cfg-info-row">
          <i data-lucide="mail" width="15" height="15" aria-hidden="true"></i>
          <span>Email:</span>
          <span class="cfg-info-val"><?= $userEmail ?></span>
        </div>
        <div class="cfg-info-row">
          <i data-lucide="layers" width="15" height="15" aria-hidden="true"></i>
          <span>Plan:</span>
          <span class="db-badge db-badge--plan" style="margin-left:2px;"><?= htmlspecialchars($planLabel) ?></span>
        </div>
      </div>

      <!-- Perfil -->
      <div class="cfg-card" role="region" aria-label="Perfil">
        <p class="cfg-card-title">Perfil</p>

        <div class="cfg-action-row">
          <div>
            <div class="cfg-action-label">Cambiar nombre</div>
            <div class="cfg-action-desc">Actualiza el nombre que aparece en tu cuenta.</div>
          </div>
          <button type="button" class="cfg-btn-disabled" disabled aria-disabled="true">
            <i data-lucide="pencil" width="14" height="14" aria-hidden="true"></i>
            <span>Editar</span>
            <span class="db-badge db-badge--plan" style="margin-left:4px;font-size:0.7rem;">Próximamente</span>
          </button>
        </div>

        <div class="cfg-action-row">
          <div>
            <div class="cfg-action-label">Cambiar email</div>
            <div class="cfg-action-desc">Requiere confirmación en la dirección actual y en la nueva.</div>
          </div>
          <button type="button" class="cfg-btn-disabled" disabled aria-disabled="true">
            <i data-lucide="pencil" width="14" height="14" aria-hidden="true"></i>
            <span>Editar</span>
            <span class="db-badge db-badge--plan" style="margin-left:4px;font-size:0.7rem;">Próximamente</span>
          </button>
        </div>
      </div>

      <!-- Seguridad -->
      <div class="cfg-card" role="region" aria-label="Seguridad">
        <p class="cfg-card-title">Seguridad</p>

        <div class="cfg-action-row">
          <div>
            <div class="cfg-action-label">Cambiar contraseña</div>
            <div class="cfg-action-desc">Requiere verificar la contraseña actual antes de establecer una nueva.</div>
          </div>
          <button type="button" class="cfg-btn-disabled" disabled aria-disabled="true">
            <i data-lucide="lock" width="14" height="14" aria-hidden="true"></i>
            <span>Cambiar</span>
            <span class="db-badge db-badge--plan" style="margin-left:4px;font-size:0.7rem;">Próximamente</span>
          </button>
        </div>
      </div>

      <!-- Zona sensible -->
      <div class="cfg-card cfg-zone-danger" role="region" aria-label="Zona sensible">
        <p class="cfg-card-title">Zona sensible</p>

        <div class="cfg-action-row">
          <div>
            <div class="cfg-action-label">Eliminar cuenta</div>
            <div class="cfg-action-desc">Borrado permanente de datos. Requiere confirmación explícita y doble verificación.</div>
          </div>
          <button type="button" class="cfg-btn-disabled" disabled aria-disabled="true">
            <i data-lucide="trash-2" width="14" height="14" aria-hidden="true"></i>
            <span>Eliminar</span>
            <span class="db-badge db-badge--plan" style="margin-left:4px;font-size:0.7rem;">Próximamente</span>
          </button>
        </div>
      </div>

      <p class="cfg-note">
        Las acciones de esta página requieren flujos de confirmación adicionales para proteger tu cuenta y tus datos.
        Quedan como evolución segura post-lanzamiento.
      </p>

      <a href="/dashboard" class="cfg-back">
        <i data-lucide="arrow-left" width="15" height="15" aria-hidden="true"></i>
        Volver al dashboard
      </a>

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
});
</script>

</body>
</html>
