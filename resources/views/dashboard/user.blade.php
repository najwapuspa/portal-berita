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
      @forelse($kategori as $k)
        <a href="{{ route('categories.show', $k->slug) }}"
           style="--c: {{ config('categories.' . $k->slug . '.color', '#b8b3ec') }}20;
                  border-left: 3px solid {{ config('categories.' . $k->slug . '.color', '#b8b3ec') }}">
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
      @forelse($artikelPopuler as $a)
        <li>
          <div class="list-meta">
            <span class="tag" style="background: {{ config('categories.' . optional($a->category)->slug . '.color', '#222') }}">
              {{ optional($a->category)->name ?? 'Berita' }}
            </span>
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
      @forelse($artikelTerbaru as $a)
        @php
          $imgSrc = $a->image
            ? (\Illuminate\Support\Str::startsWith($a->image, ['http://', 'https://'])
                ? $a->image
                : asset('storage/' . $a->image))
            : asset('images/placeholder-news.svg');
        @endphp
        <article class="news-card" data-cat="{{ optional($a->category)->slug }}">
          <a href="{{ route('articles.show', $a->slug) }}" class="nc-img"
             style="background-image: url('{{ $imgSrc }}')"
             aria-label="{{ $a->title }}">
            <img src="{{ $imgSrc }}" alt="{{ $a->title }}" loading="lazy"
                 style="display:none"
                 onerror="this.parentElement.style.backgroundImage='url({{ asset('images/placeholder-news.svg') }})'">
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

  {{-- Komentar saya --}}
  <section class="panel wide">
    <div class="panel-head">
      <h2>Komentar Saya</h2>
      <span style="font-size:13px;color:var(--muted)">{{ $jumlahKomentar }} total</span>
    </div>
    @forelse($komentarSaya as $kom)
      <div style="padding:12px 0;border-bottom:1px solid var(--line);display:flex;gap:14px;align-items:flex-start">
        <div style="flex:0 0 auto;width:38px;height:38px;border-radius:50%;background:var(--c1);display:grid;place-items:center;font-weight:700;color:#111;font-size:14px" aria-hidden="true">
          {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-size:12px;color:var(--muted);margin-bottom:4px;display:flex;gap:10px;flex-wrap:wrap;align-items:center">
            @if($kom->article)
              <a href="{{ route('articles.show', $kom->article->slug) }}"
                 style="font-weight:600;color:var(--brand);text-decoration:none">
                {{ \Illuminate\Support\Str::limit($kom->article->title, 50) }}
              </a>
            @else
              <span style="color:var(--muted)">(Berita dihapus)</span>
            @endif
            <span>{{ $kom->created_at->locale('id')->translatedFormat('j M Y, H:i') }}</span>
          </div>
          <p style="margin:0;font-size:14px;color:var(--ink);line-height:1.55">
            {{ \Illuminate\Support\Str::limit($kom->body, 200) }}
          </p>
        </div>
      </div>
    @empty
      <p class="kosong" style="padding:16px 0">
        Kamu belum membuat komentar.
        <a href="{{ route('articles.index') }}" style="color:var(--brand)">Mulai baca berita →</a>
      </p>
    @endforelse
  </section>

  {{-- Ganti nama --}}
  <section class="panel">
    <h2>Ganti Nama</h2>
    @if(session('ok_nama'))<div class="ok">{{ session('ok_nama') }}</div>@endif
    <form method="POST" action="{{ route('profil.nama') }}">
      @csrf @method('PATCH')
      <label for="name">Nama baru</label>
      <input id="name" name="name" type="text"
             value="{{ old('name', $user->name) }}" required autocomplete="name">
      @if($errors->nama->has('name'))
        <p class="err">{{ $errors->nama->first('name') }}</p>
      @endif
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
      <input id="password_lama" name="password_lama" type="password"
             autocomplete="current-password" required>
      @if($errors->password->has('password_lama'))
        <p class="err">{{ $errors->password->first('password_lama') }}</p>
      @endif

      <label for="password">Kata sandi baru</label>
      <input id="password" name="password" type="password"
             autocomplete="new-password" required>
      @if($errors->password->has('password'))
        <p class="err">{{ $errors->password->first('password') }}</p>
      @endif

      <label for="password_confirmation">Ulangi kata sandi baru</label>
      <input id="password_confirmation" name="password_confirmation" type="password"
             autocomplete="new-password" required>
      <button type="submit" class="btn-out">Ganti Kata Sandi</button>
    </form>
  </section>

</div>
@endsection

@push('scripts')
<script>
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
