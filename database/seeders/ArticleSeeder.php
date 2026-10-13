<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ArticleSeeder — idempotent (skip jika slug sudah ada).
 * Semua artikel adalah DATA DEMO untuk keperluan pengembangan NewsXpaper.
 */
class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        if (! $admin) {
            $this->command->warn('Tidak ada user admin. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $cats = Category::all()->keyBy('slug');
        $inserted = 0;
        $skipped  = 0;

        foreach ($this->articles() as $row) {
            $cat = $cats->get($row['kategori']);
            if (! $cat) {
                $this->command->warn("Kategori '{$row['kategori']}' tidak ditemukan, dilewati.");
                continue;
            }

            $slug = Str::slug($row['title']);

            // Idempotent: skip jika slug sudah ada
            if (Article::where('slug', $slug)->exists()) {
                $skipped++;
                continue;
            }

            $createdAt = now()
                ->subDays($row['days_ago'] ?? rand(1, 90))
                ->subHours(rand(0, 23));

            Article::create([
                'category_id' => $cat->id,
                'user_id'     => $admin->id,
                'title'       => $row['title'],
                'slug'        => $slug,
                'content'     => $row['content'],
                'image'       => $row['image'],
                'status'      => 'published',
                'views'       => $row['views'] ?? rand(100, 8000),
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ]);
            $inserted++;
        }

        $this->command->info("ArticleSeeder: {$inserted} artikel baru, {$skipped} dilewati (sudah ada).");

        // Tambah komentar contoh pada artikel yang belum punya komentar
        $users   = User::all();
        $contoh  = [
            'Artikel yang sangat informatif dan mudah dipahami.',
            'Terima kasih telah berbagi informasi yang berguna ini.',
            'Perspektif yang menarik. Perlu dikaji lebih lanjut.',
            'Sangat relevan dengan kondisi terkini. Setuju dengan analisisnya.',
            'Informasi yang bermanfaat. Semoga terus konsisten melaporkan.',
        ];
        Article::whereDoesntHave('comments')->take(20)->get()
            ->each(function ($article, $i) use ($users, $contoh) {
                Comment::create([
                    'article_id' => $article->id,
                    'user_id'    => $users->random()->id,
                    'body'       => $contoh[$i % count($contoh)],
                ]);
            });
    }

