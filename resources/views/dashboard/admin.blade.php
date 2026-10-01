@extends('layouts.dashboard')
@section('judul', 'Dashboard Admin')
@section('breadcrumb', 'Dashboard')
@section('hero', 'Ringkasan & Statistik')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('konten')
@php
  $f    = fn ($n) => number_format($n, 0, ',', '.');
  $maks = max(1, $kunjungan->max('jumlah'));
@endphp

{{-- ===== STAT CARDS ===== --}}
<section class="stats" aria-label="Ringkasan">
  <div class="stat s1">
    <span>Kunjungan hari ini</span>
    <b>{{ $f($hariIni) }}</b>
    <em>
      @if(is_null($selisih)) Belum ada data kemarin
      @elseif($selisih >= 0) ▲ +{{ str_replace('.', ',', $selisih) }}% dari kemarin
      @else ▼ {{ str_replace('.', ',', $selisih) }}% dari kemarin
      @endif
    </em>
  </div>
  <div class="stat s2">
    <span>Total berita</span>
    <b>{{ $f($total['artikel']) }}</b>
    <em>+{{ $baru['artikel'] }} minggu ini</em>
  </div>
  <div class="stat s3">
    <span>Total pengguna</span>
    <b>{{ $f($total['user']) }}</b>
    <em>+{{ $baru['user'] }} minggu ini</em>
  </div>
  <div class="stat s4">
    <span>Total tayangan</span>
    <b>{{ $f($total['views']) }}</b>
    <em>+{{ $baru['komentar'] }} komentar baru</em>
  </div>
</section>

{{-- ===== GRID PANEL ===== --}}
<div class="grid">

  {{-- Grafik kunjungan 7 hari --}}
  <section class="panel">
    <div class="panel-head">
      <h2>Kunjungan 7 Hari Terakhir</h2>
    </div>
    <div class="cols" aria-hidden="true">
      @foreach ($kunjungan as $h)
        <div class="col {{ $loop->last ? 'today' : '' }}">
          <b>{{ $f($h['jumlah']) }}</b>
          <div class="cb" style="height: {{ $h['jumlah'] / $maks * 130 }}px; min-height: 3px"></div>
          <span>{{ $h['tanggal']->translatedFormat('D') }}</span>
          <span>{{ $h['tanggal']->translatedFormat('j M') }}</span>
        </div>
      @endforeach
    </div>
  </section>

  {{-- Grafik kunjungan 30 hari (Chart.js line) --}}
  <section class="panel">
    <div class="panel-head">
      <h2>Tren 30 Hari</h2>
    </div>
    <canvas id="lineChart" height="160" aria-label="Grafik tren kunjungan 30 hari"></canvas>
  </section>

  {{-- Grafik tayangan per kategori (bar) --}}
  <section class="panel wide">
    <div class="panel-head">
      <h2>Tayangan per Kategori</h2>
    </div>
    <canvas id="barChart" height="90" aria-label="Grafik tayangan per kategori"></canvas>
  </section>

  {{-- Berita terpopuler --}}
  <section class="panel">
    <div class="panel-head">
      <h2>Berita Terpopuler</h2>
      <a href="{{ route('admin.articles.index') }}" class="panel-link">Lihat semua →</a>
    </div>
    <div class="scroll">
      <table>
        <thead><tr><th>Judul</th><th>Kategori</th><th>Dilihat</th></tr></thead>
        <tbody>
          @forelse ($artikelPopuler as $a)
            <tr>
              <td><a href="{{ route('articles.show', $a->slug) }}" target="_blank">{{ Str::limit($a->title, 45) }}</a></td>
              <td><span class="tag">{{ $a->category->name ?? '-' }}</span></td>
              <td>{{ $f($a->views) }}</td>
            </tr>
          @empty
            <tr><td colspan="3" class="kosong">Belum ada berita.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  {{-- Berita terbaru --}}
  <section class="panel">
    <div class="panel-head">
      <h2>Berita Terbaru</h2>
      <a href="{{ route('admin.articles.create') }}" class="btn-sm">+ Tambah</a>
    </div>
    <div class="scroll">
      <table>
        <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th>Tanggal</th></tr></thead>
        <tbody>
          @forelse ($artikelTerbaru as $a)
            <tr>
              <td><a href="{{ route('admin.articles.edit', $a) }}">{{ Str::limit($a->title, 40) }}</a></td>
              <td><span class="tag">{{ $a->category->name ?? '-' }}</span></td>
              <td>
                <span class="badge-status {{ $a->status === 'published' ? 'pub' : 'dft' }}">
                  {{ $a->status === 'published' ? 'Tayang' : 'Draft' }}
                </span>
              </td>
              <td>{{ $a->created_at->translatedFormat('j M Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="kosong">Belum ada berita.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  {{-- Pengguna terbaru --}}
  <section class="panel wide">
    <div class="panel-head">
      <h2>Pengguna Terbaru</h2>
      <a href="{{ route('admin.users.index') }}" class="panel-link">Lihat semua →</a>
    </div>
    <div class="scroll">
      <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Bergabung</th><th>Aksi</th></tr></thead>
        <tbody>
          @foreach ($userTerbaru as $u)
            <tr>
              <td>{{ $u->name }}</td>
              <td>{{ $u->email }}</td>
              <td><span class="badge-role {{ $u->role === 'admin' ? 'r-admin' : 'r-user' }}">{{ $u->role }}</span></td>
              <td>{{ $u->created_at->translatedFormat('j M Y') }}</td>
              <td class="act"><a href="{{ route('admin.users.edit', $u) }}">Edit</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script>
const palette = ['#b8b3ec','#a7d4ef','#f3b3d6','#a6e3c6','#f7c88a'];

// Line chart – 30 hari
const kunjungan30 = @json($kunjungan30);
new Chart(document.getElementById('lineChart'), {
  type: 'line',
  data: {
    labels: kunjungan30.map(d => d.tanggal.slice(5)),
    datasets: [{
      label: 'Kunjungan',
      data: kunjungan30.map(d => d.jumlah),
      borderColor: '#b8b3ec',
      backgroundColor: 'rgba(184,179,236,.15)',
      borderWidth: 2,
      pointRadius: 2,
      fill: true,
      tension: 0.3,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      x: { ticks: { maxTicksLimit: 8, font: { size: 11 } } },
      y: { beginAtZero: true, ticks: { font: { size: 11 } } }
    }
  }
});

// Bar chart – views per kategori
const viewsPerKat = @json($viewsPerKat);
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels: viewsPerKat.map(d => d.name),
    datasets: [{
      label: 'Tayangan',
      data: viewsPerKat.map(d => d.views),
      backgroundColor: palette,
      borderRadius: 4,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { font: { size: 11 } } }
    }
  }
});
</script>
@endpush
