# NEWS X PAPER — Portal Berita

<p align="center">
  <strong>Portal Berita Digital Indonesia</strong><br>
  Dibangun dengan Laravel 13 · PHP 8.5 · MySQL
</p>

---

## Deskripsi

**NEWS X PAPER** adalah portal berita digital berbasis web yang menyajikan informasi aktual dari berbagai kategori seperti Nasional, Ekonomi, Teknologi, Olahraga, dan lainnya. Website ini dirancang untuk memberikan pengalaman membaca berita yang nyaman, modern, dan responsif di semua perangkat.

> _"the art of publishing"_

---

## Tujuan Pembuatan

Proyek ini dibuat sebagai bagian dari tugas sekolah untuk mempelajari pengembangan web menggunakan framework PHP modern (Laravel), dengan menerapkan konsep:

- Autentikasi dan otorisasi dua peran (admin & user)
- Operasi CRUD lengkap
- Pagination dan pencarian
- Ekspor laporan (PDF & Excel)
- Desain responsif

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework Backend | Laravel 13 |
| Bahasa Pemrograman | PHP 8.5.1 |
| Database | MySQL |
| Frontend | HTML, CSS (custom), JavaScript |
| Grafik | Chart.js 4.4 |
| Ekspor PDF | barryvdh/laravel-dompdf ^3.1 |
| Ekspor Excel | phpoffice/phpspreadsheet ^5.11 |
| Versi Kontrol | Git + GitHub |
| Server Lokal | Laragon (Windows) |

---

## Fitur Utama

### Fitur Publik
- 🏠 Homepage dengan berita utama, trending, dan kategori
- 📰 Daftar berita dengan pagination
- 🔍 Pencarian berita berdasarkan judul dan konten
- 📂 Halaman kategori berita (12 kategori)
- 📄 Halaman detail berita dengan komentar
- 🔗 Tombol berbagi ke Facebook, X, WhatsApp, dan salin tautan
- 📱 Desain responsif untuk semua ukuran layar
- 📧 Langganan newsletter

### Fitur Admin
- 📊 Dashboard statistik lengkap
- ✍️ CRUD berita (tambah, lihat, edit, hapus, ubah status)
- 🗂️ CRUD kategori (tambah, lihat, edit, hapus)
- 👥 Kelola pengguna (tambah, edit, ubah role, hapus)
- 💬 Moderasi komentar
- 📈 Laporan statistik berita
- 📄 Ekspor laporan ke PDF
- 📊 Ekspor laporan ke Excel (4 sheet)
- 🖨️ Cetak laporan
- 📡 Sistem RSS otomatis (CNN Indonesia, ANTARA, Republika, dll.)

### Fitur User Terdaftar
- 🔐 Login dan daftar akun
- 💬 Menulis komentar pada berita
- 👤 Dashboard pribadi pembaca
- 📋 Melihat komentar sendiri

---

## Struktur Database (Ringkasan)

| Tabel | Deskripsi |
|---|---|
| `users` | Data pengguna (admin & user), kolom `role` |
| `categories` | Kategori berita (12 kategori) |
| `articles` | Artikel berita dengan metadata lengkap |
| `comments` | Komentar pembaca pada artikel |
| `kunjungan` | Statistik kunjungan harian |
| `article_bookmarks` | Bookmark artikel oleh pengguna |
| `newsletter_subscribers` | Daftar email langganan |
| `sessions` | Sesi pengguna |
| `cache` | Cache aplikasi |

---

## Persyaratan Instalasi

- PHP >= 8.3
- Composer >= 2.x
- MySQL >= 5.7 / MariaDB >= 10.4
- Node.js (opsional, untuk asset building)
- Git

---

## Cara Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/najwapuspa/portal-berita.git
cd portal-berita
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database di `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Buat Database

Buka phpMyAdmin atau MySQL client, kemudian buat database baru:

```sql
CREATE DATABASE portal_berita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Jalankan Migration

```bash
php artisan migrate
```

### 7. (Opsional) Isi Data Contoh

```bash
php artisan db:seed
```

### 8. Buat Symbolic Link Storage

```bash
php artisan storage:link
```

### 9. Jalankan Website

```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## Cara Konfigurasi `.env`

| Key | Nilai |
|---|---|
| `APP_NAME` | `NEWS X PAPER` |
| `APP_ENV` | `local` (dev) atau `production` |
| `APP_DEBUG` | `true` (dev) atau `false` (production) |
| `APP_URL` | URL website Anda |
| `DB_DATABASE` | Nama database MySQL |
| `DB_USERNAME` | Username MySQL |
| `DB_PASSWORD` | Password MySQL |

---

## Cara Menjalankan Scheduler (RSS Otomatis)

Scheduler mengambil berita baru setiap 30 menit.

**Windows (Laragon/lokal) — via Task Scheduler:**

Buka PowerShell sebagai Administrator:

```powershell
$action  = New-ScheduledTaskAction `
  -Execute "C:\laragon\bin\php\php-8.5.1-Win32-vs17-x64\php.exe" `
  -Argument "D:\portal-berita\artisan schedule:run" `
  -WorkingDirectory "D:\portal-berita"
$trigger = New-ScheduledTaskTrigger -RepetitionInterval (New-TimeSpan -Minutes 1) -Once -At (Get-Date)
Register-ScheduledTask -TaskName "LaravelScheduler-NewsXPaper" -Action $action -Trigger $trigger -RunLevel Highest -Force
```

**Jalankan manual sekali:**

```bash
php artisan news:fetch
```

---

## Akun Demo

> ⚠️ Ganti password di production. Jangan gunakan akun ini di server publik.

| Role | Email | Password |
|---|---|---|
| Admin | admin@newsxpaper.test | (atur via seeder atau langsung di DB) |
| User | user@newsxpaper.test | (atur via seeder atau langsung di DB) |

Untuk membuat akun admin baru:

```bash
php artisan tinker
>>> App\Models\User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>Hash::make('password123'),'role'=>'admin'])
```

---

## Struktur Folder Penting

```
portal-berita/
├── app/
│   ├── Http/Controllers/     ← Controller aplikasi
│   ├── Models/               ← Model Eloquent
│   └── Services/             ← RssNewsService
├── database/
│   ├── migrations/           ← Skema database
│   └── seeders/              ← Data awal
├── docs/                     ← ERD, screenshot, panduan
├── public/css/               ← CSS kustom
├── resources/views/          ← Blade templates
├── routes/web.php            ← Semua route
└── .env.example              ← Template konfigurasi
```

---

## Lisensi

Proyek ini dibuat untuk keperluan tugas sekolah.
© 2026 NEWS X PAPER — All rights reserved.
