# KOSERA MITRA

 aplikasi layanan jasa untuk pengguna kos (anak kos) yang membutuhkan bantuan harian secara cepat, rapi, dan terstruktur.

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
1. `index.php`: front controller untuk semua halaman.
2. `auth.php`: helper auth + flash + redirect.
3. `app/Controllers/`: logika request untuk auth dan service.
4. `app/Models/`: akses data untuk user dan service.
5. `app/Views/`: template HTML untuk halaman, termasuk partial bersama.
6. `app/Support/`: helper session dan view rendering.
7. `config/database.php`: konfigurasi koneksi database.
8. `database.sql`: skema tabel + data awal.

## Cara Menjalankan di Localhost

### Setup untuk Linux (Ubuntu/Debian dengan LAMP)

#### 1. Prasyarat
Pastikan sudah terpasang:
```bash
sudo apt-get update
sudo apt-get install apache2 mysql-server php php-mysql php-cli
```

#### 2. Letakkan Proyek di Web Root
```bash
sudo cp -r kosera-mitra /var/www/html/
sudo chown -R www-data:www-data /var/www/html/kosera-mitra
sudo chmod -R 755 /var/www/html/kosera-mitra
```

#### 3. Buat dan Import Database
```bash
sudo mysql -u root -p < /var/www/html/kosera-mitra/database.sql
```

Jika tidak ada password root (setup default):
```bash
sudo mysql -u root < /var/www/html/kosera-mitra/database.sql
```

#### 4. Atur Konfigurasi Database di `config/database.php`
Sesuaikan dengan credential lokal Anda:
```php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = 'your_password';  // Sesuaikan dengan password MySQL Anda
$DB_NAME = 'kosera_db';
```

#### 5. Pastikan Apache dan MySQL Berjalan
```bash
sudo systemctl start apache2
sudo systemctl start mysql
sudo systemctl enable apache2
sudo systemctl enable mysql
```

#### 6. Akses Aplikasi
Buka browser:
```text
http://localhost/kosera-mitra/index.php?page=auth/login
```

---

### Setup untuk Windows dengan XAMPP

#### 1. Download dan Install XAMPP
- Kunjungi https://www.apachefriends.org/
- Download XAMPP for Windows (PHP 8.x)
- Jalankan installer dan pilih komponen:
  - ✅ Apache
  - ✅ MySQL
  - ✅ PHP
  - ✅ phpMyAdmin (opsional, tapi memudahkan)

#### 2. Letakkan Proyek di XAMPP htdocs
- Ekstrak folder `kosera-mitra` ke: `C:\xampp\htdocs\kosera-mitra\`
- Struktur folder akan seperti:
  ```
  C:\xampp\htdocs\kosera-mitra\
  ├── index.php
  ├── config/
  ├── assets/
  └── ...
  ```

#### 3. Mulai Apache dan MySQL dari XAMPP Control Panel
- Buka XAMPP Control Panel (`C:\xampp\xampp-control.exe`)
- Klik **Start** pada baris Apache
- Klik **Start** pada baris MySQL
- Tunggu sampai status menunjukkan "Running" (text berwarna hijau)

#### 4. Buat Database via phpMyAdmin atau Command Line

**Cara A: Menggunakan phpMyAdmin (Mudah)**
- Buka browser: `http://localhost/phpmyadmin`
- Login dengan user `root`, password kosong (default XAMPP)
- Klik menu **Import** di bagian atas
- Pilih file `database.sql` dari folder kosera-mitra
- Klik **Go** untuk mengimport
- Database `kosera_db` akan terbuat otomatis

**Cara B: Command Line**
- Buka Command Prompt (`cmd.exe`)
- Arahkan ke folder XAMPP:
  ```cmd
  cd C:\xampp\mysql\bin
  ```
- Import database:
  ```cmd
  mysql -u root < C:\xampp\htdocs\kosera-mitra\database.sql
  ```
  (tanpa password, karena default XAMPP tidak ada password untuk root)

