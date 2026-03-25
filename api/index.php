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
    <style>
      /* ── FONT IMPORTS ─────────────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap');
@property --border-angle {
    syntax: '<angle>';
    initial-value: 0deg;
    inherits: false;
}
*, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --orange:          #FFA500;
    --orange-dim:      rgba(255, 165, 0, 0.15);
    --orange-glow:     rgba(255, 165, 0, 0.35);
    --orange-border:   rgba(255, 165, 0, 0.08);
    --dark-bg:         #080808;
    --card-bg:         #111111;
    --card-bg-alt:     #161616;
    --text-primary:    #f5f5f5;
    --text-secondary:  #888;
    --border:          #222;
    --border-subtle:   #1a1a1a;
    --font-display:    'Syne', sans-serif;
    --font-body:       'DM Sans', sans-serif;
    --radius:          14px;
    --transition:      0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

html {
    scroll-behavior: smooth;
    font-size: 16px;
}

body {
    font-family: var(--font-body);
    background: var(--dark-bg);
    color: var(--text-primary);
    line-height: 1.6;
    overflow-x: hidden;
    background-image:
        radial-gradient(ellipse 80% 50% at 50% -10%, rgba(255,165,0,0.07), transparent),
        url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

/* ── KEYFRAMES ────────────────────────────────────────────── */

/* Rotating gradient border for cards */
@keyframes border-spin {
    to { --border-angle: 360deg; }
}

/* Ambient pulse glow for icon containers */
@keyframes icon-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255, 165, 0, 0); }
    50%       { box-shadow: 0 0 20px 4px rgba(255, 165, 0, 0.15); }
}

/* Floating orbs in hero */
@keyframes float-orb {
    0%, 100% { transform: translateY(0) scale(1); }
    50%       { transform: translateY(-30px) scale(1.05); }
}

/* Marquee strip ticker */
@keyframes marquee {
    from { transform: translateX(0); }
    to   { transform: translateX(-50%); }
}

/* Subtle shimmer on footer logo */
@keyframes shimmer {
    0%   { background-position: -200% center; }
    100% { background-position:  200% center; }
}

/* ── HEADER ───────────────────────────────────────────────── */
header {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 1000;
    /* Initially hidden — GSAP slides it in on load */
    background: rgba(8, 8, 8, 0.85);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid var(--border-subtle);
}

nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.logo {
    font-family: var(--font-display);
    font-size: 1.15rem;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--text-primary);
    text-decoration: none;
}

/* Animated underline dot on logo */
.logo span {
    color: var(--orange);
}

.nav-links {
    display: flex;
    gap: 2.5rem;
    list-style: none;
    align-items: center;
}

.nav-links a {
    text-decoration: none;
    color: var(--text-secondary);
    font-size: 0.9rem;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: color var(--transition);
    position: relative;
}

.nav-links a::after {
    content: '';
    position: absolute;
    bottom: -3px; left: 0;
    width: 0; height: 1px;
    background: var(--orange);
    transition: width var(--transition);
}

.nav-links a:hover {
    color: var(--text-primary);
}

.nav-links a:hover::after {
    width: 100%;
}

/* Nav CTA button */
.btn-cta {
    background: var(--orange);
    color: #000;
    padding: 0.6rem 1.4rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
    text-decoration: none;
    letter-spacing: 0.3px;
    transition: all var(--transition);
    border: 1px solid var(--orange);
}

.btn-cta:hover {
    background: transparent;
    color: var(--orange);
    box-shadow: 0 0 20px rgba(255, 165, 0, 0.3);
}

/* Remove underline override for CTA */
.btn-cta::after { display: none; }

/* Mobile hamburger — hidden on desktop */
.mobile-toggle {
    display: none;
    flex-direction: column;
    gap: 5px;
    cursor: pointer;
    padding: 4px;
}

.mobile-toggle span {
    width: 22px; height: 1.5px;
    background: var(--text-primary);
    border-radius: 2px;
    transition: all 0.3s ease;
    display: block;
}

/* Animate hamburger → X when active */
.mobile-toggle.active span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
.mobile-toggle.active span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.mobile-toggle.active span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }

/* ── HERO ─────────────────────────────────────────────────── */
.hero {
    padding: 13rem 0 8rem;
    position: relative;
    overflow: hidden;
    min-height: 100vh;
    display: flex;
    align-items: center;
}

/* Decorative floating orbs — actual divs so GSAP can target them */
.hero-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    filter: blur(80px);
    will-change: transform;
}

