<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- SEO primario -->
  <title>Oxphyre | Tours Virtuales 3D para Negocios Locales</title>
  <meta name="description" content="Crea tours virtuales 3D de tu negocio en minutos. Sube fotos, construye el tour con nuestro editor y compártelo con un QR. Sin hardware especial. Gratis para empezar.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="https://oxphyre.com/">
  <meta property="og:title"       content="Oxphyre | Tours Virtuales 3D para Negocios Locales">
  <meta property="og:description" content="Convierte tu local en una experiencia 360° que tus clientes pueden visitar desde cualquier lugar. Sin cámaras especiales, sin técnicos.">
  <meta property="og:image"       content="https://oxphyre.com/assets/og-image.jpg">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="Oxphyre | Tours Virtuales 3D">
  <meta name="twitter:description" content="Tours virtuales 3D para negocios locales. Sin hardware especial. Gratis para empezar.">
  <meta name="twitter:image"       content="https://oxphyre.com/assets/og-image.jpg">

  <!-- Preconnect para fuentes -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <!-- Google Fonts: Wix Madefor Display + Inter + JetBrains Mono -->
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>

  <!-- CSS -->
  <link rel="stylesheet" href="/css/main.css">

  <!-- Schema.org: SoftwareApplication -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Oxphyre",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "description": "Plataforma SaaS para crear tours virtuales 3D de negocios locales. Sin hardware especial.",
    "offers": [
      { "@type": "Offer", "name": "Free",     "price": "0",  "priceCurrency": "EUR" },
      { "@type": "Offer", "name": "Pro",      "price": "19", "priceCurrency": "EUR", "billingIncrement": "P1M" },
      { "@type": "Offer", "name": "Business", "price": "49", "priceCurrency": "EUR", "billingIncrement": "P1M" }
    ],
    "url": "https://oxphyre.com"
  }
  </script>

  <!-- Schema.org: FAQPage -->
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
        "acceptedAnswer": { "@type": "Answer", "text": "Con el plan Free, el tour está listo en minutos. Con los planes Pro y Business, el procesado con IA de profundidad tarda entre 5 y 15 minutos." }
      },
      {
        "@type": "Question",
        "name": "¿Puedo insertar el tour en mi web existente?",
        "acceptedAnswer": { "@type": "Answer", "text": "Sí. Todos los planes incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace." }
      },
      {
        "@type": "Question",
        "name": "¿Qué pasa si cancelo mi suscripción?",
        "acceptedAnswer": { "@type": "Answer", "text": "Tus tours siguen siendo accesibles en modo Free (1 tour, 5 posiciones). Si tenías más tours, quedan archivados y los puedes reactivar cuando vuelvas a suscribirte." }
      },
      {
        "@type": "Question",
        "name": "¿Funciona en móviles y tablets?",
        "acceptedAnswer": { "@type": "Answer", "text": "Sí. El tour funciona en cualquier dispositivo con un navegador moderno. Está optimizado especialmente para la experiencia desde móvil al escanear el QR." }
      },
      {
        "@type": "Question",
        "name": "¿Mis fotos y datos están seguros?",
        "acceptedAnswer": { "@type": "Answer", "text": "Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros. Cumplimos con el RGPD europeo." }
      }
    ]
  }
  </script>
</head>

