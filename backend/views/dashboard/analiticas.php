<?php
/**
 * Vista: Analíticas — inyectada por DashboardController::showAnalytics()
 * Las variables siguientes están disponibles en scope via require_once.
 * Las declaraciones @var suprimen los avisos P1008 del análisis estático.
 *
 * @var string      $userName
 * @var string      $userEmail
 * @var string      $userRole
 * @var string      $planLabel
 * @var string      $userInitial
 * @var string      $csrfToken
 * @var int         $totalScans
 * @var string|null $lastScanAt
 * @var int         $totalTours
 * @var int         $publishedTours
 * @var int         $totalPositions
 * @var array       $scansByDay        'Y-m-d' => count, últimos 7 días
 * @var int         $limitTours
 * @var int         $limitPositions
 * @var bool        $isPro
 * @var bool        $isBusinessPlan
 * @var array       $scansByDay14      'Y-m-d' => count, solo si $isPro
 * @var array       $deviceCounts      'type' => count, solo si $isPro
 * @var array       $tourRanking       [['tour_name'=>string,'scan_count'=>int]], solo si $isPro
 * @var array       $weekComparison    ['last7'=>int,'prev7'=>int], solo si $isPro
 */
?>
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
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <link rel="stylesheet" href="<?= asset('/css/dashboard.css') ?>">
  <style>
    /* ══════════════════════════════════════════════════════════════════════════
       Analíticas — estilos aislados en esta vista
       Cubre tanto la experiencia Free como la Pro.
    ══════════════════════════════════════════════════════════════════════════ */

    /* ── Cabecera de página ─────────────────────────────────────────────── */
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

    /* ── Cards genéricas de sección ─────────────────────────────────────── */
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

    /* ── Gráfico de barras CSS-only (FREE 7d) ───────────────────────────── */
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

    /* ── Estado vacío del gráfico ───────────────────────────────────────── */
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

    /* ── Embudo ─────────────────────────────────────────────────────────── */
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
    .atl-funnel-step.is-locked .atl-funnel-icon,
    .atl-funnel-step.is-pending .atl-funnel-icon {
      background: oklch(0.18 0.005 60);
      color: var(--ox-text-dim);
    }
    .atl-funnel-body { flex: 1; }
    .atl-funnel-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--ox-text);
    }
    .atl-funnel-step.is-locked .atl-funnel-label,
    .atl-funnel-step.is-pending .atl-funnel-label { color: var(--ox-text-muted); }
    .atl-funnel-note { font-size: 0.78rem; color: var(--ox-text-dim); margin-top: 2px; }
    .atl-funnel-badge-active {
      display: inline-flex; align-items: center; gap: 4px;
      font-size: 0.6875rem; font-weight: 600;
      color: oklch(0.75 0.14 145);
      background: oklch(0.55 0.12 145 / 0.12);
      border: 1px solid oklch(0.55 0.12 145 / 0.3);
      border-radius: 20px; padding: 2px 9px;
      white-space: nowrap; flex-shrink: 0;
    }
    .atl-funnel-badge-pending {
      display: inline-flex; align-items: center; gap: 4px;
      font-size: 0.6875rem; font-weight: 600;
      color: oklch(0.72 0.08 80);
      background: oklch(0.65 0.06 80 / 0.1);
      border: 1px solid oklch(0.65 0.06 80 / 0.3);
      border-radius: 20px; padding: 2px 9px;
      white-space: nowrap; flex-shrink: 0;
    }
    .atl-funnel-badge-roadmap {
      display: inline-flex; align-items: center; gap: 4px;
      font-size: 0.6875rem; font-weight: 600;
      color: var(--ox-text-dim);
      background: oklch(0.12 0.005 60);
      border: 1px solid var(--ox-border);
      border-radius: 20px; padding: 2px 9px;
      white-space: nowrap; flex-shrink: 0;
    }

    /* ── Cards Pro bloqueadas (FREE) ────────────────────────────────────── */
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
      display: flex; flex-direction: column; gap: 6px;
      filter: blur(2px); pointer-events: none; user-select: none;
    }
    .atl-locked-icon { color: var(--ox-amber); opacity: 0.5; }
    .atl-locked-title { font-size: 0.875rem; font-weight: 600; color: var(--ox-text); }
    .atl-locked-desc  { font-size: 0.75rem; color: var(--ox-text-muted); }
    .atl-locked-bar { height: 6px; border-radius: 3px; background: var(--ox-amber); opacity: 0.35; margin-top: 6px; }
    .atl-locked-overlay {
      position: absolute; inset: 0;
      display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;
      background: oklch(0.08 0.005 60 / 0.55);
    }
    .atl-locked-lock { color: var(--ox-text-dim); }

    /* ── Nota de privacidad ─────────────────────────────────────────────── */
    .atl-privacy-note {
      display: flex; align-items: flex-start; gap: 10px;
      margin-top: 1.5rem; padding: 12px 16px; border-radius: 8px;
      background: oklch(0.12 0.005 60); border: 1px solid var(--ox-border);
      font-size: 0.78rem; color: var(--ox-text-dim); line-height: 1.6;
    }
    .atl-privacy-note i { flex-shrink: 0; margin-top: 1px; }

    /* ══════════════════════════════════════════════════════════════════════
       PRO — estilos específicos de la experiencia Pro
    ══════════════════════════════════════════════════════════════════════ */

    /* ── KPI Pro 4 columnas ─────────────────────────────────────────────── */
    .atl-pro-kpi {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .atl-pro-kpi-card {
      background: var(--ox-bg-elevated);
      border: 1px solid var(--ox-border);
      border-radius: 14px;
      padding: 1.25rem 1.375rem;
      display: flex;
      flex-direction: column;
      gap: 2px;
      transition: border-color 0.2s;
    }
    .atl-pro-kpi-card:hover { border-color: oklch(0.78 0.16 65 / 0.35); }
    .atl-pro-kpi-icon { color: var(--ox-amber); margin-bottom: 8px; }
    .atl-pro-kpi-val {
      font-family: 'JetBrains Mono', monospace;
      font-size: clamp(1.6rem, 3vw, 2rem);
      font-weight: 700;
      color: var(--ox-text);
      line-height: 1;
      letter-spacing: -0.02em;
    }
    .atl-pro-kpi-val.is-empty { font-size: 1.5rem; color: var(--ox-text-dim); }
    .atl-pro-kpi-label {
      font-size: 0.6875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      color: var(--ox-text-muted);
      margin-top: 4px;
    }
    .atl-pro-kpi-note {
      font-size: 0.75rem;
      color: var(--ox-text-dim);
      margin-top: 3px;
      line-height: 1.45;
    }
    .atl-pro-kpi-sub {
      font-size: 0.75rem;
      color: var(--ox-text-muted);
      margin-top: 1px;
    }

    /* ── Trend badges ───────────────────────────────────────────────────── */
    .atl-trend-badge {
      display: inline-flex; align-items: center; gap: 3px;
      font-size: 0.6875rem; font-weight: 600;
      padding: 2px 8px; border-radius: 20px;
      margin-top: 5px; width: fit-content;
    }
    .atl-trend-badge.up {
      color: oklch(0.72 0.15 145);
      background: oklch(0.5 0.12 145 / 0.1);
      border: 1px solid oklch(0.5 0.12 145 / 0.28);
    }
    .atl-trend-badge.down {
      color: oklch(0.68 0.15 20);
      background: oklch(0.5 0.12 20 / 0.1);
      border: 1px solid oklch(0.5 0.12 20 / 0.28);
    }
    .atl-trend-badge.flat {
      color: var(--ox-text-dim);
      background: oklch(0.15 0.005 60);
      border: 1px solid var(--ox-border);
    }

    /* ── Gráfico Pro 14 días ────────────────────────────────────────────── */
    .atl-chart14-bars-row {
      height: 110px;
      align-items: flex-end;
      display: flex;
      gap: 4px;
      margin: 4px 0;
    }
    .atl-chart14-bar,
    .atl-chart14-count-val,
    .atl-chart14-day-label { flex: 1; }
    .atl-chart14-counts-row,
    .atl-chart14-labels-row { display: flex; gap: 4px; }

    .atl-chart14-bar {
      border-radius: 3px 3px 0 0;
      background: var(--ox-amber);
      min-height: 3px;
      transition: opacity 0.2s;
    }
    .atl-chart14-bar:hover { opacity: 0.72; }
    .atl-chart14-bar.is-zero { background: oklch(0.22 0.01 60); }

    .atl-chart14-count-val {
      text-align: center;
      font-size: 0.5rem;
      color: var(--ox-text-muted);
      font-variant-numeric: tabular-nums;
      min-height: 12px;
    }
    .atl-chart14-day-label {
      text-align: center;
      font-size: 0.45rem;
      color: var(--ox-text-dim);
      text-transform: uppercase;
      letter-spacing: 0.04em;
      margin-top: 4px;
    }

    /* ── Leyenda del gráfico ────────────────────────────────────────────── */
    .atl-chart-legend {
      display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
      margin-top: 10px; font-size: 0.72rem; color: var(--ox-text-dim);
    }
    .atl-chart-legend-item { display: flex; align-items: center; gap: 6px; }
    .atl-chart-legend-dot {
      width: 8px; height: 8px; border-radius: 2px; flex-shrink: 0;
    }

    /* ── Nota de estado en el gráfico ──────────────────────────────────── */
    .atl-chart-insight {
      margin-top: 10px;
      padding: 8px 12px;
      border-radius: 7px;
      background: oklch(0.12 0.005 60);
      border: 1px solid var(--ox-border);
      font-size: 0.76rem;
      color: var(--ox-text-dim);
      line-height: 1.5;
    }

    /* ── Grid Pro desbloqueado ──────────────────────────────────────────── */
    .atl-pro-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(265px, 1fr));
      gap: 1rem;
      margin-top: 1rem;
    }
    .atl-pro-grid-card {
      background: var(--ox-bg-elevated);
      border: 1px solid var(--ox-border);
      border-radius: 12px;
      padding: 1.1875rem 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .atl-pro-grid-card-header {
      display: flex; align-items: center; gap: 8px;
    }
    .atl-pro-grid-card-icon { color: var(--ox-amber); flex-shrink: 0; }
    .atl-pro-grid-card-title {
      font-size: 0.8125rem; font-weight: 600; color: var(--ox-text);
    }
    .atl-pro-grid-card-copy {
      font-size: 0.74rem; color: var(--ox-text-muted); line-height: 1.5;
    }
    .atl-pro-grid-empty {
      font-size: 0.73rem; color: var(--ox-text-dim); line-height: 1.5;
      background: oklch(0.115 0.003 60);
      border-radius: 7px; padding: 9px 11px;
      border: 1px solid var(--ox-border);
      margin-top: 2px;
    }
    .atl-pro-grid-pending {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 0.6875rem; font-weight: 600;
      color: oklch(0.72 0.08 80);
      background: oklch(0.65 0.06 80 / 0.08);
      border: 1px solid oklch(0.65 0.06 80 / 0.25);
      border-radius: 20px; padding: 2px 9px;
      margin-top: 3px; width: fit-content;
    }

    /* ── Barras de dispositivo ──────────────────────────────────────────── */
    .atl-device-item {
      display: flex; align-items: center; gap: 8px; margin-bottom: 7px;
    }
    .atl-device-item:last-child { margin-bottom: 0; }
    .atl-device-icon { color: var(--ox-text-muted); flex-shrink: 0; }
    .atl-device-label {
      font-size: 0.74rem; color: var(--ox-text-muted); min-width: 58px;
    }
    .atl-device-bar-wrap {
      flex: 1; background: oklch(0.16 0.005 60);
      border-radius: 3px; height: 5px; overflow: hidden;
    }
    .atl-device-bar-fill {
      height: 100%; border-radius: 3px; background: var(--ox-amber);
      transition: width 0.4s ease;
    }
    .atl-device-count {
      font-size: 0.6875rem; color: var(--ox-text-dim);
      min-width: 24px; text-align: right; font-variant-numeric: tabular-nums;
    }

    /* ── Ranking de tours ───────────────────────────────────────────────── */
    .atl-tour-rank-item {
      display: flex; align-items: center; gap: 9px;
      padding: 7px 0; border-top: 1px solid var(--ox-border);
    }
    .atl-tour-rank-item:first-child { border-top: none; padding-top: 0; }
    .atl-tour-rank-num {
      font-size: 0.6875rem; font-weight: 600; color: var(--ox-text-dim);
      min-width: 16px;
    }
    .atl-tour-rank-name {
      font-size: 0.8rem; color: var(--ox-text); flex: 1;
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .atl-tour-rank-bar-wrap {
      width: 52px; background: oklch(0.16 0.005 60);
      border-radius: 3px; height: 4px;
    }
    .atl-tour-rank-bar-fill {
      height: 100%; border-radius: 3px; background: var(--ox-amber);
    }
    .atl-tour-rank-count {
      font-size: 0.6875rem; color: var(--ox-text-dim);
      min-width: 18px; text-align: right; font-variant-numeric: tabular-nums;
    }

    /* ── Mini chart (Visitas por día card) ──────────────────────────────── */
    .atl-mini-chart {
      display: flex; align-items: flex-end; gap: 3px; height: 32px; margin-top: 4px;
    }
    .atl-mini-bar {
      flex: 1; border-radius: 2px 2px 0 0;
      background: var(--ox-amber); opacity: 0.65; min-height: 2px;
    }
    .atl-mini-bar.is-zero {
      background: oklch(0.22 0.01 60); opacity: 1;
    }

    /* ── Comparativa semanal ────────────────────────────────────────────── */
    .atl-week-compare {
      display: flex; gap: 10px; align-items: stretch; margin-top: 4px;
    }
    .atl-week-col {
      flex: 1; background: oklch(0.115 0.003 60);
      border: 1px solid var(--ox-border); border-radius: 8px;
      padding: 10px; display: flex; flex-direction: column; gap: 2px;
    }
    .atl-week-col-label { font-size: 0.6875rem; color: var(--ox-text-dim); }
    .atl-week-col-val {
      font-family: 'JetBrains Mono', monospace;
      font-size: 1.25rem; font-weight: 700; color: var(--ox-text); line-height: 1;
    }
    .atl-week-col-sub { font-size: 0.6875rem; color: var(--ox-text-dim); }

    /* ── Botón exportación disabled ─────────────────────────────────────── */
    .atl-btn-disabled {
      display: inline-flex; align-items: center; gap: 8px;
      background: oklch(0.16 0.005 60);
      color: var(--ox-text-dim); font-size: 0.8125rem; font-weight: 500;
      padding: 8px 16px; border-radius: 8px;
      border: 1px solid var(--ox-border);
      cursor: not-allowed; text-decoration: none;
      margin-top: 4px; width: fit-content;
    }
    .atl-soon-badge {
      display: inline-flex; align-items: center;
      font-size: 0.625rem; font-weight: 700; text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--ox-text-dim);
      background: oklch(0.16 0.005 60);
      border: 1px solid var(--ox-border);
      border-radius: 20px; padding: 2px 8px; margin-left: 2px;
    }

    /* ── Nota embudo Pro ────────────────────────────────────────────────── */
    .atl-funnel-footer-note {
      margin-top: 14px; padding: 10px 14px;
      border-radius: 8px;
      background: oklch(0.115 0.003 60);
      border: 1px solid var(--ox-border);
      font-size: 0.775rem; color: var(--ox-text-dim); line-height: 1.55;
    }

    /* ══════════════════════════════════════════════════════════════════════
       BUSINESS — sección aspiracional dentro de la vista Pro
    ══════════════════════════════════════════════════════════════════════ */
    .atl-business-section {
      margin-top: 2.25rem;
      border: 1px solid oklch(0.80 0.14 75 / 0.28);
      border-radius: 16px;
      overflow: hidden;
      background: linear-gradient(
        140deg,
        oklch(0.10 0.012 70) 0%,
        oklch(0.08 0.006 60) 55%,
        oklch(0.09 0.010 68) 100%
      );
    }
    .atl-business-header {
      padding: 1.625rem 1.75rem 1.125rem;
      border-bottom: 1px solid oklch(0.80 0.14 75 / 0.15);
    }
    .atl-business-eyebrow {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 0.6875rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.08em;
      color: oklch(0.83 0.14 78);
      background: oklch(0.83 0.14 78 / 0.1);
      border: 1px solid oklch(0.83 0.14 78 / 0.32);
      border-radius: 20px; padding: 3px 11px; margin-bottom: 10px;
    }
    .atl-business-title {
      font-size: clamp(1.05rem, 2vw, 1.3rem);
      font-weight: 700; color: var(--ox-text); margin-bottom: 7px; line-height: 1.25;
    }
    .atl-business-sub {
      font-size: 0.855rem; color: var(--ox-text-muted); line-height: 1.6; max-width: 540px;
    }
    .atl-business-highlight {
      font-size: 0.84rem; color: oklch(0.83 0.14 78);
      font-weight: 500; font-style: italic; margin-top: 9px;
    }
    .atl-business-modules {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
      gap: 1px;
      background: oklch(0.80 0.14 75 / 0.1);
    }
    .atl-business-module {
      padding: 1.0625rem 1.125rem;
      background: oklch(0.09 0.009 68);
      display: flex; flex-direction: column; gap: 5px;
    }
    .atl-business-module-icon { color: oklch(0.83 0.14 78); opacity: 0.75; margin-bottom: 2px; }
    .atl-business-module-title {
      font-size: 0.8125rem; font-weight: 600; color: var(--ox-text);
      display: flex; align-items: center; justify-content: space-between; gap: 6px;
    }
    .atl-business-module-badge {
      display: inline-flex;
      font-size: 0.5625rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.05em;
      color: oklch(0.83 0.14 78 / 0.7);
      background: oklch(0.83 0.14 78 / 0.08);
      border: 1px solid oklch(0.83 0.14 78 / 0.2);
      border-radius: 20px; padding: 1px 7px; white-space: nowrap;
    }
    .atl-business-module-desc {
      font-size: 0.72rem; color: var(--ox-text-dim); line-height: 1.5;
    }
    .atl-business-footer {
      padding: 1.125rem 1.75rem;
      display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 1rem;
      border-top: 1px solid oklch(0.80 0.14 75 / 0.12);
    }
    .atl-business-footer-text { font-size: 0.8125rem; color: var(--ox-text-muted); }
    .atl-business-cta {
      display: inline-flex; align-items: center; gap: 8px;
      background: oklch(0.83 0.14 78);
      color: #000; font-weight: 700; font-size: 0.875rem;
      padding: 9px 20px; border-radius: 8px;
      text-decoration: none; transition: opacity 0.2s;
    }
    .atl-business-cta:hover { opacity: 0.85; }
    .atl-business-already {
      font-size: 0.8125rem; color: oklch(0.83 0.14 78); font-weight: 500;
    }

    /* ── Responsive ─────────────────────────────────────────────────────── */
    @media (max-width: 1000px) {
      .atl-pro-kpi { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
      .atl-page-header { flex-direction: column; }
      .atl-locked-grid { grid-template-columns: 1fr 1fr; }
      .atl-pro-kpi { grid-template-columns: 1fr 1fr; }
      .atl-pro-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 420px) {
      .atl-locked-grid { grid-template-columns: 1fr; }
      .atl-pro-kpi { grid-template-columns: 1fr; }
      .atl-business-modules { grid-template-columns: 1fr; }
      .atl-business-footer { flex-direction: column; align-items: flex-start; }
      .atl-week-compare { flex-direction: column; }
    }
  </style>
</head>
<body>

<?php
// ── Helpers de vista (compartidos por Free y Pro) ─────────────────────────────

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

/** Etiqueta del día en español abreviado (3 letras) */
function dayLabel(string $ymd): string {
    $days = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié',
             'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb', 'Sun' => 'Dom'];
    $en = date('D', strtotime($ymd));
    return $days[$en] ?? $en;
}

/** Etiqueta corta: inicial del día (1 letra) */
function dayLabelShort(string $ymd): string {
    $days = ['Mon' => 'L', 'Tue' => 'M', 'Wed' => 'X',
             'Thu' => 'J', 'Fri' => 'V', 'Sat' => 'S', 'Sun' => 'D'];
    $en = date('D', strtotime($ymd));
    return $days[$en] ?? '?';
}

// ── Datos 7 días (siempre, usado en gráfico Free y mini-chart Pro) ───────────
$chartDays = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $chartDays[$date] = $scansByDay[$date] ?? 0;
}
$maxCount    = max(1, max($chartDays));
$hasAnyScans = array_sum($chartDays) > 0;

