/* =============================================
   THREADS EMBROIDERY & PRINT LTD
   script.js
   ============================================= */

$(document).ready(function () {

  /* =============================================
     1. TOP BAR TICKER — duplicate content for
        seamless infinite scroll loop
     ============================================= */
  const $track = $('#tickerTrack');
  if ($track.length) {
    // Clone the entire inner HTML and append it
    // so the CSS animation can loop seamlessly
    const originalHTML = $track.html();
    $track.append(originalHTML);
  }


  /* =============================================
     2. HEADER — SCROLL BEHAVIOUR
     ============================================= */
  const $header = $('#site-header');
  let lastScrollY = 0;

  $(window).on('scroll', function () {
    const currentY = $(this).scrollTop();

    // Compact header after 50px
    if (currentY > 50) {
      $header.addClass('scrolled');
    } else {
      $header.removeClass('scrolled');
    }

    // Hide on scroll down (fast), reveal on scroll up
    if (currentY > lastScrollY + 6 && currentY > 180) {
      $header.css('transform', 'translateY(-100%)');
      closeMegaMenu(); // close mega when header hides
    } else if (currentY < lastScrollY - 3) {
      $header.css('transform', 'translateY(0)');
    }

    lastScrollY = currentY;
  });


  /* =============================================
     3. MEGA MENU — TOGGLE
     ============================================= */
  const $trigger    = $('#catalogueTrigger');
  const $megaMenu   = $('#megaMenu');
  const $megaParent = $('#catalogueItem');

  // Click to open / close (works on all devices)
  $trigger.on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const isOpen = $megaMenu.hasClass('open');
    closeMegaMenu();
    if (!isOpen) openMegaMenu();
  });

  // Close on outside click
  $(document).on('click', function (e) {
    if (!$megaParent[0].contains(e.target)) {
      closeMegaMenu();
    }
  });

  // Close on Escape
  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') closeMegaMenu();
  });

  function openMegaMenu() {
    $megaMenu.addClass('open');
    $trigger.addClass('open');
    // Recalculate top position based on header height
    if ($(window).width() > 991) {
      const headerH = $header.outerHeight();
      $megaMenu.css('top', headerH + 'px');
    }
  }

  function closeMegaMenu() {
    $megaMenu.removeClass('open');
    $trigger.removeClass('open');
  }

  // Desktop: also open on hover for smooth UX
  if ($(window).width() > 991) {
    let hoverTimer;

    $megaParent.on('mouseenter', function () {
      clearTimeout(hoverTimer);
      openMegaMenu();
    });

    $megaParent.on('mouseleave', function () {
      hoverTimer = setTimeout(function () {
        if (!$megaMenu.is(':hover') && !$megaParent.is(':hover')) {
          closeMegaMenu();
        }
      }, 130);
    });

    $megaMenu.on('mouseenter', function () {
      clearTimeout(hoverTimer);
    });

    $megaMenu.on('mouseleave', function () {
      hoverTimer = setTimeout(closeMegaMenu, 130);
    });
  }


  /* =============================================
     4. HERO STAT COUNTERS — count up on view
     ============================================= */
  function animateCounter($el) {
    const target = parseInt($el.data('target'));
    const suffix = $el.data('suffix') || '';
    if (!target) return;

    let current   = 0;
    const duration = 1400;
    const steps    = 55;
    const increment = target / steps;
    const interval  = duration / steps;

    const timer = setInterval(function () {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      // Format with comma if >= 1000
      const display = current >= 1000
        ? Math.round(current).toLocaleString('en-GB')
        : Math.round(current);
      $el.text(display + suffix);
    }, interval);
  }

  // Fire counters when hero enters viewport
  if (typeof IntersectionObserver !== 'undefined') {
    const io = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting) {
        $('.stat-num[data-target]').each(function () {
          animateCounter($(this));
        });
        io.disconnect();
      }
    }, { threshold: 0.4 });

    if ($('#hero').length) io.observe($('#hero')[0]);
  }


  /* =============================================
     5. MOBILE NAV — close collapse on link click
     ============================================= */
  $('#navbarContent .nav-link:not(.mega-trigger)').on('click', function () {
    if ($(window).width() < 992) {
      const bsCollapse = bootstrap.Collapse.getInstance(
        document.getElementById('navbarContent')
      );
      if (bsCollapse) bsCollapse.hide();
    }
  });


  /* =============================================
     6. HERO IMAGE COLLAGE — subtle mouse parallax
     ============================================= */
  if ($(window).width() > 991) {
    let ticking = false;

    $(document).on('mousemove', function (e) {
      if (ticking) return;
      ticking = true;

      requestAnimationFrame(function () {
        const xRatio = e.clientX / $(window).width()  - 0.5;
        const yRatio = e.clientY / $(window).height() - 0.5;

        $('.hero-img-main img').css('transform',
          `scale(1.04) translate(${xRatio * -6}px, ${yRatio * -4}px)`
        );

        $('.hero-float-card').css('transform',
          `translateY(${Math.sin(Date.now() / 2000) * 8}px) translate(${xRatio * 8}px, ${yRatio * 5}px)`
        );

        $('.hero-delivery-badge').css('transform',
          `translateY(${Math.sin(Date.now() / 2200 + 1) * 8}px) translate(${xRatio * -6}px, ${yRatio * 4}px)`
        );

        ticking = false;
      });
    });
  }

});




/* =============================================
   ABOUT US SECTION — about-section.js
   Scroll-triggered reveal animations
   ============================================= */

