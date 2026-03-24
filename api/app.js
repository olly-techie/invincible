/**
 * ============================================================
 *  Invincible Studio — app.js
 *  React application loaded via Babel CDN (no build step).
 *
 *  HOW THIS FILE WORKS (for beginners):
 *  ------------------------------------------------------------
 *  • This file uses JSX — an HTML-like syntax inside JavaScript.
 *    Babel (loaded in index.php) compiles JSX into plain JS in
 *    the browser so no build tool (Webpack/Vite) is needed.
 *
 *  • React.useState()  → stores component-level reactive data
 *  • React.useEffect() → runs code after the component renders
 *    (similar to DOMContentLoaded in vanilla JS)
 *
 *  • Every function that starts with a capital letter is a
 *    React Component — a reusable piece of UI.
 *
 *  FILE STRUCTURE:
 *  1.  Shared SVG icon components
 *  2.  useScrollReveal hook (IntersectionObserver)
 *  3.  Navbar
 *  4.  Ticker
 *  5.  Hero
 *  6.  Services
 *  7.  About
 *  8.  Work
 *  9.  Testimonials
 *  10. CTABanner
 *  11. Contact
 *  12. Footer
 *  13. App (root — assembles all sections)
 *  14. ReactDOM.createRoot — mounts App into #root
 * ============================================================
 */

/* Destructure hooks from the global React object (loaded via CDN).
   In a build-tool project you'd write: import { useState } from 'react' */
const { useState, useEffect } = React;


/* ============================================================
   SECTION 1 — SVG ICON COMPONENTS
   Each icon is a tiny React component that returns one <svg>.
   Using components keeps JSX readable — <IconArrowRight />
   is much cleaner than 5 lines of raw SVG inline everywhere.

   All icons use:
   • currentColor → inherits text colour from the parent element
   • strokeLinecap / strokeLinejoin: "round" → softer, modern look
   • fill="none" + stroke → outline style icons
   ============================================================ */

