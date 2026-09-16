/* ============================================================
   Uniform Shop — public site module
   ============================================================ */
(function () {
  const T = window.THREADS;
  const U = T.util || {};
  const money = U.money || ((v) => '£' + Number(v).toFixed(2));
  const esc = (s) =>
    String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

  const cfg = () => window.UNIFORM_SHOP || {};
  const CART_KEY = 'threads_uniform_cart_v1';

  let _cart = [];
  let _school = null;
  let _product = null;
  let _stripeCtx = null;
  let _unlocked = false;

  function rootEl() {
    return document.getElementById('uniformShopRoot');
  }

  function loadPersisted() {
    try {
      const raw = localStorage.getItem(CART_KEY);
      if (!raw) return;
      const data = JSON.parse(raw);
      _cart = Array.isArray(data.items) ? data.items : [];
      if (data.school) _school = data.school;
    } catch (e) {
      _cart = [];
    }
  }

  function persistCart() {
    try {
      localStorage.setItem(CART_KEY, JSON.stringify({ items: _cart, school: _school }));
    } catch (e) { /* ignore */ }
  }

  function cartTotal() {
    return _cart.reduce((s, i) => s + i.price * i.qty, 0);
  }
  function cartCount() {
    return _cart.reduce((s, i) => s + i.qty, 0);
  }

  function addToCart(variantId, productId, productName, size, price, qty) {
    const existing = _cart.find((i) => i.variantId === variantId);
    if (existing) existing.qty += qty;
    else _cart.push({ variantId, productId, productName, size, price, qty });
    persistCart();
  }

  function removeFromCart(variantId) {
    _cart = _cart.filter((i) => i.variantId !== variantId);
    persistCart();
  }

  function updateCartQty(variantId, qty) {
    const item = _cart.find((i) => i.variantId === variantId);
    if (!item) return;
    item.qty = qty;
    if (item.qty <= 0) removeFromCart(variantId);
    else persistCart();
  }

  function shopRender(view, data) {
    const el = rootEl();
    if (!el) return;
    _stripeCtx = null;
    switch (view) {
      case 'pin': el.innerHTML = viewPinGate(); break;
      case 'school': el.innerHTML = viewSchool(data); break;
      case 'product': el.innerHTML = viewProduct(data); break;
      case 'cart': el.innerHTML = viewCart(); break;
      case 'checkout': el.innerHTML = viewCheckout(); break;
      case 'confirmation': el.innerHTML = viewConfirmation(data); break;
    }
    wireShop();
    if (view === 'checkout') initStripeCheckout();
    if (view === 'pin') wirePinGate();
  }

  async function initStripeCheckout() {
    if (!window.TEStripeCheckout) {
      const errEl = document.getElementById('stripeCardErrors');
      if (errEl) errEl.textContent = 'Payment library failed to load. Please refresh the page.';
      return;
    }
    try {
      _stripeCtx = await window.TEStripeCheckout.initCardElement({
        mountSelector: '#stripeCardElement',
        errorSelector: '#stripeCardErrors',
      });
    } catch (err) {
      const errEl = document.getElementById('stripeCardErrors');
      if (errEl) errEl.textContent = err.message || 'Could not load payment form.';
      T.toast && T.toast(err.message || 'Could not load payment form.');
    }
  }

  async function apiGet(url) {
    const res = await fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
      credentials: 'same-origin',
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok) throw { data: json, status: res.status };
    return json;
  }

  async function apiPost(url, body) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!res.ok) throw { data: json, status: res.status };
    return json;
  }

  function shopHeader(title, backView, backLabel, backData) {
    const cartBadge = cartCount() > 0
      ? `<button type="button" class="su-cart-btn" data-shop-nav="cart"><i class="bi bi-cart3"></i> Cart <span class="su-cart-badge">${cartCount()}</span></button>`
      : `<button type="button" class="su-cart-btn su-cart-empty" data-shop-nav="cart"><i class="bi bi-cart3"></i> Cart</button>`;

    let back = '';
    if (backView === 'schools') {
      back = `<a class="su-back-btn" href="${esc(cfg().schoolsUrl || '/uniforms')}"><i class="bi bi-arrow-left"></i> ${esc(backLabel || 'All schools')}</a>`;
    } else if (backView) {
      back = `<button type="button" class="su-back-btn" data-shop-nav="${backView}" ${backData ? `data-shop-data='${JSON.stringify(backData)}'` : ''}><i class="bi bi-arrow-left"></i> ${esc(backLabel || 'Back')}</button>`;
    }

    return `<div class="su-header">
      <div class="su-header-left">${back}<h2>${esc(title)}</h2></div>
      <div class="su-header-right">${cartBadge}</div>
    </div>`;
  }

  function viewPinGate() {
    const school = cfg().school || _school || {};
    return `<div class="su-pin-gate panel-card">
      <div class="su-pin-icon"><i class="bi bi-shield-lock"></i></div>
      <h3>Enter school PIN</h3>
      <p class="muted">This catalogue is protected. Ask your school for the 4-digit PIN to continue.</p>
      <form id="suPinForm" class="su-pin-form" novalidate>
        <label class="lbl" for="suPinInput">4-digit PIN</label>
        <input class="input su-pin-input" id="suPinInput" name="pin" type="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" autocomplete="one-time-code" placeholder="••••" required>
        <p class="su-pin-error" id="suPinError" role="alert"></p>
        <button type="submit" class="btn btn--block" id="suPinSubmit"><i class="bi bi-unlock"></i> Unlock catalogue</button>
      </form>
      <a class="link-arrow" href="${esc(cfg().schoolsUrl || '/uniforms')}" style="margin-top:1rem;display:inline-flex">Back to all schools</a>
    </div>`;
  }

  function wirePinGate() {
    const form = document.getElementById('suPinForm');
    if (!form) return;
    form.onsubmit = async (e) => {
      e.preventDefault();
      const input = document.getElementById('suPinInput');
      const err = document.getElementById('suPinError');
      const btn = document.getElementById('suPinSubmit');
      const pin = String(input?.value || '').replace(/\D/g, '').slice(0, 4);
      if (pin.length !== 4) {
        if (err) err.textContent = 'Please enter a 4-digit PIN.';
        return;
      }
      if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Checking…'; }
      try {
        await apiPost(cfg().unlockUrl, { pin });
        _unlocked = true;
        await loadSchool(cfg().school?.id);
      } catch (ex) {
        const msg = ex?.data?.errors?.pin?.[0] || ex?.data?.message || 'Incorrect PIN. Please try again.';
        if (err) err.textContent = msg;
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-unlock"></i> Unlock catalogue'; }
      }
    };
  }

  function qtyControl(variantId, value, price, extraClass) {
    const priceAttr = price != null ? ` data-price="${price}"` : '';
    return `<div class="su-qty-wrap ${extraClass || ''}">
      <button type="button" class="su-qty-btn su-qty-minus" data-variant-id="${variantId}" aria-label="Decrease"><i class="bi bi-dash"></i></button>
      <input class="su-qty-input" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="3" value="${value}" data-variant-id="${variantId}"${priceAttr} aria-label="Quantity">
      <button type="button" class="su-qty-btn su-qty-plus" data-variant-id="${variantId}" aria-label="Increase"><i class="bi bi-plus"></i></button>
    </div>`;
  }

  function viewSchools(schools) {
    if (!schools) {
      return `<div class="portal-head"><h1>Uniform Shop</h1><p class="muted">Loading schools…</p></div>
              <div class="su-spinner"><span class="su-spin"></span></div>`;
    }
    if (!schools.length) {
      return `<div class="portal-head"><h1>Uniform Shop</h1></div>
              <div class="panel-card"><p class="muted" style="text-align:center;padding:2rem">No schools available yet.</p></div>`;
    }
    return `${shopHeader('Uniform Shop')}
    <p class="su-subtitle">Select your school to browse uniforms</p>
    <div class="su-school-grid">
      ${schools.map((s) => `
        <article class="su-school-card" style="--accent:${esc(s.accent_colour || '#e85820')}">
          <div class="su-school-logo">
            ${s.logo_url
              ? `<img src="${esc(s.logo_url)}" alt="${esc(s.name)}">`
              : `<span class="su-school-initial">${esc(s.name.charAt(0))}</span>`}
          </div>
          <div class="su-school-body">
            <h3>${esc(s.name)}</h3>
            ${s.description ? `<p>${esc(s.description)}</p>` : ''}
          </div>
          <button type="button" class="btn su-school-cta" data-shop-nav="school" data-school-id="${s.id}">
            View Products <i class="bi bi-arrow-right"></i>
          </button>
        </article>`).join('')}
    </div>`;
  }

  function viewSchool(data) {
    if (!data) return `<div class="su-spinner"><span class="su-spin"></span></div>`;
    const { school, categories, uncategorised } = data;

    function productCard(p) {
      return `<article class="su-prod-card" data-shop-nav="product" data-product-id="${p.id}" role="button" tabindex="0">
        <div class="su-prod-thumb">
          ${p.primary_image
            ? `<img src="${esc(p.primary_image)}" alt="${esc(p.name)}">`
            : `<div class="su-prod-placeholder"><i class="bi bi-bag"></i></div>`}
        </div>
        <div class="su-prod-info">
          <b>${esc(p.name)}</b>
          ${p.category_name ? `<span class="su-prod-meta">${esc(p.category_name)}</span>` : ''}
          ${p.variants.length
            ? `<span class="su-from">From ${money(p.from_price)}</span>`
            : '<span class="su-from muted">No sizes set</span>'}
          <span class="su-prod-cta">View product <i class="bi bi-arrow-right"></i></span>
        </div>
      </article>`;
    }

    let sections = '';
    if (categories.length) {
      sections += categories.map((cat) => cat.products.length ? `
        <div class="su-cat-section">
          <h3 class="su-cat-title">${esc(cat.name)}</h3>
          ${cat.description ? `<p class="su-cat-desc muted">${esc(cat.description)}</p>` : ''}
          <div class="su-prod-grid">${cat.products.map(productCard).join('')}</div>
        </div>` : '').join('');
    }
    if (uncategorised.length) {
      sections += `<div class="su-cat-section">
        <h3 class="su-cat-title">Other Products</h3>
        <div class="su-prod-grid">${uncategorised.map(productCard).join('')}</div>
      </div>`;
    }
    if (!sections) {
      sections = `<div class="panel-card"><p class="muted" style="text-align:center;padding:2rem">No products available for this school yet.</p></div>`;
    }

    return `${shopHeader(school.name, 'schools', 'All Schools')}
    ${sections}`;
  }

  function viewProduct(data) {
    if (!data) return `<div class="su-spinner"><span class="su-spin"></span></div>`;
    const { product, school } = data;

    const images = product.images && product.images.length
      ? `<div class="su-prod-images">
          <img id="suMainImg" src="${esc(product.images[0].url)}" alt="${esc(product.name)}" class="su-main-img">
          ${product.images.length > 1
            ? `<div class="su-thumbs">${product.images.map((img, i) =>
                `<button type="button" class="su-thumb ${i === 0 ? 'active' : ''}" data-img-src="${esc(img.url)}"><img src="${esc(img.url)}" alt=""></button>`
              ).join('')}</div>`
            : ''}
        </div>`
      : `<div class="su-prod-images su-no-image"><i class="bi bi-bag" style="font-size:3.5rem;color:var(--muted)"></i></div>`;

    const variants = product.variants;
    const sizeRows = variants.length
      ? `<div class="su-size-section">
          <h4>Select Sizes & Quantities</h4>
          <div class="su-size-table">
            <div class="su-size-header"><span>Size</span><span>Price</span><span>Qty</span></div>
            ${variants.map((v) => `
              <div class="su-size-row" data-variant-id="${v.id}">
                <span class="su-size-name">${esc(v.size)}</span>
                <span class="su-size-price">${money(v.price)}</span>
                ${qtyControl(v.id, 0, v.price)}
              </div>`).join('')}
          </div>
          <div class="su-prod-total" id="suProdTotal" hidden>
            <span>Selected total:</span><b id="suProdTotalVal"></b>
          </div>
        </div>`
      : `<p class="muted">No sizes available for this product.</p>`;

    return `${shopHeader(product.name, 'school', school.name, { id: school.id })}
    <div class="su-product-layout">
      ${images}
      <div class="su-product-detail">
        <h3 class="su-product-name">${esc(product.name)}</h3>
        ${product.category_name ? `<p class="su-prod-cat muted">${esc(product.category_name)}</p>` : ''}
        ${product.description ? `<p class="su-prod-desc">${esc(product.description)}</p>` : ''}
        ${sizeRows}
        <button type="button" class="su-add-cart btn btn--block" id="suAddCart" disabled><i class="bi bi-cart-plus"></i> Add to Cart</button>
      </div>
    </div>`;
  }

  function viewCart() {
    if (!_cart.length) {
      return `${shopHeader('Your Cart', _school ? 'school' : 'schools', _school ? _school.name : 'Back', _school ? { id: _school.id } : null)}
      <div class="panel-card" style="text-align:center;padding:3rem 1rem">
        <i class="bi bi-cart-x" style="font-size:3rem;color:var(--muted);display:block;margin-bottom:1rem"></i>
        <p class="muted">Your cart is empty.</p>
        <button type="button" class="btn btn--ghost" style="margin-top:1rem" data-shop-nav="${_school ? 'school' : 'schools'}" ${_school ? `data-school-id="${_school.id}"` : ''}>Continue Shopping</button>
      </div>`;
    }

    const subtotal = cartTotal();
    const vat = subtotal * 0.20;
    const delivery = subtotal >= 50 ? 0 : 4.99;
    const total = subtotal + vat + delivery;

    return `${shopHeader('Your Cart', _school ? 'school' : 'schools', _school ? _school.name : 'Back', _school ? { id: _school.id } : null)}
    <div class="su-cart-layout">
      <div class="su-cart-items">
        ${_cart.map((item) => `
          <div class="su-cart-row" data-variant-id="${item.variantId}">
            <div class="su-cart-info">
              <b>${esc(item.productName)}</b>
              <span class="su-cart-size">Size: <strong>${esc(item.size)}</strong></span>
              <span class="muted">${money(item.price)} each</span>
            </div>
            ${qtyControl(item.variantId, item.qty, null, 'su-cart-qty')}
            <div class="su-cart-line">${money(item.price * item.qty)}</div>
            <button type="button" class="su-remove-btn" data-variant-id="${item.variantId}" aria-label="Remove"><i class="bi bi-x"></i></button>
          </div>`).join('')}
      </div>
      <div class="su-cart-summary panel-card">
        <h4>Order Summary</h4>
        <div class="su-summary-line"><span>Subtotal</span><span>${money(subtotal)}</span></div>
        <div class="su-summary-line"><span>VAT (20%)</span><span>${money(vat)}</span></div>
        <div class="su-summary-line"><span>Delivery</span><span>${delivery === 0 ? '<span style="color:var(--ok)">Free</span>' : money(delivery)}</span></div>
        ${delivery > 0 ? `<p class="su-free-del-note muted">Free delivery on orders over £50</p>` : ''}
        <div class="su-summary-total"><span>Total</span><b>${money(total)}</b></div>
        <button type="button" class="btn btn--block su-checkout-btn" data-shop-nav="checkout"><i class="bi bi-lock"></i> Proceed to Checkout</button>
      </div>
    </div>`;
  }

  function viewCheckout() {
    const user = cfg().user || {};
    const subtotal = cartTotal();
    const vat = subtotal * 0.20;
    const delivery = subtotal >= 50 ? 0 : 4.99;
    const total = subtotal + vat + delivery;

    return `${shopHeader('Checkout', 'cart', 'Back to Cart')}
    <div class="su-checkout-layout">
      <form id="suCheckoutForm" class="su-checkout-form" novalidate>
        <div class="panel-card">
          <div class="su-checkout-section-head"><span class="n">1</span><div><h4>Contact Details</h4><p class="muted">We'll use these to confirm your order.</p></div></div>
          <div class="su-form-row">
            <div class="field-group"><label class="lbl">Full Name <span class="req">*</span></label><input class="input" name="name" value="${esc(user.name || '')}" required autocomplete="name"></div>
            <div class="field-group"><label class="lbl">Email <span class="req">*</span></label><input class="input" type="email" name="email" value="${esc(user.email || '')}" required autocomplete="email"></div>
          </div>
          <div class="su-form-row">
            <div class="field-group"><label class="lbl">Phone</label><input class="input" type="tel" name="phone" value="${esc(user.phone || '')}" autocomplete="tel"></div>
            <div class="field-group"><label class="lbl">Company / Organisation</label><input class="input" name="company_name" value="${esc(user.company_name || '')}" autocomplete="organization"></div>
          </div>
        </div>

        <div class="panel-card" style="margin-top:1rem">
          <div class="su-checkout-section-head"><span class="n">2</span><div><h4>Shipping Address</h4><p class="muted">Where should we deliver your order?</p></div></div>
          <div class="field-group"><label class="lbl">Address Line 1 <span class="req">*</span></label><input class="input" name="shipping_address_line1" required placeholder="Street address"></div>
          <div class="field-group"><label class="lbl">Address Line 2</label><input class="input" name="shipping_address_line2" placeholder="Apartment, suite, etc."></div>
          <div class="su-form-row">
            <div class="field-group"><label class="lbl">City / Town <span class="req">*</span></label><input class="input" name="shipping_city" required></div>
            <div class="field-group"><label class="lbl">Postcode <span class="req">*</span></label><input class="input" name="shipping_postcode" required></div>
          </div>
          <div class="field-group"><label class="lbl">Country</label><input class="input" name="shipping_country" value="United Kingdom"></div>
        </div>

        <div class="panel-card" style="margin-top:1rem">
          <div class="su-checkout-section-head"><span class="n">3</span><div><h4>Billing Address</h4><p class="muted">For invoices and payment records.</p></div></div>
          <label class="su-checkout-same">
            <input type="checkbox" id="suBillingSame" name="billing_same_as_shipping" value="1" checked>
            <span>Billing address is the same as shipping address</span>
          </label>
          <div id="suBillingFields" class="su-billing-fields" hidden>
            <div class="su-form-row">
              <div class="field-group"><label class="lbl">Billing Name <span class="req">*</span></label><input class="input" name="billing_name" value="${esc(user.name || '')}" placeholder="Name on invoice"></div>
              <div class="field-group"><label class="lbl">Billing Company</label><input class="input" name="billing_company_name" value="${esc(user.company_name || '')}" placeholder="Optional"></div>
            </div>
            <div class="field-group"><label class="lbl">Address Line 1 <span class="req">*</span></label><input class="input" name="billing_address_line1" placeholder="Street address"></div>
            <div class="field-group"><label class="lbl">Address Line 2</label><input class="input" name="billing_address_line2" placeholder="Apartment, suite, etc."></div>
            <div class="su-form-row">
              <div class="field-group"><label class="lbl">City / Town <span class="req">*</span></label><input class="input" name="billing_city"></div>
              <div class="field-group"><label class="lbl">Postcode <span class="req">*</span></label><input class="input" name="billing_postcode"></div>
            </div>
            <div class="field-group"><label class="lbl">Country</label><input class="input" name="billing_country" value="United Kingdom"></div>
          </div>
        </div>

        <div class="panel-card" style="margin-top:1rem">
          <div class="su-checkout-section-head"><span class="n">4</span><div><h4>Payment</h4><p class="muted">Pay securely by card — your details stay on this page.</p></div></div>
          <div id="stripeCardElement" class="su-stripe-element"></div>
          <p id="stripeCardErrors" class="su-stripe-error" role="alert"></p>
          <p class="help muted" style="margin-top:.55rem;font-size:.82rem"><i class="bi bi-shield-lock"></i> Payments are processed securely by Stripe.</p>
        </div>

        <div class="panel-card" style="margin-top:1rem">
          <div class="su-checkout-section-head"><span class="n">5</span><div><h4>Order Notes</h4><p class="muted">Optional — anything we should know before production.</p></div></div>
          <div class="field-group"><textarea class="input textarea" name="notes" rows="3" placeholder="Delivery instructions, artwork notes, deadlines…"></textarea></div>
        </div>
      </form>

      <div class="su-cart-summary panel-card">
        <h4>Order Summary</h4>
        ${_cart.map((i) => `
          <div class="su-summary-item">
            <span><b>${esc(i.productName)}</b><br><span class="su-cart-size">Size: ${esc(i.size)}</span> × ${i.qty}</span>
            <span>${money(i.price * i.qty)}</span>
          </div>`).join('')}
        <hr style="border-color:var(--line);margin:.8rem 0">
        <div class="su-summary-line"><span>Subtotal</span><span>${money(subtotal)}</span></div>
        <div class="su-summary-line"><span>VAT (20%)</span><span>${money(vat)}</span></div>
        <div class="su-summary-line"><span>Delivery</span><span>${delivery === 0 ? '<span style="color:var(--ok)">Free</span>' : money(delivery)}</span></div>
        <div class="su-summary-total"><span>Total</span><b>${money(total)}</b></div>
        <button type="button" class="btn btn--block" id="suPlaceOrder"><i class="bi bi-lock-fill"></i> Pay &amp; Place Order</button>
        <p class="muted" style="text-align:center;font-size:.8rem;margin-top:.65rem"><i class="bi bi-shield-check"></i> Secure card payment via Stripe</p>
      </div>
    </div>`;
  }

  function viewConfirmation(orderData) {
    return `<div class="su-confirm">
      <div class="su-confirm-icon"><i class="bi bi-check-circle-fill"></i></div>
      <h2>Order Placed!</h2>
      <p>Thank you for your order. Your reference is <b>${esc(orderData?.order_number || '')}</b>.</p>
      <p class="muted">You'll receive a confirmation email shortly.</p>
      <div style="display:flex;gap:.8rem;justify-content:center;margin-top:1.6rem;flex-wrap:wrap">
        <a class="btn btn--ghost" href="${esc(cfg().schoolsUrl || '/uniforms')}"><i class="bi bi-shop"></i> Continue Shopping</a>
      </div>
    </div>`;
  }

  function readQty(input) {
    const n = parseInt(String(input?.value || '0').replace(/\D/g, ''), 10);
    return Number.isFinite(n) ? n : 0;
  }

  function setQtyInput(input, value) {
    if (!input) return;
    input.value = String(Math.max(0, Math.min(999, value)));
  }

  function syncProductTotal() {
    const el = rootEl();
    if (!el || !_product) return;
    let total = 0;
    el.querySelectorAll('.su-size-row .su-qty-input').forEach((input) => {
      total += readQty(input) * parseFloat(input.dataset.price || 0);
    });
    const hasQty = total > 0;
    const addBtn = el.querySelector('#suAddCart');
    if (addBtn) addBtn.disabled = !hasQty;
    const totalEl = el.querySelector('#suProdTotal');
    const totalValEl = el.querySelector('#suProdTotalVal');
    if (totalEl) totalEl.hidden = !hasQty;
    if (totalValEl) totalValEl.textContent = money(total);
  }

  function wireShop() {
    const el = rootEl();
    if (!el) return;

    const same = el.querySelector('#suBillingSame');
    const billingWrap = el.querySelector('#suBillingFields');
    if (same && billingWrap) {
      const syncBilling = () => {
        billingWrap.hidden = same.checked;
        billingWrap.querySelectorAll('input').forEach((inp) => {
          if (['billing_name', 'billing_address_line1', 'billing_city', 'billing_postcode'].includes(inp.name)) {
            inp.required = !same.checked;
          }
        });
      };
      same.onchange = syncBilling;
      syncBilling();
    }

    el.onclick = async (e) => {
      const qtyPlus = e.target.closest('.su-qty-plus');
      if (qtyPlus) {
        e.preventDefault();
        e.stopPropagation();
        const wrap = qtyPlus.closest('.su-qty-wrap');
        const input = wrap?.querySelector('.su-qty-input');
        const next = Math.min(999, readQty(input) + 1);
        setQtyInput(input, next);
        if (el.querySelector('.su-cart-items')) {
          updateCartQty(parseInt(qtyPlus.dataset.variantId, 10), next);
          shopRender('cart');
        } else {
          syncProductTotal();
        }
        return;
      }

      const qtyMinus = e.target.closest('.su-qty-minus');
      if (qtyMinus) {
        e.preventDefault();
        e.stopPropagation();
        const wrap = qtyMinus.closest('.su-qty-wrap');
        const input = wrap?.querySelector('.su-qty-input');
        const isCart = !!el.querySelector('.su-cart-items');
        const next = Math.max(isCart ? 0 : 0, readQty(input) - 1);
        setQtyInput(input, next);
        if (isCart) {
          updateCartQty(parseInt(qtyMinus.dataset.variantId, 10), next);
          shopRender('cart');
        } else {
          syncProductTotal();
        }
        return;
      }

      const navBtn = e.target.closest('[data-shop-nav]');
      if (navBtn) {
        e.preventDefault();
        const view = navBtn.dataset.shopNav;
        if (view === 'schools') {
          location.href = cfg().schoolsUrl || '/uniforms';
          return;
        } else if (view === 'school') {
          const schoolId = navBtn.dataset.schoolId || (navBtn.dataset.shopData && JSON.parse(navBtn.dataset.shopData).id) || cfg().school?.id;
          if (schoolId) await loadSchool(schoolId);
          else location.href = cfg().schoolsUrl || '/uniforms';
        } else if (view === 'product') {
          const prodId = navBtn.dataset.productId;
          if (prodId) await loadProduct(prodId);
        } else if (view === 'cart') shopRender('cart');
        else if (view === 'checkout') {
          if (!_unlocked) { shopRender('pin'); return; }
          shopRender('checkout');
        }
        return;
      }

      const removeBtn = e.target.closest('.su-remove-btn');
      if (removeBtn) {
        removeFromCart(parseInt(removeBtn.dataset.variantId, 10));
        shopRender('cart');
        return;
      }

      const thumb = e.target.closest('.su-thumb');
      if (thumb) {
        el.querySelectorAll('.su-thumb').forEach((t) => t.classList.remove('active'));
        thumb.classList.add('active');
        const mainImg = el.querySelector('#suMainImg');
        if (mainImg) mainImg.src = thumb.dataset.imgSrc;
        return;
      }

      const addBtn = e.target.closest('#suAddCart');
      if (addBtn && _product) {
        let added = 0;
        el.querySelectorAll('.su-size-row').forEach((row) => {
          const vid = parseInt(row.dataset.variantId, 10);
          const qty = readQty(row.querySelector('.su-qty-input'));
          if (qty > 0) {
            const variant = _product.product.variants.find((v) => v.id === vid);
            if (variant) {
              addToCart(vid, _product.product.id, _product.product.name, variant.size, variant.price, qty);
              added++;
            }
          }
        });
        if (added) {
          T.toast && T.toast(`${added} size${added > 1 ? 's' : ''} added to cart.`, true);
          shopRender('cart');
        } else {
          T.toast && T.toast('Please enter a quantity for at least one size.');
        }
        return;
      }

      if (e.target.closest('#suPlaceOrder')) {
        await placeOrder();
      }
    };

    el.oninput = (e) => {
      if (!e.target.classList.contains('su-qty-input')) return;
      e.target.value = String(e.target.value).replace(/\D/g, '').slice(0, 3);
      if (el.querySelector('.su-cart-items')) {
        const qty = Math.max(1, readQty(e.target) || 1);
        setQtyInput(e.target, qty);
        updateCartQty(parseInt(e.target.dataset.variantId, 10), qty);
      } else {
        syncProductTotal();
      }
    };

    el.onchange = (e) => {
      if (e.target.classList.contains('su-qty-input') && el.querySelector('.su-cart-items')) {
        shopRender('cart');
      }
    };
  }

  async function placeOrder() {
    const form = document.getElementById('suCheckoutForm');
    if (!form) return;
    const placeBtn = document.getElementById('suPlaceOrder');
    const errEl = document.getElementById('stripeCardErrors');
    if (placeBtn) { placeBtn.disabled = true; placeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing payment…'; }

    const fd = new FormData(form);
    const sameBilling = !!document.getElementById('suBillingSame')?.checked;
    const body = {
      school_id: _school?.id || null,
      items: _cart.map((i) => ({ variant_id: i.variantId, quantity: i.qty })),
      name: fd.get('name') || '',
      email: fd.get('email') || '',
      phone: fd.get('phone') || '',
      company_name: fd.get('company_name') || '',
      shipping_address_line1: fd.get('shipping_address_line1') || '',
      shipping_address_line2: fd.get('shipping_address_line2') || '',
      shipping_city: fd.get('shipping_city') || '',
      shipping_postcode: fd.get('shipping_postcode') || '',
      shipping_country: fd.get('shipping_country') || 'United Kingdom',
      billing_same_as_shipping: sameBilling,
      notes: fd.get('notes') || '',
    };

    if (!sameBilling) {
      body.billing_name = fd.get('billing_name') || '';
      body.billing_company_name = fd.get('billing_company_name') || '';
      body.billing_address_line1 = fd.get('billing_address_line1') || '';
      body.billing_address_line2 = fd.get('billing_address_line2') || '';
      body.billing_city = fd.get('billing_city') || '';
      body.billing_postcode = fd.get('billing_postcode') || '';
      body.billing_country = fd.get('billing_country') || 'United Kingdom';
    }

    if (!body.school_id && _school?.id) body.school_id = _school.id;

    if (!_stripeCtx) {
      const msg = 'Payment form is still loading. Please wait a moment.';
      if (errEl) errEl.textContent = msg;
      T.toast && T.toast(msg);
      if (placeBtn) { placeBtn.disabled = false; placeBtn.innerHTML = '<i class="bi bi-lock-fill"></i> Pay &amp; Place Order'; }
      return;
    }

    try {
      const paid = await window.TEStripeCheckout.pay({
        stripe: _stripeCtx.stripe,
        card: _stripeCtx.card,
        context: 'uniform',
        email: body.email,
        name: body.name,
        items: body.items,
        billing: {
          line1: sameBilling ? body.shipping_address_line1 : body.billing_address_line1,
          city: sameBilling ? body.shipping_city : body.billing_city,
          postal_code: sameBilling ? body.shipping_postcode : body.billing_postcode,
          country: 'GB',
        },
        onStatus: (msg) => {
          if (placeBtn) placeBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> ${msg}`;
        },
      });

      body.payment_intent_id = paid.paymentIntentId;
      if (placeBtn) placeBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Placing order…';

      const res = await apiPost(cfg().checkoutUrl || '/api/uniforms/checkout', body);
      _cart = [];
      persistCart();
      shopRender('confirmation', res);
    } catch (err) {
      const errors = err?.data?.errors;
      let msg = err?.data?.message || err.message || 'Could not place order. Please check your details.';
      if (errors && typeof errors === 'object') {
        const first = Object.values(errors)[0];
        if (Array.isArray(first) && first[0]) msg = first[0];
      }
      if (errEl) errEl.textContent = msg;
      T.toast && T.toast(msg);
      if (placeBtn) { placeBtn.disabled = false; placeBtn.innerHTML = '<i class="bi bi-lock-fill"></i> Pay &amp; Place Order'; }
    }
  }

  async function loadSchool(id) {
    shopRender('school', null);
    try {
      const url = cfg().catalogueUrl || `/api/uniforms/schools/${id}`;
      const data = await apiGet(url);
      _school = data.school;
      _unlocked = true;
      persistCart();
      shopRender('school', data);
    } catch (e) {
      if (e?.data?.errors?.pin || e?.status === 422 || e?.status === 403) {
        _unlocked = false;
        shopRender('pin');
        return;
      }
      T.toast && T.toast('Could not load school data.');
      location.href = cfg().schoolsUrl || '/uniforms';
    }
  }

  async function loadProduct(id) {
    shopRender('product', null);
    try {
      const base = cfg().productUrlBase || '/api/uniforms/products';
      const data = await apiGet(`${base}/${id}`);
      _product = data;
      if (data.school) {
        _school = data.school;
        persistCart();
      }
      shopRender('product', data);
    } catch (e) {
      if (e?.data?.errors?.pin || e?.status === 422 || e?.status === 403) {
        _unlocked = false;
        shopRender('pin');
        return;
      }
      T.toast && T.toast('Could not load product.');
    }
  }

  T.initPublicUniformShop = async function () {
    loadPersisted();
    _product = null;
    _school = cfg().school || _school;
    _unlocked = !!cfg().unlocked;
    if (!_school?.id) {
      location.href = cfg().schoolsUrl || '/uniforms';
      return;
    }
    if (!_unlocked) {
      shopRender('pin');
      return;
    }
    await loadSchool(_school.id);
  };
})();
