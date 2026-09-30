@extends('layouts.dashboard')
@section('judul', 'Dashboard Admin')
@section('hero', 'Dashboard pengelolaan')

@section('menu')
    {{-- Ganti '#' dengan route halaman kelola milikmu --}}
    <a class="on" href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a href="#">Berita</a>
    <a href="#">Pengguna</a>
    <a href="#">Komentar</a>
    <a href="#">Kategori</a>
@endsection

@section('konten')
@php
    $f = fn ($n) => number_format($n, 0, ',', '.');
    $maks = max(1, $kunjungan->max('jumlah'));
    $maksKat = max(1, $kategori->max('articles_count'));
@endphp

<section class="stats" aria-label="Ringkasan">
    <div class="stat s1">
        <span>Kunjungan hari ini</span>
        <b>{{ $f($hariIni) }}</b>
        <em>
            @if (is_null($selisih)) Belum ada data kemarin
            @else {{ $selisih >= 0 ? '+' : '' }}{{ str_replace('.', ',', $selisih) }}% dari kemarin @endif
        </em>
    </div>
    <div class="stat s2"><span>Jumlah berita</span><b>{{ $f($total['artikel']) }}</b><em>+{{ $baru['artikel'] }} minggu ini</em></div>
    <div class="stat s3"><span>Jumlah pengguna</span><b>{{ $f($total['user']) }}</b><em>+{{ $baru['user'] }} minggu ini</em></div>
    <div class="stat s4"><span>Jumlah komentar</span><b>{{ $f($total['komentar']) }}</b><em>+{{ $baru['komentar'] }} minggu ini</em></div>
</section>

<div class="grid">
    <section class="panel">
        <h2>Kunjungan 7 hari terakhir</h2>
        <div class="cols">
            @foreach ($kunjungan as $h)
                <div class="col {{ $loop->last ? 'today' : '' }}">
                    <b>{{ $f($h['jumlah']) }}</b>
                    <div class="cb" style="height: {{ $h['jumlah'] / $maks * 130 }}px"></div>
                    <span>{{ $h['tanggal']->translatedFormat('D') }}</span>
                    <span>{{ $h['tanggal']->translatedFormat('j M') }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel">
        <h2>Berita terpopuler hari ini</h2>
        <div class="scroll">
            <table>
                <thead><tr><th>Judul</th><th>Dibaca</th></tr></thead>
                <tbody>
                @forelse ($artikelPopuler as $a)
                    <tr>
                        <td><a href="{{ route('articles.show', $a->slug) }}">{{ $a->title }}</a></td>
                        <td>{{ $f($a->views) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="kosong">Belum ada berita.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel wide">
        <h2>Berita per kategori</h2>
        <div class="bars">
            @forelse ($kategori as $k)
                <div class="bar">
                    <span>{{ $k->name }}</span>
                    <div class="track"><div class="fill" style="width: {{ $k->articles_count / $maksKat * 100 }}%; background: var(--c{{ $loop->index % 4 + 1 }})"></div></div>
                    <i>{{ $k->articles_count }}</i>
                </div>
            @empty
                <p class="kosong">Belum ada kategori.</p>
            @endforelse
        </div>
    </section>

    <section class="panel">
        <h2>Berita terbaru</h2>
        <div class="scroll">
            <table>
                <thead><tr><th>Judul</th><th>Kategori</th><th>Tanggal</th></tr></thead>
                <tbody>
                @forelse ($artikelTerbaru as $a)
                    <tr>
                        <td><a href="{{ route('articles.show', $a->slug) }}">{{ $a->title }}</a></td>
                        <td><span class="tag">{{ $a->category->name ?? '-' }}</span></td>
                        <td>{{ $a->created_at->translatedFormat('j M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="kosong">Belum ada berita.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <h2>Pengguna terbaru</h2>
        <div class="scroll">
            <table>
                <thead><tr><th>Nama</th><th>Email</th><th>Bergabung</th><th></th></tr></thead>
                <tbody>
                @foreach ($userTerbaru as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->created_at->translatedFormat('j M Y') }}</td>
                        <td class="act"><a href="#">Lihat</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection