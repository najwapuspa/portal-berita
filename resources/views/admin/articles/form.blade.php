@extends('layouts.dashboard')
@section('judul', isset($article) ? 'Edit Berita' : 'Tambah Berita')
@section('breadcrumb', 'Admin › Kelola Berita › ' . (isset($article) ? 'Edit' : 'Tambah'))
@section('hero', isset($article) ? 'Edit Berita' : 'Tambah Berita Baru')

@section('konten')
<div class="panel" style="max-width:860px">
  <form method="POST"
        action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
    @csrf
    @if(isset($article)) @method('PUT') @endif

    <div class="form-row">
      <div class="form-col">
        <label for="title">Judul <span class="req">*</span></label>
        <input id="title" name="title" type="text"
               value="{{ old('title', $article->title ?? '') }}"
               placeholder="Masukkan judul berita…"
               required autocomplete="off">
        @error('title')<p class="err">{{ $message }}</p>@enderror
      </div>
    </div>

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

    <div class="form-row">
      <div class="form-col">
        <label for="image">URL Gambar</label>
        <input id="image" name="image" type="url"
               value="{{ old('image', $article->image ?? '') }}"
               placeholder="https://images.unsplash.com/…"
               oninput="previewImg(this.value)">
        @error('image')<p class="err">{{ $message }}</p>@enderror
        <div id="img-preview" style="margin-top:10px">
          @if(isset($article) && $article->image)
            <img src="{{ $article->image }}" alt="Preview" id="preview-tag"
                 style="max-width:100%;height:180px;object-fit:cover;border-radius:4px"
                 onerror="this.style.display='none'">
          @else
            <img id="preview-tag" alt="Preview" style="display:none;max-width:100%;height:180px;object-fit:cover;border-radius:4px">
          @endif
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="form-col">
        <label for="content">Isi Berita <span class="req">*</span></label>
        <textarea id="content" name="content" rows="14"
                  placeholder="Tulis isi artikel di sini… (HTML diperbolehkan)"
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
function previewImg(url) {
  const img = document.getElementById('preview-tag');
  if (url) { img.src = url; img.style.display = 'block'; }
  else      { img.style.display = 'none'; }
}
</script>
@endpush
