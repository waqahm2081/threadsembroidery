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

  const pricing = customizerData.pricing || {};
  const PLACEMENT_FEES = pricing.placement_fees || (window.TEDesignPrintArea?.PLACEMENT_FEES || {
    left_chest: 3.50,
    back_print: 6.00,
    back_embroidery: 8.00,
  });
  const LOGO_SETUP_FEE = pricing.logo_setup_fee || 12.00;
  const hasLogoSetup = parseFloat(customizerData.logo_setup_fee || 0) > 0;

  const garmentImages = {
    front: customizerData.customizer_image || customizerData.fallback_customizer_front || '',
    back: customizerData.customizer_image_back || customizerData.fallback_customizer_back || '',
  };

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
    placement: null,
    placementLabel: null,
    allowedViews: null,
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

  const summaryGarment   = document.getElementById('pcSummaryGarment');
  const summaryColour    = document.getElementById('pcSummaryColour');
  const summaryPlacement = document.getElementById('pcSummaryPlacement');
  const summaryElements  = document.getElementById('pcSummaryElements');
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

  function getGarmentImageForView(view) {
    return view === 'back' ? garmentImages.back : garmentImages.front;
  }

  function updateGarmentImageSrc() {
    const src = getGarmentImageForView(state.view);
    if (!src) return;
    if (garmentImg) garmentImg.src = src;
    applyColourToGarment();
  }

  if (garmentImages.front) {
    const thumb = document.getElementById('pclProductThumb');
    if (thumb) thumb.src = garmentImages.front;
    updateGarmentImageSrc();
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

    if (customizerData.size_breakdown && customizerData.size_breakdown.length) {
      state.sizeBreakdown = customizerData.size_breakdown;
      state.qty = customizerData.size_breakdown.reduce((sum, r) => sum + (r.qty || 0), 0);
    }

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

  function getSizeDisplay() {
    if (customizerData.size_display) return customizerData.size_display;
    if (state.sizeBreakdown && state.sizeBreakdown.length) {
      return state.sizeBreakdown.map(r => `${r.size} × ${r.qty}`).join(', ');
    }
    return state.size;
  }

  function syncTopBar() {
    if (topSize) topSize.textContent = getSizeDisplay();
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
     4b. DECORATION PLACEMENT — modal on load
     ============================================================ */
  const pcApp = document.getElementById('pc-app');
  const placementModalEl = document.getElementById('pcPlacementModal');
  let placementModal = null;

  function getDefaultLayerPosition(type) {
    if (state.placement === 'left_chest') {
      return type === 'text'
        ? { x: 52, y: 28, w: 22, h: 12 }
        : { x: 54, y: 26, w: 16, h: 18 };
    }
    if (state.placement === 'back_print' || state.placement === 'back_embroidery') {
      return type === 'text'
        ? { x: 25, y: 28, w: 50, h: 14 }
        : { x: 22, y: 22, w: 56, h: 40 };
    }
    return type === 'text'
      ? { x: 28, y: 42, w: 44, h: 14 }
      : { x: 32, y: 28, w: 36, h: 36 };
  }

  function applyViewToggleRestrictions() {
    [viewToggle, viewToggleTop].forEach(toggle => {
      if (!toggle) return;
      toggle.querySelectorAll('[data-view]').forEach(btn => {
        const allowed = !state.allowedViews || state.allowedViews.includes(btn.dataset.view);
        btn.disabled = !allowed;
      });
    });
  }

  function applyPlacement(key) {
    const pa = window.TEDesignPrintArea;
    const placement = pa ? pa.getPlacement(key) : null;
    if (!placement) return;

    state.placement = key;
    state.placementLabel = placement.label;
    state.allowedViews = placement.allowedViews;

    if (printArea && pa) {
      pa.applyToElement(printArea, placement.printArea);
    }
    if (layersContainer && pa) {
      pa.applyClip(layersContainer, placement.printArea);
    }

    setView(placement.view);
    applyViewToggleRestrictions();

    if (pcApp) pcApp.classList.remove('pc-awaiting-placement');
    if (summaryPlacement) summaryPlacement.textContent = placement.label;

    state.layers = state.layers.filter(l => state.allowedViews.includes(l.view));
    renderLayers();
    renderLayersList();
    updateSummary();
  }

  function initPlacementModal() {
    if (pcApp) pcApp.classList.add('pc-awaiting-placement');

    if (!placementModalEl || typeof bootstrap === 'undefined') return;

    placementModal = new bootstrap.Modal(placementModalEl);
    placementModal.show();

    placementModalEl.querySelectorAll('[data-placement]').forEach(btn => {
      btn.addEventListener('click', function () {
        applyPlacement(btn.dataset.placement);
        placementModal.hide();
        updateSummary();
      });
    });
  }

  function requirePlacement() {
    if (state.placement) return true;
    if (placementModal) placementModal.show();
    return false;
  }

  /* ============================================================
     5. VIEW TOGGLE (Front / Back) — synced left panel + toolbar
     ============================================================ */
  function setView(view) {
    if (state.allowedViews && !state.allowedViews.includes(view)) return;

    state.view = view;
    updateGarmentImageSrc();

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
    if (!requirePlacement()) return;
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
    const pos = getDefaultLayerPosition('image');
    const layer = {
      id: state.nextId++,
      type: 'image',
      view: state.view,
      x: pos.x, y: pos.y,
      w: pos.w, h: pos.h,
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
      if (!requirePlacement()) return;

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
      const pos = getDefaultLayerPosition('text');

      const layer = {
        id: state.nextId++,
        type: 'text',
        view: state.view,
        x: pos.x + offset, y: pos.y + offset,
        w: pos.w, h: pos.h,
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

  function buildLayerElement(layer, isSelected, scaleFactor) {
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
      const pa = window.TEDesignPrintArea;
      if (pa) pa.styleImageLayerElement(el, img);
      el.appendChild(img);
    } else {
      const pa = window.TEDesignPrintArea;
      const type = pa ? pa.scaledTypography(layer, scaleFactor || 1) : {
        fontSize: layer.fontSize,
        letterSpacing: layer.letterSpacing,
      };
      const span = document.createElement('span');
      span.className = 'pcc-layer-text';
      span.textContent = layer.text;
      span.style.fontFamily = layer.font;
      span.style.fontSize = type.fontSize + 'px';
      span.style.fontWeight = (layer.fontWeight === 'italic') ? '400' : layer.fontWeight;
      span.style.fontStyle = (layer.fontWeight === 'italic') ? 'italic' : 'normal';
      span.style.letterSpacing = type.letterSpacing + 'px';
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
  function getPlacementFee() {
    if (!state.placement) return 0;
    const pa = window.TEDesignPrintArea;
    if (pa && typeof pa.getPlacementFee === 'function') {
      return pa.getPlacementFee(state.placement);
    }
    return PLACEMENT_FEES[state.placement] || 0;
  }

  function calculateUnitPriceExVat() {
    return state.basePrice + getPlacementFee();
  }

  function calculateLineSubtotalExVat() {
    return (calculateUnitPriceExVat() * state.qty) + (hasLogoSetup ? LOGO_SETUP_FEE : 0);
  }

  const VAT_NOTE_HTML = ' <small class="pc-vat-note">+ VAT</small>';

  function formatPrice(val) {
    const fixed = val.toFixed(2);
    const [pounds, pence] = fixed.split('.');
    return `£${pounds}<small>.${pence}</small>`;
  }

  function updateSummary() {
    const unitPrice = calculateUnitPriceExVat();
    const lineSubtotal = calculateLineSubtotalExVat();

    if (summaryGarment)  summaryGarment.textContent = state.garmentLabel;
    if (summaryColour)   summaryColour.textContent = state.colourName;
    if (summaryElements) summaryElements.textContent = state.layers.length;
    if (summaryTotal)    summaryTotal.innerHTML = formatPrice(lineSubtotal) + VAT_NOTE_HTML;

    if (topPrice) topPrice.textContent = `£${unitPrice.toFixed(2)} + VAT`;
  }

  /* ============================================================
     14. ADD TO CART — save design & show success modal
     ============================================================ */
  const previewModalEl = document.getElementById('pcPreviewModal');
  const pcmConfirmAddBtn = document.getElementById('pcmConfirmAddBtn');
  const pcmEditingActions = document.getElementById('pcmEditingActions');
  const pcmSuccessActions = document.getElementById('pcmSuccessActions');
  const pcmCheckoutNowBtn = document.getElementById('pcmCheckoutNowBtn');
  const pcmViewCartBtn = document.getElementById('pcmViewCartBtn');
  let previewModal = null;

  if (previewModalEl) {
    previewModal = new bootstrap.Modal(previewModalEl);
    previewModalEl.addEventListener('hidden.bs.modal', () => {
      if (pcmEditingActions) pcmEditingActions.style.display = '';
      if (pcmSuccessActions) pcmSuccessActions.style.display = 'none';
    });
    previewModalEl.addEventListener('shown.bs.modal', buildPreviewModal);
  }

  if (addToCartBtn) {
    addToCartBtn.addEventListener('click', function () {
      if (!requirePlacement()) return;
      if (previewModal) previewModal.show();
    });
  }

  if (pcmConfirmAddBtn) {
    pcmConfirmAddBtn.addEventListener('click', saveToCart);
  }

  async function saveToCart() {
    if (!customizerData.cart_item_id || !customizerData.customize_url) {
      alert('No cart item found. Please go back to the product page and try again.');
      return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const unitPrice = state.basePrice;
    const decorationFee = getPlacementFee();

    const designPayload = {
      garment: state.garmentLabel,
      colour: { name: state.colourName, hex: state.colourHex, code: state.colourCode },
      customizer_image: customizerData.customizer_image || null,
      customizer_image_back: customizerData.customizer_image_back || garmentImages.back || null,
      canvas_width: canvas.getBoundingClientRect().width,
      decoration_placement: state.placement,
      decoration_placement_label: state.placementLabel,
      print_area: getPrintAreaBounds(),
      size: state.size,
      qty: state.qty,
      view: state.view,
      layers: state.layers,
    };

    const formData = new FormData();
    formData.append('design_data', JSON.stringify(designPayload));
    formData.append('unit_price', String(unitPrice));
    formData.append('decoration_fee', String(decorationFee));
    formData.append('quantity', String(state.qty));
    formData.append('size', state.size);
    formData.append('colour_name', state.colourName);

    try {
      const previewBlob = await exportPreviewBlob();
      if (previewBlob) {
        formData.append('preview_image', previewBlob, 'preview.png');
      }
    } catch {
      // Preview export is optional
    }

    pcmConfirmAddBtn.disabled = true;
    pcmConfirmAddBtn.classList.add('pcm-saving');
    pcmConfirmAddBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving…';

    try {
      const res = await fetch(customizerData.customize_url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData,
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.message || 'Failed to save design');
      }

      if (pcmCheckoutNowBtn) pcmCheckoutNowBtn.href = data.checkout_url;
      if (pcmViewCartBtn) pcmViewCartBtn.href = data.cart_url;

      if (pcmEditingActions) pcmEditingActions.style.display = 'none';
      if (pcmSuccessActions) pcmSuccessActions.style.display = 'flex';

      const modalTitle = document.getElementById('pcPreviewModalLabel');
      const modalSub = document.querySelector('.pcm-sub');
      if (modalTitle) {
        modalTitle.innerHTML = '<i class="bi bi-check-circle-fill"></i> Added to Cart!';
      }
      if (modalSub) {
        modalSub.textContent = 'Your custom design has been saved successfully.';
      }
    } catch (err) {
      alert(err.message || 'Could not save your design. Please try again.');
    } finally {
      pcmConfirmAddBtn.disabled = false;
      pcmConfirmAddBtn.classList.remove('pcm-saving');
      pcmConfirmAddBtn.innerHTML = '<i class="bi bi-cart-plus"></i> Confirm &amp; Add to Cart';
    }
  }

  function getPrintAreaBounds() {
    const pa = window.TEDesignPrintArea;
    if (state.placement && pa) {
      const placement = pa.getPlacement(state.placement);
      if (placement) return { ...placement.printArea };
    }
    if (!pa) return { x: 57, y: 24, w: 18, h: 20, centered: false };
    return pa.fromElements(canvas, printArea);
  }

  async function exportPreviewBlob() {
    const wrap = document.getElementById('pcmPreviewCanvas');
    if (!wrap) return null;

    const canvas = document.createElement('canvas');
    const w = 400;
    const h = 500;
    canvas.width = w;
    canvas.height = h;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#f0f0f0';
    ctx.fillRect(0, 0, w, h);

    const garment = wrap.querySelector('#pcmGarmentImg');
    if (garment && garment.complete) {
      await drawImageToCanvas(ctx, garment, 0, 0, w, h);
    }

    const previewW = wrap.getBoundingClientRect().width || 260;
    const exportScale = w / previewW;

    const bounds = getPrintAreaBounds();
    const pa = window.TEDesignPrintArea;
    const clip = pa ? pa.clipRect(w, h, bounds) : null;

    if (clip) {
      ctx.save();
      ctx.beginPath();
      ctx.rect(clip.x, clip.y, clip.w, clip.h);
      ctx.clip();
    }

    const layerEls = wrap.querySelectorAll('.pcc-layer-item');
    for (const el of layerEls) {
      const img = el.querySelector('img');
      if (img) {
        const lx = (parseFloat(el.style.left) / 100) * w;
        const ly = (parseFloat(el.style.top) / 100) * h;
        const lw = (parseFloat(el.style.width) / 100) * w;
        const lh = (parseFloat(el.style.height) / 100) * h;
        await drawImageToCanvas(ctx, img, lx, ly, lw, lh);
      } else {
        const span = el.querySelector('.pcc-layer-text');
        const lx = (parseFloat(el.style.left) / 100) * w;
        const ly = (parseFloat(el.style.top) / 100) * h;
        const lw = (parseFloat(el.style.width) / 100) * w;
        const domFontSize = parseInt(span?.style.fontSize, 10) || 16;
        const fontSize = Math.max(1, Math.round(domFontSize * exportScale));
        ctx.fillStyle = span?.style.color || el.style.color || '#fff';
        ctx.font = `${span?.style.fontStyle === 'italic' ? 'italic ' : ''}${span?.style.fontWeight || 'normal'} ${fontSize}px ${span?.style.fontFamily || 'sans-serif'}`;
        ctx.textAlign = 'center';
        ctx.fillText((span?.textContent || el.textContent || '').trim(), lx + lw / 2, ly + fontSize);
      }
    }

    if (clip) ctx.restore();

    return new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
  }

  function drawImageToCanvas(ctx, img, x, y, w, h) {
    return new Promise(resolve => {
      if (img.complete && img.naturalWidth) {
        ctx.drawImage(img, x, y, w, h);
        resolve();
      } else {
        img.onload = () => { ctx.drawImage(img, x, y, w, h); resolve(); };
        img.onerror = resolve;
      }
    });
  }

  function buildPreviewModal() {
    if (pcmEditingActions) pcmEditingActions.style.display = '';
    if (pcmSuccessActions) pcmSuccessActions.style.display = 'none';

    const modalTitle = document.getElementById('pcPreviewModalLabel');
    const modalSub = document.querySelector('.pcm-sub');
    if (modalTitle) modalTitle.innerHTML = '<i class="bi bi-eye"></i> Preview Your Design';
    if (modalSub) modalSub.textContent = 'Review your design before adding to cart';

    if (pcmLayers) pcmLayers.innerHTML = '';

    const printBounds = getPrintAreaBounds();
    const pa = window.TEDesignPrintArea;
    const previewCanvas = document.getElementById('pcmPreviewCanvas');
    const scaleFactor = pa ? pa.previewScaleFactor(canvas, previewCanvas, 260) : 1;
    if (pcmGarmentImg) {
      pcmGarmentImg.src = getGarmentImageForView(state.view);
      pcmGarmentImg.style.filter = garmentImg?.style.filter || 'none';
    }

    if (pcmLayers && pa) {
      pa.applyClip(pcmLayers, printBounds);
    }

    const visibleLayers = state.layers
      .filter(l => l.view === state.view)
      .filter(l => !pa || pa.intersects(l, printBounds));

    visibleLayers.forEach(layer => {
      const el = buildLayerElement(layer, false, scaleFactor);
      if (pcmLayers) pcmLayers.appendChild(el);
    });

    pcmGarment.textContent = state.garmentLabel;
    pcmColour.textContent  = state.colourName;
    pcmSize.textContent    = getSizeDisplay();
    pcmQty.textContent     = state.qty;
    pcmElements.textContent = visibleLayers.length;
    pcmTotal.innerHTML     = formatPrice(calculateLineSubtotalExVat()) + VAT_NOTE_HTML;
  }

  /* ============================================================
     15. INITIALISE
     ============================================================ */
  applyColourToGarment();
  renderLayers();
  renderLayersList();
  updateSummary();
  initPlacementModal();

});