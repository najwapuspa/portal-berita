@extends('layouts.dashboard')
@section('judul', 'Laporan Statistik Berita')
@section('breadcrumb', 'Admin → Laporan Statistik')
@section('hero', 'Laporan Statistik Berita')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('konten')
@php
  $f   = fn($n) => number_format($n, 0, ',', '.');
  $req = $request;

  // Warna untuk grafik
  $chartColors = [
    '#b8b3ec','#a7d4ef','#f3b3d6','#a6e3c6','#f7c59f',
    '#c3e6a0','#f4a7a7','#aec6cf','#b5ead7','#ffdac1',
    '#e2b4ff','#bde3ff'
  ];
@endphp

{{-- ===== HEADER LAPORAN ===== --}}
<div class="lap-header" id="laporan-print-area">
  <p class="lap-desc">Halaman ini menyajikan ringkasan dan statistik pengelolaan berita NEWS X PAPER berdasarkan data yang tersimpan dalam database.</p>
  <div class="lap-meta">
    <span>📅 Periode: <strong>{{ $periodeLabel }}</strong></span>
    <span>🕐 Dibuat: {{ $dibuatPada->format('d/m/Y H:i') }} WIB</span>
  </div>
</div>

{{-- ===== FILTER ===== --}}
<section class="panel no-print" style="margin-bottom:20px">
  <h2 style="font-size:15px;margin-bottom:14px;font-weight:700">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:-2px;margin-right:5px"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
    Filter Laporan
  </h2>
  <form method="GET" action="{{ route('admin.laporan.index') }}" class="lap-filter-form">
    <div class="lap-filter-row">
      <div class="lap-filter-group">
        <label>Tanggal Mulai</label>
        <input type="date" name="dari" value="{{ $req->dari ?? '' }}" class="toolbar-input" style="width:160px">
      </div>
      <div class="lap-filter-group">
        <label>Tanggal Akhir</label>
        <input type="date" name="sampai" value="{{ $req->sampai ?? '' }}" class="toolbar-input" style="width:160px">
      </div>
      <div class="lap-filter-group">
        <label>Kategori</label>
        <select name="kategori_id" class="toolbar-select">
          <option value="">Semua Kategori</option>
          @foreach($semuaKategori as $kat)
            <option value="{{ $kat->id }}" {{ ($req->kategori_id == $kat->id) ? 'selected' : '' }}>{{ $kat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="lap-filter-group">
        <label>Status</label>
        <select name="status" class="toolbar-select">
          <option value="">Semua Status</option>
          <option value="published" {{ ($req->status === 'published') ? 'selected' : '' }}>Published</option>
          <option value="draft"     {{ ($req->status === 'draft')     ? 'selected' : '' }}>Draft</option>
        </select>
      </div>
      <div class="lap-filter-actions">
        <button type="submit" class="btn-sm primary">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Terapkan
        </button>
        <a href="{{ route('admin.laporan.index') }}" class="btn-sm">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3"/></svg>
          Reset
        </a>
      </div>
    </div>
  </form>
</section>

{{-- ===== TOMBOL EKSPOR ===== --}}
<div class="lap-export-bar no-print">
  <span class="lap-export-label">Ekspor Laporan:</span>
  <a href="{{ route('admin.laporan.pdf', $req->only(['dari','sampai','kategori_id','status'])) }}"
     class="btn-sm" target="_blank" title="Unduh PDF">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    Ekspor PDF
  </a>
  <a href="{{ route('admin.laporan.excel', $req->only(['dari','sampai','kategori_id','status'])) }}"
     class="btn-sm" title="Unduh Excel">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
    Ekspor Excel
  </a>
  <button onclick="window.print()" class="btn-sm" title="Cetak halaman ini">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Cetak Laporan
  </button>
</div>

{{-- ===== KARTU STATISTIK ===== --}}
<section class="stats lap-stats" aria-label="Ringkasan Statistik">
  <div class="stat s1">
    <span>Total Artikel</span>
    <b>{{ $f($totalArtikel) }}</b>
    <em>Semua status</em>
  </div>
  <div class="stat s2">
    <span>Diterbitkan</span>
    <b>{{ $f($totalPublished) }}</b>
    <em>Status published</em>
  </div>
  <div class="stat s3">
    <span>Draft</span>
    <b>{{ $f($totalDraft) }}</b>
    <em>Belum diterbitkan</em>
  </div>
  <div class="stat s4">
    <span>Total Kategori</span>
    <b>{{ $f($totalKategori) }}</b>
    <em>Kategori aktif</em>
  </div>
  <div class="stat s1">
    <span>Total Views</span>
    <b>{{ $f($totalViews) }}</b>
    <em>Jumlah pembacaan</em>
  </div>
  <div class="stat s2">
    <span>Total Penulis</span>
    <b>{{ $f($totalPenulis) }}</b>
    <em>Penulis aktif</em>
  </div>
</section>

{{-- ===== GRAFIK ===== --}}
<div class="grid" style="margin-top:20px">

  {{-- Grafik distribusi status --}}
  <section class="panel">
    <div class="panel-head">
      <h2>Distribusi Status Publikasi</h2>
    </div>
    <div style="position:relative;height:220px;display:flex;align-items:center;justify-content:center">
      <canvas id="chartStatus" aria-label="Grafik distribusi status" role="img"></canvas>
    </div>
    <div style="margin-top:12px;display:flex;gap:16px;justify-content:center;font-size:12px">
      <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:12px;background:#a7d4ef;display:inline-block;border-radius:2px"></span>Published ({{ $f($totalPublished) }})</span>
      <span style="display:flex;align-items:center;gap:5px"><span style="width:12px;height:12px;background:#f3b3d6;display:inline-block;border-radius:2px"></span>Draft ({{ $f($totalDraft) }})</span>
    </div>
  </section>

  {{-- Grafik distribusi kategori --}}
  <section class="panel">
    <div class="panel-head">
      <h2>Distribusi Artikel per Kategori</h2>
    </div>
    <div style="position:relative;height:220px">
      <canvas id="chartKategori" aria-label="Grafik distribusi kategori" role="img"></canvas>
    </div>
  </section>

</div>

{{-- ===== TABEL STATISTIK KATEGORI ===== --}}
<section class="panel" style="margin-top:0">
  <div class="panel-head">
    <h2>Statistik Berita per Kategori</h2>
    <span class="panel-link">{{ $statsKategori->count() }} kategori</span>
  </div>
  <div class="scroll">
    <table>
      <thead>
        <tr>
          <th style="width:40px">No.</th>
          <th>Nama Kategori</th>
          <th style="text-align:right">Jumlah Artikel</th>
          <th style="text-align:right">Diterbitkan</th>
          <th style="text-align:right">Draft</th>
          <th style="width:160px">Proporsi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($statsKategori as $i => $kat)
          @php $pct = $totalArtikel > 0 ? round(($kat->total_artikel / $totalArtikel) * 100, 1) : 0; @endphp
          <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $kat->name }}</strong></td>
            <td style="text-align:right;font-weight:700">{{ $f($kat->total_artikel ?? 0) }}</td>
            <td style="text-align:right"><span class="badge-status pub">{{ $f($kat->total_published ?? 0) }}</span></td>
            <td style="text-align:right"><span class="badge-status dft">{{ $f($kat->total_draft ?? 0) }}</span></td>
            <td>
              <div style="display:flex;align-items:center;gap:6px">
                <div style="flex:1;background:#f0f0f0;height:8px;border-radius:4px;overflow:hidden">
                  <div style="width:{{ $pct }}%;height:100%;background:{{ $chartColors[$i % count($chartColors)] }};border-radius:4px"></div>
                </div>
                <span style="font-size:11px;color:#888;width:36px;text-align:right">{{ $pct }}%</span>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:#888;padding:24px">Tidak ada data kategori</td></tr>
        @endforelse
      </tbody>
      <tfoot>
        <tr style="font-weight:700;background:#f8f8f8">
          <td colspan="2">TOTAL</td>
          <td style="text-align:right">{{ $f($totalArtikel) }}</td>
          <td style="text-align:right">{{ $f($totalPublished) }}</td>
          <td style="text-align:right">{{ $f($totalDraft) }}</td>
          <td>100%</td>
        </tr>
      </tfoot>
    </table>
  </div>
