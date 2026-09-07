/* Shared Stripe Card Element helper for on-page payments (no redirect). */
(function (window) {
  'use strict';

  const STYLE = {
    base: {
      color: '#1a1a1a',
      fontFamily: 'system-ui, -apple-system, Segoe UI, Roboto, sans-serif',
      fontSize: '16px',
      '::placeholder': { color: '#8a8a8a' },
    },
    invalid: { color: '#c0392b' },
  };

  async function loadStripeJs() {
    if (window.Stripe) return window.Stripe;
    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.src = 'https://js.stripe.com/v3/';
      s.async = true;
      s.onload = resolve;
      s.onerror = () => reject(new Error('Could not load Stripe.js'));
      document.head.appendChild(s);
    });
    return window.Stripe;
  }

  function ensureMount(selector) {
    let el = document.querySelector(selector);
    if (el) return el;
    return null;
  }

  /**
   * @param {{ mountSelector:string, errorSelector?:string }} opts
   */
  async function initCardElement(opts) {
    const mountEl = ensureMount(opts.mountSelector);
    if (!mountEl) throw new Error('Payment card mount not found.');

    const cfgRes = await fetch('/api/stripe/config', {
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    });
    const cfg = await cfgRes.json();
    if (!cfgRes.ok || !cfg.publishableKey) {
      throw new Error(cfg.message || cfg.errors?.payment?.[0] || 'Stripe is not configured.');
    }

    await loadStripeJs();
    const stripe = window.Stripe(cfg.publishableKey);
    const elements = stripe.elements({ appearance: { theme: 'stripe' } });
    const card = elements.create('card', { style: STYLE, hidePostalCode: false });
    card.mount(mountEl);

    const errorEl = opts.errorSelector ? document.querySelector(opts.errorSelector) : null;
    card.on('change', (event) => {
      if (!errorEl) return;
      errorEl.textContent = event.error ? event.error.message : '';
    });

    return { stripe, elements, card, currency: cfg.currency || 'gbp' };
  }

  function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  }

  /**
   * Create PaymentIntent then confirm card payment on-page.
   * @returns {Promise<{paymentIntentId:string}>}
   */
  async function pay(opts) {
    const { stripe, card, context, email, name, billing, amount, items, onStatus } = opts;
    const setStatus = typeof onStatus === 'function' ? onStatus : () => {};

    setStatus('Preparing secure payment…');
    const intentRes = await fetch('/api/stripe/payment-intent', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
      },
      body: JSON.stringify({
        context,
        email: email || null,
        amount: amount ?? null,
        items: items || null,
      }),
    });
    const intentData = await intentRes.json();
    if (!intentRes.ok) {
      const msg = intentData.message
        || intentData.errors?.payment?.[0]
        || intentData.errors?.cart?.[0]
        || 'Could not start payment.';
      throw new Error(msg);
    }

    setStatus('Confirming card payment…');
    const result = await stripe.confirmCardPayment(intentData.clientSecret, {
      payment_method: {
        card,
        billing_details: {
          name: name || undefined,
          email: email || undefined,
          address: billing || undefined,
        },
      },
    });

    if (result.error) {
      throw new Error(result.error.message || 'Payment failed.');
    }

    if (result.paymentIntent?.status !== 'succeeded') {
      throw new Error('Payment was not completed.');
    }

    return {
      paymentIntentId: result.paymentIntent.id,
      amount: intentData.amount,
    };
  }

  window.TEStripeCheckout = { initCardElement, pay, csrfToken };
})(window);
