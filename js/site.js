(() => {
  const nav = document.querySelector('[data-nav]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const overlay = document.querySelector('[data-nav-overlay]');
  const closeNav = () => {
    nav?.classList.remove('show');
    overlay?.classList.remove('active');
    toggle?.setAttribute('aria-expanded', 'false');
    document.body.classList.remove('astro-nav-open');
  };
  toggle?.addEventListener('click', () => {
    const open = !nav?.classList.contains('show');
    nav?.classList.toggle('show', open);
    overlay?.classList.toggle('active', open);
    toggle.setAttribute('aria-expanded', String(open));
    document.body.classList.toggle('astro-nav-open', open);
  });
  overlay?.addEventListener('click', closeNav);
  nav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeNav));
})();
