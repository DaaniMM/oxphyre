<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($business['name']) ?> — Oxphyre</title>
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
      <?= htmlspecialchars($business['name']) ?>
    </h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido ── -->
  <main class="db-main">
    <div class="db-page">

      <?php if ($flash): ?>
        <div role="alert" style="
          padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.25rem;
          <?= $flash['type'] === 'success'
            ? 'background:oklch(0.35 0.10 145/0.2);border:1px solid oklch(0.55 0.12 145/0.4);color:oklch(0.80 0.14 145);'
            : 'background:oklch(0.35 0.12 25/0.2);border:1px solid oklch(0.55 0.15 25/0.4);color:oklch(0.80 0.10 25);'
          ?>"><?= htmlspecialchars($flash['message']) ?></div>
      <?php endif; ?>

      <?php $planBadgeNames = [PLAN_FREE => 'Free', PLAN_PRO => 'Pro', PLAN_BUSINESS => 'Business']; ?>

      <!-- ── PANEL SUPERIOR: info del negocio ── -->
      <div class="db-manage-header" id="info-view">
        <div class="db-manage-header-left">

          <div class="db-manage-header-top">
            <h2 class="db-manage-name"><?= htmlspecialchars($business['name']) ?></h2>
            <span class="db-badge db-badge--plan">
              <?= htmlspecialchars($planBadgeNames[$business['plan_id']] ?? 'Free') ?>
            </span>
          </div>

          <div class="db-manage-url-row">
            <span class="db-manage-url">oxphyre.com/<?= htmlspecialchars($business['slug']) ?></span>
            <button type="button" class="db-manage-copy-btn" id="btn-copy-url"
              data-url="https://oxphyre.com/<?= htmlspecialchars($business['slug']) ?>"
              aria-label="Copiar URL">
              <i data-lucide="copy" width="13" height="13" aria-hidden="true"></i>
            </button>
          </div>

          <?php if (!empty($business['description'])): ?>
            <p class="db-manage-desc"><?= htmlspecialchars($business['description']) ?></p>
          <?php endif; ?>

          <?php if (!empty($business['phone']) || !empty($business['address'])): ?>
            <div class="db-manage-meta">
              <?php if (!empty($business['phone'])): ?>
                <span class="db-manage-meta-row">
                  <i data-lucide="phone" width="13" height="13" aria-hidden="true"></i>
                  <?= htmlspecialchars($business['phone']) ?>
                </span>
              <?php endif; ?>
              <?php if (!empty($business['address'])): ?>
                <span class="db-manage-meta-row">
                  <i data-lucide="map-pin" width="13" height="13" aria-hidden="true"></i>
                  <?= htmlspecialchars($business['address']) ?>
                </span>
              <?php endif; ?>
            </div>
          <?php endif; ?>

        </div>

        <div class="db-manage-header-right">
          <button type="button" class="db-btn-secondary" id="btn-edit">
            <i data-lucide="pencil" width="14" height="14" aria-hidden="true"></i>
            Editar
          </button>
        </div>
      </div>

      <!-- ── FORMULARIO DE EDICIÓN (full-width, oculto por defecto) ── -->
      <div class="db-manage-card" id="edit-wrapper" hidden>
        <form id="edit-form"
          action="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/edit"
          method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

          <div class="db-manage-edit-grid">
            <div class="db-form-group db-manage-edit-full">
              <label class="db-form-label" for="edit-name">
                Nombre<span class="required" aria-hidden="true">*</span>
              </label>
              <input class="db-form-input" type="text" id="edit-name" name="name"
                maxlength="100" value="<?= htmlspecialchars($business['name']) ?>" required>
            </div>

            <div class="db-form-group db-manage-edit-full">
              <label class="db-form-label" for="edit-desc">Descripción</label>
              <textarea class="db-form-textarea" id="edit-desc" name="description"
                maxlength="300" rows="2"><?= htmlspecialchars($business['description'] ?? '') ?></textarea>
            </div>

            <div class="db-form-group" style="margin-bottom:0;">
              <label class="db-form-label" for="edit-phone">Teléfono</label>
              <input class="db-form-input" type="tel" id="edit-phone" name="phone"
                maxlength="20" value="<?= htmlspecialchars($business['phone'] ?? '') ?>">
            </div>

            <div class="db-form-group" style="margin-bottom:0;">
              <label class="db-form-label" for="edit-address">Dirección</label>
              <input class="db-form-input" type="text" id="edit-address" name="address"
                maxlength="200" value="<?= htmlspecialchars($business['address'] ?? '') ?>">
            </div>
          </div>

          <div class="db-manage-divider"></div>

          <div class="db-manage-actions">
            <button type="submit" class="db-btn-primary" style="font-size:0.8125rem;padding:0.5rem 0.875rem;">
              <i data-lucide="check" width="14" height="14" aria-hidden="true"></i>
              Guardar cambios
            </button>
            <button type="button" class="db-btn-secondary" id="btn-cancel-edit">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- ── SECCIÓN TOURS (full-width) ── -->
      <section class="db-manage-tours-section" aria-label="Tours de este negocio">

        <div class="db-manage-tours-header">
          <span class="db-manage-tours-title">Tours</span>
          <a href="/dashboard/tours/nuevo?negocio=<?= htmlspecialchars($business['slug']) ?>"
             class="db-btn-secondary" style="font-size:0.8125rem;">
            <i data-lucide="plus" width="14" height="14" aria-hidden="true"></i>
            Nuevo tour
          </a>
        </div>

        <?php if (empty($tours)): ?>
          <div class="db-empty" style="padding:2.5rem 1rem;">
            <div class="db-empty-icon" aria-hidden="true">
              <i data-lucide="play-circle" width="24" height="24"></i>
            </div>
            <p class="db-empty-title">Este negocio aún no tiene tours.</p>
            <p class="db-empty-sub">Crea el primer tour para empezar a recibir visitas virtuales.</p>
            <a href="/dashboard/tours/nuevo?negocio=<?= htmlspecialchars($business['slug']) ?>"
               class="db-btn-primary">Crear primer tour →</a>
          </div>

        <?php else: ?>
          <div class="db-tour-grid">
            <?php foreach ($tours as $tour): ?>
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
                <div style="margin-top:0.75rem;">
                  <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>"
                     class="db-btn-secondary" style="font-size:0.8125rem;width:100%;justify-content:center;">
                    Gestionar
                  </a>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </section>

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

  // ── Toggle formulario de edición ──────────────────────────────────────────
  const infoView   = document.getElementById('info-view');
  const editWrapper = document.getElementById('edit-wrapper');
  const btnEdit    = document.getElementById('btn-edit');
  const btnCancel  = document.getElementById('btn-cancel-edit');

  btnEdit.addEventListener('click', () => {
    infoView.hidden = true;
    editWrapper.hidden = false;
    document.getElementById('edit-name').focus();
  });

  btnCancel.addEventListener('click', () => {
    editWrapper.hidden = true;
    infoView.hidden = false;
  });

  // ── Copiar URL ────────────────────────────────────────────────────────────
  const copyBtn = document.getElementById('btn-copy-url');
  if (copyBtn && navigator.clipboard) {
    copyBtn.addEventListener('click', () => {
      navigator.clipboard.writeText(copyBtn.dataset.url).then(() => {
        copyBtn.classList.add('copied');
        copyBtn.innerHTML = '<i data-lucide="check" width="13" height="13"></i>';
        lucide.createIcons();
        setTimeout(() => {
          copyBtn.classList.remove('copied');
          copyBtn.innerHTML = '<i data-lucide="copy" width="13" height="13"></i>';
          lucide.createIcons();
        }, 2000);
      });
    });
  }
});
</script>

</body>
</html>
