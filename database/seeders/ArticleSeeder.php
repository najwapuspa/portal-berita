<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $cats = Category::all()->keyBy('slug');

        $data = [
            // ── POLITIK (5 artikel) ──────────────────────────────────
            [
                'kategori' => 'politik',
                'title' => 'Pemerintah Umumkan Kebijakan Baru Tahun Ini',
                'image' => 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=800&q=80',
                'views' => 4821,
                'trending' => true,
                'content' => '<p>Pemerintah resmi mengumumkan paket kebijakan nasional yang akan mulai berlaku pada kuartal pertama tahun ini. Kebijakan mencakup reformasi birokrasi, digitalisasi layanan publik, dan percepatan pembangunan infrastruktur di wilayah terpencil.</p><p>Menteri Koordinator Bidang Politik mengungkapkan bahwa kebijakan ini merupakan tindak lanjut dari evaluasi kinerja pemerintahan selama dua tahun terakhir. Sejumlah indikator menunjukkan perlunya akselerasi di sektor pelayanan masyarakat dan transparansi pengelolaan anggaran daerah.</p><p>Anggaran yang disiapkan mencapai Rp 450 triliun, dengan porsi terbesar dialokasikan untuk sektor pendidikan dan kesehatan. Pemerintah juga memastikan adanya mekanisme pengawasan independen yang melibatkan lembaga masyarakat sipil agar dana tidak disalahgunakan.</p><p>Sejumlah pengamat politik menyambut positif pengumuman ini, namun mengingatkan agar implementasi tidak sekadar jargon. "Yang terpenting adalah konsistensi di lapangan. Kebijakan terbaik sekalipun tidak akan berarti tanpa eksekusi yang solid," ujar Dr. Rahmat Setiawan, pakar kebijakan publik dari Universitas Indonesia.</p>',
            ],
            [
                'kategori' => 'politik',
                'title' => 'Pemilu Daerah Digelar dengan Lancar di Seluruh Provinsi',
                'image' => 'https://images.unsplash.com/photo-1540910419892-4a36d2c3266c?w=800&q=80',
                'views' => 3210,
                'trending' => false,
                'content' => '<p>Pemilihan umum kepala daerah serentak berlangsung aman dan kondusif di 38 provinsi. Komisi Pemilihan Umum (KPU) melaporkan tingkat partisipasi pemilih mencapai 74 persen, meningkat dibanding periode sebelumnya yang hanya 68 persen.</p><p>Petugas keamanan gabungan dari Polri dan TNI disiagakan di seluruh titik rawan untuk memastikan proses pemungutan suara berjalan tertib. Tidak ada laporan insiden keamanan berarti selama hari H pemungutan berlangsung.</p><p>Ketua KPU menyatakan bahwa keberhasilan penyelenggaraan ini merupakan buah dari persiapan matang selama delapan bulan. Sistem penghitungan suara digital yang baru pertama kali diterapkan secara nasional juga berjalan tanpa hambatan teknis berarti.</p><p>Hasil rekapitulasi resmi dijadwalkan diumumkan dalam dua minggu ke depan. Sementara itu, tim pemantau dari lembaga internasional menyatakan proses pemilu Indonesia memenuhi standar demokrasi yang baik.</p>',
            ],
            [
                'kategori' => 'politik',
                'title' => 'Koalisi Partai Besar Sepakat Dukung RUU Reformasi Hukum',
                'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=800&q=80',
                'views' => 2890,
                'trending' => false,
                'content' => '<p>Lima partai besar di parlemen akhirnya mencapai kesepakatan untuk mendukung pengesahan Rancangan Undang-Undang Reformasi Sistem Hukum Nasional. RUU yang telah digodok selama tiga tahun ini diharapkan menjadi tonggak pembaruan hukum terbesar sejak era reformasi.</p><p>Isi utama RUU mencakup pembatasan masa penahanan pra-sidang, penguatan hak-hak terdakwa, dan penerapan sistem peradilan elektronik di seluruh pengadilan negeri. Selain itu, RUU juga mengatur pembentukan Komisi Independen Pengawas Hakim yang memiliki kewenangan lebih luas.</p><p>Fraksi oposisi sempat mempersoalkan beberapa pasal terkait kewenangan Jaksa Agung, namun setelah serangkaian lobi intensif, keberatan tersebut berhasil diakomodasi melalui amendemen pada pasal 47 dan 52.</p><p>Sidang paripurna pengesahan dijadwalkan berlangsung akhir bulan ini. Presiden dikabarkan akan menandatangani undang-undang baru tersebut dalam acara khusus di Istana Negara yang disiarkan secara langsung.</p>',
            ],
            [
                'kategori' => 'politik',
                'title' => 'Startup Lokal Raih Pendanaan Baru dari Investor Asing',
                'image' => 'https://images.unsplash.com/photo-1553484771-371a605b060b?w=800&q=80',
                'views' => 5102,
                'trending' => true,
                'content' => '<p>Sebuah startup teknologi asal Jakarta berhasil menutup putaran pendanaan Seri B senilai 45 juta dolar AS yang dipimpin oleh konsorsium investor dari Singapura dan Tokyo. Ini menjadi salah satu deal terbesar di ekosistem startup Indonesia dalam dua tahun terakhir.</p><p>Perusahaan yang bergerak di bidang solusi logistik berbasis kecerdasan buatan ini akan menggunakan dana tersebut untuk ekspansi ke Vietnam, Thailand, dan Filipina pada semester kedua tahun ini. Mereka juga berencana menambah 200 tenaga kerja baru, terutama di posisi insinyur data dan analis produk.</p><p>CEO perusahaan, Budi Santoso, menyatakan kepercayaan investor asing ini membuktikan bahwa ekosistem teknologi Indonesia telah matang dan siap bersaing di tingkat Asia Tenggara. "Kami tidak sekadar membangun produk, kami membangun ekosistem yang menguntungkan semua pihak," ujarnya dalam konferensi pers.</p><p>Kementerian Investasi menyambut positif perkembangan ini dan menyatakan pemerintah akan terus menciptakan iklim investasi yang kondusif. Regulasi baru terkait perizinan startup digital dijanjikan akan dirampungkan sebelum akhir kuartal ini.</p>',
            ],
            [
                'kategori' => 'politik',
                'title' => 'DPR Setujui Anggaran Pertahanan Naik 18 Persen Tahun Depan',
                'image' => 'https://images.unsplash.com/photo-1568495248636-6432b97bd949?w=800&q=80',
                'views' => 1980,
                'trending' => false,
                'content' => '<p>Dewan Perwakilan Rakyat menyetujui usulan pemerintah untuk menaikkan anggaran pertahanan sebesar 18 persen pada tahun anggaran mendatang. Kenaikan ini merupakan yang terbesar dalam satu dekade terakhir dan mencerminkan prioritas penguatan alutsista nasional.</p><p>Dana tambahan akan difokuskan pada modernisasi armada kapal perang, pengadaan drone tempur generasi terbaru, dan peningkatan kemampuan siber militer. Kementerian Pertahanan juga menganggarkan pelatihan bagi 5.000 prajurit dalam program perang asimetris dan operasi khusus.</p><p>Sejumlah anggota fraksi menyatakan dukungan penuh mengingat dinamika geopolitik kawasan yang semakin kompleks. Namun beberapa pengamat mengingatkan agar transparansi pengadaan tetap dijaga agar tidak menjadi celah korupsi seperti yang terjadi pada periode sebelumnya.</p><p>Menteri Pertahanan menegaskan semua pengadaan akan melalui mekanisme audit ketat oleh BPK dan Komisi Pemberantasan Korupsi. "Setiap rupiah anggaran pertahanan harus dapat dipertanggungjawabkan kepada rakyat," tegasnya di hadapan Komisi I DPR.</p>',
            ],

            // ── EKONOMI (5 artikel) ──────────────────────────────────
            [
                'kategori' => 'ekonomi',
                'title' => 'Harga Bahan Pokok Stabil Menjelang Akhir Bulan',
                'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&q=80',
                'views' => 6340,
                'trending' => true,
                'content' => '<p>Harga sejumlah bahan pokok di pasar tradisional dan ritel modern terpantau stabil memasuki pekan terakhir bulan ini. Badan Pangan Nasional mencatat tidak ada lonjakan harga berarti untuk komoditas utama seperti beras, minyak goreng, gula, dan telur ayam.</p><p>Stabilitas ini disebut sebagai hasil intervensi pemerintah melalui operasi pasar yang telah berjalan selama tiga minggu berturut-turut. Cadangan beras pemerintah saat ini berada di angka aman, yakni 2,1 juta ton, jauh di atas kebutuhan bulanan nasional sebesar 1,3 juta ton.</p><p>Kepala Badan Pangan Nasional mengungkapkan bahwa koordinasi antara Bulog, Kemendag, dan pemerintah daerah berjalan lebih baik dibanding tahun lalu. Sistem pemantauan harga berbasis aplikasi juga membantu identifikasi dini potensi kelangkaan di daerah tertentu.</p><p>Namun demikian, petani di sejumlah sentra produksi mengeluhkan harga di tingkat produsen yang justru stagnan. Mereka meminta pemerintah meninjau ulang kebijakan harga pembelian pemerintah (HPP) agar petani juga ikut menikmati manfaat stabilitas harga di tingkat konsumen.</p>',
            ],
            [
                'kategori' => 'ekonomi',
                'title' => 'Bank Sentral Pertahankan Suku Bunga Acuan di 5,75 Persen',
                'image' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80',
                'views' => 3780,
                'trending' => false,
                'content' => '<p>Rapat Dewan Gubernur Bank Indonesia memutuskan untuk mempertahankan suku bunga acuan BI Rate di level 5,75 persen. Keputusan ini sejalan dengan ekspektasi pasar dan mencerminkan sikap hati-hati di tengah ketidakpastian ekonomi global yang masih tinggi.</p><p>Gubernur BI menyatakan bahwa inflasi domestik saat ini berada dalam kisaran target 2,5±1 persen, sehingga tidak ada tekanan mendesak untuk mengubah arah kebijakan moneter. Nilai tukar rupiah juga dinilai relatif stabil meski tekanan dari penguatan dolar AS masih terasa.</p><p>Kalangan perbankan menyambut baik keputusan ini karena memberikan kepastian bagi perencanaan bisnis jangka pendek. Beberapa bank besar sudah menurunkan suku bunga kredit konsumsi secara selektif dalam upaya mendorong pertumbuhan kredit yang melambat.</p><p>Para ekonom memperkirakan BI baru akan membuka ruang penurunan suku bunga pada kuartal ketiga jika inflasi terus terkendali dan data pertumbuhan ekonomi menunjukkan tren positif. Saat ini proyeksi pertumbuhan PDB Indonesia tahun ini berada di kisaran 5,1—5,3 persen.</p>',
            ],
            [
                'kategori' => 'ekonomi',
                'title' => 'Ekspor Manufaktur RI Tumbuh 12 Persen di Kuartal Pertama',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&q=80',
                'views' => 2640,
                'trending' => false,
                'content' => '<p>Ekspor produk manufaktur Indonesia mencatat pertumbuhan 12 persen secara tahunan pada kuartal pertama. Peningkatan ini terutama didorong oleh lonjakan permintaan produk elektronik, otomotif, dan tekstil dari negara-negara mitra dagang utama di Eropa dan Amerika.</p><p>Badan Pusat Statistik mengungkapkan nilai ekspor manufaktur mencapai 42,8 miliar dolar AS, melampaui target yang ditetapkan sebesar 40 miliar dolar. Produk berbasis nikel dan turunannya masih menjadi primadona dengan kontribusi mencapai 28 persen dari total nilai ekspor.</p><p>Kementerian Perindustrian mengatakan tren positif ini merupakan buah dari kebijakan hilirisasi yang konsisten dijalankan sejak tiga tahun lalu. Investasi di kawasan industri terpadu juga terus meningkat, dengan beberapa perusahaan multinasional memindahkan sebagian lini produksi mereka ke Indonesia.</p><p>Meski begitu, tantangan tetap ada. Biaya logistik yang masih relatif tinggi dibanding negara kompetitor dan ketergantungan pada bahan baku impor menjadi pekerjaan rumah yang perlu segera diatasi agar pertumbuhan ekspor dapat dipertahankan secara berkelanjutan.</p>',
            ],
            [
                'kategori' => 'ekonomi',
                'title' => 'Indeks Saham Gabungan Sentuh Level Tertinggi Sepanjang Masa',
                'image' => 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&q=80',
                'views' => 4510,
                'trending' => true,
                'content' => '<p>Indeks Harga Saham Gabungan (IHSG) di Bursa Efek Indonesia menyentuh level 8.750 pada sesi perdagangan pagi, mencetak rekor tertinggi sepanjang sejarah. Penguatan ini dipicu oleh sentimen positif dari data pertumbuhan ekonomi kuartal pertama yang melampaui ekspektasi.</p><p>Sektor perbankan, energi, dan konsumsi menjadi motor penggerak utama dengan kenaikan rata-rata 2,3 persen dalam sehari. Asing tercatat melakukan pembelian bersih senilai Rp 1,2 triliun, menandai perubahan arah setelah beberapa pekan berturut-turut melakukan penjualan.</p><p>Analis pasar modal menilai momentum ini cukup kuat dan berpotensi berlanjut jika data inflasi dan neraca perdagangan bulan depan tetap solid. "Pasar sedang dalam fase akumulasi yang sehat. Investor institusional mulai membangun posisi jangka panjang," kata Arief Nugroho, Kepala Riset PT Bahana Sekuritas.</p><p>Namun investor diminta tetap waspada terhadap risiko global yang belum sepenuhnya mereda, termasuk potensi eskalasi konflik geopolitik di Timur Tengah yang bisa mempengaruhi harga minyak dan selera risiko investor asing secara keseluruhan.</p>',
            ],
            [
                'kategori' => 'ekonomi',
                'title' => 'UMKM Digital Jadi Tulang Punggung Pemulihan Ekonomi Daerah',
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80',
                'views' => 2190,
                'trending' => false,
                'content' => '<p>Usaha Mikro, Kecil, dan Menengah (UMKM) yang telah bertransformasi ke platform digital menjadi tulang punggung pemulihan ekonomi di daerah-daerah yang terdampak perlambatan ekonomi. Data Kementerian Koperasi dan UKM menunjukkan UMKM digital tumbuh 34 persen dalam setahun terakhir.</p><p>Program digitalisasi UMKM yang digulirkan sejak dua tahun lalu berhasil mendampingi lebih dari 500.000 pelaku usaha untuk masuk ke ekosistem marketplace dan pembayaran digital. Dari jumlah tersebut, 40 persen berhasil meningkatkan omzet lebih dari dua kali lipat.</p><p>Platform e-commerce besar turut berperan dengan menyediakan program pelatihan gratis, kemudahan akses modal usaha, dan fasilitas pengiriman bersubsidi ke seluruh wilayah Indonesia. Kolaborasi ini dinilai lebih efektif ketimbang program bantuan tunai langsung yang tidak menciptakan kemandirian jangka panjang.</p><p>Kendati demikian, masalah konektivitas internet di daerah 3T (Tertinggal, Terdepan, Terluar) masih menjadi hambatan utama. Pemerintah berjanji menuntaskan proyek Palapa Ring pada akhir tahun ini untuk menjamin akses internet yang merata ke seluruh pelosok nusantara.</p>',
            ],

            // ── OLAHRAGA (5 artikel) ─────────────────────────────────
            [
                'kategori' => 'olahraga',
                'title' => 'Tim Nasional Menang Dramatis di Laga Terakhir Kualifikasi',
                'image' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=800&q=80',
                'views' => 8920,
                'trending' => true,
                'content' => '<p>Tim nasional sepak bola Indonesia meraih kemenangan dramatis 3-2 atas tuan rumah dalam laga pamungkas babak kualifikasi zona Asia. Gol penentu kemenangan dicetak oleh striker muda Raka Pratama pada menit ke-89, memastikan Garuda lolos ke putaran berikutnya untuk pertama kalinya dalam 14 tahun.</p><p>Laga berlangsung penuh intensitas sejak menit awal. Indonesia sempat tertinggal 0-1 sebelum membalik keadaan menjadi 2-1 berkat gol bunuh diri lawan dan tendangan bebas akurat kapten tim. Tuan rumah menyamakan kedudukan menjadi 2-2 di menit 75 sebelum Raka memastikan kemenangan dramatis di injury time.</p><p>Pelatih kepala Patrick Kluivert memuji mentalitas juang para pemain yang tidak menyerah meski berada dalam tekanan besar. "Ini bukan hanya tentang teknik dan taktik, ini tentang hati dan keberanian. Anak-anak membuktikan bahwa mereka layak bersaing di level tertinggi Asia," ujarnya berlinang air mata di konferensi pers.</p><p>Ratusan ribu pendukung Garuda yang menonton di layar raksasa seluruh Indonesia meledak dalam kegembiraan begitu peluit akhir berbunyi. Media sosial dibanjiri ucapan selamat, dengan tagar #GarudaLolos menjadi trending topik nomor satu secara global di platform X.</p>',
            ],
            [
                'kategori' => 'olahraga',
                'title' => 'Atlet Bulu Tangkis Indonesia Borong Emas di Kejuaraan Dunia',
                'image' => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&q=80',
                'views' => 7450,
                'trending' => true,
                'content' => '<p>Kontingen bulu tangkis Indonesia tampil gemilang di Kejuaraan Dunia BWF dengan meraih total 4 medali emas, 2 perak, dan 3 perunggu. Prestasi ini menempatkan Indonesia di posisi kedua dalam daftar perolehan medali, hanya kalah dari China yang mengumpulkan 5 emas.</p><p>Pasangan ganda putra Kevin Sanjaya dan Marcus Fernaldi kembali meraih emas setelah melewati perjuangan panjang di babak semifinal dan final. Mereka mengalahkan pasangan unggulan asal Denmark dengan skor 21-19, 19-21, dan 21-17 dalam pertandingan yang berlangsung lebih dari satu jam.</p><p>Kejutan datang dari tunggal putri muda Kiran Ayu, 19 tahun, yang menjadi juara dunia termuda dalam sejarah nomor tunggal putri Indonesia. Kiran mengalahkan petahana asal Korea Selatan dalam tiga game dengan penampilan yang jauh melampaui ekspektasi.</p><p>Ketua PBSI menyatakan hasil ini menjadi modal mental yang kuat menjelang olimpiade tahun depan. Pemusatan latihan nasional akan ditingkatkan intensitasnya, dengan tambahan tenaga pelatih spesialis dari Eropa yang akan bergabung bulan depan.</p>',
            ],
            [
                'kategori' => 'olahraga',
                'title' => 'Liga 1 Musim Ini Catat Rekor Penonton Tertinggi Sepanjang Sejarah',
                'image' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&q=80',
                'views' => 5230,
                'trending' => false,
                'content' => '<p>Liga 1 musim ini resmi mencetak rekor jumlah penonton tertinggi sepanjang sejarah kompetisi sepak bola domestik Indonesia. Hingga pekan ke-28, total penonton yang hadir langsung ke stadion mencapai 6,8 juta orang, melampaui rekor lama yang dibukukan musim 2019.</p><p>Pertandingan derby antara Persija Jakarta dan Persib Bandung menjadi yang paling banyak disaksikan, dengan 78.000 penonton memadati Stadion Utama Gelora Bung Karno. Tingkat hunian rata-rata stadion musim ini mencapai 82 persen, angka yang impresif bahkan dibandingkan liga-liga elite Asia Tenggara lainnya.</p><p>PSSI dan PT Liga Indonesia Baru mengaitkan lonjakan ini dengan berbagai pembenahan yang dilakukan, mulai dari peningkatan kualitas siaran televisi, digitalisasi sistem tiket, hingga program promosi yang lebih agresif. Kehadiran beberapa pemain bintang asing juga dinilai turut mendongkrak daya tarik kompetisi.</p><p>Pendapatan komersial Liga 1 musim ini diproyeksikan melampaui Rp 500 miliar untuk pertama kalinya, termasuk dari penjualan hak siar, sponsorship, dan merchandise resmi. Sebagian pendapatan ini akan direinvestasikan untuk program pengembangan pemain muda di seluruh Indonesia.</p>',
            ],
            [
                'kategori' => 'olahraga',
                'title' => 'Pelari Maraton Indonesia Tembus 10 Besar Dunia di Boston Marathon',
                'image' => 'https://images.unsplash.com/photo-1530549387789-4c1017266635?w=800&q=80',
                'views' => 3140,
                'trending' => false,
                'content' => '<p>Pelari andalan Indonesia, Agus Prayogi, membuat sejarah dengan finis di posisi delapan pada Boston Marathon, ajang maraton tertua dan paling bergengsi di dunia. Dengan catatan waktu 2 jam 6 menit 42 detik, Agus sekaligus memecahkan rekor nasional maraton yang telah bertahan selama 12 tahun.</p><p>Prayogi memulai perlombaan dengan strategi konservatif dan baru mulai meningkatkan tempo pada kilometer ke-25. Ia berhasil melewati beberapa pelari Kenya dan Ethiopia di kilometer-kilometer terakhir berkat kekuatan fisik dan mental yang telah ditempa dalam persiapan intensif selama enam bulan.</p><p>Pelatihnya, mantan juara Olimpiade asal Jepang Kenji Yamamoto, menyatakan kekagumannya atas eksekusi taktik yang sempurna dari atletnya. "Agus berhasil mempertahankan ritme pernapasannya bahkan di tanjakan Heartbreak Hill yang terkenal mematikan. Ini kemajuan luar biasa," pujinya.</p><p>Kemenpora segera menghubungi Agus untuk membicarakan persiapan menuju Olimpiade musim panas tahun depan. Dengan pencapaian ini, Indonesia kini memiliki peluang nyata untuk meraih medali di nomor maraton olimpiade untuk pertama kalinya dalam sejarah.</p>',
            ],
            [
                'kategori' => 'olahraga',
                'title' => 'Indonesia Tuan Rumah SEA Games, Infrastruktur Olahraga Siap 100%',
                'image' => 'https://images.unsplash.com/photo-1519766304817-4f37bda74a26?w=800&q=80',
                'views' => 2870,
                'trending' => false,
                'content' => '<p>Indonesia resmi siap menjadi tuan rumah SEA Games edisi mendatang setelah Menteri Pemuda dan Olahraga menyatakan semua fasilitas olahraga telah mencapai kesiapan 100 persen. Total 42 venue olahraga di 5 kota penyelenggara telah melalui serangkaian inspeksi teknis oleh Komite Olimpiade Asia Tenggara.</p><p>Stadion Utama yang baru selesai direnovasi dengan kapasitas 100.000 kursi akan menjadi pusat kegiatan dan arena upacara pembukaan. Fasilitas atlet di tiga wisma yang dibangun khusus mampu menampung 10.000 atlet dan official dari 11 negara peserta.</p><p>Panitia pelaksana (Inaspoc) menjamin pengalaman terbaik bagi atlet, official, dan penonton. Sistem manajemen pertandingan berbasis AI akan digunakan untuk pertama kalinya dalam sejarah SEA Games guna meningkatkan efisiensi pengelolaan jadwal dan hasil pertandingan secara real-time.</p><p>Indonesia menargetkan mempertahankan gelar juara umum yang telah diraih pada dua edisi SEA Games terakhir. Kemenpora telah menyiapkan 1.800 atlet yang akan bertarung di 56 cabang olahraga, dengan fokus utama pada cabang-cabang yang dipertandingkan di Olimpiade.</p>',
            ],

            // ── TEKNOLOGI (5 artikel) ────────────────────────────────
            [
                'kategori' => 'teknologi',
                'title' => 'Perkembangan Kecerdasan Buatan Semakin Pesat di Asia Tenggara',
                'image' => 'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=800&q=80',
                'views' => 9120,
                'trending' => true,
                'content' => '<p>Adopsi kecerdasan buatan (AI) di kawasan Asia Tenggara mengalami akselerasi luar biasa dalam dua tahun terakhir. Laporan terbaru McKinsey & Company mencatat nilai pasar AI di kawasan ini mencapai 15 miliar dolar AS dan diproyeksikan melampaui 45 miliar dolar pada 2030.</p><p>Indonesia menjadi salah satu pasar terbesar dengan pertumbuhan pengguna solusi AI di sektor perbankan, kesehatan, dan ritel yang meningkat 67 persen secara tahunan. Pemerintah telah merilis Strategi Nasional Kecerdasan Buatan yang menargetkan Indonesia menjadi pemimpin AI di ASEAN pada 2030.</p><p>Berbagai perusahaan teknologi global berlomba memperkuat kehadiran mereka di Indonesia. Google mengumumkan investasi tambahan 1 miliar dolar untuk pengembangan infrastruktur cloud dan pelatihan talenta digital. Microsoft dan Amazon Web Services juga memperluas kapasitas pusat data mereka di Batam dan Cikarang.</p><p>Di sisi lain, para ahli mengingatkan perlunya regulasi yang tepat untuk memastikan pengembangan AI berjalan secara etis dan bertanggung jawab. Isu keamanan data pribadi, potensi bias algoritma, dan dampak otomasi terhadap lapangan kerja menjadi agenda kritis yang harus dijawab oleh pembuat kebijakan.</p>',
            ],
            [
                'kategori' => 'teknologi',
                'title' => 'Smartphone Lipat Generasi Terbaru Hadir dengan Layar 8 Inci',
                'image' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&q=80',
                'views' => 6780,
                'trending' => true,
                'content' => '<p>Produsen smartphone terkemuka asal Korea Selatan meluncurkan ponsel lipat generasi terbaru dengan layar berukuran 8 inci ketika dibuka penuh. Menggunakan panel OLED ultra-tipis 0,003 mm, ponsel ini mengklaim ketahanan lipatan 300.000 kali tanpa degradasi kualitas layar yang signifikan.</p><p>Dari sisi performa, perangkat ini ditenagai chip octa-core 3nm generasi terbaru yang diklaim 40 persen lebih efisien secara energi dibanding pendahulunya. Sistem kamera triple-lens dengan sensor utama 200 megapiksel mampu merekam video 8K dengan stabilisasi optis enam sumbu.</p><p>Harga yang dibanderol di Indonesia mulai dari Rp 22 juta untuk varian dasar dan Rp 28 juta untuk varian tertinggi. Pada hari pertama pre-order, lebih dari 15.000 unit telah dipesan melalui platform e-commerce resmi, melampaui target awal pabrikan.</p><p>Para analis gadget menilai produk ini sebagai lompatan evolusioner yang signifikan, meski tetap mempertanyakan apakah harganya yang premium sebanding dengan nilai yang ditawarkan bagi konsumen umum. Segmen utamanya jelas adalah kalangan profesional dan peminat teknologi kelas atas.</p>',
            ],
            [
                'kategori' => 'teknologi',
                'title' => 'Revolusi Energi Surya: Panel Baru Capai Efisiensi 47 Persen',
                'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800&q=80',
                'views' => 4320,
                'trending' => false,
                'content' => '<p>Ilmuwan dari Institut Teknologi Bandung bekerja sama dengan peneliti dari MIT berhasil mengembangkan panel surya perovskite-silicon tandem yang mencapai efisiensi konversi energi sebesar 47,3 persen dalam kondisi laboratorium. Ini merupakan rekor baru dunia, mengungguli rekord sebelumnya yang dipegang panel buatan Jepang sebesar 45,1 persen.</p><p>Terobosan kunci terletak pada penggunaan lapisan anti-reflektor nano-tekstur yang mampu menangkap foton dengan sudut datang yang lebih lebar, ditambah modifikasi komposisi kimia lapisan perovskite yang meminimalkan kehilangan energi akibat rekombinasi muatan.</p><p>Tim riset kini tengah mengembangkan proses manufaktur yang memungkinkan produksi massal dengan biaya yang kompetitif. Jika berhasil komersialisasi, teknologi ini berpotensi memangkas biaya listrik tenaga surya hingga 60 persen dibanding panel konvensional yang kini beredar di pasaran.</p><p>Kementerian ESDM menyatakan minat untuk mendukung komersialisasi penemuan ini melalui program insentif riset dan pengembangan. Indonesia yang memiliki potensi energi surya sangat besar diyakini bisa menjadi pemain utama dalam transisi energi terbarukan global jika terobosan ini berhasil diimplementasikan dalam skala industri.</p>',
            ],
            [
                'kategori' => 'teknologi',
                'title' => 'Internet Satelit Hadir di 500 Desa Terpencil Indonesia',
                'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&q=80',
                'views' => 3650,
                'trending' => false,
                'content' => '<p>Program pemerintah untuk menghadirkan konektivitas internet berkecepatan tinggi di daerah terpencil memasuki babak baru dengan aktivasi layanan internet satelit di 500 desa di Papua, Kalimantan, dan Nusa Tenggara Timur. Program ini memanfaatkan teknologi Low Earth Orbit (LEO) satellite yang memiliki latensi jauh lebih rendah dari satelit geostasioner konvensional.</p><p>Masing-masing titik layanan dilengkapi dengan terminal penerima berdiameter 60 cm dan perangkat router untuk distribusi koneksi Wi-Fi ke area seluas 1 km persegi. Kecepatan unduh rata-rata mencapai 150 Mbps, lebih dari cukup untuk mendukung video conference, layanan kesehatan jarak jauh, dan pendidikan daring.</p><p>Di Desa Wamena Selatan, Papua, koneksi internet pertama ini disambut dengan antusias oleh warga. Kepala desa setempat mengungkapkan betapa besarnya dampak yang sudah dirasakan: siswa sekolah kini bisa mengakses materi belajar dari internet dan berkonsultasi dengan dokter di Jayapura tanpa harus melakukan perjalanan berjam-jam.</p><p>Kominfo menargetkan 3.000 desa tambahan akan mendapat layanan serupa dalam 18 bulan ke depan. Investasi infrastruktur ini diharapkan tidak hanya meningkatkan kualitas layanan publik, tetapi juga membuka peluang ekonomi baru bagi komunitas-komunitas yang selama ini terisolir dari arus utama perekonomian nasional.</p>',
            ],
            [
                'kategori' => 'teknologi',
                'title' => 'Startup HealthTech Indonesia Kembangkan AI Deteksi Kanker Dini',
                'image' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80',
                'views' => 5890,
                'trending' => false,
                'content' => '<p>Sebuah startup kesehatan digital asal Surabaya mengumumkan keberhasilan pengembangan sistem AI untuk deteksi dini kanker paru-paru melalui analisis citra CT-scan. Dalam uji klinis yang melibatkan 2.400 pasien di tiga rumah sakit besar, akurasi sistem ini mencapai 94,7 persen, setara dengan diagnosis dokter spesialis radiologi berpengalaman.</p><p>Teknologi ini mengandalkan arsitektur deep learning convolutional neural network yang dilatih menggunakan lebih dari 200.000 gambar CT-scan berlabel dari database medis internasional, dipadukan dengan data lokal pasien Indonesia untuk meningkatkan relevansi klinis di populasi Asia Tenggara.</p><p>Keunggulan utama sistem ini adalah kecepatannya: analisis yang biasanya membutuhkan 20-30 menit oleh dokter spesialis kini bisa diselesaikan dalam 3 detik, dengan laporan interpretasi otomatis yang komprehensif. Hal ini sangat relevan mengingat rasio dokter radiologi per penduduk di Indonesia yang masih sangat rendah.</p><p>Kementerian Kesehatan telah mengeluarkan izin edar untuk penggunaan sistem ini di fasilitas kesehatan pemerintah. Startup tersebut kini bernegosiasi dengan BPJS Kesehatan untuk mengintegrasikan teknologi ini dalam program skrining kanker nasional, yang berpotensi menyelamatkan ribuan nyawa setiap tahunnya.</p>',
            ],

            // ── OPINI (4 artikel) ────────────────────────────────────
            [
                'kategori' => 'opini',
                'title' => 'Opini: Membaca Berita di Era Post-Truth',
                'image' => 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80',
                'views' => 3420,
                'trending' => false,
                'content' => '<p>Di era di mana algorima menentukan apa yang kita baca dan siapa yang bisa kita dengar, kemampuan untuk membaca berita secara kritis bukan lagi kemewahan melainkan kebutuhan dasar. Post-truth — kondisi di mana fakta objektif lebih sedikit memengaruhi opini publik dibanding emosi dan keyakinan personal — telah menjadi realita bukan hanya di negara maju, tetapi juga di Indonesia.</p><p>Data survei terbaru menunjukkan 63 persen pengguna media sosial Indonesia mengakui sering membagikan artikel tanpa membacanya sampai selesai. Lebih mengkhawatirkan, 41 persen mengaku kesulitan membedakan berita asli dari hoaks. Ini bukan hanya masalah literasi digital, ini adalah krisis epistemis yang mengancam fondasi demokrasi kita.</p><p>Solusinya tidak sederhana. Pendidikan literasi media harus masuk kurikulum formal sejak sekolah dasar. Platform digital perlu dibuat bertanggung jawab secara hukum atas konten yang mereka amplifikasi melalui algoritma. Dan masing-masing dari kita perlu melatih kebiasaan berhenti sejenak sebelum berbagi, memverifikasi sumber, dan mencari perspektif yang berbeda.</p><p>Membaca berita yang baik adalah aksi sipil. Setiap kali kita memilih untuk membaca lebih dari sekadar judul, kita berkontribusi pada ekosistem informasi yang lebih sehat. Jurnalisme berkualitas butuh pembaca berkualitas untuk bertahan. Dan demokrasi kita butuh keduanya untuk berfungsi dengan benar.</p>',
            ],
            [
                'kategori' => 'opini',
                'title' => 'Opini: Transisi Energi Harus Adil bagi Semua',
                'image' => 'https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=800&q=80',
                'views' => 2180,
                'trending' => false,
                'content' => '<p>Indonesia berkomitmen mencapai net zero emissions pada 2060, sebuah target ambisius yang membutuhkan transformasi fundamental dalam cara kita memproduksi dan mengonsumsi energi. Namun di balik retorika hijau yang marak terdengar, ada satu pertanyaan yang sering luput dari diskusi kebijakan: apakah transisi energi ini akan adil bagi semua lapisan masyarakat?</p><p>Ribuan pekerja di sektor batubara dan minyak bumi yang selama ini menghidupi keluarga mereka dari industri fosil berpotensi kehilangan mata pencaharian jika transisi tidak dikelola dengan baik. Komunitas di Kalimantan, Sumatera, dan Papua yang ekonominya sangat bergantung pada eksploitasi sumber daya alam membutuhkan alternatif yang nyata, bukan sekadar janji investasi yang belum pasti.</p><p>Sebaliknya, subsidi energi terbarukan yang besar-besaran yang tengah dikucurkan pemerintah justru lebih banyak dinikmati oleh kelompok menengah ke atas yang mampu membeli panel surya dan kendaraan listrik. Rakyat miskin di pedesaan masih berjuang membayar tagihan listrik dari grid konvensional yang mahal.</p><p>Transisi yang adil berarti memastikan beban perubahan tidak ditanggung oleh mereka yang paling tidak berdaya. Ini membutuhkan dana transisi yang berpihak kepada pekerja dan komunitas terdampak, pelatihan ulang vokasional yang serius, dan keterlibatan masyarakat lokal dalam perencanaan proyek energi terbarukan di wilayah mereka.</p>',
            ],
            [
                'kategori' => 'opini',
                'title' => 'Opini: Mengapa Infrastruktur Digital Sama Pentingnya dengan Jalan Raya',
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&q=80',
                'views' => 1890,
                'trending' => false,
                'content' => '<p>Ketika pemerintah mengumumkan pembangunan jalan tol baru, hampir tidak ada protes. Tapi ketika anggaran untuk infrastruktur digital ditingkatkan, masih banyak yang mempertanyakan urgensinya. Padahal di abad ke-21 ini, kabel fiber optik adalah jalan raya baru, dan ketimpangan akses internet adalah bentuk ketimpangan infrastruktur yang sama nyatanya dengan ketiadaan jembatan.</p><p>Realita di lapangan sangat gamblang: seorang anak di Jakarta bisa mengakses ribuan konten pendidikan online, berkonsultasi dengan tutor privat virtual, dan melamar beasiswa ke universitas di seluruh dunia. Sementara teman sebayanya di Pegunungan Tengah Papua tidak punya sinyal telepon, apalagi internet. Kesenjangan ini menciptakan ketimpangan kesempatan yang akan terus melebar dari generasi ke generasi.</p><p>Argumen klasik bahwa pembangunan fisik harus didahulukan tidak lagi relevan. Infrastruktur digital dan fisik bukan kompetitor melainkan komplemen. Jalan tol tanpa konektivitas digital adalah seperti kapal tanpa kemudi di era ini. Pelabuhan pintar, pertanian presisi, telemedisin — semua membutuhkan koneksi yang handal sebagai fondasi.</p><p>Sudah saatnya pemerintah menetapkan internet berkecepatan minimal sebagai hak dasar warga negara, bukan sekadar layanan opsional. Investasi di infrastruktur digital bukan pengeluaran, melainkan investasi terbaik untuk memutus rantai kemiskinan struktural yang telah terlalu lama mengikat potensi anak bangsa di luar Jawa.</p>',
            ],
            [
                'kategori' => 'opini',
                'title' => 'Opini: Generasi Muda dan Beban Utang Negara',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&q=80',
                'views' => 2640,
                'trending' => false,
                'content' => '<p>Utang negara Indonesia saat ini mencapai Rp 8.400 triliun, atau sekitar 39 persen dari Produk Domestik Bruto. Angka yang secara teknis masih di bawah batas aman 60 persen yang ditetapkan undang-undang, namun trennya yang terus meningkat perlu menjadi perhatian serius — terutama bagi generasi yang akan menanggung konsekuensinya.</p><p>Para pemuda berusia 20-an hari ini akan menjadi wajib pajak utama selama empat dekade ke depan. Mereka yang akan membayar cicilan utang ini melalui pajak penghasilan yang dipotong setiap bulan. Sayangnya, diskursus publik tentang keberlanjutan fiskal sangat jarang menyertakan perspektif dan suara generasi muda ini.</p><p>Utang bukan inheren buruk jika digunakan untuk investasi produktif yang menghasilkan pertumbuhan lebih besar dari biaya bunganya. Pembangunan infrastruktur yang meningkatkan produktivitas ekonomi, pendidikan yang meningkatkan kualitas sumber daya manusia, dan kesehatan yang memperpanjang usia produktif warga negara adalah contoh utang yang bermakna.</p><p>Yang patut dipertanyakan adalah ketika utang digunakan untuk membiayai pengeluaran konsumtif, subsidi yang tidak tepat sasaran, atau proyek-proyek mercusuar yang nilai ekonominya tidak sepadan dengan biayanya. Generasi muda berhak menuntut transparansi penuh atas bagaimana setiap rupiah utang negara digunakan hari ini — karena merekalah yang akan melunasinya esok hari.</p>',
            ],
        ];

        foreach ($data as $idx => $row) {
            $cat = $cats->get($row['kategori']);

            if (! $cat) {
                continue;
            }

            $slug = Str::slug($row['title']);

            // Lewati artikel jika slug sudah ada.
            if (Article::where('slug', $slug)->exists()) {
                continue;
            }

            $createdAt = now()
                ->subDays(($idx % 30) + 1 + rand(0, 5))
                ->subHours(rand(0, 23));

            Article::create([
                'category_id' => $cat->id,
                'user_id' => $admin->id,
                'title' => $row['title'],
                'slug' => $slug,
                'content' => $row['content'],
                'image' => $row['image'],
                'status' => 'published',
                'views' => $row['views'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        // Buat beberapa komentar contoh.
        // Hanya dibuat jika artikel tersebut belum memiliki komentar
        // agar menjalankan seeder berulang kali tidak menggandakan komentar.
        $articles = Article::all();

        $komentar = [
            'Artikel yang sangat informatif, terima kasih!',
            'Saya sangat setuju dengan analisis ini.',
            'Perlu dikaji lebih mendalam lagi.',
            'Informasi yang berguna dan relevan.',
            'Terima kasih telah berbagi berita ini.',
        ];

        foreach ($articles->take(10) as $i => $article) {
            if ($article->comments()->exists()) {
                continue;
            }

            Comment::create([
                'article_id' => $article->id,
                'user_id' => $admin->id,
                'body' => $komentar[$i % count($komentar)],
            ]);
        }
    }
}