// ── Datos y cálculos exclusivos de Pro ───────────────────────────────────────
if ($isPro) {
    // Gráfico 14 días
    $chartDays14 = [];
    for ($i = 13; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $chartDays14[$date] = $scansByDay14[$date] ?? 0;
    }
    $maxCount14    = max(1, max($chartDays14));
    $hasAnyScans14 = array_sum($chartDays14) > 0;

    // Tendencia semanal
    $scansLast7 = (int) $weekComparison['last7'];
    $scansPrev7 = (int) $weekComparison['prev7'];
    $trendDiff  = $scansLast7 - $scansPrev7;
    $trendPct   = ($scansPrev7 > 0) ? (int) round(($trendDiff / $scansPrev7) * 100) : null;
    $trendDir   = ($trendDiff > 0) ? 'up' : (($trendDiff < 0) ? 'down' : 'flat');

    // Dispositivos
    $totalDeviceScans = array_sum($deviceCounts);
    $mobileCount  = (int) ($deviceCounts['mobile']  ?? 0);
    $tabletCount  = (int) ($deviceCounts['tablet']  ?? 0);
    $desktopCount = (int) ($deviceCounts['desktop'] ?? 0);
    $unknownCount = (int) ($deviceCounts['unknown'] ?? 0);

    // Ranking de tours
    $tourRankMax     = max(1, (int) ($tourRanking[0]['scan_count'] ?? 0));
    $tourHasAnyScans = !empty($tourRanking) && (int) ($tourRanking[0]['scan_count'] ?? 0) > 0;
}
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

