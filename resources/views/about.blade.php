@extends('layouts.app')

@section('title', __('Tentang Kami') . ' | NEWS X PAPER')
@section('description', app()->getLocale() === 'en' ? 'Get to know NEWS X PAPER, a digital news portal providing accurate, insightful, and diverse journalism to expand readers\' perspectives.' : 'Kenali NEWS X PAPER, portal berita digital yang menyajikan informasi aktual, informatif, dan beragam untuk memperluas wawasan pembaca.')

@push('head')
<style>
  /* ─── ABOUT PAGE CUSTOM STYLES ─────────────────────────── */
  .about-page {
    padding: 24px 0 60px 0;
    overflow-x: hidden;
  }

  /* Section 1: Hero */
  .about-hero {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 48px 36px;
    text-align: center;
    margin-bottom: 40px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
  }
  .about-hero-tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--blue, #1e8fd6);
    background: #eef6fc;
    padding: 6px 14px;
    border-radius: 2px;
    margin-bottom: 16px;
  }
  .about-hero-title {
    font-size: 38px;
    font-weight: 800;
    color: #111111;
    line-height: 1.2;
    margin-bottom: 14px;
    letter-spacing: -0.5px;
    word-break: break-word;
    overflow-wrap: break-word;
  }
  .about-hero-subtitle {
    font-size: 19px;
    font-weight: 600;
    color: #444444;
    line-height: 1.4;
    margin-bottom: 18px;
  }
  .about-hero-desc {
    max-width: 780px;
    margin: 0 auto 28px auto;
    font-size: 15px;
    line-height: 1.7;
    color: #555555;
  }
  .about-hero-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
  }
  .about-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 26px;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border-radius: 3px;
    text-decoration: none;
    transition: background 0.18s ease, transform 0.12s ease;
  }
  .about-btn-primary {
    background: #111111;
    color: #ffffff;
  }
  .about-btn-primary:hover {
    background: #2a2a2a;
    color: #ffffff;
    transform: translateY(-1px);
  }
  .about-btn-outline {
    background: transparent;
    color: #222222;
    border: 1px solid #c8c8c8;
  }
  .about-btn-outline:hover {
    background: #f7f7f7;
    border-color: #111111;
    color: #111111;
    transform: translateY(-1px);
  }

  /* General Section Structure */
  .about-section {
    margin-bottom: 48px;
  }
  .about-sec-head {
    border-bottom: 2px solid #111111;
    padding-bottom: 10px;
    margin-bottom: 24px;
    display: flex;
    align-items: baseline;
    justify-content: space-between;
  }
  .about-sec-title {
    font-size: 24px;
    font-weight: 800;
    color: #111111;
    letter-spacing: -0.3px;
    margin: 0;
  }

  /* Section 2: Siapa Kami */
  .about-intro-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-left: 4px solid var(--blue, #1e8fd6);
    border-radius: 4px;
    padding: 28px 32px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  }
  .about-intro-text {
    font-size: 15.5px;
    line-height: 1.8;
    color: #333333;
    margin-bottom: 16px;
  }
  .about-intro-text:last-child {
    margin-bottom: 0;
  }

  /* Section 3: Visi & Section 4: Misi in 2 Column Layout */
  .about-vm-grid {
    display: grid;
    grid-template-columns: 1fr 1.35fr;
    gap: 28px;
    align-items: start;
  }
  .about-visi-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 32px 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
  }
  .about-visi-quote {
    font-size: 18px;
    font-weight: 600;
    line-height: 1.6;
    color: #1a1a1a;
    font-style: italic;
    position: relative;
    padding-left: 20px;
    border-left: 3px solid #111111;
  }

  .about-misi-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .about-misi-card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    padding: 16px 20px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.02);
    transition: transform 0.15s ease, border-color 0.15s ease;
  }
  .about-misi-card:hover {
    transform: translateX(4px);
    border-color: #cfcfcf;
  }
  .about-misi-num {
    flex: 0 0 28px;
    width: 28px;
    height: 28px;
    background: #111111;
    color: #ffffff;
    font-size: 12px;
    font-weight: 800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
  }
  .about-misi-text {
    font-size: 14.5px;
    line-height: 1.55;
    color: #333333;
    margin: 0;
  }

  /* Section 5: Nilai-Nilai Kami (5 Cards Grid) */
  .about-values-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
  }
  .about-val-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-top: 3px solid #111111;
    border-radius: 4px;
    padding: 22px 18px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    display: flex;
    flex-direction: column;
  }
  .about-val-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
  }
  .about-val-icon {
    width: 38px;
    height: 38px;
    border-radius: 6px;
    background: #f4f4f4;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
    color: #222222;
  }
  .about-val-title {
    font-size: 16px;
    font-weight: 700;
    color: #111111;
    margin-bottom: 8px;
  }
  .about-val-desc {
    font-size: 13px;
    line-height: 1.55;
    color: #555555;
    margin: 0;
  }

  /* Section 6: Kategori Berita */
  .about-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
  }
  .about-cat-card {
    background: #ffffff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 16px 18px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    position: relative;
    overflow: hidden;
  }
  .about-cat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    border-color: #cccccc;
  }
  .about-cat-indicator {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
  }
  .about-cat-info {
    padding-left: 8px;
  }
  .about-cat-name {
    font-size: 14.5px;
    font-weight: 700;
    color: #111111;
    margin-bottom: 2px;
  }
  .about-cat-count {
    font-size: 11.5px;
    color: #777777;
  }
  .about-cat-arrow {
    font-size: 16px;
    color: #999999;
    transition: transform 0.15s ease, color 0.15s ease;
  }
  .about-cat-card:hover .about-cat-arrow {
    transform: translateX(3px);
    color: #111111;
  }

  /* Section 7: Penutup / CTA */
  .about-cta {
    background: #111111;
    color: #ffffff;
    border-radius: 6px;
    padding: 44px 36px;
    text-align: center;
    margin-top: 20px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  }
  .about-cta-title {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 12px;
    letter-spacing: -0.3px;
    color: #ffffff;
  }
  .about-cta-desc {
    max-width: 660px;
    margin: 0 auto 26px auto;
    font-size: 15px;
    line-height: 1.65;
    color: #cccccc;
  }
  .about-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    color: #111111;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 13px 32px;
    border-radius: 3px;
    text-decoration: none;
    transition: background 0.18s ease, transform 0.12s ease;
  }
  .about-cta-btn:hover {
    background: #f0f0f0;
    color: #000000;
    transform: translateY(-2px);
  }

  /* Responsive Rules */
  @media (max-width: 992px) {
    .about-values-grid {
      grid-template-columns: repeat(3, 1fr);
    }
    .about-vm-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .about-hero {
      padding: 30px 18px;
      margin-bottom: 28px;
    }
    .about-hero-title {
      font-size: 26px;
    }
    .about-hero-subtitle {
      font-size: 15px;
    }
    .about-hero-desc {
      font-size: 13.5px;
    }
    .about-sec-title {
      font-size: 20px;
    }
    .about-values-grid {
      grid-template-columns: repeat(2, 1fr);
    }
    .about-categories-grid {
      grid-template-columns: 1fr;
    }
    .about-cta {
      padding: 30px 18px;
    }
    .about-cta-title {
      font-size: 22px;
    }
    .about-cta-desc {
      font-size: 13.5px;
    }
  }

  @media (max-width: 480px) {
    .about-hero {
      padding: 24px 14px;
    }
    .about-hero-title {
      font-size: 22px;
    }
    .about-values-grid {
      grid-template-columns: 1fr;
    }
    .about-hero-actions {
      flex-direction: column;
      width: 100%;
    }
    .about-btn {
      width: 100%;
    }
  }
