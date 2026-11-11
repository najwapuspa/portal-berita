<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'News X Paper')</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ─── RESET & ROOT VARIABLES ──────────────────────────────── */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    :root {
      --bg-outer: #292929;
      --bg-frame: #FDFDFC;
      --bg-ivory: #EAE9E5;
      --text-primary: #202020;
      --text-secondary: #777570;
      --border-line: #B7B4AE;
      --btn-bg: #202020;
      --btn-hover: #383838;
      --accent-champagne: #B69B69;
      --error-color: #a82727;
      --success-color: #276738;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    html, body {
      min-height: 100vh;
      background: var(--bg-outer);
      font-family: var(--font-sans);
      color: var(--text-primary);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* ─── VIEWPORT WRAPPER ────────────────────────────────────── */
    .haus-viewport {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 36px 24px;
    }

    /* ─── WINDOW FRAME (WHITE BEZEL LIKE HAUS REFERENCE) ──────── */
    .haus-frame {
      position: relative;
      width: 100%;
      max-width: 1060px;
      height: 640px;
      background: var(--bg-frame);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 25px 70px rgba(0, 0, 0, 0.45), 0 4px 15px rgba(0, 0, 0, 0.25);
    }

    /* Camera aperture / dot in the top-left white bezel */
    .haus-frame-dot {
      position: absolute;
      top: 10px;
      left: 14px;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--text-primary);
      z-index: 25;
      opacity: 0.85;
      pointer-events: none;
    }

    /* ─── SPLIT-SCREEN CONTAINER ──────────────────────────────── */
    .haus-split {
      display: grid;
      grid-template-columns: 51.6% 48.4%;
      height: 100%;
      width: 100%;
    }

    /* ─── LEFT PANEL (WARM IVORY) ─────────────────────────────── */
    .haus-left {
      background: var(--bg-ivory);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 24px 44px 22px 44px;
      position: relative;
      overflow-y: auto;
    }

    /* Top Navigation Left */
    .haus-header-left {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      width: 100%;
    }
    .haus-logo {
      font-family: var(--font-serif);
      font-size: 23px;
      font-weight: 700;
      color: var(--text-primary);
      text-decoration: none;
      letter-spacing: -0.4px;
      line-height: 1;
      transition: opacity 0.15s ease;
    }
    .haus-logo:hover {
      opacity: 0.8;
    }
    .haus-nav-links {
      display: flex;
      align-items: center;
      gap: 22px;
      list-style: none;
    }
    .haus-nav-links a {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.16em;
      color: var(--text-primary);
      text-decoration: none;
      transition: color 0.15s ease;
    }
    .haus-nav-links a:hover {
      color: var(--accent-champagne);
    }

    /* Form Container Middle */
    .haus-main {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;
      margin: auto 0;
      padding: 16px 0;
    }
    .haus-form-wrap {
      width: 100%;
      max-width: 290px;
    }

    /* Heading: Large Elegant Serif */
    .haus-heading {
      font-family: var(--font-serif);
      font-size: 38px;
      font-weight: 600;
      color: var(--text-primary);
      text-align: center;
      letter-spacing: -0.5px;
      margin-bottom: 24px;
      line-height: 1.15;
    }
    .haus-desc {
      font-size: 12.5px;
      color: var(--text-secondary);
      text-align: center;
      line-height: 1.5;
      margin-top: -16px;
      margin-bottom: 24px;
    }

    /* Alerts */
    .haus-alert {
      font-size: 11.5px;
      line-height: 1.45;
      padding: 10px 12px;
      border-radius: 2px;
      margin-bottom: 18px;
      text-align: left;
    }
    .haus-alert-error {
      background: #fbf0f0;
      border-left: 3px solid var(--error-color);
      color: var(--error-color);
    }
    .haus-alert-success {
      background: #f0f7f2;
      border-left: 3px solid var(--success-color);
      color: var(--success-color);
    }

    /* Fields: Underline minimalist */
    .haus-field {
      margin-bottom: 18px;
      position: relative;
    }
    .haus-label {
      display: block;
      font-size: 9.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.16em;
      color: var(--text-secondary);
      margin-bottom: 2px;
    }
    .haus-input {
      width: 100%;
      background: transparent;
      border: none;
      border-bottom: 1px solid var(--border-line);
      border-radius: 0;
      padding: 6px 0;
      font-family: var(--font-sans);
      font-size: 13.5px;
      color: var(--text-primary);
      outline: none;
      transition: border-color 0.2s ease;
    }
    .haus-input:focus {
      border-bottom-color: var(--text-primary);
    }
    .haus-input.has-error {
      border-bottom-color: var(--error-color);
    }
    .haus-error-text {
      font-size: 10.5px;
      color: var(--error-color);
      margin-top: 4px;
    }

    /* Password wrap with show/hide toggle */
    .haus-password-wrap {
      position: relative;
      display: flex;
      align-items: center;
      width: 100%;
    }
    .haus-password-wrap .haus-input {
      padding-right: 32px;
    }
    .haus-pw-toggle {
      position: absolute;
      right: 0;
      bottom: 4px;
      background: transparent;
      border: none;
      padding: 4px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: var(--text-secondary);
      border-radius: 2px;
      line-height: 1;
      transition: color 0.15s ease;
    }
    .haus-pw-toggle:hover,
    .haus-pw-toggle:focus-visible {
      color: var(--text-primary);
      outline: none;
    }
    .haus-pw-toggle svg {
      width: 17px;
      height: 17px;
      display: block;
      pointer-events: none;
    }

    /* Sub-field links (Forgot password, Remember me) */
    .haus-field-sub {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-top: 6px;
    }
    .haus-forgot-link {
      font-size: 9.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      color: var(--text-secondary);
      text-decoration: none;
      transition: color 0.15s ease;
    }
    .haus-forgot-link:hover {
      color: var(--text-primary);
      text-decoration: underline;
    }

    /* Remember me */
    .haus-remember-wrap {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 14px;
      cursor: pointer;
    }
    .haus-remember-wrap input[type="checkbox"] {
      accent-color: var(--text-primary);
      cursor: pointer;
    }
    .haus-remember-wrap span {
      font-size: 11px;
      color: var(--text-secondary);
      letter-spacing: 0.04em;
    }

    /* Button */
    .haus-btn-wrap {
      margin-top: 24px;
      text-align: center;
    }
    .haus-btn {
      display: inline-block;
      background: var(--btn-bg);
      color: #ffffff;
      font-family: var(--font-sans);
      font-size: 10.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.22em;
      padding: 11px 36px;
      border: none;
      border-radius: 2px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.18s ease, transform 0.1s ease;
    }
    .haus-btn:hover {
      background: var(--btn-hover);
      transform: translateY(-1px);
    }
    .haus-btn:active {
      transform: translateY(0);
    }

    /* Footer: Thin divider and sub-link */
    .haus-footer {
      width: 100%;
      padding-top: 10px;
    }
    .haus-divider {
      border: none;
      border-top: 1px solid var(--border-line);
      width: 100%;
      margin: 0 0 14px 0;
      opacity: 0.85;
    }
    .haus-footer-text {
      text-align: center;
      font-size: 9.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      color: var(--text-secondary);
      line-height: 1.4;
    }
    .haus-footer-text a {
      color: var(--text-primary);
      font-weight: 700;
      text-decoration: underline;
      transition: color 0.15s ease;
    }
    .haus-footer-text a:hover {
      color: var(--accent-champagne);
    }

    /* ─── RIGHT PANEL (EXACT HAUS DRINK PHOTOGRAPH) ───────────── */
    .haus-right {
      position: relative;
      background-image: url('{{ asset('images/city-metropolitan.jpg') }}');
      background-size: cover;
      background-position: center center;
      background-repeat: no-repeat;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      overflow: hidden;
    }
    .haus-header-right {
      display: flex;
      justify-content: flex-end;
      align-items: baseline;
      padding: 24px 38px 0 0;
      z-index: 10;
    }
    .haus-header-right a {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.16em;
      color: var(--text-primary);
      text-decoration: none;
      background: rgba(253, 253, 252, 0.45);
      padding: 4px 10px;
      border-radius: 2px;
      backdrop-filter: blur(4px);
      transition: background 0.15s ease, color 0.15s ease;
    }
    .haus-header-right a:hover {
      background: rgba(253, 253, 252, 0.8);
      color: var(--accent-champagne);
    }

    /* ─── RESPONSIVE: TABLET & MOBILE ─────────────────────────── */
    @media (max-width: 900px) {
      .haus-frame {
        max-width: 92%;
        height: auto;
        min-height: 600px;
      }
      .haus-left {
        padding: 22px 30px 20px 30px;
      }
      .haus-nav-links {
        gap: 14px;
      }
    }

    @media (max-width: 768px) {
      .haus-viewport {
        padding: 12px;
        min-height: 100vh;
      }
      .haus-frame {
        max-width: 100%;
        height: auto;
        min-height: auto;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
      }
      .haus-split {
        display: flex;
        flex-direction: column;
      }
      /* Photo becomes top banner on mobile per instruction */
      .haus-right {
        order: 1;
        height: 200px;
        width: 100%;
        border-bottom: 1px solid var(--border-line);
      }
      .haus-header-right {
        padding: 14px 18px 0 0;
      }
      .haus-left {
        order: 2;
        padding: 24px 22px 24px 22px;
        min-height: 480px;
      }
      .haus-heading {
        font-size: 32px;
        margin-bottom: 20px;
      }
      .haus-form-wrap {
        max-width: 100%;
      }
    }
  </style>
  @stack('styles')