<?php if ($isPro): ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     EXPERIENCIA PRO — vista completa desbloqueada
     Métricas reales de qr_scans, device_type y tour ranking.
     Estado vacío honesto donde no hay tracking real todavía.
══════════════════════════════════════════════════════════════════════════ -->

      <!-- ── Cabecera Pro ── -->
      <div class="atl-page-header">
        <div class="atl-page-header-left">
          <h2 class="atl-page-title">Analíticas</h2>
          <p class="atl-page-sub">Mide el rendimiento real de tus QRs, tours y puntos de contacto.</p>
          <div class="atl-page-badges">
            <span class="db-badge db-badge--plan">Plan <?= htmlspecialchars($planLabel) ?></span>
            <span class="db-badge db-badge--plan" style="font-size:0.6875rem;">QR profesional</span>
            <span class="db-badge db-badge--plan" style="font-size:0.6875rem;">Analíticas básicas</span>
          </div>
        </div>
        <?php if (!$isBusinessPlan && $userRole !== 'admin'): ?>
        <a href="/precios" class="db-btn-brand-outline" style="white-space:nowrap;align-self:flex-start;font-size:0.8125rem;">
          <i data-lucide="sparkles" width="13" height="13" aria-hidden="true"></i>
          Mejorar a Business
        </a>
        <?php endif; ?>
      </div>

      <!-- ── A) Fila de 4 KPIs Pro ── -->
      <div class="atl-pro-kpi" role="region" aria-label="KPIs principales">

        <!-- KPI 1: Escaneos totales -->
        <div class="atl-pro-kpi-card">
          <div class="atl-pro-kpi-icon" aria-hidden="true">
            <i data-lucide="scan-line" width="20" height="20"></i>
          </div>
          <div class="atl-pro-kpi-val <?= $totalScans === 0 ? 'is-empty' : '' ?>">
            <?= $totalScans > 0 ? $totalScans : '—' ?>
          </div>
          <div class="atl-pro-kpi-label">Escaneos QR · total</div>
          <div class="atl-pro-kpi-note">
            <?= $totalScans > 0
              ? 'Acumulados desde el primer escaneo registrado.'
              : 'Comparte tu QR profesional para empezar a medir.' ?>
          </div>
        </div>

        <!-- KPI 2: Esta semana + tendencia -->
        <div class="atl-pro-kpi-card">
          <div class="atl-pro-kpi-icon" aria-hidden="true">
            <i data-lucide="trending-up" width="20" height="20"></i>
          </div>
          <div class="atl-pro-kpi-val <?= $scansLast7 === 0 ? 'is-empty' : '' ?>">
            <?= $scansLast7 > 0 ? $scansLast7 : '—' ?>
          </div>
          <div class="atl-pro-kpi-label">Escaneos · últimos 7 días</div>
          <?php if ($scansLast7 === 0 && $scansPrev7 === 0): ?>
            <span class="atl-trend-badge flat" aria-label="Sin actividad reciente">Sin actividad reciente</span>
          <?php elseif ($scansLast7 > 0 && $scansPrev7 === 0): ?>
            <span class="atl-trend-badge up" aria-label="Primeros datos registrados">
              <i data-lucide="arrow-up" width="10" height="10"></i>
              Primeros datos
            </span>
          <?php elseif ($trendPct !== null && $trendDiff > 0): ?>
            <span class="atl-trend-badge up" aria-label="Subida del <?= $trendPct ?>% respecto a la semana anterior">
              <i data-lucide="arrow-up" width="10" height="10"></i>
              +<?= $trendPct ?>% vs semana anterior
            </span>
          <?php elseif ($trendPct !== null && $trendDiff < 0): ?>
            <span class="atl-trend-badge down" aria-label="Bajada del <?= abs($trendPct) ?>% respecto a la semana anterior">
              <i data-lucide="arrow-down" width="10" height="10"></i>
              <?= $trendPct ?>% vs semana anterior
            </span>
          <?php else: ?>
            <span class="atl-trend-badge flat" aria-label="Sin cambio respecto a la semana anterior">
              Igual que la semana anterior
            </span>
          <?php endif; ?>
        </div>

        <!-- KPI 3: Conversión QR → Tour — métrica Pro, pendiente de tracking de apertura -->
        <div class="atl-pro-kpi-card">
          <div class="atl-pro-kpi-icon" aria-hidden="true">
            <i data-lucide="arrow-right-circle" width="20" height="20"></i>
          </div>
          <div class="atl-pro-kpi-val is-empty">—</div>
          <div class="atl-pro-kpi-label">Conversión QR → Tour</div>
          <div class="atl-pro-kpi-note">
            Necesitamos tracking de apertura del tour para calcular esta métrica.
          </div>
          <span class="atl-trend-badge flat" style="margin-top:5px;font-size:0.625rem;">
            <i data-lucide="clock" width="9" height="9"></i>
            Pendiente de tracking
          </span>
        </div>

        <!-- KPI 4: Último escaneo -->
        <div class="atl-pro-kpi-card">
          <div class="atl-pro-kpi-icon" aria-hidden="true">
            <i data-lucide="clock" width="20" height="20"></i>
          </div>
          <div class="atl-pro-kpi-val is-empty" style="font-size:<?= $lastScanAt ? '1rem' : '1.5rem' ?>;">
            <?= $lastScanAt ? htmlspecialchars(formatScanDate($lastScanAt)) : '—' ?>
          </div>
          <div class="atl-pro-kpi-label">Último escaneo registrado</div>
          <div class="atl-pro-kpi-note">
            <?= $lastScanAt
              ? 'Última señal de interés de un cliente real.'
              : 'Sin escaneos todavía en ningún QR.' ?>
          </div>
        </div>

      </div>

      <!-- ── B) Gráfica principal 14 días ── -->
      <div class="atl-card" role="region" aria-label="Actividad de los últimos 14 días">
        <p class="atl-card-title">
          <i data-lucide="bar-chart-2" width="15" height="15" aria-hidden="true"></i>
          Actividad · últimos 14 días
          <span style="margin-left:auto;font-size:0.6875rem;font-weight:400;color:var(--ox-text-dim);text-transform:none;letter-spacing:0;">
            Escaneos, aperturas y señales de actividad agrupadas por día.
          </span>
        </p>

        <?php if (!$hasAnyScans14): ?>
          <div class="atl-chart-empty">
            <p class="atl-empty-title">Aún no hay actividad suficiente.</p>
            <p class="atl-empty-desc">
              Comparte tu QR profesional para empezar a medir la actividad de tus tours.
              Cuando lleguen escaneos aparecerán aquí día a día.
            </p>
            <div class="atl-empty-steps" aria-label="Pasos para empezar">
              <div class="atl-empty-step">
                <div class="atl-empty-step-num">1</div>
                <span class="atl-empty-step-text">Descarga el QR profesional</span>
              </div>
              <div class="atl-empty-step">
                <div class="atl-empty-step-num">2</div>
                <span class="atl-empty-step-text">Colócalo en tu negocio</span>
              </div>
              <div class="atl-empty-step">
                <div class="atl-empty-step-num">3</div>
                <span class="atl-empty-step-text">Los datos aparecen aquí</span>
              </div>
            </div>
          </div>
        <?php else: ?>

          <!-- Conteos -->
          <div class="atl-chart14-counts-row" aria-hidden="true">
            <?php foreach ($chartDays14 as $date => $count): ?>
            <span class="atl-chart14-count-val"><?= $count > 0 ? $count : '' ?></span>
            <?php endforeach; ?>
          </div>

          <!-- Barras -->
          <div class="atl-chart14-bars-row" role="img" aria-label="Gráfico 14 días de escaneos QR">
            <?php foreach ($chartDays14 as $date => $count): ?>
            <?php $pct14 = $count > 0 ? max(6, (int) round(($count / $maxCount14) * 100)) : 3; ?>
            <div class="atl-chart14-bar <?= $count === 0 ? 'is-zero' : '' ?>"
                 style="height:<?= $pct14 ?>%;"
                 title="<?= htmlspecialchars(date('d/m', strtotime($date))) ?>: <?= $count ?> escaneo<?= $count !== 1 ? 's' : '' ?>">
            </div>
            <?php endforeach; ?>
          </div>

          <!-- Etiquetas -->
          <div class="atl-chart14-labels-row" aria-hidden="true">
            <?php foreach ($chartDays14 as $date => $count): ?>
            <span class="atl-chart14-day-label"><?= htmlspecialchars(dayLabelShort($date)) ?></span>
            <?php endforeach; ?>
          </div>

        <?php endif; ?>

        <!-- Leyenda -->
        <div class="atl-chart-legend">
          <div class="atl-chart-legend-item">
            <div class="atl-chart-legend-dot" style="background:var(--ox-amber);"></div>
            <span>Escaneos QR · datos reales</span>
          </div>
          <div class="atl-chart-legend-item" style="opacity:0.55;">
            <div class="atl-chart-legend-dot" style="background:oklch(0.60 0.10 200);"></div>
            <span>Aperturas del tour · pendiente de tracking</span>
          </div>
        </div>

        <?php if ($hasAnyScans14): ?>
        <div class="atl-chart-insight">
          <strong style="color:var(--ox-text-muted);">Nota:</strong>
          Los datos reflejan únicamente escaneos QR registrados por el sistema.
          El tracking de aperturas directas del tour se activará en una próxima versión de Oxphyre.
        </div>
        <?php endif; ?>
      </div>

      <!-- ── C) Embudo Pro ── -->
      <div class="atl-card" role="region" aria-label="Embudo de visita Pro">
        <p class="atl-card-title">
          <i data-lucide="filter" width="15" height="15" aria-hidden="true"></i>
          Embudo de visita
          <span style="margin-left:auto;font-size:0.6875rem;font-weight:400;color:var(--ox-text-dim);text-transform:none;letter-spacing:0;">
            Del escaneo del QR a la interacción con el tour.
          </span>
        </p>

        <div class="atl-funnel">

          <!-- Paso 1: QR scan — dato real -->
          <div class="atl-funnel-step">
            <div class="atl-funnel-icon" aria-hidden="true">
              <i data-lucide="qr-code" width="18" height="18"></i>
            </div>
            <div class="atl-funnel-body">
              <div class="atl-funnel-label">Cliente escanea el QR</div>
              <div class="atl-funnel-note">
                <?= $totalScans ?> escaneo<?= $totalScans !== 1 ? 's' : '' ?> acumulado<?= $totalScans !== 1 ? 's' : '' ?> —
                dato real del sistema.
              </div>
            </div>
            <span class="atl-funnel-badge-active" aria-label="Disponible con datos reales">
              <i data-lucide="check" width="11" height="11" aria-hidden="true"></i>
              Disponible
            </span>
          </div>

          <!-- Paso 2: Apertura del tour — sin tracking todavía -->
          <div class="atl-funnel-step is-pending">
            <div class="atl-funnel-icon" aria-hidden="true">
              <i data-lucide="eye" width="18" height="18"></i>
            </div>
            <div class="atl-funnel-body">
              <div class="atl-funnel-label">Cliente visita el tour</div>
              <div class="atl-funnel-note">Pendiente de datos de apertura — sin tracking real todavía.</div>
            </div>
            <span class="atl-funnel-badge-pending" aria-label="Pendiente de tracking">
              <i data-lucide="clock" width="11" height="11" aria-hidden="true"></i>
              Pendiente
            </span>
          </div>

          <!-- Paso 3: Interacción — roadmap -->
          <div class="atl-funnel-step is-pending">
            <div class="atl-funnel-icon" aria-hidden="true">
              <i data-lucide="heart" width="18" height="18"></i>
            </div>
            <div class="atl-funnel-body">
              <div class="atl-funnel-label">Cliente interactúa con el tour</div>
              <div class="atl-funnel-note">Señales futuras de interacción dentro del visor: clics en hotspots y zonas destacadas.</div>
            </div>
            <span class="atl-funnel-badge-roadmap" aria-label="En roadmap">
              Roadmap Pro
            </span>
          </div>

        </div>

        <div class="atl-funnel-footer-note">
          Pro mide el rendimiento básico. <strong style="color:var(--ox-text-muted);">Business</strong>
          desbloquea intención comercial, atribución y recomendaciones avanzadas.
        </div>
      </div>

      <!-- ── D) Grid de analíticas Pro desbloqueadas ── -->
      <div class="atl-card" role="region" aria-label="Analíticas Pro incluidas">
        <p class="atl-card-title">
          <i data-lucide="layout-grid" width="15" height="15" aria-hidden="true"></i>
          Analíticas Pro incluidas
        </p>
        <p style="font-size:0.8125rem;color:var(--ox-text-muted);margin-bottom:1rem;margin-top:-0.5rem;">
          Métricas activas en tu plan. Las que aún no tienen datos muestran su estado real.
        </p>

        <div class="atl-pro-grid">

          <!-- Card 1: Actividad por día — mini chart real de escaneos QR -->
          <div class="atl-pro-grid-card">
            <div class="atl-pro-grid-card-header">
              <div class="atl-pro-grid-card-icon" aria-hidden="true">
                <i data-lucide="trending-up" width="17" height="17"></i>
              </div>
              <div class="atl-pro-grid-card-title">Actividad por día</div>
            </div>
            <div class="atl-pro-grid-card-copy">Detecta qué días generan más interés en tus QRs.</div>

            <?php if ($hasAnyScans): ?>
              <!-- Mini chart con datos reales de los últimos 7 días -->
              <div class="atl-mini-chart" role="img" aria-label="Mini gráfico 7 días de escaneos">
                <?php foreach ($chartDays as $date => $count):
                    $miniMax = max(1, max($chartDays));
                    $miniPct = $count > 0 ? max(10, (int) round(($count / $miniMax) * 100)) : 5;
                ?>
                <div class="atl-mini-bar <?= $count === 0 ? 'is-zero' : '' ?>"
                     style="height:<?= $miniPct ?>%;"
                     title="<?= htmlspecialchars(dayLabel($date)) ?>: <?= $count ?>">
                </div>
                <?php endforeach; ?>
              </div>
              <div style="font-size:0.68rem;color:var(--ox-text-dim);margin-top:3px;">Escaneos QR · últimos 7 días</div>
            <?php else: ?>
              <div class="atl-pro-grid-empty">Sin escaneos en los últimos 7 días. Comparte tu QR para ver actividad aquí.</div>
            <?php endif; ?>

            <div class="atl-pro-grid-pending" aria-label="Aperturas del tour pendientes">
              <i data-lucide="clock" width="10" height="10"></i>
              Aperturas del tour: pendiente de tracking
            </div>
          </div>

          <!-- Card 2: Dispositivos — dato real de device_type -->
          <div class="atl-pro-grid-card">
            <div class="atl-pro-grid-card-header">
              <div class="atl-pro-grid-card-icon" aria-hidden="true">
                <i data-lucide="smartphone" width="17" height="17"></i>
              </div>
              <div class="atl-pro-grid-card-title">Dispositivos</div>
            </div>
            <div class="atl-pro-grid-card-copy">Cómo acceden tus clientes al tour desde tu QR.</div>

            <?php if ($totalDeviceScans > 0): ?>
              <!-- Barras reales por device_type -->
              <?php if ($mobileCount > 0): ?>
              <div class="atl-device-item">
                <div class="atl-device-icon" aria-hidden="true"><i data-lucide="smartphone" width="13" height="13"></i></div>
                <span class="atl-device-label">Móvil</span>
                <div class="atl-device-bar-wrap">
                  <div class="atl-device-bar-fill" style="width:<?= (int) round(($mobileCount / $totalDeviceScans) * 100) ?>%;"></div>
                </div>
                <span class="atl-device-count"><?= $mobileCount ?></span>
              </div>
              <?php endif; ?>
              <?php if ($desktopCount > 0): ?>
              <div class="atl-device-item">
                <div class="atl-device-icon" aria-hidden="true"><i data-lucide="monitor" width="13" height="13"></i></div>
                <span class="atl-device-label">Escritorio</span>
                <div class="atl-device-bar-wrap">
                  <div class="atl-device-bar-fill" style="width:<?= (int) round(($desktopCount / $totalDeviceScans) * 100) ?>%;"></div>
                </div>
                <span class="atl-device-count"><?= $desktopCount ?></span>
              </div>
              <?php endif; ?>
              <?php if ($tabletCount > 0): ?>
              <div class="atl-device-item">
                <div class="atl-device-icon" aria-hidden="true"><i data-lucide="tablet" width="13" height="13"></i></div>
                <span class="atl-device-label">Tablet</span>
                <div class="atl-device-bar-wrap">
                  <div class="atl-device-bar-fill" style="width:<?= (int) round(($tabletCount / $totalDeviceScans) * 100) ?>%;"></div>
                </div>
                <span class="atl-device-count"><?= $tabletCount ?></span>
              </div>
              <?php endif; ?>
              <?php if ($unknownCount > 0): ?>
              <div class="atl-device-item">
                <div class="atl-device-icon" aria-hidden="true"><i data-lucide="help-circle" width="13" height="13"></i></div>
                <span class="atl-device-label">Otro</span>
                <div class="atl-device-bar-wrap">
                  <div class="atl-device-bar-fill" style="width:<?= (int) round(($unknownCount / $totalDeviceScans) * 100) ?>%;"></div>
                </div>
                <span class="atl-device-count"><?= $unknownCount ?></span>
              </div>
              <?php endif; ?>
              <div style="font-size:0.67rem;color:var(--ox-text-dim);margin-top:2px;">
                Basado en <?= $totalDeviceScans ?> escaneo<?= $totalDeviceScans !== 1 ? 's' : '' ?> con device detectado.
              </div>
            <?php else: ?>
              <div class="atl-pro-grid-empty">
                Tracking de dispositivo activo. Sin escaneos todavía.<br>
                Cuando lleguen escaneos, verás aquí la distribución real de móvil, escritorio y tablet.
              </div>
            <?php endif; ?>
          </div>

          <!-- Card 3: Rendimiento por tour — dato real -->
          <div class="atl-pro-grid-card">
            <div class="atl-pro-grid-card-header">
              <div class="atl-pro-grid-card-icon" aria-hidden="true">
                <i data-lucide="bar-chart" width="17" height="17"></i>
              </div>
              <div class="atl-pro-grid-card-title">Rendimiento por tour</div>
            </div>
            <div class="atl-pro-grid-card-copy">Compara qué tours generan más escaneos QR.</div>

            <?php if (empty($tourRanking)): ?>
              <div class="atl-pro-grid-empty">Crea y publica tours para ver su rendimiento aquí.</div>
            <?php elseif (!$tourHasAnyScans): ?>
              <!-- Tours existen pero ninguno tiene escaneos -->
              <?php foreach ($tourRanking as $idx => $tr): ?>
              <div class="atl-tour-rank-item">
                <span class="atl-tour-rank-num"><?= $idx + 1 ?></span>
                <span class="atl-tour-rank-name" title="<?= htmlspecialchars((string)$tr['tour_name']) ?>">
                  <?= htmlspecialchars((string)$tr['tour_name']) ?>
                </span>
                <div class="atl-tour-rank-bar-wrap">
                  <div class="atl-tour-rank-bar-fill" style="width:0%;"></div>
                </div>
                <span class="atl-tour-rank-count">0</span>
              </div>
              <?php endforeach; ?>
              <div style="font-size:0.67rem;color:var(--ox-text-dim);margin-top:4px;">
                Ningún tour ha recibido escaneos todavía.
              </div>
            <?php else: ?>
              <!-- Ranking real -->
              <?php foreach ($tourRanking as $idx => $tr):
                  $tScan  = (int) $tr['scan_count'];
                  $tPct   = $tourRankMax > 0 ? (int) round(($tScan / $tourRankMax) * 100) : 0;
              ?>
              <div class="atl-tour-rank-item">
                <span class="atl-tour-rank-num"><?= $idx + 1 ?></span>
                <span class="atl-tour-rank-name" title="<?= htmlspecialchars((string)$tr['tour_name']) ?>">
                  <?= htmlspecialchars((string)$tr['tour_name']) ?>
                </span>
                <div class="atl-tour-rank-bar-wrap">
                  <div class="atl-tour-rank-bar-fill" style="width:<?= $tPct ?>%;"></div>
                </div>
                <span class="atl-tour-rank-count"><?= $tScan ?></span>
              </div>
              <?php endforeach; ?>
              <div style="font-size:0.67rem;color:var(--ox-text-dim);margin-top:2px;">
                Escaneos QR por tour — datos acumulados reales.
              </div>
            <?php endif; ?>
          </div>

          <!-- Card 4: Puntos de contacto QR — sin tracking por canal todavía -->
          <div class="atl-pro-grid-card">
            <div class="atl-pro-grid-card-header">
              <div class="atl-pro-grid-card-icon" aria-hidden="true">
                <i data-lucide="map-pin" width="17" height="17"></i>
              </div>
              <div class="atl-pro-grid-card-title">Puntos de contacto QR</div>
            </div>
            <div class="atl-pro-grid-card-copy">
              Compara campañas y ubicaciones de QR para saber qué origen convierte mejor.
            </div>
            <div class="atl-pro-grid-empty">
              Disponible cuando se creen QRs diferenciados por canal o ubicación (escaparate, mesa, redes, tarjeta).
            </div>
            <div class="atl-pro-grid-pending">
              <i data-lucide="clock" width="10" height="10"></i>
              Pendiente de tracking por canal
            </div>
          </div>

          <!-- Card 5: Evolución semanal — dato real -->
          <div class="atl-pro-grid-card">
            <div class="atl-pro-grid-card-header">
              <div class="atl-pro-grid-card-icon" aria-hidden="true">
                <i data-lucide="calendar" width="17" height="17"></i>
              </div>
              <div class="atl-pro-grid-card-title">Evolución semanal</div>
            </div>
            <div class="atl-pro-grid-card-copy">Comparativa semana a semana del interés en tus QRs.</div>

            <?php if ($scansLast7 === 0 && $scansPrev7 === 0): ?>
              <div class="atl-pro-grid-empty">
                Necesitamos más actividad para comparar periodos.
                Comparte tu QR para ver la evolución aquí.
              </div>
            <?php else: ?>
              <div class="atl-week-compare">
                <div class="atl-week-col">
                  <div class="atl-week-col-label">Esta semana</div>
                  <div class="atl-week-col-val"><?= $scansLast7 ?></div>
                  <div class="atl-week-col-sub">escaneos</div>
                </div>
                <div class="atl-week-col">
                  <div class="atl-week-col-label">Semana anterior</div>
                  <div class="atl-week-col-val"><?= $scansPrev7 ?></div>
                  <div class="atl-week-col-sub">escaneos</div>
                </div>
              </div>
              <?php if ($trendPct !== null): ?>
              <div style="font-size:0.67rem;color:var(--ox-text-dim);margin-top:3px;">
                <?php if ($trendDiff > 0): ?>
                  +<?= $trendPct ?>% respecto a la semana anterior.
                <?php elseif ($trendDiff < 0): ?>
                  <?= $trendPct ?>% respecto a la semana anterior.
                <?php else: ?>
                  Sin cambio respecto a la semana anterior.
                <?php endif; ?>
              </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <!-- Card 6: Exportación — deshabilitada (próximamente) -->
          <div class="atl-pro-grid-card">
            <div class="atl-pro-grid-card-header">
              <div class="atl-pro-grid-card-icon" aria-hidden="true">
                <i data-lucide="download" width="17" height="17"></i>
              </div>
              <div class="atl-pro-grid-card-title">
                Exportación básica
                <span class="atl-soon-badge">Próximamente</span>
              </div>
            </div>
            <div class="atl-pro-grid-card-copy">
              Descarga informes básicos de actividad cuando el módulo esté activado.
            </div>
            <span class="atl-btn-disabled" aria-disabled="true" role="button" tabindex="-1">
              <i data-lucide="download" width="14" height="14" aria-hidden="true"></i>
              Descargar informe
            </span>
          </div>

        </div>
      </div>

      <!-- ── E) Sección Business aspiracional ── -->
      <div class="atl-business-section" role="region" aria-label="Inteligencia avanzada Business">

        <div class="atl-business-header">
          <div class="atl-business-eyebrow">
            <i data-lucide="sparkles" width="11" height="11"></i>
            Business
          </div>
          <h3 class="atl-business-title">Inteligencia avanzada con Business</h3>
          <p class="atl-business-sub">
            Convierte visitas en decisiones comerciales con señales de intención,
            atribución y recomendaciones automáticas.
          </p>
          <p class="atl-business-highlight">
            Business no añade solo más métricas. Añade contexto, intención y recomendaciones para vender más.
          </p>
        </div>

        <div class="atl-business-modules">

          <div class="atl-business-module">
            <div class="atl-business-module-icon" aria-hidden="true">
              <i data-lucide="flame" width="18" height="18"></i>
            </div>
            <div class="atl-business-module-title">
              Heatmaps de atención
              <span class="atl-business-module-badge">Business</span>
            </div>
            <div class="atl-business-module-desc">
              Visualiza qué zonas del tour concentran más interés e interacción de los visitantes.
            </div>
          </div>

          <div class="atl-business-module">
            <div class="atl-business-module-icon" aria-hidden="true">
              <i data-lucide="target" width="18" height="18"></i>
            </div>
            <div class="atl-business-module-title">
              Scoring de intención
              <span class="atl-business-module-badge">Business</span>
            </div>
            <div class="atl-business-module-desc">
              Detecta visitantes con alto interés según profundidad, repetición e interacción.
            </div>
          </div>

          <div class="atl-business-module">
            <div class="atl-business-module-icon" aria-hidden="true">
              <i data-lucide="git-branch" width="18" height="18"></i>
            </div>
            <div class="atl-business-module-title">
              Atribución avanzada
              <span class="atl-business-module-badge">Business</span>
            </div>
            <div class="atl-business-module-desc">
              Compara campañas, canales y ubicaciones QR para saber qué origen convierte mejor.
            </div>
          </div>

          <div class="atl-business-module">
            <div class="atl-business-module-icon" aria-hidden="true">
              <i data-lucide="funnel" width="18" height="18"></i>
            </div>
            <div class="atl-business-module-title">
              Embudo comercial completo
              <span class="atl-business-module-badge">Business</span>
            </div>
            <div class="atl-business-module-desc">
              Mide el recorrido desde QR hasta CTA, contacto o reserva dentro del tour.
            </div>
          </div>

          <div class="atl-business-module">
            <div class="atl-business-module-icon" aria-hidden="true">
              <i data-lucide="brain-circuit" width="18" height="18"></i>
            </div>
            <div class="atl-business-module-title">
              Recomendaciones automáticas
              <span class="atl-business-module-badge">Business</span>
            </div>
            <div class="atl-business-module-desc">
              Recibe sugerencias para mejorar ubicación de QRs, escenas y contenido del tour.
            </div>
          </div>

          <div class="atl-business-module">
            <div class="atl-business-module-icon" aria-hidden="true">
              <i data-lucide="sliders-horizontal" width="18" height="18"></i>
            </div>
            <div class="atl-business-module-title">
              Segmentación avanzada
              <span class="atl-business-module-badge">Business</span>
            </div>
            <div class="atl-business-module-desc">
              Filtra por tour, QR, campaña, dispositivo, horario, recurrencia y comportamiento.
            </div>
          </div>

        </div>

        <div class="atl-business-footer">
          <?php if ($isBusinessPlan): ?>
            <span class="atl-business-footer-text">
              Estas funcionalidades avanzadas están en desarrollo para tu plan Business. Las irás viendo activadas en las próximas versiones.
            </span>
            <span class="atl-business-already">
              <i data-lucide="check-circle" width="15" height="15" style="display:inline;vertical-align:-3px;margin-right:4px;"></i>
              Ya tienes Business
            </span>
          <?php else: ?>
            <span class="atl-business-footer-text">
              Disponible en el plan Business — analytics de nivel comercial para tu negocio.
            </span>
            <a href="/precios" class="atl-business-cta">
              <i data-lucide="arrow-right" width="15" height="15"></i>
              Explorar Business
            </a>
          <?php endif; ?>
        </div>

      </div>

      <!-- ── Nota de privacidad ── -->
      <div class="atl-privacy-note" role="note">
        <i data-lucide="shield" width="15" height="15" aria-hidden="true"></i>
        <span>
          Oxphyre usa tracking básico y privacidad por diseño: no se registran IPs completas ni user agents.
          Los escaneos se identifican mediante hash pseudonimizado. El tipo de dispositivo se detecta del user agent
          sin almacenarlo. Los datos son agregados y nunca se vinculan a personas identificables.
        </span>
      </div>

