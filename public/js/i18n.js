/**
 * Sistema de internacionalización (i18n) para Oxphyre.
 *
 * Todos los textos visibles tienen un atributo data-i18n con una clave.
 * applyLang() recorre el DOM y sustituye el contenido con el texto del idioma activo.
 * La preferencia se guarda en localStorage para mantenerla entre visitas.
 */

const translations = {
  es: {
    nav: {
      how: 'Cómo funciona', demo: 'Demo',
      features: 'Características', pricing: 'Precios', faq: 'FAQ',
      login: 'Iniciar sesión', cta: 'Empezar gratis'
    },
    hero: {
      phase1_0:   'Bienvenido a la profundidad.',
      phase1_90:  'Aquí, tu espacio cobra vida.',
      phase1_180: 'Cada rincón, capturado en su mejor momento.',
      phase1_270: 'No es una foto. Es tu negocio vivo.',
      phase1_360: '↓ Explora la dimensión Oxphyre',
      h1:       'Tours virtuales 3D para negocios que quieren brillar.',
      subtitle: 'Convierte tu local en una experiencia 360° que tus clientes pueden visitar desde cualquier lugar. Sin cámaras especiales, sin técnicos, sin complicaciones.',
      cta_primary:   'Crear mi tour gratis →',
      cta_secondary: 'Ver un tour en vivo',
      pill1: '✓ Sin hardware especial',
      pill2: '✓ Listo en menos de 1 hora',
      pill3: '✓ Funciona en cualquier móvil'
    },
    carousel: {
      title: 'Tu negocio, en primera persona',
      c1_title: 'Restaurante',  c1_text: 'Que reserven antes de probar tu cocina.',
      c2_title: 'Gimnasio',     c2_text: 'Que vean las instalaciones antes de apuntarse.',
      c3_title: 'Peluquería',   c3_text: 'Que conozcan tu espacio antes de su cita.',
      c4_title: 'Hotel',        c4_text: 'Que elijan su habitación antes de reservar.',
      c5_title: 'Tienda',       c5_text: 'Que exploren tu tienda desde el sofá.',
      c6_title: 'Inmobiliaria', c6_text: 'Que visiten la propiedad sin salir de casa.',
      c7_title: 'Clínica',      c7_text: 'Que conozcan tu consulta antes de su primera cita.',
      c8_title: 'Coworking',    c8_text: 'Que sientan el espacio antes de reservar su mesa.'
    },
    steps: {
      title: 'Cómo funciona',
      subtitle: 'Tu tour virtual en tres pasos. Sin curva de aprendizaje.',
      s1_num: '01', s1_title: 'Fotografías tu local',
      s1_desc: 'Fotografía cada posición en 4 direcciones (N, S, E, O). Solo necesitas tu móvil.',
      s2_num: '02', s2_title: 'Construyes el tour',
      s2_desc: 'Sube las fotos a Oxphyre y conecta las posiciones en nuestro editor visual drag & drop.',
      s3_num: '03', s3_title: 'Lo compartes',
      s3_desc: 'Descarga el QR y ponlo donde quieras. Tus clientes escanean y exploran tu negocio en 3D.'
    },
    demo: {
      title: 'Mira cómo funciona',
      subtitle: 'Descubre cómo un negocio real se convierte en un tour virtual 3D navegable. Sin registro.',
      cta: 'Ver tour en vivo'
    },
    features: {
      title: 'Todo lo que necesitas',
      subtitle: 'Herramientas pensadas para negocios reales.',
      f1_title: 'Tour 3D navegable',
      f1_desc: 'Renderizado con Three.js. Tus clientes se mueven por el local como si estuvieran allí.',
      f2_title: 'Hotspots interactivos',
      f2_desc: 'Añade puntos de información, precios, productos o links en cualquier punto del tour.',
      f3_title: 'QR + embed',
      f3_desc: 'Un código QR descargable y un snippet para insertar el tour en tu web con una línea.',
      f4_title: 'Analíticas de visitas',
      f4_desc: 'Sabe cuántas personas han explorado tu negocio, desde dónde y cuánto tiempo estuvieron.',
      f5_title: 'Modo día/noche',
      f5_desc: 'El tour se adapta automáticamente a las preferencias del dispositivo del visitante.',
      f6_title: 'Compatible con cualquier móvil',
      f6_desc: 'Funciona en iOS y Android sin instalar nada. Solo un navegador moderno.'
    },
    pricing: {
      title: 'Precios transparentes',
      subtitle: 'Sin comisiones ocultas. Cancela cuando quieras.',
      toggle_monthly: 'Mensual', toggle_annual: 'Anual',
      badge_save: 'Ahorra 20%',
      free_name: 'Free',     free_price_monthly: '0€',  free_price_annual: '0€',
      free_desc: 'Para probar Oxphyre sin compromiso.',
      pro_name: 'Pro',       pro_price_monthly: '19€',  pro_price_annual: '15€',
      pro_desc: 'Para negocios que quieren destacar.',
      biz_name: 'Business',  biz_price_monthly: '49€',  biz_price_annual: '39€',
      biz_desc: 'Para cadenas y agencias de marketing.',
      per_month: '/mes',
      cta_free: 'Empezar gratis', cta_pro: 'Empezar con Pro', cta_biz: 'Contactar ventas',
      popular: 'Más popular',
      free_f1: '1 tour activo',        free_f2: 'Hasta 5 posiciones',
      free_f3: 'QR descargable',       free_f4: 'Marca de agua Oxphyre',
      pro_f1: 'Tours ilimitados',      pro_f2: 'Hasta 20 posiciones',
      pro_f3: 'MiDaS IA profundidad',  pro_f4: 'Analíticas básicas',      pro_f5: 'Sin marca de agua',
      biz_f1: 'Todo ilimitado',        biz_f2: 'MiDaS máxima calidad',
      biz_f3: 'Analíticas avanzadas',  biz_f4: 'Dominio personalizado',   biz_f5: 'API access'
    },
    faq: {
      title: 'Preguntas frecuentes',
      q1: '¿Necesito equipo especial para hacer el tour?',
      a1: 'No. Solo necesitas un smartphone con cámara decente. Nuestro sistema procesa las fotos automáticamente y genera la profundidad con inteligencia artificial (MiDaS de Intel). Nada de cámaras 360 ni software de edición.',
      q2: '¿Cuánto tiempo tarda en estar listo el tour?',
      a2: 'Con el plan Free, el tour está listo en minutos. Con los planes Pro y Business, el procesado con IA de profundidad (MiDaS) tarda entre 5 y 15 minutos según el número de posiciones.',
      q3: '¿Puedo insertar el tour en mi web existente?',
      a3: 'Sí. Todos los planes incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace. El plan Business incluye además dominio personalizado.',
      q4: '¿Qué pasa si cancelo mi suscripción?',
      a4: 'Tus tours siguen siendo accesibles en modo Free (1 tour, 5 posiciones). Si tenías más tours, quedan archivados y los puedes reactivar cuando vuelvas a suscribirte.',
      q5: '¿Funciona en móviles y tablets?',
      a5: 'Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hay que instalar ninguna app. Está optimizado especialmente para la experiencia desde móvil al escanear el QR.',
      q6: '¿Mis fotos y datos están seguros?',
      a6: 'Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros ni las usamos para entrenar modelos. Cumplimos con el RGPD europeo.'
    },
    cta_final: {
      title: 'Tu negocio merece ser descubierto.',
      subtitle: 'Empieza gratis hoy. Sin tarjeta de crédito.',
      cta: 'Crear mi tour gratis →'
    },
    footer: {
      tagline: 'Tours virtuales 3D para negocios locales.',
      product: 'Producto', features: 'Características', pricing: 'Precios',
      demo: 'Demo', changelog: 'Novedades',
      legal: 'Legal', privacy: 'Privacidad', terms: 'Términos', cookies: 'Cookies',
      contact: 'Contacto', about: 'Sobre nosotros', blog: 'Blog', support: 'Soporte',
      social: 'Redes',
      copyright: '© 2026 Oxphyre. Todos los derechos reservados.'
    }
  },
  en: {
    nav: {
      how: 'How it works', demo: 'Demo',
      features: 'Features', pricing: 'Pricing', faq: 'FAQ',
      login: 'Sign in', cta: 'Start for free'
    },
    hero: {
      phase1_0:   'Welcome to the depth.',
      phase1_90:  'Here, your space comes alive.',
      phase1_180: 'Every corner, captured at its best.',
      phase1_270: 'Not a photo. Your business, alive.',
      phase1_360: '↓ Explore the Oxphyre dimension',
      h1:       '3D virtual tours for businesses that want to shine.',
      subtitle: 'Turn your space into a 360° experience your customers can visit from anywhere. No special cameras, no technicians, no hassle.',
      cta_primary:   'Create my free tour →',
      cta_secondary: 'See a live tour',
      pill1: '✓ No special hardware',
      pill2: '✓ Ready in under 1 hour',
      pill3: '✓ Works on any phone'
    },
    carousel: {
      title: 'Your business, in first person',
      c1_title: 'Restaurant',  c1_text: 'Let them book before tasting your food.',
      c2_title: 'Gym',         c2_text: 'Let them see the facilities before joining.',
      c3_title: 'Hair Salon',  c3_text: 'Let them know your space before their appointment.',
      c4_title: 'Hotel',       c4_text: 'Let them choose their room before booking.',
      c5_title: 'Shop',        c5_text: 'Let them browse your store from the sofa.',
      c6_title: 'Real Estate', c6_text: 'Let them visit the property without leaving home.',
      c7_title: 'Clinic',      c7_text: 'Let them see your office before their first appointment.',
      c8_title: 'Coworking',   c8_text: 'Let them feel the space before reserving their desk.'
    },
    steps: {
      title: 'How it works',
      subtitle: 'Your virtual tour in three steps. No learning curve.',
      s1_num: '01', s1_title: 'Photograph your space',
      s1_desc: 'Photograph each position in 4 directions (N, S, E, W). All you need is your phone.',
      s2_num: '02', s2_title: 'Build the tour',
      s2_desc: 'Upload photos to Oxphyre and connect positions in our drag & drop visual editor.',
      s3_num: '03', s3_title: 'Share it',
      s3_desc: 'Download the QR and place it anywhere. Customers scan and explore your business in 3D.'
    },
    demo: {
      title: 'See it in action',
      subtitle: 'Discover how a real business becomes a navigable 3D virtual tour. No sign-up required.',
      cta: 'View live tour'
    },
    features: {
      title: 'Everything you need',
      subtitle: 'Tools built for real businesses.',
      f1_title: '3D navigable tour',
      f1_desc: 'Rendered with Three.js. Customers move through your space as if they were there.',
      f2_title: 'Interactive hotspots',
      f2_desc: 'Add info points, prices, products or links anywhere in the tour.',
      f3_title: 'QR + embed',
      f3_desc: 'A downloadable QR code and a one-line snippet to embed the tour on your site.',
      f4_title: 'Visit analytics',
      f4_desc: "Know how many people explored your business, where they're from and how long they stayed.",
      f5_title: 'Light/dark mode',
      f5_desc: "The tour adapts automatically to the visitor's device preferences.",
      f6_title: 'Works on any phone',
      f6_desc: 'Works on iOS and Android without installing anything. Just a modern browser.'
    },
    pricing: {
      title: 'Transparent pricing',
      subtitle: 'No hidden fees. Cancel anytime.',
      toggle_monthly: 'Monthly', toggle_annual: 'Annual',
      badge_save: 'Save 20%',
      free_name: 'Free',     free_price_monthly: '€0',  free_price_annual: '€0',
      free_desc: 'Try Oxphyre without commitment.',
      pro_name: 'Pro',       pro_price_monthly: '€19',  pro_price_annual: '€15',
      pro_desc: 'For businesses that want to stand out.',
      biz_name: 'Business',  biz_price_monthly: '€49',  biz_price_annual: '€39',
      biz_desc: 'For chains and marketing agencies.',
      per_month: '/mo',
      cta_free: 'Start for free', cta_pro: 'Start with Pro', cta_biz: 'Contact sales',
      popular: 'Most popular',
      free_f1: '1 active tour',        free_f2: 'Up to 5 positions',
      free_f3: 'Downloadable QR',      free_f4: 'Oxphyre watermark',
      pro_f1: 'Unlimited tours',       pro_f2: 'Up to 20 positions',
      pro_f3: 'MiDaS AI depth',        pro_f4: 'Basic analytics',         pro_f5: 'No watermark',
      biz_f1: 'Everything unlimited',  biz_f2: 'MiDaS max quality',
      biz_f3: 'Advanced analytics',    biz_f4: 'Custom domain',           biz_f5: 'API access'
    },
    faq: {
      title: 'Frequently asked questions',
      q1: 'Do I need special equipment to create a tour?',
      a1: 'No. You only need a smartphone with a decent camera. Our system processes the photos automatically and generates depth using AI (Intel MiDaS). No 360° cameras or editing software needed.',
      q2: 'How long does it take for the tour to be ready?',
      a2: 'With the Free plan, the tour is ready in minutes. With Pro and Business plans, the AI depth processing (MiDaS) takes 5 to 15 minutes depending on the number of positions.',
      q3: 'Can I embed the tour on my existing website?',
      a3: 'Yes. All plans include an embed code (iframe) you can paste into any website, WordPress, Wix, or Squarespace. The Business plan also includes a custom domain.',
      q4: 'What happens if I cancel my subscription?',
      a4: 'Your tours remain accessible in Free mode (1 tour, 5 positions). If you had more tours, they are archived and can be reactivated when you resubscribe.',
      q5: 'Does it work on mobile and tablets?',
      a5: 'Yes. The tour works on any device with a modern browser. No app installation needed. It is especially optimized for the mobile scanning experience.',
      q6: 'Are my photos and data safe?',
      a6: 'Yes. Photos are stored on our own encrypted servers. We do not share them with third parties or use them to train models. We comply with the European GDPR.'
    },
    cta_final: {
      title: 'Your business deserves to be discovered.',
      subtitle: 'Start for free today. No credit card required.',
      cta: 'Create my free tour →'
    },
    footer: {
      tagline: '3D virtual tours for local businesses.',
      product: 'Product', features: 'Features', pricing: 'Pricing',
      demo: 'Demo', changelog: "What's new",
      legal: 'Legal', privacy: 'Privacy', terms: 'Terms', cookies: 'Cookies',
      contact: 'Contact', about: 'About us', blog: 'Blog', support: 'Support',
      social: 'Social',
      copyright: '© 2026 Oxphyre. All rights reserved.'
    }
  }
};

/** Recorre todos los elementos con data-i18n y sustituye su contenido. */
function applyLang(lang) {
  const t = translations[lang];
  if (!t) return;

  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.dataset.i18n;
    const parts = key.split('.');
    let value = t;
    for (const part of parts) {
      value = value?.[part];
    }
    if (value !== undefined) {
      if (el.hasAttribute('placeholder')) {
        el.placeholder = value;
      } else {
        el.textContent = value;
      }
    }
  });

  document.documentElement.lang = lang;

  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.lang === lang);
  });
}

/** Inicializa el idioma: localStorage → prefers navegador → 'es' */
function initLang() {
  const saved = localStorage.getItem('oxphyre-lang');
  const browser = navigator.language?.startsWith('en') ? 'en' : 'es';
  const lang = saved || browser;
  applyLang(lang);
}

window.i18n = { applyLang, initLang, translations };
