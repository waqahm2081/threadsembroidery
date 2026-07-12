/* ==============================================
   product-upload-wizard.js
   Multi-step design upload wizard (replaces customizer redirect)
   ============================================== */
(function () {
  'use strict';

  const PLACEMENTS = {
    left_chest: {
      label: 'Left Chest',
      fee: 3.50,
      methods: ['embroidery', 'print'],
    },
    back_print: {
      label: 'Back Print',
      fee: 6.00,
      methods: ['print'],
    },
    back_embroidery: {
      label: 'Back Embroidery',
      fee: 8.00,
      methods: ['embroidery'],
    },
  };

  const METHOD_LABELS = {
    embroidery: 'Embroidery (Stitching)',
    print: 'Print',
  };

  let state = {
    step: 1,
    placement: null,
    method: null,
    designMode: 'upload',
    artworkFile: null,
    cartContext: null,
  };

  let modal = null;
  let modalEl = null;

  function $(sel) {
    return document.querySelector(sel);
  }

  function $$(sel) {
    return document.querySelectorAll(sel);
  }

  function resetState() {
    state = {
      step: 1,
      placement: null,
      method: null,
      designMode: 'upload',
      artworkFile: null,
      cartContext: state.cartContext,
    };
    clearUpload();
    clearTextForm();
    $$('.uw-position-card').forEach(c => c.classList.remove('selected'));
    $$('.uw-method-card').forEach(c => {
      c.classList.remove('selected');
      c.classList.remove('disabled');
    });
    setDesignMode('upload');
  }

  function showStep(step) {
    state.step = step;
    $$('.uw-step').forEach(el => {
      el.classList.toggle('uw-step-active', parseInt(el.dataset.step, 10) === step);
    });
    updateProgress(step);

    const backBtn = $('#uwBackBtn');
    const continueBtn = $('#uwContinueBtn');

    if (backBtn) backBtn.hidden = step === 1;
    if (continueBtn) continueBtn.textContent = step === 3 ? 'Add to Cart' : 'Continue';

    updateFooterStatus();
    updateContinueBtn();
  }

  function updateProgress(step) {
    const fill = $('#uwProgressFill');
    const progress = Math.max(0, Math.min(100, ((step - 1) / 2) * 100));

    if (fill) {
      fill.style.width = progress + '%';
    }

    $$('.uw-progress-step').forEach(el => {
      const stepNumber = parseInt(el.dataset.progressStep, 10);
      el.classList.toggle('is-active', stepNumber === step);
      el.classList.toggle('is-complete', stepNumber < step);
    });
  }

  function updateFooterStatus() {
    const status = $('#uwFooterStatus');
    if (!status) return;

    if (state.step === 1) {
      status.textContent = state.placement
        ? PLACEMENTS[state.placement].label + ' selected'
        : '';
    } else if (state.step === 2) {
      status.textContent = state.method
        ? METHOD_LABELS[state.method] + ' selected'
        : '';
    } else {
      status.textContent = state.designMode === 'upload'
        ? (state.artworkFile ? 'Artwork ready' : '')
        : (getTextLines().length ? 'Text design ready' : '');
    }
  }

  function updateContinueBtn() {
    const btn = $('#uwContinueBtn');
    if (!btn) return;

    let enabled = false;
    if (state.step === 1) enabled = !!state.placement;
    else if (state.step === 2) enabled = !!state.method;
    else if (state.step === 3) {
      if (state.designMode === 'upload') enabled = !!state.artworkFile;
      else enabled = !!$('#uwTextLine1')?.value.trim();
    }
    btn.disabled = !enabled;
  }

  function updatePositionCount() {
    const el = $('#uwPositionCount');
    if (el) {
      el.textContent = state.placement ? '1 position selected' : '0 position selected';
    }
  }

  function selectPlacement(key) {
    state.placement = key;
    $$('.uw-position-card').forEach(c => {
      c.classList.toggle('selected', c.dataset.placement === key);
    });
    updatePositionCount();
    updateFooterStatus();
    updateContinueBtn();
  }

  function selectMethod(key) {
    state.method = key;
    $$('.uw-method-card').forEach(c => {
      c.classList.toggle('selected', c.dataset.method === key);
    });
    updateFooterStatus();
    updateContinueBtn();
  }

  function setupMethodStep() {
    const placement = PLACEMENTS[state.placement];
    if (!placement) return;

    $$('.uw-method-card').forEach(card => {
      const method = card.dataset.method;
      const allowed = placement.methods.includes(method);
      card.classList.toggle('disabled', !allowed);
      card.classList.remove('selected');
    });

    if (placement.methods.length === 1) {
      selectMethod(placement.methods[0]);
    } else {
      state.method = null;
      updateContinueBtn();
    }
  }

  function setDesignMode(mode) {
    state.designMode = mode;
    $$('.uw-design-toggle-btn').forEach(btn => {
      btn.classList.toggle('uw-design-toggle-btn--active', btn.dataset.designMode === mode);
    });
    $('#uwUploadPanel')?.classList.toggle('uw-design-panel--active', mode === 'upload');
    $('#uwTextPanel')?.classList.toggle('uw-design-panel--active', mode === 'text');
    updateFooterStatus();
    updateContinueBtn();
  }

  function clearUpload() {
    state.artworkFile = null;
    const zone = $('#uwUploadZone');
    const placeholder = $('#uwUploadPlaceholder');
    const preview = $('#uwUploadPreview');
    const input = $('#uwFileInput');
    const img = $('#uwPreviewImg');

    if (zone) zone.classList.remove('has-file');
    if (placeholder) placeholder.hidden = false;
    if (preview) preview.hidden = true;
    if (input) input.value = '';
    if (img) img.src = '';
  }

  function clearTextForm() {
    ['uwTextLine1', 'uwTextLine2', 'uwTextLine3'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    const font = $('#uwTextFont');
    const color = $('#uwTextColor');
    if (font) font.selectedIndex = 0;
    if (color) color.selectedIndex = 0;
    updateTextPreview();
  }

  function handleFile(file) {
    if (!file) return;
    const allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/webp'];
    if (!allowed.includes(file.type)) {
      alert('Please upload a PNG, JPG, SVG, or WebP image.');
      return;
    }
    if (file.size > 10 * 1024 * 1024) {
      alert('File size must be under 10 MB.');
      return;
    }

    state.artworkFile = file;
    const reader = new FileReader();
    reader.onload = e => {
      const img = $('#uwPreviewImg');
      const placeholder = $('#uwUploadPlaceholder');
      const preview = $('#uwUploadPreview');
      const zone = $('#uwUploadZone');

      if (img) img.src = e.target.result;
      if (placeholder) placeholder.hidden = true;
      if (preview) preview.hidden = false;
      if (zone) zone.classList.add('has-file');
      updateFooterStatus();
      updateContinueBtn();
    };
    reader.readAsDataURL(file);
  }

  function getTextLines() {
    const lines = [
      $('#uwTextLine1')?.value.trim(),
      $('#uwTextLine2')?.value.trim(),
      $('#uwTextLine3')?.value.trim(),
    ].filter(Boolean);
    return lines;
  }

  function updateTextPreview() {
    const content = $('#uwTextPreviewContent');
    const preview = $('#uwTextPreview');
    const font = $('#uwTextFont')?.value || 'Arial';
    const color = $('#uwTextColor')?.value || '#ffffff';
    const lines = getTextLines();

    if (content) {
      content.textContent = lines.length ? lines.join('\n') : 'Your text will appear here';
      content.style.fontFamily = font;
      content.style.color = color;
    }
    if (preview) {
      preview.style.fontFamily = font;
    }
    updateFooterStatus();
    updateContinueBtn();
  }

  function getSelectedColorLabel() {
    const sel = $('#uwTextColor');
    if (!sel) return 'White';
    return sel.options[sel.selectedIndex]?.dataset.label || sel.options[sel.selectedIndex]?.text || 'White';
  }

  async function generateTextPreviewBlob() {
    const lines = getTextLines();
    const font = $('#uwTextFont')?.value || 'Arial';
    const color = $('#uwTextColor')?.value || '#ffffff';

    const canvas = document.createElement('canvas');
    canvas.width = 800;
    canvas.height = 400;
    const ctx = canvas.getContext('2d');

    ctx.fillStyle = '#2c3e50';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    ctx.fillStyle = color;
    ctx.font = `bold 48px "${font}", sans-serif`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    const lineHeight = 58;
    const startY = canvas.height / 2 - ((lines.length - 1) * lineHeight) / 2;
    lines.forEach((line, i) => {
      ctx.fillText(line, canvas.width / 2, startY + i * lineHeight);
    });

    return new Promise(resolve => {
      canvas.toBlob(blob => resolve(blob), 'image/png');
    });
  }

  async function submitDesign() {
    const ctx = state.cartContext;
    if (!ctx?.cart_item_id || !ctx?.customize_url) {
      alert('Cart item not found. Please try again from the product page.');
      return;
    }

    const placement = PLACEMENTS[state.placement];
    const designData = {
      wizard_version: 2,
      design_type: state.designMode,
      application_method: state.method,
      decoration_placement: state.placement,
      decoration_placement_label: placement.label,
      garment: ctx.product_title || '',
      colour: ctx.colour || {},
      size: ctx.size || '',
      qty: ctx.quantity || 1,
    };

    if (state.designMode === 'text') {
      designData.text_design = {
        line1: $('#uwTextLine1')?.value.trim() || '',
        line2: $('#uwTextLine2')?.value.trim() || '',
        line3: $('#uwTextLine3')?.value.trim() || '',
        font: $('#uwTextFont')?.value || 'Arial',
        color: getSelectedColorLabel(),
        color_hex: $('#uwTextColor')?.value || '#ffffff',
      };
    }

    const formData = new FormData();
    formData.append('design_data', JSON.stringify(designData));
    formData.append('unit_price', ctx.unit_price || 0);
    formData.append('decoration_fee', placement.fee);
    formData.append('quantity', ctx.quantity || 1);
    formData.append('size', ctx.size || '');
    formData.append('colour_name', ctx.colour?.name || '');

    if (state.designMode === 'upload' && state.artworkFile) {
      formData.append('artwork_file', state.artworkFile);
      formData.append('preview_image', state.artworkFile);
    } else if (state.designMode === 'text') {
      const blob = await generateTextPreviewBlob();
      if (blob) {
        formData.append('preview_image', blob, 'text-preview.png');
      }
    }

    const btn = $('#uwContinueBtn');
    const originalText = btn?.textContent;
    if (btn) {
      btn.classList.add('loading');
      btn.textContent = 'Saving…';
      btn.disabled = true;
    }

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
      const res = await fetch(ctx.customize_url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData,
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Failed to save design');

      const badge = document.getElementById('navCartBadge');
      if (badge && data.cart_count > 0) {
        badge.textContent = data.cart_count;
        badge.style.display = '';
      }

      modal?.hide();
      window.location.href = data.cart_url || '/cart';
    } catch (err) {
      alert(err.message || 'Could not save your design. Please try again.');
      if (btn) {
        btn.classList.remove('loading');
        btn.textContent = originalText;
        btn.disabled = false;
      }
    }
  }

  function bindEvents() {
    $$('.uw-position-card').forEach(card => {
      card.addEventListener('click', () => selectPlacement(card.dataset.placement));
    });

    $$('.uw-method-card').forEach(card => {
      card.addEventListener('click', () => {
        if (!card.classList.contains('disabled')) {
          selectMethod(card.dataset.method);
        }
      });
    });

    $$('.uw-design-toggle-btn').forEach(btn => {
      btn.addEventListener('click', () => setDesignMode(btn.dataset.designMode));
    });

    const uploadZone = $('#uwUploadZone');
    const fileInput = $('#uwFileInput');

    if (uploadZone && fileInput) {
      uploadZone.addEventListener('dragover', e => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
      });
      uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
      uploadZone.addEventListener('drop', e => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        if (e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
      });
      fileInput.addEventListener('change', () => {
        if (fileInput.files.length) handleFile(fileInput.files[0]);
      });
    }

    $('#uwRemoveFile')?.addEventListener('click', e => {
      e.stopPropagation();
      clearUpload();
      updateFooterStatus();
      updateContinueBtn();
    });

    ['uwTextLine1', 'uwTextLine2', 'uwTextLine3', 'uwTextFont', 'uwTextColor'].forEach(id => {
      document.getElementById(id)?.addEventListener('input', updateTextPreview);
      document.getElementById(id)?.addEventListener('change', updateTextPreview);
    });

    $('#uwBackBtn')?.addEventListener('click', () => {
      if (state.step > 1) showStep(state.step - 1);
    });

    $('#uwContinueBtn')?.addEventListener('click', async () => {
      if (state.step === 1 && state.placement) {
        setupMethodStep();
        showStep(2);
      } else if (state.step === 2 && state.method) {
        showStep(3);
      } else if (state.step === 3) {
        await submitDesign();
      }
    });

    $$('[data-uw-close]').forEach(btn => {
      btn.addEventListener('click', () => {
        if (confirm('Close without saving your design?')) {
          modal?.hide();
        }
      });
    });
  }

  function open(context) {
    if (!modalEl) return;
    state.cartContext = context;
    resetState();
    showStep(1);
    modal.show();
  }

  function init() {
    modalEl = document.getElementById('pdUploadWizard');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    modal = new bootstrap.Modal(modalEl);
    bindEvents();

    modalEl.addEventListener('hidden.bs.modal', () => {
      resetState();
      state.cartContext = null;
    });
  }

  window.TEUploadWizard = { open, init };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
