@extends('layouts.app')

@section('title', 'News X Paper – the art of publishing')

@section('content')
@php
  $str = fn($v) => is_string($v) && $v !== '' ? $v : null;

  $items = collect($articles)->values()->map(function ($a) use ($str) {
    $cat    = $str(data_get($a, 'category.name')) ?? 'Berita';
    $catSlug= $str(data_get($a, 'category.slug')) ?? '';
    $author = $str(data_get($a, 'user.name')) ?? 'Admin';
    $img    = $str(data_get($a, 'image'));
    if ($img && ! \Illuminate\Support\Str::startsWith($img, ['http://','https://','/' ])) {
      $img = asset('storage/' . $img);
    }
    $slug = data_get($a, 'slug');
    $body = $str(data_get($a, 'content')) ?? '';
    return [
      'title'    => $str(data_get($a, 'title')) ?? 'Tanpa judul',
      'url'      => $slug ? route('articles.show', $slug) : '#',
      'cat'      => $cat,
      'catSlug'  => $catSlug,
      'author'   => $author,
      'img'      => $img,
      'date'     => \Illuminate\Support\Carbon::parse(data_get($a, 'created_at') ?? now())->locale('id')->translatedFormat('j M Y'),
      'comments' => (int) data_get($a, 'comments_count', 0),
      'views'    => (int) data_get($a, 'views', 0),
      'excerpt'  => \Illuminate\Support\Str::limit(strip_tags($body), 130),
    ];
  });

  // Fallback jika kosong
  if ($items->isEmpty()) {
    $items = collect([
      ['title' => 'Harga Bahan Pokok Stabil Menjelang Akhir Bulan', 'cat' => 'Ekonomi'],
      ['title' => 'Startup Lokal Raih Pendanaan Baru', 'cat' => 'Teknologi'],
      ['title' => 'Pemerintah Umumkan Kebijakan Baru Tahun Ini', 'cat' => 'Politik'],
      ['title' => 'Perkembangan Kecerdasan Buatan Semakin Pesat', 'cat' => 'Teknologi'],
      ['title' => 'Tim Nasional Bersiap Hadapi Laga Kualifikasi', 'cat' => 'Olahraga'],
      ['title' => 'Opini: Membaca Berita Tanpa Terjebak Judul', 'cat' => 'Opini'],
    ])->map(fn($s) => $s + [
      'url' => '#', 'author' => 'Admin', 'img' => null, 'comments' => 0, 'views' => 0,
      'catSlug' => '',
      'date' => now()->locale('id')->translatedFormat('j M Y'),
      'excerpt' => 'Ringkasan berita akan tampil setelah artikel pertama dipublikasikan.',
    ]);
  }

  $n    = $items->count();
  $pick = fn($i) => $items[$i % $n];

  // Artikel populer dari variable controller
  $popItems = isset($artikelPopuler) ? $artikelPopuler : collect();

  // Filter jangan lewatkan
  $trenItems = $items->filter(fn($x) => in_array($x['catSlug'], ['politik','ekonomi']))->values();
  if ($trenItems->count() < 4) { $trenItems = $items; }
  $tN = $trenItems->count();
  $tp = fn($i) => $trenItems[$i % $tN];

  // Filter ekonomi
  $ekoItems = $items->filter(fn($x) => $x['catSlug'] === 'ekonomi')->values();
  if ($ekoItems->count() < 2) { $ekoItems = $items; }
  $eN = $ekoItems->count();
  $ek = fn($i) => $ekoItems[$i % $eN];

  // Trending (dari controller atau fallback item)
  $trendingList = isset($trending) ? $trending : collect();
@endphp

