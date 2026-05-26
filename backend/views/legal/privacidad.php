<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Política de privacidad — Oxphyre</title>
  <meta name="description" content="Política de privacidad inicial de Oxphyre: datos recogidos, finalidades, conservación, derechos del usuario y contacto.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/privacidad">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/privacidad">
  <meta property="og:title" content="Política de privacidad — Oxphyre">
  <meta property="og:description" content="Información clara sobre el tratamiento de datos en Oxphyre.">
  <meta property="og:image" content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">

  <style>
    * { cursor: auto !important; }
    #cursor-ring { display: none !important; }
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
  <nav id="nav" role="navigation" aria-label="Navegación principal">
    <a href="/" class="nav-logo" aria-label="Oxphyre inicio">Oxphyre</a>
    <div class="nav-links">
      <a href="/#como-funciona">Cómo funciona</a>
      <a href="/#demo">Demo</a>
      <a href="/#caracteristicas">Características</a>
      <a href="/precios">Precios</a>
      <a href="/#faq">FAQ</a>
    </div>
    <div class="nav-actions">
      <a href="/login" class="btn-ghost">Iniciar sesión</a>
      <a href="/registro?plan=free" class="btn-primary">Empezar gratis</a>
    </div>
  </nav>

  <main class="legal-page">
    <header class="legal-hero">
      <p class="legal-kicker">Legal · Privacidad</p>
      <h1 class="legal-title">Política de privacidad</h1>
      <p class="legal-sub">Esta página explica, de forma clara, qué datos puede tratar Oxphyre para crear cuentas, gestionar negocios y publicar tours virtuales.</p>
    </header>

    <div class="legal-content">
      <p class="legal-note">Este documento es una versión inicial para el proyecto Oxphyre y podrá actualizarse antes del lanzamiento comercial definitivo. En producción real, estos textos deberían revisarse legalmente.</p>

      <section class="legal-section">
        <h2>Responsable del proyecto</h2>
        <p>Oxphyre es una plataforma web para crear y compartir tours virtuales de negocios locales. En esta fase de TFG y pre-lanzamiento, el proyecto se gestiona bajo la marca Oxphyre. Para cualquier consulta sobre privacidad puedes escribir a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a>.</p>
      </section>

      <section class="legal-section">
        <h2>Datos que recogemos</h2>
        <p>Oxphyre puede tratar los siguientes datos cuando una persona usa la plataforma:</p>
        <ul>
          <li>Datos de cuenta: nombre, email, contraseña almacenada mediante hash y estado de verificación del email.</li>
          <li>Datos del negocio: nombre comercial, descripción, teléfono, dirección, ciudad, código postal, país y datos de ubicación si el usuario decide guardarlos.</li>
          <li>Contenido subido por el usuario: imágenes, panorámicas, tours, posiciones y elementos necesarios para mostrar la experiencia pública.</li>
          <li>Datos técnicos básicos: dirección IP, información de sesión, navegador o datos similares necesarios para seguridad, registro, login y funcionamiento del servicio.</li>
          <li>Datos de uso relacionados con QR: escaneos contabilizados, tipo de dispositivo e identificadores técnicos seudonimizados para evitar duplicados y mostrar métricas básicas.</li>
        </ul>
      </section>

      <section class="legal-section">
        <h2>Para qué usamos los datos</h2>
        <p>Usamos los datos para crear y proteger cuentas, permitir la gestión de negocios y tours, mostrar tours públicos cuando el usuario los publica, ofrecer soporte, prevenir abuso, mantener la seguridad y mejorar el servicio. Las imágenes y datos públicos del negocio se muestran únicamente cuando forman parte de una experiencia publicada o compartida por el usuario.</p>
      </section>

      <section class="legal-section">
        <h2>Base y consentimiento</h2>
        <p>El tratamiento se apoya, de forma general, en la solicitud del usuario para usar Oxphyre, en la necesidad de prestar el servicio y en medidas razonables de seguridad. Cuando una función requiera una acción voluntaria, como publicar un tour, subir imágenes o guardar una dirección, el usuario decide si aporta esos datos.</p>
      </section>

      <section class="legal-section">
        <h2>Conservación</h2>
        <p>Los datos se conservan mientras la cuenta o los contenidos asociados sigan activos, mientras sean necesarios para prestar el servicio o mientras existan obligaciones técnicas, de seguridad o administrativas. Algunos elementos pueden quedar archivados o marcados como eliminados lógicamente para evitar pérdida accidental y mantener coherencia interna.</p>
      </section>

      <section class="legal-section">
        <h2>Derechos del usuario</h2>
        <p>El usuario puede solicitar acceso, rectificación, eliminación u oposición al tratamiento de sus datos escribiendo a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a>. Es posible que pidamos información mínima para verificar la identidad antes de aplicar cambios sobre una cuenta o un negocio.</p>
      </section>

      <section class="legal-section">
        <h2>Servicios externos</h2>
        <p>Oxphyre puede usar servicios técnicos para correo transaccional, mapas, alojamiento, almacenamiento o seguridad. En esta fase se minimiza el uso de integraciones externas y se evita introducir servicios de pago o seguimiento no necesarios para el TFG/MVP.</p>
      </section>
    </div>
  </main>

  <footer id="footer" role="contentinfo">
    <div class="footer-inner">
      <div class="footer-top">
        <div class="footer-brand footer-col">
          <a href="/" class="footer-logo">Oxphyre</a>
          <p class="footer-tagline">Tours virtuales 3D para negocios locales.</p>
        </div>
        <div class="footer-col">
          <p class="footer-col-title">Producto</p>
          <ul>
            <li><a href="/#caracteristicas">Características</a></li>
            <li><a href="/precios">Precios</a></li>
            <li><a href="/#demo">Demo</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <p class="footer-col-title">Legal</p>
          <ul>
            <li><a href="/privacidad">Privacidad</a></li>
            <li><a href="/terminos">Términos</a></li>
            <li><a href="/cookies">Cookies</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <p class="footer-col-title">Contacto</p>
          <ul>
            <li><a href="/contacto">Contacto</a></li>
            <li><a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p class="footer-copyright">© <?= date('Y') ?> Oxphyre. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</body>
</html>
