<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'content',
        'image',
        'status',
        'views',
        // Kolom metadata baru (nullable, aman untuk artikel lama)
        'source_name',
        'source_url',
        'excerpt',
        'excerpt_en',
        'title_en',
        'content_en',
        'read_time',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views'        => 'integer',
        'read_time'    => 'integer',
    ];

    // ─── Relasi ────────────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // ─── Accessor ──────────────────────────────────────────────

    /**
     * Judul dinamis berdasarkan locale aktif (ID / EN).
     */
    public function getDisplayTitleAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return (string) ($this->title ?? '');
    }

    /**
     * Ringkasan dinamis berdasarkan locale aktif (ID / EN).
     */
    public function getDisplayExcerptAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->excerpt_en)) {
            return $this->excerpt_en;
        }
        return $this->getExcerptShortAttribute();
    }

    /**
     * Konten dinamis berdasarkan locale aktif (ID / EN).
     * Secara otomatis membersihkan kotak pengalihan eksternal lama
     * agar pembaca membaca berita mandiri langsung di website sendiri.
     */
    public function getDisplayContentAttribute(): string
    {
        if (app()->getLocale() === 'en' && !empty($this->content_en)) {
            $raw = $this->content_en;
        } else {
            $raw = (string) ($this->content ?? '');
        }

        // Hapus blok pengalihan eksternal lawas (source-notice) jika ada
        $clean = preg_replace('/<div class="source-notice"[^>]*>.*?<\/div>/s', '', $raw);
        return trim($clean ?? '');
    }

    /**
     * Teks waktu baca mengikuti bahasa aktif (ID: "X menit baca", EN: "X min read").
     */
    public function getReadTimeFormattedAttribute(): string
    {
        $mins = $this->getReadTimeMinutesAttribute();
        if (app()->getLocale() === 'en') {
            return $mins . ' min read';
        }
        return $mins . ' menit baca';
    }

    /**
     * Format tanggal publikasi mengikuti bahasa aktif.
     */
    public function getFormattedDateAttribute(): string
    {
        $date = $this->getDisplayDateAttribute();
        $locale = app()->getLocale();
        if ($locale === 'en') {
            return $date->locale('en')->isoFormat('MMMM D, YYYY');
        }
        return $date->locale('id')->translatedFormat('j F Y');
    }

    /**
     * Ambil URL gambar yang sudah dinormalisasi.
     * Jika tidak ada gambar, gunakan placeholder per kategori.
     */
    public function getImageUrlAttribute(): string
    {
        // Coba ambil gambar dari kolom image
        if ($this->image && trim($this->image) !== '') {
            $img = $this->image;
            if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
                return $img;
            }
            // Path relatif → storage URL
            return asset('storage/' . $img);
        }

        // Fallback: placeholder per kategori
        return $this->getCategoryPlaceholder();
    }

    /**
     * Placeholder per kategori — selalu URL lokal yang stabil.
     */
    public function getCategoryPlaceholder(): string
    {
        $slug = optional($this->category)->slug ?? '';
        $map  = [
            'nasional'      => 'placeholder-nasional.svg',
            'internasional' => 'placeholder-internasional.svg',
            'politik'       => 'placeholder-politik.svg',
            'ekonomi'       => 'placeholder-ekonomi.svg',
            'teknologi'     => 'placeholder-teknologi.svg',
            'pendidikan'    => 'placeholder-pendidikan.svg',
            'kesehatan'     => 'placeholder-kesehatan.svg',
            'olahraga'      => 'placeholder-olahraga.svg',
            'hiburan'       => 'placeholder-hiburan.svg',
            'gaya-hidup'    => 'placeholder-gaya-hidup.svg',
            'lingkungan'    => 'placeholder-lingkungan.svg',
            'opini'         => 'placeholder-opini.svg',
        ];
        $file = $map[$slug] ?? 'placeholder-news.svg';
        return asset('images/' . $file);
    }

    /**
     * Estimasi waktu baca (menit).
     * Gunakan kolom read_time jika sudah tersimpan,
     * hitung otomatis dari content jika belum.
     */
    public function getReadTimeMinutesAttribute(): int
    {
        if ($this->read_time && $this->read_time > 0) {
            return $this->read_time;
        }
        $words = str_word_count(strip_tags((string) $this->content));
        return max(1, (int) ceil($words / 200));
    }

    /**
     * Tanggal tampil — gunakan published_at jika ada, fallback ke created_at.
     */
    public function getDisplayDateAttribute(): \Illuminate\Support\Carbon
    {
        return $this->published_at ?? $this->created_at;
    }

    /**
     * Ringkasan pendek — gunakan excerpt jika ada, auto-generate dari content.
     */
    public function getExcerptShortAttribute(): string
    {
        if ($this->excerpt) {
            return Str::limit($this->excerpt, 160);
        }
        return Str::limit(strip_tags((string) $this->content), 160);
    }

    // ─── Static Helper ─────────────────────────────────────────

    /**
     * Hitung dan simpan read_time berdasarkan panjang content.
     * Dipanggil saat menyimpan artikel baru.
     */
    public static function calculateReadTime(string $content): int
    {
        $words = str_word_count(strip_tags($content));
        return max(1, (int) ceil($words / 200));
    }

    /**
     * Cek apakah URL artikel dari sumber eksternal sudah ada di database.
     * Digunakan untuk mencegah duplikat dari RSS feed.
     */
    public static function existsBySourceUrl(string $sourceUrl): bool
    {
        return static::where('source_url', $sourceUrl)->exists();
    }
}
