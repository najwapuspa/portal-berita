@extends('layouts.dashboard')
@section('judul', 'Dashboard Saya')
@section('hero', 'Halo, ' . auth()->user()->name . '!')

@section('menu')
    <a class="on" href="{{ route('user.dashboard') }}">Dashboard saya</a>
    <a href="{{ url('/') }}">Beranda</a>
@endsection

@section('konten')
<div class="grid">
    {{-- Profil --}}
    <section class="panel">
        <h2>Profil</h2>
        <div class="profil">
            <div class="avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
            <div>
                <strong>{{ $user->name }}</strong>
                <p>{{ $user->email }}</p>
            </div>
        </div>
        <div class="meta">
            <div><b>{{ $user->created_at->translatedFormat('j M Y') }}</b><span>Bergabung</span></div>
            <div><b>{{ $jumlahKomentar }}</b><span>Komentar saya</span></div>
        </div>
    </section>

    {{-- Pintasan kategori --}}
    <section class="panel">
        <h2>Pintasan kategori</h2>
        <div class="chips">
            @forelse ($kategori as $k)
                <a href="{{ route('categories.show', $k->slug) }}">{{ $k->name }}</a>
            @empty
                <p class="kosong">Belum ada kategori.</p>
            @endforelse
        </div>
    </section>

    {{-- Berita terbaru --}}
    <section class="panel wide">
        <h2>Berita terbaru</h2>
        <ul class="list">
            @forelse ($artikelTerbaru as $a)
                <li>
                    <span class="tag">{{ $a->category->name ?? '-' }}</span>
                    <small>{{ $a->created_at->translatedFormat('j M Y') }}</small>
                    <a href="{{ route('articles.show', $a->slug) }}">{{ $a->title }}</a>
                </li>
            @empty
                <li class="kosong">Belum ada berita.</li>
            @endforelse
        </ul>
    </section>

    {{-- Ganti nama --}}
    <section class="panel">
        <h2>Ganti nama</h2>
        @if (session('ok_nama')) <div class="ok">{{ session('ok_nama') }}</div> @endif
        <form method="POST" action="{{ route('profil.nama') }}">
            @csrf @method('PATCH')
            <label for="name">Nama baru</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
            @if ($errors->nama->has('name')) <p class="err">{{ $errors->nama->first('name') }}</p> @endif
            <button type="submit" class="btn-out">Simpan nama</button>
        </form>
    </section>

    {{-- Ganti kata sandi --}}
    <section class="panel">
        <h2>Ganti kata sandi</h2>
        @if (session('ok_password')) <div class="ok">{{ session('ok_password') }}</div> @endif
        <form method="POST" action="{{ route('profil.password') }}">
            @csrf @method('PUT')
            <label for="password_lama">Kata sandi lama</label>
            <input id="password_lama" name="password_lama" type="password" autocomplete="current-password" required>
            @if ($errors->password->has('password_lama')) <p class="err">{{ $errors->password->first('password_lama') }}</p> @endif

            <label for="password">Kata sandi baru</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required>
            @if ($errors->password->has('password')) <p class="err">{{ $errors->password->first('password') }}</p> @endif

            <label for="password_confirmation">Ulangi kata sandi baru</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

            <button type="submit" class="btn-out">Ganti kata sandi</button>
        </form>
    </section>
</div>
@endsection