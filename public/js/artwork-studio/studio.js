/* ============================================================
   Artwork Studio — UI controller
   ============================================================ */
(function(){
  const $=id=>document.getElementById(id);
  const state={src:null, name:'artwork', result:null, busy:false};

  const STEPS=['Reading artwork','Removing background','Reducing to thread colours','Cleaning up noise','Tracing vector outlines','Matching thread chart'];

  function toast(msg, ok){
    const w=$('toasts'); const t=document.createElement('div');
    t.className='toast'+(ok?' ok':''); t.textContent=msg; w.appendChild(t);
    setTimeout(()=>{t.style.opacity='0';setTimeout(()=>t.remove(),300);},3000);
  }

  /* ---------- input ---------- */
  const drop=$('drop'), file=$('file');
  drop.onclick=()=>file.click();
  drop.ondragover=e=>{e.preventDefault();drop.classList.add('over');};
  drop.ondragleave=()=>drop.classList.remove('over');
  drop.ondrop=e=>{e.preventDefault();drop.classList.remove('over');
    if(e.dataTransfer.files[0]) readFile(e.dataTransfer.files[0]);};
  file.onchange=()=>{ if(file.files[0]) readFile(file.files[0]); };

  function readFile(f){
    if(!/\.(png|jpe?g|svg|webp)$/i.test(f.name)){ toast('Please use a PNG, JPG, SVG or WebP file.'); return; }
    const r=new FileReader();
    r.onload=e=>setSource(e.target.result, f.name.replace(/\.[^.]+$/,''));
    r.readAsDataURL(f);
  }

  document.querySelectorAll('[data-sample]').forEach(b=>{
    b.onclick=()=>{
      const url=b.dataset.sample;
      fetch(url).then(r=>r.blob()).then(bl=>{
        const r=new FileReader();
        r.onload=e=>setSource(e.target.result, url.split('/').pop().replace(/\.[^.]+$/,''));
        r.readAsDataURL(bl);
      }).catch(()=>toast('Could not load that sample.'));
    };
  });

  function setSource(dataURL, name){
    /* Clear and enable synchronously. Doing this inside img.onload meant a
       slow-decoding image could land AFTER a run had finished and wipe the
       finished result. */
    state.src=dataURL; state.name=name||'artwork'; state.result=null;
    resetOutput();
    $('runBtn').disabled=false;
    $('inMeta').textContent='';
    const img=new Image();
    img.onload=()=>{
      if(state.src!==dataURL) return;            // superseded by a newer upload
      $('beforeBox').innerHTML='';
      $('beforeBox').appendChild(img);
      $('inMeta').textContent=img.naturalWidth+'×'+img.naturalHeight;
    };
    img.src=dataURL;
    toast('Artwork loaded. Press Prepare Artwork.', true);
  }

  function resetOutput(){
    $('afterBox').innerHTML='<div class="empty"><span class="ic"><i class="bi bi-vector-pen"></i></span>Vector output appears here</div>'+
      '<div class="proc" id="proc"><div class="ring"></div><div class="step" id="procStep">Working…</div>'+
      '<div class="bar"><i id="procBar"></i></div><div class="sub" id="procSub">Processing locally in your browser</div></div>';
    $('outMeta').textContent='';
    $('resBar').innerHTML='';
    $('threadList').innerHTML='<div class="empty-note">Colours and their matching threads will be listed here once artwork is prepared.</div>';
    $('threadCount').textContent='—';
    $('mockWrap').hidden=true;
    ['dlSvg','dlPng','dlSheet'].forEach(id=>$(id).disabled=true);
  }

  /* ---------- controls ---------- */
  const detailLabel=v=>v<=7?'Sharp':(v<=14?'Balanced':'Very smooth');
  $('colours').oninput=e=>$('coloursVal').textContent=e.target.value;
  $('tolerance').oninput=e=>$('tolVal').textContent=e.target.value;
  $('detail').oninput=e=>$('detailVal').textContent=detailLabel(+e.target.value);
  $('resetBtn').onclick=()=>{
    state.src=null; state.result=null; $('runBtn').disabled=true;
    $('beforeBox').innerHTML='<div class="empty"><span class="ic"><i class="bi bi-image"></i></span>Drop a logo to begin</div>';
    $('inMeta').textContent=''; resetOutput(); file.value='';
  };

  /* ---------- run ---------- */
  $('runBtn').onclick=async()=>{
    if(!state.src||state.busy) return;
    state.busy=true; $('runBtn').disabled=true;
    const proc=$('proc'); proc.classList.add('on');
    let stepI=0;
    const onStep=label=>{
      stepI++;
      $('procStep').textContent=label+'…';
      $('procBar').style.width=Math.round((stepI/STEPS.length)*100)+'%';
    };
    try{
      const v=+$('detail').value;
      const res=await ArtEngine.process(state.src,{
        colours:+$('colours').value,
        tolerance:+$('tolerance').value,
        detail:v/10,
        smooth:Math.round(v/3)
      },onStep);
      state.result=res;
      renderResult(res);
      toast('Artwork prepared in '+(res.ms<1000? res.ms+'ms' : (res.ms/1000).toFixed(1)+'s')+'.', true);
    }catch(err){
      toast(err.message||'Could not process that image.');
    }finally{
      proc.classList.remove('on');
      state.busy=false; $('runBtn').disabled=false;
    }
  };

  /* ---------- render ---------- */
  function renderResult(res){
    // vector output
    const box=$('afterBox');
    box.innerHTML=res.svg;
    box.insertAdjacentHTML('beforeend','<div class="proc" id="proc"><div class="ring"></div><div class="step" id="procStep">Working…</div><div class="bar"><i id="procBar"></i></div><div class="sub" id="procSub">Processing locally in your browser</div></div>');
    $('outMeta').textContent=res.layers.length+(res.layers.length===1?' colour':' colours');

    // result chips
    const pathCount=res.layers.reduce((n,l)=>n+(l.path.match(/M/g)||[]).length,0);
    $('resBar').innerHTML=[
      ['bi-stopwatch', (res.ms<1000? res.ms+'ms' : (res.ms/1000).toFixed(1)+'s')+' processing'],
      ['bi-eraser', res.backgroundRemoved?'Background removed':'No flat background found'],
      ['bi-palette', res.layers.length+' thread colours'],
      ['bi-bezier2', pathCount+' vector shapes'],
      ['bi-aspect-ratio','Scales to any size without loss']
    ].map(([i,t])=>`<span class="pill"><i class="bi ${i}"></i> ${t}</span>`).join('');

    // threads
    $('threadCount').textContent=res.threads.length+' colours';
    $('threadList').innerHTML=res.threads.map(t=>`
      <div class="thread-row">
        <span class="sw" style="background:${t.thread.hex}"></span>
        <span class="info"><span class="nm">${t.thread.name}</span><span class="cd">${t.thread.code} &middot; ${t.thread.hex.toUpperCase()}</span></span>
        <span class="pc">${Math.round(t.share*100)}%</span>
      </div>`).join('');

    ['dlSvg','dlPng','dlSheet'].forEach(id=>$(id).disabled=false);
    renderMockups(res);
  }

  /* ---------- garment mockups ----------
     Real product photography (same Pencarrie shots as the main site) with
     the traced artwork placed at the calibrated left-chest / centre points.
     Drawn vector garments were tried and looked like clip-art. */
  const GARMENTS=[
    {label:'Polo · left chest',   img:'/assets/artwork-studio/garments/polo.jpg',   x:38, y:31, w:11},
    {label:'Hoodie · left chest', img:'/assets/artwork-studio/garments/hoodie.jpg', x:38, y:35, w:11},
    {label:'Tote · centre',       img:'/assets/artwork-studio/garments/tote.jpg',   x:50, y:57, w:30}
  ];

  function renderMockups(res){
    const svgURL='data:image/svg+xml;utf8,'+encodeURIComponent(res.svg);
    $('mockGrid').innerHTML=GARMENTS.map(g=>`
      <div class="mock-item">
        <img class="gm" src="${g.img}" alt="">
        <span class="lg" style="left:${g.x}%;top:${g.y}%;width:${g.w}%">
          <img src="${svgURL}" alt="artwork on garment">
        </span>
        <span class="tagx">${g.label}</span>
      </div>`).join('');
    $('mockWrap').hidden=false;
  }

  /* ---------- exports ---------- */
  function download(href,name){ const a=document.createElement('a'); a.href=href; a.download=name; a.click(); }

  $('dlSvg').onclick=()=>{
    if(!state.result) return;
    download('data:image/svg+xml;charset=utf-8,'+encodeURIComponent(state.result.svg), state.name+'-vector.svg');
    toast('SVG downloaded.', true);
  };

  $('dlPng').onclick=()=>{
    if(!state.result) return;
    const r=state.result, scale=Math.max(1, Math.round(1400/Math.max(r.width,r.height)));
    const c=document.createElement('canvas'); c.width=r.width*scale; c.height=r.height*scale;
    const ctx=c.getContext('2d');
    const img=new Image();
    img.onload=()=>{ ctx.drawImage(img,0,0,c.width,c.height); download(c.toDataURL('image/png'), state.name+'-transparent.png'); toast('PNG downloaded.', true); };
    img.src='data:image/svg+xml;charset=utf-8,'+encodeURIComponent(r.svg);
  };

  $('dlSheet').onclick=async()=>{
    if(!state.result) return;
    const r=state.result;
    const lines=['THREADS EMBROIDERY & PRINT / THREAD SHEET','Artwork: '+state.name,'Colours: '+r.threads.length,''];
    r.threads.forEach((t,i)=>lines.push(`${i+1}. ${t.thread.name}  (${t.thread.code})  ${t.thread.hex.toUpperCase()}  ${Math.round(t.share*100)}% coverage`));
    lines.push('','Thread codes use the standard demo chart; map to your own chart on setup.');
    const text=lines.join('\n');
    try{ await navigator.clipboard.writeText(text); toast('Thread sheet copied to clipboard.', true); }
    catch(e){ download('data:text/plain;charset=utf-8,'+encodeURIComponent(text), state.name+'-thread-sheet.txt'); toast('Thread sheet downloaded.', true); }
  };

  $('detailVal').textContent=detailLabel(+$('detail').value);
})();