<body>

  <!-- Cursor personalizado — solo visible en desktop (CSS oculta en touch) -->
  <div id="cursor-ring" aria-hidden="true"></div>

  <!-- Loader: foco de luz revela OXPHYRE letra a letra -->
  <div id="loader" role="status" aria-label="Cargando Oxphyre">
    <div id="loader-beam"></div>
    <div id="loader-text" aria-hidden="true">
      <span class="loader-letter">O</span>
      <span class="loader-letter">X</span>
      <span class="loader-letter">P</span>
      <span class="loader-letter">H</span>
      <span class="loader-letter">Y</span>
      <span class="loader-letter">R</span>
      <span class="loader-letter">E</span>
    </div>
  </div>

  <!-- Logo solo: visible durante Phase 1 del hero -->
  <div id="nav-logo-solo" aria-hidden="true">Oxphyre</div>

  <!-- ── NAV ───────────────────────────────────────────────────────────── -->
  <nav id="nav" role="navigation" aria-label="Navegación principal">
    <a href="/" class="nav-logo" aria-label="Oxphyre inicio">Oxphyre</a>

    <div class="nav-links">
      <a href="#como-funciona"   data-i18n="nav.how">Cómo funciona</a>
      <a href="#demo"            data-i18n="nav.demo">Demo</a>
      <a href="#caracteristicas" data-i18n="nav.features">Características</a>
      <a href="#precios"         data-i18n="nav.pricing">Precios</a>
      <a href="#faq"             data-i18n="nav.faq">FAQ</a>
    </div>

    <div class="nav-actions">
      <!-- Toggle tema -->
      <button id="theme-toggle" aria-label="Activar modo claro" data-theme="dark">
        <i data-lucide="sun" width="18" height="18"></i>
      </button>

      <!-- Selector de idioma -->
      <button class="lang-btn active" data-lang="es" aria-label="Español">ES</button>
      <span class="lang-divider">/</span>
      <button class="lang-btn" data-lang="en" aria-label="English">EN</button>

      <a href="/login"    class="btn-ghost"   data-i18n="nav.login">Iniciar sesión</a>
      <a href="/registro" class="btn-primary" data-i18n="nav.cta">Empezar gratis</a>
    </div>

    <!-- Hamburguesa (solo móvil) -->
    <button id="menu-toggle" aria-label="Abrir menú" aria-expanded="false" style="margin-left:auto;">
      <i data-lucide="menu" width="24" height="24"></i>
    </button>
  </nav>

  <!-- Menú móvil overlay -->
  <div id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menú de navegación">
    <button id="mobile-menu-close" aria-label="Cerrar menú" style="position:absolute;top:24px;right:24px;color:var(--text-2);">
      <i data-lucide="x" width="28" height="28"></i>
    </button>
    <a href="#como-funciona"   data-i18n="nav.how">Cómo funciona</a>
    <a href="#demo"            data-i18n="nav.demo">Demo</a>
    <a href="#caracteristicas" data-i18n="nav.features">Características</a>
    <a href="#precios"         data-i18n="nav.pricing">Precios</a>
    <a href="#faq"             data-i18n="nav.faq">FAQ</a>
    <a href="/login"           data-i18n="nav.login">Iniciar sesión</a>
    <a href="/registro" class="btn-primary" data-i18n="nav.cta">Empezar gratis</a>
    <div class="mobile-menu-footer">
      <button class="lang-btn active" data-lang="es">ES</button>
      <span class="lang-divider">/</span>
      <button class="lang-btn" data-lang="en">EN</button>
    </div>
  </div>


  <!-- ═══════════════════════════════════════════════════════════════════
       S1 — HERO (Two-Phase Three.js)
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="hero" aria-label="Hero">

    <canvas id="hero-canvas" aria-hidden="true"></canvas>

    <!-- Phase 1: frases según ángulo de rotación -->
    <div id="hero-phrases" aria-hidden="true">
      <p class="phrase active" data-angle="0"   data-i18n="hero.phase1_0">Bienvenido a la profundidad.</p>
      <p class="phrase"        data-angle="90"  data-i18n="hero.phase1_90">Aquí, tu espacio cobra vida.</p>
      <p class="phrase"        data-angle="180" data-i18n="hero.phase1_180">Cada rincón, capturado en su mejor momento.</p>
      <p class="phrase"        data-angle="270" data-i18n="hero.phase1_270">No es una foto. Es tu negocio vivo.</p>
      <p class="phrase phrase-cta" data-angle="350" data-i18n="hero.phase1_360">↓ Explora la dimensión Oxphyre</p>
    </div>

    <!-- Phase 2: contenido del hero -->
    <div id="hero-content">
      <h1 class="hero-h1" data-i18n="hero.h1">
        Tours virtuales 3D para negocios que quieren brillar.
      </h1>
      <p class="hero-subtitle" data-i18n="hero.subtitle">
        Convierte tu local en una experiencia 360° que tus clientes pueden visitar desde cualquier lugar. Sin cámaras especiales, sin técnicos, sin complicaciones.
      </p>
      <div class="hero-ctas">
        <a href="/registro" class="btn-primary"   data-i18n="hero.cta_primary">Crear mi tour gratis →</a>
        <a href="#demo"     class="btn-secondary" data-i18n="hero.cta_secondary">Ver un tour en vivo</a>
      </div>
      <div class="hero-pills">
        <span class="hero-pill" data-i18n="hero.pill1">✓ Sin hardware especial</span>
        <span class="hero-pill" data-i18n="hero.pill2">✓ Listo en menos de 1 hora</span>
        <span class="hero-pill" data-i18n="hero.pill3">✓ Funciona en cualquier móvil</span>
      </div>
    </div>

    <!-- Scroll hint -->
    <div id="scroll-hint" aria-hidden="true">
      <div class="scroll-line"></div>
    </div>

  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S2 — CARRUSEL NEGOCIOS
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="carousel-section" aria-label="Sectores">
    <h2 class="carousel-title animate-on-scroll" data-i18n="carousel.title">Tu negocio, en primera persona</h2>

    <div id="carousel" role="region" aria-label="Carrusel de negocios" aria-live="polite">

      <article class="carousel-card active">
        <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=680&q=80&auto=format" alt="Interior de restaurante atmosférico" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c1_title">Restaurante</p>
          <p class="carousel-card-text"  data-i18n="carousel.c1_text">Que reserven antes de probar tu cocina.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=680&q=80&auto=format" alt="Gimnasio moderno con equipamiento" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c2_title">Gimnasio</p>
          <p class="carousel-card-text"  data-i18n="carousel.c2_text">Que vean las instalaciones antes de apuntarse.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=680&q=80&auto=format" alt="Peluquería estilosa y moderna" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c3_title">Peluquería</p>
          <p class="carousel-card-text"  data-i18n="carousel.c3_text">Que conozcan tu espacio antes de su cita.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=680&q=80&auto=format" alt="Habitación de hotel de lujo" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c4_title">Hotel</p>
          <p class="carousel-card-text"  data-i18n="carousel.c4_text">Que elijan su habitación antes de reservar.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?w=680&q=80&auto=format" alt="Tienda boutique con iluminación cálida" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c5_title">Tienda</p>
          <p class="carousel-card-text"  data-i18n="carousel.c5_text">Que exploren tu tienda desde el sofá.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1560185007-cde436f6a4d0?w=680&q=80&auto=format" alt="Salón de piso luminoso" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c6_title">Inmobiliaria</p>
          <p class="carousel-card-text"  data-i18n="carousel.c6_text">Que visiten la propiedad sin salir de casa.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?w=680&q=80&auto=format" alt="Consulta médica limpia y moderna" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c7_title">Clínica</p>
          <p class="carousel-card-text"  data-i18n="carousel.c7_text">Que conozcan tu consulta antes de su primera cita.</p>
        </div>
      </article>

      <article class="carousel-card">
        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=680&q=80&auto=format" alt="Espacio de coworking luminoso" loading="lazy">
        <div class="carousel-card-overlay">
          <p class="carousel-card-title" data-i18n="carousel.c8_title">Coworking</p>
          <p class="carousel-card-text"  data-i18n="carousel.c8_text">Que sientan el espacio antes de reservar su mesa.</p>
        </div>
      </article>

    </div><!-- #carousel -->

    <div class="carousel-controls">
      <button id="carousel-prev" class="carousel-btn" aria-label="Anterior">
        <i data-lucide="chevron-left" width="20" height="20"></i>
      </button>
      <div class="carousel-dots" aria-hidden="true">
        <span class="carousel-dot active"></span>
        <span class="carousel-dot"></span>
        <span class="carousel-dot"></span>
        <span class="carousel-dot"></span>
        <span class="carousel-dot"></span>
        <span class="carousel-dot"></span>
        <span class="carousel-dot"></span>
        <span class="carousel-dot"></span>
      </div>
      <button id="carousel-next" class="carousel-btn" aria-label="Siguiente">
        <i data-lucide="chevron-right" width="20" height="20"></i>
      </button>
    </div>

  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S3 — CÓMO FUNCIONA
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="como-funciona" aria-labelledby="steps-h2">
    <div class="section-header">
      <h2 id="steps-h2" class="section-h2 animate-on-scroll" data-i18n="steps.title">Cómo funciona</h2>
      <p class="section-subtitle animate-on-scroll" data-i18n="steps.subtitle">
        Tu tour virtual en tres pasos. Sin curva de aprendizaje.
      </p>
    </div>

    <div class="steps-grid">

      <article class="step-card animate-on-scroll">
        <p class="step-num" data-i18n="steps.s1_num">01</p>
        <h3 class="step-title" data-i18n="steps.s1_title">Fotografías tu local</h3>
        <p class="step-desc" data-i18n="steps.s1_desc">
          Fotografía cada posición en 4 direcciones (N, S, E, O). Solo necesitas tu móvil.
        </p>
      </article>

      <article class="step-card animate-on-scroll">
        <p class="step-num" data-i18n="steps.s2_num">02</p>
        <h3 class="step-title" data-i18n="steps.s2_title">Construyes el tour</h3>
        <p class="step-desc" data-i18n="steps.s2_desc">
          Sube las fotos a Oxphyre y conecta las posiciones en nuestro editor visual drag &amp; drop.
        </p>
      </article>

      <article class="step-card animate-on-scroll">
        <p class="step-num" data-i18n="steps.s3_num">03</p>
        <h3 class="step-title" data-i18n="steps.s3_title">Lo compartes</h3>
        <p class="step-desc" data-i18n="steps.s3_desc">
          Descarga el QR y ponlo donde quieras. Tus clientes escanean y exploran tu negocio en 3D.
        </p>
      </article>

    </div>
  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S4 — DEMO VIDEO
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="demo" aria-labelledby="demo-h2">
    <div class="section-header animate-on-scroll">
      <h2 id="demo-h2" class="section-h2" data-i18n="demo.title">Mira cómo funciona</h2>
      <p class="section-subtitle" data-i18n="demo.subtitle">
        Descubre cómo un negocio real se convierte en un tour virtual 3D navegable. Sin registro.
      </p>
    </div>

    <div class="video-wrapper animate-on-scroll">
      <div class="video-placeholder">
        <button class="video-play-btn" aria-label="Reproducir vídeo demo">
          <i data-lucide="play" width="32" height="32"></i>
        </button>
        <p>Demo disponible pronto</p>
      </div>
    </div>

    <div class="demo-cta animate-on-scroll">
      <a href="/registro" class="btn-primary" data-i18n="demo.cta">Ver tour en vivo</a>
    </div>
  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S5 — CARACTERÍSTICAS (bento grid + cursor spotlight)
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="caracteristicas" aria-labelledby="features-h2">
    <div class="section-header animate-on-scroll">
      <h2 id="features-h2" class="section-h2" data-i18n="features.title">Todo lo que necesitas</h2>
      <p class="section-subtitle" data-i18n="features.subtitle">
        Herramientas pensadas para negocios reales.
      </p>
    </div>

    <div class="features-grid">

      <article class="feature-card animate-on-scroll">
        <i data-lucide="box" class="feature-icon" aria-hidden="true"></i>
        <h3 class="feature-title" data-i18n="features.f1_title">Tour 3D navegable</h3>
        <p class="feature-desc" data-i18n="features.f1_desc">
          Renderizado con Three.js. Tus clientes se mueven por el local como si estuvieran allí.
        </p>
      </article>

      <article class="feature-card animate-on-scroll">
        <i data-lucide="map-pin" class="feature-icon" aria-hidden="true"></i>
        <h3 class="feature-title" data-i18n="features.f2_title">Hotspots interactivos</h3>
        <p class="feature-desc" data-i18n="features.f2_desc">
          Añade puntos de información, precios, productos o links en cualquier punto del tour.
        </p>
      </article>

      <article class="feature-card animate-on-scroll">
        <i data-lucide="qr-code" class="feature-icon" aria-hidden="true"></i>
        <h3 class="feature-title" data-i18n="features.f3_title">QR + embed</h3>
        <p class="feature-desc" data-i18n="features.f3_desc">
          Un código QR descargable y un snippet para insertar el tour en tu web con una línea.
        </p>
      </article>

      <article class="feature-card animate-on-scroll">
        <i data-lucide="bar-chart-2" class="feature-icon" aria-hidden="true"></i>
        <h3 class="feature-title" data-i18n="features.f4_title">Analíticas de visitas</h3>
        <p class="feature-desc" data-i18n="features.f4_desc">
          Sabe cuántas personas han explorado tu negocio, desde dónde y cuánto tiempo estuvieron.
        </p>
      </article>

      <article class="feature-card animate-on-scroll">
        <i data-lucide="sun-moon" class="feature-icon" aria-hidden="true"></i>
        <h3 class="feature-title" data-i18n="features.f5_title">Modo día/noche</h3>
        <p class="feature-desc" data-i18n="features.f5_desc">
          El tour se adapta automáticamente a las preferencias del dispositivo del visitante.
        </p>
      </article>

      <article class="feature-card animate-on-scroll">
        <i data-lucide="smartphone" class="feature-icon" aria-hidden="true"></i>
        <h3 class="feature-title" data-i18n="features.f6_title">Compatible con cualquier móvil</h3>
        <p class="feature-desc" data-i18n="features.f6_desc">
          Funciona en iOS y Android sin instalar nada. Solo un navegador moderno.
        </p>
      </article>

    </div>
  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S6 — PRECIOS
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="precios" aria-labelledby="pricing-h2">
    <div class="section-header animate-on-scroll">
      <h2 id="pricing-h2" class="section-h2" data-i18n="pricing.title">Precios transparentes</h2>
      <p class="section-subtitle" data-i18n="pricing.subtitle">
        Sin comisiones ocultas. Cancela cuando quieras.
      </p>
    </div>

    <div class="pricing-toggle animate-on-scroll">
      <span class="toggle-label monthly" data-i18n="pricing.toggle_monthly">Mensual</span>
      <button id="billing-toggle" role="switch" aria-checked="false" aria-label="Cambiar entre facturación mensual y anual"></button>
      <span class="toggle-label annual" data-i18n="pricing.toggle_annual">Anual</span>
      <span class="badge-save" data-i18n="pricing.badge_save">Ahorra 20%</span>
    </div>

    <div class="pricing-grid">

      <article class="pricing-card animate-on-scroll">
        <p class="plan-name" data-i18n="pricing.free_name">Free</p>
        <p class="plan-desc" data-i18n="pricing.free_desc">Para probar Oxphyre sin compromiso.</p>
        <div class="plan-price">
          <span class="price-amount" data-monthly="0€" data-annual="0€">0€</span>
          <span class="price-period" data-i18n="pricing.per_month">/mes</span>
        </div>
        <ul class="plan-features" aria-label="Características del plan Free">
          <li data-i18n="pricing.free_f1">1 tour activo</li>
          <li data-i18n="pricing.free_f2">Hasta 5 posiciones</li>
          <li data-i18n="pricing.free_f3">QR descargable</li>
          <li data-i18n="pricing.free_f4">Marca de agua Oxphyre</li>
        </ul>
        <a href="/registro" class="plan-cta" data-i18n="pricing.cta_free">Empezar gratis</a>
      </article>

      <article class="pricing-card featured animate-on-scroll">
        <span class="popular-badge" data-i18n="pricing.popular">Más popular</span>
        <p class="plan-name" data-i18n="pricing.pro_name">Pro</p>
        <p class="plan-desc" data-i18n="pricing.pro_desc">Para negocios que quieren destacar.</p>
        <div class="plan-price">
          <span class="price-amount" data-monthly="19€" data-annual="15€">19€</span>
          <span class="price-period" data-i18n="pricing.per_month">/mes</span>
        </div>
        <ul class="plan-features" aria-label="Características del plan Pro">
          <li data-i18n="pricing.pro_f1">Tours ilimitados</li>
          <li data-i18n="pricing.pro_f2">Hasta 20 posiciones</li>
          <li data-i18n="pricing.pro_f3">MiDaS IA profundidad</li>
          <li data-i18n="pricing.pro_f4">Analíticas básicas</li>
          <li data-i18n="pricing.pro_f5">Sin marca de agua</li>
        </ul>
        <a href="/registro" class="plan-cta featured-cta" data-i18n="pricing.cta_pro">Empezar con Pro</a>
      </article>

      <article class="pricing-card animate-on-scroll">
        <p class="plan-name" data-i18n="pricing.biz_name">Business</p>
        <p class="plan-desc" data-i18n="pricing.biz_desc">Para cadenas y agencias de marketing.</p>
        <div class="plan-price">
          <span class="price-amount" data-monthly="49€" data-annual="39€">49€</span>
          <span class="price-period" data-i18n="pricing.per_month">/mes</span>
        </div>
        <ul class="plan-features" aria-label="Características del plan Business">
          <li data-i18n="pricing.biz_f1">Todo ilimitado</li>
          <li data-i18n="pricing.biz_f2">MiDaS máxima calidad</li>
          <li data-i18n="pricing.biz_f3">Analíticas avanzadas</li>
          <li data-i18n="pricing.biz_f4">Dominio personalizado</li>
          <li data-i18n="pricing.biz_f5">API access</li>
        </ul>
        <a href="/contacto" class="plan-cta" data-i18n="pricing.cta_biz">Contactar ventas</a>
      </article>

    </div>
  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S7 — FAQ
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="faq" aria-labelledby="faq-h2">
    <div class="section-header animate-on-scroll">
      <h2 id="faq-h2" class="section-h2" data-i18n="faq.title">Preguntas frecuentes</h2>
    </div>

    <div class="faq-list" role="list">

      <div class="faq-item" role="listitem">
        <button class="faq-question" aria-expanded="false">
          <span data-i18n="faq.q1">¿Necesito equipo especial para hacer el tour?</span>
          <i data-lucide="plus" class="faq-question-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p data-i18n="faq.a1">No. Solo necesitas un smartphone con cámara decente. Nuestro sistema procesa las fotos automáticamente y genera la profundidad con inteligencia artificial (MiDaS de Intel). Nada de cámaras 360 ni software de edición.</p>
        </div>
      </div>

      <div class="faq-item" role="listitem">
        <button class="faq-question" aria-expanded="false">
          <span data-i18n="faq.q2">¿Cuánto tiempo tarda en estar listo el tour?</span>
          <i data-lucide="plus" class="faq-question-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p data-i18n="faq.a2">Con el plan Free, el tour está listo en minutos. Con los planes Pro y Business, el procesado con IA de profundidad (MiDaS) tarda entre 5 y 15 minutos según el número de posiciones.</p>
        </div>
      </div>

      <div class="faq-item" role="listitem">
        <button class="faq-question" aria-expanded="false">
          <span data-i18n="faq.q3">¿Puedo insertar el tour en mi web existente?</span>
          <i data-lucide="plus" class="faq-question-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p data-i18n="faq.a3">Sí. Todos los planes incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace. El plan Business incluye además dominio personalizado.</p>
        </div>
      </div>

      <div class="faq-item" role="listitem">
        <button class="faq-question" aria-expanded="false">
          <span data-i18n="faq.q4">¿Qué pasa si cancelo mi suscripción?</span>
          <i data-lucide="plus" class="faq-question-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p data-i18n="faq.a4">Tus tours siguen siendo accesibles en modo Free (1 tour, 5 posiciones). Si tenías más tours, quedan archivados y los puedes reactivar cuando vuelvas a suscribirte.</p>
        </div>
      </div>

      <div class="faq-item" role="listitem">
        <button class="faq-question" aria-expanded="false">
          <span data-i18n="faq.q5">¿Funciona en móviles y tablets?</span>
          <i data-lucide="plus" class="faq-question-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p data-i18n="faq.a5">Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hay que instalar ninguna app. Está optimizado especialmente para la experiencia desde móvil al escanear el QR.</p>
        </div>
      </div>

      <div class="faq-item" role="listitem">
        <button class="faq-question" aria-expanded="false">
          <span data-i18n="faq.q6">¿Mis fotos y datos están seguros?</span>
          <i data-lucide="plus" class="faq-question-icon" aria-hidden="true"></i>
        </button>
        <div class="faq-answer" role="region">
          <p data-i18n="faq.a6">Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros ni las usamos para entrenar modelos. Cumplimos con el RGPD europeo.</p>
        </div>
      </div>

    </div>
  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S8 — CTA FINAL
       ═══════════════════════════════════════════════════════════════════ -->
  <section id="cta-final" aria-labelledby="cta-h2">

    <canvas id="cta-canvas" aria-hidden="true"></canvas>

    <h2 id="cta-h2" class="cta-final-h2 animate-on-scroll" data-i18n="cta_final.title">
      Tu negocio merece ser descubierto.
    </h2>
    <p class="cta-final-sub animate-on-scroll" data-i18n="cta_final.subtitle">
      Empieza gratis hoy. Sin tarjeta de crédito.
    </p>
    <a href="/registro" class="cta-final-btn animate-on-scroll" data-i18n="cta_final.cta">
      Crear mi tour gratis →
    </a>

  </section>


  <!-- ═══════════════════════════════════════════════════════════════════
       S9 — FOOTER
       ═══════════════════════════════════════════════════════════════════ -->
  <footer id="footer" role="contentinfo">
    <div class="footer-inner">
      <div class="footer-top">

        <div class="footer-brand footer-col">
          <a href="/" class="footer-logo">Oxphyre</a>
          <p class="footer-tagline" data-i18n="footer.tagline">
            Tours virtuales 3D para negocios locales.
          </p>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.product">Producto</p>
          <ul>
            <li><a href="#caracteristicas" data-i18n="footer.features">Características</a></li>
            <li><a href="#precios"         data-i18n="footer.pricing">Precios</a></li>
            <li><a href="#demo"            data-i18n="footer.demo">Demo</a></li>
            <li><a href="/novedades"       data-i18n="footer.changelog">Novedades</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.legal">Legal</p>
          <ul>
            <li><a href="/privacidad" data-i18n="footer.privacy">Privacidad</a></li>
            <li><a href="/terminos"   data-i18n="footer.terms">Términos</a></li>
            <li><a href="/cookies"    data-i18n="footer.cookies">Cookies</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.contact">Contacto</p>
          <ul>
            <li><a href="/sobre-nosotros" data-i18n="footer.about">Sobre nosotros</a></li>
            <li><a href="/blog"           data-i18n="footer.blog">Blog</a></li>
            <li><a href="/soporte"        data-i18n="footer.support">Soporte</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <p class="footer-col-title" data-i18n="footer.social">Redes</p>
          <ul>
            <li><a href="https://instagram.com/oxphyre" rel="noopener noreferrer" target="_blank">Instagram</a></li>
            <li><a href="https://twitter.com/oxphyre"   rel="noopener noreferrer" target="_blank">Twitter / X</a></li>
            <li><a href="https://linkedin.com/company/oxphyre" rel="noopener noreferrer" target="_blank">LinkedIn</a></li>
          </ul>
        </div>

      </div><!-- .footer-top -->

      <div class="footer-bottom">
        <p class="footer-copyright" data-i18n="footer.copyright">
          © <?= date('Y') ?> Oxphyre. Todos los derechos reservados.
        </p>
        <div class="footer-lang">
          <button class="lang-btn active" data-lang="es" aria-label="Español">ES</button>
          <span class="lang-divider">/</span>
          <button class="lang-btn" data-lang="en" aria-label="English">EN</button>
        </div>
      </div>

    </div><!-- .footer-inner -->
  </footer>


  <!-- Scripts con defer — Three.js primero para que esté disponible cuando main.js lo necesita -->
  <script src="https://unpkg.com/three@0.160.0/build/three.min.js" defer></script>
  <script src="/js/i18n.js" defer></script>
  <script src="/js/main.js" defer></script>

  <!-- Inicializar iconos Lucide tras cargar el DOM -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (typeof lucide !== 'undefined') lucide.createIcons();
    });
  </script>

</body>
</html>
