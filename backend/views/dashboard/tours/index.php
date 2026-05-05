<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis tours — Oxphyre</title>
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
    <h1 class="db-topbar-title">Mis tours</h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido ── -->
  <main class="db-main">
    <div class="db-page">

      <?php $totalTours = (int) $stats['tours']; ?>

      <?php if (empty($businesses)): ?>

        <!-- Sin negocios -->
        <div class="db-empty">
          <div class="db-empty-icon" aria-hidden="true">
            <i data-lucide="play-circle" width="24" height="24"></i>
          </div>
          <p class="db-empty-title">Aún no tienes ningún tour.</p>
          <p class="db-empty-sub">Empieza creando tu primer negocio para poder añadir tours.</p>
          <a href="/dashboard/negocios" class="db-btn-primary">Empieza con tu primer negocio →</a>
        </div>

      <?php else: ?>

        <!-- Mini-navbar de estadísticas -->
        <div class="db-stat-bar" role="region" aria-label="Estadísticas">
          <div class="db-stat-bar-nums">
            <strong><?= (int) $stats['businesses'] ?></strong>
            <span><?= $stats['businesses'] === 1 ? 'negocio' : 'negocios' ?></span>
            <span class="db-stat-bar-sep">·</span>
            <strong><?= $totalTours ?></strong>
            <span><?= $totalTours === 1 ? 'tour' : 'tours' ?></span>
            <span class="db-stat-bar-sep">·</span>
            <strong><?= (int) $stats['qr_scans'] ?></strong>
            <span>escaneos este mes</span>
          </div>
          <a href="#" class="db-btn-secondary" aria-label="Nuevo tour (próximamente)">
            <i data-lucide="plus" width="14" height="14" aria-hidden="true"></i>
            Nuevo tour
          </a>
        </div>

        <?php if ($totalTours === 0): ?>
          <!-- Tiene negocios pero ningún tour -->
          <div class="db-empty">
            <div class="db-empty-icon" aria-hidden="true">
              <i data-lucide="play-circle" width="24" height="24"></i>
            </div>
            <p class="db-empty-title">Aún no tienes ningún tour.</p>
            <p class="db-empty-sub">Empieza con tu primer negocio →</p>
            <a href="/dashboard/negocios" class="db-btn-primary">Ir a mis negocios</a>
          </div>

        <?php else: ?>

          <!-- Secciones por negocio -->
          <?php foreach ($businesses as $biz): ?>
            <?php if (empty($biz['tours'])) continue; ?>
            <section class="db-tour-section" aria-label="Tours de <?= htmlspecialchars($biz['name']) ?>">

              <div class="db-tour-section-header">
                <span class="db-tour-section-title"><?= htmlspecialchars($biz['name']) ?></span>
                <div class="db-tour-section-hr" aria-hidden="true"></div>
              </div>

              <div class="db-tour-grid">
                <?php foreach ($biz['tours'] as $tour): ?>
                  <article class="db-tour-card">
                    <h3 class="db-tour-card-title"><?= htmlspecialchars($tour['title']) ?></h3>
                    <?php if (!empty($tour['description'])): ?>
                      <p class="db-tour-card-desc"><?= htmlspecialchars($tour['description']) ?></p>
                    <?php endif; ?>
                    <div class="db-tour-card-footer">
                      <span class="db-tour-card-date">
                        <?= date('d/m/Y', strtotime($tour['created_at'])) ?>
                      </span>
                      <span class="db-badge <?= $tour['is_published'] ? 'db-badge--published' : 'db-badge--draft' ?>">
                        <?= $tour['is_published'] ? 'Publicado' : 'Borrador' ?>
                      </span>
                    </div>
                  </article>
                <?php endforeach; ?>
              </div>

            </section>
          <?php endforeach; ?>

          <!-- Negocios sin tours -->
          <?php foreach ($businesses as $biz): ?>
            <?php if (!empty($biz['tours'])) continue; ?>
            <section class="db-tour-section" aria-label="<?= htmlspecialchars($biz['name']) ?> sin tours">
              <div class="db-tour-section-header">
                <span class="db-tour-section-title"><?= htmlspecialchars($biz['name']) ?></span>
                <div class="db-tour-section-hr" aria-hidden="true"></div>
              </div>
              <p style="font-size:0.8125rem;color:var(--ox-text-dim);padding-left:0.25rem;">
                Sin tours aún.
                <a href="#" style="color:var(--ox-amber);margin-left:0.5rem;">Crear tour →</a>
              </p>
            </section>
          <?php endforeach; ?>

        <?php endif; ?>

      <?php endif; ?>

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
