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
      features: 'Características', pricing: 'Precios', demo: 'Demo',
      contact: 'Contacto', login: 'Iniciar sesión', cta: 'Empezar gratis'
    },
    hero: {
      eyebrow: 'Tours virtuales 3D para negocios locales',
      h1: 'Haz que tus clientes visiten tu negocio antes de llegar',
      subtitle: 'Sube fotos de tu local, construye el tour en minutos y compártelo con un QR. Sin instalaciones ni hardware especial.',
      cta_primary: 'Empezar gratis', cta_secondary: 'Ver demo',
      stat1_label: 'Tour activo', stat1_value: 'En vivo',
      stat2_label: 'Tiempo medio', stat2_value: '4:32 min',
      stat3_label: 'Visitantes hoy', stat3_value: '127'
    },
    logos: { title: 'Confían en Oxphyre' },
    steps: {
      title: 'Cómo funciona',
      subtitle: 'Tu tour virtual en tres pasos. Sin curva de aprendizaje.',
      s1_title: 'Haz fotos de tu local',
      s1_desc: 'Fotografía cada posición en 4 direcciones (N, S, E, O). Solo necesitas tu móvil.',
      s2_title: 'Construye el tour',
      s2_desc: 'Sube las fotos a Oxphyre y conecta las posiciones en nuestro editor visual drag & drop.',
      s3_title: 'Compártelo con un QR',
      s3_desc: 'Descarga el QR y ponlo donde quieras. Tus clientes escanean y exploran tu negocio en 3D.'
    },
    features: {
      title: 'Todo lo que necesitas',
      subtitle: 'Herramientas pensadas para negocios reales, no para agencias.',
      f1_title: 'Tour navegable en 3D', f1_desc: 'Renderizado con Three.js. Tus clientes se mueven por el local como si estuvieran allí.',
      f2_title: 'Hotspots interactivos', f2_desc: 'Añade puntos de información, precios, productos o links en cualquier punto del tour.',
      f3_title: 'QR + embed para tu web', f3_desc: 'Un código QR descargable y un snippet para insertar el tour en tu web con una línea.',
      f4_title: 'Analíticas de visitas', f4_desc: 'Sabe cuántas personas han explorado tu negocio, desde dónde y cuánto tiempo estuvieron.',
      f5_title: 'Modo día/noche', f5_desc: 'El tour se adapta automáticamente a las preferencias del dispositivo del visitante.',
      f6_title: 'Compatible con cualquier móvil', f6_desc: 'Funciona en iOS y Android sin instalar nada. Solo un navegador moderno.'
    },
    demo: {
      title: 'Prueba un tour de ejemplo',
      subtitle: 'Sin registro. Explora cómo queda un tour real en un negocio de ejemplo.',
      cta: 'Ver tour de demo'
    },
    pricing: {
      title: 'Precios transparentes',
      subtitle: 'Sin comisiones ocultas. Cancela cuando quieras.',
      toggle_monthly: 'Mensual', toggle_annual: 'Anual',
      badge_save: 'Ahorra 20%',
      free_name: 'Free', free_price_monthly: '0€', free_price_annual: '0€',
      free_desc: 'Para probar Oxphyre sin compromiso.',
      pro_name: 'Pro', pro_price_monthly: '19€', pro_price_annual: '15€',
      pro_desc: 'Para negocios que quieren destacar.',
      biz_name: 'Business', biz_price_monthly: '49€', biz_price_annual: '39€',
      biz_desc: 'Para cadenas y agencias de marketing.',
      per_month: '/mes', cta_free: 'Empezar gratis',
      cta_pro: 'Empezar con Pro', cta_biz: 'Contactar ventas', popular: 'Más popular'
    },
    testimonials: {
      title: 'Lo que dicen nuestros clientes',
      t1_name: 'Carlos M.', t1_role: 'Propietario, GymFit Madrid',
      t1_text: 'Desde que puse el tour virtual en mi Instagram, recibo el doble de consultas. La gente ya viene sabiendo cómo es el gimnasio.',
      t2_name: 'Laura S.', t2_role: 'Dueña, Peluquería Glamour',
      t2_text: 'Lo monté en una tarde sin saber nada de tecnología. El QR en mi escaparate lo escanean constantemente.',
      t3_name: 'Ahmed R.', t3_role: 'Gerente, Restaurante Babel',
      t3_text: 'Mis clientes reservan mesa después de ver el tour. El ambiente del local es lo que les convence antes de llegar.'
    },
    faq: {
      title: 'Preguntas frecuentes',
      q1: '¿Necesito equipo especial para hacer el tour?', a1: 'No. Solo necesitas un smartphone con cámara decente. Nuestro sistema procesa las fotos automáticamente y genera la profundidad con inteligencia artificial (MiDaS de Intel). Nada de cámaras 360 ni software de edición.',
      q2: '¿Cuánto tiempo tarda en estar listo el tour?', a2: 'Con el plan Free, el tour está listo en minutos. Con los planes Pro y Business, el procesado con IA de profundidad (MiDaS) tarda entre 5 y 15 minutos según el número de posiciones.',
      q3: '¿Puedo insertar el tour en mi web existente?', a3: 'Sí. Todos los planes incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace. El plan Business incluye además dominio personalizado.',
      q4: '¿Qué pasa si cancelo mi suscripción?', a4: 'Tus tours siguen siendo accesibles en modo Free (1 tour, 5 posiciones). Si tenías más tours, quedan archivados y los puedes reactivar cuando vuelvas a suscribirte.',
      q5: '¿Funciona en móviles y tablets?', a5: 'Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hay que instalar ninguna app. Está optimizado especialmente para la experiencia desde móvil al escanear el QR.',
      q6: '¿Mis fotos y datos están seguros?', a6: 'Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros ni las usamos para entrenar modelos. Cumplimos con el RGPD europeo.'
    },
    cta_final: {
      title: 'Tu negocio merece ser visitado antes de que lleguen',
      subtitle: 'Únete a los negocios que ya usan Oxphyre para atraer más clientes.',
      cta: 'Crear mi tour gratis'
    },
    footer: {
      tagline: 'Tours virtuales 3D para negocios locales.',
      product: 'Producto', features: 'Características', pricing: 'Precios',
      demo: 'Demo', changelog: 'Novedades',
      legal: 'Legal', privacy: 'Privacidad', terms: 'Términos', cookies: 'Cookies',
      contact: 'Contacto', about: 'Sobre nosotros', blog: 'Blog', support: 'Soporte',
      copyright: '© 2026 Oxphyre. Todos los derechos reservados.'
    }
  },
  en: {
    nav: {
      features: 'Features', pricing: 'Pricing', demo: 'Demo',
      contact: 'Contact', login: 'Sign in', cta: 'Start for free'
    },
    hero: {
      eyebrow: '3D virtual tours for local businesses',
      h1: 'Let your customers visit your business before they arrive',
      subtitle: 'Upload photos of your space, build the tour in minutes and share it with a QR code. No installs, no special hardware.',
      cta_primary: 'Start for free', cta_secondary: 'See demo',
      stat1_label: 'Active tour', stat1_value: 'Live',
      stat2_label: 'Avg. duration', stat2_value: '4:32 min',
      stat3_label: "Today's visitors", stat3_value: '127'
    },
    logos: { title: 'Trusted by' },
    steps: {
      title: 'How it works',
      subtitle: 'Your virtual tour in three steps. No learning curve.',
      s1_title: 'Take photos of your space',
      s1_desc: 'Photograph each position in 4 directions (N, S, E, W). All you need is your phone.',
      s2_title: 'Build the tour',
      s2_desc: 'Upload photos to Oxphyre and connect positions in our drag & drop visual editor.',
      s3_title: 'Share with a QR code',
      s3_desc: 'Download the QR and place it anywhere. Customers scan and explore your business in 3D.'
    },
    features: {
      title: 'Everything you need',
      subtitle: 'Tools built for real businesses, not agencies.',
      f1_title: '3D navigable tour', f1_desc: 'Rendered with Three.js. Customers move through your space as if they were there.',
      f2_title: 'Interactive hotspots', f2_desc: 'Add info points, prices, products or links anywhere in the tour.',
      f3_title: 'QR + embed for your website', f3_desc: 'A downloadable QR code and a one-line snippet to embed the tour on your site.',
      f4_title: 'Visit analytics', f4_desc: "Know how many people explored your business, where they're from and how long they stayed.",
      f5_title: 'Light/dark mode', f5_desc: "The tour adapts automatically to the visitor's device preferences.",
      f6_title: 'Works on any phone', f6_desc: 'Works on iOS and Android without installing anything. Just a modern browser.'
    },
    demo: {
      title: 'Try a sample tour',
      subtitle: 'No sign-up required. Explore what a real tour looks like in a sample business.',
      cta: 'View demo tour'
    },
    pricing: {
      title: 'Transparent pricing',
      subtitle: 'No hidden fees. Cancel anytime.',
      toggle_monthly: 'Monthly', toggle_annual: 'Annual',
      badge_save: 'Save 20%',
      free_name: 'Free', free_price_monthly: '€0', free_price_annual: '€0',
      free_desc: 'Try Oxphyre without commitment.',
      pro_name: 'Pro', pro_price_monthly: '€19', pro_price_annual: '€15',
      pro_desc: 'For businesses that want to stand out.',
      biz_name: 'Business', biz_price_monthly: '€49', biz_price_annual: '€39',
      biz_desc: 'For chains and marketing agencies.',
      per_month: '/mo', cta_free: 'Start for free',
      cta_pro: 'Start with Pro', cta_biz: 'Contact sales', popular: 'Most popular'
    },
    testimonials: {
      title: 'What our customers say',
      t1_name: 'Carlos M.', t1_role: 'Owner, GymFit Madrid',
      t1_text: "Since I added the virtual tour to my Instagram, I get twice as many inquiries. People already know what the gym looks like.",
      t2_name: 'Laura S.', t2_role: 'Owner, Glamour Hair Salon',
      t2_text: 'I set it up in an afternoon with zero tech knowledge. The QR in my window gets scanned constantly.',
      t3_name: 'Ahmed R.', t3_role: 'Manager, Babel Restaurant',
      t3_text: 'Customers book tables after seeing the tour. The atmosphere of the place is what convinces them before they arrive.'
    },
    faq: {
      title: 'Frequently asked questions',
      q1: 'Do I need special equipment to create a tour?', a1: 'No. You only need a smartphone with a decent camera. Our system processes the photos automatically and generates depth using AI (Intel MiDaS). No 360° cameras or editing software needed.',
      q2: 'How long does it take for the tour to be ready?', a2: 'With the Free plan, the tour is ready in minutes. With Pro and Business plans, the AI depth processing (MiDaS) takes 5 to 15 minutes depending on the number of positions.',
      q3: 'Can I embed the tour on my existing website?', a3: 'Yes. All plans include an embed code (iframe) you can paste into any website, WordPress, Wix, or Squarespace. The Business plan also includes a custom domain.',
      q4: 'What happens if I cancel my subscription?', a4: 'Your tours remain accessible in Free mode (1 tour, 5 positions). If you had more tours, they are archived and can be reactivated when you resubscribe.',
      q5: 'Does it work on mobile and tablets?', a5: 'Yes. The tour works on any device with a modern browser. No app installation needed. It is especially optimized for the mobile scanning experience.',
      q6: 'Are my photos and data safe?', a6: 'Yes. Photos are stored on our own encrypted servers. We do not share them with third parties or use them to train models. We comply with the European GDPR.'
    },
    cta_final: {
      title: 'Your business deserves to be visited before they arrive',
      subtitle: 'Join the businesses already using Oxphyre to attract more customers.',
      cta: 'Create my free tour'
    },
    footer: {
      tagline: '3D virtual tours for local businesses.',
      product: 'Product', features: 'Features', pricing: 'Pricing',
      demo: 'Demo', changelog: "What's new",
      legal: 'Legal', privacy: 'Privacy', terms: 'Terms', cookies: 'Cookies',
      contact: 'Contact', about: 'About us', blog: 'Blog', support: 'Support',
      copyright: '© 2026 Oxphyre. All rights reserved.'
    }
  }
};

/** Recorre todos los elementos con data-i18n y sustituye su contenido. */
function applyLang(lang) {
  const t = translations[lang];
  if (!t) return;

  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.dataset.i18n; // ej: "hero.h1"
    const parts = key.split('.');
    let value = t;
    for (const part of parts) {
      value = value?.[part];
    }
    if (value !== undefined) {
      // placeholder es el atributo de inputs; el resto es textContent
      if (el.hasAttribute('placeholder')) {
        el.placeholder = value;
      } else {
        el.textContent = value;
      }
    }
  });

  // Actualiza el atributo lang del HTML para SEO y accesibilidad
  document.documentElement.lang = lang;

  // Actualiza el texto del botón de idioma activo
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.lang === lang);
  });
}

/** Inicializa el idioma: localStorage → prefers → 'es' */
function initLang() {
  const saved = localStorage.getItem('oxphyre-lang');
  const browser = navigator.language?.startsWith('en') ? 'en' : 'es';
  const lang = saved || browser;
  applyLang(lang);
}

// Exponemos la función para que main.js pueda llamarla al cambiar idioma
window.i18n = { applyLang, initLang, translations };
