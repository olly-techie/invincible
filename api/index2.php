<?php
/**
 * ============================================================
 *  Invincible Studio — index.php
 *  Single-file PHP entry point.
 *
 *  HOW THIS FILE WORKS (for beginners):
 *  ------------------------------------------------------------
 *  1. PHP runs on the SERVER before anything reaches the browser.
 *  2. When the browser sends a normal page load (GET request),
 *     PHP skips the if-block below and renders the HTML.
 *  3. When the contact form is submitted, JavaScript sends a
 *     POST request to this SAME file. PHP catches it, processes
 *     it, and returns a JSON response — never rendering HTML.
 *  4. React (loaded via CDN) mounts inside <div id="root"> and
 *     takes over all interactivity inside the browser.
 * ============================================================
 */


/* ============================================================
   SECTION 1 — FORM / API HANDLER
   Runs ONLY when the browser sends a POST request.
   ============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
     * Tell the browser we are replying with JSON, not HTML.
     * This header() call MUST come before any echo/print.
     */
    header('Content-Type: application/json');

    /* ----------------------------------------------------------
       HONEYPOT SPAM TRAP
       A hidden field called "website" sits in the HTML form.
       Real users never fill it in because it is invisible.
       Bots that blindly auto-fill every input WILL fill it.
       If it has any value → it is a bot → reject silently.
    ---------------------------------------------------------- */
    if (!empty($_POST['website'])) {
        echo json_encode(['status' => 'error', 'message' => 'Bot detected.']);
        exit; // stop PHP immediately — nothing more is sent
    }

    /* ----------------------------------------------------------
       SANITISE INPUTS
       htmlspecialchars() converts dangerous characters such as
       < > " ' & into safe HTML entities, preventing attackers
       from injecting scripts or breaking out of HTML tags.
       trim()   → strips accidental leading/trailing whitespace
       ?? ''    → use empty string if the key does not exist
       ENT_QUOTES → escapes both single AND double quotes
    ---------------------------------------------------------- */
    $name    = htmlspecialchars(trim($_POST['name']    ?? ''), ENT_QUOTES, 'UTF-8');
    $email   = htmlspecialchars(trim($_POST['email']   ?? ''), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    /* ----------------------------------------------------------
       REQUIRED FIELD VALIDATION
       The ! operator means "not truthy". An empty string is
       falsy in PHP, so !$name is true when $name is empty.
    ---------------------------------------------------------- */
    if (!$name || !$email || !$message) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    /* ----------------------------------------------------------
       EMAIL FORMAT VALIDATION
       filter_var() is a built-in PHP function that validates
       common data formats. FILTER_VALIDATE_EMAIL checks that
       the string looks like a properly formatted email address.
    ---------------------------------------------------------- */
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        exit;
    }

    /* ----------------------------------------------------------
       RATE LIMITING — Prevent repeated spam floods
       We store a small JSON file in the server's /tmp folder
       that logs each IP address and its submission timestamps.
       If one IP submits more than 5 times within one hour
       (3600 seconds), we reject the request.

       sys_get_temp_dir() → returns the server tmp path, e.g. /tmp
       time()             → current Unix timestamp (integer seconds)
    ---------------------------------------------------------- */
    $rateFile = sys_get_temp_dir() . '/invincible_rate.json';
    $rates    = [];

    // Load existing rate-limit data if the file already exists
    if (file_exists($rateFile)) {
        $decoded = json_decode(file_get_contents($rateFile), true);
        $rates   = is_array($decoded) ? $decoded : [];
    }

    $ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown'; // visitor's IP address
    $now = time();                                 // current time in seconds

    // Purge entries older than 1 hour so the file never grows forever
    $rates = array_filter($rates, fn($entry) => ($now - $entry['time']) < 3600);

    // Count how many times this specific IP has submitted in the last hour
    $ipCount = count(array_filter($rates, fn($entry) => $entry['ip'] === $ip));

    if ($ipCount >= 5) {
        echo json_encode(['status' => 'error', 'message' => 'Too many submissions. Please try again later.']);
        exit;
    }

    // Record this new submission
    $rates[] = ['ip' => $ip, 'time' => $now];
    file_put_contents($rateFile, json_encode(array_values($rates)));

    /* ----------------------------------------------------------
       PERSIST THE MESSAGE TO DISK
       Each submission is appended as one JSON line to a file
       called messages.json in the same directory as index.php.

       __DIR__      → absolute path to this file's directory
       FILE_APPEND  → add to end of file instead of overwriting
       LOCK_EX      → exclusive lock prevents two simultaneous
                      writes from corrupting the file
       PHP_EOL      → newline character (\n on Linux)
       date('c')    → ISO 8601 timestamp e.g. 2025-01-01T12:00:00+00:00
    ---------------------------------------------------------- */
    $entry = [
        'name'    => $name,
        'email'   => $email,
        'message' => $message,
        'time'    => date('c'),
        'ip'      => $ip,
    ];

    file_put_contents(
        __DIR__ . '/messages.json',
        json_encode($entry) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    // All good — tell React the submission succeeded
    echo json_encode(['status' => 'success', 'message' => "Thanks! We'll be in touch within 24 hours."]);
    exit;

} // ← end of POST handler. Code below only runs on GET requests.


