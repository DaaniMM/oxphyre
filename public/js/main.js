/**
 * main.js — Lógica de interacción de la landing page.
 *
 * Módulos:
 *  1. Tema día/noche
 *  2. Idioma (delega en i18n.js)
 *  3. Nav glassmorphism al hacer scroll
 *  4. Menú móvil
 *  5. IntersectionObserver para animaciones al entrar en viewport
 *  6. Acordeón FAQ
 *  7. Toggle de precios mensual/anual
 *  8. Three.js — esfera flotante en el hero
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── 1. TEMA DÍA/NOCHE ─────────────────────────────────────────────────────
  // Primera visita: respetamos prefers-color-scheme del sistema operativo.
  // Visitas siguientes: aplicamos la preferencia guardada en localStorage.
  // La clase 'light' en body activa las variables CSS del modo claro.
  const themeBtn = document.getElementById('theme-toggle');
  const saved = localStorage.getItem('oxphyre-theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initTheme = saved ?? (prefersDark ? 'dark' : 'light');

  function setTheme(theme) {
    document.body.classList.toggle('light', theme === 'light');
    localStorage.setItem('oxphyre-theme', theme);
    // Actualiza el aria-label para lectores de pantalla
    if (themeBtn) {
      themeBtn.setAttribute('aria-label', theme === 'light' ? 'Activar modo oscuro' : 'Activar modo claro');
      themeBtn.dataset.theme = theme;
    }
  }

  setTheme(initTheme);

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const current = localStorage.getItem('oxphyre-theme') || 'dark';
      setTheme(current === 'dark' ? 'light' : 'dark');
    });
  }


  // ── 2. IDIOMA ─────────────────────────────────────────────────────────────
  // i18n.js se carga antes que main.js (orden de scripts en home.php).
  // initLang() aplica el idioma guardado o detecta el del navegador.
  if (window.i18n) {
    window.i18n.initLang();
  }

  // Botones de cambio de idioma (nav y footer)
  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const lang = btn.dataset.lang;
      localStorage.setItem('oxphyre-lang', lang);
      window.i18n?.applyLang(lang);
    });
  });


  // ── 3. NAV GLASSMORPHISM AL SCROLL ────────────────────────────────────────
  // Sin scroll: nav transparente integrado con el hero.
  // Con scroll: fondo con glassmorphism para que los links sean legibles
  // sobre cualquier contenido de la página. Usamos IntersectionObserver
  // en lugar de un listener 'scroll' para evitar thrashing del layout.
  const nav = document.getElementById('nav');
  if (nav) {
    const sentinel = document.createElement('div');
    sentinel.style.cssText = 'position:absolute;top:80px;height:1px;width:1px;pointer-events:none;';
    document.body.prepend(sentinel);

    new IntersectionObserver(([entry]) => {
      nav.classList.toggle('nav-scrolled', !entry.isIntersecting);
    }, { threshold: 0 }).observe(sentinel);
  }


  // ── 4. MENÚ MÓVIL ─────────────────────────────────────────────────────────
  // En móvil el nav colapsa. El botón hamburguesa abre/cierra el menú.
  // Cerramos también al hacer click en cualquier enlace del menú.
  const menuBtn = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', open);
    });

    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        menuBtn.setAttribute('aria-expanded', 'false');
      });
    });
  }


  // ── 5. ANIMACIONES AL SCROLL (IntersectionObserver) ───────────────────────
  // Los elementos con clase 'animate-on-scroll' empiezan invisibles (opacity:0, translateY:24px).
  // Cuando entran en el viewport, se añade 'is-visible' que activa la transición CSS.
  // Usamos IntersectionObserver en lugar de escuchar el evento scroll
  // porque es asíncrono, no bloquea el hilo principal y es más eficiente.
  const animObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        // Una vez visible, dejamos de observarlo para no procesar eventos innecesarios
        animObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.animate-on-scroll').forEach((el, i) => {
    // Delay escalonado para elementos hermanos (ej: 3 cards aparecen en cascada)
    el.style.transitionDelay = `${i * 0.07}s`;
    animObserver.observe(el);
  });


  // ── 6. ACORDEÓN FAQ ───────────────────────────────────────────────────────
  // Cada item FAQ tiene un botón .faq-question y un div .faq-answer.
  // Al hacer click abrimos el activo y cerramos el anterior (solo uno abierto).
  // Animamos con max-height (de 0 a el scrollHeight real) + opacity.
  // Evitamos usar display:none porque no se puede animar.
  document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');
    if (!question || !answer) return;

    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');

      // Cerramos todos antes de abrir el seleccionado
      document.querySelectorAll('.faq-item.open').forEach(open => {
        open.classList.remove('open');
        open.querySelector('.faq-answer').style.maxHeight = '0';
      });

      if (!isOpen) {
        item.classList.add('open');
        // scrollHeight da la altura real del contenido sin overflow
        answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });


  // ── 7. TOGGLE PRECIOS MENSUAL / ANUAL ─────────────────────────────────────
  // El botón toggle cambia la clase 'annual' en la sección de precios.
  // Los precios mensuales y anuales están en data-monthly y data-annual
  // para no depender de llamadas al servidor al cambiar el toggle.
  const billingToggle = document.getElementById('billing-toggle');
  const pricingSection = document.getElementById('precios');

  if (billingToggle && pricingSection) {
    billingToggle.addEventListener('click', () => {
      const isAnnual = pricingSection.classList.toggle('annual');
      billingToggle.setAttribute('aria-checked', isAnnual);

      // Actualizamos los precios visibles con los valores del data attribute
      pricingSection.querySelectorAll('[data-monthly]').forEach(el => {
        el.textContent = isAnnual ? el.dataset.annual : el.dataset.monthly;
      });
    });
  }


  // ── 8. THREE.JS — ESFERA FLOTANTE ─────────────────────────────────────────
  // Three.js se carga desde CDN con defer y está disponible como global THREE.
  // Usamos requestAnimationFrame para la animación; no bloqueamos el hilo principal.
  // El canvas es transparente: el fondo lo gestiona el CSS (radial-gradient cálido).
  function initThreeJS() {
    const canvas = document.getElementById('hero-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    const scene = new THREE.Scene();

    // Cámara perspectiva con relación de aspecto 1:1 (el canvas es cuadrado)
    const camera = new THREE.PerspectiveCamera(55, 1, 0.1, 100);
    camera.position.set(0, 0, 3.2);

    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0); // Fondo transparente

    function resize() {
      const size = Math.min(canvas.parentElement.clientWidth, 520);
      renderer.setSize(size, size);
    }
    resize();
    window.addEventListener('resize', resize);

    // Esfera interior: material oscuro cálido con emisivo sutil
    const geo = new THREE.SphereGeometry(1, 48, 48);
    const mat = new THREE.MeshStandardMaterial({
      color: 0x1A1710,
      roughness: 0.85,
      metalness: 0.15,
      emissive: 0x3D2A08,
      emissiveIntensity: 0.4
    });
    const sphere = new THREE.Mesh(geo, mat);
    scene.add(sphere);

    // Wireframe dorado superpuesto — evoca la estructura de un tour 360
    const wGeo = new THREE.WireframeGeometry(geo);
    const wMat = new THREE.LineBasicMaterial({ color: 0xFEB354, transparent: true, opacity: 0.25 });
    const wireframe = new THREE.LineSegments(wGeo, wMat);
    sphere.add(wireframe);

    // Anillo ecuatorial para dar sensación de profundidad
    const ringGeo = new THREE.TorusGeometry(1.3, 0.006, 8, 80);
    const ringMat = new THREE.MeshBasicMaterial({ color: 0xFEB354, transparent: true, opacity: 0.18 });
    const ring = new THREE.Mesh(ringGeo, ringMat);
    ring.rotation.x = Math.PI / 2.5;
    scene.add(ring);

    // Luz ambiental cálida: ilumina la escena uniformemente
    scene.add(new THREE.AmbientLight(0xFEB354, 0.6));

    // Punto de luz frontal-superior: crea el highlight en la esfera
    const pointLight = new THREE.PointLight(0xFFD580, 2.5, 12);
    pointLight.position.set(2.5, 2, 2.5);
    scene.add(pointLight);

    // Luz trasera fría para contraste y profundidad
    const backLight = new THREE.PointLight(0x4444AA, 0.8, 10);
    backLight.position.set(-2, -1, -2);
    scene.add(backLight);

    // Loop de animación: rotación suave + oscilación vertical sinusoidal
    // Solo transform, no modificamos posición con top/left (sin reflow)
    let t = 0;
    function animate() {
      requestAnimationFrame(animate);
      t += 0.004;
      sphere.rotation.y = t;
      sphere.rotation.x = Math.sin(t * 0.35) * 0.18;
      ring.rotation.z = t * 0.3;
      // Movimiento vertical sutil de toda la escena (oscilación atmosférica)
      sphere.position.y = Math.sin(t * 0.6) * 0.06;
      ring.position.y = sphere.position.y;
      renderer.render(scene, camera);
    }
    animate();
  }

  // Si Three.js ya cargó (defer puede ejecutarse antes), lo iniciamos directamente.
  // Si no, esperamos al evento load del script.
  if (typeof THREE !== 'undefined') {
    initThreeJS();
  } else {
    const threeScript = document.querySelector('script[src*="three"]');
    if (threeScript) {
      threeScript.addEventListener('load', initThreeJS);
    }
  }

});