    private function articles(): array
    {
        return [

            // ══════════════════════════════════════════════════════
            // NASIONAL — 8 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'nasional',
                'title'    => 'Pemerintah Percepat Program Swasembada Pangan Nasional',
                'image'    => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&q=80',
                'views'    => 5420,
                'days_ago' => 2,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Pemerintah resmi mempercepat program swasembada pangan nasional melalui paket kebijakan terpadu yang mencakup modernisasi pertanian, penguatan infrastruktur irigasi, dan pemberdayaan petani di seluruh pelosok Indonesia. Program ini menargetkan peningkatan produksi beras, jagung, dan kedelai secara signifikan dalam tiga tahun ke depan.</p>
<h3>Strategi Percepatan</h3>
<p>Kementerian Pertanian menetapkan tiga strategi utama: pertama, perluasan lahan pertanian produktif melalui optimalisasi lahan tidur; kedua, penerapan teknologi pertanian presisi berbasis data dan kecerdasan buatan; ketiga, penguatan sistem distribusi pupuk bersubsidi agar tepat sasaran kepada petani kecil.</p>
<p>Program cetak sawah baru juga digalakkan di pulau-pulau luar Jawa, khususnya Kalimantan dan Papua, yang memiliki potensi lahan pertanian yang sangat besar namun belum dioptimalkan secara penuh. Pemerintah mengalokasikan anggaran khusus untuk mendukung program ini.</p>
<h3>Peran Teknologi</h3>
<p>Drone pertanian, sensor tanah pintar, dan sistem irigasi tetes berbasis IoT mulai diperkenalkan kepada kelompok tani di berbagai provinsi. Teknologi ini diharapkan dapat memangkas penggunaan air hingga 40 persen sekaligus meningkatkan produktivitas lahan secara keseluruhan.</p>
<p>Pemerintah daerah diminta berperan aktif mendampingi petani dalam adaptasi teknologi baru ini. Program pelatihan intensif diselenggarakan di balai penyuluhan pertanian yang tersebar di seluruh kabupaten dan kota.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Infrastruktur Jalan Tol Trans-Sumatera Rampung 80 Persen',
                'image'    => 'https://images.unsplash.com/photo-1505118380757-91f5f5632de0?w=800&q=80',
                'views'    => 4180,
                'days_ago' => 5,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Pembangunan Jalan Tol Trans-Sumatera telah mencapai progres 80 persen dari total panjang yang direncanakan. Mega proyek konektivitas nasional ini diyakini akan menjadi katalis pertumbuhan ekonomi di seluruh pulau Sumatera ketika rampung sepenuhnya.</p>
<h3>Ruas yang Sudah Beroperasi</h3>
<p>Beberapa ruas strategis sudah beroperasi penuh dan melayani jutaan pengguna setiap bulannya. Waktu tempuh antarkota di Sumatera memendek drastis, yang berpengaruh positif terhadap biaya logistik dan harga barang di tingkat konsumen akhir.</p>
<p>Ruas-ruas yang masih dalam pengerjaan diprioritaskan untuk diselesaikan sesuai jadwal. Kendala pembebasan lahan di beberapa titik sudah diselesaikan melalui mekanisme musyawarah dan kompensasi yang adil bagi masyarakat terdampak.</p>
<h3>Dampak Ekonomi</h3>
<p>Studi menunjukkan bahwa setiap rupiah yang diinvestasikan dalam infrastruktur jalan tol memberikan multiplier effect ekonomi yang signifikan. Kawasan-kawasan industri baru bermunculan di sepanjang koridor tol, menyerap tenaga kerja lokal dan mendorong diversifikasi ekonomi daerah.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Bencana Alam Melanda Tiga Provinsi, Ribuan Warga Mengungsi',
                'image'    => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=800&q=80',
                'views'    => 7820,
                'days_ago' => 1,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Bencana banjir dan longsor yang dipicu hujan ekstrem melanda tiga provinsi secara bersamaan, memaksa ribuan warga meninggalkan rumah mereka dan mengungsi ke tempat yang lebih aman. Pemerintah pusat dan daerah segera mengerahkan seluruh sumber daya untuk penanganan darurat.</p>
<h3>Penanganan Darurat</h3>
<p>BNPB mengaktifkan posko darurat dan mengerahkan personel SAR dari berbagai daerah. Bantuan logistik berupa makanan, air bersih, selimut, dan obat-obatan terus mengalir ke lokasi pengungsian. Helikopter dikerahkan untuk menjangkau daerah yang terisolir akibat jalan terputus.</p>
<p>Tenaga medis dan psikolog juga dikerahkan untuk menangani warga yang mengalami luka fisik maupun trauma psikologis akibat kejadian yang tiba-tiba ini. Layanan kesehatan gratis dibuka di seluruh titik pengungsian.</p>
<h3>Pemulihan Jangka Panjang</h3>
<p>Pemerintah berkomitmen untuk tidak sekadar memberikan bantuan darurat, tetapi juga memastikan pemulihan jangka panjang yang berkelanjutan. Rencana relokasi warga dari daerah rawan bencana mulai disiapkan bersama pemerintah daerah setempat.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Angkatan Kerja Muda Indonesia Capai Angka Tertinggi',
                'image'    => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=800&q=80',
                'views'    => 3650,
                'days_ago' => 8,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Data Badan Pusat Statistik menunjukkan jumlah angkatan kerja usia muda Indonesia mencapai angka tertinggi sepanjang sejarah, sebuah fenomena demografis yang dikenal sebagai bonus demografi. Fenomena ini membawa peluang besar sekaligus tantangan yang harus dikelola dengan kebijakan yang tepat.</p>
<h3>Peluang Bonus Demografi</h3>
<p>Dengan jumlah penduduk usia produktif yang melimpah, Indonesia memiliki potensi pertumbuhan ekonomi yang luar biasa jika sumber daya manusia ini dapat dibekali dengan keterampilan yang relevan. Investasi dalam pendidikan vokasi dan pelatihan berbasis industri menjadi sangat krusial.</p>
<p>Ekosistem startup dan ekonomi digital menjadi salah satu sektor yang paling banyak menyerap tenaga kerja muda. Jutaan lapangan kerja baru tercipta di sektor ini dalam beberapa tahun terakhir, meski masih perlu perluasan ke sektor-sektor lainnya.</p>
<h3>Kebijakan Ketenagakerjaan</h3>
<p>Kementerian Ketenagakerjaan meluncurkan berbagai program peningkatan kompetensi yang disesuaikan dengan kebutuhan industri masa kini. Kerja sama dengan dunia usaha diperkuat untuk memastikan setiap program pelatihan benar-benar menghasilkan tenaga kerja yang siap pakai.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Revisi Undang-Undang Cipta Kerja Masuk Agenda DPR',
                'image'    => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=800&q=80',
                'views'    => 4920,
                'days_ago' => 12,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Revisi Undang-Undang Cipta Kerja resmi masuk dalam daftar prioritas Program Legislasi Nasional tahun ini. Revisi ini merespons berbagai masukan dari kalangan pekerja, pengusaha, akademisi, dan masyarakat sipil yang menginginkan penyempurnaan regulasi ketenagakerjaan yang lebih berkeadilan.</p>
<h3>Poin-Poin Revisi</h3>
<p>Beberapa poin kritis yang akan direvisi mencakup ketentuan pesangon, status hubungan kerja pekerja kontrak, perlindungan pekerja di platform digital, dan mekanisme penyelesaian perselisihan hubungan industrial yang lebih cepat dan murah.</p>
<p>Serikat pekerja dan asosiasi pengusaha sepakat bahwa kepastian hukum yang jelas dan adil bagi semua pihak adalah kunci hubungan industrial yang harmonis dan produktif.</p>
<h3>Proses Legislasi</h3>
<p>Pembahasan akan dilakukan secara terbuka dengan melibatkan seluruh pemangku kepentingan dalam forum tripartit. DPR berkomitmen untuk menyelesaikan pembahasan dalam tenggat waktu yang ditetapkan tanpa mengorbankan kualitas substansi regulasi.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Program Vaksinasi Nasional Capai Cakupan 90 Persen',
                'image'    => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&q=80',
                'views'    => 5340,
                'days_ago' => 15,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Program vaksinasi nasional berhasil mencapai cakupan 90 persen dari target populasi, sebuah pencapaian yang menempatkan Indonesia di antara negara-negara dengan program imunisasi terbaik di dunia. Keberhasilan ini merupakan buah kerja keras ribuan tenaga kesehatan di seluruh pelosok negeri.</p>
<h3>Strategi Jangkauan</h3>
<p>Untuk menjangkau masyarakat di daerah terpencil, pemerintah menggunakan pendekatan door-to-door yang melibatkan kader kesehatan dan tokoh masyarakat setempat. Vaksin mobile unit dioperasikan untuk mengakses wilayah-wilayah yang jauh dari fasilitas kesehatan permanen.</p>
<p>Kerja sama dengan pemimpin agama dan tokoh adat sangat signifikan dalam mengatasi hesitancy dan meningkatkan kepercayaan masyarakat terhadap program vaksinasi.</p>
<h3>Dampak Kesehatan Masyarakat</h3>
<p>Data epidemiologi menunjukkan penurunan drastis angka kesakitan dan kematian akibat penyakit yang dapat dicegah dengan vaksin. Sistem kesehatan nasional menjadi lebih tangguh dan kapasitasnya dapat dialihkan untuk menangani penyakit lainnya.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Digitalisasi Arsip Nasional Lindungi Warisan Sejarah Bangsa',
                'image'    => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=800&q=80',
                'views'    => 2180,
                'days_ago' => 22,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Arsip Nasional Republik Indonesia meluncurkan program digitalisasi besar-besaran yang bertujuan menyelamatkan dan memperluas akses terhadap jutaan dokumen bersejarah yang selama ini tersimpan dalam bentuk fisik. Program ini merupakan bagian dari upaya pelestarian warisan budaya bangsa di era digital.</p>
<h3>Proses Digitalisasi</h3>
<p>Dengan menggunakan teknologi pemindaian resolusi tinggi dan sistem manajemen arsip digital terkini, dokumen-dokumen berumur ratusan tahun kini dapat diakses oleh peneliti, pelajar, dan masyarakat umum melalui portal online yang tersedia 24 jam.</p>
<p>Algoritma kecerdasan buatan diterapkan untuk melakukan indexing dan tagging konten secara otomatis, memudahkan pencarian dan penelusuran lintas dokumen yang sebelumnya membutuhkan waktu berhari-hari jika dilakukan secara manual.</p>
<h3>Akses Publik</h3>
<p>Sistem akses publik yang baru memungkinkan siapapun mengakses koleksi arsip digital dari manapun di seluruh dunia. Ini membuka peluang penelitian sejarah yang sebelumnya terbatas hanya bagi mereka yang dapat hadir langsung ke gedung arsip.</p>',
            ],
            [
                'kategori' => 'nasional',
                'title'    => 'Kereta Api Cepat Perkotaan Mulai Diuji Coba di Empat Kota',
                'image'    => 'https://images.unsplash.com/photo-1474487548417-781cb6d646b4?w=800&q=80',
                'views'    => 6120,
                'days_ago' => 18,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Uji coba kereta api ringan perkotaan dimulai secara bersamaan di empat kota besar sebagai bagian dari program transformasi transportasi publik nasional. Sistem angkutan massal ini diharapkan dapat mengurangi kemacetan, polusi udara, dan ketergantungan masyarakat pada kendaraan pribadi.</p>
<h3>Spesifikasi Teknis</h3>
<p>Kereta yang diuji coba menggunakan teknologi propulsi listrik yang ramah lingkungan. Kapasitas setiap rangkaian dapat mengangkut ratusan penumpang dan beroperasi dengan frekuensi tinggi untuk memastikan waktu tunggu yang minimal bagi pengguna.</p>
<p>Sistem tiket terintegrasi yang terhubung dengan moda transportasi lain memudahkan perpindahan penumpang antar moda tanpa perlu membeli tiket terpisah, sebuah kemudahan yang sangat dinantikan oleh komuter perkotaan.</p>
<h3>Antusiasme Warga</h3>
<p>Antusiasme warga yang tinggi terhadap layanan baru ini tercermin dari padatnya penumpang sejak hari pertama uji coba. Pemerintah berkomitmen untuk terus meningkatkan kualitas layanan dan memperluas jangkauan jaringan berdasarkan feedback dari pengguna nyata.</p>',
            ],

            // ══════════════════════════════════════════════════════
            // INTERNASIONAL — 7 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'internasional',
                'title'    => 'Perundingan Damai Kawasan Timur Tengah Masuki Babak Baru',
                'image'    => 'https://images.unsplash.com/photo-1492892132812-a00a8b245c45?w=800&q=80',
                'views'    => 5890,
                'days_ago' => 3,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Perundingan damai di kawasan Timur Tengah memasuki babak paling krusial setelah mediator internasional berhasil mempertemukan semua pihak yang bersengketa dalam sesi negosiasi intensif. Komunitas internasional menyambut perkembangan ini dengan harapan besar sekaligus kehati-hatian yang wajar.</p>
<h3>Isu-Isu Kritis</h3>
<p>Beberapa isu kritis yang masih menjadi titik perdebatan mencakup masalah perbatasan wilayah, status kota-kota strategis, hak pengungsi untuk kembali, dan mekanisme jaminan keamanan bagi semua pihak. Setiap isu memiliki kompleksitas historis dan politis yang sangat dalam.</p>
<p>Para mediator menggunakan pendekatan step-by-step yang berfokus pada isu-isu yang lebih mudah diselesaikan terlebih dahulu, untuk membangun kepercayaan dan momentum menuju penyelesaian isu-isu yang lebih sulit.</p>
<h3>Peran Komunitas Internasional</h3>
<p>Berbagai negara dan organisasi internasional menyatakan komitmen untuk mendukung proses perdamaian, termasuk memberikan bantuan kemanusiaan dan rekonstruksi ekonomi jika kesepakatan damai berhasil dicapai.</p>',
            ],
            [
                'kategori' => 'internasional',
                'title'    => 'Ekonomi Global Diproyeksikan Tumbuh Moderat Tahun Ini',
                'image'    => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80',
                'views'    => 4230,
                'days_ago' => 6,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Dana Moneter Internasional memperbarui proyeksi pertumbuhan ekonomi global untuk tahun ini menjadi lebih moderat dibanding prediksi sebelumnya. Berbagai faktor ketidakpastian geopolitik dan perlambatan di beberapa ekonomi utama menjadi penyebab revisi ini.</p>
<h3>Faktor Penekan</h3>
<p>Ketegangan perdagangan antara beberapa kekuatan ekonomi besar, inflasi yang masih persisten di sejumlah negara maju, dan dampak perubahan iklim terhadap produktivitas pertanian global menjadi faktor-faktor yang menekan proyeksi pertumbuhan.</p>
<p>Namun demikian, kemajuan teknologi yang pesat dan pertumbuhan kelas menengah di negara-negara berkembang memberikan dorongan positif yang cukup signifikan untuk mencegah perlambatan yang lebih dalam.</p>
<h3>Prospek Asia Tenggara</h3>
<p>Asia Tenggara, termasuk Indonesia, diproyeksikan tumbuh di atas rata-rata global, didorong oleh konsumsi domestik yang kuat, investasi asing yang terus mengalir, dan momentum digitalisasi ekonomi yang belum berhenti.</p>',
            ],
            [
                'kategori' => 'internasional',
                'title'    => 'Konferensi Iklim Global Hasilkan Perjanjian Bersejarah',
                'image'    => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80',
                'views'    => 6750,
                'days_ago' => 9,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Konferensi Perubahan Iklim PBB menghasilkan perjanjian bersejarah yang disebut-sebut sebagai yang paling ambisius dan komprehensif sejak Perjanjian Paris. Semua negara peserta sepakat untuk mempercepat transisi dari bahan bakar fosil dan meningkatkan komitmen pengurangan emisi karbon.</p>
<h3>Komitmen Utama</h3>
<p>Negara-negara maju berkomitmen meningkatkan pendanaan iklim untuk membantu negara berkembang dalam transisi energi dan adaptasi terhadap dampak perubahan iklim yang sudah terjadi. Jumlah komitmen yang dijanjikan melampaui angka yang pernah disepakati sebelumnya.</p>
<p>Mekanisme verifikasi yang lebih ketat juga disepakati untuk memastikan bahwa komitmen yang dibuat benar-benar diimplementasikan, bukan sekadar janji tanpa realisasi yang nyata.</p>
<h3>Reaksi Aktivis Lingkungan</h3>
<p>Meski menyambut positif, sejumlah kelompok aktivis lingkungan mengingatkan bahwa perjanjian di atas kertas tidak cukup. Yang dibutuhkan adalah tindakan nyata dan konsisten, serta mekanisme akuntabilitas yang dapat memastikan setiap negara memenuhi janjinya.</p>',
            ],
            [
                'kategori' => 'internasional',
                'title'    => 'Teknologi AI Generatif Ubah Lanskap Industri Global',
                'image'    => 'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=800&q=80',
                'views'    => 8140,
                'days_ago' => 4,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Teknologi kecerdasan buatan generatif terus mengubah lanskap industri secara global dengan kecepatan yang mengejutkan banyak pihak. Hampir tidak ada sektor yang tidak tersentuh oleh gelombang transformasi ini, dari manufaktur, layanan keuangan, kesehatan, hingga media dan hiburan.</p>
<h3>Transformasi di Berbagai Sektor</h3>
<p>Di sektor keuangan, AI generatif digunakan untuk analisis risiko yang lebih akurat dan personalisasi layanan nasabah. Di bidang kesehatan, teknologi ini mempercepat penemuan obat dan membantu diagnosis penyakit dengan tingkat akurasi yang mendekati kemampuan dokter spesialis berpengalaman.</p>
<p>Di industri kreatif, AI generatif memunculkan perdebatan sengit tentang hak cipta, kreativitas, dan masa depan para seniman dan penulis. Regulasi yang mengimbangi inovasi dengan perlindungan hak-hak manusia menjadi sangat mendesak.</p>
<h3>Tantangan Regulasi Global</h3>
<p>Berbagai negara sedang berlomba menyusun regulasi AI yang komprehensif. Tantangannya adalah bagaimana menciptakan regulasi yang cukup fleksibel untuk tidak menghambat inovasi, namun cukup ketat untuk mencegah penyalahgunaan yang dapat merugikan masyarakat luas.</p>',
            ],
            [
                'kategori' => 'internasional',
                'title'    => 'Krisis Air Bersih Ancam Ratusan Juta Orang di Benua Afrika',
                'image'    => 'https://images.unsplash.com/photo-1433888104776-2c143b77d748?w=800&q=80',
                'views'    => 5210,
                'days_ago' => 11,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Laporan PBB terbaru mengungkapkan bahwa krisis air bersih di Afrika sub-Sahara terus memburuk, mengancam ratusan juta orang dengan risiko kesehatan serius dan memperparah kemiskinan yang sudah ada. Perubahan iklim menjadi faktor penguat yang mempercepat degradasi sumber daya air di kawasan ini.</p>
<h3>Skala Krisis</h3>
<p>Tingkat akses air bersih di banyak wilayah masih sangat rendah, memaksa jutaan keluarga mengandalkan sumber air yang terkontaminasi. Penyakit berbasis air seperti diare, kolera, dan tifus masih menjadi penyebab kematian utama terutama di kalangan anak-anak.</p>
<p>Perempuan dan anak-anak perempuan menanggung beban terberat dari krisis ini, karena mereka yang umumnya bertugas mengambil air dari sumber jauh. Waktu yang dihabiskan untuk kegiatan ini mengurangi kesempatan mereka untuk mengakses pendidikan dan peluang ekonomi.</p>
<h3>Solusi yang Ditawarkan</h3>
<p>Berbagai organisasi internasional bekerja sama dengan pemerintah lokal untuk mengimplementasikan solusi yang berkelanjutan, mulai dari pembangunan sistem penyulingan air bertenaga surya hingga program konservasi dan rehabilitasi daerah aliran sungai.</p>',
            ],
            [
                'kategori' => 'internasional',
                'title'    => 'Perdagangan Bebas ASEAN Capai Rekor Nilai Transaksi Baru',
                'image'    => 'https://images.unsplash.com/photo-1487958449943-2429e8be8625?w=800&q=80',
                'views'    => 3850,
                'days_ago' => 16,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Nilai transaksi perdagangan dalam kerangka ASEAN Free Trade Area mencapai rekor baru pada tahun ini, mencerminkan semakin eratnya integrasi ekonomi kawasan Asia Tenggara. Tren positif ini didorong oleh pemulihan pascapandemi dan peningkatan investasi intra-ASEAN yang signifikan.</p>
<h3>Sektor Pendorong</h3>
<p>Perdagangan produk manufaktur elektronik, otomotif, dan produk pertanian olahan menjadi pendorong utama peningkatan nilai transaksi. Penyederhanaan prosedur kepabeanan yang disepakati bersama memperlancar arus barang antar negara anggota.</p>
<p>Platform perdagangan digital terus tumbuh sebagai kanal baru yang menghubungkan pelaku usaha kecil dan menengah dari berbagai negara ASEAN secara langsung, mengurangi ketergantungan pada perantara tradisional.</p>
<h3>Tantangan Ke Depan</h3>
<p>Meski tren positif, masih terdapat kesenjangan dalam pembangunan ekonomi antara negara-negara anggota ASEAN. Narrowing the gap ini menjadi agenda penting yang membutuhkan komitmen bersama dan alokasi sumber daya yang adil.</p>',
            ],
            [
                'kategori' => 'internasional',
                'title'    => 'Hubungan Diplomatik Indonesia dan Negara-Negara Afrika Menguat',
                'image'    => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80',
                'views'    => 2920,
                'days_ago' => 24,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Indonesia secara aktif memperkuat hubungan diplomatik dan ekonomi dengan negara-negara Afrika melalui rangkaian kunjungan diplomatik tingkat tinggi dan penandatanganan berbagai perjanjian kerja sama bilateral. Strategi ini mencerminkan visi Indonesia sebagai kekuatan global yang semakin signifikan.</p>
<h3>Area Kerja Sama</h3>
<p>Kerja sama difokuskan pada beberapa bidang strategis: transfer teknologi pertanian, pengembangan infrastruktur, pendidikan vokasi, dan perdagangan produk-produk unggulan Indonesia. Sejumlah perusahaan Indonesia sudah mulai berinvestasi di berbagai negara Afrika.</p>
<p>Program beasiswa bagi mahasiswa Afrika untuk belajar di universitas-universitas terkemuka di Indonesia juga diperluas, membangun fondasi hubungan people-to-people yang lebih kuat untuk jangka panjang.</p>
<h3>Potensi Pasar</h3>
<p>Benua Afrika dengan populasi yang terus berkembang dan kelas menengah yang tumbuh pesat merupakan pasar yang sangat potensial bagi produk-produk Indonesia. Meningkatkan kehadiran di pasar Afrika adalah langkah strategis yang akan memberikan manfaat jangka panjang.</p>',
            ],

