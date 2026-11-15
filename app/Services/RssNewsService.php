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
 * Mengambil berita dari RSS feed sumber terpercaya.
 * Konten disusun dari excerpt RSS dan metadata yang tersedia.
 * Tidak menyalin isi penuh artikel karena alasan hak cipta.
 *
 * Prioritas gambar: enclosure → media:content → media:thumbnail → img di description
 * Anti-duplikat: cek source_url sebelum insert.
 */
class RssNewsService
{
    private const HTTP_TIMEOUT  = 20;
    private const MAX_PER_FEED  = 20;
    private const MIN_EXCERPT   = 30; // karakter minimal agar artikel tidak kosong

    // ─────────────────────────────────────────────────────────────
    // Daftar Feed
    // ─────────────────────────────────────────────────────────────

    private function feedList(): array
    {
        return [
            // ── CNN Indonesia ─────────────────────────────────────
            ['url' => 'https://www.cnnindonesia.com/nasional/rss',      'source_name' => 'CNN Indonesia', 'slug_kategori' => 'nasional'],
            ['url' => 'https://www.cnnindonesia.com/internasional/rss', 'source_name' => 'CNN Indonesia', 'slug_kategori' => 'internasional'],
            ['url' => 'https://www.cnnindonesia.com/ekonomi/rss',       'source_name' => 'CNN Indonesia', 'slug_kategori' => 'ekonomi'],
            ['url' => 'https://www.cnnindonesia.com/teknologi/rss',     'source_name' => 'CNN Indonesia', 'slug_kategori' => 'teknologi'],
            ['url' => 'https://www.cnnindonesia.com/olahraga/rss',      'source_name' => 'CNN Indonesia', 'slug_kategori' => 'olahraga'],
            ['url' => 'https://www.cnnindonesia.com/hiburan/rss',       'source_name' => 'CNN Indonesia', 'slug_kategori' => 'hiburan'],
            ['url' => 'https://www.cnnindonesia.com/gaya-hidup/rss',    'source_name' => 'CNN Indonesia', 'slug_kategori' => 'gaya-hidup'],
            ['url' => 'https://www.cnnindonesia.com/rss',               'source_name' => 'CNN Indonesia', 'slug_kategori' => 'nasional'],

            // ── Republika ─────────────────────────────────────────
            ['url' => 'https://republika.co.id/rss',                   'source_name' => 'Republika',     'slug_kategori' => 'nasional'],

            // ── ANTARA ────────────────────────────────────────────
            ['url' => 'https://www.antaranews.com/rss/terkini.xml',              'source_name' => 'ANTARA', 'slug_kategori' => 'nasional'],
            ['url' => 'https://www.antaranews.com/rss/politik.xml',              'source_name' => 'ANTARA', 'slug_kategori' => 'politik'],
            ['url' => 'https://www.antaranews.com/rss/ekonomi.xml',              'source_name' => 'ANTARA', 'slug_kategori' => 'ekonomi'],
            ['url' => 'https://www.antaranews.com/rss/olahraga.xml',             'source_name' => 'ANTARA', 'slug_kategori' => 'olahraga'],
            ['url' => 'https://www.antaranews.com/rss/tekno.xml',                'source_name' => 'ANTARA', 'slug_kategori' => 'teknologi'],
            ['url' => 'https://www.antaranews.com/rss/dunia-internasional.xml',  'source_name' => 'ANTARA', 'slug_kategori' => 'internasional'],
            ['url' => 'https://www.antaranews.com/rss/humaniora.xml',            'source_name' => 'ANTARA', 'slug_kategori' => 'pendidikan'],
            ['url' => 'https://www.antaranews.com/rss/lifestyle.xml',            'source_name' => 'ANTARA', 'slug_kategori' => 'gaya-hidup'],
            ['url' => 'https://www.antaranews.com/rss/warta-bumi.xml',           'source_name' => 'ANTARA', 'slug_kategori' => 'lingkungan'],

            // ── CNBC Indonesia ─────────────────────────────────────
            ['url' => 'https://www.cnbcindonesia.com/news/rss',   'source_name' => 'CNBC Indonesia', 'slug_kategori' => 'nasional'],
            ['url' => 'https://www.cnbcindonesia.com/market/rss', 'source_name' => 'CNBC Indonesia', 'slug_kategori' => 'ekonomi'],
            ['url' => 'https://www.cnbcindonesia.com/tech/rss',   'source_name' => 'CNBC Indonesia', 'slug_kategori' => 'teknologi'],

            // ── Tempo ─────────────────────────────────────────────
            ['url' => 'https://rss.tempo.co/nasional',  'source_name' => 'Tempo', 'slug_kategori' => 'nasional'],
            ['url' => 'https://rss.tempo.co/bisnis',    'source_name' => 'Tempo', 'slug_kategori' => 'ekonomi'],

            // ── Sindonews ──────────────────────────────────────────
            ['url' => 'https://www.sindonews.com/feed', 'source_name' => 'Sindonews', 'slug_kategori' => 'nasional'],
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────

    /**
     * Jalankan fetch semua feed.
     *
     * @return array{inserted:int, skipped:int, errors:int, per_source:array}
     */
    public function fetchAll(): array
    {
        $stats = [
            'inserted'   => 0,
            'skipped'    => 0,
            'errors'     => 0,
            'per_source' => [],
        ];

        $categories = Category::all()->keyBy('slug');

        foreach ($this->feedList() as $feed) {
            $source = $feed['source_name'];
            if (! isset($stats['per_source'][$source])) {
                $stats['per_source'][$source] = ['inserted' => 0, 'skipped' => 0, 'errors' => 0];
            }

            try {
                $result = $this->fetchFeed(
                    $feed['url'],
                    $source,
                    $feed['slug_kategori'],
                    $categories
                );
                $stats['inserted']                      += $result['inserted'];
                $stats['skipped']                       += $result['skipped'];
                $stats['per_source'][$source]['inserted'] += $result['inserted'];
                $stats['per_source'][$source]['skipped']  += $result['skipped'];

                Log::info("RSS OK: [{$source}] {$feed['url']}", [
                    'inserted' => $result['inserted'],
                    'skipped'  => $result['skipped'],
                ]);
            } catch (\Throwable $e) {
                $stats['errors']++;
                $stats['per_source'][$source]['errors']++;
                Log::warning("RSS FAIL: [{$source}] {$feed['url']}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('RssNewsService: selesai', [
            'inserted' => $stats['inserted'],
            'skipped'  => $stats['skipped'],
            'errors'   => $stats['errors'],
            'run_at'   => now()->toIso8601String(),
        ]);

        return $stats;
    }

    // ─────────────────────────────────────────────────────────────
    // Internal: Fetch satu feed
    // ─────────────────────────────────────────────────────────────

    private function fetchFeed(
        string $url,
        string $sourceName,
        string $categorySlug,
        \Illuminate\Support\Collection $categories
    ): array {
        $inserted = 0;
        $skipped  = 0;

        $category = $categories->get($categorySlug);
        if (! $category) {
            Log::warning("RSS: kategori '{$categorySlug}' tidak ada di DB — feed dilewati.", ['url' => $url]);
            return ['inserted' => 0, 'skipped' => 0];
        }

        $response = Http::timeout(self::HTTP_TIMEOUT)
            ->withOptions([
                'version' => 1.0,   // HTTP/1.0 — bekerja lebih baik dengan beberapa feed (mis. ANTARA)
                'verify'  => false,
                'curl'    => [CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_0],
            ])
            ->withHeaders([
                'User-Agent' => 'curl/7.88.1',
                'Accept'     => 'application/rss+xml, application/xml, text/xml, */*',
                'Connection' => 'close',
            ])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} dari {$url}");
        }

        $body = $response->body();
        if (empty(trim($body))) {
            throw new \RuntimeException("Body kosong dari {$url}");
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body, SimpleXMLElement::class, LIBXML_NOCDATA);
        if ($xml === false) {
            $errs = libxml_get_errors();
            libxml_clear_errors();
            throw new \RuntimeException('XML parse error: ' . ($errs[0]->message ?? 'unknown'));
        }

        $items = $xml->channel->item ?? [];
        $count = 0;

        foreach ($items as $item) {
            if ($count >= self::MAX_PER_FEED) break;
            try {
                $r = $this->processItem($item, $sourceName, $category->id);
                if ($r === 'inserted') $inserted++;
                else                   $skipped++;
                $count++;
            } catch (\Throwable $e) {
                Log::debug('RSS item skip', [
                    'title' => (string) ($item->title ?? ''),
                    'error' => $e->getMessage(),
                ]);
                $skipped++;
            }
        }

        return compact('inserted', 'skipped');
    }

    // ─────────────────────────────────────────────────────────────
    // Internal: Proses satu item RSS
    // ─────────────────────────────────────────────────────────────

    private function processItem(
        SimpleXMLElement $item,
        string $sourceName,
        int $categoryId
    ): string {
        $title     = trim((string) ($item->title ?? ''));
        $sourceUrl = trim((string) ($item->link ?? $item->guid ?? ''));
        $pubDate   = trim((string) ($item->pubDate ?? ''));

        // Wajib: judul & URL
        if ($title === '' || $sourceUrl === '') return 'skipped';
        if (! filter_var($sourceUrl, FILTER_VALIDATE_URL)) return 'skipped';
        if (! Str::startsWith($sourceUrl, ['http://', 'https://'])) return 'skipped';

        // Anti-duplikat — cek source_url
        if (Article::existsBySourceUrl($sourceUrl)) return 'skipped';

        // Konten & excerpt
        $rawContent = $this->extractRawContent($item);
        $excerpt    = $this->cleanToExcerpt($rawContent, 400);

        // Jika excerpt terlalu pendek, gunakan judul sebagai fallback
        if (strlen($excerpt) < self::MIN_EXCERPT) {
            $excerpt = $title;
        }

        // Gambar
        $imageUrl = $this->extractImage($item);

        // Tanggal publikasi asli
        $publishedAt = null;
        if ($pubDate !== '') {
            try {
                $publishedAt = \Carbon\Carbon::parse($pubDate)->setTimezone('Asia/Jakarta');
                // Jika tanggal tidak masuk akal (terlalu lampau atau masa depan), gunakan now()
                if ($publishedAt->lt(now()->subYears(5)) || $publishedAt->gt(now()->addDay())) {
                    $publishedAt = now();
                }
            } catch (\Exception $e) {
                $publishedAt = now();
            }
        }

        // Build konten artikel yang informatif dan readable
        $content  = $this->buildRichContent($title, $rawContent, $excerpt, $sourceName, $sourceUrl);
        $readTime = Article::calculateReadTime($content);

        // Slug unik
        $baseSlug = Str::slug(Str::limit($title, 90));
        if ($baseSlug === '') $baseSlug = 'artikel-' . Str::random(8);
        $slug = $this->makeUniqueSlug($baseSlug);

        Article::create([
            'category_id' => $categoryId,
            'user_id'     => 1,
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

    // ─────────────────────────────────────────────────────────────
    // Internal: Ekstraksi konten mentah
    // ─────────────────────────────────────────────────────────────

    /**
     * Ambil teks terpanjang yang tersedia dari RSS item.
     * Urutan: content:encoded → description → title
     */
    private function extractRawContent(SimpleXMLElement $item): string
    {
        $namespaces = $item->getNamespaces(true);

        // Coba content:encoded (biasanya lebih panjang)
        if (isset($namespaces['content'])) {
            $ns  = $item->children($namespaces['content']);
            $raw = (string) ($ns->encoded ?? '');
            if (strlen(trim(strip_tags($raw))) > 50) {
                return $raw;
            }
        }

        // Fallback ke description
        $desc = (string) ($item->description ?? '');
        if ($desc !== '') return $desc;

        return (string) ($item->title ?? '');
    }

    /**
     * Bersihkan HTML menjadi teks plain, hapus atribusi sumber di awal,
     * batasi panjang.
     */
    private function cleanToExcerpt(string $raw, int $limit = 400): string
    {
        // Hapus semua tag HTML
        $plain = strip_tags($raw);

        // Hapus atribusi media di awal seperti "REPUBLIKA.CO.ID, JAKARTA — "
        $plain = preg_replace('/^[A-Z0-9\.\s]+,\s*[A-Z\s]+\s*[-–—]\s*/u', '', $plain);
        $plain = preg_replace('/^(CNN Indonesia|Republika|Antara|Tempo|CNBC|Sindonews)[^—–-]*[—–-]\s*/i', '', $plain);

        // Normalisasi spasi
        $plain = preg_replace('/\s+/', ' ', $plain);
        $plain = trim($plain);

        return Str::limit($plain, $limit, '...');
    }

    /**
     * Susun konten artikel yang lebih kaya dan terbaca dari bahan RSS.
     * Memecah teks menjadi paragraf alami dengan lead yang kuat.
     * Tidak menambah fakta yang tidak ada di sumber.
     */
    private function buildRichContent(
        string $title,
        string $rawHtml,
        string $excerpt,
        string $sourceName,
        string $sourceUrl
    ): string {
        // Bersihkan HTML sumber — izinkan tag dasar saja
        $allowedTags = '<p><br><b><strong><em><i><h2><h3><h4><ul><ol><li><blockquote>';
        $cleanHtml   = strip_tags($rawHtml, $allowedTags);

        // Ambil teks plain untuk disusun menjadi paragraf
        $plainText = strip_tags($rawHtml);
        $plainText = preg_replace('/^[A-Z0-9\.\s]+,\s*[A-Z\s]+\s*[-–—]\s*/u', '', $plainText);
        $plainText = preg_replace('/\s+/', ' ', $plainText);
        $plainText = trim($plainText);

        $html = '';

        // Jika ada HTML cukup panjang dari feed, gunakan langsung
        $cleanHtmlPlain = trim(strip_tags($cleanHtml));
        if (strlen($cleanHtmlPlain) >= 200) {
            // Pecah menjadi paragraf jika belum ada tag <p>
            if (strpos($cleanHtml, '<p>') === false && strpos($cleanHtml, '<h') === false) {
                // Pecah berdasarkan kalimat panjang
                $sentences = preg_split('/(?<=[.!?।])\s+(?=[A-Z\p{Lu}])/u', $plainText);
                $chunks    = array_chunk($sentences, max(2, (int) ceil(count($sentences) / 3)));
                foreach ($chunks as $chunk) {
                    $para = trim(implode(' ', $chunk));
                    if ($para !== '') {
                        $html .= '<p>' . e($para) . '</p>' . "\n";
                    }
                }
            } else {
                // Gunakan HTML yang sudah bersih
                $html = $cleanHtml;
            }
        } elseif (strlen($plainText) >= self::MIN_EXCERPT) {
            // Konten pendek — susun dari teks yang ada
            // Pecah berdasarkan kalimat
            $sentences = preg_split('/(?<=[.!?।])\s+(?=[A-Z\p{Lu}])/u', $plainText);
            $sentences = array_values(array_filter($sentences, fn ($s) => strlen(trim($s)) > 10));

            if (count($sentences) >= 4) {
                // Paragraf 1: lead (kalimat 1-2)
                $p1 = implode(' ', array_slice($sentences, 0, 2));
                // Paragraf 2: isi (kalimat 3-4)
                $p2 = implode(' ', array_slice($sentences, 2, 2));
                // Paragraf 3: sisa
                $p3 = implode(' ', array_slice($sentences, 4));
                $html .= '<p>' . e(trim($p1)) . '</p>' . "\n";
                $html .= '<p>' . e(trim($p2)) . '</p>' . "\n";
                if (strlen(trim($p3)) > 20) {
                    $html .= '<p>' . e(trim($p3)) . '</p>' . "\n";
                }
            } elseif (count($sentences) >= 2) {
                $half = (int) ceil(count($sentences) / 2);
                $p1   = implode(' ', array_slice($sentences, 0, $half));
                $p2   = implode(' ', array_slice($sentences, $half));
                $html .= '<p>' . e(trim($p1)) . '</p>' . "\n";
                if (strlen(trim($p2)) > 10) {
                    $html .= '<p>' . e(trim($p2)) . '</p>' . "\n";
                }
            } else {
                $html .= '<p>' . e($plainText) . '</p>' . "\n";
            }
        } else {
            // Fallback: gunakan judul sebagai konten minimal
            $html = '<p>' . e($excerpt) . '</p>';
        }

        return trim($html);
    }

    // ─────────────────────────────────────────────────────────────
    // Internal: Ekstraksi gambar
    // ─────────────────────────────────────────────────────────────

    private function extractImage(SimpleXMLElement $item): ?string
    {
        $namespaces = $item->getNamespaces(true);

        // 1. <enclosure> — CNN Indonesia, beberapa feed
        if (isset($item->enclosure)) {
            $url  = trim((string) ($item->enclosure['url'] ?? ''));
            $type = trim((string) ($item->enclosure['type'] ?? ''));
            if ($url !== '' && (str_starts_with($type, 'image/') || $type === '')) {
                if ($this->isValidImageUrl($url)) return $url;
            }
        }

        // 2. media:content (Republika, ANTARA, Sindonews)
        if (isset($namespaces['media'])) {
            $mediaNs = $item->children($namespaces['media']);
            if (isset($mediaNs->content)) {
                $url = trim((string) ($mediaNs->content['url'] ?? ''));
                if ($this->isValidImageUrl($url)) return $url;
            }
            if (isset($mediaNs->thumbnail)) {
                $url = trim((string) ($mediaNs->thumbnail['url'] ?? ''));
                if ($this->isValidImageUrl($url)) return $url;
            }
        }

        // 3. <img> di dalam description atau content:encoded
        $rawHtml = $this->extractRawContent($item);
        if ($rawHtml !== '') {
            if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $rawHtml, $m)) {
                if ($this->isValidImageUrl($m[1])) return $m[1];
            }
        }

        // 4. <image> tag langsung di item (beberapa feed menggunakannya)
        if (isset($item->image)) {
            $url = trim((string) $item->image);
            if ($this->isValidImageUrl($url)) return $url;
        }

        return null;
    }

    private function isValidImageUrl(?string $url): bool
    {
        if (! $url || strlen($url) < 12) return false;
        if (! Str::startsWith($url, ['http://', 'https://'])) return false;
        // Hindari favicon, logo, placeholder kecil
        if (preg_match('/(favicon|logo-icon|site-logo|\.ico$|placeholder)/i', $url)) return false;
        // Cegah SSRF ke IP privat
        if (preg_match('/https?:\/\/(localhost|127\.|10\.|192\.168\.|172\.(1[6-9]|2\d|3[01])\.)/i', $url)) return false;
        if (! filter_var($url, FILTER_VALIDATE_URL)) return false;
        return true;
    }

    // ─────────────────────────────────────────────────────────────
    // Internal: Slug unik
    // ─────────────────────────────────────────────────────────────

    private function makeUniqueSlug(string $base): string
    {
        if (! Article::where('slug', $base)->exists()) {
            return $base;
        }
        // Tambah suffix acak pendek
        return $base . '-' . Str::random(5);
    }
}
