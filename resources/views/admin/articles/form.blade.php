@extends('layouts.dashboard')
@section('judul', isset($article) ? 'Edit Berita' : 'Tambah Berita')
@section('breadcrumb', 'Admin › Kelola Berita › ' . (isset($article) ? 'Edit' : 'Tambah'))
@section('hero', isset($article) ? 'Edit Berita' : 'Tambah Berita Baru')

@section('konten')
@php
  $existingImg = null;
  if (isset($article) && $article->image) {
    $existingImg = \Illuminate\Support\Str::startsWith($article->image, ['http://', 'https://'])
      ? $article->image
      : asset('storage/' . $article->image);
  }
@endphp

<div class="panel" style="max-width:860px">
  {{-- enctype multipart untuk upload file --}}
  <form method="POST"
        action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
        enctype="multipart/form-data">
    @csrf
    @if(isset($article)) @method('PUT') @endif

    {{-- Judul --}}
    <div class="form-row">
      <div class="form-col">
        <label for="title">Judul <span class="req">*</span></label>
        <input id="title" name="title" type="text"
               value="{{ old('title', $article->title ?? '') }}"
               placeholder="Masukkan judul berita…" required autocomplete="off">
        @error('title')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Kategori + Status --}}
    <div class="form-row form-2col">
      <div class="form-col">
        <label for="category_id">Kategori <span class="req">*</span></label>
        <select id="category_id" name="category_id" required>
          <option value="">— Pilih kategori —</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}"
              {{ old('category_id', $article->category_id ?? '') == $c->id ? 'selected' : '' }}>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
        @error('category_id')<p class="err">{{ $message }}</p>@enderror
      </div>
      <div class="form-col">
        <label for="status">Status</label>
        <select id="status" name="status">
          <option value="draft"     {{ old('status', $article->status ?? 'draft') === 'draft'     ? 'selected' : '' }}>Draft</option>
          <option value="published" {{ old('status', $article->status ?? 'draft') === 'published' ? 'selected' : '' }}>Tayang</option>
        </select>
      </div>
    </div>

    {{-- Gambar: upload file ATAU URL --}}
    <div class="form-row">
      <div class="form-col">
        <label>Gambar Artikel</label>

        {{-- Tab pilihan --}}
        <div class="img-mode-tabs">
          <button type="button" class="img-mode-tab on" id="tab-file" onclick="switchImgMode('file')">
            Upload File
          </button>
          <button type="button" class="img-mode-tab" id="tab-url" onclick="switchImgMode('url')">
            URL Gambar
          </button>
        </div>

        {{-- Mode upload file --}}
        <div id="img-mode-file">
          <input id="image_file" name="image_file" type="file"
                 accept="image/jpeg,image/png,image/webp,image/gif"
                 onchange="previewUpload(this)"
                 style="margin-top:8px">
          <p class="form-hint">JPEG, PNG, WebP, GIF · Maks. 2 MB</p>
          @error('image_file')<p class="err">{{ $message }}</p>@enderror
        </div>

        {{-- Mode URL --}}
        <div id="img-mode-url" style="display:none">
          <input id="image_url" name="image_url" type="url"
                 value="{{ old('image_url', isset($article) && \Illuminate\Support\Str::startsWith($article->image ?? '', ['http://', 'https://']) ? $article->image : '') }}"
                 placeholder="https://images.unsplash.com/…"
                 oninput="previewUrl(this.value)"
                 style="margin-top:8px">
          @error('image_url')<p class="err">{{ $message }}</p>@enderror
        </div>

        {{-- Preview --}}
        <div style="margin-top:12px" id="img-preview-wrap">
          @if($existingImg)
            <p style="font-size:12px;color:var(--muted);margin:0 0 6px">Gambar saat ini:</p>
            <img src="{{ $existingImg }}" alt="Gambar saat ini" id="preview-tag"
                 style="max-width:100%;height:180px;object-fit:cover;border-radius:4px;display:block"
                 onerror="this.style.display='none'">
          @else
            <img id="preview-tag" alt="Preview gambar"
                 style="display:none;max-width:100%;height:180px;object-fit:cover;border-radius:4px">
          @endif
        </div>
      </div>
    </div>

    {{-- Isi Berita --}}
    <div class="form-row">
      <div class="form-col">
        <label for="content">Isi Berita <span class="req">*</span></label>
        <textarea id="content" name="content" rows="14"
                  placeholder="Tulis isi artikel… (HTML diperbolehkan)"
                  required>{{ old('content', $article->content ?? '') }}</textarea>
        @error('content')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn-submit">
        {{ isset($article) ? 'Simpan Perubahan' : 'Tambah Berita' }}
      </button>
      <a href="{{ route('admin.articles.index') }}" class="btn-cancel">Batal</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
function switchImgMode(mode) {
  document.getElementById('img-mode-file').style.display = mode === 'file' ? '' : 'none';
  document.getElementById('img-mode-url').style.display  = mode === 'url'  ? '' : 'none';
  document.getElementById('tab-file').classList.toggle('on', mode === 'file');
  document.getElementById('tab-url').classList.toggle('on',  mode === 'url');

  // Kosongkan input yang tidak dipakai agar tidak konflik
  if (mode === 'file') document.getElementById('image_url').value = '';
  else                 document.getElementById('image_file').value = '';
}

function previewUpload(input) {
  const img = document.getElementById('preview-tag');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}

function previewUrl(url) {
  const img = document.getElementById('preview-tag');
  if (url) { img.src = url; img.style.display = 'block'; }
  else      img.style.display = 'none';
}

// Jika artikel sudah punya URL gambar, aktifkan tab URL secara otomatis
@if(isset($article) && $article->image && \Illuminate\Support\Str::startsWith($article->image, ['http://', 'https://']))
  switchImgMode('url');
@endif
</script>
@endpush
