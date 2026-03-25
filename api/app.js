/**
 * ============================================================
 * INVINCIBLE STUDIO — app.js
 * ============================================================
 *
 * Sections:
 *   1. Init — Lucide icons + footer year
 *   2. Mobile navigation toggle
 *   3. GSAP page-load timeline (header, hero, stats counter)
 *   4. GSAP ScrollTrigger animations (sections, cards, CTA)
 *   5. Smooth anchor scroll override
 *   6. Contact form AJAX submission
 * ============================================================
 */

/* ============================================================
 * 1. INIT
 * ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Lucide Icons ────────────────────────────────────────
    // Replace every <i data-lucide="..."> with an inline SVG
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // ── Footer year ─────────────────────────────────────────
    // Always correct — no hardcoding
    const yearEl = document.getElementById('currentYear');
    if (yearEl) yearEl.textContent = new Date().getFullYear();


    /* ========================================================
     * 2. MOBILE NAVIGATION TOGGLE
     * ======================================================== */
    const mobileToggle = document.getElementById('mobileToggle');
    const navLinks     = document.getElementById('navLinks');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('active');
            mobileToggle.classList.toggle('active', isOpen);
            // Prevent body scroll while nav is open
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        // Close nav on any link click
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                mobileToggle.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }


    /* ========================================================
     * 3. GSAP PAGE-LOAD TIMELINE
     * Runs immediately on page open — no ScrollTrigger needed.
     * ======================================================== */

    // Register ScrollTrigger plugin
    gsap.registerPlugin(ScrollTrigger);

    // -- Header slide down --
    gsap.from('header', {
        y: -80,
        opacity: 0,
        duration: 1,
        ease: 'power3.out'
    });

    // -- Hero orchestrated timeline --
    // Each element cascades in after the previous one
    const heroTl = gsap.timeline({ delay: 0.3 });

    heroTl
        // Badge pill fades up
        .from('.hero-badge', {
            opacity: 0,
            y: 25,
            duration: 0.7,
            ease: 'power3.out'
        })
        // Headline lines: each .line-inner slides up from below its
        // .line-wrap overflow:hidden container — the clip-masked wipe effect
        .from('.line-inner', {
            y: '115%',
            opacity: 0,
            duration: 1.1,
            stagger: 0.13,
            ease: 'power4.out'
        }, '-=0.3')
        // Subtitle paragraph
        .from('.hero-subtitle', {
            opacity: 0,
            y: 20,
            duration: 0.8,
            ease: 'power3.out'
        }, '-=0.5')
        // CTA buttons staggered
        .from('.hero-buttons .btn', {
            opacity: 0,
            y: 18,
            stagger: 0.12,
            duration: 0.65,
            ease: 'power3.out'
        }, '-=0.4')
        // Stats row
        .from('.hero-stats', {
            opacity: 0,
            y: 15,
            duration: 0.6,
            ease: 'power2.out'
        }, '-=0.35');

    // -- Stat number counter animation --
    // Triggers when the stat section enters viewport
    document.querySelectorAll('.stat-number').forEach(el => {
        // Store the final display value (e.g. "150+", "50+", "6+")
        const target    = el.textContent.trim();
        const numericPart = parseFloat(target.replace(/[^0-9.]/g, ''));
        const suffix      = target.replace(/[0-9.]/g, ''); // e.g. "+", "yr"

        gsap.fromTo(
            el,
            { textContent: 0 },
            {
                textContent: numericPart,
                duration: 2.2,
                ease: 'power2.out',
                snap: { textContent: 1 },
                // Format output with original suffix
                onUpdate: function () {
                    el.textContent = Math.round(this.targets()[0].textContent) + suffix;
                },
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    once: true // Only fires once
                }
            }
        );
    });


    /* ========================================================
     * 4. GSAP SCROLLTRIGGER ANIMATIONS
     * Elements animate in as they enter the viewport.
     * ======================================================== */

    // Helper: generic stagger-reveal for a list of elements
    function revealStagger(selector, xOffset = 0) {
        const els = gsap.utils.toArray(selector);
        if (!els.length) return;

        gsap.from(els, {
            opacity: 0,
            y: 50,
            x: xOffset,
            stagger: 0.1,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: els[0].closest('section') || els[0],
                start: 'top 82%',
                once: true
            }
        });
    }

    // -- Section headers: label + h2 + p cascade --
    gsap.utils.toArray('.section-header').forEach(header => {
        const children = Array.from(header.children);
        gsap.from(children, {
            opacity: 0,
            y: 35,
            stagger: 0.12,
            duration: 0.85,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: header,
                start: 'top 85%',
                once: true
            }
        });
    });

    // -- Marquee strip subtle fade-in --
    gsap.from('.marquee-strip', {
        opacity: 0,
        duration: 0.8,
        scrollTrigger: { trigger: '.marquee-strip', start: 'top 95%', once: true }
    });

    // -- Feature cards: 3-column staggered --
    gsap.from('.feature-card', {
        opacity: 0,
        y: 55,
        stagger: {
            each: 0.08,
            from: 'start'
        },
        duration: 0.85,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.features-grid',
            start: 'top 80%',
            once: true
        },
        // Remove the CSS initial opacity:0 / transform after animation
        onComplete: () => {
            document.querySelectorAll('.feature-card').forEach(el => {
                el.style.opacity = '';
                el.style.transform = '';
            });
        }
    });

    // -- Project cards: slight scale + fade --
    gsap.from('.project-card', {
        opacity: 0,
        y: 50,
        scale: 0.97,
        stagger: 0.12,
        duration: 0.9,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.projects-grid',
            start: 'top 80%',
            once: true
        }
    });

    // -- About intro text (two paragraphs in section-header) --
    // Already covered by the section-header loop above

    // -- Value cards: odd cards come from left, even from right --
    gsap.utils.toArray('.value-card').forEach((card, i) => {
        gsap.from(card, {
            opacity: 0,
            x: i % 2 === 0 ? -40 : 40,
            y: 20,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: card,
                start: 'top 85%',
                once: true
            }
        });
    });

    // -- Testimonials: stagger with slight rotation --
    gsap.from('.testimonial', {
        opacity: 0,
        y: 45,
        rotateX: 8,         // Subtle 3D tilt on entry
        stagger: 0.13,
        duration: 0.85,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: '.testimonials-grid',
            start: 'top 80%',
            once: true
        }
    });

    // -- CTA section: headline scales up from slightly smaller --
    const ctaTl = gsap.timeline({
        scrollTrigger: {
            trigger: '.cta',
            start: 'top 70%',
            once: true
        }
    });

    ctaTl
        .from('.cta h2', {
            opacity: 0,
            scale: 0.88,
            y: 30,
            duration: 1.1,
            ease: 'power4.out'
        })
        .from('.cta p', {
            opacity: 0,
            y: 20,
            duration: 0.8,
            ease: 'power3.out'
        }, '-=0.5')
        .from('.cta .btn', {
            opacity: 0,
            y: 15,
            duration: 0.6,
            ease: 'power3.out'
        }, '-=0.4')
        .from('.contact-info', {
            opacity: 0,
            y: 15,
            duration: 0.6,
            ease: 'power3.out'
        }, '-=0.4')
        .from('.contact-form', {
            opacity: 0,
            y: 25,
            duration: 0.8,
            ease: 'power3.out'
        }, '-=0.3');

    // -- Footer: graceful fade-up --
    gsap.from('footer .footer-brand, footer .footer-social', {
        opacity: 0,
        y: 30,
        stagger: 0.15,
        duration: 0.8,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: 'footer',
            start: 'top 90%',
            once: true
        }
    });

    gsap.from('.footer-bottom', {
        opacity: 0,
        duration: 0.7,
        ease: 'power2.out',
        scrollTrigger: {
            trigger: '.footer-bottom',
            start: 'top 95%',
            once: true
        }
    });

    // -- Parallax on hero orbs (scrub-based depth effect) --
    gsap.to('.hero-orb-1', {
        y: -120,
        ease: 'none',
        scrollTrigger: {
            trigger: '.hero',
            start: 'top top',
            end: 'bottom top',
            scrub: 1.5
        }
    });

    gsap.to('.hero-orb-2', {
        y: -80,
        ease: 'none',
        scrollTrigger: {
            trigger: '.hero',
            start: 'top top',
            end: 'bottom top',
            scrub: 2
        }
    });

    // -- Horizontal scroll-linked pin for projects on wide screens --
    // Only active on desktop where the projects are a 2-col grid
    if (window.innerWidth > 1024) {
        // Subtle horizontal shift on project cards as they scroll
        gsap.utils.toArray('.project-card').forEach((card, i) => {
            gsap.from(card, {
                x: i % 2 === 0 ? -20 : 20,
                scrollTrigger: {
                    trigger: card,
                    start: 'top 85%',
                    end: 'top 50%',
                    scrub: 1.5,
                    once: false
                }
            });
        });
    }


    /* ========================================================
     * 5. SMOOTH ANCHOR SCROLL
     * Overrides default anchor jump to account for fixed header.
     * ======================================================== */
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href   = this.getAttribute('href');
            const target = document.querySelector(href);
            if (!target) return;

            e.preventDefault();

            const headerH = document.querySelector('header')?.offsetHeight || 80;
            const top     = target.getBoundingClientRect().top + window.pageYOffset - headerH - 20;

            // Use GSAP for smooth scroll so it integrates with other animations
            gsap.to(window, {
                scrollTo: { y: top },
                duration: 1,
                ease: 'power3.inOut'
            });
        });
    });


    /* ========================================================
     * 6. CONTACT FORM — AJAX SUBMISSION
     * POSTs form data to index.php, shows feedback inline.
     * ======================================================== */
    const contactForm = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    if (contactForm && formMessage) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData     = new FormData(contactForm);
            const submitBtn    = contactForm.querySelector('button[type="submit"]');

            // Disable button while in-flight
            submitBtn.disabled     = true;
            submitBtn.textContent  = 'Sending…';

            // Animate the button slightly
            gsap.to(submitBtn, { scale: 0.96, duration: 0.15, ease: 'power2.in',
                onComplete: () => gsap.to(submitBtn, { scale: 1, duration: 0.3, ease: 'back.out(2)' })
            });

            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Show feedback message
                formMessage.textContent = data.message;
                formMessage.className   = `form-message show ${data.status}`;

                // Animate message entrance
                gsap.from(formMessage, { opacity: 0, y: -10, duration: 0.4, ease: 'power2.out' });

                if (data.status === 'success') {
                    contactForm.reset();
                    setTimeout(() => {
                        gsap.to(formMessage, {
                            opacity: 0,
                            duration: 0.4,
                            onComplete: () => formMessage.classList.remove('show')
                        });
                    }, 5000);
                }

            } catch {
                formMessage.textContent = 'Network error — please try again.';
                formMessage.className   = 'form-message show error';
            } finally {
                submitBtn.disabled    = false;
                submitBtn.textContent = 'Send Message';
            }
        });
    }

}); // end DOMContentLoaded