(function () {
  'use strict';

  function initAboutReveal() {
    // Elements to animate on scroll
    var targets = document.querySelectorAll(
      '#about .about-images, #about .about-content'
    );

    if (!targets.length) return;

    // Use IntersectionObserver if available
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            io.unobserve(entry.target); // fire once
          }
        });
      }, { threshold: 0.18 });

      targets.forEach(function (el) { io.observe(el); });

    } else {
      // Fallback: show immediately
      targets.forEach(function (el) { el.classList.add('in-view'); });
    }
  }

  // Run after DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAboutReveal);
  } else {
    initAboutReveal();
  }

})();






/* ============================================================
   CUSTOMIZER SECTION  —  customizer-section.js
   Vanilla JS — no dependencies
   ============================================================ */

(function () {
  'use strict';

  /* ── 1. Scroll-reveal for section columns ─────────────────── */
  function initCustReveal() {
    const targets = document.querySelectorAll('#customizer [data-reveal]');
    if (!targets.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('in-view');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.15 }
    );
    targets.forEach((el) => observer.observe(el));
  }

  /* ── 2. Staggered reveal for feature items ────────────────── */
  function initFeatureReveal() {
    const items = document.querySelectorAll('#customizer .cust-feat[data-reveal-item]');
    if (!items.length) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const index = Array.from(items).indexOf(entry.target);
            entry.target.style.transitionDelay = `${index * 0.12 + 0.3}s`;
            entry.target.style.transition =
              'opacity 0.55s var(--ease), transform 0.55s var(--ease)';
            entry.target.classList.add('in-view');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.2 }
    );
    items.forEach((el) => observer.observe(el));
  }

  /* ── 3. Interactive mock colour swatches ──────────────────── */
  function initSwatches() {
    const swatches = document.querySelectorAll('#customizer .mock-swatch');
    if (!swatches.length) return;

    // Map swatch background colours to t-shirt gradient stops
    const colourMap = {
      '#1a1a2e': ['#1a1a2e', '#0f0f1a'],
      '#2c3e50': ['#2c3e50', '#1a252f'],
      '#ee6023': ['#ee6023', '#c94e18'],
      '#f5f5f5': ['#e8e8e8', '#d0d0d0'],
      '#2d6a4f': ['#2d6a4f', '#1b4332'],
    };

    swatches.forEach((swatch) => {
      swatch.addEventListener('click', function () {
        swatches.forEach((s) => s.classList.remove('active'));
        this.classList.add('active');

        const bg = getComputedStyle(this).backgroundColor;
        // Convert to hex-ish for lookup — try direct style attribute
        const rawBg = this.style.background;
        const stops = colourMap[rawBg];
        if (!stops) return;

        const grad = document.getElementById('tshirtGrad');
        if (!grad) return;
        const stopEls = grad.querySelectorAll('stop');
        if (stopEls[0]) stopEls[0].style.stopColor = stops[0];
        if (stopEls[1]) stopEls[1].style.stopColor = stops[1];
      });
    });
  }

  /* ── 4. Interactive mock size buttons ─────────────────────── */
  function initSizes() {
    const sizes = document.querySelectorAll('#customizer .mock-size');
    sizes.forEach((size) => {
      size.addEventListener('click', function () {
        sizes.forEach((s) => s.classList.remove('active'));
        this.classList.add('active');
      });
    });
  }

  /* ── 5. Mock quantity counter ─────────────────────────────── */
  function initQtyCounter() {
    const ctrl = document.querySelector('#customizer .mock-qty-ctrl');
    if (!ctrl) return;

    const qtyEl = ctrl.querySelector('strong');
    const [minus, , plus] = ctrl.children;

    // Base price per unit (pence) — used to update estimate
    const unitPrice = 750; // £7.50

    function updatePrice(qty) {
      const priceEl = document.querySelector('#customizer .mock-price-val');
      if (!priceEl) return;
      const total = (qty * unitPrice) / 100;
      const pounds = Math.floor(total);
      const pence = Math.round((total - pounds) * 100)
        .toString()
        .padStart(2, '0');
      priceEl.innerHTML = `£${pounds}<small>.${pence}</small>`;
    }

    minus.addEventListener('click', () => {
      let qty = parseInt(qtyEl.textContent, 10);
      if (qty > 1) {
        qty -= 1;
        qtyEl.textContent = qty;
        updatePrice(qty);
      }
    });

    plus.addEventListener('click', () => {
      let qty = parseInt(qtyEl.textContent, 10);
      qty += 1;
      qtyEl.textContent = qty;
      updatePrice(qty);
    });
  }

  /* ── 6. Mock "Add to Cart" pulse feedback ─────────────────── */
  function initAddToCart() {
    const btn = document.querySelector('#customizer .mock-add-btn');
    if (!btn) return;

    btn.addEventListener('click', function () {
      const original = this.innerHTML;
      this.innerHTML = '<i class="bi bi-check-lg"></i> Added!';
      this.style.background = '#27c93f';
      setTimeout(() => {
        this.innerHTML = original;
        this.style.background = '';
      }, 1800);
    });
  }

  /* ── 7. Subtle parallax on mockup card (desktop only) ─────── */
  function initMockParallax() {
    if (window.matchMedia('(max-width: 991.98px)').matches) return;
    const visual = document.querySelector('#customizer .cust-visual');
    if (!visual) return;

    document.addEventListener('mousemove', (e) => {
      const cx = window.innerWidth / 2;
      const cy = window.innerHeight / 2;
      const dx = (e.clientX - cx) / cx;
      const dy = (e.clientY - cy) / cy;

      const card = visual.querySelector('.cust-mockup-card');
      if (card) {
        card.style.transform = `
          perspective(1000px)
          rotateY(${dx * 3}deg)
          rotateX(${-dy * 2}deg)
          translateY(var(--hover-y, 0px))
        `;
      }
    });

    // Reset on leave
    document.addEventListener('mouseleave', () => {
      const card = visual.querySelector('.cust-mockup-card');
      if (card) card.style.transform = '';
    });
  }

  /* ── Init ─────────────────────────────────────────────────── */
  function init() {
    initCustReveal();
    initFeatureReveal();
    initSwatches();
    initSizes();
    initQtyCounter();
    initAddToCart();
    initMockParallax();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();








/* ============================================================
   CATALOGUE SECTION  —  catalogue-section.js
   Vanilla JS slider — no dependencies
   Features: responsive card width, autoplay (3s), dots,
             prev/next arrows, touch/mouse drag, pause on hover
   ============================================================ */

(function () {
  'use strict';

  /* ── Config ─────────────────────────────────────────────── */
  const AUTOPLAY_MS   = 3000;   // slide interval
  const GAP_PX        = 20;     // must match CSS gap
  const SIDE_PADDING  = 60;     // must match CSS track side padding (desktop)
  const SIDE_PAD_TAB  = 28;     // tablet
  const SIDE_PAD_MOB  = 18;     // mobile

  const TOTAL_CARDS   = 6;

  /* ── Elements ────────────────────────────────────────────── */
  const viewport  = document.getElementById('catViewport');
  const track     = document.getElementById('catTrack');
  const dotsWrap  = document.getElementById('catDots');
  const prevBtn   = document.querySelector('#catalogue .cat-arrow-prev');
  const nextBtn   = document.querySelector('#catalogue .cat-arrow-next');

  if (!viewport || !track) return;

  /* ── State ───────────────────────────────────────────────── */
  let currentIndex  = 0;
  let slidesVisible = 3;       // recalculated on resize
  let cardWidth     = 0;       // recalculated on resize
  let maxIndex      = 0;
  let autoTimer     = null;
  let isPaused      = false;

  /* ── Dots ────────────────────────────────────────────────── */
  function buildDots() {
    dotsWrap.innerHTML = '';
    const dotCount = maxIndex + 1;
    for (let i = 0; i < dotCount; i++) {
      const btn = document.createElement('button');
      btn.className = 'cat-dot' + (i === 0 ? ' active' : '');
      btn.setAttribute('role', 'tab');
      btn.setAttribute('aria-label', `Go to slide ${i + 1}`);
      btn.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(btn);
    }
  }

  function updateDots() {
    const dots = dotsWrap.querySelectorAll('.cat-dot');
    dots.forEach((d, i) => {
      d.classList.toggle('active', i === currentIndex);
    });
  }

  /* ── Card / layout metrics ───────────────────────────────── */
  function getSidePadding() {
    const w = window.innerWidth;
    if (w <= 575) return SIDE_PAD_MOB;
    if (w <= 991) return SIDE_PAD_TAB;
    return SIDE_PADDING;
  }

  function getSlidesVisible() {
    const w = window.innerWidth;
    if (w <= 575)  return 1.15;   // peek next card on mobile
    if (w <= 768)  return 2.1;
    if (w <= 991)  return 2.5;
    if (w <= 1199) return 3;
    return 3.5;                   // peek on large screens
  }

  function recalc() {
    const sidePad = getSidePadding();
    slidesVisible = getSlidesVisible();

    const fullVisible = Math.floor(slidesVisible);
    const trackInnerW = viewport.offsetWidth - sidePad * 2;
    cardWidth = (trackInnerW - GAP_PX * (fullVisible - 1)) / slidesVisible;

    // Apply card width to all cards
    const cards = track.querySelectorAll('.cat-card');
    cards.forEach(c => {
      c.style.setProperty('--cat-card-w', cardWidth + 'px');
      c.style.flex = `0 0 ${cardWidth}px`;
    });

    maxIndex = Math.max(0, TOTAL_CARDS - Math.floor(slidesVisible));

    // Clamp current index
    if (currentIndex > maxIndex) currentIndex = maxIndex;

    buildDots();
    updateDots();
    applyTranslate(false); // instant on resize
  }

  /* ── Translation ─────────────────────────────────────────── */
  function applyTranslate(animate = true) {
    const offset = currentIndex * (cardWidth + GAP_PX);
    track.style.transition = animate
      ? 'transform 0.52s cubic-bezier(0.4, 0, 0.2, 1)'
      : 'none';
    track.style.transform = `translateX(-${offset}px)`;
    updateDots();
    updateArrows();
  }

  function updateArrows() {
    if (!prevBtn || !nextBtn) return;
    prevBtn.classList.toggle('disabled', currentIndex === 0);
    nextBtn.classList.toggle('disabled', currentIndex >= maxIndex);
  }

  /* ── Navigation ──────────────────────────────────────────── */
  function goTo(index) {
    currentIndex = Math.max(0, Math.min(index, maxIndex));
    applyTranslate(true);
    resetAutoplay();
  }

  function next() { goTo(currentIndex < maxIndex ? currentIndex + 1 : 0); }
  function prev() { goTo(currentIndex > 0       ? currentIndex - 1 : maxIndex); }

  /* ── Autoplay ────────────────────────────────────────────── */
  function startAutoplay() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => {
      if (!isPaused) next();
    }, AUTOPLAY_MS);
  }

  function resetAutoplay() {
    clearInterval(autoTimer);
    startAutoplay();
  }

  /* ── Pause on hover / focus ──────────────────────────────── */
  viewport.addEventListener('mouseenter', () => { isPaused = true; });
  viewport.addEventListener('mouseleave', () => { isPaused = false; });
  viewport.addEventListener('focusin',    () => { isPaused = true; });
  viewport.addEventListener('focusout',   () => { isPaused = false; });

  /* ── Arrow buttons ───────────────────────────────────────── */
  if (prevBtn) prevBtn.addEventListener('click', prev);
  if (nextBtn) nextBtn.addEventListener('click', next);

  /* ── Keyboard ────────────────────────────────────────────── */
  document.addEventListener('keydown', (e) => {
    const section = document.getElementById('catalogue');
    if (!section) return;
    const rect = section.getBoundingClientRect();
    if (rect.top > window.innerHeight || rect.bottom < 0) return;
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft')  prev();
  });

  /* ── Touch / Mouse drag ──────────────────────────────────── */
  let dragStartX    = 0;
  let dragCurrentX  = 0;
  let isDragging    = false;
  let dragStartTime = 0;

  function onDragStart(clientX) {
    isDragging    = true;
    dragStartX    = clientX;
    dragCurrentX  = clientX;
    dragStartTime = Date.now();
    isPaused      = true;
    viewport.classList.add('is-dragging');
    track.style.transition = 'none';
  }

  function onDragMove(clientX) {
    if (!isDragging) return;
    dragCurrentX = clientX;
    const delta  = dragCurrentX - dragStartX;
    const base   = currentIndex * (cardWidth + GAP_PX);
    track.style.transform = `translateX(${-(base - delta)}px)`;
  }

  function onDragEnd() {
    if (!isDragging) return;
    isDragging = false;
    isPaused   = false;
    viewport.classList.remove('is-dragging');

    const delta    = dragCurrentX - dragStartX;
    const duration = Date.now() - dragStartTime;
    const isFlick  = Math.abs(delta) > 40 || (Math.abs(delta) > 20 && duration < 300);

    if (isFlick) {
      if (delta < 0) next();
      else           prev();
    } else {
      applyTranslate(true); // snap back
    }
    resetAutoplay();
  }

  // Mouse
  viewport.addEventListener('mousedown',  (e) => onDragStart(e.clientX));
  window .addEventListener('mousemove',   (e) => { if (isDragging) onDragMove(e.clientX); });
  window .addEventListener('mouseup',     ()  => onDragEnd());

  // Touch
  viewport.addEventListener('touchstart', (e) => onDragStart(e.touches[0].clientX), { passive: true });
  viewport.addEventListener('touchmove',  (e) => onDragMove(e.touches[0].clientX),  { passive: true });
  viewport.addEventListener('touchend',   ()  => onDragEnd());

  /* ── Prevent click-through on drag ──────────────────────── */
  track.addEventListener('click', (e) => {
    if (Math.abs(dragCurrentX - dragStartX) > 6) {
      e.preventDefault();
      e.stopPropagation();
    }
  }, true);

  /* ── Resize ──────────────────────────────────────────────── */
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(recalc, 120);
  });

  /* ── Intersection Observer — pause when off-screen ───────── */
  const sectionEl = document.getElementById('catalogue');
  if (sectionEl && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      ([entry]) => { isPaused = !entry.isIntersecting; },
      { threshold: 0.1 }
    );
    io.observe(sectionEl);
  }

  /* ── Scroll reveal for header ────────────────────────────── */
  function initHeaderReveal() {
    const header = document.querySelector('#catalogue .cat-header');
    if (!header || !('IntersectionObserver' in window)) return;
    header.style.opacity = '0';
    header.style.transform = 'translateY(24px)';
    header.style.transition = 'opacity 0.65s ease, transform 0.65s ease';

    const io = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        header.style.opacity = '1';
        header.style.transform = 'none';
        io.unobserve(header);
      }
    }, { threshold: 0.2 });
    io.observe(header);
  }

  /* ── Init ────────────────────────────────────────────────── */
  function init() {
    recalc();
    startAutoplay();
    initHeaderReveal();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();






/* ============================================================
   FEATURED PRODUCTS SECTION  —  featured-products.js
   Vanilla JS — scroll reveal with stagger
   ============================================================ */

(function () {
  'use strict';

  if (!('IntersectionObserver' in window)) {
    // Fallback: just show everything
    document.querySelectorAll('#featured-products [data-fp-reveal]')
      .forEach(el => el.classList.add('fp-in-view'));
    return;
  }

  /* ── Staggered reveal ─────────────────────────────────────── */
  const reveals = document.querySelectorAll('#featured-products [data-fp-reveal]');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;

      const el    = entry.target;
      const delay = el.dataset.fpDelay ? parseFloat(el.dataset.fpDelay) * 0.14 : 0;

      el.style.transitionDelay = `${delay}s`;
      el.classList.add('fp-in-view');
      observer.unobserve(el);
    });
  }, { threshold: 0.12 });

  reveals.forEach(el => observer.observe(el));

})();






