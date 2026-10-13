@extends('layouts.app')

@section('title', 'News X Paper – the art of publishing')
@section('description', 'Kabar terkini seputar politik, ekonomi, olahraga, teknologi, dan opini dari News X Paper.')

@section('content')
@php
  // Helper: normalkan URL gambar artikel
  $imgUrl = function(?string $img): ?string {
    if (!$img || trim($img) === '') return null;
    if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) return $img;
    return asset('storage/' . $img);
  };

  $placeholder = asset('images/placeholder-news.svg');

  $str = fn($v) => is_string($v) && $v !== '' ? $v : null;

  // $articles adalah LengthAwarePaginator dari controller
  $items = collect($articles->items())->values()->map(function ($a) use ($str, $imgUrl) {
    $cat     = $str(data_get($a, 'category.name')) ?? 'Berita';
    $catSlug = $str(data_get($a, 'category.slug')) ?? '';
    $catColor= config('categories.' . $catSlug . '.color', '#222');
    $body    = $str(data_get($a, 'content')) ?? '';
    $slug    = data_get($a, 'slug');
    return [
      'title'    => $str(data_get($a, 'title')) ?? 'Tanpa judul',
      'url'      => $slug ? route('articles.show', $slug) : '#',
      'cat'      => $cat,
      'catSlug'  => $catSlug,
      'catColor' => $catColor,
      'author'   => $str(data_get($a, 'user.name')) ?? 'Admin',
      'img'      => $imgUrl(data_get($a, 'image')),
      'date'     => \Illuminate\Support\Carbon::parse(data_get($a, 'created_at') ?? now())->locale('id')->translatedFormat('j M Y'),
      'comments' => (int) data_get($a, 'comments_count', 0),
      'views'    => (int) data_get($a, 'views', 0),
      'excerpt'  => \Illuminate\Support\Str::limit(strip_tags($body), 160),
    ];
  });

  // Fallback jika tidak ada artikel sama sekali
  if ($items->isEmpty()) {
    $items = collect([
      ['title'=>'Harga Bahan Pokok Stabil','cat'=>'Ekonomi','catSlug'=>'ekonomi'],
      ['title'=>'Startup Lokal Raih Pendanaan','cat'=>'Teknologi','catSlug'=>'teknologi'],
      ['title'=>'Pemerintah Umumkan Kebijakan','cat'=>'Politik','catSlug'=>'politik'],
      ['title'=>'Perkembangan Kecerdasan Buatan','cat'=>'Teknologi','catSlug'=>'teknologi'],
      ['title'=>'Tim Nasional Lolos Kualifikasi','cat'=>'Olahraga','catSlug'=>'olahraga'],
      ['title'=>'Opini: Membaca Berita Kritis','cat'=>'Opini','catSlug'=>'opini'],
    ])->map(fn($s) => $s + [
      'url'=>'#','author'=>'Admin','img'=>null,'comments'=>0,'views'=>0,
      'catColor'=>'#222',
      'date'=>now()->locale('id')->translatedFormat('j M Y'),
      'excerpt'=>'Ringkasan berita akan tampil setelah artikel pertama dipublikasikan.',
    ]);
  }

  $n    = $items->count();
  $pick = fn($i) => $items[$i % max($n, 1)];

  $featured     = $pick(0);
  $popItems     = isset($artikelPopuler) ? $artikelPopuler : collect();
  $trendingList = isset($trending) ? $trending : collect();

  // Filter jangan lewatkan
  $trenItems = $items->filter(fn($x) => in_array($x['catSlug'], ['politik','ekonomi']))->values();
  if ($trenItems->count() < 4) $trenItems = $items;
  $tN = max($trenItems->count(), 1);
  $tp = fn($i) => $trenItems[$i % $tN];

  // Filter ekonomi
  $ekoItems = $items->filter(fn($x) => $x['catSlug'] === 'ekonomi')->values();
  if ($ekoItems->count() < 2) $ekoItems = $items;
  $eN = max($ekoItems->count(), 1);
  $ek = fn($i) => $ekoItems[$i % $eN];
