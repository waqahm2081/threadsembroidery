/* ============================================================
   Client Portal — server-backed auth + bundle orders
   ============================================================ */
(function(){
  const T = window.THREADS, U = T.util, money = U.money;
  const esc = (s) => U.escape ? U.escape(String(s ?? '')) : String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  function routes(){
    return window.THREADS_AUTH_ROUTES || {};
  }

  function firstError(err){
    const errors = err?.data?.errors;
    if (errors && typeof errors === 'object') {
      const first = Object.values(errors)[0];
      if (Array.isArray(first) && first[0]) return first[0];
    }
    return err?.data?.message || err?.message || 'Something went wrong.';
  }

  function wirePasswordToggles(root){
    (root || document).querySelectorAll('.password-toggle').forEach(btn => {
      if (btn.dataset.bound) return;
      btn.dataset.bound = '1';
      btn.addEventListener('click', () => {
        const wrap = btn.closest('.password-field');
        const input = wrap?.querySelector('input');
        if (!input) return;
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        btn.innerHTML = show ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      });
    });
  }

  async function doLogout(url){
    try {
      const res = await T.api.send(url || '/logout', { method: 'POST' });
      T.toast(res.message || 'Logged out.', true);
      setTimeout(() => location.href = res.redirect || '/', 350);
    } catch (e) {
      location.href = '/';
    }
  }

  /* ---------- LOGIN PAGE ---------- */
  T.initLogin = function(){
    const r = routes();
    const tabs = document.querySelectorAll('.auth-tabs button');
    const login = document.getElementById('loginForm');
    const signup = document.getElementById('signupForm');
    if (!login || !signup) return;

    wirePasswordToggles(document);

    const showTab = (t) => {
      tabs.forEach(x => x.classList.toggle('active', x.dataset.tab === t));
      login.style.display = t === 'login' ? '' : 'none';
      signup.style.display = t === 'signup' ? '' : 'none';
    };

    tabs.forEach(b => b.onclick = () => showTab(b.dataset.tab));
    if (location.hash === '#signup') showTab('signup');

    login.onsubmit = async (e) => {
      e.preventDefault();
      const btn = login.querySelector('[type="submit"]');
      btn.disabled = true;
      try {
        const body = {
          email: login.email.value.trim(),
          password: login.password.value,
          remember: !!login.remember?.checked
        };
        const res = await T.api.send(r.login || '/login', { body });
        T.toast(res.message || 'Welcome back!', true);
        setTimeout(() => location.href = res.redirect || r.portal || '/portal', 400);
      } catch (err) {
        T.toast(firstError(err));
        btn.disabled = false;
      }
    };

    signup.onsubmit = async (e) => {
      e.preventDefault();
      const btn = signup.querySelector('[type="submit"]');
      btn.disabled = true;
      try {
        const body = {
          name: signup.name.value.trim(),
          org: signup.org.value.trim(),
          email: signup.email.value.trim(),
          password: signup.password.value
        };
        const res = await T.api.send(r.register || '/register', { body });
        T.toast(res.message || 'Account created!', true);
        setTimeout(() => location.href = res.redirect || r.portal || '/portal', 400);
      } catch (err) {
        T.toast(firstError(err));
        btn.disabled = false;
      }
    };
  };

  /* ---------- PORTAL PAGE ---------- */
  T.initPortal = function(){
    const data = window.THREADS_PORTAL;
    if (!data || !data.user) {
      location.href = '/login';
      return;
    }

    let state = data;
    let accountTab = 'profile';
    let pendingAvatar = null;
    const user = () => state.user;
    const orders = () => state.orders || [];
    const enquiries = () => state.enquiries || [];
    const artwork = () => state.artwork || [];
    const stats = () => state.stats || {};
    const r = () => state.routes || {};

    function setSidebarUser(){
      const av = document.getElementById('pUserAv');
      const nameEl = document.getElementById('pUserName');
      const orgEl = document.getElementById('pUserOrg');
      if (!av || !nameEl || !orgEl) return;

      if (user().avatar_url) {
        av.classList.add('has-img');
        av.innerHTML = `<img src="${esc(user().avatar_url)}" alt="">`;
      } else {
        av.classList.remove('has-img');
        av.textContent = user().initials || 'U';
      }
      nameEl.textContent = user().name || 'User';
      orgEl.textContent = user().company_name || user().email || '';
      orgEl.title = user().company_name || user().email || '';
    }

    setSidebarUser();

    function statusClass(key){
      return ['production','dispatched','delivered','cancelled','review','quoted'].includes(key) ? key : 'production';
    }

    function passwordField(id, label, placeholder, autocomplete){
      return `<div class="field-group">
        <label class="lbl" for="${id}">${label}</label>
        <div class="password-field">
          <input class="input" id="${id}" type="password" placeholder="${placeholder}" autocomplete="${autocomplete}">
          <button type="button" class="password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button>
        </div>
      </div>`;
    }

    function orderRow(o, compact){
      const itemsLabel = o.item_count === 1
        ? (o.title || 'Bundle')
        : `${o.item_count} bundles`;
      const sub = [
        o.ref,
        `${o.garment_qty || 0} garments`,
        o.method,
        o.date
      ].filter(Boolean).join(' · ');

      return `<div class="order-row" data-order-id="${o.id}">
        <div class="order-thumb"><i class="bi bi-box-seam"></i></div>
        <div class="meta">
          <b>${esc(itemsLabel)}</b>
          <div class="sub">${esc(sub)}${o.summary ? ' · ' + esc(o.summary) : ''}</div>
          <div class="order-action-btns">
            <button type="button" class="btn btn--ghost btn--sm order-action-btn" data-view-order="${o.id}"><i class="bi bi-eye"></i> Details</button>
            ${compact ? '' : `<button type="button" class="btn btn--sm order-action-btn" data-reorder="${o.id}"><i class="bi bi-arrow-repeat"></i> Reorder</button>`}
          </div>
        </div>
        <span class="status ${statusClass(o.status_key)}">${esc(o.status)}</span>
        <div class="order-total">${money(o.total)}</div>
      </div>`;
    }

    function sizeLine(sizes){
      if (!sizes || typeof sizes !== 'object') return '';
      const parts = Object.entries(sizes).filter(([,q]) => Number(q) > 0).map(([s,q]) => `${s}: ${q}`);
      return parts.length ? `<div class="muted" style="font-size:.8rem;margin-top:.25rem">${esc(parts.join(' · '))}</div>` : '';
    }

    function itemFiles(files){
      if (!files?.length) return '<span class="muted" style="font-size:.8rem">No artwork uploaded</span>';
      return files.map(f =>
        `<button type="button" class="btn btn--ghost btn--sm art-btn" data-art-url="${esc(f.url)}" data-art-label="${esc(f.label || f.name || 'Artwork')}" data-art-name="${esc(f.name || '')}"><i class="bi bi-image"></i> ${esc(f.label || f.name || 'Artwork')}</button>`
      ).join(' ');
    }

    function orderDetail(o){
      const ship = o.shipping || {};
      const shipLine = [ship.line1, ship.line2, ship.city, ship.postcode, ship.country].filter(Boolean).join(', ');
      const itemsHtml = (o.items || []).map(item => {
        const garments = (item.garments || []).map(g => esc(g.name || g.title || g)).join(', ');
        return `<div class="order-item">
          <div class="flex justify-between gap-2">
            <div>
              <b>${esc(item.title)}</b>
              <div class="sub muted">${esc(item.summary || '')}</div>
              ${garments ? `<div class="muted" style="font-size:.8rem;margin-top:.2rem">Includes: ${garments}</div>` : ''}
              ${item.colour ? `<div class="muted" style="font-size:.8rem">Colour: ${esc(item.colour)}</div>` : ''}
              ${sizeLine(item.sizes)}
            </div>
            <div style="text-align:right;flex:none">
              <div class="order-total">${money(item.total)}</div>
              <div class="muted" style="font-size:.78rem">${item.quantity} garments · ${esc(item.method || '')}</div>
            </div>
          </div>
          <div class="order-item-art" style="margin-top:.7rem;display:flex;flex-wrap:wrap;gap:.4rem">${itemFiles(item.files)}</div>
        </div>`;
      }).join('');

      return `<div class="panel-card order-detail">
        <div class="flex justify-between items-center" style="margin-bottom:1rem;gap:1rem;flex-wrap:wrap">
          <button type="button" class="btn btn--ghost" data-go="orders"><i class="bi bi-arrow-left"></i> Back</button>
          <button type="button" class="btn" data-reorder="${o.id}"><i class="bi bi-arrow-repeat"></i> Reorder</button>
        </div>
        <div class="portal-head" style="margin-bottom:1.2rem">
          <h1 style="font-size:1.6rem;margin:0">${esc(o.ref)}</h1>
          <p class="muted" style="margin-top:.35rem">${esc(o.date)} · <span class="status ${statusClass(o.status_key)}">${esc(o.status)}</span></p>
        </div>
        <div class="order-items">${itemsHtml || '<p class="muted">No items on this order.</p>'}</div>
        <hr class="divider" style="margin:1.2rem 0">
        <div class="order-totals">
          <div><span>Subtotal</span><b>${money(o.subtotal)}</b></div>
          <div><span>Delivery</span><b>${money(o.delivery)}</b></div>
          <div><span>VAT</span><b>${money(o.vat)}</b></div>
          <div class="grand"><span>Total</span><b>${money(o.total)}</b></div>
        </div>
        ${shipLine ? `<div style="margin-top:1.2rem"><h3 style="font-size:1rem;margin-bottom:.4rem">Delivery</h3><p class="muted" style="margin:0">${esc(shipLine)}</p>${o.billing_same_as_shipping ? '<p class="muted" style="font-size:.8rem;margin-top:.3rem">Billing same as shipping</p>' : ''}</div>` : ''}
        ${o.notes ? `<div style="margin-top:1rem"><h3 style="font-size:1rem;margin-bottom:.4rem">Notes</h3><p class="muted" style="margin:0">${esc(o.notes)}</p></div>` : ''}
      </div>`;
    }

    const views = {};
    views.overview = () => {
      const first = (user().name || 'there').split(/\s+/)[0];
      const recent = orders().slice(0, 3);
      return `
      <div class="portal-head"><h1>Welcome back, ${esc(first)}</h1><p class="muted">Here's what's happening with your account.</p></div>
      <div class="kpi-grid">
        <div class="kpi"><div class="ic"><i class="bi bi-box-seam"></i></div><div class="n">${stats().orders || 0}</div><div class="l">Total orders</div></div>
        <div class="kpi"><div class="ic"><i class="bi bi-arrow-repeat"></i></div><div class="n">${stats().in_production || 0}</div><div class="l">In production</div></div>
        <div class="kpi"><div class="ic"><i class="bi bi-images"></i></div><div class="n">${stats().artwork || 0}</div><div class="l">Saved artworks</div></div>
        <div class="kpi"><div class="ic"><i class="bi bi-cash-stack"></i></div><div class="n">${money(stats().lifetime_spend || 0)}</div><div class="l">Lifetime spend</div></div>
      </div>
      <div class="panel-card"><div class="flex justify-between items-center" style="margin-bottom:.6rem"><h3 style="margin:0">Recent Orders</h3><button type="button" class="link-arrow" data-go="orders">View all <i class="bi bi-arrow-right"></i></button></div>
        ${recent.length ? recent.map(o => orderRow(o, true)).join('') : '<p class="muted" style="margin:0">No orders yet — start with a bundle pack.</p>'}</div>
      <div class="panel-card"><h3>Quick Actions</h3><div class="hero-cta" style="display:flex;gap:12px;flex-wrap:wrap">
        <a class="btn" href="${esc(r().bundles || '/bundles')}"><i class="bi bi-collection"></i> New Order</a>
        <a class="btn btn--ghost" href="${esc(r().branding || '/branding-enquiry')}"><i class="bi bi-chat-left-text"></i> New Enquiry</a>
        <button type="button" class="btn btn--ghost" data-go="artwork"><i class="bi bi-images"></i> My Artwork</button>
        <button type="button" class="btn btn--ghost" data-go="orders"><i class="bi bi-box-seam"></i> Order History</button>
      </div></div>`;
    };

    views.orders = () => `
      <div class="portal-head"><h1>Order History</h1><p class="muted">Track your bundle orders and reorder in one click.</p></div>
      <div class="panel-card">${orders().length ? orders().map(o => orderRow(o)).join('') : '<p class="muted" style="margin:0">No orders yet. <a class="link-arrow" href="' + esc(r().bundles || '/bundles') + '">Browse bundles</a></p>'}</div>`;

    function enquiryRow(e){
      const places = (e.placements || []).join(', ');
      const sub = [e.ref, e.enquiry_type, e.method, e.quantity ? (e.quantity + ' qty') : '', e.date].filter(Boolean).join(' · ');
      return `<div class="order-row enquiry-row">
        <div class="order-thumb"><i class="bi bi-chat-left-text"></i></div>
        <div class="meta">
          <b>${esc(e.enquiry_type || 'Branding Enquiry')}</b>
          <div class="sub">${esc(sub)}${places ? ' · ' + esc(places) : ''}</div>
          <div class="order-action-btns">
            <button type="button" class="btn btn--ghost btn--sm order-action-btn" data-view-enquiry="${e.id}"><i class="bi bi-eye"></i> Details</button>
          </div>
        </div>
        <span class="status ${statusClass(e.status_key)}">${esc(e.status)}</span>
      </div>`;
    }

    function enquiryDetail(e){
      const places = (e.placements || []).join(', ') || 'Not specified';
      const files = (e.files || []).length
        ? e.files.map(f => {
            const isImg = /\.(png|jpe?g|gif|webp|svg)(\?|$)/i.test(f.url || f.name || '');
            return `<a class="enquiry-file" href="${esc(f.url)}" target="_blank" rel="noopener">
              <span class="thumb">${isImg ? `<img src="${esc(f.url)}" alt="">` : `<i class="bi bi-file-earmark"></i>`}</span>
              <span class="meta"><b>${esc(f.name || 'File')}</b><span>${esc(f.type || '')}</span></span>
            </a>`;
          }).join('')
        : '<p class="muted" style="margin:0">No files attached.</p>';

      return `<div class="panel-card order-detail">
        <div class="flex justify-between items-center" style="margin-bottom:1rem;gap:1rem;flex-wrap:wrap">
          <button type="button" class="btn btn--ghost" data-go="enquiries"><i class="bi bi-arrow-left"></i> Back</button>
          <a class="btn" href="${esc(r().branding || '/branding-enquiry')}"><i class="bi bi-plus-lg"></i> New Enquiry</a>
        </div>
        <div class="portal-head" style="margin-bottom:1.2rem">
          <h1 style="font-size:1.6rem;margin:0">${esc(e.ref)}</h1>
          <p class="muted" style="margin-top:.35rem">${esc(e.date)} · <span class="status ${statusClass(e.status_key)}">${esc(e.status)}</span></p>
        </div>
        <div class="enquiry-detail-grid">
          <div><span class="muted">Type</span><b>${esc(e.enquiry_type || '—')}</b></div>
          <div><span class="muted">Method</span><b>${esc(e.method || 'No preference')}</b></div>
          <div><span class="muted">Quantity</span><b>${esc(e.quantity || '—')}</b></div>
          <div><span class="muted">Placements</span><b>${esc(places)}</b></div>
        </div>
        <hr class="divider" style="margin:1.2rem 0">
        <h3 style="font-size:1rem;margin-bottom:.5rem">Message</h3>
        <p style="margin:0;white-space:pre-wrap">${esc(e.message || '')}</p>
        <hr class="divider" style="margin:1.2rem 0">
        <h3 style="font-size:1rem;margin-bottom:.7rem">Attached files</h3>
        <div class="enquiry-files">${files}</div>
        <p class="help mt-3"><i class="bi bi-info-circle"></i> Enquiry files are kept with this enquiry and are not added to your Artwork Library.</p>
      </div>`;
    }

    views.enquiries = () => `
      <div class="portal-head">
        <div class="flex justify-between items-center wrap gap-2">
          <div>
            <h1>Branding Enquiries</h1>
            <p class="muted">Your branding enquiry submissions and quotes.</p>
          </div>
          <a class="btn" href="${esc(r().branding || '/branding-enquiry')}"><i class="bi bi-plus-lg"></i> New Enquiry</a>
        </div>
      </div>
      <div class="panel-card">${enquiries().length ? enquiries().map(enquiryRow).join('') : '<p class="muted" style="margin:0">No enquiries yet. <a class="link-arrow" href="' + esc(r().branding || '/branding-enquiry') + '">Send a branding enquiry</a></p>'}</div>`;

    views.enquiryDetail = (id) => {
      const e = enquiries().find(x => String(x.id) === String(id));
      if (!e) return views.enquiries();
      return `<div class="portal-head"><h1>Enquiry Details</h1><p class="muted">Submitted branding enquiry.</p></div>${enquiryDetail(e)}`;
    };

    views.artwork = () => `
      <div class="portal-head">
        <div class="flex justify-between items-center wrap gap-2">
          <div>
            <h1>Artwork Library</h1>
            <p class="muted">Save logos and designs for future orders.</p>
          </div>
          <button type="button" class="btn" id="openArtworkUpload"><i class="bi bi-plus-lg"></i> Add Artwork</button>
        </div>
      </div>
      <div class="panel-card"><div class="artwork-grid">
        ${artwork().length ? artwork().map(a => {
          const isImg = /\.(png|jpe?g|gif|webp|svg)(\?|$)/i.test(a.url || a.name || '');
          return `<div class="artwork">
            <div class="thumb">${isImg ? `<img src="${esc(a.url)}" alt="${esc(a.name)}">` : `<i class="bi bi-file-earmark" style="font-size:2rem;color:var(--brand)"></i>`}</div>
            <div class="meta">
              <b title="${esc(a.name)}">${esc(a.name)}</b>
              <span>${esc(a.type)}</span>
              <button type="button" class="artwork-delete" data-delete-artwork="${a.id}" aria-label="Delete artwork"><i class="bi bi-trash"></i> Delete</button>
            </div>
          </div>`;
        }).join('') : '<p class="muted" style="margin:0;grid-column:1/-1">No artwork yet. Click <b>Add Artwork</b> to upload logos or design files.</p>'}
      </div></div>`;

    function ensureArtworkUploadModal(){
      let modal = document.getElementById('artworkUploadModal');
      if (modal) return modal;

      modal = document.createElement('div');
      modal.id = 'artworkUploadModal';
      modal.className = 'art-upload-modal';
      modal.innerHTML = `
        <div class="art-upload-card" role="dialog" aria-modal="true" aria-labelledby="artUploadTitle">
          <div class="art-upload-head">
            <div>
              <span class="h-eyebrow">Artwork Library</span>
              <h3 class="h-3 mt-1" id="artUploadTitle">Add Artwork</h3>
            </div>
            <button type="button" class="icon-btn" id="artUploadClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
          </div>
          <div class="art-upload-body">
            <div class="art-dropzone" id="artDropzone">
              <input type="file" id="artworkUploadInput" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,image/*" multiple hidden>
              <div class="art-drop-icon"><i class="bi bi-cloud-arrow-up"></i></div>
              <b>Drag & drop files here</b>
              <p class="muted">or click to browse — upload up to 20 files</p>
              <span class="art-drop-hint">JPG, PNG, WEBP, SVG, PDF · max 10MB each</span>
            </div>
            <div class="art-upload-list" id="artUploadList" hidden></div>
          </div>
          <div class="art-upload-foot">
            <button type="button" class="btn btn--ghost" id="artUploadCancel">Cancel</button>
            <button type="button" class="btn" id="artUploadSubmit" disabled><i class="bi bi-upload"></i> Upload <span id="artUploadCount"></span></button>
          </div>
        </div>`;
      document.body.appendChild(modal);

      const close = () => {
        modal.classList.remove('open');
        clearPendingArtwork();
      };
      modal.querySelector('#artUploadClose').onclick = close;
      modal.querySelector('#artUploadCancel').onclick = close;
      modal.addEventListener('click', e => { if (e.target === modal) close(); });
      document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) close();
      });

      return modal;
    }

    let pendingArtworkFiles = [];

    function clearPendingArtwork(){
      pendingArtworkFiles.forEach(item => {
        if (item.preview) URL.revokeObjectURL(item.preview);
      });
      pendingArtworkFiles = [];
      const list = document.getElementById('artUploadList');
      const input = document.getElementById('artworkUploadInput');
      const submit = document.getElementById('artUploadSubmit');
      const count = document.getElementById('artUploadCount');
      if (list) { list.innerHTML = ''; list.hidden = true; }
      if (input) input.value = '';
      if (submit) submit.disabled = true;
      if (count) count.textContent = '';
    }

    function fileIsAllowed(file){
      return /\.(jpe?g|png|gif|webp|svg|pdf)$/i.test(file.name) || /^image\//.test(file.type) || file.type === 'application/pdf';
    }

    function addPendingArtworkFiles(fileList){
      const incoming = Array.from(fileList || []);
      for (const file of incoming) {
        if (!fileIsAllowed(file)) {
          T.toast(`${file.name}: unsupported file type.`);
          continue;
        }
        if (file.size > 10 * 1024 * 1024) {
          T.toast(`${file.name}: must be 10MB or less.`);
          continue;
        }
        if (pendingArtworkFiles.length >= 20) {
          T.toast('You can upload up to 20 files at once.');
          break;
        }
        const exists = pendingArtworkFiles.some(f => f.file.name === file.name && f.file.size === file.size);
        if (exists) continue;

        const isImg = /^image\//.test(file.type) || /\.(jpe?g|png|gif|webp|svg)$/i.test(file.name);
        pendingArtworkFiles.push({
          id: `${file.name}-${file.size}-${Date.now()}-${Math.random()}`,
          file,
          preview: isImg ? URL.createObjectURL(file) : null
        });
      }
      renderPendingArtworkList();
    }

    function renderPendingArtworkList(){
      const list = document.getElementById('artUploadList');
      const submit = document.getElementById('artUploadSubmit');
      const count = document.getElementById('artUploadCount');
      if (!list || !submit) return;

      if (!pendingArtworkFiles.length) {
        list.hidden = true;
        list.innerHTML = '';
        submit.disabled = true;
        if (count) count.textContent = '';
        return;
      }

      list.hidden = false;
      list.innerHTML = pendingArtworkFiles.map(item => {
        const ext = (item.file.name.split('.').pop() || 'FILE').toUpperCase();
        const thumb = item.preview
          ? `<img src="${item.preview}" alt="">`
          : `<i class="bi bi-file-earmark"></i>`;
        return `<div class="art-upload-item" data-pending-id="${esc(item.id)}">
          <div class="art-upload-thumb">${thumb}</div>
          <div class="art-upload-meta">
            <b title="${esc(item.file.name)}">${esc(item.file.name)}</b>
            <span>${ext} · ${(item.file.size / 1024).toFixed(0)} KB</span>
          </div>
          <button type="button" class="icon-btn art-upload-remove" data-remove-pending="${esc(item.id)}" aria-label="Remove file"><i class="bi bi-x"></i></button>
        </div>`;
      }).join('');

      list.querySelectorAll('[data-remove-pending]').forEach(btn => {
        btn.onclick = () => {
          const id = btn.dataset.removePending;
          const idx = pendingArtworkFiles.findIndex(f => f.id === id);
          if (idx >= 0) {
            if (pendingArtworkFiles[idx].preview) URL.revokeObjectURL(pendingArtworkFiles[idx].preview);
            pendingArtworkFiles.splice(idx, 1);
          }
          renderPendingArtworkList();
        };
      });

      submit.disabled = false;
      if (count) count.textContent = `(${pendingArtworkFiles.length})`;
    }

    function openArtworkUploadModal(){
      const modal = ensureArtworkUploadModal();
      clearPendingArtwork();
      modal.classList.add('open');

      const drop = modal.querySelector('#artDropzone');
      const input = modal.querySelector('#artworkUploadInput');
      const submit = modal.querySelector('#artUploadSubmit');

      drop.onclick = () => input.click();
      input.onchange = () => {
        addPendingArtworkFiles(input.files);
        input.value = '';
      };

      drop.ondragover = e => {
        e.preventDefault();
        drop.classList.add('dragover');
      };
      drop.ondragleave = () => drop.classList.remove('dragover');
      drop.ondrop = e => {
        e.preventDefault();
        drop.classList.remove('dragover');
        addPendingArtworkFiles(e.dataTransfer?.files);
      };

      submit.onclick = async () => {
        if (!pendingArtworkFiles.length) return;
        submit.disabled = true;
        submit.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading…';
        try {
          const fd = new FormData();
          pendingArtworkFiles.forEach(item => fd.append('artwork[]', item.file));
          const res = await T.api.send(r().artwork || '/api/portal/artwork', { method: 'POST', formData: fd });
          state = res.portal || state;
          T.toast(res.message || 'Artwork uploaded.', true);
          modal.classList.remove('open');
          clearPendingArtwork();
          render('artwork');
        } catch (err) {
          T.toast(firstError(err));
          submit.disabled = false;
          submit.innerHTML = `<i class="bi bi-upload"></i> Upload <span id="artUploadCount">(${pendingArtworkFiles.length})</span>`;
        }
      };
    }

    views.account = () => {
      const avPreview = user().avatar_url
        ? `<img src="${esc(user().avatar_url)}" alt="">`
        : `<span>${esc(user().initials || 'U')}</span>`;

      return `
      <div class="portal-head"><h1>Account</h1><p class="muted">Manage your profile and password.</p></div>
      <div class="account-tabs">
        <button type="button" class="${accountTab === 'profile' ? 'active' : ''}" data-account-tab="profile">Update Profile</button>
        <button type="button" class="${accountTab === 'password' ? 'active' : ''}" data-account-tab="password">Update Password</button>
      </div>
      <div class="panel-card" ${accountTab === 'profile' ? '' : 'hidden'}>
        <div class="profile-avatar-row">
          <div class="profile-avatar" id="accAvatarPreview">${avPreview}</div>
          <div>
            <label class="btn btn--ghost" for="accAvatar"><i class="bi bi-camera"></i> Upload photo</label>
            <input type="file" id="accAvatar" accept="image/*" hidden>
            <p class="muted" style="font-size:.8rem;margin:.5rem 0 0">JPG, PNG or WEBP · max 2MB</p>
          </div>
        </div>
        <div class="field-group"><label class="lbl" for="accName">Name</label><input class="input" id="accName" value="${esc(user().name || '')}"></div>
        <div class="field-group"><label class="lbl" for="accOrg">Organisation</label><input class="input" id="accOrg" value="${esc(user().company_name || '')}"></div>
        <div class="field-group"><label class="lbl" for="accPhone">Phone</label><input class="input" id="accPhone" value="${esc(user().phone || '')}"></div>
        <div class="field-group"><label class="lbl">Email</label><input class="input" value="${esc(user().email || '')}" disabled style="opacity:.6"></div>
        <button class="btn" type="button" id="accSaveProfile"><i class="bi bi-check-lg"></i> Save Profile</button>
      </div>
      <div class="panel-card" ${accountTab === 'password' ? '' : 'hidden'}>
        ${passwordField('accCurrentPass', 'Current password', 'Enter current password', 'current-password')}
        ${passwordField('accNewPass', 'New password', 'Min. 6 characters', 'new-password')}
        ${passwordField('accConfirmPass', 'Confirm new password', 'Re-enter new password', 'new-password')}
        <button class="btn" type="button" id="accSavePassword"><i class="bi bi-shield-lock"></i> Update Password</button>
      </div>`;
    };

    views.detail = (orderId) => {
      const o = orders().find(x => String(x.id) === String(orderId));
      if (!o) return views.orders();
      return `<div class="portal-head"><h1>Order Details</h1><p class="muted">Bundle order breakdown and artwork.</p></div>${orderDetail(o)}`;
    };

    function bindArtButtons(root){
      root.querySelectorAll('[data-art-url]').forEach(btn => {
        btn.onclick = () => {
          if (typeof T.openArtworkModal === 'function') {
            T.openArtworkModal({
              url: btn.dataset.artUrl,
              label: btn.dataset.artLabel,
              name: btn.dataset.artName
            });
          } else {
            window.open(btn.dataset.artUrl, '_blank');
          }
        };
      });
    }

    async function reorder(orderId){
      try {
        const res = await T.api.send(`/api/portal/orders/${orderId}/reorder`, { method: 'POST' });
        if (res.cart) T.api.syncBadge(res.cart);
        T.toast(res.message || 'Added to cart.', true);
        setTimeout(() => location.href = res.redirect || r().cart || '/cart', 450);
      } catch (err) {
        T.toast(firstError(err));
      }
    }

    function render(view, arg){
      document.querySelectorAll('.portal-nav button').forEach(b => b.classList.toggle('active',
        b.dataset.view === view
        || (view === 'detail' && b.dataset.view === 'orders')
        || (view === 'enquiryDetail' && b.dataset.view === 'enquiries')
      ));
      const main = document.getElementById('portalContent');
      main.innerHTML = typeof views[view] === 'function' ? views[view](arg) : '';
      main.querySelectorAll('[data-go]').forEach(b => b.onclick = () => render(b.dataset.go));
      main.querySelectorAll('[data-view-order]').forEach(b => b.onclick = () => render('detail', b.dataset.viewOrder));
      main.querySelectorAll('[data-view-enquiry]').forEach(b => b.onclick = () => render('enquiryDetail', b.dataset.viewEnquiry));
      main.querySelectorAll('[data-reorder]').forEach(b => b.onclick = () => reorder(b.dataset.reorder));
      if (view !== 'artwork') bindArtButtons(main);
      wirePasswordToggles(main);

      const openUpload = document.getElementById('openArtworkUpload');
      if (openUpload) openUpload.onclick = openArtworkUploadModal;

      main.querySelectorAll('[data-delete-artwork]').forEach(btn => {
        btn.onclick = async () => {
          if (!confirm('Delete this artwork from your library?')) return;
          btn.disabled = true;
          try {
            const res = await T.api.send(`/api/portal/artwork/${btn.dataset.deleteArtwork}`, { method: 'DELETE' });
            state = res.portal || state;
            T.toast(res.message || 'Artwork deleted.', true);
            render('artwork');
          } catch (err) {
            T.toast(firstError(err));
            btn.disabled = false;
          }
        };
      });

      main.querySelectorAll('[data-account-tab]').forEach(btn => {
        btn.onclick = () => {
          accountTab = btn.dataset.accountTab;
          pendingAvatar = null;
          render('account');
        };
      });

      const avatarInput = document.getElementById('accAvatar');
      const avatarPreview = document.getElementById('accAvatarPreview');
      if (avatarInput && avatarPreview) {
        avatarInput.onchange = () => {
          const file = avatarInput.files?.[0];
          if (!file) return;
          if (file.size > 2 * 1024 * 1024) {
            T.toast('Image must be 2MB or less.');
            avatarInput.value = '';
            return;
          }
          pendingAvatar = file;
          const url = URL.createObjectURL(file);
          avatarPreview.innerHTML = `<img src="${url}" alt="">`;
        };
      }

      const saveProfile = document.getElementById('accSaveProfile');
      if (saveProfile) {
        saveProfile.onclick = async () => {
          saveProfile.disabled = true;
          try {
            const fd = new FormData();
            fd.append('name', document.getElementById('accName').value.trim());
            fd.append('company_name', document.getElementById('accOrg').value.trim());
            fd.append('phone', document.getElementById('accPhone').value.trim());
            if (pendingAvatar) fd.append('avatar', pendingAvatar);

            const res = await T.api.send(r().profile || '/api/portal/profile', { method: 'POST', formData: fd });
            state = res.portal || state;
            pendingAvatar = null;
            setSidebarUser();
            T.toast(res.message || 'Profile updated.', true);
            render('account');
          } catch (err) {
            T.toast(firstError(err));
            saveProfile.disabled = false;
          }
        };
      }

      const savePassword = document.getElementById('accSavePassword');
      if (savePassword) {
        savePassword.onclick = async () => {
          const current = document.getElementById('accCurrentPass')?.value || '';
          const next = document.getElementById('accNewPass')?.value || '';
          const confirm = document.getElementById('accConfirmPass')?.value || '';
          if (next.length < 6) {
            T.toast('New password must be at least 6 characters.');
            return;
          }
          if (next !== confirm) {
            T.toast('New password and confirmation do not match.');
            return;
          }
          savePassword.disabled = true;
          try {
            const res = await T.api.send(r().password || '/api/portal/password', {
              method: 'POST',
              body: {
                current_password: current,
                password: next,
                password_confirmation: confirm
              }
            });
            state = res.portal || state;
            T.toast(res.message || 'Password updated.', true);
            render('account');
          } catch (err) {
            T.toast(firstError(err));
            savePassword.disabled = false;
          }
        };
      }

      document.querySelector('.portal-side')?.classList.remove('open');
    }

    document.querySelectorAll('.portal-nav button[data-view]').forEach(b => b.onclick = () => {
      if (b.dataset.view === 'account') accountTab = 'profile';
      render(b.dataset.view);
    });
    const mt = document.getElementById('portalMenuBtn');
    if (mt) mt.onclick = () => document.querySelector('.portal-side')?.classList.toggle('open');
    render('overview');
  };
})();
