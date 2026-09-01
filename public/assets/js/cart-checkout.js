/* ==============================================
   cart-checkout.js — Cart, checkout & auth prompt
   ============================================== */
(function () {
  'use strict';

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

  function updateCartBadge(count) {
    const badge = document.getElementById('navShopCartBadge')
      || document.getElementById('navCartBadge')
      || document.querySelector('[data-cart-badge]');
    if (!badge) return;
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = '';
    } else {
      badge.style.display = 'none';
    }
  }

  function formatMoney(amount) {
    return '£' + Number(amount).toFixed(2);
  }

  function updateCartSummary(totals) {
    if (!totals) return;

    const subtotalEl = document.getElementById('cartSummarySubtotal');
    const vatEl = document.getElementById('cartSummaryVat');
    const totalEl = document.getElementById('cartSummaryTotal');

    if (subtotalEl && totals.subtotal != null) subtotalEl.textContent = formatMoney(totals.subtotal);
    if (vatEl && totals.vat != null) vatEl.textContent = formatMoney(totals.vat);
    if (totalEl && totals.total != null) totalEl.textContent = formatMoney(totals.total);
  }

  /* ── Remove cart item ── */
  function initRemoveButtons() {
    document.querySelectorAll('[data-remove-item]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const itemId = btn.dataset.removeItem;
        if (!confirm('Remove this item from your cart?')) return;

        btn.disabled = true;
        try {
          const shopRoutes = window.SHOP_ROUTES || {};
          const destroyUrl = shopRoutes.cart_destroy_base
            ? `${shopRoutes.cart_destroy_base}/${itemId}`
            : `/shop/cart/items/${itemId}`;
          const res = await fetch(destroyUrl, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json',
            },
          });
          const data = await res.json();
          if (res.ok) {
            updateCartBadge(data.cart_count);
            updateCartSummary(data);
            const item = btn.closest('.cc-item');
            if (item) {
              item.style.opacity = '0';
              setTimeout(() => {
                item.remove();
                if (!document.querySelector('.cc-item')) {
                  window.location.reload();
                }
              }, 300);
            }
          }
        } catch {
          btn.disabled = false;
        }
      });
    });
  }

  /* ── Billing same as shipping toggle ── */
  function initBillingToggle() {
    const checkbox = document.getElementById('billingSame');
    const billingFields = document.getElementById('billingFields');
    if (!checkbox || !billingFields) return;

    checkbox.addEventListener('change', () => {
      billingFields.style.display = checkbox.checked ? 'none' : '';
    });
  }

  /* ── Place order button loading state ── */
  function initCheckoutForm() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    form.addEventListener('submit', () => {
      const btn = document.getElementById('placeOrderBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Placing Order…';
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    initRemoveButtons();
    initBillingToggle();
    initCheckoutForm();
  });

  window.TECart = { updateCartBadge };
})();
