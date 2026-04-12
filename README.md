# KOSERA MITRA

Portfolio aplikasi layanan jasa untuk pengguna kos (anak kos) yang membutuhkan bantuan harian secara cepat, rapi, dan terstruktur.

## Ringkasan Produk
KOSERA MITRA adalah web app berbasis PHP Native + MySQL yang membantu pengguna kos menemukan dan mengelola jasa seperti perbaikan, kebersihan, dan antar jemput.

Fokus aplikasi:
1. Mempermudah pengguna kos mencari layanan sesuai kategori.
2. Menyediakan data jasa yang jelas: judul, deskripsi, harga, mitra, dan bukti sertifikat.
3. Memberikan pengalaman CRUD yang sederhana untuk admin/mitra terautentikasi.

## Fitur Utama
1. Autentikasi pengguna
   Login, registrasi, logout, dan proteksi halaman dengan session.
2. Manajemen jasa
   Tambah, lihat detail, edit, dan hapus data jasa.
3. Kategori dan sub-kategori
   Struktur layanan lebih rapi dengan relasi kategori -> sub-kategori.
4. Filter layanan
   Pengguna dapat memfilter daftar jasa berdasarkan kategori.
5. Upload media
   Mendukung upload gambar cover jasa dan foto sertifikat (JPG/PNG, maksimal 2MB).
6. Validasi data
   Validasi input di sisi backend dan frontend untuk mencegah data tidak valid.

## Alur Pengguna Kos
1. Pengguna membuka aplikasi dan login atau daftar akun.
2. Pengguna melihat daftar jasa pada beranda.
3. Pengguna memfilter jasa sesuai kebutuhan.
4. Pengguna membuka halaman detail untuk melihat informasi lengkap.
5. Pengguna terautentikasi dapat menambah atau mengelola data jasa.

## Tech Stack
1. Backend: PHP Native
2. Database: MySQL / MariaDB
3. Frontend: HTML, CSS, JavaScript
4. Web Server: Apache (XAMPP/LAMP)

## Struktur Proyek Singkat
1. `index.php`: daftar jasa + filter kategori.
2. `login.php`, `register.php`, `logout.php`: autentikasi pengguna.
3. `form_jasa.php`: form tambah/edit jasa.
4. `save_service.php`: proses simpan data jasa.
5. `detail.php`: detail jasa.
6. `delete.php`: hapus jasa.
7. `config/database.php`: konfigurasi koneksi database.
8. `database.sql`: skema tabel + data awal.

## Cara Menjalankan di Localhost

### 1. Prasyarat
Pastikan sudah terpasang:
1. PHP 8.x
2. MySQL/MariaDB
3. Apache (atau XAMPP/LAMP)

### 2. Letakkan Proyek di Web Root
Contoh pada Linux:
```bash
/var/www/html/kosera-mitra
```

### 3. Buat dan Import Database
Jalankan perintah:
```bash
mysql -u root -p < database.sql
```

Catatan:
Perintah di atas akan membuat database `kosera_db` otomatis sesuai isi file SQL.

### 4. Atur Konfigurasi Database
Buka file `config/database.php`, lalu sesuaikan nilai berikut dengan environment lokal:
1. `$DB_HOST`
2. `$DB_USER`
3. `$DB_PASS`
4. `$DB_NAME`

### 5. Jalankan Apache dan MySQL
Pastikan service Apache dan MySQL aktif.

### 6. Akses Aplikasi
Buka browser:
```text
http://localhost/kosera-mitra/login.php
```

Jika belum punya akun, daftar melalui halaman register.

## Akun Uji
Database awal menyertakan satu akun admin pada tabel `users`.
Jika tidak mengetahui kata sandinya, lakukan registrasi akun baru dari halaman aplikasi.

## Catatan Pengembangan
1. Aplikasi menyimpan gambar dalam database (`LONGBLOB`).
2. Hindari upload file berukuran besar agar performa tetap baik.
3. Untuk produksi, disarankan memindahkan kredensial database ke environment variable.

## Lisensi
Portfolio pembelajaran dan pengembangan internal.
