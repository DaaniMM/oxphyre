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
  <title>Iniciar sesión — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body>

<div class="auth-layout">

  <!-- ── Panel izquierdo: brand + esfera ── -->
  <div id="auth-brand-panel">
    <canvas id="auth-sphere-canvas"></canvas>
    <div class="sphere-overlay-glow"  aria-hidden="true"></div>
    <div class="sphere-overlay-fade"  aria-hidden="true"></div>
    <div class="sphere-overlay-stage" aria-hidden="true"></div>

    <div class="brand-content">
      <div class="brand-top">
        <a href="/" class="brand-logo">◉ Oxphyre</a>
      </div>

      <div class="brand-center">
        <p class="brand-eyebrow" data-i18n="auth.login_eyebrow">OXPHYRE · ACCESO</p>
        <h2 class="brand-h2">
          <span data-i18n="auth.login_h2_prefix">Bienvenido </span><em data-i18n="auth.login_h2_em">de vuelta.</em>
        </h2>
        <p class="brand-sub" data-i18n="auth.login_brand_sub">Continúa construyendo tu espacio.</p>
      </div>

      <div class="brand-bottom">
        <span class="brand-domain">oxphyre.com</span>
      </div>
    </div>
  </div>

  <!-- ── Panel derecho: formulario ── -->
  <div class="auth-form-panel">
    <div class="auth-form-bleed" aria-hidden="true"></div>

    <div class="auth-form-inner">

      <!-- Logo solo en móvil -->
      <div class="mobile-logo">
        <a href="/" class="brand-logo">◉ Oxphyre</a>
      </div>

      <h1 class="form-h1" data-i18n="auth.login_h1">Bienvenido de vuelta</h1>
      <p class="form-sub" data-i18n="auth.login_form_sub">Accede a tu cuenta Oxphyre.</p>

      <?php if ($flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" role="alert">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <!-- Botones sociales -->
      <div class="social-grid">
        <button type="button" class="btn-social" disabled aria-disabled="true">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
          </svg>
          <span data-i18n="auth.social_google">Google</span>
        </button>
        <button type="button" class="btn-social" disabled aria-disabled="true">
          <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
          </svg>
          <span data-i18n="auth.social_apple">Apple</span>
        </button>
      </div>

      <!-- Divisor -->
      <div class="auth-divider" aria-hidden="true">
        <span data-i18n="auth.divider">o con email</span>
      </div>

      <!-- Formulario -->
      <form action="/login" method="POST" novalidate id="login-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="fields-group">
          <div class="field">
            <label class="field-label" for="email" data-i18n="auth.field_email">Email</label>
            <input class="field-input" type="email" id="email" name="email"
              autocomplete="email" placeholder="tu@email.com" required>
            <span class="field-error" id="email-error" aria-live="polite"></span>
          </div>

          <div class="field">
            <label class="field-label" for="password" data-i18n="auth.field_password">Contraseña</label>
            <div class="input-wrap">
              <input class="field-input" type="password" id="password" name="password"
                autocomplete="current-password" placeholder="••••••••" required>
              <button type="button" class="toggle-pass" id="toggle-password" aria-label="Mostrar contraseña">
                <i data-lucide="eye" width="16" height="16"></i>
              </button>
            </div>
            <span class="field-error" id="password-error" aria-live="polite"></span>
          </div>
        </div>

        <div class="login-row">
          <label class="checkbox-wrap">
            <input type="checkbox" name="remember_me" value="1">
            <span data-i18n="auth.remember_me">Recuérdame</span>
          </label>
          <a href="/recover" class="forgot-link" data-i18n="auth.forgot">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-submit" data-i18n="auth.login_submit">Iniciar sesión</button>
      </form>

      <p class="auth-toggle">
        <span data-i18n="auth.login_toggle_text">¿No tienes cuenta?</span>
        <a href="/registro" data-i18n="auth.login_toggle_link">Regístrate gratis</a>
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

  const emailInput    = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const emailError    = document.getElementById('email-error');
  const passwordError = document.getElementById('password-error');
  const toggleBtn     = document.getElementById('toggle-password');
  const form          = document.getElementById('login-form');

  toggleBtn.addEventListener('click', () => {
    const isText = passwordInput.type === 'text';
    passwordInput.type = isText ? 'password' : 'text';
    toggleBtn.innerHTML = isText
      ? '<i data-lucide="eye" width="16" height="16"></i>'
      : '<i data-lucide="eye-off" width="16" height="16"></i>';
    lucide.createIcons();
  });

  emailInput.addEventListener('input', () => {
    const v = emailInput.value.trim();
    if (!v) setField(emailInput, emailError, '', null);
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) setField(emailInput, emailError, 'Introduce un email válido.', false);
    else setField(emailInput, emailError, '', true);
  });

  passwordInput.addEventListener('input', () => {
    const v = passwordInput.value;
    if (!v) setField(passwordInput, passwordError, '', null);
    else if (v.length < 6) setField(passwordInput, passwordError, 'Contraseña demasiado corta.', false);
    else setField(passwordInput, passwordError, '', true);
  });

  form.addEventListener('submit', e => {
    let ok = true;
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
      setField(emailInput, emailError, 'Introduce un email válido.', false); ok = false;
    }
    if (!passwordInput.value) {
      setField(passwordInput, passwordError, 'Introduce tu contraseña.', false); ok = false;
    }
    if (!ok) e.preventDefault();
  });

  function setField(input, el, msg, valid) {
    input.classList.toggle('is-error', valid === false);
    input.classList.toggle('is-valid', valid === true);
    el.textContent = msg;
  }
});
</script>
</body>
</html>
