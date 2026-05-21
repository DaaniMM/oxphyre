<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Soporte y Ayuda | Oxphyre</title>
  <meta name="description" content="Centro de ayuda de Oxphyre: crear cuenta, negocio, tours, fotos, zonas, flechas, QR, ubicación, planes y recuperación de contraseña.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/soporte">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/soporte">
  <meta property="og:title" content="Soporte y Ayuda | Oxphyre">
  <meta property="og:description" content="Guía básica para empezar a usar Oxphyre y resolver dudas frecuentes.">
  <meta property="og:image" content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">

  <style>
    * { cursor: auto !important; }
    #cursor-ring { display: none !important; }
    #nav { opacity: 1 !important; pointer-events: auto !important; }

    .support-page {
      min-height: 100vh;
      padding-top: var(--nav-h);
      overflow-x: hidden;
      background:
        radial-gradient(circle at 50% 0%, rgba(254, 179, 84, 0.15), transparent 34%),
        #000;
      color: var(--text-1);
    }

    .support-hero {
      max-width: 980px;
      margin: 0 auto;
      padding: 88px 24px 44px;
      text-align: center;
    }

    .support-kicker {
      margin-bottom: 16px;
      font-family: var(--font-mono);
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--accent);
    }

    .support-title {
      font-family: var(--font-display);
      font-size: clamp(2.5rem, 7vw, 5rem);
      line-height: 0.96;
      color: var(--text-1);
      overflow-wrap: break-word;
    }

    .support-subtitle {
      max-width: 760px;
      margin: 24px auto 0;
      color: var(--text-2);
      font-size: clamp(1rem, 2vw, 1.18rem);
      line-height: 1.75;
    }

    .support-actions {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
      margin-top: 32px;
    }

    .support-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      padding: 0 22px;
      border-radius: 8px;
      font-weight: 700;
      text-decoration: none;
    }

    .support-button-primary {
      background: var(--accent);
      color: #000 !important;
      box-shadow: 0 16px 40px rgba(254, 179, 84, 0.25);
    }

    .support-button-secondary {
      border: 1px solid rgba(255, 255, 255, 0.22);
      color: var(--text-1);
      background: rgba(255, 255, 255, 0.04);
    }

    .support-content {
      max-width: 1040px;
      margin: 0 auto;
      padding: 20px 24px 96px;
    }

    .support-section {
      padding: 34px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .support-section h2 {
      max-width: 760px;
      margin-bottom: 16px;
      font-family: var(--font-display);
      font-size: clamp(1.55rem, 3vw, 2.35rem);
      color: var(--text-1);
    }

    .support-section p,
    .support-section li {
      color: var(--text-2);
      line-height: 1.75;
    }

    .support-section a {
      color: var(--accent);
    }

    .support-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin-top: 22px;
    }

    .support-card {
      padding: 22px;
      border: 1px solid rgba(255, 255, 255, 0.11);
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.045);
    }

    .support-card h3 {
      margin-bottom: 10px;
      color: var(--text-1);
      font-size: 1.05rem;
    }

    .support-card p {
      margin: 0;
    }

    .support-list {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
      margin-top: 22px;
      padding: 0;
      list-style: none;
    }

    .support-list li {
      padding: 18px;
      border: 1px solid rgba(255, 255, 255, 0.11);
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.04);
    }

    .support-contact {
      margin-top: 22px;
      padding: 24px;
      border: 1px solid rgba(254, 179, 84, 0.24);
      border-radius: 8px;
      background: rgba(254, 179, 84, 0.07);
    }

    @media (max-width: 768px) {
      .support-hero {
        padding-top: 70px;
        text-align: left;
      }

      .support-actions {
        justify-content: flex-start;
      }

      .support-button {
        width: 100%;
      }

      .support-grid,
      .support-list {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <nav id="nav" role="navigation" aria-label="Navegación principal">
    <a href="/" class="nav-logo" aria-label="Oxphyre inicio">Oxphyre</a>
    <div class="nav-links">
      <a href="/tour-virtual-para-negocios">Tour para negocios</a>
      <a href="/precios">Precios</a>
      <a href="/sobre-nosotros">Sobre nosotros</a>
    </div>
    <div class="nav-actions">
      <a href="/login" class="btn-ghost">Iniciar sesión</a>
      <a href="/registro?plan=free" class="btn-primary">Empezar gratis</a>
    </div>
  </nav>

  <main class="support-page">
    <header class="support-hero">
      <p class="support-kicker">Soporte</p>
      <h1 class="support-title">Centro de ayuda de Oxphyre</h1>
      <p class="support-subtitle">Una guía simple para empezar: cuenta, negocio, fotos, zonas, flechas, QR, ubicación, planes y acceso. Si necesitas ayuda directa, puedes escribirnos.</p>
      <div class="support-actions">
        <a class="support-button support-button-primary" href="/registro?plan=free">Crear cuenta gratis</a>
        <a class="support-button support-button-secondary" href="/precios">Ver planes</a>
      </div>
    </header>

    <div class="support-content">
      <section class="support-section">
        <h2>Primeros pasos</h2>
        <div class="support-grid">
          <article class="support-card">
            <h3>Crear cuenta</h3>
            <p>Regístrate desde <a href="/registro?plan=free">/registro</a>, confirma tus datos y accede al panel para empezar a preparar tu primer negocio.</p>
          </article>
          <article class="support-card">
            <h3>Crear negocio</h3>
            <p>Desde el dashboard puedes registrar el nombre del negocio, su información básica y la ubicación que después ayudará a presentarlo mejor.</p>
          </article>
          <article class="support-card">
            <h3>Recuperar contraseña</h3>
            <p>Si no puedes entrar, usa la pantalla de recuperación de contraseña para solicitar un enlace de restablecimiento.</p>
          </article>
          <article class="support-card">
            <h3>Elegir plan</h3>
            <p>El plan Free sirve para empezar. Pro y Business están pensados para negocios que necesitan más capacidad o una presencia más completa.</p>
          </article>
        </div>
      </section>

      <section class="support-section">
        <h2>Crear una visita virtual</h2>
        <p>Oxphyre organiza el recorrido por posiciones o zonas. Cada zona representa una parte del local: entrada, barra, comedor, sala, recepción, escaparate o cualquier punto que ayude al cliente a entender el espacio.</p>
        <ul class="support-list">
          <li><strong>Subir fotos:</strong> añade imágenes claras del local, preferiblemente horizontales, luminosas y sin elementos sensibles a la vista.</li>
          <li><strong>Crear posiciones o zonas:</strong> separa el recorrido en puntos concretos para que la visita tenga orden.</li>
          <li><strong>Conectar con flechas:</strong> enlaza las zonas para que el visitante avance de forma natural dentro del tour.</li>
          <li><strong>Revisar la vista pública:</strong> comprueba que el recorrido se entiende antes de compartirlo con clientes.</li>
        </ul>
      </section>

      <section class="support-section">
        <h2>Compartir el tour</h2>
        <div class="support-grid">
          <article class="support-card">
            <h3>Enlace público</h3>
            <p>Cuando el tour esté listo, puedes compartirlo con un enlace para que tus clientes lo abran desde el móvil o el ordenador.</p>
          </article>
          <article class="support-card">
            <h3>QR</h3>
            <p>El QR permite llevar la visita virtual a carteles, mesas, escaparates, tarjetas o cualquier material físico del negocio.</p>
          </article>
          <article class="support-card">
            <h3>Mapa y ubicación</h3>
            <p>Añadir la ubicación ayuda a contextualizar el negocio y facilita que el cliente pase de explorar el espacio a planificar la visita.</p>
          </article>
          <article class="support-card">
            <h3>Planes</h3>
            <p>Consulta <a href="/precios">precios</a> para comparar Free, Pro y Business según el número de tours, zonas y necesidades del negocio.</p>
          </article>
        </div>
      </section>

      <section class="support-section">
        <h2>Dudas habituales</h2>
        <ul class="support-list">
          <li><strong>¿Necesito cámara especial?</strong> No para empezar. Oxphyre está pensado para crear visitas con fotos del local.</li>
          <li><strong>¿Puedo actualizar fotos?</strong> La idea es que puedas mantener el tour alineado con el estado real del negocio desde tu panel.</li>
          <li><strong>¿Qué fotos funcionan mejor?</strong> Imágenes nítidas, bien iluminadas y tomadas desde puntos donde un cliente se colocaría al entrar o moverse por el local.</li>
          <li><strong>¿Dónde pido ayuda?</strong> Escríbenos a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a> con el correo de tu cuenta y una explicación breve.</li>
        </ul>
        <div class="support-contact">
          <h2>Contacto</h2>
          <p>Para soporte, dudas comerciales o problemas de acceso, escribe a <a href="mailto:hola@oxphyre.com">hola@oxphyre.com</a>. Intentaremos ayudarte con una respuesta clara y práctica.</p>
          <div class="support-actions">
            <a class="support-button support-button-primary" href="/registro?plan=free">Empezar gratis</a>
            <a class="support-button support-button-secondary" href="/precios">Ver planes y precios</a>
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
          <p class="footer-tagline">Tours virtuales para negocios locales.</p>
        </div>
        <div class="footer-col">
          <p class="footer-col-title">Producto</p>
          <ul>
            <li><a href="/tour-virtual-para-negocios">Tour para negocios</a></li>
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
            <li><a href="/sobre-nosotros">Sobre nosotros</a></li>
            <li><a href="/soporte">Soporte</a></li>
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
