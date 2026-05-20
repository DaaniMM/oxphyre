'use strict';

(function () {
  const loc = TOUR_DATA && TOUR_DATA.location;
  if (!loc || !loc.hasCoords) return;

  const btn      = document.getElementById('tour-location-btn');
  const backdrop = document.getElementById('tour-location-backdrop');
  const sheet    = document.getElementById('tour-location-sheet');
  const closeBtn = document.getElementById('tour-location-close');
  const mapEl    = document.getElementById('tour-location-map');

  if (!btn || !backdrop || !sheet || !closeBtn || !mapEl) return;

  let map    = null;
  let isOpen = false;

  function openSheet() {
    if (isOpen) return;
    isOpen = true;
    backdrop.classList.add('is-open');
    sheet.classList.add('is-open');
    sheet.setAttribute('aria-hidden', 'false');
    document.body.classList.add('location-sheet-open');

    if (!map) {
      // Init Leaflet lazily la primera vez que se abre el sheet.
      // El timeout de 50ms garantiza que el contenedor ya tiene tamaño visible.
      setTimeout(() => {
        map = L.map(mapEl, { zoomControl: true })
          .setView([loc.lat, loc.lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors',
          maxZoom: 19,
        }).addTo(map);
        L.marker([loc.lat, loc.lng]).addTo(map);
        setTimeout(() => map.invalidateSize(), 350);
      }, 50);
    } else {
      // En aperturas posteriores el mapa ya existe; invalidateSize tras la animación CSS.
      setTimeout(() => map.invalidateSize(), 350);
    }
  }

  function closeSheet() {
    if (!isOpen) return;
    isOpen = false;
    backdrop.classList.remove('is-open');
    sheet.classList.remove('is-open');
    sheet.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('location-sheet-open');
  }

  btn.addEventListener('click', openSheet);
  closeBtn.addEventListener('click', closeSheet);
  backdrop.addEventListener('click', closeSheet);
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && isOpen) closeSheet();
  });
})();