<div class="container">

  {{-- Trending ticker --}}
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

  {{-- Hero grid --}}
  <section class="hero" aria-label="Berita utama">
    @foreach([0,1,2,3] as $k)
      @php $a = $pick($k); @endphp
      <a href="{{ $a['url'] }}" class="hero-item hero-{{ $k }} g{{ $k % 5 }}"
         @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif
         aria-label="{{ $a['title'] }}">
        @if($a['img'])
          <img src="{{ $a['img'] }}" alt="{{ $a['title'] }}" loading="{{ $k === 0 ? 'eager' : 'lazy' }}"
               style="display:none" onerror="this.closest('.hero-item').style.backgroundImage='none'">
        @endif
        <div class="hero-body">
          <span class="tag">{{ $a['cat'] }}</span>
          <h2 class="hero-title">{{ $a['title'] }}</h2>
          @if($k === 0)
            <div class="hero-meta"><b>{{ $a['author'] }}</b> – {{ $a['date'] }}</div>
          @endif
        </div>
      </a>
    @endforeach
  </section>

  <div class="layout">
    {{-- ===== Kolom utama ===== --}}
    <div class="main-col">

      {{-- Jangan Lewatkan dengan tab filter --}}
      <section class="block b-yellow">
        <div class="block-head">
          <span class="block-title">Jangan Lewatkan</span>
          <div class="block-tabs" id="tabs-jl">
            <span class="tab on" data-tab="jl-semua">Semua</span>
            <span class="tab" data-tab="jl-politik">Politik</span>
            <span class="tab" data-tab="jl-ekonomi">Ekonomi</span>
          </div>
        </div>

        @php $big = $tp(0); @endphp

        {{-- Tab: Semua --}}
        <div id="jl-semua" class="tab-pane">
          <div class="dm">
            <article>
              <a href="{{ $big['url'] }}" class="card-thumb g4"
                 @if($big['img']) style="background-image:url('{{ $big['img'] }}')" @endif
                 aria-label="{{ $big['title'] }}">
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
              @foreach([1,2,3,4] as $i)
                @php $a = $tp($i); @endphp
                <a href="{{ $a['url'] }}" class="list-item">
                  <span class="list-thumb g{{ $i % 5 }}"
                    @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
                  <div>
                    <h4 class="list-title">{{ $a['title'] }}</h4>
                    <span class="card-meta">{{ $a['date'] }}</span>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Tab: Politik --}}
        @php
          $polItems = $items->filter(fn($x) => $x['catSlug'] === 'politik')->values();
          if ($polItems->isEmpty()) $polItems = $items;
          $pN = $polItems->count(); $pp = fn($i) => $polItems[$i % $pN];
        @endphp
        <div id="jl-politik" class="tab-pane" style="display:none">
          <div class="list">
            @for($i = 0; $i < min(5, $pN); $i++)
              @php $a = $pp($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i % 5 }}"
                  @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
                <div>
                  <h4 class="list-title">{{ $a['title'] }}</h4>
                  <span class="card-meta">{{ $a['date'] }}</span>
                </div>
              </a>
            @endfor
          </div>
        </div>

        {{-- Tab: Ekonomi --}}
        <div id="jl-ekonomi" class="tab-pane" style="display:none">
          <div class="list">
            @for($i = 0; $i < min(5, $eN); $i++)
              @php $a = $ek($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i % 5 }}"
                  @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
                <div>
                  <h4 class="list-title">{{ $a['title'] }}</h4>
                  <span class="card-meta">{{ $a['date'] }}</span>
                </div>
              </a>
            @endfor
          </div>
        </div>
      </section>

      {{-- Ekonomi dengan tab --}}
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
            @foreach([0,1] as $i)
              @php $a = $ek($i); @endphp
              <article>
                <a href="{{ $a['url'] }}" class="card-thumb g{{ ($i+1)%5 }}"
                   @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif>
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
            @for($i = 2; $i < min(6, $eN); $i++)
              @php $a = $ek($i); @endphp
              <a href="{{ $a['url'] }}" class="list-item">
                <span class="list-thumb g{{ $i%5 }}"
                  @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
                <div>
                  <h4 class="list-title">{{ $a['title'] }}</h4>
                  <span class="card-meta">{{ $a['date'] }}</span>
                </div>
              </a>
            @endfor
          </div>
        </div>

        <div id="ek-bisnis" class="tab-pane" style="display:none">
          @php $a = $ek(0); @endphp
          <div class="list">
            <a href="{{ $a['url'] }}" class="list-item">
              <span class="list-thumb g1" @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
              <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
            </a>
          </div>
        </div>

        <div id="ek-pasar" class="tab-pane" style="display:none">
          @php $a = $ek(1 % $eN); @endphp
          <div class="list">
            <a href="{{ $a['url'] }}" class="list-item">
              <span class="list-thumb g3" @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
              <div><h4 class="list-title">{{ $a['title'] }}</h4><span class="card-meta">{{ $a['date'] }}</span></div>
            </a>
          </div>
        </div>
      </section>

    </div>

    {{-- ===== Sidebar ===== --}}
    <aside>
      {{-- Tetap terhubung --}}
      <section class="side-block block b-dark" style="margin-bottom:32px">
        <div class="block-head"><span class="block-title">Tetap Terhubung</span></div>
        <div class="social-list">
          <div class="social-row"><span class="social-ico s-fb">f</span><span>Suka di Facebook</span></div>
          <div class="social-row"><span class="social-ico s-tw">X</span><span>Ikuti di X</span></div>
          <div class="social-row"><span class="social-ico s-yt">▶</span><span>Langganan YouTube</span></div>
        </div>
      </section>

      {{-- Ruang iklan --}}
      <div class="side-block"><div class="ad-box">Ruang Iklan<br>300 × 250</div></div>

      {{-- Terpopuler by views --}}
      <section class="side-block block b-dark" style="margin-top:32px">
        <div class="block-head"><span class="block-title">Terpopuler</span></div>
        <div class="pop-grid">
          @if($popItems->isNotEmpty())
            @foreach($popItems->take(4) as $i => $pop)
              <a href="{{ route('articles.show', $pop->slug) }}" aria-label="{{ $pop->title }}">
                <span class="pop-thumb g{{ $i % 5 }}"
                  @if($pop->image) style="background-image:url('{{ $pop->image }}')" @endif></span>
                <div class="pop-title">{{ Str::limit($pop->title, 55) }}</div>
              </a>
            @endforeach
          @else
            @foreach([1,2,3,4] as $i)
              @php $a = $pick($i); @endphp
              <a href="{{ $a['url'] }}">
                <span class="pop-thumb g{{ $i % 5 }}"
                  @if($a['img']) style="background-image:url('{{ $a['img'] }}')" @endif></span>
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
// ── Trending ticker ──────────────────────────────────────────────
(function() {
  const ticker = document.getElementById('trending-ticker');
  if (!ticker) return;
  const items  = ticker.querySelectorAll('.ticker-item');
  if (items.length === 0) return;
  let idx = 0;
  function show(i) {
    items.forEach((el, j) => el.style.display = j === i ? 'block' : 'none');
  }
  show(0);
  const timer = setInterval(() => { idx = (idx + 1) % items.length; show(idx); }, 4000);
  document.getElementById('tick-next')?.addEventListener('click', () => {
    clearInterval(timer); idx = (idx + 1) % items.length; show(idx);
  });
  document.getElementById('tick-prev')?.addEventListener('click', () => {
    clearInterval(timer); idx = (idx - 1 + items.length) % items.length; show(idx);
  });
})();

// ── Tab filter ───────────────────────────────────────────────────
document.querySelectorAll('.block-tabs').forEach(tabGroup => {
  tabGroup.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      tabGroup.querySelectorAll('.tab').forEach(t => t.classList.remove('on'));
      tab.classList.add('on');
      const paneId = tab.dataset.tab;
      const block  = tabGroup.closest('.block');
      block.querySelectorAll('.tab-pane').forEach(p => {
        p.style.display = p.id === paneId ? '' : 'none';
      });
    });
  });
});
</script>
@endpush