.hero-orb-1 {
    width: 600px; height: 600px;
    top: -20%; left: -15%;
    background: radial-gradient(circle, rgba(255,165,0,0.07) 0%, transparent 70%);
    animation: float-orb 12s ease-in-out infinite;
}

.hero-orb-2 {
    width: 500px; height: 500px;
    bottom: -15%; right: -10%;
    background: radial-gradient(circle, rgba(255,165,0,0.05) 0%, transparent 70%);
    animation: float-orb 16s ease-in-out infinite reverse;
}

.hero-content {
    text-align: center;
    position: relative;
    z-index: 1;
    width: 100%;
}

/* Badge pill */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255, 165, 0, 0.08);
    border: 1px solid rgba(255, 165, 0, 0.2);
    padding: 0.5rem 1.1rem;
    border-radius: 50px;
    font-size: 0.82rem;
    font-weight: 500;
    letter-spacing: 0.5px;
    margin-bottom: 2.5rem;
    color: var(--orange);
}

.hero-badge .badge-icon {
    width: 14px; height: 14px;
    color: var(--orange);
}

/* Headline line masking for GSAP wipe-up animation */
.hero-title {
    font-family: var(--font-display);
    font-size: clamp(3rem, 7vw, 5.5rem);
    font-weight: 800;
    line-height: 1.08;
    margin-bottom: 2rem;
    letter-spacing: -0.03em;
}

/* Each line wrapped to mask the GSAP slide-up */
.line-wrap {
    display: block;
    overflow: hidden;
    padding-bottom: 0.1em; /* Prevents descenders from clipping */
}

.line-inner {
    display: block;
}

.highlight { color: var(--orange); }

.hero-subtitle {
    font-size: 1.1rem;
    color: var(--text-secondary);
    max-width: 620px;
    margin: 0 auto 3rem;
    line-height: 1.8;
    font-weight: 300;
}

/* CTA button row */
.hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 6rem;
}

/* ── BUTTONS ──────────────────────────────────────────────── */
.btn {
    padding: 0.95rem 2.2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition);
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: var(--font-body);
    letter-spacing: 0.2px;
}

.btn-primary {
    background: var(--orange);
    color: #000;
    border-color: var(--orange);
}

.btn-primary:hover {
    background: transparent;
    color: var(--orange);
    box-shadow: 0 0 30px rgba(255, 165, 0, 0.25), inset 0 0 0 1px var(--orange);
    transform: translateY(-2px);
}

.btn-secondary {
    background: transparent;
    color: var(--text-primary);
    border-color: var(--border);
}

.btn-secondary:hover {
    border-color: rgba(255, 165, 0, 0.4);
    color: var(--orange);
    transform: translateY(-2px);
}

/* ── HERO STATS ───────────────────────────────────────────── */
.hero-stats {
    display: flex;
    gap: 5rem;
    justify-content: center;
    padding-top: 4rem;
    border-top: 1px solid var(--border-subtle);
    max-width: 600px;
    margin: 0 auto;
}

.stat { text-align: center; }

.stat-number {
    font-family: var(--font-display);
    font-size: 2.8rem;
    font-weight: 800;
    color: var(--orange);
    display: block;
    line-height: 1;
}

.stat-label {
    font-size: 0.82rem;
    color: var(--text-secondary);
    margin-top: 0.5rem;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    font-weight: 400;
}

/* ── MARQUEE TICKER STRIP ─────────────────────────────────── */
.marquee-strip {
    overflow: hidden;
    border-top: 1px solid var(--border-subtle);
    border-bottom: 1px solid var(--border-subtle);
    padding: 1rem 0;
    background: var(--card-bg);
    white-space: nowrap;
}

.marquee-track {
    display: inline-flex;
    gap: 0;
    animation: marquee 25s linear infinite;
    /* Duplicate content fills the loop gap */
}

.marquee-item {
    font-family: var(--font-display);
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--text-secondary);
    padding: 0 2.5rem;
}

.marquee-item.dot {
    color: var(--orange);
    padding: 0;
}

/* ── SECTION HEADER ───────────────────────────────────────── */
.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-label {
    display: inline-block;
    color: var(--orange);
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 3px;
    margin-bottom: 1.2rem;
    padding: 0.35rem 1rem;
    border: 1px solid rgba(255,165,0,0.2);
    border-radius: 50px;
    background: rgba(255,165,0,0.05);
}

.section-header h2 {
    font-family: var(--font-display);
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 1.2rem;
    letter-spacing: -0.02em;
}

