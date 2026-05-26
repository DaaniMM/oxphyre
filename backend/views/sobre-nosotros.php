<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Sobre Nosotros | Oxphyre</title>
  <meta name="description" content="Conoce Oxphyre: una herramienta para que negocios locales creen visitas virtuales con fotos, zonas navegables y enlaces fáciles de compartir.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/sobre-nosotros">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/sobre-nosotros">
  <meta property="og:title" content="Sobre Nosotros | Oxphyre">
  <meta property="og:description" content="Oxphyre ayuda a negocios locales a mostrar su espacio con visitas virtuales creadas por ellos mismos.">
  <meta property="og:image" content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">

  <style>
    #nav { opacity: 1 !important; pointer-events: auto !important; }

    .info-page {
      min-height: 100vh;
      padding-top: var(--nav-h);
      overflow-x: hidden;
      background:
        radial-gradient(circle at 50% 0%, rgba(254, 179, 84, 0.15), transparent 34%),
        #000;
      color: var(--text-1);
    }

    .info-hero {
      max-width: 980px;
      margin: 0 auto;
      padding: 88px 24px 44px;
      text-align: center;
    }

    .info-kicker {
      margin-bottom: 16px;
      font-family: var(--font-mono);
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--accent);
    }

    .info-title {
      font-family: var(--font-display);
      font-size: clamp(2.5rem, 7vw, 5.1rem);
      line-height: 0.96;
      color: var(--text-1);
      overflow-wrap: break-word;
    }

    .info-subtitle {
      max-width: 760px;
      margin: 24px auto 0;
      color: var(--text-2);
      font-size: clamp(1rem, 2vw, 1.18rem);
      line-height: 1.75;
    }

    .info-actions {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-top: 32px;
    }

    .info-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      padding: 0 22px;
      border-radius: 8px;
      font-weight: 700;
      text-decoration: none;
    }

    .info-button-primary {
      background: var(--accent);
      color: #000 !important;
      box-shadow: 0 16px 40px rgba(254, 179, 84, 0.25);
    }

    .info-button-secondary {
      border: 1px solid rgba(255, 255, 255, 0.22);
      color: var(--text-1);
      background: rgba(255, 255, 255, 0.04);
    }

    .info-content {
      max-width: 980px;
      margin: 0 auto;
      padding: 20px 24px 96px;
    }

    .info-section {
      padding: 34px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .info-section h2 {
      max-width: 760px;
      margin-bottom: 16px;
      font-family: var(--font-display);
      font-size: clamp(1.55rem, 3vw, 2.35rem);
      color: var(--text-1);
    }

    .info-section p,
    .info-section li {
      color: var(--text-2);
      line-height: 1.75;
    }

    .info-section p {
      max-width: 780px;
    }

    .info-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin-top: 22px;
    }

    .info-card {
      padding: 22px;
      border: 1px solid rgba(255, 255, 255, 0.11);
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.045);
    }

    .info-card h3 {
      margin-bottom: 10px;
      color: var(--text-1);
      font-size: 1.05rem;
    }

    .info-card p {
      margin: 0;
    }

    .info-note {
      margin-top: 20px;
      padding: 20px;
      border: 1px solid rgba(254, 179, 84, 0.24);
      border-radius: 8px;
      background: rgba(254, 179, 84, 0.07);
    }

    .info-section a {
      color: var(--accent);
    }

    .info-cta {
      margin-top: 22px;
      padding: 28px;
      border: 1px solid rgba(254, 179, 84, 0.24);
      border-radius: 8px;
      background: linear-gradient(135deg, rgba(254, 179, 84, 0.13), rgba(255, 255, 255, 0.04));
    }

    @media (max-width: 768px) {
      .info-hero {
        padding-top: 70px;
        text-align: left;
      }

      .info-actions {
        justify-content: flex-start;
      }

      .info-button {
        width: 100%;
      }

      .info-grid {
        grid-template-columns: 1fr;
      }
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

  <main class="info-page">
    <header class="info-hero">
      <p class="info-kicker">Sobre Oxphyre</p>
      <h1 class="info-title">Una forma más simple de enseñar tu negocio por dentro</h1>
      <p class="info-subtitle">Oxphyre nace para que restaurantes, bares, comercios, peluquerías, gimnasios pequeños y otros negocios locales puedan crear una visita virtual sin depender de una producción externa.</p>
      <div class="info-actions">
        <a class="info-button info-button-primary" href="/tour-virtual-para-negocios">Ver cómo funciona</a>
        <a class="info-button info-button-secondary" href="/precios">Ver planes y precios</a>
      </div>
    </header>

    <div class="info-content">
      <section class="info-section">
        <h2>Qué es Oxphyre</h2>
        <p>Oxphyre es una herramienta web para crear visitas virtuales navegables a partir de fotos del propio local. La idea es sencilla: subes imágenes, organizas zonas, conectas recorridos con flechas y compartes el resultado mediante un enlace o un QR.</p>
        <p>No está pensado para sustituir una producción audiovisual compleja, sino para resolver una necesidad mucho más cotidiana: que un cliente pueda entender cómo es un espacio antes de visitarlo.</p>
      </section>

      <section class="info-section">
        <h2>El problema que queremos resolver</h2>
        <p>Muchos negocios locales tienen buenas fotos, buen ambiente y un espacio cuidado, pero ese valor se pierde cuando el cliente solo ve una ficha, una galería desordenada o una publicación aislada en redes. Oxphyre busca convertir esas fotos en una experiencia más clara, guiada y fácil de compartir.</p>
        <div class="info-grid">
          <article class="info-card">
            <h3>Menos fricción</h3>
            <p>El dueño puede empezar con su móvil, sin coordinar una sesión externa para cada pequeño cambio del local.</p>
          </article>
          <article class="info-card">
            <h3>Más contexto</h3>
            <p>El cliente no solo ve imágenes: recorre zonas y entiende mejor mesas, entrada, barra, salas o escaparate.</p>
          </article>
        </div>
      </section>

      <section class="info-section">
        <h2>Enfoque para negocios locales y PYMES</h2>
        <p>Oxphyre se diseña pensando en negocios que necesitan algo útil, entendible y asumible. No todo local necesita una producción grande para mostrar su espacio; muchas veces basta con una visita clara, actualizable y compartible desde la web, la ficha del negocio, redes sociales o material impreso.</p>
      </section>

      <section class="info-section">
        <h2>Transparencia del proyecto</h2>
        <p>Oxphyre nace como Trabajo de Fin de Grado de Desarrollo de Aplicaciones Web y se está construyendo con vocación comercial real. Eso significa que combina una base académica, decisiones técnicas documentadas y una intención práctica: convertirse en una herramienta útil para negocios reales.</p>
        <p class="info-note">Preferimos explicar el estado del producto con claridad antes que prometer más de lo que existe. Las funciones disponibles se comunican en la web y los planes; las mejoras futuras se tratarán como evolución del producto, no como algo ya incluido.</p>
      </section>

      <section class="info-section">
        <h2>Visión</h2>
        <p>La visión de Oxphyre es hacer que enseñar un local por internet sea tan natural como subir fotos, pero con más orden, navegación y contexto. Queremos que un pequeño negocio pueda tener una presencia visual más completa sin necesitar conocimientos técnicos.</p>
        <div class="info-cta">
          <h2>Empieza por la página guía</h2>
          <p>Si quieres entender el caso de uso principal, hemos preparado una explicación específica sobre cómo crear un tour virtual para un negocio con fotos y zonas navegables.</p>
          <div class="info-actions">
            <a class="info-button info-button-primary" href="/tour-virtual-para-negocios">Crear un tour para mi negocio</a>
            <a class="info-button info-button-secondary" href="/precios">Comparar planes</a>
          </div>
        </div>
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
