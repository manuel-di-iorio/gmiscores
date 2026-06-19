// Script to open and close the navbar
function w3_open() {
  document.getElementById("navbar").classList.add("navbar-visible");
  document.getElementById("overlay").style.display = "block";
}

function w3_close() {
  document.getElementById("navbar").classList.remove("navbar-visible");
  document.getElementById("overlay").style.display = "none";
}

// Modals
function openModal(modalId, onOpen, ctx) {
  var el = document.getElementById(modalId);
  el.style.display = "block";
  el.removeAttribute('data-armed');
  var btn = el.querySelector('.ui-destructive');
  if (btn) {
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
    btn.classList.remove('is-armed');
  }
  if (onOpen) onOpen(ctx);
}

function closeModal(modalId, onClose, ctx) {
  document.getElementById(modalId).style.display = 'none';
  if (onClose) onClose(ctx);
}

// Two-click destructive confirmation (capture phase to intercept before onclick)
document.addEventListener('click', function(e) {
  var btn = e.target.closest('.ui-destructive');
  if (!btn) return;

  var overlay = btn.closest('.ui-modal-overlay');
  if (!overlay) return;

  if (overlay.getAttribute('data-armed') === '1') {
    overlay.removeAttribute('data-armed');
    btn.classList.remove('is-armed');
    return;
  }

  e.stopPropagation();
  e.preventDefault();

  overlay.setAttribute('data-armed', '1');
  if (!btn.getAttribute('data-original-html')) {
    btn.setAttribute('data-original-html', btn.innerHTML);
  }
  btn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Conferma?';
  btn.classList.add('is-armed');

  setTimeout(function() {
    overlay.removeAttribute('data-armed');
    btn.innerHTML = btn.getAttribute('data-original-html') || btn.innerHTML;
    btn.classList.remove('is-armed');
  }, 5000);
}, true);

// Accordions
function toggleAccordion(el) {
  const elIcon = el.querySelector("i");
  direction = elIcon.className.includes("down") ? "up" : "down";
  elIcon.classList.remove("fa-arrow-circle-down");
  elIcon.classList.remove("fa-arrow-circle-up");
  elIcon.classList.add("fa-arrow-circle-" + direction);
  if (direction === "down") {
    el.nextElementSibling.style.display = "none";
  } else {
    el.nextElementSibling.style.display = "block";
  }
}

// ================================================================
// SCROLL PROGRESS BAR
// ================================================================
(function initScrollProgress() {
  const bar = document.getElementById('scroll-progress');
  if (!bar) return;
  window.addEventListener('scroll', () => {
    const h = document.documentElement;
    const total = h.scrollHeight - h.clientHeight;
    bar.style.width = (h.scrollTop / total * 100) + '%';
  }, { passive: true });
})();

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
      item.style.transitionDelay = (i * 0.08) + 's';
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
// COUNTER ANIMATION
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
  const themeToggle = document.querySelector('.theme-toggle-home');
  if (!header || !hero) return;

  const heroThreshold = hero.offsetHeight * 0.7;

  const scrollHandler = () => {
    if (window.scrollY > heroThreshold) {
      header.classList.add('is-visible');
      if (themeToggle) themeToggle.classList.add('theme-toggle-hidden');
    } else {
      header.classList.remove('is-visible');
      if (themeToggle) themeToggle.classList.remove('theme-toggle-hidden');
    }
  };

  window.addEventListener('scroll', scrollHandler, { passive: true });
  scrollHandler();
})();