.section-header p {
    font-size: 1rem;
    color: var(--text-secondary);
    max-width: 580px;
    margin: 0 auto;
    font-weight: 300;
    line-height: 1.8;
}

/* ── GLOWING CARD BORDER MIXIN ────────────────────────────── */
/*
   The rotating gradient border technique:
   - border: 1.5px solid transparent  (makes space for the gradient)
   - background: card-color as padding-box, conic-gradient as border-box
   - The conic-gradient rotates via --border-angle custom property
   - @property enables CSS to animate this value
*/
.glow-card {
    position: relative;
    border: 1.5px solid transparent;
    border-radius: var(--radius);
    background:
        linear-gradient(var(--card-bg), var(--card-bg)) padding-box,
        conic-gradient(
            from var(--border-angle),
            rgba(255, 165, 0, 0.06) 0deg,
            rgba(255, 165, 0, 0.06) 200deg,
            rgba(255, 165, 0, 0.55) 240deg,
            rgba(255, 210, 80, 0.85) 260deg,
            rgba(255, 165, 0, 0.55) 280deg,
            rgba(255, 165, 0, 0.06) 320deg,
            rgba(255, 165, 0, 0.06) 360deg
        ) border-box;
    animation: border-spin 7s linear infinite;
    transition: box-shadow var(--transition), transform var(--transition), animation-duration 0.1s;
}

.glow-card:hover {
    animation-duration: 2.5s; /* Speeds up on hover */
    box-shadow:
        0 0 30px rgba(255, 165, 0, 0.08),
        0 20px 60px rgba(0,0,0,0.5),
        inset 0 1px 0 rgba(255,255,255,0.04);
    transform: translateY(-6px);
    background:
        linear-gradient(var(--card-bg), var(--card-bg)) padding-box,
        conic-gradient(
            from var(--border-angle),
            rgba(255, 165, 0, 0.15) 0deg,
            rgba(255, 165, 0, 0.15) 200deg,
            rgba(255, 165, 0, 0.9) 240deg,
            rgba(255, 220, 80, 1) 260deg,
            rgba(255, 165, 0, 0.9) 280deg,
            rgba(255, 165, 0, 0.15) 320deg,
            rgba(255, 165, 0, 0.15) 360deg
        ) border-box;
}

/* ── SERVICES / FEATURES SECTION ──────────────────────────── */
.features {
    padding: 7rem 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.feature-card {
    padding: 2.25rem;
    /* glow-card provides border + bg */
    opacity: 0; /* GSAP animates this in */
    transform: translateY(30px);
}

/* Lucide icon container */
.feature-icon {
    width: 46px; height: 46px;
    background: var(--orange-dim);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: var(--orange);
    animation: icon-pulse 4s ease-in-out infinite;
}

.feature-icon svg {
    width: 20px; height: 20px;
    stroke-width: 1.75;
}

.feature-card h3 {
    font-family: var(--font-display);
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 0.7rem;
    letter-spacing: -0.01em;
}

.feature-card p {
    color: var(--text-secondary);
    font-size: 0.92rem;
    line-height: 1.7;
    font-weight: 300;
}

/* ── PROJECTS SECTION ─────────────────────────────────────── */
.projects {
    padding: 7rem 0;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
}

.project-card {
    overflow: hidden;
    opacity: 0;
    transform: translateY(30px);
}

/* Image placeholder area */
.project-image {
    height: 220px;
    background: linear-gradient(135deg, #1a1000 0%, #0f0c00 50%, #080808 100%);
    position: relative;
    overflow: hidden;
}

/* Subtle shimmer overlay on project image */
.project-image::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, transparent 40%, rgba(255,165,0,0.04) 60%, transparent 80%);
}

/* Arrow link icon in top-right corner */
.project-icon {
    position: absolute;
    top: 1rem; right: 1rem;
    width: 36px; height: 36px;
    background: rgba(255, 165, 0, 0.1);
    border: 1px solid rgba(255, 165, 0, 0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--orange);
    transition: all var(--transition);
    z-index: 1;
}

.project-icon svg {
    width: 15px; height: 15px;
    stroke-width: 2;
}

.project-card:hover .project-icon {
    background: var(--orange);
    color: #000;
    transform: rotate(45deg);
}

.project-info {
    padding: 1.75rem;
}

.project-tags {
    font-size: 0.7rem;
    color: var(--orange);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 0.6rem;
    font-weight: 600;
}

.project-info h3 {
    font-family: var(--font-display);
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    letter-spacing: -0.01em;
}

