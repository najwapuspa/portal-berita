<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'News X Paper')</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ─── RESET & ROOT VARIABLES ──────────────────────────────── */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    :root {
      --bg-viewport: radial-gradient(circle at 50% 0%, #1e293b 0%, #0b1329 65%, #060b17 100%);
      --card-bg: #ffffff;
      --navy-950: #060b17;
      --navy-900: #0b1329;
      --navy-850: #0f172a;
      --navy-800: #1e293b;
      --navy-700: #334155;
      --slate-500: #64748b;
      --slate-400: #94a3b8;
      --border-soft: #e2e8f0;
      --border-input: #cbd5e1;
      --input-bg: #f8fafc;
      --accent-blue: #1e8fd6;
      --accent-blue-hover: #1677b5;
      --accent-blue-soft: rgba(30, 143, 214, 0.12);
      --error-color: #dc2626;
      --error-bg: #fef2f2;
      --error-border: #fecaca;
      --success-color: #16a34a;
      --success-bg: #f0fdf4;
      --success-border: #bbf7d0;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
    }

    html, body {
      min-height: 100vh;
      background: var(--navy-900);
      background-image: var(--bg-viewport);
      background-attachment: fixed;
      font-family: var(--font-sans);
      color: var(--navy-850);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* ─── VIEWPORT WRAPPER ────────────────────────────────────── */
    .auth-viewport, .haus-viewport {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 20px;
    }

    /* ─── AUTH CARD CONTAINER ─────────────────────────────────── */
    .auth-card, .haus-frame {
      position: relative;
      width: 100%;
      max-width: 1040px;
      min-height: 630px;
      background: var(--card-bg);
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 25px 60px -12px rgba(2, 6, 23, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.08);
      display: flex;
    }

    /* ─── SPLIT-SCREEN CONTAINER ──────────────────────────────── */
    .auth-split, .haus-split {
      display: grid;
      grid-template-columns: 52% 48%;
      width: 100%;
      min-height: 100%;
    }

    /* ─── LEFT PANEL (CLEAN PROFESSIONAL WHITE) ───────────────── */
    .auth-left, .haus-left {
      background: #ffffff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 30px 44px 26px 44px;
      position: relative;
      overflow-y: auto;
    }

    /* Top Navigation Left */
    .auth-header-left, .haus-header-left {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      padding-bottom: 8px;
    }
    .auth-logo, .haus-logo {
      font-family: var(--font-serif);
      font-size: 22px;
      font-weight: 700;
      color: var(--navy-850);
      text-decoration: none;
      letter-spacing: -0.3px;
      line-height: 1;
      display: inline-flex;
      align-items: baseline;
      transition: opacity 0.15s ease;
    }
    .auth-logo:hover, .haus-logo:hover {
      opacity: 0.85;
    }
    .auth-logo-x {
      color: var(--accent-blue);
      font-style: italic;
      font-weight: 800;
      padding: 0 1px;
    }
    .auth-nav, .haus-nav {
      display: flex;
      align-items: center;
    }
    .auth-nav-links, .haus-nav-links {
      display: flex;
      align-items: center;
      gap: 18px;
      list-style: none;
    }
    .auth-nav-links a, .haus-nav-links a {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--slate-500);
      text-decoration: none;
      transition: color 0.15s ease;
    }
    .auth-nav-links a:hover, .haus-nav-links a:hover {
      color: var(--accent-blue);
    }

    /* Form Container Middle */
    .auth-main, .haus-main {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;
      margin: 16px 0 20px 0;
    }
    .auth-form-wrap, .haus-form-wrap {
      width: 100%;
      max-width: 370px;
    }

    /* Heading & Subheading: Professional Hierarchy */
    .auth-heading, .haus-heading {
      font-family: var(--font-sans);
      font-size: 26px;
      font-weight: 700;
      color: var(--navy-850);
      text-align: left;
      letter-spacing: -0.4px;
      margin-bottom: 6px;
      line-height: 1.25;
    }
    .auth-subheading, .haus-desc {
      font-size: 13px;
      color: var(--slate-500);
      text-align: left;
      line-height: 1.5;
      margin-bottom: 20px;
    }

    /* Alerts */
    .auth-alert, .haus-alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 12.5px;
      line-height: 1.45;
      padding: 10px 14px;
      border-radius: 6px;
      margin-bottom: 18px;
      text-align: left;
    }
    .auth-alert svg, .haus-alert svg {
      flex-shrink: 0;
      margin-top: 1px;
    }
    .auth-alert-error, .haus-alert-error {
      background: var(--error-bg);
      border: 1px solid var(--error-border);
      color: var(--error-color);
    }
    .auth-alert-success, .haus-alert-success {
      background: var(--success-bg);
      border: 1px solid var(--success-border);
      color: var(--success-color);
    }

    /* Fields: Modern Crisp Inputs */
    .auth-field, .haus-field {
      margin-bottom: 16px;
      position: relative;
    }
    .auth-label, .haus-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--navy-700);
      margin-bottom: 6px;
    }
    .auth-input, .haus-input {
      width: 100%;
      height: 42px;
      background: var(--input-bg);
      border: 1px solid var(--border-input);
      border-radius: 6px;
      padding: 0 14px;
      font-family: var(--font-sans);
      font-size: 13.5px;
      color: var(--navy-850);
      outline: none;
      transition: all 0.2s ease;
    }
    .auth-input::placeholder, .haus-input::placeholder {
      color: var(--slate-400);
    }
    .auth-input:focus, .haus-input:focus {
      background: #ffffff;
      border-color: var(--accent-blue);
      box-shadow: 0 0 0 3px var(--accent-blue-soft);
    }
    .auth-input.has-error, .haus-input.has-error {
      border-color: var(--error-color);
      background: #fff8f8;
    }
    .auth-error-text, .haus-error-text {
      font-size: 11px;
      color: var(--error-color);
      margin-top: 5px;
      display: block;
    }

    /* Password wrap with show/hide toggle */
    .auth-password-wrap, .haus-password-wrap {
      position: relative;
      display: flex;
      align-items: center;
      width: 100%;
    }
    .auth-password-wrap .auth-input,
    .auth-password-wrap .haus-input,
    .haus-password-wrap .haus-input {
      padding-right: 42px;
    }
    .auth-pw-toggle, .haus-pw-toggle {
      position: absolute;
      right: 8px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      width: 30px;
      height: 30px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: var(--slate-400);
      border-radius: 4px;
      line-height: 1;
      transition: color 0.15s ease, background 0.15s ease;
    }
    .auth-pw-toggle:hover,
    .auth-pw-toggle:focus-visible,
    .haus-pw-toggle:hover,
    .haus-pw-toggle:focus-visible {
      color: var(--navy-850);
      background: rgba(0, 0, 0, 0.04);
      outline: none;
    }
    .auth-pw-toggle svg, .haus-pw-toggle svg {
      width: 18px;
      height: 18px;
      display: block;
      pointer-events: none;
    }

    /* Row for Remember Me & Forgot Password */
    .auth-options-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-top: 8px;
      margin-bottom: 20px;
    }
    .auth-remember-wrap, .haus-remember-wrap {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
      user-select: none;
    }
    .auth-remember-wrap input[type="checkbox"],
    .haus-remember-wrap input[type="checkbox"] {
      width: 15px;
      height: 15px;
      accent-color: var(--navy-850);
      cursor: pointer;
    }
    .auth-remember-wrap span,
    .haus-remember-wrap span {
      font-size: 12px;
      color: var(--slate-500);
    }
    .auth-forgot-link, .haus-forgot-link {
      font-size: 12px;
      font-weight: 600;
      color: var(--accent-blue);
      text-decoration: none;
      transition: color 0.15s ease;
      white-space: nowrap;
    }
    .auth-forgot-link:hover, .haus-forgot-link:hover {
      color: var(--accent-blue-hover);
      text-decoration: underline;
    }

    /* Submit Button: Full-width Professional */
    .auth-btn-wrap, .haus-btn-wrap {
      margin-top: 14px;
      width: 100%;
    }
    .auth-btn, .haus-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 44px;
      background: var(--navy-850);
      color: #ffffff;
      font-family: var(--font-sans);
      font-size: 13.5px;
      font-weight: 600;
      letter-spacing: 0.04em;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
      transition: background 0.18s ease, transform 0.1s ease, box-shadow 0.18s ease;
    }
    .auth-btn:hover, .haus-btn:hover {
      background: var(--navy-800);
      transform: translateY(-1px);
      box-shadow: 0 6px 14px rgba(15, 23, 42, 0.22);
    }
    .auth-btn:active, .haus-btn:active {
      transform: translateY(0);
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
    }

    /* Footer: Subtle divider and bottom link */
    .auth-footer, .haus-footer {
      width: 100%;
      padding-top: 14px;
    }
    .auth-divider, .haus-divider {
      border: none;
      border-top: 1px solid var(--border-soft);
      width: 100%;
      margin: 0 0 14px 0;
    }
    .auth-footer-text, .haus-footer-text {
      text-align: center;
      font-size: 12.5px;
      color: var(--slate-500);
      line-height: 1.5;
    }
    .auth-footer-text a, .haus-footer-text a {
      color: var(--accent-blue);
      font-weight: 700;
      text-decoration: none;
      transition: color 0.15s ease;
    }
    .auth-footer-text a:hover, .haus-footer-text a:hover {
      color: var(--accent-blue-hover);
      text-decoration: underline;
    }

    /* ─── RIGHT PANEL (METROPOLITAN EDITORIAL HERO) ───────────── */
    .auth-right, .haus-right {
      position: relative;
      background-image: linear-gradient(180deg, rgba(15, 23, 42, 0.35) 0%, rgba(11, 19, 41, 0.88) 100%), url('{{ asset('images/city-metropolitan.jpg') }}');
      background-size: cover;
      background-position: center center;
      background-repeat: no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 30px 36px 36px 36px;
      overflow: hidden;
    }
    .auth-header-right, .haus-header-right {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      width: 100%;
      z-index: 10;
    }
    .auth-header-right a, .haus-header-right a {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: #ffffff;
      text-decoration: none;
      background: rgba(255, 255, 255, 0.16);
      border: 1px solid rgba(255, 255, 255, 0.28);
      padding: 6px 16px;
      border-radius: 20px;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      transition: background 0.15s ease, border-color 0.15s ease, transform 0.1s ease;
    }
    .auth-header-right a:hover, .haus-header-right a:hover {
      background: rgba(255, 255, 255, 0.28);
      border-color: rgba(255, 255, 255, 0.5);
      transform: translateY(-1px);
    }

    /* Right Panel Editorial Quote */
    .auth-right-content {
      position: relative;
      z-index: 10;
      color: #ffffff;
      max-width: 420px;
    }
    .auth-brand-pill {
      display: inline-block;
      font-size: 9.5px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.18em;
      background: var(--accent-blue);
      color: #ffffff;
      padding: 4px 10px;
      border-radius: 3px;
      margin-bottom: 12px;
    }
    .auth-quote {
      font-family: var(--font-serif);
      font-size: 20px;
      font-style: italic;
      line-height: 1.45;
      color: #f8fafc;
      margin-bottom: 12px;
      letter-spacing: -0.2px;
    }
    .auth-edition {
      display: block;
      font-size: 11.5px;
      font-weight: 500;
      letter-spacing: 0.08em;
      color: rgba(255, 255, 255, 0.7);
    }

    /* ─── RESPONSIVE: LAPTOP & TABLET ─────────────────────────── */
    @media (max-width: 960px) {
      .auth-card, .haus-frame {
        max-width: 95%;
        min-height: 580px;
      }
      .auth-left, .haus-left {
        padding: 26px 32px 22px 32px;
      }
      .auth-right, .haus-right {
        padding: 26px 28px 28px 28px;
      }
      .auth-quote {
        font-size: 18px;
      }
    }

    /* ─── RESPONSIVE: SMARTPHONE ──────────────────────────────── */
    @media (max-width: 768px) {
      .auth-viewport, .haus-viewport {
        padding: 14px 10px;
        min-height: 100vh;
      }
      .auth-card, .haus-frame {
        max-width: 100%;
        min-height: auto;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        flex-direction: column;
      }
      .auth-split, .haus-split {
        display: flex;
        flex-direction: column;
      }
      /* Photo becomes top banner on mobile */
      .auth-right, .haus-right {
        order: 1;
        height: 150px;
        width: 100%;
        padding: 16px 20px;
        justify-content: space-between;
      }
      .auth-right-content {
        display: none;
      }
      .auth-header-right, .haus-header-right {
        padding: 0;
      }
      .auth-left, .haus-left {
        order: 2;
        padding: 24px 20px 22px 20px;
        min-height: auto;
      }
      .auth-header-left, .haus-header-left {
        margin-bottom: 8px;
      }
      .auth-nav-links, .haus-nav-links {
        gap: 12px;
      }
      .auth-nav-links a, .haus-nav-links a {
        font-size: 10px;
      }
      .auth-heading, .haus-heading {
        font-size: 23px;
      }
      .auth-subheading, .haus-desc {
        font-size: 12.5px;
        margin-bottom: 16px;
      }
      .auth-form-wrap, .haus-form-wrap {
        max-width: 100%;
      }
      .auth-options-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }
  </style>
  @stack('styles')
</head>
<body>

<div class="auth-viewport">

  <div class="auth-card">

    <div class="auth-split">

      {{-- LEFT PANEL: FORM AUTENTIKASI UTAMA --}}
      <section class="auth-left">

        {{-- Topbar: Logo Brand & Navigasi Bahasa Indonesia Saja --}}
        <header class="auth-header-left">
          <a href="{{ route('articles.index') }}" class="auth-logo" aria-label="News X Paper Beranda">
            News<span class="auth-logo-x">X</span>Paper
          </a>

          <nav class="auth-nav" aria-label="Navigasi Utama">
            <ul class="auth-nav-links">
              <li><a href="{{ route('articles.index') }}">BERANDA</a></li>
              <li><a href="{{ route('news.index') }}">BERITA</a></li>
              <li><a href="{{ route('about') }}">TENTANG KAMI</a></li>
            </ul>
          </nav>
        </header>

        {{-- Kontainer Form Tengah --}}
        <main class="auth-main">
          <div class="auth-form-wrap">
            <h1 class="auth-heading">@yield('heading')</h1>
            @hasSection('subheading')
              <p class="auth-subheading">@yield('subheading')</p>
            @endif

            @yield('content')
          </div>
        </main>

        {{-- Footer Bawah: Garis Pembatas & Tautan Akun --}}
        <footer class="auth-footer">
          <hr class="auth-divider" aria-hidden="true">
          <p class="auth-footer-text">
            @yield('footer_text')
          </p>
        </footer>

      </section>

      {{-- RIGHT PANEL: CITRA FOTO METROPOLITAN & KUTIPAN EDITORIAL --}}
      <aside class="auth-right" aria-label="Visual Editorial Metropolitan">
        <div class="auth-header-right">
          @yield('top_right_nav')
        </div>
        <div class="auth-right-content">
          <span class="auth-brand-pill">NEWS X PAPER INDEPENDENT</span>
          <blockquote class="auth-quote">"Menyajikan jurnalisme berimbang, aktual, dan tepercaya untuk seluruh pembaca Indonesia."</blockquote>
          <span class="auth-edition">Edisi Digital Berkelanjutan</span>
        </div>
      </aside>

    </div>

  </div>

</div>

<script>
  /**
   * Fitur Lihat/Sembunyikan Kata Sandi untuk form autentikasi.
   * Mengubah tipe input password <-> text tanpa reload halaman.
   */
  function togglePasswordVisibility(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;

    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
    btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');

    var eyeOpen = btn.querySelector('.icon-eye');
    var eyeClosed = btn.querySelector('.icon-eye-slash');

    if (eyeOpen && eyeClosed) {
      if (isPassword) {
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
      } else {
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
      }
    }
  }
</script>
@stack('scripts')
</body>
</html>
