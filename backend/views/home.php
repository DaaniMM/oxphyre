<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- ── SEO PRIMARIO ──────────────────────────────────────────────────────── -->
  <!-- Title: keyword principal + nombre de marca, máx 60 caracteres -->
  <title>Oxphyre | Tours Virtuales 3D para tu Negocio</title>

  <!-- Description: résume el valor del producto con la keyword principal en los primeros 100 chars -->
  <meta name="description" content="Crea un tour virtual 3D de tu negocio con fotos de tu móvil. Compártelo con un QR o en tu web. Empieza gratis, sin hardware especial.">

  <!-- Canonical: evita contenido duplicado si la misma página es accesible por varias URLs -->
  <link rel="canonical" href="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/">

  <!-- Keywords (valor informativo para algunos motores, no penaliza tenerlas) -->
  <meta name="keywords" content="tour virtual 3D, tour virtual negocio, visita virtual tienda, tour 360 restaurante, tour virtual gimnasio, tour virtual inmersivo">

  <!-- Robots: indexar la página y seguir los links -->
  <meta name="robots" content="index, follow">

  <!-- Autor -->
  <meta name="author" content="Oxphyre">

  <!-- ── OPEN GRAPH (redes sociales) ──────────────────────────────────────── -->
  <!-- OG permite controlar cómo se ve la página cuando se comparte en redes -->
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/">
  <meta property="og:title"       content="Oxphyre | Tours Virtuales 3D para tu Negocio">
  <meta property="og:description" content="Crea un tour virtual 3D de tu negocio con fotos de tu móvil. Compártelo con un QR o en tu web. Empieza gratis.">
  <meta property="og:image"       content="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/assets/og-image.jpg">
  <meta property="og:locale"      content="es_ES">
  <meta property="og:site_name"   content="Oxphyre">

  <!-- ── TWITTER CARD ─────────────────────────────────────────────────────── -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="Oxphyre | Tours Virtuales 3D para tu Negocio">
  <meta name="twitter:description" content="Tours virtuales 3D para negocios locales. Fotos de móvil → tour inmersivo → QR para tus clientes.">
  <meta name="twitter:image"       content="<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>/assets/og-image.jpg">

  <!-- ── FUENTES (Google Fonts con preconnect para rendimiento) ───────────── -->
  <!-- preconnect establece la conexión con los servidores de Google Fonts
       antes de que el navegador las necesite, reduciendo la latencia percibida -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- display=swap: el texto se muestra con la fuente fallback mientras carga la web,
       evitando el FOIT (Flash of Invisible Text) que penaliza el PageSpeed -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap">

  <!-- ── CSS PRINCIPAL ─────────────────────────────────────────────────────── -->
  <link rel="stylesheet" href="/css/main.css">

  <!-- ── SCHEMA.ORG: SoftwareApplication ──────────────────────────────────── -->
  <!-- JSON-LD no es JavaScript ejecutable, por lo que no viola la CSP script-src.
       Google lo lee para entender qué es la aplicación y puede mostrar rich results. -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Oxphyre",
    "url": "<?= htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') ?>",
    "description": "Plataforma SaaS para crear tours virtuales 3D de negocios locales con inteligencia artificial y compartirlos mediante QR.",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "offers": [
      {
        "@type": "Offer",
        "name": "Free",
        "price": "0",
        "priceCurrency": "EUR"
      },
      {
        "@type": "Offer",
        "name": "Pro",
        "price": "19",
        "priceCurrency": "EUR",
        "billingIncrement": "P1M"
      },
      {
        "@type": "Offer",
        "name": "Business",
        "price": "49",
        "priceCurrency": "EUR",
        "billingIncrement": "P1M"
      }
    ],
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "ratingCount": "48"
    }
  }
  </script>

  <!-- ── SCHEMA.ORG: FAQPage ───────────────────────────────────────────────── -->
  <!-- FAQ schema puede generar featured snippets en Google (acordeones en SERP).
       Importante que las preguntas/respuestas coincidan con las del HTML. -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "¿Necesito equipo especial para hacer el tour?",
        "acceptedAnswer": { "@type": "Answer", "text": "No. Solo necesitas un smartphone con cámara decente. Nuestro sistema procesa las fotos automáticamente y genera la profundidad con inteligencia artificial (MiDaS de Intel)." }
      },
      {
        "@type": "Question",
        "name": "¿Cuánto tiempo tarda en estar listo el tour?",
        "acceptedAnswer": { "@type": "Answer", "text": "Con el plan Free, el tour está listo en minutos. Con Pro y Business, el procesado con IA (MiDaS) tarda entre 5 y 15 minutos según el número de posiciones." }
      },
      {
        "@type": "Question",
        "name": "¿Puedo insertar el tour en mi web existente?",
        "acceptedAnswer": { "@type": "Answer", "text": "Sí. Todos los planes incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace." }
      },
      {
        "@type": "Question",
        "name": "¿Qué pasa si cancelo mi suscripción?",
        "acceptedAnswer": { "@type": "Answer", "text": "Tus tours siguen siendo accesibles en modo Free (1 tour, 5 posiciones). Los tours adicionales quedan archivados y puedes reactivarlos cuando vuelvas a suscribirte." }
      },
      {
        "@type": "Question",
        "name": "¿Funciona en móviles y tablets?",
        "acceptedAnswer": { "@type": "Answer", "text": "Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hay que instalar ninguna app." }
      },
      {
        "@type": "Question",
        "name": "¿Mis fotos y datos están seguros?",
        "acceptedAnswer": { "@type": "Answer", "text": "Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros ni las usamos para entrenar modelos. Cumplimos con el RGPD europeo." }
      }
    ]
  }
  </script>

  <!-- ── FAVICON ────────────────────────────────────────────────────────────── -->
  <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
