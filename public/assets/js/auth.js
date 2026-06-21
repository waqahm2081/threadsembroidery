/* ==============================================
   AUTH.JS — Shared auth page logic
   login.php | signup.php | forgot-password.php
   ============================================== */
(function () {
  'use strict';

  /* ── Password toggle ── */
  document.querySelectorAll('.auth-eye-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      const icon  = btn.querySelector('.auth-eye-icon');
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
      }
    });
  });

  /* ── Clear errors on input ── */
  document.querySelectorAll('.auth-input').forEach(input => {
    input.addEventListener('input', () => {
      input.classList.remove('invalid');
      const field = input.closest('.auth-field');
      if (field) field.classList.remove('has-error');
    });
  });

  /* ── Password strength meter (signup) ── */
  const pwInput   = document.getElementById('signupPassword');
  const fill      = document.getElementById('strengthFill');
  const label     = document.getElementById('strengthLabel');

  if (pwInput && fill && label) {
    pwInput.addEventListener('input', () => {
      const pw = pwInput.value;
      let score = 0;
      if (pw.length >= 8)          score++;
      if (/[A-Z]/.test(pw))        score++;
      if (/[0-9]/.test(pw))        score++;
      if (/[^A-Za-z0-9]/.test(pw)) score++;

      const widths = ['0%', '25%', '50%', '75%', '100%'];
      const colours = ['transparent', '#ef4444', '#f59e0b', '#22c55e', '#22c55e'];
      const labels  = ['', 'Weak', 'Fair', 'Good', 'Strong'];

      fill.style.width      = widths[score];
      fill.style.background = colours[score];
      label.textContent     = labels[score];
      label.style.color = score < 2 ? '#ef4444' : score < 3 ? '#f59e0b' : '#22c55e';
    });
  }

  /* ── Validate helper ── */
  function validateField(id, validFn, customError) {
    const el = document.getElementById(id);
    if (!el) return true;
    const field = el.closest('.auth-field');
    const valid = validFn(el.value.trim());
    if (!valid) {
      el.classList.add('invalid');
      if (field) field.classList.add('has-error');
      if (customError) {
        const errEl = field ? field.querySelector('.auth-error') : null;
        if (errEl && customError) errEl.textContent = customError;
      }
    }
    return valid;
  }

  /* ── LOGIN form ── */
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      const ok = [
        validateField('loginEmail',    v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)),
        validateField('loginPassword', v => v.length >= 1),
      ].every(Boolean);
      if (!ok) return;

      const btn = document.getElementById('loginSubmit');
      btn.disabled = true;
      btn.innerHTML = '<span class="auth-submit-inner"><span class="auth-spinner"></span>Signing in…</span>';

      /* Redirect to dashboard after brief delay (no real auth) */
      setTimeout(() => {
        window.location.href = '/user-dashboard';
      }, 1000);
    });
  }

  /* ── SIGNUP form ── */
  const signupForm = document.getElementById('signupForm');
  if (signupForm) {
    signupForm.addEventListener('submit', e => {
      e.preventDefault();

      const pw  = (document.getElementById('signupPassword')  || {}).value || '';
      const pw2 = (document.getElementById('signupConfirm') || {}).value || '';
      const terms = document.getElementById('signupTerms');
      const termsError = document.getElementById('termsError');

      let ok = [
        validateField('signupFirstName', v => v.length >= 2),
        validateField('signupLastName',  v => v.length >= 2),
        validateField('signupEmail',     v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)),
        validateField('signupPassword',  v => v.length >= 8),
        validateField('signupConfirm',   v => v === pw, 'Passwords do not match.'),
      ].every(Boolean);

      if (terms && !terms.checked) {
        ok = false;
        if (termsError) { termsError.style.display = 'block'; termsError.classList.add('visible'); }
      } else if (termsError) {
        termsError.style.display = 'none';
      }

      if (!ok) return;

      const btn = document.getElementById('signupSubmit');
      btn.disabled = true;
      btn.innerHTML = '<span class="auth-submit-inner"><span class="auth-spinner"></span>Creating account…</span>';

      setTimeout(() => {
        window.location.href = '/user-dashboard';
      }, 1200);
    });
  }

  /* ── FORGOT PASSWORD form ── */
  const forgotForm = document.getElementById('forgotForm');
  if (forgotForm) {
    forgotForm.addEventListener('submit', e => {
      e.preventDefault();
      const ok = validateField('forgotEmail', v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v));
      if (!ok) return;

      const btn = document.getElementById('forgotSubmit');
      btn.disabled = true;
      btn.innerHTML = '<span class="auth-submit-inner"><span class="auth-spinner"></span>Sending…</span>';

      const emailVal = (document.getElementById('forgotEmail') || {}).value || '';

      setTimeout(() => {
        forgotForm.style.display = 'none';
        const switchEl = document.getElementById('fpSwitch');
        if (switchEl) switchEl.style.display = 'none';

        const success = document.getElementById('resetSuccess');
        const display = document.getElementById('resetEmailDisplay');
        if (success) success.style.display = 'block';
        if (display) display.textContent = emailVal;
      }, 1000);
    });
  }

})();