/** → Arrow pointing right. Used in buttons. */
function IconArrowRight({ size = 16 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 16 16" fill="none" aria-hidden="true">
      <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** [mail] Envelope icon. Used in the contact info row. */
function IconMail({ size = 20 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" strokeWidth="1.5" />
      <path d="M2 7l10 7 10-7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** [pin] Map pin / location icon. Used in the contact info row. */
function IconMapPin({ size = 20 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M12 2C8.686 2 6 4.686 6 8c0 5.25 6 14 6 14s6-8.75 6-14c0-3.314-2.686-6-6-6z" stroke="currentColor" strokeWidth="1.5" />
      <circle cx="12" cy="8" r="2" stroke="currentColor" strokeWidth="1.5" />
    </svg>
  );
}

/** ⏱ Clock icon. Used in the contact info row for response time. */
function IconClock({ size = 20 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="12" r="9" stroke="currentColor" strokeWidth="1.5" />
      <path d="M12 7v5l3.5 3.5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** [zap] Lightning bolt. Used in the About "Fast Delivery" value card. */
function IconZap({ size = 22 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M13 2L4.5 13H12l-1 9 8.5-11H12l1-9z" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** [target] Target / crosshair. Used for "Pixel Perfect" value card. */
function IconTarget({ size = 22 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="12" r="9" stroke="currentColor" strokeWidth="1.5" />
      <circle cx="12" cy="12" r="5" stroke="currentColor" strokeWidth="1.5" />
      <circle cx="12" cy="12" r="1.5" fill="currentColor" />
    </svg>
  );
}

/** [lock] Padlock. Used for "Secure by Default" value card. */
function IconLock({ size = 22 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" strokeWidth="1.5" />
      <path d="M8 11V7a4 4 0 018 0v4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
      <circle cx="12" cy="16" r="1.5" fill="currentColor" />
    </svg>
  );
}

/** [chart] Trending up chart. Used for "Growth-Ready" value card. */
function IconTrendingUp({ size = 22 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
      <polyline points="17 6 23 6 23 12" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** [check] Checkmark circle. Used in the form success state. */
function IconCheckCircle({ size = 18 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5" />
      <path d="M7 12l3.5 3.5L17 9" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

/** [alert] Alert circle. Used in the form error state. */
function IconAlertCircle({ size = 18 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1.5" />
      <path d="M12 7v5" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" />
      <circle cx="12" cy="16.5" r="1" fill="currentColor" />
    </svg>
  );
}

/** ↺ Spinning loader circle. Used on the submit button while loading. */
function IconSpinner({ size = 18 }) {
  return (
    <svg
      width={size} height={size} viewBox="0 0 24 24" fill="none"
      style={{ animation: 'spin 0.8s linear infinite' }}
      aria-hidden="true"
    >
      <style>{`@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`}</style>
      <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2" strokeOpacity="0.25" />
      <path d="M12 2a10 10 0 0110 10" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
    </svg>
  );
}

/** X Twitter logo. Used in the Footer social links. */
function IconTwitterX({ size = 16 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
    </svg>
  );
}

/** LinkedIn logo. Used in the Footer social links. */
function IconLinkedIn({ size = 16 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
    </svg>
  );
}

/** GitHub octocat logo. Used in the Footer social links. */
function IconGitHub({ size = 16 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
      <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
    </svg>
  );
}

/** [star] Filled star. Used in Testimonial star ratings. */
function IconStar({ size = 14 }) {
  return (
    <svg width={size} height={size} viewBox="0 0 14 14" fill="currentColor" aria-hidden="true">
      <polygon points="7 1 8.8 4.9 13 5.5 10 8.4 10.7 12.6 7 10.7 3.3 12.6 4 8.4 1 5.5 5.2 4.9" />
    </svg>
  );
}

/** ◇ Diamond dot separator used in the Ticker marquee. */
function IconDot() {
  return (
    <svg width="6" height="6" viewBox="0 0 6 6" fill="none" aria-hidden="true">
      <rect x="1" y="1" width="4" height="4" rx="1" fill="#6ee7f7" transform="rotate(45 3 3)" />
    </svg>
  );
}

/* Service card SVG icons — inlined as components so they
   can use currentColor and are easy to update individually */

function IconLayers() { /* UI/UX Design */
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <rect x="3"  y="3"  width="7" height="7" rx="1.5" stroke="#6ee7f7" strokeWidth="1.5" />
      <rect x="14" y="3"  width="7" height="7" rx="1.5" stroke="#6ee7f7" strokeWidth="1.5" />
      <rect x="3"  y="14" width="7" height="7" rx="1.5" stroke="#6ee7f7" strokeWidth="1.5" />
      <path d="M17.5 14v7M14 17.5h7" stroke="#a3e635" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  );
}

function IconCode() { /* Web Development */
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <polyline points="16 18 22 12 16 6" stroke="#6ee7f7" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
      <polyline points="8 6 2 12 8 18"   stroke="#6ee7f7" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
      <line x1="12" y1="4" x2="12" y2="20" stroke="#a3e635" strokeWidth="1.5" strokeLinecap="round" strokeDasharray="2 3" />
    </svg>
  );
}

function IconCpu() { /* AI Integration */
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <rect x="7" y="7" width="10" height="10" rx="1" stroke="#6ee7f7" strokeWidth="1.5" />
      <path d="M9 3v4M15 3v4M9 17v4M15 17v4M3 9h4M17 9h4M3 15h4M17 15h4" stroke="#a3e635" strokeWidth="1.5" strokeLinecap="round" />
      <path d="M10 11.5h1.5l1 1.5-1.5.5" stroke="#6ee7f7" strokeWidth="1" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}

function IconSmartphone() { /* Mobile Apps */
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <rect x="5" y="2" width="14" height="20" rx="3" stroke="#6ee7f7" strokeWidth="1.5" />
      <circle cx="12" cy="17" r="1.5" fill="#a3e635" />
      <line x1="8" y1="6"  x2="16" y2="6"  stroke="#6ee7f7" strokeWidth="1.5" strokeLinecap="round" />
      <line x1="8" y1="10" x2="16" y2="10" stroke="#6ee7f7" strokeWidth="1.5" strokeLinecap="round" strokeOpacity="0.5" />
    </svg>
  );
}

function IconPenTool() { /* Brand Identity */
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M12 2l3 7H21l-6 4.5 2 7-5-3.5-5 3.5 2-7L3 9h6l3-7z" stroke="#6ee7f7" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
      <circle cx="12" cy="12" r="1.5" fill="#a3e635" />
    </svg>
  );
}

function IconActivity() { /* Performance & SEO */
  return (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <polyline points="22 12 18 12 15 21 9 3 6 12 2 12" stroke="#6ee7f7" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
    </svg>
  );
}


/* ============================================================
   SECTION 2 — CUSTOM HOOK: useScrollReveal
   ============================================================
   A "hook" is a special React function that lets you plug into
   React features (like running side effects) from within a
   component. Hooks must always start with "use".

   This hook uses the browser's IntersectionObserver API to
   watch every element with class="reveal". When one enters the
   viewport, the hook adds class="visible" to it, which triggers
   the CSS transition defined in style.css (.reveal.visible).

   useEffect(() => { ... }, []) runs ONCE after first render.
   The empty [] dependency array means "don't re-run this".
   The returned cleanup function disconnects the observer when
   the component unmounts (good practice, prevents memory leaks).
   ============================================================ */
function useScrollReveal() {
  useEffect(() => {
    // Grab every element that should animate on scroll
    const elements = document.querySelectorAll('.reveal');

    // threshold: 0.12 means "trigger when 12% of the element is visible"
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible'); // trigger CSS transition
            observer.unobserve(entry.target);      // stop watching — we only animate once
          }
        });
      },
      { threshold: 0.12 }
    );

    elements.forEach((el) => observer.observe(el));

    // Cleanup: called when this component unmounts from the DOM
    return () => observer.disconnect();
  }, []); // empty array = run once on mount
}


/* ============================================================
   SHARED HELPER
   smoothTo(selector) scrolls to a CSS selector smoothly.
   Centralising it avoids repeating the same three lines.
   ============================================================ */
const smoothTo = (selector) => {
  document.querySelector(selector)?.scrollIntoView({ behavior: 'smooth' });
};


/* ============================================================
   SECTION 3 — NAVBAR COMPONENT
   ============================================================
   State:
   • scrolled   → true once user scrolls past 40px, triggers
                  the frosted-glass look via CSS class .scrolled
   • menuOpen   → controls the mobile hamburger menu visibility

   useEffect adds/removes a 'scroll' event listener.
   The cleanup function (returned from useEffect) removes the
   listener when the component unmounts — prevents memory leaks.
   ============================================================ */
function Navbar() {
  const [scrolled,  setScrolled]  = useState(false);
  const [menuOpen,  setMenuOpen]  = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll); // cleanup
  }, []);

  // Nav link data — centralised so adding a new link = one line change
  const links = [
    { label: 'Services',     href: '#services'     },
    { label: 'About',        href: '#about'         },
    { label: 'Work',         href: '#work'          },
    { label: 'Testimonials', href: '#testimonials'  },
    { label: 'Contact',      href: '#contact'       },
  ];

  // Handles clicks on any nav link
  const handleNavClick = (e, href) => {
    e.preventDefault();       // stop browser default anchor jump
    setMenuOpen(false);       // close mobile menu if open
    smoothTo(href);           // smooth scroll to the section
  };

  return (
    <nav
      id="navbar"
      className={`fixed top-0 left-0 right-0 z-50 border-b border-transparent ${scrolled ? 'scrolled' : ''}`}
    >
      {/* ── Main bar ─────────────────────────────────────────── */}
      <div className="max-w-7xl mx-auto px-5 lg:px-8 flex items-center justify-between h-16 lg:h-20">

        {/* Logo */}
        <a href="#" onClick={(e) => handleNavClick(e, '#hero')} className="flex items-center gap-2.5 group">
          <div
            className="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
            style={{ background: 'linear-gradient(135deg, #6ee7f7, #a3e635)' }}
          >
            {/* Hexagonal mark */}
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <path d="M8 1L14 4.5V11.5L8 15L2 11.5V4.5L8 1Z" fill="#09090b" />
              <path d="M8 5L11 7V11L8 13L5 11V7L8 5Z" fill="#09090b" opacity="0.45" />
            </svg>
          </div>
          <span className="font-display font-bold text-lg tracking-tight text-snow group-hover:text-arc transition-colors duration-200">
            Invincible
          </span>
        </a>

        {/* Desktop navigation links (hidden on mobile) */}
        <div className="hidden lg:flex items-center gap-8">
          {links.map((link) => (
            <a
              key={link.href}
              href={link.href}
              onClick={(e) => handleNavClick(e, link.href)}
              className="text-sm font-medium text-ash hover:text-snow transition-colors duration-200"
            >
              {link.label}
            </a>
          ))}
        </div>

        {/* Right side: CTA button + hamburger */}
        <div className="flex items-center gap-3">
          <a
            href="#contact"
            onClick={(e) => handleNavClick(e, '#contact')}
            className="btn-primary text-sm py-2.5 px-5 hidden sm:inline-flex"
          >
            Start a Project <IconArrowRight />
          </a>

          {/* Hamburger — visible only on mobile (lg:hidden) */}
          <button
            onClick={() => setMenuOpen((prev) => !prev)}
            className="lg:hidden w-10 h-10 flex flex-col items-center justify-center gap-1.5 rounded-lg hover:bg-edge transition-colors"
            aria-label={menuOpen ? 'Close menu' : 'Open menu'}
            aria-expanded={menuOpen}
          >
            {/* Three bars that animate into an X when open */}
            <span className={`block w-5 h-0.5 bg-snow transition-transform duration-300 ${menuOpen ? 'rotate-45 translate-y-2' : ''}`} />
            <span className={`block w-5 h-0.5 bg-snow transition-opacity duration-300 ${menuOpen ? 'opacity-0' : ''}`} />
            <span className={`block w-5 h-0.5 bg-snow transition-transform duration-300 ${menuOpen ? '-rotate-45 -translate-y-2' : ''}`} />
          </button>
        </div>
      </div>

      {/* ── Mobile dropdown menu ─────────────────────────────── */}
      {/* The id="mobile-menu" is targeted by CSS in style.css
          for the slide-down animation via max-height transition */}
      <div
        id="mobile-menu"
        className={menuOpen ? 'open' : ''}
        style={{ background: 'rgba(9,9,11,.97)', borderBottom: '1px solid #2a2a32' }}
      >
        <div className="max-w-7xl mx-auto px-5 py-4 flex flex-col gap-1">
          {links.map((link) => (
            <a
              key={link.href}
              href={link.href}
              onClick={(e) => handleNavClick(e, link.href)}
              className="py-3 px-4 text-snow text-base font-medium rounded-lg hover:bg-edge transition-colors"
            >
              {link.label}
            </a>
          ))}
          <a
            href="#contact"
            onClick={(e) => handleNavClick(e, '#contact')}
            className="btn-primary mt-2 justify-center"
          >
            Start a Project <IconArrowRight />
          </a>
        </div>
      </div>
    </nav>
  );
}


