/* ==============================================
   SHOP.JS — Filter UI, product rendering
   ============================================== */
(function () {
  'use strict';

  const shopData = window.__SHOP_DATA__ || {};
  const filtersMeta = shopData.filters || {};
  const products = (shopData.products || []).map(p => ({
    ...p,
    rating: '4.8',
    badge: p.is_new ? 'New In' : (p.is_clearance ? 'Clearance' : null),
  }));

  const BRANDS = filtersMeta.brands || [];
  const COLOURS = filtersMeta.colours || [];
  const priceFloor = filtersMeta.price_min ?? 0;
  const priceCeil = filtersMeta.price_max ?? 100;

  let activeColours = new Set();
  let activeBrands  = new Set();
  let activeTypes   = new Set();
  let priceMin = priceFloor;
  let priceMax = priceCeil;
  let sortMode  = 'featured';
  let viewMode  = 'grid';

  const PAGE_SIZE = 25;
  let visibleCount = PAGE_SIZE;
  let loadObserver = null;

  /* ── DOM refs ── */
  const grid         = document.getElementById('sbProductsGrid');
  const loadSentinel = document.getElementById('sbLoadSentinel');
  const resultCount  = document.getElementById('sbResultCount');
  const activeFilters= document.getElementById('sbActiveFilters');
  const filterCount  = document.getElementById('sbFilterCount');
  const brandsList   = document.getElementById('brandsList');
  const colourSwatches= document.getElementById('colourSwatches');
  const brandSearch  = document.getElementById('brandSearch');
  const priceRangeMin= document.getElementById('priceRangeMin');
  const priceRangeMax= document.getElementById('priceRangeMax');
  const priceFill    = document.getElementById('priceFill');
  const priceMinLbl  = document.getElementById('priceMin');
  const priceMaxLbl  = document.getElementById('priceMax');
  const sortSelect   = document.getElementById('sbSortSelect');
  const viewGridBtn  = document.getElementById('viewGrid');
  const viewListBtn  = document.getElementById('viewList');
  const clearAllBtn  = document.getElementById('sbClearAll');
  const mobileFilterBtn = document.getElementById('sbMobileFilterBtn');
  const sidebar      = document.getElementById('sbSidebar');
  const backdrop     = document.getElementById('sbBackdrop');
  const sidebarClose = document.getElementById('sbSidebarClose');
  const emptyState   = document.getElementById('sbEmptyState');

  /* ── Build brand checkboxes ── */
  function renderBrands(filter) {
    if (!brandsList) return;
    const f = (filter || '').toLowerCase();
    const filtered = f ? BRANDS.filter(b => b.toLowerCase().includes(f)) : BRANDS;
    brandsList.innerHTML = filtered.map(b => `
      <label class="sb-checkbox-item">
        <input type="checkbox" value="${b}" ${activeBrands.has(b) ? 'checked' : ''}>
        <span class="sb-checkbox-label">${b}</span>
      </label>
    `).join('');
    brandsList.querySelectorAll('input[type="checkbox"]').forEach(cb => {
      cb.addEventListener('change', () => {
        cb.checked ? activeBrands.add(cb.value) : activeBrands.delete(cb.value);
        renderAll();
      });
    });
  }

  /* ── Build colour swatches ── */
  function renderColours() {
    if (!colourSwatches) return;
    colourSwatches.innerHTML = COLOURS.map(c => `
      <button class="sb-swatch-btn ${activeColours.has(c.name) ? 'active' : ''}"
              style="background:${c.hex};"
              title="${c.name}"
              aria-label="${c.name}${activeColours.has(c.name) ? ' (selected)' : ''}">
      </button>
    `).join('');
    colourSwatches.querySelectorAll('.sb-swatch-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const name = btn.getAttribute('title');
        if (activeColours.has(name)) {
          activeColours.delete(name);
          btn.classList.remove('active');
        } else {
          activeColours.add(name);
          btn.classList.add('active');
        }
        renderAll();
      });
    });
  }

  /* ── Price range ── */
  function updatePriceFill() {
    if (!priceFill || !priceRangeMin || !priceRangeMax) return;
    const min = parseInt(priceRangeMin.value, 10);
    const max = parseInt(priceRangeMax.value, 10);
    const span = Math.max(priceCeil - priceFloor, 1);
    const pct = (v) => ((v - priceFloor) / span) * 100;
    priceFill.style.left  = pct(min) + '%';
    priceFill.style.width = Math.max(0, pct(max) - pct(min)) + '%';
    if (priceMinLbl) priceMinLbl.textContent = min <= priceFloor ? `£${priceFloor}` : `£${min}`;
    if (priceMaxLbl) priceMaxLbl.textContent = max >= priceCeil ? `£${priceCeil}+` : `£${max}`;
    priceMin = min;
    priceMax = max;
  }

  function getFiltered() {
    return products.filter(p => {
      if (p.min_price < priceMin || p.min_price > priceMax) return false;
      if (activeBrands.size && !activeBrands.has(p.brand)) return false;
      if (activeTypes.size && !activeTypes.has(p.type)) return false;
      if (activeColours.size) {
        const names = (p.colours || []).map(c => c.name);
        const match = names.some(name => activeColours.has(name));
        if (!match) return false;
      }
      return true;
    });
  }

  function getSorted(list) {
    const copy = [...list];
    switch (sortMode) {
      case 'az':         return copy.sort((a,b) => a.title.localeCompare(b.title));
      case 'za':         return copy.sort((a,b) => b.title.localeCompare(a.title));
      case 'price-asc':  return copy.sort((a,b) => a.min_price - b.min_price);
      case 'price-desc': return copy.sort((a,b) => b.min_price - a.min_price);
      default:           return copy;
    }
  }

  function getCurrentList() {
    return getSorted(getFiltered());
  }

  function productCardHtml(p) {
    const badgeHtml = p.badge ? `<span class="shop-card-badge">${p.badge}</span>` : '';
    const whole = Math.floor(p.min_price);
    const dec = String(Math.round((p.min_price - whole) * 100)).padStart(2, '0');
    const price = `£${whole}.${dec}`;
    const url = p.url || '#';
    return `
      <article class="shop-card">
        <div class="shop-card-img">
          <img src="${p.image}" alt="${p.title}" loading="lazy">
          <div class="shop-card-actions">
            <a href="${url}" class="shop-card-action-btn">
              <i class="bi bi-eye"></i> View
            </a>
          </div>
          ${badgeHtml}
        </div>
        <div class="shop-card-body">
          <div class="shop-card-meta">
            <span class="shop-card-cat"><i class="bi bi-tag"></i> ${p.category_name || ''}</span>
            <span class="shop-card-rating"><i class="bi bi-star-fill"></i> ${p.rating}</span>
          </div>
          <h3 class="shop-card-name">${p.title}</h3>
          <div class="shop-card-footer">
            <div class="shop-card-price">
              <span class="shop-card-from">From</span>
              <span class="shop-card-amount">${price}</span>
              <span class="shop-card-unit">/ unit</span>
            </div>
            <a href="${url}" class="shop-card-btn">
              <i class="bi bi-bag-plus"></i> View Product
            </a>
          </div>
        </div>
      </article>
    `;
  }

  function updateResultCount(shown, total) {
    if (!resultCount) return;
    if (total === 0) {
      resultCount.innerHTML = 'Showing <strong>0</strong> products';
    } else if (shown < total) {
      resultCount.innerHTML = `Showing <strong>${shown}</strong> of <strong>${total}</strong> products`;
    } else {
      resultCount.innerHTML = `Showing <strong>${total}</strong> products`;
    }
  }

  function updateLoadSentinel(total) {
    if (!loadSentinel) return;

    const hasMore = visibleCount < total;
    loadSentinel.style.display = hasMore ? 'flex' : 'none';

    if (loadObserver) {
      loadObserver.disconnect();
      loadObserver = null;
    }

    if (!hasMore) return;

    loadObserver = new IntersectionObserver((entries) => {
      if (entries.some(entry => entry.isIntersecting)) {
        loadMoreProducts();
      }
    }, { rootMargin: '240px' });

    loadObserver.observe(loadSentinel);
  }

  function loadMoreProducts() {
    const list = getCurrentList();
    if (visibleCount >= list.length) {
      updateLoadSentinel(list.length);
      return;
    }

    if (loadObserver) {
      loadObserver.disconnect();
      loadObserver = null;
    }

    visibleCount = Math.min(visibleCount + PAGE_SIZE, list.length);
    renderProducts(false);
  }

  function renderProducts(reset = true) {
    if (!grid) return;

    const list = getCurrentList();

    if (reset) {
      visibleCount = Math.min(PAGE_SIZE, list.length);
      grid.innerHTML = '';
    }

    const toShow = list.slice(0, visibleCount);
    updateResultCount(toShow.length, list.length);

    if (emptyState) emptyState.style.display = list.length ? 'none' : 'block';
    if (loadSentinel) loadSentinel.style.display = list.length && visibleCount < list.length ? 'flex' : 'none';

    if (viewMode === 'list') {
      grid.classList.add('list-view');
    } else {
      grid.classList.remove('list-view');
    }

    if (reset) {
      grid.innerHTML = toShow.map(productCardHtml).join('');
    } else {
      const rendered = grid.children.length;
      const newItems = list.slice(rendered, visibleCount);
      if (newItems.length) {
        grid.insertAdjacentHTML('beforeend', newItems.map(productCardHtml).join(''));
      }
    }

    updateLoadSentinel(list.length);
  }

  /* ── Active filter pills ── */
  function renderActiveFilters() {
    if (!activeFilters) return;
    const pills = [];
    activeBrands.forEach(b => pills.push({ label: b, type: 'brand', value: b }));
    activeTypes.forEach(t => pills.push({ label: t, type: 'type', value: t }));
    activeColours.forEach(c => pills.push({ label: c, type: 'colour', value: c }));
    if (priceMin > priceFloor || priceMax < priceCeil) {
      pills.push({ label: `£${priceMin}–${priceMax >= priceCeil ? '£' + priceCeil + '+' : '£' + priceMax}`, type: 'price', value: 'price' });
    }
    activeFilters.innerHTML = pills.map(p => `
      <span class="sb-filter-pill" data-type="${p.type}" data-value="${p.value}">
        ${p.label}
        <button aria-label="Remove filter ${p.label}">✕</button>
      </span>
    `).join('');
    activeFilters.querySelectorAll('.sb-filter-pill button').forEach(btn => {
      btn.addEventListener('click', () => {
        const pill = btn.parentElement;
        const type  = pill.dataset.type;
        const value = pill.dataset.value;
        if (type === 'brand') { activeBrands.delete(value); renderBrands(brandSearch ? brandSearch.value : ''); }
        if (type === 'type')  { activeTypes.delete(value); syncTypeCheckboxes(); }
        if (type === 'colour') { activeColours.delete(value); renderColours(); }
        if (type === 'price') {
          priceMin = priceFloor; priceMax = priceCeil;
          if (priceRangeMin) priceRangeMin.value = priceFloor;
          if (priceRangeMax) priceRangeMax.value = priceCeil;
          updatePriceFill();
        }
        renderAll();
      });
    });

    // Filter count on mobile button
    if (filterCount) {
      const count = pills.length;
      filterCount.textContent = count;
      filterCount.style.display = count > 0 ? 'inline-flex' : 'none';
    }
  }

  function syncTypeCheckboxes() {
    document.querySelectorAll('#sfgType input[type="checkbox"]').forEach(cb => {
      cb.checked = activeTypes.has(cb.value);
    });
  }

  function renderAll() {
    renderProducts();
    renderActiveFilters();
  }

  /* ── Filter group toggles ── */
  document.querySelectorAll('.sb-filter-toggle').forEach(btn => {
    const group = btn.closest('.sb-filter-group');
    btn.addEventListener('click', () => {
      group.classList.toggle('collapsed');
      btn.setAttribute('aria-expanded', !group.classList.contains('collapsed'));
    });
  });

  /* ── Product type checkboxes ── */
  document.querySelectorAll('#sfgType input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', () => {
      cb.checked ? activeTypes.add(cb.value) : activeTypes.delete(cb.value);
      renderAll();
    });
  });

  /* ── Price range ── */
  if (priceRangeMin && priceRangeMax) {
    priceRangeMin.addEventListener('input', () => {
      if (parseInt(priceRangeMin.value) > parseInt(priceRangeMax.value) - 5) {
        priceRangeMin.value = parseInt(priceRangeMax.value) - 5;
      }
      updatePriceFill(); renderAll();
      document.querySelectorAll('.sb-preset-btn').forEach(b => b.classList.remove('active'));
    });
    priceRangeMax.addEventListener('input', () => {
      if (parseInt(priceRangeMax.value) < parseInt(priceRangeMin.value) + 5) {
        priceRangeMax.value = parseInt(priceRangeMin.value) + 5;
      }
      updatePriceFill(); renderAll();
      document.querySelectorAll('.sb-preset-btn').forEach(b => b.classList.remove('active'));
    });
    updatePriceFill();
  }

  /* Price presets */
  document.querySelectorAll('.sb-preset-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.sb-preset-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if (priceRangeMin) priceRangeMin.value = btn.dataset.min;
      if (priceRangeMax) priceRangeMax.value = btn.dataset.max;
      updatePriceFill(); renderAll();
    });
  });

  /* ── Brand search ── */
  if (brandSearch) {
    brandSearch.addEventListener('input', () => renderBrands(brandSearch.value));
  }

  /* ── Sort ── */
  if (sortSelect) {
    sortSelect.addEventListener('change', () => { sortMode = sortSelect.value; renderAll(); });
  }

  /* ── View toggle ── */
  if (viewGridBtn) {
    viewGridBtn.addEventListener('click', () => {
      viewMode = 'grid';
      viewGridBtn.classList.add('active');
      if (viewListBtn) viewListBtn.classList.remove('active');
      renderAll();
    });
  }
  if (viewListBtn) {
    viewListBtn.addEventListener('click', () => {
      viewMode = 'list';
      viewListBtn.classList.add('active');
      if (viewGridBtn) viewGridBtn.classList.remove('active');
      renderAll();
    });
  }

  /* ── Clear all ── */
  if (clearAllBtn) {
    clearAllBtn.addEventListener('click', () => {
      activeBrands.clear();
      activeTypes.clear();
      activeColours.clear();
      priceMin = priceFloor; priceMax = priceCeil;
      if (priceRangeMin) priceRangeMin.value = priceFloor;
      if (priceRangeMax) priceRangeMax.value = priceCeil;
      updatePriceFill();
      document.querySelectorAll('#sfgType input[type="checkbox"]').forEach(cb => cb.checked = false);
      renderBrands('');
      renderColours();
      renderAll();
    });
  }

  if (!products.length && grid) {
    grid.innerHTML = '';
    if (emptyState) emptyState.style.display = 'block';
  }

  /* ── Mobile sidebar ── */
  if (mobileFilterBtn) {
    mobileFilterBtn.addEventListener('click', () => {
      sidebar.classList.add('open');
      backdrop.classList.add('visible');
    });
  }
  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (backdrop) backdrop.classList.remove('visible');
  }
  if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  /* ── Init ── */
  if (products.length) {
    renderBrands();
    renderColours();
    updatePriceFill();
    renderAll();
  }

})();
