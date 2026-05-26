/* public-cursor.js — Oxphyre
 * Cursor personalizado tipo anillo para páginas públicas ligeras.
 * No usa Three.js ni main.js.
 * Reglas:
 *  - Solo en pointer:fine (ratón/trackpad). En táctil no hace nada.
 *  - Cancela si #cursor-ring no existe en el DOM.
 *  - Mueve el anillo con mousemove.
 *  - Añade/quita .cursor-hover al pasar por elementos interactivos.
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

  /* Selector de elementos interactivos que amplían el anillo */
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

  function bindHover() {
    document.querySelectorAll(INTERACTIVE).forEach(function (el) {
      el.addEventListener('mouseenter', function () {
        ring.classList.add('cursor-hover');
      });
      el.addEventListener('mouseleave', function () {
        ring.classList.remove('cursor-hover');
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
