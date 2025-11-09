(function () {
  const detail = document.getElementById('productDetail');
  if (!detail) return;

  const qtyInput = document.getElementById('quantityInput');
  const minusBtn = document.getElementById('qtyMinus');
  const plusBtn = document.getElementById('qtyPlus');
  const addBtn = document.getElementById('addToCartBtn');
  const toastEl = document.getElementById('productToast');
  const stockStatusEl = document.getElementById('stockStatus');
  const stockValueEl = document.getElementById('stockValue');

  const sku = detail.dataset.sku;
  const productName = detail.dataset.name || 'product';
  let stock = Number(detail.dataset.stock) || 0;
  let toastTimer = null;

  const clampQty = (value) => {
    const max = Math.max(1, stock);
    let qty = parseInt(value, 10);
    if (Number.isNaN(qty)) qty = 1;
    qty = Math.min(Math.max(qty, 1), max);
    if (qtyInput) qtyInput.value = qty;
    return qty;
  };

  const updateStockUI = () => {
    if (stockValueEl) {
      stockValueEl.textContent = Math.max(0, stock);
    }
    if (stockStatusEl) {
      const inStock = stock > 0;
      stockStatusEl.textContent = inStock ? 'In stock' : 'Out of stock';
      stockStatusEl.classList.toggle('in-stock', inStock);
      stockStatusEl.classList.toggle('out-of-stock', !inStock);
    }
    if (qtyInput) {
      qtyInput.disabled = stock <= 0;
      qtyInput.max = String(Math.max(1, stock));
      if (stock > 0) {
        clampQty(parseInt(qtyInput.value || '1', 10));
      }
    }
    if (addBtn) {
      addBtn.disabled = stock <= 0;
      addBtn.textContent = stock <= 0 ? 'Out of Stock' : 'Add to Cart';
    }
  };

  const showToast = (message, isError = false) => {
    if (!toastEl) {
      window.alert(message);
      return;
    }
    toastEl.textContent = message;
    toastEl.classList.remove('error', 'show');
    if (isError) toastEl.classList.add('error');
    void toastEl.offsetWidth;
    toastEl.classList.add('show');
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toastEl.classList.remove('show');
    }, 3200);
  };

  const requestStock = async () => {
    try {
      const response = await fetch(`check_stock.php?sku=${encodeURIComponent(sku)}`);
      if (!response.ok) throw new Error('Unable to verify stock');
      const data = await response.json();
      if (typeof data.stock === 'number') {
        stock = data.stock;
        detail.dataset.stock = String(stock);
        updateStockUI();
        return stock;
      }
      throw new Error('Invalid stock response');
    } catch (error) {
      console.error(error);
      showToast('Could not verify stock right now.', true);
      return null;
    }
  };

  const getCartToken = () => {
    let token = localStorage.getItem('cart_token');
    if (!token) {
      token = `ct_${Date.now()}_${Math.random().toString(36).slice(2, 9)}`;
      localStorage.setItem('cart_token', token);
    }
    return token;
  };

  minusBtn?.addEventListener('click', () => {
    if (qtyInput?.disabled) return;
    clampQty((parseInt(qtyInput.value, 10) || 1) - 1);
  });

  plusBtn?.addEventListener('click', () => {
    if (qtyInput?.disabled) return;
    clampQty((parseInt(qtyInput.value, 10) || 1) + 1);
  });

  qtyInput?.addEventListener('input', () => {
    clampQty(qtyInput.value);
  });

  addBtn?.addEventListener('click', async () => {
    if (addBtn.disabled) return;
    const desiredQty = clampQty(qtyInput?.value || '1');

    addBtn.disabled = true;
    addBtn.textContent = 'Adding...';

    const latestStock = await requestStock();
    if (latestStock === null) {
      addBtn.disabled = stock <= 0;
      addBtn.textContent = stock <= 0 ? 'Out of Stock' : 'Add to Cart';
      return;
    }

    if (latestStock <= 0) {
      showToast('This item just sold out.', true);
      updateStockUI();
      return;
    }

    if (desiredQty > latestStock) {
      clampQty(latestStock);
      showToast(`Only ${latestStock} left in stock.`, true);
      addBtn.disabled = false;
      addBtn.textContent = 'Add to Cart';
      return;
    }

    const payload = new URLSearchParams();
    payload.append('action', 'add');
    payload.append('cart_token', getCartToken());
    payload.append('sku', sku);
    payload.append('qty', String(desiredQty));

    try {
      const response = await fetch('cart.php', {
        method: 'POST',
        body: payload,
      });
      const data = await response.json();
      if (!data.success) {
        throw new Error(data.message || 'Failed to add to cart');
      }
      showToast(`"${productName}" added to cart.`);
      stock = Math.max(0, latestStock - desiredQty);
      detail.dataset.stock = String(stock);
      updateStockUI();
      window.dispatchEvent(new CustomEvent('cart.updated', { detail: data }));
    } catch (error) {
      console.error(error);
      showToast(error.message || 'Unable to add to cart.', true);
    } finally {
      addBtn.disabled = stock <= 0;
      addBtn.textContent = stock <= 0 ? 'Out of Stock' : 'Add to Cart';
    }
  });

  updateStockUI();
})();