</style>
@endpush

@section('content')
<div class="container about-page">

  {{-- ======================================================== --}}
  {{-- BAGIAN 1 — HERO / INTRODUCTION                          --}}
  {{-- ======================================================== --}}
  <header class="about-hero">
    <span class="about-hero-tag">{{ __('Profil Resmi Media') }}</span>
    <h1 class="about-hero-title">Tentang NEWS X PAPER</h1>
    <p class="about-hero-subtitle">{{ __('Informasi Aktual, Wawasan Luas, dan Berita untuk Semua.') }}</p>
    <p class="about-hero-desc">
      {{ app()->getLocale() === 'en'
        ? 'NEWS X PAPER is a digital news portal committed to providing accessible, informative, and engaging journalism for the public. We curate stories spanning vital sectors of life, empowering readers to stay informed and broaden their horizons.'
        : 'NEWS X PAPER merupakan portal berita digital yang hadir untuk memberikan akses informasi kepada masyarakat secara praktis, informatif, dan mudah dipahami. Kami menghadirkan berbagai informasi dari beragam bidang kehidupan agar pembaca dapat mengikuti perkembangan peristiwa dan memperoleh wawasan yang lebih luas.' }}
    </p>
    <div class="about-hero-actions">
      <a href="{{ route('news.index') }}" class="about-btn about-btn-primary">
        {{ __('Jelajahi Berita →') }}
      </a>
      <a href="{{ route('articles.index') }}" class="about-btn about-btn-outline">
        {{ __('Kembali ke Beranda') }}
      </a>
    </div>
  </header>

  {{-- ======================================================== --}}
  {{-- BAGIAN 2 — SIAPA KAMI?                                  --}}
  {{-- ======================================================== --}}
  <section class="about-section" aria-labelledby="sec-siapa-kami">
    <div class="about-sec-head">
      <h2 id="sec-siapa-kami" class="about-sec-title">{{ __('Siapa Kami?') }}</h2>
    </div>
    <div class="about-intro-card">
      <p class="about-intro-text">
        {{ app()->getLocale() === 'en'
          ? 'NEWS X PAPER is a digital news portal platform presenting diverse information across a wide spectrum of categories, ranging from national, international, politics, economy, technology, education, health, sports, entertainment, to lifestyle.'
          : 'NEWS X PAPER adalah platform portal berita digital yang menyajikan beragam informasi dari berbagai kategori, mulai dari nasional, internasional, politik, ekonomi, teknologi, pendidikan, kesehatan, olahraga, hiburan, hingga gaya hidup.' }}
      </p>
      <p class="about-intro-text">
        {{ app()->getLocale() === 'en'
          ? 'We are here to facilitate readers in finding relevant information through a comfortable, structured, and accessible digital news experience.'
          : 'Kami hadir untuk memudahkan pembaca menemukan informasi yang relevan melalui tampilan website yang nyaman, terstruktur, dan mudah diakses.' }}
      </p>
    </div>
  </section>

  {{-- ======================================================== --}}
  {{-- BAGIAN 3 & 4 — VISI DAN MISI KAMI                       --}}
  {{-- ======================================================== --}}
  <section class="about-section" aria-label="Visi dan Misi Kami">
    <div class="about-vm-grid">

      {{-- Bagian 3: Visi Kami --}}
      <div>
        <div class="about-sec-head">
          <h2 class="about-sec-title">{{ __('Visi Kami') }}</h2>
        </div>
        <div class="about-visi-card">
          <blockquote class="about-visi-quote">
            {{ app()->getLocale() === 'en'
              ? '"To become an informative, trusted, and universally accessible digital news portal that provides meaningful insights for everyday life."'
              : '"Menjadi portal berita digital yang informatif, terpercaya, dan mudah diakses oleh masyarakat serta mampu memberikan wawasan yang bermanfaat bagi kehidupan sehari-hari."' }}
          </blockquote>
        </div>
      </div>

      {{-- Bagian 4: Misi Kami --}}
      <div>
        <div class="about-sec-head">
          <h2 class="about-sec-title">{{ __('Misi Kami') }}</h2>
        </div>
        <div class="about-misi-list">
          <div class="about-misi-card">
            <span class="about-misi-num" aria-hidden="true">1</span>
            <p class="about-misi-text">
              {{ app()->getLocale() === 'en' ? 'Delivering information that is up-to-date, informative, and easily understood.' : 'Menyajikan informasi yang aktual, informatif, dan mudah dipahami.' }}
            </p>
          </div>
          <div class="about-misi-card">
            <span class="about-misi-num" aria-hidden="true">2</span>
            <p class="about-misi-text">
              {{ app()->getLocale() === 'en' ? 'Facilitating public access to diverse information through modern digital platforms.' : 'Mempermudah masyarakat dalam mengakses berbagai informasi melalui platform digital.' }}
            </p>
          </div>
          <div class="about-misi-card">
            <span class="about-misi-num" aria-hidden="true">3</span>
            <p class="about-misi-text">
              {{ app()->getLocale() === 'en' ? 'Presenting news from diverse categories to expand readers\' perspectives.' : 'Menghadirkan berita dari beragam kategori untuk memperluas wawasan pembaca.' }}
            </p>
          </div>
          <div class="about-misi-card">
            <span class="about-misi-num" aria-hidden="true">4</span>
            <p class="about-misi-text">
              {{ app()->getLocale() === 'en' ? 'Prioritizing responsible and verified journalism.' : 'Mengutamakan penyajian informasi yang bertanggung jawab.' }}
            </p>
          </div>
          <div class="about-misi-card">
            <span class="about-misi-num" aria-hidden="true">5</span>
            <p class="about-misi-text">
              {{ app()->getLocale() === 'en' ? 'Creating a comfortable and responsive reading experience across all devices.' : 'Menciptakan pengalaman membaca berita yang nyaman dan responsif di berbagai perangkat.' }}
            </p>
          </div>
        </div>
      </div>

    </div>
  </section>

  {{-- ======================================================== --}}
  {{-- BAGIAN 5 — NILAI-NILAI KAMI                             --}}
  {{-- ======================================================== --}}
  <section class="about-section" aria-labelledby="sec-nilai">
    <div class="about-sec-head">
      <h2 id="sec-nilai" class="about-sec-title">{{ __('Nilai yang Kami Junjung') }}</h2>
    </div>
    <div class="about-values-grid">

      {{-- Nilai 1: Keakuratan --}}
      <article class="about-val-card">
        <div class="about-val-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 12 11 14 15 10"/>
          </svg>
        </div>
        <h3 class="about-val-title">{{ __('Keakuratan') }}</h3>
        <p class="about-val-desc">
          {{ app()->getLocale() === 'en' ? 'Prioritizing verified, transparent, and accountable reporting.' : 'Mengutamakan informasi yang jelas dan dapat dipertanggungjawabkan.' }}
        </p>
      </article>

      {{-- Nilai 2: Objektivitas --}}
      <article class="about-val-card">
        <div class="about-val-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="2" x2="12" y2="22"/>
          </svg>
        </div>
        <h3 class="about-val-title">{{ __('Objektivitas') }}</h3>
        <p class="about-val-desc">
          {{ app()->getLocale() === 'en' ? 'Delivering balanced perspectives without partiality.' : 'Menyajikan informasi secara berimbang.' }}
        </p>
      </article>

      {{-- Nilai 3: Profesionalisme --}}
      <article class="about-val-card">
        <div class="about-val-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
          </svg>
        </div>
        <h3 class="about-val-title">{{ __('Profesionalisme') }}</h3>
        <p class="about-val-desc">
          {{ app()->getLocale() === 'en' ? 'Upholding uncompromising editorial quality and reader experience.' : 'Menjaga kualitas penyajian dan pengalaman membaca.' }}
        </p>
      </article>

      {{-- Nilai 4: Aksesibilitas --}}
      <article class="about-val-card">
        <div class="about-val-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="2" y1="12" x2="22" y2="12"/>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
          </svg>
        </div>
        <h3 class="about-val-title">{{ __('Aksesibilitas') }}</h3>
        <p class="about-val-desc">
          {{ app()->getLocale() === 'en' ? 'Ensuring fast, round-the-clock news accessibility for all readers.' : 'Memudahkan pembaca mengakses informasi kapan saja.' }}
        </p>
      </article>

      {{-- Nilai 5: Inovasi --}}
      <article class="about-val-card">
        <div class="about-val-icon" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
          </svg>
        </div>
        <h3 class="about-val-title">{{ __('Inovasi') }}</h3>
        <p class="about-val-desc">
          {{ app()->getLocale() === 'en' ? 'Continuously enhancing the digital reading journey through modern technology.' : 'Terus mengembangkan pengalaman membaca melalui teknologi digital.' }}
        </p>
      </article>

    </div>
  </section>

  {{-- ======================================================== --}}
  {{-- BAGIAN 6 — KATEGORI BERITA                              --}}
  {{-- ======================================================== --}}
  <section class="about-section" aria-labelledby="sec-kategori">
    <div class="about-sec-head">
      <h2 id="sec-kategori" class="about-sec-title">{{ __('Informasi dalam Berbagai Kategori') }}</h2>
    </div>
    <div class="about-categories-grid">
      @foreach($categories as $cat)
        @php
          $catColor = config('categories.' . $cat->slug . '.color', '#222222');
          $articleCount = $cat->articles_count ?? 0;
          $catDisplayName = $cat->display_name;
        @endphp
        <a href="{{ route('categories.show', $cat->slug) }}" class="about-cat-card" title="{{ __('Lihat berita kategori') }} {{ $catDisplayName }}">
          <span class="about-cat-indicator" style="background: {{ $catColor }}"></span>
          <div class="about-cat-info">
            <div class="about-cat-name">{{ $catDisplayName }}</div>
            <div class="about-cat-count">{{ $articleCount }} {{ __('artikel terbit') }}</div>
          </div>
          <span class="about-cat-arrow" aria-hidden="true">→</span>
        </a>
      @endforeach
    </div>
  </section>

  {{-- ======================================================== --}}
  {{-- BAGIAN 7 — PENUTUP & CALL TO ACTION                     --}}
  {{-- ======================================================== --}}
  <section class="about-cta" aria-labelledby="sec-penutup">
    <h2 id="sec-penutup" class="about-cta-title">{{ __('Temukan Informasi yang Anda Butuhkan') }}</h2>
    <p class="about-cta-desc">
      {{ app()->getLocale() === 'en'
        ? 'Stay abreast of breaking developments and discover new perspectives with NEWS X PAPER. We are dedicated to providing a seamless, enjoyable, and enlightening reading experience.'
        : 'Terus ikuti perkembangan informasi dan temukan berbagai wawasan baru bersama NEWS X PAPER. Kami hadir untuk memberikan pengalaman membaca berita yang lebih mudah, nyaman, dan informatif.' }}
    </p>
    <a href="{{ route('news.index') }}" class="about-cta-btn">
      {{ __('Jelajahi Berita →') }}
    </a>
  </section>

</div>
@endsection
