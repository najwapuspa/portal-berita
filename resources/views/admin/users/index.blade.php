@extends('layouts.dashboard')
@section('judul', 'Kelola Pengguna')
@section('breadcrumb', 'Admin › Kelola Pengguna')
@section('hero', 'Kelola Pengguna')

@section('konten')
<div class="toolbar">
  <form method="GET" action="{{ route('admin.users.index') }}" class="toolbar-form">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama / email…" class="toolbar-input">
    <select name="role" class="toolbar-select">
      <option value="">Semua Role</option>
      <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
      <option value="user"  {{ request('role') === 'user'  ? 'selected' : '' }}>Pembaca</option>
    </select>
    <button type="submit" class="btn-sm">Filter</button>
    @if(request()->hasAny(['q','role']))
      <a href="{{ route('admin.users.index') }}" class="btn-sm ghost">Reset</a>
    @endif
  </form>
  <a href="{{ route('admin.users.create') }}" class="btn-sm primary">+ Tambah Pengguna</a>
</div>

<div class="panel" style="padding:0">
  <div class="scroll">
    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Role</th>
          <th>Berita</th>
          <th>Komentar</th>
          <th>Bergabung</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $u)
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <span style="width:34px;height:34px;border-radius:50%;background:var(--c3);color:#111;display:grid;place-items:center;font-weight:700;flex-shrink:0">
                  {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                </span>
                {{ $u->name }}
              </div>
            </td>
            <td>{{ $u->email }}</td>
            <td>
              <span class="badge-role {{ $u->role === 'admin' ? 'r-admin' : 'r-user' }}">{{ $u->role }}</span>
            </td>
            <td>{{ $u->articles_count }}</td>
            <td>{{ $u->comments_count }}</td>
            <td style="white-space:nowrap">{{ $u->created_at->translatedFormat('j M Y') }}</td>
            <td class="act" style="white-space:nowrap">
              <a href="{{ route('admin.users.edit', $u) }}">Edit</a>
              @if($u->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle', $u) }}" style="display:inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn-inline">
                    {{ $u->role === 'admin' ? 'Jadi Pembaca' : 'Jadi Admin' }}
                  </button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                      style="display:inline"
                      onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-inline danger">Hapus</button>
                </form>
              @else
                <span style="color:var(--muted);font-size:12px">(akun Anda)</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="kosong" style="padding:32px;text-align:center">Belum ada pengguna.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div style="margin-top:16px">
  {{ $users->links('vendor.pagination.simple-dash') }}
</div>
@endsection
