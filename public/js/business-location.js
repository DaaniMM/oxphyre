'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const btn      = document.getElementById('business-geocode-btn');
  const statusEl = document.getElementById('business-geocode-status');

  if (!btn || !statusEl) return;

  const bizSlug   = btn.dataset.bizSlug;
  const csrfToken = btn.dataset.csrfToken;

  if (!bizSlug || !csrfToken) return;

  const setStatus = (msg, type) => {
    statusEl.textContent = msg;
    statusEl.className   = 'business-geocode-status' + (type ? ` business-geocode-status--${type}` : '');
    statusEl.hidden      = msg === '';
  };

  btn.addEventListener('click', async () => {
    btn.disabled = true;
    setStatus('Buscando ubicación...', '');

    // Lee los valores actuales del formulario para geocodificar lo que el usuario ve,
    // no la versión guardada en BD (que puede estar desactualizada si no guardó).
    const address    = document.getElementById('edit-address')?.value     ?? '';
    const city       = document.getElementById('edit-city')?.value        ?? '';
    const postalCode = document.getElementById('edit-postal-code')?.value ?? '';
    const country    = document.getElementById('edit-country')?.value     ?? '';

    try {
      const response = await fetch(`/dashboard/negocios/${bizSlug}/geocode`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept':        'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          csrf_token:  csrfToken,
          address,
          city,
          postal_code: postalCode,
          country,
        }),
      });

      const payload = await response.json();

      if (!response.ok || !payload.success) {
        setStatus(
          payload.message || 'No hemos encontrado esa dirección. Prueba a escribirla de otra forma o añade la ciudad al final.',
          'error'
        );
      } else {
        setStatus(payload.message || 'Ubicación encontrada. Ya podremos mostrarla en tu tour.', 'success');
      }
    } catch {
      setStatus('No hemos podido conectar con el servicio de mapas. Inténtalo de nuevo.', 'error');
    } finally {
      btn.disabled = false;
    }
  });
});
