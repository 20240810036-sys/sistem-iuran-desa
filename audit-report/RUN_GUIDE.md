# PANDUAN MENJALANKAN PROYEK (RUN GUIDE)
## SISTEM IURAN SAMPAH DESA

Panduan ini mendokumentasikan langkah-langkah untuk menyiapkan, menginstal dependensi, mengonfigurasi basis data, dan menjalankan aplikasi Sistem Iuran Sampah Desa baik di lingkungan lokal (**Localhost**) maupun di server produksi (**Shared Hosting**).

---

## 1. Prasyarat Sistem
* **PHP:** Versi 8.2 atau lebih baru.
  * Ekstensi PHP yang wajib aktif: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`.
* **Database:** MySQL 8.0+ atau MariaDB 10.4+.
* **Composer:** Versi 2.5 atau lebih baru.
* **Web Browser:** Google Chrome, Mozilla Firefox, Microsoft Edge, atau browser modern lainnya.

---

## 2. Instalasi di Lingkungan Lokal (Localhost)

### Langkah 1: Kloning Repositori & Masuk Folder
```bash
git clone https://github.com/20240810036-sys/sistem-iuran-desa.git
cd sistem-iuran-desa
```

### Langkah 2: Instalasi Dependensi PHP
```bash
composer install
```

### Langkah 3: Konfigurasi Environment (`.env`)
Salin file konfigurasi contoh menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` lalu sesuaikan konfigurasi koneksi basis data Anda:
```env
APP_NAME="Sistem Iuran Sampah Desa"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_iuran_sampah
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 4: Generate Application Key
```bash
php artisan key:generate
```

### Langkah 5: Migrasi Database & Data Awal (Seeder)
Buat database baru bernama `db_iuran_sampah` di MySQL/phpMyAdmin lokal Anda, lalu jalankan:
```bash
php artisan migrate --seed
```
*Atau alternatif menggunakan file SQL dump lengkap:*
Import file `database.sql` ke dalam database MySQL Anda via phpMyAdmin.

### Langkah 6: Pembuatan Symlink Direktori Penyimpanan
```bash
php artisan storage:link
```

### Langkah 7: Menjalankan Server Lokal
```bash
php artisan serve
```
Aplikasi kini dapat diakses di browser melalui tautan:  
**`http://127.0.0.1:8000`** atau **`http://localhost:8000`**

---

## 3. Akun Uji Coba Default (Demo Credentials)

> [!NOTE]
> Format password mematuhi kebijakan password kuat (minimal 8 karakter, huruf besar, huruf kecil, angka, dan karakter khusus).

* **Akun Administrator Desa:**
  * **Email:** `admin@desa.id`
  * **Password:** `@Admindesa123`
  * **Peran:** Akses penuh ke seluruh menu administrasi, verifikasi pembayaran, manajemen warga, dan laporan.

* **Akun Warga:**
  * **Email:** `budi@warga.id`
  * **Password:** `@Wargadesa123`
  * **Peran:** Akses dashboard warga, melihat tagihan, mengunggah bukti pembayaran, dan melihat riwayat.

---

## 4. Panduan Deployment ke Shared Hosting (Contoh: InfinityFree)

1. **Struktur Folder:**
   * Seluruh isi folder proyek diunggah ke folder `htdocs/`.
   * Konfigurasi `.htaccess` di root `htdocs` harus mengarahkan traffic publik langsung ke subfolder `public/` dengan keamanan proteksi file `.env`.
2. **Konfigurasi Database Hosting:**
   * Buka control panel hosting dan buat database MySQL.
   * Masuk ke phpMyAdmin hosting dan import file `database.sql`.
   * Buka file `.env` di File Manager hosting dan sesuaikan:
     * `DB_HOST`: Host server database hosting (contoh: `sql309.infinityfree.com`)
     * `DB_DATABASE`: Nama database hosting
     * `DB_USERNAME`: Username database hosting
     * `DB_PASSWORD`: Password database hosting
3. **Penyimpanan Bukti (Storage):**
   * Di File Manager hosting, pastikan folder `storage/app/public/proofs` dan `storage/app/public/qris` telah dibuat.
   * Karena shared hosting tidak memiliki akses SSH untuk menjalankan `storage:link`, sistem telah dilengkapi dengan **Dynamic Storage Handler** di `routes/web.php` (`Route::get('/storage/{path}', ...)`) yang otomatis menyajikan berkas QRIS dan bukti transfer secara aman.
4. **Verifikasi Keamanan Akhir:**
   * Pastikan `APP_DEBUG=false` di file `.env` server hosting sebelum aplikasi resmi diserahkan atau dinilai.

---

## 5. Troubleshooting Masalah Umum

* **Error 500 saat Upload Bukti di Hosting:**
  Pastikan tipe data kolom `status` di tabel `pembayaran` sudah mencakup `menunggu_verifikasi`. Jalankan perintah SQL berikut di phpMyAdmin jika belum:
  ```sql
  ALTER TABLE `pembayaran`
  MODIFY COLUMN `status` ENUM('menunggu_verifikasi','lunas','ditolak','belum_lunas')
  NOT NULL DEFAULT 'menunggu_verifikasi';
  ```
* **Gambar QRIS / Bukti tidak muncul di Shared Hosting:**
  Pastikan file `routes/web.php` terbaru yang memiliki handler `storage/{path}` telah terunggah ke server hosting.
* **Error Token Mismatch (419 Page Expired):**
  Periksa pengaturan domain `APP_URL` dan konfigurasi `SESSION_DRIVER` di file `.env`. Bersihkan cache browser atau cookie sesi lama.
