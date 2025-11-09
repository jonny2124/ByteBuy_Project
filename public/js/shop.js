// ===== PRODUCT DATA (fetched from backend) =====
let PRODUCTS = [];
let isLoadingProducts = false;
let hasLoadedProducts = false;

// ===== Helpers =====
const grid = document.getElementById('productGrid');
const filterBtns = document.querySelectorAll('.filter-btn');
const sortSelect = document.getElementById('sortSelect');
const searchInput = document.getElementById('searchInput');
const LS_KEY = 'bytebuy_ratings';

function setControlsDisabled(disabled) {
  filterBtns.forEach(btn => { btn.disabled = disabled; });
  sortSelect.disabled = disabled;
  searchInput.disabled = disabled;
}

function showStatus(message, cssClass = 'state-message') {
  grid.innerHTML = `<p class="${cssClass}">${message}</p>`;
}

async function fetchProducts() {
  if (isLoadingProducts) return;
  isLoadingProducts = true;
  setControlsDisabled(true);
  showStatus('Loading products...', 'state-message loading');

  try {
    const response = await fetch('products.php');
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    const payload = await response.json();
    if (!payload.success || !Array.isArray(payload.items)) {
      throw new Error(payload.message || 'Invalid product response');
    }

    PRODUCTS = payload.items.map(item => ({
      ...item,
      price: Number(item.price) || 0,
      rating: Number(item.rating) || 0,
      img: item.img || 'assets/products/placeholder.png',
    }));

    hasLoadedProducts = true;
    render(getFilteredSorted());
  } catch (error) {
    console.error('Failed to load products', error);
    showStatus('Unable to load products right now. Please refresh.', 'state-message error');
  } finally {
    isLoadingProducts = false;
    setControlsDisabled(false);
  }
}

// Load ratings from localStorage
function loadRatings() {
  try {
    return JSON.parse(localStorage.getItem(LS_KEY)) || {};
  } catch {
    return {};
  }
}

// Save ratings to localStorage
function saveRatings(store) {
  localStorage.setItem(LS_KEY, JSON.stringify(store));
}

// Generate a star rating HTML (interactive)
function starRatingHTML(productId, currentRating = 0) {
  // Round to nearest half not needed for click; show full stars based on floor
  const filled = Math.round(currentRating);
  let stars = '';
  for (let i = 1; i <= 5; i++) {
    const cls = i <= filled ? 'star filled' : 'star';
    stars += `<span class="${cls}" data-value="${i}" aria-label="${i} stars">★</span>`;
  }
  return `<div class="stars" data-id="${productId}" role="radiogroup" aria-label="Rate this product">
            ${stars}
          </div>`;
}

