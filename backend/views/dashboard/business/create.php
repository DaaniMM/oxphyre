<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nuevo negocio — Oxphyre</title>
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
      <div class="db-plan-badge">
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
    <h1 class="db-topbar-title">Nuevo negocio</h1>
    <div class="db-avatar" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido: wizard ── -->
  <main class="db-main">
    <div class="db-page">

      <?php if ($flash && $flash['type'] === 'error'): ?>
        <div style="background:oklch(0.35 0.12 25/0.25);border:1px solid oklch(0.55 0.15 25/0.4);color:oklch(0.80 0.10 25);padding:0.75rem 1rem;border-radius:8px;font-size:0.875rem;margin-bottom:1.5rem;" role="alert">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <!-- Indicador de pasos -->
      <div class="wizard-header">
        <h2 class="wizard-title">Crea tu <em>negocio.</em></h2>
        <p class="wizard-subtitle">Solo necesitas un par de datos para empezar.</p>

        <div class="wizard-steps" role="list" aria-label="Pasos del wizard">
          <div class="wizard-step is-active" id="step-indicator-1" role="listitem">
            <div class="step-bubble">1</div>
            <span class="step-label">Tu negocio</span>
          </div>
          <div class="wizard-connector"></div>
          <div class="wizard-step" id="step-indicator-2" role="listitem">
            <div class="step-bubble">2</div>
            <span class="step-label">Tu plan</span>
          </div>
          <div class="wizard-connector"></div>
          <div class="wizard-step" id="step-indicator-3" role="listitem">
            <div class="step-bubble">3</div>
            <span class="step-label">Listo</span>
          </div>
        </div>
      </div>

      <!-- Formulario (único POST, pasos 1 y 2 por JS) -->
      <form action="/dashboard/business/store" method="POST" novalidate id="wizard-form">
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <!-- ── PASO 1: datos del negocio ── -->
        <div class="wizard-panel is-active" id="panel-1">
          <div class="wizard-card">

            <div class="db-form-group">
              <label class="db-form-label" for="biz-name">
                Nombre del negocio<span class="required" aria-hidden="true">*</span>
              </label>
              <input class="db-form-input" type="text" id="biz-name" name="name"
                maxlength="100" placeholder="Ej. Restaurante El Rincón"
                autocomplete="organization" required>
              <div class="char-counter" id="name-counter" aria-live="polite">0 / 100</div>
              <span class="db-form-error" id="name-error" aria-live="polite"></span>
            </div>

            <div class="db-form-group">
              <label class="db-form-label" for="biz-slug">
                URL pública del negocio<span class="required" aria-hidden="true">*</span>
              </label>
              <div class="slug-row">
                <span class="slug-prefix">oxphyre.com/</span>
                <input class="db-form-input" type="text" id="biz-slug" name="slug"
                  maxlength="60" placeholder="mi-negocio" required
                  pattern="[a-z0-9][a-z0-9\-]*[a-z0-9]"
                  aria-describedby="slug-hint">
              </div>
              <span class="db-form-error" id="slug-error" aria-live="polite"></span>
              <span id="slug-hint" style="font-size:11px;color:var(--ox-text-dim);margin-top:2px;">
                Solo letras minúsculas, números y guiones. Mínimo 2 caracteres.
              </span>
            </div>

            <div class="db-form-group">
              <label class="db-form-label" for="biz-desc">Descripción breve</label>
              <textarea class="db-form-textarea" id="biz-desc" name="description"
                maxlength="300" rows="3"
                placeholder="Describe brevemente tu negocio..."></textarea>
              <div class="char-counter" id="desc-counter" aria-live="polite">0 / 300</div>
            </div>

            <div class="db-form-group">
              <label class="db-form-label" for="biz-phone">Teléfono</label>
              <input class="db-form-input" type="tel" id="biz-phone" name="phone"
                maxlength="20" placeholder="+34 600 000 000">
            </div>

            <div class="db-form-group" style="margin-bottom:0;">
              <label class="db-form-label" for="biz-address">Dirección</label>
              <input class="db-form-input" type="text" id="biz-address" name="address"
                maxlength="200" placeholder="Calle Mayor 1, Madrid">
            </div>
          </div>

          <div class="wizard-nav">
            <a href="/dashboard" class="wizard-btn-back">
              <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
              Cancelar
            </a>
            <button type="button" class="wizard-btn-next" id="btn-next">
              Siguiente
              <i data-lucide="arrow-right" width="16" height="16" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <!-- ── PASO 2: confirmar plan ── -->
        <div class="wizard-panel" id="panel-2">
          <div class="wizard-card">
            <p style="font-family:'JetBrains Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.25em;color:var(--ox-text-dim);margin-bottom:1rem;">Plan actual</p>
            <h3 style="font-family:'Instrument Serif',serif;font-size:1.4rem;font-weight:400;color:var(--ox-text);margin-bottom:1.25rem;">
              Empezando con <em style="font-style:italic;color:var(--ox-amber);">Free.</em>
            </h3>

            <ul class="plan-features-list" aria-label="Características del plan Free">
              <li class="plan-feature-item included">
                <i data-lucide="check" width="16" height="16" class="plan-feature-icon yes" aria-hidden="true"></i>
                1 negocio, 1 tour activo
              </li>
              <li class="plan-feature-item included">
                <i data-lucide="check" width="16" height="16" class="plan-feature-icon yes" aria-hidden="true"></i>
                Hasta 5 posiciones por tour
              </li>
              <li class="plan-feature-item included">
                <i data-lucide="check" width="16" height="16" class="plan-feature-icon yes" aria-hidden="true"></i>
                1 posición con profundidad IA real (MiDaS) incluida
              </li>
              <li class="plan-feature-item included">
                <i data-lucide="check" width="16" height="16" class="plan-feature-icon yes" aria-hidden="true"></i>
                Esfera Three.js navegable en el resto de posiciones
              </li>
              <li class="plan-feature-item included">
                <i data-lucide="check" width="16" height="16" class="plan-feature-icon yes" aria-hidden="true"></i>
                QR descargable · URL pública oxphyre.com/tu-negocio
              </li>
              <li class="plan-feature-item">
                <i data-lucide="x" width="16" height="16" class="plan-feature-icon no" aria-hidden="true"></i>
                Sin embed / sin minimapa
              </li>
              <li class="plan-feature-item">
                <i data-lucide="x" width="16" height="16" class="plan-feature-icon no" aria-hidden="true"></i>
                Marca de agua Oxphyre visible en el visor
              </li>
            </ul>
          </div>

          <div class="wizard-nav">
            <button type="button" class="wizard-btn-back" id="btn-back">
              <i data-lucide="arrow-left" width="16" height="16" aria-hidden="true"></i>
              Atrás
            </button>
            <button type="submit" class="wizard-btn-submit">
              <i data-lucide="check" width="16" height="16" aria-hidden="true"></i>
              Continuar con Free
            </button>
            <a href="/precios" style="font-size:0.8125rem;color:var(--ox-text-muted);margin-left:0.5rem;align-self:center;">
              Ver planes Pro / Business →
            </a>
          </div>
        </div>

      </form>

    </div>
  </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();

  // ── Sidebar móvil ──────────────────────────────────────────────────────
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

  // ── Wizard ──────────────────────────────────────────────────────────────
  const panel1 = document.getElementById('panel-1');
  const panel2 = document.getElementById('panel-2');
  const step1  = document.getElementById('step-indicator-1');
  const step2  = document.getElementById('step-indicator-2');
  const btnNext = document.getElementById('btn-next');
  const btnBack = document.getElementById('btn-back');

  const nameInput = document.getElementById('biz-name');
  const slugInput = document.getElementById('biz-slug');
  const descInput = document.getElementById('biz-desc');
  const nameError = document.getElementById('name-error');
  const slugError = document.getElementById('slug-error');
  const nameCtr   = document.getElementById('name-counter');
  const descCtr   = document.getElementById('desc-counter');

  let slugManuallyEdited = false;

  // Autogenerar slug desde nombre
  function slugify(str) {
    return str
      .toLowerCase()
      .normalize('NFD')
      .replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 60);
  }

  nameInput.addEventListener('input', () => {
    nameCtr.textContent = `${nameInput.value.length} / 100`;
    if (!slugManuallyEdited) {
      slugInput.value = slugify(nameInput.value);
    }
    if (nameInput.value.trim()) nameInput.classList.remove('is-error');
  });

  slugInput.addEventListener('input', () => {
    slugManuallyEdited = true;
    slugInput.value = slugInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
    if (slugInput.value.length >= 2) slugInput.classList.remove('is-error');
  });

  descInput.addEventListener('input', () => {
    descCtr.textContent = `${descInput.value.length} / 300`;
  });

  // Validar paso 1 antes de avanzar
  function validateStep1() {
    let ok = true;
    const name = nameInput.value.trim();
    const slug = slugInput.value.trim();

    if (!name) {
      nameInput.classList.add('is-error');
      nameError.textContent = 'El nombre es obligatorio.';
      ok = false;
    } else if (name.length > 100) {
      nameInput.classList.add('is-error');
      nameError.textContent = 'Máximo 100 caracteres.';
      ok = false;
    } else {
      nameInput.classList.remove('is-error');
      nameError.textContent = '';
    }

    if (slug.length < 2) {
      slugInput.classList.add('is-error');
      slugError.textContent = 'El slug debe tener al menos 2 caracteres.';
      ok = false;
    } else if (!/^[a-z0-9][a-z0-9-]*[a-z0-9]$/.test(slug) && slug.length >= 2) {
      slugInput.classList.add('is-error');
      slugError.textContent = 'Solo letras minúsculas, números y guiones. No puede empezar ni terminar con guión.';
      ok = false;
    } else {
      slugInput.classList.remove('is-error');
      slugError.textContent = '';
    }

    return ok;
  }

  btnNext.addEventListener('click', () => {
    if (!validateStep1()) return;
    panel1.classList.remove('is-active');
    panel2.classList.add('is-active');
    step1.classList.remove('is-active');
    step1.classList.add('is-done');
    step2.classList.add('is-active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  btnBack.addEventListener('click', () => {
    panel2.classList.remove('is-active');
    panel1.classList.add('is-active');
    step2.classList.remove('is-active');
    step1.classList.remove('is-done');
    step1.classList.add('is-active');
  });
});
</script>

</body>
</html>
