<?php
$old = $formData ?? [];
$errors = $errors ?? [];
$successMessage = $successMessage ?? '';
$csrfToken = htmlspecialchars($_SESSION['csrf_token'] ?? '');

$inquiryOptions = [
    'trial' => 'Quiero probar Oxphyre',
    'local_business' => 'Soy un negocio local',
    'support' => 'Soporte o problema de acceso',
    'collaboration' => 'Colaboración',
    'other' => 'Otro',
];

$planOptions = [
    'free' => 'Free',
    'pro' => 'Pro',
    'business' => 'Business',
    'unknown' => 'No lo sé todavía',
];

$value = static fn(string $key): string => htmlspecialchars((string) ($old[$key] ?? ''));
$selected = static fn(string $key, string $option): string => (($old[$key] ?? '') === $option) ? ' selected' : '';
$checked = static fn(string $key): string => !empty($old[$key]) ? ' checked' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Contacto | Oxphyre</title>
  <meta name="description" content="Contacta con Oxphyre para probar la plataforma, resolver dudas sobre tours virtuales o pedir ayuda con tu cuenta.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/contacto">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="alternate icon" href="/favicon.ico">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/contacto">
  <meta property="og:title" content="Contacto | Oxphyre">
  <meta property="og:description" content="Cuéntanos tu caso y te responderemos con contexto sobre Oxphyre, tours virtuales o soporte de acceso.">
  <meta property="og:image" content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">
