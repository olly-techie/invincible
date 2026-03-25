<?php
/**
 * ============================================================
 * INVINCIBLE STUDIO — Single-File Landing Page
 * ============================================================
 *
 * This file serves a dual purpose:
 *   1. Acts as a PHP backend that handles contact form submissions
 *      via POST requests, validates input, and appends messages
 *      to a local `messages.json` file.
 *   2. Renders the full HTML landing page (on GET requests).
 *
 * Structure:
 *   - PHP Backend (top of file, runs before any HTML output)
 *   - HTML Document (DOCTYPE → <html> → <head> → <body>)
 *     - <head>: Meta tags, SEO, Open Graph, Schema.org JSON-LD, CSS styles
 *     - <body>: Header/Nav, Hero, Services, Work, About, Testimonials,
 *               Contact/CTA, Footer
 *     - <script>: Mobile nav toggle, scroll animations, form AJAX submission
 * ============================================================
 */


/* ============================================================
 * SECTION 1: PHP FORM HANDLER
 * ============================================================
 * This block only executes when the page receives a POST request
 * (i.e., when the contact form is submitted via AJAX).
 * It validates input, then writes the data to messages.json.
 * After echoing a JSON response, it exits immediately so no
 * HTML is rendered for POST requests.
 * ============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // All responses from this handler are JSON
    header('Content-Type: application/json');

    /**
     * Sanitise a single string value from user input.
     * - trim()             → removes leading/trailing whitespace
     * - htmlspecialchars() → converts special chars to HTML entities
     *                        (prevents XSS if data is ever rendered)
     * - ENT_QUOTES         → encodes both single and double quotes
     * - 'UTF-8'            → explicit charset to avoid encoding edge cases
     *
     * @param  string $data  Raw input value
     * @return string        Sanitised value
     */
    function clean($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /* ----------------------------------------------------------
     * HONEYPOT SPAM CHECK
     * A hidden "website" field is in the HTML form but is visually
     * hidden from real users (positioned off-screen, tab-index -1).
     * Bots that blindly fill every field will populate it.
     * If it's non-empty, we silently reject the submission.
     * ---------------------------------------------------------- */
    if (!empty($_POST['website'])) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Spam detected'
        ]);
        exit;
    }

    /* ----------------------------------------------------------
     * COLLECT & SANITISE FORM FIELDS
     * The null-coalescing operator (??) returns an empty string
     * if the key doesn't exist in $_POST, preventing notices.
     * ---------------------------------------------------------- */
    $name    = clean($_POST['name']    ?? '');
    $email   = clean($_POST['email']   ?? '');
    $message = clean($_POST['message'] ?? '');

    /* ----------------------------------------------------------
     * REQUIRED FIELDS VALIDATION
     * All three fields must be non-empty strings after cleaning.
     * ---------------------------------------------------------- */
    if (!$name || !$email || !$message) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'All fields required'
        ]);
        exit;
    }

    /* ----------------------------------------------------------
     * EMAIL FORMAT VALIDATION
     * PHP's built-in FILTER_VALIDATE_EMAIL checks RFC-compliant
     * email format — returns false if invalid.
     * ---------------------------------------------------------- */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid email'
        ]);
        exit;
    }

    /* ----------------------------------------------------------
     * BUILD THE MESSAGE ENTRY
     * Captures the submitter's IP for basic logging/audit purposes.
     * REMOTE_ADDR may be missing in some server configs (e.g. CLI),
     * so we fall back to 'unknown'.
     * date('c') produces an ISO 8601 timestamp (e.g. 2024-06-01T12:30:00+00:00).
     * ---------------------------------------------------------- */
    $entry = [
        'name'      => $name,
        'email'     => $email,
        'message'   => $message,
        'ip'        => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'timestamp' => date('c')
    ];

    /* ----------------------------------------------------------
     * PERSIST TO messages.json
     * file_put_contents() with FILE_APPEND adds a new JSON line
     * each time (newline-delimited JSON / NDJSON format).
     * LOCK_EX acquires an exclusive lock to prevent race conditions
     * when multiple users submit simultaneously.
     * Returns the number of bytes written on success, or false on failure.
     * ---------------------------------------------------------- */
    $success = file_put_contents(
        'messages.json',
        json_encode($entry) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    /* ----------------------------------------------------------
     * RESPOND WITH SUCCESS OR FAILURE
     * Ternary: if $success is not false → send success response,
     * otherwise send a generic error message.
     * ---------------------------------------------------------- */
    echo json_encode(
        $success !== false
            ? ['status' => 'success', 'message' => 'Message sent successfully!']
            : ['status' => 'error',   'message' => 'Failed to save. Try again.']
    );

    // Stop execution — do not render any HTML for POST requests
    exit;
}

