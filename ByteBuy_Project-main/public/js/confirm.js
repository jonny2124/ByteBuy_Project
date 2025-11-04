(() => {
  const TAX_RATE = 0.08;
  const SHIPPING_DEFAULT = 0; // fallback if unknown

  const $ = (s, c = document) => c.querySelector(s);

  function currency(n){ return `$${n.toFixed(2)}`; }

  function loadCart(){
    try {
      const raw = localStorage.getItem('bytebuy_cart');
      if (!raw) return null;
      return JSON.parse(raw);
    } catch(e){ return null; }
  }

  function formatDate(d){
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
  }

  function estimateRange(daysStart, daysEnd){
    const now = new Date();
    const a = new Date(now); a.setDate(a.getDate() + daysStart);
    const b = new Date(now); b.setDate(b.getDate() + daysEnd);
    return `${formatDate(a)} – ${formatDate(b)}`;
  }

  function genOrderId(){
    const part = Math.floor(100000 + Math.random() * 900000);
    return `BB-${part}`;
  }

  function renderItems(data){
    const wrap = $('#orderItems');
    wrap.innerHTML = '';
    if (!data || !data.items || data.items.length === 0){
      wrap.innerHTML = '<div class="empty">No items found. <a href="shop.php">Continue shopping</a>.</div>';
      return;
    }
    data.items.forEach(item => {
      const row = document.createElement('div');
      row.className = 'mini';
      row.innerHTML = `
        <img src="${item.image}" alt="${item.name}">
        <div class="meta">
          <div><strong>${item.name}</strong></div>
          <div>${item.category} • Qty ${item.qty}${item.warranty ? ' • +Warranty' : ''}</div>
        </div>
        <div class="price">${currency(item.total)}</div>
      `;
      wrap.appendChild(row);
    });
  }

  function updateSummary(data){
    const subtotal = data?.subtotal || 0;
    const discount = data?.discount || 0;
    const tax = Math.max(0, (subtotal - discount)) * TAX_RATE;
    const shipping = SHIPPING_DEFAULT; // fallback
    const total = Math.max(0, subtotal - discount) + tax + shipping;

    $('#confSubtotal').textContent = currency(subtotal);
    if (discount > 0){
      $('#confDiscountRow').classList.remove('hidden');
      $('#confDiscount').textContent = `−${currency(discount)}`;
    }
    $('#confTax').textContent = currency(tax);
    $('#confShipping').textContent = currency(shipping);
    $('#confTotal').textContent = currency(total);
  }

  // Init
  // Init — prefer server-provided order data when available
  const serverOrder = window.__ORDER_DATA || null;
  if (serverOrder) {
    const orderDate = serverOrder.created_at ? new Date(serverOrder.created_at) : new Date();
    $('#orderId').textContent = serverOrder.code || serverOrder.id || 'BB-000000';
    $('#orderDate').textContent = formatDate(orderDate);
  $('#deliveryEstimate').textContent = estimateRange(3, 5);
    renderItems(serverOrder);
    updateSummary({ subtotal: serverOrder.subtotal || 0 });
    try { localStorage.setItem('bytebuy_last_order', JSON.stringify({ id: serverOrder.code || serverOrder.id, date: orderDate.toISOString() })); } catch (e) {}
  } else {
    const cart = loadCart();
    const orderId = genOrderId();
    const today = new Date();

    $('#orderId').textContent = orderId;
    $('#orderDate').textContent = formatDate(today);
    $('#deliveryEstimate').textContent = estimateRange(3, 5);

    renderItems(cart);
    updateSummary(cart);

    // Optionally record last order snapshot
    try { localStorage.setItem('bytebuy_last_order', JSON.stringify({ id: orderId, date: today.toISOString() })); } catch(e){}
  }
})();

