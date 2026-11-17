# CHANGELOG AUDIT & PERBAIKAN — SISTEM IURAN SAMPAH DESA

Dokumen ini mencatat seluruh riwayat penyesuaian, perbaikan bug, dan penguatan keamanan yang diterapkan pada proyek selama siklus audit.

---

## 1. Ringkasan Perubahan Berkas

### A. Routing & Penyajian Berkas (`routes/web.php`)
* **Masalah:** Pada server shared hosting tanpa terminal SSH (seperti InfinityFree), perintah `php artisan storage:link` tidak dapat dijalankan sehingga gambar QRIS dan berkas bukti pembayaran menghasilkan error 404 saat diakses via URL `/storage/...`.
* **Perbaikan:** Menambahkan dynamic storage fallback route:
  ```php
  Route::get('/storage/{path}', function ($path) {
      $filePath = storage_path('app/public/' . $path);
      if (!file_exists($filePath)) {
          abort(404);
      }
      $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
      return response()->file($filePath, ['Content-Type' => $mimeType]);
  })->where('path', '.*')->name('storage.local');
  ```
* **Hasil Uji:** Berkas gambar QRIS dan bukti transfer dapat disajikan secara mulus langsung dari direktori privat tanpa memerlukan symbolic link OS.

---

### B. Sinkronisasi Skema Basis Data Lengkap (`database.sql`)
* **Masalah:** Berkas dump `database.sql` lama belum mencakup pembaruan kolom metode pembayaran (`payment_method`, `payment_channel`, `proof_of_payment`, `verification_note`) serta belum memiliki tabel `payment_settings` dan `activity_logs`.
* **Perbaikan:** Memperbarui struktur tabel `pembayaran` dengan nilai ENUM `status` yang valid (`menunggu_verifikasi`, `lunas`, `ditolak`, `belum_lunas`), menambahkan definisi tabel `payment_settings` beserta seedernya, dan menambahkan tabel `activity_logs`.
* **Hasil Uji:** Import file `database.sql` pada database bersih berhasil 100% tanpa error skema atau kehilangan kolom.

---

### C. Seeder Kanal Pembayaran (`database/seeders/DatabaseSeeder.php`)
* **Masalah:** `PaymentSettingSeeder::class` belum terdaftar dalam array pemanggilan `DatabaseSeeder.php`, sehingga saat `php artisan db:seed` dijalankan tabel metode pembayaran tetap kosong.
* **Perbaikan:** Mendaftarkan `PaymentSettingSeeder::class` ke dalam daftar seeder utama.
* **Hasil Uji:** Perintah `php artisan db:seed` otomatis menginisialisasi rekening bank (BCA, BRI, Mandiri, BNI), dompet digital (DANA, OVO, GoPay, ShopeePay), dan QRIS.

---

### D. Penanganan Autentikasi Pengguna (`app/Http/Middleware/RedirectIfAuthenticated.php`)
* **Masalah:** Redirect bawaan Laravel mengarahkan pengguna yang sudah login ke URL `/home`, yang tidak terdaftar di sistem sehingga memicu error 404.
* **Perbaikan:** Mengubah logika middleware agar me-redirect cerdas berdasarkan role:
  * Role `admin` -> `admin.dashboard`
  * Role `warga` -> `warga.dashboard`
* **Hasil Uji:** Pengguna yang sudah login dan membuka `/login` atau `/register` otomatis diarahkan ke dashboard masing-masing tanpa error 404.

---

### E. Penanganan Reverse Proxy Hosting (`app/Http/Middleware/TrustProxies.php`)
* **Masalah:** Shared hosting seperti InfinityFree menggunakan load balancer / reverse proxy di depan server web. Akibatnya, Laravel membaca protokol sebagai HTTP dan fungsi `isSecure()` menghasilkan false.
* **Perbaikan:** Mengonfigurasi `$proxies = '*'` sehingga Laravel mengenali header HTTPS dari reverse proxy dan mendeteksi enkripsi SSL secara akurat.
* **Hasil Uji:** Seluruh asset, URL redirect, dan cookie sesi aman ter-generate dengan skema `https://` yang valid.

---

### F. Penguatan Keamanan Siber Defensif
* **Perbaikan yang Diterapkan:**
  1. Hashing password Bcrypt dengan kebijakan password kuat terstandarisasi.
  2. Implementasi pembatasan laju request (*rate limiting*) pada form login (maksimal 5 percobaan gagal per menit).
  3. Pemasangan middleware `SecurityHeaders` (CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, HSTS).
  4. Pencatatan jejak audit forensik (`ActivityLog`) pada aktivitas krusial sistem.
  5. Pengujian mandiri terotomatisasi (*automated self-testing*) pada `tests/Feature/SecurityTest.php` dengan 14 skenario validasi.
