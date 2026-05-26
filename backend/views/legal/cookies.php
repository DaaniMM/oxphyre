<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Política de cookies — Oxphyre</title>
  <meta name="description" content="Política de cookies inicial de Oxphyre: cookies técnicas, sesión, preferencias, analíticas y gestión desde el navegador.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/cookies">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/cookies">
  <meta property="og:title" content="Política de cookies — Oxphyre">
  <meta property="og:description" content="Información sobre el uso de cookies en Oxphyre.">
  <meta property="og:image" content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">

  <style>
    #nav { opacity: 1 !important; pointer-events: auto !important; }
    .legal-page {
      min-height: 100vh;
      padding-top: var(--nav-h);
      background:
        radial-gradient(circle at 50% 0%, rgba(254, 179, 84, 0.16), transparent 34%),
        #000;
    }
    .legal-hero {
      max-width: 920px;
      margin: 0 auto;
      padding: 92px 24px 36px;
      text-align: center;
    }
    .legal-kicker {
      margin-bottom: 16px;
      font-family: var(--font-mono);
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--accent);
    }
    .legal-title {
      font-family: var(--font-display);
      font-size: clamp(2.5rem, 7vw, 5rem);
      line-height: 0.95;
      color: var(--text-1);
    }
    .legal-sub {
      max-width: 720px;
      margin: 24px auto 0;
      color: var(--text-2);
      line-height: 1.7;
    }
    .legal-content {
      max-width: 860px;
      margin: 0 auto;
      padding: 28px 24px 96px;
    }
    .legal-note {
      margin-bottom: 34px;
      padding: 18px 20px;
      border: 1px solid rgba(254, 179, 84, 0.24);
      border-radius: 8px;
      background: rgba(254, 179, 84, 0.07);
      color: var(--text-2);
      line-height: 1.7;
    }
    .legal-section {
      padding: 30px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    .legal-section h2 {
      margin-bottom: 16px;
      font-family: var(--font-display);
      font-size: clamp(1.5rem, 3vw, 2rem);
      color: var(--text-1);
    }
    .legal-section p,
    .legal-section li {
      color: var(--text-2);
      line-height: 1.75;
    }
    .legal-section ul {
      margin: 12px 0 0 20px;
    }
    .legal-section a {
      color: var(--accent);
    }
    @media (max-width: 768px) {
      .legal-hero { padding-top: 72px; text-align: left; }
      .legal-title { font-size: 2.8rem; }
    }
  </style>
</head>
<body class="public-secondary">
  <nav id="nav" role="navigation" aria-label="Navegacion principal">
    <a href="/" class="nav-logo" aria-label="Oxphyre inicio">Oxphyre</a>
    <div class="nav-links">
      <a href="/tour-virtual-para-negocios">Producto</a>
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

  <main class="legal-page">
    <header class="legal-hero">
      <p class="legal-kicker">Legal · Cookies</p>
      <h1 class="legal-title">Política de cookies</h1>
      <p class="legal-sub">Esta página explica qué son las cookies y cómo puede usarlas Oxphyre para que la web funcione correctamente.</p>
    </header>

    <div class="legal-content">
      <p class="legal-note">Este documento es una versión inicial para el proyecto Oxphyre y podrá actualizarse antes del lanzamiento comercial definitivo. En producción real, estos textos deberían revisarse legalmente.</p>

      <section class="legal-section">
        <h2>Qué son las cookies</h2>
        <p>Las cookies son pequeños archivos que el navegador puede guardar en el dispositivo del usuario. Sirven para recordar información técnica, mantener sesiones, conservar preferencias o ayudar a entender cómo se usa una web, según el caso.</p>
      </section>

      <section class="legal-section">
        <h2>Cookies técnicas necesarias</h2>
        <p>Oxphyre puede usar cookies técnicas imprescindibles para que la plataforma funcione: mantener una sesión segura, proteger formularios, recordar estados básicos de navegación o permitir el acceso al dashboard cuando el usuario inicia sesión. Estas cookies no están pensadas para publicidad.</p>
      </section>

      <section class="legal-section">
        <h2>Cookies de sesión y login</h2>
        <p>Cuando un usuario inicia sesión, el navegador puede recibir una cookie de sesión. Esta cookie permite identificar la sesión del servidor sin guardar la contraseña en el navegador. Si el usuario cierra sesión o la sesión caduca, deja de utilizarse para acceder a la cuenta.</p>
      </section>

      <section class="legal-section">
        <h2>Preferencias</h2>
        <p>Oxphyre puede recordar preferencias no sensibles, como idioma o modo visual, para mejorar la experiencia. Algunas preferencias pueden guardarse en el navegador mediante mecanismos similares a cookies, siempre evitando guardar tokens de sesión o datos sensibles en localStorage.</p>
      </section>

      <section class="legal-section">
        <h2>Analíticas</h2>
        <p>Actualmente Oxphyre no usa analíticas externas de terceros en estas páginas legales ni en el flujo público básico. El sistema sí puede registrar datos técnicos mínimos propios, como escaneos QR seudonimizados, para mostrar métricas del producto. Si en el futuro se añaden analíticas externas, esta política se actualizará antes de activarlas de forma estable.</p>
      </section>

      <section class="legal-section">
        <h2>Cómo gestionar las cookies</h2>
        <p>El usuario puede borrar, bloquear o limitar cookies desde la configuración de su navegador. Si se bloquean cookies técnicas necesarias, algunas partes de Oxphyre pueden dejar de funcionar correctamente, especialmente el registro, login, dashboard o formularios protegidos.</p>
      </section>

      <section class="legal-section">
        <h2>Cambios y contacto</h2>
        <p>Esta política puede actualizarse si cambian las funciones, preferencias o herramientas técnicas usadas por Oxphyre. Para dudas sobre cookies, escribe a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a>.</p>
      </section>
    </div>
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
