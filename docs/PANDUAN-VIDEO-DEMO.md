# Panduan Video Demo — NEWS X PAPER

**Durasi target:** 2–3 menit  
**Format rekaman:** Screen recording + narasi lisan  
**Alat rekaman:** OBS Studio, Loom, Bandicam, atau Xbox Game Bar (Win+G)

---

## Persiapan Sebelum Rekaman

1. Pastikan server sudah berjalan: `php artisan serve`
2. Buka browser di: `http://localhost:8000`
3. Siapkan akun admin dan akun user
4. Pastikan ada berita yang sudah diterbitkan
5. Zoom browser ke 90% agar seluruh halaman terlihat
6. Tutup notifikasi Windows selama rekaman

---

## Naskah Demo

---

### BAGIAN 1 — Pembukaan (0:00 – 0:20)

**Tampilkan:** Homepage NEWS X PAPER

**Narasi:**
> "Halo, perkenalkan saya [nama]. Ini adalah NEWS X PAPER, portal berita digital yang saya buat menggunakan Laravel 13 dan PHP 8.5. Website ini dirancang untuk menyajikan berita terkini dari berbagai kategori secara profesional dan responsif."

**Yang dilakukan:**
- Zoom ke logo NEWS X PAPER di header
- Scroll halaman sebentar agar terlihat tampilan keseluruhan

---

### BAGIAN 2 — Homepage dan Navigasi (0:20 – 0:40)

**Tampilkan:** Homepage, navbar, berita utama

**Narasi:**
> "Di halaman utama, pengunjung bisa melihat berita headline, artikel terbaru, dan navigasi kategori. Ada 12 kategori berita tersedia mulai dari Nasional, Ekonomi, hingga Teknologi."

**Yang dilakukan:**
- Klik salah satu kategori (misal: Teknologi)
- Tampilkan daftar berita dengan pagination
- Klik tombol halaman 2 untuk menunjukkan pagination berfungsi

---

### BAGIAN 3 — Pencarian dan Detail Berita (0:40 – 1:00)

**Tampilkan:** Fitur pencarian dan halaman detail berita

**Narasi:**
> "Pengunjung bisa mencari berita menggunakan kolom pencarian. Hasilnya muncul secara dinamis dari database. Dan ini contoh halaman detail berita — lengkap dengan foto, penulis, tanggal, dan tombol berbagi."

**Yang dilakukan:**
- Ketikkan kata kunci di kolom pencarian (misal: "ekonomi")
- Tekan Enter, tampilkan hasil pencarian
- Klik salah satu berita
- Tunjukkan tombol berbagi (Facebook, X, WhatsApp, Salin Tautan)

---

### BAGIAN 4 — Login Admin (1:00 – 1:15)

**Tampilkan:** Halaman login → masuk sebagai admin

**Narasi:**
> "Sekarang saya masuk sebagai administrator."

**Yang dilakukan:**
- Buka `/login`
- Masukkan email dan password admin
- Klik Login → diarahkan ke Dashboard Admin

---

### BAGIAN 5 — Dashboard Admin dan CRUD Berita (1:15 – 1:45)

**Tampilkan:** Dashboard Admin, pengelolaan berita

**Narasi:**
> "Ini dashboard admin yang menampilkan statistik lengkap: jumlah berita, pengguna, komentar, dan grafik kunjungan. Admin bisa mengelola berita dari sini — tambah, edit, ubah status, atau hapus artikel."

**Yang dilakukan:**
- Tunjukkan kartu statistik di dashboard
- Klik menu "Kelola Berita"
- Tunjukkan daftar berita dengan filter dan pencarian
- Klik "+ Tambah Berita" — tampilkan formulir
- Isi judul, pilih kategori, tulis konten singkat, pilih status "Draft"
- Klik Simpan
- Kembali ke daftar, berita baru muncul
- Klik "Edit" pada berita tersebut, ubah status ke "Tayang", Simpan
- Tunjukkan tombol "Hapus" (opsional: hapus berita uji)

---

### BAGIAN 6 — CRUD Kategori (1:45 – 2:00)

**Tampilkan:** Kelola Kategori

**Narasi:**
> "Admin juga bisa mengelola kategori berita — menambah kategori baru, mengedit, atau menghapus kategori yang tidak terpakai."

**Yang dilakukan:**
- Klik menu "Kelola Kategori"
- Tunjukkan daftar 12 kategori dengan jumlah artikel
- Klik "+ Tambah Kategori", isi nama "Kesehatan Digital", klik Simpan
- Tampilkan kategori baru muncul di daftar

---

### BAGIAN 7 — Laporan Statistik dan Ekspor (2:00 – 2:30)

**Tampilkan:** Laporan Statistik, ekspor PDF, ekspor Excel

**Narasi:**
> "Fitur unggulan dashboard adalah Laporan Statistik Berita. Admin bisa melihat ringkasan lengkap, grafik distribusi, dan melakukan filter berdasarkan periode atau kategori. Laporan ini bisa diekspor ke PDF maupun Excel."

**Yang dilakukan:**
- Klik menu "Laporan Statistik"
- Tunjukkan 6 kartu statistik dan grafik
- Isi filter tanggal, klik Terapkan
- Klik "Ekspor PDF" → PDF terunduh, buka dan tampilkan sebentar
- Klik "Ekspor Excel" → file `.xlsx` terunduh

---

### BAGIAN 8 — Login User dan Komentar (2:30 – 2:45)

**Tampilkan:** Dashboard user, membaca berita, komentar

**Narasi:**
> "Untuk pengguna biasa, tampilan dashboard berbeda — lebih fokus ke berita terbaru dan aktivitas komentar. User bisa membaca dan berkomentar, tapi tidak bisa mengakses panel admin."

**Yang dilakukan:**
- Logout dari akun admin
- Login dengan akun user
- Tampilkan dashboard user (berbeda dari admin)
- Buka halaman berita, scroll ke bagian komentar
- Tulis komentar singkat, klik Kirim

---

### BAGIAN 9 — Penutup (2:45 – 3:00)

**Tampilkan:** Homepage

**Narasi:**
> "Itulah NEWS X PAPER — portal berita digital yang saya bangun menggunakan Laravel. Fitur lengkap meliputi CRUD berita dan kategori, sistem dua peran, pencarian, pagination, laporan PDF dan Excel, serta desain responsif. Terima kasih."

---

## Tips Rekaman

1. **Bicara pelan dan jelas** — jangan terburu-buru
2. **Hindari gerakan mouse yang terlalu cepat** — pengulas perlu melihat perubahan
3. **Zoom ke elemen penting** saat menjelaskan detail
4. **Gunakan browser tanpa ekstensi** agar tampilan bersih
5. **Record ulang jika ada kesalahan** — lebih baik dari pada diedit
6. **Tambahkan teks overlay** di video editor jika perlu judul bagian

## Alat Rekaman yang Disarankan

| Alat | Kelebihan |
|---|---|
| OBS Studio | Gratis, kualitas tinggi, bisa rekam + streaming |
| Xbox Game Bar (Win+G) | Sudah built-in Windows, mudah digunakan |
| Loom | Mudah, langsung upload ke link, gratis |
| Bandicam | Ringan, watermark minimal di versi gratis |

---

> Panduan ini tidak berarti video sudah dibuat.
> Perekaman harus dilakukan secara manual oleh Anda menggunakan panduan di atas.
