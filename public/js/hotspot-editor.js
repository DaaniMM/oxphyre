'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const config = window.OXPHYRE_HOTSPOT_EDITOR;
  const openBtn = document.getElementById('navigation-arrows-open');
  const statusEl = document.getElementById('navigation-arrows-status');
  const listEl = document.getElementById('navigation-arrows-list');

  if (!config?.canEdit || !openBtn || !statusEl || !listEl) {
    return;
  }

  const buildListUrl = () => {
    const url = new URL(config.endpoints.list, window.location.origin);
    url.searchParams.set('biz_slug', config.bizSlug);
    url.searchParams.set('tour_slug', config.tourSlug);
    url.searchParams.set('position_id', String(config.positionId));
    return url;
  };

  const renderArrows = arrows => {
    listEl.innerHTML = '';
    listEl.hidden = false;

    if (!arrows.length) {
      const empty = document.createElement('p');
      empty.className = 'navigation-arrows-empty';
      empty.textContent = 'Todavía no hay flechas de navegación en esta zona.';
      listEl.appendChild(empty);
      return;
    }

    arrows.forEach(arrow => {
      const item = document.createElement('div');
      item.className = 'navigation-arrow-item';

      const title = document.createElement('span');
      title.className = 'navigation-arrow-title';
      title.textContent = arrow.targetPositionName || 'Zona del tour';

      const state = document.createElement('span');
      state.className = arrow.isActive ? 'navigation-arrow-state is-active' : 'navigation-arrow-state';
      state.textContent = arrow.isActive ? 'Activa' : 'Pausada';

      item.append(title, state);
      listEl.appendChild(item);
    });
  };

  const loadArrows = async () => {
    statusEl.textContent = 'Cargando flechas...';
    openBtn.disabled = true;

    try {
      const response = await fetch(buildListUrl(), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      const payload = await response.json();

      if (!response.ok || !payload.success) {
        statusEl.textContent = payload.message || 'No hemos podido cargar las flechas.';
        return;
      }

      renderArrows(payload.data?.arrows || []);
      statusEl.textContent = 'Flechas listas para editar.';
    } catch {
      statusEl.textContent = 'No hemos podido cargar las flechas.';
    } finally {
      openBtn.disabled = false;
    }
  };

  openBtn.addEventListener('click', loadArrows);
});
