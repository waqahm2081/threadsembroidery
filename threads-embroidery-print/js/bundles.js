/* ============================================================
   Bundles page + builder
   ============================================================ */
(function(){
  const T=window.THREADS, C=T.CONFIG, U=T.util, money=U.money;
  const PALETTE=[{name:'Black',hex:'#1c1c1c'},{name:'Navy',hex:'#1f2a44'},{name:'White',hex:'#f4f4f2'},{name:'Red',hex:'#b4232a'},{name:'Royal Blue',hex:'#1f5fd0'},{name:'Bottle Green',hex:'#12432c'},{name:'Grey',hex:'#6b6f76'},{name:'Purple',hex:'#5b2a86'}];
  const state={bundle:null,colour:PALETTE[0],method:null,addons:{},sizes:{}};

  function totalGarments(b){ return b.baseGarments.reduce((s,g)=>s+g.qty,0); }

  function renderGrid(){
    const grid=document.getElementById('bundlesGrid'); if(!grid) return;
    grid.innerHTML=THREADS.BUNDLES.map((b,i)=>`
      <div class="bundle-full ${b.featured?'feat':''}" id="bundle-${b.id}" data-reveal data-delay="${i%3}">
        ${b.badge?`<span class="btag pill pill--brand">${b.badge}</span>`:''}
        <div class="ic"><i class="bi ${b.icon}"></i></div>
        <div class="tl">${b.tagline}</div>
        <h3>${b.name}</h3>
        <p class="blurb">${b.blurb}</p>
        <div class="price">
          <div class="price-main"><span class="amt">${money(b.price)}</span><small>from · ex VAT</small></div>
          <span class="save">Save ~${money(b.save)}</span>
        </div>
        <ul>${b.includes.map(x=>`<li><i class="bi bi-check-circle-fill"></i> ${x}</li>`).join('')}</ul>
        <div class="bundle-actions">
          <button type="button" class="btn btn--ghost btn--block" data-size-guide="${b.id}"><i class="bi bi-rulers"></i> Size Guide</button>
          <button class="btn btn--block" data-build="${b.id}"><i class="bi bi-sliders"></i> Build This Bundle</button>
        </div>
      </div>`).join('');
    grid.querySelectorAll('[data-build]').forEach(btn=>btn.onclick=()=>openBuilder(btn.dataset.build));
    // deep link
    if(location.hash){ const id=location.hash.slice(1); if(THREADS.BUNDLES.find(b=>b.id===id)) setTimeout(()=>openBuilder(id),400); }
    const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.1});
    grid.querySelectorAll('[data-reveal]').forEach(el=>io.observe(el));
  }

  function openBuilder(id){
    const b=THREADS.BUNDLES.find(x=>x.id===id); if(!b) return;
    state.bundle=b; state.colour=PALETTE[0]; state.method=b.methods[0]; state.addons={}; state.sizes={};
    b.baseGarments.forEach((g,gi)=>{ state.sizes[gi]={}; C.sizeScale.forEach(s=>state.sizes[gi][s]=0); });
    // sensible default: put all qty into M
    b.baseGarments.forEach((g,gi)=>{ state.sizes[gi]['M']=g.qty; });
    drawBuilder();
    document.getElementById('bundleModal').classList.add('open');
  }

  // add-on placements (exclude the included left chest)
  function addonPlacements(){ return C.placements.filter(p=>p.id!=='left_chest' && (p.method==='both'||p.method===state.method)); }

  function calc(){
    const b=state.bundle;
    const tg=totalGarments(b);
    let addon=0; Object.keys(state.addons).forEach(id=>{ if(state.addons[id]){ const pl=C.placements.find(p=>p.id===id); if(pl) addon+=pl.price*tg; } });
    const sub=b.price+addon;
    return {tg,addon,sub};
  }

  function drawBuilder(){
    const b=state.bundle, body=document.getElementById('bundleBody');
    const methodBtns=b.methods.map(m=>{const lbl=m==='embroidery'?'Embroidery':'DTF / Print';return `<button class="${state.method===m?'active':''}" data-m="${m}">${lbl}</button>`}).join('');
    body.innerHTML=`
      <div>
        <div class="bb-block"><h4>1 · Colour</h4><div class="bb-swatches" id="bbColours">${PALETTE.map((c,i)=>`<button class="${state.colour.name===c.name?'active':''}" style="background:${c.hex}" data-i="${i}" title="${c.name}"></button>`).join('')}</div>
          <p class="help mt-1">Selected: <b id="bbColourName" style="color:var(--text)">${state.colour.name}</b></p></div>
        <div class="bb-block"><h4>2 · Branding Method</h4><div class="seg" id="bbMethod" style="max-width:320px">${methodBtns}</div></div>
        <div class="bb-block"><h4>3 · Sizes per garment <span class="muted" style="text-transform:none;letter-spacing:0">(included in price)</span>
          <button type="button" class="bb-size-link" data-size-guide="${b.id}"><i class="bi bi-rulers"></i> Size Guide</button></h4>
          <div class="bb-garments" id="bbGarments">${b.baseGarments.map((g,gi)=>`
            <div class="bb-gline"><div class="top"><b>${g.label}</b><span class="q" data-gq="${gi}">0 / ${g.qty}</span></div>
              <div class="bb-sizes">${C.sizeScale.map(s=>`<div class="c"><div class="s">${s}</div><input type="number" min="0" value="${state.sizes[gi][s]||0}" data-g="${gi}" data-s="${s}" inputmode="numeric"></div>`).join('')}</div></div>`).join('')}</div></div>
        <div class="bb-block"><h4>4 · Add extra placements <span class="muted" style="text-transform:none;letter-spacing:0">(left chest included)</span></h4>
          <div class="bb-addons" id="bbAddons">${addonPlacements().map(p=>`
            <div class="bb-addon ${state.addons[p.id]?'on':''}" data-a="${p.id}"><span class="chk"><i class="bi bi-check-lg"></i></span><span class="lb">${p.label}</span><span class="pr">+${money(p.price)}/garment</span></div>`).join('')}</div></div>
      </div>
      <div class="bb-side" id="bbSide"></div>`;
    // colour
    body.querySelectorAll('#bbColours button').forEach(bt=>bt.onclick=()=>{state.colour=PALETTE[+bt.dataset.i];drawBuilder();});
    // method
    body.querySelectorAll('#bbMethod button').forEach(bt=>bt.onclick=()=>{state.method=bt.dataset.m;state.addons={};drawBuilder();});
    // sizes
    body.querySelectorAll('#bbGarments input').forEach(inp=>inp.oninput=()=>{ state.sizes[inp.dataset.g][inp.dataset.s]=Math.max(0,+inp.value||0); updateGQ(); drawSide(); });
    // addons
    body.querySelectorAll('#bbAddons .bb-addon').forEach(a=>a.onclick=()=>{ const id=a.dataset.a; state.addons[id]=!state.addons[id]; a.classList.toggle('on',state.addons[id]); drawSide(); });
    updateGQ(); drawSide();
  }
  function updateGQ(){
    state.bundle.baseGarments.forEach((g,gi)=>{ const sum=Object.values(state.sizes[gi]).reduce((a,b)=>a+(+b||0),0); const el=document.querySelector(`[data-gq="${gi}"]`); if(el){el.textContent=`${sum} / ${g.qty}`; el.style.color=sum===g.qty?'var(--ok)':(sum>g.qty?'var(--danger)':'var(--muted)');} });
  }
  function drawSide(){
    const c=calc(), vat=U.vatState(), b=state.bundle;
    document.getElementById('bbSide').innerHTML=`
      <h4 style="font-family:var(--font-body);font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-2);margin-bottom:.9rem">${b.name}</h4>
      <div class="line"><span>Bundle base (${c.tg} garments)</span><b>${money(b.price)}</b></div>
      <div class="line"><span>Left chest branding</span><b>Included</b></div>
      <div class="line"><span>Extra placements</span><b>${c.addon?money(c.addon):'—'}</b></div>
      <div class="line"><span>Colour</span><b>${state.colour.name}</b></div>
      <div class="line"><span>Method</span><b>${state.method==='embroidery'?'Embroidery':'DTF Print'}</b></div>
      <div class="tot"><div><div style="font-size:.74rem;color:var(--muted)">${vat?'inc':'ex'} VAT</div><label class="vat-switch" style="font-size:.78rem;color:var(--muted);display:flex;gap:.4rem;align-items:center;cursor:pointer"><input type="checkbox" id="bbVat" ${vat?'checked':''} style="accent-color:var(--brand)"> inc VAT</label></div><div class="big">${U.show(c.sub,vat)}</div></div>
      <button class="btn btn--block mt-3" id="bbAdd"><i class="bi bi-bag-plus"></i> Add Bundle to Order</button>
      <p class="help mt-2" style="text-align:center"><i class="bi bi-info-circle"></i> Contents &amp; pricing shown are a demo for approval.</p>`;
    document.getElementById('bbVat').onchange=e=>{U.setVat(e.target.checked);drawSide();};
    document.getElementById('bbAdd').onclick=addBundle;
  }
  function addBundle(){
    const c=calc(), b=state.bundle;
    // validate size totals
    let okSizes=b.baseGarments.every((g,gi)=>Object.values(state.sizes[gi]).reduce((a,x)=>a+(+x||0),0)===g.qty);
    if(!okSizes){ T.toast('Please set sizes to match each garment quantity.'); return; }
    const addons=Object.keys(state.addons).filter(id=>state.addons[id]).map(id=>C.placements.find(p=>p.id===id).label);
    T.cart.add({
      title:b.name+' (Bundle)', image:'assets/img/logo.png',
      method:state.method==='embroidery'?'Embroidery':'DTF Print',
      summary:`${state.colour.name} · ${c.tg} garments · Left chest${addons.length?' + '+addons.join(', '):''}`,
      totalQty:c.tg, total:c.sub
    });
    T.toast('Bundle added to your order.',true);
    document.getElementById('bundleModal').classList.remove('open');
  }

  T.mount({active:'bundles', base:'', onReady:()=>{
    renderGrid();
    const m=document.getElementById('bundleModal');
    document.getElementById('bundleClose').onclick=()=>m.classList.remove('open');
    m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');});
  }});
})();
