/* ============================================================
   Homepage dynamic content
   ============================================================ */
(function(){
  const T=window.THREADS, C=T.CONFIG, U=T.util;
  T.mount({active:'home', base:'', onReady:build});

  function swatchRow(cols){
    const shown=cols.slice(0,5).map(c=>`<i style="background:${c.hex||'#888'}" title="${c.name}"></i>`).join('');
    const more=cols.length>5?`<span class="more">+${cols.length-5}</span>`:'';
    return `<div class="sw">${shown}${more}</div>`;
  }
  function productCard(p){
    const price=U.retail(p.base_price);
    const vat=U.vatState();
    return `<a class="prod-card" href="product.php?id=${p.slug}">
      <div class="prod-media">
        <img src="${p.image}" alt="${p.title}" loading="lazy">
        <div class="flags">${p.is_new?'<span class="tag-new">New In</span>':''}</div>
        <div class="rate"><i class="bi bi-star-fill"></i>4.9</div>
      </div>
      <div class="prod-body">
        <span class="brand">${p.brand||'Threads'}</span>
        <h3>${p.title}</h3>
        ${swatchRow(p.colours||[])}
        <div class="prod-foot">
          <div class="p" data-price="${price}">${U.show(price,vat)} <small>from / unit</small></div>
          <span class="go"><i class="bi bi-arrow-up-right"></i></span>
        </div>
      </div></a>`;
  }

  function build(){
    // Bundles (feature 3)
    const bWrap=document.getElementById('homeBundles');
    if(bWrap){
      bWrap.innerHTML=THREADS.BUNDLES.slice(0,3).map((b,i)=>`
        <div class="bundle-card ${b.featured?'feat':''}" data-reveal data-delay="${i}">
          ${b.badge?`<span class="btag pill pill--brand">${b.badge}</span>`:''}
          <div class="ic"><i class="bi ${b.icon}"></i></div>
          <h3>${b.name}</h3>
          <div class="desc">${b.tagline}</div>
          <div class="price">${U.money(b.price)} <small>from · ex VAT</small></div>
          <ul>${b.includes.slice(0,4).map(x=>`<li><i class="bi bi-check-circle-fill"></i> ${x}</li>`).join('')}</ul>
          <div class="bundle-actions">
            <button type="button" class="btn btn--ghost btn--block" data-size-guide="${b.id}"><i class="bi bi-rulers"></i> Size Guide</button>
            <a class="btn btn--block" href="bundles.php#${b.id}">Build This Bundle <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>`).join('');
    }
    // Featured products
    const pWrap=document.getElementById('homeProducts');
    if(pWrap){
      const feat=THREADS.PRODUCTS.filter(p=>p.is_new).slice(0,6);
      const list=(feat.length>=6?feat:THREADS.PRODUCTS).slice(0,6);
      pWrap.innerHTML=list.map(productCard).join('');
    }
    // Testimonials
    const tWrap=document.getElementById('homeTesti');
    if(tWrap){
      const TES=[
        ['Absolutely brilliant service from start to finish. The embroidery on our staff polos was flawless and the turnaround was faster than expected. Will be ordering again without hesitation.','James Thornton','Thornton Building Services'],
        ["We've used Threads three years running for our school's PE kits and uniform. The quality is consistently excellent and the team are always responsive and helpful.",'Sarah Mitchell','Greenfield Academy'],
        ['Ordered 50 branded hoodies for our football club. The print quality was superb and every detail of our crest came out perfectly. Fantastic value too.','Dean Kavanagh','Riverside FC'],
        ['The online customiser made the whole process so easy. I could see exactly how our logo would look before ordering. Delivery was quick and the jackets looked incredible.','Priya Sharma','Horizon Hospitality Group'],
        ['We kitted out our entire warehouse team — 80 people — without a hitch. Great communication, great product and a price that beat every other quote we got.','Mark Ellison','Ellison Logistics Ltd'],
        ['Our salon staff look so professional in the branded tunics. The artwork team were patient with revisions and the result was better than we imagined.','Lauren Davies','Luxe Hair & Beauty Studio']
      ];
      tWrap.innerHTML=TES.slice(0,6).map((t,i)=>`
        <div class="testi" data-reveal data-delay="${i%3}">
          <div class="stars">${'<i class="bi bi-star-fill"></i>'.repeat(5)}</div>
          <p>"${t[0]}"</p>
          <div class="who"><span class="av">${t[1].split(' ').map(w=>w[0]).slice(0,2).join('')}</span><div><b>${t[1]}</b><span>${t[2]}</span></div></div>
        </div>`).join('');
      wireTestiSlider(tWrap);
    }
    // mock customiser image + light UI polish
    const mock=document.getElementById('custMockImg');
    if(mock){
      const tee=THREADS.PRODUCTS.find(p=>p.category_slug==='t-shirts')||THREADS.PRODUCTS[0];
      const frontSrc=tee.image;
      const backSrc=/FRONT/i.test(frontSrc)
        ? frontSrc.replace(/FRONT/g,'BACK').replace(/Front/g,'Back')
        : null;
      mock.src=frontSrc;
      const title=document.getElementById('custMockTitle');
      if(title) title.textContent=tee.title||'Softstyle Tee';
      const logoChip=document.querySelector('.cust-mock .logo-chip');
      const posSel=[...document.querySelectorAll('.cust-mock .panel .row')].find(r=>{
        const label=r.querySelector('.muted');
        return label&&label.textContent.trim()==='Position';
      })?.querySelector('.sel');
      const viewsWrap=document.querySelector('.cust-mock .pv-views');
      const views=viewsWrap?viewsWrap.querySelectorAll('button'):[];
      const setView=(view)=>{
        const isBack=view==='back';
        mock.src=isBack&&backSrc?backSrc:frontSrc;
        if(logoChip) logoChip.classList.toggle('is-back',isBack);
        if(posSel) posSel.textContent=isBack?'Centre Back':'Left Chest';
        views.forEach(b=>{
          const label=(b.textContent||'').trim().toLowerCase();
          b.classList.toggle('active',isBack?label==='back':label==='front');
        });
      };
      if(viewsWrap&&backSrc){
        const probe=new Image();
        probe.onload=()=>{
          views.forEach(btn=>btn.addEventListener('click',()=>{
            setView((btn.textContent||'').trim().toLowerCase()==='back'?'back':'front');
          }));
        };
        probe.onerror=()=>{ viewsWrap.remove(); };
        probe.src=backSrc;
      }else if(viewsWrap){
        viewsWrap.remove();
      }
      const sw=document.querySelectorAll('.cust-mock .pv-swatches i');
      sw.forEach(dot=>dot.addEventListener('click',()=>{
        sw.forEach(d=>d.classList.remove('on'));
        dot.classList.add('on');
        const colourSel=document.querySelector('.cust-mock .sel--sw');
        if(colourSel){
          colourSel.innerHTML=`<i style="background:${dot.style.background}"></i> ${dot.title||'Colour'}`;
        }
      }));
    }

    // VAT toggle
    const vt=document.getElementById('vatToggle');
    if(vt){ vt.checked=U.vatState();
      vt.addEventListener('change',()=>{ U.setVat(vt.checked);
        document.querySelectorAll('#homeProducts .p').forEach(el=>{ const base=parseFloat(el.dataset.price); el.innerHTML=`${U.show(base,vt.checked)} <small>from / unit</small>`; });
      });
    }
    // re-observe newly injected reveals
    const io=new IntersectionObserver((es)=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}}),{threshold:.12});
    document.querySelectorAll('[data-reveal]:not(.in)').forEach(el=>io.observe(el));
  }

  function wireTestiSlider(track){
    const cards=[...track.querySelectorAll('.testi')];
    if(!cards.length) return;
    const dots=document.getElementById('homeTestiDots');
    const nav=document.getElementById('homeTestiNav');
    if(dots){
      dots.hidden=false;
      dots.innerHTML=cards.map((_,i)=>`<button type="button" aria-label="Go to review ${i+1}"${i===0?' aria-current="true"':''}></button>`).join('');
      [...dots.children].forEach((btn,i)=>{
        btn.onclick=()=>cards[i].scrollIntoView({behavior:'smooth',inline:'start',block:'nearest'});
      });
    }
    if(nav){
      nav.hidden=false;
      nav.querySelectorAll('[data-dir]').forEach(btn=>{
        btn.onclick=()=>{
          const dir=Number(btn.dataset.dir);
          const amount=Math.round(track.clientWidth*dir);
          track.scrollBy({left:amount,behavior:'smooth'});
        };
      });
    }
    const sync=()=>{
      if(!dots) return;
      const left=track.scrollLeft;
      let best=0, bestDist=Infinity;
      cards.forEach((card,i)=>{
        const d=Math.abs(card.offsetLeft-left);
        if(d<bestDist){bestDist=d;best=i;}
      });
      [...dots.children].forEach((btn,i)=>btn.setAttribute('aria-current',i===best?'true':'false'));
    };
    track.addEventListener('scroll',()=>{ window.requestAnimationFrame(sync); },{passive:true});
    sync();
  }
})();
