@extends('layouts.dashboard')
@section('judul', 'Dashboard Saya')
@section('breadcrumb', 'Dashboard Saya')
@section('hero', 'Halo, ' . auth()->user()->name . '!')

@section('konten')
<div class="grid">

  {{-- Profil --}}
  <section class="panel">
    <h2>Profil Saya</h2>
    <div class="profil">
      <div class="avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
      <div>
        <strong>{{ $user->name }}</strong>
        <p>{{ $user->email }}</p>
        <span class="badge-role r-user">Pembaca</span>
      </div>
    </div>
    <div class="meta">
      <div><b>{{ $user->created_at->translatedFormat('j M Y') }}</b><span>Bergabung</span></div>
      <div><b>{{ $jumlahKomentar }}</b><span>Komentar saya</span></div>
    </div>
  </section>

  {{-- Pintasan kategori --}}
  <section class="panel">
    <h2>Jelajah Kategori</h2>
    <div class="chips">
      @forelse ($kategori as $k)
        <a href="{{ route('categories.show', $k->slug) }}" style="--c: {{ config('categories.' . $k->slug . '.color', '#b8b3ec') }}20; border-left: 3px solid {{ config('categories.' . $k->slug . '.color', '#b8b3ec') }}">
          {{ $k->name }}
        </a>
      @empty
        <p class="kosong">Belum ada kategori.</p>
      @endforelse
    </div>
  </section>

  {{-- Berita terpopuler --}}
  <section class="panel">
    <h2>Berita Terpopuler</h2>
    <ul class="list">
      @forelse ($artikelPopuler as $a)
        <li>
          <div class="list-meta">
            <span class="tag" style="background: {{ config('categories.' . optional($a->category)->slug . '.color', '#222') }}">{{ optional($a->category)->name ?? 'Berita' }}</span>
            <small>{{ number_format($a->views, 0, ',', '.') }} dilihat</small>
          </div>
          <a href="{{ route('articles.show', $a->slug) }}">{{ $a->title }}</a>
        </li>
      @empty
        <li class="kosong">Belum ada berita.</li>
      @endforelse
    </ul>
  </section>

  {{-- Berita terbaru - grid kartu --}}
  <section class="panel wide">
    <div class="panel-head">
      <h2>Berita Terbaru</h2>
      <a href="{{ route('articles.index') }}" class="panel-link">Lihat semua →</a>
    </div>

    {{-- Filter kategori --}}
    <div class="cat-filter" id="cat-filter">
      <button class="cf-btn on" data-cat="semua">Semua</button>
      @foreach($kategori as $k)
        <button class="cf-btn" data-cat="{{ $k->slug }}"
          style="--accent: {{ config('categories.' . $k->slug . '.color', '#888') }}">
          {{ $k->name }}
        </button>
      @endforeach
    </div>

    <div class="news-grid" id="news-grid">
      @forelse ($artikelTerbaru as $a)
        <article class="news-card" data-cat="{{ optional($a->category)->slug }}">
          <a href="{{ route('articles.show', $a->slug) }}" class="nc-img"
            style="{{ $a->image ? 'background-image:url(' . $a->image . ')' : 'background:linear-gradient(135deg,#614385,#516395)' }}"
            aria-label="{{ $a->title }}">
            @if($a->image)
              <img src="{{ $a->image }}" alt="{{ $a->title }}" loading="lazy"
                   onerror="this.parentElement.style.backgroundImage='none'" style="display:none">
            @endif
            <span class="nc-cat" style="background: {{ config('categories.' . optional($a->category)->slug . '.color', '#222') }}">
              {{ optional($a->category)->name ?? 'Berita' }}
            </span>
          </a>
          <div class="nc-body">
            <h3 class="nc-title"><a href="{{ route('articles.show', $a->slug) }}">{{ $a->title }}</a></h3>
            <div class="nc-meta">
              <span>{{ $a->created_at->translatedFormat('j M Y') }}</span>
              <span>👁 {{ number_format($a->views, 0, ',', '.') }}</span>
            </div>
          </div>
        </article>
      @empty
        <p class="kosong" style="grid-column:1/-1">Belum ada berita.</p>
      @endforelse
    </div>
  </section>

  {{-- Ganti nama --}}
  <section class="panel">
    <h2>Ganti Nama</h2>
    @if(session('ok_nama'))<div class="ok">{{ session('ok_nama') }}</div>@endif
    <form method="POST" action="{{ route('profil.nama') }}">
      @csrf @method('PATCH')
      <label for="name">Nama baru</label>
      <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name">
      @if($errors->nama->has('name'))<p class="err">{{ $errors->nama->first('name') }}</p>@endif
      <button type="submit" class="btn-out">Simpan Nama</button>
    </form>
  </section>

  {{-- Ganti kata sandi --}}
  <section class="panel">
    <h2>Ganti Kata Sandi</h2>
    @if(session('ok_password'))<div class="ok">{{ session('ok_password') }}</div>@endif
    <form method="POST" action="{{ route('profil.password') }}">
      @csrf @method('PUT')
      <label for="password_lama">Kata sandi lama</label>
      <input id="password_lama" name="password_lama" type="password" autocomplete="current-password" required>
      @if($errors->password->has('password_lama'))<p class="err">{{ $errors->password->first('password_lama') }}</p>@endif

      <label for="password">Kata sandi baru</label>
      <input id="password" name="password" type="password" autocomplete="new-password" required>
      @if($errors->password->has('password'))<p class="err">{{ $errors->password->first('password') }}</p>@endif

      <label for="password_confirmation">Ulangi kata sandi baru</label>
      <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
      <button type="submit" class="btn-out">Ganti Kata Sandi</button>
    </form>
  </section>

</div>
@endsection

@push('scripts')
<script>
// Filter kategori berita
const btns = document.querySelectorAll('.cf-btn');
btns.forEach(btn => {
  btn.addEventListener('click', () => {
    btns.forEach(b => b.classList.remove('on'));
    btn.classList.add('on');
    const cat = btn.dataset.cat;
    document.querySelectorAll('.news-card').forEach(card => {
      card.style.display = (cat === 'semua' || card.dataset.cat === cat) ? '' : 'none';
    });
  });
});
</script>
@endpush
