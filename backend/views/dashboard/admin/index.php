<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Admin — Oxphyre</title>
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

<?php
// Mapas de etiquetas para usar en tablas (solo lectura)
$planLabels = [1 => 'Free', 2 => 'Pro', 3 => 'Business'];
$roleLabels = [
    'business_free'     => 'Free',
    'business_pro'      => 'Pro',
    'business_business' => 'Business',
    'admin'             => 'Admin',
];
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
      <a href="/dashboard/analiticas"    class="db-nav-item">
        <i data-lucide="bar-chart-2"     width="18" height="18" aria-hidden="true"></i>
        <span>Analíticas</span>
      </a>
      <a href="/dashboard/configuracion" class="db-nav-item">
        <i data-lucide="settings"        width="18" height="18" aria-hidden="true"></i>
        <span>Configuración</span>
      </a>
      <a href="/dashboard/admin"         class="db-nav-item active" aria-current="page">
        <i data-lucide="shield"          width="18" height="18" aria-hidden="true"></i>
        <span>Admin</span>
      </a>
    </nav>
    <div class="db-sidebar-footer">
      <div class="db-plan-badge" aria-label="Rol: Admin">
        <span class="db-plan-label">Rol</span>
        <span class="db-plan-name">Admin</span>
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
    <h1 class="db-topbar-title">Panel Admin</h1>
    <div class="db-avatar" aria-label="Usuario: <?= $userName ?>" title="<?= $userName ?> · <?= $userEmail ?>">
      <?= $userInitial ?>
    </div>
  </header>

  <!-- ── Contenido principal ── -->
  <main class="db-main" id="db-main">
    <div class="db-page">

      <!-- Cabecera -->
      <div class="db-welcome">
        <h2 class="db-welcome-heading">Panel de <em>supervisión</em></h2>
        <p class="db-welcome-sub">Vista global de la plataforma. Solo lectura — no hay acciones de modificación en esta versión.</p>
      </div>

      <!-- ── Métricas globales ── -->
      <div class="db-metrics" role="region" aria-label="Métricas globales">

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="users" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['users'] ?></div>
          <div class="db-metric-label">Usuarios registrados</div>
          <div class="db-metric-note">Total acumulado</div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="building-2" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['businesses'] ?></div>
          <div class="db-metric-label">Negocios activos</div>
          <div class="db-metric-note">Sin soft delete</div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="play-circle" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['tours'] ?></div>
          <div class="db-metric-label">Tours activos</div>
          <div class="db-metric-note">Sin soft delete</div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="map-pin" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['positions'] ?></div>
          <div class="db-metric-label">Posiciones</div>
          <div class="db-metric-note">Sin soft delete</div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="image" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['photos'] ?></div>
          <div class="db-metric-label">Fotos subidas</div>
          <div class="db-metric-note">Sin soft delete</div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="qr-code" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['qr_codes'] ?></div>
          <div class="db-metric-label">QR generados</div>
          <div class="db-metric-note">Total histórico</div>
        </div>

        <div class="db-metric-card">
          <div class="db-metric-icon" aria-hidden="true">
            <i data-lucide="scan-line" width="20" height="20"></i>
          </div>
          <div class="db-metric-value"><?= (int) $stats['qr_scans'] ?></div>
          <div class="db-metric-label">Escaneos QR</div>
          <div class="db-metric-note">Total histórico</div>
        </div>

      </div><!-- /db-metrics -->

      <!-- ── Últimos usuarios ── -->
      <section style="margin-top:2.5rem;" aria-labelledby="admin-users-heading">

        <div class="db-list-header" style="margin-bottom:1rem;">
          <h3 class="db-list-title" id="admin-users-heading">
            <i data-lucide="users" width="16" height="16" aria-hidden="true" style="vertical-align:-2px;margin-right:0.4rem;"></i>
            Últimos usuarios registrados
          </h3>
        </div>

        <?php if (empty($latestUsers)): ?>
          <p style="color:var(--ox-text-muted);font-size:0.875rem;">No hay usuarios registrados todavía.</p>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
              <thead>
                <tr style="border-bottom:1px solid var(--ox-border);">
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">#</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Nombre</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Email</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Rol / Plan</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Email verificado</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Registro</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latestUsers as $u): ?>
                  <tr style="border-bottom:1px solid var(--ox-border);transition:background 0.15s;"
                      onmouseover="this.style.background='var(--ox-bg-hover)'"
                      onmouseout="this.style.background=''">
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-dim);font-family:'JetBrains Mono',monospace;font-size:0.8rem;"><?= (int) $u['id'] ?></td>
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text);"><?= htmlspecialchars($u['name']) ?></td>
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-muted);font-size:0.82rem;"><?= htmlspecialchars($u['email']) ?></td>
                    <td style="padding:0.6rem 0.75rem;">
                      <?php
                        $rLabel = $roleLabels[$u['role']] ?? htmlspecialchars($u['role']);
                        $rColor = match($u['role']) {
                            'admin'             => 'var(--ox-amber)',
                            'business_pro'      => 'oklch(0.78 0.18 145)',
                            'business_business' => 'oklch(0.78 0.16 220)',
                            default             => 'var(--ox-text-muted)',
                        };
                      ?>
                      <span class="db-badge" style="color:<?= $rColor ?>;border-color:<?= $rColor ?>;opacity:0.9;"><?= $rLabel ?></span>
                    </td>
                    <td style="padding:0.6rem 0.75rem;">
                      <?php if ((bool) $u['email_verified']): ?>
                        <span style="color:oklch(0.72 0.17 145);font-size:0.8rem;">✓ Verificado</span>
                      <?php else: ?>
                        <span style="color:var(--ox-text-dim);font-size:0.8rem;">Pendiente</span>
                      <?php endif; ?>
                    </td>
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-dim);font-size:0.8rem;white-space:nowrap;">
                      <?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <!-- ── Últimos negocios ── -->
      <section style="margin-top:2.5rem;" aria-labelledby="admin-biz-heading">

        <div class="db-list-header" style="margin-bottom:1rem;">
          <h3 class="db-list-title" id="admin-biz-heading">
            <i data-lucide="building-2" width="16" height="16" aria-hidden="true" style="vertical-align:-2px;margin-right:0.4rem;"></i>
            Últimos negocios creados
          </h3>
        </div>

        <?php if (empty($latestBusinesses)): ?>
          <p style="color:var(--ox-text-muted);font-size:0.875rem;">No hay negocios creados todavía.</p>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
              <thead>
                <tr style="border-bottom:1px solid var(--ox-border);">
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">#</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Negocio</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Propietario</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Plan</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Creado</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latestBusinesses as $b): ?>
                  <tr style="border-bottom:1px solid var(--ox-border);transition:background 0.15s;"
                      onmouseover="this.style.background='var(--ox-bg-hover)'"
                      onmouseout="this.style.background=''">
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-dim);font-family:'JetBrains Mono',monospace;font-size:0.8rem;"><?= (int) $b['id'] ?></td>
                    <td style="padding:0.6rem 0.75rem;">
                      <span style="color:var(--ox-text);display:block;"><?= htmlspecialchars($b['name']) ?></span>
                      <span style="color:var(--ox-text-dim);font-size:0.78rem;font-family:'JetBrains Mono',monospace;">/<?= htmlspecialchars($b['slug']) ?></span>
                    </td>
                    <td style="padding:0.6rem 0.75rem;">
                      <span style="color:var(--ox-text);display:block;"><?= htmlspecialchars($b['owner_name']) ?></span>
                      <span style="color:var(--ox-text-dim);font-size:0.78rem;"><?= htmlspecialchars($b['owner_email']) ?></span>
                    </td>
                    <td style="padding:0.6rem 0.75rem;">
                      <?php $pLabel = $planLabels[(int) $b['plan_id']] ?? 'Desconocido'; ?>
                      <span class="db-badge db-badge--plan"><?= $pLabel ?></span>
                    </td>
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-dim);font-size:0.8rem;white-space:nowrap;">
                      <?= htmlspecialchars(substr($b['created_at'], 0, 10)) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

      <!-- ── Últimos tours ── -->
      <section style="margin-top:2.5rem;margin-bottom:3rem;" aria-labelledby="admin-tours-heading">

        <div class="db-list-header" style="margin-bottom:1rem;">
          <h3 class="db-list-title" id="admin-tours-heading">
            <i data-lucide="play-circle" width="16" height="16" aria-hidden="true" style="vertical-align:-2px;margin-right:0.4rem;"></i>
            Últimos tours creados
          </h3>
        </div>

        <?php if (empty($latestTours)): ?>
          <p style="color:var(--ox-text-muted);font-size:0.875rem;">No hay tours creados todavía.</p>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
              <thead>
                <tr style="border-bottom:1px solid var(--ox-border);">
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">#</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Tour</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Negocio</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Estado</th>
                  <th style="padding:0.6rem 0.75rem;text-align:left;color:var(--ox-text-muted);font-weight:500;">Creado</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latestTours as $t): ?>
                  <tr style="border-bottom:1px solid var(--ox-border);transition:background 0.15s;"
                      onmouseover="this.style.background='var(--ox-bg-hover)'"
                      onmouseout="this.style.background=''">
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-dim);font-family:'JetBrains Mono',monospace;font-size:0.8rem;"><?= (int) $t['id'] ?></td>
                    <td style="padding:0.6rem 0.75rem;">
                      <span style="color:var(--ox-text);display:block;"><?= htmlspecialchars($t['title']) ?></span>
                      <span style="color:var(--ox-text-dim);font-size:0.78rem;font-family:'JetBrains Mono',monospace;">/<?= htmlspecialchars($t['business_slug']) ?>/<?= htmlspecialchars($t['slug']) ?></span>
                    </td>
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-muted);">
                      <?= htmlspecialchars($t['business_name']) ?>
                    </td>
                    <td style="padding:0.6rem 0.75rem;">
                      <?php if ((bool) $t['is_published']): ?>
                        <span class="db-badge db-badge--published">Publicado</span>
                      <?php else: ?>
                        <span class="db-badge db-badge--draft">Borrador</span>
                      <?php endif; ?>
                    </td>
                    <td style="padding:0.6rem 0.75rem;color:var(--ox-text-dim);font-size:0.8rem;white-space:nowrap;">
                      <?= htmlspecialchars(substr($t['created_at'], 0, 10)) ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>

    </div><!-- /db-page -->
  </main>

</div><!-- /db-layout -->

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
