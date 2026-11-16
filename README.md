# NEWS X PAPER — Portal Berita

<p align="center">
  <strong>Portal Berita Digital Indonesia</strong><br>
  <em>"the art of publishing"</em><br><br>
  Dibangun dengan Laravel 13 · PHP 8.5 · MySQL
</p>

---

## Deskripsi

**NEWS X PAPER** adalah portal berita digital berbasis web yang menyajikan informasi aktual dari berbagai kategori seperti Nasional, Ekonomi, Teknologi, Olahraga, dan lainnya. Website ini dirancang untuk memberikan pengalaman membaca berita yang nyaman, modern, dan responsif di semua perangkat.

Berita diperbarui secara otomatis melalui sistem RSS dari sumber terpercaya seperti CNN Indonesia, ANTARA, Republika, CNBC Indonesia, Tempo, dan Sindonews.

---

## Tujuan Pembuatan

Proyek ini dibuat sebagai bagian dari tugas sekolah untuk mempelajari pengembangan web menggunakan framework PHP modern (Laravel), dengan menerapkan konsep:

- Autentikasi dan otorisasi dua peran (admin & user)
- Operasi CRUD lengkap (berita, kategori, pengguna, komentar)
- Pagination dan pencarian
- Ekspor laporan (PDF & Excel)
- Sistem RSS otomatis dengan scheduler
- Desain responsif

---

## Teknologi yang Digunakan

| Komponen | Teknologi | Versi |
|---|---|---|
| Framework Backend | Laravel | 13 |
| Bahasa Pemrograman | PHP | 8.5.1 |
| Database | MySQL | 5.7+ / MariaDB 10.4+ |
| Frontend | HTML, CSS (custom), JavaScript | — |
| Grafik Dashboard | Chart.js (CDN) | 4.4 |
| Ekspor PDF | barryvdh/laravel-dompdf | ^3.1 |
| Ekspor Excel | phpoffice/phpspreadsheet | ^5.11 |
| Server Lokal | Laragon (Windows) | — |
| Versi Kontrol | Git + GitHub | — |

---

## Fitur Utama

### Fitur Publik (tanpa login)

- **Homepage** dengan berita utama, trending ticker, dan navigasi kategori
- **Daftar berita** dengan pagination (12 per halaman)
- **Pencarian berita** berdasarkan judul dan konten
- **Halaman kategori** untuk 12 kategori berita
- **Detail berita** dengan foto, sumber, dan komentar
- **Tombol berbagi** ke Facebook, X (Twitter), WhatsApp, dan salin tautan
- **Langganan newsletter** melalui email
- **Desain responsif** untuk desktop, tablet, dan smartphone
- **Optimasi performa**: lazy loading gambar, CSS minified, font preload

### Fitur Admin

- **Dashboard statistik** dengan grafik kunjungan, views per kategori, dan ringkasan
- **CRUD Berita** — tambah, lihat, edit, hapus, ubah status (draft/published)
- **CRUD Kategori** — tambah, lihat, edit, hapus (dengan proteksi kategori yang memiliki artikel)
- **Kelola Pengguna** — tambah, edit, ubah role, hapus
- **Moderasi Komentar** — lihat dan hapus komentar
- **Laporan Statistik Berita** dengan filter periode, kategori, dan status
- **Ekspor PDF** — laporan profesional format A4 dengan identitas NEWS X PAPER
- **Ekspor Excel** — 4 sheet (Ringkasan, Kategori, Penulis, Data Artikel)
- **Cetak Laporan** — tampilan print-friendly dengan `@media print`
- **Sistem RSS otomatis** — mengambil berita dari 6 sumber setiap 30 menit

### Fitur User Terdaftar

- **Login dan registrasi** akun
- **Dashboard pribadi** pembaca dengan berita terbaru dan populer
- **Komentar** pada berita yang diterbitkan
- **Riwayat komentar** sendiri
- **Bookmark** artikel untuk dibaca nanti

---

## Sumber Berita RSS

Website secara otomatis mengambil berita dari sumber berikut:

