@extends('layouts.app')

@php
  $catColor   = config('categories.' . optional($article->category)->slug . '.color', '#222');
  $wordCount  = str_word_count(strip_tags($article->content));
  $readTime   = max(1, (int) ceil($wordCount / 200));
  $excerpt    = \Illuminate\Support\Str::limit(strip_tags($article->content), 160);
  $ogImage    = $article->image
      ? (Str::startsWith($article->image, ['http://', 'https://'])
          ? $article->image
          : asset('storage/' . $article->image))
      : asset('images/placeholder-news.svg');
@endphp

@section('title', $article->title . ' – News X Paper')
@section('description', $excerpt)
@section('og_title',       $article->title)
@section('og_description', $excerpt)
@section('og_image',       $ogImage)

@push('meta')
  <meta property="og:type"            content="article">
  <meta property="article:author"     content="{{ optional($article->user)->name }}">
  <meta property="article:published_time" content="{{ $article->created_at->toIso8601String() }}">
  <meta property="article:section"    content="{{ optional($article->category)->name }}">
@endpush

@section('content')
<div class="container">

  {{-- Breadcrumb --}}
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('articles.index') }}">Beranda</a>
    <span aria-hidden="true">›</span>
    <a href="{{ route('categories.show', optional($article->category)->slug ?? 'politik') }}">
      {{ optional($article->category)->name ?? 'Berita' }}
    </a>
    <span aria-hidden="true">›</span>
    <span>{{ Str::limit($article->title, 50) }}</span>
  </nav>

  <div class="article-layout">

    {{-- ===== KONTEN ARTIKEL ===== --}}
    <article class="article-main">

      {{-- Badge kategori --}}
      <div class="article-cats">
        <a href="{{ route('categories.show', optional($article->category)->slug ?? 'politik') }}"
           class="article-cat-badge"
           style="background: {{ $catColor }}">
          {{ optional($article->category)->name ?? 'Berita' }}
        </a>
      </div>

      <h1 class="article-title">{{ $article->title }}</h1>

      {{-- Meta penulis/waktu/views --}}
      <div class="article-meta">
        <span class="am-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(optional($article->user)->name ?? 'A', 0, 1)) }}</span>
        <div class="am-info">
          <strong>{{ optional($article->user)->name ?? 'Admin' }}</strong>
          <div class="am-sub">
            <time datetime="{{ $article->created_at->toIso8601String() }}">
              {{ $article->created_at->locale('id')->translatedFormat('j F Y, H:i') }}
            </time>
            <span aria-hidden="true">·</span>
            <span>{{ $readTime }} menit baca</span>
            <span aria-hidden="true">·</span>
            <span>👁 {{ number_format($article->views, 0, ',', '.') }} dilihat</span>
          </div>
        </div>
      </div>

      {{-- Gambar utama --}}
      @php
        $imgSrc = null;
        if ($article->image) {
          $imgSrc = Str::startsWith($article->image, ['http://', 'https://'])
            ? $article->image
            : asset('storage/' . $article->image);
        }
      @endphp

      <figure class="article-figure">
        <img src="{{ $imgSrc ?? asset('images/placeholder-news.svg') }}"
             alt="{{ $article->title }}"
             loading="eager"
             class="article-img"
             onerror="this.src='{{ asset('images/placeholder-news.svg') }}'">
        <figcaption>
          {{ optional($article->category)->name ?? 'News X Paper' }}
          — {{ $article->created_at->translatedFormat('j M Y') }}
        </figcaption>
      </figure>

      {{-- Isi artikel --}}
      <div class="article-body">
        {!! $article->content !!}
      </div>

      {{-- Tombol share --}}
      @php
        $shareUrl   = urlencode(url()->current());
        $shareTitle = urlencode($article->title);
      @endphp
      <div class="article-share">
        <span>Bagikan:</span>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer" class="share-btn share-fb" aria-label="Bagikan ke Facebook">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          Facebook
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer" class="share-btn share-tw" aria-label="Bagikan ke X">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
          X
        </a>
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer" class="share-btn share-wa" aria-label="Bagikan ke WhatsApp">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
          WhatsApp
        </a>
        <button class="share-btn share-copy" id="btn-copy-link" aria-label="Salin tautan">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          <span id="copy-label">Salin Tautan</span>
        </button>
      </div>

      {{-- ===== NAVIGASI PREV / NEXT ===== --}}
      @if($prevArticle || $nextArticle)
        <nav class="article-nav" aria-label="Navigasi artikel">
          <div class="an-prev">
            @if($prevArticle)
              <a href="{{ route('articles.show', $prevArticle->slug) }}" class="an-link">
                <span class="an-dir">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                  Sebelumnya
                </span>
                <span class="an-title">{{ Str::limit($prevArticle->title, 60) }}</span>
              </a>
            @endif
          </div>
          <div class="an-next">
            @if($nextArticle)
              <a href="{{ route('articles.show', $nextArticle->slug) }}" class="an-link an-link-right">
                <span class="an-dir">
                  Berikutnya
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
                <span class="an-title">{{ Str::limit($nextArticle->title, 60) }}</span>
              </a>
            @endif
          </div>
        </nav>
      @endif

      {{-- ===== KOMENTAR ===== --}}
      <section class="comments" id="komentar">
        <h2 class="comments-title">Komentar ({{ $article->comments->count() }})</h2>

        @if(session('comment_success'))
          <div class="comment-alert comment-alert-ok" role="alert">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('comment_success') }}
          </div>
        @endif

        @auth
          <form class="comment-form" method="POST"
                action="{{ route('comments.store', $article->slug) }}" novalidate>
            @csrf
            <div class="cf-header">
              <span class="cf-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
              <span class="cf-name">{{ auth()->user()->name }}</span>
            </div>
            <textarea name="body" id="comment-body"
                      class="cf-textarea {{ $errors->has('body') ? 'cf-error' : '' }}"
                      rows="4" placeholder="Tulis komentar kamu…"
                      maxlength="2000" required
                      aria-label="Isi komentar">{{ old('body') }}</textarea>
            @error('body')
              <p class="cf-error-msg">{{ $message }}</p>
            @enderror
            <div class="cf-footer">
              <span class="cf-hint" id="cf-counter">0 / 2000</span>
              <button type="submit" class="cf-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Kirim Komentar
              </button>
            </div>
          </form>
        @else
          <div class="comment-login-prompt">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span><a href="{{ route('login') }}">Masuk</a> untuk memberikan komentar.</span>
          </div>
        @endauth

        <div class="comments-list">
          @forelse($article->comments as $comment)
            <div class="comment-item" id="comment-{{ $comment->id }}">
              <div class="comment-avatar" aria-hidden="true">
                {{ mb_strtoupper(mb_substr(optional($comment->user)->name ?? 'A', 0, 1)) }}
              </div>
              <div class="comment-body">
                <div class="comment-meta">
                  <strong>{{ optional($comment->user)->name ?? 'Anonim' }}</strong>
                  <time datetime="{{ $comment->created_at->toIso8601String() }}">
                    {{ $comment->created_at->locale('id')->translatedFormat('j M Y, H:i') }}
                  </time>
                  @if(auth()->check() && auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                          style="margin-left:auto" onsubmit="return confirm('Hapus komentar ini?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="comment-delete-btn" aria-label="Hapus komentar">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Hapus
                      </button>
                    </form>
                  @endif
                </div>
                <p>{{ $comment->body }}</p>
              </div>
            </div>
          @empty
            <p class="comments-empty">Belum ada komentar. Jadilah yang pertama!</p>
          @endforelse
        </div>
      </section>

    </article>

    {{-- ===== SIDEBAR ===== --}}
    <aside class="article-sidebar">

      <section class="side-block block b-dark">
        <div class="block-head"><span class="block-title">Berita Terkait</span></div>
        @forelse($terkait as $t)
          @php
            $tImg = $t->image
              ? (Str::startsWith($t->image, ['http://', 'https://']) ? $t->image : asset('storage/' . $t->image))
              : null;
          @endphp
          <article class="related-item">
            <a href="{{ route('articles.show', $t->slug) }}" class="related-thumb"
               style="{{ $tImg ? 'background-image:url(' . $tImg . ')' : '' }}"
               aria-label="{{ $t->title }}">
              @if(!$tImg)
                <img src="{{ asset('images/placeholder-news.svg') }}" alt="" loading="lazy" class="related-thumb-img">
              @endif
            </a>
            <div>
              <h4 class="related-title">
                <a href="{{ route('articles.show', $t->slug) }}">{{ Str::limit($t->title, 60) }}</a>
              </h4>
              <span class="related-meta">{{ $t->created_at->translatedFormat('j M Y') }}</span>
            </div>
          </article>
        @empty
          <p style="font-size:13px;color:#888;padding:8px 0">Tidak ada berita terkait.</p>
        @endforelse
      </section>

      <section class="side-block block b-dark" style="margin-top:28px">
        <div class="block-head"><span class="block-title">Kategori</span></div>
        <ul class="cat-list">
          @foreach(config('categories') as $s => $c)
            <li>
              <a href="{{ route('categories.show', $s) }}">
                <i style="background: {{ $c['color'] }}"></i>{{ $c['name'] }}
              </a>
            </li>
          @endforeach
        </ul>
      </section>

    </aside>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Salin tautan
document.getElementById('btn-copy-link')?.addEventListener('click', () => {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const label = document.getElementById('copy-label');
    label.textContent = 'Tersalin!';
    setTimeout(() => label.textContent = 'Salin Tautan', 2000);
  });
});

// Counter karakter komentar
(function() {
  const ta = document.getElementById('comment-body');
  const counter = document.getElementById('cf-counter');
  if (!ta || !counter) return;
  const update = () => {
    counter.textContent = ta.value.length + ' / 2000';
    counter.style.color = ta.value.length > 1900 ? '#e74c3c' : '';
  };
  ta.addEventListener('input', update);
  update();
})();
</script>
@endpush
