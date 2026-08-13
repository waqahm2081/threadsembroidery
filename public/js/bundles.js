/* ============================================================
   Bundles page + builder (DB-backed) + optional artwork step
   ============================================================ */
(function(){
  const T=window.THREADS, C=T.CONFIG, U=T.util, money=U.money;
  const PALETTE = T.BUNDLE_COLOURS || [{name:'Black',hex:'#1c1c1c'},{name:'Navy',hex:'#1f2a44'},{name:'White',hex:'#f4f4f2'},{name:'Red',hex:'#b4232a'},{name:'Royal Blue',hex:'#1f5fd0'},{name:'Bottle Green',hex:'#12432c'},{name:'Grey',hex:'#6b6f76'},{name:'Purple',hex:'#5b2a86'}];
  const state={bundle:null,colour:PALETTE[0],method:null,addons:{},sizes:{},step:1,artwork:{}};

  function totalGarments(b){ return b.baseGarments.reduce((s,g)=>s+g.qty,0); }
  function placements(){ return T.BUNDLE_PLACEMENTS || C.placements || []; }

  function renderGrid(){
    const grid=document.getElementById('bundlesGrid'); if(!grid) return;
    if(!THREADS.BUNDLES.length){
      grid.innerHTML=`<div class="empty" style="grid-column:1/-1"><i class="bi bi-box"></i><p>No bundles available yet.</p></div>`;
      return;
    }
    grid.innerHTML=THREADS.BUNDLES.map((b,i)=>`
      <div class="bundle-full ${b.featured?'feat':''}" id="bundle-${b.id}" data-reveal data-delay="${i%3}">
        ${b.badge?`<span class="btag pill pill--brand">${b.badge}</span>`:''}
        <div class="ic"><i class="bi ${b.icon}"></i></div>
        <div class="tl">${b.tagline||''}</div>
        <h3>${b.name}</h3>
        <p class="blurb">${b.blurb||''}</p>
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
    if(location.hash){ const id=location.hash.slice(1); if(THREADS.BUNDLES.find(b=>b.id===id)) setTimeout(()=>openBuilder(id),400); }
    const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.1});
    grid.querySelectorAll('[data-reveal]').forEach(el=>io.observe(el));
  }

  function clearArtwork(){
    Object.values(state.artwork||{}).forEach(a=>{ if(a?.preview && a.source==='upload') URL.revokeObjectURL(a.preview); });
    state.artwork={};
    state.artworkMode={};
  }

  function openBuilder(id){
    const b=THREADS.BUNDLES.find(x=>x.id===id); if(!b) return;
    state.bundle=b; state.colour=PALETTE[0]; state.method=b.methods[0]; state.addons={}; state.sizes={}; state.step=1;
    clearArtwork();
    b.baseGarments.forEach((g,gi)=>{ state.sizes[gi]={}; C.sizeScale.forEach(s=>state.sizes[gi][s]=0); state.sizes[gi]['M']=g.qty; });
    drawBuilder();
    document.getElementById('bundleModal').classList.add('open');
  }

  function addonPlacements(){
    return placements().filter(p=>!p.included && p.id!=='left_chest' && (p.method==='both'||p.method===state.method));
  }

  function selectedPlacementList(){
    const list=[{id:'left_chest', label:'Left Chest (included)'}];
    addonPlacements().forEach(p=>{ if(state.addons[p.id]) list.push({id:p.id, label:p.label}); });
    return list;
  }

  function calc(){
    const b=state.bundle;
    const tg=totalGarments(b);
    let addon=0;
    Object.keys(state.addons).forEach(id=>{
      if(state.addons[id]){
        const pl=placements().find(p=>p.id===id);
        if(pl) addon+=pl.price*tg;
      }
    });
    return {tg,addon,sub:b.price+addon};
  }

  function sizesValid(){
    return state.bundle.baseGarments.every((g,gi)=>Object.values(state.sizes[gi]).reduce((a,x)=>a+(+x||0),0)===g.qty);
  }

  function drawBuilder(){
    if(state.step===2){ drawArtworkStep(); return; }
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
    body.querySelectorAll('#bbColours button').forEach(bt=>bt.onclick=()=>{state.colour=PALETTE[+bt.dataset.i];drawBuilder();});
    body.querySelectorAll('#bbMethod button').forEach(bt=>bt.onclick=()=>{state.method=bt.dataset.m;state.addons={};drawBuilder();});
    body.querySelectorAll('#bbGarments input').forEach(inp=>inp.oninput=()=>{ state.sizes[inp.dataset.g][inp.dataset.s]=Math.max(0,+inp.value||0); updateGQ(); drawSide(); });
    body.querySelectorAll('#bbAddons .bb-addon').forEach(a=>a.onclick=()=>{ const id=a.dataset.a; state.addons[id]=!state.addons[id]; a.classList.toggle('on',state.addons[id]); drawSide(); });
    updateGQ(); drawSide();
  }

  function isLoggedIn(){ return !!(window.THREADS_USER && window.THREADS_USER.id); }
  function libraryArtwork(){ return Array.isArray(window.THREADS_USER_ARTWORK) ? window.THREADS_USER_ARTWORK : []; }

  function isImageFile(file){
    return file && /^image\/(png|jpe?g|svg\+xml|webp|gif)$/i.test(file.type);
  }
  function isImageUrl(url, name){
    return /\.(png|jpe?g|gif|webp|svg)(\?|$)/i.test(url || name || '');
  }

  function clearArtworkSlot(slug){
    const prev=state.artwork[slug];
    if(prev?.preview && prev.source==='upload') URL.revokeObjectURL(prev.preview);
    delete state.artwork[slug];
  }

  function setArtworkFile(slug, file){
    clearArtworkSlot(slug);
    if(!file){ return; }
    state.artwork[slug]={
      source:'upload',
      file,
      name:file.name,
      preview: isImageFile(file) ? URL.createObjectURL(file) : null,
      isImage: isImageFile(file)
    };
  }

  function setArtworkLibrary(slug, item){
    clearArtworkSlot(slug);
    if(!item){ return; }
    state.artwork[slug]={
      source:'library',
      libraryId:item.id,
      name:item.name,
      preview: isImageUrl(item.url, item.name) ? item.url : null,
      isImage: isImageUrl(item.url, item.name),
      url:item.url
    };
  }

  function setArtworkMode(slug, mode){
    if(!state.artworkMode) state.artworkMode={};
    state.artworkMode[slug]=mode;
    // Changing mode clears a selection from the other source
    if(state.artwork[slug] && state.artwork[slug].source !== mode){
      clearArtworkSlot(slug);
    }
  }

  function artworkModeFor(slug){
    if(!isLoggedIn()) return 'upload';
    return (state.artworkMode && state.artworkMode[slug]) || 'upload';
  }

  function drawArtworkStep(){
    const body=document.getElementById('bundleBody');
    const places=selectedPlacementList();
    const loggedIn=isLoggedIn();
    const library=libraryArtwork();

    body.innerHTML=`
      <div>
        <div class="bb-block">
          <h4>5 · Upload artwork <span class="muted" style="text-transform:none;letter-spacing:0">(optional)</span></h4>
          <p class="help" style="margin-bottom:1rem">${loggedIn
            ? 'Upload a new file or pick one from your Artwork Library for each position. You can skip this and send artwork later.'
            : 'Upload a logo/file for each selected position. You can skip this and send artwork later.'}</p>
          <div class="bb-artwork-list">
            ${places.map(p=>{
              const art=state.artwork[p.id];
              const mode=artworkModeFor(p.id);
              return `
              <div class="bb-art-row" data-art="${p.id}">
                <div class="top"><b>${p.label}</b><span class="muted" style="font-size:.82rem">${art?.name || 'No file chosen'}</span></div>
                ${loggedIn ? `
                  <div class="bb-art-source">
                    <button type="button" class="bb-art-source-btn ${mode==='upload'?'active':''}" data-art-mode="upload" data-art-slug="${p.id}"><i class="bi bi-cloud-arrow-up"></i> Upload new</button>
                    <button type="button" class="bb-art-source-btn ${mode==='library'?'active':''}" data-art-mode="library" data-art-slug="${p.id}"><i class="bi bi-images"></i> From library</button>
                  </div>` : ''}
                ${mode==='upload' ? `
                  <label class="btn btn--ghost btn--block" style="cursor:pointer;margin-top:.6rem">
                    <i class="bi bi-cloud-arrow-up"></i> ${art?.source==='upload' ? 'Change file' : 'Choose file'}
                    <input type="file" accept=".png,.jpg,.jpeg,.svg,.pdf" hidden data-art-input="${p.id}">
                  </label>` : `
                  <div class="bb-lib-picker" data-lib-for="${p.id}">
                    ${library.length ? library.map(item=>{
                      const selected = art?.source==='library' && String(art.libraryId)===String(item.id);
                      const thumb = isImageUrl(item.url, item.name)
                        ? `<img src="${item.url}" alt="">`
                        : `<i class="bi bi-file-earmark"></i>`;
                      return `<button type="button" class="bb-lib-item ${selected?'selected':''}" data-lib-pick="${item.id}" data-art-slug="${p.id}" title="${item.name}">
                        <div class="thumb">${thumb}</div>
                        <span>${item.name}</span>
                      </button>`;
                    }).join('') : `<p class="muted bb-lib-empty">No saved artwork yet. <a href="/portal">Open Artwork Library</a> to upload some, or switch to Upload new.</p>`}
                  </div>`}
                ${art?.preview ? `<div class="bb-art-preview"><img src="${art.preview}" alt="${art.name}"></div>` : (art ? `<div class="bb-art-preview bb-art-preview--file"><i class="bi bi-file-earmark"></i><span>${art.name}</span></div>` : '')}
                ${art ? `<button type="button" class="bb-art-clear" data-art-clear="${p.id}"><i class="bi bi-x"></i> Remove</button>` : ''}
              </div>`;
            }).join('')}
          </div>
        </div>
      </div>
      <div class="bb-side" id="bbSide"></div>`;

    body.querySelectorAll('[data-art-mode]').forEach(btn=>{
      btn.onclick=()=>{
        setArtworkMode(btn.dataset.artSlug, btn.dataset.artMode);
        drawArtworkStep();
      };
    });
    body.querySelectorAll('[data-art-input]').forEach(inp=>{
      inp.onchange=()=>{
        setArtworkFile(inp.dataset.artInput, inp.files&&inp.files[0] ? inp.files[0] : null);
        setArtworkMode(inp.dataset.artInput, 'upload');
        drawArtworkStep();
      };
    });
    body.querySelectorAll('[data-lib-pick]').forEach(btn=>{
      btn.onclick=()=>{
        const item=libraryArtwork().find(a=>String(a.id)===String(btn.dataset.libPick));
        if(!item) return;
        setArtworkLibrary(btn.dataset.artSlug, item);
        setArtworkMode(btn.dataset.artSlug, 'library');
        drawArtworkStep();
      };
    });
    body.querySelectorAll('[data-art-clear]').forEach(btn=>{
      btn.onclick=()=>{
        clearArtworkSlot(btn.dataset.artClear);
        drawArtworkStep();
      };
    });
    drawSide();
  }

  function updateGQ(){
    state.bundle.baseGarments.forEach((g,gi)=>{ const sum=Object.values(state.sizes[gi]).reduce((a,b)=>a+(+b||0),0); const el=document.querySelector(`[data-gq="${gi}"]`); if(el){el.textContent=`${sum} / ${g.qty}`; el.style.color=sum===g.qty?'var(--ok)':(sum>g.qty?'var(--danger)':'var(--muted)');} });
  }

  function drawSide(){
    const c=calc(), vat=U.vatState(), b=state.bundle;
    const side=document.getElementById('bbSide'); if(!side) return;
    if(state.step===1){
      side.innerHTML=`
        <h4 style="font-family:var(--font-body);font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-2);margin-bottom:.9rem">${b.name}</h4>
        <div class="line"><span>Bundle base (${c.tg} garments)</span><b>${money(b.price)}</b></div>
        <div class="line"><span>Left chest branding</span><b>Included</b></div>
        <div class="line"><span>Extra placements</span><b>${c.addon?money(c.addon):'—'}</b></div>
        <div class="line"><span>Colour</span><b>${state.colour.name}</b></div>
        <div class="line"><span>Method</span><b>${state.method==='embroidery'?'Embroidery':'DTF Print'}</b></div>
        <div class="tot"><div><div style="font-size:.74rem;color:var(--muted)">${vat?'inc':'ex'} VAT</div><label class="vat-switch" style="font-size:.78rem;color:var(--muted);display:flex;gap:.4rem;align-items:center;cursor:pointer"><input type="checkbox" id="bbVat" ${vat?'checked':''} style="accent-color:var(--brand)"> inc VAT</label></div><div class="big">${U.show(c.sub,vat)}</div></div>
        <button class="btn btn--block mt-3" id="bbNext"><i class="bi bi-arrow-right"></i> Continue to Artwork</button>
        <p class="help mt-2" style="text-align:center"><i class="bi bi-info-circle"></i> Artwork upload is optional on the next step.</p>`;
      document.getElementById('bbVat').onchange=e=>{U.setVat(e.target.checked);drawSide();};
      document.getElementById('bbNext').onclick=()=>{
        if(!sizesValid()){ T.toast('Please set sizes to match each garment quantity.'); return; }
        state.step=2; drawBuilder();
      };
      return;
    }

    side.innerHTML=`
      <h4 style="font-family:var(--font-body);font-size:.82rem;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-2);margin-bottom:.9rem">${b.name}</h4>
      <div class="line"><span>Bundle total</span><b>${U.show(c.sub,vat)}</b></div>
      <div class="line"><span>Artwork files</span><b>${Object.keys(state.artwork).length || 'None'}</b></div>
      <button class="btn btn--block mt-3" id="bbAdd"><i class="bi bi-bag-plus"></i> Add Bundle to Cart</button>
      <button class="btn btn--ghost btn--block mt-1" id="bbBack" type="button"><i class="bi bi-arrow-left"></i> Back</button>
      <button class="btn btn--ghost btn--block mt-1" id="bbSkip" type="button">Skip artwork &amp; add</button>`;
    document.getElementById('bbBack').onclick=()=>{ state.step=1; drawBuilder(); };
    document.getElementById('bbSkip').onclick=()=>{ clearArtwork(); addBundle(); };
    document.getElementById('bbAdd').onclick=addBundle;
  }

  async function addBundle(){
    if(!sizesValid()){ T.toast('Please set sizes to match each garment quantity.'); return; }
    const btn=document.getElementById('bbAdd')||document.getElementById('bbSkip');
    if(btn){ btn.disabled=true; btn.innerHTML='<i class="bi bi-hourglass-split"></i> Adding…'; }

    const fd=new FormData();
    fd.append('bundle_slug', state.bundle.id);
    fd.append('method', state.method);
    fd.append('colour_name', state.colour.name);
    fd.append('colour_hex', state.colour.hex||'');
    fd.append('sizes', JSON.stringify(state.sizes));
    Object.keys(state.addons).filter(id=>state.addons[id]).forEach(id=>fd.append('addons[]', id));

    const labels={};
    selectedPlacementList().forEach(p=>{ labels[p.id]=p.label; });
    fd.append('artwork_labels', JSON.stringify(labels));

    const libraryMap={};
    Object.keys(state.artwork).forEach(slug=>{
      const art=state.artwork[slug];
      if(art?.source==='upload' && art.file){
        fd.append('artwork_'+slug, art.file);
      } else if(art?.source==='library' && art.libraryId){
        libraryMap[slug]=art.libraryId;
      }
    });
    if(Object.keys(libraryMap).length){
      fd.append('artwork_library', JSON.stringify(libraryMap));
    }

    try{
      const res=await T.api.send('/api/cart/bundles', { formData: fd });
      T.api.syncBadge(res.cart);
      T.toast('Bundle added to your cart.', true);
      document.getElementById('bundleModal').classList.remove('open');
    }catch(err){
      const msg=err?.data?.errors ? Object.values(err.data.errors).flat()[0] : (err.message||'Could not add bundle.');
      T.toast(msg);
      if(btn){ btn.disabled=false; btn.innerHTML='<i class="bi bi-bag-plus"></i> Add Bundle to Cart'; }
    }
  }

  T.mount({active:'bundles', base:'', onReady:()=>{
    renderGrid();
    const m=document.getElementById('bundleModal');
    document.getElementById('bundleClose').onclick=()=>m.classList.remove('open');
    m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('open');});
  }});
})();
