@extends('layouts.dashboard')
@section('judul', 'Kelola Komentar')
@section('breadcrumb', 'Admin › Komentar')
@section('hero', 'Kelola Komentar')

@section('konten')

{{-- Toolbar filter --}}
<div class="toolbar">
  <form method="GET" action="{{ route('admin.comments.index') }}" class="toolbar-form">
    <input type="search"
           name="q"
           value="{{ request('q') }}"
           placeholder="Cari nama, email, judul berita, atau isi komentar…"
           class="toolbar-input"
           style="width:320px">
    <button type="submit" class="btn-sm">Filter</button>
    @if(request('q'))
      <a href="{{ route('admin.comments.index') }}" class="btn-sm ghost">Reset</a>
    @endif
  </form>
  <span style="font-size:13px;color:var(--muted)">
    {{ $comments->total() }} komentar
  </span>
</div>

<div class="panel" style="padding:0">
  <div class="scroll">
    <table>
      <thead>
        <tr>
          <th style="width:160px">Pengguna</th>
          <th style="width:180px">Berita</th>
          <th>Isi Komentar</th>
          <th style="width:120px">Tanggal</th>
          <th style="width:80px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($comments as $comment)
          <tr>
            {{-- Kolom Pengguna --}}
            <td>
              <div style="font-weight:600;font-size:13px">
                {{ optional($comment->user)->name ?? '(Akun dihapus)' }}
              </div>
              <div style="font-size:12px;color:var(--muted)">
                {{ optional($comment->user)->email ?? '—' }}
              </div>
            </td>

            {{-- Kolom Berita --}}
            <td>
              @if($comment->article)
                <a href="{{ route('articles.show', $comment->article->slug) }}"
                   target="_blank"
                   style="font-size:13px;font-weight:500">
                  {{ \Illuminate\Support\Str::limit($comment->article->title, 45) }}
                </a>
              @else
                <span style="font-size:13px;color:var(--muted)">(Berita dihapus)</span>
              @endif
            </td>

            {{-- Isi Komentar --}}
            <td style="white-space:normal;max-width:320px">
              <p style="margin:0;font-size:13px;line-height:1.5;color:var(--ink)">
                {{ \Illuminate\Support\Str::limit($comment->body, 200) }}
              </p>
            </td>

            {{-- Tanggal --}}
            <td style="font-size:12px;color:var(--muted);white-space:nowrap">
              {{ $comment->created_at->translatedFormat('j M Y') }}<br>
              <span style="font-size:11px">{{ $comment->created_at->format('H:i') }}</span>
            </td>

            {{-- Aksi Hapus --}}
            <td class="act">
              <form method="POST"
                    action="{{ route('comments.destroy', $comment) }}"
                    onsubmit="return confirm('Hapus komentar ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-inline danger">Hapus</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="kosong" style="padding:32px;text-align:center">
              @if(request('q'))
                Tidak ada komentar yang cocok dengan pencarian "{{ request('q') }}".
              @else
                Belum ada komentar.
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Pagination --}}
<div style="margin-top:16px">
  {{ $comments->links('vendor.pagination.simple-dash') }}
</div>

@endsection
