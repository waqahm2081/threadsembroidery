/* ==============================================
   BRANDING-ENQUIRY.JS — Multi-step form (frontend only)
   ============================================== */
(function () {
  'use strict';

  const form       = document.getElementById('brandingEnquiryForm');
  const steps      = form ? form.querySelectorAll('.be-step') : [];
  const progressItems = document.querySelectorAll('#beProgressList .be-progress-item');
  const backBtn    = document.getElementById('beBackBtn');
  const nextBtn    = document.getElementById('beNextBtn');
  const submitBtn  = document.getElementById('beSubmitBtn');
  const nav        = document.getElementById('beNav');
  const successEl  = document.getElementById('beSuccess');
  const mobileFill  = document.getElementById('beMobileProgressFill');
  const mobileLabel = document.getElementById('beMobileProgressLabel');
  const uploadZone = document.getElementById('beUploadZone');
  const fileInput  = document.getElementById('beDesignFiles');
  const fileList   = document.getElementById('beFileList');

  if (!form) return;

  const TOTAL_STEPS = 4;
  let currentStep = 1;
  let selectedFiles = [];

  const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB

  /* ── Choice box toggling ── */
  function initChoiceBoxes() {
    form.querySelectorAll('.be-choice-box').forEach(box => {
      const input = box.querySelector('.be-choice-input');
      if (!input) return;

      const sync = () => {
        if (input.type === 'radio') {
          const group = form.querySelectorAll(`input[name="${input.name}"]`);
          group.forEach(r => {
            r.closest('.be-choice-box').classList.toggle('selected', r.checked);
          });
        } else {
          box.classList.toggle('selected', input.checked);
        }
      };

      input.addEventListener('change', sync);
      sync();
    });
  }

  /* ── File upload ── */
  function formatSize(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function renderFileList() {
    if (!fileList) return;
    fileList.innerHTML = '';
    selectedFiles.forEach((file, index) => {
      const li = document.createElement('li');
      li.className = 'be-file-item';
      li.innerHTML = `
        <span class="be-file-item-name">
          <i class="bi bi-file-earmark-image"></i>
          <span>${file.name} (${formatSize(file.size)})</span>
        </span>
        <button type="button" class="be-file-remove" data-index="${index}" aria-label="Remove file">
          <i class="bi bi-x-lg"></i>
        </button>`;
      fileList.appendChild(li);
    });

    fileList.querySelectorAll('.be-file-remove').forEach(btn => {
      btn.addEventListener('click', () => {
        selectedFiles.splice(parseInt(btn.dataset.index, 10), 1);
        renderFileList();
        clearUploadError();
      });
    });
  }

  function addFiles(files) {
    Array.from(files).forEach(file => {
      if (file.size > MAX_FILE_SIZE) {
        alert(`"${file.name}" exceeds the 10 MB limit and was not added.`);
        return;
      }
      const exists = selectedFiles.some(f => f.name === file.name && f.size === file.size);
      if (!exists) selectedFiles.push(file);
    });
    renderFileList();
    clearUploadError();
  }

  if (uploadZone && fileInput) {
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length) addFiles(fileInput.files);
      fileInput.value = '';
    });

    uploadZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadZone.classList.add('dragover');
    });

    uploadZone.addEventListener('dragleave', () => {
      uploadZone.classList.remove('dragover');
    });

    uploadZone.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadZone.classList.remove('dragover');
      if (e.dataTransfer.files.length) addFiles(e.dataTransfer.files);
    });
  }

  /* ── Progress UI ── */
  function updateProgress() {
    progressItems.forEach(item => {
      const step = parseInt(item.dataset.step, 10);
      item.classList.remove('active', 'completed');
      if (step < currentStep) item.classList.add('completed');
      else if (step === currentStep) item.classList.add('active');
    });

    if (mobileFill) {
      mobileFill.style.width = `${(currentStep / TOTAL_STEPS) * 100}%`;
    }

    if (mobileLabel) {
      mobileLabel.textContent = `Step ${currentStep} of ${TOTAL_STEPS}`;
    }

    backBtn.style.display = currentStep > 1 ? 'inline-flex' : 'none';
    nextBtn.style.display = currentStep < TOTAL_STEPS ? 'inline-flex' : 'none';
    submitBtn.style.display = currentStep === TOTAL_STEPS ? 'inline-flex' : 'none';
  }

  function goToStep(step) {
    steps.forEach(s => s.classList.remove('active'));
    const target = form.querySelector(`.be-step[data-step="${step}"]`);
    if (target) target.classList.add('active');
    currentStep = step;
    updateProgress();
    window.scrollTo({ top: document.getElementById('be-body').offsetTop - 80, behavior: 'smooth' });
  }

  /* ── Validation ── */
  function clearFieldError(field) {
    if (!field) return;
    field.classList.remove('has-error');
    const input = field.querySelector('.be-input, .be-textarea');
    if (input) input.classList.remove('invalid');
  }

  function setFieldError(field) {
    if (!field) return;
    field.classList.add('has-error');
    const input = field.querySelector('.be-input, .be-textarea');
    if (input) input.classList.add('invalid');
  }

  function validateInput(id, testFn) {
    const el = document.getElementById(id);
    if (!el) return true;
    const field = el.closest('.be-field');
    const valid = testFn(el.value.trim());
    valid ? clearFieldError(field) : setFieldError(field);
    return valid;
  }

  function clearUploadError() {
    const field = uploadZone ? uploadZone.closest('.be-field') : null;
    if (field) field.classList.remove('has-error');
    if (uploadZone) uploadZone.classList.remove('invalid');
  }

  function validateStep(step) {
    switch (step) {
      case 1: {
        const confirm = document.getElementById('beMinOrderConfirm');
        const field = confirm ? confirm.closest('.be-field') : null;
        if (!confirm || !confirm.checked) {
          if (field) field.classList.add('has-error');
          return false;
        }
        if (field) field.classList.remove('has-error');
        return true;
      }
      case 2:
        return [
          validateInput('beFullName', v => v.length >= 2),
          validateInput('beBusinessName', v => v.length >= 1),
          validateInput('beEmail', v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)),
          validateInput('beWhatsapp', v => v.length >= 7),
        ].every(Boolean);
      case 3:
        return [
          validateInput('beGarments', v => v.length >= 5),
          validateInput('beColours', v => v.length >= 1),
        ].every(Boolean);
      case 4: {
        const placements = form.querySelectorAll('input[name="placements[]"]:checked');
        const method = form.querySelector('input[name="branding_method"]:checked');
        const placementsField = document.getElementById('bePlacements');
        const methodField = document.getElementById('beBrandingMethod');
        const uploadField = uploadZone ? uploadZone.closest('.be-field') : null;

        let ok = true;

        if (placements.length === 0) {
          if (placementsField) placementsField.closest('.be-field').classList.add('has-error');
          ok = false;
        } else {
          if (placementsField) placementsField.closest('.be-field').classList.remove('has-error');
        }

        if (!method) {
          if (methodField) methodField.closest('.be-field').classList.add('has-error');
          ok = false;
        } else {
          if (methodField) methodField.closest('.be-field').classList.remove('has-error');
        }

        if (selectedFiles.length === 0) {
          if (uploadField) uploadField.classList.add('has-error');
          if (uploadZone) uploadZone.classList.add('invalid');
          ok = false;
        } else {
          clearUploadError();
        }

        return ok;
      }
      default:
        return true;
    }
  }

  /* Clear errors on input */
  form.querySelectorAll('.be-input, .be-textarea').forEach(el => {
    el.addEventListener('input', () => clearFieldError(el.closest('.be-field')));
  });

  document.getElementById('beMinOrderConfirm')?.addEventListener('change', function () {
    if (this.checked) this.closest('.be-field')?.classList.remove('has-error');
  });

  form.querySelectorAll('input[name="placements[]"], input[name="branding_method"]').forEach(el => {
    el.addEventListener('change', () => {
      el.closest('.be-field')?.classList.remove('has-error');
    });
  });

  /* ── Navigation ── */
  nextBtn.addEventListener('click', () => {
    if (!validateStep(currentStep)) return;
    if (currentStep < TOTAL_STEPS) goToStep(currentStep + 1);
  });

  backBtn.addEventListener('click', () => {
    if (currentStep > 1) goToStep(currentStep - 1);
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!validateStep(currentStep)) return;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="be-spinner"></span> Submitting…';

    setTimeout(() => {
      steps.forEach(s => s.style.display = 'none');
      if (nav) nav.style.display = 'none';
      if (successEl) successEl.style.display = 'block';
      progressItems.forEach(item => {
        item.classList.remove('active');
        item.classList.add('completed');
      });
    }, 1400);
  });

  initChoiceBoxes();
  updateProgress();

})();
