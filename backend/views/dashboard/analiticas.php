<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analíticas — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="alternate icon" href="/favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <link rel="stylesheet" href="<?= asset('/css/dashboard.css') ?>">
  <style>
    /* ── Analíticas: estilos aislados en esta vista ── */

    .atl-page-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
      margin-bottom: 1.75rem;
    }
    .atl-page-header-left { display: flex; flex-direction: column; gap: 6px; }
    .atl-page-title {
      font-family: 'Instrument Serif', Georgia, serif;
      font-size: clamp(1.4rem, 3vw, 1.9rem);
      font-style: italic;
      color: var(--ox-amber);
      line-height: 1.1;
    }
    .atl-page-sub { font-size: 0.875rem; color: var(--ox-text-muted); }
    .atl-page-badges { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 4px; }

    /* ── Cards sección ── */
    .atl-card {
      background: var(--ox-bg-elevated);
      border: 1px solid var(--ox-border);
      border-radius: 14px;
      padding: 1.375rem 1.5rem;
      margin-bottom: 1.5rem;
    }
    .atl-card-title {
      font-size: 0.8125rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--ox-text-muted);
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ── Gráfico de barras CSS-only ── */
    .atl-chart-counts-row,
    .atl-chart-bars-row,
    .atl-chart-labels-row { display: flex; gap: 6px; }

    .atl-chart-bars-row {
      height: 96px;
      align-items: flex-end;
      margin: 4px 0;
    }
    .atl-chart-bar,
    .atl-chart-count-val,
    .atl-chart-day-label { flex: 1; }

    .atl-chart-bar {
      border-radius: 4px 4px 0 0;
      background: var(--ox-amber);
      min-height: 3px;
      transition: opacity 0.2s;
    }
    .atl-chart-bar:hover { opacity: 0.75; }
    .atl-chart-bar.is-zero { background: oklch(0.22 0.01 60); }

    .atl-chart-count-val {
      text-align: center;
      font-size: 0.625rem;
      color: var(--ox-text-muted);
      font-variant-numeric: tabular-nums;
      min-height: 14px;
    }
    .atl-chart-day-label {
      text-align: center;
      font-size: 0.5625rem;
      color: var(--ox-text-dim);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-top: 5px;
    }
    .atl-chart-empty {
      text-align: center;
      padding: 18px 0 8px;
    }
    .atl-empty-title {
      font-size: 0.9375rem;
      font-weight: 600;
      color: var(--ox-text-muted);
      margin-bottom: 6px;
    }
    .atl-empty-desc {
      font-size: 0.8125rem;
      color: var(--ox-text-dim);
      line-height: 1.6;
      max-width: 480px;
      margin: 0 auto 20px;
    }
    .atl-empty-steps {
      display: flex;
      justify-content: center;
      gap: 0;
      flex-wrap: wrap;
    }
    .atl-empty-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      width: 120px;
      padding: 0 8px;
      position: relative;
    }
    .atl-empty-step:not(:last-child)::after {
      content: '→';
      position: absolute;
      right: -6px;
      top: 10px;
      color: var(--ox-text-dim);
      font-size: 0.75rem;
    }
    .atl-empty-step-num {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: oklch(0.18 0.005 60);
      border: 1px solid var(--ox-border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.6875rem;
      font-weight: 600;
      color: var(--ox-text-dim);
    }
    .atl-empty-step-text {
      font-size: 0.6875rem;
      color: var(--ox-text-dim);
      text-align: center;
      line-height: 1.4;
    }

    /* ── Embudo ── */
    .atl-funnel { display: flex; flex-direction: column; gap: 0; }
    .atl-funnel-step {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 0;
      border-top: 1px solid var(--ox-border);
    }
    .atl-funnel-step:first-child { border-top: none; padding-top: 0; }
    .atl-funnel-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      background: oklch(0.78 0.16 65 / 0.1);
      color: var(--ox-amber);
    }
    .atl-funnel-step.is-locked .atl-funnel-icon {
      background: oklch(0.18 0.005 60);
      color: var(--ox-text-dim);
    }
    .atl-funnel-body { flex: 1; }
    .atl-funnel-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--ox-text);
    }
    .atl-funnel-step.is-locked .atl-funnel-label { color: var(--ox-text-muted); }
    .atl-funnel-note { font-size: 0.78rem; color: var(--ox-text-dim); margin-top: 2px; }
    .atl-funnel-badge-active {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 0.6875rem;
      font-weight: 600;
      color: oklch(0.75 0.14 145);
      background: oklch(0.55 0.12 145 / 0.12);
      border: 1px solid oklch(0.55 0.12 145 / 0.3);
      border-radius: 20px;
      padding: 2px 9px;
      white-space: nowrap;
      flex-shrink: 0;
    }

    /* ── Cards Pro bloqueadas ── */
    .atl-locked-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }
    .atl-locked-card {
      position: relative;
      background: var(--ox-bg-elevated);
      border: 1px solid var(--ox-border);
      border-radius: 12px;
      padding: 1.25rem;
      overflow: hidden;
    }
    .atl-locked-content {
      display: flex;
      flex-direction: column;
      gap: 6px;
      filter: blur(2px);
      pointer-events: none;
      user-select: none;
    }
    .atl-locked-icon { color: var(--ox-amber); opacity: 0.5; }
    .atl-locked-title { font-size: 0.875rem; font-weight: 600; color: var(--ox-text); }
    .atl-locked-desc  { font-size: 0.75rem; color: var(--ox-text-muted); }
    .atl-locked-bar { height: 6px; border-radius: 3px; background: var(--ox-amber); opacity: 0.35; margin-top: 6px; }

    .atl-locked-overlay {
      position: absolute;
      inset: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: oklch(0.08 0.005 60 / 0.55);
    }
    .atl-locked-lock { color: var(--ox-text-dim); }

    /* ── Nota de privacidad ── */
    .atl-privacy-note {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-top: 1.5rem;
      padding: 12px 16px;
      border-radius: 8px;
      background: oklch(0.12 0.005 60);
      border: 1px solid var(--ox-border);
      font-size: 0.78rem;
      color: var(--ox-text-dim);
      line-height: 1.6;
    }
    .atl-privacy-note i { flex-shrink: 0; margin-top: 1px; }

    /* ── Pro plan notice (non-Free) ── */
    .atl-pro-notice {
      padding: 14px 18px;
      border-radius: 10px;
      background: oklch(0.78 0.16 65 / 0.06);
      border: 1px solid oklch(0.78 0.16 65 / 0.2);
      font-size: 0.8125rem;
      color: var(--ox-text-muted);
      margin-bottom: 1.5rem;
    }
    .atl-pro-notice strong { color: var(--ox-amber); }

    @media (max-width: 640px) {
      .atl-page-header { flex-direction: column; }
      .atl-locked-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 420px) {
      .atl-locked-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<?php
// ── Helpers de vista ──────────────────────────────────────────────────────────

/** Formato relativo de fecha de escaneo */
function formatScanDate(?string $rawDate): string {
    if ($rawDate === null) return 'Sin escaneos todavía';
    $ts    = strtotime($rawDate);
    $today = strtotime('today');
    $diff  = $today - strtotime(date('Y-m-d', $ts));
    if ($diff === 0)     return 'Hoy, ' . date('H:i', $ts);
    if ($diff === 86400) return 'Ayer, ' . date('H:i', $ts);
    return date('d/m/Y H:i', $ts);
}

/** Etiqueta del día en español abreviado */
function dayLabel(string $ymd): string {
    $days = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié',
             'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb', 'Sun' => 'Dom'];
    $en = date('D', strtotime($ymd));
    return $days[$en] ?? $en;
}

