@extends('layouts.app')

@php
  $catColor     = config('categories.' . optional($article->category)->slug . '.color', '#222');
  $catName      = optional($article->category)->display_name ?? __('Berita');
  $readTimeFmt  = $article->read_time_formatted;
  $displayDate  = $article->display_date;  // published_at jika ada, fallback created_at
  $excerpt      = $article->display_excerpt;
  $ogImage      = $article->image_url ?? asset('images/placeholder-news.svg');
  $isRssArticle = ! empty($article->source_name) && ! empty($article->source_url);
@endphp

@section('title', $article->display_title . ' – News X Paper')
@section('description', $excerpt)
@section('og_title',       $article->display_title)
@section('og_description', $excerpt)
@section('og_image',       $ogImage)

@push('meta')
  <meta property="og:type"                content="article">
  <meta property="article:author"         content="{{ optional($article->user)->name }}">
  <meta property="article:published_time" content="{{ $displayDate->toIso8601String() }}">
  <meta property="article:section"        content="{{ $catName }}">
@endpush

@section('content')
<div class="container">

  {{-- Navigasi Atas: Kembali & Breadcrumb --}}
  <div class="article-back-bar">
    <a href="{{ route('news.index') }}" class="article-back-link">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      {{ __('Kembali ke Semua Berita') }}
    </a>
  </div>

  <nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('articles.index') }}">{{ __('Beranda') }}</a>
    <span aria-hidden="true">›</span>
    <a href="{{ route('categories.show', optional($article->category)->slug ?? 'nasional') }}">
      {{ $catName }}
    </a>
    <span aria-hidden="true">›</span>
    <span>{{ Str::limit($article->display_title, 50) }}</span>
  </nav>

  <div class="article-layout">

    {{-- ===== KONTEN UTAMA ARTIKEL ===== --}}
    <article class="article-main">

      {{-- Badge Kategori & Label Sumber Redaksi --}}
      <div class="article-cats">
        <a href="{{ route('categories.show', optional($article->category)->slug ?? 'nasional') }}"
           class="article-cat-badge"
           style="background: {{ $catColor }}">
          {{ $catName }}
        </a>
        @if($isRssArticle)
          <span class="article-source-pill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            {{ $article->source_name }}
          </span>
        @endif
      </div>

      {{-- Judul Utama Berita --}}
      <h1 class="article-title">{{ $article->display_title }}</h1>

      {{-- Subjudul / Editorial Lead Deck --}}
      @if(!empty($excerpt) && $excerpt !== $article->display_title)
        <div class="article-lead">
          {{ $excerpt }}
        </div>
      @endif

      {{-- Meta: Penulis / Sumber, Waktu Publikasi, Waktu Baca, Views --}}
      <div class="article-meta">
        <span class="am-avatar" aria-hidden="true">
          {{ mb_strtoupper(mb_substr($isRssArticle ? $article->source_name : (optional($article->user)->name ?? 'A'), 0, 1)) }}
        </span>
        <div class="am-info">
          <strong>{{ $isRssArticle ? $article->source_name : (optional($article->user)->name ?? 'Redaksi NEWS X PAPER') }}</strong>
          <div class="am-sub">
            <time datetime="{{ $displayDate->toIso8601String() }}">
              {{ $displayDate->setTimezone('Asia/Jakarta')->locale(app()->getLocale())->translatedFormat('j F Y, H:i') }} WIB
            </time>
            <span aria-hidden="true">·</span>
            <span>{{ $readTimeFmt }}</span>
            <span aria-hidden="true">·</span>
            <span>👁 {{ number_format($article->views, 0, ',', '.') }} {{ __('dilihat') }}</span>
          </div>
        </div>
      </div>

      {{-- Foto Utama Berita --}}
      <figure class="article-figure">
        <img src="{{ $ogImage }}"
             alt="{{ $article->display_title }}"
             loading="eager"
             class="article-img"
             onerror="this.src='{{ asset('images/placeholder-news.svg') }}'">
        <figcaption>
          {{ $catName }} — {{ $displayDate->setTimezone('Asia/Jakarta')->locale(app()->getLocale())->translatedFormat('j M Y') }}
          @if($isRssArticle) · Foto & Laporan: {{ $article->source_name }} @endif
        </figcaption>
      </figure>

      {{-- Pemberitahuan Liputan Jika Berita Bersumber dari RSS --}}
      @if($isRssArticle)
        <div class="article-summary-badge" role="note">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          <span>{{ __('Ringkasan Liputan') }}: {{ __('Laporan berita ini disajikan berdasarkan rangkuman fakta jurnalistik terpercaya dari :source.', ['source' => $article->source_name]) }}</span>
        </div>
      @endif

      {{-- Isi Bersih Artikel Berita (Dibaca Mandiri di Website Sendiri) --}}
      <div class="article-body">
        {!! $article->display_content !!}
      </div>

      {{-- Kotak Atribusi Sumber & Hak Cipta (Elegan & Sesuai Etika Jurnalistik) --}}
      @if($isRssArticle)
        <aside class="article-attribution-card" aria-label="{{ __('Atribusi Sumber Berita') }}">
          <div class="aac-header">
            <span class="aac-badge">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              {{ __('Keterangan Sumber & Hak Cipta') }}
            </span>
            <span class="aac-source">{{ $article->source_name }}</span>
          </div>
          <p class="aac-text">
            {{ __('Laporan berita ini dikurasi dan disadur dari kanal resmi :source untuk menghadirkan cakupan informasi yang aktual, berimbang, dan tepercaya bagi pembaca NEWS X PAPER.', ['source' => $article->source_name]) }}
          </p>
          <div class="aac-footer">
            <span class="aac-copyright">
              © {{ optional($displayDate)->format('Y') ?? date('Y') }} {{ $article->source_name }}. {{ __('Hak cipta materi berita sepenuhnya milik penerbit asli.') }}
            </span>
            <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="aac-link" title="{{ __('Kunjungi publikasi asli di tab baru') }}">
              {{ __('Tautan Artikel Asli') }} ↗
            </a>
          </div>
        </aside>
      @endif

      {{-- Tombol Bagikan Berita --}}
      @php
        $shareUrl   = urlencode(url()->current());
        $shareTitle = urlencode($article->display_title);
      @endphp
      <div class="article-share">
        <span>{{ __('Bagikan:') }}</span>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer" class="share-btn share-fb" aria-label="Facebook">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          Facebook
        </a>
        <a href="https://twitter.com/intent/tweet?text={{ $shareTitle }}&url={{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer" class="share-btn share-tw" aria-label="X">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
          X
        </a>
        <a href="https://api.whatsapp.com/send?text={{ $shareTitle }}%20{{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer" class="share-btn share-wa" aria-label="WhatsApp">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
          WhatsApp
        </a>
        <button class="share-btn share-copy" id="btn-copy-link" aria-label="{{ __('Salin Tautan') }}">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          <span id="copy-label">{{ __('Salin Tautan') }}</span>
        </button>
      </div>

      {{-- Bar Navigasi Bawah: Kembali ke Daftar Berita & Kategori --}}
      <div class="article-bottom-actions">
        <a href="{{ route('news.index') }}" class="article-back-btn">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          {{ __('Kembali ke Semua Berita') }}
        </a>
        <a href="{{ route('categories.show', optional($article->category)->slug ?? 'nasional') }}" class="article-cat-btn">
          {{ __('Lihat Kategori :name', ['name' => $catName]) }} →
        </a>
      </div>

      {{-- Navigasi Artikel Sebelumnya & Berikutnya --}}
      @if($prevArticle || $nextArticle)
        <nav class="article-nav" aria-label="Navigasi artikel">
          <div class="an-prev">
            @if($prevArticle)
              <a href="{{ route('articles.show', $prevArticle->slug) }}" class="an-link">
                <span class="an-dir">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                  {{ __('Sebelumnya') }}
                </span>
                <span class="an-title">{{ Str::limit($prevArticle->display_title ?? $prevArticle->title, 60) }}</span>
              </a>
            @endif
          </div>
          <div class="an-next">
            @if($nextArticle)
              <a href="{{ route('articles.show', $nextArticle->slug) }}" class="an-link an-link-right">
                <span class="an-dir">
                  {{ __('Berikutnya') }}
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
                <span class="an-title">{{ Str::limit($nextArticle->display_title ?? $nextArticle->title, 60) }}</span>
              </a>
            @endif
          </div>
        </nav>
      @endif

      {{-- ===== SEKSI KOMENTAR ===== --}}
      <section class="comments" id="komentar">
        <h2 class="comments-title">{{ __('Komentar (:count)', ['count' => $article->comments->count()]) }}</h2>

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
                      rows="4" placeholder="{{ __('Tulis komentar kamu…') }}"
                      maxlength="2000" required
                      aria-label="Isi komentar">{{ old('body') }}</textarea>
            @error('body')
              <p class="cf-error-msg">{{ $message }}</p>
            @enderror
            <div class="cf-footer">
              <span class="cf-hint" id="cf-counter">0 / 2000</span>
              <button type="submit" class="cf-submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ __('Kirim Komentar') }}
              </button>
            </div>
          </form>
        @else
          <div class="comment-login-prompt">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span><a href="{{ route('login') }}">{{ __('Masuk') }}</a> {{ __('untuk memberikan komentar.') }}</span>
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
                    {{ $comment->created_at->locale(app()->getLocale())->translatedFormat('j M Y, H:i') }}
                  </time>
                  @if(auth()->check() && auth()->user()->role === 'admin')
                    <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                          style="margin-left:auto" onsubmit="return confirm('{{ __('Hapus komentar ini?') }}')">
                      @csrf @method('DELETE')
                      <button type="submit" class="comment-delete-btn" aria-label="{{ __('Hapus') }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        {{ __('Hapus') }}
                      </button>
                    </form>
                  @endif
                </div>
                <p>{{ $comment->body }}</p>
              </div>
            </div>
          @empty
            <p class="comments-empty">{{ __('Belum ada komentar. Jadilah yang pertama!') }}</p>
          @endforelse
        </div>
      </section>

    </article>

    {{-- ===== SIDEBAR: BERITA TERKAIT & KATEGORI ===== --}}
    <aside class="article-sidebar">

      {{-- Berita Terkait --}}
      <section class="side-block block b-dark">
        <div class="block-head"><span class="block-title">{{ __('Artikel Terkait') }}</span></div>
        @forelse($terkait as $t)
          @php
            $tImg = $t->image_url;
          @endphp
          <article class="related-item">
            <a href="{{ route('articles.show', $t->slug) }}" class="related-thumb"
               style="{{ $tImg ? 'background-image:url(' . $tImg . ')' : '' }}"
               aria-label="{{ $t->display_title }}">
              @if(! $tImg)
                <img src="{{ asset('images/placeholder-news.svg') }}" alt="" loading="lazy" class="related-thumb-img">
              @endif
            </a>
            <div>
              <h4 class="related-title">
                <a href="{{ route('articles.show', $t->slug) }}">{{ Str::limit($t->display_title, 60) }}</a>
              </h4>
              <span class="related-meta">
                {{ $t->formatted_date }}
              </span>
            </div>
          </article>
        @empty
          <p style="font-size:13px;color:#888;padding:8px 0">{{ __('Tidak ada berita terkait.') }}</p>
        @endforelse
      </section>

      {{-- Daftar Kategori --}}
      <section class="side-block block b-dark" style="margin-top:28px">
        <div class="block-head"><span class="block-title">{{ __('Kategori') }}</span></div>
        <ul class="cat-list">
          @foreach(config('categories') as $s => $c)
            @php
              $cName = app()->getLocale() === 'en' ? ($c['name_en'] ?? $c['name']) : $c['name'];
            @endphp
            <li>
              <a href="{{ route('categories.show', $s) }}">
                <i style="background: {{ $c['color'] }}"></i>{{ $cName }}
              </a>
            </li>
          @endforeach
        </ul>
      </section>

      {{-- Ruang Iklan Editorial --}}
      <div class="side-block" style="margin-top:28px">
        <div class="ad-box">{{ __('Ruang Iklan') }}<br>300 × 250</div>
      </div>

    </aside>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-copy-link')?.addEventListener('click', () => {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const label = document.getElementById('copy-label');
    const oldText = label.textContent;
    label.textContent = "{{ __('Tersalin!') }}";
    setTimeout(() => label.textContent = oldText, 2000);
  });
});
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
