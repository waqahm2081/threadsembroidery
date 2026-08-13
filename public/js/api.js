/* Shared API helpers for dynamic cart / bundles */
(function(){
  const T = window.THREADS = window.THREADS || {};

  function csrf(){
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  }

  T.api = {
    async get(url){
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });
      if(!res.ok) throw await T.api.error(res);
      return res.json();
    },
    async send(url, { method='POST', body=null, formData=null } = {}){
      const headers = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf()
      };
      let payload = body;
      if(formData){
        payload = formData;
      } else if(body && typeof body === 'object'){
        headers['Content-Type'] = 'application/json';
        payload = JSON.stringify(body);
      }
      const res = await fetch(url, { method, headers, body: payload, credentials: 'same-origin' });
      if(!res.ok) throw await T.api.error(res);
      return res.json();
    },
    async error(res){
      let data = {};
      try { data = await res.json(); } catch(e) {}
      const err = new Error(data.message || 'Request failed');
      err.status = res.status;
      err.data = data;
      return err;
    },
    syncBadge(cart){
      const n = cart?.totals?.item_count || 0;
      document.querySelectorAll('[data-cart-badge]').forEach(b=>{
        b.textContent = n;
        b.style.display = n ? 'grid' : 'none';
      });
    }
  };

  // Prefer server cart badge on load when bootstrap exists
  document.addEventListener('DOMContentLoaded', ()=>{
    if(window.THREADS_CART) T.api.syncBadge(window.THREADS_CART);
    else {
      T.api.get('/api/cart').then(cart => T.api.syncBadge(cart)).catch(()=>{});
    }
  });
})();
