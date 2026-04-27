/**
 * main.js — Lógica completa de la landing page Oxphyre.
 *
 * Arquitectura: canvas Three.js position:fixed activo durante todo el scroll.
 * Un único loop de animación. El scrollY se mapea a estados de la esfera con lerp.
 *
 * Módulos:
 *  1.  Cursor personalizado
 *  2.  Loader (beam exacto sobre texto + letras + explosión)
 *  3.  Tema día/noche
 *  4.  Idioma (delega en i18n.js)
 *  5.  Nav glassmorphism al scroll
 *  6.  Menú móvil
 *  7.  Hero Three.js — Phase 1 (dentro de la esfera, BackSide + partículas + drag)
 *  8.  Hero Three.js — Phase 2 (scroll libera, cámara sale, esfera centrada como fondo)
 *  9.  Scroll state machine — esfera sigue el scroll por todas las secciones
 *  10. Scroll hint (mouse SVG, desaparece al primer scroll)
 *  11. Carrusel negocios (5s autoavance, drag, hover preview)
 *  12. IntersectionObserver animaciones de scroll
 *  13. Cursor spotlight en Características
 *  14. S3 tilt hover + línea conector SVG
 *  15. Acordeón FAQ
 *  16. Toggle precios mensual/anual
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── 1. CURSOR PERSONALIZADO ──────────────────────────────────────────────
  const cursorRing = document.getElementById('cursor-ring');

  if (cursorRing && window.matchMedia('(pointer: fine)').matches) {
    window.addEventListener('mousemove', e => {
      cursorRing.style.transform = `translate(${e.clientX}px, ${e.clientY}px) translate(-50%, -50%)`;
    });

    document.querySelectorAll('a, button, [role="button"], input, label, .carousel-card, .step-card, .feature-card, .pricing-card').forEach(el => {
      el.addEventListener('mouseenter', () => cursorRing.classList.add('cursor-hover'));
      el.addEventListener('mouseleave', () => cursorRing.classList.remove('cursor-hover'));
    });
  }


  // ── 2. LOADER ────────────────────────────────────────────────────────────
  const loader  = document.getElementById('loader');
  const beam    = document.getElementById('loader-beam');
  const letters = document.querySelectorAll('.loader-letter');

  function runLoader() {
    if (!loader || !beam) {
      document.body.classList.add('phase-2');
      return;
    }

    // BUG 12: beam recorre exactamente la anchura de OXPHYRE (de O a E)
    setTimeout(() => {
      const textEl    = document.getElementById('loader-text');
      const spans     = textEl ? textEl.querySelectorAll('.loader-letter') : [];
      if (spans.length) {
        const firstRect = spans[0].getBoundingClientRect();
        const lastRect  = spans[spans.length - 1].getBoundingClientRect();
        const beamW     = 80;
        beam.style.top       = firstRect.top    + 'px';
        beam.style.height    = firstRect.height + 'px';
        beam.style.left      = firstRect.left   + 'px';
        beam.style.transform = `translateX(-${beamW}px)`;
        const travel = (lastRect.right - firstRect.left) + beamW;
        beam.style.transition = 'transform 2.2s linear';
        requestAnimationFrame(() => {
          beam.style.transform = `translateX(${travel}px)`;
        });
      }
    }, 500);

    // Letras reveladas escalonadas desde t=1.5s
    letters.forEach((letter, i) => {
      setTimeout(() => letter.classList.add('revealed'), 1500 + i * 200);
    });

    // Explosión a t=3.5s
    setTimeout(() => {
      letters.forEach((letter, i) => {
        setTimeout(() => letter.classList.add('explode'), i * 60);
      });
    }, 3500);

    // Loader desaparece a t=4.0s → inicia Three.js
    setTimeout(() => {
      loader.classList.add('hidden');
      document.documentElement.style.scrollBehavior = ''; // BUG 16: restaurar smooth
      startThreeJS();
    }, 4000);
  }

  runLoader();


  // ── 3. TEMA DÍA/NOCHE ───────────────────────────────────────────────────
  const themeBtn    = document.getElementById('theme-toggle');
  const saved       = localStorage.getItem('oxphyre-theme');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initTheme   = saved ?? (prefersDark ? 'dark' : 'light');

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
  if (window.i18n) window.i18n.initLang();

  document.querySelectorAll('.lang-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const lang = btn.dataset.lang;
      localStorage.setItem('oxphyre-lang', lang);
      window.i18n?.applyLang(lang);
    });
  });


  // ── 5. NAV GLASSMORPHISM ─────────────────────────────────────────────────
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


  // ── 7, 8, 9. THREE.JS — CANVAS FIXED + SCROLL STATE MACHINE ─────────────
  // Un único canvas position:fixed activo en toda la página.
  // Phase 1: cámara dentro de la esfera (BackSide + partículas + drag).
  // Phase 2: cámara fuera, esfera centrada como fondo de hero.
  // Scroll state machine: scrollY mapea a estados de la esfera por sección.

  function startThreeJS() {
    // BUG 1: forzar scroll a top antes de que el navegador restaure la posición (F5)
    window.scrollTo(0, 0);
    document.body.style.overflow = 'hidden';

    if (window.innerWidth <= 768) {
      document.body.style.overflow = '';
      document.body.classList.add('phase-2');
      return;
    }

    const canvas = document.getElementById('hero-canvas');
    if (!canvas || typeof THREE === 'undefined') {
      document.body.style.overflow = '';
      document.body.classList.add('phase-2');
      return;
    }

    const scene  = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.01, 200);
    // BUG 11: antialias desactivado en Chrome para mejor rendimiento
    const isChrome = /Chrome/.test(navigator.userAgent) && !/Edg|Brave/.test(navigator.userAgent);
    const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: !isChrome });

    // FIX 8: pixelRatio máximo 1.5 para rendimiento en Chrome
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
    renderer.setClearColor(0x000000, 0);

    function resize() {
      renderer.setSize(window.innerWidth, window.innerHeight);
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
    }
    resize();
    window.addEventListener('resize', resize);

    // ── Esfera principal (usada en Phase 1 BackSide y Phase 2 frontal) ──
    const sphereGeo = new THREE.SphereGeometry(4, 48, 48);

    // Material interno (Phase 1: negro BackSide)
    const innerMat = new THREE.MeshBasicMaterial({ color: 0x000000, side: THREE.BackSide });
    const innerSphere = new THREE.Mesh(sphereGeo, innerMat);
    scene.add(innerSphere);

    // Material externo (Phase 2: ember dark)
    const outerMat = new THREE.MeshStandardMaterial({
      color: 0x050300,
      roughness: 0.95,
      metalness: 0.0,
      emissive: 0x000000,
      emissiveIntensity: 0
    });
    const outerSphere = new THREE.Mesh(sphereGeo, outerMat);
    outerSphere.visible = false;
    scene.add(outerSphere);

    // Wireframe
    const wireMat = new THREE.LineBasicMaterial({ color: 0xFEB354, transparent: true, opacity: 0.04 });
    const wireframe = new THREE.LineSegments(new THREE.WireframeGeometry(sphereGeo), wireMat);
    outerSphere.add(wireframe);

    // ── Partículas ámbar ──
    const PARTICLE_COUNT = 300;
    const pBasePos = new Float32Array(PARTICLE_COUNT * 3);
    const pOffsets = new Float32Array(PARTICLE_COUNT);

    for (let i = 0; i < PARTICLE_COUNT; i++) {
      const r     = 2.5 + Math.random() * 1.2;
      const theta = Math.random() * Math.PI * 2;
      const phi   = Math.acos(2 * Math.random() - 1);
      pBasePos[i * 3]     = r * Math.sin(phi) * Math.cos(theta);
      pBasePos[i * 3 + 1] = r * Math.sin(phi) * Math.sin(theta);
      pBasePos[i * 3 + 2] = r * Math.cos(phi);
      pOffsets[i] = Math.random() * Math.PI * 2;
    }

    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.Float32BufferAttribute(pBasePos.slice(), 3));

    // BUG 5: textura circular con gradiente radial — puntos de luz difusos, no cuadrados
    function createParticleTexture() {
      const size = 32;
      const cvs  = document.createElement('canvas');
      cvs.width = cvs.height = size;
      const ctx = cvs.getContext('2d');
      const g   = ctx.createRadialGradient(size/2, size/2, 0, size/2, size/2, size/2);
      g.addColorStop(0,   'rgba(254, 179, 84, 1)');
      g.addColorStop(0.3, 'rgba(254, 179, 84, 0.6)');
      g.addColorStop(1,   'rgba(254, 179, 84, 0)');
      ctx.fillStyle = g;
      ctx.fillRect(0, 0, size, size);
      return new THREE.CanvasTexture(cvs);
    }

    const pMat = new THREE.PointsMaterial({
      map: createParticleTexture(),
      size: 0.05,
      transparent: true,
      opacity: 0.8,
      sizeAttenuation: true,
      depthWrite: false,
      blending: THREE.AdditiveBlending
    });
    const particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    // ── Luces ──
    const emberLight = new THREE.PointLight(0xFEB354, 8, 10);
    emberLight.position.set(0, -5, 1);
    scene.add(emberLight);

    const haloLight = new THREE.PointLight(0xFF7A20, 3, 14);
    haloLight.position.set(0, -7, 2);
    scene.add(haloLight);

    const ambientLight = new THREE.AmbientLight(0x080503, 5);
    scene.add(ambientLight);

    // BUG 14: drag Phase 1 eliminado — la rotación es solo automática
    // BUG 15: frases rotan automáticamente cada 3.6s con fade in/out
    const phrasesEl  = document.querySelectorAll('#hero-phrases .phrase');
    let phraseIndex  = 0;
    let phraseTimer  = null;

    function showPhrase(idx) {
      phrasesEl.forEach(p => p.classList.remove('active'));
      if (phrasesEl[idx]) phrasesEl[idx].classList.add('active');
    }

    function startPhraseRotation() {
      showPhrase(0);
      phraseTimer = setInterval(() => {
        phraseIndex = (phraseIndex + 1) % phrasesEl.length;
        showPhrase(phraseIndex);
      }, 3600); // 0.8s fade-in + 2s hold + 0.8s fade-out
    }

    function stopPhraseRotation() {
      clearInterval(phraseTimer);
      phrasesEl.forEach(p => p.classList.remove('active'));
    }

    startPhraseRotation();

    // El overflow ya fue bloqueado al inicio de startThreeJS() (BUG 1)

    let isPhase1   = true;
    let isPhase2   = false;
    let scrollAccum = 0;
    let targetCamZ  = 0.01;
    let camZ        = 0.01;

    function onPhase1Wheel(e) {
      if (!isPhase1) return;
      scrollAccum += e.deltaY * 0.015;
      scrollAccum  = Math.max(0, scrollAccum);
      if (scrollAccum > 3) {
        targetCamZ = 12;
      }
    }
    window.addEventListener('wheel', onPhase1Wheel, { passive: true });

    function activatePhase2() {
      if (isPhase2) return;
      isPhase2 = true;
      isPhase1 = false;
      stopPhraseRotation(); // BUG 15: detener ciclo de frases al salir

      // FIX 4: liberar scroll y resetear posición
      document.body.style.overflow = '';
      window.scrollTo(0, 0);
      document.body.classList.add('phase-2');

      // Eliminar el wheel listener de Phase 1 — no se puede volver a entrar
      window.removeEventListener('wheel', onPhase1Wheel);

      // Cambiar visibilidad: BackSide out, outer sphere in
      innerSphere.visible = false;
      outerSphere.visible = true;

      // La esfera externa ocupa ~60-70% del viewport centrada como fondo del hero
      outerSphere.position.set(0, 0, 0);
      outerSphere.scale.setScalar(1);

      camera.position.set(0, 0, 5.5);
      camera.fov = 55;
      camera.updateProjectionMatrix();
      camera.lookAt(0, 0, 0);

      // Luces ajustadas para Phase 2
      emberLight.position.set(0, -2.8, 1);
      emberLight.intensity = 8;
      haloLight.position.set(0, -4, 2);
    }

    // Estado actual de la esfera para el scroll state machine
    let sphereScale   = 0;
    let sphereOpacity = 0;
    let sphereY       = 0;

    // ── Secciones del DOM para el scroll state machine ──
    function getSectionMid(id) {
      const el = document.getElementById(id);
      if (!el) return Infinity;
      const r = el.getBoundingClientRect();
      return window.scrollY + r.top + r.height / 2;
    }

    // Drag para rotar la esfera en Phase 2
    let extDragging = false, extPrevX = 0, extPrevY = 0;
    let extRotY = 0, extRotX = 0;
    let extAutoRotate = true, extResumeTimer = null;

    canvas.addEventListener('mousedown', e => {
      if (!isPhase2) return;
      extDragging  = true;
      extAutoRotate = false;
      extPrevX = e.clientX; extPrevY = e.clientY;
      if (extResumeTimer) clearTimeout(extResumeTimer);
    });

    window.addEventListener('mousemove', e => {
      if (!extDragging || !isPhase2) return;
      extRotY += (e.clientX - extPrevX) * 0.006;
      extRotX += (e.clientY - extPrevY) * 0.004;
      extRotX  = Math.max(-0.7, Math.min(0.7, extRotX));
      extPrevX = e.clientX; extPrevY = e.clientY;
    });

    window.addEventListener('mouseup', () => {
      if (!extDragging) return;
      extDragging  = false;
      extResumeTimer = setTimeout(() => { extAutoRotate = true; }, 1500);
    });

    // ── FIX 8: pausar loop cuando tab no visible ──
    let animId;
    let isTabVisible = !document.hidden;
    document.addEventListener('visibilitychange', () => {
      isTabVisible = !document.hidden;
      if (isTabVisible) animate();
    });

    let t = 0;
    const pAttr = pGeo.attributes.position;

    function lerp(a, b, f) { return a + (b - a) * f; }

    function animate() {
      if (!isTabVisible) return;
      animId = requestAnimationFrame(animate);
      t += 0.006;

      if (isPhase1) {
        // ── PHASE 1: dentro de la esfera ──
        targetTheta += 0.002; // BUG 4: auto-rotación siempre activa (drag eliminado en BUG 14)
        spherical.theta += (targetTheta - spherical.theta) * 0.08;
        spherical.phi   += (targetPhi   - spherical.phi)   * 0.08;

        // Lerp hacia afuera si el usuario scrolleó
        camZ += (targetCamZ - camZ) * 0.05;

        const r = camZ;
        camera.position.set(
          r * Math.sin(spherical.phi) * Math.cos(spherical.theta),
          r * Math.cos(spherical.phi),
          r * Math.sin(spherical.phi) * Math.sin(spherical.theta)
        );
        camera.lookAt(0, 0, 0);

        // Oscilación de partículas
        for (let i = 0; i < PARTICLE_COUNT; i++) {
          const wave = Math.sin(t + pOffsets[i]) * 0.05;
          pAttr.setXYZ(
            i,
            pBasePos[i * 3]     + wave,
            pBasePos[i * 3 + 1] + wave * 0.5,
            pBasePos[i * 3 + 2] + wave
          );
        }
        pAttr.needsUpdate = true;

        // Activar Phase 2 cuando la cámara sale suficientemente lejos
        if (camZ > 7) activatePhase2();

      } else {
        // ── PHASE 2: esfera external + scroll state machine ──
        if (extAutoRotate) {
          extRotY += 0.004;
          extRotX  = Math.sin(t * 0.3) * 0.06;
        }

        outerSphere.rotation.y = extRotY;
        outerSphere.rotation.x = extRotX;

        // Scroll state machine: mapear scrollY a estado de la esfera
        const sy = window.scrollY;
        const vh = window.innerHeight;

        // Obtener posiciones de las secciones
        const heroEl  = document.getElementById('hero');
        const s2El    = document.getElementById('carousel-section');
        const s3El    = document.getElementById('como-funciona');
        const s4El    = document.getElementById('demo');
        const s5El    = document.getElementById('caracteristicas');
        const s6El    = document.getElementById('precios');
        const s7El    = document.getElementById('faq');
        const ctaEl   = document.getElementById('cta-final');

        const getTop = el => el ? el.getBoundingClientRect().top + sy : Infinity;

        const heroTop = getTop(heroEl);
        const s2Top   = getTop(s2El);
        const s3Top   = getTop(s3El);
        const s4Top   = getTop(s4El);
        const s5Top   = getTop(s5El);
        const s6Top   = getTop(s6El);
        const s7Top   = getTop(s7El);
        const ctaTop  = getTop(ctaEl);
        const ctaHeight = ctaEl ? ctaEl.getBoundingClientRect().height + ctaEl.getBoundingClientRect().top + sy - ctaTop : 0;

        // BUG 6: estado simplificado — hero grande, S2-S7 pequeña constante, CTA peachweb
        // BUG 10: wireMat.opacity crece en CTA; partículas NUNCA en Phase 2
        let tScale = 1.4, tOY = 0, tOpacity = 1, tLI = 8;
        let tFloatY = 0;
        particles.visible = false;

        if (sy >= ctaTop - vh * 0.3) {
          // S8 CTA + Footer: peachweb — esfera crece hasta cubrir la pantalla
          const p = Math.min(1, (sy - (ctaTop - vh * 0.3)) / (vh * 0.7));
          tScale   = lerp(0.3, 8, p);
          tOpacity = lerp(0.3, 1, p);
          tLI      = lerp(3, 10, p);
          tOY      = 0;
          // Wireframe más visible al entrar — crea el efecto de grid sutil (BUG 10)
          wireMat.opacity = lerp(0.04, 0.18, p);
        } else if (sy >= s2Top - vh * 0.5) {
          // S2-S7: esfera pequeña constante de fondo (BUG 6)
          tScale   = 0.3;
          tOpacity = 0.2;
          tLI      = 2;
          tOY      = 0;
          wireMat.opacity = 0.04;
        } else {
          // Hero Phase 2: esfera grande centrada con flotación
          tScale   = 1.4;
          tOpacity = 1;
          tLI      = 8;
          tFloatY  = Math.sin(t * 0.5) * 0.07;
          tOY      = 0;
          wireMat.opacity = 0.04;
        }

        // Lerp suave del estado de la esfera
        sphereScale   = lerp(sphereScale,   tScale,   0.04);
        sphereY       = lerp(sphereY,       tOY,      0.04);
        sphereOpacity = lerp(sphereOpacity, tOpacity, 0.04);

        outerSphere.scale.setScalar(Math.max(0, sphereScale));
        outerSphere.position.y = sphereY + tFloatY;
        outerMat.opacity = sphereOpacity;
        outerMat.transparent = sphereOpacity < 1;
        outerSphere.visible = sphereScale > 0.02;

        // Luces siguen la esfera
        const lI = lerp(emberLight.intensity, tLI, 0.04);
        emberLight.intensity = lI;
        emberLight.position.y = lerp(emberLight.position.y, sphereY - 2.8, 0.04);
        haloLight.position.y  = lerp(haloLight.position.y,  sphereY - 4.5, 0.04);

        // BUG 10: sin partículas en Phase 2 — el wireframe da el efecto de fondo
        innerSphere.visible = false;
      }

      renderer.render(scene, camera);
    }

    animate();
  }

  // Si Three.js cargó tras DOMContentLoaded, el loader lo llama. Si falló, fallback.
  if (typeof THREE === 'undefined') {
    const threeScript = document.querySelector('script[src*="three"]');
    if (threeScript) {
      threeScript.addEventListener('load', () => { /* loader ya planificado */ });
    }
  }


  // ── 10. SCROLL HINT ──────────────────────────────────────────────────────
  const scrollHint = document.getElementById('scroll-hint');
  if (scrollHint) {
    window.addEventListener('scroll', () => {
      scrollHint.classList.add('hide');
    }, { once: true, passive: true });
  }


  // ── 11. CARRUSEL NEGOCIOS ────────────────────────────────────────────────
  const carousel = document.getElementById('carousel');
  const cards    = carousel ? Array.from(carousel.querySelectorAll('.carousel-card')) : [];
  const dots     = document.querySelectorAll('.carousel-dot');
  const prevBtn  = document.getElementById('carousel-prev');
  const nextBtn  = document.getElementById('carousel-next');
  const TOTAL    = cards.length;
  let   current  = 0;
  let   autoTimer = null;

  function setCarousel(index) {
    cards.forEach((card, i) => {
      card.className = 'carousel-card';
      const rel = ((i - index) % TOTAL + TOTAL) % TOTAL;
      if      (rel === 0)           card.classList.add('active');
      else if (rel === 1)           card.classList.add('next-1');
      else if (rel === 2)           card.classList.add('next-2');
      else if (rel === TOTAL - 1)   card.classList.add('prev-1');
      else if (rel === TOTAL - 2)   card.classList.add('prev-2');
      else                          card.classList.add('c-hidden');
    });
    dots.forEach((dot, i) => dot.classList.toggle('active', i === index));
    current = index;
  }

  function carouselNext() { setCarousel((current + 1) % TOTAL); }
  function carouselPrev() { setCarousel((current - 1 + TOTAL) % TOTAL); }

  function resetAuto() {
    clearInterval(autoTimer);
    // FIX 9: autoavance 5s (era 4s)
    autoTimer = setInterval(carouselNext, 5000);
  }

  if (TOTAL > 0) {
    setCarousel(0);
    resetAuto();

    if (nextBtn) nextBtn.addEventListener('click', () => { carouselNext(); resetAuto(); });
    if (prevBtn) prevBtn.addEventListener('click', () => { carouselPrev(); resetAuto(); });

    let dragStart = 0;
    carousel.addEventListener('mousedown', e => { dragStart = e.clientX; });
    carousel.addEventListener('mouseup', e => {
      const delta = e.clientX - dragStart;
      if (Math.abs(delta) > 50) { delta < 0 ? carouselNext() : carouselPrev(); resetAuto(); }
    });
    carousel.addEventListener('touchstart', e => { dragStart = e.touches[0].clientX; }, { passive: true });
    carousel.addEventListener('touchend', e => {
      const delta = e.changedTouches[0].clientX - dragStart;
      if (Math.abs(delta) > 50) { delta < 0 ? carouselNext() : carouselPrev(); resetAuto(); }
    }, { passive: true });

    // BUG 17: modal en lugar de preview — solo en la card activa
    const modal        = document.getElementById('carousel-modal');
    const modalImg     = document.getElementById('carousel-modal-img');
    const modalTitle   = document.getElementById('carousel-modal-title');
    const modalDesc    = document.getElementById('carousel-modal-desc');
    const modalClose   = document.getElementById('carousel-modal-close');
    const modalOverlay = document.getElementById('carousel-modal-overlay');

    function openCarouselModal(card) {
      const img   = card.querySelector('img');
      const title = card.querySelector('.carousel-card-title');
      const desc  = card.querySelector('.carousel-card-text');
      if (modalImg)   { modalImg.src = img?.src || ''; modalImg.alt = img?.alt || ''; }
      if (modalTitle) modalTitle.textContent = title?.textContent || '';
      if (modalDesc)  modalDesc.textContent  = desc?.textContent  || '';
      modal?.classList.add('open');
      modal?.setAttribute('aria-hidden', 'false');
      clearInterval(autoTimer);
    }

    function closeCarouselModal() {
      modal?.classList.remove('open');
      modal?.setAttribute('aria-hidden', 'true');
      resetAuto();
    }

    if (modal) {
      modalClose?.addEventListener('click', closeCarouselModal);
      modalOverlay?.addEventListener('click', closeCarouselModal);
      document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closeCarouselModal();
      });

      // Desktop: hover en card activa abre el modal
      carousel.addEventListener('mouseover', e => {
        const card = e.target.closest('.carousel-card.active');
        if (card && window.matchMedia('(pointer: fine)').matches && !modal.classList.contains('open')) {
          openCarouselModal(card);
        }
      });

      // Móvil: click en card activa abre el modal
      carousel.addEventListener('click', e => {
        const card = e.target.closest('.carousel-card.active');
        if (card && window.matchMedia('(pointer: coarse)').matches) {
          openCarouselModal(card);
        }
      });
    }
  }


  // ── 12. ANIMACIONES DE SCROLL ────────────────────────────────────────────
  const animObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        animObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' }); // BUG 9: rootMargin reducido

  document.querySelectorAll('.animate-on-scroll').forEach((el, i) => {
    // BUG 9: elementos del FAQ sin delay escalonado para que entren sincronizados
    const inFaq = el.closest('#faq');
    el.style.transitionDelay = inFaq ? '0s' : `${i * 0.07}s`;
    animObserver.observe(el);
  });


  // ── 13. CURSOR SPOTLIGHT EN CARACTERÍSTICAS ──────────────────────────────
  const featSection = document.getElementById('caracteristicas');
  if (featSection && window.matchMedia('(pointer: fine)').matches) {
    featSection.addEventListener('mousemove', e => {
      featSection.querySelectorAll('.feature-card').forEach(card => {
        const cr = card.getBoundingClientRect();
        card.style.setProperty('--mx', `${e.clientX - cr.left}px`);
        card.style.setProperty('--my', `${e.clientY - cr.top}px`);
      });
    });
  }


  // ── 14. S3 TILT HOVER + LÍNEA CONECTOR ──────────────────────────────────
  // FIX 10: tilt 3D siguiendo el cursor en hover de cada step card
  document.querySelectorAll('.step-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const r = card.getBoundingClientRect();
      const x = (e.clientX - r.left - r.width  / 2) / r.width  * 12;
      const y = (e.clientY - r.top  - r.height / 2) / r.height * 12;
      card.style.transform = `perspective(600px) rotateY(${x}deg) rotateX(${-y}deg) translateY(-4px)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = '';
    });
  });

  // Línea conector: animar el SVG cuando la sección es visible
  const connectorSvg = document.querySelector('.step-connector');
  if (connectorSvg) {
    const paths = connectorSvg.querySelectorAll('path');
    const connObserver = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        paths.forEach((path, i) => {
          const length = path.getTotalLength();
          path.style.strokeDasharray  = length;
          path.style.strokeDashoffset = length;
          path.style.transition = `stroke-dashoffset 0.8s ease ${i * 0.3}s`;
          requestAnimationFrame(() => { path.style.strokeDashoffset = '0'; });
        });
        connObserver.disconnect();
      }
    }, { threshold: 0.3 });
    connObserver.observe(connectorSvg);
  }


  // ── 15. ACORDEÓN FAQ ─────────────────────────────────────────────────────
  document.querySelectorAll('.faq-item').forEach(item => {
    const question = item.querySelector('.faq-question');
    const answer   = item.querySelector('.faq-answer');
    if (!question || !answer) return;

    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');

      document.querySelectorAll('.faq-item.open').forEach(open => {
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


  // ── 16. TOGGLE PRECIOS MENSUAL / ANUAL ───────────────────────────────────
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

});
