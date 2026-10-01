@extends('layouts.app')

@section('title', $article->title . ' – News X Paper')

@section('content')
@php
  $catColor = config('categories.' . optional($article->category)->slug . '.color', '#222');
  $wordCount = str_word_count(strip_tags($article->content));
  $readTime  = max(1, (int) ceil($wordCount / 200));
@endphp

<div class="container">

  {{-- Breadcrumb --}}
  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('articles.index') }}">Beranda</a>
    <span>›</span>
    <a href="{{ route('categories.show', optional($article->category)->slug) }}">{{ optional($article->category)->name ?? 'Berita' }}</a>
    <span>›</span>
    <span>{{ Str::limit($article->title, 50) }}</span>
  </nav>

  <div class="article-layout">

    {{-- ===== KONTEN ARTIKEL ===== --}}
    <article class="article-main">

      {{-- Badge kategori --}}
      <div class="article-cats">
        <a href="{{ route('categories.show', optional($article->category)->slug) }}"
           class="article-cat-badge"
           style="background: {{ $catColor }}">
          {{ optional($article->category)->name ?? 'Berita' }}
        </a>
      </div>

      <h1 class="article-title">{{ $article->title }}</h1>

      {{-- Meta --}}
      <div class="article-meta">
        <span class="am-avatar">{{ mb_strtoupper(mb_substr(optional($article->user)->name ?? 'A', 0, 1)) }}</span>
        <div class="am-info">
          <strong>{{ optional($article->user)->name ?? 'Admin' }}</strong>
          <div class="am-sub">
            <time datetime="{{ $article->created_at->toIso8601String() }}">
              {{ $article->created_at->locale('id')->translatedFormat('j F Y, H:i') }}
            </time>
            <span>·</span>
            <span>{{ $readTime }} menit baca</span>
            <span>·</span>
            <span>👁 {{ number_format($article->views, 0, ',', '.') }} dilihat</span>
          </div>
        </div>
      </div>

      {{-- Gambar utama --}}
      @if($article->image)
        <figure class="article-figure">
          <img src="{{ $article->image }}"
               alt="{{ $article->title }}"
               loading="lazy"
               class="article-img"
               onerror="this.closest('figure').style.display='none'">
          <figcaption>{{ optional($article->category)->name ?? 'News X Paper' }} — {{ $article->created_at->translatedFormat('j M Y') }}</figcaption>
        </figure>
      @endif

      {{-- Isi artikel --}}
      <div class="article-body">
        {!! $article->content !!}
      </div>

      {{-- Tombol share --}}
      @php
        $shareUrl = url()->current();
        $shareTitle = urlencode($article->title);
      @endphp
      <div class="article-share">
        <span>Bagikan:</span>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
           target="_blank" rel="noopener" class="share-btn share-fb" aria-label="Bagikan ke Facebook">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          Facebook
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ urlencode($shareUrl) }}"
           target="_blank" rel="noopener" class="share-btn share-tw" aria-label="Bagikan ke X">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
          X
        </a>
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ urlencode($shareUrl) }}"
           target="_blank" rel="noopener" class="share-btn share-wa" aria-label="Bagikan ke WhatsApp">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
          WhatsApp
        </a>
        <button class="share-btn share-copy" onclick="copyLink()" aria-label="Salin tautan">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          <span id="copy-label">Salin Tautan</span>
        </button>
      </div>

      {{-- Komentar --}}
      <section class="comments" id="komentar">
        <h2 class="comments-title">Komentar ({{ $article->comments->count() }})</h2>
        @forelse ($article->comments as $comment)
          <div class="comment-item">
            <div class="comment-avatar">{{ mb_strtoupper(mb_substr(optional($comment->user)->name ?? 'A', 0, 1)) }}</div>
            <div class="comment-body">
              <div class="comment-meta">
                <strong>{{ optional($comment->user)->name ?? 'Anonim' }}</strong>
                <time>{{ $comment->created_at->locale('id')->diffForHumans() }}</time>
              </div>
              <p>{{ $comment->body }}</p>
            </div>
          </div>
        @empty
          <p class="comments-empty">Belum ada komentar. Jadilah yang pertama!</p>
        @endforelse
      </section>

    </article>

    {{-- ===== SIDEBAR ===== --}}
    <aside class="article-sidebar">

      {{-- Berita terkait --}}
      <section class="side-block block b-dark">
        <div class="block-head"><span class="block-title">Berita Terkait</span></div>
        @forelse($terkait as $t)
          <article class="related-item">
            <a href="{{ route('articles.show', $t->slug) }}" class="related-thumb"
               style="{{ $t->image ? 'background-image:url(' . $t->image . ')' : 'background:linear-gradient(135deg,#614385,#516395)' }}"
               aria-label="{{ $t->title }}">
              @if($t->image)
                <img src="{{ $t->image }}" alt="{{ $t->title }}" loading="lazy" style="display:none"
                     onerror="this.parentElement.style.backgroundImage='none'">
              @endif
            </a>
            <div>
              <h4 class="related-title"><a href="{{ route('articles.show', $t->slug) }}">{{ Str::limit($t->title, 60) }}</a></h4>
              <span class="related-meta">{{ $t->created_at->translatedFormat('j M Y') }}</span>
            </div>
          </article>
        @empty
          <p style="font-size:13px;color:#888;padding:8px 0">Tidak ada berita terkait.</p>
        @endforelse
      </section>

      {{-- Kategori lain --}}
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
function copyLink() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const label = document.getElementById('copy-label');
    label.textContent = 'Tersalin!';
    setTimeout(() => label.textContent = 'Salin Tautan', 2000);
  });
}
</script>
@endpush