/* ============================================================
   VIDEO SECTION  —  video-section.js
   Scroll reveal + ensure video plays when scrolled into view
   ============================================================ */

(function () {
  'use strict';

  /* ── Scroll reveal ────────────────────────────────────────── */
  const revealEls = document.querySelectorAll('#video-section [data-vs-reveal]');

  if ('IntersectionObserver' in window && revealEls.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('vs-in-view');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    revealEls.forEach(el => observer.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('vs-in-view'));
  }

  /* ── Ensure autoplay resumes when scrolled into view ─────── */
  const video = document.querySelector('#video-section video');
  if (video && 'IntersectionObserver' in window) {
    const videoObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          video.play().catch(() => {});
        } else {
          video.pause();
        }
      });
    }, { threshold: 0.25 });

    videoObserver.observe(video);
  }

})();







/* ============================================================
   TESTIMONIALS SECTION  —  testimonials-section.js
   Vanilla JS · 3-visible slider · autoplay 4s · drag/swipe
   ============================================================ */

(function () {
  'use strict';

  const AUTOPLAY_MS  = 4000;
  const GAP_PX       = 22;
  const TOTAL_CARDS  = 6;
  const VISIBLE      = 3;      // cards shown at once on desktop

  const viewport  = document.getElementById('tmViewport');
  const track     = document.getElementById('tmTrack');
  const dotsWrap  = document.getElementById('tmDots');
  const prevBtn   = document.querySelector('#testimonials .tm-arrow--prev');
  const nextBtn   = document.querySelector('#testimonials .tm-arrow--next');

  if (!viewport || !track) return;

  let currentIndex = 0;
  let cardWidth    = 0;
  let visibleCount = VISIBLE;
  let maxIndex     = 0;
  let autoTimer    = null;
  let isPaused     = false;

  /* ── Card width calc ──────────────────────────────────────── */
  function getVisibleCount() {
    const w = window.innerWidth;
    if (w <= 575)  return 1;
    if (w <= 850)  return 1.9;
    if (w <= 991)  return 2;
    return 3;
  }

  function recalc() {
    visibleCount = getVisibleCount();
    const fullVisible = Math.floor(visibleCount);
    const containerW  = viewport.offsetWidth;
    cardWidth = (containerW - GAP_PX * (fullVisible - 1)) / visibleCount;

    const cards = track.querySelectorAll('.tm-card');
    cards.forEach(c => {
      c.style.flex = `0 0 ${cardWidth}px`;
    });

    maxIndex = Math.max(0, TOTAL_CARDS - Math.floor(visibleCount));
    if (currentIndex > maxIndex) currentIndex = maxIndex;

    buildDots();
    applyTranslate(false);
  }

  /* ── Dots ─────────────────────────────────────────────────── */
  function buildDots() {
    dotsWrap.innerHTML = '';
    for (let i = 0; i <= maxIndex; i++) {
      const btn = document.createElement('button');
      btn.className = 'tm-dot' + (i === currentIndex ? ' active' : '');
      btn.setAttribute('aria-label', `Go to slide ${i + 1}`);
      btn.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(btn);
    }
  }

  function updateDots() {
    dotsWrap.querySelectorAll('.tm-dot').forEach((d, i) =>
      d.classList.toggle('active', i === currentIndex)
    );
  }

  /* ── Translate ────────────────────────────────────────────── */
  function applyTranslate(animate = true) {
    const offset = currentIndex * (cardWidth + GAP_PX);
    track.style.transition = animate
      ? 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)'
      : 'none';
    track.style.transform = `translateX(-${offset}px)`;
    updateDots();
    updateArrows();
  }

  function updateArrows() {
    if (!prevBtn || !nextBtn) return;
    prevBtn.classList.toggle('tm-disabled', currentIndex === 0);
    nextBtn.classList.toggle('tm-disabled', currentIndex >= maxIndex);
  }

  /* ── Navigation ───────────────────────────────────────────── */
  function goTo(i) {
    currentIndex = Math.max(0, Math.min(i, maxIndex));
    applyTranslate(true);
    resetAutoplay();
  }
  function next() { goTo(currentIndex < maxIndex ? currentIndex + 1 : 0); }
  function prev() { goTo(currentIndex > 0 ? currentIndex - 1 : maxIndex); }

  /* ── Autoplay ─────────────────────────────────────────────── */
  function startAutoplay() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => { if (!isPaused) next(); }, AUTOPLAY_MS);
  }
  function resetAutoplay() {
    clearInterval(autoTimer);
    startAutoplay();
  }

  viewport.addEventListener('mouseenter', () => { isPaused = true; });
  viewport.addEventListener('mouseleave', () => { isPaused = false; });

  /* ── Arrow buttons ────────────────────────────────────────── */
  if (prevBtn) prevBtn.addEventListener('click', prev);
  if (nextBtn) nextBtn.addEventListener('click', next);

  /* ── Drag / swipe ─────────────────────────────────────────── */
  let dragStartX = 0, dragCurrentX = 0, isDragging = false, dragStartTime = 0;

  function onDragStart(x) {
    isDragging = true; dragStartX = x; dragCurrentX = x;
    dragStartTime = Date.now(); isPaused = true;
    viewport.classList.add('is-dragging');
    track.style.transition = 'none';
  }
  function onDragMove(x) {
    if (!isDragging) return;
    dragCurrentX = x;
    const base  = currentIndex * (cardWidth + GAP_PX);
    track.style.transform = `translateX(${-(base - (x - dragStartX))}px)`;
  }
  function onDragEnd() {
    if (!isDragging) return;
    isDragging = false; isPaused = false;
    viewport.classList.remove('is-dragging');
    const delta    = dragCurrentX - dragStartX;
    const elapsed  = Date.now() - dragStartTime;
    const isFlick  = Math.abs(delta) > 40 || (Math.abs(delta) > 20 && elapsed < 300);
    if (isFlick) { delta < 0 ? next() : prev(); }
    else { applyTranslate(true); }
    resetAutoplay();
  }

  viewport.addEventListener('mousedown',  e => onDragStart(e.clientX));
  window .addEventListener('mousemove',   e => { if (isDragging) onDragMove(e.clientX); });
  window .addEventListener('mouseup',     ()  => onDragEnd());
  viewport.addEventListener('touchstart', e => onDragStart(e.touches[0].clientX), { passive: true });
  viewport.addEventListener('touchmove',  e => onDragMove(e.touches[0].clientX),  { passive: true });
  viewport.addEventListener('touchend',   ()  => onDragEnd());

  track.addEventListener('click', e => {
    if (Math.abs(dragCurrentX - dragStartX) > 6) {
      e.preventDefault(); e.stopPropagation();
    }
  }, true);

  /* ── Resize ───────────────────────────────────────────────── */
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(recalc, 120);
  });

  /* ── Intersection: pause off-screen ──────────────────────── */
  const section = document.getElementById('testimonials');
  if (section && 'IntersectionObserver' in window) {
    new IntersectionObserver(([e]) => { isPaused = !e.isIntersecting; }, { threshold: 0.1 })
      .observe(section);
  }

  /* ── Scroll reveal ────────────────────────────────────────── */
  const revealEls = document.querySelectorAll('#testimonials [data-tm-reveal]');
  if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) { e.target.classList.add('tm-in-view'); io.unobserve(e.target); }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('tm-in-view'));
  }

  /* ── Init ─────────────────────────────────────────────────── */
  function init() { recalc(); startAutoplay(); }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();







