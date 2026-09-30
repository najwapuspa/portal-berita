<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daftar – News X Paper</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --card-top: rgba(96, 34, 160, .80);
      --card-bottom: rgba(58, 14, 118, .90);
      --field: rgba(255, 255, 255, .22);
      --field-focus: rgba(255, 255, 255, .34);
      --text: #fff;
      --text-soft: rgba(255, 255, 255, .78);
      --error-text: #ffd3dc;
      --font: "Raleway", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    * { box-sizing: border-box; }
    html, body { margin: 0; height: 100%; }
    body { font-family: var(--font); color: var(--text); -webkit-font-smoothing: antialiased; }

    .stage {
      position: relative; min-height: 100vh; overflow: hidden;
      background: linear-gradient(180deg, #4318b5 0%, #6a24bd 38%, #a63fbc 66%, #d95fb2 86%, #c247a6 100%);
    }
    .sky {
      position: absolute; inset: 0; filter: blur(6px);
      background:
        radial-gradient(ellipse 30% 20% at 16% 58%, rgba(255, 160, 220, .55), transparent 70%),
        radial-gradient(ellipse 34% 24% at 44% 30%, rgba(255, 255, 255, .20), transparent 70%),
        radial-gradient(ellipse 26% 18% at 30% 72%, rgba(255, 140, 210, .50), transparent 70%),
        radial-gradient(ellipse 22% 30% at 88% 46%, rgba(255, 150, 230, .32), transparent 70%),
        radial-gradient(ellipse 40% 16% at 62% 14%, rgba(180, 140, 255, .28), transparent 70%);
    }
    .scene { position: absolute; left: 0; bottom: 0; width: 100%; height: 44%; pointer-events: none; }
    .ring { position: absolute; border-radius: 50%; border: 7px solid rgba(190, 88, 224, .75); pointer-events: none; }
    .ring.r1 { width: 64px; height: 64px; top: -14px; left: 22px; }
    .ring.r2 { width: 92px; height: 92px; right: 4%; bottom: 34%; border-color: rgba(176, 60, 140, .6); }
    .ring.r3 { width: 92px; height: 92px; right: calc(4% + 40px); bottom: 30%; border-color: rgba(176, 60, 140, .6); }
    .ring.r4 { width: 130px; height: 130px; left: 17%; bottom: -26px; border-color: rgba(200, 80, 220, .7); }
    .tri {
      position: absolute; top: 0; left: 50%; width: 0; height: 0; pointer-events: none;
      border-top: 24px solid transparent; border-bottom: 24px solid transparent;
      border-left: 40px solid rgba(255, 255, 255, .22);
    }

    .wrap {
      position: relative; z-index: 2; min-height: 100vh;
      max-width: 1100px; margin: 0 auto; padding: 40px 28px;
      display: flex; align-items: center; justify-content: space-between; gap: 40px;
    }
    .welcome h1 {
      margin: 0; font-size: clamp(34px, 5vw, 56px); font-weight: 800;
      letter-spacing: .12em; line-height: 1.15; text-shadow: 0 4px 24px rgba(50, 10, 100, .35);
    }
    .welcome p { margin: 10px 0 0; font-size: clamp(14px, 1.7vw, 18px); font-weight: 500; letter-spacing: .14em; color: var(--text-soft); }
    .brand {
      display: inline-block; margin-bottom: 22px; font-size: 13px; font-weight: 700;
      letter-spacing: .2em; text-transform: uppercase; color: rgba(255, 255, 255, .85); text-decoration: none;
    }

    .panel {
      position: relative; overflow: hidden; flex: 0 0 auto; width: 340px;
      padding: 40px 34px 30px;
      background: linear-gradient(180deg, var(--card-top), var(--card-bottom));
      -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
      box-shadow: 0 24px 60px rgba(30, 0, 70, .5);
    }
    .panel h2 { margin: 0 0 20px 4px; font-size: 20px; font-weight: 800; letter-spacing: .04em; }

    .field { margin-bottom: 14px; }
    label.lbl { display: block; margin: 0 0 6px 4px; font-size: 13px; font-weight: 700; }
    .input-wrap { position: relative; }
    input[type="text"], input[type="email"], input[type="password"] {
      width: 100%; height: 40px; padding: 0 18px;
      font: inherit; font-size: 13.5px; font-weight: 500; color: var(--text);
      background: var(--field); border: 1px solid transparent; border-radius: 999px; outline: none;
      transition: background .15s, border-color .15s, box-shadow .15s;
    }
    input::placeholder { color: rgba(255, 255, 255, .6); }
    input:focus { background: var(--field-focus); border-color: rgba(255, 255, 255, .65); box-shadow: 0 0 0 4px rgba(255, 255, 255, .14); }
    input.invalid { border-color: #ff9db1; }
    #password { padding-right: 68px; }
    .toggle-pw {
      position: absolute; top: 50%; right: 8px; transform: translateY(-50%);
      padding: 5px 10px; font: inherit; font-size: 12px; font-weight: 700;
      color: rgba(255, 255, 255, .9); background: none; border: 0; border-radius: 999px; cursor: pointer;
    }
    .toggle-pw:hover { background: rgba(255, 255, 255, .14); }
    .toggle-pw:focus-visible { outline: 2px solid #fff; }

    .error-msg { margin: 6px 0 0 6px; font-size: 12px; color: var(--error-text); }
    .error-msg.js { display: none; }
    .field.has-error .error-msg.js { display: block; }

    .btn {
      width: 100%; height: 40px; margin-top: 6px;
      font: inherit; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
      color: #fff; background: linear-gradient(90deg, #9548d8, #55179c);
      border: 0; border-radius: 999px; box-shadow: 0 8px 18px rgba(30, 0, 80, .35);
      cursor: pointer; transition: filter .15s, transform .05s;
    }
    .btn:hover { filter: brightness(1.12); }
    .btn:active { transform: translateY(1px); }
    .btn:focus-visible { outline: 3px solid rgba(255, 255, 255, .6); outline-offset: 2px; }
    .btn[disabled] { opacity: .7; cursor: progress; }

    .links { margin-top: 22px; font-size: 12px; }
    .links p { margin: 0; color: rgba(255, 255, 255, .85); }
    .links a { color: #fff; font-weight: 700; text-decoration: underline; }

    @media (max-width: 820px) {
      .wrap { flex-direction: column; justify-content: center; align-items: stretch; gap: 28px; padding: 36px 20px 48px; }
      .welcome { text-align: center; }
      .panel { width: 100%; max-width: 380px; margin: 0 auto; }
      .ring.r2, .ring.r3 { display: none; }
      .scene { height: 30%; }
    }
    @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
  </style>
</head>
<body>

  <div class="stage">
    <div class="sky"></div>
    <span class="ring r1"></span>
    <span class="ring r2"></span>
    <span class="ring r3"></span>
    <span class="ring r4"></span>
    <span class="tri"></span>

    <svg class="scene" viewBox="0 0 1440 360" preserveAspectRatio="xMidYMax slice" aria-hidden="true">
      <defs>
        <linearGradient id="gnd" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0" stop-color="#e460ae"/>
          <stop offset="1" stop-color="#b53ea4"/>
        </linearGradient>
      </defs>
      <path d="M0 262 Q 260 236 560 258 T 1100 250 T 1440 256 V360 H0Z" fill="#c9509f" opacity=".55"/>
      <path d="M0 292 Q 320 262 700 286 T 1440 276 V360 H0Z" fill="url(#gnd)"/>
      <path d="M0 292 L10 256 L22 244 L52 240 L74 250 L82 292Z" fill="#a137a2"/>
      <path d="M318 296 L330 214 L346 204 L392 206 L410 216 L424 296Z" fill="#a137a2"/>
      <path d="M318 296 L330 214 L346 204 L362 206 L362 296Z" fill="#8a2c98"/>
      <path d="M470 296 L482 248 L500 240 L534 242 L548 254 L556 296Z" fill="#a137a2"/>
      <path d="M1320 292 L1332 244 L1352 236 L1388 240 L1404 252 L1412 292Z" fill="#a137a2"/>
    </svg>

    <main class="wrap">
      <section class="welcome">
        <a class="brand" href="{{ route('articles.index') }}">News X Paper</a>
        <h1>Bergabung</h1>
        <p>Buat akun untuk mulai membaca dan berdiskusi…</p>
      </section>

      <section class="panel">
        <h2>Buat akun baru</h2>

        <form id="register-form" method="POST" action="{{ route('register') }}" novalidate>
          @csrf

          <div class="field" id="field-name">
            <label class="lbl" for="name">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" required class="{{ $errors->has('name') ? 'invalid' : '' }}">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            <p class="error-msg js">Nama wajib diisi.</p>
          </div>

          <div class="field" id="field-email">
            <label class="lbl" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" required class="{{ $errors->has('email') ? 'invalid' : '' }}">
            @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            <p class="error-msg js">Masukkan alamat email yang valid.</p>
          </div>

          <div class="field" id="field-password">
            <label class="lbl" for="password">Kata sandi</label>
            <div class="input-wrap">
              <input type="password" id="password" name="password" autocomplete="new-password" required class="{{ $errors->has('password') ? 'invalid' : '' }}">
              <button type="button" class="toggle-pw" id="toggle-pw" aria-label="Tampilkan kata sandi">Lihat</button>
            </div>
            @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            <p class="error-msg js">Kata sandi minimal 8 karakter.</p>
          </div>

          <div class="field" id="field-confirm">
            <label class="lbl" for="password_confirmation">Ulangi kata sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
            <p class="error-msg js">Konfirmasi kata sandi tidak cocok.</p>
          </div>

          <button type="submit" class="btn" id="submit-btn">Daftar</button>

          <div class="links">
            <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
          </div>
        </form>
      </section>
    </main>
  </div>

  <script>
    const form = document.getElementById('register-form');
    const nameEl = document.getElementById('name');
    const emailEl = document.getElementById('email');
    const pwEl = document.getElementById('password');
    const cfEl = document.getElementById('password_confirmation');
    const btn = document.getElementById('submit-btn');

    const toggle = document.getElementById('toggle-pw');
    toggle.addEventListener('click', () => {
      const show = pwEl.type === 'password';
      pwEl.type = show ? 'text' : 'password';
      cfEl.type = show ? 'text' : 'password';
      toggle.textContent = show ? 'Sembunyi' : 'Lihat';
      toggle.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });

    function setError(input, fieldId, hasError) {
      document.getElementById(fieldId).classList.toggle('has-error', hasError);
      input.classList.toggle('invalid', hasError);
    }

    function validate() {
      const okName = nameEl.value.trim().length > 0;
      const okEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailEl.value.trim());
      const okPw = pwEl.value.length >= 8;
      const okCf = cfEl.value === pwEl.value && cfEl.value.length > 0;
      setError(nameEl, 'field-name', !okName);
      setError(emailEl, 'field-email', !okEmail);
      setError(pwEl, 'field-password', !okPw);
      setError(cfEl, 'field-confirm', !okCf);
      return okName && okEmail && okPw && okCf;
    }

    form.addEventListener('submit', (e) => {
      if (!validate()) { e.preventDefault(); return; }
      btn.disabled = true;
      btn.textContent = 'Memproses…';
    });
  </script>
</body>
</html>