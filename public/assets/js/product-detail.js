/* ============================================================
   PRODUCT DETAIL PAGE - product-detail.js
   Thumbnail gallery, colour swatches, qty +/-, price calc,
   description tabs, review modal (star picker + validation)
   ============================================================ */

(function () {
  'use strict';

  const productData = window.__PRODUCT_DATA__ || {};
  const pricing = productData.pricing || {};
  const LOGO_SETUP_FEE = pricing.logo_setup_fee || 12.00;
  let activeColourCode = productData.colours?.[0]?.code || null;

  function getActiveColour() {
    return (productData.colours || []).find(c => c.code === activeColourCode) || productData.colours?.[0] || null;
  }

  function formatMoney(amount) {
    const whole = Math.floor(amount);
    const dec = String(Math.round((amount - whole) * 100)).padStart(2, '0');
    // Use unicode escape so the pound sign cannot be corrupted by file encoding
    return `\u00A3${whole}<small style="font-size:0.8em">.${dec}</small>`;
  }

  const VAT_NOTE = ' <small class="pd-vat-note">+ VAT</small>';

  function updateThumbnails(images) {
    const mainImg = document.getElementById('pdMainImage');
    const thumbsWrap = document.getElementById('pdThumbs');
    if (!thumbsWrap || !images?.length) return;

    if (mainImg) {
      mainImg.style.opacity = '0';
      setTimeout(() => {
        mainImg.src = images[0];
        mainImg.style.opacity = '1';
      }, 150);
    }

    thumbsWrap.innerHTML = images.map((src, index) => `
      <button class="pd-thumb ${index === 0 ? 'active' : ''}" data-src="${src}" aria-label="View image ${index + 1}">
        <img src="${src}" alt="Product image ${index + 1}">
      </button>
    `).join('');

    thumbsWrap.querySelectorAll('.pd-thumb').forEach(thumb => {
      thumb.addEventListener('click', function () {
        const src = this.dataset.src;
        if (!src || !mainImg) return;
        mainImg.style.opacity = '0';
        setTimeout(() => { mainImg.src = src; mainImg.style.opacity = '1'; }, 200);
        thumbsWrap.querySelectorAll('.pd-thumb').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
      });
    });
  }

  function updateSizeGrid(colour) {
    const grid = document.getElementById('pdSizeQtyGrid');
    if (!grid || !colour?.sizes?.length) return;

    const header = grid.querySelector('.pd-size-qty-header');
    grid.innerHTML = '';
    if (header) grid.appendChild(header);

    colour.sizes.forEach(sizeRow => {
      const slug = sizeRow.size.replace(/[^a-z0-9]+/gi, '-').toLowerCase();
      const row = document.createElement('div');
      row.className = 'pd-size-row-item';
      row.dataset.size = sizeRow.size;
      row.innerHTML = `
        <label class="pd-size-label" for="qty-${slug}"><span class="pd-size-chip">${sizeRow.size}</span></label>
        <div class="pd-qty-ctrl">
          <button type="button" class="pd-qty-btn pd-qty-minus" aria-label="Decrease ${sizeRow.size}"><i class="bi bi-dash"></i></button>
          <input type="number" id="qty-${slug}" class="pd-qty-input" value="0" min="0" max="999" data-unit-price="${sizeRow.price_single}">
          <button type="button" class="pd-qty-btn pd-qty-plus" aria-label="Increase ${sizeRow.size}"><i class="bi bi-plus"></i></button>
        </div>`;
      grid.appendChild(row);
    });

    initQtyButtons();
    recalcTotal();
  }

  /* --- 1. Thumbnail gallery -------------------------------- */
  const mainImg = document.getElementById('pdMainImage');
  const thumbs  = document.querySelectorAll('#pd-detail .pd-thumb');

  thumbs.forEach(thumb => {
    thumb.addEventListener('click', function () {
      const src = this.dataset.src;
      if (!src || !mainImg) return;
      mainImg.style.opacity = '0';
      setTimeout(() => { mainImg.src = src; mainImg.style.opacity = '1'; }, 200);
      thumbs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });


  /* --- 2. Colour swatches ---------------------------------- */
  const swatches    = document.querySelectorAll('#pd-detail .pd-swatch');
  const colourLabel = document.getElementById('pdColourName');

  swatches.forEach(swatch => {
    swatch.addEventListener('click', function () {
      swatches.forEach(s => s.classList.remove('active'));
      this.classList.add('active');
      if (colourLabel) colourLabel.textContent = this.dataset.colour;
      activeColourCode = this.dataset.code;
      const images = JSON.parse(this.dataset.images || '[]');
      updateThumbnails(images);
      updateSizeGrid(getActiveColour());
    });
  });


  /* --- 3. Qty +/- buttons ---------------------------------- */
  function initQtyButtons() {
    document.querySelectorAll('#pd-detail .pd-size-row-item').forEach(row => {
      const minus = row.querySelector('.pd-qty-minus');
      const plus  = row.querySelector('.pd-qty-plus');
      const input = row.querySelector('.pd-qty-input');
      if (!minus || !plus || !input) return;
      minus.addEventListener('click', () => {
        const v = parseInt(input.value, 10) || 0;
        if (v > 0) { input.value = v - 1; recalcTotal(); }
      });
      plus.addEventListener('click', () => {
        input.value = (parseInt(input.value, 10) || 0) + 1;
        recalcTotal();
      });
      input.addEventListener('input', recalcTotal);
    });
  }


  /* --- 4. Live price calc ---------------------------------- */
  const totalUnitsEl = document.getElementById('pdTotalUnits');
  const estTotalEl   = document.getElementById('pdEstTotal');

  function recalcTotal() {
    let total = 0;
    let amount = 0;
    const displayMarkup = productData.display_decoration_included
      || productData.pricing?.display_included_decoration
      || 0;

    document.querySelectorAll('#pd-detail .pd-qty-input').forEach(inp => {
      const v = parseInt(inp.value, 10);
      const baseUnit = parseFloat(inp.dataset.unitPrice || productData.min_price || 0);
      const unit = baseUnit + displayMarkup;
      if (!isNaN(v) && v > 0) {
        total += v;
        amount += v * unit;
      }
    });

    const logoSetupChecked = document.getElementById('pdLogoSetup')?.checked;
    if (logoSetupChecked) {
      amount += LOGO_SETUP_FEE;
    }

    if (totalUnitsEl) totalUnitsEl.textContent = total;
    if (estTotalEl) {
      estTotalEl.innerHTML = amount > 0
        ? formatMoney(amount) + VAT_NOTE
        : formatMoney(0);
    }
    if (typeof syncCtaForQuantity === 'function') {
      syncCtaForQuantity();
    }
  }

  function getSelectedDecorationMethods() {
    const selected = document.querySelector('input[name="decoration_method"]:checked');
    if (selected?.value) return [selected.value];

    // Fall back to first available method if radios missing
    if (productData.decoration?.methods?.length) {
      return [productData.decoration.methods[0]];
    }
    return [];
  }

  function syncLogoSetupAvailability() {
    const logoOption = document.getElementById('pdLogoSetupOption');
    const logoInput = document.getElementById('pdLogoSetup');
    if (!logoOption || !logoInput) return;

    const embroiderySelected = document.getElementById('pdMethodEmbroidery')?.checked === true;
    logoOption.hidden = !embroiderySelected;
    if (!embroiderySelected) {
      logoInput.checked = false;
    }
  }


  /* --- 5. Description / Features tabs ---------------------- */
  document.querySelectorAll('#pd-desc .pd-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      document.querySelectorAll('#pd-desc .pd-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('#pd-desc .pd-tab-panel').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      const panel = document.getElementById(`pd-panel-${this.dataset.tab}`);
      if (panel) panel.classList.add('active');
    });
  });


  /* --- 6. Review modal ------------------------------------- */
  const starBtns   = document.querySelectorAll('.pd-rev-star-btn');
  const starLabel  = document.getElementById('pdRevStarLabel');
  const ratingInput = document.getElementById('pdRevRating');
  const textarea   = document.getElementById('pdRevText');
  const charCount  = document.getElementById('pdRevCharCount');
  const submitBtn  = document.getElementById('pdRevSubmitBtn');
  const errorBox   = document.getElementById('pdRevError');
  const errorMsg   = document.getElementById('pdRevErrorMsg');

  const STAR_LABELS = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

  let selectedRating = 0;

  /* Star hover */
  starBtns.forEach(btn => {
    btn.addEventListener('mouseenter', function () {
      const val = parseInt(this.dataset.val, 10);
      highlightStars(val);
      if (starLabel) starLabel.textContent = STAR_LABELS[val];
    });
    btn.addEventListener('mouseleave', () => {
      highlightStars(selectedRating);
      if (starLabel) starLabel.textContent = selectedRating ? STAR_LABELS[selectedRating] : 'Select a rating';
    });
    btn.addEventListener('click', function () {
      selectedRating = parseInt(this.dataset.val, 10);
      if (ratingInput) ratingInput.value = selectedRating;
      highlightStars(selectedRating);
      if (starLabel) starLabel.textContent = STAR_LABELS[selectedRating];
      starBtns.forEach(b => b.classList.remove('selected'));
      for (let i = 0; i < selectedRating; i++) {
        starBtns[i].classList.add('selected');
      }
    });
  });

  function highlightStars(count) {
    starBtns.forEach((btn, i) => {
      const icon = btn.querySelector('i');
      if (!icon) return;
      if (i < count) {
        btn.classList.add('hovered');
        icon.className = 'bi bi-star-fill';
      } else {
        btn.classList.remove('hovered');
        icon.className = 'bi bi-star';
      }
    });
  }

  /* Char counter */
  if (textarea && charCount) {
    textarea.addEventListener('input', function () {
      const len = this.value.length;
      charCount.textContent = Math.min(len, 500);
      if (len > 500) this.value = this.value.slice(0, 500);
    });
  }

  /* Form validation + submit */
  if (submitBtn) {
    submitBtn.addEventListener('click', () => {
      const name   = (document.getElementById('pdRevName')?.value  || '').trim();
      const email  = (document.getElementById('pdRevEmail')?.value || '').trim();
      const review = (textarea?.value || '').trim();
      const rating = parseInt(ratingInput?.value || '0', 10);

      // Hide previous error
      if (errorBox) errorBox.style.display = 'none';

      if (!rating) {
        showError('Please select a star rating.'); return;
      }
      if (!name) {
        showError('Please enter your full name.'); return;
      }
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError('Please enter a valid email address.'); return;
      }
      if (review.length < 10) {
        showError('Please write a review of at least 10 characters.'); return;
      }

      // Simulate submit
      submitBtn.disabled  = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';

      setTimeout(() => {
        // Inject new review card at the top of the grid
        injectReviewCard({ name, review, rating });

        // Reset form
        selectedRating = 0;
        highlightStars(0);
        if (starLabel)  starLabel.textContent  = 'Select a rating';
        if (ratingInput) ratingInput.value     = '0';
        if (document.getElementById('pdRevName'))  document.getElementById('pdRevName').value  = '';
        if (document.getElementById('pdRevEmail')) document.getElementById('pdRevEmail').value = '';
        if (textarea)   textarea.value         = '';
        if (charCount)  charCount.textContent  = '0';

        submitBtn.disabled  = false;
        submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit Review';

        // Close modal
        const modalEl = document.getElementById('reviewModal');
        if (modalEl && window.bootstrap) {
          bootstrap.Modal.getInstance(modalEl)?.hide();
        }
      }, 1000);
    });
  }

  function showError(msg) {
    if (!errorBox || !errorMsg) return;
    errorMsg.textContent   = msg;
    errorBox.style.display = 'flex';
  }

  function injectReviewCard({ name, review, rating }) {
    const grid = document.getElementById('pdReviewsGrid');
    if (!grid) return;

    const initials = name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    const stars    = Array.from({ length: 5 }, (_, i) =>
      `<i class="bi bi-star${i < rating ? '-fill' : ''}"></i>`
    ).join('');

    const now  = new Date();
    const date = now.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });

    const card = document.createElement('div');
    card.className = 'pd-review-card';
    card.style.cssText = 'opacity:0; transform:translateY(16px); transition:opacity 0.4s ease, transform 0.4s ease;';
    card.innerHTML = `
      <div class="pd-rev-card-top">
        <div class="pd-rev-stars">${stars}</div>
        <span class="pd-rev-date">${date}</span>
      </div>
      <p class="pd-rev-body">${review.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</p>
      <div class="pd-rev-author">
        <div class="pd-rev-avatar">${initials}</div>
        <div>
          <span class="pd-rev-name">${name.replace(/</g, '&lt;')}</span>
          <span class="pd-rev-company">New Review</span>
        </div>
        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
      </div>`;

    grid.prepend(card);
    requestAnimationFrame(() => {
      card.style.opacity   = '1';
      card.style.transform = 'translateY(0)';
    });
  }


  function collectCartPayload() {
    const sizeBreakdown = [];
    let totalQty = 0;
    let primarySize = '';

    document.querySelectorAll('#pdSizeQtyGrid .pd-size-row-item').forEach(row => {
      const input = row.querySelector('.pd-qty-input');
      const val = parseInt(input?.value || '0', 10);
      if (val > 0) {
        totalQty += val;
        if (!primarySize) primarySize = row.dataset.size;
        sizeBreakdown.push({
          size: row.dataset.size,
          qty: val,
          unit_price: parseFloat(input?.dataset.unitPrice || productData.min_price || 0),
        });
      }
    });

    return { sizeBreakdown, totalQty, primarySize };
  }

  async function startCustomize(btn) {
    const { sizeBreakdown, totalQty, primarySize } = collectCartPayload();

    if (totalQty === 0) {
      alert('Please select a quantity for at least one size before customizing.');
      return;
    }

    const methods = getSelectedDecorationMethods();
    if (!methods.length) {
      alert('Please select at least one decoration method (Print and/or Embroidery).');
      return;
    }

    const colour = getActiveColour();
    const avgPrice = sizeBreakdown.reduce((sum, r) => sum + r.unit_price * r.qty, 0) / totalQty;
    const inner = btn.querySelector('.pd-customize-btn-inner');
    const originalHtml = inner ? inner.innerHTML : btn.innerHTML;

    btn.disabled = true;
    btn.style.pointerEvents = 'none';
    if (inner) {
      inner.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Opening customizer…';
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      const shopRoutes = window.SHOP_ROUTES || { cart_store: '/shop/cart', cart_url: '/shop/cart' };
      const embroiderySelected = methods.includes('embroidery');
      const res = await fetch(shopRoutes.cart_store, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          style_code: productData.style_code,
          product_title: productData.product_title,
          category_slug: productData.category_slug,
          product_slug: productData.product_slug,
          colour_code: colour?.code || null,
          colour_name: colour?.name || null,
          colour_hex: colour?.hex || null,
          size: primarySize,
          size_breakdown: sizeBreakdown,
          quantity: totalQty,
          unit_price: avgPrice,
          logo_setup: embroiderySelected && (document.getElementById('pdLogoSetup')?.checked || false),
          decoration_methods: methods,
        }),
      });

      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.message || 'Failed to start customizer');
      }

      const badge = document.getElementById('navShopCartBadge')
        || document.querySelector('[data-cart-badge]');
      if (badge && data.cart_count > 0) {
        badge.textContent = data.cart_count;
        badge.style.display = '';
      }

      window.location.href = data.customizer_url || productData.customizer_url;
    } catch (err) {
      alert(err.message || 'Could not open the customizer. Please try again.');
      btn.disabled = false;
      btn.style.pointerEvents = '';
      if (inner) {
        inner.innerHTML = originalHtml;
      }
    }
  }


  const BULK_QTY_THRESHOLD = 100;

  function syncCtaForQuantity() {
    const { totalQty } = collectCartPayload();
    const customizeBtn = document.getElementById('pdCustomizeBtn');
    const enquireBtn = document.getElementById('pdEnquireBtn');
    const hint = document.getElementById('pdBulkHint');
    const isBulk = totalQty > BULK_QTY_THRESHOLD;

    // Use class + !important CSS — display:block on .pd-customize-btn overrides [hidden]
    if (customizeBtn) {
      customizeBtn.classList.toggle('is-hidden', isBulk);
      customizeBtn.hidden = isBulk;
      customizeBtn.setAttribute('aria-hidden', isBulk ? 'true' : 'false');
    }
    if (enquireBtn) {
      enquireBtn.classList.toggle('is-hidden', !isBulk);
      enquireBtn.hidden = !isBulk;
      enquireBtn.setAttribute('aria-hidden', isBulk ? 'false' : 'true');
    }
    if (hint) {
      hint.classList.toggle('is-hidden', !isBulk);
      hint.hidden = !isBulk;
    }
  }

  function openEnquireModal() {
    const { sizeBreakdown, totalQty } = collectCartPayload();
    if (totalQty <= BULK_QTY_THRESHOLD) {
      alert('Increase quantity above 100 to request a bulk quote.');
      return;
    }

    const colour = getActiveColour();
    const methods = getSelectedDecorationMethods();
    const summary = document.getElementById('pdEnquireSummary');
    const sizeLabel = sizeBreakdown.map(r => `${r.size} \u00D7 ${r.qty}`).join(', ');
    const methodLabel = methods.map(m => m === 'embroidery' ? 'Embroidery' : 'Print').join(', ') || '—';

    if (summary) {
      summary.innerHTML = [
        `<div><strong>Product:</strong> ${escapeHtml(productData.product_title || '')}</div>`,
        `<div><strong>Colour:</strong> ${escapeHtml(colour?.name || '—')}</div>`,
        `<div><strong>Sizes:</strong> ${escapeHtml(sizeLabel)}</div>`,
        `<div><strong>Total qty:</strong> ${totalQty}</div>`,
        `<div><strong>Decoration:</strong> ${escapeHtml(methodLabel)}</div>`,
      ].join('');
    }

    const err = document.getElementById('pdEnquireError');
    if (err) {
      err.hidden = true;
      err.classList.add('is-hidden');
    }

    const modal = document.getElementById('pdBulkEnquireModal');
    if (modal) {
      modal.hidden = false;
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeEnquireModal() {
    const modal = document.getElementById('pdBulkEnquireModal');
    if (modal) {
      modal.hidden = true;
      modal.setAttribute('aria-hidden', 'true');
    }
    document.body.style.overflow = '';
  }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = String(str ?? '');
    return d.innerHTML;
  }

  function showEnquireError(msg) {
    const box = document.getElementById('pdEnquireError');
    const text = document.getElementById('pdEnquireErrorMsg');
    if (text) text.textContent = msg;
    if (box) {
      box.hidden = false;
      box.classList.remove('is-hidden');
    }
  }

  async function submitBulkEnquiry(e) {
    e.preventDefault();
    const { sizeBreakdown, totalQty } = collectCartPayload();
    if (totalQty <= BULK_QTY_THRESHOLD) {
      showEnquireError('Total quantity must be above 100.');
      return;
    }

    const name = document.getElementById('pdEnqName')?.value.trim();
    const email = document.getElementById('pdEnqEmail')?.value.trim();
    const phone = document.getElementById('pdEnqPhone')?.value.trim();
    const company = document.getElementById('pdEnqCompany')?.value.trim();
    const message = document.getElementById('pdEnqMessage')?.value.trim();
    const colour = getActiveColour();
    const methods = getSelectedDecorationMethods();
    const avgPrice = sizeBreakdown.reduce((sum, r) => sum + r.unit_price * r.qty, 0) / totalQty;
    const displayMarkup = productData.display_decoration_included
      || productData.pricing?.display_included_decoration
      || 0;
    const unitWithDecor = avgPrice + displayMarkup;
    const logoSetup = methods.includes('embroidery') && !!document.getElementById('pdLogoSetup')?.checked;
    const estSubtotal = (unitWithDecor * totalQty) + (logoSetup ? (LOGO_SETUP_FEE || 0) : 0);

    if (!name) { showEnquireError('Please enter your full name.'); return; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showEnquireError('Please enter a valid email address.');
      return;
    }

    const submitBtn = document.getElementById('pdEnquireSubmit');
    const original = submitBtn?.innerHTML;
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending…';
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      const shopRoutes = window.SHOP_ROUTES || {};
      const res = await fetch(shopRoutes.bulk_enquiry || '/api/product-bulk-enquiries', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken || '',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          full_name: name,
          email,
          phone: phone || null,
          company_name: company || null,
          message: message || null,
          product_title: productData.product_title,
          product_style_code: productData.style_code,
          category_slug: productData.category_slug,
          product_slug: productData.product_slug,
          colour_name: colour?.name || null,
          colour_code: colour?.code || null,
          colour_hex: colour?.hex || null,
          size_breakdown: sizeBreakdown,
          quantity: totalQty,
          decoration_methods: methods,
          logo_setup: logoSetup,
          estimated_unit_price: unitWithDecor,
          estimated_subtotal: estSubtotal,
        }),
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok) {
        throw new Error(data.message || 'Could not submit enquiry.');
      }

      closeEnquireModal();
      document.getElementById('pdBulkEnquireForm')?.reset();
      alert(data.message || (`Enquiry submitted. Reference: ${data.reference || ''}`));
    } catch (err) {
      showEnquireError(err.message || 'Could not submit enquiry. Please try again.');
    } finally {
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = original || '<i class="bi bi-send"></i> Submit Enquiry';
      }
    }
  }

  /* ── Init ─────────────────────────────────────────────────── */
  function init() {
    initQtyButtons();
    syncLogoSetupAvailability();
    recalcTotal();

    ['pdMethodPrint', 'pdMethodEmbroidery', 'pdLogoSetup'].forEach(id => {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener('change', () => {
          syncLogoSetupAvailability();
          recalcTotal();
        });
      }
    });

    const customizeBtn = document.getElementById('pdCustomizeBtn');
    if (customizeBtn) {
      customizeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        startCustomize(customizeBtn);
      });
    }

    const enquireBtn = document.getElementById('pdEnquireBtn');
    if (enquireBtn) {
      enquireBtn.addEventListener('click', (e) => {
        e.preventDefault();
        openEnquireModal();
      });
    }

    document.getElementById('pdEnqClose')?.addEventListener('click', closeEnquireModal);
    document.getElementById('pdEnqCancel')?.addEventListener('click', closeEnquireModal);
    document.getElementById('pdEnqBackdrop')?.addEventListener('click', closeEnquireModal);
    document.getElementById('pdBulkEnquireForm')?.addEventListener('submit', submitBulkEnquiry);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();