/* ============================================================
   PAGE LOADER  —  loader.js
   Simulates progress, syncs with window load, then exits
   ============================================================ */

(function () {
  'use strict';

  const loader   = document.getElementById('page-loader');
  const fill     = document.getElementById('loaderBarFill');
  const knot     = document.getElementById('loaderBarKnot');
  const pctLabel = document.getElementById('loaderPct');

  if (!loader) return;

  /* Lock scroll */
  document.body.classList.add('loader-active');

  let current  = 0;
  let target   = 0;
  let rafId    = null;
  let finished = false;

  /* ── Update bar visuals ─────────────────────────────────── */
  function setProgress(pct) {
    pct = Math.min(100, Math.max(0, pct));
    if (fill)     fill.style.width      = pct + '%';
    if (knot)     knot.style.left       = pct + '%';
    if (pctLabel) pctLabel.textContent  = Math.round(pct) + '%';
  }

  /* ── Smooth animation loop ──────────────────────────────── */
  function animateProgress() {
    if (current < target) {
      current += (target - current) * 0.06 + 0.3;
      if (current > target) current = target;
      setProgress(current);
    }
    if (!finished || current < 100) {
      rafId = requestAnimationFrame(animateProgress);
    } else {
      setProgress(100);
      exitLoader();
    }
  }

  /* ── Simulate incremental progress ─────────────────────── */
  function simulateProgress() {
    /* Phase 1: quick burst to ~30% */
    setTimeout(() => { target = 30; }, 100);
    /* Phase 2: slow crawl to ~65% */
    setTimeout(() => { target = 65; }, 600);
    /* Phase 3: stall near 85% — waiting for real load */
    setTimeout(() => { target = 85; }, 1400);
  }

  /* ── Exit ───────────────────────────────────────────────── */
  function exitLoader() {
    if (loader.dataset.exiting) return;
    loader.dataset.exiting = '1';

    target   = 100;
    current  = 100;
    setProgress(100);

    setTimeout(() => {
      loader.classList.add('loader-exit');
      document.body.classList.remove('loader-active');

      loader.addEventListener('transitionend', () => {
        loader.style.display = 'none';
      }, { once: true });

      /* Safety fallback */
      setTimeout(() => { loader.style.display = 'none'; }, 900);
    }, 320);
  }

  /* ── Hook to real page load ─────────────────────────────── */
  function onPageReady() {
    finished = true;
    target   = 100;
  }

  if (document.readyState === 'complete') {
    /* Already loaded (e.g. cached page) */
    target   = 100;
    finished = true;
  } else {
    window.addEventListener('load', onPageReady, { once: true });
    /* Safety: force finish after 4.5s regardless */
    setTimeout(onPageReady, 4500);
  }

  /* ── Start ──────────────────────────────────────────────── */
  simulateProgress();
  rafId = requestAnimationFrame(animateProgress);

})();






