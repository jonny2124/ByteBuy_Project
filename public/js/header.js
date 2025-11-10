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

// Mega menu interactions for new header
const setupMegaNav = () => {
  const nav = document.querySelector('.mega-nav');
  if (!nav) return;

  const items = nav.querySelectorAll('.nav-item');

  const closeAll = () => {
    items.forEach(item => {
      item.classList.remove('open');
      const trigger = item.querySelector('.nav-trigger');
      if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
  };

  const selectItem = (target) => {
    items.forEach(item => item.classList.remove('is-selected'));
    if (target) target.classList.add('is-selected');
  };

  items.forEach(item => {
    const trigger = item.querySelector('.nav-trigger');
    if (!trigger) return;

    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      const isOpen = item.classList.contains('open');
      closeAll();
      selectItem(item);
      if (!isOpen) {
        item.classList.add('open');
        trigger.setAttribute('aria-expanded', 'true');
      }
    });
  });

  document.addEventListener('click', (e) => {
    if (!nav.contains(e.target)) closeAll();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAll();
  });
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    applyHeaderScrollEffect();
    setupMegaNav();
  });
} else {
  applyHeaderScrollEffect();
  setupMegaNav();
}
