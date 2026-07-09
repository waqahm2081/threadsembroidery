(function () {
  'use strict';

  const section = document.getElementById('branding-service');
  if (!section || !('IntersectionObserver' in window)) return;

  const revealEls = section.querySelectorAll('[data-bs-reveal]');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('bs-in-view');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

  revealEls.forEach(el => observer.observe(el));
})();
