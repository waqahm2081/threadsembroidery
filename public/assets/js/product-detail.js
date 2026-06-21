/* ============================================================
   PRODUCT DETAIL PAGE  —  product-detail.js
   Thumbnail gallery · colour swatches · qty +/- · price calc ·
   description tabs · review modal (star picker + validation)
   ============================================================ */

(function () {
  'use strict';

  const productData = window.__PRODUCT_DATA__ || {};
  let activeColourCode = productData.colours?.[0]?.code || null;

  function getActiveColour() {
    return (productData.colours || []).find(c => c.code === activeColourCode) || productData.colours?.[0] || null;
  }

  function formatMoney(amount) {
    const whole = Math.floor(amount);
    const dec = String(Math.round((amount - whole) * 100)).padStart(2, '0');
    return `£${whole}<small style="font-size:0.8em">.${dec}</small>`;
  }

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
          <button type="button" class="pd-qty-btn pd-qty-minus" aria-label="Decrease ${sizeRow.size}">−</button>
          <input type="number" id="qty-${slug}" class="pd-qty-input" value="0" min="0" max="999" data-unit-price="${sizeRow.price_single}">
          <button type="button" class="pd-qty-btn pd-qty-plus" aria-label="Increase ${sizeRow.size}">+</button>
        </div>`;
      grid.appendChild(row);
    });

    initQtyButtons();
    recalcTotal();
  }

  /* ── 1. Thumbnail gallery ─────────────────────────────────── */
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


  /* ── 2. Colour swatches ──────────────────────────────────── */
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


  /* ── 3. Decoration option tiles ─────────────────────────── */
  function initOptionTiles() {
    // Map of group ID → label element ID
    const LABEL_MAP = {
      tech: 'pdTechName',
      area: 'pdAreaName',
    };

    document.querySelectorAll('#pd-detail .pd-option-tile').forEach(tile => {
      tile.addEventListener('click', function () {
        const group = this.dataset.optionGroup;
        const value = this.dataset.value;

        // Deselect all tiles in same group
        document.querySelectorAll(
          `#pd-detail .pd-option-tile[data-option-group="${group}"]`
        ).forEach(t => t.classList.remove('active'));

        // Select clicked
        this.classList.add('active');

        // Update label
        const labelEl = document.getElementById(LABEL_MAP[group]);
        if (labelEl) labelEl.textContent = value;
      });
    });
  }


  /* ── 3. Qty +/− buttons ─────────────────────────────────── */
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


  /* ── 4. Live price calc ──────────────────────────────────── */
  const totalUnitsEl = document.getElementById('pdTotalUnits');
  const estTotalEl   = document.getElementById('pdEstTotal');

  function recalcTotal() {
    let total = 0;
    let amount = 0;
    document.querySelectorAll('#pd-detail .pd-qty-input').forEach(inp => {
      const v = parseInt(inp.value, 10);
      const unit = parseFloat(inp.dataset.unitPrice || productData.min_price || 0);
      if (!isNaN(v) && v > 0) {
        total += v;
        amount += v * unit;
      }
    });
    if (totalUnitsEl) totalUnitsEl.textContent = total;
    if (estTotalEl) estTotalEl.innerHTML = formatMoney(amount);
  }


  /* ── 5. Description / Features tabs ─────────────────────── */
  document.querySelectorAll('#pd-desc .pd-tab').forEach(tab => {
    tab.addEventListener('click', function () {
      document.querySelectorAll('#pd-desc .pd-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('#pd-desc .pd-tab-panel').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
      const panel = document.getElementById(`pd-panel-${this.dataset.tab}`);
      if (panel) panel.classList.add('active');
    });
  });


  /* ── 6. Review modal ─────────────────────────────────────── */
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
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting…';

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


  /* ── Init ─────────────────────────────────────────────────── */
  function init() {
    initQtyButtons();
    initOptionTiles();
    recalcTotal();

    const customizeBtn = document.getElementById('pdCustomizeBtn');
    if (customizeBtn && productData.customizer_url) {
      customizeBtn.addEventListener('click', function (e) {
        const selectedSize = document.querySelector('#pdSizeQtyGrid .pd-qty-input[value]:not([value="0"])');
        let size = '';
        let qty = 0;
        document.querySelectorAll('#pdSizeQtyGrid .pd-size-row-item').forEach(row => {
          const input = row.querySelector('.pd-qty-input');
          const val = parseInt(input?.value || '0', 10);
          if (val > 0) {
            qty += val;
            if (!size) size = row.dataset.size;
          }
        });
        const url = new URL(productData.customizer_url, window.location.origin);
        if (size) url.searchParams.set('size', size);
        if (qty > 0) url.searchParams.set('qty', String(qty));
        if (activeColourCode) url.searchParams.set('colour', activeColourCode);
        this.href = url.pathname + url.search;
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();