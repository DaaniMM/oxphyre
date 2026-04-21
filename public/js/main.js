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


  // ── 8. THREE.JS — ESFERA EMBER INMERSIVA ─────────────────────────────────
  // Hero fullscreen: el canvas ocupa toda la ventana como fondo animado.
  // Solo se inicia en > 768px. En móvil el glow CSS ya da el efecto visual.
  // Inspirado en ember-orb de Lovable: esfera oscura con luz ámbar interior desde abajo.
  function initThreeJS() {
    // En móvil ahorramos batería y recursos — el CSS hace de fallback visual
    if (window.innerWidth <= 768) return;

    const canvas = document.getElementById('hero-canvas');
    if (!canvas || typeof THREE === 'undefined') return;

    const scene = new THREE.Scene();

    // Cámara con aspecto de toda la ventana (el canvas es fullscreen, no cuadrado)
    const camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.1, 100);
    camera.position.set(0, 0, 5.5);

    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0); // Transparente — el negro y el glow vienen del CSS

    function resize() {
      renderer.setSize(window.innerWidth, window.innerHeight);
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
    }
    resize();
    window.addEventListener('resize', resize);

    // Esfera principal: muy oscura con emisivo ámbar muy bajo.
    // El emisivo simula calor interno: la superficie parece iluminada desde dentro.
    const geo = new THREE.SphereGeometry(1.4, 64, 64);
    const mat = new THREE.MeshStandardMaterial({
      color: 0x080401,
      roughness: 0.92,
      metalness: 0.03,
      emissive: 0xFEB354,
      emissiveIntensity: 0.04  // Muy bajo: brasa interior, no color en superficie
    });
    const sphere = new THREE.Mesh(geo, mat);
    // Ligeramente derecha y abajo para composición más interesante con el texto centrado
    sphere.position.set(0.5, -0.2, 0);
    scene.add(sphere);

    // Wireframe sutil parented a sphere — rota y flota con ella sin cálculos extra
    const wireframe = new THREE.LineSegments(
      new THREE.WireframeGeometry(geo),
      new THREE.LineBasicMaterial({ color: 0xFEB354, transparent: true, opacity: 0.055 })
    );
    sphere.add(wireframe);

    // Luz puntual ámbar bajo la esfera: genera el highlight inferior, efecto ember-orb.
    // La posición por debajo de la geometría crea luz desde el suelo hacia arriba.
    const emberLight = new THREE.PointLight(0xFEB354, 6, 5);
    emberLight.position.set(0.5, -2.8, 0.5);
    scene.add(emberLight);

    // Halo más difuso y lejano: extiende el glow alrededor de la silueta inferior
    const haloLight = new THREE.PointLight(0xFF7A20, 2, 9);
    haloLight.position.set(0.5, -3.5, 1.5);
    scene.add(haloLight);

    // Ambient mínima: solo para que la silueta superior sea legible (no negro plano)
    scene.add(new THREE.AmbientLight(0x0A0604, 8));

    // ── Drag para rotar la esfera ──────────────────────────────────────────────
    // El usuario arrastra el ratón sobre el canvas → rota la esfera en X e Y.
    // Al soltar, esperamos 1.5s y reanudamos el auto-rotate suavemente.
    // Usamos deltas relativos para que el movimiento sea siempre proporcional al arrastre.
    let isDragging  = false;
    let prevX = 0, prevY = 0;
    let rotY = 0,  rotX = 0;
    let autoRotating = true;
    let resumeTimer  = null;

    canvas.addEventListener('mousedown', e => {
      isDragging   = true;
      autoRotating = false;
      prevX = e.clientX;
      prevY = e.clientY;
      if (resumeTimer) clearTimeout(resumeTimer);
    });

    window.addEventListener('mousemove', e => {
      if (!isDragging) return;
      rotY += (e.clientX - prevX) * 0.006;
      rotX += (e.clientY - prevY) * 0.004;
      rotX  = Math.max(-0.7, Math.min(0.7, rotX)); // Evita que quede cabeza abajo
      prevX = e.clientX;
      prevY = e.clientY;
    });

    window.addEventListener('mouseup', () => {
      if (!isDragging) return;
      isDragging  = false;
      resumeTimer = setTimeout(() => { autoRotating = true; }, 1500);
    });

    // Loop de animación: auto-rotate lento + flotación sinusoidal vertical.
    // Las luces siguen la posición Y de la esfera para que el glow sea coherente.
    let t = 0;
    function animate() {
      requestAnimationFrame(animate);
      t += 0.004;

      if (autoRotating) {
        rotY += 0.004;                       // Rotación automática lenta
        rotX  = Math.sin(t * 0.3) * 0.07;  // Oscilación sutil en X
      }

      sphere.rotation.y = rotY;
      sphere.rotation.x = rotX;

      // Flotación vertical atmosférica: la esfera respira suavemente
      const floatY = Math.sin(t * 0.5) * 0.07;
      sphere.position.y     = -0.2 + floatY;
      emberLight.position.y = -2.8 + floatY;
      haloLight.position.y  = -3.5 + floatY;

      renderer.render(scene, camera);
    }
    animate();
  }

  // Si Three.js ya cargó (defer puede adelantarse a DOMContentLoaded),
  // lo iniciamos directamente. Si no, esperamos al evento load del script.
  if (typeof THREE !== 'undefined') {
    initThreeJS();
  } else {
    const threeScript = document.querySelector('script[src*="three"]');
    if (threeScript) {
      threeScript.addEventListener('load', initThreeJS);
    }
  }

});
