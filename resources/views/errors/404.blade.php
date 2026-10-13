<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 – Halaman Tidak Ditemukan · News X Paper</title>
  <meta name="robots" content="noindex">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/newspaper.css') }}">
  <style>
    .err-page {
      min-height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 48px 24px;
    }
    .err-code {
      font-size: clamp(80px, 16vw, 160px);
      font-weight: 900;
      line-height: 1;
      color: #111;
      letter-spacing: -4px;
    }
    .err-code span { color: #4db2ec; }
    .err-title {
      font-size: clamp(20px, 3vw, 28px);
      font-weight: 700;
      margin: 16px 0 12px;
      color: #111;
    }
    .err-desc {
      font-size: 15px;
      color: #777;
      max-width: 420px;
      line-height: 1.65;
      margin: 0 auto 32px;
    }
    .err-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: center;
    }
    .err-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 13px 28px;
      font-size: 14px;
      font-weight: 700;
      border-radius: 4px;
      text-decoration: none;
      transition: background .15s;
    }
    .err-btn-primary { background: #111; color: #fff; }
    .err-btn-primary:hover { background: #4db2ec; }
    .err-btn-ghost { background: #f3f4f6; color: #444; border: 1px solid #e5e5e5; }
    .err-btn-ghost:hover { background: #e5e7eb; }
    .err-divider {
      width: 60px; height: 3px; background: #4db2ec;
      margin: 0 auto 20px;
    }
    .err-categories {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-top: 36px;
    }
    .err-cat-link {
      padding: 7px 16px;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      border: 1px solid #e5e5e5;
      border-radius: 2px;
      color: #555;
      text-decoration: none;
      transition: background .12s, color .12s;
    }
    .err-cat-link:hover { background: #111; color: #fff; border-color: #111; }
  </style>
</head>
<body>

{{-- Mini header --}}
<header class="site-header" style="position:static">
  <div class="container hd-row">
    <a href="{{ route('articles.index') }}" class="logo" aria-label="News X Paper Beranda">
      <span class="logo-word">News<span class="x">X</span>Paper</span>
      <span class="logo-tag">the art of publishing</span>
    </a>
    <div class="hd-actions" style="margin-left:auto">
      <a href="{{ route('articles.index') }}" class="hd-btn ghost">Beranda</a>
      @auth
        <a href="{{ route('dashboard') }}" class="hd-btn primary">Dashboard</a>
      @else
        <a href="{{ route('login') }}" class="hd-btn primary">Masuk</a>
      @endauth
    </div>
  </div>
</header>

{{-- Konten 404 --}}
<main class="container err-page" role="main">
  <div class="err-code">4<span>0</span>4</div>
  <div class="err-divider" aria-hidden="true"></div>
  <h1 class="err-title">Halaman Tidak Ditemukan</h1>
  <p class="err-desc">
    Halaman yang kamu cari tidak ada, mungkin sudah dipindahkan,
    dihapus, atau URL-nya salah ketik.
  </p>

  <div class="err-actions">
    <a href="{{ route('articles.index') }}" class="err-btn err-btn-primary">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Kembali ke Beranda
    </a>
    <a href="{{ route('search') }}" class="err-btn err-btn-ghost">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
      Cari Berita
    </a>
  </div>

  <div class="err-categories" aria-label="Jelajahi kategori">
    @foreach(config('categories') as $slug => $cat)
      <a href="{{ route('categories.show', $slug) }}" class="err-cat-link">{{ $cat['name'] }}</a>
    @endforeach
  </div>
</main>

{{-- Mini footer --}}
<div style="border-top:1px solid #e5e5e5;padding:16px 0;text-align:center;font-size:12px;color:#aaa;margin-top:40px">
  &copy; {{ date('Y') }} News X Paper. Semua hak dilindungi.
</div>

</body>
</html>