</head>

<body>

<!-- ════════════════════════════════════════════════════════════════════════════
     NAV
     Sticky desde el principio. Glassmorphism se activa al hacer scroll (main.js).
     ════════════════════════════════════════════════════════════════════════════ -->
<header>
<nav id="nav" role="navigation" aria-label="Navegación principal">
  <div class="nav-inner">

    <a href="/" class="nav-logo" aria-label="Oxphyre — Inicio">Oxphyre</a>

    <ul class="nav-links" role="list">
      <li><a href="#caracteristicas" data-i18n="nav.features">Características</a></li>
      <li><a href="#precios"         data-i18n="nav.pricing">Precios</a></li>
      <li><a href="#demo"            data-i18n="nav.demo">Demo</a></li>
      <li><a href="#faq"             data-i18n="nav.contact">Contacto</a></li>
    </ul>

    <div class="nav-actions">
      <!-- Tema día/noche: el icono cambia en CSS según data-theme -->
      <button id="theme-toggle" class="icon-btn" aria-label="Activar modo claro" data-theme="dark">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="5"/>
          <line x1="12" y1="1"  x2="12" y2="3"/>
          <line x1="12" y1="21" x2="12" y2="23"/>
          <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
          <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
          <line x1="1" y1="12" x2="3" y2="12"/>
          <line x1="21" y1="12" x2="23" y2="12"/>
          <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
          <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
      </button>

      <!-- Selector de idioma -->
      <div class="footer-lang" aria-label="Selector de idioma">
        <button class="lang-btn active" data-lang="es" aria-label="Español">ES</button>
        <span class="lang-sep" aria-hidden="true">/</span>
        <button class="lang-btn" data-lang="en" aria-label="English">EN</button>
      </div>

      <a href="/login"    class="btn-ghost"   data-i18n="nav.login">Iniciar sesión</a>
      <a href="/registro" class="btn-primary" data-i18n="nav.cta">Empezar gratis</a>
    </div>

    <!-- Hamburguesa móvil — visible solo en breakpoint pequeño via CSS -->
    <button id="menu-toggle" class="icon-btn" aria-label="Abrir menú" aria-expanded="false" aria-controls="mobile-menu">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
        <line x1="3" y1="6"  x2="21" y2="6"/>
        <line x1="3" y1="12" x2="21" y2="12"/>
        <line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>

  </div>

  <!-- Menú móvil — gestionado por main.js -->
  <div id="mobile-menu" role="menu" aria-label="Menú móvil">
    <a href="#caracteristicas" role="menuitem" data-i18n="nav.features">Características</a>
    <a href="#precios"         role="menuitem" data-i18n="nav.pricing">Precios</a>
    <a href="#demo"            role="menuitem" data-i18n="nav.demo">Demo</a>
    <a href="#faq"             role="menuitem" data-i18n="nav.contact">Contacto</a>
    <a href="/login"           role="menuitem" data-i18n="nav.login">Iniciar sesión</a>
    <a href="/registro"        role="menuitem" class="btn-primary" style="text-align:center;margin-top:8px" data-i18n="nav.cta">Empezar gratis</a>
  </div>
