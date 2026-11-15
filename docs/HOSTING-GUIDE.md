# Panduan Hosting — NEWS X PAPER

Panduan ini menjelaskan cara mendeploy NEWS X PAPER ke server publik.

---

## Opsi Hosting yang Kompatibel dengan Laravel

| Penyedia | Harga | Cocok Untuk | Catatan |
|---|---|---|---|
| **InfinityFree** | Gratis | Demo tugas | PHP 8.x, MySQL, tanpa SSH |
| **000webhost** | Gratis | Demo tugas | Terbatas, cocok untuk portofolio |
| **Niagahoster** | Mulai Rp 10rb/bln | Shared hosting | PHP 8.x, MySQL, cPanel |
| **Domainesia** | Mulai Rp 15rb/bln | Shared hosting | PHP 8.x, cPanel, support Indonesia |
| **Railway** | Free tier tersedia | App platform | Support Laravel via Nixpacks |
| **Render** | Free tier tersedia | App platform | Web service + PostgreSQL gratis |
| **VPS (DigitalOcean/Vultr)** | $4-6/bln | Full control | Butuh konfigurasi manual |

> **Rekomendasi untuk tugas sekolah:** Niagahoster atau Domainesia (harga terjangkau, support Indonesia, cPanel mudah digunakan).

---

## Checklist Kesiapan Project

Sebelum deploy, pastikan semua item berikut sudah siap:

```
[ ] APP_KEY sudah di-generate (php artisan key:generate)
[ ] APP_ENV=production di .env server
[ ] APP_DEBUG=false di .env server
[ ] DB_* sudah diisi sesuai database server
[ ] APP_URL=https://domainanda.com di .env server
[ ] composer install --optimize-autoloader --no-dev
[ ] php artisan config:cache
[ ] php artisan route:cache
[ ] php artisan view:cache
[ ] php artisan storage:link
[ ] Folder storage/ dan bootstrap/cache/ writable (chmod 775)
[ ] File .env TIDAK ada di repository GitHub
```

---

## Langkah Deploy ke Shared Hosting (cPanel)

### 1. Siapkan Database di cPanel

1. Login cPanel hosting
2. Buka **MySQL Databases**
3. Buat database baru: `namauser_portalberita`
4. Buat user database dengan password kuat
5. Assign user ke database dengan semua privilege
6. Catat: nama database, username, password

### 2. Upload File Project

**Cara A — via File Manager:**
1. Buka **File Manager** di cPanel
2. Masuk ke folder `public_html`
3. Upload file ZIP project (tanpa folder `node_modules`, `vendor`, `.git`)
4. Extract ZIP

**Cara B — via Git (jika hosting mendukung):**
```bash
git clone https://github.com/najwapuspa/portal-berita.git
cd portal-berita
composer install --optimize-autoloader --no-dev
```

### 3. Konfigurasi Document Root

> Laravel harus mengarah ke folder `public/`, bukan root project.

Di cPanel → **Domains** atau **Addon Domains**:
- Ubah **Document Root** ke: `public_html/portal-berita/public`

Atau gunakan `.htaccess` di `public_html`:
```apache
RewriteEngine On
RewriteCond %{REQUEST_URI} !^/portal-berita/public
RewriteRule ^(.*)$ /portal-berita/public/$1 [L]
```

### 4. Buat File `.env` di Server

Upload file `.env` via File Manager (jangan lewat Git) dengan isi:

```env
APP_NAME="NEWS X PAPER"
APP_ENV=production
APP_KEY=base64:GENERATE_ULANG_DI_SERVER
APP_DEBUG=false
APP_URL=https://domainanda.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=namauser_portalberita
DB_USERNAME=namauser_dbuser
DB_PASSWORD=passwordkuat

SESSION_DRIVER=file
CACHE_STORE=file
```

### 5. Generate APP_KEY di Server

Via cPanel → **Terminal** (jika tersedia) atau via PHP script sementara:
```bash
php artisan key:generate
```

### 6. Jalankan Migration

```bash
php artisan migrate --force
```

### 7. Buat Storage Link

```bash
php artisan storage:link
```

### 8. Set Permission Folder

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 9. Cache Konfigurasi (Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Deploy ke Railway (Gratis)

Railway mendukung Laravel dengan otomatis.

1. Push project ke GitHub
2. Daftar di [railway.app](https://railway.app)
3. Klik **New Project** → **Deploy from GitHub**
4. Pilih repo `portal-berita`
5. Tambahkan service **MySQL** dari Railway
6. Set environment variables:
   - `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`
   - `DB_*` dari MySQL service Railway
   - `APP_URL=https://xxx.railway.app`
7. Railway otomatis build dan deploy

---

## Catatan Penting Keamanan

```
JANGAN lakukan ini di production:
❌ APP_DEBUG=true  → menampilkan error detail ke pengunjung
❌ DB_PASSWORD kosong
❌ Upload file .env ke GitHub
❌ Menggunakan password default admin

WAJIB dilakukan di production:
✅ APP_DEBUG=false
✅ HTTPS (SSL Certificate)
✅ Password admin yang kuat
✅ Validasi semua input
✅ File .env di luar public folder
```

---

## Status Hosting Saat Ini

> Website saat ini berjalan secara **lokal** di `http://localhost:8000` menggunakan Laragon.
> Untuk deploy ke server publik, ikuti langkah-langkah di panduan ini.
> Website belum online di domain publik.
