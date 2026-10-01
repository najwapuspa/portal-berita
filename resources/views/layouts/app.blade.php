<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'News X Paper – the art of publishing')</title>
  <meta name="description" content="@yield('description', 'News X Paper — kabar terkini seputar politik, ekonomi, olahraga, teknologi, dan opini.')">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/newspaper.css') }}">
  @stack('head')
</head>
<body>

{{-- ===== HEADER ===== --}}
<header class="site-header">
  <div class="container hd-row">

    {{-- Logo --}}
    <a href="{{ route('articles.index') }}" class="logo" aria-label="News X Paper Beranda">
      <span class="logo-word">News<span class="x">X</span>Paper</span>
      <span class="logo-tag">the art of publishing</span>
    </a>

    {{-- Search + autosuggest --}}
    <div class="search-wrap" role="search">
      <form class="search" id="search-form" action="{{ route('search') }}" method="GET">
        <input type="search" name="q" id="search-input"
               value="{{ request('q') }}"
               placeholder="Cari berita, misalnya: politik"
               aria-label="Cari berita"
               autocomplete="off"
               aria-expanded="false"
               aria-haspopup="listbox">
        <button type="submit" aria-label="Cari">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>
          </svg>
        </button>
      </form>
      <ul class="suggest-list" id="suggest-list" role="listbox" aria-label="Saran pencarian" hidden></ul>
    </div>

    {{-- Aksi header --}}
    <div class="hd-actions">
      @auth
        <div class="hd-user">
          <span class="hd-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
          <span class="hd-name">{{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}</span>
        </div>
        <a href="{{ route('dashboard') }}" class="hd-btn ghost">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="hd-btn ghost">Keluar</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="hd-btn primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
          </svg>
          Masuk
        </a>
        <a href="{{ route('register') }}" class="hd-btn ghost hide-sm">Daftar</a>
      @endauth
    </div>

    {{-- Hamburger mobile --}}
    <button class="hamburger-nav" id="hamburger-nav" aria-label="Buka navigasi" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>

  {{-- Navigasi kategori --}}
  <nav class="mainnav" id="mainnav" aria-label="Kategori berita">
    <div class="container">
      <ul class="menu" id="main-menu">
        <li>
          <a href="{{ route('articles.index') }}"
             class="{{ request()->routeIs('articles.index') ? 'active' : '' }}">Berita</a>
        </li>
        @foreach(config('categories') as $slug => $cat)
          <li>
            <a href="{{ route('categories.show', $slug) }}"
               class="{{ request()->is('kategori/' . $slug) ? 'active' : '' }}">
              {{ $cat['name'] }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </nav>
</header>

@yield('content')

{{-- ===== FOOTER ===== --}}
<footer class="site-footer">
  <div class="sponsor-strip">
    <div class="container">
      <span class="sponsor-label">Didukung oleh</span>
      <div class="sponsor-logos">
        <span>SPONSOR 1</span><span>SPONSOR 2</span>
        <span>SPONSOR 3</span><span>SPONSOR 4</span><span>SPONSOR 5</span>
      </div>
    </div>
  </div>

  <div class="footer-main">
    <div class="container footer-grid">

      <div class="f-col f-about">
        <a href="{{ route('articles.index') }}" class="f-logo">
          <span class="logo-word">News<span class="x">X</span>Paper</span>
          <span class="f-tag">the art of publishing</span>
        </a>
        <p>News X Paper menghadirkan kabar terkini seputar politik, ekonomi, olahraga, dan teknologi — cepat, akurat, mudah dibaca.</p>
        <div class="f-social">
          <a href="#" aria-label="Facebook">f</a>
          <a href="#" aria-label="Instagram">in</a>
          <a href="#" aria-label="X">X</a>
          <a href="#" aria-label="YouTube">▶</a>
        </div>
      </div>

      <div class="f-col">
        <h4 class="f-title">Kategori</h4>
        <ul class="f-links">
          @foreach(config('categories') as $slug => $cat)
            <li><a href="{{ route('categories.show', $slug) }}">{{ $cat['name'] }}</a></li>
          @endforeach
        </ul>
      </div>

      <div class="f-col">
        <h4 class="f-title">Perusahaan</h4>
        <ul class="f-links">
          <li><a href="#">Tentang kami</a></li>
          <li><a href="#">Redaksi</a></li>
          <li><a href="#">Pedoman media siber</a></li>
          <li><a href="#">Pasang iklan</a></li>
          <li><a href="#">Hubungi kami</a></li>
        </ul>
      </div>

      <div class="f-col f-news">
        <h4 class="f-title">Buletin</h4>
        <p>Dapatkan ringkasan berita pilihan langsung di email kamu.</p>

        {{-- Flash messages newsletter --}}
        @if(session('newsletter_success'))
          <div class="nl-alert nl-success" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('newsletter_success') }}
          </div>
        @elseif(session('newsletter_info'))
          <div class="nl-alert nl-info" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('newsletter_info') }}
          </div>
        @endif

        {{-- Form newsletter — real Laravel POST --}}
        <form class="f-form" method="POST" action="{{ route('newsletter.subscribe') }}" novalidate>
          @csrf
          <div class="f-form-group">
            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="Alamat email"
                   aria-label="Alamat email untuk newsletter"
                   autocomplete="email"
                   class="{{ $errors->has('email') ? 'f-input-error' : '' }}">
            <button type="submit">Langganan</button>
          </div>
          @error('email')
            <p class="nl-error">{{ $message }}</p>
          @enderror
        </form>

        <ul class="f-contact">
          <li>redaksi@newsxpaper.test</li>
          <li>+62 21 0000 0000</li>
          <li>Jl. Contoh No. 1, Jakarta</li>
        </ul>
      </div>

    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <span>&copy; {{ date('Y') }} News X Paper. Semua hak dilindungi.</span>
      <div class="fb-links">
        <a href="#">Kebijakan privasi</a>
        <a href="#">Syarat &amp; ketentuan</a>
        <a href="#">Peta situs</a>
      </div>
    </div>
  </div>
