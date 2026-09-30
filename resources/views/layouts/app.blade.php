<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'News X Paper')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/newspaper.css') }}">
</head>
<body>

  {{-- ================= HEADER ================= --}}
  <header class="site-header">
    <div class="container hd-row">

      <a href="{{ route('articles.index') }}" class="logo" aria-label="News X Paper">
        <span class="logo-word">News<span class="x">X</span>Paper</span>
        <span class="logo-tag">the art of publishing</span>
      </a>

      <form class="search" role="search" action="{{ route('search') }}" method="GET">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berita, misalnya: politik" aria-label="Cari berita" autocomplete="off">
        <button type="submit" aria-label="Cari">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
        </button>
      </form>

      <div class="hd-actions">
        @auth
          <div class="hd-user">
            <span class="hd-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="hd-name">{{ \Illuminate\Support\Str::words(auth()->user()->name, 1, '') }}</span>
          </div>
          @if (auth()->user()->is_admin)
            <a href="{{ route('dashboard') }}" class="hd-btn ghost">Dashboard</a>
          @endif
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="hd-btn ghost">Keluar</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="hd-btn primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
            Masuk
          </a>
          <a href="{{ route('register') }}" class="hd-btn ghost hide-sm">Daftar</a>
        @endauth
      </div>

    </div>

    <nav class="mainnav">
      <div class="container">
        <ul class="menu">
          <li><a href="{{ route('articles.index') }}" class="{{ request()->routeIs('articles.index') ? 'active' : '' }}">Berita</a></li>
          @foreach (config('categories') as $slug => $cat)
            <li><a href="{{ route('categories.show', $slug) }}" class="{{ request()->is('kategori/' . $slug) ? 'active' : '' }}">{{ $cat['name'] }}</a></li>
          @endforeach
        </ul>
      </div>
    </nav>
  </header>

  @yield('content')

  {{-- ================= FOOTER ================= --}}
  <footer class="site-footer">

    {{-- Baris sponsor --}}
    <div class="sponsor-strip">
      <div class="container">
        <span class="sponsor-label">Didukung oleh</span>
        <div class="sponsor-logos">
          <span>SPONSOR 1</span>
          <span>SPONSOR 2</span>
          <span>SPONSOR 3</span>
          <span>SPONSOR 4</span>
          <span>SPONSOR 5</span>
        </div>
      </div>
    </div>

    {{-- Bagian utama --}}
    <div class="footer-main">
      <div class="container footer-grid">

        <div class="f-col f-about">
          <a href="{{ route('articles.index') }}" class="f-logo">
            <span class="logo-word">News<span class="x">X</span>Paper</span>
            <span class="f-tag">the art of publishing</span>
          </a>
          <p>
            News X Paper menghadirkan kabar terkini seputar politik, ekonomi, olahraga,
            dan teknologi. Kami berkomitmen menyajikan berita yang cepat, akurat, dan mudah dibaca.
          </p>
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
            @foreach (config('categories') as $slug => $cat)
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
            <li><a href="#">Karier</a></li>
            <li><a href="#">Hubungi kami</a></li>
          </ul>
        </div>

        <div class="f-col f-news">
          <h4 class="f-title">Buletin</h4>
          <p>Dapatkan ringkasan berita pilihan langsung di email kamu.</p>
          <form class="f-form" onsubmit="event.preventDefault(); this.reset(); alert('Terima kasih! Fitur langganan belum aktif.');">
            <input type="email" placeholder="Alamat email" aria-label="Alamat email" required>
            <button type="submit">Langganan</button>
          </form>
          <ul class="f-contact">
            <li>redaksi@newsxpaper.test</li>
            <li>+62 21 0000 0000</li>
            <li>Jl. Contoh No. 1, Jakarta</li>
          </ul>
        </div>

      </div>
    </div>

    {{-- Baris paling bawah --}}
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

</body>
</html>