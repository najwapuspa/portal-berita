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
        $shareUrlRaw   = url()->current();
        $shareUrl      = urlencode($shareUrlRaw);
        $shareTitle    = urlencode($article->display_title);
        $waMsg         = urlencode(
            "[Baca berita terbaru di NEWS X PAPER]\n" .
            "Judul: " . $article->display_title . "\n" .
            "Baca selengkapnya: " . $shareUrlRaw
        );
        $xHashtags     = urlencode('#NewsXPaper #BeritaTerkini');
        $xIntent       = "https://twitter.com/intent/tweet?text={$shareTitle}&url={$shareUrl}&hashtags=" . rawurlencode('NewsXPaper,BeritaTerkini');
      @endphp

      {{-- Toast notifikasi salin tautan --}}
      <div id="copy-toast" class="copy-toast" role="status" aria-live="polite" aria-atomic="true">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
        {{ __('Tautan berhasil disalin!') }}
      </div>

      <div class="article-share">
        <span>{{ __('Bagikan:') }}</span>

        {{-- Facebook Share Dialog --}}
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
           target="_blank" rel="noopener noreferrer"
           class="share-btn share-fb"
           aria-label="{{ __('Bagikan ke Facebook') }}">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          Facebook
        </a>

        {{-- X (Twitter) Intent Tweet --}}
        <a href="{{ $xIntent }}"
           target="_blank" rel="noopener noreferrer"
           class="share-btn share-tw"
           aria-label="{{ __('Bagikan ke X') }}">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L2.021 2.25H8.1l4.265 5.638L18.244 2.25Zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          X
        </a>

        {{-- WhatsApp Share --}}
        <a href="https://api.whatsapp.com/send?text={{ $waMsg }}"
           target="_blank" rel="noopener noreferrer"
           class="share-btn share-wa"
           aria-label="{{ __('Bagikan ke WhatsApp') }}">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
          WhatsApp
        </a>

        {{-- Salin Tautan --}}
        <button class="share-btn share-copy"
                id="btn-copy-link"
                data-url="{{ $shareUrlRaw }}"
                aria-label="{{ __('Salin Tautan') }}">
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
            <span><a href="{{ route('login') }}">Login</a> {{ __('untuk memberikan komentar.') }}</span>
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
(function () {
  var btn   = document.getElementById('btn-copy-link');
  var label = document.getElementById('copy-label');
  var toast = document.getElementById('copy-toast');
  if (!btn) return;

  var originalLabel = label ? label.textContent.trim() : '';
  var toastTimer;

  function showToast() {
    if (!toast) return;
    toast.classList.add('copy-toast--show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () {
      toast.classList.remove('copy-toast--show');
    }, 2500);
  }

  function onSuccess() {
    if (label) {
      label.textContent = '{{ __("Tersalin!") }}';
      setTimeout(function () { label.textContent = originalLabel; }, 2500);
    }
    btn.classList.add('share-copy--done');
    setTimeout(function () { btn.classList.remove('share-copy--done'); }, 2500);
    showToast();
  }

  function fallbackCopy(text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0;';
    document.body.appendChild(ta);
    ta.focus();
    ta.select();
    var ok = false;
    try { ok = document.execCommand('copy'); } catch (e) {}
    document.body.removeChild(ta);
    return ok;
  }

  btn.addEventListener('click', function () {
    var url = btn.getAttribute('data-url') || window.location.href;
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(url).then(onSuccess).catch(function () {
        if (fallbackCopy(url)) onSuccess();
      });
    } else {
      if (fallbackCopy(url)) onSuccess();
    }
  });
})();
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