// ── Construir los 7 días con sus conteos ────────────────────────────────────
$chartDays = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chartDays[$date] = $scansByDay[$date] ?? 0;
}
$maxCount = max(1, max($chartDays));
$hasAnyScans = array_sum($chartDays) > 0;
?>

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
      <a href="/dashboard/analiticas"    class="db-nav-item active" aria-current="page">
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
    <h1 class="db-topbar-title">Analíticas</h1>
    <div class="db-avatar" aria-label="Usuario: <?= $userName ?>" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido principal ── -->
  <main class="db-main" id="db-main">
    <div class="db-page">

      <!-- ── Encabezado de página ── -->
      <div class="atl-page-header">
        <div class="atl-page-header-left">
          <h2 class="atl-page-title">Analíticas</h2>
          <p class="atl-page-sub">Comprueba si tu QR y tu tour empiezan a generar interés.</p>
          <div class="atl-page-badges">
            <span class="db-badge db-badge--plan">Plan <?= htmlspecialchars($planLabel) ?></span>
            <?php if ($planLabel === 'Free'): ?>
              <span class="db-badge db-badge--draft" style="font-size:0.6875rem;">QR tracking · uso del plan</span>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($planLabel === 'Free'): ?>
        <a href="/precios" class="db-btn-brand-outline" style="white-space:nowrap;align-self:flex-start;">
          <i data-lucide="zap" width="14" height="14" aria-hidden="true"></i>
          Mejorar a Pro
        </a>
        <?php endif; ?>
      </div>

      <?php if ($planLabel !== 'Free'): ?>
      <!-- Aviso para planes no-Free mientras se prepara su vista específica -->
      <div class="atl-pro-notice">
        <strong>Vista Free activa.</strong> Analíticas completas de <?= htmlspecialchars($planLabel) ?> se prepararán en la siguiente fase del producto.
      </div>
      <?php endif; ?>

      <!-- ── KPIs ── -->
      <div class="db-metrics" role="region" aria-label="Métricas clave">

        <!-- KPI 1: Escaneos QR totales -->
        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="scan-line" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= $totalScans ?></div>
          <div class="db-metric-label">Escaneos QR totales</div>
          <div class="db-metric-note">
            <?= $totalScans > 0
              ? 'Tu QR ya está generando aperturas del tour.'
              : 'Comparte tu QR para empezar a medir interés.' ?>
          </div>
        </div>

        <!-- KPI 2: Último escaneo -->
        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="clock" width="20" height="20"></i>
          </div>
          <div class="db-metric-value" style="font-size:<?= $lastScanAt ? '1.05rem' : '1.5rem' ?>;<?= $lastScanAt ? '' : 'color:var(--ox-text-dim);' ?>">
            <?= $lastScanAt ? htmlspecialchars(formatScanDate($lastScanAt)) : '—' ?>
          </div>
          <div class="db-metric-label">Último escaneo</div>
          <div class="db-metric-note">
            <?= $lastScanAt ? 'Última señal de interés recibida.' : 'Sin escaneos todavía.' ?>
          </div>
        </div>

        <!-- KPI 3: Tours publicados -->
        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="globe" width="20" height="20"></i>
          </div>
          <?php if ($publishedTours > 0): ?>
            <div class="db-metric-value"><?= $publishedTours ?></div>
            <div class="db-metric-label">Tour<?= $publishedTours > 1 ? 's' : '' ?> publicado<?= $publishedTours > 1 ? 's' : '' ?></div>
            <div class="db-metric-note">Tu visita está disponible para clientes.</div>
          <?php elseif ($totalTours > 0): ?>
            <div class="db-metric-value" style="font-size:1.05rem;color:var(--ox-text-muted);">Creado</div>
            <div class="db-metric-label">Tour sin publicar</div>
            <div class="db-metric-note">Publica el tour para poder medir actividad.</div>
          <?php else: ?>
            <div class="db-metric-value" style="font-size:1.5rem;color:var(--ox-text-dim);">—</div>
            <div class="db-metric-label">Sin tour todavía</div>
            <div class="db-metric-note"><a href="/dashboard/negocios/nuevo">Crear ahora →</a></div>
          <?php endif; ?>
        </div>

        <!-- KPI 4: Uso del plan -->
        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="layers" width="20" height="20"></i>
          </div>
          <div class="db-metric-value" style="font-size:1.1rem;">
            <?= $totalTours ?>/<?= $limitTours ?> tour
            <span style="color:var(--ox-text-dim);font-size:0.8rem;"> · </span>
            <?= $totalPositions ?>/<?= $limitPositions ?> pos.
          </div>
          <div class="db-metric-label">Uso del plan Free</div>
          <div class="db-metric-note">Límite actual: <?= $limitTours ?> tour y <?= $limitPositions ?> posiciones.</div>
        </div>

      </div>

      <!-- ── Gráfico: escaneos últimos 7 días ── -->
      <div class="atl-card" role="region" aria-label="Escaneos QR últimos 7 días">
        <p class="atl-card-title">
          <i data-lucide="bar-chart-2" width="15" height="15" aria-hidden="true"></i>
          Escaneos QR · últimos 7 días
        </p>

        <?php if (!$hasAnyScans): ?>
          <div class="atl-chart-empty">
            <p class="atl-empty-title">Aún no hay escaneos.</p>
            <p class="atl-empty-desc">Descarga tu QR y colócalo donde tus clientes lo vean: escaparate, mesa, tarjeta o redes sociales.</p>
            <div class="atl-empty-steps" aria-label="Pasos para empezar a medir">
              <div class="atl-empty-step">
                <div class="atl-empty-step-num">1</div>
                <span class="atl-empty-step-text">Comparte el QR</span>
              </div>
              <div class="atl-empty-step">
                <div class="atl-empty-step-num">2</div>
                <span class="atl-empty-step-text">El cliente abre el tour</span>
              </div>
              <div class="atl-empty-step">
                <div class="atl-empty-step-num">3</div>
                <span class="atl-empty-step-text">Vuelve aquí para ver actividad</span>
              </div>
            </div>
          </div>
        <?php else: ?>

          <!-- Conteos encima de las barras -->
          <div class="atl-chart-counts-row" aria-hidden="true">
            <?php foreach ($chartDays as $date => $count): ?>
            <span class="atl-chart-count-val"><?= $count > 0 ? $count : '' ?></span>
            <?php endforeach; ?>
          </div>

          <!-- Barras -->
          <div class="atl-chart-bars-row" role="img" aria-label="Gráfico de barras de escaneos por día">
            <?php foreach ($chartDays as $date => $count): ?>
            <?php $pct = $count > 0 ? max(6, (int) round(($count / $maxCount) * 100)) : 3; ?>
            <div class="atl-chart-bar <?= $count === 0 ? 'is-zero' : '' ?>"
                 style="height:<?= $pct ?>%;"
                 title="<?= htmlspecialchars(date('d/m', strtotime($date))) ?>: <?= $count ?> escaneo<?= $count !== 1 ? 's' : '' ?>">
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Etiquetas de día -->
          <div class="atl-chart-labels-row" aria-hidden="true">
            <?php foreach ($chartDays as $date => $count): ?>
            <span class="atl-chart-day-label"><?= htmlspecialchars(dayLabel($date)) ?></span>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>
      </div>

      <!-- ── Embudo de visita ── -->
      <div class="atl-card" role="region" aria-label="Embudo de visita">
        <p class="atl-card-title">
          <i data-lucide="filter" width="15" height="15" aria-hidden="true"></i>
          Tu embudo de visita
        </p>

        <div class="atl-funnel">

          <!-- Paso 1: medido en Free -->
          <div class="atl-funnel-step">
            <div class="atl-funnel-icon" aria-hidden="true">
              <i data-lucide="qr-code" width="18" height="18"></i>
            </div>
            <div class="atl-funnel-body">
              <div class="atl-funnel-label">Cliente escanea el QR</div>
              <div class="atl-funnel-note">
                Medido en Free —
                <?= $totalScans ?> escaneo<?= $totalScans !== 1 ? 's' : '' ?> acumulado<?= $totalScans !== 1 ? 's' : '' ?>
              </div>
            </div>
            <span class="atl-funnel-badge-active" aria-label="Disponible en Free">
              <i data-lucide="check" width="11" height="11" aria-hidden="true"></i>
              Disponible
            </span>
          </div>

          <!-- Paso 2: Pro -->
          <div class="atl-funnel-step is-locked">
            <div class="atl-funnel-icon" aria-hidden="true">
              <i data-lucide="eye" width="18" height="18"></i>
            </div>
            <div class="atl-funnel-body">
              <div class="atl-funnel-label">Cliente visita el tour</div>
              <div class="atl-funnel-note">Medición de aperturas y actividad del tour disponible en Pro.</div>
            </div>
            <span class="db-badge db-badge--plan">Pro</span>
          </div>

          <!-- Paso 3: Pro/Roadmap -->
          <div class="atl-funnel-step is-locked">
            <div class="atl-funnel-icon" aria-hidden="true">
              <i data-lucide="heart" width="18" height="18"></i>
            </div>
            <div class="atl-funnel-body">
              <div class="atl-funnel-label">Cliente toma una decisión</div>
              <div class="atl-funnel-note">Señales comerciales avanzadas como clics en zonas destacadas o CTAs.</div>
            </div>
            <span class="db-badge db-badge--draft">Roadmap</span>
          </div>

        </div>
      </div>

      <!-- ── Cards Pro bloqueadas ── -->
      <div class="atl-card" role="region" aria-label="Analíticas disponibles en Pro">
        <p class="atl-card-title">
          <i data-lucide="lock" width="15" height="15" aria-hidden="true"></i>
          Desbloquea analíticas Pro
          <a href="/precios" class="db-btn-brand-outline" style="font-size:0.75rem;padding:3px 10px;margin-left:auto;">Ver planes →</a>
        </p>
        <p style="font-size:0.8125rem;color:var(--ox-text-muted);margin-bottom:1rem;margin-top:-0.5rem;">
          Mejora a Pro para ver visitas por día, dispositivos, evolución y rendimiento de tus tours.
        </p>

        <div class="atl-locked-grid">

          <!-- Visitas por día -->
          <div class="atl-locked-card">
            <div class="atl-locked-content" aria-hidden="true">
              <div class="atl-locked-icon"><i data-lucide="trending-up" width="18" height="18"></i></div>
              <div class="atl-locked-title">Visitas por día</div>
              <div class="atl-locked-desc">Evolución diaria del tráfico hacia tu tour.</div>
              <div class="atl-locked-bar" style="width:72%;"></div>
              <div class="atl-locked-bar" style="width:44%;margin-top:3px;opacity:0.2;"></div>
            </div>
            <div class="atl-locked-overlay">
              <i data-lucide="lock" width="20" height="20" class="atl-locked-lock" aria-hidden="true"></i>
              <span class="db-badge db-badge--plan">Pro</span>
            </div>
          </div>

          <!-- Dispositivos -->
          <div class="atl-locked-card">
            <div class="atl-locked-content" aria-hidden="true">
              <div class="atl-locked-icon"><i data-lucide="smartphone" width="18" height="18"></i></div>
              <div class="atl-locked-title">Dispositivos</div>
              <div class="atl-locked-desc">Móvil, tablet o escritorio — cómo acceden tus clientes.</div>
              <div class="atl-locked-bar" style="width:85%;"></div>
              <div class="atl-locked-bar" style="width:30%;margin-top:3px;opacity:0.2;"></div>
            </div>
            <div class="atl-locked-overlay">
              <i data-lucide="lock" width="20" height="20" class="atl-locked-lock" aria-hidden="true"></i>
              <span class="db-badge db-badge--plan">Pro</span>
            </div>
          </div>

          <!-- Evolución semanal -->
          <div class="atl-locked-card">
            <div class="atl-locked-content" aria-hidden="true">
              <div class="atl-locked-icon"><i data-lucide="calendar" width="18" height="18"></i></div>
              <div class="atl-locked-title">Evolución semanal</div>
              <div class="atl-locked-desc">Comparativa semana a semana del interés en tu negocio.</div>
              <div class="atl-locked-bar" style="width:55%;"></div>
              <div class="atl-locked-bar" style="width:80%;margin-top:3px;opacity:0.2;"></div>
            </div>
            <div class="atl-locked-overlay">
              <i data-lucide="lock" width="20" height="20" class="atl-locked-lock" aria-hidden="true"></i>
              <span class="db-badge db-badge--plan">Pro</span>
            </div>
          </div>

          <!-- Rendimiento por tour -->
          <div class="atl-locked-card">
            <div class="atl-locked-content" aria-hidden="true">
              <div class="atl-locked-icon"><i data-lucide="bar-chart" width="18" height="18"></i></div>
              <div class="atl-locked-title">Rendimiento por tour</div>
              <div class="atl-locked-desc">Cuál de tus tours genera más interés y retención.</div>
              <div class="atl-locked-bar" style="width:60%;"></div>
              <div class="atl-locked-bar" style="width:40%;margin-top:3px;opacity:0.2;"></div>
            </div>
            <div class="atl-locked-overlay">
              <i data-lucide="lock" width="20" height="20" class="atl-locked-lock" aria-hidden="true"></i>
              <span class="db-badge db-badge--plan">Pro</span>
            </div>
          </div>

          <!-- Comparativa entre tours -->
          <div class="atl-locked-card">
            <div class="atl-locked-content" aria-hidden="true">
              <div class="atl-locked-icon"><i data-lucide="layers" width="18" height="18"></i></div>
              <div class="atl-locked-title">Comparativa entre tours</div>
              <div class="atl-locked-desc">Detecta qué zonas funcionan y cuáles mejorar.</div>
              <div class="atl-locked-bar" style="width:90%;"></div>
              <div class="atl-locked-bar" style="width:50%;margin-top:3px;opacity:0.2;"></div>
            </div>
            <div class="atl-locked-overlay">
              <i data-lucide="lock" width="20" height="20" class="atl-locked-lock" aria-hidden="true"></i>
              <span class="db-badge db-badge--plan">Pro</span>
            </div>
          </div>

          <!-- Exportación / informes -->
          <div class="atl-locked-card">
            <div class="atl-locked-content" aria-hidden="true">
              <div class="atl-locked-icon"><i data-lucide="download" width="18" height="18"></i></div>
              <div class="atl-locked-title">Exportación e informes</div>
              <div class="atl-locked-desc">Descarga datos de actividad para informes o clientes.</div>
              <div class="atl-locked-bar" style="width:65%;"></div>
              <div class="atl-locked-bar" style="width:35%;margin-top:3px;opacity:0.2;"></div>
            </div>
            <div class="atl-locked-overlay">
              <i data-lucide="lock" width="20" height="20" class="atl-locked-lock" aria-hidden="true"></i>
              <span class="db-badge db-badge--plan">Pro</span>
            </div>
          </div>

        </div>
      </div>

      <!-- ── Nota de privacidad ── -->
      <div class="atl-privacy-note" role="note">
        <i data-lucide="shield" width="15" height="15" aria-hidden="true"></i>
        <span>Oxphyre usa métricas agregadas y tracking básico del QR para evitar exponer datos personales innecesarios. No se registran IP completas ni user agents.</span>
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
});
</script>

</body>
</html>
