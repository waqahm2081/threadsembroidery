/* Shared artwork preview modal (cart + order confirmation) */
(function(){
  const T = window.THREADS = window.THREADS || {};

  function isImageUrl(url, name){
    const src = (name || url || '').toLowerCase();
    return /\.(png|jpe?g|gif|webp|svg)(\?|$)/i.test(src);
  }

  function ensureArtModal(){
    let m = document.getElementById('cartArtModal');
    if (m) return m;
    m = document.createElement('div');
    m.id = 'cartArtModal';
    m.className = 'cart-art-modal';
    m.innerHTML = `
      <div class="cart-art-card">
        <div class="cart-art-head">
          <div>
            <span class="h-eyebrow">Artwork</span>
            <h3 class="h-3 mt-1" id="cartArtTitle">Preview</h3>
          </div>
          <button class="icon-btn" type="button" id="cartArtClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="cart-art-body" id="cartArtBody"></div>
      </div>`;
    document.body.appendChild(m);
    m.querySelector('#cartArtClose').onclick = () => m.classList.remove('open');
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') m.classList.remove('open'); });
    return m;
  }

  T.openArtworkModal = function(file){
    const m = ensureArtModal();
    m.querySelector('#cartArtTitle').textContent = file.label || 'Artwork';
    const body = m.querySelector('#cartArtBody');
    if (isImageUrl(file.url, file.name)) {
      body.innerHTML = `<img src="${file.url}" alt="${file.label || file.name || 'Artwork'}">`;
    } else {
      body.innerHTML = `
        <div class="cart-art-file">
          <i class="bi bi-file-earmark"></i>
          <b>${file.name || 'Uploaded file'}</b>
          <a class="btn mt-2" href="${file.url}" target="_blank" rel="noopener">Open file</a>
        </div>`;
    }
    m.classList.add('open');
  };

  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-artwork-modal]');
    if (!btn) return;
    e.preventDefault();
    T.openArtworkModal({
      label: btn.dataset.label || btn.textContent.trim(),
      name: btn.dataset.name || '',
      url: btn.dataset.url || btn.getAttribute('href') || ''
    });
  });
})();