// Render grid based on filters/sort/search
function render(products) {
  if (!Array.isArray(products) || products.length === 0) {
    if (hasLoadedProducts) {
      showStatus('No products match your filters.');
    }
    return;
  }

  const ratingsStore = loadRatings();
  grid.innerHTML = products.map(p => {
    const rating = Number(ratingsStore[p.id] ?? p.rating ?? 0);
    const price = Number(p.price) || 0;
    const detailUrl = `product-details.php?id=${encodeURIComponent(p.id)}`;
    return `
      <article id="product-${p.id}" class="product-card" data-category="${p.category}">
        <figure class="product-media">
          <a href="${detailUrl}" class="product-link" aria-label="View ${p.name} details">
            <img src="${p.img}" alt="${p.name}">
          </a>
        </figure>
        <div class="product-info">
          <p class="product-category">${p.category}</p>
          <h3 class="product-name">
            <a href="${detailUrl}">${p.name}</a>
          </h3>
          <div class="product-meta">
            <div class="product-rating">
              ${starRatingHTML(p.id, rating)}
              <span class="rating-number">${rating.toFixed(1)}</span>
            </div>
            <p class="product-price">$${price.toFixed(2)}</p>
          </div>
          <button class="btn-add" data-id="${p.id}">Add to cart</button>
        </div>
      </article>
    `;
  }).join('');

  // Bind star interactions
  grid.querySelectorAll('.stars').forEach(starWrap => {
    const pid = starWrap.dataset.id;
    const stars = starWrap.querySelectorAll('.star');

    const highlight = value => {
      stars.forEach(s => s.classList.toggle('filled', Number(s.dataset.value) <= value));
    };

    const restore = () => {
      const store = loadRatings();
      const base = PRODUCTS.find(x => x.id === pid);
      const fallback = base ? Number(base.rating) || 0 : 0;
      const current = Math.round(store[pid] ?? fallback);
      highlight(current);
    };

    stars.forEach(star => {
      star.addEventListener('mouseenter', () => {
        highlight(Number(star.dataset.value));
      });
      star.addEventListener('click', () => {
        const store = loadRatings();
        const chosen = Number(star.dataset.value);
        store[pid] = chosen;
        saveRatings(store);
        const num = starWrap.parentElement.querySelector('.rating-number');
        if (num) {
          num.textContent = chosen.toFixed(1);
        }
        highlight(chosen);
      });
    });

    starWrap.addEventListener('mouseleave', restore);
    restore();
  });

  // Check stock level for a product
  async function checkStock(sku) {
    try {
      const response = await fetch(`check_stock.php?sku=${encodeURIComponent(sku)}`);
      const data = await response.json();
      return data.stock;
    } catch (error) {
      console.error('Error checking stock:', error);
      return null;
    }
  }

  // Update button state based on stock
  async function updateButtonState(btn, sku) {
    const stock = await checkStock(sku);
    if (stock !== null) {
      btn.disabled = stock <= 0;
      if (stock <= 0) {
        btn.textContent = 'Out of Stock';
      } else {
        btn.textContent = 'Add to cart';
      }
    }
  }

  // Show notification function
  function showNotification(message, isError = false) {
    const overlay = document.getElementById('notificationOverlay');
    const notification = document.getElementById('notification');
    
    notification.textContent = message;
    notification.style.backgroundColor = isError ? '#dc2626' : '#444444';
    overlay.style.backgroundColor = isError ? 'rgba(0, 0, 0, 0.6)' : 'rgba(0, 0, 0, 0.5)';
    
    overlay.classList.add('show');
    notification.classList.add('show');
    
    // Remove after 1.5 seconds
    setTimeout(() => {
      notification.classList.add('hide');
      overlay.classList.add('hide');
      setTimeout(() => {
        overlay.classList.remove('show', 'hide');
        notification.classList.remove('show', 'hide');
      }, 300);
    }, 1500);
  }

  // Bind add to cart: send to server using a local cart token (no session cookie required)
  grid.querySelectorAll('.btn-add').forEach(btn => {
    const sku = btn.dataset.id;
    // Check initial stock level
    updateButtonState(btn, sku);
    
    btn.addEventListener('click', async () => {
      // Check stock again when clicking (in case it changed)
      const stock = await checkStock(sku);
      const product = PRODUCTS.find(p => p.id === sku);
      const productName = product?.name || sku;

      if (stock === null) {
        showNotification(`Could not verify stock for "${productName}". Please try again.`, true);
        return;
      }
      
      if (stock <= 0) {
        showNotification(`"${productName}" is out of stock`, true); // true indicates error state
        updateButtonState(btn, sku); // Update button state
        return;
      }
      
      // create cart token if missing
      let token = localStorage.getItem('cart_token');
      if (!token) {
        token = 'ct_' + Date.now() + '_' + Math.random().toString(36).slice(2,9);
        localStorage.setItem('cart_token', token);
      }

      const form = new URLSearchParams();
      form.append('action', 'add');
      form.append('cart_token', token);
      form.append('sku', sku);
      form.append('qty', '1');

      try {
        const res = await fetch('cart.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) {
          // Show success notification after server confirms
          showNotification(`"${productName}" added to cart`);
          btn.classList.add('added');
          setTimeout(() => btn.classList.remove('added'), 900);
          // optional: update a cart count UI if present
          const evt = new CustomEvent('cart.updated', { detail: data });
          window.dispatchEvent(evt);
        } else {
          // show error notification instead of alert; include product name when helpful
          const errMsg = data.message || 'Failed to add to cart';
          const namePrefix = productName ? `"${productName}" ` : '';
          const fullMsg = `${namePrefix}${errMsg}`.trim();
          showNotification(fullMsg, true);
        }
      } catch (e) {
        console.error(e);
        showNotification('Network error while adding to cart', true);
      }
    });
  });
}

