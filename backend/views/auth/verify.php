<?php $verified = $verified ?? false; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $verified ? 'Email verificado' : 'Enlace inválido' ?> — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body>

<div class="auth-layout">

  <div id="auth-brand-panel">
    <canvas id="auth-sphere-canvas"></canvas>
    <div class="sphere-overlay-glow"  aria-hidden="true"></div>
    <div class="sphere-overlay-fade"  aria-hidden="true"></div>
    <div class="sphere-overlay-stage" aria-hidden="true"></div>
    <div class="brand-content">
      <div class="brand-top"><a href="/" class="brand-logo">◉ Oxphyre</a></div>
      <div class="brand-center">
        <p class="brand-eyebrow">OXPHYRE · VERIFICACIÓN</p>
        <?php if ($verified): ?>
          <h2 class="brand-h2">Cuenta<br><em>activada.</em></h2>
          <p class="brand-sub">Ya puedes iniciar sesión y empezar a crear tu tour virtual.</p>
        <?php else: ?>
          <h2 class="brand-h2">Enlace<br><em>no válido.</em></h2>
          <p class="brand-sub">El enlace de verificación ha expirado o ya fue utilizado.</p>
        <?php endif; ?>
      </div>
      <div class="brand-bottom"><span class="brand-domain">oxphyre.com</span></div>
    </div>
  </div>

  <div class="auth-form-panel">
    <div class="auth-form-bleed" aria-hidden="true"></div>
    <div class="auth-form-inner">

      <div class="mobile-logo"><a href="/" class="brand-logo">◉ Oxphyre</a></div>

      <?php if ($verified): ?>
        <div class="verify-icon verify-icon--success" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="oklch(0.80 0.10 145)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h1 class="form-h1">¡Email verificado!</h1>
        <p class="form-sub">Tu cuenta está activa. Ya puedes iniciar sesión y empezar a crear tu primer tour virtual.</p>
        <a href="/login" class="btn-submit">Iniciar sesión →</a>
      <?php else: ?>
        <div class="verify-icon verify-icon--error" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="oklch(0.80 0.10 25)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <h1 class="form-h1">Enlace no válido</h1>
        <p class="form-sub">El enlace de verificación ha expirado o ya fue utilizado. Si necesitas uno nuevo, regístrate de nuevo o contacta con soporte.</p>
        <a href="/registro" class="btn-submit">Crear nueva cuenta</a>
        <p class="auth-toggle"><a href="/login">Ir al inicio de sesión</a></p>
      <?php endif; ?>

    </div>
  </div>
</div>

<script src="https://unpkg.com/three@0.160.0/build/three.min.js" defer></script>
<script src="/js/auth-sphere.js" defer></script>
</body>
</html>
