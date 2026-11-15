@extends('layouts.dashboard')
@section('judul', 'Kelola Kategori')
@section('breadcrumb', 'Admin › Kelola Kategori')
@section('hero', 'Kelola Kategori')

@section('konten')

<div class="toolbar">
  <span style="font-size:13px;color:var(--muted)">{{ $kategori->count() }} kategori tersedia</span>
  <a href="{{ route('admin.categories.create') }}" class="btn-sm primary">+ Tambah Kategori</a>
</div>

@if(session('success'))
  <div class="alert alert-ok">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-err">{{ session('error') }}</div>
@endif

<div class="panel" style="padding:0">
  <div class="scroll">
    <table>
      <thead>
        <tr>
          <th style="width:40px">No.</th>
          <th>Nama Kategori</th>
          <th>Slug</th>
          <th>Deskripsi</th>
          <th style="text-align:right">Jumlah Artikel</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($kategori as $i => $kat)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>
              <strong>{{ $kat->name }}</strong>
            </td>
            <td><code style="font-size:12px;background:#f5f5f5;padding:2px 6px;border-radius:3px">{{ $kat->slug }}</code></td>
            <td style="max-width:260px;white-space:normal;color:var(--muted);font-size:12px">
              {{ $kat->description ? \Illuminate\Support\Str::limit($kat->description, 60) : '—' }}
            </td>
            <td style="text-align:right">
              <span class="badge-status {{ $kat->articles_count > 0 ? 'pub' : 'dft' }}">
                {{ number_format($kat->articles_count, 0, ',', '.') }}
              </span>
            </td>
            <td class="act" style="white-space:nowrap">
              <a href="{{ route('categories.show', $kat->slug) }}" target="_blank" title="Lihat di situs">Lihat</a>
              <a href="{{ route('admin.categories.edit', $kat) }}">Edit</a>
              @if($kat->articles_count === 0)
                <form method="POST" action="{{ route('admin.categories.destroy', $kat) }}"
                      style="display:inline"
                      onsubmit="return confirm('Hapus kategori &quot;{{ addslashes($kat->name) }}&quot;? Tindakan ini tidak dapat dibatalkan.')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-inline danger">Hapus</button>
                </form>
              @else
                <span title="Kategori memiliki {{ $kat->articles_count }} artikel, tidak dapat dihapus"
                      style="font-size:12px;color:#bbb;padding:3px 10px;border:1px solid #eee;border-radius:4px;cursor:not-allowed">
                  Hapus
                </span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="kosong" style="padding:32px;text-align:center">
              Belum ada kategori.
              <a href="{{ route('admin.categories.create') }}" style="color:var(--brand)">Tambah sekarang</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="panel" style="margin-top:16px;background:#fffbea;border-color:#f0d060">
  <p style="font-size:13px;color:#7a6000;margin:0">
    <strong>⚠ Catatan:</strong>
    Kategori yang memiliki artikel tidak dapat dihapus langsung.
    Pindahkan atau hapus artikel di kategori tersebut terlebih dahulu melalui
    <a href="{{ route('admin.articles.index') }}" style="color:#1b8bd8">Kelola Berita</a>.
  </p>
</div>

@endsection