?><!DOCTYPE html>
<!--
    ============================================================
    HTML DOCUMENT
    The PHP block above has already exited if this was a POST
    request. Everything below is only rendered for GET requests.
    ============================================================
-->
<html lang="en">

<head>
    <!-- ========================================================
         META & SEO
         ======================================================== -->

    <!-- Character encoding — always declare first -->
    <meta charset="UTF-8">

    <!-- Responsive viewport: width follows device, 1:1 initial scale -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Primary page title (shown in browser tab & search results) -->
    <title>INVINCIBLE — Design, Development &amp; AI Studio | Premium Digital Solutions</title>

    <!-- Meta description — used as the snippet in search engine results -->
    <meta name="description" content="We build brands that are truly invincible. From stunning visuals to powerful digital products, we transform your vision into experiences that captivate, convert, and endure.">

    <!-- Keywords — less influential for modern SEO but still included -->
    <meta name="keywords" content="web design, web development, AI solutions, graphics design, motion design, UI/UX design, app development, digital agency, branding">

    <!-- Author attribution -->
    <meta name="author" content="Invincible Studio">

    <!-- Tell search bots to index this page and follow its links -->
    <meta name="robots" content="index, follow">


    <!-- ========================================================
         OPEN GRAPH (OG) TAGS
         Controls how the page looks when shared on Facebook,
         LinkedIn, WhatsApp, and other OG-compatible platforms.
         ======================================================== -->
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="https://yourdomain.com/">
    <meta property="og:title"       content="INVINCIBLE — Design, Development &amp; AI Studio">
    <meta property="og:description" content="We build brands that are truly invincible. Transform your vision into digital excellence.">
    <!-- og:image should be at least 1200×630px for best display -->
    <meta property="og:image"       content="https://yourdomain.com/og-image.jpg">


    <!-- ========================================================
         TWITTER CARD TAGS
         Controls how the page looks when shared on Twitter/X.
         summary_large_image = big banner-style card.
         ======================================================== -->
    <meta property="twitter:card"        content="summary_large_image">
    <meta property="twitter:url"         content="https://yourdomain.com/">
    <meta property="twitter:title"       content="INVINCIBLE — Design, Development &amp; AI Studio">
    <meta property="twitter:description" content="We build brands that are truly invincible. Transform your vision into digital excellence.">
    <meta property="twitter:image"       content="https://yourdomain.com/og-image.jpg">


    <!-- ========================================================
         FAVICON
         Inline SVG favicon using a data URI — no separate file needed.
         The fire emoji renders as the tab icon in modern browsers.
         ======================================================== -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🔥</text></svg>">


    <!-- ========================================================
         SCHEMA.ORG STRUCTURED DATA (JSON-LD)
         Helps search engines understand the entity behind this page.
         Google uses this to potentially show rich results.
         @type: Organization — the most appropriate type for a studio.
         ======================================================== -->
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


    <!-- ========================================================
         STYLES
         All CSS is inlined for single-file portability.
         Sections are clearly labelled below.
         ======================================================== -->
    <style>

        /* --------------------------------------------------------
         * FONT IMPORT
         * Loading Inter from Google Fonts — a clean, versatile
         * sans-serif used extensively in modern UI design.
         * Weights: 300 (light) through 900 (black).
         * -------------------------------------------------------- */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');


        /* --------------------------------------------------------
         * GLOBAL RESET
         * Remove default browser margins/padding and use
         * border-box sizing so padding doesn't expand element width.
         * -------------------------------------------------------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* --------------------------------------------------------
         * CSS CUSTOM PROPERTIES (DESIGN TOKENS)
         * Centralised colour/value definitions on :root so they
         * can be reused throughout and changed in one place.
         * -------------------------------------------------------- */
        :root {
            --orange:         #FFA500;  /* Primary brand accent colour */
            --dark-bg:        #0a0a0a;  /* Page background — near black */
            --card-bg:        #1a1a1a;  /* Card/panel background */
            --text-primary:   #fff;     /* Main body text */
            --text-secondary: #999;     /* Muted/secondary text */
            --border:         #2a2a2a;  /* Subtle border colour */
        }


        /* --------------------------------------------------------
         * BASE DOCUMENT STYLES
         * -------------------------------------------------------- */

        /* Smooth anchor scroll for in-page links (#section) */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden; /* Prevent horizontal scroll on mobile */
        }


        /* --------------------------------------------------------
         * LAYOUT CONTAINER
         * Constrains content to 1200px max-width and adds
         * horizontal padding so content doesn't hug the edges
         * on smaller screens.
         * -------------------------------------------------------- */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }


        /* --------------------------------------------------------
         * HEADER & NAVIGATION
         * Fixed to the top of the viewport with a blur backdrop
         * so content is visible beneath it as the user scrolls.
         * -------------------------------------------------------- */
        header {
            position: fixed;         /* Stays at top while scrolling */
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;           /* Above all page content */
            background: rgba(10, 10, 10, 0.95); /* Near-opaque dark bg */
            backdrop-filter: blur(10px);         /* Frosted-glass effect */
            border-bottom: 1px solid var(--border);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Studio wordmark / logo text */
        .logo {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Desktop navigation link list */
        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--text-primary);
        }

        /* "Start a Project" CTA button in the nav */
        .btn-cta {
            background: var(--orange);
            color: var(--dark-bg);
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 165, 0, 0.3);
        }

        /* Hamburger menu icon — hidden on desktop, shown on mobile */
        .mobile-toggle {
            display: none;            /* Hidden by default */
            flex-direction: column;
            gap: 0.35rem;
            cursor: pointer;
        }

        /* Each bar of the hamburger icon */
        .mobile-toggle span {
            width: 24px;
            height: 2px;
            background: var(--text-primary);
            transition: all 0.3s ease;
        }


        /* --------------------------------------------------------
         * HERO SECTION
         * Full-width introductory section with headline, sub-copy,
         * CTA buttons, and stat counters.
         * Uses pseudo-elements (::before / ::after) for decorative
         * radial gradient glows in the background.
         * -------------------------------------------------------- */
        .hero {
            padding: 12rem 0 8rem; /* Large top padding clears fixed header */
            position: relative;
            overflow: hidden;
        }

        /* Top-left decorative orange glow */
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.08) 0%, transparent 70%);
            pointer-events: none; /* Does not block mouse events */
        }

        /* Bottom-right decorative orange glow */
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            right: -10%;
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Small pill-shaped badge above the headline */
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            margin-bottom: 2rem;
            color: var(--orange);
        }

        /* Lightning bolt icon prepended to the badge via CSS */
        .hero-badge::before {
            content: '⚡';
            font-size: 1rem;
        }

        /* Centre-aligned hero content, layered above the pseudo-elements */
        .hero-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        /* Main headline */
        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em; /* Tight tracking for large display type */
        }

        /* Orange accent on keywords inside the headline */
        .hero h1 .highlight {
            color: var(--orange);
        }

        /* Subtitle / sub-headline paragraph */
        .hero p {
            font-size: 1.15rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto 3rem;
            line-height: 1.7;
        }

        /* Container for the two CTA buttons */
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 5rem;
        }


        /* --------------------------------------------------------
         * GENERIC BUTTON STYLES
         * .btn is the base; .btn-primary and .btn-secondary are variants.
         * -------------------------------------------------------- */
        .btn {
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        /* Filled orange button */
        .btn-primary {
            background: var(--orange);
            color: var(--dark-bg);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.3);
        }

        /* Ghost / outline button */
        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--text-primary);
        }


        /* --------------------------------------------------------
         * HERO STATISTICS ROW
         * Three KPIs displayed horizontally below the CTA buttons.
         * -------------------------------------------------------- */
        .hero-stats {
            display: flex;
            gap: 4rem;
            justify-content: center;
        }

        .stat {
            text-align: center;
        }

        /* Large numeric value */
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--orange);
            display: block;
        }

        /* Descriptive label below the number */
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }


        /* --------------------------------------------------------
         * REUSABLE SECTION HEADER
         * Used in every major section — centres a label, heading,
         * and optional description paragraph.
         * -------------------------------------------------------- */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        /* Small all-caps orange eyebrow label */
        .section-label {
            color: var(--orange);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 1rem;
        }

        .section-header h2 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .section-header h2 .highlight {
            color: var(--orange);
        }

        .section-header p {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 650px;
            margin: 0 auto;
        }


        /* --------------------------------------------------------
         * SERVICES / FEATURES SECTION
         * 2-column card grid listing what the studio offers.
         * Cards animate in via IntersectionObserver (JS below).
         * -------------------------------------------------------- */
        .features {
            padding: 6rem 0;
        }

        /* Responsive 2-column grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        /* Individual service card — starts invisible, animates in */
        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 2.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            /* Initial state: invisible + shifted down (for scroll animation) */
            opacity: 0;
            transform: translateY(20px);
        }

        /* JS adds .visible once card enters the viewport */
        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-card:hover {
            border-color: rgba(255, 165, 0, 0.3);
            transform: translateY(-5px); /* Subtle lift on hover */
        }

        /* Icon container — a small square with an orange tint border */
        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--orange);
        }

        .feature-card h3 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }


        /* --------------------------------------------------------
         * PORTFOLIO / PROJECTS SECTION
         * 2-column grid of project cards with a 16:10 aspect ratio.
         * -------------------------------------------------------- */
        .projects {
            padding: 6rem 0;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        /* Project card — same scroll animation pattern as feature cards */
        .project-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            position: relative;
            aspect-ratio: 16 / 10; /* Maintains consistent card proportions */
        }

        .project-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .project-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 165, 0, 0.3);
        }

        /* Placeholder image area (top 65% of the card) */
        .project-image {
            width: 100%;
            height: 65%;
            background: linear-gradient(135deg, #2a1a0f 0%, #1a1410 100%);
            position: relative;
        }

        /* Small arrow icon in the top-right corner of the image area */
        .project-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--orange);
            font-size: 1.2rem;
        }

        /* Bottom info panel of the project card */
        .project-info {
            padding: 1.5rem;
        }

        /* Service category tags */
        .project-tags {
            font-size: 0.75rem;
            color: var(--orange);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .project-info h3 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .project-info p {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }


        /* --------------------------------------------------------
         * ABOUT / VALUES SECTION
         * Studio mission statement + 2-column values grid.
         * -------------------------------------------------------- */
        .about {
            padding: 6rem 0;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }

        /* Individual value card — same scroll-in animation */
        .value-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 2rem;
            border-radius: 12px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .value-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Icon box — same style as feature cards */
        .value-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1.25rem;
            color: var(--orange);
        }

        .value-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .value-card p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.6;
        }


        /* --------------------------------------------------------
         * TESTIMONIALS SECTION
         * 3-column grid of client quotes with star ratings.
         * -------------------------------------------------------- */
        .testimonials {
            padding: 6rem 0;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        /* Individual testimonial card */
        .testimonial {
            background: var(--card-bg);
            border: 1px solid var(--border);
            padding: 2rem;
            border-radius: 12px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        .testimonial.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Star rating row */
        .stars {
            color: var(--orange);
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        /* Quote text */
        .testimonial-content {
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
        }

        /* Name and role of the reviewer */
        .testimonial-author h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .testimonial-author p {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }


        /* --------------------------------------------------------
         * CALL TO ACTION (CTA) / CONTACT SECTION
         * Large centred headline with contact form below.
         * -------------------------------------------------------- */
        .cta {
            padding: 6rem 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .cta h2 .highlight {
            color: var(--orange);
        }

        .cta p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        /* Row of contact info items (email, location) */
        .contact-info {
            display: flex;
            gap: 3rem;
            justify-content: center;
            margin-top: 3rem;
            font-size: 0.95rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
        }

        /* Icon prepended via CSS using ::before with class-specific content */
        .contact-item::before {
            color: var(--orange);
        }

        .contact-item.email::before {
            content: '✉';
        }

        .contact-item.location::before {
            content: '🌍';
        }


        /* --------------------------------------------------------
         * FOOTER
         * Simple two-row footer with logo, copyright, and links.
         * -------------------------------------------------------- */
        footer {
            border-top: 1px solid var(--border);
            padding: 3rem 0 2rem;
            margin-top: 6rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .footer-text {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--text-primary);
        }


        /* --------------------------------------------------------
         * CONTACT FORM
         * Constrained max-width form centred below the CTA text.
         * -------------------------------------------------------- */
        .contact-form {
            max-width: 600px;
            margin: 3rem auto 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Shared styles for text inputs and textarea */
        input,
        textarea {
            width: 100%;
            padding: 1rem;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 1rem;
            font-family: inherit; /* Match body font, not browser default */
            transition: all 0.3s ease;
        }

        /* Orange border glow on focus */
        input:focus,
        textarea:focus {
            outline: 0;
            border-color: var(--orange);
        }

        textarea {
            resize: vertical;      /* User can resize height, not width */
            min-height: 150px;
        }

        /* --------------------------------------------------------
         * HONEYPOT INPUT
         * Visually hidden — positioned far off-screen.
         * Real users never see or fill this in.
         * Bots that auto-fill all fields will populate it,
         * allowing server-side spam detection.
         * -------------------------------------------------------- */
        .honeypot {
            position: absolute;
            left: -9999px;
        }

        /* --------------------------------------------------------
         * FORM FEEDBACK MESSAGE
         * Shown after form submission — success or error state.
         * Hidden by default; JS adds .show to make it visible.
         * -------------------------------------------------------- */
        .form-message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: none;           /* Hidden until .show is added */
            font-size: 0.95rem;
        }

        /* Green success state */
        .form-message.success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        /* Red error state */
        .form-message.error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Toggled by JS to make the message visible */
        .form-message.show {
            display: block;
        }


        /* --------------------------------------------------------
         * MOBILE RESPONSIVE OVERRIDES
         * Applied when viewport width ≤ 768px.
         * -------------------------------------------------------- */
        @media (max-width: 768px) {

            /* Show hamburger toggle icon */
            .mobile-toggle {
                display: flex;
            }

            /* Stack nav links vertically, slide in from top */
            .nav-links {
                position: fixed;
                top: 70px;            /* Just below the fixed header */
                left: 0;
                right: 0;
                background: var(--dark-bg);
                flex-direction: column;
                padding: 2rem;
                border-bottom: 1px solid var(--border);
                /* Off-screen by default; JS toggles .active */
                transform: translateY(-200%);
                transition: transform 0.3s ease;
                gap: 1.5rem;
            }

            /* Nav visible state — JS adds this class */
            .nav-links.active {
                transform: translateY(0);
            }

            /* Reduce hero padding and font size on small screens */
            .hero {
                padding: 9rem 0 5rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            /* Stack CTA buttons vertically */
            .hero-buttons {
                flex-direction: column;
            }

            /* Stack stats vertically */
            .hero-stats {
                flex-direction: column;
                gap: 2rem;
            }

            /* All multi-column grids collapse to single column */
            .features-grid,
            .projects-grid,
            .about-grid,
            .testimonials-grid {
                grid-template-columns: 1fr;
            }

            /* Reduce section heading sizes */
            .section-header h2 {
                font-size: 2rem;
            }

            .cta h2 {
                font-size: 2rem;
            }

            /* Stack contact info items vertically */
            .contact-info {
                flex-direction: column;
                gap: 1rem;
            }

            /* Stack footer content vertically and centre it */
            .footer-content {
                flex-direction: column;
                gap: 2rem;
                text-align: center;
            }

            /* Stack footer links vertically */
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

    </style>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <!-- ========================================================
         HEADER / NAVIGATION
         Fixed to top. Logo left, nav links + CTA right.
         Hamburger toggle is hidden on desktop, shown on mobile.
         ======================================================== -->
    <header>
        <nav>
            <!-- Studio wordmark -->
            <div class="logo">INVINCIBLE</div>

            <!-- Navigation links — id used by JS for mobile toggle -->
            <ul class="nav-links" id="navLinks">
              <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
<!--                 <li><a href="#work">Work</a></li> -->
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
                <!-- Highlighted CTA button styled differently from regular links -->
                <li><a href="#contact" class="btn-cta">Start a Project</a></li>
            </ul>

            <!-- Hamburger icon — 3 horizontal bars, only visible on mobile -->
            <div class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>


    <main>

        <!-- ======================================================
             HERO SECTION
             Page-opening full-width section with headline, subtitle,
             two CTA buttons, and three KPI stats.
             ====================================================== -->
        <section class="hero" id="home">
            <div class="container">
                <div class="hero-content">

                    <!-- Small pill badge above the headline -->
                    <div class="hero-badge">We craft digital excellence</div>

                    <!-- Main headline — line breaks are intentional for visual impact -->
                    <h1>
                        We build brands<br>
                        that are <span class="highlight">truly<br>invincible</span>
                    </h1>

                    <!-- Subtitle -->
                    <p>
                        From stunning visuals to powerful digital products, we transform
                        your vision into experiences that captivate, convert, and endure.
                    </p>

                    <!-- Call-to-action button row -->
                    <div class="hero-buttons">
                        <a href="#contact" class="btn btn-primary">Let's Work Together</a>
                        <a href="#work"    class="btn btn-secondary">View Our Work</a>
                    </div>

                    <!-- Key stats / social proof numbers -->
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


        <!-- ======================================================
             SERVICES SECTION
             6 service cards in a 2-column grid.
             id="services" is the anchor for the nav link.
             ====================================================== -->
        <section id="services" class="features">
            <div class="container">

                <div class="section-header">
                    <div class="section-label">WHAT WE DO</div>
                    <h2>Expertise that <span class="highlight">delivers</span></h2>
                    <p>
                        We bring together diverse creative and technical disciplines
                        to build complete digital ecosystems.
                    </p>
                </div>

                <div class="features-grid">

                    <!-- Graphics Design -->
                    <article class="feature-card">
                        <div class="feature-icon">
                          <i class="fa-solid fa-palette"></i>
                        </div>
                        <h3>Graphics Design</h3>
                        <p>Brand identities, marketing materials, and visual storytelling that make your brand unforgettable.</p>
                    </article>

                    <!-- Web Development -->
                    <article class="feature-card">
                        <div class="feature-icon">
                          <i class="fa-solid fa-globe"></i>
                        </div>
                        <h3>Web Development</h3>
                        <p>High-performance websites and web apps built with modern tech stacks that scale with your ambitions.</p>
                    </article>

                    <!-- Motion Design -->
                    <article class="feature-card">
                        <div class="feature-icon">
                          <i class="fa-solid fa-film"></i>
                        </div>
                        <h3>Video Edits</h3>
                        <p>Captivating animations and video content that bring your stories to life and hold attention.</p>
                    </article>

                    <!-- App Development -->
                    <article class="feature-card">
                        <div class="feature-icon">
                          <i class="fa-solid fa-mobile"></i>
                        </div>
                        <h3>App Development</h3>
                        <p>Native and cross-platform mobile applications engineered for seamless user experiences.</p>
                    </article>

                    <!-- UI/UX Design -->
                    <article class="feature-card">
                        <div class="feature-icon">
                          <i class="fa-solid fa-bezier-curve"></i>
                        </div>
                        <h3>UI/UX Design</h3>
                        <p>Research-driven interfaces that delight users and drive measurable business outcomes.</p>
                    </article>

                    <!-- AI Products -->
                    <article class="feature-card">
                        <div class="feature-icon">
                          <i class="fa-solid fa-robot"></i>
                        </div>
                        <h3>AI Products</h3>
                        <p>Intelligent solutions powered by cutting-edge AI that automate, predict, and transform industries.</p>
                    </article>

                </div>
            </div>
        </section>


        <!-- ======================================================
             PORTFOLIO / WORK SECTION
             4 project cards, 2-column grid.
             id="work" is the anchor for the nav link.
             ====================================================== -->
<!--         <section id="work" class="projects">
            <div class="container">

                <div class="section-header">
                    <div class="section-label">SELECTED WORK</div>
                    <h2>Projects that <span class="highlight">speak volumes</span></h2>
                </div>

                <div class="projects-grid">

                
                    <article class="project-card">
                        <div class="project-image">
                            <div class="project-icon">↗</div>
                        </div>
                        <div class="project-info">
                            <div class="project-tags">WEB DEVELOPMENT · UI/UX</div>
                            <h3>Nova Finance</h3>
                            <p>A next-gen fintech platform with intuitive dashboards and real-time analytics.</p>
                        </div>
                    </article>

              
                    <article class="project-card">
                        <div class="project-image">
                            <div class="project-icon">↗</div>
                        </div>
                        <div class="project-info">
                            <div class="project-tags">MOTION DESIGN · BRANDING</div>
                            <h3>Meridian Studios</h3>
                            <p>Complete brand identity and motion system for an award-winning film studio.</p>
                        </div>
                    </article>

                
                    <article class="project-card">
                        <div class="project-image">
                            <div class="project-icon">↗</div>
                        </div>
                        <div class="project-info">
                            <div class="project-tags">AI PRODUCTS · APP DEVELOPMENT</div>
                            <h3>PulseAI Health</h3>
                            <p>AI-powered health monitoring app processing 10M+ data points daily.</p>
                        </div>
                    </article>

                
                    <article class="project-card">
                        <div class="project-image">
                            <div class="project-icon">↗</div>
                        </div>
                        <div class="project-info">
                            <div class="project-tags">WEB DEVELOPMENT · GRAPHICS</div>
                            <h3>Aether Commerce</h3>
                            <p>Luxury e-commerce experience with 200% increase in conversion rates.</p>
                        </div>
                    </article>

                </div>
            </div>
        </section> -->


        <!-- ======================================================
             ABOUT / VALUES SECTION
             Mission statement + 4 core values in a 2-column grid.
             id="about" is the anchor for the nav link.
             ====================================================== -->
        <section id="about" class="about">
            <div class="container">

                <div class="section-header">
                    <div class="section-label">WHY INVINCIBLE</div>
                    <h2>We don't just build —<br>we <span class="highlight">dominate</span></h2>
                    <p>
                        Invincible was founded on one belief: great design and great engineering
                        aren't luxuries — they're competitive advantages. We partner with ambitious
                        brands to create digital experiences that don't just compete, they conquer.
                    </p>
                    <p style="margin-top: 1rem;">
                        Our multidisciplinary team spans designers, developers, motion artists,
                        and AI engineers — all driven by the obsession to make your brand unstoppable.
                    </p>
                </div>

                <div class="about-grid">

                    <!-- Value 1: Quality -->
<article class="value-card">
    <div class="value-icon">
        <i class="fa-solid fa-shield-halved"></i>
    </div>
    <h3>Uncompromising Quality</h3>
    <p>Every pixel, every line of code meets our relentless standard.</p>
</article>

<!-- Value 2: Speed -->
<article class="value-card">
    <div class="value-icon">
        <i class="fa-solid fa-bolt"></i>
    </div>
    <h3>Rapid Execution</h3>
    <p>We move fast without breaking things. Your timeline matters.</p>
</article>

<!-- Value 3: Partnership -->
<article class="value-card">
    <div class="value-icon">
        <i class="fa-solid fa-users"></i>
    </div>
    <h3>True Partnership</h3>
    <p>We're not vendors — we're invested partners in your success.</p>
</article>

<!-- Value 4: Results -->
<article class="value-card">
    <div class="value-icon">
        <i class="fa-solid fa-trophy"></i>
    </div>
    <h3>Proven Results</h3>
    <p>Our work drives real metrics: more users, more revenue, more growth.</p>
</article>

                </div>
            </div>
        </section>


        <!-- ======================================================
             TESTIMONIALS SECTION
             3 client quotes in a 3-column grid.
             ====================================================== -->
<!-- <section class="testimonials">
    <div class="container">

        <div class="section-header">
            <div class="section-label">CLIENT LOVE</div>
            <h2>Words from those who <span class="highlight">trust us</span></h2>
        </div>

        <div class="testimonials-grid">

          
            <article class="testimonial">
                <div class="stars">★★★★★</div>
                <div class="testimonial-content">
                    "Invincible didn't just build our platform — they redefined our entire
                    digital presence. Our conversion rate tripled."
                </div>
                <div class="testimonial-author">
                    <img src="https://i.pravatar.cc/80?img=32" alt="Sarah Chen">
                    <div>
                        <h4>Sarah Chen</h4>
                        <p>CEO, Nova Finance</p>
                    </div>
                </div>
            </article>

        
            <article class="testimonial">
                <div class="stars">★★★★★</div>
                <div class="testimonial-content">
                    "Their motion design work gave our brand a soul. The animations they
                    created became the centrepiece of our launch campaign."
                </div>
                <div class="testimonial-author">
                    <img src="https://i.pravatar.cc/80?img=12" alt="Marcus Webb">
                    <div>
                        <h4>Marcus Webb</h4>
                        <p>Creative Director, Meridian Studios</p>
                    </div>
                </div>
            </article>

        
            <article class="testimonial">
                <div class="stars">★★★★★</div>
                <div class="testimonial-content">
                    "Working with Invincible felt like adding a world-class team to our
                    company. They truly care about results."
                </div>
                <div class="testimonial-author">
                    <img src="https://i.pravatar.cc/80?img=45" alt="Amira Patel">
                    <div>
                        <h4>Amira Patel</h4>
                        <p>Founder, PulseAI Health</p>
                    </div>
                </div>
            </article>

        </div>
    </div>
</section> -->
        <!-- ======================================================
             CALL TO ACTION (CTA) + CONTACT FORM
             Large closing headline encouraging project enquiries,
             followed by the contact form.
             id="contact" is the anchor for the nav link.
             ====================================================== -->
        <section id="contact" class="cta">
            <div class="container">

                <!-- Eyebrow label -->
                <div class="section-label">LET'S TALK</div>

                <!-- Closing headline -->
                <h2>
                    Ready to become<br>
                    <span class="highlight">invincible</span>?
                </h2>

                <!-- Sub-copy -->
                <p>
                    Tell us about your project and let's create something extraordinary
                    together. No fluff, no bureaucracy — just great work.
                </p>

                <!-- Scroll-to-form CTA button -->
                <a href="#contact-form" class="btn btn-primary">Start Your Project</a>

                <!-- Contact detail row -->
                <div class="contact-info">
                    <div class="contact-item location">Available Worldwide</div>
                </div>


                <!-- ------------------------------------------
                     CONTACT FORM
                     Submitted via AJAX (fetch API) in the JS below.
                     The form posts to this same file (index.php).
                     
                     Fields:
                       - website  : honeypot (hidden from real users)
                       - name     : required text
                       - email    : required email
                       - message  : required textarea
                     ------------------------------------------ -->
<form 
    class="contact-form" 
    action="mailto:ollyverse01@gmail.com,olamiposiayeriyina@gmail.com"
    method="POST"
    enctype="text/plain"
>

    <!-- Feedback message area -->
    <div class="form-message" id="formMessage"></div>

    <!-- Honeypot -->
    <input
        type="text"
        name="website"
        class="honeypot"
        tabindex="-1"
        autocomplete="off"
    >

    <!-- Name -->
    <div class="form-group">
        <label for="name">Your Name</label>
        <input type="text" id="name" name="Name" required>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="Email" required>
    </div>

    <!-- Message -->
    <div class="form-group">
        <label for="message">Tell Us About Your Project</label>
        <textarea id="message" name="Message" required></textarea>
    </div>

    <!-- Submit -->
    <button type="submit" class="btn btn-primary">Send Message</button>

</form>
            </div>
        </section>

    </main>


    <!-- ==========================================================
         FOOTER
         Logo + copyright left, social links right.
         ========================================================== -->
<footer>
  <style>
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
  </style>
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

                </nav>

                <!-- Social media icon links -->
<div class="social-links">
    
    <!-- X (Twitter) -->
    <a href="#" class="social-link" aria-label="Twitter / X">
        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
            <path d="M18.244 2H21l-6.5 7.43L22 22h-6.828l-5.34-6.99L3.5 22H1l6.94-7.93L2 2h6.828l4.84 6.36L18.244 2Zm-2.39 18h1.89L8.06 4H6.09l9.764 16Z"/>
        </svg>
    </a>

    <!-- Instagram -->
    <a href="#" class="social-link" aria-label="Instagram">
        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
            <path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2Zm0 2A3.75 3.75 0 0 0 4 7.75v8.5A3.75 3.75 0 0 0 7.75 20h8.5A3.75 3.75 0 0 0 20 16.25v-8.5A3.75 3.75 0 0 0 16.25 4h-8.5ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm4.75-2.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5Z"/>
        </svg>
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



    <!-- ==========================================================
         JAVASCRIPT
         Three independent behaviours:
           1. Mobile navigation toggle
           2. Scroll-triggered card animations (IntersectionObserver)
           3. Async contact form submission
           4. Smooth scroll for all in-page anchor links
         ========================================================== -->
    <script>
 const yearEl = document.getElementById('currentYear');
    if (yearEl) yearEl.textContent = new Date().getFullYear();
        /* --------------------------------------------------------
         * 1. MOBILE NAVIGATION TOGGLE
         * Clicking the hamburger icon toggles the .active class
         * on the nav links list, which slides it into view via CSS.
         * Clicking any nav link also closes the menu.
         * -------------------------------------------------------- */
        const mobileToggle = document.getElementById('mobileToggle');
        const navLinks     = document.getElementById('navLinks');

        // Toggle the mobile nav open/closed when hamburger is clicked
        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });

        // Close the mobile nav whenever any link inside it is clicked
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });


        /* --------------------------------------------------------
         * 2. SCROLL-TRIGGERED CARD ANIMATIONS
         * IntersectionObserver watches each card element.
         * When a card enters the viewport (threshold: 15%),
         * the .visible class is added after a staggered delay
         * (index × 100ms) so cards animate in sequentially.
         * The CSS handles the actual opacity/transform transition.
         * -------------------------------------------------------- */
        const observerOptions = {
            threshold:   0.15,              // Trigger when 15% of element is visible
            rootMargin: '0px 0px -50px 0px' // Shrink the bottom of the viewport trigger area
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Stagger each card's animation by 100ms × its index
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 100);
                }
            });
        }, observerOptions);

        // Observe all animatable card elements
        document.querySelectorAll('.feature-card, .project-card, .value-card, .testimonial').forEach(el => {
            observer.observe(el);
        });


        /* --------------------------------------------------------
         * 3. ASYNC CONTACT FORM SUBMISSION
         * Intercepts the form's default submit event, sends the
         * form data to this same PHP file via fetch (AJAX POST),
         * then shows a success or error message without a page reload.
         * -------------------------------------------------------- */
        const contactForm  = document.getElementById('contactForm');
        const formMessage  = document.getElementById('formMessage');

        contactForm.addEventListener('submit', async (e) => {

            // Prevent the browser from doing a full page POST redirect
            e.preventDefault();

            // Collect all form field values into a FormData object
            const formData     = new FormData(contactForm);
            const submitButton = contactForm.querySelector('button[type="submit"]');

            // Disable the button and show a loading state to prevent double-submit
            submitButton.disabled   = true;
            submitButton.textContent = 'Sending...';

            try {
                // POST the form data to index.php (this file)
                const response = await fetch('index.php', {
                    method: 'POST',
                    body:   formData
                });

                // Parse the JSON response from the PHP handler
                const data = await response.json();

                // Display the server's message in the feedback area
                formMessage.textContent = data.message;

                // Apply the appropriate CSS class: 'success' or 'error'
                // 'show' makes the element visible (overrides display:none)
                formMessage.className = 'form-message show ' + data.status;

                if (data.status === 'success') {
                    // Clear the form on success
                    contactForm.reset();

                    // Auto-hide the success message after 5 seconds
                    setTimeout(() => {
                        formMessage.classList.remove('show');
                    }, 5000);
                }

            } catch (error) {
                // Network failure or malformed JSON from the server
                formMessage.textContent = 'An error occurred. Please try again.';
                formMessage.className   = 'form-message show error';

            } finally {
                // Always re-enable the button regardless of outcome
                submitButton.disabled    = false;
                submitButton.textContent = 'Send Message';
            }
        });


        /* --------------------------------------------------------
         * 4. SMOOTH SCROLL FOR IN-PAGE ANCHOR LINKS
         * Overrides the default instant jump behaviour for all
         * links starting with '#'. Accounts for the fixed header
         * height (80px) so the target section isn't hidden underneath.
         * -------------------------------------------------------- */
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                // Find the element the anchor points to
                const target = document.querySelector(this.getAttribute('href'));

                if (target) {
                    const headerOffset   = 80; // Height of the fixed header in px
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition  = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top:      offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

    </script>

</body>
</html>
