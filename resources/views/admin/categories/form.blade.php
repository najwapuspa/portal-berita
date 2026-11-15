@extends('layouts.dashboard')
@php $isEdit = isset($category); @endphp
@section('judul', $isEdit ? 'Edit Kategori' : 'Tambah Kategori')
@section('breadcrumb', 'Admin › Kelola Kategori › ' . ($isEdit ? 'Edit' : 'Tambah'))
@section('hero', $isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru')

@section('konten')
<div class="panel" style="max-width:580px">

  @if($errors->any())
    <div class="alert alert-err">
      <strong>Periksa isian berikut:</strong>
      <ul style="margin:8px 0 0 16px;padding:0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST"
        action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
        novalidate>
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Nama Kategori --}}
    <div class="form-row">
      <div class="form-col">
        <label for="name">Nama Kategori <span class="req">*</span></label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $isEdit ? $category->name : '') }}"
               placeholder="Contoh: Teknologi"
               maxlength="100" required
               class="{{ $errors->has('name') ? 'is-error' : '' }}">
        @error('name')<p class="form-hint" style="color:var(--err)">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Slug (hanya tampil saat tambah) --}}
    @if(! $isEdit)
    <div class="form-row">
      <div class="form-col">
        <label for="slug">Slug URL <span style="color:var(--muted);font-weight:400">(otomatis jika kosong)</span></label>
        <input type="text" id="slug" name="slug"
               value="{{ old('slug') }}"
               placeholder="contoh: teknologi"
               maxlength="100">
        <p class="form-hint">Slug digunakan pada URL kategori, misal: /kategori/teknologi</p>
      </div>
    </div>
    @else
    <div class="form-row">
      <div class="form-col">
        <label>Slug URL <span style="color:var(--muted);font-weight:400">(tidak dapat diubah)</span></label>
        <input type="text" value="{{ $category->slug }}" disabled
               style="background:#f5f5f5;cursor:not-allowed;opacity:.7">
        <p class="form-hint">Mengubah slug dapat merusak tautan berita yang sudah ada.</p>
      </div>
    </div>
    @endif

    {{-- Deskripsi --}}
    <div class="form-row">
      <div class="form-col">
        <label for="description">Deskripsi <span style="color:var(--muted);font-weight:400">(opsional)</span></label>
        <textarea id="description" name="description" rows="3"
                  placeholder="Deskripsi singkat kategori ini…"
                  maxlength="500"
                  style="min-height:80px">{{ old('description', $isEdit ? $category->description : '') }}</textarea>
        <p class="form-hint">Maks. 500 karakter.</p>
      </div>
    </div>

    {{-- Tombol aksi --}}
    <div style="display:flex;gap:10px;margin-top:4px">
      <button type="submit" class="btn-sm primary">
        {{ $isEdit ? '💾 Simpan Perubahan' : '+ Tambah Kategori' }}
      </button>
      <a href="{{ route('admin.categories.index') }}" class="btn-sm">Batal</a>
    </div>
  </form>
</div>

@if($isEdit)
<div class="panel" style="max-width:580px;margin-top:12px;background:#f8f9fa">
  <p style="font-size:13px;color:var(--muted);margin:0">
    <strong>Kategori ini memiliki {{ $category->articles()->count() }} artikel.</strong>
    @if($category->articles()->count() > 0)
      <a href="{{ route('admin.articles.index', ['kategori' => $category->id]) }}" style="color:var(--brand)">
        Lihat artikel di kategori ini →
      </a>
    @endif
  </p>
</div>
@endif

@push('scripts')
<script>
  // Auto-generate slug dari nama (hanya saat tambah)
  const nameInput = document.getElementById('name');
  const slugInput = document.getElementById('slug');
  if (nameInput && slugInput) {
    nameInput.addEventListener('input', function() {
      if (slugInput.dataset.manual) return;
      slugInput.value = nameInput.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    });
    slugInput.addEventListener('input', function() {
      slugInput.dataset.manual = '1';
    });
  }
</script>
@endpush
@endsection
