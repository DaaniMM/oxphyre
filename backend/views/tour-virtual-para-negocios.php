<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Crea un Tour Virtual para tu Negocio con el Móvil | Oxphyre</title>
  <meta name="description" content="Sube fotos de tu local y crea una visita inmersiva navegable. Tus clientes exploran zona a zona antes de llegar. Sin cámara 360 ni fotógrafo.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/tour-virtual-para-negocios">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type" content="website">
  <meta property="og:url" content="https://oxphyre.com/tour-virtual-para-negocios">
  <meta property="og:title" content="Crea un Tour Virtual para tu Negocio con el Móvil | Oxphyre">
  <meta property="og:description" content="Crea una visita virtual navegable de tu local con fotos hechas desde el móvil. Sin cámara 360 ni fotógrafo.">
  <meta property="og:image" content="https://oxphyre.com/assets/og-image.png">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Crea un Tour Virtual para tu Negocio con el Móvil | Oxphyre">
  <meta name="twitter:description" content="Sube fotos de tu local y permite que tus clientes exploren zona a zona antes de llegar.">
  <meta name="twitter:image" content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "SoftwareApplication",
        "@id": "https://oxphyre.com/#software",
        "name": "Oxphyre",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "url": "https://oxphyre.com/tour-virtual-para-negocios",
        "description": "Herramienta web para crear visitas virtuales navegables de negocios locales con fotos hechas desde el móvil.",
        "offers": {
          "@type": "Offer",
          "name": "Plan Free",
          "price": "0",
          "priceCurrency": "EUR",
          "url": "https://oxphyre.com/registro?plan=free"
        }
      },
      {
        "@type": "FAQPage",
        "@id": "https://oxphyre.com/tour-virtual-para-negocios#seo-faq",
        "mainEntity": [
          {
            "@type": "Question",
            "name": "¿Puedo crear la visita virtual de mi negocio solo con el móvil?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Sí. Oxphyre está pensado para que puedas empezar con fotos hechas desde un móvil normal. No necesitas contratar una agencia ni comprar una cámara especial."
            }
          },
          {
            "@type": "Question",
            "name": "¿Necesito una cámara 360 para usar Oxphyre?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "No. Puedes subir fotos o panorámicas hechas con tu móvil y organizar el recorrido por zonas del local."
            }
          },
          {
            "@type": "Question",
            "name": "¿Qué tipo de negocios pueden usar un tour virtual?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Funciona especialmente bien para restaurantes, bares, peluquerías, barberías, comercios y gimnasios pequeños que quieren enseñar su espacio antes de la visita."
            }
          },
          {
            "@type": "Question",
            "name": "¿Puedo empezar gratis?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Sí. El plan Free permite crear un primer tour para probar la idea y compartirlo con enlace público o QR básico."
            }
          }
        ]
      },
      {
        "@type": "BreadcrumbList",
        "@id": "https://oxphyre.com/tour-virtual-para-negocios#breadcrumb",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Inicio",
            "item": "https://oxphyre.com/"
          },
          {
            "@type": "ListItem",
            "position": 2,
            "name": "Tour virtual para negocios",
            "item": "https://oxphyre.com/tour-virtual-para-negocios"
          }
        ]
      }
    ]
  }
  </script>

  <style>
    #nav { opacity: 1 !important; pointer-events: auto !important; }

    .seo-page {
      min-height: 100vh;
      padding-top: var(--nav-h);
      overflow-x: hidden;
      background:
        radial-gradient(circle at 48% 0%, rgba(254, 179, 84, 0.18), transparent 36%),
        linear-gradient(180deg, #050505 0%, #000 34%, #050505 100%);
    }
    .seo-section,
    .seo-hero {
      width: 100%;
      max-width: 960px;
      margin: 0 auto;
      padding-left: 24px;
      padding-right: 24px;
    }
    .seo-hero {
      padding-top: 92px;
      padding-bottom: 54px;
    }
    .seo-kicker {
      margin-bottom: 18px;
      font-family: var(--font-mono);
      font-size: 12px;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--accent);
    }
    .seo-h1 {
      max-width: 980px;
      font-family: var(--font-display);
      font-size: clamp(2.4rem, 6vw, 5.4rem);
      line-height: 0.96;
      color: var(--text-1);
      letter-spacing: 0;
    }
    .seo-subtitle {
      max-width: 760px;
      margin-top: 28px;
      color: var(--text-2);
      font-size: clamp(1.05rem, 2vw, 1.25rem);
      line-height: 1.75;
    }
    .seo-actions,
    .seo-final-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      margin-top: 34px;
    }
    .seo-primary,
    .seo-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 46px;
      padding: 13px 22px;
      border-radius: 8px;
      font-weight: 700;
      transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .seo-primary {
      background: var(--accent);
      color: #000 !important;
      box-shadow: 0 0 24px rgba(254, 179, 84, 0.18);
    }
    .seo-secondary {
      border: 1px solid rgba(255, 255, 255, 0.14);
      color: var(--text-1);
      background: rgba(255, 255, 255, 0.03);
    }
    .seo-primary:hover,
    .seo-secondary:hover {
      transform: translateY(-2px);
      border-color: rgba(254, 179, 84, 0.4);
      box-shadow: 0 0 28px rgba(254, 179, 84, 0.22);
    }
    .seo-primary:hover {
      color: #000;
    }
    .seo-section {
      padding-top: 58px;
      padding-bottom: 58px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .seo-section h2 {
      max-width: 760px;
      margin-bottom: 18px;
      font-family: var(--font-display);
      font-size: clamp(1.75rem, 3.5vw, 2.7rem);
      line-height: 1.12;
      color: var(--text-1);
    }
    .seo-section h3 {
      margin-top: 22px;
      margin-bottom: 8px;
      font-family: var(--font-display);
      font-size: 1.12rem;
      color: var(--text-1);
    }
    .seo-section p,
    .seo-section li {
      max-width: 760px;
      color: var(--text-2);
      line-height: 1.78;
    }
    .seo-section p a,
    .seo-section li a {
      color: var(--accent);
    }
    .seo-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin-top: 24px;
    }
    .seo-card {
      padding: 22px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.035);
    }
    .seo-card p,
    .seo-card li {
      color: var(--text-2);
    }
    .seo-card strong {
      color: var(--text-1);
    }
    .seo-steps {
      display: grid;
      gap: 14px;
      margin-top: 24px;
      counter-reset: seo-step;
    }
    .seo-step {
      position: relative;
      padding: 20px 22px 20px 68px;
      border: 1px solid rgba(254, 179, 84, 0.16);
      border-radius: 8px;
      background: rgba(254, 179, 84, 0.045);
    }
    .seo-step::before {
      counter-increment: seo-step;
      content: counter(seo-step, decimal-leading-zero);
      position: absolute;
      left: 22px;
      top: 21px;
      font-family: var(--font-mono);
      font-size: 13px;
      color: var(--accent);
    }
    .seo-step p {
      margin: 0;
    }
    .seo-list {
      margin-top: 16px;
      padding-left: 20px;
    }
    .seo-faq {
      display: grid;
      gap: 18px;
      margin-top: 26px;
    }
    .seo-faq article {
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .seo-final {
      padding-bottom: 96px;
      text-align: left;
    }

    @media (max-width: 768px) {
      .seo-hero {
        padding-top: 72px;
        padding-bottom: 38px;
      }
      .seo-h1 {
        font-size: clamp(2rem, 9vw, 2.4rem);
        line-height: 1.05;
        overflow-wrap: break-word;
      }
      .seo-section h2 {
        overflow-wrap: break-word;
      }
      .nav-actions {
        display: flex;
        margin-left: auto;
      }
      .nav-actions .btn-primary {
        display: inline-flex;
        padding: 8px 10px;
        font-size: 12px;
        white-space: nowrap;
      }
      .seo-grid {
        grid-template-columns: 1fr;
      }
      .seo-actions,
      .seo-final-actions {
        flex-direction: column;
      }
      .seo-primary,
      .seo-secondary {
        width: 100%;
      }
      .seo-section {
        padding-top: 44px;
        padding-bottom: 44px;
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

  <main class="seo-page">
    <header class="seo-hero">
      <p class="seo-kicker">Tour virtual para negocios</p>
      <h1 class="seo-h1">Crea la visita virtual de tu negocio con tu móvil — tú mismo, sin agencias ni cámaras 360</h1>
      <p class="seo-subtitle">Oxphyre no es una agencia de tours virtuales: es una herramienta para que prepares tu propio recorrido con fotos, zonas, flechas, QR y enlace público. Tus clientes pueden explorar zona a zona antes de llegar, entender mejor el ambiente y decidir con más confianza si quieren reservar, comprar o visitarte.</p>
      <div class="seo-actions" aria-label="Acciones principales">
        <a class="seo-primary" href="/registro?plan=free">Crear mi tour gratis →</a>
        <a class="seo-secondary" href="/precios">Ver planes y precios →</a>
      </div>
    </header>

    <section class="seo-section" aria-labelledby="que-es">
      <h2 id="que-es">Qué es una visita virtual para negocios</h2>
      <p>Una visita virtual para negocios es una forma visual de enseñar tu local en internet. En lugar de mostrar solo una galería de fotos sueltas, organizas el espacio por zonas: entrada, barra, mesas, sala, cabinas, escaparate, zona de espera o cualquier parte relevante de tu negocio.</p>
      <p>El visitante no mira una imagen aislada. Avanza por el recorrido, cambia de zona y entiende cómo es el sitio antes de ir físicamente. Para un restaurante, puede ver el ambiente. Para una peluquería, puede hacerse una idea del estilo. Para una tienda, puede reconocer el espacio y perder menos tiempo al llegar.</p>
    </section>

    <section class="seo-section" aria-labelledby="tu-lo-haces">
      <h2 id="tu-lo-haces">La diferencia clave: tú lo haces, no necesitas agencia</h2>
      <p>La mayoría de soluciones de visita virtual parecen pensadas para proyectos grandes, sesiones con fotógrafo o presupuestos que no siempre encajan con un pequeño negocio. Oxphyre parte de otra idea: si tú conoces tu local mejor que nadie, también puedes crear una primera experiencia útil con tu propio móvil.</p>
      <p>No hace falta coordinar una sesión externa ni esperar entregas. Puedes preparar el local, hacer fotos cuando esté ordenado y subirlas a tu ritmo. La herramienta está pensada para que el resultado sea claro y compartible sin convertir el proceso en una producción complicada.</p>
    </section>

    <section class="seo-section" aria-labelledby="seo-como-funciona">
      <h2 id="seo-como-funciona">Cómo funciona Oxphyre</h2>
      <p>Oxphyre estructura el tour por zonas para que el recorrido tenga sentido. El objetivo no es prometer magia automática, sino darte una forma práctica de construir una visita navegable con fotos reales de tu negocio.</p>
      <div class="seo-steps">
        <div class="seo-step"><p><strong>Haces o subes fotos del local.</strong> Puedes empezar con imágenes tomadas desde el móvil, cuidando luz, orden y encuadre.</p></div>
        <div class="seo-step"><p><strong>Creas zonas.</strong> Cada zona representa un punto del recorrido: entrada, salón, barra, sala principal, escaparate o espacio de trabajo.</p></div>
        <div class="seo-step"><p><strong>Conectas zonas con flechas.</strong> Así el cliente entiende por dónde avanzar y cómo moverse dentro de la visita.</p></div>
        <div class="seo-step"><p><strong>Compartes con enlace o QR.</strong> Puedes enviar el enlace, poner el QR en el escaparate o usarlo en materiales comerciales.</p></div>
        <div class="seo-step"><p><strong>Añades la ubicación del negocio.</strong> El visitante puede ver dónde estás y pasar de explorar el local a planear la visita real.</p></div>
      </div>
    </section>

    <section class="seo-section" aria-labelledby="movil">
      <h2 id="movil">Tour virtual con móvil, sin cámara 360</h2>
      <p>Oxphyre está pensado para negocios que ya tienen lo necesario para empezar: un móvil y un local que enseñar. Una cámara especial puede mejorar algunos resultados, pero no debería ser una barrera para validar si una visita virtual ayuda a tus clientes.</p>
      <p>Lo importante es enseñar el espacio de forma honesta. Si una foto no cubre todo el entorno, no pasa nada: el recorrido puede organizarse por zonas. El cliente no necesita una promesa técnica complicada; necesita hacerse una idea clara de cómo es tu negocio, qué ambiente tiene y qué encontrará al llegar.</p>
    </section>

    <section class="seo-section" aria-labelledby="negocios">
      <h2 id="negocios">Para qué negocios funciona mejor</h2>
      <p>Un tour virtual para negocios tiene más sentido cuando el espacio influye en la decisión del cliente. Si la estética, la distribución, la comodidad o la confianza importan, enseñar el local antes de la visita puede reducir dudas.</p>
      <div class="seo-grid">
        <article class="seo-card">
          <h3>Restaurantes y bares</h3>
          <p>Ayuda a enseñar ambiente, mesas, barra, terraza interior o rincones especiales antes de una reserva.</p>
        </article>
        <article class="seo-card">
          <h3>Peluquerías y barberías</h3>
          <p>Permite transmitir estilo, limpieza, comodidad y profesionalidad antes de que alguien pida cita.</p>
        </article>
        <article class="seo-card">
          <h3>Comercios</h3>
          <p>Sirve para mostrar escaparate, distribución, zonas de producto y sensación general de la tienda.</p>
        </article>
        <article class="seo-card">
          <h3>Gimnasios pequeños</h3>
          <p>Hace visible el tamaño, las máquinas, zonas de entrenamiento y ambiente antes de apuntarse.</p>
        </article>
      </div>
    </section>

    <section class="seo-section" aria-labelledby="cliente">
      <h2 id="cliente">Lo que ve el cliente</h2>
      <p>El cliente abre una URL pública desde el móvil o el ordenador. Entra en una zona del negocio, mira la imagen principal, avanza con flechas y descubre otras partes del local. La experiencia no obliga a instalar nada y está pensada para una navegación directa.</p>
      <p>Esto puede acompañar tus redes sociales, tu ficha de negocio, una campaña local, un cartel con QR o una conversación por WhatsApp. En vez de decir “ven a verlo”, puedes enviar una visita previa para que la persona llegue con una imagen más clara del sitio.</p>
    </section>

    <section class="seo-section" aria-labelledby="free">
      <h2 id="free">Plan Free para empezar</h2>
      <p>Puedes empezar con el plan Free y crear tu primer tour sin tarjeta. Es una forma sencilla de probar si una visita virtual encaja con tu negocio antes de pasar a un plan de pago.</p>
      <p>El plan Free está pensado para validar la idea: un negocio, un tour, hasta 3 zonas básicas, enlace público, QR básico y marca Oxphyre visible. Si después quieres más capacidad o una presentación más profesional, puedes revisar los <a href="/precios">planes y precios</a>.</p>
      <div class="seo-actions">
        <a class="seo-primary" href="/registro?plan=free">Crear mi tour gratis →</a>
        <a class="seo-secondary" href="/precios">Ver planes y precios →</a>
      </div>
    </section>

    <section class="seo-section" id="seo-faq" aria-labelledby="seo-faq-title">
      <h2 id="seo-faq-title">Preguntas frecuentes</h2>
      <div class="seo-faq">
        <article>
          <h3>¿Puedo crear la visita virtual de mi negocio solo con el móvil?</h3>
          <p>Sí. Oxphyre está pensado para empezar con fotos hechas desde un móvil normal. Cuanto mejor sea la luz y más cuidado esté el local, mejor será la percepción final.</p>
        </article>
        <article>
          <h3>¿Necesito una cámara 360?</h3>
          <p>No. Puedes usar fotos o panorámicas hechas desde tu móvil y organizarlas por zonas. El recorrido se construye conectando esas zonas para que el visitante entienda el espacio.</p>
        </article>
        <article>
          <h3>¿Esto sustituye a una sesión profesional?</h3>
          <p>No necesariamente. Una sesión profesional puede tener más calidad visual, pero Oxphyre busca otra cosa: que un pequeño negocio pueda crear una visita útil, rápida y compartible sin depender de terceros.</p>
        </article>
        <article>
          <h3>¿Dónde puedo compartir mi tour?</h3>
          <p>Puedes compartirlo mediante enlace público o QR básico desde el plan Free. Es útil para redes sociales, mensajes directos, carteles, escaparate o material de presentación.</p>
        </article>
        <article>
          <h3>¿Cuánto tiempo se tarda en preparar un primer tour?</h3>
          <p>Depende del tamaño del local y de cuántas zonas quieras enseñar. Para un negocio pequeño, puedes empezar con unas pocas zonas bien elegidas y ampliarlo después.</p>
        </article>
      </div>
    </section>

    <section class="seo-section" aria-labelledby="recursos-relacionados">
      <h2 id="recursos-relacionados">Recursos relacionados</h2>
      <p>Si quieres profundizar sin perder de vista esta guía principal, puedes revisar recursos específicos para preparar fotos, crear un recorrido con móvil, compartirlo con QR o aplicarlo a restauración.</p>
      <div class="seo-grid">
        <article class="seo-card">
          <h3>Restaurantes</h3>
          <p>Aplicación sectorial para comedor, barra, terraza y reservas.</p>
          <a href="/tour-virtual-para-restaurantes">Ver tour virtual para restaurantes</a>
        </article>
        <article class="seo-card">
          <h3>Fotos</h3>
          <p>Checklist para preparar el local antes de subir imágenes.</p>
          <a href="/blog/como-hacer-fotos-para-tour-virtual">Cómo hacer fotos para tour virtual</a>
        </article>
        <article class="seo-card">
          <h3>Móvil</h3>
          <p>Qué puedes conseguir sin cámara 360 y qué límites conviene explicar.</p>
          <a href="/blog/tour-virtual-con-movil-sin-camara-360">Tour virtual con móvil</a>
        </article>
        <article class="seo-card">
          <h3>QR</h3>
          <p>Ideas para enseñar el local desde escaparate, mesas o tarjetas.</p>
          <a href="/blog/como-usar-qr-para-ensenar-tu-local">Usar QR para enseñar tu local</a>
        </article>
      </div>
    </section>

    <section class="seo-section seo-final" aria-labelledby="seo-cta-final">
      <h2 id="seo-cta-final">Empieza enseñando tu negocio como realmente se vive</h2>
      <p>Tu local ya comunica algo cuando una persona entra. Oxphyre te ayuda a llevar esa primera impresión a internet, con una visita virtual sencilla de crear y fácil de compartir.</p>
      <div class="seo-final-actions">
        <a class="seo-primary" href="/registro?plan=free">Crear mi tour gratis →</a>
        <a class="seo-secondary" href="/precios">Ver planes y precios →</a>
      </div>
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