.project-info p {
    font-size: 0.88rem;
    color: var(--text-secondary);
    line-height: 1.65;
    font-weight: 300;
}

/* ── ABOUT / VALUES SECTION ───────────────────────────────── */
.about {
    padding: 7rem 0;
    position: relative;
}

/* Decorative vertical line */
.about::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 5%;
    height: 90%;
    width: 1px;
    background: linear-gradient(to bottom, transparent, var(--border), transparent);
    transform: translateX(-50%);
    pointer-events: none;
}

.about-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-top: 3.5rem;
}

.value-card {
    padding: 2rem;
    opacity: 0;
    transform: translateY(30px);
}

.value-icon {
    width: 44px; height: 44px;
    background: var(--orange-dim);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    color: var(--orange);
}

.value-icon svg {
    width: 20px; height: 20px;
    stroke-width: 1.75;
}

.value-card h3 {
    font-family: var(--font-display);
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.value-card p {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.7;
    font-weight: 300;
}

/* ── TESTIMONIALS ─────────────────────────────────────────── */
.testimonials {
    padding: 7rem 0;
}

.testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.testimonial {
    padding: 2rem;
    opacity: 0;
    transform: translateY(30px);
}

.stars {
    display: flex;
    gap: 3px;
    margin-bottom: 1.25rem;
    color: var(--orange);
}

.stars svg {
    width: 15px; height: 15px;
    fill: var(--orange);
    stroke: none;
}

.testimonial-content {
    font-size: 0.92rem;
    line-height: 1.8;
    margin-bottom: 1.5rem;
    color: var(--text-secondary);
    font-style: italic;
    font-weight: 300;
}

.testimonial-author h4 {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.2rem;
    letter-spacing: -0.01em;
}

.testimonial-author p {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

/* ── CTA SECTION ──────────────────────────────────────────── */
.cta {
    padding: 8rem 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Big background text decoration */
.cta::before {
    content: 'INVINCIBLE';
    position: absolute;
    font-family: var(--font-display);
    font-size: clamp(5rem, 14vw, 14rem);
    font-weight: 800;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255,165,0,0.04);
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    white-space: nowrap;
    pointer-events: none;
    letter-spacing: -0.02em;
}

.cta h2 {
    font-family: var(--font-display);
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 1.5rem;
    letter-spacing: -0.03em;
    position: relative;
}

.cta p {
    font-size: 1.05rem;
    color: var(--text-secondary);
    max-width: 620px;
    margin: 0 auto 3rem;
    line-height: 1.8;
    font-weight: 300;
    position: relative;
}

.contact-info {
    display: flex;
    gap: 2.5rem;
    justify-content: center;
    margin-top: 3rem;
    font-size: 0.9rem;
    position: relative;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    transition: color var(--transition);
}

.contact-item:hover { color: var(--text-primary); }

.contact-item svg {
    width: 16px; height: 16px;
    color: var(--orange);
    flex-shrink: 0;
    stroke-width: 1.75;
}

/* ── CONTACT FORM ─────────────────────────────────────────── */
.contact-form {
    max-width: 560px;
    margin: 4rem auto 0;
    position: relative;
}

.form-group { margin-bottom: 1.5rem; }

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    font-size: 0.88rem;
    color: var(--text-secondary);
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

input, textarea {
    width: 100%;
    padding: 0.95rem 1.25rem;
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--text-primary);
    font-size: 0.95rem;
    font-family: var(--font-body);
    transition: all var(--transition);
}

input:focus, textarea:focus {
    outline: none;
    border-color: rgba(255, 165, 0, 0.5);
    background: var(--card-bg-alt);
    box-shadow: 0 0 0 4px rgba(255, 165, 0, 0.06);
}

textarea {
    resize: vertical;
    min-height: 140px;
    line-height: 1.6;
}

/* Honeypot — off-screen, never shown to real users */
.honeypot {
    position: absolute;
    left: -9999px;
    opacity: 0;
    pointer-events: none;
}
/* Form feedback message */
.form-message {
    padding: 0.9rem 1.2rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: none;
    font-size: 0.9rem;
    font-weight: 500;
    align-items: center;
    gap: 0.5rem;
}

.form-message.success {
    background: rgba(34, 197, 94, 0.08);
    color: #4ade80;
    border: 1px solid rgba(34, 197, 94, 0.2);
}

.form-message.error {
    background: rgba(239, 68, 68, 0.08);
    color: #f87171;
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.form-message.show { display: flex; }

/* ── FOOTER ───────────────────────────────────────────────── */
footer {
    border-top: 1px solid var(--border-subtle);
    padding: 5rem 0 2.5rem;
    margin-top: 4rem;
    position: relative;
    overflow: hidden;
}

/* Large watermark */
footer::before {
    content: 'INC';
    position: absolute;
    font-family: var(--font-display);
    font-size: 20vw;
    font-weight: 800;
    color: transparent;
    -webkit-text-stroke: 1px rgba(255,165,0,0.03);
    right: -2%;
    bottom: -10%;
    pointer-events: none;
    letter-spacing: -0.05em;
}

.footer-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 4rem;
}

.footer-brand {}

.footer-logo {
    font-family: var(--font-display);
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    text-transform: uppercase;
    /* Shimmer gradient animation on footer logo */
    background: linear-gradient(
        90deg,
        var(--text-primary) 0%,
        var(--orange) 25%,
        var(--text-primary) 50%,
        var(--orange) 75%,
        var(--text-primary) 100%
    );
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer 6s linear infinite;
    display: inline-block;
    margin-bottom: 0.75rem;
}

.footer-tagline {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 300;
    max-width: 260px;
    line-height: 1.6;
}

/* Social icon cluster */
.footer-social {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 1.5rem;
}

.footer-nav-group {
    display: flex;
    gap: 2.5rem;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.footer-nav-group a {
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 400;
    transition: color var(--transition);
}

.footer-nav-group a:hover { color: var(--text-primary); }

.social-links {
    display: flex;
    gap: 0.75rem;
}

.social-link {
    width: 42px; height: 42px;
    border-radius: 50%;
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all var(--transition);
    background: var(--card-bg);
}

.social-link svg {
    width: 17px; height: 17px;
    stroke-width: 1.75;
}

.social-link:hover {
    border-color: var(--orange);
    color: var(--orange);
    background: var(--orange-dim);
    box-shadow: 0 0 20px rgba(255,165,0,0.15);
    transform: translateY(-3px);
}

/* Footer divider */
.footer-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border), transparent);
    margin-bottom: 2rem;
}