</section>

{{-- ===== TABEL STATISTIK PENULIS ===== --}}
<section class="panel" style="margin-top:14px">
  <div class="panel-head">
    <h2>Statistik Berita per Penulis</h2>
    <span class="panel-link">{{ $statsPenulis->count() }} penulis</span>
  </div>
  <div class="scroll">
    <table>
      <thead>
        <tr>
          <th style="width:40px">No.</th>
          <th>Nama Penulis</th>
          <th style="text-align:right">Total Artikel</th>
          <th style="text-align:right">Published</th>
          <th style="text-align:right">Draft</th>
          <th style="text-align:right">Total Views</th>
        </tr>
      </thead>
      <tbody>
        @forelse($statsPenulis as $i => $p)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $p->name }}</strong></td>
            <td style="text-align:right;font-weight:700">{{ $f($p->total_artikel ?? 0) }}</td>
            <td style="text-align:right"><span class="badge-status pub">{{ $f($p->total_published ?? 0) }}</span></td>
            <td style="text-align:right"><span class="badge-status dft">{{ $f($p->total_draft ?? 0) }}</span></td>
            <td style="text-align:right;color:#888">{{ $f($p->total_views ?? 0) }}</td>
          </tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:#888;padding:24px">Tidak ada data penulis</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

<div style="height:40px"></div>

