@extends('layouts.app')

@section('title', ($q !== '' ? 'Cari: ' . $q : 'Cari Berita') . ' – News X Paper')

@section('content')
<div class="container">

  <div class="cat-head" style="--accent: #222">
    <h1 class="cat-title">Hasil Pencarian</h1>
    @if($q !== '')
      <span class="cat-count">{{ $total }} berita untuk "{{ $q }}"</span>
    @endif
  </div>

  <div class="layout">
    {{-- ===== Kolom utama ===== --}}
    <div class="main-col">

      @if($q === '')
        <div class="cat-empty">
          <h2>Ketik kata kunci di kolom pencarian</h2>
          <p>Contoh: politik, ekonomi, olahraga, atau judul berita.</p>
          <a href="{{ route('articles.index') }}" class="cat-back">Kembali ke Beranda</a>
        </div>

      @else

        {{-- Link ke halaman kategori jika cocok --}}
        @if($catSlug)
          <a class="search-cat" style="--accent: {{ config('categories')[$catSlug]['color'] }}"
             href="{{ route('categories.show', $catSlug) }}">
            Lihat semua berita kategori {{ config('categories')[$catSlug]['name'] }} →
          </a>
        @endif

        {{-- Toolbar filter --}}
        <div class="search-toolbar">
          <form method="GET" action="{{ route('search') }}" class="search-filter-form">
            <input type="hidden" name="q" value="{{ $q }}">
            <div class="sf-group">
              <label>Kategori:</label>
              <select name="kat" onchange="this.form.submit()">
                <option value="">Semua</option>
                @foreach(config('categories') as $s => $c)
                  <option value="{{ $s }}" {{ $katFilter === $s ? 'selected' : '' }}>{{ $c['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="sf-group">
              <label>Urutan:</label>
              <select name="sort" onchange="this.form.submit()">
                <option value="relevan"    {{ $sort === 'relevan'    ? 'selected' : '' }}>Paling Relevan</option>
                <option value="terbaru"    {{ $sort === 'terbaru'    ? 'selected' : '' }}>Terbaru</option>
                <option value="terpopuler" {{ $sort === 'terpopuler' ? 'selected' : '' }}>Terpopuler</option>
              </select>
            </div>
          </form>
        </div>

        @if($total === 0)
          <div class="cat-empty">
            <h2>Tidak ada hasil untuk "{{ $q }}"</h2>
            <p>Coba kata kunci lain, atau periksa ejaan kamu.</p>
            <a href="{{ route('articles.index') }}" class="cat-back">Kembali ke Beranda</a>
          </div>

          {{-- Rekomendasi saat kosong --}}
          @if($popular->isNotEmpty())
            <div style="margin-top:36px">
              <h3 style="font-size:14px;font-weight:700;text-transform:uppercase;border-bottom:2px solid #222;padding-bottom:6px;margin-bottom:18px">Berita Terpopuler</h3>
              @foreach($popular as $i => $pop)
                <article class="result">
                  <a href="{{ route('articles.show', $pop->slug) }}" class="card-thumb g{{ $i % 5 }}"
                     @if($pop->image) style="background-image:url('{{ $pop->image }}')" @endif
                     aria-label="{{ $pop->title }}">
                    <span class="tag">{{ optional($pop->category)->name ?? 'Berita' }}</span>
                  </a>
                  <div>
                    <h3 class="card-title"><a href="{{ route('articles.show', $pop->slug) }}">{{ $pop->title }}</a></h3>
                    <div class="card-meta">
                      <b>{{ optional($pop->user)->name ?? 'Admin' }}</b>
                      <span>–</span><span>{{ $pop->created_at->translatedFormat('j M Y') }}</span>
                      <span>👁 {{ number_format($pop->views, 0, ',', '.') }}</span>
                    </div>
                    <p class="excerpt">{{ Str::limit(strip_tags($pop->content), 160) }}</p>
                  </div>
                </article>
              @endforeach
            </div>
          @endif

        @else

          @foreach($paginator as $i => $a)
            <article class="result">
              <a href="{{ $a['url'] }}" class="card-thumb g{{ $i % 5 }}"
                 @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif
                 aria-label="{{ $a['title'] }}">
                @if($a['img'])
                  <img src="{{ $a['img'] }}" alt="{{ $a['title'] }}" loading="lazy"
                       style="display:none" onerror="this.parentElement.style.backgroundImage='none'">
                @endif
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
      @endif
    </div>

    {{-- ===== Sidebar ===== --}}
    <aside>
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Kategori</span></div>
        <ul class="cat-list">
          @foreach(config('categories') as $s => $c)
            <li>
              <a href="{{ route('categories.show', $s) }}"
                 class="{{ $katFilter === $s ? 'active-cat' : '' }}">
                <i style="background: {{ $c['color'] }}"></i>{{ $c['name'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </section>
      <div class="side-block"><div class="ad-box">Ruang Iklan<br>300 × 250</div></div>
    </aside>
  </div>
</div>
@endsection
