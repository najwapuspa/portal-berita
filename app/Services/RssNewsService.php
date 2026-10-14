<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;

/**
 * RssNewsService
 *
 * Mengambil berita dari RSS feed (CNN Indonesia, Republika, ANTARA).
 * Hanya menyimpan: judul, ringkasan pendek, URL sumber, gambar,
 * tanggal publikasi, nama media. Isi lengkap artikel TIDAK disalin
 * karena berpotensi melanggar hak cipta.
 *
 * Anti-duplikat: cek source_url sebelum insert.
 * Status default: 'published' — dapat diubah ke 'draft' jika ingin review manual.
 */
class RssNewsService
{
    /** Timeout HTTP dalam detik */
    private const HTTP_TIMEOUT = 15;

    /** Jumlah artikel maks per feed per run */
    private const MAX_PER_FEED = 20;

    /**
     * Daftar feed beserta pemetaan ke slug kategori project.
     * slug_kategori harus cocok dengan kolom `categories.slug` di DB.
     */
    private function feedList(): array
    {
        return [
            // ── CNN Indonesia ──────────────────────────────────────
            [
                'url'          => 'https://www.cnnindonesia.com/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'nasional',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/nasional/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'nasional',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/internasional/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'internasional',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/ekonomi/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'ekonomi',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/teknologi/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'teknologi',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/olahraga/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'olahraga',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/hiburan/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'hiburan',
            ],
            [
                'url'          => 'https://www.cnnindonesia.com/gaya-hidup/rss',
                'source_name'  => 'CNN Indonesia',
                'slug_kategori'=> 'gaya-hidup',
            ],
            // ── Republika ─────────────────────────────────────────
            [
                'url'          => 'https://republika.co.id/rss',
                'source_name'  => 'Republika',
                'slug_kategori'=> 'nasional',
            ],
            // ── ANTARA ────────────────────────────────────────────
            [
                'url'          => 'https://www.antaranews.com/rss/terkini.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'nasional',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/politik.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'politik',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/ekonomi.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'ekonomi',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/olahraga.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'olahraga',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/tekno.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'teknologi',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/dunia-internasional.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'internasional',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/humaniora.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'pendidikan',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/lifestyle.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'gaya-hidup',
            ],
            [
                'url'          => 'https://www.antaranews.com/rss/warta-bumi.xml',
                'source_name'  => 'ANTARA',
                'slug_kategori'=> 'lingkungan',
            ],
        ];
    }

