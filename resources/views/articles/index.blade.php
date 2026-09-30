@extends('layouts.app')

@section('title', 'News X Paper – the art of publishing')

@section('content')
@php
  // Ambil data artikel dari controller (variabel $articles)
  $src = $articles ?? $latest ?? $posts ?? [];
  if (is_object($src) && method_exists($src, 'items')) { $src = $src->items(); }

  $str = function ($v) { return is_string($v) && $v !== '' ? $v : null; };

  $items = collect($src)->values()->map(function ($a) use ($str) {
      $cat    = $str(data_get($a, 'category.name')) ?? $str(data_get($a, 'category')) ?? 'Berita';
      $author = $str(data_get($a, 'author.name')) ?? $str(data_get($a, 'author')) ?? $str(data_get($a, 'user.name')) ?? 'Admin';
      $img    = $str(data_get($a, 'image')) ?? $str(data_get($a, 'thumbnail')) ?? $str(data_get($a, 'cover')) ?? $str(data_get($a, 'image_url'));
      if ($img && ! \Illuminate\Support\Str::startsWith($img, ['http://', 'https://', '/'])) {
          $img = asset('storage/' . $img);
      }
      $slug = data_get($a, 'slug');
      $body = $str(data_get($a, 'excerpt')) ?? $str(data_get($a, 'summary')) ?? $str(data_get($a, 'body')) ?? $str(data_get($a, 'content')) ?? '';

      return [
          'title'    => $str(data_get($a, 'title')) ?? 'Tanpa judul',
          'url'      => $slug ? route('articles.show', $slug) : '#',
          'cat'      => $cat,
          'author'   => $author,
          'img'      => $img,
          'date'     => \Illuminate\Support\Carbon::parse(data_get($a, 'published_at') ?? data_get($a, 'created_at') ?? now())->locale('id')->translatedFormat('j M Y'),
          'comments' => (int) data_get($a, 'comments_count', 0),
          'excerpt'  => \Illuminate\Support\Str::limit(strip_tags($body), 130),
      ];
  });

  // Data contoh kalau belum ada artikel sama sekali
  if ($items->isEmpty()) {
      $items = collect([
          ['title' => 'Harga Bahan Pokok Stabil Menjelang Akhir Bulan', 'cat' => 'Ekonomi'],
          ['title' => 'Startup Lokal Raih Pendanaan Baru', 'cat' => 'Teknologi'],
          ['title' => 'Pemerintah Umumkan Kebijakan Baru Tahun Ini', 'cat' => 'Politik'],
          ['title' => 'Perkembangan Kecerdasan Buatan Semakin Pesat', 'cat' => 'Teknologi'],
          ['title' => 'Tim Nasional Bersiap Hadapi Laga Kualifikasi', 'cat' => 'Olahraga'],
          ['title' => 'Opini: Membaca Berita Tanpa Terjebak Judul', 'cat' => 'Opini'],
      ])->map(fn ($s) => $s + [
          'url' => '#', 'author' => 'Admin', 'img' => null, 'comments' => 0,
          'date' => now()->locale('id')->translatedFormat('j M Y'),
          'excerpt' => 'Ringkasan berita akan tampil di sini setelah artikel pertama kamu dipublikasikan.',
      ]);
  }

  // Ambil artikel ke-i (diulang otomatis kalau artikel kurang dari yang dibutuhkan layout)
  $n    = $items->count();
  $pick = fn ($i) => $items[$i % $n];

  // Khusus blok Ekonomi: pakai artikel kategori Ekonomi, kalau kurang pakai semua artikel
  $eko = $items->filter(fn ($x) => strcasecmp($x['cat'], 'Ekonomi') === 0)->values();
  if ($eko->count() < 2) { $eko = $items; }
  $ekoN = $eko->count();
  $ek   = fn ($i) => $eko[$i % $ekoN];
@endphp