            // ══════════════════════════════════════════════════════
            // PENDIDIKAN — 7 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'pendidikan',
                'title'    => 'Kurikulum Merdeka Belajar Diterapkan di Seluruh Sekolah',
                'image'    => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80',
                'views'    => 5670,
                'days_ago' => 4,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Kurikulum Merdeka Belajar resmi diterapkan di seluruh jenjang pendidikan dasar dan menengah di Indonesia. Implementasi penuh ini menandai transformasi terbesar sistem pendidikan nasional dalam dua dekade terakhir, berfokus pada pengembangan kompetensi holistik dan karakter siswa.</p>
<h3>Filosofi Kurikulum Baru</h3>
<p>Berbeda dengan kurikulum sebelumnya yang lebih berfokus pada pengetahuan hafalan, Kurikulum Merdeka mendorong pembelajaran berbasis proyek, eksplorasi, dan pengembangan kemampuan berpikir kritis. Siswa diberikan kebebasan untuk memilih topik yang diminati dalam batas-batas tertentu.</p>
<p>Guru berperan sebagai fasilitator pembelajaran yang membimbing dan mendampingi siswa dalam proses penemuan pengetahuan, bukan sekadar mentransfer informasi dari buku ke kepala siswa.</p>
<h3>Tantangan Implementasi</h3>
<p>Tantangan terbesar adalah kesiapan guru untuk beradaptasi dengan pendekatan pedagogis yang berbeda secara fundamental. Program pelatihan guru yang masif dan berkelanjutan menjadi kunci keberhasilan implementasi kurikulum baru ini di lapangan.</p>',
            ],
            [
                'kategori' => 'pendidikan',
                'title'    => 'Universitas Indonesia Masuk 50 Besar Asia Versi QS Ranking',
                'image'    => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=800&q=80',
                'views'    => 4890,
                'days_ago' => 7,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Universitas Indonesia berhasil menembus peringkat 50 besar Asia versi QS World University Rankings, sebuah capaian bersejarah yang mencerminkan kemajuan signifikan dalam kualitas penelitian, pengajaran, dan reputasi akademis institusi ini.</p>
<h3>Faktor Pendorong</h3>
<p>Peningkatan peringkat ini didorong oleh beberapa faktor: meningkatnya jumlah publikasi penelitian di jurnal internasional bereputasi, penguatan kerja sama dengan universitas kelas dunia, dan peningkatan rasio dosen-mahasiswa yang signifikan dalam beberapa tahun terakhir.</p>
<p>Program doktoral dan pasca-doktoral yang semakin kompetitif berhasil menarik peneliti berbakat dari dalam dan luar negeri, menciptakan lingkungan akademis yang lebih dinamis dan produktif.</p>
<h3>Target Ke Depan</h3>
<p>Rektor menyatakan bahwa pencapaian ini bukan titik akhir melainkan pemantik semangat untuk terus meningkatkan standar. Target 25 besar Asia dalam lima tahun ke depan telah ditetapkan dengan peta jalan yang terinci dan ambisius.</p>',
            ],
            [
                'kategori' => 'pendidikan',
                'title'    => 'Program Beasiswa Nasional Diperluas untuk Mahasiswa Daerah 3T',
                'image'    => 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=800&q=80',
                'views'    => 6230,
                'days_ago' => 10,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Pemerintah memperluas program beasiswa nasional dengan mengalokasikan kuota khusus yang lebih besar bagi mahasiswa dari daerah Tertinggal, Terdepan, dan Terluar (3T). Langkah afirmasi ini bertujuan mempersempit kesenjangan akses pendidikan tinggi yang masih cukup lebar antara Jawa dan luar Jawa.</p>
<h3>Cakupan Beasiswa</h3>
<p>Beasiswa mencakup biaya kuliah penuh, biaya hidup bulanan, dana penelitian, dan akses ke berbagai program pengembangan kepemimpinan. Penerima juga mendapatkan pendampingan dari mentor akademis dan profesional selama masa studi.</p>
<p>Syarat penerima difokuskan pada prestasi akademis yang baik dengan mempertimbangkan keterbatasan kondisi ekonomi keluarga. Mekanisme seleksi dirancang untuk memastikan keadilan dan transparansi di seluruh proses.</p>
<h3>Dampak Jangka Panjang</h3>
<p>Program ini diharapkan menciptakan efek berganda yang positif. Lulusan penerima beasiswa dari daerah 3T diharapkan kembali dan berkontribusi pada pembangunan daerah asal mereka, memutus siklus kemiskinan dan membuka peluang bagi generasi berikutnya.</p>',
            ],
            [
                'kategori' => 'pendidikan',
                'title'    => 'Literasi Digital Wajib Masuk Kurikulum Sekolah Dasar',
                'image'    => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&q=80',
                'views'    => 3780,
                'days_ago' => 14,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Kementerian Pendidikan mewajibkan literasi digital sebagai mata pelajaran mandiri di jenjang sekolah dasar mulai tahun ajaran baru. Kebijakan ini merespons kebutuhan mendesak untuk mempersiapkan generasi muda menghadapi dunia yang semakin digital sejak dini.</p>
<h3>Materi Literasi Digital</h3>
<p>Kurikulum literasi digital mencakup pemahaman dasar tentang keamanan online, cara mengenali informasi yang benar dan salah (fact-checking), etika berkomunikasi di dunia digital, perlindungan privasi, dan penggunaan teknologi secara positif dan produktif.</p>
<p>Pendekatan pembelajaran dirancang sesuai usia, menggunakan metode bermain dan bercerita untuk anak-anak kelas bawah, dan pendekatan yang lebih analitis untuk kelas atas.</p>
<h3>Kesiapan Guru</h3>
<p>Pelatihan guru secara masif sedang berlangsung untuk memastikan semua pendidik memiliki kompetensi yang diperlukan untuk mengajarkan literasi digital secara efektif. Kemitraan dengan perusahaan teknologi terkemuka juga dilibatkan untuk pengembangan materi dan pelatihan guru.</p>',
            ],
            [
                'kategori' => 'pendidikan',
                'title'    => 'SMK Berbasis Industri Lahirkan Ribuan Teknisi Terampil',
                'image'    => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?w=800&q=80',
                'views'    => 2940,
                'days_ago' => 20,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Model Sekolah Menengah Kejuruan berbasis industri yang dikembangkan pemerintah berhasil melahirkan ribuan teknisi terampil yang langsung terserap oleh dunia industri. Model ini menerapkan sistem pembelajaran dual track di mana siswa menghabiskan separuh waktu belajar di sekolah dan separuh lagi magang di perusahaan mitra.</p>
<h3>Keberhasilan Program</h3>
<p>Tingkat penyerapan lulusan oleh industri sangat tinggi, dengan sebagian besar sudah mendapatkan tawaran pekerjaan bahkan sebelum resmi lulus. Ini berbeda drastis dengan lulusan SMK konvensional yang masih mengalami tingkat pengangguran yang relatif tinggi.</p>
<p>Perusahaan mitra yang terlibat dalam program ini mendapatkan manfaat ganda: mereka mendapatkan calon tenaga kerja yang sudah terlatih sesuai standar perusahaan, sekaligus berkontribusi pada pengembangan SDM nasional.</p>
<h3>Replikasi Nasional</h3>
<p>Keberhasilan model ini mendorong pemerintah untuk mereplikasikannya ke lebih banyak SMK di seluruh Indonesia. Setiap daerah didorong untuk mengembangkan kluster SMK berbasis industri yang sesuai dengan keunggulan ekonomi daerah masing-masing.</p>',
            ],
            [
                'kategori' => 'pendidikan',
                'title'    => 'Pendidikan Anak Usia Dini Dapat Perhatian Lebih dari Pemerintah',
                'image'    => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=800&q=80',
                'views'    => 3120,
                'days_ago' => 26,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Pemerintah meningkatkan perhatian dan anggaran untuk pendidikan anak usia dini, mengakui bahwa investasi di periode ini memberikan return terbesar dalam jangka panjang bagi perkembangan individu dan kemajuan bangsa. Penelitian konsisten menunjukkan bahwa stimulasi yang tepat di usia dini membentuk fondasi kognitif dan emosional yang kuat sepanjang hidup.</p>
<h3>Program PAUD Berkualitas</h3>
<p>Standar kualitas PAUD dinaikkan, mencakup standar kurikulum, kompetensi pendidik, dan fasilitas pembelajaran. Subsidi bagi keluarga kurang mampu untuk mengakses PAUD berkualitas diperluas untuk memastikan tidak ada anak yang tertinggal sejak awal.</p>
<p>Pelatihan pendidik PAUD juga ditingkatkan secara masif. Sebelumnya, banyak pendidik PAUD yang tidak memiliki kualifikasi formal dalam pengembangan anak usia dini, situasi yang kini mulai dibenahi secara sistematis.</p>',
            ],
            [
                'kategori' => 'pendidikan',
                'title'    => 'Riset Kolaboratif Perguruan Tinggi dan Industri Capai Rekor',
                'image'    => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&q=80',
                'views'    => 2510,
                'days_ago' => 32,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Jumlah proyek riset kolaboratif antara perguruan tinggi dan industri mencapai rekor baru tahun ini, mencerminkan semakin eratnya sinergi antara dunia akademis dan dunia usaha. Kolaborasi ini menghasilkan inovasi-inovasi yang langsung dapat diaplikasikan dalam konteks industri nyata.</p>
<h3>Bidang Riset Utama</h3>
<p>Bidang-bidang yang paling aktif meliputi kecerdasan buatan dan machine learning, material baru untuk energi terbarukan, bioteknologi untuk pertanian dan kesehatan, serta teknologi pengolahan limbah yang ramah lingkungan.</p>
<p>Setiap proyek kolaboratif dirancang dengan output yang jelas, baik berupa paten, produk komersial, atau publikasi ilmiah. Hak kekayaan intelektual dibagi secara adil antara universitas dan mitra industri sesuai kontribusi masing-masing.</p>',
            ],

            // ══════════════════════════════════════════════════════
            // KESEHATAN — 7 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'kesehatan',
                'title'    => 'Inovasi Obat Kanker Lokal Berhasil Melewati Uji Klinis Fase II',
                'image'    => 'https://images.unsplash.com/photo-1576671081837-49000212a370?w=800&q=80',
                'views'    => 6890,
                'days_ago' => 3,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Sebuah inovasi obat kanker yang dikembangkan oleh konsorsium peneliti Indonesia berhasil melewati uji klinis fase II dengan hasil yang sangat menjanjikan. Keberhasilan ini merupakan tonggak bersejarah dalam industri farmasi nasional dan membuka harapan baru bagi jutaan pasien kanker di Indonesia.</p>
