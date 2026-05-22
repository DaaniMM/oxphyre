<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Precios — Oxphyre | Tours Virtuales 3D para Negocios</title>
  <meta name="description" content="Empieza gratis con Oxphyre. Planes Free, Pro y Business para crear y compartir tours virtuales 3D de tu negocio. Sin tarjeta de crédito. Sin comisiones ocultas.">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="https://oxphyre.com/precios">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#FEB354">

  <meta property="og:type"        content="website">
  <meta property="og:url"         content="https://oxphyre.com/precios">
  <meta property="og:title"       content="Precios — Oxphyre | Tours Virtuales 3D">
  <meta property="og:description" content="Elige el plan que mejor se adapta a tu negocio. Free para empezar, Pro para crecer, Business para escalar.">
  <meta property="og:image"       content="https://oxphyre.com/assets/og-image.png">

  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="Precios — Oxphyre">
  <meta name="twitter:description" content="Planes Free, Pro y Business para tours virtuales 3D. Sin tarjeta de crédito.">
  <meta name="twitter:image"       content="https://oxphyre.com/assets/og-image.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Wix+Madefor+Display:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>

  <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">

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
    "url": "https://oxphyre.com/precios"
  }
  </script>

  <style>
    /* ── Overrides específicos para /precios ── */
    * { cursor: auto !important; }
    #cursor-ring { display: none !important; }

    /* Nav siempre visible (sin Three.js / phase-2) */
    #nav { opacity: 1 !important; pointer-events: auto !important; }

    /* Contenedor principal */
    .precios-page {
      padding-top: var(--nav-h);
      position: relative;
      overflow: hidden;
    }
    .precios-page::before {
      content: '';
      position: absolute;
      top: 120px;
      left: 50%;
      width: min(780px, 86vw);
      aspect-ratio: 1;
      transform: translateX(-50%);
      border-radius: 50%;
      background:
        radial-gradient(circle at 50% 48%, rgba(254, 179, 84, 0.16), rgba(254, 179, 84, 0.045) 34%, transparent 66%),
        radial-gradient(circle at 50% 50%, rgba(255,255,255,0.06), transparent 58%);
      filter: blur(2px);
      opacity: 0.9;
      pointer-events: none;
      z-index: 0;
    }
    .precios-page::after {
      content: '';
      position: absolute;
      top: 172px;
      left: 50%;
      width: min(560px, 72vw);
      aspect-ratio: 1;
      transform: translateX(-50%);
      border-radius: 50%;
      border: 1px solid rgba(254, 179, 84, 0.14);
      box-shadow: inset 0 0 80px rgba(254, 179, 84, 0.055), 0 0 120px rgba(254, 179, 84, 0.08);
      pointer-events: none;
      z-index: 0;
    }

    /* Hero de la página */
    .pricing-page-hero {
      text-align: center;
      padding: 96px 40px 20px;
      max-width: 680px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }
    .pricing-page-h1 {
      font-family: var(--font-display);
      font-size: clamp(30px, 4vw, 48px);
      font-weight: 800;
      color: var(--text-1);
      line-height: 1.15;
      margin-bottom: 16px;
    }
    .pricing-page-h1 em {
      font-style: normal;
      color: var(--accent);
    }
    .pricing-page-sub {
      font-size: 16px;
      color: var(--text-2);
      line-height: 1.65;
    }

    /* Sección de cards: anular min-height 100vh del CSS de landing */
    #precios {
      min-height: auto !important;
      padding: 16px 40px 88px !important;
      position: relative;
      z-index: 1;
    }
    #precios .pricing-toggle {
      margin-top: 40px;
    }

    /* Tabla comparativa */
    .pricing-compare {
      max-width: 920px;
      margin: 0 auto;
      padding: 0 40px 80px;
      position: relative;
      z-index: 1;
    }
    .pricing-compare h2 {
      font-family: var(--font-display);
      font-size: 20px;
      font-weight: 700;
      color: var(--text-1);
      margin-bottom: 28px;
      text-align: center;
    }
    .pricing-seo-note {
      max-width: 640px;
      margin: 0 auto 28px;
      color: var(--text-3);
      font-size: 14px;
      line-height: 1.7;
      text-align: center;
    }
    .pricing-seo-note a {
      color: var(--accent);
      font-weight: 600;
    }
    .pricing-seo-note a:hover {
      color: var(--text-1);
    }
    .compare-wrap {
      overflow-x: auto;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,0.06);
    }
    .compare-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    .compare-table thead tr {
      background: #0A0A0A;
    }
    .compare-table th {
      padding: 14px 18px;
      text-align: center;
      font-family: var(--font-display);
      font-size: 13px;
      font-weight: 700;
      color: var(--text-2);
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .compare-table th:first-child { text-align: left; width: 40%; }
    .compare-table th.th-pro { color: var(--accent); }
    .compare-table td {
      padding: 11px 18px;
      color: var(--text-3);
      border-bottom: 1px solid rgba(255,255,255,0.04);
      text-align: center;
      vertical-align: middle;
    }
    .compare-table td:first-child { text-align: left; color: var(--text-2); }
    .compare-table tbody tr:last-child td { border-bottom: none; }
    .compare-table tbody tr:hover td { background: rgba(255,255,255,0.02); }
    .compare-table .check { color: var(--accent); font-weight: 600; }
    .compare-table .cross  { color: rgba(255,255,255,0.18); }
    .compare-table .val    { color: var(--text-2); }
    .compare-table .soon   {
      display: inline-block;
      font-size: 10px;
      font-weight: 600;
      color: var(--text-3);
      background: rgba(254,179,84,0.07);
      border: 1px solid rgba(254,179,84,0.14);
      border-radius: 4px;
      padding: 2px 7px;
      letter-spacing: 0.04em;
      white-space: nowrap;
    }
    .compare-table .row-price td {
      padding-top: 16px;
      padding-bottom: 16px;
      font-family: var(--font-mono);
      font-weight: 700;
      color: var(--text-1);
    }
    .compare-table .row-price td.th-pro { color: var(--accent); }

    /* FAQ sección de planes */
    .pricing-faq-section {
      max-width: 680px;
      margin: 0 auto;
      padding: 0 40px 80px;
      position: relative;
      z-index: 1;
    }
    .pricing-faq-section h2 {
      font-family: var(--font-display);
      font-size: 20px;
      font-weight: 700;
      color: var(--text-1);
      margin-bottom: 28px;
      text-align: center;
    }

    /* CTA bottom */
    .pricing-cta-section {
      text-align: center;
      padding: 0 40px 80px;
      border-top: 1px solid rgba(255,255,255,0.05);
      padding-top: 64px;
      position: relative;
      z-index: 1;
    }
    .pricing-cta-section p {
      font-size: 22px;
      font-family: var(--font-display);
      font-weight: 700;
      color: var(--text-1);
      margin-bottom: 8px;
    }
    .pricing-cta-section .cta-sub {
      font-size: 15px;
      color: var(--text-3);
      margin-bottom: 28px;
    }
    .pricing-cta-section .cta-btn {
      display: inline-block;
      padding: 14px 36px;
      background: var(--accent);
      color: #000;
      font-weight: 700;
      font-size: 15px;
      border-radius: 8px;
      transition: box-shadow 0.3s ease;
      font-family: var(--font-display);
    }
    .pricing-cta-section .cta-btn:hover {
      box-shadow: 0 0 28px rgba(254, 179, 84, 0.4);
    }

    /* Features "Próximamente" en card Business */
    .plan-features li.soon-feature::before { content: '○'; color: var(--text-3); flex-shrink: 0; }
    .plan-features li.soon-feature { color: var(--text-3); }
    /* Etiqueta "Activo" en nav link de precios */
    .nav-link-active {
      color: var(--accent) !important;
    }

    @media (max-width: 768px) {
      .precios-page::before { top: 96px; width: 110vw; opacity: 0.62; }
      .precios-page::after { top: 146px; width: 82vw; opacity: 0.68; }
      .pricing-page-hero { padding: 64px 20px 14px; }
      #precios { padding: 10px 20px 64px !important; }
      .pricing-compare { padding: 0 20px 60px; }
      .pricing-faq-section { padding: 0 20px 60px; }
      .pricing-cta-section { padding: 48px 20px 60px; }
    }
  </style>
</head>

<body class="phase-2">

  <!-- ── NAV ─────────────────────────────────────────────────────────── -->
  <nav id="nav" role="navigation" aria-label="Navegación principal">
    <a href="/" class="nav-logo" aria-label="Oxphyre inicio">Oxphyre</a>

    <div class="nav-links">
      <a href="/#carousel-section" data-i18n="nav.carousel">Negocios</a>
      <a href="/#como-funciona"    data-i18n="nav.how">Cómo funciona</a>
      <a href="/#demo"             data-i18n="nav.demo">Demo</a>
      <a href="/#caracteristicas"  data-i18n="nav.features">Características</a>
      <a href="/precios" class="nav-link-active" data-i18n="nav.pricing">Precios</a>
      <a href="/#faq"              data-i18n="nav.faq">FAQ</a>
    </div>

    <div class="nav-actions">
      <button id="theme-toggle" aria-label="Activar modo claro" data-theme="dark">
        <i data-lucide="sun" width="18" height="18"></i>
      </button>
      <button class="lang-btn active" data-lang="es" aria-label="Español">ES</button>
      <span class="lang-divider">/</span>
      <button class="lang-btn" data-lang="en" aria-label="English">EN</button>
      <a href="/login"    class="btn-ghost"   data-i18n="nav.login">Iniciar sesión</a>
      <a href="/registro?plan=free" class="btn-primary" data-i18n="nav.cta">Empezar gratis</a>
    </div>

    <button id="menu-toggle" aria-label="Abrir menú" aria-expanded="false" style="margin-left:auto;">
      <i data-lucide="menu" width="24" height="24"></i>
    </button>
  </nav>

  <!-- Menú móvil -->
  <div id="mobile-menu" role="dialog" aria-modal="true" aria-label="Menú">
    <button id="mobile-menu-close" aria-label="Cerrar menú" style="position:absolute;top:24px;right:24px;color:var(--text-2);">
      <i data-lucide="x" width="28" height="28"></i>
    </button>
    <a href="/#carousel-section" data-i18n="nav.carousel">Negocios</a>
    <a href="/#como-funciona"    data-i18n="nav.how">Cómo funciona</a>
    <a href="/#demo"             data-i18n="nav.demo">Demo</a>
    <a href="/#caracteristicas"  data-i18n="nav.features">Características</a>
    <a href="/precios" data-i18n="nav.pricing">Precios</a>
    <a href="/#faq"              data-i18n="nav.faq">FAQ</a>
    <a href="/login"             data-i18n="nav.login">Iniciar sesión</a>
    <a href="/registro?plan=free" class="btn-primary" data-i18n="nav.cta">Empezar gratis</a>
    <div class="mobile-menu-footer">
      <button class="lang-btn active" data-lang="es">ES</button>
      <span class="lang-divider">/</span>
      <button class="lang-btn" data-lang="en">EN</button>
    </div>
  </div>


  <!-- ── CONTENIDO PRINCIPAL ──────────────────────────────────────────── -->
  <main class="precios-page">

    <!-- Header de la página -->
    <header class="pricing-page-hero">
      <h1 class="pricing-page-h1">Elige el plan perfecto<br>para <em>tu negocio</em></h1>
      <p class="pricing-page-sub">Sin tarjeta de crédito. Sin comisiones ocultas. Cancela cuando quieras.</p>
    </header>

    <!-- ── CARDS DE PLANES ── -->
    <section id="precios" aria-labelledby="pricing-cards-label">
      <p id="pricing-cards-label" class="sr-only">Planes y precios</p>

      <div class="pricing-toggle">
        <span class="toggle-label monthly" data-i18n="pricing.toggle_monthly">Mensual</span>
        <button id="billing-toggle" role="switch" aria-checked="false" aria-label="Cambiar entre facturación mensual y anual"></button>
        <span class="toggle-label annual" data-i18n="pricing.toggle_annual">Anual</span>
        <span class="badge-save" data-i18n="pricing.badge_save">Ahorra 20%</span>
      </div>

      <div class="pricing-grid">

        <!-- FREE -->
        <article class="pricing-card">
          <p class="plan-name" data-i18n="pricing.free_name">Free</p>
          <p class="plan-desc" data-i18n="pricing.free_desc">Para probar Oxphyre sin compromiso.</p>
          <div class="plan-price">
            <span class="price-amount" data-monthly="0€" data-annual="0€">0€</span>
            <span class="price-period" data-i18n="pricing.per_month">/mes</span>
          </div>
          <p class="plan-annual-total" aria-hidden="true">&nbsp;</p>
          <ul class="plan-features" aria-label="Características del plan Free">
            <li data-i18n="pricing.free_f1">1 negocio · 1 tour activo</li>
            <li data-i18n="pricing.free_f2">Hasta 3 posiciones/zonas</li>
            <li data-i18n="pricing.free_f3">QR básico con branding Oxphyre</li>
            <li data-i18n="pricing.free_f4">Flechas de navegación básicas</li>
            <li data-i18n="pricing.free_f5">Mapa de ubicación del negocio</li>
            <li data-i18n="pricing.free_f6">Marca de agua Oxphyre en el visor</li>
          </ul>
          <a href="/registro?plan=free" class="plan-cta" data-i18n="pricing.cta_free">Empezar gratis</a>
          <p class="plan-micro-note" data-i18n="pricing.free_note">Sin tarjeta. Sin compromiso.</p>
        </article>

        <!-- PRO — destacado -->
        <article class="pricing-card featured">
          <span class="popular-badge" data-i18n="pricing.popular">Más popular</span>
          <p class="plan-name" data-i18n="pricing.pro_name">Pro</p>
          <p class="plan-desc" data-i18n="pricing.pro_desc">Para negocios que quieren destacar.</p>
          <div class="plan-price">
            <span class="price-amount" data-monthly="19€" data-annual="15€">19€</span>
            <span class="price-period" data-i18n="pricing.per_month">/mes</span>
          </div>
          <p class="plan-annual-total" data-i18n="pricing.pro_annual_total">182€/año · Ahorras 46€</p>
          <ul class="plan-features" aria-label="Características del plan Pro">
            <li data-i18n="pricing.pro_f1">Hasta 5 negocios · Tours ilimitados</li>
            <li data-i18n="pricing.pro_f2">Hasta 20 posiciones por tour</li>
            <li data-i18n="pricing.pro_f3">Sin marca de agua</li>
            <li data-i18n="pricing.pro_f4">Embed/iframe en tu web</li>
            <li data-i18n="pricing.pro_f5">Analíticas básicas · QR profesional</li>
          </ul>
          <a href="/registro?plan=pro" class="plan-cta featured-cta" data-i18n="pricing.cta_pro">Empezar con Pro</a>
          <p class="plan-micro-note" data-i18n="pricing.pro_note">Actualiza o cancela en cualquier momento.</p>
        </article>

        <!-- BUSINESS -->
        <article class="pricing-card">
          <p class="plan-name" data-i18n="pricing.biz_name">Business</p>
          <p class="plan-desc" data-i18n="pricing.biz_desc">Para empresas con necesidades avanzadas.</p>
          <div class="plan-price">
            <span class="price-amount" data-monthly="49€" data-annual="39€">49€</span>
            <span class="price-period" data-i18n="pricing.per_month">/mes</span>
          </div>
          <p class="plan-annual-total" data-i18n="pricing.biz_annual_total">470€/año · Ahorras 118€</p>
          <ul class="plan-features" aria-label="Características del plan Business">
            <li data-i18n="pricing.biz_f1">Negocios y posiciones ilimitadas</li>
            <li class="soon-feature" data-i18n="pricing.biz_f2">Dominio personalizado (próximamente)</li>
            <li class="soon-feature" data-i18n="pricing.biz_f3">Analíticas avanzadas (próximamente)</li>
            <li data-i18n="pricing.biz_f4">Soporte prioritario + onboarding</li>
            <li class="soon-feature" data-i18n="pricing.biz_f5">API access (próximamente)</li>
          </ul>
          <a href="/registro?plan=business" class="plan-cta" data-i18n="pricing.cta_biz">Empezar con Business</a>
          <p class="plan-micro-note" data-i18n="pricing.biz_note">Acceso completo. Sin límites.</p>
        </article>

      </div>
    </section>


    <!-- ── TABLA COMPARATIVA ─────────────────────────────────────────── -->
    <section class="pricing-compare" aria-labelledby="compare-h2">
      <h2 id="compare-h2">¿Qué incluye cada plan?</h2>
      <p class="pricing-seo-note">Antes de elegir plan, puedes ver <a href="/tour-virtual-para-negocios">cómo funciona un tour virtual para negocios</a> y qué verá tu cliente al abrirlo.</p>
      <div class="compare-wrap">
        <table class="compare-table">
          <thead>
            <tr>
              <th scope="col">Característica</th>
              <th scope="col">Free</th>
              <th scope="col" class="th-pro">Pro</th>
              <th scope="col">Business</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Negocios</td>
              <td class="val">1</td>
              <td class="val">Hasta 5</td>
              <td class="val">Ilimitados</td>
            </tr>
            <tr>
              <td>Tours por negocio</td>
              <td class="val">1</td>
              <td class="val">Ilimitados</td>
              <td class="val">Ilimitados</td>
            </tr>
            <tr>
              <td>Posiciones por tour</td>
              <td class="val">3</td>
              <td class="val">20</td>
              <td class="val">Ilimitadas</td>
            </tr>
            <tr>
              <td>Enlace público</td>
              <td class="check">✓</td>
              <td class="check">✓</td>
              <td class="check">✓</td>
            </tr>
            <tr>
              <td>QR descargable</td>
              <td class="val">Básico</td>
              <td class="val">Profesional</td>
              <td class="val">Profesional</td>
            </tr>
            <tr>
              <td>Flechas de navegación</td>
              <td class="val">Básicas</td>
              <td class="val">Avanzadas</td>
              <td class="val">Avanzadas</td>
            </tr>
            <tr>
              <td>Mapa de ubicación del negocio</td>
              <td class="check">✓</td>
              <td class="check">✓</td>
              <td class="check">✓</td>
            </tr>
            <tr>
              <td>Marca de agua Oxphyre</td>
              <td class="val">Sí</td>
              <td class="cross">—</td>
              <td class="cross">—</td>
            </tr>
            <tr>
              <td>Embed/iframe en tu web</td>
              <td class="cross">—</td>
              <td class="check">✓</td>
              <td class="check">✓</td>
            </tr>
            <tr>
              <td>Analíticas</td>
              <td class="cross">—</td>
              <td class="val">Básicas</td>
              <td class="val">Avanzadas</td>
            </tr>
            <tr>
              <td>Dominio personalizado</td>
              <td class="cross">—</td>
              <td class="cross">—</td>
              <td><span class="soon">Próximamente</span></td>
            </tr>
            <tr>
              <td>Marca blanca</td>
              <td class="cross">—</td>
              <td class="cross">—</td>
              <td><span class="soon">Próximamente</span></td>
            </tr>
            <tr>
              <td>API access</td>
              <td class="cross">—</td>
              <td class="cross">—</td>
              <td><span class="soon">Próximamente</span></td>
            </tr>
            <tr>
              <td>Soporte</td>
              <td class="cross">—</td>
              <td class="val">Email 48h</td>
              <td class="val">Prioritario</td>
            </tr>
            <tr class="row-price">
              <td>Precio mensual</td>
              <td>0€</td>
              <td class="th-pro">19€/mes</td>
              <td>49€/mes</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>


    <!-- ── FAQ DE PLANES ─────────────────────────────────────────────── -->
    <section class="pricing-faq-section" aria-labelledby="pfaq-h2">
      <h2 id="pfaq-h2">Preguntas sobre los planes</h2>
      <div class="faq-list" role="list">

        <div class="faq-item" role="listitem">
          <button class="faq-question" aria-expanded="false">
            <span>¿El plan Free tiene límite de tiempo?</span>
            <i data-lucide="chevron-down" class="faq-question-icon" aria-hidden="true"></i>
          </button>
          <div class="faq-answer" role="region">
            <p>No. El plan Free es gratuito para siempre. Sin tarjeta de crédito. Puedes crear 1 negocio con 1 tour de hasta 3 posiciones/zonas y compartir el enlace público sin pagar nada.</p>
          </div>
        </div>

        <div class="faq-item" role="listitem">
          <button class="faq-question" aria-expanded="false">
            <span>¿Puedo cambiar de plan en cualquier momento?</span>
            <i data-lucide="chevron-down" class="faq-question-icon" aria-hidden="true"></i>
          </button>
          <div class="faq-answer" role="region">
            <p>Sí. Puedes subir de Free a Pro o Business desde tu dashboard en cualquier momento. También puedes cancelar y volver a Free cuando quieras, sin penalizaciones.</p>
          </div>
        </div>

        <div class="faq-item" role="listitem">
          <button class="faq-question" aria-expanded="false">
            <span>¿Qué pasa con mis tours si cancelo Pro o Business?</span>
            <i data-lucide="chevron-down" class="faq-question-icon" aria-hidden="true"></i>
          </button>
          <div class="faq-answer" role="region">
            <p>Tu primer tour sigue siendo accesible en modo Free (hasta 3 posiciones). Los tours y posiciones adicionales quedan archivados y los puedes reactivar cuando vuelvas a suscribirte.</p>
          </div>
        </div>

        <div class="faq-item" role="listitem">
          <button class="faq-question" aria-expanded="false">
            <span>¿El embed/iframe está disponible en el plan Free?</span>
            <i data-lucide="chevron-down" class="faq-question-icon" aria-hidden="true"></i>
          </button>
          <div class="faq-answer" role="region">
            <p>No. El embed solo está disponible desde el plan Pro. Con Free puedes compartir tu tour mediante enlace público o QR básico, pero no puedes incrustarlo en tu web propia.</p>
          </div>
        </div>

        <div class="faq-item" role="listitem">
          <button class="faq-question" aria-expanded="false">
            <span>¿Funciona en móviles y tablets?</span>
            <i data-lucide="chevron-down" class="faq-question-icon" aria-hidden="true"></i>
          </button>
          <div class="faq-answer" role="region">
            <p>Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hace falta instalar ninguna app. Está optimizado especialmente para la experiencia desde móvil al escanear el QR.</p>
          </div>
        </div>

      </div>
    </section>


    <!-- ── CTA FINAL ─────────────────────────────────────────────────── -->
    <section class="pricing-cta-section" aria-labelledby="pricing-cta-h2">
      <p id="pricing-cta-h2">Tu negocio merece ser descubierto.</p>
      <p class="cta-sub">Empieza gratis hoy. Sin tarjeta de crédito.</p>
      <a href="/registro?plan=free" class="cta-btn">Crear mi tour gratis →</a>
    </section>


    <!-- ── FOOTER ────────────────────────────────────────────────────── -->
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
              <li><a href="/#caracteristicas" data-i18n="footer.features">Características</a></li>
              <li><a href="/blog">Blog</a></li>
              <li><a href="/precios"           data-i18n="footer.pricing">Precios</a></li>
              <li><a href="/#demo"             data-i18n="footer.demo">Demo</a></li>
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

        </div>

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

      </div>
    </footer>

  </main>


  <!-- Scripts: Lucide + i18n + inline JS (sin Three.js, sin main.js) -->
  <script src="<?= asset('/js/i18n.js') ?>" defer></script>

  <script>
  (function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
      // ── Lucide icons ──
      if (typeof lucide !== 'undefined') lucide.createIcons();

      // ── i18n ──
      if (window.i18n) window.i18n.initLang();

      // ── Nav glassmorphism al hacer scroll ──
      var nav = document.getElementById('nav');
      if (nav) {
        window.addEventListener('scroll', function () {
          nav.classList.toggle('nav-scrolled', window.scrollY > 40);
        }, { passive: true });
      }

      // ── Menú móvil ──
      var menuToggle  = document.getElementById('menu-toggle');
      var mobileMenu  = document.getElementById('mobile-menu');
      var mobileClose = document.getElementById('mobile-menu-close');

      function openMobileMenu() {
        mobileMenu.classList.add('open');
        menuToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      }
      function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }

      if (menuToggle) menuToggle.addEventListener('click', openMobileMenu);
      if (mobileClose) mobileClose.addEventListener('click', closeMobileMenu);
      if (mobileMenu) {
        mobileMenu.querySelectorAll('a').forEach(function (a) {
          a.addEventListener('click', closeMobileMenu);
        });
      }

      // ── Toggle facturación mensual / anual ──
      var billingToggle   = document.getElementById('billing-toggle');
      var pricingSection  = document.getElementById('precios');

      if (billingToggle && pricingSection) {
        billingToggle.addEventListener('click', function () {
          var isAnnual = pricingSection.classList.toggle('annual');
          billingToggle.setAttribute('aria-checked', isAnnual);
          pricingSection.querySelectorAll('[data-monthly]').forEach(function (el) {
            el.textContent = isAnnual ? el.dataset.annual : el.dataset.monthly;
          });
        });
      }

      // ── Acordeón FAQ ──
      document.querySelectorAll('.faq-item').forEach(function (item) {
        var question = item.querySelector('.faq-question');
        var answer   = item.querySelector('.faq-answer');
        if (!question || !answer) return;

        question.addEventListener('click', function () {
          var isOpen = item.classList.contains('open');
          document.querySelectorAll('.faq-item.open').forEach(function (open) {
            open.classList.remove('open');
            open.querySelector('.faq-answer').style.maxHeight = '0';
            open.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
          });
          if (!isOpen) {
            item.classList.add('open');
            answer.style.maxHeight = answer.scrollHeight + 'px';
            question.setAttribute('aria-expanded', 'true');
          }
        });
      });

      // ── ES/EN toggle (mismo patrón que main.js) ──
      document.querySelectorAll('.lang-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (window.i18n) window.i18n.applyLang(btn.dataset.lang);
          localStorage.setItem('oxphyre-lang', btn.dataset.lang);
        });
      });

    });
  })();
  </script>

</body>
</html>
