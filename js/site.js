(() => {
  const nav = document.querySelector('[data-nav]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const overlay = document.querySelector('[data-nav-overlay]');
  const closeNav = () => {
    nav?.classList.remove('show');
    overlay?.classList.remove('active');
    toggle?.setAttribute('aria-expanded', 'false');
    toggle?.setAttribute('aria-label', 'Open navigation');
    toggle?.classList.remove('is-open');
    document.body.classList.remove('astro-nav-open');
  };
  toggle?.addEventListener('click', () => {
    const open = !nav?.classList.contains('show');
    nav?.classList.toggle('show', open);
    overlay?.classList.toggle('active', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    toggle.classList.toggle('is-open', open);
    document.body.classList.toggle('astro-nav-open', open);
  });
  overlay?.addEventListener('click', closeNav);
  nav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeNav));
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || !nav?.classList.contains('show')) return;
    closeNav();
    toggle?.focus();
  });
  window.matchMedia('(min-width: 1081px)').addEventListener('change', (event) => {
    if (event.matches) closeNav();
  });
})();
