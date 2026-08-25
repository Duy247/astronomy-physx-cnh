(() => {
  const root = document.documentElement;
  const nav = document.querySelector('[data-nav]');
  const toggle = document.querySelector('[data-nav-toggle]');
  const overlay = document.querySelector('[data-nav-overlay]');
  const setTheme = (theme) => {
    root.dataset.theme = theme;
    localStorage.setItem('theme', theme);
  };
  setTheme(localStorage.getItem('theme') || 'dark');
  document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
    setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
  });
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
