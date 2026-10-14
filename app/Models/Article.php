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
     * Ambil URL gambar yang sudah dinormalisasi.
     * Mengembalikan URL absolut (http/https), path storage, atau null.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) return null;
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return asset('storage/' . $this->image);
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