<h3>Mekanisme Kerja Obat</h3>
<p>Obat ini menggunakan pendekatan targeted therapy yang menyerang sel kanker secara spesifik berdasarkan profil genetik tumor, meminimalkan kerusakan pada sel sehat di sekitarnya. Pendekatan ini terbukti jauh lebih efektif dan memiliki efek samping yang lebih ringan dibandingkan kemoterapi konvensional.</p>
<p>Komponen aktif obat ini sebagian besar bersumber dari bahan alam hayati Indonesia yang selama ini kurang dioptimalkan. Penemuan ini sekaligus membuktikan potensi biodiversitas Indonesia sebagai sumber inovasi farmasi yang belum sepenuhnya tergali.</p>
<h3>Langkah Selanjutnya</h3>
<p>Tim peneliti bersiap untuk memulai uji klinis fase III yang akan melibatkan lebih banyak pasien di berbagai rumah sakit. Regulator obat nasional menyatakan kesiapan untuk mendampingi proses ini dengan jalur akselerasi mengingat kebutuhan medis yang mendesak.</p>',
            ],
            [
                'kategori' => 'kesehatan',
                'title'    => 'Program JKN Diperluas Jangkau Lebih Banyak Layanan Kesehatan',
                'image'    => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&q=80',
                'views'    => 5420,
                'days_ago' => 6,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Program Jaminan Kesehatan Nasional mengumumkan perluasan cakupan layanan yang signifikan, mencakup lebih banyak prosedur medis, obat-obatan, dan terapi yang sebelumnya tidak ditanggung. Perluasan ini menjawab aspirasi jutaan peserta yang selama ini merasa cakupan JKN masih belum memadai.</p>
<h3>Layanan Baru yang Ditanggung</h3>
<p>Di antara layanan baru yang kini ditanggung adalah beberapa prosedur bedah spesialistik, terapi fisik dan rehabilitasi jangka panjang, layanan kesehatan jiwa yang lebih komprehensif, dan akses ke obat-obatan generik berkualitas tinggi yang lebih luas.</p>
<p>Layanan telemedisin juga resmi diintegrasikan ke dalam sistem JKN, memungkinkan peserta berkonsultasi dengan dokter spesialis secara online tanpa harus mengantri berjam-jam di fasilitas kesehatan.</p>
<h3>Keberlanjutan Finansial</h3>
<p>BPJS Kesehatan menyatakan bahwa perluasan cakupan ini didukung oleh perbaikan manajemen keuangan dan penguatan sistem rujukan yang lebih efisien, sehingga tidak membebani keberlanjutan finansial program secara keseluruhan.</p>',
            ],
            [
                'kategori' => 'kesehatan',
                'title'    => 'Stunting pada Anak Turun Signifikan Berkat Program Gizi Nasional',
                'image'    => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80',
                'views'    => 7180,
                'days_ago' => 8,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Prevalensi stunting pada anak balita di Indonesia mengalami penurunan yang signifikan, melampaui target yang ditetapkan pemerintah. Keberhasilan ini merupakan hasil dari program intervensi gizi terpadu yang telah berjalan selama beberapa tahun dengan pendekatan yang lebih terkoordinasi dan berbasis bukti.</p>
<h3>Intervensi Kunci</h3>
<p>Program intervensi mencakup suplementasi gizi spesifik untuk ibu hamil dan balita, edukasi gizi bagi keluarga, akses air bersih dan sanitasi yang lebih baik, serta pemberdayaan posyandu sebagai ujung tombak pemantauan tumbuh kembang anak di tingkat komunitas.</p>
<p>Konvergensi program lintas sektoral menjadi kunci keberhasilan. Masalah stunting tidak bisa diselesaikan hanya oleh sektor kesehatan, melainkan membutuhkan koordinasi dengan sektor pendidikan, pertanian, air bersih, dan perlindungan sosial secara bersamaan.</p>
<h3>Target Ke Depan</h3>
<p>Meski penurunan sudah signifikan, prevalensi stunting masih di atas rata-rata global dan di atas target WHO. Pemerintah berkomitmen untuk terus mengintensifkan program di daerah-daerah dengan angka stunting yang masih tinggi, terutama di provinsi-provinsi kepulauan dan terpencil.</p>',
            ],
            [
                'kategori' => 'kesehatan',
                'title'    => 'Rumah Sakit Rujukan Nasional Diperkuat dengan Alat Medis Terkini',
                'image'    => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=800&q=80',
                'views'    => 3450,
                'days_ago' => 13,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Pemerintah mengalokasikan anggaran besar untuk memperkuat kapasitas rumah sakit rujukan nasional dengan peralatan medis berteknologi terkini. Investasi ini bertujuan meningkatkan standar pelayanan kesehatan tingkat lanjut agar setara dengan negara-negara maju.</p>
<h3>Peralatan Baru</h3>
<p>Di antara peralatan yang baru diadakan adalah mesin MRI generasi terbaru dengan resolusi lebih tinggi, sistem robotik untuk prosedur bedah minimal invasif, dan peralatan radiologi canggih yang memungkinkan diagnosis yang lebih akurat dan lebih cepat.</p>
<p>Sistem manajemen informasi klinis terintegrasi juga diperbarui untuk memastikan data pasien dapat diakses secara real-time oleh tim medis yang terlibat, meningkatkan koordinasi dan mengurangi risiko kesalahan medis.</p>
<h3>Pengembangan SDM Medis</h3>
<p>Pengadaan peralatan canggih diimbangi dengan program pelatihan intensif bagi para dokter spesialis dan tenaga teknis medis. Tanpa SDM yang kompeten, peralatan secanggih apapun tidak akan memberikan manfaat optimal bagi pasien.</p>',
            ],
            [
                'kategori' => 'kesehatan',
                'title'    => 'Kesehatan Mental Kini Menjadi Prioritas dalam Sistem Kesehatan',
                'image'    => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&q=80',
                'views'    => 8240,
                'days_ago' => 5,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Kesehatan mental akhirnya mendapatkan perhatian yang selayaknya dalam sistem kesehatan nasional. Setelah lama diabaikan dan distigmatisasi, pemerintah kini secara eksplisit menempatkan kesehatan mental sebagai prioritas setara dengan kesehatan fisik dalam agenda kesehatan nasional.</p>
<h3>Peningkatan Layanan</h3>
<p>Jumlah puskesmas yang memiliki layanan kesehatan jiwa terus ditingkatkan. Psikolog klinis dan psikiater didistribusikan lebih merata, tidak hanya terkonsentrasi di kota-kota besar. Hotline kesehatan jiwa yang beroperasi 24 jam juga diluncurkan untuk memberikan bantuan awal kepada mereka yang membutuhkan.</p>
<p>Program destigmatisasi kesehatan mental di masyarakat mendapat dukungan dari tokoh-tokoh publik yang berani berbagi pengalaman mereka sendiri dalam menghadapi masalah kesehatan jiwa. Keterbukaan ini terbukti efektif mengurangi rasa malu yang membuat banyak orang enggan mencari bantuan.</p>
<h3>Dampak Pandemi</h3>
<p>Peningkatan kasus gangguan kesehatan mental yang signifikan pasca-pandemi mempercepat kesadaran akan pentingnya perhatian terhadap dimensi ini. Berbagai penelitian menunjukkan bahwa investasi dalam kesehatan mental justru berdampak positif pada produktivitas dan kualitas hidup secara keseluruhan.</p>',
            ],
            [
                'kategori' => 'kesehatan',
                'title'    => 'Riset Stem Cell Indonesia Menuju Terapi Penyakit Degeneratif',
                'image'    => 'https://images.unsplash.com/photo-1532187863486-abf9dbad1b69?w=800&q=80',
                'views'    => 4120,
                'days_ago' => 19,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Riset sel punca (stem cell) yang dikembangkan oleh konsorsium peneliti Indonesia menunjukkan kemajuan yang sangat menjanjikan dalam pengembangan terapi untuk penyakit degeneratif seperti Parkinson, Alzheimer, dan gagal jantung. Penelitian ini berpotensi membuka era baru pengobatan regeneratif di Indonesia.</p>
<h3>Perkembangan Riset</h3>
<p>Tim peneliti berhasil mengembangkan metode kultivasi sel punca yang lebih efisien dan aman. Uji pra-klinis pada model hewan menunjukkan hasil yang konsisten dan menjanjikan, menjadi dasar kuat untuk melangkah ke tahap uji klinis pada manusia.</p>
<p>Indonesia memiliki keunggulan komparatif dalam riset ini karena keberagaman genetik populasinya yang tinggi, menyediakan data yang kaya untuk penelitian yang lebih komprehensif dan relevan secara lokal.</p>',
            ],
            [
                'kategori' => 'kesehatan',
                'title'    => 'Pola Hidup Sehat Gen Z Menjadi Tren yang Semakin Kuat',
                'image'    => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=800&q=80',
                'views'    => 5640,
                'days_ago' => 12,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Generasi Z menunjukkan perhatian yang lebih besar terhadap kesehatan dan pola hidup sehat dibandingkan generasi-generasi sebelumnya. Tren ini tecermin dalam meningkatnya konsumsi makanan bergizi, aktivitas fisik yang lebih teratur, dan kepedulian yang lebih tinggi terhadap kesehatan mental.</p>
<h3>Tren Kesehatan Gen Z</h3>
<p>Olahraga fungsional seperti calisthenics, yoga, dan lari menjadi sangat populer di kalangan anak muda. Gerakan plant-based diet dan mindful eating juga menunjukkan pertumbuhan yang pesat, meski tidak semua mengadopsinya secara penuh.</p>
<p>Media sosial berperan ganda dalam fenomena ini: di satu sisi menjadi sarana penyebaran informasi kesehatan dan komunitas dukungan; di sisi lain, bisa menjadi sumber tekanan yang memperburuk citra diri dan kesehatan mental jika tidak digunakan secara bijak.</p>',
            ],

            // ══════════════════════════════════════════════════════
            // HIBURAN — 6 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'hiburan',
                'title'    => 'Film Indonesia Meraih Penghargaan di Festival Film Internasional',
                'image'    => 'https://images.unsplash.com/photo-1478720568477-152d9b164e26?w=800&q=80',
                'views'    => 9120,
                'days_ago' => 2,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Industri perfilman Indonesia kembali menorehkan prestasi di panggung internasional ketika sebuah film karya sutradara muda Indonesia berhasil meraih penghargaan bergengsi di festival film internasional terkemuka. Pencapaian ini makin mengukuhkan posisi sinema Indonesia dalam percaturan film dunia.</p>