/* ============================================================
   SECTION 4 — TICKER COMPONENT
   Scrolling marquee of skills/technologies.
   Items are doubled so the animation loops seamlessly:
   when the first copy exits left, the second copy is already
   in position and looks identical → no visible jump.
   The CSS animation is defined in style.css (.ticker-track).
   ============================================================ */
function Ticker() {
  const items = [
    'UI/UX Design', 'Web Development', 'AI Integration', 'Mobile Apps',
    'Brand Identity', 'Performance SEO', 'React & Next.js', 'LLM Pipelines',
    'Digital Products', 'Design Systems',
  ];

  // Duplicate the array so the loop is seamless
  const doubled = [...items, ...items];

  return (
    <div className="ticker-wrap border-y border-wire py-3 bg-panel overflow-hidden">
      <div className="ticker-track">
        {doubled.map((item, index) => (
          /* index is fine as key here because the list never reorders */
          <span
            key={index}
            className="inline-flex items-center gap-3 px-6 text-sm text-ash font-display font-semibold uppercase tracking-widest"
          >
            <IconDot />
            {item}
          </span>
        ))}
      </div>
    </div>
  );
}


/* ============================================================
   SECTION 5 — HERO COMPONENT
   Full-viewport opening section with headline, CTAs and stats.
   Animations are CSS-based (animate-fade-up from Tailwind config)
   with animationDelay inline styles for staggered reveal.
   ============================================================ */