</footer>

<script>
// ── Hamburger mobile nav ─────────────────────────────────────────
(function() {
  const btn  = document.getElementById('hamburger-nav');
  const menu = document.getElementById('main-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => {
    const open = menu.classList.toggle('mobile-open');
    btn.setAttribute('aria-expanded', open);
  });
})();

// ── Autosuggest ──────────────────────────────────────────────────
(function() {
  const input  = document.getElementById('search-input');
  const list   = document.getElementById('suggest-list');
  if (!input || !list) return;

  let timer, controller;

  function hide() { list.hidden = true; input.setAttribute('aria-expanded', 'false'); }
  function show() { list.hidden = false; input.setAttribute('aria-expanded', 'true'); }

  input.addEventListener('input', () => {
    clearTimeout(timer);
    const q = input.value.trim();
    if (q.length < 2) { hide(); return; }

    timer = setTimeout(async () => {
      if (controller) controller.abort();
      controller = new AbortController();
      try {
        const res  = await fetch('/api/suggest?q=' + encodeURIComponent(q), { signal: controller.signal });
        const data = await res.json();
        list.innerHTML = '';
        if (!data.length) { hide(); return; }
        data.forEach(item => {
          const li = document.createElement('li');
          li.setAttribute('role', 'option');
          li.innerHTML = `<span class="sg-type sg-${item.type}">${item.type === 'kategori' ? '📂' : '📰'}</span>
                          <span class="sg-label">${item.label}</span>`;
          li.addEventListener('click', () => {
            if (item.type === 'kategori') {
              window.location.href = '/kategori/' + item.slug;
            } else {
              window.location.href = '/berita/' + item.slug;
            }
          });
          list.appendChild(li);
        });
        show();
      } catch(e) { /* aborted or network error */ }
    }, 300);
  });

  // Keyboard navigation
  input.addEventListener('keydown', e => {
    const items  = list.querySelectorAll('li');
    const active = list.querySelector('li.sg-active');
    let idx = Array.from(items).indexOf(active);
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (active) active.classList.remove('sg-active');
      idx = (idx + 1) % items.length;
      items[idx]?.classList.add('sg-active');
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (active) active.classList.remove('sg-active');
      idx = (idx - 1 + items.length) % items.length;
      items[idx]?.classList.add('sg-active');
    } else if (e.key === 'Enter' && active) {
      e.preventDefault();
      active.click();
    } else if (e.key === 'Escape') {
      hide();
    }
  });

  document.addEventListener('click', e => {
    if (!e.target.closest('.search-wrap')) hide();
  });
})();

// ── Auto-scroll ke pesan newsletter jika ada ────────────────────
(function() {
  const nl = document.querySelector('.nl-alert');
  if (nl) {
    nl.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
})();
</script>
@stack('scripts')
</body>
</html>
