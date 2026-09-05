/* ==============================================
   design-print-area.js
   Print area bounds & clipping for design previews
   ============================================== */
(function () {
  'use strict';

  const PLACEMENT_FEES = {
    left_chest: 3.50,
    right_chest: 3.50,
    front_print: 3.50,
    front_embroidery: 3.50,
    back_print: 6.00,
    back_embroidery: 8.00,
    apron_left: 3.50,
    apron_right: 3.50,
    apron_middle: 3.50,
    rucksack_top_print: 6.00,
    rucksack_top_embroidery: 3.50,
    bag_print: 6.00,
    keyring_print: 6.00,
    towel_embroidery: 3.50,
    full_print: 6.00,
  };

  const FULL = { x: 8, y: 8, w: 84, h: 84, centered: false };

  const PLACEMENTS = {
    left_chest: {
      label: 'Left Chest',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 51, y: 20, w: 18, h: 20, centered: false },
    },
    right_chest: {
      label: 'Right Chest',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 31, y: 20, w: 18, h: 20, centered: false },
    },
    front_print: {
      label: 'Front Print',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 50, y: 18, w: 34, h: 42, centered: true },
    },
    front_embroidery: {
      label: 'Front Embroidery',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 51, y: 20, w: 18, h: 20, centered: false },
    },
    back_print: {
      label: 'Back Print',
      view: 'back',
      allowedViews: ['back'],
      printArea: { x: 51, y: 12, w: 38, h: 84, centered: true },
    },
    back_embroidery: {
      label: 'Back Embroidery',
      view: 'back',
      allowedViews: ['back'],
      printArea: { x: 51, y: 12, w: 38, h: 84, centered: true },
    },
    apron_left: {
      label: 'Left Side',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 28, y: 28, w: 20, h: 24, centered: false },
    },
    apron_right: {
      label: 'Right Side',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 52, y: 28, w: 20, h: 24, centered: false },
    },
    apron_middle: {
      label: 'Centre',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 50, y: 26, w: 30, h: 32, centered: true },
    },
    rucksack_top_print: {
      label: 'Top Print',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 50, y: 12, w: 42, h: 32, centered: true },
    },
    rucksack_top_embroidery: {
      label: 'Top Embroidery',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 50, y: 14, w: 28, h: 22, centered: true },
    },
    bag_print: {
      label: 'Bag Print',
      view: 'front',
      allowedViews: ['front'],
      printArea: { ...FULL },
    },
    keyring_print: {
      label: 'Keyring Print',
      view: 'front',
      allowedViews: ['front'],
      printArea: { ...FULL },
    },
    towel_embroidery: {
      label: 'Towel Embroidery',
      view: 'front',
      allowedViews: ['front'],
      printArea: { x: 50, y: 34, w: 36, h: 28, centered: true },
    },
    full_print: {
      label: 'Full Product Print',
      view: 'front',
      allowedViews: ['front', 'back'],
      printArea: { ...FULL },
    },
  };

  const DEFAULT = PLACEMENTS.full_print.printArea;

  function fromElements(canvasEl, printAreaEl) {
    if (!canvasEl || !printAreaEl) return { ...DEFAULT };

    const cRect = canvasEl.getBoundingClientRect();
    const pRect = printAreaEl.getBoundingClientRect();

    if (!cRect.width || !cRect.height) return { ...DEFAULT };

    return {
      x: ((pRect.left - cRect.left) / cRect.width) * 100,
      y: ((pRect.top - cRect.top) / cRect.height) * 100,
      w: (pRect.width / cRect.width) * 100,
      h: (pRect.height / cRect.height) * 100,
      centered: false,
    };
  }

  function applyToElement(el, bounds) {
    if (!el || !bounds) return;
    el.style.top = bounds.y + '%';
    el.style.left = bounds.x + '%';
    el.style.width = bounds.w + '%';
    el.style.height = bounds.h + '%';
    el.style.transform = bounds.centered ? 'translateX(-50%)' : 'none';
    el.style.opacity = '1';
  }

  function clipPath(bounds) {
    const b = bounds || DEFAULT;
    const right = 100 - (b.x + b.w);
    const bottom = 100 - (b.y + b.h);
    return `inset(${b.y}% ${right}% ${bottom}% ${b.x}%)`;
  }

  function clipPathFromPlacement(bounds) {
    const b = bounds || DEFAULT;
    let left = b.x;
    let width = b.w;

    if (b.centered) {
      left = b.x - b.w / 2;
    }

    const right = 100 - (left + width);
    const bottom = 100 - (b.y + b.h);
    return `inset(${b.y}% ${right}% ${bottom}% ${left}%)`;
  }

  function clipRect(canvasW, canvasH, bounds) {
    const b = bounds || DEFAULT;
    let x = (canvasW * b.x) / 100;
    if (b.centered) x -= (canvasW * b.w) / 200;

    return {
      x,
      y: (canvasH * b.y) / 100,
      w: (canvasW * b.w) / 100,
      h: (canvasH * b.h) / 100,
    };
  }

  function intersects(layer, bounds) {
    const b = bounds || DEFAULT;
    let bx = b.x;
    if (b.centered) bx = b.x - b.w / 2;

    const lx2 = layer.x + (layer.w || 0);
    const ly2 = layer.y + (layer.h || 0);
    const bx2 = bx + b.w;
    const by2 = b.y + b.h;
    return layer.x < bx2 && lx2 > bx && layer.y < by2 && ly2 > b.y;
  }

  function applyClip(el, bounds) {
    if (el) el.style.clipPath = clipPathFromPlacement(bounds);
  }

  function resolveBounds(bounds) {
    if (bounds && typeof bounds.x === 'number') return bounds;
    return { ...DEFAULT };
  }

  function previewScaleFactor(sourceCanvas, targetCanvas, fallbackTargetMax) {
    const sourceW = sourceCanvas?.getBoundingClientRect().width;
    if (!sourceW) return 1;

    let targetW = targetCanvas?.getBoundingClientRect().width;
    if (!targetW && targetCanvas) {
      const maxW = parseFloat(getComputedStyle(targetCanvas).maxWidth);
      const parentW = targetCanvas.parentElement?.getBoundingClientRect().width;
      const cap = Number.isFinite(maxW) && maxW > 0 ? maxW : (fallbackTargetMax || 260);
      targetW = parentW ? Math.min(cap, parentW) : cap;
    }
    if (!targetW) targetW = fallbackTargetMax || 260;

    return targetW / sourceW;
  }

  function scaleFromReference(referenceWidth, targetCanvas, fallbackTargetMax) {
    if (!referenceWidth) return 1;
    let targetW = targetCanvas?.getBoundingClientRect().width;
    if (!targetW && targetCanvas) {
      const maxW = parseFloat(getComputedStyle(targetCanvas).maxWidth);
      const parentW = targetCanvas.parentElement?.getBoundingClientRect().width;
      const cap = Number.isFinite(maxW) && maxW > 0 ? maxW : (fallbackTargetMax || 280);
      targetW = parentW ? Math.min(cap, parentW) : cap;
    }
    if (!targetW) targetW = fallbackTargetMax || 280;
    return targetW / referenceWidth;
  }

  function scaledTypography(layer, scaleFactor) {
    const scale = scaleFactor || 1;
    return {
      fontSize: Math.max(1, Math.round((layer.fontSize || 16) * scale)),
      letterSpacing: Math.round((layer.letterSpacing || 0) * scale),
    };
  }

  function styleImageLayerElement(el, img) {
    el.style.display = 'flex';
    el.style.alignItems = 'center';
    el.style.justifyContent = 'center';
    el.style.boxSizing = 'border-box';
    el.style.overflow = 'hidden';

    img.style.width = '100%';
    img.style.height = '100%';
    img.style.maxWidth = '100%';
    img.style.maxHeight = '100%';
    img.style.objectFit = 'contain';
    img.style.minWidth = '0';
    img.style.minHeight = '0';
    img.style.flex = '0 1 auto';
    img.style.display = 'block';
    img.removeAttribute('width');
    img.removeAttribute('height');
  }

  function getPlacement(key) {
    return PLACEMENTS[key] || null;
  }

  function getPlacementFee(key) {
    return PLACEMENT_FEES[key] || 0;
  }

  window.TEDesignPrintArea = {
    DEFAULT,
    PLACEMENTS,
    PLACEMENT_FEES,
    fromElements,
    applyToElement,
    clipPath,
    clipPathFromPlacement,
    clipRect,
    intersects,
    applyClip,
    resolveBounds,
    previewScaleFactor,
    scaleFromReference,
    scaledTypography,
    styleImageLayerElement,
    getPlacement,
    getPlacementFee,
  };
})();