    /**
     * Jalankan fetch semua feed.
     *
     * @return array{inserted: int, skipped: int, errors: int}
     */
    public function fetchAll(): array
    {
        $stats = ['inserted' => 0, 'skipped' => 0, 'errors' => 0];

        // Cache kategori agar tidak query DB berulang
        $categories = Category::all()->keyBy('slug');

        foreach ($this->feedList() as $feed) {
            try {
                $result = $this->fetchFeed(
                    $feed['url'],
                    $feed['source_name'],
                    $feed['slug_kategori'],
                    $categories
                );
                $stats['inserted'] += $result['inserted'];
                $stats['skipped']  += $result['skipped'];
            } catch (\Throwable $e) {
                $stats['errors']++;
                Log::warning('RssNewsService: feed gagal', [
                    'url'   => $feed['url'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('RssNewsService: selesai', $stats);
        return $stats;
    }

    /**
     * Fetch satu feed RSS.
     */
    private function fetchFeed(
        string $url,
        string $sourceName,
        string $categorySlug,
        \Illuminate\Support\Collection $categories
    ): array {
        $inserted = 0;
        $skipped  = 0;

        // Cari kategori di DB; jika tidak ada, skip feed ini
        $category = $categories->get($categorySlug);
        if (! $category) {
            Log::warning("RssNewsService: kategori '{$categorySlug}' tidak ditemukan di DB.");
            return ['inserted' => 0, 'skipped' => 0];
        }

        // HTTP GET dengan timeout
        $response = Http::timeout(self::HTTP_TIMEOUT)
            ->withHeaders(['User-Agent' => 'NewsXpaper RSS Aggregator/1.0'])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} dari {$url}");
        }

        $body = $response->body();

        // Parse XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body, SimpleXMLElement::class, LIBXML_NOCDATA);
        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new \RuntimeException('XML parse error: ' . ($errors[0]->message ?? 'unknown'));
        }

        $items = $xml->channel->item ?? [];
        $count = 0;

        foreach ($items as $item) {
            if ($count >= self::MAX_PER_FEED) break;

            try {
                $result = $this->processItem($item, $sourceName, $category->id);
                if ($result === 'inserted') $inserted++;
                else                        $skipped++;
                $count++;
            } catch (\Throwable $e) {
                Log::debug('RssNewsService: item dilewati', [
                    'title' => (string) ($item->title ?? ''),
                    'error' => $e->getMessage(),
                ]);
                $skipped++;
            }
        }

        return compact('inserted', 'skipped');
    }

    /**
     * Proses satu item RSS: validasi, anti-duplikat, simpan ke DB.
     *
     * @return string 'inserted'|'skipped'
     */
    private function processItem(
        SimpleXMLElement $item,
        string $sourceName,
        int $categoryId
    ): string {
        // ── Ambil field dasar ──────────────────────────────────
        $title     = trim((string) $item->title);
        $sourceUrl = trim((string) ($item->link ?? $item->guid ?? ''));
        $pubDate   = trim((string) ($item->pubDate ?? ''));

        // Judul dan URL wajib ada
        if ($title === '' || $sourceUrl === '') {
            return 'skipped';
        }

        // Validasi URL sumber — harus http/https
        if (! filter_var($sourceUrl, FILTER_VALIDATE_URL)) {
            return 'skipped';
        }
        if (! Str::startsWith($sourceUrl, ['http://', 'https://'])) {
            return 'skipped';
        }

        // ── Anti-duplikat ──────────────────────────────────────
        if (Article::existsBySourceUrl($sourceUrl)) {
            return 'skipped';
        }

        // ── Excerpt ───────────────────────────────────────────
        $excerpt = $this->extractExcerpt($item);

        // ── Gambar ────────────────────────────────────────────
        $imageUrl = $this->extractImage($item);

        // ── Tanggal publikasi ─────────────────────────────────
        $publishedAt = null;
        if ($pubDate !== '') {
            try {
                $publishedAt = \Carbon\Carbon::parse($pubDate)->setTimezone('Asia/Jakarta');
            } catch (\Exception $e) {
                $publishedAt = now();
            }
        }

        // ── Slug unik ─────────────────────────────────────────
        $baseSlug = Str::slug(Str::limit($title, 80));
        $slug     = $this->makeUniqueSlug($baseSlug);

        // ── Hitung read_time dari excerpt ─────────────────────
        $readTime = Article::calculateReadTime($excerpt);

        // ── Konten — HANYA ringkasan, bukan artikel penuh ─────
        // Isi ditampilkan sebagai excerpt + link ke sumber asli.
        // Ini menghormati hak cipta: isi lengkap tidak disalin.
        $content = $this->buildContentHtml($title, $excerpt, $sourceUrl, $sourceName);

        // ── Simpan ke DB ──────────────────────────────────────
        Article::create([
            'category_id' => $categoryId,
            'user_id'     => 1, // Admin default; bisa diubah sesuai kebutuhan
            'title'       => $title,
            'slug'        => $slug,
            'content'     => $content,
            'image'       => $imageUrl,
            'status'      => 'published',
            'views'       => 0,
            'source_name' => $sourceName,
            'source_url'  => $sourceUrl,
            'excerpt'     => $excerpt,
            'read_time'   => $readTime,
            'published_at'=> $publishedAt,
        ]);

        return 'inserted';
    }

    /**
     * Ekstrak ringkasan dari description atau content:encoded.
     * Strip HTML, normalisasi spasi, batasi 500 karakter.
     */
    private function extractExcerpt(SimpleXMLElement $item): string
    {
        // content:encoded lebih lengkap jika tersedia
        $namespaces = $item->getNamespaces(true);
        $content    = '';

        if (isset($namespaces['content'])) {
            $contentNs = $item->children($namespaces['content']);
            $content   = (string) ($contentNs->encoded ?? '');
        }

        if ($content === '') {
            $content = (string) ($item->description ?? '');
        }

        // Strip HTML tags
        $plain = strip_tags($content);

        // Hapus atribusi "REPUBLIKA.CO.ID, JAKARTA —" dll di awal
        $plain = preg_replace('/^[A-Z]+\.CO\.ID[^—–-]*[—–-]\s*/u', '', $plain);
        $plain = preg_replace('/^[A-Z]+\s*,\s*[A-Z]+\s*[-–—]\s*/u', '', $plain);

        // Normalisasi spasi dan newline
        $plain = preg_replace('/\s+/', ' ', $plain);
        $plain = trim($plain);

        return Str::limit($plain, 500);
    }

    /**
     * Ekstrak URL gambar dari berbagai format RSS:
     * - <enclosure url="..."> (CNN Indonesia)
     * - <media:content url="..."> (Republika, ANTARA)
     * - <img src="..."> di dalam description
     */
    private function extractImage(SimpleXMLElement $item): ?string
    {
        // Coba enclosure (CNN)
        if (isset($item->enclosure)) {
            $url = (string) $item->enclosure['url'];
            if ($this->isValidImageUrl($url)) return $url;
        }

        // Coba media:content (Republika, ANTARA)
        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['media'])) {
            $mediaNs = $item->children($namespaces['media']);
            if (isset($mediaNs->content)) {
                $url = (string) $mediaNs->content['url'];
                if ($this->isValidImageUrl($url)) return $url;
            }
        }

        // Coba ambil dari <img> dalam description
        $desc = (string) ($item->description ?? '');
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $desc, $m)) {
            $url = $m[1];
            if ($this->isValidImageUrl($url)) return $url;
        }

        return null;
    }

    /**
     * Validasi URL gambar: harus http/https, bukan placeholder kecil.
     */
    private function isValidImageUrl(?string $url): bool
    {
        if (! $url || strlen($url) < 10) return false;
        if (! Str::startsWith($url, ['http://', 'https://'])) return false;
        if (! filter_var($url, FILTER_VALIDATE_URL)) return false;
        return true;
    }

    /**
     * Generate slug unik — tambahkan angka acak jika sudah ada di DB.
     */
    private function makeUniqueSlug(string $base): string
    {
        $slug = $base;
        if (! Article::where('slug', $slug)->exists()) {
            return $slug;
        }
        // Tambahkan random suffix
        return $base . '-' . Str::random(6);
    }

    /**
     * Buat konten HTML artikel:
     * Ringkasan + link menuju artikel asli di media sumber.
     * Ini memastikan pengguna membaca artikel lengkap di sumber aslinya.
     */
    private function buildContentHtml(
        string $title,
        string $excerpt,
        string $sourceUrl,
        string $sourceName
    ): string {
        $safeExcerpt    = htmlspecialchars($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeSourceName = htmlspecialchars($sourceName, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeSourceUrl  = htmlspecialchars($sourceUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return <<<HTML
<p>{$safeExcerpt}</p>

<div class="source-notice" style="margin-top:24px;padding:16px;background:#f5f8fb;border-left:4px solid #4db2ec;border-radius:2px">
  <p style="margin:0 0 8px;font-size:13px;color:#666;font-weight:600">Sumber Berita</p>
  <p style="margin:0;font-size:14px">
    Artikel ini bersumber dari <strong>{$safeSourceName}</strong>.
    Untuk membaca berita lengkap, silakan kunjungi:
  </p>
  <a href="{$safeSourceUrl}" target="_blank" rel="noopener noreferrer"
     style="display:inline-block;margin-top:10px;padding:9px 18px;background:#4db2ec;color:#fff;border-radius:4px;font-weight:700;font-size:13px;text-decoration:none">
    Baca Selengkapnya di {$safeSourceName} →
  </a>
</div>
HTML;
    }
}
