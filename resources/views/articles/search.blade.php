@extends('layouts.app')

@section('title', ($q !== '' ? 'Cari: ' . $q : 'Cari berita') . ' – News X Paper')

@section('content')
<div class="container">

  <div class="cat-head" style="--accent: #222">
    <h1 class="cat-title">Hasil pencarian</h1>
    @if ($q !== '')
      <span class="cat-count">{{ $total }} berita untuk “{{ $q }}”</span>
    @endif
  </div>

  <div class="layout">

    {{-- ===== Kolom utama ===== --}}
    <div class="main-col">

      @if ($q === '')
        <div class="cat-empty">
          <h2>Ketik kata kunci di kolom pencarian</h2>
          <p>Contoh: politik, ekonomi, olahraga, atau judul berita.</p>
          <a href="{{ route('articles.index') }}" class="cat-back">Kembali ke beranda</a>
        </div>

      @else

        @if ($catSlug)
          <a class="search-cat" style="--accent: {{ config('categories')[$catSlug]['color'] }}" href="{{ route('categories.show', $catSlug) }}">
            Lihat semua berita kategori {{ config('categories')[$catSlug]['name'] }} ›
          </a>
        @endif

        @if ($total === 0)
          <div class="cat-empty">
            <h2>Tidak ada berita untuk “{{ $q }}”</h2>
            <p>Coba kata kunci lain, atau periksa ejaan kamu.</p>
            <a href="{{ route('articles.index') }}" class="cat-back">Kembali ke beranda</a>
          </div>
        @else

          @foreach ($paginator as $i => $a)
            <article class="result">
              <a href="{{ $a['url'] }}" class="card-thumb g{{ $i % 5 }}" @if ($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif>
                <span class="tag">{{ $a['cat'] }}</span>
              </a>
              <div>
                <h3 class="card-title"><a href="{{ $a['url'] }}">{{ $a['title'] }}</a></h3>
                <div class="card-meta">
                  <b>{{ $a['author'] }}</b><span>–</span><span>{{ $a['date'] }}</span>
                  <span class="badge-count">{{ $a['comments'] }}</span>
                </div>
                <p class="excerpt">{{ $a['excerpt'] }}</p>
              </div>
            </article>
          @endforeach

          @if ($paginator->lastPage() > 1)
            <nav class="pager" aria-label="Halaman">
              @if ($paginator->previousPageUrl())
                <a href="{{ $paginator->previousPageUrl() }}">‹ Sebelumnya</a>
              @else
                <span class="off">‹ Sebelumnya</span>
              @endif

              <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>

              @if ($paginator->nextPageUrl())
                <a href="{{ $paginator->nextPageUrl() }}">Berikutnya ›</a>
              @else
                <span class="off">Berikutnya ›</span>
              @endif
            </nav>
          @endif

        @endif
      @endif
    </div>

    {{-- ===== Sidebar ===== --}}
    <aside>
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Kategori</span></div>
        <ul class="cat-list">
          @foreach (config('categories') as $s => $c)
            <li>
              <a href="{{ route('categories.show', $s) }}">
                <i style="background: {{ $c['color'] }}"></i>{{ $c['name'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </section>

      <div class="side-block"><div class="ad-box">Ruang iklan<br>300 × 250</div></div>
    </aside>

  </div>
</div>
@endsection