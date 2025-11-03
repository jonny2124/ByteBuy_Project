(() => {
  const TAX_RATE = 0.08;
  const STUDENT_BUSINESS_DISCOUNT = 0.05;
  const VALID_PROMOS = {
    'BYTE10': 0.10,
    'STUDENT5': 0.05
  };

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  const list = $('#cartList');
  const subtotalEl = $('#summarySubtotal');
  const discountEl = $('#summaryDiscount');
  const discountRow = $('#discountRow');
  const taxEl = $('#summaryTax');
  const totalEl = $('#summaryTotal');
  const discountToggle = $('#discountToggle');
  const promoInput = $('#promoCode');
  const promoBtn = $('#applyPromo');

  let appliedPromoRate = 0; // 0..1

  function currency(n){
    return `$${n.toFixed(2)}`;
  }

  function itemTotal(article){
    const unit = parseFloat(article.dataset.price || '0');
    const qty = Math.max(1, parseInt($('.qty-input', article).value || '1', 10));
    const warranty = $('.warranty', article);
    const warrantyPrice = warranty && warranty.checked ? parseFloat(warranty.dataset.warranty || '0') : 0;
    return (unit + warrantyPrice) * qty;
  }

  function recalc(){
    const items = $$('.cart-item', list);
    const subtotal = items.reduce((sum, a) => sum + itemTotal(a), 0);

    // combined discount: toggle + promo
    const toggleRate = discountToggle?.checked ? STUDENT_BUSINESS_DISCOUNT : 0;
    const combinedRate = Math.min(1, toggleRate + appliedPromoRate);
    const discount = subtotal * combinedRate;
    const taxable = Math.max(0, subtotal - discount);
    const tax = taxable * TAX_RATE;
    const total = taxable + tax;

    subtotalEl.textContent = currency(subtotal);
    if (discount > 0) {
      discountRow.classList.remove('hidden');
      discountEl.textContent = `−${currency(discount)}`;
    } else {
      discountRow.classList.add('hidden');
      discountEl.textContent = '−$0.00';
    }
    taxEl.textContent = currency(tax);
    totalEl.textContent = currency(total);

    // Update per-item displayed totals
    items.forEach(a => {
      const t = itemTotal(a);
      $('.item-total', a).textContent = currency(t);
    });

    // Persist lightweight cart snapshot for checkout
    try {
      const payload = {
        items: items.map(a => ({
          name: $('.item-name', a)?.textContent?.trim() || '',
          category: $('.item-category', a)?.textContent?.trim() || '',
          image: $('img', a)?.getAttribute('src') || '',
          unitPrice: parseFloat($('.unit-price', a)?.textContent || a.dataset.price || '0'),
          qty: Math.max(1, parseInt($('.qty-input', a)?.value || '1', 10)),
          warranty: !!$('.warranty', a)?.checked,
          warrantyPrice: parseFloat($('.warranty', a)?.dataset?.warranty || '0'),
          total: itemTotal(a)
        })),
        subtotal,
        discount,
        tax,
        total,
        ts: Date.now()
      };
      localStorage.setItem('bytebuy_cart', JSON.stringify(payload));
    } catch(e) { /* no-op */ }

    // Empty state
    if (items.length === 0) {
      list.innerHTML = '<div class="empty">Your cart is empty. <a href="shop.php">Continue shopping</a>.</div>';
    }
  }

  function bindItem(article){
    const minus = $('.minus', article);
    const plus = $('.plus', article);
    const input = $('.qty-input', article);
    const remove = $('.remove', article);
    const warranty = $('.warranty', article);

    minus?.addEventListener('click', () => {
      const v = Math.max(1, (parseInt(input.value || '1', 10) - 1));
      input.value = v;
      recalc();
    });
    plus?.addEventListener('click', () => {
      const v = Math.max(1, (parseInt(input.value || '1', 10) + 1));
      input.value = v;
      recalc();
    });
    input?.addEventListener('input', () => {
      const n = parseInt(input.value, 10);
      if (isNaN(n) || n < 1) input.value = 1;
      recalc();
    });
    remove?.addEventListener('click', () => {
      article.remove();
      recalc();
    });
    warranty?.addEventListener('change', recalc);
  }

  // Bind all items
  $$('.cart-item', list).forEach(bindItem);

  // Discount toggle
  discountToggle?.addEventListener('change', recalc);

  // Promo handling
  promoBtn?.addEventListener('click', () => {
    const code = (promoInput.value || '').trim().toUpperCase();
    appliedPromoRate = VALID_PROMOS[code] || 0;
    recalc();
  });

  // Initial calc
  recalc();
})();