#### 5. Atur Konfigurasi Database di `config/database.php`
- Buka file: `C:\xampp\htdocs\kosera-mitra\config\database.php`
- Sesuaikan konfigurasi (default XAMPP sudah tepat):
  ```php
  $DB_HOST = 'localhost';
  $DB_USER = 'root';
  $DB_PASS = '';  // XAMPP default: KOSONG (no password)
  $DB_NAME = 'kosera_db';
  ```

#### 6. Verifikasi Setup
- Buka browser dan akses: `http://localhost/kosera-mitra/index.php?page=auth/login`
- Jika halaman muncul, setup sudah berhasil ✅

#### 7. Troubleshooting Windows XAMPP
Jika ada error:

**Error: "Connection refused"**
- Pastikan Apache dan MySQL sudah di-start dari XAMPP Control Panel
- Cek port 80 (Apache) dan 3306 (MySQL) tidak terpakai aplikasi lain

**Error: "Access denied for user 'root'@'localhost'"**
- Konfigurasi password di `config/database.php` tidak sesuai
- Default XAMPP: user=`root`, password=`kosong` (string kosong, bukan null)

**Error: "Database kosera_db not found"**
- Database belum di-import dari `database.sql`
- Gunakan phpMyAdmin atau command line untuk import (lihat Step 4)

**Port 80 sudah terpakai**
- Buka XAMPP Control Panel → Apache → Config → Apache (`httpd.conf`)
- Ubah baris `Listen 80` menjadi port lain, misal `Listen 8080`
- Akses dengan: `http://localhost:8080/kosera-mitra/index.php?page=auth/login`

## Akun Uji
Database awal menyertakan satu akun admin pada tabel `users`:
- **Email**: `admin@kosera.local`
- **Password**: Periksa langsung dari database atau lakukan registrasi akun baru

Jika tidak mengetahui password akun admin, Anda dapat langsung daftar akun baru melalui halaman register aplikasi tanpa batasan khusus.

## Database Schema

### Tabel `users`
```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  phone VARCHAR(15),
  email VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel `categories`
```sql
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL
);
```

### Tabel `sub_categories`
```sql
CREATE TABLE sub_categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### Tabel `services`
```sql
CREATE TABLE services (
  id INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT NOT NULL,
  sub_category_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(12, 2),
  provider_name VARCHAR(100),
  image LONGBLOB,            -- Gambar cover (JPG/PNG, max 2MB)
  certificate LONGBLOB,      -- Gambar sertifikat (JPG/PNG, max 2MB)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (sub_category_id) REFERENCES sub_categories(id)
);
```

