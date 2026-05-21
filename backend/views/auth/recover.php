<?php
$flash     = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar contraseña — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/auth.css">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
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
        <p class="brand-eyebrow">OXPHYRE · ACCESO</p>
        <h2 class="brand-h2">Recupera<br><em>tu acceso.</em></h2>
        <p class="brand-sub">Te enviamos las instrucciones por email.</p>
      </div>
      <div class="brand-bottom"><span class="brand-domain">oxphyre.com</span></div>
    </div>
  </div>

  <div class="auth-form-panel">
    <div class="auth-form-bleed" aria-hidden="true"></div>
    <div class="auth-form-inner">

      <div class="mobile-logo"><a href="/" class="brand-logo">◉ Oxphyre</a></div>

      <h1 class="form-h1">Recuperar contraseña</h1>
      <p class="form-sub">Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</p>

      <?php if ($flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" role="alert">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <form action="/recover" method="POST" novalidate id="recover-form" style="margin-top:1.5rem;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="fields-group">
          <div class="field">
            <label class="field-label" for="email">Email</label>
            <input class="field-input" type="email" id="email" name="email"
              autocomplete="email" placeholder="tu@email.com" required>
            <span class="field-error" id="email-error" aria-live="polite"></span>
          </div>
        </div>

        <button type="submit" class="btn-submit" style="margin-top:1.25rem;">Enviar instrucciones</button>
      </form>

      <p class="auth-toggle">
        <a href="/login">← Volver al inicio de sesión</a>
      </p>

    </div>
  </div>
</div>

<script src="/js/i18n.js"></script>
<script src="https://unpkg.com/three@0.160.0/build/three.min.js" defer></script>
<script src="/js/auth-sphere.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();
  if (window.i18n) window.i18n.initLang();

  const emailInput = document.getElementById('email');
  const emailError = document.getElementById('email-error');
  const form       = document.getElementById('recover-form');

  emailInput.addEventListener('input', () => {
    const v = emailInput.value.trim();
    if (!v) { emailInput.classList.remove('is-error','is-valid'); emailError.textContent = ''; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) { emailInput.classList.add('is-error'); emailInput.classList.remove('is-valid'); emailError.textContent = 'Introduce un email válido.'; }
    else { emailInput.classList.add('is-valid'); emailInput.classList.remove('is-error'); emailError.textContent = ''; }
  });

  form.addEventListener('submit', e => {
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
      emailInput.classList.add('is-error'); emailError.textContent = 'Introduce un email válido.';
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>
