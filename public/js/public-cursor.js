/* public-cursor.js — Oxphyre
 * Cursor personalizado tipo anillo para páginas públicas ligeras.
 * No usa Three.js ni main.js.
 * Reglas:
 *  - Solo en pointer:fine (ratón/trackpad). En táctil no hace nada.
 *  - Cancela si #cursor-ring no existe en el DOM.
 *  - Mueve el anillo con mousemove.
 *  - Añade/quita .cursor-hover al pasar por elementos interactivos.
 *  - Añade .cursor-accent (borde oscuro) sobre CTAs con fondo ámbar.
 * ----------------------------------------------------------------- */
(function () {
  'use strict';

  /* Solo dispositivos con puntero fino (ratón / trackpad) */
  if (!window.matchMedia('(pointer: fine)').matches) return;

  const ring = document.getElementById('cursor-ring');
  if (!ring) return;

  /* Mover el anillo siguiendo el ratón */
  window.addEventListener('mousemove', function (e) {
    ring.style.transform =
      'translate(' + e.clientX + 'px, ' + e.clientY + 'px) translate(-50%, -50%)';
  }, { passive: true });

  /* ── Selector de elementos interactivos que amplían el anillo ── */
  var INTERACTIVE = [
    'a',
    'button',
    '[role="button"]',
    'input[type="submit"]',
    'input[type="button"]',
    'input[type="reset"]',
    'label[for]',
    'select',
    'summary',
    '.pricing-card',
    '.faq-question',
    '.faq-item summary',
    '.step-card',
    '.feature-card'
  ].join(', ');

  /* ── CTAs con fondo ámbar: el anillo cambia a oscuro sobre ellos ── */
  var ACCENT_TARGETS = [
    '.btn-primary',
    '.plan-cta.featured-cta',
    '.cta-final-btn',
    '.mvp-btn-primary',
    '.contact-submit',
    '.seo-primary',
    '.support-button-primary',
    '.pricing-cta-section .cta-btn',
    '.info-button-primary'
  ].join(', ');

  function bindHover() {
    document.querySelectorAll(INTERACTIVE).forEach(function (el) {
      el.addEventListener('mouseenter', function () {
        ring.classList.add('cursor-hover');
        /* Si el elemento coincide con un CTA ámbar, añadir estado oscuro */
        if (el.matches(ACCENT_TARGETS)) {
          ring.classList.add('cursor-accent');
        }
      });
      el.addEventListener('mouseleave', function () {
        /* Quitar ambas clases en un único remove para no dejar estado residual */
        ring.classList.remove('cursor-hover', 'cursor-accent');
      });
    });
  }

  /* Con defer el DOM ya está disponible; el check es por robustez */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindHover);
  } else {
    bindHover();
  }
}());