// Current UI state
let currentFilter = 'All';
let currentSearch = '';
let currentSort = 'default';

// Apply UI state to data
function getFilteredSorted() {
  let list = PRODUCTS.slice();

  // filter
  if (currentFilter !== 'All') {
    list = list.filter(p => p.category === currentFilter);
  }
  // search
  if (currentSearch.trim()) {
    const q = currentSearch.toLowerCase();
    list = list.filter(p => p.name.toLowerCase().includes(q));
  }
  // sort
  switch (currentSort) {
    case 'price_asc':
      list.sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0)); break;
    case 'price_desc':
      list.sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0)); break;
    case 'rating_desc': {
      const store = loadRatings();
      list.sort((a, b) => {
        const br = Number(store[b.id] ?? b.rating ?? 0);
        const ar = Number(store[a.id] ?? a.rating ?? 0);
        return br - ar;
      });
      break;
    }
    case 'name_asc':
      list.sort((a, b) => a.name.localeCompare(b.name)); break;
    default:
      // no-op (featured)
      break;
  }
  return list;
}

// Optionally apply initial filter/sort from URL (?filter=...&sort=... or #filter=...)
(() => {
  try {
    const params = new URLSearchParams(window.location.search);
    const raw = (params.get('filter') || window.location.hash.replace('#filter=', '') || '').toLowerCase();
    const map = {
      'all': 'All',
      'laptop': 'Laptops',
      'laptops': 'Laptops',
      'smartphone': 'Smartphones',
      'smartphones': 'Smartphones',
      'audio': 'Audio',
      'storage': 'Storage',
      'accessory': 'Accessories',
      'accessories': 'Accessories'
    };
    const mapped = map[raw];
    if (mapped) currentFilter = mapped;

    const sort = (params.get('sort') || '').toLowerCase();
    const allowedSort = ['default','price_asc','price_desc','rating_desc','name_asc'];
    if (allowedSort.includes(sort)) currentSort = sort;
  } catch {}
})();

// Init
fetchProducts();

// If URL contains #product-..., scroll to it after initial render
try {
  if (window.location.hash && window.location.hash.startsWith('#product-')) {
    const el = document.querySelector(window.location.hash);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
} catch {}

// Reflect initial active filter button if present
filterBtns.forEach(b => {
  if (b.dataset.filter === currentFilter) b.classList.add('active');
  else b.classList.remove('active');
});

// Bind controls
filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.dataset.filter;
    if (!hasLoadedProducts) return;
    render(getFilteredSorted());
  });
});

sortSelect.addEventListener('change', () => {
  currentSort = sortSelect.value;
  if (!hasLoadedProducts) return;
  render(getFilteredSorted());
});

// Reflect sort from URL into the select if present
try {
  const params = new URLSearchParams(window.location.search);
  const sort = params.get('sort');
  if (sort) sortSelect.value = sort;
} catch {}

searchInput.addEventListener('input', () => {
  currentSearch = searchInput.value;
  if (!hasLoadedProducts) return;
  render(getFilteredSorted());
});

// Make controls bar sticky shadow after scroll
document.addEventListener('scroll', () => {
  const bar = document.getElementById('shopControls');
  if (window.scrollY > 10) bar.classList.add('stick');
  else bar.classList.remove('stick');
});
