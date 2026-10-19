@extends('layouts.app')

@section('title', $cfg['name'] . ' – News X Paper')
@section('description', 'Berita terbaru kategori ' . $cfg['name'] . ' dari News X Paper.')

@section('content')
@php
  $items    = $paginator->getCollection();
  $first    = $items->first();
  $rest     = $items->slice(1)->values();
  // Fallback default jika tidak ada gambar sama sekali
  $imgFallback = asset('images/placeholder-' . $slug . '.svg');
  // Jika file per slug tidak ada, gunakan placeholder generik
  if (!file_exists(public_path('images/placeholder-' . $slug . '.svg'))) {
      $imgFallback = asset('images/placeholder-news.svg');
  }
@endphp

<div class="container">

  <div class="cat-head" style="--accent: {{ $cfg['color'] }}">
    <h1 class="cat-title">{{ $cfg['name'] }}</h1>
    <span class="cat-count">{{ $total }} berita</span>
  </div>

  <div class="layout">
    {{-- ===== Kolom utama ===== --}}
    <div class="main-col">

      @if(!$first)
        <div class="cat-empty">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" style="margin-bottom:16px" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          <h2>Belum ada berita di kategori {{ $cfg['name'] }}</h2>
          <p>Berita akan muncul di sini setelah dipublikasikan.</p>
          <a href="{{ route('articles.index') }}" class="cat-back">Kembali ke Beranda</a>
        </div>
      @else

        {{-- Berita utama --}}
        <article class="cat-feature">
          @php
            $firstImg = $first['img'] ?? $imgFallback;
          @endphp
          <a href="{{ $first['url'] }}" class="card-thumb"
             style="background-image: url('{{ $firstImg }}')"
             aria-label="{{ $first['title'] }}">
            <img src="{{ $firstImg }}" alt="" loading="eager" style="display:none"
                 onerror="this.parentElement.style.backgroundImage='url({{ asset('images/placeholder-news.svg') }})'">
            <span class="tag" style="background: {{ $cfg['color'] }}">{{ $first['cat'] }}</span>
          </a>
          <div>
            <h2 class="card-title lg"><a href="{{ $first['url'] }}">{{ $first['title'] }}</a></h2>
            <div class="card-meta">
              <b>{{ $first['author'] }}</b>
              <span>–</span>
              <span>{{ $first['date'] }}</span>
              @if(!empty($first['read_time']))
                <span>· {{ $first['read_time'] }} menit baca</span>
              @endif
              <span class="badge-count">{{ $first['comments'] }}</span>
            </div>
            @if(!empty($first['source']))
              <div style="margin:6px 0;font-size:11px;color:#1e8fd6;font-weight:700;text-transform:uppercase;letter-spacing:.04em">
                📡 {{ $first['source'] }}
              </div>
            @endif
            <p class="excerpt">{{ $first['excerpt'] }}</p>
            <a href="{{ $first['url'] }}" class="fa-read-btn" style="margin-top:14px;font-size:12px;padding:8px 18px">
              Baca Selengkapnya →
            </a>
          </div>
        </article>

        {{-- Berita lainnya --}}
        @if($rest->isNotEmpty())
          <div class="cat-grid">
            @foreach($rest as $i => $a)
              @php $aImg = $a['img'] ?? $imgFallback; @endphp
              <article>
                <a href="{{ $a['url'] }}" class="card-thumb"
                   style="background-image: url('{{ $aImg }}')"
                   aria-label="{{ $a['title'] }}">
                  <img src="{{ $aImg }}" alt="" loading="lazy" style="display:none"
                       onerror="this.parentElement.style.backgroundImage='url({{ asset('images/placeholder-news.svg') }})'">
                  <span class="tag" style="background: {{ $cfg['color'] }}">{{ $a['cat'] }}</span>
                </a>
                <h3 class="card-title"><a href="{{ $a['url'] }}">{{ $a['title'] }}</a></h3>
                <div class="card-meta">
                  <b>{{ $a['author'] }}</b><span>–</span><span>{{ $a['date'] }}</span>
                  @if(!empty($a['read_time']))
                    <span>· {{ $a['read_time'] }} mnt</span>
                  @endif
                  <span class="badge-count">{{ $a['comments'] }}</span>
                </div>
                @if(!empty($a['source']))
                  <span style="font-size:10px;color:#1e8fd6;font-weight:700;text-transform:uppercase">{{ $a['source'] }}</span>
                @endif
                <p class="excerpt">{{ $a['excerpt'] }}</p>
              </article>
            @endforeach
          </div>
        @endif

        {{-- Pagination --}}
        @if($paginator->lastPage() > 1)
          <nav class="pager" aria-label="Halaman">
            @if($paginator->previousPageUrl())
              <a href="{{ $paginator->previousPageUrl() }}">‹ Sebelumnya</a>
            @else
              <span class="off">‹ Sebelumnya</span>
            @endif
            <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
            @if($paginator->nextPageUrl())
              <a href="{{ $paginator->nextPageUrl() }}">Berikutnya ›</a>
            @else
              <span class="off">Berikutnya ›</span>
            @endif
          </nav>
        @endif

      @endif
    </div>

    {{-- ===== Sidebar ===== --}}
    <aside>
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Kategori Lainnya</span></div>
        <ul class="cat-list">
          @foreach(config('categories') as $s => $c)
            @if($s !== $slug)
              <li>
                <a href="{{ route('categories.show', $s) }}"
                   style="{{ request()->is('kategori/' . $s) ? 'color:' . $c['color'] . ';font-weight:700' : '' }}">
                  <i style="background: {{ $c['color'] }}"></i>{{ $c['name'] }}
                </a>
              </li>
            @endif
          @endforeach
        </ul>
      </section>
      <div class="side-block"><div class="ad-box">Ruang Iklan<br>300 × 250</div></div>
    </aside>
  </div>
</div>
@endsection