@endphp

<div class="container">

  {{-- ===== FEATURED ARTICLE ===== --}}
  <section class="featured-article" aria-label="Artikel utama">
    <div class="fa-img-wrap">
      <img src="{{ $featured['img'] ?? $placeholder }}"
           alt="{{ $featured['title'] }}"
           class="fa-img"
           loading="eager"
           onerror="this.src='{{ $placeholder }}'">
      <div class="fa-img-overlay" aria-hidden="true"></div>
      <a href="{{ $featured['catSlug'] ? route('categories.show', $featured['catSlug']) : '#' }}"
         class="fa-cat-badge" style="background: {{ $featured['catColor'] }}">
        {{ $featured['cat'] }}
      </a>
    </div>
    <div class="fa-info">
      <h1 class="fa-title"><a href="{{ $featured['url'] }}">{{ $featured['title'] }}</a></h1>
      @if($featured['excerpt'])
        <p class="fa-excerpt">{{ $featured['excerpt'] }}</p>
      @endif
      <div class="fa-meta">
        <span class="fa-author">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-8 8-8s8 4 8 8"/></svg>
          {{ $featured['author'] }}
        </span>
        <span class="fa-date">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          {{ $featured['date'] }}
        </span>
        @if($featured['views'] > 0)
          <span class="fa-views">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ number_format($featured['views'], 0, ',', '.') }} dilihat
          </span>
        @endif
      </div>
      <a href="{{ $featured['url'] }}" class="fa-read-btn">
        Baca Selengkapnya
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
  </section>

  {{-- ===== TRENDING TICKER ===== --}}
  <div class="trending" aria-label="Berita trending">
    <span class="trending-label">Sedang Tren</span>
    <div class="trending-ticker" id="trending-ticker">
      @if($trendingList->isNotEmpty())
        @foreach($trendingList as $t)
          <a href="{{ route('articles.show', $t->slug) }}" class="ticker-item">{{ $t->title }}</a>
        @endforeach
      @else
        <span class="ticker-item">{{ $pick(0)['title'] }}</span>
      @endif
    </div>
    <div class="arrows" aria-hidden="true">
      <button class="arrow" id="tick-prev" aria-label="Sebelumnya">‹</button>
      <button class="arrow" id="tick-next" aria-label="Berikutnya">›</button>
    </div>
  </div>

  {{-- ===== HERO GRID ===== --}}
  <section class="hero" aria-label="Berita utama">
    @foreach([0, 1, 2, 3] as $k)
      @php $a = $pick($k); $bg = $a['img'] ?? $placeholder; @endphp
      <a href="{{ $a['url'] }}" class="hero-item hero-{{ $k }} g{{ $k % 5 }}"
         style="background-image: url('{{ $bg }}')"
         aria-label="{{ $a['title'] }}">
        <img src="{{ $bg }}" alt="" loading="{{ $k === 0 ? 'eager' : 'lazy' }}"
             style="position:absolute;width:0;height:0;opacity:0;pointer-events:none"
             onerror="this.closest('.hero-item').style.backgroundImage='url({{ $placeholder }})'">
        <div class="hero-body">
          <span class="tag" style="{{ $a['catColor'] !== '#222' ? 'background:' . $a['catColor'] : '' }}">{{ $a['cat'] }}</span>
          <h2 class="hero-title">{{ $a['title'] }}</h2>
          @if($k === 0)
            <div class="hero-meta"><b>{{ $a['author'] }}</b> – {{ $a['date'] }}</div>
          @endif
        </div>
      </a>
    @endforeach
  </section>

  <div class="layout">
    {{-- ===== KOLOM UTAMA ===== --}}
    <div class="main-col">

      {{-- Jangan Lewatkan --}}
      <section class="block b-yellow">
        <div class="block-head">
          <span class="block-title">Jangan Lewatkan</span>
          <div class="block-tabs" id="tabs-jl">
            <span class="tab on" data-tab="jl-semua">Semua</span>
            <span class="tab" data-tab="jl-politik">Politik</span>
            <span class="tab" data-tab="jl-ekonomi">Ekonomi</span>
          </div>
        </div>

        @php $big = $tp(0); $bigBg = $big['img'] ?? $placeholder; @endphp

        <div id="jl-semua" class="tab-pane">
          <div class="dm">
            <article>
              <a href="{{ $big['url'] }}" class="card-thumb g4"
                 style="background-image: url('{{ $bigBg }}')"
                 aria-label="{{ $big['title'] }}">
                <span class="tag">{{ $big['cat'] }}</span>
              </a>
              <div>
                <h3 class="card-title"><a href="{{ $big['url'] }}">{{ $big['title'] }}</a></h3>
                <div class="card-meta">
                  <b>{{ $big['author'] }}</b><span>–</span><span>{{ $big['date'] }}</span>
                  <span class="badge-count">{{ $big['comments'] }}</span>
                </div>
                <p class="excerpt">{{ $big['excerpt'] }}</p>
              </div>
            </article>
            <div class="list">
              @foreach([1, 2, 3, 4] as $i)
                @php $a = $tp($i); @endphp
                <a href="{{ $a['url'] }}" class="list-item">
                  <span class="list-thumb g{{ $i % 5 }}"
                        style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
                  <div>
                    <h4 class="list-title">{{ $a['title'] }}</h4>
                    <span class="card-meta">{{ $a['date'] }}</span>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Tab Politik --}}
        @php
          $polItems = $items->filter(fn($x) => $x['catSlug'] === 'politik')->values();
          if ($polItems->isEmpty()) $polItems = $items;
          $pN = max($polItems->count(), 1);
          $pp = fn($i) => $polItems[$i % $pN];
        @endphp
        <div id="jl-politik" class="tab-pane" style="display:none">
          <div class="list">
            @for($i = 0; $i < min(5, $pN); $i++)
              @php $a = $pp($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i % 5 }}"
                      style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
                <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
              </a>
            @endfor
          </div>
        </div>

        {{-- Tab Ekonomi --}}
        <div id="jl-ekonomi" class="tab-pane" style="display:none">
          <div class="list">
            @for($i = 0; $i < min(5, $eN); $i++)
              @php $a = $ek($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i % 5 }}"
                      style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
                <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
              </a>
            @endfor
          </div>
        </div>
      </section>

      {{-- Ekonomi --}}
      <section class="block b-green">
        <div class="block-head">
          <span class="block-title">Ekonomi</span>
          <div class="block-tabs" id="tabs-ek">
            <span class="tab on" data-tab="ek-semua">Semua</span>
            <span class="tab" data-tab="ek-bisnis">Bisnis</span>
            <span class="tab" data-tab="ek-pasar">Pasar</span>
          </div>
        </div>
        <div id="ek-semua" class="tab-pane">
          <div class="split">
            @foreach([0, 1] as $i)
              @php $a = $ek($i); @endphp
              <article>
                <a href="{{ $a['url'] }}" class="card-thumb g{{ ($i+1)%5 }}"
                   style="background-image: url('{{ $a['img'] ?? $placeholder }}')">
                  <span class="tag">{{ $a['cat'] }}</span>
                </a>
                <h3 class="card-title"><a href="{{ $a['url'] }}">{{ $a['title'] }}</a></h3>
                <div class="card-meta"><b>{{ $a['author'] }}</b><span>–</span><span>{{ $a['date'] }}</span><span class="badge-count">{{ $a['comments'] }}</span></div>
                <p class="excerpt">{{ $a['excerpt'] }}</p>
              </article>
            @endforeach
          </div>
          <div class="list-2">
            @for($i = 2; $i < min(6, $eN); $i++)
              @php $a = $ek($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i%5 }}" style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
                <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
              </a>
            @endfor
          </div>
        </div>
        <div id="ek-bisnis" class="tab-pane" style="display:none">
          @php $a = $ek(0); @endphp
          <div class="list">
            <a href="{{ $a['url'] }}" class="list-item">
              <span class="list-thumb g1" style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
              <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
            </a>
          </div>
        </div>
        <div id="ek-pasar" class="tab-pane" style="display:none">
          @php $a = $ek(1 % $eN); @endphp
          <div class="list">
            <a href="{{ $a['url'] }}" class="list-item">
              <span class="list-thumb g3" style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
              <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
            </a>
          </div>
        </div>
      </section>

      {{-- ===== PILIHAN EDITOR ===== --}}
      @if(isset($pilihanEditor) && $pilihanEditor->isNotEmpty())
      <section class="block b-dark" style="margin-top:8px">
        <div class="block-head">
          <span class="block-title">Pilihan Editor</span>
          <a href="{{ route('articles.index') }}" style="font-size:12px;color:var(--muted)">Lihat semua</a>
        </div>
        <div class="editor-grid">
          @foreach($pilihanEditor->take(6) as $ep)
            @php
              $epImg = $ep->image
                ? (\Illuminate\Support\Str::startsWith($ep->image, ['http://', 'https://']) ? $ep->image : asset('storage/' . $ep->image))
                : $placeholder;
              $epColor = config('categories.' . optional($ep->category)->slug . '.color', '#222');
            @endphp
            <article class="eg-card">
              <a href="{{ route('articles.show', $ep->slug) }}" class="eg-img"
                 style="background-image: url('{{ $epImg }}')"
                 aria-label="{{ $ep->title }}">
                <img src="{{ $epImg }}" alt="" loading="lazy"
                     style="position:absolute;width:0;height:0;opacity:0"
                     onerror="this.closest('.eg-img').style.backgroundImage='url({{ $placeholder }})'">
                <span class="tag" style="{{ $epColor !== '#222' ? 'background:' . $epColor : '' }}">
                  {{ optional($ep->category)->name ?? 'Berita' }}
                </span>
              </a>
              <div class="eg-body">
                <h4 class="eg-title">
                  <a href="{{ route('articles.show', $ep->slug) }}">{{ $ep->title }}</a>
                </h4>
                <div class="eg-meta">
                  <span>{{ $ep->created_at->locale('id')->translatedFormat('j M Y') }}</span>
                  <span>👁 {{ number_format($ep->views, 0, ',', '.') }}</span>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      </section>
      @endif

      {{-- ===== BERITA PER KATEGORI (grid 2 kolom, semua 12 kategori) ===== --}}
      @if(isset($beritaPerKategori) && $beritaPerKategori->isNotEmpty())
        {{-- Group pertama: 2 kategori besar (satu berita besar + 3 kecil) --}}
        @foreach($beritaPerKategori->take(2) as $katGroup)
          @if($katGroup['items']->isNotEmpty())
          <section class="block" style="--accent: {{ $katGroup['color'] }}">
            <div class="block-head" style="border-bottom: 2px solid {{ $katGroup['color'] }}">
              <span class="block-title" style="background: {{ $katGroup['color'] }}">{{ $katGroup['name'] }}</span>
              <a href="{{ route('categories.show', $katGroup['slug']) }}"
                 style="font-size:12px;color:{{ $katGroup['color'] }};font-weight:700">
                Selengkapnya →
              </a>
            </div>
            <div class="kat-row">
              @php $katBig = $katGroup['items']->first(); $katBigImg = $katBig->image ? (\Illuminate\Support\Str::startsWith($katBig->image, ['http://', 'https://']) ? $katBig->image : asset('storage/' . $katBig->image)) : $placeholder; @endphp
              <article class="kat-big">
                <a href="{{ route('articles.show', $katBig->slug) }}" class="card-thumb g0"
                   style="background-image: url('{{ $katBigImg }}')" aria-label="{{ $katBig->title }}">
                  <span class="tag" style="background: {{ $katGroup['color'] }}">{{ $katGroup['name'] }}</span>
                </a>
                <h3 class="card-title" style="font-size:17px">
                  <a href="{{ route('articles.show', $katBig->slug) }}">{{ $katBig->title }}</a>
                </h3>
                <div class="card-meta">
                  <b>{{ optional($katBig->user)->name ?? 'Admin' }}</b>
                  <span>–</span><span>{{ $katBig->created_at->translatedFormat('j M Y') }}</span>
                  <span>👁 {{ number_format($katBig->views, 0, ',', '.') }}</span>
                </div>
                <p class="excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($katBig->content), 120) }}</p>
              </article>
              <div class="kat-list">
                @foreach($katGroup['items']->skip(1) as $katItem)
                  @php $katImg = $katItem->image ? (\Illuminate\Support\Str::startsWith($katItem->image, ['http://', 'https://']) ? $katItem->image : asset('storage/' . $katItem->image)) : $placeholder; @endphp
                  <a href="{{ route('articles.show', $katItem->slug) }}" class="list-item">
                    <span class="list-thumb g{{ $loop->index % 5 }}" style="background-image: url('{{ $katImg }}')"></span>
                    <div>
                      <h4 class="list-title">{{ \Illuminate\Support\Str::limit($katItem->title, 65) }}</h4>
                      <span class="card-meta">{{ $katItem->created_at->translatedFormat('j M Y') }} · 👁 {{ number_format($katItem->views, 0, ',', '.') }}</span>
                    </div>
                  </a>
                @endforeach
              </div>
            </div>
          </section>
          @endif
        @endforeach

        {{-- Sisa kategori: tampil dalam grid 2 kolom mini agar tidak terlalu panjang --}}
        @php $sisaKat = $beritaPerKategori->skip(2)->values(); @endphp
        @if($sisaKat->isNotEmpty())
        <div class="kat-section-grid">
          @foreach($sisaKat as $katGroup)
            @if($katGroup['items']->isNotEmpty())
            <div class="kat-section-block">
              <div class="block-head" style="border-bottom: 2px solid {{ $katGroup['color'] }}; margin-bottom:12px">
                <span class="block-title" style="background: {{ $katGroup['color'] }}; font-size:11px">{{ $katGroup['name'] }}</span>
                <a href="{{ route('categories.show', $katGroup['slug']) }}" style="font-size:11px;color:{{ $katGroup['color'] }};font-weight:700">Selengkapnya →</a>
              </div>
              <div class="kat-mini-list">
                @foreach($katGroup['items']->take(3) as $km)
                  @php $kmImg = $km->image ? (\Illuminate\Support\Str::startsWith($km->image, ['http://', 'https://']) ? $km->image : asset('storage/' . $km->image)) : $placeholder; @endphp
                  <a href="{{ route('articles.show', $km->slug) }}" class="kat-mini-item">
                    <span class="kat-mini-thumb" style="background-image: url('{{ $kmImg }}')" aria-hidden="true"></span>
                    <div class="kat-mini-info">
                      <span class="kat-mini-title">{{ \Illuminate\Support\Str::limit($km->title, 70) }}</span>
                      <span class="kat-mini-meta">{{ $km->created_at->translatedFormat('j M Y') }}</span>
                    </div>
                  </a>
                @endforeach
              </div>
            </div>
            @endif
          @endforeach
        </div>
        @endif
      @endif

      {{-- ===== PAGINATION ===== --}}
      @if($articles->hasPages())
        <nav class="homepage-pager" aria-label="Navigasi halaman berita">
          {{-- Prev --}}
          @if($articles->onFirstPage())
            <span class="hp-btn hp-btn-off">‹ Sebelumnya</span>
          @else
            <a href="{{ $articles->previousPageUrl() }}" class="hp-btn">‹ Sebelumnya</a>
          @endif

          {{-- Nomor halaman --}}
          <div class="hp-pages">
            @foreach($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
              @if($page == $articles->currentPage())
                <span class="hp-page hp-page-active">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="hp-page">{{ $page }}</a>
              @endif
            @endforeach
          </div>

          {{-- Next --}}
          @if($articles->hasMorePages())
            <a href="{{ $articles->nextPageUrl() }}" class="hp-btn">Berikutnya ›</a>
          @else
            <span class="hp-btn hp-btn-off">Berikutnya ›</span>
          @endif
        </nav>
      @endif

    </div>

    {{-- ===== SIDEBAR ===== --}}
    <aside>
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Tetap Terhubung</span></div>
        <div class="social-list">
          <div class="social-row"><span class="social-ico s-fb">f</span><span>Suka di Facebook</span></div>
          <div class="social-row"><span class="social-ico s-tw">X</span><span>Ikuti di X</span></div>
          <div class="social-row"><span class="social-ico s-yt">▶</span><span>Langganan YouTube</span></div>
        </div>
      </section>

      <div class="side-block"><div class="ad-box">Ruang Iklan<br>300 × 250</div></div>

      <section class="side-block block b-dark" style="margin-top:32px">
        <div class="block-head"><span class="block-title">Terpopuler</span></div>
        <div class="pop-grid">
          @if($popItems->isNotEmpty())
            @foreach($popItems->take(4) as $i => $pop)
              @php
                $popImg = $pop->image
                  ? (\Illuminate\Support\Str::startsWith($pop->image, ['http://', 'https://']) ? $pop->image : asset('storage/' . $pop->image))
                  : $placeholder;
              @endphp
              <a href="{{ route('articles.show', $pop->slug) }}" aria-label="{{ $pop->title }}">
                <span class="pop-thumb g{{ $i % 5 }}" style="background-image: url('{{ $popImg }}')"></span>
                <div class="pop-title">{{ \Illuminate\Support\Str::limit($pop->title, 55) }}</div>
              </a>
            @endforeach
          @else
            @foreach([1, 2, 3, 4] as $i)
              @php $a = $pick($i); @endphp
              <a href="{{ $a['url'] }}">
                <span class="pop-thumb g{{ $i % 5 }}" style="background-image: url('{{ $a['img'] ?? $placeholder }}')"></span>
                <div class="pop-title">{{ $a['title'] }}</div>
              </a>
            @endforeach
          @endif
        </div>
      </section>
    </aside>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
  const ticker = document.getElementById('trending-ticker');
  if (!ticker) return;
  const items = ticker.querySelectorAll('.ticker-item');
  if (!items.length) return;
  let idx = 0;
  const show = i => items.forEach((el, j) => el.style.display = j === i ? 'block' : 'none');
  show(0);
  const timer = setInterval(() => { idx = (idx + 1) % items.length; show(idx); }, 4000);
  document.getElementById('tick-next')?.addEventListener('click', () => { clearInterval(timer); idx = (idx+1)%items.length; show(idx); });
  document.getElementById('tick-prev')?.addEventListener('click', () => { clearInterval(timer); idx = (idx-1+items.length)%items.length; show(idx); });
})();

document.querySelectorAll('.block-tabs').forEach(tabGroup => {
  tabGroup.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      tabGroup.querySelectorAll('.tab').forEach(t => t.classList.remove('on'));
      tab.classList.add('on');
      const paneId = tab.dataset.tab;
      tabGroup.closest('.block').querySelectorAll('.tab-pane').forEach(p => {
        p.style.display = p.id === paneId ? '' : 'none';
      });
    });
  });
});
</script>
@endpush
