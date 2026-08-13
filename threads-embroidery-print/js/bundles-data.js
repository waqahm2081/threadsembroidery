/* ============================================================
   Bundle Deals — six fixed-price kits (demo contents & pricing
   for client approval). Edit values here — nothing hardcoded
   in the engine.

   All garments are Pro RTX. Size guides use official Pro RTX
   flat measurements (cm) unless noted as "to fit".
   ============================================================ */
window.THREADS = window.THREADS || {};

/* ---------- Pro RTX size guides (keyed by style) ---------- */
THREADS.SIZE_GUIDES = {
  rx101: {
    code:'RX101', name:'Pro Polo',
    note:'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm (sleeve ±1cm).',
    sizes:['XS','S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (½)', values:[45,49,53,57,61,65,69,73]},
      {label:'Body length', values:[67,70,72.5,75,77.5,80,82.5,85]},
      {label:'Sleeve', values:[21.5,23,23,23.5,24,24,24.5,24.5]}
    ]
  },
  rx151: {
    code:'RX151', name:'Pro T-Shirt',
    note:'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm (sleeve ±1cm).',
    sizes:['XS','S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (½)', values:[45,49,53,57,61,65,69,73]},
      {label:'Body length', values:[67,70,72.5,75,77.5,80,82.5,85]},
      {label:'Sleeve', values:[23,24.5,24.5,25,25.5,25.5,26,26]}
    ]
  },
  rx301: {
    code:'RX301', name:'Pro Sweatshirt',
    note:'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm.',
    sizes:['XS','S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (½)', values:[48,52,56,60,64,68,72,76]},
      {label:'Body length', values:[65,68,71,74,77,80,83,86]},
      {label:'Sleeve', values:[61.5,62.5,65,64,67,68.5,70,71]}
    ]
  },
  rx350: {
    code:'RX350', name:'Pro Hoodie',
    note:'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm.',
    sizes:['XS','S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (½)', values:[50,54,58,62,66,70,74,79]},
      {label:'Body length', values:[67,70,73,76,79,82,85,88]},
      {label:'Sleeve', values:[60.5,62,63.5,65,66.5,68,69.5,71.5]}
    ]
  },
  rx500: {
    code:'RX500', name:'Pro Softshell Jacket',
    note:'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm.',
    sizes:['S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (½)', values:[50,54,58,62,66,68,72]},
      {label:'Body length', values:[71,73,75,77,79,81,83]},
      {label:'Sleeve', values:[64,66,68,70,72,74,76]}
    ]
  },
  rx700: {
    code:'RX700', name:'Pro Hi-Vis Vest',
    note:'Chest to-fit sizes in inches — the chest circumference each size is designed for.',
    sizes:['S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (to fit)', values:['36"','40"','44"','48"','52"','56"','60"']}
    ]
  },
  rx710: {
    code:'RX710', name:'Pro Hi-Vis Polo',
    note:'Flat garment measurements in cm. Chest is half-chest (pit to pit). Tol. ±2cm (sleeve ±1cm).',
    sizes:['S','M','L','XL','2XL','3XL','4XL'],
    rows:[
      {label:'Chest (½)', values:[50,54,58,62,66,70,74]},
      {label:'Body length', values:[74,76.5,79,81.5,84,86.5,89]},
      {label:'Sleeve', values:[22.5,23,23.5,24,24.5,25,25.5]}
    ]
  }
};