/* Footer bottom row */
.footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.footer-copyright {
    font-size: 0.82rem;
    color: var(--text-secondary);
    font-weight: 300;
}

.footer-copyright span { color: var(--orange); }

.footer-legal {
    display: flex;
    gap: 2rem;
}

.footer-legal a {
    font-size: 0.82rem;
    color: var(--text-secondary);
    text-decoration: none;
    transition: color var(--transition);
}

.footer-legal a:hover { color: var(--text-primary); }

/* ── RESPONSIVE ───────────────────────────────────────────── */
@media (max-width: 1024px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .mobile-toggle { display: flex; }

    .nav-links {
        position: fixed;
        top: 68px; left: 0; right: 0;
        background: rgba(8,8,8,0.98);
        backdrop-filter: blur(20px);
        flex-direction: column;
        padding: 2rem;
        border-bottom: 1px solid var(--border);
        transform: translateY(-120%);
        opacity: 0;
        transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94),
                    opacity 0.3s ease;
        gap: 1.5rem;
        pointer-events: none;
    }

    .nav-links.active {
        transform: translateY(0);
        opacity: 1;
        pointer-events: all;
    }

    .hero { padding: 10rem 0 5rem; min-height: auto; }
    .hero-title { font-size: clamp(2.4rem, 8vw, 3.5rem); }
    .hero-buttons { flex-direction: column; align-items: center; }
    .hero-stats { gap: 2.5rem; }

    .features-grid,
    .projects-grid,
    .about-grid,
    .testimonials-grid { grid-template-columns: 1fr; }

    .about::before { display: none; }

    .contact-info { flex-direction: column; align-items: center; gap: 1rem; }

    .footer-top {
        flex-direction: column;
        gap: 2.5rem;
    }

    .footer-social {
        align-items: flex-start;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 1.25rem;
        text-align: center;
    }

    .footer-legal { justify-content: center; }

    .cta::before { font-size: 4rem; }
}

@media (max-width: 480px) {
    .container { padding: 0 1.25rem; }
    .hero-stats { flex-direction: column; gap: 2rem; }
}

    </style>

    <!-- Lucide icon library (replaces all emoji icons with clean SVGs) -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>

    <!-- GSAP core + ScrollTrigger plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
    <!-- GSAP ScrollTo plugin (used in smooth scroll) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollToPlugin.min.js" defer></script>

    <!-- App logic (runs after all deferred scripts above) -->

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


 <script defer>
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

      
    </script>
</body>
</html>
