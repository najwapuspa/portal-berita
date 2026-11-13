<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- SEO dasar --}}
  <title>@yield('title', 'News X Paper – the art of publishing')</title>
  <meta name="description" content="@yield('description', 'News X Paper — kabar terkini seputar politik, ekonomi, olahraga, teknologi, dan opini.')">
  <meta name="robots" content="index, follow">
  <link rel="canonical" href="{{ url()->current() }}">

  {{-- Open Graph default (bisa di-override tiap halaman) --}}
  <meta property="og:site_name" content="News X Paper">
  <meta property="og:type"      content="website">
  <meta property="og:url"       content="{{ url()->current() }}">
  <meta property="og:title"     content="@yield('og_title', 'News X Paper – the art of publishing')">
  <meta property="og:description" content="@yield('og_description', 'Kabar terkini seputar politik, ekonomi, olahraga, teknologi, dan opini.')">
  @hasSection('og_image')
    <meta property="og:image"   content="@yield('og_image')">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
  @endif

  {{-- Twitter Card --}}
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="@yield('og_title', 'News X Paper')">
  <meta name="twitter:description" content="@yield('og_description', 'Kabar terkini seputar politik, ekonomi, olahraga, dan teknologi.')">
  @hasSection('og_image')
    <meta name="twitter:image"  content="@yield('og_image')">
  @endif

  {{-- Slot tambahan meta dari halaman anak --}}
  @stack('meta')

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/newspaper.css') }}">
  <style>
    /* Language Switcher in Header */
    .hd-lang-switcher {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #202020;
      border: 1px solid #383838;
      border-radius: 4px;
      padding: 5px 9px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.06em;
      line-height: 1;
    }
    .hd-lang-btn {
      color: #888888;
      text-decoration: none;
      transition: color 0.15s ease;
    }
    .hd-lang-btn:hover {
      color: #ffffff;
    }
    .hd-lang-btn.active {
      color: #ffffff;
      font-weight: 800;
      text-decoration: underline;
      text-underline-offset: 3px;
    }
    .hd-lang-divider {
      color: #444444;
      font-size: 10px;
    }
  </style>
  @stack('head')
</head>
<body>

