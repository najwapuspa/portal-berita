@extends('layouts.dashboard')
@section('judul', 'Kelola Berita')
@section('breadcrumb', 'Admin › Kelola Berita')
@section('hero', 'Kelola Berita')

@section('konten')
<div class="toolbar">
  <form method="GET" action="{{ route('admin.articles.index') }}" class="toolbar-form">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul…" class="toolbar-input">
    <select name="kategori" class="toolbar-select">
      <option value="">Semua Kategori</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}" {{ request('kategori') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
      @endforeach
    </select>
    <select name="status" class="toolbar-select">
      <option value="">Semua Status</option>
      <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Tayang</option>
      <option value="draft"     {{ request('status') === 'draft'     ? 'selected' : '' }}>Draft</option>
    </select>
    <button type="submit" class="btn-sm">Filter</button>
    @if(request()->hasAny(['q','kategori','status']))
      <a href="{{ route('admin.articles.index') }}" class="btn-sm ghost">Reset</a>
    @endif
  </form>
  <a href="{{ route('admin.articles.create') }}" class="btn-sm primary">+ Tambah Berita</a>
</div>

<div class="panel" style="padding:0">
  <div class="scroll">
    <table>
      <thead>
        <tr>
          <th style="width:40%">Judul</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Dilihat</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $a)
          <tr>
            <td>
              <a href="{{ route('articles.show', $a->slug) }}" target="_blank" class="article-title">
                {{ Str::limit($a->title, 55) }}
              </a>
              <div style="font-size:12px;color:var(--muted);margin-top:2px">{{ $a->comments_count }} komentar</div>
            </td>
            <td><span class="tag">{{ optional($a->category)->name ?? '-' }}</span></td>
            <td>
              <span class="badge-status {{ $a->status === 'published' ? 'pub' : 'dft' }}">
                {{ $a->status === 'published' ? 'Tayang' : 'Draft' }}
              </span>
            </td>
            <td>{{ number_format($a->views, 0, ',', '.') }}</td>
            <td style="white-space:nowrap">{{ $a->created_at->translatedFormat('j M Y') }}</td>
            <td class="act" style="white-space:nowrap">
              <a href="{{ route('admin.articles.edit', $a) }}">Edit</a>
              <form method="POST" action="{{ route('admin.articles.toggle', $a) }}" style="display:inline">
                @csrf @method('PATCH')
                <button type="submit" class="btn-inline">
                  {{ $a->status === 'published' ? 'Draft' : 'Tayang' }}
                </button>
              </form>
              <form method="POST" action="{{ route('admin.articles.destroy', $a) }}"
                    style="display:inline"
                    onsubmit="return confirm('Hapus berita ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-inline danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="kosong" style="padding:32px;text-align:center">Belum ada berita.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top:16px">
  {{ $articles->links('vendor.pagination.simple-dash') }}
</div>
@endsection