/* ============================================================
   SECTION 2 — SEO METADATA
   Defining values as PHP variables here keeps the <head>
   section below clean and easy to update in one place.
   ============================================================ */

$site_name   = 'Invincible Studio';
$site_url    = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'invinciblestudio.dev');
$title       = 'Invincible — Design, Development & AI Studio';
$description = 'Invincible is a premium design, development & AI studio building bold digital products. We craft stunning websites, AI-powered apps, and scalable software solutions.';
$keywords    = 'design studio, web development, AI studio, software development, UI/UX design, React development, digital products, AI solutions, web design agency';
$og_image    = $site_url . '/og-image.jpg';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <!-- ============================================================
       PRIMARY SEO TAGS
       These are read by Google to understand and rank this page.
       <?= ... ?> is shorthand for <?php echo ... ?>
  ============================================================ -->
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($description) ?>" />
  <meta name="keywords"    content="<?= htmlspecialchars($keywords) ?>" />
  <meta name="author"      content="Invincible Studio" />
  <meta name="robots"      content="index, follow" />
  <!-- Canonical URL tells Google the authoritative URL for this page -->
  <link rel="canonical" href="<?= htmlspecialchars($site_url) ?>" />

  <!-- ============================================================
       OPEN GRAPH TAGS
       Control how this page looks when shared on Facebook,
       LinkedIn, Slack, WhatsApp, Discord, etc.
  ============================================================ -->
  <meta property="og:type"        content="website" />
  <meta property="og:title"       content="<?= htmlspecialchars($title) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($description) ?>" />
  <meta property="og:url"         content="<?= htmlspecialchars($site_url) ?>" />
  <meta property="og:image"       content="<?= htmlspecialchars($og_image) ?>" />
  <meta property="og:site_name"   content="<?= htmlspecialchars($site_name) ?>" />

  <!-- ============================================================
       TWITTER CARD TAGS
       Control how this page looks when shared on Twitter / X.
       "summary_large_image" displays a big preview image.
  ============================================================ -->
  <meta name="twitter:card"        content="summary_large_image" />
  <meta name="twitter:title"       content="<?= htmlspecialchars($title) ?>" />
  <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>" />
  <meta name="twitter:image"       content="<?= htmlspecialchars($og_image) ?>" />

  <!-- ============================================================
       STRUCTURED DATA (JSON-LD)
       Machine-readable information about our organisation.
       Google uses this to show rich results in search.
       @graph lets us define multiple schema types in one block.
  ============================================================ -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "name": "Invincible Studio",
        "url": "<?= $site_url ?>",
        "description": "<?= addslashes($description) ?>",
        "foundingDate": "2024",
        "serviceType": ["Web Design", "Web Development", "AI Solutions", "UI/UX Design"]
      },
      {
        "@type": "WebSite",
        "name": "Invincible Studio",
        "url": "<?= $site_url ?>",
        "potentialAction": {
          "@type": "SearchAction",
          "target": "<?= $site_url ?>/?s={search_term_string}",
          "query-input": "required name=search_term_string"
        }
      }
    ]
  }
  </script>

  <!-- ============================================================
       GOOGLE FONTS
       Syne    → headings / display text (geometric, strong)
       DM Sans → body copy (clean, highly legible)
       preconnect hints tell the browser to open the connection
       early so fonts arrive before they are needed.
  ============================================================ -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet" />

  <!-- ============================================================
       TAILWIND CSS (CDN)
       Utility-first CSS framework.
       The config block below extends it with brand tokens.
  ============================================================ -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- ============================================================
       TAILWIND CONFIGURATION
       Must come AFTER the Tailwind CDN <script>.
       We add custom colours, fonts and animations here so they
       are available as Tailwind utility classes in app.js.
  ============================================================ -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          /* Custom fonts — used via class font-display / font-body */
          fontFamily: {
            display: ['Syne', 'sans-serif'],
            body:    ['DM Sans', 'sans-serif'],
          },

          /* ── Brand colour palette ──────────────────────────────
             Use these as bg-arc, text-volt, border-wire, etc.   */
          colors: {
            ink:   '#09090b', /* near-black page background       */
            panel: '#111114', /* card / elevated surface          */
            edge:  '#1a1a1f', /* hover states on dark surfaces    */
            wire:  '#2a2a32', /* borders and dividers             */
            mist:  '#52525b', /* muted / disabled text            */
            ash:   '#a1a1aa', /* secondary body text              */
            snow:  '#f4f4f5', /* primary text                     */
            arc:   '#6ee7f7', /* cyan accent                      */
            volt:  '#a3e635', /* lime / green accent              */
            flare: '#f97316', /* orange accent / error tones      */
          },

          /* ── Custom gradient backgrounds ──────────────────────── */
          backgroundImage: {
            'glow-c': 'radial-gradient(ellipse 60% 40% at 50% 0%, rgba(110,231,247,.15) 0%, transparent 70%)',
            'glow-v': 'radial-gradient(ellipse 60% 40% at 50% 0%, rgba(163,230,53,.10)  0%, transparent 70%)',
          },

          /* ── Named animations ─────────────────────────────────── */
          animation: {
            'fade-up':    'fadeUp .7s ease both',
            'fade-in':    'fadeIn .6s ease both',
            'pulse-glow': 'pulseGlow 3s ease-in-out infinite',
          },

          /* ── Keyframe definitions ─────────────────────────────── */
          keyframes: {
            fadeUp: {
              '0%':   { opacity: '0', transform: 'translateY(28px)' },
              '100%': { opacity: '1', transform: 'translateY(0)'    },
            },
            fadeIn: {
              '0%':   { opacity: '0' },
              '100%': { opacity: '1' },
            },
            pulseGlow: {
              '0%,100%': { opacity: '.6' },
              '50%':     { opacity: '1'  },
            },
          },
        },
      },
    };
  </script>

  <!-- ============================================================
       CUSTOM STYLESHEET
       All hand-written CSS lives in style.css.
       Loaded after Tailwind so our rules can override it.
  ============================================================ -->
  <link rel="stylesheet" href="style.css" />

