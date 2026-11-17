# AUDIT INVENTORY — SISTEM IURAN SAMPAH DESA

Dokumen inventaris ini memetakan seluruh aset teknis dalam proyek, mencakup struktur direktori, model database, routing, hak akses peran, serta dependensi.

---

## 1. Inventaris Direktori Utama

| Direktori | Fungsi & Tanggung Jawab |
| :--- | :--- |
| `app/Http/Controllers/Admin/` | Controller modul backend admin (Warga, Tagihan, Pembayaran, Setting, User, Laporan, Log). |
| `app/Http/Controllers/Warga/` | Controller modul mandiri warga (Dashboard, Tagihan, Riwayat Bayar). |
| `app/Http/Middleware/` | Middleware keamanan: `SecurityHeaders`, `RoleMiddleware`, `RedirectIfAuthenticated`. |
| `app/Http/Requests/` | Kelas validasi Form Request berbasis server-side. |
| `app/Models/` | Eloquent ORM: `User`, `Warga`, `Tagihan`, `Pembayaran`, `PaymentSetting`, `ActivityLog`. |
| `database/migrations/` | Definisi skema tabel database dan riwayat perubahan struktur. |
| `database/seeders/` | Data awal sistem (admin, warga, tagihan, metode pembayaran). |
| `resources/views/admin/` | Antarmuka Blade khusus untuk Administrator desa. |
| `resources/views/warga/` | Antarmuka Blade khusus untuk Warga desa. |
| `resources/views/auth/` | Halaman autentikasi (Login dan Registrasi Warga). |
| `storage/app/public/` | Direktori penyimpanan berkas publik (QRIS dan bukti pembayaran). |
| `tests/Feature/` | Skrip pengujian otomatis keamanan (`SecurityTest.php`). |

---

## 2. Inventaris Entitas Database & Relasi

1. **`users`**
   * Primary Key: `id` (BIGINT UNSIGNED)
   * Kolom: `name`, `email` (UNIQUE), `password` (Bcrypt), `role` (`admin`/`warga`), timestamps.
   * Relasi: `hasOne(Warga)`, `hasMany(ActivityLog)`.
2. **`warga`**
   * Primary Key: `id` (BIGINT UNSIGNED)
   * Foreign Key: `user_id` -> `users.id` (ON DELETE CASCADE)
   * Kolom: `nama`, `alamat`, `no_hp`, timestamps.
   * Relasi: `belongsTo(User)`, `hasMany(Pembayaran)`.
3. **`tagihan`**
   * Primary Key: `id` (BIGINT UNSIGNED)
   * Kolom: `bulan` (INT 1-12), `tahun` (INT), `jumlah_iuran` (INT UNSIGNED), timestamps.
   * Relasi: `hasMany(Pembayaran)`.
4. **`pembayaran`**
   * Primary Key: `id` (BIGINT UNSIGNED)
   * Foreign Keys: `warga_id` -> `warga.id`, `tagihan_id` -> `tagihan.id` (ON DELETE CASCADE)
   * Kolom: `tanggal_bayar`, `jumlah_bayar`, `status` (`menunggu_verifikasi`, `lunas`, `ditolak`, `belum_lunas`), `payment_method`, `payment_channel`, `proof_of_payment`, `verification_note`, timestamps.
5. **`payment_settings`**
   * Primary Key: `id` (BIGINT UNSIGNED)
   * Kolom: `type` (`bank`, `ewallet`, `qris`), `provider_name`, `account_number`, `account_name`, `qr_image`, `is_active`, timestamps.
6. **`activity_logs`**
   * Primary Key: `id` (BIGINT UNSIGNED)
   * Foreign Key: `user_id` -> `users.id` (ON DELETE SET NULL)
   * Kolom: `action`, `description`, `ip_address`, `user_agent`, `created_at`.

---

## 3. Inventaris Hak Akses (Role-Based Access Control)

| Role | Batasan & Hak Akses |
| :--- | :--- |
| **Guest (Unauthenticated)** | Hanya dapat mengakses `/login` dan `/register`. Seluruh request ke `/admin/*` dan `/warga/*` langsung diarahkan ke `/login`. |
| **Warga** | Mengakses `/warga/dashboard`, melihat tagihan pribadi `/warga/tagihan`, mengunggah bukti bayar `/warga/tagihan/{id}/upload-bukti`, melihat `/warga/riwayat`. Dilarang membuka `/admin/*` (HTTP 403 Forbidden). |
| **Admin** | Memiliki kontrol penuh terhadap data master: Warga, Tagihan, Pembayaran, Verifikasi Pembayaran, Pengaturan Kanal Bayar, Manajemen User, Log Aktivitas Audit Trail, dan Ekspor Laporan. Dilarang membuka halaman warga. |

---

## 4. Inventaris Dependensi Paket (Composer)

| Nama Paket | Versi | Status Lisensi & Keamanan |
| :--- | :---: | :--- |
| `laravel/framework` | `^12.0` | MIT (Bebas kerentanan, modern LTS release) |
| `barryvdh/laravel-dompdf` | `~3.0` | LGPL-2.1 (Pembangkit laporan PDF sisi server) |
| `maatwebsite/excel` | `~3.1` | MIT (Pembangkit data format spreadsheet) |
| `laravel/sanctum` | `^4.0` | MIT (Proteksi API & CSRF Cookie) |
| `phpunit/phpunit` | `~11.5` | BSD-3-Clause (Harness self-testing otomatis) |
