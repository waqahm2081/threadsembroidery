/* ============================================================
   CLIENT PORTAL v2 — client-portal.js
   Tab switching · filters · tracking · order modal · profile
   ============================================================ */

(function () {
  'use strict';

  const ORDER_DATA   = window.__CP_TRACKING_DATA__ || {};
  const ORDER_DETAIL = window.__CP_ORDER_DATA__ || {};
  const PROFILE_URL  = window.__CP_PROFILE_URL__ || '';
  const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')?.content || '';

  const ICON_MAP = {
    done:    'bi-check-lg',
    active:  'bi-arrow-repeat',
    pending: 'bi-clock'
  };

  const STATUS_LABELS = {
    processing: 'Processing',
    shipped:    'Shipped',
    delivered:  'Delivered',
    cancelled:  'Cancelled',
    pending:    'Pending',
  };

  let activeModalOrder = null;
  let activeOrderData  = null;


  /* ══════════════════════════════════════════════════════════
     1. TAB SWITCHING
  ══════════════════════════════════════════════════════════ */
  const navItems = document.querySelectorAll('.cp-nav-item');
  const panels   = document.querySelectorAll('.cp-tab-panel');

  function switchTab(tabId) {
    navItems.forEach(n => n.classList.toggle('active', n.dataset.tab === tabId));
    panels.forEach(p => {
      p.classList.toggle('active', p.id === `cp-tab-${tabId}`);
    });
    requestAnimationFrame(() => {
      window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
    });
  }

  navItems.forEach(item => {
    item.addEventListener('click', () => switchTab(item.dataset.tab));
  });

  document.querySelectorAll('[data-tab-link]').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.dataset.tabLink));
  });


  /* ══════════════════════════════════════════════════════════
     2. ORDER TRACKING
  ══════════════════════════════════════════════════════════ */
  const trackSelect    = document.getElementById('cpTrackSelect');
  const trackResult    = document.getElementById('cpTrackResult');
  const trackEmpty     = document.getElementById('cpTrackEmpty');
  const trackOrderNum  = document.getElementById('cpTrackOrderNum');
  const trackOrderDesc = document.getElementById('cpTrackOrderDesc');
  const trackBadge     = document.getElementById('cpTrackStatusBadge');
  const timeline       = document.getElementById('cpTimeline');
  const etaDate        = document.getElementById('cpEtaDate');

  function renderTracking(orderKey) {
    const data = ORDER_DATA[orderKey];
    if (!data) return;

    if (trackOrderNum)  trackOrderNum.textContent  = `#${orderKey}`;
    if (trackOrderDesc) trackOrderDesc.textContent = data.desc;
    if (trackBadge) {
      trackBadge.className   = `cp-status cp-status--${data.status}`;
      trackBadge.textContent = STATUS_LABELS[data.status] || data.status;
    }

    if (timeline) {
      timeline.innerHTML = data.steps.map(step => `
        <div class="cp-timeline-item">
          <div class="cp-timeline-left">
            <div class="cp-timeline-dot cp-timeline-dot--${step.state}">
              <i class="bi ${ICON_MAP[step.state]}"></i>
            </div>
            <div class="cp-timeline-line"></div>
          </div>
          <div class="cp-timeline-content">
            <div class="cp-timeline-event${step.state === 'pending' ? ' cp-timeline-event--pending' : ''}">${step.event}</div>
            <div class="cp-timeline-date">${step.date}</div>
          </div>
        </div>
      `).join('');
    }

    if (etaDate) etaDate.textContent = data.eta;
    if (trackResult) trackResult.style.display = 'block';
    if (trackEmpty)  trackEmpty.style.display  = 'none';
  }

  function goToTrack(orderNum) {
    const key = String(orderNum).replace('#', '');
    switchTab('track');
    if (trackSelect) {
      trackSelect.value = key;
      renderTracking(key);
    }
  }

  if (trackSelect) {
    trackSelect.addEventListener('change', () => {
      const val = trackSelect.value;
      if (val) {
        renderTracking(val);
      } else {
        if (trackResult) trackResult.style.display = 'none';
        if (trackEmpty)  trackEmpty.style.display  = 'flex';
      }
    });
  }

  document.querySelectorAll('.cp-btn-track').forEach(btn => {
    btn.addEventListener('click', () => goToTrack(btn.dataset.order));
  });


  /* ══════════════════════════════════════════════════════════
     3. ORDER DETAIL MODAL
  ══════════════════════════════════════════════════════════ */
  const modalOverlay  = document.getElementById('cpOrderModal');
  const modalTitle    = document.getElementById('cpModalTitle');
  const modalStatus   = document.getElementById('cpModalStatus');
  const modalBody     = document.getElementById('cpModalBody');
  const modalClose    = document.getElementById('cpModalClose');
  const modalCloseBtn = document.getElementById('cpModalCloseBtn');
  const modalTrackBtn = document.getElementById('cpModalTrackBtn');

  function buildModalContent(order) {
    const itemsHtml = (order.items || []).map(item => {
      const designBtn = item.has_design
        ? `<button type="button" class="cp-btn-view-design" data-design-id="${item.item_id}">
             <i class="bi bi-eye"></i> View Design
           </button>`
        : '';

      return `
      <div class="cp-modal-item">
        <div class="cp-modal-item-info">
          <div class="cp-modal-item-title">${escapeHtml(item.product_title)}</div>
          <div class="cp-modal-item-meta">
            ${item.category ? `Category: ${escapeHtml(item.category)}<br>` : ''}
            ${item.colour ? `Colour: ${escapeHtml(item.colour)} · ` : ''}
            ${item.size ? `Size: ${escapeHtml(item.size)} · ` : ''}
            Qty: ${item.quantity}
          </div>
          ${designBtn}
        </div>
        <span class="cp-modal-item-price">£${item.line_total}</span>
      </div>
    `;
    }).join('');

    const instructionsHtml = order.instructions
      ? `<div class="cp-modal-section-title">Special Instructions</div>
         <div class="cp-modal-instructions"><p>${order.instructions}</p></div>`
      : '';

    return `
      <div class="cp-modal-meta">
        <div class="cp-modal-meta-item">
          <span class="cp-modal-meta-label">Order Date</span>
          <span class="cp-modal-meta-val">${order.date_full || order.date}</span>
        </div>
        <div class="cp-modal-meta-item">
          <span class="cp-modal-meta-label">Total Items</span>
          <span class="cp-modal-meta-val">${order.total_qty || '—'}</span>
        </div>
        <div class="cp-modal-meta-item">
          <span class="cp-modal-meta-label">Est. Delivery</span>
          <span class="cp-modal-meta-val">${order.eta || '—'}</span>
        </div>
      </div>

      <div class="cp-modal-section-title">Order Items</div>
      <div class="cp-modal-items">${itemsHtml || '<p style="color:#aaa;font-size:0.85rem;">No items found.</p>'}</div>

      <div class="cp-modal-totals">
        <div class="cp-modal-total-row">
          <span>Subtotal (ex. VAT)</span>
          <strong>£${order.subtotal}</strong>
        </div>
        <div class="cp-modal-total-row">
          <span>VAT</span>
          <strong>£${order.vat_amount}</strong>
        </div>
        <div class="cp-modal-total-row cp-modal-total-row--grand">
          <span>Total (inc. VAT)</span>
          <strong>£${order.total}</strong>
        </div>
      </div>

      <div class="cp-modal-section-title">Shipping Address</div>
      <div class="cp-modal-shipping">
        <p>
          <strong>${order.name || ''}</strong><br>
          ${order.shipping_address || '—'}<br>
          ${order.phone && order.phone !== '—' ? `Tel: ${order.phone}` : ''}
        </p>
      </div>

      ${instructionsHtml}
    `;
  }

  function openOrderModal(orderNum) {
    const key = String(orderNum).replace('#', '');
    const order = ORDER_DETAIL[key];
    if (!order || !modalOverlay) return;

    activeModalOrder = key;
    activeOrderData  = order;

    if (modalTitle)  modalTitle.textContent  = `#${key}`;
    if (modalStatus) {
      modalStatus.className   = `cp-status cp-status--${order.status}`;
      modalStatus.textContent = order.status_label;
    }
    if (modalBody) modalBody.innerHTML = buildModalContent(order);

    modalOverlay.classList.add('is-open');
    modalOverlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeOrderModal() {
    if (!modalOverlay) return;
    modalOverlay.classList.remove('is-open');
    modalOverlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    activeModalOrder = null;
    activeOrderData  = null;
  }

  document.querySelectorAll('.cp-btn-view').forEach(btn => {
    btn.addEventListener('click', () => openOrderModal(btn.dataset.order));
  });

  if (modalClose)    modalClose.addEventListener('click', closeOrderModal);
  if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeOrderModal);

  if (modalTrackBtn) {
    modalTrackBtn.addEventListener('click', () => {
      if (activeModalOrder) {
        closeOrderModal();
        goToTrack(activeModalOrder);
      }
    });
  }

  if (modalOverlay) {
    modalOverlay.addEventListener('click', (e) => {
      if (e.target === modalOverlay) closeOrderModal();
    });
  }

  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (designOverlay?.classList.contains('is-open')) {
      closeDesignModal();
      return;
    }
    if (modalOverlay?.classList.contains('is-open')) {
      closeOrderModal();
    }
  });


  /* ══════════════════════════════════════════════════════════
     3b. DESIGN PREVIEW MODAL
  ══════════════════════════════════════════════════════════ */
  const designOverlay   = document.getElementById('cpDesignModalOverlay');
  const designGarment   = document.getElementById('cpDesignGarmentImg');
  const designLayers    = document.getElementById('cpDesignLayers');
  const designCanvas    = document.getElementById('cpDesignPreviewCanvas');
  const designTitle     = document.getElementById('cpDesignModalLabel');
  const designSubtitle  = document.getElementById('cpDesignSubtitle');
  const designDetails   = document.getElementById('cpDesignDetails');
  const designClose     = document.getElementById('cpDesignModalClose');

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
  }

  function resolveGarmentImage(preview, data, view) {
    const front = preview.customizer_image || data.customizer_image || '';
    const back = preview.customizer_image_back || data.customizer_image_back || front;
    return view === 'back' ? back : front;
  }

  function resolveLayerSrc(layer, storageBase) {
    if (layer.src) return layer.src;
    if (layer.asset_path) return storageBase + '/' + layer.asset_path;
    return '';
  }

  function hexToRgb(hex) {
    hex = (hex || '#ffffff').replace('#', '');
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    const num = parseInt(hex, 16);
    return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
  }

  function hexToHsl(hex) {
    let { r, g, b } = hexToRgb(hex);
    r /= 255; g /= 255; b /= 255;
    const max = Math.max(r, g, b);
    const min = Math.min(r, g, b);
    let h = 0, s = 0;
    const l = (max + min) / 2;
    const d = max - min;
    if (d !== 0) {
      s = d / (1 - Math.abs(2 * l - 1));
      switch (max) {
        case r: h = ((g - b) / d) % 6; break;
        case g: h = (b - r) / d + 2; break;
        case b: h = (r - g) / d + 4; break;
      }
      h *= 60;
      if (h < 0) h += 360;
    }
    return { h, s, l };
  }

  function computeGarmentFilter(hex) {
    if (!hex || hex.toLowerCase() === '#ffffff' || hex.toLowerCase() === '#fff') {
      return 'none';
    }
    const { h, s, l } = hexToHsl(hex);
    const baseLuminance = 0.85;
    const brightnessMult = l > 0
      ? Math.min(1.6, Math.max(0.18, l / baseLuminance))
      : 0.18;
    const saturateMult = 1 + (s * 6);
    return [
      'grayscale(1)',
      `brightness(${brightnessMult.toFixed(3)})`,
      'sepia(1)',
      `hue-rotate(${Math.round(h - 30)}deg)`,
      `saturate(${saturateMult.toFixed(2)})`,
    ].join(' ');
  }

  function buildLayerElement(layer, storageBase, scaleFactor) {
    const pa = window.TEDesignPrintArea;
    const el = document.createElement('div');
    el.className = 'cdm-layer-item';
    el.style.left = layer.x + '%';
    el.style.top = layer.y + '%';
    el.style.width = layer.w + '%';
    el.style.height = layer.h + '%';

    if (layer.type === 'image') {
      const img = document.createElement('img');
      img.src = resolveLayerSrc(layer, storageBase);
      img.alt = layer.name || 'Artwork';
      img.draggable = false;
      if (pa) pa.styleImageLayerElement(el, img);
      el.appendChild(img);
    } else {
      const type = pa ? pa.scaledTypography(layer, scaleFactor || 1) : {
        fontSize: layer.fontSize || 16,
        letterSpacing: layer.letterSpacing || 0,
      };
      const span = document.createElement('span');
      span.className = 'cdm-layer-text';
      span.textContent = layer.text || '';
      span.style.fontFamily = layer.font || 'sans-serif';
      span.style.fontSize = type.fontSize + 'px';
      span.style.fontWeight = layer.fontWeight === 'italic' ? '400' : (layer.fontWeight || '400');
      span.style.fontStyle = layer.fontWeight === 'italic' ? 'italic' : 'normal';
      span.style.letterSpacing = type.letterSpacing + 'px';
      span.style.color = layer.colour || '#fff';
      el.style.height = 'auto';
      el.style.display = 'flex';
      el.style.alignItems = 'center';
      el.style.justifyContent = 'center';
      el.appendChild(span);
    }
    return el;
  }

  function designDetailRow(label, value) {
    return `<div class="cdm-detail-row"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`;
  }

  function openDesignPreview(preview) {
    const data = preview.design_data || {};
    const pa = window.TEDesignPrintArea;
    const colourHex = preview.colour_hex || data.colour?.hex || '#ffffff';
    const colourName = preview.colour_name || data.colour?.name || '';
    const view = data.view || 'front';
    const printBounds = pa ? pa.resolveBounds(data.print_area) : null;
    const scaleFactor = pa
      ? pa.scaleFromReference(data.canvas_width || 620, designCanvas, 280)
      : 1;
    const layers = (data.layers || [])
      .filter(l => l.view === view)
      .filter(l => !pa || !printBounds || pa.intersects(l, printBounds));

    if (designTitle) {
      designTitle.innerHTML = '<i class="bi bi-palette"></i> ' + escapeHtml(preview.title);
    }
    if (designSubtitle) {
      designSubtitle.textContent = colourName
        ? colourName + ' garment · custom design preview'
        : 'Custom design preview';
    }

    if (designGarment) {
      designGarment.src = resolveGarmentImage(preview, data, view);
      designGarment.style.filter = computeGarmentFilter(colourHex);
    }

    if (designLayers) {
      designLayers.innerHTML = '';
      if (pa && printBounds) pa.applyClip(designLayers, printBounds);
      layers.forEach(layer => {
        designLayers.appendChild(buildLayerElement(layer, preview.storage_base, scaleFactor));
      });
    }

    if (designDetails) {
      const sizeLabel = preview.size_breakdown?.length
        ? preview.size_breakdown.map(r => `${r.size} × ${r.qty}`).join(', ')
        : (data.size || '—');
      designDetails.innerHTML = [
        designDetailRow('Garment', data.garment || preview.title),
        designDetailRow('Colour', colourName || '—'),
        designDetailRow('Sizes', sizeLabel),
        designDetailRow('Quantity', String(preview.quantity || data.qty || '—')),
        designDetailRow('Design Elements', String(layers.length)),
      ].join('');
    }
  }

  function openDesignModal() {
    if (!designOverlay) return;
    designOverlay.classList.add('is-open');
    designOverlay.setAttribute('aria-hidden', 'false');
  }

  function closeDesignModal() {
    if (!designOverlay) return;
    designOverlay.classList.remove('is-open');
    designOverlay.setAttribute('aria-hidden', 'true');
  }

  function showDesignPreview(itemId) {
    const item = activeOrderData?.items?.find(
      i => String(i.item_id) === String(itemId)
    );
    const preview = item?.design;
    if (!preview) return;

    openDesignPreview(preview);
    openDesignModal();
  }

  if (designClose) {
    designClose.addEventListener('click', closeDesignModal);
  }

  if (designOverlay) {
    designOverlay.addEventListener('click', (e) => {
      if (e.target === designOverlay) closeDesignModal();
    });
  }

  if (modalBody) {
    modalBody.addEventListener('click', (e) => {
      const btn = e.target.closest('.cp-btn-view-design');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();
      showDesignPreview(btn.dataset.designId);
    });
  }


  /* ══════════════════════════════════════════════════════════
     4. ORDER FILTERS + SEARCH
  ══════════════════════════════════════════════════════════ */
  const filterBtns  = document.querySelectorAll('.cp-filter-btn');
  const searchInput = document.getElementById('cpOrderSearch');
  let activeFilter = 'all';
  let searchQuery  = '';

  function getTableRows() {
    return document.querySelectorAll('#cpOrdersTable tbody tr[data-status]');
  }

  function applyFilters() {
    getTableRows().forEach(row => {
      const status     = row.dataset.status || '';
      const text       = row.textContent.toLowerCase();
      const passFilter = activeFilter === 'all' || status === activeFilter;
      const passSearch = searchQuery === '' || text.includes(searchQuery);
      row.style.display = (passFilter && passSearch) ? '' : 'none';
    });
  }

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeFilter = btn.dataset.filter;
      applyFilters();
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      searchQuery = searchInput.value.toLowerCase().trim();
      applyFilters();
    });
  }


  /* ══════════════════════════════════════════════════════════
     5. AVATAR PREVIEW
  ══════════════════════════════════════════════════════════ */
  const avatarInput      = document.getElementById('cpAvatarInput');
  const profileAvatarImg = document.getElementById('cpProfileAvatarImg');
  const headerAvatarImg  = document.getElementById('cpAvatarPreview');
  let pendingAvatarFile  = null;

  if (avatarInput) {
    avatarInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file || !file.type.startsWith('image/')) return;
      if (file.size > 2 * 1024 * 1024) {
        alert('Image must be under 2MB.');
        return;
      }
      pendingAvatarFile = file;
      const reader = new FileReader();
      reader.onload = function (e) {
        if (profileAvatarImg) profileAvatarImg.src = e.target.result;
        if (headerAvatarImg)  headerAvatarImg.src  = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }


  /* ══════════════════════════════════════════════════════════
     6. PASSWORD VISIBILITY TOGGLE
  ══════════════════════════════════════════════════════════ */
  document.querySelectorAll('.cp-pass-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      const isText = input.type === 'text';
      input.type = isText ? 'password' : 'text';
      btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
  });


  /* ══════════════════════════════════════════════════════════
     7. SAVE PROFILE
  ══════════════════════════════════════════════════════════ */
  const saveBtn      = document.getElementById('cpSaveBtn');
  const saveFeedback = document.getElementById('cpSaveFeedback');

  function showFeedback(message, isError) {
    if (!saveFeedback) return;
    saveFeedback.textContent = message;
    saveFeedback.classList.toggle('show', true);
    saveFeedback.classList.toggle('cp-save-feedback--error', !!isError);
    setTimeout(() => saveFeedback.classList.remove('show'), 3500);
  }

  if (saveBtn) {
    saveBtn.addEventListener('click', async () => {
      if (!PROFILE_URL) return;

      const passNew     = document.getElementById('cpPassNew')?.value || '';
      const passConfirm = document.getElementById('cpPassConfirm')?.value || '';

      if (passNew && passNew !== passConfirm) {
        showFeedback('Passwords do not match.', true);
        return;
      }

      const formData = new FormData();
      formData.append('first_name', document.getElementById('cpFirstName')?.value || '');
      formData.append('last_name', document.getElementById('cpLastName')?.value || '');
      formData.append('email', document.getElementById('cpEmail')?.value || '');
      formData.append('phone', document.getElementById('cpPhone')?.value || '');
      formData.append('address_line1', document.getElementById('cpAddress1')?.value || '');
      formData.append('address_line2', document.getElementById('cpAddress2')?.value || '');
      formData.append('city', document.getElementById('cpCity')?.value || '');
      formData.append('postcode', document.getElementById('cpPostcode')?.value || '');
      formData.append('country', document.getElementById('cpCountry')?.value || '');

      if (passNew) {
        formData.append('password', passNew);
        formData.append('password_confirmation', passConfirm);
      }

      if (pendingAvatarFile) {
        formData.append('avatar', pendingAvatarFile);
      }

      saveBtn.disabled = true;
      saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving…';

      try {
        const res = await fetch(PROFILE_URL, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json',
          },
          body: formData,
        });

        const data = await res.json();

        if (!res.ok) {
          const message = data.message
            || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Could not save profile.');
          throw new Error(message);
        }

        pendingAvatarFile = null;

        if (data.user) {
          const headerName  = document.querySelector('.cp-header-name');
          const headerEmail = document.querySelector('.cp-header-email');
          const nameAccent  = document.querySelector('.cp-name-accent');
          if (headerName)  headerName.textContent  = data.user.name;
          if (headerEmail) headerEmail.textContent = data.user.email;
          if (nameAccent)  nameAccent.textContent  = (data.user.name || '').split(' ')[0] || '';
          if (data.user.avatar_url) {
            if (profileAvatarImg) profileAvatarImg.src = data.user.avatar_url;
            if (headerAvatarImg)  headerAvatarImg.src  = data.user.avatar_url;
          }
        }

        const passNewEl     = document.getElementById('cpPassNew');
        const passConfirmEl = document.getElementById('cpPassConfirm');
        if (passNewEl)     passNewEl.value = '';
        if (passConfirmEl) passConfirmEl.value = '';

        showFeedback('✓ Changes saved successfully', false);
      } catch (err) {
        showFeedback(err.message || 'Could not save profile.', true);
      } finally {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Save Changes';
      }
    });
  }

})();