// ================================================================
// 3D TILT ON CARDS
// ================================================================
(function initTiltCards() {
  const cards = document.querySelectorAll('.tilt-card');
  if (cards.length === 0) return;

  cards.forEach(card => {
    card.addEventListener('mousemove', (e) => {
      const rect = card.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const centerX = rect.width / 2;
      const centerY = rect.height / 2;
      const rotateX = (y - centerY) / centerY * -10;
      const rotateY = (x - centerX) / centerX * 10;

      card.style.transition = 'transform 0.15s cubic-bezier(0.16,1,0.3,1)';
      card.style.transform = `perspective(800px) translateY(-8px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;

      const shine = card.querySelector('.tilt-card__shine');
      if (shine) {
        const pctX = (x / rect.width) * 100;
        const pctY = (y / rect.height) * 100;
        shine.style.setProperty('--mx', pctX + '%');
        shine.style.setProperty('--my', pctY + '%');
      }
    });

    card.addEventListener('mouseleave', () => {
      card.style.transition = 'transform 0.4s cubic-bezier(0.16,1,0.3,1)';
      card.style.transform = 'perspective(800px) translateY(0) rotateX(0deg) rotateY(0deg) scale(1)';
    });
  });
})();

// ================================================================
// BUTTON RIPPLE EFFECT
// ================================================================
(function initRippleButtons() {
  document.querySelectorAll('.ripple-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const rect = this.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'ripple-effect';
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
      ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
      this.appendChild(ripple);
      ripple.addEventListener('animationend', () => ripple.remove());
    });
  });
})();

// ================================================================
// FAQ ACCORDION (modern)
// ================================================================
(function initFAQ() {
  document.querySelectorAll('.faq-question').forEach(q => {
    q.addEventListener('click', function () {
      const item = this.parentElement;
      const isOpen = item.classList.contains('is-open');
      // Close all
      item.parentElement.querySelectorAll('.faq-item.is-open').forEach(open => {
        open.classList.remove('is-open');
      });
      if (!isOpen) {
        item.classList.add('is-open');
      }
    });
  });
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
// HERO PARTICLES
// ================================================================
(function initHeroParticles() {
  const container = document.getElementById('hero-particles');
  if (!container) return;

  const count = Math.min(25, Math.floor(window.innerWidth / 35));

  for (let i = 0; i < count; i++) {
    const particle = document.createElement('div');
    particle.className = 'hero-particle';
    const size = 2 + Math.random() * 3;
    particle.style.width = size + 'px';
    particle.style.height = size + 'px';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.animationDuration = (10 + Math.random() * 15) + 's';
    particle.style.animationDelay = (Math.random() * 12) + 's';
    particle.style.opacity = 0.15 + Math.random() * 0.35;
    container.appendChild(particle);
  }
})();

// ================================================================
// HERO PARALLAX ON SCROLL
// ================================================================
(function initHeroParallax() {
  const hero = document.querySelector('.HomeBanner');
  if (!hero) return;

  window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const maxScroll = window.innerHeight;
    if (scrolled <= maxScroll) {
      hero.style.backgroundPositionY = (scrolled * 0.35) + 'px';
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

// Tabs
document.addEventListener('click', function (e) {
  var btn = e.target.closest('.ui-tabs__btn');
  if (!btn) return;

  var tabsEl = btn.closest('.ui-tabs');
  if (!tabsEl) return;

  var tabId = btn.getAttribute('data-tab');
  if (!tabId) return;

  tabsEl.querySelectorAll('.ui-tabs__btn').forEach(function (b) {
    b.classList.remove('is-active');
    b.setAttribute('aria-selected', 'false');
  });
  btn.classList.add('is-active');
  btn.setAttribute('aria-selected', 'true');

  tabsEl.querySelectorAll('.ui-tabs__panel').forEach(function (p) {
    p.classList.remove('is-active');
  });
  var panel = tabsEl.querySelector('#panel-' + tabId);
  if (panel) {
    panel.classList.add('is-active');
    panel.dispatchEvent(new CustomEvent('tabshown', { bubbles: true }));

    var url = panel.getAttribute('data-url');
    if (url && panel.getAttribute('data-loaded') === 'false') {
      panel.setAttribute('data-loaded', 'loading');
      fetch(url)
        .then(function (r) { return r.text(); })
        .then(function (html) {
          panel.innerHTML = html;
          panel.setAttribute('data-loaded', 'true');
          panel.querySelectorAll('script').forEach(function (s) {
            var ns = document.createElement('script');
            if (s.src) { ns.src = s.src; } else { ns.textContent = s.textContent; }
            s.parentNode.replaceChild(ns, s);
          });
          panel.dispatchEvent(new CustomEvent('tabloaded', { bubbles: true }));
          tippy(panel.querySelectorAll('[data-tippy-content]'));
        })
        .catch(function () {
          panel.setAttribute('data-loaded', 'false');
        });
    }
  }
});