<div class="container">

  {{-- Sedang tren --}}
  <div class="trending">
    <span class="trending-label">Sedang tren</span>
    <span class="trending-text">{{ $pick(0)['title'] }}</span>
    <span class="arrows"><span class="arrow">‹</span><span class="arrow">›</span></span>
  </div>

  {{-- Hero --}}
  <section class="hero">
    @foreach ([0, 1, 2, 3] as $k)
      @php $a = $pick($k); @endphp
      <a href="{{ $a['url'] }}" class="hero-item hero-{{ $k }} g{{ $k % 5 }}" @if ($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif>
        <div class="hero-body">
          <span class="tag">{{ $a['cat'] }}</span>
          <h2 class="hero-title">{{ $a['title'] }}</h2>
          @if ($k === 0)
            <div class="hero-meta"><b>{{ $a['author'] }}</b> – {{ $a['date'] }}</div>
          @endif
        </div>
      </a>
    @endforeach
  </section>

  <div class="layout">

    {{-- ===== Kolom utama ===== --}}
    <div class="main-col">

      {{-- Jangan lewatkan --}}
      <section class="block b-yellow">
        <div class="block-head">
          <span class="block-title">Jangan lewatkan</span>
          <div class="block-tabs"><span class="on">Semua</span><span>Politik</span><span>Ekonomi</span></div>
        </div>
        @php $big = $pick(4); @endphp
        <div class="dm">
          <article>
            <a href="{{ $big['url'] }}" class="card-thumb g4" @if ($big['img']) style="background-image:url('{{ $big['img'] }}')" @endif>
              <span class="tag">{{ $big['cat'] }}</span>
            </a>
            <h3 class="card-title"><a href="{{ $big['url'] }}">{{ $big['title'] }}</a></h3>
            <div class="card-meta">
              <b>{{ $big['author'] }}</b><span>–</span><span>{{ $big['date'] }}</span>
              <span class="badge-count">{{ $big['comments'] }}</span>
            </div>
            <p class="excerpt">{{ $big['excerpt'] }}</p>
          </article>

          <div class="list">
            @foreach ([5, 6, 7, 8] as $i)
              @php $a = $pick($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i % 5 }}" @if ($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
                <div>
                  <h4 class="list-title">{{ $a['title'] }}</h4>
                  <span class="card-meta">{{ $a['date'] }}</span>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </section>

      {{-- Ekonomi --}}
      <section class="block b-green">
        <div class="block-head">
          <span class="block-title">Ekonomi</span>
          <div class="block-tabs"><span class="on">Semua</span><span>Bisnis</span><span>Pasar</span></div>
        </div>
        <div class="split">
          @foreach ([0, 1] as $i)
            @php $a = $ek($i); @endphp
            <article>
              <a href="{{ $a['url'] }}" class="card-thumb g{{ ($i + 1) % 5 }}" @if ($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif>
                <span class="tag">{{ $a['cat'] }}</span>
              </a>
              <h3 class="card-title"><a href="{{ $a['url'] }}">{{ $a['title'] }}</a></h3>
              <div class="card-meta">
                <b>{{ $a['author'] }}</b><span>–</span><span>{{ $a['date'] }}</span>
                <span class="badge-count">{{ $a['comments'] }}</span>
              </div>
              <p class="excerpt">{{ $a['excerpt'] }}</p>
            </article>
          @endforeach
        </div>

        <div class="list-2">
          @foreach ([2, 3, 4, 5] as $i)
            @php $a = $ek($i); @endphp
            <a href="{{ $a['url'] }}" class="list-item">
              <span class="list-thumb g{{ $i % 5 }}" @if ($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
              <div>
                <h4 class="list-title">{{ $a['title'] }}</h4>
                <span class="card-meta">{{ $a['date'] }}</span>
              </div>
            </a>
          @endforeach
        </div>
      </section>

    </div>

    {{-- ===== Sidebar ===== --}}
    <aside>
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Tetap terhubung</span></div>
        <div class="social-list">
          <div class="social-row"><span class="social-ico s-fb">f</span><span>Suka</span></div>
          <div class="social-row"><span class="social-ico s-tw">X</span><span>Ikuti</span></div>
          <div class="social-row"><span class="social-ico s-yt">▶</span><span>Langganan</span></div>
        </div>
      </section>

      <div class="side-block"><div class="ad-box">Ruang iklan<br>300 × 250</div></div>

      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Terpopuler</span></div>
        <div class="pop-grid">
          @foreach ([1, 2, 3, 4] as $i)
            @php $a = $pick($i); @endphp
            <a href="{{ $a['url'] }}">
              <span class="pop-thumb g{{ $i % 5 }}" @if ($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
              <div class="pop-title">{{ $a['title'] }}</div>
            </a>
          @endforeach
        </div>
      </section>
    </aside>

  </div>
</div>
@endsection