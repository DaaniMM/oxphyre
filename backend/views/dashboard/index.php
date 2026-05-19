<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Oxphyre</title>
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

    <nav class="db-nav" aria-label="Secciones del dashboard">
      <a href="/dashboard"               class="db-nav-item active" aria-current="page">
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
    <h1 class="db-topbar-title">Inicio</h1>
    <div class="db-avatar" aria-label="Usuario: <?= $userName ?>" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido principal ── -->
  <main class="db-main" id="db-main">
    <div class="db-page">

      <!-- Saludo -->
      <div class="db-welcome">
        <h2 class="db-welcome-heading">Hola, <em><?= $userName ?>.</em></h2>
        <p class="db-welcome-sub">Aquí tienes un resumen de tu actividad en Oxphyre.</p>
      </div>

      <!-- Métricas reales desde BD -->
      <div class="db-metrics" role="region" aria-label="Métricas">

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="play-circle" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['tours'] ?></div>
          <div class="db-metric-label">Tours activos</div>
          <div class="db-metric-note">
            <?= $planLabel === 'Free' ? 'Plan Free · máx 1' : ($planLabel === 'Pro' ? 'Plan Pro · tours ilimitados' : 'Business · ilimitados') ?>
          </div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="building-2" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['businesses'] ?></div>
          <div class="db-metric-label">Negocios</div>
          <div class="db-metric-note">
            <?= $planLabel === 'Free' ? 'Plan Free · máx 1' : ($planLabel === 'Pro' ? 'Plan Pro · máx 5' : 'Business · ilimitados') ?>
          </div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="scan-line" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['qr_scans'] ?></div>
          <div class="db-metric-label">Escaneos QR</div>
          <div class="db-metric-note">Últimos 30 días</div>
        </div>

      </div>

      <!-- CTA si no tiene tours -->
      <?php if ((int) $stats['tours'] === 0): ?>
        <div class="db-cta-card" role="region" aria-label="Crear primer tour">
          <div class="db-cta-card-icon" aria-hidden="true">
            <i data-lucide="plus-circle" width="32" height="32"></i>
          </div>
          <h3>Crea tu primer tour</h3>
          <p>Fotografía tu negocio, sube las fotos y en minutos tendrás un tour virtual 3D listo para compartir con un QR.</p>
          <button type="button" class="db-btn-primary" id="btn-start-tour"
            data-at-limit="<?= $atBusinessLimit ? '1' : '0' ?>">Empezar ahora →</button>
        </div>
      <?php endif; ?>

      <!-- Modal: límite de negocios alcanzado (siempre en el DOM, JS lo muestra) -->
      <div class="db-modal-overlay" id="limit-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="limit-modal-title">
        <div class="db-modal">
          <button class="db-modal-close" id="btn-limit-close" aria-label="Cerrar">
            <i data-lucide="x" width="18" height="18" aria-hidden="true"></i>
          </button>
          <div class="db-modal-icon" aria-hidden="true">
            <i data-lucide="lock" width="28" height="28"></i>
          </div>
          <h3 class="db-modal-title" id="limit-modal-title">Límite del plan <?= htmlspecialchars($planLabel) ?></h3>
          <p class="db-modal-body">
            Has alcanzado el límite de tu plan <?= htmlspecialchars($planLabel) ?>
            (<?= (int) $businessLimit ?> negocio<?= $businessLimit === 1 ? '' : 's' ?>).
            Mejora a Pro para crear hasta 5 negocios con tours ilimitados y sin marca de agua.
          </p>
          <div class="db-modal-actions">
            <a href="/precios" class="db-btn-primary">Ver planes →</a>
            <button type="button" class="db-btn-ghost" id="btn-limit-cancel">Cerrar</button>
          </div>
        </div>
      </div>

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

  // ── Modal: límite de negocios ──────────────────────────────────────────────
  const limitModal = document.getElementById('limit-modal');
  const btnStart   = document.getElementById('btn-start-tour');
  const btnClose   = document.getElementById('btn-limit-close');
  const btnCancel  = document.getElementById('btn-limit-cancel');

  const openModal  = () => { limitModal.classList.add('is-visible'); limitModal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; };
  const closeModal = () => { limitModal.classList.remove('is-visible'); limitModal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; };

  if (btnStart) {
    btnStart.addEventListener('click', () => {
      if (btnStart.dataset.atLimit === '1') {
        openModal();
      } else {
        window.location.href = '/dashboard/negocios/nuevo';
      }
    });
  }

  btnClose.addEventListener('click', closeModal);
  btnCancel.addEventListener('click', closeModal);
  limitModal.addEventListener('click', e => { if (e.target === limitModal) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && limitModal.classList.contains('is-visible')) closeModal(); });
});
</script>

</body>
</html>