{{-- ===== HEADER ===== --}}
<header class="site-header">
  <div class="container hd-row">

    <a href="{{ route('articles.index') }}" class="logo" aria-label="News X Paper Beranda">
      <span class="logo-word">News<span class="x">X</span>Paper</span>
      <span class="logo-tag">the art of publishing</span>
    </a>

    {{-- Search + autosuggest --}}
    <div class="search-wrap" role="search">
      <form class="search" id="search-form" action="{{ route('search') }}" method="GET">
        <input type="search" name="q" id="search-input"
               value="{{ request('q') }}"
               placeholder="{{ __('Cari berita, misalnya: politik') }}"
               aria-label="{{ __('Cari berita') }}"
               autocomplete="off"
               aria-expanded="false"
               aria-haspopup="listbox">
        <button type="submit" aria-label="{{ __('Cari') }}">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>
          </svg>
        </button>
      </form>
      <ul class="suggest-list" id="suggest-list" role="listbox" aria-label="Saran pencarian" hidden></ul>
    </div>

    {{-- Aksi header --}}
    <div class="hd-actions">
      {{-- Language Switcher ID | EN --}}
      <div class="hd-lang-switcher" aria-label="{{ __('Pilihan Bahasa') }}">
        <a href="{{ route('lang.switch', 'id') }}"
           class="hd-lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}"
           title="Bahasa Indonesia">ID</a>
        <span class="hd-lang-divider">|</span>
        <a href="{{ route('lang.switch', 'en') }}"
           class="hd-lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}"
           title="English">EN</a>
      </div>

      @auth
        <div class="hd-user">
          <span class="hd-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
          <span class="hd-name">{{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}</span>
        </div>
        <a href="{{ route('dashboard') }}" class="hd-btn ghost">{{ __('Dashboard') }}</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="hd-btn ghost">{{ __('Keluar') }}</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="hd-btn primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
            <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/>
          </svg>
          {{ __('Masuk') }}
        </a>
        <a href="{{ route('register') }}" class="hd-btn ghost hide-sm">{{ __('Daftar') }}</a>
      @endauth
    </div>

    <button class="hamburger-nav" id="hamburger-nav" aria-label="{{ __('Buka navigasi') }}" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>

  <nav class="mainnav" id="mainnav" aria-label="{{ __('Kategori') }}">
    <div class="container">
      <ul class="menu" id="main-menu">
        <li><a href="{{ route('articles.index') }}"
               class="{{ request()->routeIs('articles.index') ? 'active' : '' }}">{{ __('Beranda') }}</a></li>
        <li><a href="{{ route('news.index') }}"
               class="{{ request()->routeIs('news.index') ? 'active' : '' }}">{{ __('Berita') }}</a></li>
        {{-- Kategori utama tampil langsung (7 pertama) --}}
        @php $catArray = array_slice(config('categories'), 0, 7, true); @endphp
        @foreach($catArray as $slug => $cat)
          @php $catName = app()->getLocale() === 'en' && !empty($cat['name_en']) ? $cat['name_en'] : $cat['name']; @endphp
          <li><a href="{{ route('categories.show', $slug) }}"
                 class="{{ request()->is('kategori/' . $slug) ? 'active' : '' }}">{{ $catName }}</a></li>
        @endforeach
        {{-- Sisa kategori dalam dropdown "Lainnya" --}}
        @php $moreArray = array_slice(config('categories'), 7, null, true); @endphp
        @if(count($moreArray) > 0)
          <li class="menu-more">
            <button class="menu-more-btn" id="menu-more-btn" aria-expanded="false" aria-haspopup="true">
              {{ __('Lainnya') }} <span class="menu-more-arrow">▾</span>
            </button>
            <ul class="menu-dropdown" id="menu-dropdown" role="menu">
              @foreach($moreArray as $slug => $cat)
                @php $catName = app()->getLocale() === 'en' && !empty($cat['name_en']) ? $cat['name_en'] : $cat['name']; @endphp
                <li role="none"><a href="{{ route('categories.show', $slug) }}" role="menuitem"
                   class="{{ request()->is('kategori/' . $slug) ? 'active' : '' }}">{{ $catName }}</a></li>
              @endforeach
            </ul>
          </li>
        @endif
        <li><a href="{{ route('about') }}"
               class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('Tentang Kami') }}</a></li>
      </ul>
    </div>
  </nav>
</header>

@yield('content')

{{-- ===== FOOTER ===== --}}
<footer class="site-footer">

  <div class="footer-main">
    <div class="container footer-grid">

      <div class="f-col f-about">
        <a href="{{ route('articles.index') }}" class="f-logo">
          <span class="logo-word">News<span class="x">X</span>Paper</span>
          <span class="f-tag">{{ __('the art of publishing') }}</span>
        </a>
        <p>{{ __('News X Paper menghadirkan kabar terkini seputar politik, ekonomi, olahraga, dan teknologi — cepat, akurat, mudah dibaca.') }}</p>
        <div class="f-social">
          <a href="#" aria-label="Facebook">f</a>
          <a href="#" aria-label="Instagram">in</a>
          <a href="#" aria-label="X">X</a>
          <a href="#" aria-label="YouTube">▶</a>
        </div>
      </div>

      <div class="f-col">
        <h4 class="f-title">{{ __('Kategori') }}</h4>
        <ul class="f-links">
          @foreach(config('categories') as $slug => $cat)
            @php $catName = app()->getLocale() === 'en' && !empty($cat['name_en']) ? $cat['name_en'] : $cat['name']; @endphp
            <li><a href="{{ route('categories.show', $slug) }}">{{ $catName }}</a></li>
          @endforeach
        </ul>
      </div>

      <div class="f-col">
        <h4 class="f-title">{{ __('Perusahaan') }}</h4>
        <ul class="f-links">
          <li><a href="{{ route('about') }}">{{ __('Tentang kami') }}</a></li>
          <li><a href="#">{{ __('Redaksi') }}</a></li>
          <li><a href="#">{{ __('Pedoman media siber') }}</a></li>
          <li><a href="#">{{ __('Pasang iklan') }}</a></li>
          <li><a href="#">{{ __('Hubungi kami') }}</a></li>
        </ul>
      </div>

      <div class="f-col f-news">
        <h4 class="f-title">{{ __('Buletin') }}</h4>
        <p>{{ __('Dapatkan ringkasan berita pilihan langsung di email kamu.') }}</p>

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

        <form class="f-form" method="POST" action="{{ route('newsletter.subscribe') }}" novalidate>
          @csrf
          <div class="f-form-group">
            <input type="email" name="email" value="{{ old('email') }}"
                   placeholder="{{ __('Alamat email kamu') }}" aria-label="{{ __('Alamat email kamu') }}"
                   autocomplete="email"
                   class="{{ $errors->has('email') ? 'f-input-error' : '' }}">
            <button type="submit">{{ __('Langganan') }}</button>
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
      <span>&copy; {{ date('Y') }} News X Paper. {{ __('Hak cipta dilindungi undang-undang.') }}</span>
      <div class="fb-links">
      <a href="#">{{ __('Kebijakan privasi') }}</a>
        <a href="#">{{ __('Syarat &amp; ketentuan') }}</a>
        <a href="#">{{ __('Peta situs') }}</a>
      </div>
    </div>
  </div>
