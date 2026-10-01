@extends('layouts.app')

@section('title', $cfg['name'] . ' – News X Paper')

@section('content')
@php
  $items = $paginator->getCollection();
  $first = $items->first();
  $rest  = $items->slice(1)->values();
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
          <h2>Belum ada berita di kategori {{ $cfg['name'] }}</h2>
          <p>Berita akan tampil di sini setelah dipublikasikan.</p>
          <a href="{{ route('articles.index') }}" class="cat-back">Kembali ke Beranda</a>
        </div>
      @else

        {{-- Berita utama --}}
        <article class="cat-feature">
          <a href="{{ $first['url'] }}" class="card-thumb"
             style="{{ $first['img'] ? 'background-image:url(' . $first['img'] . ')' : 'background:linear-gradient(135deg,#614385,#516395)' }}"
             aria-label="{{ $first['title'] }}">
            @if($first['img'])
              <img src="{{ $first['img'] }}" alt="{{ $first['title'] }}" loading="eager"
                   style="display:none" onerror="this.parentElement.style.backgroundImage='none'">
            @endif
            <span class="tag" style="background: {{ $cfg['color'] }}">{{ $first['cat'] }}</span>
          </a>
          <div>
            <h2 class="card-title lg"><a href="{{ $first['url'] }}">{{ $first['title'] }}</a></h2>
            <div class="card-meta">
              <b>{{ $first['author'] }}</b><span>–</span><span>{{ $first['date'] }}</span>
              <span class="badge-count">{{ $first['comments'] }}</span>
            </div>
            <p class="excerpt">{{ $first['excerpt'] }}</p>
          </div>
        </article>

        {{-- Berita lainnya --}}
        @if($rest->isNotEmpty())
          <div class="cat-grid">
            @foreach($rest as $i => $a)
              <article>
                <a href="{{ $a['url'] }}" class="card-thumb"
                   style="{{ $a['img'] ? 'background-image:url(' . $a['img'] . ')' : 'background:linear-gradient(135deg,#614385,#516395)' }}"
                   aria-label="{{ $a['title'] }}">
                  @if($a['img'])
                    <img src="{{ $a['img'] }}" alt="{{ $a['title'] }}" loading="lazy"
                         style="display:none" onerror="this.parentElement.style.backgroundImage='none'">
                  @endif
                  <span class="tag" style="background: {{ $cfg['color'] }}">{{ $a['cat'] }}</span>
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
        @endif

        {{-- Pagination --}}
        @if($paginator->lastPage() > 1)
          <nav class="pager" aria-label="Halaman">
            @if($paginator->previousPageUrl())
              <a href="{{ $paginator->previousPageUrl() }}">‹ Sebelumnya</a>
            @else <span class="off">‹ Sebelumnya</span>
            @endif
            <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
            @if($paginator->nextPageUrl())
              <a href="{{ $paginator->nextPageUrl() }}">Berikutnya ›</a>
            @else <span class="off">Berikutnya ›</span>
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
                <a href="{{ route('categories.show', $s) }}">
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
