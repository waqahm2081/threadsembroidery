/* ==============================================
   ABOUT.JS — Scroll reveal for about page
   ============================================== */
(function () {
  'use strict';

  // Simple fade-in-up on scroll for about page sections
  const revealEls = document.querySelectorAll(
    '#about-story .abs-container > *, #about-why .abw-card, #about-stats .abst-item'
  );

  if (!revealEls.length) return;

  // Set initial state
  revealEls.forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(28px)';
    el.style.transition = `opacity 0.6s ease ${i * 0.08}s, transform 0.6s ease ${i * 0.08}s`;
  });

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  revealEls.forEach(el => observer.observe(el));
})();
