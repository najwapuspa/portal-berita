@extends('layouts.dashboard')
@section('judul', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('breadcrumb', 'Admin › Kelola Pengguna › ' . (isset($user) ? 'Edit' : 'Tambah'))
@section('hero', isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna Baru')

@section('konten')
<div class="panel" style="max-width:520px">
  <form method="POST"
        action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
    @csrf
    @if(isset($user)) @method('PUT') @endif

    <div class="form-row">
      <div class="form-col">
        <label for="name">Nama <span class="req">*</span></label>
        <input id="name" name="name" type="text"
               value="{{ old('name', $user->name ?? '') }}" required autocomplete="name">
        @error('name')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-col">
        <label for="email">Email <span class="req">*</span></label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $user->email ?? '') }}" required autocomplete="email">
        @error('email')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-col">
        <label for="role">Role <span class="req">*</span></label>
        <select id="role" name="role" required>
          <option value="user"  {{ old('role', $user->role ?? 'user') === 'user'  ? 'selected' : '' }}>Pembaca</option>
          <option value="admin" {{ old('role', $user->role ?? 'user') === 'admin' ? 'selected' : '' }}>Admin</option>
        </select>
        @error('role')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-row">
      <div class="form-col">
        <label for="password">
          Kata Sandi {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}
          @if(!isset($user))<span class="req">*</span>@endif
        </label>
        <input id="password" name="password" type="password"
               autocomplete="new-password"
               {{ isset($user) ? '' : 'required' }}>
        @error('password')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-submit">
        {{ isset($user) ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
      </button>
      <a href="{{ route('admin.users.index') }}" class="btn-cancel">Batal</a>
    </div>
  </form>
</div>
@endsection
