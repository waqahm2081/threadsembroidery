/* Server-backed cart page */
(function(){
  const T=window.THREADS, U=T.util, money=U.money;
  let cart = window.THREADS_CART || {items:[], totals:{subtotal:0,vat:0,delivery:0,total:0}};

  function artworkButtons(files){
    if(!files||!files.length) return '';
    return `<div class="cart-art-btns">
      ${files.map(f=>`
        <button type="button" class="cart-art-btn" data-artwork-modal
          data-label="${(f.label||'Artwork').replace(/"/g,'&quot;')}"
          data-name="${(f.name||'').replace(/"/g,'&quot;')}"
          data-url="${(f.url||'').replace(/"/g,'&quot;')}">
          <i class="bi bi-image"></i> ${f.label||'Artwork'}
        </button>`).join('')}
    </div>`;
  }

  function render(){
    const root=document.getElementById('cartRoot'); if(!root) return;
    const items=cart.items||[], vat=U.vatState();
    if(!items.length){
      root.innerHTML=`<div class="empty"><i class="bi bi-bag"></i><h3 class="h-3">Your cart is empty</h3><p class="mt-1">Grab a fixed-price bundle to get started.</p><div class="hero-cta mt-3" style="justify-content:center"><a class="btn" href="/bundles"><i class="bi bi-collection"></i> Shop Bundles</a></div></div>`;
      T.api.syncBadge(cart);
      return;
    }
    const rows=items.map(it=>`
      <div class="cart-row card card--pad">
        <div class="cart-row-main">
          <div class="cart-row-info">
            <b>${it.title}</b>
            <div class="muted" style="font-size:.85rem">${it.summary||''}</div>
            <div class="muted" style="font-size:.85rem">Qty ${it.totalQty} · ${it.method||''}</div>
            ${artworkButtons(it.files)}
          </div>
          <div class="cart-row-side">
            <div class="p" style="font-family:var(--font-display);font-weight:800;font-size:1.2rem;color:var(--brand)">${U.show(it.total,vat)}</div>
            <button class="link-arrow" style="color:var(--muted);font-size:.82rem" data-remove="${it.id}">Remove</button>
          </div>
        </div>
      </div>`).join('');
    const t=cart.totals||{};
    root.innerHTML=`<div class="cart-layout">
      <div>${rows}</div>
      <div class="card card--pad" style="position:sticky;top:calc(var(--header-h) + 16px)">
        <h3 class="h-3" style="margin-bottom:1rem">Order Summary</h3>
        <div class="flex justify-between mt-1"><span class="muted">Subtotal (ex VAT)</span><b>${money(t.subtotal||0)}</b></div>
        <div class="flex justify-between mt-1"><span class="muted">VAT (20%)</span><b>${money(t.vat||0)}</b></div>
        <div class="flex justify-between mt-1"><span class="muted">Delivery</span><b>${(t.delivery||0)>0?money(t.delivery):'FREE'}</b></div>
        <hr class="divider" style="margin:1rem 0">
        <div class="flex justify-between"><span style="font-weight:700">Total (inc VAT)</span><b style="font-family:var(--font-display);font-size:1.5rem;color:var(--brand)">${money(t.total||0)}</b></div>
        <a class="btn btn--block btn--lg mt-3" href="/checkout"><i class="bi bi-lock-fill"></i> Secure Checkout</a>
        <p class="help mt-2" style="text-align:center"><i class="bi bi-shield-check"></i> Free UK delivery on orders over £150.</p>
      </div></div>`;

    root.querySelectorAll('[data-remove]').forEach(b=>b.onclick=()=>removeItem(+b.dataset.remove));
    T.api.syncBadge(cart);
  }

  async function removeItem(id){
    try{
      const res=await T.api.send('/api/cart/items/'+id, { method:'DELETE' });
      cart=res.cart; render();
      T.toast('Item removed.');
    }catch(e){ T.toast('Could not remove item.'); }
  }

  T.mount({active:'', base:'', onReady:async ()=>{
    try{ cart = await T.api.get('/api/cart'); }catch(e){}
    render();
  }});
})();
