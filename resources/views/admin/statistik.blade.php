@extends('layouts.dashboard')
@section('judul', 'Statistik Pengunjung')
@section('breadcrumb', 'Admin › Statistik')
@section('hero', 'Statistik & Analitik')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('konten')
@php $f = fn($n) => number_format($n, 0, ',', '.'); @endphp

{{-- Stat cards --}}
<section class="stats" aria-label="Ringkasan statistik">
  <div class="stat s1">
    <span>Kunjungan hari ini</span>
    <b>{{ $f($stats['kunjungan_hari']) }}</b>
    <em>Kunjungan unik halaman</em>
  </div>
  <div class="stat s2">
    <span>Kunjungan minggu ini</span>
    <b>{{ $f($stats['kunjungan_minggu']) }}</b>
    @if(!is_null($trenMinggu))
      <em>{{ $trenMinggu >= 0 ? '▲ +' : '▼ ' }}{{ $trenMinggu }}% vs minggu lalu</em>
    @else
      <em>—</em>
    @endif
  </div>
  <div class="stat s3">
    <span>Total tayangan berita</span>
    <b>{{ $f($stats['total_views']) }}</b>
    <em>{{ $f($stats['published']) }} berita tayang</em>
  </div>
  <div class="stat s4">
    <span>Total pengguna</span>
    <b>{{ $f($stats['total_user']) }}</b>
    <em>{{ $f($stats['total_komentar']) }} komentar</em>
  </div>
</section>

<div class="grid">

  {{-- Grafik kunjungan 30 hari --}}
  <section class="panel wide">
    <h2>Kunjungan 30 Hari Terakhir</h2>
    <canvas id="lineChart30" height="80" aria-label="Grafik kunjungan 30 hari"></canvas>
  </section>

  {{-- Views per kategori --}}
  <section class="panel">
    <h2>Tayangan per Kategori</h2>
    <canvas id="donutKat" height="200" aria-label="Donut tayangan per kategori"></canvas>
    <div class="kat-legend">
      @foreach($viewsPerKat as $i => $k)
        <div class="kat-leg-item">
          <span class="kat-dot" style="background: var(--c{{ ($i % 4) + 1 }})"></span>
          <span>{{ $k->name }}</span>
          <b>{{ $f($k->articles_sum_views ?? 0) }}</b>
        </div>
      @endforeach
    </div>
  </section>

  {{-- Berita terpopuler --}}
  <section class="panel">
    <h2>Berita Terpopuler (Top 10)</h2>
    <div class="scroll">
      <table>
        <thead><tr><th>Judul</th><th>Kategori</th><th>Tayangan</th></tr></thead>
        <tbody>
          @foreach($artikelPopuler as $a)
            <tr>
              <td><a href="{{ route('articles.show', $a->slug) }}" target="_blank">{{ Str::limit($a->title, 40) }}</a></td>
              <td><span class="tag">{{ optional($a->category)->name ?? '-' }}</span></td>
              <td>{{ $f($a->views) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </section>

  {{-- Kata kunci pencarian populer --}}
  <section class="panel">
    <h2>Kata Kunci Populer</h2>
    <div class="bars">
      @foreach($pencarianPopuler as $pk)
        <div class="bar">
          <span style="truncate;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $pk['kata'] }}</span>
          <div class="track">
            <div class="fill" style="width: {{ $pencarianPopuler->max('jumlah') > 0 ? ($pk['jumlah'] / $pencarianPopuler->max('jumlah') * 100) : 0 }}%; background:var(--c1)"></div>
          </div>
          <i>{{ $f($pk['jumlah']) }}</i>
        </div>
      @endforeach
    </div>
  </section>

</div>
@endsection

@push('scripts')
<script>
const palette = ['#b8b3ec','#a7d4ef','#f3b3d6','#a6e3c6','#f7c88a'];

// Line 30 hari
const data30 = @json($kunjungan);
new Chart(document.getElementById('lineChart30'), {
  type: 'line',
  data: {
    labels: data30.map(d => d.label),
    datasets:[{
      label: 'Kunjungan',
      data: data30.map(d => d.jumlah),
      borderColor: '#a7d4ef',
      backgroundColor: 'rgba(167,212,239,.15)',
      borderWidth: 2,
      pointRadius: 1.5,
      fill: true,
      tension: 0.35,
    }]
  },
  options:{
    responsive:true,
    plugins:{legend:{display:false}},
    scales:{
      x:{ticks:{maxTicksLimit:10,font:{size:11}}},
      y:{beginAtZero:true,ticks:{font:{size:11}}}
    }
  }
});

// Donut kategori
const katData = @json($viewsPerKat);
new Chart(document.getElementById('donutKat'),{
  type:'doughnut',
  data:{
    labels: katData.map(d => d.name),
    datasets:[{
      data: katData.map(d => d.articles_sum_views || 0),
      backgroundColor: palette,
      borderWidth:2,
    }]
  },
  options:{
    responsive:true,
    plugins:{legend:{display:false}}
  }
});
</script>
@endpush
