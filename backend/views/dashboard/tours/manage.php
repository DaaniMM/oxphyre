<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($tour['title']) ?> — Oxphyre</title>
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
      <?= htmlspecialchars($tour['title']) ?>
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

      <?php if (!empty($arrowsNeedReviewByPosition)): ?>
        <?php $reviewCount = count($arrowsNeedReviewByPosition); ?>
        <div role="alert" class="navigation-review-alert">
          <i data-lucide="triangle-alert" width="18" height="18"
             class="navigation-review-alert__icon" aria-hidden="true"></i>
          <div class="navigation-review-alert__content">
            <p class="navigation-review-alert__title">
              Hay flechas de navegación pendientes de revisar
            </p>
            <?php if ($reviewCount === 1): ?>
              <p class="navigation-review-alert__text">
                Has cambiado la panorámica de «<?= htmlspecialchars($arrowsNeedReviewByPosition[0]['positionName']) ?>». Algunas flechas de esa zona no aparecerán en el tour hasta que las recoloques.
              </p>
              <a href="/dashboard/posicion/upload?position=<?= (int) $arrowsNeedReviewByPosition[0]['positionId'] ?>&negocio=<?= htmlspecialchars($business['slug']) ?>&tour=<?= htmlspecialchars($tour['slug']) ?>#navigation-arrows-panel"
                 class="wizard-btn-submit navigation-review-alert__action">
                <i data-lucide="navigation" width="14" height="14" aria-hidden="true"></i>
                Revisar flechas
              </a>
            <?php else: ?>
              <p class="navigation-review-alert__text">
                Hay flechas pendientes en varias zonas. Revísalas para que vuelvan a aparecer en el tour.
              </p>
              <ul class="navigation-review-alert__list">
                <?php foreach ($arrowsNeedReviewByPosition as $reviewPos): ?>
                  <li class="navigation-review-alert__item">
                    <i data-lucide="map-pin" width="13" height="13" aria-hidden="true" class="navigation-review-alert__item-icon"></i>
                    <?= htmlspecialchars($reviewPos['positionName']) ?>
                    <a href="/dashboard/posicion/upload?position=<?= (int) $reviewPos['positionId'] ?>&negocio=<?= htmlspecialchars($business['slug']) ?>&tour=<?= htmlspecialchars($tour['slug']) ?>#navigation-arrows-panel"
                       class="db-btn-ghost navigation-review-alert__action navigation-review-alert__action--compact">
                      Revisar
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php $isPublished = (bool)(int) $tour['is_published']; ?>

      <!-- ── BLOQUE 1: Header del tour ── -->
      <div class="db-manage-header" id="info-view">
        <div class="db-manage-header-left">

          <div class="db-manage-header-top">
            <h2 class="db-manage-name"><?= htmlspecialchars($tour['title']) ?></h2>
            <span class="db-badge <?= $isPublished ? 'db-badge--published' : 'db-badge--draft' ?>">
              <?= $isPublished ? 'Publicado' : 'Borrador' ?>
            </span>
          </div>

          <div class="db-manage-url-row">
            <span class="db-manage-url">oxphyre.com/<?= htmlspecialchars($business['slug']) ?>/<?= htmlspecialchars($tour['slug']) ?></span>
            <button type="button" class="db-manage-copy-btn" id="btn-copy-url"
              data-url="https://oxphyre.com/<?= htmlspecialchars($business['slug']) ?>/<?= htmlspecialchars($tour['slug']) ?>"
              aria-label="Copiar URL">
              <i data-lucide="copy" width="13" height="13" aria-hidden="true"></i>
            </button>
          </div>

          <?php if (!empty($tour['description'])): ?>
            <p class="db-manage-desc"><?= htmlspecialchars($tour['description']) ?></p>
          <?php endif; ?>

          <div class="db-manage-meta">
            <span class="db-manage-meta-row">
              <i data-lucide="calendar" width="13" height="13" aria-hidden="true"></i>
              Creado <?= date('d/m/Y', strtotime($tour['created_at'])) ?>
            </span>
          </div>

        </div>

        <div class="db-manage-header-right">
          <?php if ($isPublished): ?>
            <a href="/tour/<?= htmlspecialchars($business['slug']) ?>/<?= htmlspecialchars($tour['slug']) ?>"
               class="db-btn-secondary db-btn-brand-outline"
               target="_blank"
               rel="noopener">
              <i data-lucide="external-link" width="14" height="14" aria-hidden="true"></i>
              Ver tour público
            </a>
            <a href="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>/qr/download"
               class="db-btn-secondary">
              <i data-lucide="qr-code" width="14" height="14" aria-hidden="true"></i>
              Descargar QR
            </a>
            <?php $qrScanCount = (int) ($qrScanCount ?? 0); ?>
            <span style="font-size:0.75rem;color:var(--ox-text-muted);line-height:1.2;">
              <?= $qrScanCount > 0 ? $qrScanCount . ' escaneos desde el QR' : 'QR listo para compartir' ?>
            </span>
          <?php endif; ?>

          <button type="button" class="db-btn-secondary" id="btn-edit">
            <i data-lucide="pencil" width="14" height="14" aria-hidden="true"></i>
            Editar
          </button>

          <!-- Toggle publicado/borrador como mini-form -->
          <form method="POST"
            action="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>/edit"
            style="display:inline;">
            <input type="hidden" name="csrf_token"   value="<?= $csrfToken ?>">
            <input type="hidden" name="title"        value="<?= htmlspecialchars($tour['title']) ?>">
            <input type="hidden" name="description"  value="<?= htmlspecialchars($tour['description'] ?? '') ?>">
            <input type="hidden" name="is_published" value="<?= $isPublished ? '0' : '1' ?>">
            <button type="submit" class="db-btn-secondary">
              <i data-lucide="<?= $isPublished ? 'eye-off' : 'eye' ?>" width="14" height="14" aria-hidden="true"></i>
              <?= $isPublished ? 'Despublicar' : 'Publicar' ?>
            </button>
          </form>

          <button type="button" class="db-btn-danger" id="btn-delete-tour">
            <i data-lucide="trash-2" width="14" height="14" aria-hidden="true"></i>
            Eliminar
          </button>
        </div>
      </div>

      <!-- ── BLOQUE 2: Formulario de edición inline ── -->
      <div class="db-manage-card" id="edit-wrapper" hidden>
        <form id="edit-form"
          action="/dashboard/negocios/<?= htmlspecialchars($business['slug']) ?>/tours/<?= htmlspecialchars($tour['slug']) ?>/edit"
          method="POST" novalidate>
          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

          <div class="db-manage-edit-grid">
            <div class="db-form-group db-manage-edit-full">
              <label class="db-form-label" for="edit-title">
                Título<span class="required" aria-hidden="true">*</span>
              </label>
              <input class="db-form-input" type="text" id="edit-title" name="title"
                maxlength="100" value="<?= htmlspecialchars($tour['title']) ?>" required>
            </div>

            <div class="db-form-group db-manage-edit-full">
              <label class="db-form-label" for="edit-desc">Descripción</label>
              <textarea class="db-form-textarea" id="edit-desc" name="description"
                maxlength="500" rows="2"><?= htmlspecialchars($tour['description'] ?? '') ?></textarea>
            </div>

            <div class="db-form-group db-manage-edit-full" style="margin-bottom:0;display:flex;align-items:center;gap:0.625rem;">
              <input type="checkbox" id="edit-published" name="is_published" value="1"
                <?= $isPublished ? 'checked' : '' ?>
                style="width:16px;height:16px;accent-color:var(--ox-amber);cursor:pointer;">
              <label for="edit-published" class="db-form-label" style="margin-bottom:0;cursor:pointer;">
                Publicado (visible para visitantes)
              </label>
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

      <!-- ── BLOQUE 3: Posiciones ── -->
      <section class="db-manage-tours-section" aria-label="Posiciones del tour">

        <div class="db-manage-tours-header">
          <span class="db-manage-tours-title" style="display:inline-flex;align-items:center;gap:0.375rem;">
            Posiciones
            <button type="button" class="db-help-icon" aria-label="¿Qué es una posición?">
              <i data-lucide="circle-help" width="15" height="15" aria-hidden="true"></i>
              <span class="db-help-tooltip">
                Una posición es un punto de tu local desde el que el cliente podrá mirar a su alrededor.
                Ej: la entrada, la barra, la terraza.
              </span>
            </button>
          </span>
          <a href="/dashboard/posicion/nueva?negocio=<?= htmlspecialchars($business['slug']) ?>&tour=<?= htmlspecialchars($tour['slug']) ?>"
             class="db-btn-secondary" style="font-size:0.8125rem;">
            <i data-lucide="plus" width="14" height="14" aria-hidden="true"></i>
            Añadir posición
          </a>
        </div>

        <?php if (empty($positions)): ?>
          <div class="db-empty" style="padding:2.5rem 1rem;">
            <div class="db-empty-icon" aria-hidden="true">
              <i data-lucide="image" width="24" height="24"></i>
            </div>
            <p class="db-empty-title">Este tour aún no tiene posiciones.</p>
            <p class="db-empty-sub">Añade la primera posición para empezar a subir fotos 360°.</p>
            <a href="/dashboard/posicion/nueva?negocio=<?= htmlspecialchars($business['slug']) ?>&tour=<?= htmlspecialchars($tour['slug']) ?>"
               class="db-btn-primary">Añadir primera posición →</a>
          </div>

        <?php else: ?>
          <div class="db-pos-grid">
            <?php foreach ($positions as $pos): ?>
              <article class="db-pos-card">
                <?php
                  // Una posicion solo es visitable cuando tiene panoramica 360.
                  // Las fotos detalle son opcionales y no desbloquean por si solas
                  // el acceso al visor publico de esa posicion.
                  $hasPanorama = !empty($pos['has_panorama']);
                  $positionPreviewTooltip = 'Sube una panorámica principal para activar esta experiencia Oxphyre Room. Las fotos detalle son opcionales.';
                ?>
                <div class="db-pos-card-top">
                  <span class="db-pos-card-order">#<?= (int) $pos['order_index'] ?></span>
                  <button type="button" class="db-pos-card-delete btn-delete-position"
                    data-position-id="<?= (int) $pos['id'] ?>"
                    data-position-name="<?= htmlspecialchars($pos['name']) ?>"
                    aria-label="Eliminar posición <?= htmlspecialchars($pos['name']) ?>">
                    <i data-lucide="trash-2" width="14" height="14" aria-hidden="true"></i>
                  </button>
                </div>

                <h3 class="db-pos-card-title"><?= htmlspecialchars($pos['name']) ?></h3>

                <?php if (isset($positionsWithArrowsNeedReview[(int) $pos['id']])): ?>
                  <span class="navigation-review-badge">
                    <i data-lucide="triangle-alert" width="10" height="10" aria-hidden="true"></i>
                    Flechas por revisar
                  </span>
                <?php endif; ?>

                <div class="db-pos-card-actions">
                  <a href="/dashboard/posicion/upload?position=<?= (int) $pos['id'] ?>&negocio=<?= htmlspecialchars($business['slug']) ?>&tour=<?= htmlspecialchars($tour['slug']) ?>"
                     class="db-btn-secondary db-pos-card-action">
                    Gestionar
                  </a>
                  <?php if ($isPublished): ?>
                    <?php if ($hasPanorama): ?>
                      <a href="/tour/<?= htmlspecialchars($business['slug']) ?>/<?= htmlspecialchars($tour['slug']) ?>?position=<?= (int) $pos['id'] ?>"
                         class="db-btn-secondary db-btn-brand-outline db-pos-card-action db-pos-card-action--preview"
                         target="_blank"
                         rel="noopener">
                        <i data-lucide="external-link" width="13" height="13" aria-hidden="true"></i>
                        Ver posición
                      </a>
                    <?php else: ?>
                      <span class="db-btn-secondary db-pos-card-action db-pos-card-action--preview"
                            role="button"
                            aria-disabled="true"
                            title="<?= htmlspecialchars($positionPreviewTooltip) ?>"
                            style="opacity:0.55;cursor:not-allowed;filter:grayscale(0.35);">
                        <i data-lucide="external-link" width="13" height="13" aria-hidden="true"></i>
                        Ver posición
                      </span>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

      </section>

      <!-- ── Modal: eliminar tour ── -->
      <div class="db-modal-overlay" id="modal-delete-tour" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="delete-tour-title">
        <div class="db-modal">
          <button class="db-modal-close" id="btn-close-delete-modal" aria-label="Cerrar">
            <i data-lucide="x" width="18" height="18" aria-hidden="true"></i>
          </button>
          <div class="db-modal-icon db-modal-icon--danger" aria-hidden="true">
            <i data-lucide="trash-2" width="28" height="28"></i>
          </div>
          <h3 class="db-modal-title" id="delete-tour-title">¿Eliminar este tour?</h3>
          <p class="db-modal-body">Esta acción no se puede deshacer. El tour y todas sus posiciones y fotos quedarán eliminados.</p>
          <form method="POST" action="/dashboard/tours/<?= htmlspecialchars($tour['slug']) ?>/delete">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="biz_slug"   value="<?= htmlspecialchars($business['slug']) ?>">
            <div class="db-modal-actions">
              <button type="submit" class="db-btn-danger">Eliminar tour</button>
              <button type="button" class="db-btn-ghost" id="btn-cancel-delete-modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>

      <!-- ── Modal: eliminar posición ── -->
      <div class="db-modal-overlay" id="modal-delete-position" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="delete-position-title">
        <div class="db-modal">
          <button class="db-modal-close" id="btn-close-delete-position-modal" aria-label="Cerrar">
            <i data-lucide="x" width="18" height="18" aria-hidden="true"></i>
          </button>
          <div class="db-modal-icon db-modal-icon--danger" aria-hidden="true">
            <i data-lucide="trash-2" width="28" height="28"></i>
          </div>
          <h3 class="db-modal-title" id="delete-position-title">¿Eliminar esta posición?</h3>
          <p class="db-modal-body" id="delete-position-body">La posición dejará de aparecer en este tour.</p>
          <form method="POST" action="/dashboard/posicion/delete">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <input type="hidden" name="biz_slug"   value="<?= htmlspecialchars($business['slug']) ?>">
            <input type="hidden" name="tour_slug"  value="<?= htmlspecialchars($tour['slug']) ?>">
            <input type="hidden" name="position_id" id="delete-position-id" value="">
            <div class="db-modal-actions">
              <button type="submit" class="db-btn-danger">Eliminar posición</button>
              <button type="button" class="db-btn-ghost" id="btn-cancel-delete-position-modal">Cancelar</button>
            </div>
          </form>
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

  // ── Toggle formulario de edición ──────────────────────────────────────────
  const infoView    = document.getElementById('info-view');
  const editWrapper = document.getElementById('edit-wrapper');
  const btnEdit     = document.getElementById('btn-edit');
  const btnCancel   = document.getElementById('btn-cancel-edit');

  btnEdit.addEventListener('click', () => {
    infoView.hidden = true;
    editWrapper.hidden = false;
    document.getElementById('edit-title').focus();
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

  // ── Modal: eliminar tour ──────────────────────────────────────────────────
  const modalDelete = document.getElementById('modal-delete-tour');
  const btnDelete   = document.getElementById('btn-delete-tour');
  const btnClose    = document.getElementById('btn-close-delete-modal');
  const btnCancel2  = document.getElementById('btn-cancel-delete-modal');

  const openDeleteModal  = () => { modalDelete.classList.add('is-visible'); modalDelete.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; };
  const closeDeleteModal = () => { modalDelete.classList.remove('is-visible'); modalDelete.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; };

  btnDelete?.addEventListener('click', openDeleteModal);
  btnClose?.addEventListener('click', closeDeleteModal);
  btnCancel2?.addEventListener('click', closeDeleteModal);
  modalDelete?.addEventListener('click', e => { if (e.target === modalDelete) closeDeleteModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && modalDelete?.classList.contains('is-visible')) closeDeleteModal(); });

  // ── Modal: eliminar posición ───────────────────────────────────────────────
  const modalDeletePosition = document.getElementById('modal-delete-position');
  const deletePositionId    = document.getElementById('delete-position-id');
  const deletePositionBody  = document.getElementById('delete-position-body');
  const btnClosePosition    = document.getElementById('btn-close-delete-position-modal');
  const btnCancelPosition   = document.getElementById('btn-cancel-delete-position-modal');

  const openDeletePositionModal = btn => {
    if (!modalDeletePosition || !deletePositionId || !deletePositionBody) return;
    deletePositionId.value = btn.dataset.positionId || '';
    deletePositionBody.textContent = `La posición "${btn.dataset.positionName || 'seleccionada'}" dejará de aparecer en este tour.`;
    modalDeletePosition.classList.add('is-visible');
    modalDeletePosition.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  };
  const closeDeletePositionModal = () => {
    if (!modalDeletePosition) return;
    modalDeletePosition.classList.remove('is-visible');
    modalDeletePosition.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  };

  document.querySelectorAll('.btn-delete-position').forEach(btn => {
    btn.addEventListener('click', () => openDeletePositionModal(btn));
  });
  btnClosePosition?.addEventListener('click', closeDeletePositionModal);
  btnCancelPosition?.addEventListener('click', closeDeletePositionModal);
  modalDeletePosition?.addEventListener('click', e => { if (e.target === modalDeletePosition) closeDeletePositionModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && modalDeletePosition?.classList.contains('is-visible')) closeDeletePositionModal(); });
});
</script>

</body>
</html>
