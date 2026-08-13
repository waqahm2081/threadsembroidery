/* ============================================================
   Client Portal — DEMO auth via localStorage.
   (Real secure individual logins wire in on launch — no shared PINs.)
   ============================================================ */
(function(){
  const T=window.THREADS, U=T.util, money=U.money;
  const UKEY='threads_users', SKEY='threads_session';
  const users=()=>{try{return JSON.parse(localStorage.getItem(UKEY))||{}}catch(e){return{}}};
  const saveUsers=u=>localStorage.setItem(UKEY,JSON.stringify(u));
  const session=()=>{try{return JSON.parse(localStorage.getItem(SKEY))}catch(e){return null}};

  function seedFor(email,name,org,role){
    const P=T.PRODUCTS;
    const pick=(i)=>P[i%P.length];
    return {
      email,name,org:org||'',role:role||'Account Owner',
      orders:[
        {ref:'THR-104582',product:pick(3),qty:48,method:'Embroidery',placement:'Left Chest',status:'delivered',date:'12 Jun 2026',total:612.40},
        {ref:'THR-106190',product:pick(7),qty:24,method:'DTF Print',placement:'Back Print',status:'dispatched',date:'02 Jul 2026',total:388.00},
        {ref:'THR-107733',product:pick(11),qty:60,method:'Embroidery',placement:'Left Chest + Sleeve',status:'production',date:'21 Jul 2026',total:905.50}
      ],
      artwork:[
        {name:'Primary Logo',type:'PNG · transparent',img:'assets/img/logo.png'},
        {name:'Crest / Badge',type:'SVG · vector',img:'assets/img/logo.png'},
        {name:'Wordmark',type:'PDF · print-ready',img:'assets/img/logo.png'}
      ]
    };
  }

  /* ---------- LOGIN PAGE ---------- */
  T.initLogin=function(){
    if(session()){ location.href='portal.php'; return; }
    const tabs=document.querySelectorAll('.auth-tabs button');
    const login=document.getElementById('loginForm'), signup=document.getElementById('signupForm');
    tabs.forEach(b=>b.onclick=()=>{ tabs.forEach(x=>x.classList.toggle('active',x===b)); const t=b.dataset.tab;
      login.style.display=t==='login'?'':'none'; signup.style.display=t==='signup'?'':'none'; });
    // demo account
    const u=users();
    if(!u['demo@school.co.uk']){ u['demo@school.co.uk']={pass:'demo1234',data:seedFor('demo@school.co.uk','Sarah Mitchell','Greenfield Academy','PE Department')}; saveUsers(u); }

    login.onsubmit=e=>{e.preventDefault();
      const em=login.email.value.trim().toLowerCase(), pw=login.password.value;
      const all=users();
      if(all[em] && all[em].pass===pw){ localStorage.setItem(SKEY,JSON.stringify({email:em})); T.toast('Welcome back!',true); setTimeout(()=>location.href='portal.php',500); }
      else T.toast('Incorrect email or password. Try the demo account below.');
    };
    signup.onsubmit=e=>{e.preventDefault();
      const em=signup.email.value.trim().toLowerCase(), pw=signup.password.value, nm=signup.name.value.trim(), org=signup.org.value.trim();
      if(pw.length<6){T.toast('Password must be at least 6 characters.');return;}
      const all=users();
      if(all[em]){T.toast('An account with that email already exists — please log in.');return;}
      all[em]={pass:pw,data:seedFor(em,nm,org,'Account Owner')}; saveUsers(all);
      localStorage.setItem(SKEY,JSON.stringify({email:em})); T.toast('Account created!',true);
      setTimeout(()=>location.href='portal.php',500);
    };
    const df=document.getElementById('fillDemo'); if(df) df.onclick=()=>{ login.email.value='demo@school.co.uk'; login.password.value='demo1234'; };
  };

  /* ---------- PORTAL PAGE ---------- */
  T.initPortal=function(){
    const s=session(); if(!s){ location.href='login.php'; return; }
    const rec=users()[s.email]; if(!rec){ localStorage.removeItem(SKEY); location.href='login.php'; return; }
    const d=rec.data;
    const initials=(d.name||'U').split(' ').map(w=>w[0]).slice(0,2).join('');
    document.getElementById('pUserAv').textContent=initials;
    document.getElementById('pUserName').textContent=d.name;
    document.getElementById('pUserOrg').textContent=d.org||d.role;

    const views={};
    views.overview=()=>`
      <div class="portal-head"><h1>Welcome back, ${d.name.split(' ')[0]}</h1><p class="muted">Here's what's happening with your account.</p></div>
      <div class="kpi-grid">
        <div class="kpi"><div class="ic"><i class="bi bi-box-seam"></i></div><div class="n">${d.orders.length}</div><div class="l">Total orders</div></div>
        <div class="kpi"><div class="ic"><i class="bi bi-arrow-repeat"></i></div><div class="n">${d.orders.filter(o=>o.status==='production').length}</div><div class="l">In production</div></div>
        <div class="kpi"><div class="ic"><i class="bi bi-images"></i></div><div class="n">${d.artwork.length}</div><div class="l">Saved artworks</div></div>
        <div class="kpi"><div class="ic"><i class="bi bi-cash-stack"></i></div><div class="n">${money(d.orders.reduce((s,o)=>s+o.total,0))}</div><div class="l">Lifetime spend</div></div>
      </div>
      <div class="panel-card"><div class="flex justify-between items-center" style="margin-bottom:.6rem"><h3 style="margin:0">Recent Orders</h3><button class="link-arrow" data-go="orders">View all <i class="bi bi-arrow-right"></i></button></div>
        ${d.orders.slice(0,3).map(orderRow).join('')}</div>
      <div class="panel-card"><h3>Quick Actions</h3><div class="hero-cta" style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn" href="configurator.php"><i class="bi bi-magic"></i> New Order</a><a class="btn btn--ghost" href="bundles.php">Browse Bundles</a><button class="btn btn--ghost" data-go="artwork"><i class="bi bi-images"></i> My Artwork</button></div></div>`;
    views.orders=()=>`
      <div class="portal-head"><h1>Order History</h1><p class="muted">Track your orders and reorder in one click.</p></div>
      <div class="panel-card">${d.orders.map(orderRow).join('')}</div>`;
    views.artwork=()=>`
      <div class="portal-head"><h1>Artwork Library</h1><p class="muted">Your saved logos and designs — ready to apply to any order.</p></div>
      <div class="panel-card"><div class="artwork-grid">
        ${d.artwork.map((a,i)=>`<div class="artwork"><div class="thumb"><img src="${a.img}" alt="${a.name}"></div><div class="meta"><b>${a.name}</b><span>${a.type}</span></div></div>`).join('')}
        <button class="artwork" style="border-style:dashed;display:grid;place-items:center;min-height:160px;color:var(--muted)" id="addArt"><div style="text-align:center"><i class="bi bi-plus-lg" style="font-size:1.6rem;color:var(--brand)"></i><div style="font-size:.82rem;margin-top:.3rem">Upload artwork</div></div></button>
      </div></div>`;
    views.account=()=>`
      <div class="portal-head"><h1>Account</h1><p class="muted">Manage your details.</p></div>
      <div class="panel-card">
        <div class="field-group"><label class="lbl">Name</label><input class="input" id="accName" value="${d.name}"></div>
        <div class="field-group"><label class="lbl">Organisation</label><input class="input" id="accOrg" value="${d.org||''}"></div>
        <div class="field-group"><label class="lbl">Email</label><input class="input" value="${d.email}" disabled style="opacity:.6"></div>
        <div class="field-group"><label class="lbl">Role</label><input class="input" value="${d.role}" disabled style="opacity:.6"></div>
        <button class="btn" id="accSave"><i class="bi bi-check-lg"></i> Save Changes</button>
        <hr class="divider" style="margin:1.6rem 0">
        <button class="btn btn--ghost" id="pLogout"><i class="bi bi-box-arrow-right"></i> Log Out</button>
      </div>`;

    function orderRow(o){
      return `<div class="order-row"><img src="${o.product.image}" alt="">
        <div class="meta"><b>${o.product.title}</b><div class="sub">${o.ref} · ${o.qty} × ${o.method} · ${o.placement} · ${o.date}</div></div>
        <span class="status ${o.status}">${o.status==='production'?'In Production':o.status.charAt(0).toUpperCase()+o.status.slice(1)}</span>
        <div style="text-align:right"><div style="font-family:var(--font-display);font-weight:800;color:var(--brand)">${money(o.total)}</div>
        <button class="link-arrow" style="font-size:.8rem" data-reorder="${o.ref}">Reorder</button></div></div>`;
    }
    function render(view){
      document.querySelectorAll('.portal-nav button').forEach(b=>b.classList.toggle('active',b.dataset.view===view));
      const main=document.getElementById('portalContent'); main.innerHTML=views[view]();
      main.querySelectorAll('[data-go]').forEach(b=>b.onclick=()=>render(b.dataset.go));
      main.querySelectorAll('[data-reorder]').forEach(b=>b.onclick=()=>{ const o=d.orders.find(x=>x.ref===b.dataset.reorder); T.cart.add({title:o.product.title,image:o.product.image,method:o.method,summary:'Reorder · '+o.placement,totalQty:o.qty,total:o.total}); T.toast('Reorder added to your cart.',true); });
      const add=document.getElementById('addArt'); if(add) add.onclick=()=>T.toast('Artwork upload saves to your library on launch.');
      const save=document.getElementById('accSave'); if(save) save.onclick=()=>{ d.name=document.getElementById('accName').value; d.org=document.getElementById('accOrg').value; const all=users(); all[s.email].data=d; saveUsers(all); document.getElementById('pUserName').textContent=d.name; T.toast('Changes saved.',true); };
      const lo=document.getElementById('pLogout'); if(lo) lo.onclick=logout;
      // close mobile drawer
      document.querySelector('.portal-side').classList.remove('open');
    }
    function logout(){ localStorage.removeItem(SKEY); T.toast('Logged out.'); setTimeout(()=>location.href='login.php',400); }

    document.querySelectorAll('.portal-nav button[data-view]').forEach(b=>b.onclick=()=>render(b.dataset.view));
    document.getElementById('navLogout').onclick=logout;
    const mt=document.getElementById('portalMenuBtn'); if(mt) mt.onclick=()=>document.querySelector('.portal-side').classList.toggle('open');
    render('overview');
  };
})();
