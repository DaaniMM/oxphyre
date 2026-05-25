<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis negocios — Oxphyre</title>
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
      <a href="/dashboard/tours"         class="db-nav-item">
        <i data-lucide="play-circle"     width="18" height="18" aria-hidden="true"></i>
        <span>Mis tours</span>
      </a>
      <a href="/dashboard/negocios"      class="db-nav-item active" aria-current="page">
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
    <h1 class="db-topbar-title">Mis negocios</h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido ── -->
  <main class="db-main">
    <div class="db-page">

      <?php
        $planBadgeNames = [PLAN_FREE => 'Free', PLAN_PRO => 'Pro', PLAN_BUSINESS => 'Business'];
      ?>

      <?php if (empty($businesses)): ?>

        <!-- Estado vacío -->
        <div class="db-empty">
          <div class="db-empty-icon" aria-hidden="true">
            <i data-lucide="building-2" width="24" height="24"></i>
          </div>
          <p class="db-empty-title">Aún no tienes ningún negocio.</p>
          <p class="db-empty-sub">Crea tu primer negocio para empezar a construir tours virtuales.</p>
          <a href="/dashboard/negocios/nuevo" class="db-btn-primary">Crear mi primer negocio →</a>
        </div>

      <?php else: ?>

        <!-- Header con CTA -->
        <div class="db-list-header">
          <h2 class="db-list-title">Mis <em>negocios.</em></h2>
          <button type="button" class="db-btn-primary" id="btn-nuevo-negocio"
            data-at-limit="<?= $atBusinessLimit ? '1' : '0' ?>">
            Nuevo negocio →
          </button>
        </div>

        <!-- Grid de negocios -->
        <div class="db-biz-grid">
          <?php foreach ($businesses as $biz): ?>
            <article class="db-biz-card">

              <div class="db-biz-card-top">
                <h3 class="db-biz-card-name"><?= htmlspecialchars($biz['name']) ?></h3>
                <span class="db-badge db-badge--plan">
                  <?= htmlspecialchars($planBadgeNames[$biz['plan_id']] ?? 'Free') ?>
                </span>
              </div>

              <p class="db-biz-card-url">oxphyre.com/<?= htmlspecialchars($biz['slug']) ?></p>

              <?php if (!empty($biz['description'])): ?>
                <p class="db-biz-card-desc"><?= htmlspecialchars($biz['description']) ?></p>
              <?php endif; ?>

              <?php if (!empty($biz['phone']) || !empty($biz['address'])): ?>
                <div class="db-biz-card-meta">
                  <?php if (!empty($biz['phone'])): ?>
                    <span class="db-biz-card-meta-row">
                      <i data-lucide="phone" width="13" height="13" aria-hidden="true"></i>
                      <?= htmlspecialchars($biz['phone']) ?>
                    </span>
                  <?php endif; ?>
                  <?php if (!empty($biz['address'])): ?>
                    <span class="db-biz-card-meta-row">
                      <i data-lucide="map-pin" width="13" height="13" aria-hidden="true"></i>
                      <?= htmlspecialchars($biz['address']) ?>
                    </span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <div class="db-biz-card-actions">
                <a href="/dashboard/negocios/<?= htmlspecialchars($biz['slug']) ?>"
                   class="db-btn-primary" style="font-size:0.8125rem;padding:0.5rem 0.875rem;">
                  Gestionar →
                </a>
                <a href="/dashboard/tours?negocio=<?= htmlspecialchars($biz['slug']) ?>"
                   class="db-btn-secondary">
                  <i data-lucide="play-circle" width="14" height="14" aria-hidden="true"></i>
                  Ver tours
                </a>
              </div>

            </article>
          <?php endforeach; ?>
        </div>

      <?php endif; ?>

      <!-- Modal: límite de negocios (siempre en DOM) -->
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
  const btnNuevo   = document.getElementById('btn-nuevo-negocio');
  const btnClose   = document.getElementById('btn-limit-close');
  const btnCancel  = document.getElementById('btn-limit-cancel');

  const openModal  = () => { limitModal.classList.add('is-visible'); limitModal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; };
  const closeModal = () => { limitModal.classList.remove('is-visible'); limitModal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; };

  if (btnNuevo) {
    btnNuevo.addEventListener('click', () => {
      btnNuevo.dataset.atLimit === '1' ? openModal() : window.location.href = '/dashboard/negocios/nuevo';
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