<?php else: ?>
<!-- ══════════════════════════════════════════════════════════════════════════
     EXPERIENCIA FREE — vista original intacta
══════════════════════════════════════════════════════════════════════════ -->

      <!-- ── Encabezado Free ── -->
      <div class="atl-page-header">
        <div class="atl-page-header-left">
          <h2 class="atl-page-title">Analíticas</h2>
          <p class="atl-page-sub">Comprueba si tu QR y tu tour empiezan a generar interés.</p>
          <div class="atl-page-badges">
            <span class="db-badge db-badge--plan">Plan <?= htmlspecialchars($planLabel) ?></span>
            <span class="db-badge db-badge--draft" style="font-size:0.6875rem;">QR tracking · uso del plan</span>
          </div>
        </div>
        <a href="/precios" class="db-btn-brand-outline" style="white-space:nowrap;align-self:flex-start;">
          <i data-lucide="zap" width="14" height="14" aria-hidden="true"></i>
          Mejorar a Pro
        </a>
      </div>

      <!-- ── KPIs Free ── -->
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

      <!-- ── Gráfico 7 días Free ── -->
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

      <!-- ── Embudo Free ── -->
      <div class="atl-card" role="region" aria-label="Embudo de visita">
        <p class="atl-card-title">
          <i data-lucide="filter" width="15" height="15" aria-hidden="true"></i>
          Tu embudo de visita
        </p>

        <div class="atl-funnel">

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

      <!-- ── Cards Pro bloqueadas (Free) ── -->
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

      <!-- ── Nota de privacidad Free ── -->
      <div class="atl-privacy-note" role="note">
        <i data-lucide="shield" width="15" height="15" aria-hidden="true"></i>
        <span>Oxphyre usa métricas agregadas y tracking básico del QR para evitar exponer datos personales innecesarios. No se registran IP completas ni user agents.</span>
      </div>

<?php endif; // isPro vs Free ?>

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

  const openSidebar  = () => {
    sidebar.classList.add('is-open');
    overlay.classList.add('is-visible');
    hamburger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
  };
  const closeSidebar = () => {
    sidebar.classList.remove('is-open');
    overlay.classList.remove('is-visible');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };

  hamburger.addEventListener('click', openSidebar);
  closeBtn.addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && sidebar.classList.contains('is-open')) closeSidebar();
  });
});
</script>

</body>
</html>
