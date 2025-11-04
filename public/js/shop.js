// ===== PRODUCT DATA (front-end only) =====
// Keep existing assets from your home; others use placeholders you can replace.
const PRODUCTS = [
  // Laptops (8)
  {id:"lap1", name:"MacBook Air M2", category:"Laptops", price:1099, rating:4.6, img:"assets/deal2.jpeg"},
  {id:"lap2", name:"MacBook Air M4", category:"Laptops", price:1299, rating:4.8, img:"assets/macbook.jpeg"},
  {id:"lap3", name:"Dell XPS 13 (2024)", category:"Laptops", price:1199, rating:4.5, img:"assets/placeholder_laptop1.jpg"},
  {id:"lap4", name:"HP Spectre x360", category:"Laptops", price:1149, rating:4.4, img:"assets/placeholder_laptop2.jpg"},
  {id:"lap5", name:"Lenovo Yoga 7", category:"Laptops", price:999, rating:4.3, img:"assets/placeholder_laptop3.jpg"},
  {id:"lap6", name:"ASUS ZenBook 14", category:"Laptops", price:1049, rating:4.2, img:"assets/placeholder_laptop4.jpg"},
  {id:"lap7", name:"Acer Swift Go", category:"Laptops", price:899, rating:4.1, img:"assets/placeholder_laptop5.jpg"},
  {id:"lap8", name:"MSI Modern 14", category:"Laptops", price:949, rating:4.0, img:"assets/placeholder_laptop6.jpg"},

  // Smartphones (8)
  {id:"ph1", name:"Samsung Galaxy S23", category:"Smartphones", price:899, rating:4.7, img:"assets/deal1.webp"},
  {id:"ph2", name:"iPhone 15", category:"Smartphones", price:999, rating:4.6, img:"assets/placeholder_phone1.jpg"},
  {id:"ph3", name:"Google Pixel 8", category:"Smartphones", price:799, rating:4.5, img:"assets/placeholder_phone2.jpg"},
  {id:"ph4", name:"OnePlus 12", category:"Smartphones", price:749, rating:4.4, img:"assets/placeholder_phone3.jpg"},
  {id:"ph5", name:"Xiaomi 13T", category:"Smartphones", price:599, rating:4.2, img:"assets/placeholder_phone4.jpg"},
  {id:"ph6", name:"Nothing Phone (2a)", category:"Smartphones", price:499, rating:4.1, img:"assets/placeholder_phone5.jpg"},
  {id:"ph7", name:"iPhone 14", category:"Smartphones", price:799, rating:4.5, img:"assets/placeholder_phone6.jpg"},
  {id:"ph8", name:"Samsung A55", category:"Smartphones", price:449, rating:4.0, img:"assets/placeholder_phone7.jpg"},

  // Audio (8)
  {id:"au1", name:"Sony WH-1000XM5", category:"Audio", price:399, rating:4.8, img:"assets/deal3.jpg"},
  {id:"au2", name:"AirPods Pro (2nd Gen)", category:"Audio", price:249, rating:4.7, img:"assets/placeholder_audio1.jpg"},
  {id:"au3", name:"Bose QC Ultra", category:"Audio", price:349, rating:4.6, img:"assets/placeholder_audio2.jpg"},
  {id:"au4", name:"Sony WF-1000XM5", category:"Audio", price:279, rating:4.6, img:"assets/placeholder_audio3.jpg"},
  {id:"au5", name:"Sennheiser Momentum 4", category:"Audio", price:329, rating:4.5, img:"assets/placeholder_audio4.jpg"},
  {id:"au6", name:"Beats Studio Pro", category:"Audio", price:299, rating:4.3, img:"assets/placeholder_audio5.jpg"},
  {id:"au7", name:"JBL Live Pro 2", category:"Audio", price:149, rating:4.2, img:"assets/placeholder_audio6.jpg"},
  {id:"au8", name:"Anker Soundcore Q45", category:"Audio", price:129, rating:4.1, img:"assets/placeholder_audio7.jpg"},

  // Storage (8)
  {id:"st1", name:"Samsung T7 1TB SSD", category:"Storage", price:119, rating:4.7, img:"assets/placeholder_storage1.jpg"},
  {id:"st2", name:"SanDisk Extreme 1TB SSD", category:"Storage", price:129, rating:4.6, img:"assets/placeholder_storage2.jpg"},
  {id:"st3", name:"WD MyPassport 2TB HDD", category:"Storage", price:89, rating:4.3, img:"assets/placeholder_storage3.jpg"},
  {id:"st4", name:"Lexar NM790 1TB NVMe", category:"Storage", price:99, rating:4.5, img:"assets/placeholder_storage4.jpg"},
  {id:"st5", name:"Kingston KC3000 2TB", category:"Storage", price:189, rating:4.6, img:"assets/placeholder_storage5.jpg"},
  {id:"st6", name:"Crucial X9 2TB SSD", category:"Storage", price:179, rating:4.4, img:"assets/placeholder_storage6.jpg"},
  {id:"st7", name:"Seagate Expansion 4TB", category:"Storage", price:129, rating:4.2, img:"assets/placeholder_storage7.jpg"},
  {id:"st8", name:"Samsung EVO Plus 256GB", category:"Storage", price:29, rating:4.1, img:"assets/placeholder_storage8.jpg"},

  // Accessories (8)
  {id:"ac1", name:"Apple Magic Mouse", category:"Accessories", price:79, rating:4.2, img:"assets/placeholder_acc1.jpg"},
  {id:"ac2", name:"Logitech MX Master 3S", category:"Accessories", price:99, rating:4.7, img:"assets/placeholder_acc2.jpg"},
  {id:"ac3", name:"Keychron K2 V2", category:"Accessories", price:89, rating:4.5, img:"assets/placeholder_acc3.jpg"},
  {id:"ac4", name:"Anker 737 Charger", category:"Accessories", price:59, rating:4.6, img:"assets/placeholder_acc4.jpg"},
  {id:"ac5", name:"UGREEN USB-C Hub 7-in-1", category:"Accessories", price:49, rating:4.4, img:"assets/placeholder_acc5.jpg"},
  {id:"ac6", name:"Apple Pencil (2nd Gen)", category:"Accessories", price:119, rating:4.6, img:"assets/placeholder_acc6.jpg"},
  {id:"ac7", name:"Samsung 45W PD Charger", category:"Accessories", price:39, rating:4.3, img:"assets/placeholder_acc7.jpg"},
  {id:"ac8", name:"Baseus Laptop Stand", category:"Accessories", price:29, rating:4.2, img:"assets/placeholder_acc8.jpg"},
];