### Tabel `orderan`
```sql
CREATE TABLE orderan (
  id INT PRIMARY KEY AUTO_INCREMENT,
  service_id INT NOT NULL,
  user_id INT,
  status VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (service_id) REFERENCES services(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Keamanan dan Best Practices

### Implementasi Keamanan
1. **Password Hashing**: Menggunakan `password_hash()` dengan algoritma bcrypt
2. **Prepared Statements**: Semua query menggunakan prepared statements untuk mencegah SQL Injection
3. **Session Protection**: Session-based authentication dengan middleware `requireLogin()`
4. **File Validation**: Upload file divalidasi tipe MIME dan ukuran
5. **URL Encode**: Filename di-encode untuk mencegah directory traversal
6. **CORS/Same-Origin**: Apache default sudah melindungi

### Rekomendasi untuk Produksi
Sebelum deploy ke production:

1. **Gunakan Environment Variables untuk Credentials**
   ```php
   // Ganti config/database.php
   $DB_USER = $_ENV['DB_USER'] ?? 'root';
   $DB_PASS = $_ENV['DB_PASS'] ?? '';
   ```

2. **Aktifkan HTTPS**
   - Setup SSL certificate (Let's Encrypt gratis)
   - Redirect HTTP ke HTTPS di Apache config

3. **Konfigurasi PHP.ini**
   ```ini
   upload_max_filesize = 2M
   post_max_size = 2M
   max_execution_time = 30
   display_errors = Off
   log_errors = On
   ```

4. **Backup Database Berkala**
   ```bash
   mysqldump -u root -p kosera_db > backup.sql
   ```

5. **Monitor Log Files**
   - Apache: `/var/log/apache2/error.log` (Linux)
   - MySQL: `/var/log/mysql/error.log` (Linux)
   - PHP: Configure di `php.ini`

## Struktur File Lengkap

```
kosera-mitra/
├── index.php                    # Front controller
├── auth.php                     # Helper functions & middleware
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php   # Controller login/register/logout
│   │   └── ServiceController.php # Controller index/detail/form/save/delete/image
│   ├── Models/
│   │   ├── UserModel.php        # Query data user
│   │   └── ServiceModel.php     # Query data layanan
│   ├── Support/
│   │   ├── Session.php          # Session initialization dan login/logout session flow
│   │   └── View.php             # Helper render view
│   └── Views/
│       ├── auth/
│       │   ├── login.php         # View login
│       │   └── register.php      # View registrasi
│       ├── services/
│       │   ├── index.php         # View daftar jasa
│       │   ├── detail.php        # View detail jasa
│       │   └── form.php          # View form tambah/edit jasa
│       └── partials/
│           ├── head.php          # Pembuka dokumen HTML + link CSS
│           ├── end.php           # Penutup dokumen HTML
│           ├── header-home.php   # Header halaman daftar jasa
│           └── header-service.php # Header halaman service
├── config/
│   └── database.php             # Konfigurasi koneksi MySQL
├── assets/
│   ├── logo.png                 # Logo brand KOSERA
│   ├── css/
│   │   └── style.css            # Stylesheet utama
│   └── js/
│       └── script.js            # JavaScript untuk interaksi
├── database.sql                 # SQL dump untuk setup database
└── README.md                    # File ini
```

## Troubleshooting

### Error: "500 Internal Server Error"
**Penyebab**: Error pada PHP script atau konfigurasi database salah
**Solusi**:
1. Cek error log Apache/PHP
2. Verifikasi `config/database.php` sesuai dengan MySQL credentials
3. Pastikan database `kosera_db` sudah dibuat
4. Jalankan `php -l filename.php` untuk check syntax error

### Error: "Database connection failed"
**Penyebab**: MySQL tidak berjalan atau password salah
**Solusi**:
- Pastikan MySQL service aktif: `systemctl status mysql` (Linux) atau XAMPP Control Panel (Windows)
- Update password di `config/database.php`

### Error: "Call Stack" saat upload gambar
**Penyebab**: Binding parameter tipe data salah untuk LONGBLOB
**Solusi**: Gunakan tipe `'b'` (binary) di `bind_param()`, bukan `'s'` (string)

### Gambar tidak muncul setelah upload
**Penyebab**: File LONGBLOB tidak tersimpan dengan benar
**Solusi**:
1. Cek ukuran file (max 2MB)
2. Cek tipe file (JPG/PNG)
3. Jalankan query: `SELECT LENGTH(image) FROM services WHERE id = X;`
4. Jika ukuran 0, ada error saat proses penyimpanan

### Redirect loop di halaman login
**Penyebab**: Session tidak berfungsi atau `requireLogin()` error
**Solusi**:
1. Clear browser cookies/cache
2. Cek folder `tmp/` permission untuk session files
3. Verifikasi `session_start()` sudah di-include di semua halaman

## Support & Kontribusi

Aplikasi ini dikembangkan untuk memudahkan pengguna kos mencari jasa berkualitas. 

Jika ada bugs, feature requests, atau improvements:
1. Test di environment lokal (LAMP/XAMPP)
2. Dokumentasikan issue dengan detail
3. Submit pull request dengan deskripsi perubahan


