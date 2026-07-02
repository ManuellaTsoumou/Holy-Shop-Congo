/* ==========================================================================
   HOLY SHOP CONGO — JS Premium
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {

  /* ── 1. NAVIGATION & BURGER ── */
  const burger = document.getElementById('burger');
  const nav    = document.getElementById('nav');
  const header = document.querySelector('.main-header');

  if (burger && nav) {
    const closeMenu = () => {
      nav.classList.remove('active');
      burger.classList.remove('toggle');
      document.body.style.overflow = '';
    };

    burger.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = nav.classList.toggle('active');
      burger.classList.toggle('toggle', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    nav.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));

    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && !burger.contains(e.target)) closeMenu();
    });

    // Swipe pour fermer sur mobile
    let touchStartX = 0;
    nav.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
    nav.addEventListener('touchend', (e) => {
      if (e.changedTouches[0].clientX - touchStartX > 60) closeMenu();
    }, { passive: true });
  }

  /* ── 2. HEADER SCROLL ── */
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 50);
    }, { passive: true });
  }

  /* ── 3. REVEAL AU SCROLL ── */
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
  );
  document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

  /* ── 4. STEPS PROCESS ── */
  const stepObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          stepObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15 }
  );
  document.querySelectorAll('.step').forEach(step => stepObserver.observe(step));

  /* ── 5. CALCULATEUR ── */
  const priceInput  = document.getElementById('price');
  const weightInput = document.getElementById('weight');
  const EUR_TO_XAF  = 665;

  const els = {
    buyPrice:   document.getElementById('buyPrice'),
    commission: document.getElementById('commission'),
    shipping:   document.getElementById('shipping'),
    totalEUR:   document.getElementById('totalEUR'),
    totalFCFA:  document.getElementById('totalFCFA'),
    accompte:   document.getElementById('accompte'),
    reste:      document.getElementById('reste'),
  };

  const allExist = Object.values(els).every(Boolean);

  function animateValue(el, start, end, duration, formatter) {
    if (!el) return;
    if (el._rafId) cancelAnimationFrame(el._rafId);
    let startTime = null;
    const step = (ts) => {
      if (!startTime) startTime = ts;
      const p = Math.min((ts - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = formatter(start + eased * (end - start));
      if (p < 1) el._rafId = requestAnimationFrame(step);
    };
    el._rafId = requestAnimationFrame(step);
  }

  const fmtEUR = v => `${v.toFixed(2)} €`;
  const fmtXAF = v => `${Math.round(v).toLocaleString('fr-FR')} FCFA`;

  function calculate() {
    if (!allExist) return;
    const price  = Math.max(0, parseFloat(priceInput?.value)  || 0);
    const weight = Math.max(0, parseFloat(weightInput?.value) || 0);
    const commission = price * 0.15;
    const shipping   = weight * 10.5;
    const totalEUR   = price + commission + shipping;
    const totalXAF   = totalEUR * EUR_TO_XAF;
    const D = 380;
    animateValue(els.buyPrice,   0, price,          D, fmtEUR);
    animateValue(els.commission, 0, commission,     D, fmtEUR);
    animateValue(els.shipping,   0, shipping,       D, fmtEUR);
    animateValue(els.totalEUR,   0, totalEUR,       D, fmtEUR);
    animateValue(els.totalFCFA,  0, totalXAF,       D, fmtXAF);
    animateValue(els.accompte,   0, totalXAF * 0.8, D, fmtXAF);
    animateValue(els.reste,      0, totalXAF * 0.2, D, fmtXAF);
  }

  if (priceInput)  priceInput.addEventListener('input', calculate);
  if (weightInput) weightInput.addEventListener('input', calculate);

  /* ── 6. MODAL NEWSLETTER ── */
  const overlay  = document.getElementById('newsletterOverlay');
  const closeBtn = document.getElementById('closeModal');
  const form     = document.getElementById('newsletterForm');

  const hideOverlay = () => overlay?.classList.add('hidden');

  closeBtn?.addEventListener('click', hideOverlay);
  overlay?.addEventListener('click', (e) => { if (e.target === overlay) hideOverlay(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hideOverlay(); });

  form?.addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.textContent = 'Bienvenue ✓'; btn.style.background = 'linear-gradient(135deg,#6ee7b7,#22c55e)'; btn.style.color = '#000'; }
    setTimeout(hideOverlay, 900);
  });

  /* ── 7. CAROUSEL PARTENAIRES ── */
  const track = document.getElementById('partnersTrack');
  if (track) {
    Array.from(track.children).forEach(child => track.appendChild(child.cloneNode(true)));
  }

  /* ── 8. STATS HERO ANIMÉES ── */
  const statsObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        entry.target.querySelectorAll('[data-count]').forEach(el => {
          const target   = parseFloat(el.dataset.count);
          const suffix   = el.dataset.suffix || '';
          animateValue(el, 0, target, 1200,
            v => `${Number.isInteger(target) ? Math.round(v) : v.toFixed(1)}${suffix}`
          );
        });
        statsObserver.unobserve(entry.target);
      });
    },
    { threshold: 0.5 }
  );
  const heroStats = document.querySelector('.hero-stats');
  if (heroStats) statsObserver.observe(heroStats);

  /* ── 9. FAQ : icône +/− dans le <summary> ── */
  // Insère dynamiquement le bouton icône dans chaque summary s'il n'existe pas
  document.querySelectorAll('.faq-question').forEach(summary => {
    if (!summary.querySelector('.faq-icon-btn')) {
      const icon = document.createElement('span');
      icon.className = 'faq-icon-btn';
      icon.setAttribute('aria-hidden', 'true');
      icon.textContent = '+';
      summary.appendChild(icon);
    }
  });

  // Met à jour l'icône à l'ouverture/fermeture
  document.querySelectorAll('.faq-item').forEach(details => {
    details.addEventListener('toggle', () => {
      const icon = details.querySelector('.faq-icon-btn');
      if (icon) icon.textContent = details.open ? '−' : '+';
    });
  });

  /* ── 10. NAV ACTIVE AU SCROLL ── */
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.main-nav a[href^="#"]');

  if (sections.length && navLinks.length) {
    const navObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            navLinks.forEach(link => {
              link.classList.toggle('active', link.getAttribute('href') === `#${entry.target.id}`);
            });
          }
        });
      },
      { rootMargin: '-40% 0px -55% 0px' }
    );
    sections.forEach(s => navObserver.observe(s));
  }

});