<h3>Film dan Sutradara</h3>
<p>Film yang berjudul demikian mengangkat tema kemanusiaan yang universal namun sangat Indonesia dalam nuansanya. Keindahan visual, kekuatan narasi, dan penampilan akting yang memukau menjadi keunggulan yang diakui oleh para juri internasional.</p>
<p>Sang sutradara yang baru berusia awal tiga puluhan mengaku terinspirasi oleh kisah-kisah nyata dari komunitas marginal yang ia jumpai selama perjalanan penelitiannya sebelum memulai produksi. Autentisitas itulah yang membuat film ini beresonansi secara universal.</p>
<h3>Dampak bagi Industri</h3>
<p>Keberhasilan ini diharapkan membuka lebih banyak pintu bagi sineas Indonesia untuk berkarya di level internasional. Minat investor asing terhadap produksi bersama dengan sineas Indonesia juga meningkat secara signifikan.</p>',
            ],
            [
                'kategori' => 'hiburan',
                'title'    => 'Konser Musisi Legendaris Indonesia Jual Habis 80.000 Tiket',
                'image'    => 'https://images.unsplash.com/photo-1501386761578-eaa54b9cf19d?w=800&q=80',
                'views'    => 12400,
                'days_ago' => 1,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Tiket konser musisi legendaris Indonesia habis terjual dalam hitungan jam setelah dibuka untuk umum, dengan total 80.000 kursi di stadion utama terisi penuh. Fenomena ini membuktikan bahwa musik Indonesia masih memiliki tempat yang sangat istimewa di hati penggemarnya lintas generasi.</p>
<h3>Antusiasme Luar Biasa</h3>
<p>Sistem tiket online sempat mengalami gangguan akibat lonjakan trafik yang jauh melampaui kapasitas server. Ratusan ribu orang mengakses platform secara bersamaan, menciptakan antrean virtual yang panjang. Panitia terpaksa menambah server kapasitas ekstra untuk menangani permintaan yang tak terduga.</p>
<p>Penggemar dari berbagai kota bahkan rela memesan tiket perjalanan dan akomodasi jauh-jauh hari untuk bisa hadir secara langsung. Komunitas penggemar yang sudah ada selama puluhan tahun saling berbagi informasi dan membantu sesama untuk mendapatkan tiket.</p>
<h3>Setlist dan Persiapan</h3>
<p>Panitia menjanjikan pertunjukan yang tidak hanya mengandalkan nostalgia, tetapi juga menyajikan elemen-elemen visual dan audio yang modern untuk memberikan pengalaman yang unforgettable bagi semua generasi penonton.</p>',
            ],
            [
                'kategori' => 'hiburan',
                'title'    => 'Serial Web Original Indonesia Tembus Platform Streaming Global',
                'image'    => 'https://images.unsplash.com/photo-1522869635100-9f4c5e86aa37?w=800&q=80',
                'views'    => 7340,
                'days_ago' => 5,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Serial web original produksi Indonesia berhasil menembus platform streaming global dan langsung mendapatkan respons positif dari penonton internasional. Serial yang mengangkat cerita berlatar budaya dan sejarah Indonesia ini berhasil menjangkau penonton di lebih dari 50 negara.</p>
<h3>Daya Tarik Universal</h3>
<p>Keberhasilan serial ini membuktikan bahwa cerita-cerita yang sangat lokal dan spesifik dapat memiliki daya tarik universal jika dikemas dengan kualitas produksi yang tinggi dan narasi yang kuat. Keindahan visual Indonesia, kekayaan budaya, dan kompleksitas karakter menjadi nilai jual utama di pasar internasional.</p>
<p>Peran media sosial sangat signifikan dalam mendorong popularitas serial ini. Klip-klip pendek yang viral di berbagai platform menginisiasi kepenasaranan penonton dari berbagai belahan dunia.</p>',
            ],
            [
                'kategori' => 'hiburan',
                'title'    => 'Industri Musik Streaming Indonesia Tumbuh 40 Persen',
                'image'    => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800&q=80',
                'views'    => 5780,
                'days_ago' => 9,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Industri musik streaming di Indonesia mencatat pertumbuhan 40 persen secara year-on-year, menjadikannya salah satu pasar musik digital dengan pertumbuhan tercepat di Asia Tenggara. Data ini mencerminkan perubahan fundamental dalam cara masyarakat Indonesia mengonsumsi musik.</p>
<h3>Artis Lokal Mendominasi</h3>
<p>Artis musik Indonesia mendominasi chart dan playlist yang paling banyak didengarkan, mengalahkan artis internasional dalam hal jumlah stream. Ini membuktikan bahwa musisi lokal mampu bersaing dan bahkan lebih resonan bagi pendengar Indonesia dibandingkan musik impor.</p>
<p>Genre yang paling tumbuh mencakup pop urban, indie folk, dan R&B lokal yang terinspirasi elemen-elemen tradisional Indonesia. Fusi budaya ini menciptakan identitas musik yang unik dan otentik.</p>',
            ],
            [
                'kategori' => 'hiburan',
                'title'    => 'Industri Gim Indonesia Berkembang Pesat, Tembus Pasar Global',
                'image'    => 'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=800&q=80',
                'views'    => 6820,
                'days_ago' => 14,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Studio gim Indonesia semakin diperhitungkan di panggung global dengan meluncurkan judul-judul yang berhasil meraih jutaan unduhan dari pengguna di seluruh dunia. Kreativitas developer lokal dalam menggabungkan elemen budaya Indonesia dengan mekanisme gim yang inovatif menjadi daya tarik tersendiri.</p>
<h3>Gim Bertema Indonesia</h3>
<p>Beberapa gim yang mengangkat mitologi, folklore, dan sejarah Indonesia berhasil menjangkau audiens internasional yang selama ini haus akan konten dari perspektif budaya yang berbeda. Karakter-karakter berbasis wayang, kisah Mahabharata versi Nusantara, dan setting kerajaan maritim kuno mendapat sambutan yang antusias.</p>
<p>Ekosistem pengembang gim lokal yang semakin matang, didukung oleh komunitas developer yang aktif dan program akselerasi dari berbagai inkubator, menjadi fondasi pertumbuhan yang berkelanjutan.</p>',
            ],
            [
                'kategori' => 'hiburan',
                'title'    => 'Festival Budaya Nusantara Menarik Wisatawan dari 30 Negara',
                'image'    => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800&q=80',
                'views'    => 4560,
                'days_ago' => 18,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Festival Budaya Nusantara tahunan kembali digelar dan berhasil menarik wisatawan dari lebih dari 30 negara. Event akbar yang menampilkan kekayaan budaya 34 provinsi dalam satu panggung ini telah menjadi salah satu event budaya paling ditunggu-tunggu di kawasan Asia Pasifik.</p>
<h3>Ragam Pertunjukan</h3>
<p>Selama seminggu penuh, pengunjung dapat menyaksikan ratusan pertunjukan seni, mulai dari tarian tradisional, musik gamelan, wayang kulit, fashion show batik, hingga pertunjukan kuliner nusantara. Setiap provinsi membawa pertunjukan terbaik yang merepresentasikan identitas budayanya masing-masing.</p>
<p>Workshop interaktif yang memungkinkan pengunjung mencoba langsung membuat batik, memainkan instrumen musik tradisional, atau belajar memasak masakan khas daerah menjadi salah satu daya tarik terbesar bagi wisatawan mancanegara.</p>',
            ],

            // ══════════════════════════════════════════════════════
            // GAYA HIDUP — 6 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'gaya-hidup',
                'title'    => 'Tren Minimalis Merambah Gaya Hidup Anak Muda Perkotaan',
                'image'    => 'https://images.unsplash.com/photo-1449247709967-d4461a6a6103?w=800&q=80',
                'views'    => 7230,
                'days_ago' => 4,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Gaya hidup minimalis semakin banyak diadopsi oleh anak muda perkotaan Indonesia, tercermin dalam pilihan hunian yang lebih simpel, konsumsi yang lebih selektif, dan prioritas pada pengalaman daripada kepemilikan barang. Gerakan ini sejalan dengan tren global yang mengusung konsep less is more.</p>
