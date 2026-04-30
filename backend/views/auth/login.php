<?php
// Leer y limpiar flash message de la sesión
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// El CSRF token ya está generado por AuthController::showLogin()
$csrfToken = $_SESSION['csrf_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
  <link rel="stylesheet" href="/css/auth.css">
</head>
<body>

<div class="auth-layout">

  <!-- ── Esfera Three.js (panel izquierdo) ── -->
  <div id="auth-sphere-panel">
    <canvas id="auth-sphere-canvas"></canvas>

    <div class="sphere-brand">
      <a href="/" class="sphere-logo">Oxphyre</a>
      <p class="sphere-tagline">Tours virtuales 3D para negocios locales</p>
    </div>

    <div class="sphere-bottom">
      <p class="sphere-quote">
        Cada espacio tiene una historia.<br>
        <em>Nosotros la hacemos visible.</em>
      </p>
    </div>
  </div>

  <!-- ── Formulario (panel derecho) ── -->
  <div class="auth-form-panel">
    <div class="auth-form-container">

      <h1 class="auth-title">Bienvenido de vuelta</h1>
      <p class="auth-subtitle">Accede a tu cuenta Oxphyre</p>

      <?php if ($flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" role="alert">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="/login" method="POST" novalidate id="login-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <!-- Email -->
        <div class="field-group">
          <label class="field-label" for="email">Email</label>
          <input
            class="field-input"
            type="email"
            id="email"
            name="email"
            autocomplete="email"
            placeholder="tu@email.com"
            required
          >
          <span class="field-error" id="email-error" aria-live="polite"></span>
        </div>

        <!-- Contraseña -->
        <div class="field-group">
          <label class="field-label" for="password">Contraseña</label>
          <div class="input-wrapper">
            <input
              class="field-input"
              type="password"
              id="password"
              name="password"
              autocomplete="current-password"
              placeholder="••••••••"
              required
            >
            <button type="button" class="input-toggle" id="toggle-password" aria-label="Mostrar contraseña">
              <i data-lucide="eye" width="18" height="18"></i>
            </button>
          </div>
          <span class="field-error" id="password-error" aria-live="polite"></span>
        </div>

        <!-- Footer: remember me + forgot -->
        <div class="field-footer">
          <label class="checkbox-label">
            <input type="checkbox" name="remember_me" value="1">
            <span>Recuérdame</span>
          </label>
          <a href="/login/recuperar" class="forgot-link">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn-auth" id="submit-btn">Iniciar sesión</button>

        <div class="auth-divider">o continúa con</div>

        <div class="social-buttons">
          <button type="button" class="btn-social" disabled title="Próximamente">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
              <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
              <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
              <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google
          </button>
          <button type="button" class="btn-social" disabled title="Próximamente">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            Apple
          </button>
        </div>

        <p class="auth-switch">
          ¿No tienes cuenta? <a href="/registro">Regístrate gratis</a>
        </p>
      </form>

    </div>
  </div>
</div>

<script src="https://unpkg.com/three@0.160.0/build/three.min.js" defer></script>
<script src="/js/auth-sphere.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();

  const emailInput    = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const emailError    = document.getElementById('email-error');
  const passwordError = document.getElementById('password-error');
  const toggleBtn     = document.getElementById('toggle-password');
  const form          = document.getElementById('login-form');

  // Toggle visibilidad contraseña
  toggleBtn.addEventListener('click', () => {
    const isText = passwordInput.type === 'text';
    passwordInput.type = isText ? 'password' : 'text';
    toggleBtn.innerHTML = isText
      ? '<i data-lucide="eye" width="18" height="18"></i>'
      : '<i data-lucide="eye-off" width="18" height="18"></i>';
    lucide.createIcons();
  });

  // Validación en tiempo real — email
  emailInput.addEventListener('input', () => {
    const val = emailInput.value.trim();
    if (!val) {
      setFieldState(emailInput, emailError, '', null);
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
      setFieldState(emailInput, emailError, 'Introduce un email válido.', false);
    } else {
      setFieldState(emailInput, emailError, '', true);
    }
  });

  // Validación en tiempo real — password
  passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    if (!val) {
      setFieldState(passwordInput, passwordError, '', null);
    } else if (val.length < 6) {
      setFieldState(passwordInput, passwordError, 'Contraseña demasiado corta.', false);
    } else {
      setFieldState(passwordInput, passwordError, '', true);
    }
  });

  // Validación antes de enviar
  form.addEventListener('submit', e => {
    let valid = true;
    const email    = emailInput.value.trim();
    const password = passwordInput.value;

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setFieldState(emailInput, emailError, 'Introduce un email válido.', false);
      valid = false;
    }
    if (!password) {
      setFieldState(passwordInput, passwordError, 'Introduce tu contraseña.', false);
      valid = false;
    }
    if (!valid) e.preventDefault();
  });

  function setFieldState(input, errorEl, message, isValid) {
    input.classList.toggle('is-error', isValid === false);
    input.classList.toggle('is-valid', isValid === true);
    errorEl.textContent = message;
  }
});
</script>

</body>
</html>
