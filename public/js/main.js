/**
 * main.js — Lógica completa de la landing page Oxphyre.
 *
 * Módulos:
 *  1.  Cursor personalizado
 *  2.  Loader (foco de luz + letras OXPHYRE)
 *  3.  Tema día/noche
 *  4.  Idioma (delega en i18n.js)
 *  5.  Nav glassmorphism al scroll
 *  6.  Menú móvil
 *  7.  Hero Three.js — Phase 1 (dentro de la esfera)
 *  8.  Hero Three.js — Phase 2 (fuera de la esfera)
 *  9.  Scroll hint
 *  10. Carrusel negocios
 *  11. IntersectionObserver animaciones de scroll
 *  12. Cursor spotlight en Características
 *  13. Acordeón FAQ
 *  14. Toggle precios mensual/anual
 *  15. Three.js — esfera decorativa en CTA Final
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── 1. CURSOR PERSONALIZADO ──────────────────────────────────────────────
  // Solo en dispositivos con puntero fino (desktop). En táctil el CSS ya lo oculta.
  const cursorRing = document.getElementById('cursor-ring');

  if (cursorRing && window.matchMedia('(pointer: fine)').matches) {
    let cursorX = -100, cursorY = -100;

    // Usamos transform en lugar de top/left para evitar reflow del layout
    window.addEventListener('mousemove', e => {
      cursorX = e.clientX;
      cursorY = e.clientY;
      cursorRing.style.transform = `translate(${cursorX}px, ${cursorY}px) translate(-50%, -50%)`;
    });

    // Agrandar el cursor al hover sobre interactivos
    document.querySelectorAll('a, button, [role="button"], input, label, .carousel-card').forEach(el => {
      el.addEventListener('mouseenter', () => cursorRing.classList.add('cursor-hover'));
      el.addEventListener('mouseleave', () => cursorRing.classList.remove('cursor-hover'));
    });
  }


  // ── 2. LOADER ────────────────────────────────────────────────────────────
  // Timing:  0.0s inicio · 0.5s empieza beam · 1.5s letras · 3.0s completo · 4.0s desaparece
  const loader = document.getElementById('loader');
  const beam   = document.getElementById('loader-beam');
  const letters = document.querySelectorAll('.loader-letter');

  function runLoader() {
    if (!loader || !beam) {
      document.body.classList.add('phase-2');
      return;
    }

    // El beam barre de izquierda a derecha en 2.5s
    setTimeout(() => {
      beam.style.transition = 'transform 2.5s linear';
      beam.style.transform  = `translateX(${window.innerWidth + 200}px)`;
    }, 500);

    // Las letras se revelan escalonadas entre t=1.5s y t=3.0s
    letters.forEach((letter, i) => {
      setTimeout(() => {
        letter.classList.add('revealed');
      }, 1500 + i * 200);
    });

    // A t=3.5s las letras explotan hacia afuera (scale + opacity)
    setTimeout(() => {
      letters.forEach((letter, i) => {
        setTimeout(() => {
          letter.classList.add('explode');
        }, i * 60);
      });
    }, 3500);

    // A t=4.0s el loader se desvanece y se revela la escena
    setTimeout(() => {
      loader.classList.add('hidden');
      // Iniciamos en Phase 2 directamente (la Phase 1 three.js necesita pantalla limpia)
      startHeroThreeJS();
    }, 4000);
  }

  runLoader();


  // ── 3. TEMA DÍA/NOCHE ───────────────────────────────────────────────────
  const themeBtn = document.getElementById('theme-toggle');
  const saved    = localStorage.getItem('oxphyre-theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initTheme = saved ?? (prefersDark ? 'dark' : 'light');

  function setTheme(theme) {
    document.body.classList.toggle('light', theme === 'light');
    localStorage.setItem('oxphyre-theme', theme);
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


  // ── 4. IDIOMA ────────────────────────────────────────────────────────────
  if (window.i18n) {
    window.i18n.initLang();
  }

  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const lang = btn.dataset.lang;
      localStorage.setItem('oxphyre-lang', lang);
      window.i18n?.applyLang(lang);
    });
  });


  // ── 5. NAV GLASSMORPHISM AL SCROLL ──────────────────────────────────────
  const nav = document.getElementById('nav');
  if (nav) {
    const sentinel = document.createElement('div');
    sentinel.style.cssText = 'position:absolute;top:80px;height:1px;width:1px;pointer-events:none;';
    document.body.prepend(sentinel);

    new IntersectionObserver(([entry]) => {
      nav.classList.toggle('nav-scrolled', !entry.isIntersecting);
    }, { threshold: 0 }).observe(sentinel);
  }


  // ── 6. MENÚ MÓVIL ────────────────────────────────────────────────────────
  const menuBtn    = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const menuClose  = document.getElementById('mobile-menu-close');

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', () => {
      const open = mobileMenu.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', open);
    });

    if (menuClose) {
      menuClose.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        menuBtn.setAttribute('aria-expanded', 'false');
      });
    }

    mobileMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        menuBtn.setAttribute('aria-expanded', 'false');
      });
    });
  }


  // ── 7 & 8. HERO THREE.JS — DOS FASES ────────────────────────────────────
  // Phase 1: cámara dentro de una esfera invertida (BackSide) con partículas ámbar.
  //          El usuario arrastra para girar. Frases aparecen según el ángulo de vista.
  // Phase 2: al hacer scroll la cámara sale hacia atrás (eje Z). Cuando cameraZ > 8
  //          se activa body.phase-2: aparece el nav y el contenido del hero.

  function startHeroThreeJS() {
    // En móvil saltamos directamente a Phase 2 — el CSS gestiona la presentación visual
    if (window.innerWidth <= 768) {
      document.body.classList.add('phase-2');
      return;
    }

    const canvas = document.getElementById('hero-canvas');
    if (!canvas || typeof THREE === 'undefined') {
      document.body.classList.add('phase-2');
      return;
    }

    // ── Setup renderer ──
    const scene    = new THREE.Scene();
    const camera   = new THREE.PerspectiveCamera(80, window.innerWidth / window.innerHeight, 0.01, 100);
    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0);

    let cameraZ = 0.01; // Empieza dentro de la esfera
    camera.position.set(0, 0, cameraZ);
    camera.lookAt(0, 0, 0);

    function resize() {
      renderer.setSize(window.innerWidth, window.innerHeight);
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
    }
    resize();
    window.addEventListener('resize', resize);

    // ── Esfera BackSide (sala negra interior) ──
    // BackSide hace que la superficie interior sea visible desde dentro.
    const innerGeo = new THREE.SphereGeometry(8, 32, 32);
    const innerMat = new THREE.MeshBasicMaterial({ color: 0x000000, side: THREE.BackSide });
    const innerSphere = new THREE.Mesh(innerGeo, innerMat);
    scene.add(innerSphere);

    // ── Partículas ámbar ──
    // 300 puntos distribuidos aleatoriamente dentro del volumen de la esfera.
    const PARTICLE_COUNT = 300;
    const pPositions = new Float32Array(PARTICLE_COUNT * 3);
    const pOffsets   = new Float32Array(PARTICLE_COUNT); // fase de oscilación por partícula

    for (let i = 0; i < PARTICLE_COUNT; i++) {
      // Distribución uniforme dentro de la esfera (coordenadas esféricas aleatorias)
      const r     = 5 + Math.random() * 2.5;
      const theta = Math.random() * Math.PI * 2;
      const phi   = Math.acos(2 * Math.random() - 1);
      pPositions[i * 3]     = r * Math.sin(phi) * Math.cos(theta);
      pPositions[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
      pPositions[i * 3 + 2] = r * Math.cos(phi);
      pOffsets[i] = Math.random() * Math.PI * 2;
    }

    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.Float32BufferAttribute(pPositions.slice(), 3));

    const pMat = new THREE.PointsMaterial({
      color: 0xFEB354,
      size: 0.06,
      transparent: true,
      opacity: 0.7,
      sizeAttenuation: true
    });
    const particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    // ── Drag para rotar la vista (esférico) ──
    // El usuario arrastra y la cámara orbita alrededor del centro de la esfera.
    let spherical = { theta: 0, phi: Math.PI / 2 };
    let isDragging = false;
    let prevX = 0, prevY = 0;
    let targetTheta = 0, targetPhi = Math.PI / 2;

    canvas.addEventListener('mousedown', e => {
      isDragging = true;
      prevX = e.clientX;
      prevY = e.clientY;
    });

    window.addEventListener('mousemove', e => {
      if (!isDragging) return;
      targetTheta -= (e.clientX - prevX) * 0.005;
      targetPhi   -= (e.clientY - prevY) * 0.003;
      targetPhi    = Math.max(0.3, Math.min(Math.PI - 0.3, targetPhi));
      prevX = e.clientX;
      prevY = e.clientY;
    });

    window.addEventListener('mouseup', () => { isDragging = false; });

    // Touch events para móvil (aunque en móvil el hero tiene fallback CSS)
    canvas.addEventListener('touchstart', e => {
      isDragging = true;
      prevX = e.touches[0].clientX;
      prevY = e.touches[0].clientY;
    }, { passive: true });

    window.addEventListener('touchmove', e => {
      if (!isDragging) return;
      targetTheta -= (e.touches[0].clientX - prevX) * 0.005;
      targetPhi   -= (e.touches[0].clientY - prevY) * 0.003;
      targetPhi    = Math.max(0.3, Math.min(Math.PI - 0.3, targetPhi));
      prevX = e.touches[0].clientX;
      prevY = e.touches[0].clientY;
    }, { passive: true });

    window.addEventListener('touchend', () => { isDragging = false; });

    // ── Frases según ángulo de rotación ──
    const phrases = document.querySelectorAll('.phrase');
    const phraseAngles = [0, 90, 180, 270, 350]; // en grados

    function updatePhrases() {
      const thetaDeg = ((targetTheta * 180 / Math.PI) % 360 + 360) % 360;
      phrases.forEach((phrase, i) => {
        const angleDiff = Math.abs(thetaDeg - phraseAngles[i]);
        const wrapped   = Math.min(angleDiff, 360 - angleDiff);
        phrase.classList.toggle('active', wrapped < 35);
      });
    }

    // ── Scroll: cámara sale de la esfera ──
    let scrollAccum = 0;
    let isPhase2    = false;
    let targetCamZ  = 0.01;
    let lerpingOut  = false;

    window.addEventListener('wheel', e => {
      if (isPhase2) return;
      scrollAccum += e.deltaY * 0.01;
      scrollAccum  = Math.max(0, scrollAccum);

      if (scrollAccum > 3) {
        targetCamZ = 12;
        lerpingOut = true;
      }
    }, { passive: true });

    // ── Loop de animación ──
    let t = 0;
    const pPos = pGeo.attributes.position;

    function animate() {
      requestAnimationFrame(animate);
      t += 0.008;

      // Auto-rotate lento si no está arrastrando
      if (!isDragging) {
        targetTheta += 0.003;
      }

      // Lerp suave de la rotación esférica
      spherical.theta += (targetTheta - spherical.theta) * 0.08;
      spherical.phi   += (targetPhi - spherical.phi) * 0.08;

      if (!isPhase2) {
        // Phase 1: cámara en el centro, mirando hacia el perímetro
        const r = cameraZ;
        camera.position.set(
          r * Math.sin(spherical.phi) * Math.cos(spherical.theta),
          r * Math.cos(spherical.phi),
          r * Math.sin(spherical.phi) * Math.sin(spherical.theta)
        );
        camera.lookAt(0, 0, 0);

        // Oscilación de partículas
        for (let i = 0; i < PARTICLE_COUNT; i++) {
          const base = pPositions;
          const ox = base[i * 3];
          const oy = base[i * 3 + 1];
          const oz = base[i * 3 + 2];
          const wave = Math.sin(t + pOffsets[i]) * 0.06;
          pPos.setXYZ(i, ox + wave, oy + wave * 0.5, oz + wave);
        }
        pPos.needsUpdate = true;

        updatePhrases();

        // Lerp de la cámara hacia afuera al hacer scroll
        if (lerpingOut) {
          cameraZ += (targetCamZ - cameraZ) * 0.04;

          if (cameraZ > 8 && !isPhase2) {
            isPhase2 = true;
            document.body.classList.add('phase-2');
            // Eliminamos la esfera interior y partículas del render
            // y añadimos la esfera externa de Phase 2
            scene.remove(innerSphere);
            scene.remove(particles);
            setupPhase2();
          }
        }
      }

      renderer.render(scene, camera);
    }

    animate();

    // ── Phase 2: esfera externa ──
    let extSphere, extEmber, extHalo, extRotY = 0, extAutoRotate = true, extResumeTimer = null;

    function setupPhase2() {
      // Reposicionamos la cámara para ver la escena desde fuera
      camera.position.set(0, 0, 5.5);
      camera.lookAt(0, 0, 0);
      camera.fov = 55;
      camera.updateProjectionMatrix();

      // Esfera externa — mismos materiales que el ember-orb anterior
      const geo = new THREE.SphereGeometry(1.4, 64, 64);
      const mat = new THREE.MeshStandardMaterial({
        color: 0x050300,
        roughness: 0.95,
        metalness: 0.0,
        emissive: 0x000000,
        emissiveIntensity: 0
      });
      extSphere = new THREE.Mesh(geo, mat);
      extSphere.position.set(2, -0.5, 0);
      scene.add(extSphere);

      // Wireframe parented a la esfera
      const wire = new THREE.LineSegments(
        new THREE.WireframeGeometry(geo),
        new THREE.LineBasicMaterial({ color: 0xFEB354, transparent: true, opacity: 0.05 })
      );
      extSphere.add(wire);

      // Luz ámbar desde abajo
      extEmber = new THREE.PointLight(0xFEB354, 8, 5);
      extEmber.position.set(2, -2.8, 0.8);
      scene.add(extEmber);

      extHalo = new THREE.PointLight(0xFF7A20, 3, 8);
      extHalo.position.set(2, -4, 1.5);
      scene.add(extHalo);

      scene.add(new THREE.AmbientLight(0x080503, 6));

      // Drag para rotar la esfera externa
      canvas.addEventListener('mousedown', extMouseDown);
      window.addEventListener('mousemove', extMouseMove);
      window.addEventListener('mouseup', extMouseUp);

      // Reemplazamos el loop
      requestAnimationFrame(animatePhase2);
    }

    let ext_isDragging = false;
    let ext_prevX = 0, ext_prevY = 0;
    let ext_rotY = 0, ext_rotX = 0;

    function extMouseDown(e) {
      ext_isDragging  = true;
      extAutoRotate   = false;
      ext_prevX = e.clientX;
      ext_prevY = e.clientY;
      if (extResumeTimer) clearTimeout(extResumeTimer);
    }

    function extMouseMove(e) {
      if (!ext_isDragging) return;
      ext_rotY += (e.clientX - ext_prevX) * 0.006;
      ext_rotX += (e.clientY - ext_prevY) * 0.004;
      ext_rotX  = Math.max(-0.7, Math.min(0.7, ext_rotX));
      ext_prevX = e.clientX;
      ext_prevY = e.clientY;
    }

    function extMouseUp() {
      if (!ext_isDragging) return;
      ext_isDragging  = false;
      extResumeTimer  = setTimeout(() => { extAutoRotate = true; }, 1500);
    }

    let t2 = 0;

    function animatePhase2() {
      requestAnimationFrame(animatePhase2);
      t2 += 0.004;

      if (extAutoRotate) {
        ext_rotY += 0.004;
        ext_rotX  = Math.sin(t2 * 0.3) * 0.07;
      }

      if (extSphere) {
        extSphere.rotation.y = ext_rotY;
        extSphere.rotation.x = ext_rotX;

        const floatY = Math.sin(t2 * 0.5) * 0.07;
        extSphere.position.y = -0.5 + floatY;
        if (extEmber) extEmber.position.y = -2.8 + floatY;
        if (extHalo)  extHalo.position.y  = -4.0 + floatY;
      }

      renderer.render(scene, camera);
    }

  }

  // Si Three.js ya está disponible al DOMContentLoaded, esperamos al loader.
  // El loader llama a startHeroThreeJS() a t=4.0s.
  // Si Three.js no cargó aún (defer), lo escuchamos.
  if (typeof THREE === 'undefined') {
    const threeScript = document.querySelector('script[src*="three"]');
    if (threeScript) {
      threeScript.addEventListener('load', () => {
        // startHeroThreeJS ya se llamará desde el loader
      });
    }
  }


  // ── 9. SCROLL HINT ──────────────────────────────────────────────────────
  const scrollHint = document.getElementById('scroll-hint');
  if (scrollHint) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 10) {
        scrollHint.classList.add('hide');
      }
    }, { once: true, passive: true });
  }


  // ── 10. CARRUSEL NEGOCIOS ────────────────────────────────────────────────
  // 8 cards con efecto 3D CSS (rotateY en laterales). Autoavance cada 4s.
  // Drag: si el delta horizontal supera 50px se avanza/retrocede.
  const carousel = document.getElementById('carousel');
  const cards    = carousel ? Array.from(carousel.querySelectorAll('.carousel-card')) : [];
  const dots     = document.querySelectorAll('.carousel-dot');
  const prevBtn  = document.getElementById('carousel-prev');
  const nextBtn  = document.getElementById('carousel-next');
  const TOTAL    = cards.length;
  let   current  = 0;
  let   autoTimer = null;

  const POSITIONS = ['prev-2', 'prev-1', 'active', 'next-1', 'next-2'];

  function setCarousel(index) {
    cards.forEach((card, i) => {
      card.className = 'carousel-card';
      const rel = ((i - index) % TOTAL + TOTAL) % TOTAL;
      if      (rel === 0)        card.classList.add('active');
      else if (rel === 1)        card.classList.add('next-1');
      else if (rel === 2)        card.classList.add('next-2');
      else if (rel === TOTAL - 1) card.classList.add('prev-1');
      else if (rel === TOTAL - 2) card.classList.add('prev-2');
      else                        card.classList.add('c-hidden');
    });

    dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
    current = index;
  }

  function carouselNext() { setCarousel((current + 1) % TOTAL); }
  function carouselPrev() { setCarousel((current - 1 + TOTAL) % TOTAL); }

  function resetAuto() {
    clearInterval(autoTimer);
    autoTimer = setInterval(carouselNext, 4000);
  }

  if (TOTAL > 0) {
    setCarousel(0);
    resetAuto();

    if (nextBtn) nextBtn.addEventListener('click', () => { carouselNext(); resetAuto(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { carouselPrev(); resetAuto(); });

    // Drag horizontal en el carrusel
    let dragStart = 0;
    carousel.addEventListener('mousedown', e => { dragStart = e.clientX; });
    carousel.addEventListener('mouseup', e => {
      const delta = e.clientX - dragStart;
      if (Math.abs(delta) > 50) {
        delta < 0 ? carouselNext() : carouselPrev();
        resetAuto();
      }
    });

    carousel.addEventListener('touchstart', e => {
      dragStart = e.touches[0].clientX;
    }, { passive: true });
    carousel.addEventListener('touchend', e => {
      const delta = e.changedTouches[0].clientX - dragStart;
      if (Math.abs(delta) > 50) {
        delta < 0 ? carouselNext() : carouselPrev();
        resetAuto();
      }
    }, { passive: true });
  }


  // ── 11. ANIMACIONES DE SCROLL (IntersectionObserver) ────────────────────
  const animObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        animObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  document.querySelectorAll('.animate-on-scroll').forEach((el, i) => {
    el.style.transitionDelay = `${i * 0.07}s`;
    animObserver.observe(el);
  });


  // ── 12. CURSOR SPOTLIGHT EN CARACTERÍSTICAS ──────────────────────────────
  // Cuando el cursor se mueve sobre la sección, las CSS vars --mx y --my
  // de cada .feature-card::before se actualizan para seguir el puntero.
  // En móvil (pointer:coarse) el CSS ya desactiva el efecto.
  const featSection = document.getElementById('caracteristicas');
  if (featSection && window.matchMedia('(pointer: fine)').matches) {
    featSection.addEventListener('mousemove', e => {
      const rect = featSection.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      // Propagamos la posición relativa a cada card para que ::before sea correcto
      featSection.querySelectorAll('.feature-card').forEach(card => {
        const cr = card.getBoundingClientRect();
        card.style.setProperty('--mx', `${e.clientX - cr.left}px`);
        card.style.setProperty('--my', `${e.clientY - cr.top}px`);
      });
    });
  }


  // ── 13. ACORDEÓN FAQ ─────────────────────────────────────────────────────
  document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    const answer   = item.querySelector('.faq-answer');
    if (!question || !answer) return;

    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');

      document.querySelectorAll('.faq-item.open').forEach(open => {
        open.classList.remove('open');
        open.querySelector('.faq-answer').style.maxHeight = '0';
      });

      if (!isOpen) {
        item.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });


  // ── 14. TOGGLE PRECIOS MENSUAL / ANUAL ───────────────────────────────────
  const billingToggle  = document.getElementById('billing-toggle');
  const pricingSection = document.getElementById('precios');

  if (billingToggle && pricingSection) {
    billingToggle.addEventListener('click', () => {
      const isAnnual = pricingSection.classList.toggle('annual');
      billingToggle.setAttribute('aria-checked', isAnnual);

      pricingSection.querySelectorAll('[data-monthly]').forEach(el => {
        el.textContent = isAnnual ? el.dataset.annual : el.dataset.monthly;
      });
    });
  }


  // ── 15. THREE.JS — ESFERA DECORATIVA CTA FINAL ───────────────────────────
  // Pequeña esfera ember-orb en el CTA final. Solo auto-rotate, sin drag.
  // Solo en desktop (> 768px).
  function initCtaSphere() {
    if (window.innerWidth <= 768) return;

    const ctaCanvas = document.getElementById('cta-canvas');
    if (!ctaCanvas || typeof THREE === 'undefined') return;

    const ctaScene    = new THREE.Scene();
    const ctaCamera   = new THREE.PerspectiveCamera(55, 1, 0.1, 50);
    const ctaRenderer = new THREE.WebGLRenderer({ canvas: ctaCanvas, alpha: true, antialias: true });

    ctaCamera.position.set(0, 0, 3.5);
    ctaRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    ctaRenderer.setSize(200, 200);
    ctaRenderer.setClearColor(0x000000, 0);

    const geo = new THREE.SphereGeometry(0.8, 32, 32);
    const mat = new THREE.MeshStandardMaterial({
      color: 0x050300,
      roughness: 0.95,
      metalness: 0.0,
      emissive: 0x000000,
      emissiveIntensity: 0
    });
    const sphere = new THREE.Mesh(geo, mat);
    ctaScene.add(sphere);

    const wire = new THREE.LineSegments(
      new THREE.WireframeGeometry(geo),
      new THREE.LineBasicMaterial({ color: 0xFEB354, transparent: true, opacity: 0.06 })
    );
    sphere.add(wire);

    const light = new THREE.PointLight(0xFEB354, 10, 4);
    light.position.set(0, -1.5, 0.5);
    ctaScene.add(light);

    ctaScene.add(new THREE.AmbientLight(0x080503, 5));

    let tCta = 0;

    function animateCta() {
      requestAnimationFrame(animateCta);
      tCta += 0.005;
      sphere.rotation.y += 0.006;
      sphere.rotation.x = Math.sin(tCta * 0.4) * 0.06;
      ctaRenderer.render(ctaScene, ctaCamera);
    }

    // Usamos IntersectionObserver para iniciar el render solo cuando el canvas es visible
    const ctaObserver = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        animateCta();
        ctaObserver.disconnect();
      }
    });
    ctaObserver.observe(ctaCanvas);
  }

  // La esfera CTA se puede iniciar después del loader
  setTimeout(initCtaSphere, 4200);

});
