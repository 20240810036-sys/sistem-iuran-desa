# REVISI: Fitur Pembayaran Digital — Sistem Iuran Sampah

Revisi ini bersifat **incremental** — hanya menambah/mengubah file yang diperlukan, tanpa merusak CRUD yang sudah berjalan.

---

## Daftar File yang Diberikan

### Migration (baru)
| File | Keterangan |
|------|-----------|
| `database/migrations/2024_06_11_000001_add_payment_fields_to_pembayaran_table.php` | Tambah kolom baru di tabel `pembayaran` |
| `database/migrations/2024_06_11_000002_create_payment_settings_table.php` | Buat tabel `payment_settings` |

### Model
| File | Keterangan |
|------|-----------|
| `app/Models/Pembayaran.php` | **GANTI** model lama — tambah fillable & helper baru |
| `app/Models/PaymentSetting.php` | **BARU** |

### Controller
| File | Keterangan |
|------|-----------|
| `app/Http/Controllers/Admin/DashboardController.php` | **GANTI** — tambah statistik verifikasi |
| `app/Http/Controllers/Admin/PembayaranController.php` | **GANTI** — tambah `verifikasi`, `setujui`, `tolak` |
| `app/Http/Controllers/Admin/PaymentSettingController.php` | **BARU** — CRUD pengaturan pembayaran |
| `app/Http/Controllers/Warga/TagihanWargaController.php` | **GANTI** — tambah `show` & `uploadBukti` |

### Form Request
| File | Keterangan |
|------|-----------|
| `app/Http/Requests/Pembayaran/StorePembayaranRequest.php` | **GANTI** — tambah field baru |
| `app/Http/Requests/Pembayaran/UpdatePembayaranRequest.php` | **GANTI** — tambah field baru |
| `app/Http/Requests/Pembayaran/VerifikasiPembayaranRequest.php` | **BARU** |
| `app/Http/Requests/Pembayaran/UploadBuktiRequest.php` | **BARU** |
| `app/Http/Requests/PaymentSetting/StorePaymentSettingRequest.php` | **BARU** |
| `app/Http/Requests/PaymentSetting/UpdatePaymentSettingRequest.php` | **BARU** |

### Routes
| File | Keterangan |
|------|-----------|
| `routes/web.php` | **GANTI** — tambah route verifikasi, payment-setting, show tagihan warga |

### Blade Views
| File | Keterangan |
|------|-----------|
| `resources/views/admin/dashboard/index.blade.php` | **GANTI** — tambah stat verifikasi |
| `resources/views/admin/pembayaran/verifikasi.blade.php` | **BARU** |
| `resources/views/admin/payment_setting/index.blade.php` | **BARU** |
| `resources/views/admin/payment_setting/create.blade.php` | **BARU** |
| `resources/views/admin/payment_setting/edit.blade.php` | **BARU** |
| `resources/views/warga/tagihan/index.blade.php` | **GANTI** — tambah kolom aksi & tombol bayar |
| `resources/views/warga/tagihan/show.blade.php` | **BARU** — halaman pembayaran lengkap |

### Seeder
| File | Keterangan |
|------|-----------|
| `database/seeders/PaymentSettingSeeder.php` | **BARU** — data awal bank, e-wallet, QRIS |

### SQL
| File | Keterangan |
|------|-----------|
| `ALTER_TABLE.sql` | SQL manual jika tidak memakai artisan migrate |

---

## Langkah Instalasi

### 1. Copy file ke project

Salin seluruh isi folder revisi ini ke dalam project Laravel Anda, **timpa file lama** yang namanya sama.

```
cp -r revisi/* /path/to/sistem_iuran/
```

Direktori baru yang perlu dibuat otomatis:
- `app/Http/Controllers/Admin/PaymentSettingController.php`
- `app/Http/Requests/PaymentSetting/`
- `resources/views/admin/payment_setting/`
- `resources/views/warga/tagihan/show.blade.php`

### 2. Jalankan migration

```bash
php artisan migrate
```

### 3. Buat symbolic link storage (jika belum)

```bash
php artisan storage:link
```

### 4. Jalankan seeder (opsional)

```bash
php artisan db:seed --class=PaymentSettingSeeder
```

Atau tambahkan ke `DatabaseSeeder.php`:
```php
$this->call(PaymentSettingSeeder::class);
```

### 5. Tambahkan menu sidebar di `layouts/app.blade.php`

Tambahkan dua link berikut di bagian sidebar **Admin** (setelah link Pembayaran yang sudah ada):

```blade
{{-- Verifikasi Pembayaran (Admin) --}}
<a href="{{ route('admin.pembayaran.verifikasi') }}"
   class="sidebar-link {{ request()->routeIs('admin.pembayaran.verifikasi') ? 'active' : '' }}">
    <i class="bi bi-hourglass-split"></i> Verifikasi Pembayaran
</a>

{{-- Pengaturan Pembayaran (Admin) --}}
<a href="{{ route('admin.payment-setting.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.payment-setting.*') ? 'active' : '' }}">
    <i class="bi bi-gear"></i> Pengaturan Pembayaran
</a>
```

### 6. Clear cache

```bash
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

---

## Fitur yang Ditambahkan

| # | Fitur | Lokasi |
|---|-------|--------|
| 1 | Metode pembayaran (transfer bank, QRIS, e-wallet, tunai) | Warga → Detail Tagihan |
| 2 | QR Code — tampil, unduh PNG, cetak | Warga → Detail Tagihan |
| 3 | Upload bukti pembayaran (jpg/png/pdf, maks 2MB) | Warga → Detail Tagihan |
| 4 | Status `menunggu_verifikasi`, `lunas`, `ditolak` | Model & DB |
| 5 | Antrian verifikasi pembayaran admin | Admin → Verifikasi Pembayaran |
| 6 | Setujui / Tolak + catatan penolakan | Admin → Verifikasi Pembayaran |
| 7 | Statistik menunggu / lunas / ditolak di dashboard | Admin → Dashboard |
| 8 | CRUD Pengaturan Pembayaran (bank, e-wallet, QRIS statis) | Admin → Pengaturan Pembayaran |

---

## Catatan Teknis

- **Tidak ada migration lama yang diubah** — semua perubahan melalui migration tambahan.
- QRIS menggunakan **statis** (satu gambar untuk semua warga), diunggah admin di menu Pengaturan Pembayaran.
- Bukti pembayaran disimpan di `storage/app/public/proofs` (akses via `storage/proofs`).
- Gambar QRIS disimpan di `storage/app/public/qris`.
- Warga hanya bisa mengakses detail tagihan miliknya sendiri (enforce via `abort_if`).
- Status lama `belum_lunas` tetap valid — tidak ada data yang rusak.
