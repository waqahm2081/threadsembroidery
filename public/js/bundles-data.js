/* ============================================================
   Bundle size-guide modal (data comes from the database via Blade)
   ============================================================ */
window.THREADS = window.THREADS || {};
THREADS.SIZE_GUIDES = THREADS.SIZE_GUIDES || {};
THREADS.BUNDLES = THREADS.BUNDLES || [];

(function(){
  const T = window.THREADS;
  let activeTab = 0;

  function guidesForBundle(bundleId){
    const b = (T.BUNDLES||[]).find(x=>x.id===bundleId);
    if(!b) return {bundle:null, guides:[]};
    const seen = new Set();
    const guides = [];
    b.baseGarments.forEach(g=>{
      if(!g.guide || seen.has(g.guide)) return;
      const sg = T.SIZE_GUIDES[g.guide];
      if(!sg) return;
      seen.add(g.guide);
      guides.push({key:g.guide, label:g.label, ...sg});
    });
    return {bundle:b, guides};
  }

  function ensureModal(){
    let m = document.getElementById('sizeGuideModal');
    if(m) return m;
    m = document.createElement('div');
    m.id = 'sizeGuideModal';
    m.className = 'sg-modal';
    m.setAttribute('role','dialog');
    m.setAttribute('aria-modal','true');
    m.setAttribute('aria-label','Size guide');
    m.innerHTML = `
      <div class="sg-card">
        <div class="sg-head">
          <div>
            <span class="h-eyebrow">Pro RTX Size Guide</span>
            <h3 class="h-3 mt-1" id="sgTitle">Size Guide</h3>
          </div>
          <button class="icon-btn" type="button" id="sgClose" aria-label="Close size guide"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="sg-tabs" id="sgTabs" hidden></div>
        <div class="sg-body" id="sgBody"></div>
      </div>`;
    document.body.appendChild(m);
    m.querySelector('#sgClose').onclick = ()=>m.classList.remove('open');
    m.addEventListener('click', e=>{ if(e.target===m) m.classList.remove('open'); });
    document.addEventListener('keydown', e=>{ if(e.key==='Escape') m.classList.remove('open'); });
    return m;
  }

  function renderTable(g){
    return `
      <div class="sg-meta">
        <b>${g.code}</b> · ${g.name}
        <p class="muted">${g.note||''}</p>
      </div>
      <div class="sg-table-wrap">
        <table class="sg-table">
          <thead><tr><th scope="col">Measurement</th>${g.sizes.map(s=>`<th scope="col">${s}</th>`).join('')}</tr></thead>
          <tbody>
            ${g.rows.map(r=>`<tr><th scope="row">${r.label}</th>${r.values.map(v=>`<td>${v}</td>`).join('')}</tr>`).join('')}
          </tbody>
        </table>
      </div>
      <p class="sg-foot muted"><i class="bi bi-info-circle"></i> Between sizes? We usually recommend sizing up. Unsure? <a class="link-arrow" style="display:inline" href="/contact">Ask us</a> and we'll help.</p>`;
  }

  function draw(bundleId){
    const {bundle, guides} = guidesForBundle(bundleId);
    if(!bundle || !guides.length) return;
    activeTab = Math.min(activeTab, guides.length-1);
    const m = ensureModal();
    m.querySelector('#sgTitle').textContent = bundle.name;
    const tabs = m.querySelector('#sgTabs');
    if(guides.length > 1){
      tabs.hidden = false;
      tabs.innerHTML = guides.map((g,i)=>`
        <button type="button" class="${i===activeTab?'active':''}" data-sg-tab="${i}">${g.label}</button>`).join('');
      tabs.querySelectorAll('[data-sg-tab]').forEach(btn=>{
        btn.onclick = ()=>{ activeTab = +btn.dataset.sgTab; draw(bundleId); };
      });
    } else {
      tabs.hidden = true;
      tabs.innerHTML = '';
    }
    m.querySelector('#sgBody').innerHTML = renderTable(guides[activeTab]);
    m.classList.add('open');
  }

  T.openBundleSizeGuide = function(bundleId){
    activeTab = 0;
    draw(bundleId);
  };

  document.addEventListener('click', e=>{
    const btn = e.target.closest('[data-size-guide]');
    if(!btn) return;
    e.preventDefault();
    T.openBundleSizeGuide(btn.dataset.sizeGuide);
  });
})();