</nav>
</header>

<!-- ════════════════════════════════════════════════════════════════════════════
     HERO
     H1 único en la página con la keyword principal "tours virtuales 3D".
     El canvas de Three.js ocupa la mitad derecha — fallback elegante si JS falla.
     ════════════════════════════════════════════════════════════════════════════ -->
<main>
<section id="hero" aria-label="Propuesta de valor principal">
  <div class="hero-inner">

    <div class="hero-content">
      <span class="eyebrow" data-i18n="hero.eyebrow">Tours virtuales 3D para negocios locales</span>

      <!-- H1: keyword principal, único en la página -->
      <h1 data-i18n="hero.h1">Haz que tus clientes visiten tu negocio antes de llegar</h1>

      <p data-i18n="hero.subtitle">
        Sube fotos de tu local, construye el tour en minutos y compártelo con un QR.
        Sin instalaciones ni hardware especial.
      </p>

      <div class="hero-ctas">
        <a href="/registro" class="btn-primary" data-i18n="hero.cta_primary">Empezar gratis</a>
        <a href="#demo"     class="btn-ghost"   data-i18n="hero.cta_secondary">Ver demo</a>
      </div>

      <!-- Estadísticas flotantes: transmiten actividad y confianza -->
      <div class="hero-stats" aria-label="Estadísticas en tiempo real">
        <div class="stat-card">
          <span class="stat-value mono" data-i18n="hero.stat1_value">En vivo</span>
          <span class="stat-label"     data-i18n="hero.stat1_label">Tour activo</span>
        </div>
        <div class="stat-card">
          <span class="stat-value mono" data-i18n="hero.stat2_value">4:32 min</span>
          <span class="stat-label"     data-i18n="hero.stat2_label">Tiempo medio</span>
        </div>
        <div class="stat-card">
          <span class="stat-value mono" data-i18n="hero.stat3_value">127</span>
          <span class="stat-label"     data-i18n="hero.stat3_label">Visitantes hoy</span>
        </div>
      </div>
    </div>

    <!-- Visual: esfera Three.js que evoca la estructura de un tour 360 -->
    <div class="hero-visual" aria-hidden="true">
      <div class="hero-glow"></div>
      <canvas id="hero-canvas" width="480" height="480" aria-label="Visualización 3D interactiva"></canvas>
    </div>

  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     LOGOS — Señales de confianza (social proof)
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="logos" aria-label="Negocios que usan Oxphyre">
  <div class="logos-inner">
    <p class="logos-title" data-i18n="logos.title">Confían en Oxphyre</p>
    <div class="logos-row">
      <!-- Logos de ejemplo en texto — se reemplazarán por imágenes reales de clientes -->
      <span class="logo-item">GymFit</span>
      <span class="logo-item">Glamour Hair</span>
      <span class="logo-item">Babel Bistró</span>
      <span class="logo-item">MotorSpace</span>
      <span class="logo-item">Clínica Vilar</span>
      <span class="logo-item">Casa Rural El Olivo</span>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     CÓMO FUNCIONA — 3 pasos
     H2 con keyword secundaria para SEO on-page.
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="como-funciona" aria-labelledby="steps-title">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2 id="steps-title" data-i18n="steps.title">Cómo funciona</h2>
      <p data-i18n="steps.subtitle">Tu tour virtual en tres pasos. Sin curva de aprendizaje.</p>
    </div>
  </div>

  <div class="steps-grid">
    <div class="step card-glass animate-on-scroll">
      <span class="step-number mono" aria-hidden="true">01</span>
      <h3 data-i18n="steps.s1_title">Haz fotos de tu local</h3>
      <p data-i18n="steps.s1_desc">Fotografía cada posición en 4 direcciones (N, S, E, O). Solo necesitas tu móvil.</p>
    </div>
    <div class="step card-glass animate-on-scroll">
      <span class="step-number mono" aria-hidden="true">02</span>
      <h3 data-i18n="steps.s2_title">Construye el tour</h3>
      <p data-i18n="steps.s2_desc">Sube las fotos a Oxphyre y conecta las posiciones en nuestro editor visual drag &amp; drop.</p>
    </div>
    <div class="step card-glass animate-on-scroll">
      <span class="step-number mono" aria-hidden="true">03</span>
      <h3 data-i18n="steps.s3_title">Compártelo con un QR</h3>
      <p data-i18n="steps.s3_desc">Descarga el QR y ponlo donde quieras. Tus clientes escanean y exploran tu negocio en 3D.</p>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     CARACTERÍSTICAS — Grid 3 columnas
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="caracteristicas" aria-labelledby="features-title">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2 id="features-title" data-i18n="features.title">Todo lo que necesitas</h2>
      <p data-i18n="features.subtitle">Herramientas pensadas para negocios reales, no para agencias.</p>
    </div>
  </div>

  <div class="features-grid">

    <article class="feature-card card-glass animate-on-scroll">
      <div class="feature-icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
      </div>
      <h3 data-i18n="features.f1_title">Tour navegable en 3D</h3>
      <p data-i18n="features.f1_desc">Renderizado con Three.js. Tus clientes se mueven por el local como si estuvieran allí.</p>
    </article>

    <article class="feature-card card-glass animate-on-scroll">
      <div class="feature-icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/></svg>
      </div>
      <h3 data-i18n="features.f2_title">Hotspots interactivos</h3>
      <p data-i18n="features.f2_desc">Añade puntos de información, precios, productos o links en cualquier punto del tour.</p>
    </article>

    <article class="feature-card card-glass animate-on-scroll">
      <div class="feature-icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      </div>
      <h3 data-i18n="features.f3_title">QR + embed para tu web</h3>
      <p data-i18n="features.f3_desc">Un código QR descargable y un snippet para insertar el tour en tu web con una línea.</p>
    </article>

    <article class="feature-card card-glass animate-on-scroll">
      <div class="feature-icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </div>
      <h3 data-i18n="features.f4_title">Analíticas de visitas</h3>
      <p data-i18n="features.f4_desc">Sabe cuántas personas han explorado tu negocio, desde dónde y cuánto tiempo estuvieron.</p>
    </article>

    <article class="feature-card card-glass animate-on-scroll">
      <div class="feature-icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg>
      </div>
      <h3 data-i18n="features.f5_title">Modo día/noche</h3>
      <p data-i18n="features.f5_desc">El tour se adapta automáticamente a las preferencias del dispositivo del visitante.</p>
    </article>

    <article class="feature-card card-glass animate-on-scroll">
      <div class="feature-icon" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
      </div>
      <h3 data-i18n="features.f6_title">Compatible con cualquier móvil</h3>
      <p data-i18n="features.f6_desc">Funciona en iOS y Android sin instalar nada. Solo un navegador moderno.</p>
    </article>

  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     DEMO
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="demo" aria-labelledby="demo-title">
  <div class="demo-inner">
    <div class="animate-on-scroll">
      <span class="eyebrow">Demo</span>
      <h2 id="demo-title" data-i18n="demo.title">Prueba un tour de ejemplo</h2>
      <p data-i18n="demo.subtitle">Sin registro. Explora cómo queda un tour real en un negocio de ejemplo.</p>
      <a href="#" class="btn-primary" data-i18n="demo.cta">Ver tour de demo</a>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     PRECIOS — 3 columnas con toggle mensual/anual
     Los precios están en data-monthly y data-annual para que main.js los cambie
     sin peticiones al servidor al hacer toggle.
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="precios" aria-labelledby="pricing-title">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2 id="pricing-title" data-i18n="pricing.title">Precios transparentes</h2>
      <p data-i18n="pricing.subtitle">Sin comisiones ocultas. Cancela cuando quieras.</p>
    </div>

    <!-- Toggle mensual / anual -->
    <div class="pricing-toggle-wrap">
      <span data-i18n="pricing.toggle_monthly">Mensual</span>
      <button id="billing-toggle" role="switch" aria-checked="false" aria-label="Cambiar a facturación anual"></button>
      <span data-i18n="pricing.toggle_annual">Anual</span>
      <span class="badge-save" data-i18n="pricing.badge_save">Ahorra 20%</span>
    </div>
  </div>

  <div class="pricing-grid">

    <!-- Plan Free -->
    <div class="pricing-card card-glass animate-on-scroll" aria-label="Plan Free">
      <div>
        <p class="pricing-name" data-i18n="pricing.free_name">Free</p>
        <p class="pricing-price">
          <span data-monthly="0€" data-annual="0€" data-i18n="pricing.free_price_monthly">0€</span>
          <span class="pricing-per" data-i18n="pricing.per_month">/mes</span>
        </p>
        <p class="pricing-desc" data-i18n="pricing.free_desc">Para probar Oxphyre sin compromiso.</p>
      </div>
      <ul class="pricing-features" aria-label="Características del plan Free">
        <li>1 tour activo</li>
        <li>5 posiciones por tour</li>
        <li>4 fotos por posición</li>
        <li>QR descargable</li>
        <li>Con marca de agua Oxphyre</li>
      </ul>
      <a href="/registro" class="btn-ghost" data-i18n="pricing.cta_free">Empezar gratis</a>
    </div>

    <!-- Plan Pro — destacado -->
    <div class="pricing-card card-glass featured animate-on-scroll" aria-label="Plan Pro — Más popular">
      <div>
        <span class="pricing-badge" data-i18n="pricing.popular">Más popular</span>
        <p class="pricing-name" data-i18n="pricing.pro_name">Pro</p>
        <p class="pricing-price">
          <span data-monthly="19€" data-annual="15€" data-i18n="pricing.pro_price_monthly">19€</span>
          <span class="pricing-per" data-i18n="pricing.per_month">/mes</span>
        </p>
        <p class="pricing-desc" data-i18n="pricing.pro_desc">Para negocios que quieren destacar.</p>
      </div>
      <ul class="pricing-features" aria-label="Características del plan Pro">
        <li>Tours ilimitados</li>
        <li>20 posiciones por tour</li>
        <li>Profundidad IA con MiDaS</li>
        <li>Minimapa automático</li>
        <li>Sin marca de agua</li>
        <li>Analíticas básicas</li>
      </ul>
      <a href="/registro" class="btn-primary" data-i18n="pricing.cta_pro">Empezar con Pro</a>
    </div>

    <!-- Plan Business -->
    <div class="pricing-card card-glass animate-on-scroll" aria-label="Plan Business">
      <div>
        <p class="pricing-name" data-i18n="pricing.biz_name">Business</p>
        <p class="pricing-price">
          <span data-monthly="49€" data-annual="39€" data-i18n="pricing.biz_price_monthly">49€</span>
          <span class="pricing-per" data-i18n="pricing.per_month">/mes</span>
        </p>
        <p class="pricing-desc" data-i18n="pricing.biz_desc">Para cadenas y agencias de marketing.</p>
      </div>
      <ul class="pricing-features" aria-label="Características del plan Business">
        <li>Todo del plan Pro</li>
        <li>Posiciones ilimitadas</li>
        <li>MiDaS máxima calidad</li>
        <li>Dominio personalizado</li>
        <li>Analíticas avanzadas</li>
        <li>API access</li>
      </ul>
      <a href="/registro" class="btn-ghost" data-i18n="pricing.cta_biz">Contactar ventas</a>
    </div>

  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     TESTIMONIOS
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="testimonios" aria-labelledby="testimonials-title">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2 id="testimonials-title" data-i18n="testimonials.title">Lo que dicen nuestros clientes</h2>
    </div>
  </div>

  <div class="testimonials-grid">

    <blockquote class="testimonial-card card-glass animate-on-scroll">
      <p class="testimonial-text" data-i18n="testimonials.t1_text">
        "Desde que puse el tour virtual en mi Instagram, recibo el doble de consultas. La gente ya viene sabiendo cómo es el gimnasio."
      </p>
      <footer class="testimonial-author">
        <cite class="testimonial-name" data-i18n="testimonials.t1_name">Carlos M.</cite>
        <span class="testimonial-role"  data-i18n="testimonials.t1_role">Propietario, GymFit Madrid</span>
      </footer>
    </blockquote>

    <blockquote class="testimonial-card card-glass animate-on-scroll">
      <p class="testimonial-text" data-i18n="testimonials.t2_text">
        "Lo monté en una tarde sin saber nada de tecnología. El QR en mi escaparate lo escanean constantemente."
      </p>
      <footer class="testimonial-author">
        <cite class="testimonial-name" data-i18n="testimonials.t2_name">Laura S.</cite>
        <span class="testimonial-role"  data-i18n="testimonials.t2_role">Dueña, Peluquería Glamour</span>
      </footer>
    </blockquote>

    <blockquote class="testimonial-card card-glass animate-on-scroll">
      <p class="testimonial-text" data-i18n="testimonials.t3_text">
        "Mis clientes reservan mesa después de ver el tour. El ambiente del local es lo que les convence antes de llegar."
      </p>
      <footer class="testimonial-author">
        <cite class="testimonial-name" data-i18n="testimonials.t3_name">Ahmed R.</cite>
        <span class="testimonial-role"  data-i18n="testimonials.t3_role">Gerente, Restaurante Babel</span>
      </footer>
    </blockquote>

  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     FAQ — Acordeón + Schema.org en el head
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="faq" aria-labelledby="faq-title">
  <div class="container">
    <div class="section-header animate-on-scroll">
      <h2 id="faq-title" data-i18n="faq.title">Preguntas frecuentes</h2>
    </div>
  </div>

  <div class="faq-list" role="list">

    <div class="faq-item animate-on-scroll" role="listitem">
      <button class="faq-question" aria-expanded="false">
        <span data-i18n="faq.q1">¿Necesito equipo especial para hacer el tour?</span>
        <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
      <div class="faq-answer" role="region">
        <div class="faq-answer-inner">
          <span data-i18n="faq.a1">No. Solo necesitas un smartphone con cámara decente. Nuestro sistema procesa las fotos automáticamente y genera la profundidad con inteligencia artificial (MiDaS de Intel). Nada de cámaras 360 ni software de edición.</span>
        </div>
      </div>
    </div>

    <div class="faq-item animate-on-scroll" role="listitem">
      <button class="faq-question" aria-expanded="false">
        <span data-i18n="faq.q2">¿Cuánto tiempo tarda en estar listo el tour?</span>
        <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
      <div class="faq-answer" role="region">
        <div class="faq-answer-inner">
          <span data-i18n="faq.a2">Con el plan Free, el tour está listo en minutos. Con los planes Pro y Business, el procesado con IA de profundidad (MiDaS) tarda entre 5 y 15 minutos según el número de posiciones.</span>
        </div>
      </div>
    </div>

    <div class="faq-item animate-on-scroll" role="listitem">
      <button class="faq-question" aria-expanded="false">
        <span data-i18n="faq.q3">¿Puedo insertar el tour en mi web existente?</span>
        <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
      <div class="faq-answer" role="region">
        <div class="faq-answer-inner">
          <span data-i18n="faq.a3">Sí. Todos los planes incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace. El plan Business incluye además dominio personalizado.</span>
        </div>
      </div>
    </div>

    <div class="faq-item animate-on-scroll" role="listitem">
      <button class="faq-question" aria-expanded="false">
        <span data-i18n="faq.q4">¿Qué pasa si cancelo mi suscripción?</span>
        <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
      <div class="faq-answer" role="region">
        <div class="faq-answer-inner">
          <span data-i18n="faq.a4">Tus tours siguen siendo accesibles en modo Free (1 tour, 5 posiciones). Si tenías más tours, quedan archivados y los puedes reactivar cuando vuelvas a suscribirte.</span>
        </div>
      </div>
    </div>

    <div class="faq-item animate-on-scroll" role="listitem">
      <button class="faq-question" aria-expanded="false">
        <span data-i18n="faq.q5">¿Funciona en móviles y tablets?</span>
        <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
      <div class="faq-answer" role="region">
        <div class="faq-answer-inner">
          <span data-i18n="faq.a5">Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hay que instalar ninguna app. Está optimizado especialmente para la experiencia desde móvil al escanear el QR.</span>
        </div>
      </div>
    </div>

    <div class="faq-item animate-on-scroll" role="listitem">
      <button class="faq-question" aria-expanded="false">
        <span data-i18n="faq.q6">¿Mis fotos y datos están seguros?</span>
        <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
      <div class="faq-answer" role="region">
        <div class="faq-answer-inner">
          <span data-i18n="faq.a6">Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros ni las usamos para entrenar modelos. Cumplimos con el RGPD europeo.</span>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ════════════════════════════════════════════════════════════════════════════
     CTA FINAL
     ════════════════════════════════════════════════════════════════════════════ -->
