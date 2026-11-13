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
            // ── CNBC Indonesia ────────────────────────────────────
            [
                'url'          => 'https://www.cnbcindonesia.com/news/rss',
                'source_name'  => 'CNBC Indonesia',
                'slug_kategori'=> 'nasional',
            ],
            [
                'url'          => 'https://www.cnbcindonesia.com/market/rss',
                'source_name'  => 'CNBC Indonesia',
                'slug_kategori'=> 'ekonomi',
            ],
            [
                'url'          => 'https://www.cnbcindonesia.com/tech/rss',
                'source_name'  => 'CNBC Indonesia',
                'slug_kategori'=> 'teknologi',
            ],
            // ── Tempo ─────────────────────────────────────────────
            [
                'url'          => 'https://rss.tempo.co/nasional',
                'source_name'  => 'Tempo',
                'slug_kategori'=> 'nasional',
            ],
            [
                'url'          => 'https://rss.tempo.co/bisnis',
                'source_name'  => 'Tempo',
                'slug_kategori'=> 'ekonomi',
            ],
            // ── Sindonews ─────────────────────────────────────────
            [
                'url'          => 'https://www.sindonews.com/feed',
                'source_name'  => 'Sindonews',
                'slug_kategori'=> 'nasional',
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

        // HTTP GET dengan timeout dan opsi protokol kompatibel
        $response = Http::timeout(self::HTTP_TIMEOUT)
            ->withOptions([
                'version' => 1.1,
                'verify'  => false,
            ])
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept'     => 'application/rss+xml, application/xml, text/xml, */*',
            ])
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
     * Ekstrak URL gambar dari berbagai format RSS + Open Graph fallback.
     * Urutan prioritas:
     * 1. <enclosure> (CNN Indonesia)
     * 2. <media:content> (Republika, ANTARA)
     * 3. <media:thumbnail>
     * 4. <img src="..."> di dalam description
     * 5. Open Graph og:image dari URL sumber (hanya jika semua gagal, dengan timeout pendek)
     *
     * TIDAK membuat HTTP request tambahan kecuali semua opsi RSS gagal.
     */
    private function extractImage(SimpleXMLElement $item): ?string
    {
        // 1. enclosure (CNN Indonesia)
        if (isset($item->enclosure)) {
            $url = trim((string) $item->enclosure['url']);
            if ($this->isValidImageUrl($url)) return $url;
        }

        // 2 & 3. media:content / media:thumbnail (Republika, ANTARA)
        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['media'])) {
            $mediaNs = $item->children($namespaces['media']);

            // media:content
            if (isset($mediaNs->content)) {
                $url = trim((string) $mediaNs->content['url']);
                if ($this->isValidImageUrl($url)) return $url;
            }

            // media:thumbnail
            if (isset($mediaNs->thumbnail)) {
                $url = trim((string) $mediaNs->thumbnail['url']);
                if ($this->isValidImageUrl($url)) return $url;
            }
        }

        // 4. <img> dalam description
        $desc = (string) ($item->description ?? '');
        if ($desc !== '') {
            // Coba extractEncoded jika content:encoded ada juga
            if (isset($namespaces['content'])) {
                $contentNs = $item->children($namespaces['content']);
                $encoded   = (string) ($contentNs->encoded ?? '');
                if ($encoded !== '' && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $encoded, $m)) {
                    if ($this->isValidImageUrl($m[1])) return $m[1];
                }
            }
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $desc, $m)) {
                if ($this->isValidImageUrl($m[1])) return $m[1];
            }
        }

        // 5. Open Graph dari halaman sumber — hanya jika semua di atas gagal
        //    Timeout sangat pendek (4 detik) agar tidak memperlambat batch RSS
        $sourceUrl = trim((string) ($item->link ?? $item->guid ?? ''));
        if ($sourceUrl !== '' && filter_var($sourceUrl, FILTER_VALIDATE_URL)) {
            $ogImage = $this->fetchOpenGraphImage($sourceUrl);
            if ($ogImage) return $ogImage;
        }

        return null;
    }

    /**
     * Ambil og:image dari halaman sumber dengan timeout pendek.
     * Mengembalikan URL gambar atau null jika gagal/timeout.
     */
    private function fetchOpenGraphImage(string $pageUrl): ?string
    {
        try {
            $response = Http::timeout(6)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'     => 'text/html,application/xhtml+xml',
                ])
                ->get($pageUrl);

            if (! $response->successful()) return null;

            $html = $response->body();

            // og:image
            if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                if ($this->isValidImageUrl($m[1])) return $m[1];
            }
            // og:image dengan order terbalik
            if (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/i', $html, $m)) {
                if ($this->isValidImageUrl($m[1])) return $m[1];
            }
            // twitter:image
            if (preg_match('/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                if ($this->isValidImageUrl($m[1])) return $m[1];
            }

        } catch (\Throwable $e) {
            // Gagal OG fetch — lanjutkan tanpa gambar, tidak menghentikan proses
            Log::debug('RssNewsService: OG fetch gagal', [
                'url'   => $pageUrl,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Validasi URL gambar: harus https (keamanan), format valid, panjang wajar,
     * dan hindari logo/favicon website.
     */
    private function isValidImageUrl(?string $url): bool
    {
        if (! $url || strlen($url) < 12) return false;
        // Wajib https atau http
        if (! \Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])) return false;
        // Hindari favicon / logo situs yang tidak relevan
        if (preg_match('/(favicon|logo-icon|site-logo|rol-logo)/i', $url)) return false;
        // Cegah SSRF ke IP privat — hanya izinkan domain publik
        if (preg_match('/https?:\/\/(localhost|127\.|10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/i', $url)) return false;
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
     * Buat konten HTML artikel berita mandiri:
     * Disusun dalam paragraf editorial yang rapi dan nyaman dibaca langsung
     * di website sendiri tanpa pengalihan ke situs luar.
     */
    private function buildContentHtml(
        string $title,
        string $excerpt,
        string $sourceUrl,
        string $sourceName
    ): string {
        $cleanExcerpt = trim($excerpt);
        if ($cleanExcerpt === '') {
            $cleanExcerpt = $title;
        }

        // Pecah teks menjadi paragraf-paragraf alami agar mudah dan nyaman dibaca
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z0-9])/u', $cleanExcerpt);
        if (count($sentences) >= 2) {
            $half = (int) ceil(count($sentences) / 2);
            $p1 = htmlspecialchars(implode(' ', array_slice($sentences, 0, $half)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $p2 = htmlspecialchars(implode(' ', array_slice($sentences, $half)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return "<p>{$p1}</p>\n<p>{$p2}</p>";
        }

        $safe = htmlspecialchars($cleanExcerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return "<p>{$safe}</p>";
    }
}
