<?php
/**
 * ============================================================
 * INVINCIBLE STUDIO — index.php
 * ============================================================
 * POST  → JSON API: validates & saves contact form submissions
 * GET   → Renders the full landing page HTML
 * ============================================================
 */

/* ── POST HANDLER ─────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');

    /**
     * Sanitise a raw input string:
     * trim whitespace, encode HTML entities (XSS prevention).
     */
    function clean(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /* Honeypot check — bots fill hidden fields, humans don't */
    if (!empty($_POST['website'])) {
        echo json_encode(['status' => 'error', 'message' => 'Spam detected']);
        exit;
    }

    $name    = clean($_POST['name']    ?? '');
    $email   = clean($_POST['email']   ?? '');
    $message = clean($_POST['message'] ?? '');

    /* Required fields */
    if (!$name || !$email || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'All fields required']);
        exit;
    }

    /* Email format validation */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
        exit;
    }

    /* Build entry & append to messages.json (NDJSON / newline-delimited) */
    $entry = [
        'name'      => $name,
        'email'     => $email,
        'message'   => $message,
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'timestamp' => date('c')
    ];

    $saved = file_put_contents(
        __DIR__ . '/messages.json',
        json_encode($entry) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    echo json_encode(
        $saved !== false
            ? ['status' => 'success', 'message' => 'Message sent! We\'ll be in touch soon.']
            : ['status' => 'error',   'message' => 'Could not save. Please try again.']
    );
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>INVINCIBLE — Design, Development &amp; AI Studio | Premium Digital Solutions</title>

    <meta name="description" content="We build brands that are truly invincible. From stunning visuals to powerful digital products, we transform your vision into experiences that captivate, convert, and endure.">
    <meta name="keywords"    content="web design, web development, AI solutions, graphics design, motion design, UI/UX design, app development, digital agency, branding">
    <meta name="author"      content="Invincible Studio">
    <meta name="robots"      content="index, follow">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="https://yourdomain.com/">
    <meta property="og:title"       content="INVINCIBLE — Design, Development &amp; AI Studio">
    <meta property="og:description" content="We build brands that are truly invincible.">
    <meta property="og:image"       content="https://yourdomain.com/og-image.jpg">

    <!-- Twitter Card -->
    <meta property="twitter:card"        content="summary_large_image">
    <meta property="twitter:url"         content="https://yourdomain.com/">
    <meta property="twitter:title"       content="INVINCIBLE — Design, Development &amp; AI Studio">
    <meta property="twitter:description" content="We build brands that are truly invincible.">
    <meta property="twitter:image"       content="https://yourdomain.com/og-image.jpg">

    <!-- SVG Favicon (no extra file needed) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23FFA500'/><text y='72' x='50' text-anchor='middle' font-size='60' font-family='sans-serif' font-weight='900' fill='%23000'>I</text></svg>">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Invincible Studio",
        "url": "https://yourdomain.com",
        "logo": "https://yourdomain.com/logo.png",
        "description": "Design, Development & AI Studio",
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Service",
            "email": "hello@invincible.studio"
        }
    }
    </script>

    <!-- Stylesheet -->
    <link rel="stylesheet" href="style.css">

    <!-- Lucide icon library (replaces all emoji icons with clean SVGs) -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>

    <!-- GSAP core + ScrollTrigger plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
    <!-- GSAP ScrollTo plugin (used in smooth scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js" defer></script>

    <!-- App logic (runs after all deferred scripts above) -->
    <script src="app.js" defer></script>
</head>

<body>

<!-- ============================================================
     HEADER
     ============================================================ -->
<header>
    <nav>
        <a href="#" class="logo">INVINCIBLE<span>.</span></a>

        <ul class="nav-links" id="navLinks">
            <li><a href="#services">Services</a></li>
            <li><a href="#work">Work</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#contact" class="btn-cta">Start a Project</a></li>
        </ul>

        <!-- Hamburger — 3 animated bars, JS toggles .active -->
        <div class="mobile-toggle" id="mobileToggle" aria-label="Toggle menu" role="button" tabindex="0">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
</header>


<main>

    <!-- ==========================================================
         HERO
         ========================================================== -->
    <section class="hero">
        <!-- Decorative floating gradient orbs (actual divs so GSAP parallax works) -->
        <div class="hero-orb hero-orb-1" aria-hidden="true"></div>
        <div class="hero-orb hero-orb-2" aria-hidden="true"></div>

        <div class="container">
            <div class="hero-content">

                <!-- Badge -->
                <div class="hero-badge">
                    <i data-lucide="zap" class="badge-icon"></i>
                    We craft digital excellence
                </div>

                <!-- Headline — each .line-wrap has overflow:hidden so GSAP
                     can slide .line-inner up from below for a wipe-reveal -->
                <h1 class="hero-title">
                    <span class="line-wrap"><span class="line-inner">We build brands</span></span>
                    <span class="line-wrap"><span class="line-inner">that are <span class="highlight">truly</span></span></span>
                    <span class="line-wrap"><span class="line-inner"><span class="highlight">invincible</span></span></span>
                </h1>

                <p class="hero-subtitle">
                    From stunning visuals to powerful digital products, we transform
                    your vision into experiences that captivate, convert, and endure.
                </p>

                <div class="hero-buttons">
                    <a href="#contact" class="btn btn-primary">
                        <i data-lucide="arrow-right"></i>
                        Let's Work Together
                    </a>
                    <a href="#work" class="btn btn-secondary">
                        <i data-lucide="play"></i>
                        View Our Work
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number">150+</span>
                        <span class="stat-label">Projects Delivered</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">50+</span>
                        <span class="stat-label">Happy Clients</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">6+</span>
                        <span class="stat-label">Years of Craft</span>
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- ==========================================================
         MARQUEE TICKER STRIP
         Infinite scrolling brand/service keywords between sections
         ========================================================== -->
    <div class="marquee-strip" aria-hidden="true">
        <!-- Track is duplicated so the loop is seamless -->
        <div class="marquee-track">
            <span class="marquee-item">Design</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Development</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Motion</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">AI Products</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Branding</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">UI / UX</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Mobile Apps</span>
            <span class="marquee-item dot">·</span>
            <!-- Duplicate for seamless loop -->
            <span class="marquee-item">Design</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Development</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Motion</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">AI Products</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Branding</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">UI / UX</span>
            <span class="marquee-item dot">·</span>
            <span class="marquee-item">Mobile Apps</span>
            <span class="marquee-item dot">·</span>
        </div>
    </div>


    <!-- ==========================================================
         SERVICES SECTION
         ========================================================== -->
    <section id="services" class="features">
        <div class="container">

            <div class="section-header">
                <div class="section-label">What We Do</div>
                <h2>Expertise that <span class="highlight">delivers</span></h2>
                <p>We bring together diverse creative and technical disciplines to build complete digital ecosystems.</p>
            </div>

            <div class="features-grid">

                <article class="feature-card glow-card">
                    <div class="feature-icon">
                        <i data-lucide="palette"></i>
                    </div>
                    <h3>Graphics Design</h3>
                    <p>Brand identities, marketing materials, and visual storytelling that make your brand unforgettable.</p>
                </article>

                <article class="feature-card glow-card">
                    <div class="feature-icon">
                        <i data-lucide="globe"></i>
                    </div>
                    <h3>Web Development</h3>
                    <p>High-performance websites and web apps built with modern tech stacks that scale with your ambitions.</p>
                </article>

                <article class="feature-card glow-card">
                    <div class="feature-icon">
                        <i data-lucide="clapperboard"></i>
                    </div>
                    <h3>Motion Design</h3>
                    <p>Captivating animations and video content that bring your stories to life and hold attention.</p>
                </article>

                <article class="feature-card glow-card">
                    <div class="feature-icon">
                        <i data-lucide="smartphone"></i>
                    </div>
                    <h3>App Development</h3>
                    <p>Native and cross-platform mobile applications engineered for seamless user experiences.</p>
                </article>

                <article class="feature-card glow-card">
                    <div class="feature-icon">
                        <i data-lucide="gem"></i>
                    </div>
                    <h3>UI / UX Design</h3>
                    <p>Research-driven interfaces that delight users and drive measurable business outcomes.</p>
                </article>

                <article class="feature-card glow-card">
                    <div class="feature-icon">
                        <i data-lucide="bot"></i>
                    </div>
                    <h3>AI Products</h3>
                    <p>Intelligent solutions powered by cutting-edge AI that automate, predict, and transform industries.</p>
                </article>

            </div>
        </div>
    </section>


    <!-- ==========================================================
         PORTFOLIO / WORK SECTION
         ========================================================== -->
    <section id="work" class="projects">
        <div class="container">

            <div class="section-header">
                <div class="section-label">Selected Work</div>
                <h2>Projects that <span class="highlight">speak volumes</span></h2>
            </div>

            <div class="projects-grid">

                <article class="project-card glow-card">
                    <div class="project-image">
                        <div class="project-icon">
                            <i data-lucide="arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-tags">Web Development · UI/UX</div>
                        <h3>Nova Finance</h3>
                        <p>A next-gen fintech platform with intuitive dashboards and real-time analytics.</p>
                    </div>
                </article>

                <article class="project-card glow-card">
                    <div class="project-image">
                        <div class="project-icon">
                            <i data-lucide="arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-tags">Motion Design · Branding</div>
                        <h3>Meridian Studios</h3>
                        <p>Complete brand identity and motion system for an award-winning film studio.</p>
                    </div>
                </article>

                <article class="project-card glow-card">
                    <div class="project-image">
                        <div class="project-icon">
                            <i data-lucide="arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-tags">AI Products · App Development</div>
                        <h3>PulseAI Health</h3>
                        <p>AI-powered health monitoring app processing 10M+ data points daily.</p>
                    </div>
                </article>

                <article class="project-card glow-card">
                    <div class="project-image">
                        <div class="project-icon">
                            <i data-lucide="arrow-up-right"></i>
                        </div>
                    </div>
                    <div class="project-info">
                        <div class="project-tags">Web Development · Graphics</div>
                        <h3>Aether Commerce</h3>
                        <p>Luxury e-commerce experience with 200% increase in conversion rates.</p>
                    </div>
                </article>

            </div>
        </div>
    </section>


    <!-- ==========================================================
         ABOUT / VALUES SECTION
         ========================================================== -->
    <section id="about" class="about">
        <div class="container">

            <div class="section-header">
                <div class="section-label">Why Invincible</div>
                <h2>We don't just build —<br>we <span class="highlight">dominate</span></h2>
                <p>
                    Invincible was founded on one belief: great design and great engineering
                    aren't luxuries — they're competitive advantages. We partner with ambitious
                    brands to create digital experiences that don't just compete, they conquer.
                </p>
                <p style="margin-top: 1rem; color: #888; font-weight: 300;">
                    Our multidisciplinary team spans designers, developers, motion artists,
                    and AI engineers — all driven by the obsession to make your brand unstoppable.
                </p>
            </div>

            <div class="about-grid">

                <article class="value-card glow-card">
                    <div class="value-icon">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <h3>Uncompromising Quality</h3>
                    <p>Every pixel, every line of code meets our relentless standard.</p>
                </article>

                <article class="value-card glow-card">
                    <div class="value-icon">
                        <i data-lucide="zap"></i>
                    </div>
                    <h3>Rapid Execution</h3>
                    <p>We move fast without breaking things. Your timeline matters.</p>
                </article>

                <article class="value-card glow-card">
                    <div class="value-icon">
                        <i data-lucide="users"></i>
                    </div>
                    <h3>True Partnership</h3>
                    <p>We're not vendors — we're invested partners in your success.</p>
                </article>

                <article class="value-card glow-card">
                    <div class="value-icon">
                        <i data-lucide="trophy"></i>
                    </div>
                    <h3>Proven Results</h3>
                    <p>Our work drives real metrics: more users, more revenue, more growth.</p>
                </article>

            </div>
        </div>
    </section>


    <!-- ==========================================================
         TESTIMONIALS
         ========================================================== -->
    <section class="testimonials">
        <div class="container">

            <div class="section-header">
                <div class="section-label">Client Love</div>
                <h2>Words from those who <span class="highlight">trust us</span></h2>
            </div>

            <div class="testimonials-grid">

                <article class="testimonial glow-card">
                    <!-- Star rating rendered with filled star icons -->
                    <div class="stars">
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                    </div>
                    <div class="testimonial-content">
                        "Invincible didn't just build our platform — they redefined our entire
                        digital presence. Our conversion rate tripled."
                    </div>
                    <div class="testimonial-author">
                        <h4>Sarah Chen</h4>
                        <p>CEO, Nova Finance</p>
                    </div>
                </article>

                <article class="testimonial glow-card">
                    <div class="stars">
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                    </div>
                    <div class="testimonial-content">
                        "Their motion design work gave our brand a soul. The animations they created
                        became the centrepiece of our entire launch campaign."
                    </div>
                    <div class="testimonial-author">
                        <h4>Marcus Webb</h4>
                        <p>Creative Director, Meridian Studios</p>
                    </div>
                </article>

                <article class="testimonial glow-card">
                    <div class="stars">
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                        <i data-lucide="star"></i>
                    </div>
                    <div class="testimonial-content">
                        "Working with Invincible felt like adding a world-class team to our
                        company. They truly care about results."
                    </div>
                    <div class="testimonial-author">
                        <h4>Amira Patel</h4>
                        <p>Founder, PulseAI Health</p>
                    </div>
                </article>

            </div>
        </div>
    </section>


    <!-- ==========================================================
         CTA + CONTACT FORM
         ========================================================== -->
    <section id="contact" class="cta">
        <div class="container">

            <div class="section-label">Let's Talk</div>

            <h2>
                Ready to become<br>
                <span class="highlight">invincible</span>?
            </h2>

            <p>
                Tell us about your project and let's create something extraordinary together.
                No fluff, no bureaucracy — just great work.
            </p>

            <a href="#contactForm" class="btn btn-primary">
                <i data-lucide="send"></i>
                Start Your Project
            </a>

            <div class="contact-info">
                <div class="contact-item">
                    <i data-lucide="mail"></i>
                    hello@invincible.studio
                </div>
                <div class="contact-item">
                    <i data-lucide="map-pin"></i>
                    Available Worldwide
                </div>
            </div>

            <!-- Contact Form -->
            <form class="contact-form" id="contactForm" novalidate>
                <div class="form-message" id="formMessage" role="alert"></div>

                <!-- Honeypot — hidden from real users, catches bots -->
                <input type="text" name="website" class="honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" placeholder="Jane Smith" required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="jane@company.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="message">Tell Us About Your Project</label>
                    <textarea id="message" name="message" placeholder="We're looking to build…" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i data-lucide="send"></i>
                    Send Message
                </button>
            </form>

        </div>
    </section>

</main>


<!-- ============================================================
     FOOTER — modern layout with social icons + dynamic year
     ============================================================ -->
<footer>
    <div class="container">

        <!-- Top row: brand left, nav + socials right -->
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">INVINCIBLE</div>
                <p class="footer-tagline">
                    We build brands that are<br>truly invincible.
                </p>
            </div>

            <div class="footer-social">
                <!-- Quick navigation links -->
                <nav class="footer-nav-group" aria-label="Footer navigation">
                    <a href="#services">Services</a>
                    <a href="#work">Work</a>
                    <a href="#about">About</a>
                    <a href="#contact">Contact</a>
                </nav>

                <!-- Social media icon links -->
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Twitter / X">
                        <i data-lucide="twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i data-lucide="linkedin"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Dribbble">
                        <i data-lucide="dribbble"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="GitHub">
                        <i data-lucide="github"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i data-lucide="instagram"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        <!-- Bottom row: copyright (year set by JS) + legal links -->
        <div class="footer-bottom">
            <p class="footer-copyright">
                &copy; <span id="currentYear"></span> Invincible<span>.</span>
                All rights reserved.
            </p>
            <nav class="footer-legal" aria-label="Legal links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </nav>
        </div>

    </div>
</footer>

</body>
</html>
