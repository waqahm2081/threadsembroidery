/* ============================================================
   Threads Embroidery & Print — shared chrome + utilities
   ============================================================ */
(function(){
  const T = window.THREADS;
  const C = T.CONFIG;
  const cur = C.currency;

  /* ---------- graceful fallback for any dead product image ---------- */
  const IMG_PLACEHOLDER="data:image/svg+xml;utf8,"+encodeURIComponent(
    "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 300'><rect width='300' height='300' fill='#f1f1f4'/>"+
    "<path d='M112 66 L150 86 L188 66 L226 90 L210 120 L188 110 L188 236 L112 236 L112 110 L90 120 L74 90 Z' fill='#d9d9df' stroke='#c4c4cc' stroke-width='3' stroke-linejoin='round'/>"+
    "<text x='150' y='268' font-family='Inter,sans-serif' font-size='13' fill='#a7a7b0' text-anchor='middle'>Image coming soon</text></svg>");
  window.addEventListener('error',function(e){
    const t=e.target;
    if(t&&t.tagName==='IMG'&&t.dataset.ph!=='1'&&/fullcollection\.com/.test(t.src||'')){
      t.dataset.ph='1'; t.src=IMG_PLACEHOLDER;
    }
  },true);

  /* ---------- money / pricing helpers ---------- */
  const money = (n)=> cur + (Math.round(n*100)/100).toFixed(2);
  const retail = (base)=> base * C.markup;                 // customer price (cost x markup)
  const withVat = (n)=> n * (1 + C.vatRate);
  T.util = {
    money, retail, withVat,
    /* price shown depending on VAT toggle */
    show(n, vatIncluded){ return money(vatIncluded ? withVat(n) : n); },
    vatState(){ return localStorage.getItem('threads_vat') === 'inc'; },
    setVat(inc){ localStorage.setItem('threads_vat', inc ? 'inc':'exc'); },
    slugName(slug){ const c=(T.CATEGORIES||[]).find(x=>x.slug===slug); return c?c.name:slug; }
  };

  /* ---------- cart (localStorage) ---------- */
  const CART_KEY='threads_cart';
  T.cart = {
    all(){ try{return JSON.parse(localStorage.getItem(CART_KEY))||[]}catch(e){return[]} },
    count(){ return T.cart.all().reduce((s,i)=>s+(i.totalQty||1),0) },
    add(item){ const c=T.cart.all(); c.push(item); localStorage.setItem(CART_KEY,JSON.stringify(c)); T.cart.sync(); },
    remove(i){ const c=T.cart.all(); c.splice(i,1); localStorage.setItem(CART_KEY,JSON.stringify(c)); T.cart.sync(); },
    clear(){ localStorage.removeItem(CART_KEY); T.cart.sync(); },
    sync(){ document.querySelectorAll('[data-cart-badge]').forEach(b=>{ const n=T.cart.count(); b.textContent=n; b.style.display=n?'grid':'none'; }); }
  };

  /* ---------- toast ---------- */
  T.toast = function(msg, ok){
    let w=document.getElementById('toast-wrap');
    if(!w){ w=document.createElement('div'); w.id='toast-wrap'; document.body.appendChild(w); }
    const t=document.createElement('div'); t.className='toast'+(ok?' ok':'');
    t.innerHTML=`<i class="bi ${ok?'bi-check-circle-fill':'bi-bell-fill'}"></i><span>${msg}</span>`;
    w.appendChild(t); setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(10px)';setTimeout(()=>t.remove(),300)},3200);
  };

  /* ---------- preloader ---------- */
  function preloader(){
    if(sessionStorage.getItem('threads_loaded')){ return; }
    const el=document.createElement('div'); el.id='preloader';
    el.innerHTML=`
      <img class="pl-logo" src="${T.base}assets/img/logo.png" alt="Threads Embroidery & Print">
      <div class="pl-tag"><span class="g">Stitch.</span><span class="g">Print.</span><span class="b">Perfect.</span></div>
      <div class="pl-bar"><i></i></div>
      <div class="pl-meta"><b>0%</b><span>Crafting your experience…</span></div>`;
    document.body.appendChild(el);
    const bar=el.querySelector('.pl-bar i'), pct=el.querySelector('.pl-meta b');
    let p=0; const tick=setInterval(()=>{
      p+=Math.random()*16+6; if(p>=100){p=100;clearInterval(tick);
        setTimeout(()=>{el.classList.add('done');sessionStorage.setItem('threads_loaded','1');setTimeout(()=>el.remove(),600)},350);}
      bar.style.width=p+'%'; pct.textContent=Math.floor(p)+'%';
    },160);
  }

  /* ---------- auth CTA (session-aware; chrome is PHP-rendered) ---------- */
  function syncAuthCta(){
    const B=T.base||'';
    let s; try{s=JSON.parse(localStorage.getItem('threads_session'))}catch(e){}
    const slot=document.getElementById('navAuthSlot');
    if(slot){
      slot.innerHTML=s
        ? `<a class="btn nav-cta" href="${B}portal.php"><i class="bi bi-person-circle"></i> My Portal</a>`
        : `<a class="btn nav-cta" href="${B}login.php"><i class="bi bi-person"></i> Login / Signup</a>`;
    }
    const drawerBtn=document.getElementById('drawerAuthBtn');
    if(drawerBtn){
      if(s){ drawerBtn.href=B+'portal.php'; drawerBtn.innerHTML='<i class="bi bi-person-circle"></i> My Portal'; }
      else { drawerBtn.href=B+'login.php'; drawerBtn.innerHTML='<i class="bi bi-person"></i> Login / Signup'; }
    }
  }

  /* ---------- interactivity wiring ---------- */
  function wire(){
    syncAuthCta();
    // scrolled header
    const h=document.getElementById('siteHeader');
    if(h){ const onScroll=()=>h.classList.toggle('scrolled',window.scrollY>20); onScroll(); window.addEventListener('scroll',onScroll,{passive:true}); }
    // drawer
    const drawer=document.querySelector('[data-drawer]');
    document.querySelectorAll('[data-drawer-open]').forEach(b=>b.onclick=()=>drawer&&drawer.classList.add('open'));
    document.querySelectorAll('[data-drawer-close]').forEach(b=>b.onclick=()=>drawer&&drawer.classList.remove('open'));
    // search
    const so=document.querySelector('[data-search]'), si=document.querySelector('[data-search-input]'), sr=document.querySelector('[data-search-results]');
    const openS=()=>{so&&so.classList.add('open');setTimeout(()=>si&&si.focus(),80)};
    const closeS=()=>so&&so.classList.remove('open');
    document.querySelectorAll('[data-search-open]').forEach(b=>b.onclick=openS);
    document.querySelectorAll('[data-search-close]').forEach(b=>b.onclick=closeS);
    so&&so.addEventListener('click',e=>{if(e.target===so)closeS()});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeS();drawer&&drawer.classList.remove('open')}});
    if(si){ si.addEventListener('input',()=>{
      const q=si.value.trim().toLowerCase(); if(!q){sr.hidden=true;sr.innerHTML='';return;}
      const cats=(T.CATEGORIES||[]).filter(c=>c.name.toLowerCase().includes(q)).slice(0,3);
      const prods=(T.PRODUCTS||[]).filter(p=>p.title.toLowerCase().includes(q)||p.brand.toLowerCase().includes(q)).slice(0,6);
      let html=cats.map(c=>`<a href="${T.base}category.php?cat=${c.slug}"><img src="${c.image}" alt=""><div><strong>${c.name}</strong><div class="muted" style="font-size:.8rem">Category · ${c.total} products</div></div></a>`).join('');
      html+=prods.map(p=>`<a href="${T.base}product.php?id=${p.slug}"><img src="${p.image}" alt=""><div><strong>${p.title}</strong><div class="muted" style="font-size:.8rem">${p.brand} · from ${money(retail(p.base_price))}</div></div></a>`).join('');
      sr.innerHTML=html||`<div style="padding:1.2rem;color:var(--muted)">No matches. <a class="link-arrow" style="display:inline" href="${T.base}contact.php">Ask us to source it →</a></div>`;
      sr.hidden=false;
    }); }
    // reveal
    const io=new IntersectionObserver((es)=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.12,rootMargin:'0px 0px -8% 0px'});
    document.querySelectorAll('[data-reveal]').forEach(el=>io.observe(el));
    T.cart.sync();
    wireLightbox();
    initBA();
  }

  /* ---------- before / after slider ---------- */
  function initBA(){
    const box=document.getElementById('baCompare'); if(!box) return;
    const after=document.getElementById('baAfter'), handle=document.getElementById('baHandle');
    let drag=false;
    const move=(x)=>{ const r=box.getBoundingClientRect(); let p=(x-r.left)/r.width; p=Math.max(.04,Math.min(.96,p));
      after.style.width=(p*100)+'%'; handle.style.left=(p*100)+'%';
      after.querySelector('img').style.width=(100/p)+'%'; };
    const start=()=>drag=true, end=()=>drag=false;
    box.addEventListener('mousedown',e=>{start();move(e.clientX)});
    window.addEventListener('mousemove',e=>{if(drag)move(e.clientX)});
    window.addEventListener('mouseup',end);
    box.addEventListener('touchstart',e=>{start();move(e.touches[0].clientX)},{passive:true});
    window.addEventListener('touchmove',e=>{if(drag)move(e.touches[0].clientX)},{passive:true});
    window.addEventListener('touchend',end);
    move(box.getBoundingClientRect().left+box.getBoundingClientRect().width*0.5);
  }

  /* ---------- lightbox ---------- */
  function wireLightbox(){
    const links=[...document.querySelectorAll('[data-lightbox]')];
    if(!links.length) return;
    let lb=document.getElementById('siteLightbox');
    if(!lb){
      lb=document.createElement('div');
      lb.id='siteLightbox';
      lb.className='lightbox';
      lb.setAttribute('role','dialog');
      lb.setAttribute('aria-modal','true');
      lb.setAttribute('aria-label','Image gallery');
      lb.innerHTML=`
        <button class="lightbox-close" type="button" aria-label="Close"><i class="bi bi-x-lg"></i></button>
        <button class="lightbox-nav prev" type="button" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>
        <button class="lightbox-nav next" type="button" aria-label="Next"><i class="bi bi-chevron-right"></i></button>
        <div class="lightbox-inner">
          <img src="" alt="">
          <div class="lightbox-cap"></div>
        </div>
        <div class="lightbox-count"></div>`;
      document.body.appendChild(lb);
    }
    const img=lb.querySelector('img');
    const cap=lb.querySelector('.lightbox-cap');
    const count=lb.querySelector('.lightbox-count');
    let idx=0;
    const items=links.map(a=>({src:a.getAttribute('href'),alt:(a.querySelector('img')&&a.querySelector('img').alt)||a.getAttribute('aria-label')||''}));

    function show(i){
      idx=(i+items.length)%items.length;
      const it=items[idx];
      img.style.opacity='0';
      img.style.transform='scale(.96)';
      const next=new Image();
      next.onload=()=>{
        img.src=it.src; img.alt=it.alt;
        cap.textContent=it.alt||'';
        count.textContent=(idx+1)+' / '+items.length;
        requestAnimationFrame(()=>{ img.style.opacity='1'; img.style.transform='none'; });
      };
      next.src=it.src;
      lb.classList.add('open');
      document.body.style.overflow='hidden';
    }
    function close(){
      lb.classList.remove('open');
      document.body.style.overflow='';
    }

    links.forEach((a,i)=>{
      a.addEventListener('click',e=>{ e.preventDefault(); show(i); });
    });
    lb.querySelector('.lightbox-close').onclick=close;
    lb.querySelector('.lightbox-nav.prev').onclick=()=>show(idx-1);
    lb.querySelector('.lightbox-nav.next').onclick=()=>show(idx+1);
    lb.addEventListener('click',e=>{ if(e.target===lb) close(); });
    document.addEventListener('keydown',e=>{
      if(!lb.classList.contains('open')) return;
      if(e.key==='Escape') close();
      if(e.key==='ArrowLeft') show(idx-1);
      if(e.key==='ArrowRight') show(idx+1);
    });
  }

  /* ---------- mount (chrome is PHP; this wires interactivity) ---------- */
  T.mount = function(opts){
    opts=opts||{};
    T.base = opts.base || '';
    preloader();
    wire();
    if(opts.onReady) opts.onReady();
  };
})();