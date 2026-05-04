<?php
$flash     = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$csrfToken = $_SESSION['csrf_token'] ?? '';
$token     = htmlspecialchars($_GET['token'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva contraseña — Oxphyre</title>
  <meta name="robots" content="noindex, nofollow">
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
        <p class="brand-eyebrow">OXPHYRE · SEGURIDAD</p>
        <h2 class="brand-h2">Nueva<br><em>contraseña.</em></h2>
        <p class="brand-sub">Crea una contraseña segura para tu cuenta.</p>
      </div>
      <div class="brand-bottom"><span class="brand-domain">oxphyre.com</span></div>
    </div>
  </div>

  <div class="auth-form-panel">
    <div class="auth-form-bleed" aria-hidden="true"></div>
    <div class="auth-form-inner">

      <div class="mobile-logo"><a href="/" class="brand-logo">◉ Oxphyre</a></div>

      <h1 class="form-h1">Establece nueva contraseña</h1>
      <p class="form-sub">Introduce y confirma tu nueva contraseña.</p>

      <?php if ($flash): ?>
        <div class="flash flash-<?= htmlspecialchars($flash['type']) ?>" role="alert">
          <?= htmlspecialchars($flash['message']) ?>
        </div>
      <?php endif; ?>

      <form action="/reset" method="POST" novalidate id="reset-form" style="margin-top:1.5rem;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="token"      value="<?= $token ?>">

        <div class="fields-group">
          <div class="field">
            <label class="field-label" for="password">Nueva contraseña</label>
            <div class="input-wrap">
              <input class="field-input" type="password" id="password" name="password"
                autocomplete="new-password" placeholder="Mín. 8 caracteres" required>
              <button type="button" class="toggle-pass" id="toggle-password" aria-label="Mostrar contraseña">
                <i data-lucide="eye" width="16" height="16"></i>
              </button>
            </div>
            <div class="strength-meter" aria-hidden="true">
              <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
              <span class="strength-label" id="strength-label"></span>
            </div>
            <span class="field-error" id="password-error" aria-live="polite"></span>
          </div>

          <div class="field">
            <label class="field-label" for="confirm_password">Confirmar contraseña</label>
            <div class="input-wrap">
              <input class="field-input" type="password" id="confirm_password" name="confirm_password"
                autocomplete="new-password" placeholder="Repite tu contraseña" required>
              <button type="button" class="toggle-pass" id="toggle-confirm" aria-label="Mostrar contraseña">
                <i data-lucide="eye" width="16" height="16"></i>
              </button>
            </div>
            <span class="field-error" id="confirm-error" aria-live="polite"></span>
          </div>
        </div>

        <button type="submit" class="btn-submit" style="margin-top:1.25rem;">Guardar nueva contraseña</button>
      </form>

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

  const passwordInput = document.getElementById('password');
  const confirmInput  = document.getElementById('confirm_password');
  const passwordError = document.getElementById('password-error');
  const confirmError  = document.getElementById('confirm-error');
  const strengthFill  = document.getElementById('strength-fill');
  const strengthLabel = document.getElementById('strength-label');
  const form          = document.getElementById('reset-form');

  function makeToggle(btnId, inputEl) {
    document.getElementById(btnId).addEventListener('click', () => {
      const isText = inputEl.type === 'text';
      inputEl.type = isText ? 'password' : 'text';
      document.getElementById(btnId).innerHTML = isText
        ? '<i data-lucide="eye" width="16" height="16"></i>'
        : '<i data-lucide="eye-off" width="16" height="16"></i>';
      lucide.createIcons();
    });
  }
  makeToggle('toggle-password', passwordInput);
  makeToggle('toggle-confirm',  confirmInput);

  passwordInput.addEventListener('input', () => {
    const v = passwordInput.value;
    if (!v) { setF(passwordInput, passwordError, '', null); updateStrength(0); return; }
    updateStrength(calcStr(v));
    const errs = passErrs(v);
    if (errs.length) setF(passwordInput, passwordError, errs[0], false);
    else setF(passwordInput, passwordError, '', true);
    if (confirmInput.value) checkConfirm();
  });

  confirmInput.addEventListener('input', checkConfirm);

  function checkConfirm() {
    const v = confirmInput.value;
    if (!v) setF(confirmInput, confirmError, '', null);
    else if (v !== passwordInput.value) setF(confirmInput, confirmError, 'Las contraseñas no coinciden.', false);
    else setF(confirmInput, confirmError, '', true);
  }

  form.addEventListener('submit', e => {
    let ok = true;
    const errs = passErrs(passwordInput.value);
    if (errs.length) { setF(passwordInput, passwordError, errs[0], false); ok = false; }
    if (!confirmInput.value || confirmInput.value !== passwordInput.value) {
      setF(confirmInput, confirmError, 'Las contraseñas no coinciden.', false); ok = false;
    }
    if (!ok) e.preventDefault();
  });

  function setF(input, el, msg, valid) {
    input.classList.toggle('is-error', valid === false);
    input.classList.toggle('is-valid', valid === true);
    el.textContent = msg;
  }
  function passErrs(v) {
    if (v.length < 8)     return ['Mínimo 8 caracteres.'];
    if (!/[A-Z]/.test(v)) return ['Necesita al menos una mayúscula.'];
    if (!/[0-9]/.test(v)) return ['Necesita al menos un número.'];
    if (!/[\W_]/.test(v)) return ['Necesita al menos un carácter especial.'];
    return [];
  }
  function calcStr(v) { return [v.length>=8,/[A-Z]/.test(v),/[0-9]/.test(v),/[\W_]/.test(v)].filter(Boolean).length; }
  function updateStrength(score) {
    const c = ['','#c0392b','#e67e22','#7de0a8','#27ae60'];
    const l = ['','Débil','Regular','Buena','Fuerte'];
    strengthFill.style.width = score ? (score*25)+'%' : '0%';
    strengthFill.style.backgroundColor = c[score]||'';
    strengthLabel.textContent = l[score]||'';
  }
});
</script>
</body>
</html>