| Sumber | URL Feed | Kategori Utama |
|---|---|---|
| CNN Indonesia | `cnnindonesia.com/{kategori}/rss` | Nasional, Ekonomi, Teknologi, Olahraga, dll. |
| ANTARA | `antaranews.com/rss/{kategori}.xml` | Nasional, Politik, Ekonomi, Internasional, dll. |
| Republika | `republika.co.id/rss` | Nasional |
| CNBC Indonesia | `cnbcindonesia.com/{section}/rss` | Nasional, Ekonomi, Teknologi |
| Tempo | `rss.tempo.co/{kanal}` | Nasional, Bisnis |
| Sindonews | `sindonews.com/feed` | Nasional |

Sistem menggunakan deduplikasi berbasis `source_url` untuk mencegah artikel duplikat.

---

## Struktur Database

| Tabel | Deskripsi |
|---|---|
| `users` | Data pengguna dengan kolom `role` (admin/user) |
| `categories` | Kategori berita (12 kategori) |
| `articles` | Artikel berita dengan metadata (source_name, source_url, excerpt, read_time, published_at, views) |
| `comments` | Komentar pembaca pada artikel |
| `kunjungan` | Statistik kunjungan harian |
| `article_bookmarks` | Bookmark artikel oleh pengguna |
| `newsletter_subscribers` | Daftar email langganan newsletter |
| `sessions` | Sesi pengguna |
| `cache` / `cache_locks` | Cache aplikasi |
| `jobs` / `failed_jobs` | Queue jobs |
| `password_reset_tokens` | Token reset password |

Diagram ERD lengkap tersedia di `docs/ERD.mmd`.

---

## Persyaratan Instalasi

- PHP >= 8.3
- Composer >= 2.x
- MySQL >= 5.7 atau MariaDB >= 10.4
- Git
- (Opsional) Node.js untuk asset building

---

## Cara Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/najwapuspa/portal-berita.git
cd portal-berita
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
# Linux/Mac
cp .env.example .env

# Windows
copy .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Konfigurasi Database di `.env`

Edit file `.env` dan sesuaikan:

```env
APP_NAME="NEWS X PAPER"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Buat Database MySQL

Buka phpMyAdmin atau MySQL client:

```sql
CREATE DATABASE portal_berita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Jalankan Migration

```bash
php artisan migrate
```

### 8. (Opsional) Isi Data Contoh

```bash
php artisan db:seed
```

### 9. Buat Symbolic Link Storage

```bash
php artisan storage:link
```

### 10. Jalankan Website

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

> **Catatan:** Untuk mengakses website dari perangkat lain di jaringan lokal (HP, tablet), gunakan:
> ```bash
> php artisan serve --host=0.0.0.0 --port=8000
> ```
> Kemudian akses melalui `http://<IP-LAPTOP>:8000` dari perangkat lain.
> Pastikan `APP_URL` di `.env` diubah ke alamat IP laptop Anda.

---

## Konfigurasi `.env`

| Key | Dev (Lokal) | Production |
|---|---|---|
| `APP_NAME` | `NEWS X PAPER` | `NEWS X PAPER` |
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `http://localhost:8000` | `https://domainanda.com` |
| `DB_DATABASE` | `portal_berita` | nama database server |
| `DB_USERNAME` | `root` | username database server |
| `DB_PASSWORD` | _(kosong)_ | password database server |

> **Penting:** File `.env` tidak ikut ter-upload ke GitHub (sudah di `.gitignore`). Jangan menyimpan password produksi di file ini pada server publik tanpa proteksi tambahan.

---

## Menjalankan Scheduler RSS Otomatis

Scheduler mengambil berita baru dari RSS feed setiap 30 menit.

### Jalankan Manual (Sekali)

```bash
php artisan news:fetch
```

Output akan menampilkan:
- Jumlah artikel baru per sumber
- Jumlah artikel yang dilewati (duplikat)
- Feed yang gagal diakses
- Waktu eksekusi total

### Konfigurasi Otomatis — Windows Task Scheduler

Buka PowerShell sebagai Administrator:

```powershell
$action  = New-ScheduledTaskAction `
  -Execute "C:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe" `
  -Argument "D:\portal-berita\artisan schedule:run" `
  -WorkingDirectory "D:\portal-berita"
$trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 1) -Once -At (Get-Date)
Register-ScheduledTask -TaskName "LaravelScheduler-NewsXPaper" -Action $action -Trigger $trigger -RunLevel Highest -Force
```

### Konfigurasi Otomatis — Linux/Cron

```bash
* * * * * cd /path/to/portal-berita && php artisan schedule:run >> /dev/null 2>&1
```

> **Catatan:** Scheduler Laravel berjalan setiap 1 menit, tetapi Laravel sendiri yang menentukan kapan `news:fetch` dieksekusi berdasarkan interval `everyThirtyMinutes()` di `routes/console.php`.

---

## Akun Demo

> ⚠️ **Peringatan:** Ganti password akun ini sebelum deploy ke server publik. Jangan gunakan akun ini di production.

| Role | Email | Catatan |
|---|---|---|
| Admin | `admin@admin123.test` | Akun admin utama |
| Admin | `admin@newsxpaper.com` | Akun admin kedua |
| User | `user@user.test` | Akun pengguna biasa |

Password akun demo diatur melalui database seeder. Untuk membuat akun baru atau mengubah password:

```bash
php artisan tinker
```

```php
// Buat admin baru
App\Models\User::create([
    'name'     => 'Admin Baru',
    'email'    => 'adminbaru@example.com',
    'password' => Hash::make('passwordbaru'),
    'role'     => 'admin',
]);

// Ubah password user
$user = App\Models\User::find(1);
$user->update(['password' => Hash::make('passwordbaru')]);
```

---

## Struktur Folder Penting

```
portal-berita/
├── app/
│   ├── Http/Controllers/       ← Controller (Article, Category, Laporan, Auth, dll.)
│   ├── Models/                 ← Model Eloquent (Article, Category, User, Comment)
│   ├── Services/RssNewsService.php  ← Logika pengambilan RSS
│   └── Console/Commands/       ← Artisan command (news:fetch)
├── database/
│   ├── migrations/             ← Skema database (15 tabel)
│   └── seeders/                 ← Data awal (kategori, artikel contoh)
├── docs/                        ← Dokumentasi
│   ├── ERD.mmd                  ← Diagram ERD (Mermaid)
│   ├── PANDUAN-VIDEO-DEMO.md    ← Naskah video demo 2–3 menit
│   ├── HOSTING-GUIDE.md         ← Panduan deploy ke hosting
│   └── screenshots/             ← Folder screenshot
├── public/
│   ├── css/
│   │   ├── newspaper.css         ← CSS utama (68 KB)
│   │   ├── newspaper.min.css     ← CSS minified (45 KB, 32% lebih kecil)
│   │   └── dashboard.css        ← CSS dashboard admin
│   └── .htaccess                 ← Konfigurasi Apache (gzip, caching)
├── resources/views/             ← Blade templates
├── routes/
│   ├── web.php                   ← Semua route aplikasi
│   └── console.php               ← Scheduler konfigurasi
├── config/categories.php         ← Daftar 12 kategori dengan warna
└── .env.example                  ← Template konfigurasi
```

---

## Dokumentasi Tambahan

Dokumentasi lengkap tersedia di folder `docs/`:

| File | Isi |
|---|---|
| `docs/ERD.mmd` | Diagram Entity Relationship Database (format Mermaid) |
| `docs/PANDUAN-VIDEO-DEMO.md` | Naskah video demo 2–3 menit dengan 9 bagian |
| `docs/HOSTING-GUIDE.md` | Panduan deploy ke shared hosting, Railway, VPS |
| `docs/screenshots/DAFTAR-SCREENSHOT.md` | Daftar 15 screenshot yang perlu diambil |

---

## Optimasi Performa

Website telah dioptimasi untuk kecepatan loading:

| Optimasi | Implementasi |
|---|---|
| Lazy loading gambar | `IntersectionObserver` untuk semua background-image di bawah fold |
| LCP priority | `fetchpriority="high"` pada gambar utama homepage |
| CSS minification | `newspaper.css` → `newspaper.min.css` (32.5% lebih kecil) |
| Query optimization | 12 query kategori diganti 1 query `whereIn` |
| Font preload | Google Fonts di-preload untuk render teks lebih cepat |
| Gzip compression | `.htaccess` dengan `mod_deflate` (aktif di Apache) |
| Browser caching | `.htaccess` dengan `mod_expires` (CSS 7 hari, gambar 30 hari) |

---

## Lisensi

Proyek ini dibuat untuk keperluan tugas sekolah.

© 2026 NEWS X PAPER — All rights reserved.
