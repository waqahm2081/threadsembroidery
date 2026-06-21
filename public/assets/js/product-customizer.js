/* =============================================
   THREADS EMBROIDERY & PRINT LTD
   product-customizer.js
   Full-width, header/footer-free customizer app
   (Lumise-style layout).

   Garment recolouring:
   We use a single white/light-coloured PNG
   (assets/images/customizer/tshirtcustom.png).
   A CSS filter chain (grayscale -> brightness ->
   sepia -> hue-rotate -> saturate) is computed
   from each swatch's HSL values and applied
   directly to the image, producing a realistic
   recoloured garment that keeps all of the
   original fabric shading — without needing
   per-colour or per-garment image sets.

   Handles: garment colour, view toggle (front/
   back), logo upload, multiple text layers,
   drag/resize layers, pricing summary, order
   details (size/qty), accordion option groups,
   and the add-to-cart preview modal.
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

  /* ============================================================
     0. STATE
     ============================================================ */
  const customizerData = window.__CUSTOMIZER_DATA__ || {};
  const initialColour = (customizerData.colours || []).find(c => c.code === customizerData.default_colour)
    || (customizerData.colours || [])[0]
    || { name: 'White', hex: '#ffffff', code: 'default' };

  const state = {
    garment: customizerData.type || 'garment',
    garmentLabel: customizerData.type || customizerData.title || 'Garment',
    basePrice: parseFloat(customizerData.min_price) || 6.50,
    colourName: initialColour.name,
    colourHex: initialColour.hex,
    colourCode: initialColour.code,
    size: customizerData.default_size || 'M',
    qty: customizerData.default_qty || 1,
    view: 'front',
    layers: [],
    selectedLayerId: null,
    nextId: 1
  };

  /* ============================================================
     1. ELEMENT REFERENCES
     ============================================================ */
  const swatchRow       = document.getElementById('pcSwatches');
  const colourNameEl    = document.getElementById('pcColourName');

  const sizeSelect      = document.getElementById('pcSizeSelect');
  const qtyInput        = document.getElementById('pcQtyInput');

  const topSize         = document.getElementById('pctSize');
  const topQty          = document.getElementById('pctQty');
  const topPrice        = document.getElementById('pctPrice');

  const viewToggle      = document.getElementById('pcViewToggle');       // left panel (if present)
  const viewToggleTop   = document.getElementById('pcViewToggleTop');    // canvas toolbar
  const undoBtn         = document.getElementById('pcUndoBtn');
  const resetBtn        = document.getElementById('pcResetBtn');

  const canvas          = document.getElementById('pcCanvas');
  const garmentImg      = document.getElementById('pcGarmentImg');
  const printArea       = document.getElementById('pcPrintArea');
  const layersContainer = document.getElementById('pcLayers');

  const accordion       = document.getElementById('pcAccordion');

  const uploadZone      = document.getElementById('pcUploadZone');
  const fileInput       = document.getElementById('pcFileInput');

  const textInput       = document.getElementById('pcTextInput');
  const fontSelect      = document.getElementById('pcFontSelect');
  const fontSizeRange   = document.getElementById('pcFontSizeRange');
  const fontSizeVal     = document.getElementById('pcFontSizeVal');
  const textColourRow   = document.getElementById('pcTextColourRow');
  const textColourCustom = document.getElementById('pcTextColourCustom');
  const fontWeightSelect = document.getElementById('pcFontWeightSelect');
  const letterSpacingRange = document.getElementById('pcLetterSpacingRange');
  const addTextBtn      = document.getElementById('pcAddTextBtn');

  const layersEmpty     = document.getElementById('pcLayersEmpty');
  const layersList      = document.getElementById('pcLayersList');
  const layersBadge     = document.getElementById('pcLayersBadge');

  const summaryGarment  = document.getElementById('pcSummaryGarment');
  const summaryColour   = document.getElementById('pcSummaryColour');
  const summaryElements = document.getElementById('pcSummaryElements');
  const summaryTotal    = document.getElementById('pcSummaryTotal');

  const addToCartBtn    = document.getElementById('pcAddToCartBtn');

  const pcmGarmentImg   = document.getElementById('pcmGarmentImg');
  const pcmLayers       = document.getElementById('pcmLayers');
  const pcmGarment      = document.getElementById('pcmGarment');
  const pcmColour       = document.getElementById('pcmColour');
  const pcmSize         = document.getElementById('pcmSize');
  const pcmQty          = document.getElementById('pcmQty');
  const pcmElements     = document.getElementById('pcmElements');
  const pcmTotal        = document.getElementById('pcmTotal');

  let activeTextColour  = '#ffffff';

  if (customizerData.customizer_image) {
    [garmentImg, pcmGarmentImg, document.getElementById('pclProductThumb')].forEach(el => {
      if (el) el.src = customizerData.customizer_image;
    });
  }

  if (initialColour.code && swatchRow) {
    swatchRow.querySelectorAll('.pcl-swatch').forEach(s => {
      s.classList.toggle('active', s.dataset.code === initialColour.code);
    });
    if (colourNameEl) colourNameEl.textContent = initialColour.name;
  }

  /* ============================================================
     1b. READ SIZE / QUANTITY FROM URL (set on the product page)
     ============================================================ */
  (function readOrderRecap() {
    const params = new URLSearchParams(window.location.search);
    const sizeParam = params.get('size');
    const qtyParam  = parseInt(params.get('qty'), 10);
    const colourParam = params.get('colour');

    if (sizeParam) state.size = sizeParam;
    if (!isNaN(qtyParam) && qtyParam > 0) state.qty = qtyParam;

    if (colourParam && customizerData.colours) {
      const match = customizerData.colours.find(c => c.code === colourParam);
      if (match) {
        state.colourName = match.name;
        state.colourHex = match.hex;
        state.colourCode = match.code;
        if (swatchRow) {
          swatchRow.querySelectorAll('.pcl-swatch').forEach(s => {
            s.classList.toggle('active', s.dataset.code === match.code);
          });
        }
        if (colourNameEl) colourNameEl.textContent = match.name;
      }
    }

    if (sizeSelect) {
      const hasOption = Array.from(sizeSelect.options).some(o => o.value === state.size);
      if (hasOption) sizeSelect.value = state.size;
      else state.size = sizeSelect.value;
    }
    if (qtyInput) qtyInput.value = state.qty;

    syncTopBar();
  })();

  function syncTopBar() {
    if (topSize) topSize.textContent = state.size;
    if (topQty)  topQty.textContent  = state.qty;
  }

  /* ============================================================
     2. ORDER DETAILS — size & quantity
     ============================================================ */
  if (sizeSelect) {
    sizeSelect.addEventListener('change', function () {
      state.size = sizeSelect.value;
      syncTopBar();
    });
  }
  if (qtyInput) {
    qtyInput.addEventListener('input', function () {
      let val = parseInt(qtyInput.value, 10);
      if (isNaN(val) || val < 1) val = 1;
      state.qty = val;
      syncTopBar();
    });
  }

  /* ============================================================
     3. ACCORDION — Colour / Upload / Text / Layers
     ============================================================ */
  if (accordion) {
    accordion.addEventListener('click', function (e) {
      const header = e.target.closest('.pcl-acc-header');
      if (!header) return;

      const targetId = header.dataset.accTarget;
      const body = document.getElementById(targetId);
      if (!body) return;

      const willOpen = !header.classList.contains('active');

      // Close all other panels (single-open accordion)
      accordion.querySelectorAll('.pcl-acc-header').forEach(h => h.classList.remove('active'));
      accordion.querySelectorAll('.pcl-acc-body').forEach(b => b.classList.remove('active'));

      if (willOpen) {
        header.classList.add('active');
        body.classList.add('active');
      }
    });
  }

  function openAccordionPanel(targetId) {
    if (!accordion) return;
    const header = accordion.querySelector(`[data-acc-target="${targetId}"]`);
    const body = document.getElementById(targetId);
    if (!header || !body) return;

    accordion.querySelectorAll('.pcl-acc-header').forEach(h => h.classList.remove('active'));
    accordion.querySelectorAll('.pcl-acc-body').forEach(b => b.classList.remove('active'));

    header.classList.add('active');
    body.classList.add('active');
  }

  /* ============================================================
     4. GARMENT COLOUR — Lumise-style live PNG recolouring
     ============================================================ */
  if (swatchRow) {
    swatchRow.addEventListener('click', function (e) {
      const swatch = e.target.closest('.pcl-swatch');
      if (!swatch) return;

      swatchRow.querySelectorAll('.pcl-swatch').forEach(s => s.classList.remove('active'));
      swatch.classList.add('active');

      state.colourName = swatch.dataset.colour;
      state.colourHex  = swatch.dataset.hex;

      if (colourNameEl) colourNameEl.textContent = state.colourName;

      applyColourToGarment();
      updateSummary();
    });
  }

  /* The garment PNG is a white/light garment with natural shading baked
     in. To recolour it for any swatch we run a CSS filter chain that:
       1. desaturates the source image,
       2. shifts it toward the target hue,
       3. saturates it to the target colour's intensity,
       4. brightens/darkens it to roughly match the target lightness.
     This produces a realistic recoloured garment that keeps all of the
     original fabric shading and folds. */
  function applyColourToGarment() {
    const filter = computeGarmentFilter(state.colourHex);
    if (garmentImg) garmentImg.style.filter = filter;
    if (pcmGarmentImg) pcmGarmentImg.style.filter = filter;
  }

  function computeGarmentFilter(hex) {
    // White stays as the original, untouched image.
    if (hex.toLowerCase() === '#ffffff' || hex.toLowerCase() === '#fff') {
      return 'none';
    }

    const { h, s, l } = hexToHsl(hex);

    const targetLightness = l;          // 0–1
    const targetSaturation = s;         // 0–1

    // Map target lightness (0=black .. 1=white) to a brightness multiplier.
    // Source pixels (after grayscale) sit roughly around 0.85 luminance,
    // so scale relative to that.
    const baseLuminance = 0.85;
    let brightnessMult = targetLightness > 0
      ? Math.min(1.6, Math.max(0.18, targetLightness / baseLuminance))
      : 0.18;

    // Saturation boost — scale 0–1 swatch saturation to a CSS saturate()
    // multiplier; richer colours need a bigger boost since the source
    // is desaturated first.
    const saturateMult = 1 + (targetSaturation * 6);

    return [
      'grayscale(1)',
      `brightness(${brightnessMult.toFixed(3)})`,
      `sepia(1)`,
      `hue-rotate(${Math.round(h - 30)}deg)`,
      `saturate(${saturateMult.toFixed(2)})`
    ].join(' ');
  }

  /* ── Colour helpers ── */
  function hexToRgb(hex) {
    hex = hex.replace('#', '');
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

  /* ============================================================
     5. VIEW TOGGLE (Front / Back) — synced left panel + toolbar
     ============================================================ */
  function setView(view) {
    state.view = view;

    [viewToggle, viewToggleTop].forEach(toggle => {
      if (!toggle) return;
      toggle.querySelectorAll('[data-view]').forEach(b => {
        b.classList.toggle('active', b.dataset.view === view);
      });
    });

    renderLayers();
  }

  if (viewToggle) {
    viewToggle.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-view]');
      if (!btn) return;
      setView(btn.dataset.view);
    });
  }
  if (viewToggleTop) {
    viewToggleTop.addEventListener('click', function (e) {
      const btn = e.target.closest('[data-view]');
      if (!btn) return;
      setView(btn.dataset.view);
    });
  }

  /* ============================================================
     6. UPLOAD — drag/drop + file browse
     ============================================================ */
  if (uploadZone && fileInput) {
    uploadZone.addEventListener('click', function (e) {
      if (e.target.closest('.pcl-upload-btn')) return;
      fileInput.click();
    });

    ['dragenter', 'dragover'].forEach(evt => {
      uploadZone.addEventListener(evt, function (e) {
        e.preventDefault();
        uploadZone.classList.add('dragover');
      });
    });
    ['dragleave', 'drop'].forEach(evt => {
      uploadZone.addEventListener(evt, function (e) {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
      });
    });
    uploadZone.addEventListener('drop', function (e) {
      const files = e.dataTransfer.files;
      handleFiles(files);
    });

    fileInput.addEventListener('change', function () {
      handleFiles(fileInput.files);
      fileInput.value = '';
    });
  }

  function handleFiles(files) {
    if (!files || !files.length) return;
    Array.from(files).forEach(file => {
      if (!file.type.match(/^image\/(png|jpeg|jpg|svg\+xml)/)) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        addImageLayer(e.target.result, file.name);
      };
      reader.readAsDataURL(file);
    });
  }

  function addImageLayer(src, name) {
    const layer = {
      id: state.nextId++,
      type: 'image',
      view: state.view,
      x: 32, y: 28,
      w: 36, h: 36,
      src: src,
      name: name || 'Uploaded artwork'
    };
    state.layers.push(layer);
    state.selectedLayerId = layer.id;
    renderLayers();
    renderLayersList();
    updateSummary();
    openAccordionPanel('pcAccLayers');
  }

  /* ============================================================
     7. TEXT TOOL — supports adding multiple independent text layers
     ============================================================ */
  if (fontSizeRange && fontSizeVal) {
    fontSizeRange.addEventListener('input', function () {
      fontSizeVal.textContent = fontSizeRange.value;
      updateSelectedTextLive();
    });
  }

  if (textColourRow) {
    textColourRow.addEventListener('click', function (e) {
      const swatch = e.target.closest('.pcl-text-colour');
      if (!swatch) return;
      textColourRow.querySelectorAll('.pcl-text-colour').forEach(s => s.classList.remove('active'));
      swatch.classList.add('active');
      activeTextColour = swatch.dataset.hex;
      updateSelectedTextLive();
    });
  }
  if (textColourCustom) {
    textColourCustom.addEventListener('input', function () {
      textColourRow.querySelectorAll('.pcl-text-colour').forEach(s => s.classList.remove('active'));
      activeTextColour = textColourCustom.value;
      updateSelectedTextLive();
    });
  }
  if (fontSelect)    fontSelect.addEventListener('change', updateSelectedTextLive);
  if (fontWeightSelect) fontWeightSelect.addEventListener('change', updateSelectedTextLive);
  if (letterSpacingRange) letterSpacingRange.addEventListener('input', updateSelectedTextLive);

  // Live-edit only applies to an already-selected TEXT layer, so users
  // can tweak the styling of a layer they've picked from the Layers list.
  function updateSelectedTextLive() {
    const layer = getSelectedLayer();
    if (!layer || layer.type !== 'text') return;

    layer.font          = fontSelect.value;
    layer.fontSize      = parseInt(fontSizeRange.value, 10);
    layer.fontWeight     = fontWeightSelect.value;
    layer.letterSpacing = parseInt(letterSpacingRange.value, 10);
    layer.colour        = activeTextColour;

    renderLayers();
  }

  // Each click on "Add Text to Design" creates a brand new text layer
  // using the current style controls — multiple text layers can coexist.
  if (addTextBtn) {
    addTextBtn.addEventListener('click', function () {
      const text = (textInput.value || '').trim();
      if (!text) {
        textInput.focus();
        textInput.classList.add('invalid-shake');
        setTimeout(() => textInput.classList.remove('invalid-shake'), 400);
        return;
      }

      // Stagger new layers slightly so they don't all stack exactly on
      // top of one another.
      const offset = (state.layers.filter(l => l.type === 'text' && l.view === state.view).length % 5) * 4;

      const layer = {
        id: state.nextId++,
        type: 'text',
        view: state.view,
        x: 28 + offset, y: 42 + offset,
        w: 44, h: 14,
        text: text,
        font: fontSelect.value,
        fontSize: parseInt(fontSizeRange.value, 10),
        fontWeight: fontWeightSelect.value,
        letterSpacing: parseInt(letterSpacingRange.value, 10),
        colour: activeTextColour
      };

      state.layers.push(layer);
      state.selectedLayerId = layer.id;
      renderLayers();
      renderLayersList();
      updateSummary();

      textInput.value = '';
      openAccordionPanel('pcAccLayers');
    });
  }

  /* ============================================================
     8. LAYER RENDERING (canvas)
     ============================================================ */
  function getSelectedLayer() {
    return state.layers.find(l => l.id === state.selectedLayerId) || null;
  }

  function buildLayerElement(layer, isSelected) {
    const el = document.createElement('div');
    el.className = 'pcc-layer-item';
    if (isSelected) el.classList.add('selected');
    el.dataset.id = layer.id;

    el.style.left   = layer.x + '%';
    el.style.top    = layer.y + '%';
    el.style.width  = layer.w + '%';
    el.style.height = layer.h + '%';

    if (layer.type === 'image') {
      const img = document.createElement('img');
      img.src = layer.src;
      img.alt = layer.name || 'Custom artwork';
      img.draggable = false;
      el.appendChild(img);
    } else {
      const span = document.createElement('span');
      span.className = 'pcc-layer-text';
      span.textContent = layer.text;
      span.style.fontFamily = layer.font;
      span.style.fontSize = layer.fontSize + 'px';
      span.style.fontWeight = (layer.fontWeight === 'italic') ? '400' : layer.fontWeight;
      span.style.fontStyle = (layer.fontWeight === 'italic') ? 'italic' : 'normal';
      span.style.letterSpacing = layer.letterSpacing + 'px';
      span.style.color = layer.colour;
      el.appendChild(span);
      el.style.height = 'auto';
      el.style.display = 'flex';
      el.style.alignItems = 'center';
    }

    return el;
  }

  function renderLayers() {
    layersContainer.innerHTML = '';

    state.layers
      .filter(l => l.view === state.view)
      .forEach(layer => {
        const el = buildLayerElement(layer, layer.id === state.selectedLayerId);

        const handle = document.createElement('span');
        handle.className = 'pcc-resize-handle';
        el.appendChild(handle);

        const removeBtn = document.createElement('span');
        removeBtn.className = 'pcc-layer-remove';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        el.appendChild(removeBtn);

        layersContainer.appendChild(el);

        el.addEventListener('pointerdown', (e) => onLayerPointerDown(e, layer, el));

        el.addEventListener('click', (e) => {
          e.stopPropagation();
          selectLayer(layer.id);
        });

        handle.addEventListener('pointerdown', (e) => onResizePointerDown(e, layer, el));

        removeBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          removeLayer(layer.id);
        });
      });
  }

  // Toggle the `.selected` class on existing canvas layer elements without
  // rebuilding the DOM. A full renderLayers() rebuild detaches the element
  // currently being dragged/resized (its drag handlers hold a reference to
  // the OLD node), so selection changes must NOT trigger a rebuild while a
  // drag/resize could be starting on that same element.
  function updateSelectionVisuals() {
    layersContainer.querySelectorAll('.pcc-layer-item').forEach(el => {
      const id = parseInt(el.dataset.id, 10);
      el.classList.toggle('selected', id === state.selectedLayerId);
    });
  }

  function selectLayer(id) {
    state.selectedLayerId = id;
    updateSelectionVisuals();
    renderLayersList();

    const layer = getSelectedLayer();
    if (layer && layer.type === 'text') {
      textInput.value = layer.text;
      fontSelect.value = layer.font;
      fontSizeRange.value = layer.fontSize;
      fontSizeVal.textContent = layer.fontSize;
      fontWeightSelect.value = layer.fontWeight;
      letterSpacingRange.value = layer.letterSpacing;
      activeTextColour = layer.colour;

      textColourRow.querySelectorAll('.pcl-text-colour').forEach(s => {
        s.classList.toggle('active', s.dataset.hex.toLowerCase() === layer.colour.toLowerCase());
      });

      openAccordionPanel('pcAccText');
    }
  }

  function removeLayer(id) {
    state.layers = state.layers.filter(l => l.id !== id);
    if (state.selectedLayerId === id) state.selectedLayerId = null;
    renderLayers();
    renderLayersList();
    updateSummary();
  }

  canvas.addEventListener('click', function (e) {
    if (e.target === canvas || e.target === garmentImg || e.target === printArea || e.target === layersContainer) {
      state.selectedLayerId = null;
      updateSelectionVisuals();
      renderLayersList();
    }
  });

  /* ============================================================
     9. DRAG TO MOVE — pointer events listened on `document`.
     Styles are applied directly and synchronously on every
     pointermove (no requestAnimationFrame batching) so the
     element visibly follows the pointer in real time, without
     needing a follow-up click/interaction to trigger a repaint.
     Pointer capture is intentionally NOT used — this avoids
     "Failed to execute 'setPointerCapture'" errors that can occur
     depending on how the pointerdown was dispatched.
     ============================================================ */
  function onLayerPointerDown(e, layer, el) {
    if (e.target.classList.contains('pcc-resize-handle') || e.target.classList.contains('pcc-layer-remove')) return;
    e.preventDefault();
    selectLayer(layer.id);

    const canvasRect = canvas.getBoundingClientRect();
    const startX = e.clientX;
    const startY = e.clientY;
    const startLeft = layer.x;
    const startTop = layer.y;

    function onMove(ev) {
      const dxPct = ((ev.clientX - startX) / canvasRect.width) * 100;
      const dyPct = ((ev.clientY - startY) / canvasRect.height) * 100;

      layer.x = clamp(startLeft + dxPct, -10, 100 - layer.w + 10);
      layer.y = clamp(startTop + dyPct, -10, 100 - layer.h + 10);

      el.style.left = layer.x + '%';
      el.style.top  = layer.y + '%';
    }

    function onUp() {
      document.removeEventListener('pointermove', onMove);
      document.removeEventListener('pointerup', onUp);
      document.removeEventListener('pointercancel', onUp);
    }

    document.addEventListener('pointermove', onMove);
    document.addEventListener('pointerup', onUp);
    document.addEventListener('pointercancel', onUp);
  }

  /* ============================================================
     10. RESIZE HANDLE — pointer events listened on `document`,
     same direct/synchronous style-update approach as dragging.
     ============================================================ */
  function onResizePointerDown(e, layer, el) {
    e.preventDefault();
    e.stopPropagation();
    selectLayer(layer.id);

    const canvasRect = canvas.getBoundingClientRect();
    const startX = e.clientX;
    const startY = e.clientY;
    const startW = layer.w;
    const startH = layer.h;
    const startFontSize = layer.fontSize;

    function onMove(ev) {
      const dxPct = ((ev.clientX - startX) / canvasRect.width) * 100;
      const dyPct = ((ev.clientY - startY) / canvasRect.height) * 100;

      if (layer.type === 'image') {
        layer.w = clamp(startW + dxPct, 6, 100);
        layer.h = clamp(startH + dyPct, 6, 100);
        el.style.width  = layer.w + '%';
        el.style.height = layer.h + '%';
      } else {
        const scale = Math.max(0.3, 1 + (dxPct / 100));
        layer.fontSize = clamp(Math.round(startFontSize * scale), 10, 120);
        const span = el.querySelector('.pcc-layer-text');
        if (span) span.style.fontSize = layer.fontSize + 'px';
        if (getSelectedLayer() === layer) {
          fontSizeRange.value = layer.fontSize;
          fontSizeVal.textContent = layer.fontSize;
        }
      }
    }

    function onUp() {
      document.removeEventListener('pointermove', onMove);
      document.removeEventListener('pointerup', onUp);
      document.removeEventListener('pointercancel', onUp);
    }

    document.addEventListener('pointermove', onMove);
    document.addEventListener('pointerup', onUp);
    document.addEventListener('pointercancel', onUp);
  }

  function clamp(val, min, max) {
    return Math.min(Math.max(val, min), max);
  }

  /* ============================================================
     11. LAYERS PANEL LIST
     ============================================================ */
  function renderLayersList() {
    if (!layersList) return;
    layersList.innerHTML = '';

    if (layersBadge) {
      layersBadge.textContent = state.layers.length;
      layersBadge.classList.toggle('has-items', state.layers.length > 0);
    }

    if (!state.layers.length) {
      layersEmpty.style.display = 'flex';
      layersList.style.display = 'none';
      return;
    }

    layersEmpty.style.display = 'none';
    layersList.style.display = 'flex';

    state.layers.forEach(layer => {
      const li = document.createElement('li');
      li.className = 'pcl-layer-row';
      if (layer.id === state.selectedLayerId) li.classList.add('selected');

      const thumb = document.createElement('span');
      thumb.className = 'pcl-layer-row-thumb';
      if (layer.type === 'image') {
        const img = document.createElement('img');
        img.src = layer.src;
        thumb.appendChild(img);
      } else {
        thumb.innerHTML = '<i class="bi bi-fonts"></i>';
      }

      const label = document.createElement('span');
      label.className = 'pcl-layer-row-label';
      const sideTag = layer.view === 'back' ? ' (Back)' : ' (Front)';
      label.textContent = (layer.type === 'image' ? (layer.name || 'Artwork') : layer.text) + sideTag;

      const del = document.createElement('button');
      del.className = 'pcl-layer-row-del';
      del.innerHTML = '<i class="bi bi-trash3"></i>';
      del.setAttribute('aria-label', 'Remove element');

      li.appendChild(thumb);
      li.appendChild(label);
      li.appendChild(del);
      layersList.appendChild(li);

      li.addEventListener('click', (e) => {
        if (e.target.closest('.pcl-layer-row-del')) return;
        if (layer.view !== state.view) {
          setView(layer.view);
        }
        selectLayer(layer.id);
        renderLayers();
      });

      del.addEventListener('click', (e) => {
        e.stopPropagation();
        removeLayer(layer.id);
      });
    });
  }

  /* ============================================================
     12. TOOLBAR ACTIONS — remove selected / clear all
     ============================================================ */
  if (undoBtn) {
    undoBtn.addEventListener('click', function () {
      if (state.selectedLayerId !== null) {
        removeLayer(state.selectedLayerId);
      }
    });
  }
  if (resetBtn) {
    resetBtn.addEventListener('click', function () {
      if (!state.layers.length) return;
      if (confirm('Remove all design elements from this garment?')) {
        state.layers = [];
        state.selectedLayerId = null;
        renderLayers();
        renderLayersList();
        updateSummary();
      }
    });
  }

  /* ============================================================
     13. PRICING & SUMMARY
     ============================================================ */
  const DECORATION_FEE_PER_ITEM = 1.50;

  function calculateUnitPrice() {
    const elementCount = state.layers.length;
    return state.basePrice + (elementCount * DECORATION_FEE_PER_ITEM);
  }

  function formatPrice(val) {
    const fixed = val.toFixed(2);
    const [pounds, pence] = fixed.split('.');
    return `£${pounds}<small>.${pence}</small>`;
  }

  function updateSummary() {
    const unitPrice = calculateUnitPrice();

    if (summaryGarment)  summaryGarment.textContent = state.garmentLabel;
    if (summaryColour)   summaryColour.textContent = state.colourName;
    if (summaryElements) summaryElements.textContent = state.layers.length;
    if (summaryTotal)    summaryTotal.innerHTML = formatPrice(unitPrice);

    if (topPrice) topPrice.textContent = `£${unitPrice.toFixed(2)}`;
  }

  /* ============================================================
     14. ADD TO CART — build preview modal
     The modal preview canvas shares the exact same aspect ratio
     (4:5) and the same .pcc-garment-img / .pcc-layer-item markup
     as the live canvas, so every layer's stored x/y/w/h percentages
     map to identical positions and proportions in both places.
     ============================================================ */
  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function () {
      buildPreviewModal();
    });
  }

  function buildPreviewModal() {
    if (pcmLayers) pcmLayers.innerHTML = '';

    // Keep the modal garment image in sync with the live canvas
    if (pcmGarmentImg) {
      pcmGarmentImg.style.filter = garmentImg.style.filter || 'none';
    }

    state.layers
      .filter(l => l.view === state.view)
      .forEach(layer => {
        const el = buildLayerElement(layer, false);
        if (pcmLayers) pcmLayers.appendChild(el);
      });

    pcmGarment.textContent = state.garmentLabel;
    pcmColour.textContent  = state.colourName;
    pcmSize.textContent    = state.size;
    pcmQty.textContent     = state.qty;
    pcmElements.textContent = state.layers.length;
    pcmTotal.innerHTML     = formatPrice(calculateUnitPrice() * state.qty);
  }

  /* ============================================================
     15. INITIALISE
     ============================================================ */
  applyColourToGarment();
  renderLayers();
  renderLayersList();
  updateSummary();

});