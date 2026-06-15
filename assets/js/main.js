// Script to open and close the navbar
function w3_open() {
  document.getElementById("navbar").style.display = "block";
  document.getElementById("overlay").style.display = "block";
}

function w3_close() {
  document.getElementById("navbar").style.display = "none";
  document.getElementById("overlay").style.display = "none";
}

// Modals
function openModal(modalId, onOpen, ctx) {
  document.getElementById(modalId).style.display = "block";
  if (onOpen) onOpen(ctx);
}

function closeModal(modalId, onClose, ctx) {
  document.getElementById(modalId).style.display = 'none';
  if (onClose) onClose(ctx);
}

// Accordions
function toggleAccordion(el) {
  const elIcon = el.querySelector("i");
  direction = elIcon.className.includes("down") ? "up" : "down";
  elIcon.classList.remove("fa-arrow-circle-down");
  elIcon.classList.remove("fa-arrow-circle-up");
  elIcon.classList.add("fa-arrow-circle-" + direction);
  if (direction === "down") {
    el.nextElementSibling.classList.add("w3-hide");
  } else {
    el.nextElementSibling.classList.remove("w3-hide");
  }
}

// ================================================================
// INTERSECTION OBSERVER — Multi-animation scroll reveal
// ================================================================
(function initScrollAnimations() {
  const animClasses = [
    'fade-in-up-on-scroll',
    'anim-fade-up',
    'anim-fade-down',
    'anim-fade-left',
    'anim-fade-right',
    'anim-zoom-in',
    'anim-scale-up',
    'section-hidden'
  ];

  const selector = animClasses.map(c => '.' + c).join(',');
  const elements = document.querySelectorAll(selector);

  if (elements.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });

  elements.forEach(el => observer.observe(el));
})();

// ================================================================
// STAGGERED GRID ANIMATION
// ================================================================
(function initStaggeredGrids() {
  document.querySelectorAll('.stagger-grid').forEach(grid => {
    const items = grid.querySelectorAll('.stagger-item');
    items.forEach((item, i) => {
      item.style.transitionDelay = (i * 0.1) + 's';
    });

    const gridObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          gridObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    gridObserver.observe(grid);
  });
})();

// ================================================================
// COUNTER ANIMATION — Numbers count up on scroll
// ================================================================
(function initCounters() {
  const counters = document.querySelectorAll('.stat-number');
  if (counters.length === 0) return;

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.getAttribute('data-target'), 10) || 0;
        const duration = Math.min(2000, Math.max(800, target * 3));
        const startTime = performance.now();

        function update(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const current = Math.floor(eased * target);
          el.textContent = current.toLocaleString();
          if (progress < 1) {
            requestAnimationFrame(update);
          } else {
            el.textContent = target.toLocaleString();
          }
        }

        el.classList.add('counting');
        requestAnimationFrame(update);
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.3 });

  counters.forEach(c => counterObserver.observe(c));
})();

// ================================================================
// STICKY LANDING HEADER
// ================================================================
(function initLandingHeader() {
  const header = document.querySelector('.landing-header');
  const hero = document.querySelector('.HomeBanner');
  if (!header || !hero) return;

  const heroThreshold = hero.offsetHeight * 0.7;

  const scrollHandler = () => {
    if (window.scrollY > heroThreshold) {
      header.classList.add('is-visible');
    } else {
      header.classList.remove('is-visible');
    }
  };

  window.addEventListener('scroll', scrollHandler, { passive: true });
  scrollHandler();
})();

// ================================================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ================================================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const hrefAttribute = this.getAttribute('href');
    if (hrefAttribute && hrefAttribute.length > 1 && document.querySelector(hrefAttribute)) {
      e.preventDefault();
      const target = document.querySelector(hrefAttribute);
      const offset = 80;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: 'smooth' });
    }
  });
});

// ================================================================
// HERO PARTICLES (lightweight canvas-free)
// ================================================================
(function initHeroParticles() {
  const container = document.getElementById('hero-particles');
  if (!container) return;

  const count = Math.min(30, Math.floor(window.innerWidth / 30));

  for (let i = 0; i < count; i++) {
    const particle = document.createElement('div');
    particle.className = 'hero-particle';
    const size = 2 + Math.random() * 4;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDuration = (8 + Math.random() * 12) + 's';
    particle.style.animationDelay = (Math.random() * 10) + 's';
    particle.style.opacity = 0.2 + Math.random() * 0.5;
    container.appendChild(particle);
  }
})();

// ================================================================
// PARALLAX ENHANCEMENT FOR HERO
// ================================================================
(function initHeroParallax() {
  const hero = document.querySelector('.HomeBanner');
  if (!hero) return;

  window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const maxScroll = window.innerHeight;
    if (scrolled <= maxScroll) {
      const progress = scrolled / maxScroll;
      hero.style.backgroundPositionY = (progress * 30) + 'px';
    }
  }, { passive: true });
})();

// ================================================================
// COOKIE CONSENT
// ================================================================
window.addEventListener("load", () => {
  const cookieBanner = document.getElementById('cookie-banner');
  const acceptCookieBannerButton = document.getElementById('accept-cookie-banner');

  if (localStorage.getItem('cookieConsentAccepted') === 'true') {
    cookieBanner.style.display = 'none';
  } else {
    cookieBanner.style.display = 'flex';
  }

  acceptCookieBannerButton.addEventListener('click', () => {
    cookieBanner.style.display = 'none';
    localStorage.setItem('cookieConsentAccepted', 'true');
  });
});
