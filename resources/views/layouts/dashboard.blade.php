<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>@yield('judul', 'Dashboard') – NewsXPaper</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  @stack('head')
</head>
<body>

{{-- ===== TOPBAR ===== --}}
<div class="top">
  <div class="wrap">
    <a class="logo" href="{{ url('/') }}">NEWS<i>X</i>PAPER<small>the art of publishing</small></a>
    <div class="who">
      <span class="who-name">{{ auth()->user()->name }}</span>
      <span class="who-role {{ auth()->user()->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
        {{ auth()->user()->role === 'admin' ? 'Admin' : 'Pembaca' }}
      </span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn pastel">Keluar</button>
      </form>
    </div>
  </div>
</div>

{{-- ===== SIDEBAR + KONTEN ===== --}}
<div class="dash-layout">

  {{-- Sidebar --}}
  <aside class="sidebar" id="sidebar">
    <button class="sidebar-close" id="sidebar-close" aria-label="Tutup menu">✕</button>

    @if(auth()->user()->role === 'admin')
      <nav class="sidebar-nav" aria-label="Menu admin">
        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard
        </a>
        <a href="{{ route('admin.articles.index') }}"
           class="{{ request()->routeIs('admin.articles.*') ? 'on' : '' }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Kelola Berita
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="{{ request()->routeIs('admin.users.*') ? 'on' : '' }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Kelola Pengguna
        </a>
        <a href="{{ route('admin.statistik') }}"
           class="{{ request()->routeIs('admin.statistik') ? 'on' : '' }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          Statistik
        </a>
        <a href="{{ route('articles.index') }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Lihat Situs
        </a>
      </nav>
    @else
      <nav class="sidebar-nav" aria-label="Menu pembaca">
        <a href="{{ route('user.dashboard') }}"
           class="{{ request()->routeIs('user.dashboard') ? 'on' : '' }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          Dashboard Saya
        </a>
        <a href="{{ route('articles.index') }}">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
          Beranda
        </a>
      </nav>
    @endif
  </aside>

  {{-- Konten utama --}}
  <main class="dash-main">
    <div class="dash-topbar">
      <button class="hamburger" id="hamburger" aria-label="Buka menu">
        <span></span><span></span><span></span>
      </button>
      <div class="dash-breadcrumb">@yield('breadcrumb', 'Dashboard')</div>
      <div class="bar-info">
        <time id="tgl" class="dash-time"></time>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-ok" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-err" role="alert">{{ session('error') }}</div>
    @endif

    <h1 class="judul">@yield('hero')</h1>
    @yield('konten')
  </main>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<script>
  // Clock
  function tick() {
    const now = new Date();
    const tgl = now.toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric' });
    const jam = now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' }).replace('.',':');
    const el  = document.getElementById('tgl');
    if (el) { el.textContent = tgl + ', ' + jam; el.setAttribute('datetime', now.toISOString()); }
  }
  tick(); setInterval(tick, 30000);

  // Sidebar mobile
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebar-overlay');
  const ham      = document.getElementById('hamburger');
  const closeBtn = document.getElementById('sidebar-close');
  function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('show'); }
  function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('show'); }
  if (ham)      ham.addEventListener('click', openSidebar);
  if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
  if (overlay)  overlay.addEventListener('click', closeSidebar);
</script>
@stack('scripts')
</body>
</html>