</head>
<body>

<div class="haus-viewport">

  <div class="haus-frame">
    {{-- Device camera bezel dot like HAUS reference --}}
    <div class="haus-frame-dot" aria-hidden="true"></div>

    <div class="haus-split">

      {{-- LEFT PANEL: WARM IVORY AUTHENTICATION --}}
      <section class="haus-left">

        {{-- Topbar left: Logo + Navigation --}}
        <header class="haus-header-left">
          <a href="{{ route('articles.index') }}" class="haus-logo" aria-label="News X Paper Beranda">
            News X Paper
          </a>

          <nav class="haus-nav" aria-label="Navigasi Utama">
            <ul class="haus-nav-links">
              <li><a href="{{ route('articles.index') }}">BERANDA</a></li>
              <li><a href="{{ route('news.index') }}">BERITA</a></li>
              <li><a href="{{ route('about') }}">TENTANG KAMI</a></li>
            </ul>
          </nav>
        </header>

        {{-- Main form container --}}
        <main class="haus-main">
          <div class="haus-form-wrap">
            <h1 class="haus-heading">@yield('heading')</h1>

            @yield('content')
          </div>
        </main>

        {{-- Footer area: divider line + bottom account link --}}
        <footer class="haus-footer">
          <hr class="haus-divider" aria-hidden="true">
          <p class="haus-footer-text">
            @yield('footer_text')
          </p>
        </footer>

      </section>

      {{-- RIGHT PANEL: EXACT HAUS DRINK PHOTOGRAPH --}}
      <aside class="haus-right" aria-label="Visual Referensi Editorial">
        <div class="haus-header-right">
          @yield('top_right_nav')
        </div>
      </aside>

    </div>

  </div>

</div>

<script>
  /**
   * Fitur Show/Hide Password untuk form autentikasi.
   * Mengubah type input password <-> text tanpa reload atau reset nilai input.
   */
  function togglePasswordVisibility(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;

    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
    btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Lihat password');

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