</footer>

<script>
(function() {
  const btn  = document.getElementById('hamburger-nav');
  const menu = document.getElementById('main-menu');
  if (!btn || !menu) return;
  btn.addEventListener('click', () => {
    const open = menu.classList.toggle('mobile-open');
    btn.setAttribute('aria-expanded', open);
  });
})();

(function() {
  const input = document.getElementById('search-input');
  const list  = document.getElementById('suggest-list');
  if (!input || !list) return;
  let timer, controller;
  const hide = () => { list.hidden = true; input.setAttribute('aria-expanded','false'); };
  const show = () => { list.hidden = false; input.setAttribute('aria-expanded','true'); };
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
          li.innerHTML = `<span class="sg-type">${item.type === 'kategori' ? '📂' : '📰'}</span><span class="sg-label">${item.label}</span>`;
          li.addEventListener('click', () => {
            window.location.href = item.type === 'kategori' ? '/kategori/' + item.slug : '/berita/' + item.slug;
          });
          list.appendChild(li);
        });
        show();
      } catch(e) {}
    }, 300);
  });
  input.addEventListener('keydown', e => {
    const items  = list.querySelectorAll('li');
    const active = list.querySelector('li.sg-active');
    let idx = Array.from(items).indexOf(active);
    if (e.key === 'ArrowDown')  { e.preventDefault(); if(active) active.classList.remove('sg-active'); items[(idx+1)%items.length]?.classList.add('sg-active'); }
    else if (e.key === 'ArrowUp')   { e.preventDefault(); if(active) active.classList.remove('sg-active'); items[(idx-1+items.length)%items.length]?.classList.add('sg-active'); }
    else if (e.key === 'Enter' && active) { e.preventDefault(); active.click(); }
    else if (e.key === 'Escape') hide();
  });
  document.addEventListener('click', e => { if(!e.target.closest('.search-wrap')) hide(); });
})();

(function() {
  const nl = document.querySelector('.nl-alert');
  if (nl) nl.scrollIntoView({ behavior: 'smooth', block: 'center' });
})();

// ── Dropdown "Lainnya" ────────────────────────────────────────
(function() {
  const btn  = document.getElementById('menu-more-btn');
  const menu = document.getElementById('menu-dropdown');
  if (!btn || !menu) return;
  function close() { menu.classList.remove('open'); btn.setAttribute('aria-expanded','false'); }
  function open()  { menu.classList.add('open');    btn.setAttribute('aria-expanded','true'); }
  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    menu.classList.contains('open') ? close() : open();
  });
  document.addEventListener('click', () => close());
  document.addEventListener('keydown', e => { if(e.key==='Escape') close(); });
})();
</script>
@stack('scripts')
</body>
</html>
