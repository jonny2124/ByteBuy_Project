(() => {
  const TAX_RATE = 0.08;
  const SHIPPING_FEES = { standard: 0, express: 15 };

  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  function currency(n){ return `$${n.toFixed(2)}`; }

  async function loadCart(){
    // If a server cart token is present, prefer the server cart
    const token = localStorage.getItem('cart_token');
    if (token) {
      try {
        const form = new URLSearchParams();
        form.append('action','view');
        form.append('cart_token', token);
        const resp = await fetch('cart.php', { method: 'POST', body: form });
        if (resp.ok){
          const json = await resp.json();
          if (json.success) return json.cart;
        }
      } catch (e) {
        console.warn('Failed to load server cart, falling back to local cart', e);
      }
    }

    try {
      const raw = localStorage.getItem('bytebuy_cart');
      if (!raw) return null;
      return JSON.parse(raw);
    } catch(e){ return null; }
  }

  function renderItems(data){
    const wrap = $('#summaryItems');
    wrap.innerHTML = '';
    if (!data || !data.items || data.items.length === 0){
      wrap.innerHTML = '<div class="empty">Your cart is empty. <a href="cart.php">Go to cart</a>.</div>';
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

  function calcTotals(data, shipping){
    const subtotal = data?.subtotal || 0;
    const discount = data?.discount || 0;
    const taxable = Math.max(0, subtotal - discount);
    const tax = taxable * TAX_RATE;
    const shippingFee = SHIPPING_FEES[shipping] || 0;
    const total = taxable + tax + shippingFee;
    return { subtotal, discount, tax, shippingFee, total };
  }

  function updateSummary(t){
    $('#sumSubtotal').textContent = currency(t.subtotal);
    if (t.discount > 0){
      $('#sumDiscountRow').classList.remove('hidden');
      $('#sumDiscount').textContent = `−${currency(t.discount)}`;
    } else {
      $('#sumDiscountRow').classList.add('hidden');
    }
    $('#sumTax').textContent = currency(t.tax);
    $('#sumShipping').textContent = currency(t.shippingFee);
    $('#sumTotal').textContent = currency(t.total);
  }

  // Initialize
  let shipping = 'standard';
  let cartData = null;
  (async ()=>{
    cartData = await loadCart();
    renderItems(cartData);
    shipping = ($('input[name="shipping"]:checked')?.value) || 'standard';
    updateSummary(calcTotals(cartData, shipping));

    // listen for cart updates from other pages
    window.addEventListener('cart.updated', async () => {
      cartData = await loadCart();
      renderItems(cartData);
      updateSummary(calcTotals(cartData, shipping));
    });
  })();

  // Shipping change
  $$('input[name="shipping"]').forEach(r => r.addEventListener('change', () => {
    shipping = r.value;
    updateSummary(calcTotals(cartData, shipping));
  }));

  // Payment toggle
  const payCard = $('#payCard');
  const payNow = $('#payNow');
  $$('input[name="payment"]').forEach(r => r.addEventListener('change', () => {
    const v = $('input[name="payment"]:checked').value;
    if (v === 'card'){ payCard.classList.remove('hidden'); payNow.classList.add('hidden'); }
    else { payNow.classList.remove('hidden'); payCard.classList.add('hidden'); }
  }));

  // Place order: submit to server processor with cart_token (no PHP sessions)
  $('#placeOrder')?.addEventListener('click', () => {
    const requiredIds = ['firstName','lastName','email','phone','address1','city','state','postal','country'];
    const missing = requiredIds.filter(id => !($('#'+id)?.value?.trim()));
    if (missing.length){
      alert('Please fill out all required billing fields.');
      return;
    }

    // build form and submit so server can redirect to confirmation
    const token = localStorage.getItem('cart_token') || '';
    if (!token){ alert('Cart token missing. Please add items to cart.'); return; }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'checkout_process.php';

    function add(name, value){ const i = document.createElement('input'); i.type='hidden'; i.name = name; i.value = value; form.appendChild(i); }
    add('cart_token', token);
    add('guest_email', $('#email')?.value || '');
    add('shipping_address', `${$('#address1')?.value || ''} ${$('#address2')?.value || ''}`);
    add('billing_address', `${$('#address1')?.value || ''} ${$('#address2')?.value || ''}`);
    add('payment_method', $('input[name="payment"]:checked')?.value || 'card');

    document.body.appendChild(form);
    form.submit();
  });
})();