// ===== Helpers =====
const grid = document.getElementById('productGrid');
const filterBtns = document.querySelectorAll('.filter-btn');
const sortSelect = document.getElementById('sortSelect');
const searchInput = document.getElementById('searchInput');
const LS_KEY = 'bytebuy_ratings';

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
  const ratingsStore = loadRatings();
  grid.innerHTML = products.map(p => {
    const r = ratingsStore[p.id] ?? p.rating;
    return `
      <article class="product-card" data-category="${p.category}">
        <figure class="product-media">
          <img src="${p.img}" alt="${p.name}">
        </figure>
        <div class="product-info">
          <p class="product-category">${p.category}</p>
          <h3 class="product-name">${p.name}</h3>
          <div class="product-meta">
            <div class="product-rating">
              ${starRatingHTML(p.id, r)}
              <span class="rating-number">${(r).toFixed(1)}</span>
            </div>
            <p class="product-price">$${p.price.toFixed(2)}</p>
          </div>
          <button class="btn-add" data-id="${p.id}">Add to cart</button>
        </div>
      </article>
    `;
  }).join('');

  // Bind star interactions
  document.querySelectorAll('.stars').forEach(starWrap => {
    const pid = starWrap.dataset.id;
    const stars = starWrap.querySelectorAll('.star');
    stars.forEach(star => {
      // hover
      star.addEventListener('mouseenter', () => {
        const val = Number(star.dataset.value);
        stars.forEach(s => s.classList.toggle('filled', Number(s.dataset.value) <= val));
      });
      // leave: restore current stored value
      starWrap.addEventListener('mouseleave', () => {
        const store = loadRatings();
        const current = Math.round(store[pid] ?? PRODUCTS.find(x => x.id === pid).rating);
        stars.forEach(s => s.classList.toggle('filled', Number(s.dataset.value) <= current));
      });
      // click: set rating
      star.addEventListener('click', () => {
        const store = loadRatings();
        const chosen = Number(star.dataset.value);
        store[pid] = chosen;
        saveRatings(store);
        // update text number
        const num = starWrap.parentElement.querySelector('.rating-number');
        num.textContent = chosen.toFixed(1);
      });
    });
  });

  // Check stock level for a product
  async function checkStock(sku) {
    try {
      const response = await fetch(`check_stock.php?sku=${sku}`);
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
  document.querySelectorAll('.btn-add').forEach(btn => {
    const sku = btn.dataset.id;
    // Check initial stock level
    updateButtonState(btn, sku);
    
    btn.addEventListener('click', async () => {
      // Check stock again when clicking (in case it changed)
      const stock = await checkStock(sku);
      const product = PRODUCTS.find(p => p.id === sku);
      
      if (stock <= 0) {
        showNotification(`"${product.name}" is out of stock`, true); // true indicates error state
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
          showNotification(`"${product.name}" added to cart`);
          btn.classList.add('added');
          setTimeout(() => btn.classList.remove('added'), 900);
          // optional: update a cart count UI if present
          const evt = new CustomEvent('cart.updated', { detail: data });
          window.dispatchEvent(evt);
        } else {
          // show error notification instead of alert; include product name when helpful
          const errMsg = data.message || 'Failed to add to cart';
          const fullMsg = product && product.name ? `"${product.name}" ${errMsg}` : errMsg;
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
      list.sort((a, b) => a.price - b.price); break;
    case 'price_desc':
      list.sort((a, b) => b.price - a.price); break;
    case 'rating_desc': {
      const store = loadRatings();
      list.sort((a, b) => (store[b.id] ?? b.rating) - (store[a.id] ?? a.rating));
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

// Init
render(getFilteredSorted());

// Bind controls
filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    currentFilter = btn.dataset.filter;
    render(getFilteredSorted());
  });
});

sortSelect.addEventListener('change', () => {
  currentSort = sortSelect.value;
  render(getFilteredSorted());
});

searchInput.addEventListener('input', () => {
  currentSearch = searchInput.value;
  render(getFilteredSorted());
});

// Make controls bar sticky shadow after scroll
document.addEventListener('scroll', () => {
  const bar = document.getElementById('shopControls');
  if (window.scrollY > 10) bar.classList.add('stick');
  else bar.classList.remove('stick');
});
