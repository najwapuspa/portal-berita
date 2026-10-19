<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk – News X Paper</title>
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* ─── Reset & Base ─────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    html, body { margin: 0; min-height: 100%; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: #0b1220;
      color: #1a202c;
      -webkit-font-smoothing: antialiased;
    }

    /* ─── Layout split ─────────────────────────────────────────── */
    .login-page {
      display: grid;
      grid-template-columns: 1fr 480px;
      min-height: 100vh;
    }

    /* ─── Panel kiri: identitas editorial ─────────────────────── */
    .editorial-panel {
      position: relative;
      overflow: hidden;
      background: #0b1220;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 48px 56px;
    }
    .editorial-panel::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 70% 60% at 20% 110%, rgba(99,102,241,.18) 0%, transparent 70%),
        radial-gradient(ellipse 50% 40% at 90% -10%, rgba(139,92,246,.12) 0%, transparent 60%);
      pointer-events: none;
    }

    /* Garis vertikal dekoratif kanan */
    .editorial-panel::after {
      content: '';
      position: absolute;
      top: 0; right: 0;
      width: 1px;
      height: 100%;
      background: linear-gradient(to bottom, transparent, rgba(99,102,241,.4) 30%, rgba(99,102,241,.4) 70%, transparent);
    }

    /* Wordmark logo */
    .ep-logo {
      position: relative;
      z-index: 1;
    }
    .ep-logo-wordmark {
      display: flex;
      align-items: baseline;
      gap: 2px;
      line-height: 1;
    }
    .ep-logo-text {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 28px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -.5px;
    }
    .ep-logo-text .x-accent {
      color: #818cf8;
    }
    .ep-logo-tagline {
      font-size: 11px;
      font-weight: 500;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: rgba(255,255,255,.38);
      margin-top: 7px;
      display: block;
    }

    /* Konten tengah editorial */
    .ep-body {
      position: relative;
      z-index: 1;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 60px 0;
    }
    .ep-overline {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .2em;
      text-transform: uppercase;
      color: #818cf8;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .ep-overline::before {
      content: '';
      display: block;
      width: 32px;
      height: 1.5px;
      background: #818cf8;
    }
    .ep-headline {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: clamp(32px, 3.6vw, 52px);
      font-weight: 800;
      color: #ffffff;
      line-height: 1.15;
      letter-spacing: -.5px;
      margin: 0 0 24px;
      max-width: 520px;
    }
    .ep-headline em {
      font-style: italic;
      color: #818cf8;
    }
    .ep-desc {
      font-size: 15px;
      line-height: 1.7;
      color: rgba(255,255,255,.5);
      max-width: 420px;
      margin: 0 0 48px;
    }

    /* Statistik kecil */
    .ep-stats {
      display: flex;
      gap: 36px;
    }
    .ep-stat-num {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 700;
      color: #fff;
      display: block;
      line-height: 1;
      margin-bottom: 4px;
    }
    .ep-stat-label {
      font-size: 11px;
      color: rgba(255,255,255,.35);
      text-transform: uppercase;
      letter-spacing: .1em;
    }

    /* Bottom editorial panel */
    .ep-footer {
      position: relative;
      z-index: 1;
    }
    .ep-footer-links {
      display: flex;
      gap: 24px;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .ep-footer-links a {
      font-size: 12px;
      color: rgba(255,255,255,.28);
      text-decoration: none;
      transition: color .15s;
    }
    .ep-footer-links a:hover { color: rgba(255,255,255,.65); }

    /* ─── Panel kanan: form login ──────────────────────────────── */
    .form-panel {
      background: #f8f9fc;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 56px 48px;
      position: relative;
    }

    /* Header form */
    .fp-header {
      margin-bottom: 36px;
    }
    .fp-eyebrow {
      font-size: 11px;
      font-weight: 700;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #6366f1;
      margin-bottom: 10px;
    }
    .fp-title {
      font-family: 'Playfair Display', Georgia, serif;
      font-size: 28px;
      font-weight: 800;
      color: #0b1220;
      margin: 0 0 6px;
      line-height: 1.2;
    }
    .fp-subtitle {
      font-size: 14px;
      color: #64748b;
      margin: 0;
    }

    /* Alert error */
    .alert-error {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-left: 3px solid #ef4444;
      border-radius: 8px;
      padding: 12px 14px;
      font-size: 13.5px;
      color: #b91c1c;
      font-weight: 500;
      margin-bottom: 24px;
      line-height: 1.4;
    }
    .alert-error svg { flex-shrink: 0; margin-top: 1px; }

    /* Form groups */
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 7px;
      letter-spacing: .01em;
    }
    .input-wrap {
      position: relative;
    }
    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      pointer-events: none;
      transition: color .15s;
    }
    .form-input {
      width: 100%;
      height: 48px;
      padding: 0 44px 0 42px;
      font: inherit;
      font-size: 14px;
      font-weight: 500;
      color: #0b1220;
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
      -webkit-appearance: none;
    }
    .form-input::placeholder { color: #a0aec0; font-weight: 400; }
    .form-input:focus {
      border-color: #6366f1;
      box-shadow: 0 0 0 3px rgba(99,102,241,.12);
    }
    .form-input:focus ~ .input-icon,
    .input-wrap:focus-within .input-icon { color: #6366f1; }
    .form-input.input-error { border-color: #ef4444; }
    .form-input.input-error:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.12); }

    /* Password toggle button */
    .pw-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: 0;
      cursor: pointer;
      color: #94a3b8;
      padding: 6px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      transition: color .15s, background .15s;
    }
    .pw-toggle:hover { color: #6366f1; background: rgba(99,102,241,.06); }
    .pw-toggle:focus-visible { outline: 2px solid #6366f1; }
    #pw-toggle-icon-hide { display: none; }

    /* Field error message */
    .field-error {
      display: none;
      font-size: 12px;
      color: #ef4444;
      font-weight: 500;
      margin-top: 5px;
    }
    .has-error .field-error { display: block; }
    .has-error .form-input { border-color: #ef4444; }

    /* Remember + forgot row */
    .form-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }
    .remember-label {
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      font-size: 13.5px;
      font-weight: 500;
      color: #4b5563;
      user-select: none;
    }
    .remember-checkbox {
      width: 17px;
      height: 17px;
      border: 1.5px solid #cbd5e0;
      border-radius: 5px;
      appearance: none;
      -webkit-appearance: none;
      background: #fff;
      cursor: pointer;
      flex-shrink: 0;
      transition: background .12s, border-color .12s;
      position: relative;
    }
    .remember-checkbox:checked {
      background: #6366f1;
      border-color: #6366f1;
    }
    .remember-checkbox:checked::after {
      content: '';
      position: absolute;
      left: 4px; top: 2px;
      width: 5px; height: 8px;
      border: 2px solid #fff;
      border-top: 0; border-left: 0;
      transform: rotate(40deg);
    }
    .remember-checkbox:focus-visible { outline: 2px solid #6366f1; outline-offset: 1px; }
    .forgot-link {
      font-size: 13px;
      font-weight: 600;
      color: #6366f1;
      text-decoration: none;
      transition: color .15s;
    }
    .forgot-link:hover { color: #4f46e5; text-decoration: underline; }

    /* Submit button */
    .btn-submit {
      width: 100%;
      height: 50px;
      font: inherit;
      font-size: 14px;
      font-weight: 700;
      letter-spacing: .04em;
      color: #ffffff;
      background: #6366f1;
      border: 0;
      border-radius: 10px;
      cursor: pointer;
      transition: background .15s, transform .05s, box-shadow .15s;
      box-shadow: 0 2px 12px rgba(99,102,241,.3);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }
    .btn-submit:hover:not(:disabled) {
      background: #4f46e5;
      box-shadow: 0 4px 18px rgba(99,102,241,.4);
    }
    .btn-submit:active:not(:disabled) { transform: translateY(1px); }
    .btn-submit:focus-visible { outline: 3px solid rgba(99,102,241,.5); outline-offset: 2px; }
    .btn-submit:disabled { opacity: .7; cursor: not-allowed; }

    /* Loading spinner inside button */
    .btn-spinner {
      display: none;
      width: 18px; height: 18px;
      border: 2px solid rgba(255,255,255,.35);
      border-top-color: #fff;
      border-radius: 50%;
      animation: spin .65s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .btn-submit.loading .btn-text  { display: none; }
    .btn-submit.loading .btn-spinner { display: block; }

    /* Divider */
    .form-divider {
      text-align: center;
      margin: 22px 0;
      position: relative;
      font-size: 12px;
      color: #a0aec0;
      font-weight: 500;
    }
    .form-divider::before, .form-divider::after {
      content: '';
      position: absolute;
      top: 50%;
      width: calc(50% - 32px);
      height: 1px;
      background: #e2e8f0;
    }
    .form-divider::before { left: 0; }
    .form-divider::after  { right: 0; }

    /* Register link */
    .form-register {
      text-align: center;
      font-size: 13.5px;
      color: #64748b;
    }
    .form-register a {
      color: #6366f1;
      font-weight: 700;
      text-decoration: none;
      transition: color .15s;
    }
    .form-register a:hover { color: #4f46e5; text-decoration: underline; }

    /* ─── Responsif ─────────────────────────────────────────────── */
    @media (max-width: 960px) {
      .login-page {
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr;
      }
      .editorial-panel {
        padding: 32px 28px;
        min-height: auto;
      }
      .editorial-panel::after { display: none; }
      .ep-body { padding: 32px 0; }
      .ep-headline { font-size: 28px; }
      .ep-desc, .ep-stats { display: none; }
      .form-panel { padding: 40px 28px 56px; }
    }
    @media (max-width: 480px) {
      .form-panel { padding: 32px 20px 48px; }
      .editorial-panel { padding: 24px 20px; }
      .ep-headline { font-size: 24px; }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation: none !important; transition: none !important; }
    }
  </style>
</head>
<body>
<div class="login-page">

  {{-- ===== PANEL KIRI: EDITORIAL ===== --}}
  <div class="editorial-panel" aria-hidden="false">

    {{-- Logo --}}
    <div class="ep-logo">
      <a href="{{ route('articles.index') }}" style="text-decoration:none">
        <div class="ep-logo-wordmark">
          <span class="ep-logo-text">News<span class="x-accent">X</span>Paper</span>
        </div>
        <span class="ep-logo-tagline">The Art of Publishing</span>
      </a>
    </div>

    {{-- Konten editorial --}}
    <div class="ep-body">
      <p class="ep-overline">Portal Berita Premium</p>
      <h1 class="ep-headline">Kabar Terkini, <em>Terpercaya,</em> Setiap Saat.</h1>
      <p class="ep-desc">
        Akses ribuan artikel dari berbagai kategori — nasional, internasional,
        ekonomi, teknologi, olahraga, dan lebih banyak lagi.
        Selalu terdepan dalam informasi.
      </p>
      <div class="ep-stats">
        <div>
          <span class="ep-stat-num">12+</span>
          <span class="ep-stat-label">Kategori</span>
        </div>
        <div>
          <span class="ep-stat-num">455+</span>
          <span class="ep-stat-label">Artikel</span>
        </div>
        <div>
          <span class="ep-stat-num">3</span>
          <span class="ep-stat-label">Sumber Media</span>
        </div>
      </div>
    </div>

    {{-- Footer editorial --}}
    <div class="ep-footer">
      <ul class="ep-footer-links">
        <li><a href="#">Kebijakan Privasi</a></li>
        <li><a href="#">Syarat &amp; Ketentuan</a></li>
        <li><a href="{{ route('articles.index') }}">Kembali ke Beranda</a></li>
      </ul>
    </div>
  </div>

  {{-- ===== PANEL KANAN: FORM LOGIN ===== --}}
  <div class="form-panel">

    <div class="fp-header">
      <p class="fp-eyebrow">Akses Redaksi</p>
      <h2 class="fp-title">Selamat Datang</h2>
      <p class="fp-subtitle">Masuk dengan akun NEWS X PAPER Anda.</p>
    </div>

    {{-- Error global --}}
    @if($errors->any())
      <div class="alert-error" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
          <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        {{ $errors->first() }}
      </div>
    @endif

    <form id="login-form" method="POST" action="{{ route('login') }}" novalidate>
      @csrf

      {{-- Email --}}
      <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}" id="grp-email">
        <label class="form-label" for="email">Alamat Email</label>
        <div class="input-wrap">
          <svg class="input-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
          </svg>
          <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="nama@email.com"
            class="form-input {{ $errors->has('email') ? 'input-error' : '' }}"
            autocomplete="email"
            required
            aria-describedby="email-error">
        </div>
        <span class="field-error" id="email-error">
          @error('email') {{ $message }} @else Masukkan alamat email yang valid. @enderror
        </span>
      </div>

      {{-- Kata Sandi --}}
      <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}" id="grp-password">
        <label class="form-label" for="password">Kata Sandi</label>
        <div class="input-wrap">
          <svg class="input-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Kata sandi Anda"
            class="form-input {{ $errors->has('password') ? 'input-error' : '' }}"
            autocomplete="current-password"
            required
            aria-describedby="password-error">
          <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Tampilkan kata sandi" aria-pressed="false">
            {{-- Ikon mata: show --}}
            <svg id="pw-toggle-icon-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            {{-- Ikon mata: hide --}}
            <svg id="pw-toggle-icon-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
          </button>
        </div>
        <span class="field-error" id="password-error">
          @error('password') {{ $message }} @else Kata sandi wajib diisi. @enderror
        </span>
      </div>

      {{-- Remember + Forgot --}}
      <div class="form-options">
        <label class="remember-label">
          <input type="checkbox" name="remember" id="remember" class="remember-checkbox"
            {{ old('remember') ? 'checked' : '' }} value="1">
          Ingat Saya
        </label>
        <a href="#" class="forgot-link">Lupa Kata Sandi?</a>
      </div>

      {{-- Submit --}}
      <button type="submit" class="btn-submit" id="btn-submit">
        <span class="btn-text">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true" style="margin-right:4px;vertical-align:middle">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          Masuk
        </span>
        <span class="btn-spinner" aria-hidden="true"></span>
      </button>

      {{-- Divider + register --}}
      <div class="form-divider">atau</div>
      <p class="form-register">
        Belum punya akun?
        <a href="{{ route('register') }}">Daftar Sekarang</a>
      </p>
    </form>

    {{-- Copyright kecil --}}
    <p style="margin:32px 0 0;font-size:11px;color:#b0bec5;text-align:center;letter-spacing:.04em">
      &copy; {{ date('Y') }} News X Paper &middot; All rights reserved
    </p>
  </div>

</div>

<script>
// ── Toggle tampilkan/sembunyikan kata sandi ──────────────────────
(function() {
  const btn    = document.getElementById('pw-toggle');
  const input  = document.getElementById('password');
  const iconShow = document.getElementById('pw-toggle-icon-show');
  const iconHide = document.getElementById('pw-toggle-icon-hide');
  if (!btn || !input) return;
  btn.addEventListener('click', () => {
    const isHidden = input.type === 'password';
    input.type     = isHidden ? 'text' : 'password';
    iconShow.style.display = isHidden ? 'none'  : 'block';
    iconHide.style.display = isHidden ? 'block' : 'none';
    btn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
    btn.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
  });
})();

// ── Validasi client-side + loading state ────────────────────────
(function() {
  const form   = document.getElementById('login-form');
  const emailEl = document.getElementById('email');
  const pwEl    = document.getElementById('password');
  const btnEl   = document.getElementById('btn-submit');

  function setError(fieldId, inputEl, hasError) {
    const grp = document.getElementById(fieldId);
    if (!grp) return;
    grp.classList.toggle('has-error', hasError);
    inputEl.classList.toggle('input-error', hasError);
    inputEl.setAttribute('aria-invalid', hasError ? 'true' : 'false');
  }

  // Live: bersihkan error saat user mengetik
  emailEl.addEventListener('input', () => {
    if (emailEl.value.trim()) setError('grp-email', emailEl, false);
  });
  pwEl.addEventListener('input', () => {
    if (pwEl.value) setError('grp-password', pwEl, false);
  });

  form.addEventListener('submit', (e) => {
    let valid = true;
    const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailEl.value.trim());
    const pwOk    = pwEl.value.length >= 1;

    if (!emailOk) { setError('grp-email', emailEl, true); valid = false; }
    if (!pwOk)    { setError('grp-password', pwEl, true); valid = false; }

    if (!valid) { e.preventDefault(); return; }

    // Loading state
    btnEl.classList.add('loading');
    btnEl.disabled = true;
  });
})();
</script>
</body>
</html>