/* ============================================================
   GALLERY SECTION  —  gallery-section.js
   Auto-sliding gallery · 4 visible · zero-gap
   Lightbox with keyboard + swipe support
   ============================================================ */

(function () {
  'use strict';

  const AUTOPLAY_MS = 3000;
  const TOTAL       = 6;
  const VISIBLE_DEF = 4;

  /* ── Elements ────────────────────────────────────────────── */
  const viewport  = document.getElementById('glViewport');
  const track     = document.getElementById('glTrack');
  const dotsWrap  = document.getElementById('glDots');
  const prevBtn   = document.querySelector('#gallery .gl-arrow--prev');
  const nextBtn   = document.querySelector('#gallery .gl-arrow--next');
  const slides    = document.querySelectorAll('#gallery .gl-slide');

  if (!viewport || !track) return;

  /* ── State ───────────────────────────────────────────────── */
  let current    = 0;
  let slideW     = 0;
  let visible    = VISIBLE_DEF;
  let maxIndex   = 0;
  let autoTimer  = null;
  let isPaused   = false;

  /* ── Responsive visible count ────────────────────────────── */
  function getVisible() {
    const w = window.innerWidth;
    if (w <= 480)  return 1;
    if (w <= 767)  return 2;
    if (w <= 991)  return 3;
    return 4;
  }

  /* ── Recalculate metrics ─────────────────────────────────── */
  function recalc() {
    visible  = getVisible();
    slideW   = viewport.offsetWidth / visible;
    maxIndex = Math.max(0, TOTAL - visible);

    slides.forEach(s => {
      s.style.flex      = `0 0 ${slideW}px`;
      s.style.width     = `${slideW}px`;
      s.style.minWidth  = `${slideW}px`;
    });

    if (current > maxIndex) current = maxIndex;
    buildDots();
    applyTranslate(false);
  }

  /* ── Dots ────────────────────────────────────────────────── */
  function buildDots() {
    dotsWrap.innerHTML = '';
    const count = maxIndex + 1;
    for (let i = 0; i < count; i++) {
      const btn = document.createElement('button');
      btn.className   = 'gl-dot' + (i === current ? ' active' : '');
      btn.setAttribute('aria-label', `Slide ${i + 1}`);
      btn.addEventListener('click', () => goTo(i));
      dotsWrap.appendChild(btn);
    }
  }

  function updateDots() {
    dotsWrap.querySelectorAll('.gl-dot').forEach((d, i) =>
      d.classList.toggle('active', i === current)
    );
  }

  /* ── Translate ───────────────────────────────────────────── */
  function applyTranslate(animate = true) {
    track.style.transition = animate
      ? 'transform 0.55s cubic-bezier(0.4, 0, 0.2, 1)'
      : 'none';
    track.style.transform = `translateX(-${current * slideW}px)`;
    updateDots();
    updateArrows();
  }

  function updateArrows() {
    if (!prevBtn || !nextBtn) return;
    prevBtn.classList.toggle('gl-disabled', current === 0);
    nextBtn.classList.toggle('gl-disabled', current >= maxIndex);
  }

  /* ── Navigation ──────────────────────────────────────────── */
  function goTo(i) {
    current = Math.max(0, Math.min(i, maxIndex));
    applyTranslate(true);
    resetAutoplay();
  }
  function next() { goTo(current < maxIndex ? current + 1 : 0); }
  function prev() { goTo(current > 0 ? current - 1 : maxIndex); }

  /* ── Autoplay ────────────────────────────────────────────── */
  function startAutoplay() {
    clearInterval(autoTimer);
    autoTimer = setInterval(() => { if (!isPaused) next(); }, AUTOPLAY_MS);
  }
  function resetAutoplay() { clearInterval(autoTimer); startAutoplay(); }

  viewport.addEventListener('mouseenter', () => { isPaused = true; });
  viewport.addEventListener('mouseleave', () => { isPaused = false; });

  const section = document.getElementById('gallery');
  if (section && 'IntersectionObserver' in window) {
    new IntersectionObserver(([e]) => { isPaused = !e.isIntersecting; }, { threshold: 0.1 })
      .observe(section);
  }

  /* ── Arrow buttons ───────────────────────────────────────── */
  if (prevBtn) prevBtn.addEventListener('click', prev);
  if (nextBtn) nextBtn.addEventListener('click', next);

  /* ── Drag / swipe ────────────────────────────────────────── */
  let dragStartX = 0, dragCurrX = 0, isDragging = false, dragStartT = 0;

  function onDragStart(x) {
    isDragging = true; dragStartX = x; dragCurrX = x;
    dragStartT = Date.now(); isPaused = true;
    viewport.classList.add('is-dragging');
    track.style.transition = 'none';
  }
  function onDragMove(x) {
    if (!isDragging) return;
    dragCurrX = x;
    track.style.transform = `translateX(${-(current * slideW - (x - dragStartX))}px)`;
  }
  function onDragEnd() {
    if (!isDragging) return;
    isDragging = false; isPaused = false;
    viewport.classList.remove('is-dragging');
    const delta   = dragCurrX - dragStartX;
    const elapsed = Date.now() - dragStartT;
    const isFlick = Math.abs(delta) > 50 || (Math.abs(delta) > 25 && elapsed < 300);
    if (isFlick) { delta < 0 ? next() : prev(); }
    else { applyTranslate(true); }
    resetAutoplay();
  }

  viewport.addEventListener('mousedown',  e => onDragStart(e.clientX));
  window .addEventListener('mousemove',   e => { if (isDragging) onDragMove(e.clientX); });
  window .addEventListener('mouseup',     ()  => onDragEnd());
  viewport.addEventListener('touchstart', e => onDragStart(e.touches[0].clientX), { passive: true });
  viewport.addEventListener('touchmove',  e => onDragMove(e.touches[0].clientX),  { passive: true });
  viewport.addEventListener('touchend',   ()  => onDragEnd());

  /* Prevent link-drag interference */
  track.addEventListener('click', e => {
    if (Math.abs(dragCurrX - dragStartX) > 8) {
      e.preventDefault(); e.stopPropagation();
    }
  }, true);

  /* ── Resize ──────────────────────────────────────────────── */
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(recalc, 120);
  });


  /* ══════════════════════════════════════════════════════════
     LIGHTBOX
  ══════════════════════════════════════════════════════════ */
  const lightbox   = document.getElementById('glLightbox');
  const lbImg      = document.getElementById('glLbImg');
  const lbCounter  = document.getElementById('glLbCounter');
  const lbClose    = document.getElementById('glLbClose');
  const lbPrev     = document.getElementById('glLbPrev');
  const lbNext     = document.getElementById('glLbNext');
  const lbBackdrop = document.getElementById('glLbBackdrop');

  // Gather all images
  const imgData = Array.from(slides).map(slide => ({
    src: slide.querySelector('img').src,
    alt: slide.querySelector('img').alt,
  }));

  let lbCurrent = 0;

  function openLightbox(index) {
    lbCurrent = index;
    setLbImage(lbCurrent, false);
    lightbox.classList.add('open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    isPaused = true;
  }

  function closeLightbox() {
    lightbox.classList.remove('open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    isPaused = false;
  }

  function setLbImage(index, animate = true) {
    lbCurrent = ((index % TOTAL) + TOTAL) % TOTAL;
    if (animate) {
      lightbox.classList.add('changing');
      setTimeout(() => {
        lbImg.src = imgData[lbCurrent].src;
        lbImg.alt = imgData[lbCurrent].alt;
        lightbox.classList.remove('changing');
      }, 200);
    } else {
      lbImg.src = imgData[lbCurrent].src;
      lbImg.alt = imgData[lbCurrent].alt;
    }
    lbCounter.textContent = `${lbCurrent + 1} / ${TOTAL}`;
  }

  /* Click on slides to open */
  slides.forEach((slide, i) => {
    slide.addEventListener('click', () => openLightbox(i));
  });

  /* Controls */
  if (lbClose)    lbClose.addEventListener('click', closeLightbox);
  if (lbBackdrop) lbBackdrop.addEventListener('click', closeLightbox);
  if (lbPrev)     lbPrev.addEventListener('click', () => setLbImage(lbCurrent - 1));
  if (lbNext)     lbNext.addEventListener('click', () => setLbImage(lbCurrent + 1));

  /* Keyboard */
  document.addEventListener('keydown', e => {
    if (!lightbox.classList.contains('open')) return;
    if (e.key === 'ArrowRight') setLbImage(lbCurrent + 1);
    if (e.key === 'ArrowLeft')  setLbImage(lbCurrent - 1);
    if (e.key === 'Escape')     closeLightbox();
  });

  /* Touch swipe in lightbox */
  let lbTouchStartX = 0;
  lightbox.addEventListener('touchstart', e => { lbTouchStartX = e.touches[0].clientX; }, { passive: true });
  lightbox.addEventListener('touchend',   e => {
    const delta = e.changedTouches[0].clientX - lbTouchStartX;
    if (Math.abs(delta) > 50) { delta < 0 ? setLbImage(lbCurrent + 1) : setLbImage(lbCurrent - 1); }
  });


  /* ── Init ────────────────────────────────────────────────── */
  function init() { recalc(); startAutoplay(); }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

/* =============================================
   HEADER SEARCH — toggle + live suggestions
   ============================================= */
(function () {
  const toggle   = document.getElementById('navSearchToggle');
  const overlay  = document.getElementById('navSearchOverlay');
  const closeBtn = document.getElementById('navSearchClose');
  const input    = document.getElementById('navSearchInput');
  const suggestions = document.getElementById('navSearchSuggestions');

  if (!toggle || !overlay) return;

  const allSuggestions = window.__SEARCH_DATA__ || [];

  function openSearch() {
    overlay.classList.add('open');
    setTimeout(() => input && input.focus(), 350);
    document.body.style.overflow = 'hidden';
  }

  function closeSearch() {
    overlay.classList.remove('open');
    if (input) input.value = '';
    hideSuggestions();
    document.body.style.overflow = '';
  }

  function hideSuggestions() {
    if (suggestions) suggestions.classList.remove('visible');
  }

  function renderSuggestions(query) {
    if (!suggestions) return;
    const q = query.trim().toLowerCase();
    if (!q) { hideSuggestions(); return; }

    const matched = allSuggestions.filter(s => s.label.toLowerCase().includes(q));
    if (!matched.length) { hideSuggestions(); return; }

    const cats  = matched.filter(s => s.type === 'category');
    const prods = matched.filter(s => s.type === 'product');

    let html = '';
    if (cats.length) {
      html += '<div class="nss-group"><div class="nss-label">Categories</div>';
      cats.forEach(s => {
        html += `<a href="${s.href}" class="nss-item"><i class="bi bi-grid"></i> ${s.label}</a>`;
      });
      html += '</div>';
    }
    if (prods.length) {
      html += '<div class="nss-group"><div class="nss-label">Products</div>';
      prods.forEach(s => {
        html += `<a href="${s.href}" class="nss-item"><i class="bi bi-bag"></i> ${s.label}</a>`;
      });
      html += '</div>';
    }
    suggestions.innerHTML = html;
    suggestions.classList.add('visible');
  }

  toggle.addEventListener('click', openSearch);
  closeBtn.addEventListener('click', closeSearch);

  if (input) {
    input.addEventListener('input', () => renderSuggestions(input.value));
    input.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeSearch();
    });
  }

  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && overlay.classList.contains('open')) closeSearch();
  });

  overlay.addEventListener('click', e => {
    if (e.target === overlay) closeSearch();
  });
})();
