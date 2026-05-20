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
      carousel: 'Negocios',
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
      drag_hint:  'Arrastra para explorar',
      h1:       'Tours virtuales 3D para negocios que quieren brillar.',
      subtitle: 'Convierte tu local en una experiencia 360° que tus clientes pueden visitar desde cualquier lugar. Sin cámaras especiales, sin técnicos, sin complicaciones.',
      cta_primary:   'Crear mi tour gratis →',
      cta_secondary: 'Ver un tour en vivo',
      pill1: '✓ Sin hardware especial',
      pill2: '✓ Listo en menos de 1 hora',
      pill3: '✓ Funciona en cualquier móvil'
    },
    carousel: {
      title:    'Tu negocio, en primera persona',
      subtitle: 'Descubre cómo Oxphyre puede transformar la forma en que los clientes conocen tu negocio.',
      tour_hint: 'Click para ver el tour 360°',
      c1_title: 'Restaurante',  c1_text: 'Tus platos son increíbles, pero tu ambiente es lo que te diferencia. Es hora de que lo vean.',
      c2_title: 'Gimnasio',     c2_text: 'Muchos no se apuntan por miedo a no saber qué se van a encontrar. Abre tus puertas y rompe esa barrera.',
      c3_title: 'Peluquería',   c3_text: 'En imágenes muestras el antes/después. Con Oxphyre muestras el dónde, el lugar donde ocurre la magia.',
      c4_title: 'Hotel',        c4_text: 'Nadie reserva una habitación sin verla. Una experiencia inmersiva para una reserva premium.',
      c5_title: 'Tienda',       c5_text: 'Tu escaparate es tu mejor vendedor, pero solo para los que pasan por delante. Con Oxphyre, tu escaparate es el mundo entero.',
      c6_title: 'Inmobiliaria', c6_text: 'Capta la esencia de cada propiedad ofreciendo a los vendedores la tecnología de marketing más avanzada del mercado.',
      c7_title: 'Clínica',      c7_text: 'Permite a tus pacientes recorrer tus instalaciones. Que conozcan tu consulta antes de entrar cambia todo.',
      c8_title: 'Coworking',    c8_text: 'El espacio vende solo — si la gente lo ve. Deja que vean dónde va a crecer su próximo proyecto.'
    },
    steps: {
      title:    'Cómo lo creas',
      subtitle: 'Tu tour virtual en tres pasos. Sin curva de aprendizaje.',
      hook:     'Sin cursos. Sin técnicos. Sin complicaciones.',
      s1_num: '01', s1_title: 'Fotografías tu local',
      s1_desc: 'Fotografía cada posición en 4 direcciones. Solo necesitas tu móvil.',
      s1_detail: '4 fotos por posición · Norte, Sur, Este, Oeste',
      s2_num: '02', s2_title: 'Construyes el tour',
      s2_desc: 'Sube las fotos a Oxphyre y conecta las posiciones en nuestro editor visual drag & drop.',
      s2_detail: 'Editor drag & drop · Sin conocimientos técnicos',
      s3_num: '03', s3_title: 'Lo compartes',
      s3_desc: 'Descarga el QR y ponlo donde quieras. Tus clientes escanean y exploran tu negocio en 3D.',
      s3_detail: 'QR descargable · Código embed para tu web'
    },
    demo: {
      title:      'Cómo lo viven tus clientes',
      subtitle:   'Descubre cómo un negocio real se convierte en un tour virtual 3D navegable. Sin registro.',
      embed_text: '¿Tienes web propia? Embebe el tour directamente con un snippet de código.',
      cta: 'Ver tour en vivo'
    },
    features: {
      title:    'La tecnología detrás de cada tour',
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
      f6_desc: 'Funciona en iOS y Android sin instalar nada. Solo un navegador moderno.',
      f7_title: 'Profundidad real con IA',
      f7_desc: 'Nuestra IA convierte cada imagen en un espacio con profundidad 3D real. Sin cámaras 360°, sin equipos especiales, sin coste extra.'
    },
    pricing: {
      title:    'Empieza gratis. Crece cuando quieras.',
      subtitle: 'Sin comisiones ocultas. Cancela cuando quieras.',
      toggle_monthly: 'Mensual', toggle_annual: 'Anual',
      badge_save: 'Ahorra 20%',
      free_name: 'Free',     free_price_monthly: '0€',  free_price_annual: '0€',
      free_desc: 'Para probar Oxphyre sin compromiso.',
      free_note: 'Sin tarjeta. Sin compromiso.',
      pro_name: 'Pro',       pro_price_monthly: '19€',  pro_price_annual: '15€',
      pro_desc: 'Para negocios que quieren destacar.',
      pro_note: 'Actualiza o cancela en cualquier momento.',
      pro_annual_total: '182€/año · Ahorras 46€',
      biz_name: 'Business',  biz_price_monthly: '49€',  biz_price_annual: '39€',
      biz_desc: 'Para empresas con necesidades avanzadas.',
      biz_note: 'Acceso completo. Sin límites.',
      biz_annual_total: '470€/año · Ahorras 118€',
      per_month: '/mes',
      cta_free: 'Empezar gratis', cta_pro: 'Empezar con Pro', cta_biz: 'Empezar con Business',
      cta_biz_contact: 'Contactar',
      popular: 'Más popular',
      free_f1: '1 negocio · 1 tour activo', free_f2: 'Hasta 3 posiciones/zonas',
      free_f3: 'QR básico con branding Oxphyre', free_f4: 'Flechas de navegación básicas',
      free_f5: 'Mapa de ubicación del negocio',  free_f6: 'Marca de agua Oxphyre en el visor',
      pro_f1: 'Hasta 5 negocios · Tours ilimitados', pro_f2: 'Hasta 20 posiciones por tour',
      pro_f3: 'Sin marca de agua',  pro_f4: 'Embed/iframe en tu web',  pro_f5: 'Analíticas básicas · QR profesional',
      biz_f1: 'Negocios y posiciones ilimitadas', biz_f2: 'Dominio personalizado (próximamente)',
      biz_f3: 'Analíticas avanzadas (próximamente)', biz_f4: 'Soporte prioritario + onboarding', biz_f5: 'API access (próximamente)'
    },
    faq: {
      title:    'Preguntas frecuentes',
      subtitle: 'Todo lo que necesitas saber antes de empezar.',
      q1: '¿Necesito equipo especial para hacer el tour?',
      a1: 'No. Solo necesitas un smartphone con cámara decente. Nuestro sistema procesa las fotos automáticamente para generar la experiencia inmersiva. Nada de cámaras 360 ni software de edición.',
      q2: '¿Cuánto tiempo tarda en estar listo el tour?',
      a2: 'Con cualquier plan, el tour está listo en pocos minutos. La subida y el procesado automático de fotos es rápido y no requiere conocimientos técnicos.',
      q3: '¿Puedo insertar el tour en mi web existente?',
      a3: 'Sí, en los planes Pro y Business. Incluyen un código embed (iframe) que puedes pegar en cualquier web, WordPress, Wix o Squarespace. El plan Free solo incluye enlace público.',
      q4: '¿Qué pasa si cancelo mi suscripción?',
      a4: 'Tus tours siguen siendo accesibles en modo Free (1 tour, 3 posiciones). Si tenías más tours o posiciones, quedan archivados y los puedes reactivar cuando vuelvas a suscribirte.',
      q5: '¿Funciona en móviles y tablets?',
      a5: 'Sí. El tour funciona en cualquier dispositivo con un navegador moderno. No hay que instalar ninguna app. Está optimizado especialmente para la experiencia desde móvil al escanear el QR.',
      q6: '¿Mis fotos y datos están seguros?',
      a6: 'Sí. Las fotos se almacenan en servidores propios con cifrado. No las compartimos con terceros ni las usamos para entrenar modelos. Cumplimos con el RGPD europeo.',
      q7: '¿Puedo probar Oxphyre antes de pagar?',
      a7: 'Sí. El plan Free es gratuito para siempre, sin tarjeta de crédito. Crea tu primer tour, compártelo y decide si quieres crecer con un plan de pago.'
    },
    cta_final: {
      title:    'Tu negocio merece ser descubierto.',
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
    },
    auth: {
      login_eyebrow:         'OXPHYRE · ACCESO',
      login_h2_prefix:       'Bienvenido ',
      login_h2_em:           'de vuelta.',
      login_brand_sub:       'Continúa construyendo tu espacio.',
      login_h1:              'Bienvenido de vuelta',
      login_form_sub:        'Accede a tu cuenta Oxphyre.',
      login_submit:          'Iniciar sesión',
      login_toggle_text:     '¿No tienes cuenta?',
      login_toggle_link:     'Regístrate gratis',
      register_eyebrow:      'OXPHYRE · NUEVA CUENTA',
      register_h2_prefix:    'Tu negocio, ',
      register_h2_em:        'en 360°.',
      register_brand_sub:    'Gratis para empezar. Sin tarjeta.',
      register_h1:           'Crea tu cuenta',
      register_form_sub:     'Empieza gratis. Sin tarjeta de crédito.',
      register_submit:       'Crear cuenta →',
      register_toggle_text:  '¿Ya tienes cuenta?',
      register_toggle_link:  'Inicia sesión',
      field_name:            'Tu nombre',
      field_email:           'Email',
      field_password:        'Contraseña',
      field_confirm:         'Confirmar contraseña',
      remember_me:           'Recuérdame',
      forgot:                '¿Olvidaste tu contraseña?',
      social_google:         'Google',
      social_apple:          'Apple',
      divider:               'o con email'
    }
  },
  en: {
    nav: {
      carousel: 'Businesses',
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
      drag_hint:  'Drag to explore',
      h1:       '3D virtual tours for businesses that want to shine.',
      subtitle: 'Turn your space into a 360° experience your customers can visit from anywhere. No special cameras, no technicians, no hassle.',
      cta_primary:   'Create my free tour →',
      cta_secondary: 'See a live tour',
      pill1: '✓ No special hardware',
      pill2: '✓ Ready in under 1 hour',
      pill3: '✓ Works on any phone'
    },
    carousel: {
      title:    'Your business, in first person',
      subtitle: 'Discover how Oxphyre can transform the way customers experience your business.',
      tour_hint: 'Click to explore the 360° tour',
      c1_title: 'Restaurant',  c1_text: 'Your food is incredible, but your atmosphere is what sets you apart. It\'s time people saw it.',
      c2_title: 'Gym',         c2_text: 'Many don\'t join because they fear the unknown. Open your doors and break that barrier.',
      c3_title: 'Hair Salon',  c3_text: 'Photos show the before/after. Oxphyre shows the where — the place where the magic happens.',
      c4_title: 'Hotel',       c4_text: 'Nobody books a room without seeing it. An immersive experience for a premium booking.',
      c5_title: 'Shop',        c5_text: 'Your window is your best salesperson, but only for those walking by. With Oxphyre, your window is the whole world.',
      c6_title: 'Real Estate', c6_text: 'Capture the essence of each property, offering sellers the most advanced marketing technology on the market.',
      c7_title: 'Clinic',      c7_text: 'Let your patients tour your facilities. Knowing your office before they arrive changes everything.',
      c8_title: 'Coworking',   c8_text: 'The space sells itself — if people can see it. Let them see where their next project will grow.'
    },
    steps: {
      title:    'How you create it',
      subtitle: 'Your virtual tour in three steps. No learning curve.',
      hook:     'No courses. No technicians. No complications.',
      s1_num: '01', s1_title: 'Photograph your space',
      s1_desc: 'Photograph each position in 4 directions. All you need is your phone.',
      s1_detail: '4 photos per position · N, S, E, W',
      s2_num: '02', s2_title: 'Build the tour',
      s2_desc: 'Upload photos to Oxphyre and connect positions in our drag & drop visual editor.',
      s2_detail: 'Drag & drop editor · No technical knowledge',
      s3_num: '03', s3_title: 'Share it',
      s3_desc: 'Download the QR and place it anywhere. Customers scan and explore your business in 3D.',
      s3_detail: 'Downloadable QR · Embed code for your site'
    },
    demo: {
      title:      'How your customers experience it',
      subtitle:   'Discover how a real business becomes a navigable 3D virtual tour. No sign-up required.',
      embed_text: 'Have your own website? Embed the tour directly with a code snippet.',
      cta: 'View live tour'
    },
    features: {
      title:    'The technology behind every tour',
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
      f6_desc: 'Works on iOS and Android without installing anything. Just a modern browser.',
      f7_title: 'Real AI depth',
      f7_desc: 'Our AI turns every photo into a real 3D space. No 360° cameras, no special equipment, no extra cost.'
    },
    pricing: {
      title:    'Start free. Grow whenever you want.',
      subtitle: 'No hidden fees. Cancel anytime.',
      toggle_monthly: 'Monthly', toggle_annual: 'Annual',
      badge_save: 'Save 20%',
      free_name: 'Free',     free_price_monthly: '€0',  free_price_annual: '€0',
      free_desc: 'Try Oxphyre without commitment.',
      free_note: 'No card. No commitment.',
      pro_name: 'Pro',       pro_price_monthly: '€19',  pro_price_annual: '€15',
      pro_desc: 'For businesses that want to stand out.',
      pro_note: 'Upgrade or cancel at any time.',
      pro_annual_total: '€182/year · Save €46',
      biz_name: 'Business',  biz_price_monthly: '€49',  biz_price_annual: '€39',
      biz_desc: 'For businesses with advanced needs.',
      biz_note: 'Full access. No limits.',
      biz_annual_total: '€470/year · Save €118',
      per_month: '/mo',
      cta_free: 'Start for free', cta_pro: 'Start with Pro', cta_biz: 'Start with Business',
      cta_biz_contact: 'Contact us',
      popular: 'Most popular',
      free_f1: '1 business · 1 active tour', free_f2: 'Up to 3 positions/zones',
      free_f3: 'Basic QR with Oxphyre branding', free_f4: 'Basic navigation arrows',
      free_f5: 'Business location map',           free_f6: 'Oxphyre watermark on viewer',
      pro_f1: 'Up to 5 businesses · Unlimited tours', pro_f2: 'Up to 20 positions per tour',
      pro_f3: 'No watermark', pro_f4: 'Embed/iframe on your site', pro_f5: 'Basic analytics · Pro QR',
      biz_f1: 'Unlimited businesses and positions', biz_f2: 'Custom domain (coming soon)',
      biz_f3: 'Advanced analytics (coming soon)', biz_f4: 'Priority support + onboarding', biz_f5: 'API access (coming soon)'
    },
    faq: {
      title:    'Frequently asked questions',
      subtitle: 'Everything you need to know before starting.',
      q1: 'Do I need special equipment to create a tour?',
      a1: 'No. You only need a smartphone with a decent camera. Our system processes the photos automatically to create the immersive experience. No 360° cameras or editing software needed.',
      q2: 'How long does it take for the tour to be ready?',
      a2: 'With any plan, the tour is ready in just a few minutes. The automatic photo upload and processing is fast and requires no technical knowledge.',
      q3: 'Can I embed the tour on my existing website?',
      a3: 'Yes, in Pro and Business plans. They include an embed code (iframe) you can paste into any website, WordPress, Wix, or Squarespace. The Free plan only includes a public link.',
      q4: 'What happens if I cancel my subscription?',
      a4: 'Your tours remain accessible in Free mode (1 tour, 3 positions). Additional tours and positions are archived and can be reactivated when you resubscribe.',
      q5: 'Does it work on mobile and tablets?',
      a5: 'Yes. The tour works on any device with a modern browser. No app installation needed. It is especially optimized for the mobile scanning experience.',
      q6: 'Are my photos and data safe?',
      a6: 'Yes. Photos are stored on our own encrypted servers. We do not share them with third parties or use them to train models. We comply with the European GDPR.',
      q7: 'Can I try Oxphyre before paying?',
      a7: 'Yes. The Free plan is free forever, no credit card required. Create your first tour, share it, and decide if you want to grow with a paid plan.'
    },
    cta_final: {
      title:    'Your business deserves to be discovered.',
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
    },
    auth: {
      login_eyebrow:         'OXPHYRE · SIGN IN',
      login_h2_prefix:       'Welcome ',
      login_h2_em:           'back.',
      login_brand_sub:       'Keep building your space.',
      login_h1:              'Welcome back',
      login_form_sub:        'Sign in to your Oxphyre account.',
      login_submit:          'Sign in',
      login_toggle_text:     "Don't have an account?",
      login_toggle_link:     'Register for free',
      register_eyebrow:      'OXPHYRE · NEW ACCOUNT',
      register_h2_prefix:    'Your business, ',
      register_h2_em:        'in 360°.',
      register_brand_sub:    'Free to start. No credit card.',
      register_h1:           'Create your account',
      register_form_sub:     'Start for free. No credit card required.',
      register_submit:       'Create account →',
      register_toggle_text:  'Already have an account?',
      register_toggle_link:  'Sign in',
      field_name:            'Your name',
      field_email:           'Email',
      field_password:        'Password',
      field_confirm:         'Confirm password',
      remember_me:           'Remember me',
      forgot:                'Forgot your password?',
      social_google:         'Google',
      social_apple:          'Apple',
      divider:               'or with email'
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
