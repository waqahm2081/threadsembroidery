/* ============================================================
   Product Configurator engine
   - live preview, fixed owner-set sizes (NO drag-resize),
     multiple placements, text personalisation, size/qty grid
   ============================================================ */
(function(){
  const T=window.THREADS, C=T.CONFIG, U=T.util, money=U.money;
  const qs=new URLSearchParams(location.search);

  // The stage shows the REAL product photo; placement positions come from
  // js/garment-templates.js (calibrated per garment type). Only the selected
  // placement's logo is drawn on the photo. TEXT positions are centre-based %.
  const TEXT_POS={
    centre_back:{x:50,y:45,w:44,view:'back'},
    upper_back:{x:50,y:30,w:30,view:'back'},
    front_centre:{x:50,y:52,w:36,view:'front'}
  };

  const state={
    product:null, template:null, colour:null, method:'embroidery',
    placements:{}, logo:null, texts:[],
    sizes:{}, view:'front', backSrc:null, hasBack:false
  };
  C.sizeScale.forEach(s=>state.sizes[s]=0);

  const $=id=>document.getElementById(id);

  function methodPlacements(){
    const tpl=state.template; if(!tpl) return [];
    const frontIds=tpl.zones.front?Object.keys(tpl.zones.front):[];
    const backIds=(state.hasBack&&tpl.zones.back)?Object.keys(tpl.zones.back):[];
    const ids=new Set([...frontIds,...backIds]);
    return C.placements.filter(p=>ids.has(p.id) && (p.method==='both'||p.method===state.method));
  }
  function initPlacementsForMethod(){
    // preserve on-state where still valid
    const avail=methodPlacements().map(p=>p.id);
    Object.keys(state.placements).forEach(id=>{ if(!avail.includes(id)) delete state.placements[id]; });
  }

  /* ---------------- PREVIEW ---------------- */
  function renderStage(){
    const stage=$('cfgStage'); if(!state.product||!state.template) return;
    const tpl=state.template;
    if(state.view==='back'&&!state.hasBack) state.view='front';
    const photo=(state.view==='back'&&state.backSrc)?state.backSrc:state.product.image;
    const zonesForView=tpl.zones[state.view]||{};
    let marks='';
    methodPlacements().forEach(pl=>{
      const z=zonesForView[pl.id]; if(!z) return;
      const on=state.placements[pl.id]&&state.placements[pl.id].on; if(!on) return; // only selected
      const logo=(state.logo&&state.logo.dataUrl);
      const inner=logo?`<img src="${state.logo.dataUrl}" alt="logo">`:`<span class="mph">LOGO</span>`;
      marks+=`<div class="cfg-mark ${logo?'':'empty'}" style="left:${z.x}%;top:${z.y}%;width:${z.w}%"><div class="mk-in">${inner}</div><span class="mk-lbl">${pl.label}</span></div>`;
    });
    // text personalisation overlays (only if that view is available)
    state.texts.forEach(t=>{
      const z=TEXT_POS[t.position]; if(!z||z.view!==state.view||!t.text) return;
      if(z.view==='back'&&!state.hasBack) return;
      marks+=`<div class="cfg-mark" style="left:${z.x}%;top:${z.y}%;width:${z.w}%"><div class="mk-in"><span class="ztext" style="color:${t.colour};font-size:clamp(.7rem,2vw,1.3rem)">${t.text.replace(/</g,'&lt;')}</span></div></div>`;
    });
    const tabs=state.hasBack?`<div class="view-tabs">
        <button class="${state.view==='front'?'active':''}" data-view="front">Front</button>
        <button class="${state.view==='back'?'active':''}" data-view="back">Back</button>
      </div>`:'';
    stage.innerHTML=tabs+
      `<div class="cfg-photo"><img src="${photo}" alt="${state.product.title}"></div>`+
      marks+
      `<div class="badge-live"><span class="dot"></span> Live Preview${state.hasBack?' · '+(state.view==='front'?'Front':'Back'):''}</div>`;
    stage.querySelectorAll('.view-tabs button').forEach(b=>b.onclick=()=>{state.view=b.dataset.view;renderStage();});
  }

  /* ---------------- PRICING ---------------- */
  function calc(){
    const garment=U.retail(state.product?state.product.base_price:0);
    let branding=0;
    Object.keys(state.placements).forEach(id=>{ if(state.placements[id].on){ const pl=C.placements.find(p=>p.id===id); if(pl) branding+=pl.price; } });
    const textUnit=state.texts.filter(t=>t.text).length * C.textPersoPrice;
    const unit=garment+branding+textUnit;
    const qty=Object.values(state.sizes).reduce((a,b)=>a+(+b||0),0);
    const anyBrand=Object.values(state.placements).some(p=>p.on)||state.texts.some(t=>t.text);
    const setup=anyBrand?C.logoSetup:0;
    const sub=unit*qty+setup;
    return {garment,branding,textUnit,unit,qty,setup,sub};
  }
  function renderSummary(){
    const c=calc(), vat=U.vatState();
    const box=$('cfgSummary'); if(!box) return;
    box.innerHTML=`
      <div class="line"><span>Garment <span class="muted">× ${c.qty||0}</span></span><b>${U.show(c.garment,vat)}<span class="muted" style="font-weight:400">/ea</span></b></div>
      <div class="line"><span>Branding / placements</span><b>${c.branding?U.show(c.branding,vat)+'/ea':'—'}</b></div>
      ${c.textUnit?`<div class="line"><span>Personalisation</span><b>${U.show(c.textUnit,vat)}/ea</b></div>`:''}
      <div class="line"><span>Logo digitising setup <span class="muted">(one-off)</span></span><b>${c.setup?U.show(c.setup,vat):'—'}</b></div>
      <div class="total"><div><div style="font-size:.78rem;color:var(--muted)">${c.qty||0} item${c.qty!==1?'s':''} · ${vat?'inc':'ex'} VAT</div><label class="vat-switch"><input type="checkbox" id="cfgVat" ${vat?'checked':''} style="accent-color:var(--brand)"> Show inc. VAT</label></div>
        <div class="big">${U.show(c.sub,vat)}</div></div>
      <div class="flex gap-1 mt-2">
        <button class="btn btn--ghost" id="cfgSpecBtn" style="flex:1"><i class="bi bi-file-earmark-text"></i> Order Spec</button>
        <button class="btn" id="cfgAddBtn" style="flex:1.4"><i class="bi bi-bag-plus"></i> Add to Order</button>
      </div>`;
    $('cfgVat').onchange=e=>{U.setVat(e.target.checked);renderSummary();};
    $('cfgSpecBtn').onclick=showSpec;
    $('cfgAddBtn').onclick=addToOrder;
  }

  /* ---------------- STEP BUILDERS ---------------- */
  function stepSummaries(){
    // update small summaries + done state
    $('sumColour') && ($('sumColour').textContent=state.colour?state.colour.name:'—');
    $('sumMethod') && ($('sumMethod').textContent=state.method==='embroidery'?'Embroidery':'DTF / Print');
    const onP=methodPlacements().filter(p=>state.placements[p.id]&&state.placements[p.id].on);
    $('sumPlace') && ($('sumPlace').textContent=onP.length?onP.map(p=>p.label).join(', '):'None yet');
    $('sumLogo') && ($('sumLogo').textContent=state.logo?state.logo.name:'Not uploaded');
    const q=Object.values(state.sizes).reduce((a,b)=>a+(+b||0),0);
    $('sumQty') && ($('sumQty').textContent=q?q+' garments':'0');
    // done markers
    setDone('stepColour',!!state.colour);
    setDone('stepMethod',true);
    setDone('stepPlace',onP.length>0);
    setDone('stepLogo',!!state.logo);
    setDone('stepSizes',q>0);
  }
  function setDone(id,v){ const el=$(id); if(el) el.classList.toggle('done',v); }

  function buildColours(){
    const wrap=$('cfgColours'); if(!wrap||!state.product) return;
    wrap.innerHTML=(state.product.colours||[]).map((c,i)=>`<button class="${state.colour&&state.colour.name===c.name?'active':''}" style="background:${c.hex}" data-i="${i}" title="${c.name}"></button>`).join('');
    wrap.querySelectorAll('button').forEach(b=>b.onclick=()=>{ state.colour=state.product.colours[+b.dataset.i]; buildColours(); $('cfgColourName').textContent=state.colour.name; renderStage(); stepSummaries(); });
  }
  function buildPlacements(){
    const wrap=$('cfgPlacements'); if(!wrap) return;
    initPlacementsForMethod();
    wrap.innerHTML=methodPlacements().map(pl=>{
      const st=state.placements[pl.id]||{on:false,size:pl.sizes[0]};
      const opts=pl.sizes.map(s=>`<option ${st.size===s?'selected':''}>${s}</option>`).join('');
      return `<div class="place-row ${st.on?'on':''}" data-pl="${pl.id}">
        <div class="place-check ${st.on?'on':''}" data-check><i class="bi bi-check-lg"></i></div>
        <div class="pl-info"><b>${pl.label}</b><div class="pr">+${money(pl.price)} <span class="muted" style="font-weight:400">ex VAT / garment</span></div></div>
        <select data-size aria-label="${pl.label} size">${opts}</select>
      </div>`;
    }).join('');
    wrap.querySelectorAll('.place-row').forEach(row=>{
      const id=row.dataset.pl;
      row.querySelector('[data-check]').onclick=()=>{
        const cur=state.placements[id]||{on:false,size:C.placements.find(p=>p.id===id).sizes[0]};
        cur.on=!cur.on; state.placements[id]=cur; buildPlacements(); renderStage(); renderSummary(); stepSummaries();
      };
      row.querySelector('[data-size]').onchange=e=>{ if(!state.placements[id])state.placements[id]={on:true}; state.placements[id].size=e.target.value; };
    });
  }
  function buildSizes(){
    const wrap=$('cfgSizes'); if(!wrap) return;
    wrap.innerHTML=C.sizeScale.map(s=>`<div class="size-cell"><div class="s">${s}</div><input type="number" min="0" value="${state.sizes[s]||0}" data-size="${s}" inputmode="numeric"></div>`).join('');
    wrap.querySelectorAll('input').forEach(inp=>inp.oninput=()=>{ state.sizes[inp.dataset.size]=Math.max(0,+inp.value||0); updateQtyBar(); renderSummary(); stepSummaries(); });
    updateQtyBar();
  }
  function updateQtyBar(){ const q=Object.values(state.sizes).reduce((a,b)=>a+(+b||0),0); const el=$('cfgQtyTotal'); if(el)el.textContent=q; }

  function buildProductPicker(){
    const sel=$('cfgProductSelect');
    if(!sel||!T.PRODUCTS) return;
    const groups={};
    T.PRODUCTS.forEach(p=>{
      const key=p.category_name||'Other';
      (groups[key]||(groups[key]=[])).push(p);
    });
    sel.innerHTML=Object.keys(groups).sort().map(cat=>{
      const opts=groups[cat].map(p=>{
        const selected=state.product&&state.product.slug===p.slug?' selected':'';
        return `<option value="${p.slug}"${selected}>${p.brand} — ${p.title}</option>`;
      }).join('');
      return `<optgroup label="${cat}">${opts}</optgroup>`;
    }).join('');
    sel.onchange=()=>{
      const p=T.PRODUCTS.find(x=>x.slug===sel.value);
      if(p) selectProduct(p);
    };
  }

  function selectProduct(p){
    const changingType=!state.template || state.template!==T.templateFor(p);
    state.product=p; state.template=T.templateFor(p); state.colour=(p.colours&&p.colours[0])||null;
    if(changingType){ state.placements={}; }
    state.view='front'; state.hasBack=false; state.backSrc=null;
    $('cfgHeadImg').src=p.image; $('cfgHeadBrand').textContent=p.brand; $('cfgHeadTitle').textContent=p.title;
    $('cfgColourName').textContent=state.colour?state.colour.name:'';
    const prodSel=$('cfgProductSelect'); if(prodSel) prodSel.value=p.slug;
    // preload the real back photo; only show Back when it actually exists
    if(state.template.zones.back){
      const backUrl=T.backPhoto(p.image);
      if(backUrl){ const im=new Image();
        im.onload=()=>{ if(state.product!==p) return; state.hasBack=true; state.backSrc=backUrl; buildPlacements(); renderStage(); };
        im.src=backUrl; }
    }
    buildColours(); buildPlacements(); renderStage(); renderSummary(); stepSummaries();
  }

  /* ---------------- TEXT PERSONALISATION ---------------- */
  function buildTexts(){
    const wrap=$('cfgTexts'); if(!wrap) return;
    wrap.innerHTML=state.texts.map((t,i)=>`
      <div class="text-perso-row" data-i="${i}">
        <input type="text" placeholder="Name or number" value="${t.text.replace(/"/g,'&quot;')}" data-t>
        <select data-pos aria-label="Text position">
          <option value="centre_back" ${t.position==='centre_back'?'selected':''}>Back — Name</option>
          <option value="upper_back" ${t.position==='upper_back'?'selected':''}>Back — Number</option>
          <option value="front_centre" ${t.position==='front_centre'?'selected':''}>Front centre</option>
        </select>
        <input type="color" value="${t.colour}" data-col aria-label="Text colour">
        <button class="icon-btn" data-del aria-label="Remove"><i class="bi bi-trash"></i></button>
      </div>`).join('')+`<button class="btn btn--ghost btn--block mt-2" id="cfgAddText"><i class="bi bi-plus-lg"></i> Add name / number</button>`;
    wrap.querySelectorAll('.text-perso-row').forEach(row=>{
      const i=+row.dataset.i;
      row.querySelector('[data-t]').oninput=e=>{state.texts[i].text=e.target.value;renderStage();renderSummary();};
      row.querySelector('[data-pos]').onchange=e=>{state.texts[i].position=e.target.value;if(TEXT_POS[e.target.value].view!==state.view){state.view=TEXT_POS[e.target.value].view;}renderStage();};
      row.querySelector('[data-col]').oninput=e=>{state.texts[i].colour=e.target.value;renderStage();};
      row.querySelector('[data-del]').onclick=()=>{state.texts.splice(i,1);buildTexts();renderStage();renderSummary();};
    });
    $('cfgAddText').onclick=()=>{state.texts.push({text:'',position:'centre_back',colour:'#ffffff'});buildTexts();};
  }

  /* ---------------- LOGO UPLOAD ---------------- */
  function wireUpload(){
    const zone=$('cfgUpload'), input=$('cfgFile');
    if(!zone) return;
    zone.onclick=()=>input.click();
    zone.ondragover=e=>{e.preventDefault();zone.classList.add('drag');};
    zone.ondragleave=()=>zone.classList.remove('drag');
    zone.ondrop=e=>{e.preventDefault();zone.classList.remove('drag');if(e.dataTransfer.files[0])handleFile(e.dataTransfer.files[0]);};
    input.onchange=()=>{if(input.files[0])handleFile(input.files[0]);};
  }
  function handleFile(file){
    const ok=/\.(png|jpe?g|svg|pdf)$/i.test(file.name);
    if(!ok){T.toast('Please upload a PNG, JPG, SVG or PDF file.');return;}
    const isImg=/\.(png|jpe?g|svg)$/i.test(file.name);
    if(isImg){
      const r=new FileReader(); r.onload=e=>{ state.logo={dataUrl:e.target.result,name:file.name}; afterLogo(); }; r.readAsDataURL(file);
    } else {
      // PDF — can't preview inline; use placeholder
      state.logo={dataUrl:'',name:file.name,pdf:true}; afterLogo();
    }
  }
  function afterLogo(){
    const prev=$('cfgLogoPreview');
    prev.innerHTML=`<div class="flex gap-2 items-center" style="padding:.8rem;background:var(--bg-2);border:1px solid var(--line);border-radius:12px;margin-top:1rem">
      ${state.logo.dataUrl?`<img src="${state.logo.dataUrl}" style="width:50px;height:50px;object-fit:contain;background:#fff;border-radius:8px;padding:4px">`:'<i class="bi bi-file-earmark-pdf" style="font-size:2rem;color:var(--brand)"></i>'}
      <div style="flex:1"><b style="font-size:.9rem">${state.logo.name}</b><div class="muted" style="font-size:.78rem"><i class="bi bi-check-circle-fill" style="color:var(--ok)"></i> Uploaded — our team will proof it free</div></div>
      <button class="icon-btn" id="cfgLogoDel"><i class="bi bi-trash"></i></button></div>`;
    $('cfgLogoDel').onclick=()=>{state.logo=null;prev.innerHTML='';renderStage();stepSummaries();renderSummary();};
    // auto-enable left chest if nothing on
    if(!Object.values(state.placements).some(p=>p.on)){ const def=methodPlacements()[0]; state.placements[def.id]={on:true,size:def.sizes[0]}; buildPlacements(); }
    renderStage(); stepSummaries(); renderSummary();
    T.toast('Logo added to your preview.',true);
  }

  /* ---------------- ORDER SPEC / ADD ---------------- */
  function specData(){
    const c=calc();
    const zones=(state.template&&state.template.zones&&state.template.zones[state.view])||{};
    const onP=methodPlacements().filter(p=>state.placements[p.id]&&state.placements[p.id].on)
      .map(p=>{
        const z=zones[p.id]||{};
        return {
          id:p.id,
          label:p.label,
          size:state.placements[p.id].size,
          price:p.price,
          x:z.x, y:z.y, w:z.w,
          view:state.view
        };
      });
    const sizeBreak=C.sizeScale.filter(s=>state.sizes[s]>0).map(s=>`${s}×${state.sizes[s]}`);
    return {c,onP,sizeBreak};
  }
  function validate(){
    if(!state.product){T.toast('Choose a garment first.');return false;}
    const q=Object.values(state.sizes).reduce((a,b)=>a+(+b||0),0);
    if(q<1){T.toast('Add at least one size & quantity.');openStep('stepSizes');return false;}
    if(!Object.values(state.placements).some(p=>p.on)&&!state.texts.some(t=>t.text)){T.toast('Select at least one branding placement.');openStep('stepPlace');return false;}
    return true;
  }
  function showSpec(){
    if(!validate())return;
    const {c,onP,sizeBreak}=specData(); const vat=U.vatState();
    const ref='THR-'+Date.now().toString().slice(-6);
    $('specModal').classList.add('open');
    $('specBody').innerHTML=`
      <div class="flex gap-2 items-center" style="margin-bottom:1rem">
        <img src="${state.product.image}" style="width:70px;height:70px;object-fit:contain;background:#fff;border-radius:10px;padding:5px">
        <div><div class="muted" style="font-size:.75rem">Order reference</div><b style="font-family:var(--font-display);font-size:1.1rem">${ref}</b></div>
      </div>
      <div class="spec-line"><span>Garment</span><span>${state.product.title}</span></div>
      <div class="spec-line"><span>Brand / code</span><span>${state.product.brand} · ${state.product.style_code}</span></div>
      <div class="spec-line"><span>Colour</span><span>${state.colour?state.colour.name:'—'}</span></div>
      <div class="spec-line"><span>Method</span><span>${state.method==='embroidery'?'Embroidery':'DTF / Print'}</span></div>
      ${onP.map(p=>`<div class="spec-line"><span>${p.label} <span class="muted">(${p.size})</span></span><span>+${money(p.price)}/ea</span></div>`).join('')}
      ${state.texts.filter(t=>t.text).map(t=>`<div class="spec-line"><span>Personalisation</span><span>"${t.text}" · ${t.position.replace('_',' ')}</span></div>`).join('')}
      <div class="spec-line"><span>Sizes</span><span>${sizeBreak.join(' · ')||'—'}</span></div>
      <div class="spec-line"><span>Total quantity</span><span>${c.qty}</span></div>
      <div class="spec-line"><span>Logo digitising setup</span><span>${c.setup?money(c.setup):'—'}</span></div>
      <div class="spec-line" style="border-bottom:none;font-size:1.05rem;padding-top:1rem"><span style="font-weight:700">Total (${vat?'inc':'ex'} VAT)</span><span style="font-weight:800;color:var(--brand);font-family:var(--font-display);font-size:1.3rem">${U.show(c.sub,vat)}</span></div>
      <p class="help mt-2"><i class="bi bi-info-circle"></i> This is a production-ready spec. On submission it's sent to our team and confirmed before any work begins.</p>
      <div class="flex gap-1 mt-3"><button class="btn btn--ghost" style="flex:1" onclick="window.print()"><i class="bi bi-printer"></i> Print / PDF</button><button class="btn" style="flex:1" id="specAdd"><i class="bi bi-bag-plus"></i> Add to Order</button></div>`;
    $('specAdd').onclick=()=>{$('specModal').classList.remove('open');addToOrder();};
  }
  function addToOrder(){
    if(!validate())return;
    if(document.body.dataset.cfgAdmin==='1'){
      saveAdminQuote();
      return;
    }
    const {c,onP,sizeBreak}=specData();
    T.cart.add({
      title:state.product.title, image:state.product.image, method:state.method==='embroidery'?'Embroidery':'DTF Print',
      summary:`${state.colour?state.colour.name:''} · ${onP.map(p=>p.label).join(', ')||'—'} · ${sizeBreak.join(' ')}`,
      totalQty:c.qty, total:c.sub
    });
    T.toast('Added to your order.',true);
  }

  async function saveAdminQuote(){
    const {c,onP}=specData();
    const btn=$('cfgAddBtn');
    const prevHtml=btn?btn.innerHTML:'';
    if(btn){ btn.disabled=true; btn.innerHTML='<i class="bi bi-hourglass-split"></i> Saving…'; }

    const sizeBreakdown={};
    C.sizeScale.forEach(s=>{ if((+state.sizes[s]||0)>0) sizeBreakdown[s]=+state.sizes[s]; });

    const payload={
      product_slug:state.product.slug,
      product_title:state.product.title,
      product_brand:state.product.brand,
      product_style_code:state.product.style_code,
      product_image:state.product.image,
      colour_name:state.colour?state.colour.name:null,
      colour_hex:state.colour?state.colour.hex:null,
      method:state.method,
      view:state.view,
      preview_image:(state.view==='back'&&state.backSrc)?state.backSrc:state.product.image,
      placements:onP,
      texts:state.texts.filter(t=>t.text),
      size_breakdown:sizeBreakdown,
      quantity:c.qty,
      garment_unit:c.garment,
      branding_unit:c.branding,
      text_unit:c.textUnit,
      setup_fee:c.setup,
      unit_price:c.unit,
      subtotal:c.sub,
      logo_name:state.logo?state.logo.name:null
    };

    try{
      const fd=new FormData();
      fd.append('payload', JSON.stringify(payload));

      // Capture live stage preview (fallback to product image URL on server if this fails)
      const stage=$('cfgStage');
      if(stage && window.html2canvas){
        try{
          const canvas=await html2canvas(stage,{
            backgroundColor:'#ffffff',
            scale:2,
            useCORS:true,
            allowTaint:true,
            logging:false
          });
          const blob=await new Promise(resolve=>canvas.toBlob(resolve,'image/png'));
          if(blob) fd.append('preview', blob, 'preview.png');
        }catch(err){ /* keep product_image as preview */ }
      }

      if(state.logo && state.logo.dataUrl){
        const logoBlob=await (await fetch(state.logo.dataUrl)).blob();
        fd.append('logo', logoBlob, state.logo.name||'logo.png');
      }

      const csrf=document.querySelector('meta[name="csrf-token"]')?.content||'';
      const res=await fetch(T.ADMIN_QUOTE_STORE||'/admin/product-quotes',{
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},
        body:fd,
        credentials:'same-origin'
      });
      const data=await res.json().catch(()=>({}));
      if(!res.ok) throw new Error(data.message||'Could not save quote.');

      T.toast('Quote '+ (data.reference||'') +' saved.',true);
      if(data.redirect){ location.href=data.redirect; return; }
    }catch(e){
      T.toast(e.message||'Could not save quote.');
    }finally{
      if(btn){ btn.disabled=false; btn.innerHTML=prevHtml||'<i class="bi bi-clipboard-check"></i> Save Quote'; }
    }
  }

  /* ---------------- ACCORDION ---------------- */
  function openStep(id){ document.querySelectorAll('.cfg-step').forEach(s=>s.classList.remove('open')); const el=$(id); if(el)el.classList.add('open'); }
  function wireSteps(){
    document.querySelectorAll('.cfg-step-head').forEach(h=>{ h.onclick=()=>{ const step=h.closest('.cfg-step'); const wasOpen=step.classList.contains('open'); document.querySelectorAll('.cfg-step').forEach(s=>s.classList.remove('open')); if(!wasOpen)step.classList.add('open'); }; });
  }

  /* ---------------- INIT ---------------- */
  T.initConfigurator=function(){
    C.textPersoPrice = C.textPersoPrice!=null?C.textPersoPrice:2.50; // editable; £ per name/number
    // method segmented
    document.querySelectorAll('#cfgMethod button').forEach(b=>b.onclick=()=>{ state.method=b.dataset.m; document.querySelectorAll('#cfgMethod button').forEach(x=>x.classList.toggle('active',x===b)); buildPlacements(); renderStage(); renderSummary(); stepSummaries(); });
    // preselect product from ?id or default
    const initP=T.PRODUCTS.find(p=>p.slug===qs.get('id'))||T.PRODUCTS.find(p=>p.category_slug==='polos')||T.PRODUCTS[0];
    selectProduct(initP);
    buildProductPicker();
    buildPlacements(); buildSizes(); buildTexts(); wireUpload(); wireSteps();
    renderSummary(); stepSummaries();
    // start on colour — garment is already selected
    openStep('stepColour');
    if($('specClose')) $('specClose').onclick=()=>$('specModal').classList.remove('open');
    if($('specModal')) $('specModal').addEventListener('click',e=>{if(e.target===$('specModal'))$('specModal').classList.remove('open');});

    // Admin CTA labels
    if(document.body.dataset.cfgAdmin==='1'){
      const restyle=()=>{
        const add=$('cfgAddBtn'); if(add) add.innerHTML='<i class="bi bi-clipboard-check"></i> Save Quote';
      };
      restyle();
      const _rs=renderSummary;
      renderSummary=function(){ _rs(); restyle(); };
    }
  };

  if(document.body.dataset.cfgAdmin==='1'){
    if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',T.initConfigurator);
    else T.initConfigurator();
  } else {
    T.mount({active:'catalogue', base:'', onReady:T.initConfigurator});
  }
})();
