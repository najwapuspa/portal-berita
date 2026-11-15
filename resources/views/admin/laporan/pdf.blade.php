<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Statistik Berita – NEWS X PAPER</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; line-height: 1.5; }

  /* ── Header ── */
  .pdf-header { background: #111; color: #fff; padding: 18px 24px 14px; margin-bottom: 0; }
  .pdf-logo { font-size: 22px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
  .pdf-logo span { color: #1b8bd8; font-size: 30px; }
  .pdf-tagline { font-size: 8px; color: #aaa; letter-spacing: 0.08em; margin-top: 2px; text-transform: uppercase; }
  .pdf-title-bar { background: #1b8bd8; color: #fff; padding: 10px 24px; font-size: 14px; font-weight: 700; letter-spacing: 0.03em; text-transform: uppercase; }

  /* ── Meta info ── */
  .pdf-meta { padding: 10px 24px; background: #f5f7fa; border-bottom: 1px solid #e3e3e3; display: flex; gap: 40px; font-size: 10px; color: #555; }
  .pdf-meta strong { color: #111; }

  /* ── Section ── */
  .pdf-section { padding: 14px 24px 0; page-break-inside: avoid; }
  .pdf-section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #1b8bd8; border-bottom: 2px solid #1b8bd8; padding-bottom: 5px; margin-bottom: 10px; }

  /* ── Kartu statistik ── */
  .stat-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; margin-bottom: 6px; }
  .stat-row  { display: table-row; }
  .stat-cell { display: table-cell; width: 16.6%; background: #f8f9fa; border: 1px solid #e3e3e3; border-top: 3px solid #1b8bd8; padding: 10px 12px; text-align: center; border-radius: 2px; vertical-align: top; }
  .stat-cell:nth-child(2n) { border-top-color: #a7d4ef; }
  .stat-cell:nth-child(3n) { border-top-color: #f3b3d6; }
  .stat-cell:nth-child(4n) { border-top-color: #a6e3c6; }
  .stat-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 4px; }
  .stat-value { font-size: 20px; font-weight: 900; color: #111; display: block; line-height: 1; }

  /* ── Tabel ── */
  .pdf-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px; }
  .pdf-table th { background: #1b8bd8; color: #fff; padding: 7px 8px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; letter-spacing: 0.04em; }
  .pdf-table th.r { text-align: right; }
  .pdf-table td { padding: 6px 8px; border-bottom: 1px solid #eee; vertical-align: middle; }
  .pdf-table td.r { text-align: right; }
  .pdf-table tr:nth-child(even) td { background: #fafafa; }
  .pdf-table tfoot td { font-weight: 700; background: #f0f0f0; border-top: 2px solid #ddd; }

  /* ── Badge ── */
  .badge-pub { background: #d1fae5; color: #065f46; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: 700; }
  .badge-dft { background: #fee2e2; color: #991b1b; padding: 1px 6px; border-radius: 10px; font-size: 9px; font-weight: 700; }

  /* ── Footer ── */
  .pdf-footer { margin-top: 20px; border-top: 1px solid #ddd; padding: 10px 24px; font-size: 9px; color: #888; text-align: center; }

  /* ── Page break ── */
  .page-break { page-break-after: always; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="pdf-header">
  <div class="pdf-logo">News<span>X</span>Paper</div>
  <div class="pdf-tagline">the art of publishing</div>
</div>
<div class="pdf-title-bar">Laporan Statistik Berita</div>
<div class="pdf-meta">
  <div>Periode: <strong>{{ $periodeLabel }}</strong></div>
  <div>Dibuat: <strong>{{ $dibuatPada->format('d/m/Y H:i') }} WIB</strong></div>
  <div>Total Artikel: <strong>{{ number_format($totalArtikel, 0, ',', '.') }}</strong></div>
</div>

{{-- RINGKASAN STATISTIK --}}
<div class="pdf-section" style="margin-top:12px">
  <div class="pdf-section-title">1. Ringkasan Statistik</div>
  <table class="stat-grid" style="width:100%">
    <tr class="stat-row">
      <td class="stat-cell">
        <span class="stat-label">Total Artikel</span>
        <span class="stat-value">{{ number_format($totalArtikel, 0, ',', '.') }}</span>
      </td>
      <td class="stat-cell">
        <span class="stat-label">Diterbitkan</span>
        <span class="stat-value">{{ number_format($totalPublished, 0, ',', '.') }}</span>
      </td>
      <td class="stat-cell">
        <span class="stat-label">Draft</span>
        <span class="stat-value">{{ number_format($totalDraft, 0, ',', '.') }}</span>
      </td>
      <td class="stat-cell">
        <span class="stat-label">Kategori</span>
        <span class="stat-value">{{ number_format($totalKategori, 0, ',', '.') }}</span>
      </td>
      <td class="stat-cell">
        <span class="stat-label">Total Views</span>
        <span class="stat-value">{{ number_format($totalViews, 0, ',', '.') }}</span>
      </td>
      <td class="stat-cell">
        <span class="stat-label">Penulis</span>
        <span class="stat-value">{{ number_format($totalPenulis, 0, ',', '.') }}</span>
      </td>
    </tr>
  </table>
</div>

{{-- STATUS PUBLIKASI --}}
<div class="pdf-section" style="margin-top:12px">
  <div class="pdf-section-title">2. Distribusi Status Publikasi</div>
  <table class="pdf-table">
    <thead>
      <tr><th>Status</th><th class="r">Jumlah Artikel</th><th class="r">Persentase</th></tr>
    </thead>
    <tbody>
      <tr>
        <td><span class="badge-pub">Published</span></td>
        <td class="r"><strong>{{ number_format($totalPublished, 0, ',', '.') }}</strong></td>
        <td class="r">{{ $totalArtikel > 0 ? number_format($totalPublished / $totalArtikel * 100, 1) : 0 }}%</td>
      </tr>
      <tr>
        <td><span class="badge-dft">Draft</span></td>
        <td class="r"><strong>{{ number_format($totalDraft, 0, ',', '.') }}</strong></td>
        <td class="r">{{ $totalArtikel > 0 ? number_format($totalDraft / $totalArtikel * 100, 1) : 0 }}%</td>
      </tr>
    </tbody>
    <tfoot>
      <tr><td>TOTAL</td><td class="r">{{ number_format($totalArtikel, 0, ',', '.') }}</td><td class="r">100%</td></tr>
    </tfoot>
  </table>
</div>

{{-- STATISTIK KATEGORI --}}
<div class="pdf-section" style="margin-top:12px">
  <div class="pdf-section-title">3. Statistik Berita per Kategori</div>
  <table class="pdf-table">
    <thead>
      <tr>
        <th style="width:30px">No.</th>
        <th>Nama Kategori</th>
        <th class="r">Total Artikel</th>
        <th class="r">Published</th>
        <th class="r">Draft</th>
        <th class="r">%</th>
      </tr>
    </thead>
    <tbody>
      @foreach($statsKategori as $i => $kat)
        @php $pct = $totalArtikel > 0 ? number_format(($kat->total_artikel / $totalArtikel) * 100, 1) : 0; @endphp
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $kat->name }}</td>
          <td class="r"><strong>{{ number_format($kat->total_artikel ?? 0, 0, ',', '.') }}</strong></td>
          <td class="r">{{ number_format($kat->total_published ?? 0, 0, ',', '.') }}</td>
          <td class="r">{{ number_format($kat->total_draft ?? 0, 0, ',', '.') }}</td>
          <td class="r">{{ $pct }}%</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2">TOTAL</td>
        <td class="r">{{ number_format($totalArtikel, 0, ',', '.') }}</td>
        <td class="r">{{ number_format($totalPublished, 0, ',', '.') }}</td>
        <td class="r">{{ number_format($totalDraft, 0, ',', '.') }}</td>
        <td class="r">100%</td>
      </tr>
    </tfoot>
  </table>
</div>

{{-- STATISTIK PENULIS --}}
<div class="pdf-section" style="margin-top:12px">
  <div class="pdf-section-title">4. Statistik Berita per Penulis</div>
  <table class="pdf-table">
    <thead>
      <tr>
        <th style="width:30px">No.</th>
        <th>Nama Penulis</th>
        <th class="r">Total Artikel</th>
        <th class="r">Published</th>
        <th class="r">Draft</th>
        <th class="r">Total Views</th>
      </tr>
    </thead>
    <tbody>
      @foreach($statsPenulis as $i => $p)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $p->name }}</td>
          <td class="r"><strong>{{ number_format($p->total_artikel ?? 0, 0, ',', '.') }}</strong></td>
          <td class="r">{{ number_format($p->total_published ?? 0, 0, ',', '.') }}</td>
          <td class="r">{{ number_format($p->total_draft ?? 0, 0, ',', '.') }}</td>
          <td class="r">{{ number_format($p->total_views ?? 0, 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- FOOTER --}}
<div class="pdf-footer">
  Dokumen ini dibuat secara otomatis oleh sistem NEWS X PAPER &bull; {{ $dibuatPada->format('d F Y, H:i') }} WIB &bull; Periode: {{ $periodeLabel }}
</div>

</body>
</html>