THREADS.BUNDLES = [
  {
    id:'solo', name:'Solo Bundle', icon:'bi-person-badge',
    tagline:'Sole traders & one-person brands', badge:'', popular:false,
    price:89.00, save:24,
    blurb:'A tidy starter kit to get one person fully branded and looking professional from day one.',
    includes:['3 × Premium Polo Shirts','1 × Softshell Jacket','Left chest embroidery on all items','Free logo artwork setup'],
    garmentCats:['polos','jackets'], methods:['embroidery','print'],
    baseGarments:[
      {cat:'polos',qty:3,label:'Polo Shirt',guide:'rx101'},
      {cat:'jackets',qty:1,label:'Softshell Jacket',guide:'rx500'}
    ]
  },
  {
    id:'topup', name:'Top Up Bundle', icon:'bi-plus-circle',
    tagline:'Add to an existing kit', badge:'', popular:false,
    price:119.00, save:31,
    blurb:'Already set up? Top up your team with a fresh run of branded everyday essentials.',
    includes:['5 × T-Shirts','5 × Polo Shirts','Left chest branding on all items','Mix colours & sizes freely'],
    garmentCats:['t-shirts','polos'], methods:['embroidery','print'],
    baseGarments:[
      {cat:'t-shirts',qty:5,label:'T-Shirt',guide:'rx151'},
      {cat:'polos',qty:5,label:'Polo Shirt',guide:'rx101'}
    ]
  },
  {
    id:'startup', name:'Start Up Bundle', icon:'bi-rocket-takeoff', featured:true,
    tagline:'New business, fully kitted', badge:'Most Popular', popular:true,
    price:199.00, save:58,
    blurb:'Everything a new business needs to look established — a complete branded uniform in one go.',
    includes:['5 × Polo Shirts','3 × Sweatshirts','1 × Softshell Jacket','Left chest embroidery + back option','Free logo setup & digitising'],
    garmentCats:['polos','sweatshirts','jackets'], methods:['embroidery','print'],
    baseGarments:[
      {cat:'polos',qty:5,label:'Polo Shirt',guide:'rx101'},
      {cat:'sweatshirts',qty:3,label:'Sweatshirt',guide:'rx301'},
      {cat:'jackets',qty:1,label:'Softshell Jacket',guide:'rx500'}
    ]
  },
  {
    id:'safety', name:'Safety Bundle', icon:'bi-cone-striped',
    tagline:'Hi-vis & site-ready', badge:'', popular:false,
    price:79.00, save:19,
    blurb:'Compliant, branded hi-vis for site teams — clear identification that meets the job.',
    includes:['5 × Hi-Vis Vests','3 × Hi-Vis Polo Shirts','Print branding chest & back','Free logo artwork setup'],
    garmentCats:['hi-vis'], methods:['print','embroidery'],
    baseGarments:[
      {cat:'hi-vis',qty:5,label:'Hi-Vis Vest',guide:'rx700'},
      {cat:'hi-vis',qty:3,label:'Hi-Vis Polo',guide:'rx710'}
    ]
  },
  {
    id:'merch', name:'Starting Up Your Merch', icon:'bi-bag-heart',
    tagline:'Launch your own merch line', badge:'', popular:false,
    price:249.00, save:72,
    blurb:'Kick off a clothing brand or club merch drop with a print-ready starter run.',
    includes:['10 × T-Shirts','10 × Hoodies','Full-colour DTF print, front & back','Free logo artwork setup'],
    garmentCats:['t-shirts','hoods'], methods:['print'],
    baseGarments:[
      {cat:'t-shirts',qty:10,label:'T-Shirt',guide:'rx151'},
      {cat:'hoods',qty:10,label:'Hoodie',guide:'rx350'}
    ]
  },
  {
    id:'team', name:'Team Bundle', icon:'bi-people',
    tagline:'Squads, clubs & small teams', badge:'Great for Clubs', popular:false,
    price:169.00, save:45,
    blurb:'Kit out a small team or club with matching branded wear — mix sizes freely across the order.',
    includes:['8 × T-Shirts','4 × Hoodies','Left chest branding on all items','Free logo artwork setup','Mix sizes & colours freely'],
    garmentCats:['t-shirts','hoods'], methods:['embroidery','print'],
    baseGarments:[
      {cat:'t-shirts',qty:8,label:'T-Shirt',guide:'rx151'},
      {cat:'hoods',qty:4,label:'Hoodie',guide:'rx350'}
    ]
  }
];

/* ---------- Size guide modal (shared: homepage + bundles page) ---------- */
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
        <p class="muted">${g.note}</p>
      </div>
      <div class="sg-table-wrap">
        <table class="sg-table">
          <thead><tr><th scope="col">Measurement</th>${g.sizes.map(s=>`<th scope="col">${s}</th>`).join('')}</tr></thead>
          <tbody>
            ${g.rows.map(r=>`<tr><th scope="row">${r.label}</th>${r.values.map(v=>`<td>${v}</td>`).join('')}</tr>`).join('')}
          </tbody>
        </table>
      </div>
      <p class="sg-foot muted"><i class="bi bi-info-circle"></i> Between sizes? We usually recommend sizing up. Unsure? <a class="link-arrow" style="display:inline" href="contact.php">Ask us</a> and we'll help.</p>`;
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
