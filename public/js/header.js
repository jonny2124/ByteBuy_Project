// Header darkening after hero scroll (guard if no hero)
const applyHeaderScrollEffect = () => {
  const header = document.querySelector('.header');
  const hero = document.querySelector('.hero');
  if (!header) return;

  const onScroll = () => {
    if (hero) {
      const threshold = Math.max(0, hero.offsetHeight - 80);
      if (window.scrollY > threshold) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    } else {
      if (window.scrollY > 0) header.classList.add('scrolled');
      else header.classList.remove('scrolled');
    }
  };

  // Initialize state and bind
  onScroll();
  document.addEventListener('scroll', onScroll);
};

// Dropdown toggle for Shop icon
const setupShopDropdown = () => {
  const dropdown = document.getElementById('shopDropdown');
  const toggle = document.getElementById('shopMenuToggle');
  if (!dropdown || !toggle) return;

  const menu = document.getElementById('shopMenu');
  const closeMenu = () => {
    dropdown.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
  };
  const openMenu = () => {
    dropdown.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
  };
  const toggleMenu = () => {
    if (dropdown.classList.contains('open')) closeMenu();
    else openMenu();
  };

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    toggleMenu();
  });

  // Close on outside click
  document.addEventListener('click', (e) => {
    if (!dropdown.contains(e.target)) closeMenu();
  });

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    applyHeaderScrollEffect();
    setupShopDropdown();
  });
} else {
  applyHeaderScrollEffect();
  setupShopDropdown();
}
