/* Checkout form -> Stripe card payment then place order */
(function(){
  const T=window.THREADS, money=T.util.money;
  let cart = window.THREADS_CART || {items:[], totals:{}};
  let stripeCtx = null;

  function renderSummary(){
    const box=document.getElementById('checkoutSummary'); if(!box) return;
    const items=cart.items||[], t=cart.totals||{};
    box.innerHTML=`
      <h3 class="h-3" style="margin-bottom:1rem">Order Summary</h3>
      <div class="checkout-summary-items">
        ${items.map(it=>`
          <div class="checkout-summary-item">
            <div>
              <b>${it.title}</b>
              <div class="muted" style="font-size:.8rem;margin-top:.2rem">${it.summary||''}</div>
              <div class="muted" style="font-size:.78rem">Qty ${it.totalQty||1}${it.method?' · '+it.method:''}</div>
            </div>
            <b>${money(it.total)}</b>
          </div>`).join('')}
      </div>
      <hr class="divider" style="margin:1rem 0">
      <div class="flex justify-between mt-1"><span class="muted">Subtotal (ex VAT)</span><b>${money(t.subtotal||0)}</b></div>
      <div class="flex justify-between mt-1"><span class="muted">VAT (20%)</span><b>${money(t.vat||0)}</b></div>
      <div class="flex justify-between mt-1"><span class="muted">Delivery</span><b>${(t.delivery||0)>0?money(t.delivery):'FREE'}</b></div>
      <div class="flex justify-between mt-2"><span style="font-weight:700">Total (inc VAT)</span><b style="font-family:var(--font-display);font-size:1.45rem;color:var(--brand)">${money(t.total||0)}</b></div>
      <button class="btn btn--block btn--lg mt-3" type="submit" form="checkoutForm"><i class="bi bi-lock-fill"></i> Pay &amp; Place Order</button>
      <p class="help mt-2" style="text-align:center"><i class="bi bi-shield-check"></i> Secure card payment via Stripe.</p>
      <a class="link-arrow mt-2" style="justify-content:center;display:flex" href="/cart">Back to cart</a>`;
  }

  function syncBilling(){
    const same=document.getElementById('billingSame');
    const wrap=document.getElementById('billingFields');
    if(!same||!wrap) return;
    const on=same.checked;
    wrap.hidden=on;
    wrap.querySelectorAll('input').forEach(inp=>{
      if(['billing_name','billing_address_line1','billing_city','billing_postcode'].includes(inp.name)){
        inp.required=!on;
      }
      if(on) inp.closest('.field-group')?.classList.remove('invalid');
    });
  }

  function validateField(fg){
    const input=fg.querySelector('.input,.textarea');
    if(!input || input.disabled || input.closest('[hidden]')) { fg.classList.remove('invalid'); return true; }
    let ok=true, val=(input.value||'').trim();
    if(input.required && !val) ok=false;
    if(ok && input.type==='email' && val && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(val)) ok=false;
    fg.classList.toggle('invalid', !ok);
    return ok;
  }

  async function submit(e){
    e.preventDefault();
    const form=e.target;
    let firstBad=null;
    form.querySelectorAll('.field-group').forEach(fg=>{
      if(!validateField(fg) && !firstBad) firstBad=fg;
    });
    if(firstBad){
      firstBad.scrollIntoView({block:'center', behavior:'smooth'});
      firstBad.querySelector('.input,.textarea')?.focus();
      T.toast('Please check the highlighted fields.');
      return;
    }

    if(!stripeCtx){
      T.toast('Payment form is still loading. Please wait a moment.');
      return;
    }

    const btn=document.querySelector('[type=submit][form="checkoutForm"], #checkoutForm [type=submit]');
    const data=Object.fromEntries(new FormData(form).entries());
    data.billing_same_as_shipping = document.getElementById('billingSame')?.checked ? '1' : '0';

    if(btn){ btn.disabled=true; btn.innerHTML='<i class="bi bi-hourglass-split"></i> Processing payment…'; }
    try{
      const paid = await window.TEStripeCheckout.pay({
        stripe: stripeCtx.stripe,
        card: stripeCtx.card,
        context: 'bundle',
        email: data.email,
        name: data.name,
        billing: {
          line1: data.billing_same_as_shipping === '1' ? data.shipping_address_line1 : data.billing_address_line1,
          city: data.billing_same_as_shipping === '1' ? data.shipping_city : data.billing_city,
          postal_code: data.billing_same_as_shipping === '1' ? data.shipping_postcode : data.billing_postcode,
          country: 'GB',
        },
        onStatus: (msg)=>{ if(btn) btn.innerHTML = `<i class="bi bi-hourglass-split"></i> ${msg}`; },
      });

      data.payment_intent_id = paid.paymentIntentId;
      if(btn) btn.innerHTML='<i class="bi bi-hourglass-split"></i> Placing order…';

      const res=await T.api.send('/api/checkout', { body:data });
      T.api.syncBadge({totals:{item_count:0}});
      T.toast('Payment successful — order placed.', true);
      location.href=res.redirect;
    }catch(err){
      const msg=err?.data?.errors ? Object.values(err.data.errors).flat()[0] : (err?.data?.message || err.message || 'Could not complete checkout.');
      T.toast(msg);
      const errEl=document.getElementById('stripeCardErrors');
      if(errEl) errEl.textContent = msg;
      if(btn){ btn.disabled=false; btn.innerHTML='<i class="bi bi-lock-fill"></i> Pay &amp; Place Order'; }
    }
  }

  T.mount({active:'', base:'', onReady:async ()=>{
    try{ cart = await T.api.get('/api/cart'); }catch(e){}
    if(!(cart.items||[]).length){ location.href='/cart'; return; }
    renderSummary();
    const form=document.getElementById('checkoutForm');
    const same=document.getElementById('billingSame');
    if(same){ same.addEventListener('change', syncBilling); syncBilling(); }
    if(form){
      form.addEventListener('submit', submit);
      form.querySelectorAll('.field-group').forEach(fg=>{
        const input=fg.querySelector('.input,.textarea');
        if(!input) return;
        input.addEventListener('blur', ()=>validateField(fg));
        input.addEventListener('input', ()=>{ if(fg.classList.contains('invalid')) validateField(fg); });
      });
    }

    try{
      stripeCtx = await window.TEStripeCheckout.initCardElement({
        mountSelector: '#stripeCardElement',
        errorSelector: '#stripeCardErrors',
      });
    }catch(err){
      const errEl=document.getElementById('stripeCardErrors');
      if(errEl) errEl.textContent = err.message || 'Could not load payment form.';
      T.toast(err.message || 'Could not load payment form.');
    }
  }});
})();
