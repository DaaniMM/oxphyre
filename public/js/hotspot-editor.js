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
  let stageMode = null; // 'add' | 'edit'
  let activeTargetId = null;
  let activeArrowId = null;

  const setStatus = msg => {
    statusEl.textContent = msg;
  };

  const showEditor = () => {
    editorEl.hidden = false;
  };

  const showStage = () => {
    stageEl.hidden = false;
  };

  const hideStage = () => {
    stageEl.hidden = true;
  };

  const buildListUrl = () => {
    const url = new URL(config.endpoints.list, window.location.origin);
    url.searchParams.set('biz_slug', config.bizSlug);
    url.searchParams.set('tour_slug', config.tourSlug);
    url.searchParams.set('position_id', String(config.positionId));
    return url;
  };

  // Normaliza a camelCase y snake_case por si el backend cambia formato.
  const getArrowForTarget = targetId =>
    arrows.find(a => Number(a.targetPositionId ?? a.target_position_id) === targetId) ?? null;

  const renderTargetList = () => {
    listEl.innerHTML = '';

    if (!targets.length) {
      const empty = document.createElement('p');
      empty.className = 'navigation-arrows-empty';
      empty.textContent = 'Aún no hay más zonas a las que navegar. Añade al menos una zona más con panorámica para crear flechas de navegación.';
      listEl.appendChild(empty);
      return;
    }

    targets.forEach(target => {
      const arrow = getArrowForTarget(target.id);
      const hasArrow = arrow !== null;

      const item = document.createElement('div');
      item.className = 'navigation-arrow-item';

      const info = document.createElement('div');
      info.className = 'navigation-arrow-item-info';

      const nameEl = document.createElement('span');
      nameEl.className = 'navigation-arrow-title';
      nameEl.textContent = target.name || 'Zona del tour';

      const stateEl = document.createElement('span');
      stateEl.className = 'navigation-arrow-state' + (hasArrow ? ' is-linked' : '');
      stateEl.textContent = hasArrow ? 'Enlazada' : 'Sin flecha';

      info.append(nameEl, stateEl);

      const actionsEl = document.createElement('div');
      actionsEl.className = 'navigation-arrow-item-actions';

      if (!hasArrow) {
        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.className = 'db-btn-ghost navigation-arrow-action-btn';
        addBtn.textContent = 'Añadir flecha';
        addBtn.addEventListener('click', () => openStageForTarget(target.id, null));
        actionsEl.appendChild(addBtn);
      } else {
        const editBtn = document.createElement('button');
        editBtn.type = 'button';
        editBtn.className = 'db-btn-ghost navigation-arrow-action-btn';
        editBtn.textContent = 'Editar flecha';
        editBtn.addEventListener('click', () => openStageForTarget(target.id, arrow));
        actionsEl.appendChild(editBtn);

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'db-btn-ghost navigation-arrow-action-btn navigation-arrow-action-btn--danger';
        delBtn.textContent = 'Eliminar flecha';
        delBtn.addEventListener('click', () => deleteArrow(Number(arrow.id), item));
        actionsEl.appendChild(delBtn);
      }

      item.append(info, actionsEl);
      listEl.appendChild(item);
    });
  };

  const openStageForTarget = (targetId, existingArrow) => {
    stageMode = existingArrow ? 'edit' : 'add';
    activeTargetId = targetId;
    activeArrowId = existingArrow ? Number(existingArrow.id) : null;
    clearDraft();

    // Pre-llenar el select con solo este destino (ya está elegido desde el listado).
    targetSelect.innerHTML = '';
    const target = targets.find(t => t.id === targetId);
    const opt = document.createElement('option');
    opt.value = String(targetId);
    opt.textContent = target?.name || 'Zona del tour';
    targetSelect.appendChild(opt);

    if (existingArrow) {
      const tx = Number(existingArrow.textureX ?? existingArrow.texture_x);
      const ty = Number(existingArrow.textureY ?? existingArrow.texture_y);
      if (Number.isFinite(tx) && Number.isFinite(ty)) {
        updateDraftMarker({ x: tx, y: ty });
      }
    }

    showStage();
    stageEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    setStatus('Haz clic sobre la panorámica para colocar la flecha.');
  };

  const closeStage = () => {
    clearDraft();
    stageMode = null;
    activeTargetId = null;
    activeArrowId = null;
    hideStage();
    setStatus('Preparado para editar.');
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
      renderTargetList();
      showEditor();
      hideStage();
      setStatus('Preparado para editar.');
      return true;
    } catch {
      setStatus('No hemos podido cargar las flechas.');
      return false;
    } finally {
      openBtn.disabled = false;
    }
  };

  const handleStageClick = event => {
    const rect = imageEl.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;

    if (x < 0 || x > 1 || y < 0 || y > 1) return;

    updateDraftMarker({ x, y });
    setStatus('Pulsa "Guardar flecha" para confirmar.');
  };

  const saveDraft = async () => {
    if (!draftPoint || !activeTargetId) {
      setStatus('Haz clic sobre la imagen para colocar la flecha primero.');
      return;
    }

    saveBtn.disabled = true;
    setStatus('Guardando flecha...');

    try {
      let endpoint, body;

      if (stageMode === 'edit' && activeArrowId) {
        endpoint = config.endpoints.move;
        body = {
          csrf_token: config.csrfToken,
          biz_slug: config.bizSlug,
          tour_slug: config.tourSlug,
          position_id: config.positionId,
          hotspot_id: activeArrowId,
          texture_x: draftPoint.x,
          texture_y: draftPoint.y,
        };
      } else {
        endpoint = config.endpoints.create;
        body = {
          csrf_token: config.csrfToken,
          biz_slug: config.bizSlug,
          tour_slug: config.tourSlug,
          position_id: config.positionId,
          target_position_id: activeTargetId,
          texture_x: draftPoint.x,
          texture_y: draftPoint.y,
        };
      }

      const response = await fetch(endpoint, {
        method: 'POST',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(body),
      });
      const payload = await response.json();

      if (!response.ok || !payload.success) {
        setStatus(payload.message || 'No hemos podido guardar la flecha. Inténtalo de nuevo.');
        return;
      }

      closeStage();
      await loadEditorData();
      setStatus('Flecha guardada correctamente.');
    } catch {
      setStatus('No hemos podido guardar la flecha. Inténtalo de nuevo.');
    } finally {
      saveBtn.disabled = false;
    }
  };

  const deleteArrow = async (arrowId, itemEl) => {
    if (!confirm('¿Eliminar esta flecha de navegación?')) return;

    setStatus('Eliminando flecha...');
    itemEl.style.opacity = '0.5';
    itemEl.style.pointerEvents = 'none';

    try {
      const response = await fetch(config.endpoints.delete, {
        method: 'POST',
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({
          csrf_token: config.csrfToken,
          biz_slug: config.bizSlug,
          tour_slug: config.tourSlug,
          position_id: config.positionId,
          hotspot_id: arrowId,
        }),
      });
      const payload = await response.json();

      if (!response.ok || !payload.success) {
        setStatus(payload.message || 'No hemos podido eliminar la flecha. Inténtalo de nuevo.');
        itemEl.style.opacity = '';
        itemEl.style.pointerEvents = '';
        return;
      }

      await loadEditorData();
      setStatus('Flecha eliminada correctamente.');
    } catch {
      setStatus('No hemos podido eliminar la flecha. Inténtalo de nuevo.');
      itemEl.style.opacity = '';
      itemEl.style.pointerEvents = '';
    }
  };

  openBtn.addEventListener('click', async () => {
    if (await loadEditorData()) {
      showEditor();
    }
  });

  stageEl.addEventListener('click', handleStageClick);
  saveBtn.addEventListener('click', saveDraft);
  cancelBtn.addEventListener('click', closeStage);
});
