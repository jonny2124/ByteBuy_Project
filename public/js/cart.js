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
  let appliedPromoCode = '';

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
    const couponDiscount = subtotal * appliedPromoRate;
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
          coupon_code: appliedPromoCode,
        coupon_discount: couponDiscount,
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

    minus?.addEventListener('click', async () => {
      const old = parseInt(article.dataset.qty || (input.value || '1'), 10);
      const v = Math.max(1, (parseInt(input.value || '1', 10) - 1));
      input.value = v;
      recalc();
      await syncQtyWithServer(article, old, v);
    });
    plus?.addEventListener('click', async () => {
      const old = parseInt(article.dataset.qty || (input.value || '1'), 10);
      const v = Math.max(1, (parseInt(input.value || '1', 10) + 1));
      input.value = v;
      recalc();
      await syncQtyWithServer(article, old, v);
    });
    input?.addEventListener('input', async () => {
      const old = parseInt(article.dataset.qty || '1', 10);
      const n = parseInt(input.value, 10);
      if (isNaN(n) || n < 1) input.value = 1;
      recalc();
      const newV = Math.max(1, parseInt(input.value, 10));
      
      const result = await syncQtyWithServer(article, old, newV);
      
      if (!result.success && result.message) {
        showNotification(result.message, true);
        // Revert to old value if update failed
        input.value = old;
        recalc();
      }
    });
    remove?.addEventListener('click', async () => {
      // call server to remove and return stock
      const token = localStorage.getItem('cart_token');
      const sku = article.dataset.sku;
      if (token && sku) {
        try {
          const form = new URLSearchParams();
          form.append('action','remove');
          form.append('cart_token', token);
          form.append('sku', sku);
            const resp = await fetch('cart.php', { method: 'POST', body: form });
            const j = await resp.json();
            if (!j.success) {
              showNotification(j.message || 'Unable to remove item', true);
              return;
            }
          } catch (err) {
            showNotification('Unable to remove item: ' + (err.message || err), true);
            return;
          }
      }
      article.remove();
      recalc();
    });
    warranty?.addEventListener('change', recalc);
  }

  // Show notification function
  function showNotification(message, isError = false) {
    let overlay = document.getElementById('notificationOverlay');
    let notification = document.getElementById('notification');

    // If overlay/notification missing, create them and append to body
    if (!overlay || !notification) {
      overlay = document.createElement('div');
      overlay.id = 'notificationOverlay';
      overlay.className = 'notification-overlay';
      notification = document.createElement('div');
      notification.id = 'notification';
      notification.className = 'notification';
      notification.setAttribute('role', 'alert');
      notification.setAttribute('aria-live', 'polite');
      overlay.appendChild(notification);
      document.body.appendChild(overlay);
    }

    notification.textContent = message;
    notification.style.backgroundColor = isError ? '#dc2626' : '#444444';
    overlay.style.backgroundColor = isError ? 'rgba(0, 0, 0, 0.6)' : 'rgba(0, 0, 0, 0.5)';

    // allow click to dismiss immediately
    overlay.onclick = () => {
      notification.classList.add('hide');
      overlay.classList.add('hide');
      setTimeout(() => {
        overlay.classList.remove('show', 'hide');
        notification.classList.remove('show', 'hide');
      }, 300);
    };

    overlay.classList.add('show');
    notification.classList.add('show');

    // Display duration: longer for errors
    const duration = isError ? 2000 : 1500;
    setTimeout(() => {
      notification.classList.add('hide');
      overlay.classList.add('hide');
      setTimeout(() => {
        overlay.classList.remove('show', 'hide');
        notification.classList.remove('show', 'hide');
      }, 300);
    }, duration);
  }

  // Load server cart if a cart token exists, otherwise bind existing DOM items
  async function loadServerCartAndRender(){
    const token = localStorage.getItem('cart_token');
    if (!token) {
      $$('.cart-item', list).forEach(bindItem);
      recalc();
      return;
    }
    // request server cart
    try {
      const form = new URLSearchParams();
      form.append('action','view');
      form.append('cart_token', token);
      const resp = await fetch('cart.php', { method: 'POST', body: form });
      const json = await resp.json();
      if (json.success && json.cart && Array.isArray(json.cart.items)){
        // build DOM
        list.innerHTML = '';
        json.cart.items.forEach(it => {
          const article = document.createElement('article');
          article.className = 'cart-item';
          article.dataset.price = it.price.toFixed(2);
          article.dataset.sku = it.sku;
          article.dataset.qty = it.qty;
          article.innerHTML = `
            <div class="item-media"><img src="${it.image || 'assets/home/placeholder.png'}" alt="${it.name}"></div>
            <div class="item-info">
              <div class="item-head">
                <h3 class="item-name">${it.name}</h3>
                <span class="item-category">${it.category || ''}</span>
              </div>
              <p class="item-price">$<span class="unit-price">${it.price.toFixed(2)}</span></p>
              <label class="addon">
                <input type="checkbox" class="warranty" data-warranty="0" />
                <span>Add 2‑Year Protection (+$0)</span>
              </label>
              <div class="item-actions">
                <div class="qty">
                  <button class="btn-qty minus" aria-label="Decrease quantity">−</button>
                  <input type="number" class="qty-input" value="${it.qty}" min="1"/>
                  <button class="btn-qty plus" aria-label="Increase quantity">+</button>
                </div>
                <div class="item-total-wrap">
                  <span class="item-total-label">Total</span>
                  <span class="item-total">$${(it.qty * it.price).toFixed(2)}</span>
                </div>
              </div>
              <div class="item-links">
                <button class="link save-later" type="button">Save for later</button>
                <button class="link remove" type="button">Remove</button>
              </div>
            </div>
          `;
          list.appendChild(article);
        });
        $$('.cart-item', list).forEach(bindItem);
        recalc();
        return;
      }
    } catch (e) {
      console.warn('Could not load server cart', e);
    }
    // fallback: bind what is already in DOM
    $$('.cart-item', list).forEach(bindItem);
    recalc();
  }

  // Load server cart if a cart token exists, otherwise bind existing DOM items
  // Discount toggle
  discountToggle?.addEventListener('change', recalc);


  // sync qty changes to server (update cart_items and reserve/release stock)
  async function syncQtyWithServer(article, oldQty, newQty){
    const token = localStorage.getItem('cart_token');
    const sku = article.dataset.sku;
    if (!token || !sku) {
      article.dataset.qty = newQty;
      return { success: true };
    }
    try {
      const form = new URLSearchParams();
      form.append('action','update');
      form.append('cart_token', token);
      form.append('sku', sku);
      form.append('qty', String(newQty));
      const resp = await fetch('cart.php', { method: 'POST', body: form });
      const j = await resp.json();
      if (!j.success) {
        // Use overlay notification instead of alert; include item name for clarity
        const itemName = $('.item-name', article)?.textContent?.trim() || '';
        const msg = j.message ? (itemName ? `"${itemName}" ${j.message}` : j.message) : 'Failed to update quantity';
        showNotification(msg, true);
        const input = $('.qty-input', article);
        input.value = oldQty;
        recalc();
        return j;
      }
      article.dataset.qty = newQty;
      return j;
    } catch (err) {
      console.error('Failed to sync qty', err);
      showNotification('Unable to update quantity on server. Try again later.', true);
      const input = $('.qty-input', article);
      input.value = oldQty;
      recalc();
      return { success: false, message: err.message || String(err) };
    }
  }

  loadServerCartAndRender();

  // Discount toggle
  discountToggle?.addEventListener('change', recalc);

  // Promo handling
  promoBtn?.addEventListener('click', () => {
    const code = (promoInput.value || '').trim().toUpperCase();
    if (!code) {
      appliedPromoRate = 0;
      appliedPromoCode = '';
      recalc();
      return;
    }

    const rate = VALID_PROMOS[code];
    if (rate) {
      appliedPromoRate = rate;
      appliedPromoCode = code;
      recalc();
    } else {
      appliedPromoRate = 0;
      appliedPromoCode = '';
      recalc();
      alert('Invalid promo code');
    }
  });
  // Initial calc
  recalc();
})();
