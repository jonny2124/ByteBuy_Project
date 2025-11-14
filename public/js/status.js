(() => {
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  const actionEmailInput = $('#actionEmail');
  const cancelBtn = $('#cancelOrderBtn');
  const cancelHint = $('#cancelHint');
  const addressForm = $('#addressForm');
  const addressField = $('#newAddress');
  const addressSubmit = addressForm ? addressForm.querySelector('button[type="submit"]') : null;
  const paymentForm = $('#paymentForm');
  const paymentSelect = $('#paymentMethod');
  const paymentSubmit = paymentForm ? paymentForm.querySelector('button[type="submit"]') : null;
  const currentAddressEl = $('#currentAddress');
  const currentPaymentEl = $('#currentPayment');
  const feedbackEl = $('#actionFeedback');
  const addressHint = $('#addressHint');
  const paymentHint = $('#paymentHint');

  let currentOrder = null;

  const PAYMENT_LABELS = {
    card: 'Credit / Debit Card',
    paynow: 'PayNow',
    cod: 'Cash on Delivery',
    pending_verification: 'Pending Verification'
  };

  function currency(n){ return `$${Number(n || 0).toFixed(2)}`; }
  function formatDate(d){ return d.toLocaleDateString(undefined, { year:'numeric', month:'short', day:'numeric' }); }
  function humanStatus(status){
    if (!status) return '-';
    return status.replace(/_/g, ' ').split(' ').map(word => word ? word[0].toUpperCase() + word.slice(1) : '').join(' ');
  }

  function humanPayment(method){
    if (!method) return 'Not specified';
    return PAYMENT_LABELS[method] || humanStatus(method);
  }

  function setMeta(id, placed, eta, statusText){
    $('#metaOrder').textContent = id || '-';
    $('#metaDate').textContent = placed ? formatDate(new Date(placed)) : '-';
    $('#metaEta').textContent = eta || '-';
    $('#metaStatus').textContent = humanStatus(statusText);
  }

  function setSteps(state){
    const steps = $$('#tracker .step');
    steps.forEach((li, idx) => {
      li.classList.remove('completed','active');
      if (idx < state) li.classList.add('completed');
      if (idx === state) li.classList.add('active');
    });
  }

  function setWhen(placed){
    if (!placed){
      $('#whenOrdered').textContent = '-';
      $('#whenPacked').textContent = '-';
      $('#whenShipped').textContent = '-';
      $('#whenDelivered').textContent = '-';
      return;
    }
    const base = placed instanceof Date ? new Date(placed) : new Date(placed);
    const d0 = new Date(base);
    const d1 = new Date(base); d1.setDate(d1.getDate()+1);
    const d2 = new Date(base); d2.setDate(d2.getDate()+2);
    const d3 = new Date(base); d3.setDate(d3.getDate()+4);
    $('#whenOrdered').textContent = formatDate(d0);
    $('#whenPacked').textContent = formatDate(d1);
    $('#whenShipped').textContent = formatDate(d2);
    $('#whenDelivered').textContent = formatDate(d3);
  }

  function estimateRange(placed){
    const base = placed instanceof Date ? placed : new Date(placed || Date.now());
    const a = new Date(base); a.setDate(a.getDate()+3);
    const b = new Date(base); b.setDate(b.getDate()+5);
    return `${formatDate(a)} - ${formatDate(b)}`;
  }

  function renderItems(cart){
    const wrap = $('#statusItems');
    wrap.innerHTML = '';
    const items = cart?.items || [];
    if (!items.length){
      wrap.innerHTML = '<div class="empty">No items to display. <a href="shop.php">Continue shopping</a>.</div>';
      return;
    }
    items.forEach(item => {
      const category = item.category ? `${item.category} &middot; ` : '';
      const row = document.createElement('div');
      row.className = 'mini';
      row.innerHTML = `
        <img src="${item.image}" alt="${item.name}">
        <div class="meta">
          <div><strong>${item.name}</strong></div>
          <div>${category}Qty ${item.qty}${item.warranty ? ' &middot; +Warranty' : ''}</div>
        </div>
        <div class="price">${currency(item.total)}</div>
      `;
      wrap.appendChild(row);
    });
  }

  function computeState(placed){
    const start = placed instanceof Date ? placed : new Date(placed || Date.now());
    const days = (Date.now() - start.getTime()) / (1000*60*60*24);
    if (days < 1) return 0;
    if (days < 2) return 1;
    if (days < 4) return 2;
    return 3;
  }

  function setActionFeedback(type = '', message = ''){
    if (!feedbackEl) return;
    feedbackEl.textContent = message || '';
    feedbackEl.className = message ? `action-feedback ${type}` : 'action-feedback';
  }

  function resetPostSales(){
    currentOrder = null;
    if (cancelBtn) {
      cancelBtn.disabled = true;
      cancelBtn.textContent = 'Request Cancellation';
    }
    if (cancelHint) {
      cancelHint.textContent = 'Lookup your order to enable this action.';
    }
    if (currentAddressEl) currentAddressEl.textContent = 'No address on file.';
    if (currentPaymentEl) currentPaymentEl.textContent = '-';
    if (addressField) {
      addressField.value = '';
      addressField.disabled = true;
    }
    if (addressSubmit) addressSubmit.disabled = true;
    if (paymentSelect) paymentSelect.disabled = true;
    if (paymentSubmit) paymentSubmit.disabled = true;
    if (addressHint) addressHint.textContent = 'Lookup your order to edit the delivery address.';
    if (paymentHint) paymentHint.textContent = 'Lookup your order to switch payment method.';
    setActionFeedback();
  }

  function evaluateActions(order){
    if (!order) {
      resetPostSales();
      return;
    }
    const status = (order.status || '').toLowerCase();
    const created = order.created_at ? new Date(order.created_at) : null;
    const hoursOld = created ? (Date.now() - created.getTime()) / 36e5 : Number.POSITIVE_INFINITY;
    const daysOld = hoursOld / 24;
    const lockedStatuses = ['shipped','delivered','cancelled','canceled','refunded'];
    const locked = lockedStatuses.includes(status);

    if (cancelBtn) {
      const allowed = !locked && hoursOld <= 24;
      cancelBtn.disabled = !allowed;
      if (cancelHint) {
        cancelHint.textContent = allowed
          ? 'Eligible for instant cancellation.'
          : 'Disabled after 24 hours or once the order ships.';
      }
    }

    const addressAllowed = !locked && daysOld <= 3;
    if (addressSubmit) {
      addressSubmit.disabled = !addressAllowed;
    }
    if (addressHint) {
      addressHint.textContent = addressAllowed
        ? 'Address change available for this order.'
        : 'Address edits are available within 3 days and before shipment.';
    }

    const paymentAllowed = !locked;
    if (paymentSelect) {
      paymentSelect.disabled = !paymentAllowed;
    }
    if (paymentSubmit) {
      paymentSubmit.disabled = !paymentAllowed;
    }
    if (paymentHint) {
      paymentHint.textContent = paymentAllowed
        ? 'Switch payment method before the order ships.'
        : 'Payment method can no longer be updated once shipped.';
    }
  }

  function updatePostSalesUI(order){
    if (!currentAddressEl || !currentPaymentEl) return;
    currentOrder = order;
    const addressText = (order.shipping_address || '').trim();
    currentAddressEl.textContent = addressText || 'No address on file.';
    currentPaymentEl.textContent = humanPayment(order.payment_method);
    if (addressField) addressField.disabled = false;
    if (paymentSelect) paymentSelect.disabled = false;
    setActionFeedback();
    evaluateActions(order);
  }

  function requireVerificationEmail(){
    const value = (actionEmailInput?.value || '').trim();
    if (!value) {
      throw new Error('Enter the email associated with this order to continue.');
    }
    return value;
  }

  async function sendOrderAction(action, extra = {}){
    if (!currentOrder) {
      throw new Error('Find your order before using post-sales actions.');
    }
    const verifyEmail = requireVerificationEmail();
    const form = new URLSearchParams();
    form.append('action', action);
    if (currentOrder.code) form.append('orderCode', currentOrder.code);
    else form.append('orderId', String(currentOrder.id));
    form.append('email', verifyEmail);
    Object.entries(extra).forEach(([key, value]) => form.append(key, value));

    const resp = await fetch('order-status.php', { method: 'POST', body: form });
    const json = await resp.json();
    if (!json.success) {
      throw new Error(json.message || 'Unable to complete request.');
    }
    if (json.order) {
      hydrateFromOrder(json.order);
    }
    return json;
  }

  function hydrateFromOrder(order){
    const placed = order.created_at ? new Date(order.created_at) : null;
    const eta = estimateRange(placed || Date.now());
    const displayId = order.code || order.order_code || ('BB-' + String(order.id).padStart(6,'0'));
    setMeta(displayId, placed, eta, order.status || 'processing');
    setWhen(placed);
    setSteps(computeState(placed || Date.now()));
    const cart = { items: (order.items || []).map(it => ({
      name: it.name,
      category: it.category || '',
      qty: it.quantity || it.qty || 1,
      warranty: false,
      total: (it.total !== undefined) ? it.total : ((it.quantity || 1) * (it.price || 0)),
      image: it.image || 'assets/home/placeholder.png'
    })), subtotal: order.subtotal || 0 };
    renderItems(cart);
    updatePostSalesUI(order);
  }

  // Initial state
  resetPostSales();
  setMeta('-', null, '-', '-');
  setWhen(null);
  setSteps(-1);
  $('#statusItems').innerHTML = '<div class="empty">Enter Order ID or Email and click Check Status</div>';

  $('#statusForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = ($('#orderId')?.value || '').trim();
    const email = ($('#email')?.value || '').trim();
    if (!id && !email){
      alert('Enter your Order ID or email to continue.');
      return;
    }

    const form = new URLSearchParams();
    form.append('action', 'find');
    if (id) form.append('orderId', id);
    else form.append('email', email);

    try {
      const resp = await fetch('order-status.php', { method: 'POST', body: form });
      const json = await resp.json();
      if (!json.success) {
        setMeta('-', null, '-', '-');
        setWhen(null);
        setSteps(-1);
        $('#statusItems').innerHTML = '<div class="empty">No items to display. Enter a valid Order ID or email.</div>';
        resetPostSales();
        setActionFeedback('error', json.message || 'Order not found.');
        return;
      }
      if (actionEmailInput && email) {
        actionEmailInput.value = email;
      }
      hydrateFromOrder(json.order);
    } catch (err) {
      console.error(err);
      alert('Unable to contact server. Try again later.');
    }
  });

  cancelBtn?.addEventListener('click', async () => {
    if (!currentOrder) {
      alert('Find your order first.');
      return;
    }
    const originalText = cancelBtn.textContent;
    const previousDisabled = cancelBtn.disabled;
    cancelBtn.disabled = true;
    cancelBtn.textContent = 'Sending...';
    try {
      const res = await sendOrderAction('cancel');
      setActionFeedback('success', res.message || 'Cancellation request submitted.');
    } catch (err) {
      setActionFeedback('error', err.message);
    } finally {
      cancelBtn.textContent = originalText;
      if (currentOrder) evaluateActions(currentOrder);
      else cancelBtn.disabled = previousDisabled;
    }
  });

  addressForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentOrder) {
      alert('Find your order first.');
      return;
    }
    const address = (addressField?.value || '').trim();
    if (address.length < 10) {
      setActionFeedback('error', 'Please enter a full delivery address.');
      return;
    }
    const previous = addressSubmit?.textContent;
    if (addressSubmit) {
      addressSubmit.disabled = true;
      addressSubmit.textContent = 'Saving...';
    }
    try {
      const res = await sendOrderAction('update_address', { address });
      setActionFeedback('success', res.message || 'Delivery address updated.');
      if (addressField) addressField.value = '';
    } catch (err) {
      setActionFeedback('error', err.message);
    } finally {
      if (addressSubmit) {
        addressSubmit.textContent = previous || 'Update Address';
        if (currentOrder) evaluateActions(currentOrder);
      }
    }
  });

  paymentForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!currentOrder) {
      alert('Find your order first.');
      return;
    }
    const method = paymentSelect?.value;
    if (!method) {
      setActionFeedback('error', 'Select a payment method.');
      return;
    }
    const previous = paymentSubmit?.textContent;
    if (paymentSubmit) {
      paymentSubmit.disabled = true;
      paymentSubmit.textContent = 'Updating...';
    }
    try {
      const res = await sendOrderAction('change_payment', { payment_method: method });
      setActionFeedback('success', res.message || 'Payment method updated.');
    } catch (err) {
      setActionFeedback('error', err.message);
    } finally {
      if (paymentSubmit) {
        paymentSubmit.textContent = previous || 'Update Payment';
        if (currentOrder) evaluateActions(currentOrder);
      }
    }
  });
})();
