/* ==============================================
   cart-checkout.js — Cart, checkout & design preview
   ============================================== */
(function () {
  'use strict';

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  function updateCartBadge(count) {
    const badge = document.getElementById('navShopCartBadge')
      || document.getElementById('navCartBadge')
      || document.querySelector('[data-cart-badge]');
    if (!badge) return;
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = '';
    } else {
      badge.style.display = 'none';
    }
  }

  function formatMoney(amount) {
    return `\u00A3${Number(amount).toFixed(2)}`;
  }

  function updateCartSummary(totals) {
    if (!totals) return;

    const subtotalEl = document.getElementById('cartSummarySubtotal');
    const vatEl = document.getElementById('cartSummaryVat');
    const totalEl = document.getElementById('cartSummaryTotal');

    if (subtotalEl && totals.subtotal != null) subtotalEl.textContent = formatMoney(totals.subtotal);
    if (vatEl && totals.vat != null) vatEl.textContent = formatMoney(totals.vat);
    if (totalEl && totals.total != null) totalEl.textContent = formatMoney(totals.total);
  }

  /* ── Remove cart item ── */
  function initRemoveButtons() {
    document.querySelectorAll('[data-remove-item]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = btn.dataset.removeItem;
        if (!confirm('Remove this item from your cart?')) return;

        btn.disabled = true;
        try {
          const shopRoutes = window.SHOP_ROUTES || {};
          const destroyUrl = shopRoutes.cart_destroy_base
            ? `${shopRoutes.cart_destroy_base}/${itemId}`
            : `/shop/cart/items/${itemId}`;
          const res = await fetch(destroyUrl, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
            },
          });
          const data = await res.json();
          if (res.ok) {
            updateCartBadge(data.cart_count);
            updateCartSummary(data);
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

  /* ── Checkout: Stripe Card Element then submit ── */
  function initCheckoutForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    let stripeCtx = null;
    let submitting = false;

    (async () => {
      try {
        if (!window.TEStripeCheckout) return;
        stripeCtx = await window.TEStripeCheckout.initCardElement({
          mountSelector: '#stripeCardElement',
          errorSelector: '#stripeCardErrors',
          theme: 'light',
        });
      } catch (err) {
        const errEl = document.getElementById('stripeCardErrors');
        if (errEl) errEl.textContent = err.message || 'Could not load payment form.';
      }
    })();

    form.addEventListener('submit', async (e) => {
      if (submitting) return;
      e.preventDefault();

      if (!form.reportValidity()) return;

      const btn = document.getElementById('placeOrderBtn');
      const intentInput = document.getElementById('paymentIntentId');
      const errEl = document.getElementById('stripeCardErrors');

      if (!stripeCtx) {
        if (errEl) errEl.textContent = 'Payment form is still loading. Please wait a moment.';
        return;
      }

      const sameBilling = document.getElementById('billingSame')?.checked;
      const name = form.querySelector('[name="name"]')?.value || '';
      const email = form.querySelector('[name="email"]')?.value || '';

      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing payment…';
      }

      try {
        const paid = await window.TEStripeCheckout.pay({
          stripe: stripeCtx.stripe,
          card: stripeCtx.card,
          context: 'shop',
          email,
          name,
          billing: {
            line1: sameBilling
              ? form.querySelector('[name="shipping_address_line1"]')?.value
              : form.querySelector('[name="billing_address_line1"]')?.value,
            city: sameBilling
              ? form.querySelector('[name="shipping_city"]')?.value
              : form.querySelector('[name="billing_city"]')?.value,
            state: sameBilling
              ? form.querySelector('[name="shipping_state"]')?.value
              : form.querySelector('[name="billing_state"]')?.value,
            postal_code: sameBilling
              ? form.querySelector('[name="shipping_zip"]')?.value
              : form.querySelector('[name="billing_zip"]')?.value,
            country: 'GB',
          },
          onStatus: (msg) => {
            if (btn) btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> ${msg}`;
          },
        });

        if (intentInput) intentInput.value = paid.paymentIntentId;
        if (btn) btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Placing order…';
        submitting = true;
        form.submit();
      } catch (err) {
        const msg = err.message || 'Payment failed. Please try again.';
        if (errEl) errEl.textContent = msg;
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = 'Pay &amp; Place Order <i class="bi bi-lock-fill"></i>';
        }
      }
    });
  }

  /* ── Design preview modal ── */
  function initDesignPreview() {
    const designs = window.__CART_DESIGNS__ || {};
    const modalEl = document.getElementById('cartDesignModal');
    if (!modalEl || !Object.keys(designs).length) return;

    const garmentImg = document.getElementById('cdmGarmentImg');
    const layersEl = document.getElementById('cdmLayers');
    const previewCanvas = document.getElementById('cdmPreviewCanvas');
    const artworkEl = document.getElementById('cdmArtworkPreview');
    const titleEl = document.getElementById('cartDesignModalLabel');
    const subtitleEl = document.getElementById('cdmSubtitle');
    const detailsEl = document.getElementById('cdmDetails');

    function showModal() {
      modalEl.hidden = false;
      modalEl.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }

    function hideModal() {
      modalEl.hidden = true;
      modalEl.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    document.getElementById('cdmModalBackdrop')?.addEventListener('click', hideModal);
    document.getElementById('cdmModalClose')?.addEventListener('click', hideModal);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !modalEl.hidden) hideModal();
    });

    document.querySelectorAll('[data-design-id]').forEach(btn => {
      btn.addEventListener('click', () => {
        const preview = designs[btn.dataset.designId];
        if (!preview) return;
        openDesignPreview(preview);
        showModal();
      });
    });

    function escapeHtml(str) {
      const d = document.createElement('div');
      d.textContent = String(str ?? '');
      return d.innerHTML;
    }

    function detailRow(label, value) {
      return `<div class="cdm-detail-row"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`;
    }

    function resolveGarmentImage(preview, data, view) {
      const front = preview.customizer_image
        || data.customizer_image
        || preview.product_image
        || '';
      const back = preview.customizer_image_back
        || data.customizer_image_back
        || front;
      const sleeve = preview.customizer_image_sleeve
        || data.customizer_image_sleeve
        || front;
      if (view === 'back') return back;
      if (view === 'sleeve') return sleeve;
      return front;
    }

    function resolveLayerSrc(layer, storageBase) {
      if (layer.src) return layer.src;
      if (layer.asset_path) return storageBase + '/' + layer.asset_path;
      return '';
    }

    function placementKeysFromData(data, preview) {
      const fromData = Array.isArray(data.decoration_placements)
        ? data.decoration_placements.filter(Boolean)
        : [];
      if (fromData.length) return [...new Set(fromData)];

      const byPlacement = preview?.preview_by_placement || data.preview_by_placement || {};
      const fromMap = Object.keys(byPlacement || {});
      if (fromMap.length) return fromMap;

      if (data.decoration_placement) return [data.decoration_placement];

      const views = [...new Set((data.layers || []).map(l => l.placement || l.view || 'front').filter(Boolean))];
      return views.length ? views : ['front'];
    }

    function placementLabel(key, data) {
      const pa = window.TEDesignPrintArea;
      const fromPa = pa?.getPlacement?.(key)?.label;
      if (fromPa) return fromPa;
      if (data.decoration_placement_label && (!data.decoration_placements || data.decoration_placements.length <= 1)) {
        return data.decoration_placement_label;
      }
      return String(key || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    function layersForPlacement(data, key) {
      const pa = window.TEDesignPrintArea;
      const placement = pa?.getPlacement?.(key);
      const view = placement?.view || key;
      return (data.layers || []).filter(l => {
        if (l.placement) return l.placement === key;
        return (l.view || 'front') === view;
      });
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

    function isFullMockupUrl(url) {
      if (!url || typeof url !== 'string') return false;
      const trimmed = url.trim();
      if (!trimmed || /\.json(\?|#|$)/i.test(trimmed)) return false;
      return /preview-/i.test(trimmed);
    }

    function wireGarmentFallback(img, garmentSrc, preview) {
      img.onerror = function () {
        if (img.dataset.fallbackApplied === '2') return;
        if (img.dataset.fallbackApplied === '1') {
          img.dataset.fallbackApplied = '2';
          img.src = preview.product_image || '/assets/images/customizer/tshirtcustom.png';
          return;
        }
        img.dataset.fallbackApplied = '1';
        try {
          const u = new URL(garmentSrc, window.location.origin);
          if (u.origin !== window.location.origin) {
            img.src = '/shop/media-proxy?url=' + encodeURIComponent(u.href);
            return;
          }
        } catch { /* fall through */ }
        img.dataset.fallbackApplied = '2';
        img.src = preview.product_image || '/assets/images/customizer/tshirtcustom.png';
      };
    }

    function createLivePanel(preview, data, key) {
      const pa = window.TEDesignPrintArea;
      const placement = pa?.getPlacement?.(key);
      const view = placement?.view || (key === 'back' || key === 'back_print' || key === 'back_embroidery' ? 'back' : key === 'sleeve' ? 'sleeve' : 'front');
      const printBounds = placement?.printArea || (pa ? pa.resolveBounds(data.print_area) : null);
      const layers = layersForPlacement(data, key);
      const colourHex = preview.colour_hex || data.colour?.hex || '#ffffff';

      const panel = document.createElement('div');
      panel.className = 'cdm-preview-panel';

      const label = document.createElement('div');
      label.className = 'cdm-preview-panel-label';
      label.textContent = placementLabel(key, data);
      panel.appendChild(label);

      const canvas = document.createElement('div');
      canvas.className = 'cdm-preview-canvas';

      const img = document.createElement('img');
      img.className = 'cdm-garment-img';
      img.alt = placementLabel(key, data);
      img.draggable = false;
      img.referrerPolicy = 'no-referrer';
      const garmentSrc = resolveGarmentImage(preview, data, view);
      delete img.dataset.fallbackApplied;
      img.removeAttribute('crossorigin');
      wireGarmentFallback(img, garmentSrc, preview);
      img.src = garmentSrc || preview.product_image || '/assets/images/customizer/tshirtcustom.png';
      img.style.filter = computeGarmentFilter(colourHex);
      canvas.appendChild(img);

      const layersEl = document.createElement('div');
      layersEl.className = 'cdm-layers';
      const scaleFactor = pa ? pa.scaleFromReference(data.canvas_width || 620, canvas, 240) : 1;
      if (pa && printBounds) pa.applyClip(layersEl, printBounds);
      layers.forEach(layer => {
        layersEl.appendChild(buildLayerElement(layer, preview.storage_base, scaleFactor));
      });
      canvas.appendChild(layersEl);
      panel.appendChild(canvas);
      return { panel, layerCount: layers.length };
    }

    function createSavedPanel(url, labelText) {
      const panel = document.createElement('div');
      panel.className = 'cdm-preview-panel';
      if (labelText) {
        const label = document.createElement('div');
        label.className = 'cdm-preview-panel-label';
        label.textContent = labelText;
        panel.appendChild(label);
      }
      const img = document.createElement('img');
      img.className = 'cdm-saved-mockup';
      img.src = url;
      img.alt = labelText || 'Saved design preview';
      img.draggable = false;
      panel.appendChild(img);
      return panel;
    }

    function openDesignPreview(preview) {
      const data = preview.design_data || {};
      const colourName = preview.colour_name || data.colour?.name || '';
      const placement = data.decoration_placement_label || '';
      const keys = placementKeysFromData(data, preview);
      const galleryEl = document.getElementById('cdmPreviewGallery');

      if (titleEl) {
        titleEl.innerHTML = '<i class="bi bi-palette"></i> ' + escapeHtml(preview.title || 'Your Design');
      }
      if (subtitleEl) {
        const parts = [colourName, placement].filter(Boolean);
        subtitleEl.textContent = parts.length ? parts.join(' · ') : 'Custom design preview';
      }

      if (artworkEl) {
        artworkEl.style.display = 'none';
        artworkEl.innerHTML = '';
      }
      if (previewCanvas) previewCanvas.hidden = true;
      if (galleryEl) galleryEl.innerHTML = '';

      let layerCount = (data.layers || []).length;
      const byPlacement = preview.preview_by_placement || {};
      const savedMulti = keys
        .map(key => ({ key, url: byPlacement[key] }))
        .filter(row => isFullMockupUrl(row.url));

      const multi = keys.length > 1 || savedMulti.length > 1;

      if (savedMulti.length && (multi || !data.layers?.length)) {
        savedMulti.forEach(row => {
          galleryEl?.appendChild(createSavedPanel(row.url, placementLabel(row.key, data)));
        });
      } else if (multi || (data.layers || []).length) {
        // Live rebuild — supports existing multi-position designs that only saved one mockup
        let rendered = 0;
        keys.forEach(key => {
          const { panel, layerCount: count } = createLivePanel(preview, data, key);
          galleryEl?.appendChild(panel);
          rendered += count;
        });
        if (rendered) layerCount = rendered;
      } else if (isFullMockupUrl(preview.preview_url)) {
        galleryEl?.appendChild(createSavedPanel(preview.preview_url, placement || null));
      }

      if (detailsEl) {
        const sizeLabel = preview.size_breakdown?.length
          ? preview.size_breakdown.map(r => `${r.size} \u00D7 ${r.qty}`).join(', ')
          : (data.size || preview.size || '\u2014');
        detailsEl.innerHTML = [
          detailRow('Garment', data.garment || preview.title || '\u2014'),
          detailRow('Colour', colourName || '\u2014'),
          detailRow('Position', placement || '\u2014'),
          detailRow('Sizes', sizeLabel),
          detailRow('Quantity', String(preview.quantity || data.qty || '\u2014')),
          detailRow('Design Elements', String(layerCount || '\u2014')),
        ].join('');
      }
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