<h3>Mengapa Minimalis?</h3>
<p>Di tengah biaya hidup perkotaan yang terus meningkat dan tekanan konsumerisme yang tidak berhenti, minimalis menawarkan ketenangan yang sulit ditemukan. Banyak yang mulai mempertanyakan apakah memiliki lebih banyak barang benar-benar membuat mereka lebih bahagia.</p>
<p>Kesadaran lingkungan juga menjadi pendorong penting. Generasi yang tumbuh dengan informasi tentang krisis iklim dan pencemaran plastik lebih cenderung memilih produk yang berkualitas dan tahan lama daripada barang murah yang cepat rusak dan dibuang.</p>
<h3>Dampak pada Industri</h3>
<p>Tren ini berdampak pada berbagai industri. Industri properti merespons dengan mengembangkan konsep micro-apartment yang fungsional. Industri fashion mengalami pertumbuhan segmen slow fashion. Bisnis jasa rental dan sharing economy semakin populer sebagai alternatif kepemilikan.</p>',
            ],
            [
                'kategori' => 'gaya-hidup',
                'title'    => 'Wisata Kuliner Lokal Jadi Daya Tarik Pariwisata Indonesia',
                'image'    => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80',
                'views'    => 8940,
                'days_ago' => 2,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Wisata kuliner lokal semakin diakui sebagai salah satu daya tarik utama pariwisata Indonesia di mata wisatawan domestik maupun mancanegara. Kekayaan kuliner Nusantara yang luar biasa variatif, dari Sabang sampai Merauke, menjadi keunggulan kompetitif yang sulit ditandingi oleh destinasi wisata manapun.</p>
<h3>Kuliner sebagai Jembatan Budaya</h3>
<p>Makanan bukan sekadar kebutuhan biologis tetapi merupakan ekspresi budaya yang paling langsung dan mudah dipahami. Melalui pengalaman kuliner, wisatawan bisa mengenal karakter, sejarah, dan nilai-nilai suatu daerah dengan cara yang paling intim dan menyenangkan.</p>
<p>Program-program kuliner tour yang dikurasi dengan cermat, menggabungkan warung makan tradisional, pasar tradisional, dan kelas memasak, semakin diminati sebagai pilihan aktivitas wisata yang autentik dan bermakna.</p>',
            ],
            [
                'kategori' => 'gaya-hidup',
                'title'    => 'Berkebun di Rumah Jadi Tren Relaksasi yang Terus Tumbuh',
                'image'    => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&q=80',
                'views'    => 5120,
                'days_ago' => 8,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Aktivitas berkebun di rumah mengalami kebangkitan yang luar biasa, tidak hanya sebagai hobi tetapi sebagai bentuk terapi dan gaya hidup yang lebih terhubung dengan alam. Data menunjukkan peningkatan signifikan penjualan tanaman hias, bibit sayuran, dan perlengkapan berkebun di seluruh Indonesia.</p>
<h3>Manfaat Berkebun</h3>
<p>Penelitian konsisten menunjukkan bahwa berkebun memberikan manfaat psikologis yang nyata. Kontak dengan tanah, pertumbuhan makhluk hidup yang dirawat, dan ritme alamiah yang melekat dalam berkebun terbukti mengurangi stres, meningkatkan mood, dan memberikan rasa pencapaian yang memuaskan.</p>
<p>Berkebun sayuran dan tanaman herbal memiliki daya tarik tambahan: hasil panen yang bisa langsung dikonsumsi. Kepuasan memasak dengan bahan-bahan yang ditanam sendiri memberikan dimensi kebermaknaan yang unik.</p>',
            ],
            [
                'kategori' => 'gaya-hidup',
                'title'    => 'Olahraga Pagi Jadi Ritual Wajib Masyarakat Urban',
                'image'    => 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=800&q=80',
                'views'    => 6780,
                'days_ago' => 6,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Ritual olahraga pagi hari semakin mengakar dalam gaya hidup masyarakat perkotaan Indonesia. Taman-taman kota, jalur sepeda, dan lintasan lari yang semakin banyak dan ramah pengguna berkontribusi besar pada meningkatnya partisipasi olahraga pagi secara konsisten.</p>
<h3>Komunitas Olahraga Tumbuh</h3>
<p>Komunitas lari, sepeda, dan berbagai olahraga outdoor lainnya tumbuh subur di berbagai kota. Komunitas ini tidak hanya menjadi wadah berolahraga bersama, tetapi juga menjadi jaringan sosial yang bermakna di tengah kehidupan kota yang seringkali terasa individualistis.</p>
<p>Aplikasi olahraga dan fitness tracker menjadi teman setia jutaan pengguna yang ingin memantau progres dan termotivasi melalui gamifikasi pencapaian. Kompetisi virtual yang melibatkan peserta dari berbagai kota menambah dimensi kesenangan sosial.</p>',
            ],
            [
                'kategori' => 'gaya-hidup',
                'title'    => 'Kopi Nusantara Mendunia, Warung Kopi Lokal Bersaing Global',
                'image'    => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800&q=80',
                'views'    => 7450,
                'days_ago' => 11,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Kopi Indonesia semakin diakui sebagai salah satu yang terbaik di dunia, mendorong menjamurnya kedai kopi spesialti lokal yang tidak kalah bersaing dengan merek-merek internasional. Gerakan kopi ketiga (third wave coffee) yang menekankan pada origin, proses, dan kualitas biji kopi sangat menguntungkan Indonesia yang memiliki kebun kopi di hampir setiap provinsi.</p>
<h3>Keberagaman Kopi Indonesia</h3>
<p>Dari kopi Gayo Aceh yang earthy dan complex, Toraja yang bold dan chocolatey, Flores yang fruity dan bright, hingga Wamena Papua yang exotic dan rare, Indonesia memiliki keberagaman cita rasa yang membuat para pencinta kopi dunia terpesona.</p>
<p>Barista Indonesia semakin menunjukkan kemampuan di kompetisi internasional, membuktikan bahwa expertise dalam mengolah kopi berkualitas tinggi juga terus berkembang di dalam negeri.</p>',
            ],
            [
                'kategori' => 'gaya-hidup',
                'title'    => 'Perjalanan Backpacker Dalam Negeri Kembali Diminati Anak Muda',
                'image'    => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&q=80',
                'views'    => 8120,
                'days_ago' => 7,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Tren backpacking di dalam negeri kembali menggeliat di kalangan anak muda Indonesia, didorong oleh semakin berkembangnya infrastruktur transportasi, jaringan penginapan budget yang semakin luas, dan tumbuhnya kesadaran untuk mengeksplorasi kekayaan nusantara sebelum menjelajah luar negeri.</p>
<h3>Destinasi Favorit</h3>
<p>Jalur-jalur backpacker favorit yang melintasi berbagai provinsi menawarkan kombinasi petualangan alam, kekayaan budaya, dan keramahan penduduk lokal yang sulit ditemukan di destinasi manapun. Pulau-pulau terpencil di timur Indonesia khususnya semakin banyak dikunjungi setelah koneksi transportasi yang membaik.</p>
<p>Komunitas backpacker yang aktif berbagi informasi di media sosial membantu calon traveler merencanakan perjalanan dengan lebih mudah dan hemat. Tips, rekomendasi penginapan, dan guide lokal saling dibagikan secara gratis dalam semangat komunitas yang kuat.</p>',
            ],

            // ══════════════════════════════════════════════════════
            // LINGKUNGAN — 6 artikel
            // ══════════════════════════════════════════════════════
            [
                'kategori' => 'lingkungan',
                'title'    => 'Mangrove Indonesia Jadi Model Restorasi Pesisir Dunia',
                'image'    => 'https://images.unsplash.com/photo-1473580044384-7ba9967e16a0?w=800&q=80',
                'views'    => 4890,
                'days_ago' => 5,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Program restorasi mangrove Indonesia yang berhasil meregenerasi ratusan ribu hektare hutan bakau yang rusak kini menjadi model yang dipelajari oleh berbagai negara. Keberhasilan ini merupakan kombinasi dari kebijakan pemerintah yang tepat, pelibatan komunitas lokal, dan inovasi teknis yang adaptif.</p>
<h3>Pentingnya Mangrove</h3>
<p>Hutan mangrove bukan sekadar vegetasi pantai biasa. Ekosistem ini adalah penyimpan karbon yang sangat efisien, pelindung pantai dari abrasi dan tsunami, tempat pemijahan ikan yang kaya, dan sumber penghidupan jutaan nelayan. Kehilangan mangrove berarti kehilangan semua fungsi ekologis dan ekonomis tersebut sekaligus.</p>
<p>Restorasi yang berhasil terbukti dapat memulihkan semua fungsi tersebut dalam waktu yang relatif singkat, terutama jika dilakukan dengan memperhatikan kondisi lokal dan melibatkan komunitas sebagai penjaga ekosistem jangka panjang.</p>
<h3>Pelajaran bagi Dunia</h3>
<p>Para delegasi dari berbagai negara mengunjungi lokasi-lokasi restorasi mangrove Indonesia untuk belajar langsung dari pengalaman nyata di lapangan. Metodologi yang dikembangkan Indonesia kini diadaptasi untuk kondisi pesisir di Asia Selatan, Afrika Timur, dan Amerika Tengah.</p>',
            ],
            [
                'kategori' => 'lingkungan',
                'title'    => 'Kebakaran Hutan Menurun Drastis Berkat Sistem Deteksi Dini',
                'image'    => 'https://images.unsplash.com/photo-1523464862212-d6631d073194?w=800&q=80',
                'views'    => 6340,
                'days_ago' => 8,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Angka kebakaran hutan di Indonesia mengalami penurunan yang dramatis dibandingkan beberapa tahun sebelumnya, berkat implementasi sistem deteksi dini berbasis satelit dan kecerdasan buatan yang memungkinkan respons yang jauh lebih cepat sebelum api menyebar luas.</p>
<h3>Teknologi Deteksi Dini</h3>
<p>Sistem yang dikembangkan secara kolaboratif oleh LAPAN, BNPB, dan beberapa universitas mampu mendeteksi titik panas dengan akurasi tinggi dan mengklasifikasikan tingkat risiko dalam hitungan menit. Notifikasi otomatis langsung dikirimkan ke petugas pemadam di lapangan.</p>
<p>Drone surveillance yang berpatroli secara otonom di area-area rawan kebakaran memberikan tambahan lapisan deteksi yang efektif, terutama di wilayah-wilayah yang sulit dijangkau oleh patroli darat.</p>
<h3>Peran Komunitas</h3>
<p>Masyarakat adat dan komunitas lokal yang tinggal di sekitar hutan menjadi mitra penting dalam program pencegahan kebakaran. Pendekatan berbasis kearifan lokal dalam pengelolaan lahan, dikombinasikan dengan teknologi modern, terbukti lebih efektif daripada pendekatan top-down semata.</p>',
            ],
            [
                'kategori' => 'lingkungan',
                'title'    => 'Gerakan Zero Waste Sekolah Berhasil Kurangi Sampah Plastik',
                'image'    => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=800&q=80',
                'views'    => 4210,
                'days_ago' => 12,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Program Zero Waste School yang diimplementasikan di ratusan sekolah di berbagai kota berhasil mengurangi volume sampah plastik secara signifikan. Program ini tidak hanya mengurangi limbah, tetapi juga membentuk kebiasaan dan kesadaran lingkungan yang akan dibawa para siswa sepanjang hidup mereka.</p>
<h3>Implementasi di Sekolah</h3>
<p>Program mencakup penghapusan penggunaan plastik sekali pakai di kantin sekolah, kompetisi kelas dengan sampah paling sedikit, bank sampah yang dikelola siswa, dan integrasi nilai-nilai lingkungan dalam kurikulum pembelajaran sehari-hari.</p>
<p>Siswa menjadi agen perubahan yang efektif. Penelitian menunjukkan bahwa perilaku pro-lingkungan yang dipelajari di sekolah seringkali ditransfer ke rumah, mengubah kebiasaan seluruh keluarga secara bertahap.</p>',
            ],
            [
                'kategori' => 'lingkungan',
                'title'    => 'Transisi Energi Surya Percepat Elektrifikasi Desa Terpencil',
                'image'    => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&q=80',
                'views'    => 5820,
                'days_ago' => 15,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Penggunaan panel surya sebagai solusi energi terbarukan berhasil mempercepat program elektrifikasi desa-desa terpencil yang selama ini tidak terjangkau oleh jaringan listrik PLN. Biaya sistem tenaga surya yang terus turun menjadikannya pilihan yang ekonomis bahkan untuk komunitas kecil.</p>
<h3>Transformasi di Pedesaan</h3>
<p>Kedatangan listrik membawa transformasi yang mendalam bagi masyarakat desa. Anak-anak bisa belajar di malam hari, usaha kecil bisa beroperasi lebih lama, layanan kesehatan bisa menyimpan vaksin dengan aman, dan komunikasi dengan dunia luar menjadi lebih mudah.</p>
<p>Model pembiayaan yang inovatif, termasuk sistem sewa panel surya dan pay-as-you-go, memungkinkan bahkan keluarga yang paling miskin untuk mengakses listrik tanpa harus mengeluarkan uang muka yang besar.</p>',
            ],
            [
                'kategori' => 'lingkungan',
                'title'    => 'Penelitian Terumbu Karang Temukan Spesies Baru di Laut Raja Ampat',
                'image'    => 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=800&q=80',
                'views'    => 7130,
                'days_ago' => 20,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Tim peneliti biologi kelautan berhasil mengidentifikasi beberapa spesies baru yang sebelumnya tidak dikenal ilmu pengetahuan dalam ekspedisi ilmiah di perairan Raja Ampat, Papua Barat. Penemuan ini semakin mengukuhkan status Raja Ampat sebagai salah satu pusat keanekaragaman hayati laut terkaya di dunia.</p>
<h3>Spesies yang Ditemukan</h3>
<p>Di antara spesies baru yang berhasil diidentifikasi terdapat beberapa jenis ikan karang kecil dengan warna yang sangat indah, beberapa spesies nudibranch (siput laut), dan beberapa jenis krustasea yang hidup dalam simbiosis unik dengan karang tertentu.</p>
<p>Setiap penemuan spesies baru bukan hanya penting dari perspektif ilmu pengetahuan, tetapi juga memperkuat argumen untuk perlindungan ekosistem yang menjadi habitat mereka. Spesies yang belum dikenal pun mungkin menyimpan potensi manfaat bagi obat-obatan atau bioteknologi.</p>',
            ],
            [
                'kategori' => 'lingkungan',
                'title'    => 'Polusi Udara di Jakarta Mendorong Inovasi Transportasi Hijau',
                'image'    => 'https://images.unsplash.com/photo-1561731216-c3a4d99437d5?w=800&q=80',
                'views'    => 8560,
                'days_ago' => 3,
                'content'  => '<p><em>[Data Demo] Artikel ini merupakan konten contoh untuk portal berita NewsXpaper.</em></p>
<p>Masalah polusi udara yang kronis di Jakarta mendorong percepatan inovasi dan adopsi solusi transportasi hijau yang belum pernah terjadi sebelumnya. Tekanan dari masyarakat, data kesehatan yang mengkhawatirkan, dan kebijakan pemerintah yang semakin tegas menciptakan ekosistem yang kondusif untuk perubahan.</p>
<h3>Solusi Transportasi Hijau</h3>
<p>Bus listrik sudah mulai beroperasi di beberapa koridor utama TransJakarta dan mendapat respons yang positif dari penumpang. Jaringan pengisian daya untuk kendaraan listrik terus diperluas di berbagai titik strategis kota.</p>
<p>Infrastruktur jalur sepeda yang lebih aman dan nyaman mendorong lebih banyak warga untuk beralih ke moda transportasi non-motoris untuk perjalanan jarak pendek. Integrasi dengan moda transportasi massal lainnya semakin memudahkan kombinasi berbagai moda dalam satu perjalanan.</p>',
            ],
        ];
    }
}
