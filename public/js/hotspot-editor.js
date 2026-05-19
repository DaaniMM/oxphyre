'use strict';

document.addEventListener('DOMContentLoaded', () => {
  const config = window.OXPHYRE_HOTSPOT_EDITOR;
  const openBtn = document.getElementById('navigation-arrows-open');
  const statusEl = document.getElementById('navigation-arrows-status');
  const editorEl = document.getElementById('navigation-arrows-editor');
  const stageEl = document.getElementById('navigation-arrows-stage');
  const imageEl = document.getElementById('navigation-arrows-image');
  const markerEl = document.getElementById('navigation-arrows-marker');
  const formEl = document.getElementById('navigation-arrows-form');
  const targetSelect = document.getElementById('navigation-arrows-target');
  const saveBtn = document.getElementById('navigation-arrows-save');
  const cancelBtn = document.getElementById('navigation-arrows-cancel');
  const listEl = document.getElementById('navigation-arrows-list');

  if (!config?.canEdit || !openBtn || !statusEl || !editorEl || !stageEl || !imageEl || !markerEl || !formEl || !targetSelect || !saveBtn || !cancelBtn || !listEl) {
    return;
  }

  let arrows = [];
  let targets = [];
  let draftPoint = null;

  const setStatus = message => {
    statusEl.textContent = message;
  };

  const showEditor = () => {
    editorEl.hidden = false;
    editorEl.removeAttribute('hidden');
  };

  const buildListUrl = () => {
    const url = new URL(config.endpoints.list, window.location.origin);
    url.searchParams.set('biz_slug', config.bizSlug);
    url.searchParams.set('tour_slug', config.tourSlug);
    url.searchParams.set('position_id', String(config.positionId));
    return url;
  };

  const renderTargets = () => {
    targetSelect.innerHTML = '';

    targets.forEach(target => {
      const option = document.createElement('option');
      option.value = String(target.id);
      option.textContent = target.name || 'Zona del tour';
      targetSelect.appendChild(option);
    });
  };

  const renderArrows = () => {
    listEl.innerHTML = '';

    if (!arrows.length) {
      const empty = document.createElement('p');
      empty.className = 'navigation-arrows-empty';
      empty.textContent = 'Aún no hay flechas en esta zona.';
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

  const updateDraftMarker = point => {
    draftPoint = point;
    markerEl.hidden = false;
    markerEl.style.left = `${point.x * 100}%`;
    markerEl.style.top = `${point.y * 100}%`;
    formEl.hidden = false;
  };

  const clearDraft = () => {
    draftPoint = null;
    markerEl.hidden = true;
    formEl.hidden = true;
  };

  const loadEditorData = async () => {
    setStatus('Cargando flechas...');
    openBtn.disabled = true;

    try {
      const response = await fetch(buildListUrl(), {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
      });
      const payload = await response.json();

      if (!response.ok || !payload.success) {
        setStatus(payload.message || 'No hemos podido cargar las flechas.');
        return false;
      }

      arrows = payload.data?.arrows || [];
      targets = payload.data?.targets || [];
      renderTargets();
      renderArrows();
      showEditor();
      setStatus('Haz clic sobre la panorámica para colocar una flecha.');
      return true;
    } catch {
      setStatus('No hemos podido cargar las flechas.');
      return false;
    } finally {
      openBtn.disabled = false;
    }
  };

  // La vista de subida usa una imagen plana de la panorámica. El punto guardado
  // es relativo a esa imagen completa, que es el mismo contrato que consume el
  // visor público mediante texture_x/texture_y.
  const handleStageClick = event => {
    if (!targets.length) {
      setStatus('Aún no hay más zonas a las que navegar.');
      return;
    }

    const rect = imageEl.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;

    if (x < 0 || x > 1 || y < 0 || y > 1) {
      return;
    }

    updateDraftMarker({ x, y });
    setStatus('Elige a qué zona llevará esta flecha.');
  };

  const saveDraft = async () => {
    if (!draftPoint || !targetSelect.value) {
      setStatus('Elige un punto y una zona de destino.');
      return;
    }

    saveBtn.disabled = true;
    setStatus('Guardando flecha...');

    try {
      const response = await fetch(config.endpoints.create, {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          csrf_token: config.csrfToken,
          biz_slug: config.bizSlug,
          tour_slug: config.tourSlug,
          position_id: config.positionId,
          target_position_id: targetSelect.value,
          texture_x: draftPoint.x,
          texture_y: draftPoint.y,
        }),
      });
      const payload = await response.json();

      if (!response.ok || !payload.success) {
        setStatus('No hemos podido guardar la flecha. Inténtalo de nuevo.');
        return;
      }

      clearDraft();
      await loadEditorData();
      setStatus('Flecha guardada correctamente.');
    } catch {
      setStatus('No hemos podido guardar la flecha. Inténtalo de nuevo.');
    } finally {
      saveBtn.disabled = false;
    }
  };

  openBtn.addEventListener('click', async () => {
    if (await loadEditorData()) {
      showEditor();
    }
  });

  stageEl.addEventListener('click', handleStageClick);
  saveBtn.addEventListener('click', saveDraft);
  cancelBtn.addEventListener('click', () => {
    clearDraft();
    setStatus('Haz clic sobre la panorámica para colocar una flecha.');
  });
});
