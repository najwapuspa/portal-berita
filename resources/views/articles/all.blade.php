@extends('layouts.app')

@php
  $catTitle = $activeCategory ? $activeCategory->display_name : null;
  $pageTitle = $catTitle
    ? __('Berita Kategori: :name', ['name' => $catTitle]) . ' – News X Paper'
    : __('Semua Berita') . ' – News X Paper';
@endphp

@section('title', $pageTitle)
@section('description', 'Jelajahi berita dan artikel terkini dari berbagai kategori di News X Paper.')

@push('head')
<style>
  /* ─── ALL NEWS PAGE CUSTOM STYLES ────────────────────────── */
  .news-page {
    padding: 24px 0 60px 0;
    overflow-x: hidden;
  }

  /* Page Header */
  .news-header {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 28px 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  }
  .news-header-left {
    flex: 1;
    min-width: 260px;
  }
  .news-page-tag {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--blue, #1e8fd6);
    margin-bottom: 6px;
    display: inline-block;
  }
  .news-page-title {
    font-size: 30px;
    font-weight: 800;
    color: #111111;
    letter-spacing: -0.4px;
    margin: 0 0 6px 0;
  }
  .news-page-desc {
    font-size: 13.5px;
    color: #666666;
    margin: 0;
  }
  .news-header-badge {
    background: #f4f4f4;
    border: 1px solid #e0e0e0;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12.5px;
    font-weight: 700;
    color: #333333;
    white-space: nowrap;
  }

  /* Category Filter Pills */
  .news-filter-wrap {
    margin-bottom: 28px;
    position: relative;
  }
  .news-filter-list {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin: 0;
    list-style: none;
    -webkit-overflow-scrolling: touch;
  }
  .news-filter-list::-webkit-scrollbar {
    height: 4px;
  }
  .news-filter-list::-webkit-scrollbar-thumb {
    background: #d4d4d4;
    border-radius: 4px;
  }
  .news-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 15px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #333333;
    background: #ffffff;
    border: 1px solid #dcdcdc;
    border-radius: 20px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.15s ease;
  }
  .news-filter-pill:hover {
    border-color: #111111;
    color: #111111;
    background: #f7f7f7;
  }
  .news-filter-pill.active {
    background: #111111;
    color: #ffffff;
    border-color: #111111;
  }
  .news-filter-count {
    font-size: 10px;
    opacity: 0.8;
  }

  /* Articles Grid */
  .news-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    margin-bottom: 36px;
  }
  .news-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
  }
  .news-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.07);
  }
  .news-card-thumb-wrap {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #eaeaea;
    overflow: hidden;
  }
  .news-card-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s ease;
  }
  .news-card:hover .news-card-thumb {
    transform: scale(1.03);
  }
  .news-card-cat-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #111111;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 4px 10px;
    border-radius: 2px;
    text-decoration: none;
    z-index: 2;
  }
  .news-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
  }
  .news-card-title {
    font-size: 17px;
    font-weight: 800;
    line-height: 1.4;
    margin: 0 0 10px 0;
  }
  .news-card-title a {
    color: #111111;
    text-decoration: none;
    transition: color 0.15s ease;
  }
  .news-card-title a:hover {
    color: var(--blue, #1e8fd6);
  }
  .news-card-meta {
    font-size: 11.5px;
    color: #777777;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
  }
  .news-card-author {
    font-weight: 700;
    color: #333333;
  }
  .news-card-excerpt {
    font-size: 13.5px;
    line-height: 1.6;
    color: #555555;
    margin: 0 0 16px 0;
    flex: 1;
  }
  .news-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid #f0f0f0;
    margin-top: auto;
  }
  .news-read-more {
    font-size: 12px;
    font-weight: 700;
    color: #111111;
    text-decoration: none;
    transition: color 0.15s ease, transform 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .news-read-more:hover {
    color: var(--blue, #1e8fd6);
    transform: translateX(2px);
  }
  .news-comments-count {
    font-size: 11px;
    color: #888888;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Empty state */
  .news-empty {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 60px 24px;
    text-align: center;
    margin-bottom: 40px;
  }
  .news-empty-icon {
    margin-bottom: 16px;
    color: #aaaaaa;
  }
  .news-empty-title {
    font-size: 20px;
    font-weight: 700;
    color: #222222;
    margin-bottom: 8px;
  }
  .news-empty-text {
    font-size: 14px;
    color: #666666;
    margin-bottom: 20px;
  }

  /* Responsive Rules */
  @media (max-width: 992px) {
    .news-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
  }

  @media (max-width: 640px) {
    .news-grid {
      grid-template-columns: 1fr;
      gap: 16px;
    }
    .news-header {
      padding: 20px;
      flex-direction: column;
      align-items: flex-start;
    }
    .news-page-title {
      font-size: 24px;
    }
  }
</style>
@endpush

@section('content')
<div class="container news-page">

  {{-- ===== PAGE HEADER ===== --}}
  <header class="news-header">
    <div class="news-header-left">
      <span class="news-page-tag">{{ __('Katalog Informasi') }}</span>
      <h1 class="news-page-title">
        @if($activeCategory)
          {{ __('Berita Kategori: :name', ['name' => $activeCategory->display_name]) }}
        @else
          {{ __('Semua Berita') }}
        @endif
      </h1>
      <p class="news-page-desc">
        {{ __('Menghadirkan liputan jurnalistik terpercaya, tajam, dan berimbang dari berbagai rubrik kehidupan.') }}
      </p>
    </div>
    <div class="news-header-badge">
      {{ __('Total: :count Berita', ['count' => $articles->total()]) }}
    </div>
  </header>

  {{-- ===== CATEGORY FILTER PILLS ===== --}}
  <nav class="news-filter-wrap" aria-label="Filter kategori berita">
    <ul class="news-filter-list">
      <li>
        <a href="{{ route('news.index') }}"
           class="news-filter-pill {{ empty(request('kategori')) ? 'active' : '' }}">
          {{ __('Semua') }}
        </a>
      </li>
      @foreach($categories as $cat)
        @php
          $isActive = request('kategori') === $cat->slug;
        @endphp
        <li>
          <a href="{{ route('news.index', ['kategori' => $cat->slug]) }}"
             class="news-filter-pill {{ $isActive ? 'active' : '' }}">
            {{ $cat->display_name }}
            <span class="news-filter-count">({{ $cat->articles_count }})</span>
          </a>
        </li>
      @endforeach
    </ul>
  </nav>

  {{-- ===== ARTICLES GRID ===== --}}
  @if($articles->isEmpty())
    <div class="news-empty">
      <div class="news-empty-icon" aria-hidden="true">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>
      <h2 class="news-empty-title">{{ __('Belum ada berita ditemukan') }}</h2>
      <p class="news-empty-text">{{ __('Tidak ada artikel yang dipublikasikan pada kategori ini.') }}</p>
      <a href="{{ route('news.index') }}" class="about-btn about-btn-primary" style="display:inline-flex;">
        {{ __('Lihat Semua Berita') }}
      </a>
    </div>
  @else
    <div class="news-grid">
      @foreach($articles as $a)
        @php
          $catName  = $a->category->display_name ?? 'Berita';
          $catSlug  = $a->category->slug ?? '';
          $catColor = config('categories.' . $catSlug . '.color', '#111111');
          
          $imgUrl = null;
          if ($a->image && trim($a->image) !== '') {
            if (\Illuminate\Support\Str::startsWith($a->image, ['http://', 'https://'])) {
              $imgUrl = $a->image;
            } else {
              $imgUrl = asset('storage/' . $a->image);
            }
          }
          $placeholder = asset('images/placeholder-news.svg');
          $finalThumb = $imgUrl ?: $placeholder;
          $dateString = $a->formatted_date;
          $excerpt = $a->display_excerpt;
        @endphp

        <article class="news-card">
          <div class="news-card-thumb-wrap">
            <a href="{{ route('articles.show', $a->slug) }}" tabindex="-1" aria-hidden="true">
              <img src="{{ $finalThumb }}"
                   alt="{{ $a->display_title }}"
                   class="news-card-thumb"
                   loading="lazy"
                   onerror="this.src='{{ $placeholder }}'">
            </a>
            @if($catSlug)
              <a href="{{ route('categories.show', $catSlug) }}"
                 class="news-card-cat-badge"
                 style="background: {{ $catColor }}">
                {{ $catName }}
              </a>
            @else
              <span class="news-card-cat-badge">{{ $catName }}</span>
            @endif
          </div>

          <div class="news-card-body">
            <h2 class="news-card-title">
              <a href="{{ route('articles.show', $a->slug) }}">
                {{ $a->display_title }}
              </a>
            </h2>

            <div class="news-card-meta">
              <span class="news-card-author">{{ $a->user->name ?? 'Redaksi' }}</span>
              <span aria-hidden="true">–</span>
              <time datetime="{{ $a->published_at ? $a->published_at->toIso8601String() : '' }}">{{ $dateString }}</time>
              @if(!empty($a->read_time_formatted))
                <span aria-hidden="true">·</span>
                <span>{{ $a->read_time_formatted }}</span>
              @endif
            </div>

            <p class="news-card-excerpt">
              {{ $excerpt }}
            </p>

            <div class="news-card-footer">
              <a href="{{ route('articles.show', $a->slug) }}" class="news-read-more">
                {{ __('Baca Selengkapnya') }} →
              </a>
              <span class="news-comments-count" title="{{ $a->comments_count }} {{ __('komentar') }}">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                {{ $a->comments_count }}
              </span>
            </div>
          </div>
        </article>
      @endforeach
    </div>

    {{-- ===== PAGINATION ===== --}}
    @if($articles->hasPages())
      <nav class="pager" aria-label="Navigasi Halaman Berita">
        @if($articles->onFirstPage())
          <span class="off">‹ {{ __('Sebelumnya') }}</span>
        @else
          <a href="{{ $articles->previousPageUrl() }}">‹ {{ __('Sebelumnya') }}</a>
        @endif

        <span class="pager-info">{{ __('Halaman :current dari :total', ['current' => $articles->currentPage(), 'total' => $articles->lastPage()]) }}</span>

        @if($articles->hasMorePages())
          <a href="{{ $articles->nextPageUrl() }}">{{ __('Berikutnya') }} ›</a>
        @else
          <span class="off">{{ __('Berikutnya') }} ›</span>
        @endif
      </nav>
    @endif

  @endif

</div>
@endsection