/* ============================================================
   Two doors: refine existing artwork (free) / design from scratch (paid)
   ============================================================ */
(function(){
  const $=id=>document.getElementById(id);
  const CFG=window.STUDIO_CONFIG;
  const money=n=>'£'+Number(n).toFixed(2).replace(/\.00$/,'');

  function toast(msg, ok){
    const w=$('toasts'); if(!w) return;
    const t=document.createElement('div');
    t.className='toast'+(ok?' ok':''); t.textContent=msg; w.appendChild(t);
    setTimeout(()=>{t.style.opacity='0';setTimeout(()=>t.remove(),300);},3200);
  }

  /* ---------------- mode switching ---------------- */
  const cards=[...document.querySelectorAll('.mode-card')];
  function setMode(mode){
    cards.forEach(c=>c.classList.toggle('active', c.dataset.mode===mode));
    $('refineView').hidden = mode!=='refine';
    $('designView').hidden = mode!=='design';
    window.scrollTo({top:0,behavior:'smooth'});
  }
  cards.forEach(c=>c.onclick=()=>setMode(c.dataset.mode));

  const cheapest=Math.min(...CFG.packages.map(p=>p.price));
  $('modePriceTag').textContent='From '+money(cheapest);

  /* ---------------- contact capture before download ---------------- */
  const LEAD_KEY='threads_studio_lead';
  const getLead=()=>{ try{ return JSON.parse(sessionStorage.getItem(LEAD_KEY)); }catch(e){ return null; } };

  let pendingAction=null;
  const modal=$('leadModal');
  const openLead=action=>{ pendingAction=action; modal.classList.add('open'); setTimeout(()=>$('lName').focus(),120); };
  const closeLead=()=>{ modal.classList.remove('open'); pendingAction=null; };
  $('leadClose').onclick=closeLead;
  modal.addEventListener('click',e=>{ if(e.target===modal) closeLead(); });
  document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&modal.classList.contains('open')) closeLead(); });

  const emailOk=v=>/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v);
  function mark(el, ok){ el.closest('.fld').classList.toggle('bad', !ok); return ok; }

  $('leadForm').addEventListener('submit',e=>{
    e.preventDefault();
    const n=$('lName'), em=$('lEmail');
    /* mark both before combining, otherwise short-circuiting means only the
       first empty field ever gets highlighted */
    const okName = mark(n, n.value.trim().length>1);
    const okMail = mark(em, emailOk(em.value.trim()));
    if(!okName || !okMail) return;
    sessionStorage.setItem(LEAD_KEY, JSON.stringify({
      name:n.value.trim(), email:em.value.trim(),
      organisation:$('lCompany').value.trim(), phone:$('lPhone').value.trim(),
      at:new Date().toISOString()
    }));
    const run=pendingAction;
    closeLead();
    toast('Thanks, your files are on the way.', true);
    if(run) run();
  });

  /* Wrap the three export buttons so the details are asked for once. */
  ['dlSvg','dlPng','dlSheet'].forEach(id=>{
    const btn=$(id); if(!btn) return;
    const original=btn.onclick;
    btn.onclick=()=>{
      if(!CFG.captureFields || getLead()){ original && original(); return; }
      openLead(()=>original && original());
    };
  });

  /* ---------------- design brief ---------------- */
  const pkgGrid=$('pkgGrid');
  pkgGrid.innerHTML=CFG.packages.map(p=>`
    <label class="pkg">
      ${p.popular?'<span class="best">Most chosen</span>':''}
      <input type="radio" name="package" value="${p.id}">
      <div class="body">
        <div class="top"><span class="nm">${p.name}</span><span class="pr">${money(p.price)}</span></div>
        <div class="bl">${p.blurb}</div>
        <ul class="inc">${p.includes.map(x=>`<li><i class="bi bi-check-lg"></i><span>${x}</span></li>`).join('')}</ul>
      </div>
    </label>`).join('');

  const form=$('briefForm');
  const chosen=()=>{
    const r=form.querySelector('input[name="package"]:checked');
    return r ? CFG.packages.find(p=>p.id===r.value) : null;
  };
  const checked=name=>[...form.querySelectorAll(`input[name="${name}"]:checked`)].map(i=>i.value);

  function drawSummary(){
    const p=chosen();
    const org=$('bName').value.trim();
    const place=checked('placement'), style=checked('style');
    const rows=[];
    if(org) rows.push(['Name', org]);
    if(p) rows.push(['Package', p.name]);
    if(place.length) rows.push(['Placement', place.join(', ')]);
    if(style.length) rows.push(['Style', style.join(', ')]);
    if($('bColours').value.trim()) rows.push(['Colours', $('bColours').value.trim()]);
    if($('bDeadline').value) rows.push(['Needed by', $('bDeadline').value]);

    $('briefSummary').innerHTML = rows.length
      ? rows.map(([k,v])=>`<div class="bs-line"><span>${k}</span><span>${v}</span></div>`).join('')
        + `<div class="bs-total"><div><div style="font-size:.76rem;color:var(--muted)">${p?'ex VAT':'choose a package'}</div>`
        + `<div style="font-size:.76rem;color:var(--muted)">Ready in ${CFG.turnaround}</div></div>`
        + `<div class="big">${p?money(p.price):'—'}</div></div>`
        + `<button class="btn" type="button" id="briefSubmit" style="margin-top:1rem"><i class="bi bi-send"></i> Submit brief and pay</button>`
        + `<p class="hint" style="margin-top:.7rem;text-align:center">Nothing is charged until you confirm on the next step.</p>`
      : '<p class="bs-empty">Fill in the brief and it will appear here as you go, along with the price.</p>';

    const b=$('briefSubmit'); if(b) b.onclick=submitBrief;
  }
  form.addEventListener('input',drawSummary);
  form.addEventListener('change',drawSummary);
  drawSummary();

  /* reference file attachments */
  const bd=$('briefDrop'), bf=$('briefFiles');
  bd.onclick=()=>bf.click();
  bd.ondragover=e=>{e.preventDefault();bd.classList.add('over');};
  bd.ondragleave=()=>bd.classList.remove('over');
  bd.ondrop=e=>{e.preventDefault();bd.classList.remove('over');bf.files=e.dataTransfer.files;showFiles();};
  bf.onchange=showFiles;
  function showFiles(){
    $('briefFileList').innerHTML=[...bf.files].map(f=>
      `<span class="pill" style="margin:.4rem .4rem 0 0"><i class="bi bi-paperclip"></i> ${f.name}</span>`).join('');
  }

  function submitBrief(){
    const checks=[
      [$('bName'), v=>v.trim().length>1],
      [$('bWhat'), v=>v.trim().length>1],
      [$('bContact'), v=>v.trim().length>1],
      [$('bEmail'), v=>emailOk(v.trim())]
    ];
    let firstBad=null;
    checks.forEach(([el,test])=>{ if(!mark(el,test(el.value)) && !firstBad) firstBad=el; });

    const p=chosen();
    $('pkgErr').style.display = p ? 'none' : 'block';
    if(!p && !firstBad) firstBad=pkgGrid;

    if(firstBad || !p){
      (firstBad||pkgGrid).scrollIntoView({block:'center',behavior:'smooth'});
      if(firstBad.focus) firstBad.focus();
      toast('Just a couple of details missing.');
      return;
    }

    const ref='THR-A'+String(Date.now()).slice(-5);
    const data={
      reference:ref, package:p.name, price:p.price,
      organisation:$('bName').value.trim(), what_you_do:$('bWhat').value.trim(),
      placement:checked('placement').join(', '), style:checked('style').join(', '),
      colours:$('bColours').value.trim(), deadline:$('bDeadline').value,
      notes:$('bNotes').value.trim(), attachments:[...bf.files].map(f=>f.name).join(', '),
      name:$('bContact').value.trim(), email:$('bEmail').value.trim(), phone:$('bPhone').value.trim()
    };
    const body=encodeURIComponent(Object.entries(data)
      .filter(([,v])=>v).map(([k,v])=>k.replace(/_/g,' ')+': '+v).join('\n'));
    const subject=encodeURIComponent('Artwork brief '+ref+', '+data.organisation);

    document.querySelector('#designView').innerHTML=`
      <div class="panel" style="grid-column:1/-1">
        <div class="panel-bd">
          <div class="done-box">
            <div class="tick"><i class="bi bi-check-lg"></i></div>
            <h3>Thanks ${data.name.split(' ')[0]}, your brief is in.</h3>
            <p>We have everything we need to get started. Your first concepts will be with you within ${CFG.turnaround} of payment clearing.</p>
            <div class="ref-chip">${ref}</div>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:1.6rem">
              <a class="btn" style="width:auto" href="#"><i class="bi bi-lock-fill"></i> Pay ${money(p.price)} and start</a>
              <a class="btn ghost" style="width:auto" href="mailto:${CFG.ownerEmail}?subject=${subject}&body=${body}"><i class="bi bi-envelope"></i> Email the brief instead</a>
            </div>
            <p class="hint" style="margin-top:1.2rem">Payment is a demo button here. It connects to the checkout once this is live on the site.</p>
          </div>
        </div>
      </div>`;
    window.scrollTo({top:0,behavior:'smooth'});
    toast('Brief submitted.', true);
  }
})();