</head>
<body class="phase-2">
  <nav id="nav" role="navigation" aria-label="Navegacion principal">
    <a href="/" class="nav-logo" aria-label="Oxphyre inicio">Oxphyre</a>
    <div class="nav-links">
      <a href="/tour-virtual-para-negocios">Tour para negocios</a>
      <a href="/blog">Blog</a>
      <a href="/precios">Precios</a>
      <a href="/soporte">Soporte</a>
      <a href="/contacto">Contacto</a>
    </div>
    <div class="nav-actions">
      <a href="/login" class="btn-ghost">Iniciar sesi&oacute;n</a>
      <a href="/registro?plan=free" class="btn-primary">Empezar gratis</a>
    </div>
  </nav>

  <main class="contact-page">
    <header class="contact-hero">
      <p class="contact-kicker">Contacto</p>
      <h1 class="contact-title">Cuéntanos qué necesitas construir con Oxphyre</h1>
      <p class="contact-subtitle">Este formulario es para dudas comerciales, soporte de acceso, colaboraciones o casos concretos de negocios locales que quieren enseñar su espacio con una visita virtual.</p>
    </header>

    <section class="contact-shell" aria-labelledby="contact-form-title">
      <div class="contact-info-panel">
        <h2>Canal directo</h2>
        <p>Usa el formulario para que podamos entender mejor tu caso. Si prefieres escribir desde tu correo, también puedes hacerlo a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a>.</p>
        <ul>
          <li>Consultas sobre Free, Pro o Business.</li>
          <li>Dudas de acceso o soporte básico.</li>
          <li>Negocios que quieren preparar una demo.</li>
          <li>Colaboraciones relacionadas con Oxphyre.</li>
        </ul>
      </div>

      <form class="contact-form" action="/contacto" method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
        <div class="contact-honeypot" aria-hidden="true">
          <label for="contact-website">Web</label>
          <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off" value="<?= $value('website') ?>">
        </div>

        <div class="contact-form-header">
          <h2 id="contact-form-title">Formulario de contacto</h2>
          <p>Los campos marcados como obligatorios son necesarios para poder responderte.</p>
        </div>

        <?php if ($successMessage !== ''): ?>
          <div class="contact-alert contact-alert-success" role="status">
            <?= htmlspecialchars($successMessage) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors['general'])): ?>
          <div class="contact-alert contact-alert-error" role="alert">
            <?= htmlspecialchars($errors['general']) ?>
          </div>
        <?php endif; ?>

        <div class="contact-grid">
          <div class="contact-field">
            <label for="contact-name">Nombre <span>*</span></label>
            <input type="text" id="contact-name" name="name" maxlength="100" required value="<?= $value('name') ?>">
            <?php if (!empty($errors['name'])): ?><p class="contact-error"><?= htmlspecialchars($errors['name']) ?></p><?php endif; ?>
          </div>

          <div class="contact-field">
            <label for="contact-business">Apellidos o nombre del negocio</label>
            <input type="text" id="contact-business" name="business_or_lastname" maxlength="120" value="<?= $value('business_or_lastname') ?>">
          </div>

          <div class="contact-field">
            <label for="contact-email">Email <span>*</span></label>
            <input type="email" id="contact-email" name="email" maxlength="160" required value="<?= $value('email') ?>">
            <?php if (!empty($errors['email'])): ?><p class="contact-error"><?= htmlspecialchars($errors['email']) ?></p><?php endif; ?>
          </div>

          <div class="contact-field">
            <label for="contact-phone">Teléfono</label>
            <input type="tel" id="contact-phone" name="phone" maxlength="40" value="<?= $value('phone') ?>">
          </div>

          <div class="contact-field">
            <label for="contact-inquiry">Tipo de consulta <span>*</span></label>
            <select id="contact-inquiry" name="inquiry_type" required>
              <?php foreach ($inquiryOptions as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"<?= $selected('inquiry_type', $key) ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['inquiry_type'])): ?><p class="contact-error"><?= htmlspecialchars($errors['inquiry_type']) ?></p><?php endif; ?>
          </div>

          <div class="contact-field">
            <label for="contact-plan">Plan de interés <span>*</span></label>
            <select id="contact-plan" name="plan_interest" required>
              <?php foreach ($planOptions as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>"<?= $selected('plan_interest', $key) ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['plan_interest'])): ?><p class="contact-error"><?= htmlspecialchars($errors['plan_interest']) ?></p><?php endif; ?>
          </div>

          <div class="contact-field contact-field-full">
            <label for="contact-message">Mensaje <span>*</span></label>
            <textarea id="contact-message" name="message" rows="7" maxlength="2000" required><?= $value('message') ?></textarea>
            <?php if (!empty($errors['message'])): ?><p class="contact-error"><?= htmlspecialchars($errors['message']) ?></p><?php endif; ?>
          </div>
        </div>

        <div class="contact-checks">
          <label class="contact-check">
            <input type="checkbox" name="privacy_accepted" value="1" required<?= $checked('privacy_accepted') ?>>
            <span>Acepto la <a href="/privacidad">política de privacidad</a> de Oxphyre. <strong>*</strong></span>
          </label>
          <?php if (!empty($errors['privacy_accepted'])): ?><p class="contact-error"><?= htmlspecialchars($errors['privacy_accepted']) ?></p><?php endif; ?>

          <label class="contact-check">
            <input type="checkbox" name="commercial_contact" value="1"<?= $checked('commercial_contact') ?>>
            <span>Acepto que Oxphyre pueda contactarme sobre productos, demos o información relacionada con mi consulta.</span>
          </label>
        </div>

        <div class="contact-actions">
          <button type="submit" class="contact-submit">Enviar mensaje</button>
          <a href="/soporte" class="contact-secondary">Volver a soporte</a>
        </div>
      </form>
    </section>
  </main>

  <footer id="footer" role="contentinfo">
    <div class="footer-inner">
      <div class="footer-top">

        <div class="footer-brand footer-col">
          <a href="/" class="footer-logo">Oxphyre</a>
          <p class="footer-tagline" data-i18n="footer.tagline">Tours virtuales 3D para negocios locales.</p>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.product">Producto</p>
          <ul>
            <li><a href="/#caracteristicas" data-i18n="footer.features">Caracter&iacute;sticas</a></li>
            <li><a href="/blog">Blog</a></li>
            <li><a href="/precios" data-i18n="footer.pricing">Precios</a></li>
            <li><a href="/#demo" data-i18n="footer.demo">Demo</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.legal">Legal</p>
          <ul>
            <li><a href="/privacidad" data-i18n="footer.privacy">Privacidad</a></li>
            <li><a href="/terminos" data-i18n="footer.terms">T&eacute;rminos</a></li>
            <li><a href="/cookies" data-i18n="footer.cookies">Cookies</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.contact">Contacto</p>
          <ul>
            <li><a href="/contacto">Contacto</a></li>
            <li><a href="/sobre-nosotros" data-i18n="footer.about">Sobre nosotros</a></li>
            <li><a href="/soporte" data-i18n="footer.support">Soporte</a></li>
            <li><a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.social">Redes</p>
          <ul>
            <li><a href="https://instagram.com/oxphyre" rel="noopener noreferrer" target="_blank">Instagram</a></li>
            <li><a href="https://twitter.com/oxphyre" rel="noopener noreferrer" target="_blank">Twitter / X</a></li>
            <li><a href="https://linkedin.com/company/oxphyre" rel="noopener noreferrer" target="_blank">LinkedIn</a></li>
          </ul>
        </div>

      </div>

      <div class="footer-bottom">
        <p class="footer-copyright" data-i18n="footer.copyright">
          &copy; <?= date('Y') ?> Oxphyre. Todos los derechos reservados.
        </p>
      </div>

    </div>
  </footer>
  <div id="cursor-ring" aria-hidden="true"></div>
  <script src="<?= asset('/js/public-cursor.js') ?>" defer></script>
</body>
</html>
