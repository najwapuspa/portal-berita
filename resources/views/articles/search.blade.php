@extends('layouts.app')

@section('title', ($q !== '' ? __('Cari') . ': ' . $q : __('Cari Berita')) . ' – News X Paper')

@section('content')
<div class="container">

  <div class="cat-head" style="--accent: #222">
    <h1 class="cat-title">{{ __('Hasil Pencarian') }}</h1>
    @if($q !== '')
      <span class="cat-count">{{ __(':total berita untuk ":q"', ['total' => $total, 'q' => $q]) }}</span>
    @endif
  </div>

  <div class="layout">
    {{-- ===== Kolom utama ===== --}}
    <div class="main-col">

      @if($q === '')
        <div class="cat-empty">
          <h2>{{ __('Ketik kata kunci di kolom pencarian') }}</h2>
          <p>{{ __('Contoh: politik, ekonomi, olahraga, atau judul berita.') }}</p>
          <a href="{{ route('articles.index') }}" class="cat-back">{{ __('Kembali ke Beranda') }}</a>
        </div>

      @else

        {{-- Link ke halaman kategori jika cocok --}}
        @if($catSlug)
          @php
            $catConfig = config('categories')[$catSlug] ?? [];
            $catConfName = app()->getLocale() === 'en' ? ($catConfig['name_en'] ?? $catConfig['name']) : ($catConfig['name'] ?? '');
          @endphp
          <a class="search-cat" style="--accent: {{ $catConfig['color'] ?? '#222' }}"
             href="{{ route('categories.show', $catSlug) }}">
            {{ __('Lihat semua berita kategori :name →', ['name' => $catConfName]) }}
          </a>
        @endif

        {{-- Toolbar filter --}}
        <div class="search-toolbar">
          <form method="GET" action="{{ route('search') }}" class="search-filter-form">
            <input type="hidden" name="q" value="{{ $q }}">
            <div class="sf-group">
              <label>{{ __('Kategori') }}:</label>
              <select name="kat" onchange="this.form.submit()">
                <option value="">{{ __('Semua') }}</option>
                @foreach(config('categories') as $s => $c)
                  @php $cName = app()->getLocale() === 'en' ? ($c['name_en'] ?? $c['name']) : $c['name']; @endphp
                  <option value="{{ $s }}" {{ $katFilter === $s ? 'selected' : '' }}>{{ $cName }}</option>
                @endforeach
              </select>
            </div>
            <div class="sf-group">
              <label>{{ __('Urutan') }}:</label>
              <select name="sort" onchange="this.form.submit()">
                <option value="relevan"    {{ $sort === 'relevan'    ? 'selected' : '' }}>{{ __('Paling Relevan') }}</option>
                <option value="terbaru"    {{ $sort === 'terbaru'    ? 'selected' : '' }}>{{ __('Terbaru') }}</option>
                <option value="terpopuler" {{ $sort === 'terpopuler' ? 'selected' : '' }}>{{ __('Terpopuler') }}</option>
              </select>
            </div>
          </form>
        </div>

        @if($total === 0)
          <div class="cat-empty">
            <h2>{{ __('Tidak ada hasil untuk ":q"', ['q' => $q]) }}</h2>
            <p>{{ __('Coba kata kunci lain, atau periksa ejaan kamu.') }}</p>
            <a href="{{ route('articles.index') }}" class="cat-back">{{ __('Kembali ke Beranda') }}</a>
          </div>

          {{-- Rekomendasi saat kosong --}}
          @if($popular->isNotEmpty())
            <div style="margin-top:36px">
              <h3 style="font-size:14px;font-weight:700;text-transform:uppercase;border-bottom:2px solid #222;padding-bottom:6px;margin-bottom:18px">{{ __('Terpopuler') }}</h3>
              @foreach($popular as $i => $pop)
                @php
                  $popThumb = $pop->image_url ?? asset('images/placeholder-news.svg');
                  $popCat = optional($pop->category)->display_name ?? __('Berita');
                @endphp
                <article class="result">
                  <a href="{{ route('articles.show', $pop->slug) }}" class="card-thumb g{{ $i % 5 }}"
                     style="background-image:url('{{ $popThumb }}')"
                     aria-label="{{ $pop->display_title }}">
                    <span class="tag">{{ $popCat }}</span>
                  </a>
                  <div>
                    <h3 class="card-title"><a href="{{ route('articles.show', $pop->slug) }}">{{ $pop->display_title }}</a></h3>
                    <div class="card-meta">
                      <b>{{ optional($pop->user)->name ?? 'Admin' }}</b>
                      <span>–</span><span>{{ $pop->formatted_date }}</span>
                      <span>👁 {{ number_format($pop->views, 0, ',', '.') }}</span>
                    </div>
                    <p class="excerpt">{{ Str::limit($pop->display_excerpt, 160) }}</p>
                  </div>
                </article>
              @endforeach
            </div>
          @endif

        @else

          @foreach($paginator as $i => $a)
            @php
              $resImg = $a['img'] ?: asset('images/placeholder-news.svg');
            @endphp
            <article class="result">
              <a href="{{ $a['url'] }}" class="card-thumb g{{ $i % 5 }}"
                 style="background-image:url('{{ $resImg }}')"
                 aria-label="{{ $a['title'] }}">
                <span class="tag">{{ $a['cat'] }}</span>
              </a>
              <div>
                <h3 class="card-title">
                  <a href="{{ $a['url'] }}">
                    {!! \Illuminate\Support\Str::of($a['title'])->replace($q, '<mark>' . $q . '</mark>') !!}
                  </a>
                </h3>
                <div class="card-meta">
                  <b>{{ $a['author'] }}</b><span>–</span>
                  <span>{{ $a['date'] }}</span>
                  <span>👁 {{ number_format($a['views'], 0, ',', '.') }}</span>
                  <span class="badge-count">{{ $a['comments'] }}</span>
                </div>
                <p class="excerpt">
                  {!! \Illuminate\Support\Str::of($a['excerpt'])->replace($q, '<mark>' . $q . '</mark>') !!}
                </p>
              </div>
            </article>
          @endforeach

          @if($paginator->lastPage() > 1)
            <nav class="pager" aria-label="Halaman">
              @if($paginator->previousPageUrl())
                <a href="{{ $paginator->previousPageUrl() }}">‹ {{ __('Sebelumnya') }}</a>
              @else <span class="off">‹ {{ __('Sebelumnya') }}</span>
              @endif
              <span class="pager-info">{{ __('Halaman :current dari :total', ['current' => $paginator->currentPage(), 'total' => $paginator->lastPage()]) }}</span>
              @if($paginator->nextPageUrl())
                <a href="{{ $paginator->nextPageUrl() }}">{{ __('Berikutnya') }} ›</a>
              @else <span class="off">{{ __('Berikutnya') }} ›</span>
              @endif
            </nav>
          @endif
        @endif
      @endif
    </div>

    {{-- ===== Sidebar ===== --}}
    <aside>
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">{{ __('Kategori') }}</span></div>
        <ul class="cat-list">
          @foreach(config('categories') as $s => $c)
            @php $cName = app()->getLocale() === 'en' ? ($c['name_en'] ?? $c['name']) : $c['name']; @endphp
            <li>
              <a href="{{ route('categories.show', $s) }}"
                 class="{{ $katFilter === $s ? 'active-cat' : '' }}">
                <i style="background: {{ $c['color'] }}"></i>{{ $cName }}
              </a>
            </li>
          @endforeach
        </ul>
      </section>
      <div class="side-block"><div class="ad-box">{{ __('Ruang Iklan') }}<br>300 × 250</div></div>
    </aside>
  </div>
</div>
@endsection
