/* ==============================================
   CONTACT.JS — Form validation + submit
   ============================================== */
(function () {
  'use strict';

  const form      = document.getElementById('contactForm');
  const submitBtn = document.getElementById('ctSubmit');
  const successEl = document.getElementById('ctSuccess');
  const charCount = document.getElementById('ctCharCount');
  const textarea  = document.getElementById('ctMessage');

  if (!form) return;

  /* Character counter */
  if (textarea && charCount) {
    textarea.addEventListener('input', () => {
      const len = textarea.value.length;
      const max = 1000;
      if (len > max) textarea.value = textarea.value.slice(0, max);
      charCount.textContent = `${Math.min(len, max)} / ${max}`;
      charCount.style.color = len >= max * 0.9 ? '#ef4444' : '#bbb';
    });
  }

  /* Clear invalid state on focus */
  form.querySelectorAll('.ctb-input, .ctb-select, .ctb-textarea').forEach(el => {
    el.addEventListener('input', () => {
      el.classList.remove('invalid');
      el.closest('.ctb-field').classList.remove('has-error');
    });
  });

  /* Validate a single field */
  function validateField(id, validFn) {
    const el = document.getElementById(id);
    if (!el) return true;
    const field = el.closest('.ctb-field');
    const isValid = validFn(el.value.trim());
    if (!isValid) {
      el.classList.add('invalid');
      if (field) field.classList.add('has-error');
    } else {
      el.classList.remove('invalid');
      if (field) field.classList.remove('has-error');
    }
    return isValid;
  }

  /* Submit handler */
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const ok = [
      validateField('ctFirstName', v => v.length >= 2),
      validateField('ctLastName',  v => v.length >= 2),
      validateField('ctEmail',     v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)),
      validateField('ctSubject',   v => v !== ''),
      validateField('ctMessage',   v => v.length >= 10),
    ].every(Boolean);

    if (!ok) return;

    /* Simulate submission */
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="ctb-submit-inner"><span class="ctb-spinner"></span>Sending…</span>';

    setTimeout(() => {
      form.style.display = 'none';
      if (successEl) successEl.style.display = 'flex';
    }, 1200);
  });

})();