function Hero() {
  return (
    <section
      id="hero"
      className="relative min-h-screen flex flex-col items-center justify-center overflow-hidden grid-bg pt-20"
    >
      {/* Ambient colour orbs — purely decorative blurred blobs */}
      <div className="orb w-96 h-96 top-1/4 -left-32"  style={{ background: 'rgba(110,231,247,0.10)' }} />
      <div className="orb w-80 h-80 bottom-1/4 -right-20" style={{ background: 'rgba(163,230,53,0.08)' }} />
      <div className="orb w-64 h-64 top-1/2 left-1/2"
        style={{ background: 'rgba(249,115,22,0.06)', transform: 'translate(-50%,-50%)' }} />

      {/* Top glow gradient */}
      <div className="absolute inset-x-0 top-0 h-96 bg-glow-c pointer-events-none" />

      {/* ── Main content ─────────────────────────────────────── */}
      <div className="relative z-10 max-w-5xl mx-auto px-5 lg:px-8 text-center">

        {/* "Available" badge */}
        <div
          className="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-arc/20 bg-arc/5 mb-8 animate-fade-in"
          style={{ animationDelay: '0s' }}
        >
          {/* Pulsing dot indicates live / available status */}
          <span className="w-2 h-2 rounded-full bg-arc animate-pulse-glow inline-block" />
          <span className="text-xs font-display font-bold tracking-widest uppercase text-arc">
            Available for Projects
          </span>
        </div>

        {/* H1 — there must be ONLY ONE h1 per page for good SEO */}
        <h1
          className="font-display font-extrabold text-5xl sm:text-6xl md:text-7xl lg:text-8xl leading-none tracking-tight mb-6 animate-fade-up"
          style={{ animationDelay: '0.1s' }}
        >
          Build Bold.<br />
          <span className="grad-text">Ship Fast.</span><br />
          Stay Invincible.
        </h1>

        <p
          className="text-lg sm:text-xl text-ash max-w-2xl mx-auto mb-10 leading-relaxed animate-fade-up"
          style={{ animationDelay: '0.25s' }}
        >
          We're a premium design, development &amp; AI studio crafting digital experiences
          that captivate, convert, and scale — from pixel-perfect interfaces to intelligent systems.
        </p>

        {/* CTA buttons */}
        <div
          className="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-up"
          style={{ animationDelay: '0.4s' }}
        >
          <button onClick={() => smoothTo('#contact')} className="btn-primary text-base px-8 py-4">
            Start a Project <IconArrowRight size={16} />
          </button>
          <button onClick={() => smoothTo('#work')} className="btn-ghost text-base px-8 py-4">
            View Our Work
          </button>
        </div>

        {/* Stats row */}
        <div
          className="grid grid-cols-3 gap-6 sm:gap-12 max-w-lg mx-auto mt-20 pt-10 border-t border-wire animate-fade-up"
          style={{ animationDelay: '0.55s' }}
        >
          {[
            ['50+', 'Projects Shipped'],
            ['98%', 'Client Satisfaction'],
            ['3×',  'Avg. Growth'],
          ].map(([number, label]) => (
            <div key={label} className="text-center">
              <p className="stat-num text-3xl sm:text-4xl grad-text">{number}</p>
              <p className="text-xs text-mist mt-1 font-medium">{label}</p>
            </div>
          ))}
        </div>
      </div>

      {/* Scroll indicator — subtle hint to scroll down */}
      <div
        className="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-fade-in"
        style={{ animationDelay: '1s' }}
      >
        <span className="text-xs text-mist font-display uppercase tracking-widest">Scroll</span>
        <div className="w-px h-12 bg-gradient-to-b from-arc/50 to-transparent" />
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 6 — SERVICES COMPONENT
   Six service cards in a responsive grid.
   Each card has an icon, a tag badge, a title, and description.
   ============================================================ */
function Services() {
  /*
   * Service data array — keeping data separate from JSX makes
   * it trivial to add, remove or reorder cards.
   */
  const services = [
    {
      Icon: IconLayers,
      title: 'UI/UX Design',
      tag:   'Design',
      desc:  'Interfaces crafted for humans first. We turn complex flows into delightful, intuitive experiences that keep users coming back.',
    },
    {
      Icon: IconCode,
      title: 'Web Development',
      tag:   'Engineering',
      desc:  'Full-stack engineering with React, Next.js, and modern frameworks. Fast, accessible, and built to scale.',
    },
    {
      Icon: IconCpu,
      title: 'AI Integration',
      tag:   'AI',
      desc:  'LLM pipelines, RAG systems, AI agents, and custom workflows — embed intelligence into your product, production-ready.',
    },
    {
      Icon: IconSmartphone,
      title: 'Mobile Apps',
      tag:   'Mobile',
      desc:  'Native and cross-platform apps that feel at home on every device. Performance-first, design-led.',
    },
    {
      Icon: IconPenTool,
      title: 'Brand Identity',
      tag:   'Brand',
      desc:  'Visual systems that communicate instantly. Logos, typography, colour systems — every mark is deliberate.',
    },
    {
      Icon: IconActivity,
      title: 'Performance & SEO',
      tag:   'Growth',
      desc:  'Speed and visibility engineered in from day one. Core Web Vitals, structured data, and technical SEO that drives growth.',
    },
  ];

  return (
    <section id="services" className="relative py-24 lg:py-32 overflow-hidden">
      <div className="max-w-7xl mx-auto px-5 lg:px-8">

        {/* Section header */}
        <div className="text-center mb-16 reveal">
          <span className="section-label">What We Do</span>
          <h2 className="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl mt-5 mb-5 tracking-tight">
            Everything You Need<br />
            <span className="grad-text">to Ship &amp; Scale</span>
          </h2>
          <p className="text-ash text-lg max-w-xl mx-auto leading-relaxed">
            From the first sketch to the final deploy — we handle every layer of your digital product.
          </p>
        </div>

        {/* Cards grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {services.map((service, index) => (
            <div
              key={service.title}
              /* Cycle through delay classes 1–4 for staggered reveal */
              className={`card p-7 reveal reveal-delay-${(index % 4) + 1}`}
            >
              {/* Icon container */}
              <div className="icon-box mb-5">
                <service.Icon />
              </div>

              {/* Category tag badge */}
              <div className="inline-block text-xs font-display font-bold uppercase tracking-widest text-volt bg-volt/10 border border-volt/20 rounded-full px-3 py-1 mb-3">
                {service.tag}
              </div>

              <h3 className="font-display font-bold text-xl text-snow mb-3">{service.title}</h3>
              <p className="text-ash text-sm leading-relaxed">{service.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 7 — ABOUT COMPONENT
   Two-column layout: text + skill bars on the left, value
   cards on the right. Skill bar widths are set via inline
   style so the CSS transition animates from 0 → target.
   ============================================================ */
function About() {

  const traits = [
    { pct: 95, label: 'Design Quality'    },
    { pct: 98, label: 'On-time Delivery'  },
    { pct: 90, label: 'Client Retention'  },
    { pct: 92, label: 'Performance Score' },
  ];

  /*
   * Value cards data — icon is a component reference.
   * We render it with <Card.Icon size={22} /> below.
   */
  const values = [
    { Icon: IconZap,        title: 'Fast Delivery',      desc: 'Ship in weeks, not months'       },
    { Icon: IconTarget,     title: 'Pixel Perfect',      desc: 'Design fidelity, guaranteed'     },
    { Icon: IconLock,       title: 'Secure by Default',  desc: 'Security baked in from day 1'    },
    { Icon: IconTrendingUp, title: 'Growth-Ready',       desc: 'Scale without breaking'          },
  ];

  const techStack = ['React', 'Next.js', 'Node.js', 'Python', 'OpenAI', 'Figma', 'TypeScript', 'PostgreSQL'];

  return (
    <section id="about" className="relative py-24 lg:py-32 overflow-hidden">
      {/* Ambient orb top-right */}
      <div className="orb w-96 h-96 top-0 right-0"
        style={{ background: 'rgba(163,230,53,0.06)', transform: 'translate(25%,-50%)' }} />

      <div className="max-w-7xl mx-auto px-5 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-16 items-center">

          {/* ── Left column: text + tech stack ────────────────── */}
          <div>
            <span className="section-label reveal">About Us</span>
            <h2 className="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl mt-5 mb-6 tracking-tight leading-tight reveal reveal-delay-1">
              Small Team.<br />
              <span className="grad-text">Massive Impact.</span>
            </h2>
            <p className="text-ash text-lg leading-relaxed mb-5 reveal reveal-delay-2">
              Invincible is a tight-knit studio of designers, engineers, and AI specialists obsessed
              with craft. We partner with founders, startups, and growing companies to build products
              that matter.
            </p>
            <p className="text-ash leading-relaxed mb-8 reveal reveal-delay-2">
              We believe the best digital products are born at the intersection of bold design and
              solid engineering. Every pixel, every line of code, every model we train — intentional.
            </p>

            {/* Tech stack pill tags */}
            <div className="flex flex-wrap gap-3 reveal reveal-delay-3">
              {techStack.map((tech) => (
                <span
                  key={tech}
                  className="px-3 py-1.5 text-xs font-display font-bold uppercase tracking-wider text-arc bg-arc/8 border border-arc/20 rounded-lg"
                >
                  {tech}
                </span>
              ))}
            </div>
          </div>

          {/* ── Right column: skill bars + value cards ─────────── */}
          <div className="space-y-6 reveal reveal-delay-2">

            {/* Skill progress bars */}
            {traits.map(({ pct, label }) => (
              <div key={label}>
                <div className="flex justify-between mb-2">
                  <span className="text-sm font-display font-semibold text-snow">{label}</span>
                  <span className="text-sm font-display font-bold text-arc">{pct}%</span>
                </div>
                {/* Track */}
                <div className="h-2 bg-wire rounded-full overflow-hidden">
                  {/* Fill — width drives the CSS transition in style.css */}
                  <div className="skill-bar-fill" style={{ width: `${pct}%` }} />
                </div>
              </div>
            ))}

            {/* 2×2 value card grid */}
            <div className="grid grid-cols-2 gap-4 mt-8">
              {values.map(({ Icon, title, desc }) => (
                <div key={title} className="card p-5">
                  {/* Icon rendered with consistent arc colour */}
                  <div className="text-arc mb-2">
                    <Icon size={22} />
                  </div>
                  <p className="font-display font-bold text-sm text-snow mt-1">{title}</p>
                  <p className="text-xs text-mist mt-1">{desc}</p>
                </div>
              ))}
            </div>
          </div>

        </div>
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 8 — WORK / PORTFOLIO COMPONENT
   Six project cards with a hover overlay revealing a CTA.
   ============================================================ */
function Work() {

  const projects = [
    { title: 'NexaFlow Dashboard', cat: 'SaaS / AI',        tag: 'React + AI',    gradFrom: 'rgba(110,231,247,0.20)', gradTo: 'rgba(163,230,53,0.10)',  desc: 'Intelligent analytics dashboard with real-time AI insights and predictive modelling.' },
    { title: 'Pulse Health App',   cat: 'Mobile / Design',  tag: 'React Native',  gradFrom: 'rgba(249,115,22,0.20)',  gradTo: 'rgba(110,231,247,0.10)', desc: 'Award-winning health tracking app used by 200k+ users across iOS and Android.' },
    { title: 'Ember Brand System', cat: 'Brand / Design',   tag: 'Identity',      gradFrom: 'rgba(163,230,53,0.20)',  gradTo: 'rgba(249,115,22,0.10)',  desc: 'Complete brand identity and design system for a climate-tech startup Series A.' },
    { title: 'Cortex AI Platform', cat: 'AI / Engineering', tag: 'LLM + Python',  gradFrom: 'rgba(110,231,247,0.15)', gradTo: 'rgba(110,231,247,0.05)', desc: 'Multi-modal AI platform processing 10M+ API requests per day with sub-100ms latency.' },
    { title: 'Slate E-commerce',   cat: 'Web / Commerce',   tag: 'Next.js',       gradFrom: 'rgba(249,115,22,0.15)',  gradTo: 'rgba(163,230,53,0.10)',  desc: 'High-converting storefront with AI-powered product recommendations and A/B testing.' },
    { title: 'Nova Design System', cat: 'Design Systems',   tag: 'Figma + React', gradFrom: 'rgba(163,230,53,0.15)',  gradTo: 'rgba(110,231,247,0.10)', desc: 'Unified component library used across 12 products with 400+ documented components.' },
  ];

  return (
    <section id="work" className="relative py-24 lg:py-32">
      <div className="max-w-7xl mx-auto px-5 lg:px-8">

        {/* Section header row — label + description separated */}
        <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-14">
          <div>
            <span className="section-label reveal">Selected Work</span>
            <h2 className="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl mt-5 tracking-tight reveal reveal-delay-1">
              Projects We're<br />
              <span className="grad-text">Proud Of</span>
            </h2>
          </div>
          <p className="text-ash max-w-xs leading-relaxed text-sm reveal reveal-delay-2">
            A curated selection of our recent client work and internal products.
          </p>
        </div>

        {/* Project cards */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {projects.map((project, index) => (
            <div
              key={project.title}
              className={`card overflow-hidden group cursor-pointer reveal reveal-delay-${(index % 4) + 1}`}
            >
              {/* Coloured thumbnail area with hover overlay */}
              <div
                className="h-44 border-b border-wire relative overflow-hidden"
                style={{ background: `linear-gradient(135deg, ${project.gradFrom}, ${project.gradTo})` }}
              >
                {/* Faint grid lines on thumbnail */}
                <div className="absolute inset-0 grid-bg opacity-30" />

                {/* Large initial letter watermark */}
                <div className="absolute inset-0 flex items-center justify-center">
                  <span className="font-display font-extrabold text-5xl text-white/10 select-none">
                    {project.title.charAt(0)}
                  </span>
                </div>

                {/* Tag badge */}
                <div className="absolute top-4 left-4">
                  <span className="text-xs font-display font-bold uppercase tracking-widest text-arc bg-ink/70 border border-arc/30 rounded-full px-3 py-1">
                    {project.tag}
                  </span>
                </div>

                {/* Hover overlay — fades in on card hover via group-hover */}
                <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-ink/60 backdrop-blur-sm">
                  <span className="btn-primary text-sm py-2.5 px-5">
                    View Case Study <IconArrowRight size={14} />
                  </span>
                </div>
              </div>

              {/* Card body */}
              <div className="p-6">
                <p className="text-xs text-mist font-medium mb-1 uppercase tracking-wider">{project.cat}</p>
                <h3 className="font-display font-bold text-lg text-snow mb-2">{project.title}</h3>
                <p className="text-ash text-sm leading-relaxed">{project.desc}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 9 — TESTIMONIALS COMPONENT
   Six client quote cards, each with a 5-star rating,
   quote text, and an avatar with the client's initials.
   ============================================================ */
function Testimonials() {

  const testimonials = [
    {
      name:   'Sarah Chen',
      role:   'CEO, NexaFlow',
      avatar: 'SC',
      quote:  "Invincible delivered a product that exceeded every expectation. The attention to detail in both design and code is truly rare. Our conversion rate jumped 40% post-launch.",
    },
    {
      name:   'Marcus Thompson',
      role:   'CTO, Pulse Health',
      avatar: 'MT',
      quote:  "Working with this team felt like having a co-founder. They understood our vision immediately and shipped our app in record time — without cutting a single corner.",
    },
    {
      name:   'Priya Mehta',
      role:   'Founder, Ember Climate',
      avatar: 'PM',
      quote:  "The brand identity they created has been a conversation-starter at every pitch meeting. Investors notice it. Users remember it. That's the power of great design.",
    },
    {
      name:   "James O'Brien",
      role:   'Head of Product, Cortex',
      avatar: 'JO',
      quote:  "Their AI integration work is second to none. They brought our LLM pipelines from prototype to production-ready in 6 weeks. Absolutely invincible team.",
    },
    {
      name:   'Aisha Patel',
      role:   'VP Design, Slate Commerce',
      avatar: 'AP',
      quote:  "I've worked with many agencies. None come close to this level of craft. They treat your product like it's their own, and that shows in every deliverable.",
    },
    {
      name:   'Luca Ferrari',
      role:   'Founder, Nova Studio',
      avatar: 'LF',
      quote:  "The design system they built has scaled to 12 of our products without a single refactor. Rock-solid foundations. These folks think 10 steps ahead.",
    },
  ];

  return (
    <section id="testimonials" className="relative py-24 lg:py-32 overflow-hidden">
      {/* Ambient orb bottom-left */}
      <div className="orb w-96 h-96 bottom-0 left-0"
        style={{ background: 'rgba(110,231,247,0.08)', transform: 'translate(-25%, 50%)' }} />

      <div className="max-w-7xl mx-auto px-5 lg:px-8">

        {/* Section header */}
        <div className="text-center mb-16 reveal">
          <span className="section-label">Testimonials</span>
          <h2 className="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl mt-5 mb-5 tracking-tight">
            Words from<br />
            <span className="grad-text">Happy Clients</span>
          </h2>
          <p className="text-ash text-lg max-w-xl mx-auto">
            Don't take our word for it — hear from the founders and teams we've partnered with.
          </p>
        </div>

        {/* Testimonial cards */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {testimonials.map((t, index) => (
            <div
              key={t.name}
              className={`card tcard p-7 reveal reveal-delay-${(index % 4) + 1}`}
            >
              {/* 5 star icons */}
              <div className="stars flex gap-0.5 mb-5">
                {Array.from({ length: 5 }).map((_, i) => (
                  <IconStar key={i} size={14} />
                ))}
              </div>

              {/* Quote */}
              <p className="text-snow/80 text-sm leading-relaxed mb-6 italic">
                "{t.quote}"
              </p>

              {/* Author row */}
              <div className="flex items-center gap-3 pt-5 border-t border-wire">
                {/* Avatar circle with gradient background and initials */}
                <div
                  className="w-10 h-10 rounded-full flex items-center justify-center font-display font-bold text-sm flex-shrink-0"
                  style={{ background: 'linear-gradient(135deg, #6ee7f7, #a3e635)', color: '#09090b' }}
                  aria-label={`Avatar for ${t.name}`}
                >
                  {t.avatar}
                </div>
                <div>
                  <p className="font-display font-bold text-sm text-snow">{t.name}</p>
                  <p className="text-xs text-mist">{t.role}</p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 10 — CTA BANNER COMPONENT
   Full-width "ready to start?" call-to-action block with a
   glowing gradient border and grid overlay.
   ============================================================ */
function CTABanner() {
  return (
    <section className="relative py-20 lg:py-28 overflow-hidden">
      <div className="max-w-5xl mx-auto px-5 lg:px-8">
        <div
          className="reveal relative rounded-2xl overflow-hidden border border-arc/20 p-12 lg:p-16 text-center"
          style={{ background: 'linear-gradient(135deg, rgba(110,231,247,.08) 0%, rgba(163,230,53,.05) 50%, #09090b 100%)' }}
        >
          {/* Decorative grid inside the card */}
          <div className="absolute inset-0 grid-bg opacity-40 pointer-events-none" />
          {/* Top highlight line */}
          <div className="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-arc/50 to-transparent" />
          {/* Bottom highlight line */}
          <div className="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-volt/30 to-transparent" />

          <div className="relative z-10">
            <span className="section-label mb-6">Let's Build Together</span>
            <h2 className="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl mt-5 mb-5 tracking-tight">
              Ready to Go<br />
              <span className="grad-text">Invincible?</span>
            </h2>
            <p className="text-ash text-lg max-w-xl mx-auto mb-10 leading-relaxed">
              Tell us about your project. We respond within 24 hours — no corporate fluff,
              just a real conversation about what we can build together.
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
              <button onClick={() => smoothTo('#contact')} className="btn-primary text-base px-8 py-4">
                Start a Project <IconArrowRight />
              </button>
              <button onClick={() => smoothTo('#work')} className="btn-ghost text-base px-8 py-4">
                See Our Work
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 11 — CONTACT COMPONENT
   Two-column layout: info cards on the left, form on the right.

   FORM SUBMISSION FLOW:
   1. User fills fields and clicks "Send Message"
   2. handleSubmit() validates fields client-side first
   3. fetch() sends a POST request to index.php (same URL)
   4. PHP processes the request and returns JSON
   5. React reads the response and updates the status state
   6. Status state controls which UI (success/error) is shown

   STATE:
   • form    → object holding all field values
   • status  → null | 'loading' | 'success' | 'error'
   • errMsg  → error message string shown in the error UI
   ============================================================ */
function Contact() {
  // form state — one key per field
  const [form, setForm]     = useState({ name: '', email: '', message: '', website: '' });
  const [status, setStatus] = useState(null);  // controls which UI state to show
  const [errMsg, setErrMsg] = useState('');

  // Helper: update a single field in the form object
  const setField = (key, value) => setForm((prev) => ({ ...prev, [key]: value }));

  const handleSubmit = async () => {
    // Client-side validation before hitting the server
    if (!form.name || !form.email || !form.message) {
      setStatus('error');
      setErrMsg('Please fill in all required fields.');
      return;
    }

    setStatus('loading'); // show spinner on button

    try {
      /*
       * FormData serialises the fields exactly like a regular
       * HTML form submit. PHP reads them via $_POST as usual.
       * fetch() is a modern browser API for HTTP requests.
       */
      const body = new FormData();
      Object.entries(form).forEach(([key, val]) => body.append(key, val));

      const response = await fetch(window.location.href, {
        method: 'POST',
        body,
      });

      const data = await response.json(); // parse JSON response from PHP

      if (data.status === 'success') {
        setStatus('success');
        setForm({ name: '', email: '', message: '', website: '' }); // reset form
      } else {
        setStatus('error');
        setErrMsg(data.message || 'Something went wrong. Please try again.');
      }
    } catch {
      // Network failure (no internet, server down, etc.)
      setStatus('error');
      setErrMsg('Network error. Please check your connection and try again.');
    }
  };

  /* Info card data — icon, label, value
     Icons are components so we render <item.Icon /> below      */
  const contactInfo = [
    { Icon: IconMail,   label: 'Email',    value: 'hello@invinciblestudio.dev' },
    { Icon: IconMapPin, label: 'Based',    value: 'Remote-first · Worldwide'   },
    { Icon: IconClock,  label: 'Response', value: 'Within 24 hours'            },
  ];

  return (
    <section id="contact" className="relative py-24 lg:py-32 overflow-hidden">
      {/* Ambient orb top-right */}
      <div className="orb w-96 h-96 top-0 right-0"
        style={{ background: 'rgba(163,230,53,0.06)', transform: 'translate(25%,-25%)' }} />

      <div className="max-w-7xl mx-auto px-5 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-16 items-start">

          {/* ── Left column: heading + info cards ─────────────── */}
          <div>
            <span className="section-label reveal">Get In Touch</span>
            <h2 className="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl mt-5 mb-6 tracking-tight leading-tight reveal reveal-delay-1">
              Let's Start<br />
              <span className="grad-text">Something Bold</span>
            </h2>
            <p className="text-ash text-lg leading-relaxed mb-10 reveal reveal-delay-2">
              Whether you have a crystal-clear brief or just an idea on a napkin — reach out.
              We'll shape it into something extraordinary.
            </p>

            {/* Contact info cards */}
            <div className="space-y-4 reveal reveal-delay-3">
              {contactInfo.map(({ Icon, label, value }) => (
                <div key={label} className="card flex items-center gap-4 p-4">
                  {/* Icon box using the .contact-info-icon class from style.css */}
                  <div className="contact-info-icon">
                    <Icon size={20} />
                  </div>
                  <div>
                    <p className="text-xs text-mist uppercase tracking-wider font-display font-bold">{label}</p>
                    <p className="text-snow text-sm font-medium mt-0.5">{value}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* ── Right column: contact form ─────────────────────── */}
          <div className="card p-8 lg:p-10 reveal reveal-delay-2">
            <h3 className="font-display font-bold text-2xl text-snow mb-7">Send Us a Message</h3>

            {/*
              HONEYPOT FIELD
              Visually hidden via display:none so real users never see it.
              If this field has a value when the form submits, PHP rejects it
              as a bot submission. tabIndex="-1" prevents keyboard navigation.
            */}
            <input
              type="text"
              name="website"
              value={form.website}
              onChange={(e) => setField('website', e.target.value)}
              style={{ display: 'none' }}
              tabIndex="-1"
              autoComplete="off"
              aria-hidden="true"
            />

            <div className="space-y-5">

              {/* Name field */}
              <div>
                <label className="block text-xs font-display font-bold uppercase tracking-wider text-ash mb-2">
                  Your Name <span className="text-flare" aria-label="required">*</span>
                </label>
                <input
                  type="text"
                  placeholder="Alex Johnson"
                  value={form.name}
                  onChange={(e) => setField('name', e.target.value)}
                  className="form-input"
                  autoComplete="name"
                />
              </div>

              {/* Email field */}
              <div>
                <label className="block text-xs font-display font-bold uppercase tracking-wider text-ash mb-2">
                  Email Address <span className="text-flare" aria-label="required">*</span>
                </label>
                <input
                  type="email"
                  placeholder="alex@company.com"
                  value={form.email}
                  onChange={(e) => setField('email', e.target.value)}
                  className="form-input"
                  autoComplete="email"
                />
              </div>

              {/* Message field */}
              <div>
                <label className="block text-xs font-display font-bold uppercase tracking-wider text-ash mb-2">
                  Your Message <span className="text-flare" aria-label="required">*</span>
                </label>
                <textarea
                  rows="5"
                  placeholder="Tell us about your project — scope, timeline, budget, and goals..."
                  value={form.message}
                  onChange={(e) => setField('message', e.target.value)}
                  className="form-input resize-none"
                />
              </div>

              {/* SUCCESS state — shown after a successful PHP response */}
              {status === 'success' && (
                <div className="flex items-center gap-3 p-4 rounded-xl bg-volt/10 border border-volt/30 text-sm text-volt" role="alert">
                  <IconCheckCircle size={18} />
                  Message sent! We'll be in touch within 24 hours.
                </div>
              )}

              {/* ERROR state — shown on validation failure or PHP error */}
              {status === 'error' && (
                <div className="flex items-center gap-3 p-4 rounded-xl bg-flare/10 border border-flare/30 text-sm text-flare" role="alert">
                  <IconAlertCircle size={18} />
                  {errMsg}
                </div>
              )}

              {/* Submit button — changes appearance based on status */}
              <button
                onClick={handleSubmit}
                disabled={status === 'loading'}  /* prevents double-submit */
                className="btn-primary w-full justify-center py-4 text-base disabled:opacity-60 disabled:cursor-not-allowed"
              >
                {status === 'loading' ? (
                  /* Loading state: spinner + text */
                  <>
                    <IconSpinner size={18} />
                    Sending…
                  </>
                ) : (
                  /* Default state */
                  <>
                    Send Message <IconArrowRight size={16} />
                  </>
                )}
              </button>

            </div>
          </div>
        </div>
      </div>
    </section>
  );
}


/* ============================================================
   SECTION 12 — FOOTER COMPONENT
   Four-column layout: brand, services links, company links,
   newsletter signup. Collapses to two columns on mobile.
   ============================================================ */
function Footer() {
  const year = new Date().getFullYear(); // dynamic — never needs updating

  const handleLinkClick = (e, href) => {
    e.preventDefault();
    smoothTo(href);
  };

  const serviceLinks  = ['UI/UX Design', 'Web Development', 'AI Integration', 'Mobile Apps', 'Brand Identity', 'SEO & Performance'];
  const companyLinks  = [['About', '#about'], ['Work', '#work'], ['Testimonials', '#testimonials'], ['Contact', '#contact']];
  const socialLinks   = [
    { Icon: IconTwitterX,  label: 'Twitter / X', href: '#' },
    { Icon: IconLinkedIn,  label: 'LinkedIn',     href: '#' },
    { Icon: IconGitHub,    label: 'GitHub',       href: '#' },
  ];

  return (
    <footer className="relative border-t border-wire bg-panel/60 pt-16 pb-10">
      <div className="max-w-7xl mx-auto px-5 lg:px-8">

        {/* ── Four column grid ─────────────────────────────────── */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-10 mb-14">

          {/* Brand column */}
          <div className="col-span-2 md:col-span-1">
            {/* Logo mark */}
            <div className="flex items-center gap-2.5 mb-5">
              <div
                className="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                style={{ background: 'linear-gradient(135deg, #6ee7f7, #a3e635)' }}
              >
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M8 1L14 4.5V11.5L8 15L2 11.5V4.5L8 1Z" fill="#09090b" />
                </svg>
              </div>
              <span className="font-display font-bold text-lg text-snow">Invincible</span>
            </div>
            <p className="text-ash text-sm leading-relaxed mb-5">
              Design, development &amp; AI studio building bold digital products since 2024.
            </p>
            {/* Social icon links */}
            <div className="flex gap-3">
              {socialLinks.map(({ Icon, label, href }) => (
                <a
                  key={label}
                  href={href}
                  aria-label={label}
                  className="w-9 h-9 rounded-lg border border-wire flex items-center justify-center text-mist hover:text-arc hover:border-arc/40 transition-colors"
                >
                  <Icon size={16} />
                </a>
              ))}
            </div>
          </div>

          {/* Services column */}
          <div>
            <p className="font-display font-bold text-xs uppercase tracking-widest text-snow mb-5">Services</p>
            <ul className="space-y-3">
              {serviceLinks.map((label) => (
                <li key={label}>
                  <a
                    href="#services"
                    onClick={(e) => handleLinkClick(e, '#services')}
                    className="text-sm text-ash hover:text-arc transition-colors"
                  >
                    {label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Company column */}
          <div>
            <p className="font-display font-bold text-xs uppercase tracking-widest text-snow mb-5">Company</p>
            <ul className="space-y-3">
              {companyLinks.map(([label, href]) => (
                <li key={label}>
                  <a
                    href={href}
                    onClick={(e) => handleLinkClick(e, href)}
                    className="text-sm text-ash hover:text-arc transition-colors"
                  >
                    {label}
                  </a>
                </li>
              ))}
            </ul>
          </div>

          {/* Newsletter column */}
          <div>
            <p className="font-display font-bold text-xs uppercase tracking-widest text-snow mb-5">Stay Updated</p>
            <p className="text-ash text-sm mb-4 leading-relaxed">
              Design tips and AI insights in your inbox — no spam, ever.
            </p>
            <div className="flex gap-2">
              <input
                type="email"
                placeholder="Your email"
                className="form-input flex-1 py-2.5 text-sm"
                aria-label="Newsletter email address"
              />
              <button className="btn-primary py-2.5 px-4 text-sm flex-shrink-0" aria-label="Subscribe">
                <IconArrowRight size={14} />
              </button>
            </div>
          </div>

        </div>

        {/* Gradient divider line */}
        <div className="divider mb-8" />

        {/* Bottom bar: copyright + legal links */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-mist">
          <p>© {year} Invincible Studio. All rights reserved.</p>
          <div className="flex gap-6">
            <a href="#" className="hover:text-arc transition-colors">Privacy Policy</a>
            <a href="#" className="hover:text-arc transition-colors">Terms of Service</a>
            <a href="#" className="hover:text-arc transition-colors">Cookie Policy</a>
          </div>
        </div>

      </div>
    </footer>
  );
}


/* ============================================================
   SECTION 13 — APP ROOT COMPONENT
   The top-level component that composes every section in order.
   useScrollReveal() is called here once so it watches all
   .reveal elements after the full tree has mounted.
   ============================================================ */
function App() {
  useScrollReveal(); // set up IntersectionObserver for the whole page

  return (
    <>
      {/* Navbar sits outside <main> so it overlays everything */}
      <Navbar />

      <main>
        <Hero />
        <Ticker />

        <Services />
        <div className="divider" />

        <About />
        <div className="divider" />

        <Work />
        <div className="divider" />

        <Testimonials />

        <CTABanner />

        <Contact />
      </main>

      <Footer />
    </>
  );
}


/* ============================================================
   SECTION 14 — MOUNT REACT INTO THE DOM
   ============================================================
   ReactDOM.createRoot() is the React 18 way to mount an app.
   It finds the <div id="root"> in index.php and renders
   the entire <App /> component tree inside it.

   This line runs once when the browser parses this script.
   ============================================================ */
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App />);