</head>

<body class="bg-ink font-body text-snow antialiased relative">

  <!--
    ============================================================
    NOSCRIPT BLOCK — SEO + accessibility fallback
    ============================================================
    Some search engine crawlers do not execute JavaScript.
    This block provides raw HTML content that crawlers can
    read and index even if React never loads.
    Real users with JavaScript enabled never see this content
    because React fully replaces the #root div below.
    ============================================================
  -->
  <noscript>
    <header>
      <h1>Invincible — Design, Development &amp; AI Studio</h1>
      <nav>
        <a href="#services">Services</a>
        <a href="#about">About</a>
        <a href="#work">Work</a>
        <a href="#testimonials">Testimonials</a>
        <a href="#contact">Contact</a>
      </nav>
    </header>
    <main>
      <section id="hero">
        <h1>Build Bold. Ship Fast. Stay Invincible.</h1>
        <p>We are a premium design, development &amp; AI studio crafting digital experiences that captivate, convert, and scale. From pixel-perfect interfaces to intelligent systems — we make it real.</p>
      </section>
      <section id="services">
        <h2>What We Do</h2>
        <article><h3>UI/UX Design</h3><p>Interfaces crafted for humans first. We turn complex flows into delightful, intuitive experiences.</p></article>
        <article><h3>Web Development</h3><p>Full-stack engineering with React, Next.js, and modern frameworks. Fast, accessible, and built to last.</p></article>
        <article><h3>AI Integration</h3><p>LLM pipelines, RAG systems, agents, and custom AI workflows — production-ready.</p></article>
        <article><h3>Mobile Apps</h3><p>Native and cross-platform apps. Performance-first, design-led.</p></article>
        <article><h3>Brand Identity</h3><p>Visual systems that communicate instantly. Every mark deliberate.</p></article>
        <article><h3>Performance &amp; SEO</h3><p>Speed and visibility engineered in from day one.</p></article>
      </section>
      <section id="about">
        <h2>About Invincible Studio</h2>
        <p>A small team of designers, engineers, and AI specialists obsessed with craft. We partner with founders and growing companies to build products that matter.</p>
      </section>
      <section id="contact">
        <h2>Start a Project</h2>
        <p>Tell us about your vision. We'll respond within 24 hours.</p>
      </section>
    </main>
  </noscript>

  <!--
    ============================================================
    REACT MOUNT POINT
    ============================================================
    This single empty div is where React attaches the entire
    application. React calls this the "root" node.
    ReactDOM.createRoot(document.getElementById('root'))
    in app.js finds this element and renders into it.
    ============================================================
  -->
  <div id="root"></div>

  <!--
    ============================================================
    CDN SCRIPTS — Order is critical
    ============================================================
    1. react.production.min.js  → React core (components, hooks)
    2. react-dom.production.min.js → Renders React into the DOM
    3. babel.min.js             → Compiles JSX syntax in the
                                   browser (no build step needed)

    All three must load BEFORE app.js which uses them.
    ============================================================
  -->
  <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

  <!--
    type="text/babel" signals Babel to process this script.
    src="app.js" loads the React component tree from app.js.
  -->
  <script type="text/babel" src="app.js"></script>

</body>
</html>
