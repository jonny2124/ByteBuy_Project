(() => {
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  function currency(n){ return `$${n.toFixed(2)}`; }
  function formatDate(d){ return d.toLocaleDateString(undefined, { year:'numeric', month:'short', day:'numeric' }); }

  function loadCart(){ try { return JSON.parse(localStorage.getItem('bytebuy_cart') || 'null'); } catch(e){ return null; } }
  function loadLast(){ try { return JSON.parse(localStorage.getItem('bytebuy_last_order') || 'null'); } catch(e){ return null; } }

  function setMeta(id, placed, eta){
    $('#metaOrder').textContent = id || '—';
    $('#metaDate').textContent = placed ? formatDate(new Date(placed)) : '—';
    $('#metaEta').textContent = eta || '—';
  }

  function setSteps(state){
    // state: 0=ordered,1=packed,2=shipped,3=delivered
    const steps = $$('#tracker .step');
    steps.forEach((li, idx) => {
      li.classList.remove('completed','active');
      if (idx < state) li.classList.add('completed');
      if (idx === state) li.classList.add('active');
    });
  }

  function setWhen(placed){
    const base = new Date(placed || Date.now());
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
    const p = new Date(placed || Date.now());
    const a = new Date(p); a.setDate(a.getDate()+3);
    const b = new Date(p); b.setDate(b.getDate()+5);
    return `${formatDate(a)} – ${formatDate(b)}`;
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

  function computeState(placed){
    const start = new Date(placed || Date.now());
    const days = (Date.now() - start.getTime()) / (1000*60*60*24);
    if (days < 1) return 0; // ordered
    if (days < 2) return 1; // packed
    if (days < 4) return 2; // shipped
    return 3; // delivered
  }

  function hydrate(id, placed){
    const cart = loadCart();
    const eta = estimateRange(placed);
    setMeta(id, placed, eta);
    setWhen(placed);
    setSteps(computeState(placed));
    renderItems(cart);
  }

  // Initial attempt from last order
  const last = loadLast();
  if (last?.id){ hydrate(last.id, last.date || Date.now()); }
  else { hydrate('BB-000000', Date.now()); }

  // Form handling
  $('#statusForm')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const id = ($('#orderId')?.value || '').trim();
    const email = ($('#email')?.value || '').trim();
    if (!id && !email){
      alert('Enter your Order ID or email to continue.');
      return;
    }
    // Demo lookup: match local last order id if provided
    if (id && last?.id && id.toUpperCase() === last.id.toUpperCase()){
      hydrate(last.id, last.date || Date.now());
    } else {
      // Fallback: hydrate with a generated ID seeded from input for demo
      const gen = id || (email ? 'BB-' + Math.floor(100000 + Math.random()*900000) : 'BB-000000');
      hydrate(gen, Date.now());
    }
  });
})();

