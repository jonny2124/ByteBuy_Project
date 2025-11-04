(() => {
  const TAX_RATE = 0.08;
  const SHIPPING_DEFAULT = 0; // static fallback for now

  const $ = (selector, ctx = document) => ctx.querySelector(selector);

  function currency(n) {
    return `$${Number(n || 0).toFixed(2)}`;
  }

  function loadCartSnapshot() {
    try {
      const raw = localStorage.getItem('bytebuy_cart');
      if (!raw) return null;
      return JSON.parse(raw);
    } catch (err) {
      return null;
    }
  }

  function formatDate(date) {
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
  }

  function estimateRange(daysStart, daysEnd) {
    const now = new Date();
    const start = new Date(now);
    const end = new Date(now);
    start.setDate(start.getDate() + daysStart);
    end.setDate(end.getDate() + daysEnd);
    return `${formatDate(start)} - ${formatDate(end)}`;
  }

  function genOrderId() {
    const part = Math.floor(100000 + Math.random() * 900000);
    return `BB-${part}`;
  }

  function renderItems(data) {
    const wrap = $('#orderItems');
    wrap.innerHTML = '';
    const items = data?.items || [];
    if (!items.length) {
      wrap.innerHTML = '<div class="empty">No items found. <a href="shop.php">Continue shopping</a>.</div>';
      return;
    }

    items.forEach(item => {
      const row = document.createElement('div');
      row.className = 'mini';
      const image = item.image || 'assets/home/placeholder.png';
      const qty = item.qty ?? item.quantity ?? 1;
      row.innerHTML = `
        <img src="${image}" alt="${item.name}">
        <div class="meta">
          <div><strong>${item.name}</strong></div>
          <div>${item.category || ''} - Qty ${qty}${item.warranty ? ' - +Warranty' : ''}</div>
        </div>
        <div class="price">${currency(item.total ?? (qty * (item.price || 0)))}</div>
      `;
      wrap.appendChild(row);
    });
  }

  function updateSummary(data) {
    const subtotal = Number(data?.subtotal || 0);
    const discount = Number(data?.discount || 0);
    const providedTotal = (data && typeof data.total === 'number') ? Number(data.total) : null;
    const shipping = SHIPPING_DEFAULT;

    const taxable = Math.max(0, subtotal - discount);
    let tax = taxable * TAX_RATE;
    let total = providedTotal ?? (taxable + tax + shipping);

    if (providedTotal !== null) {
      total = providedTotal;
      tax = Math.max(0, total - shipping - taxable);
    }

    $('#confSubtotal').textContent = currency(subtotal);
    if (discount > 0) {
      $('#confDiscountRow').classList.remove('hidden');
      $('#confDiscount').textContent = `-${currency(discount)}`;
    } else {
      $('#confDiscountRow').classList.add('hidden');
    }
    $('#confTax').textContent = currency(tax);
    $('#confShipping').textContent = currency(shipping);
    $('#confTotal').textContent = currency(total);
  }

  const serverOrder = window.__ORDER_DATA || null;
  if (serverOrder) {
    const orderDate = serverOrder.created_at ? new Date(serverOrder.created_at) : new Date();
    $('#orderId').textContent = serverOrder.code || serverOrder.id || 'BB-000000';
    $('#orderDate').textContent = formatDate(orderDate);
    $('#deliveryEstimate').textContent = estimateRange(3, 5);

    renderItems(serverOrder);
    updateSummary({
      subtotal: Number(serverOrder.subtotal || 0),
      discount: Number(serverOrder.discount || 0),
      total: serverOrder.total !== undefined ? Number(serverOrder.total) : null
    });

    try {
      localStorage.setItem('bytebuy_last_order', JSON.stringify({ id: serverOrder.code || serverOrder.id, date: orderDate.toISOString() }));
    } catch (err) {
      // ignore storage errors
    }
  } else {
    const cartSnapshot = loadCartSnapshot() || { items: [], subtotal: 0, discount: 0 };
    const orderId = genOrderId();
    const today = new Date();

    $('#orderId').textContent = orderId;
    $('#orderDate').textContent = formatDate(today);
    $('#deliveryEstimate').textContent = estimateRange(3, 5);

    renderItems(cartSnapshot);
    updateSummary(cartSnapshot);

    try {
      localStorage.setItem('bytebuy_last_order', JSON.stringify({ id: orderId, date: today.toISOString() }));
    } catch (err) {
      // ignore storage errors
    }
  }
})();
