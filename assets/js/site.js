(function(){
  const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Header shadow on scroll
  const header = document.querySelector('header');
  const onScroll = () => {
    if (!header) return;
    if (window.scrollY > 4) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Reveal on scroll
  const toReveal = [];
  document.querySelectorAll('.card, .section-title, .mv > div').forEach(el => {
    el.classList.add('reveal');
    toReveal.push(el);
  });
  if (!prefersReduced && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries)=>{
      entries.forEach(entry=>{
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    toReveal.forEach(el=> io.observe(el));
  } else {
    toReveal.forEach(el=> el.classList.add('show'));
  }

  // Subtle parallax for hero text
  const hero = document.querySelector('.hero');
  const heroContent = document.querySelector('.hero-content');
  if (hero && heroContent && !prefersReduced) {
    hero.addEventListener('mousemove', (e)=>{
      const r = hero.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      heroContent.style.transform = `translate3d(${x * 12}px, ${y * 12}px, 0)`;
    });
    hero.addEventListener('mouseleave', ()=>{
      heroContent.style.transform = 'translate3d(0,0,0)';
    });
  }

  // Keyboard support for carousel arrows if present
  const prevBtn = document.querySelector('.carousel-arrow.prev');
  const nextBtn = document.querySelector('.carousel-arrow.next');
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'ArrowLeft' && prevBtn) prevBtn.click();
    if (e.key === 'ArrowRight' && nextBtn) nextBtn.click();
  });

  // Click-to-open dropdown (works alongside :hover)
  const dropdown = document.querySelector('.profile-dropdown');
  const icon = dropdown ? dropdown.querySelector('.profile-icon') : null;
  if (dropdown && icon) {
    icon.addEventListener('click', (e)=>{
      e.preventDefault();
      dropdown.classList.toggle('open');
    });
    window.addEventListener('click', (e)=>{
      if (!dropdown.contains(e.target)) dropdown.classList.remove('open');
    });
  }
})();
