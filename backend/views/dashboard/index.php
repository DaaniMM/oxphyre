<?php
$userName  = htmlspecialchars($_SESSION['user_name']  ?? '');
$userEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
$userRole  = htmlspecialchars($_SESSION['user_role']  ?? '');
$csrfToken = htmlspecialchars($_SESSION['csrf_token'] ?? '');
?>
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
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --ox-bg:          oklch(0.08 0.005 60);
      --ox-elevated:    oklch(0.12 0.008 60);
      --ox-border:      oklch(0.25 0.015 60 / 0.5);
      --ox-text:        oklch(0.97 0.01  80);
      --ox-text-muted:  oklch(0.65 0.02  70);
      --ox-amber:       oklch(0.78 0.16  65);
    }
    html, body {
      height: 100%;
      background: var(--ox-bg);
      color: var(--ox-text);
      font-family: 'Inter', sans-serif;
      -webkit-font-smoothing: antialiased;
    }
    .shell {
      max-width: 720px;
      margin: 0 auto;
      padding: 3rem 1.5rem;
    }
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 3rem;
    }
    .logo {
      font-family: 'JetBrains Mono', monospace;
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.3em;
      color: var(--ox-amber);
      text-decoration: none;
    }
    .card {
      background: var(--ox-elevated);
      border: 1px solid var(--ox-border);
      border-radius: 14px;
      padding: 2rem;
      margin-bottom: 1.5rem;
    }
    .card-label {
      font-family: 'JetBrains Mono', monospace;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 0.25em;
      color: oklch(0.45 0.015 65);
      margin-bottom: 1rem;
    }
    .welcome-name {
      font-family: 'Instrument Serif', Georgia, serif;
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      font-weight: 400;
      line-height: 1.1;
      margin-bottom: 0.5rem;
    }
    .welcome-name em { font-style: italic; color: var(--ox-amber); }
    .user-row {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-top: 1.25rem;
      font-size: 0.875rem;
      color: oklch(0.65 0.02 70);
    }
    .user-row span + span::before { content: '·'; margin-right: 0.75rem; }
    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 11px;
      font-family: 'JetBrains Mono', monospace;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      background: oklch(0.78 0.16 65 / 0.12);
      border: 1px solid oklch(0.78 0.16 65 / 0.3);
      color: var(--ox-amber);
    }
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    .info-card {
      background: var(--ox-elevated);
      border: 1px solid var(--ox-border);
      border-radius: 10px;
      padding: 1.25rem;
    }
    .info-card-label {
      font-size: 11px;
      color: oklch(0.45 0.015 65);
      text-transform: uppercase;
      letter-spacing: 0.15em;
      margin-bottom: 0.5rem;
    }
    .info-card-value {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--ox-text);
    }
    .info-card-note {
      font-size: 12px;
      color: oklch(0.45 0.015 65);
      margin-top: 4px;
    }
    .btn-logout {
      background: transparent;
      border: 1px solid var(--ox-border);
      border-radius: 8px;
      padding: 0.6rem 1.2rem;
      color: oklch(0.65 0.02 70);
      font-family: 'Inter', sans-serif;
      font-size: 0.875rem;
      cursor: pointer;
      transition: border-color 0.2s, color 0.2s;
    }
    .btn-logout:hover { border-color: var(--ox-amber); color: var(--ox-amber); }
    .roadmap-note {
      font-size: 0.8125rem;
      color: oklch(0.45 0.015 65);
      text-align: center;
      margin-top: 2rem;
    }
  </style>
</head>
<body>

<div class="shell">
  <header>
    <a href="/" class="logo">◉ Oxphyre</a>
    <form action="/logout" method="POST">
      <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
      <button type="submit" class="btn-logout">Cerrar sesión</button>
    </form>
  </header>

  <div class="card">
    <p class="card-label">Panel de control</p>
    <h1 class="welcome-name">Hola, <em><?= $userName ?>.</em></h1>
    <div class="user-row">
      <span><?= $userEmail ?></span>
      <span><span class="badge"><?= $userRole ?></span></span>
    </div>
  </div>

  <div class="info-grid">
    <div class="info-card">
      <p class="info-card-label">Tours activos</p>
      <p class="info-card-value">0</p>
      <p class="info-card-note">Plan Free · máx 1</p>
    </div>
    <div class="info-card">
      <p class="info-card-label">Negocios</p>
      <p class="info-card-value">0</p>
      <p class="info-card-note">Plan Free · máx 1</p>
    </div>
    <div class="info-card">
      <p class="info-card-label">Escaneos QR</p>
      <p class="info-card-value">0</p>
      <p class="info-card-note">Últimos 30 días</p>
    </div>
  </div>

  <p class="roadmap-note">Dashboard completo en desarrollo — próximas semanas.</p>
</div>

</body>
</html>