@push('scripts')
<script>
(function() {
  // ── Grafik Doughnut Status ────────────────────────────────
  const ctxStatus = document.getElementById('chartStatus');
  if (ctxStatus) {
    new Chart(ctxStatus, {
      type: 'doughnut',
      data: {
        labels: ['Published', 'Draft'],
        datasets: [{
          data: [{{ $totalPublished }}, {{ $totalDraft }}],
          backgroundColor: ['#a7d4ef', '#f3b3d6'],
          borderWidth: 2,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '60%',
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (ctx) => ' ' + ctx.label + ': ' + ctx.raw.toLocaleString('id-ID') + ' artikel'
            }
          }
        }
      }
    });
  }

  // ── Grafik Bar Kategori ──────────────────────────────────
  const ctxKat = document.getElementById('chartKategori');
  if (ctxKat) {
    const katLabels = {!! json_encode($statsKategori->map(fn($k) => $k->name)->values()) !!};
    const katPub    = {!! json_encode($statsKategori->map(fn($k) => $k->total_published ?? 0)->values()) !!};
    const katDraft  = {!! json_encode($statsKategori->map(fn($k) => $k->total_draft ?? 0)->values()) !!};

    new Chart(ctxKat, {
      type: 'bar',
      data: {
        labels: katLabels,
        datasets: [
          { label: 'Published', data: katPub,   backgroundColor: '#a7d4ef', borderRadius: 3 },
          { label: 'Draft',     data: katDraft, backgroundColor: '#f3b3d6', borderRadius: 3 },
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { stacked: true, ticks: { font: { size: 10 }, maxRotation: 45 } },
          y: { stacked: true, ticks: { precision: 0 }, grid: { color: '#f0f0f0' } }
        },
        plugins: {
          legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
          tooltip: { callbacks: { label: (ctx) => ' ' + ctx.dataset.label + ': ' + ctx.raw.toLocaleString('id-ID') }}
        }
      }
    });
  }
})();
</script>
@endpush
@endsection
