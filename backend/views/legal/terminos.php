<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Términos de uso — Oxphyre</title>
  <meta name="description" content="Términos de uso iniciales de Oxphyre: condiciones de cuenta, contenido, planes, límites, disponibilidad y propiedad intelectual.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/terminos">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/terminos">
  <meta property="og:title" content="Términos de uso — Oxphyre">
  <meta property="og:description" content="Condiciones iniciales de uso de la plataforma Oxphyre.">
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
<body>
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

  <main class="legal-page">
    <header class="legal-hero">
      <p class="legal-kicker">Legal · Términos</p>
      <h1 class="legal-title">Términos de uso</h1>
      <p class="legal-sub">Estas condiciones resumen el uso esperado de Oxphyre durante la fase MVP, TFG y pre-lanzamiento comercial.</p>
    </header>

    <div class="legal-content">
      <p class="legal-note">Este documento es una versión inicial para el proyecto Oxphyre y podrá actualizarse antes del lanzamiento comercial definitivo. En producción real, estos textos deberían revisarse legalmente.</p>

      <section class="legal-section">
        <h2>Qué es Oxphyre</h2>
        <p>Oxphyre es una plataforma web para que negocios locales creen tours virtuales, suban imágenes, organicen posiciones y compartan experiencias públicas mediante enlaces, QR o integraciones disponibles según el plan.</p>
      </section>

      <section class="legal-section">
        <h2>Condiciones de uso</h2>
        <p>El usuario se compromete a usar Oxphyre de forma razonable, respetando la ley, los derechos de terceros y la finalidad del servicio. No está permitido intentar acceder a cuentas ajenas, alterar el funcionamiento de la plataforma, automatizar abusivamente peticiones o publicar contenido ilícito, engañoso o dañino.</p>
      </section>

      <section class="legal-section">
        <h2>Cuenta de usuario</h2>
        <p>Para crear y gestionar tours es necesario registrar una cuenta. El usuario debe proporcionar datos correctos, mantener la confidencialidad de su acceso y avisar si detecta un uso no autorizado. Oxphyre puede limitar o suspender una cuenta si se usa de forma abusiva o compromete la seguridad del proyecto.</p>
      </section>

      <section class="legal-section">
        <h2>Contenido subido por el usuario</h2>
        <p>El usuario es responsable de las imágenes, textos, datos de negocio y cualquier contenido que suba o publique. Debe contar con permisos suficientes para usar fotos del local, marcas, textos, elementos visibles y datos de contacto. Oxphyre no reclama propiedad sobre el contenido del usuario, pero necesita almacenarlo y mostrarlo para prestar el servicio.</p>
      </section>

      <section class="legal-section">
        <h2>Planes Free, Pro y Business</h2>
        <p>Los planes Free, Pro y Business reflejan la estructura comercial prevista para Oxphyre. En esta fase de pre-lanzamiento algunas funciones pueden estar en validación, marcadas como próximamente o activadas para demo. Los precios, límites y funcionalidades pueden ajustarse antes del lanzamiento comercial definitivo.</p>
      </section>

      <section class="legal-section">
        <h2>Límites de uso</h2>
        <p>Cada plan puede limitar negocios, tours, posiciones, marca de agua, QR, embed, analíticas u otras funciones. Oxphyre puede aplicar límites técnicos razonables para proteger estabilidad, almacenamiento y seguridad, especialmente durante la fase MVP.</p>
      </section>

      <section class="legal-section">
        <h2>Uso aceptable</h2>
        <p>No se permite subir malware, contenido ilegal, material que vulnere derechos de terceros, datos personales innecesarios de otras personas o imágenes que expongan información sensible sin permiso. Tampoco se permite usar Oxphyre para spam, phishing, scraping agresivo o actividades que perjudiquen a otros usuarios.</p>
      </section>

      <section class="legal-section">
        <h2>Disponibilidad del servicio</h2>
        <p>Oxphyre se encuentra en desarrollo activo. Se intentará mantener el servicio disponible, pero pueden existir cambios, mantenimientos, errores o interrupciones propias de una fase inicial. Las funciones experimentales pueden cambiar o retirarse si afectan a la estabilidad.</p>
      </section>

      <section class="legal-section">
        <h2>Propiedad intelectual</h2>
        <p>La marca Oxphyre, la interfaz, el código propio, textos, diseño y elementos visuales del producto pertenecen al proyecto Oxphyre o a sus autores. El usuario conserva los derechos sobre sus contenidos, siempre que disponga de ellos legítimamente.</p>
      </section>

      <section class="legal-section">
        <h2>Cambios y contacto</h2>
        <p>Estos términos pueden actualizarse para reflejar cambios del servicio, de los planes o de la operativa real. Para dudas sobre estas condiciones, escribe a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a>.</p>
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
