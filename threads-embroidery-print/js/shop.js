/* ============================================================
   Shop engine — catalogue, category, product detail, cart
   ============================================================ */
(function(){
  const T=window.THREADS, C=T.CONFIG, U=T.util;
  const qs=new URLSearchParams(location.search);
  const money=U.money;

  function swatchRow(cols){
    const shown=(cols||[]).slice(0,5).map(c=>`<i style="background:${c.hex||'#888'}" title="${c.name}"></i>`).join('');
    const more=(cols||[]).length>5?`<span class="more">+${cols.length-5}</span>`:'';
    return `<div class="sw">${shown}${more}</div>`;
  }
  function productCard(p){
    const price=U.retail(p.base_price), vat=U.vatState();
    return `<a class="prod-card" href="product.php?id=${p.slug}">
      <div class="prod-media"><img src="${p.image}" alt="${p.title}" loading="lazy">
        <div class="flags">${p.is_new?'<span class="tag-new">New In</span>':''}</div>
        <div class="rate"><i class="bi bi-star-fill"></i>4.9</div></div>
      <div class="prod-body"><span class="brand">${p.brand||'Threads'}</span><h3>${p.title}</h3>
        ${swatchRow(p.colours)}
        <div class="prod-foot"><div class="p" data-price="${price}">${U.show(price,vat)} <small>from / unit</small></div>
        <span class="go"><i class="bi bi-arrow-up-right"></i></span></div></div></a>`;
  }
  function bindVat(scope){
    const vt=document.getElementById('vatToggle'); if(!vt) return;
    vt.checked=U.vatState();
    vt.onchange=()=>{ U.setVat(vt.checked);
      (scope||document).querySelectorAll('.p[data-price]').forEach(el=>{ const b=parseFloat(el.dataset.price); el.innerHTML=`${U.show(b,vt.checked)} <small>${el.dataset.suffix||'from / unit'}</small>`; });
    };
  }
  function reObserve(){ const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.1}); document.querySelectorAll('[data-reveal]:not(.in)').forEach(el=>io.observe(el)); }

  /* ---------- CATALOGUE ---------- */
  T.renderCatalogue=function(){
    const wrap=document.getElementById('catalogueGrid'); if(!wrap) return;
    wrap.innerHTML=T.CATEGORIES.map((c,i)=>`
      <a class="cat-card" href="category.php?cat=${c.slug}" data-reveal data-delay="${i%4}">
        <img src="${c.image}" alt="${c.name}" loading="lazy">
        <div class="info"><div class="c">${c.total} Products</div><h3>${c.name}</h3><span class="go">Browse Category <i class="bi bi-arrow-right"></i></span></div>
      </a>`).join('');
    reObserve();
  };

  /* ---------- CATEGORY ---------- */
  T.renderCategory=function(){
    const grid=document.getElementById('categoryGrid'); if(!grid) return;
    const slug=qs.get('cat')||'t-shirts';
    const cat=T.CATEGORIES.find(c=>c.slug===slug)||T.CATEGORIES[0];
    let items=T.PRODUCTS.filter(p=>p.category_slug===cat.slug);
    // header
    document.querySelectorAll('[data-cat-name]').forEach(e=>e.textContent=cat.name);
    document.title=`${cat.name} | Threads Embroidery & Print`;
    const countEl=document.getElementById('catCount');
    // side categories
    const side=document.getElementById('catFilterList');
    if(side) side.innerHTML=T.CATEGORIES.map(c=>`<a class="${c.slug===cat.slug?'active':''}" href="category.php?cat=${c.slug}">${c.name} <span class="c">${c.total}</span></a>`).join('');
    // colour filter (union)
    let activeColour=null;
    const colours=[...new Map(items.flatMap(p=>p.colours||[]).map(c=>[c.name,c])).values()].slice(0,18);
    const cf=document.getElementById('colourFilter');
    if(cf) cf.innerHTML=colours.map(c=>`<button style="background:${c.hex}" title="${c.name}" data-colour="${c.name}"></button>`).join('');
    let sort='featured';
    function apply(){
      let list=items.slice();
      if(activeColour) list=list.filter(p=>(p.colours||[]).some(c=>c.name===activeColour));
      if(sort==='low') list.sort((a,b)=>U.retail(a.base_price)-U.retail(b.base_price));
      else if(sort==='high') list.sort((a,b)=>U.retail(b.base_price)-U.retail(a.base_price));
      else if(sort==='new') list.sort((a,b)=>(b.is_new?1:0)-(a.is_new?1:0));
      grid.innerHTML=list.length?list.map(productCard).join(''):`<div class="empty" style="grid-column:1/-1"><i class="bi bi-search"></i><p>No products match. <a class="link-arrow" style="display:inline" href="contact.php">Ask us to source it →</a></p></div>`;
      if(countEl) countEl.textContent=`${list.length} product${list.length!==1?'s':''} shown · ${cat.total} in range`;
      bindVat(grid);
    }
    if(cf) cf.querySelectorAll('button').forEach(b=>b.onclick=()=>{ const v=b.dataset.colour; activeColour=activeColour===v?null:v; cf.querySelectorAll('button').forEach(x=>x.classList.toggle('active',x===b&&activeColour)); apply(); });
    const sortSel=document.getElementById('sortSelect'); if(sortSel) sortSel.onchange=()=>{sort=sortSel.value;apply();};
    // mobile filter toggle
    const ft=document.getElementById('filterToggle'), fp=document.getElementById('filterPanel');
    if(ft&&fp){ ft.onclick=()=>fp.classList.toggle('open'); }
    apply();
  };

  /* ---------- PRODUCT DETAIL ---------- */
  T.renderProduct=function(){
    const root=document.getElementById('productRoot'); if(!root) return;
    const id=qs.get('id');
    const p=T.PRODUCTS.find(x=>x.slug===id)||T.PRODUCTS[0];
    document.title=`${p.title} | Threads Embroidery & Print`;
    const price=U.retail(p.base_price), vat=U.vatState();
    const gallery=buildGallery(p);
    root.innerHTML=`
      <div class="breadcrumb"><a href="index.php">Home</a><i class="bi bi-chevron-right"></i><a href="catalogue.php">Catalogue</a><i class="bi bi-chevron-right"></i><a href="category.php?cat=${p.category_slug}">${p.category_name}</a><i class="bi bi-chevron-right"></i><span>${p.title}</span></div>
      <div class="pd-grid">
        <div class="pd-gallery">
          <div class="pd-main"><img id="pdImg" src="${gallery[0]}" alt="${p.title}"></div>
          <div class="pd-thumbs-wrap">
            <button type="button" class="pd-thumb-nav prev" aria-label="Previous images"><i class="bi bi-chevron-left"></i></button>
            <div class="pd-thumbs" id="pdThumbs">
              ${gallery.map((src,i)=>`<button type="button" class="pd-thumb ${i===0?'active':''}" data-src="${src}" aria-label="View image ${i+1}"><img src="${src}" alt=""></button>`).join('')}
            </div>
            <button type="button" class="pd-thumb-nav next" aria-label="Next images"><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>
        <div class="pd-info">
          <span class="brand">${p.brand}</span>
          <h1>${p.title}</h1>
          <div class="flex gap-2 items-center wrap"><span class="pill"><i class="bi bi-star-fill" style="color:var(--warn)"></i> 4.9 rating</span>${p.is_new?'<span class="pill pill--brand">New In</span>':''}<span class="pill"><i class="bi bi-truck"></i> 10–14 day turnaround</span></div>
          <div class="pd-price p" data-price="${price}" data-suffix="from / unit">${U.show(price,vat)} <small>from / unit · ${vat?'inc':'ex'} VAT</small></div>
          <p class="muted">Premium ${p.type.toLowerCase()} from ${p.brand}, ready to brand with your logo via embroidery or DTF print. Fully customisable — pick your colour, placements and quantities.</p>
          <label class="vat-toggle pill mt-2" style="cursor:pointer;gap:.6rem;display:inline-flex"><input type="checkbox" id="vatToggle" style="accent-color:var(--brand)"> Show prices inc. VAT</label>
          <div class="pd-block"><h4>Colour — <span id="pdColourName">${(p.colours&&p.colours[0]&&p.colours[0].name)||''}</span></h4>
            <div class="pd-colours" id="pdColours">${(p.colours||[]).map((c,i)=>`<button class="${i===0?'active':''}" style="background:${c.hex}" data-name="${c.name}" title="${c.name}"></button>`).join('')}</div></div>
          <div class="pd-block"><h4>Branding Included</h4>
            <ul class="pd-feature-list">
              <li><i class="bi bi-check-circle-fill"></i> Free logo artwork setup &amp; digitising</li>
              <li><i class="bi bi-check-circle-fill"></i> Embroidery or full-colour DTF print</li>
              <li><i class="bi bi-check-circle-fill"></i> Multiple placements — left chest, back, sleeve</li>
              <li><i class="bi bi-check-circle-fill"></i> Professional set sizing for a consistent finish</li>
            </ul></div>
          <div class="hero-cta"><a class="btn btn--lg" href="configurator.php?id=${p.slug}"><i class="bi bi-magic"></i> Customise &amp; Order</a>
            <a class="btn btn--ghost btn--lg" href="branding-enquiry.php">Bulk Enquiry</a></div>
          <p class="help mt-2"><i class="bi bi-info-circle"></i> Prices shown are per garment before branding add-ons. Placement pricing is applied in the customiser.</p>
        </div>
      </div>`;
    // colour switch
    root.querySelectorAll('#pdColours button').forEach(b=>b.onclick=()=>{
      root.querySelectorAll('#pdColours button').forEach(x=>x.classList.remove('active')); b.classList.add('active');
      document.getElementById('pdColourName').textContent=b.dataset.name;
    });
    // gallery thumbs
    const main=document.getElementById('pdImg');
    const thumbs=root.querySelectorAll('.pd-thumb');
    thumbs.forEach(t=>{
      t.querySelector('img').onerror=function(){ t.remove(); };
      t.onclick=()=>{
        thumbs.forEach(x=>x.classList.remove('active'));
        t.classList.add('active');
        main.src=t.dataset.src;
      };
    });
    const track=document.getElementById('pdThumbs');
    const scrollBy=(dir)=>{ if(track) track.scrollBy({left:dir*120,behavior:'smooth'}); };
    const prev=root.querySelector('.pd-thumb-nav.prev');
    const next=root.querySelector('.pd-thumb-nav.next');
    if(prev) prev.onclick=()=>scrollBy(-1);
    if(next) next.onclick=()=>scrollBy(1);
    bindVat(root);
    // related — exactly 3
    const rel=document.getElementById('relatedGrid');
    if(rel){
      const others=T.PRODUCTS.filter(x=>x.category_slug===p.category_slug&&x.slug!==p.slug).slice(0,3);
      rel.innerHTML=others.map(productCard).join('');
      rel.classList.add('prod-grid--related');
      bindVat(rel);
    }
  };

  function buildGallery(p){
    const out=[];
    const add=(u)=>{ if(u && !out.includes(u)) out.push(u); };
    add(p.image);
    // Derive common alternate angles from Full Collection naming
    add(p.image.replace(/FRONT/g,'BACK').replace(/Front/g,'Back'));
    add(p.image.replace(/FRONT/g,'SIDE').replace(/Front/g,'Side').replace(/BACK/g,'SIDE').replace(/Back/g,'Side'));
    add(p.image.replace(/FRONT/g,'DETAIL').replace(/Front/g,'Detail').replace(/BACK/g,'DETAIL'));
    // Pad with same-category siblings if still short
    if(out.length<4){
      T.PRODUCTS.filter(x=>x.category_slug===p.category_slug&&x.slug!==p.slug).slice(0,4).forEach(x=>add(x.image));
    }
    return out.slice(0,6);
  }

  /* ---------- CART ---------- */
  T.renderCart=function(){
    const root=document.getElementById('cartRoot'); if(!root) return;
    const items=T.cart.all(), vat=U.vatState();
    if(!items.length){ root.innerHTML=`<div class="empty"><i class="bi bi-bag"></i><h3 class="h-3">Your cart is empty</h3><p class="mt-1">Design a garment or grab a bundle to get started.</p><div class="hero-cta mt-3" style="justify-content:center"><a class="btn" href="configurator.php">Start Designing</a><a class="btn btn--ghost" href="bundles.php">View Bundles</a></div></div>`; return; }
    let sub=items.reduce((s,i)=>s+i.total,0);
    const rows=items.map((it,idx)=>`
      <div class="cart-row card card--pad" style="display:flex;gap:1.2rem;align-items:center;margin-bottom:1rem">
        <img src="${it.image}" style="width:84px;height:84px;object-fit:contain;background:#fff;border-radius:12px;padding:6px" alt="">
        <div style="flex:1">
          <b>${it.title}</b>
          <div class="muted" style="font-size:.85rem">${it.summary||''}</div>
          <div class="muted" style="font-size:.85rem">Qty ${it.totalQty} · ${it.method||''}</div>
        </div>
        <div style="text-align:right"><div class="p" style="font-family:var(--font-display);font-weight:800;font-size:1.2rem;color:var(--brand)">${U.show(it.total,vat)}</div>
        <button class="link-arrow" style="color:var(--muted);font-size:.82rem" data-remove="${idx}">Remove</button></div>
      </div>`).join('');
    root.innerHTML=`<div style="display:grid;grid-template-columns:1fr 340px;gap:2rem;align-items:start" class="cart-layout">
      <div>${rows}</div>
      <div class="card card--pad" style="position:sticky;top:calc(var(--header-h) + 16px)">
        <h3 class="h-3" style="margin-bottom:1rem">Order Summary</h3>
        <div class="flex justify-between mt-1"><span class="muted">Subtotal (ex VAT)</span><b>${money(sub)}</b></div>
        <div class="flex justify-between mt-1"><span class="muted">VAT (20%)</span><b>${money(sub*C.vatRate)}</b></div>
        <div class="flex justify-between mt-1"><span class="muted">Delivery</span><b>${sub>=C.freeDeliveryOver?'FREE':money(6.95)}</b></div>
        <hr class="divider" style="margin:1rem 0">
        <div class="flex justify-between"><span style="font-weight:700">Total (inc VAT)</span><b style="font-family:var(--font-display);font-size:1.5rem;color:var(--brand)">${money(U.withVat(sub)+(sub>=C.freeDeliveryOver?0:6.95))}</b></div>
        <button class="btn btn--block btn--lg mt-3" id="checkoutBtn"><i class="bi bi-lock-fill"></i> Secure Checkout</button>
        <p class="help mt-2" style="text-align:center"><i class="bi bi-shield-check"></i> Demo checkout — payment gateway connects on launch.</p>
      </div></div>`;
    root.querySelectorAll('[data-remove]').forEach(b=>b.onclick=()=>{T.cart.remove(+b.dataset.remove);T.renderCart();});
    const co=document.getElementById('checkoutBtn'); if(co) co.onclick=()=>{ T.toast('Demo checkout — online payment & automated invoicing activate on launch.',true); };
  };

  T.shopInit=function(active){
    T.mount({active:active||'catalogue', base:'', onReady:()=>{
      T.renderCatalogue(); T.renderCategory(); T.renderProduct(); T.renderCart();
    }});
  };
})();