<section id="cta-final" aria-labelledby="cta-title">
  <div class="cta-final-inner animate-on-scroll">
    <h2 id="cta-title" data-i18n="cta_final.title">Tu negocio merece ser visitado antes de que lleguen</h2>
    <p data-i18n="cta_final.subtitle">Únete a los negocios que ya usan Oxphyre para atraer más clientes.</p>
    <a href="/registro" class="btn-primary" data-i18n="cta_final.cta">Crear mi tour gratis</a>
  </div>
</section>
</main>

<!-- ════════════════════════════════════════════════════════════════════════════
     FOOTER
     ════════════════════════════════════════════════════════════════════════════ -->
<footer id="footer" aria-label="Pie de página">
  <div class="footer-inner">
    <div class="footer-top">

      <div class="footer-brand">
        <a href="/" class="footer-logo">Oxphyre</a>
        <p data-i18n="footer.tagline">Tours virtuales 3D para negocios locales.</p>
      </div>

      <div class="footer-col">
        <h4 data-i18n="footer.product">Producto</h4>
        <ul>
          <li><a href="#caracteristicas" data-i18n="footer.features">Características</a></li>
          <li><a href="#precios"         data-i18n="footer.pricing">Precios</a></li>
          <li><a href="#demo"            data-i18n="footer.demo">Demo</a></li>
          <li><a href="#"               data-i18n="footer.changelog">Novedades</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 data-i18n="footer.legal">Legal</h4>
        <ul>
          <li><a href="/privacidad" data-i18n="footer.privacy">Privacidad</a></li>
          <li><a href="/terminos"   data-i18n="footer.terms">Términos</a></li>
          <li><a href="/cookies"    data-i18n="footer.cookies">Cookies</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4 data-i18n="footer.contact">Contacto</h4>
        <ul>
          <li><a href="#"           data-i18n="footer.about">Sobre nosotros</a></li>
          <li><a href="#"           data-i18n="footer.blog">Blog</a></li>
          <li><a href="#"           data-i18n="footer.support">Soporte</a></li>
        </ul>
      </div>

    </div>

    <div class="footer-bottom">

      <p class="footer-copy" data-i18n="footer.copyright">© 2026 Oxphyre. Todos los derechos reservados.</p>

      <!-- Selector de idioma en el footer -->
      <div class="footer-lang" aria-label="Selector de idioma">
        <button class="lang-btn active" data-lang="es" aria-label="Español">ES</button>
        <span class="lang-sep" aria-hidden="true">/</span>
        <button class="lang-btn" data-lang="en" aria-label="English">EN</button>
      </div>

      <!-- Redes sociales -->
      <div class="footer-socials" aria-label="Redes sociales">
        <a href="#" aria-label="Twitter / X de Oxphyre" rel="noopener noreferrer">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.745l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        <a href="#" aria-label="LinkedIn de Oxphyre" rel="noopener noreferrer">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
        </a>
        <a href="#" aria-label="Instagram de Oxphyre" rel="noopener noreferrer">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </a>
      </div>

    </div>
  </div>
</footer>

<!-- ── SCRIPTS (al final del body para no bloquear el render) ────────────────── -->
<!-- Three.js desde CDN unpkg con defer: se descarga en paralelo, se ejecuta tras parsear el DOM -->
<script defer src="https://unpkg.com/three@0.161.0/build/three.min.js"></script>
<!-- i18n primero: main.js lo llama en DOMContentLoaded -->
<script defer src="/js/i18n.js"></script>
<script defer src="/js/main.js"></script>

</body>
</html>
