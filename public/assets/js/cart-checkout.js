/* ==============================================
   cart-checkout.js — Cart, checkout & auth prompt
   ============================================== */
(function () {
  'use strict';

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  function updateCartBadge(count) {
    const badge = document.getElementById('navCartBadge');
    if (!badge) return;
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = '';
    } else {
      badge.style.display = 'none';
    }
  }

  /* ── Remove cart item ── */
  function initRemoveButtons() {
    document.querySelectorAll('[data-remove-item]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = btn.dataset.removeItem;
        if (!confirm('Remove this item from your cart?')) return;

        btn.disabled = true;
        try {
          const res = await fetch(`/cart/items/${itemId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
            },
          });
          const data = await res.json();
          if (res.ok) {
            updateCartBadge(data.cart_count);
            const item = btn.closest('.cc-item');
            if (item) {
              item.style.opacity = '0';
              setTimeout(() => {
                item.remove();
                if (!document.querySelector('.cc-item')) {
                  window.location.reload();
                }
              }, 300);
            }
          }
        } catch {
          btn.disabled = false;
        }
      });
    });
  }

  /* ── Billing same as shipping toggle ── */
  function initBillingToggle() {
    const checkbox = document.getElementById('billingSame');
    const billingFields = document.getElementById('billingFields');
    if (!checkbox || !billingFields) return;

    checkbox.addEventListener('change', () => {
      billingFields.style.display = checkbox.checked ? 'none' : '';
    });
  }

  /* ── Place order button loading state ── */
  function initCheckoutForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    form.addEventListener('submit', () => {
      const btn = document.getElementById('placeOrderBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Placing Order…';
      }
    });
  }

  /* ── Design preview modal ── */
  function initDesignPreview() {
    const designs = window.__CART_DESIGNS__ || {};
    const modalEl = document.getElementById('cartDesignModal');
    if (!modalEl || !Object.keys(designs).length) return;

    const modal = new bootstrap.Modal(modalEl);
    const garmentImg = document.getElementById('cdmGarmentImg');
    const layersEl = document.getElementById('cdmLayers');
    const previewCanvas = document.getElementById('cdmPreviewCanvas');
    const titleEl = document.getElementById('cartDesignModalLabel');
    const subtitleEl = document.getElementById('cdmSubtitle');
    const detailsEl = document.getElementById('cdmDetails');
    let pendingPreview = null;

    document.querySelectorAll('[data-design-id]').forEach(btn => {
      btn.addEventListener('click', () => {
        const preview = designs[btn.dataset.designId];
        if (!preview) return;
        pendingPreview = preview;
        modal.show();
      });
    });

    modalEl.addEventListener('shown.bs.modal', () => {
      if (pendingPreview) {
        openDesignPreview(pendingPreview);
        pendingPreview = null;
      }
    });

    function resolveGarmentImage(preview, data, view) {
      const front = preview.customizer_image || data.customizer_image || '';
      const back = preview.customizer_image_back || data.customizer_image_back || front;
      return view === 'back' ? back : front;
    }

    function openDesignPreview(preview) {
      const data = preview.design_data || {};
      const pa = window.TEDesignPrintArea;
      const colourHex = preview.colour_hex || data.colour?.hex || '#ffffff';
      const colourName = preview.colour_name || data.colour?.name || '';
      const view = data.view || 'front';
      const printBounds = pa ? pa.resolveBounds(data.print_area) : null;
      const scaleFactor = pa
        ? pa.scaleFromReference(data.canvas_width || 620, previewCanvas, 280)
        : 1;
      const layers = (data.layers || [])
        .filter(l => l.view === view)
        .filter(l => !pa || !printBounds || pa.intersects(l, printBounds));

      if (titleEl) {
        titleEl.innerHTML = '<i class="bi bi-palette"></i> ' + escapeHtml(preview.title);
      }
      if (subtitleEl) {
        subtitleEl.textContent = colourName ? colourName + ' garment · custom design preview' : 'Custom design preview';
      }

      if (garmentImg) {
        garmentImg.src = resolveGarmentImage(preview, data, view);
        garmentImg.style.filter = computeGarmentFilter(colourHex);
      }

      if (layersEl) {
        layersEl.innerHTML = '';
        if (pa && printBounds) pa.applyClip(layersEl, printBounds);
        layers.forEach(layer => {
          layersEl.appendChild(buildLayerElement(layer, preview.storage_base, scaleFactor));
        });
      }

      if (detailsEl) {
        const sizeLabel = preview.size_breakdown?.length
          ? preview.size_breakdown.map(r => `${r.size} × ${r.qty}`).join(', ')
          : (data.size || '—');
        detailsEl.innerHTML = [
          detailRow('Garment', data.garment || preview.title),
          detailRow('Colour', colourName || '—'),
          detailRow('Sizes', sizeLabel),
          detailRow('Quantity', String(preview.quantity || data.qty || '—')),
          detailRow('Design Elements', String(layers.length)),
        ].join('');
      }
    }

    function detailRow(label, value) {
      return `<div class="cdm-detail-row"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`;
    }

    function escapeHtml(str) {
      const d = document.createElement('div');
      d.textContent = String(str);
      return d.innerHTML;
    }

    function resolveLayerSrc(layer, storageBase) {
      if (layer.src) return layer.src;
      if (layer.asset_path) return storageBase + '/' + layer.asset_path;
      return '';
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
  }

  document.addEventListener('DOMContentLoaded', () => {
    initRemoveButtons();
    initBillingToggle();
    initCheckoutForm();
    initDesignPreview();
  });

  window.TECart = { updateCartBadge };
})();
