# Sistem Informasi Pengelolaan Iuran Sampah Desa
**Framework:** Laravel 10 | **PHP:** 8.1+ | **Database:** MySQL

---

## 📋 Deskripsi

Sistem web berbasis Laravel untuk mengelola iuran sampah desa, mencakup pencatatan warga, tagihan bulanan, pembayaran, dan laporan. Dibangun berdasarkan hasil observasi dan wawancara langsung di lapangan.

---

## 🚀 Cara Instalasi

### 1. Clone / Extract Project
```bash
# Extract zip, lalu masuk ke folder:
cd sistem-iuran-sampah
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

Edit `.env`, sesuaikan bagian database:
```env
DB_DATABASE=sistem_iuran_sampah
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Buat Database
```sql
CREATE DATABASE sistem_iuran_sampah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan Migration + Seeder
```bash
php artisan migrate
php artisan db:seed
```

Atau gunakan `database.sql` langsung via phpMyAdmin / MySQL CLI:
```bash
mysql -u root -p sistem_iuran_sampah < database.sql
```

### 6. Jalankan Server
```bash
php artisan serve
```

Akses: **http://localhost:8000**

---

## 👤 Akun Default

| Role  | Email             | Password |
|-------|-------------------|----------|
| Admin | admin@desa.id     | password |
| Warga | budi@warga.id     | password |
| Warga | siti@warga.id     | password |
| Warga | ahmad@warga.id    | password |
| Warga | dewi@warga.id     | password |
| Warga | hendra@warga.id   | password |

---

## 📁 Struktur Folder

```
app/
├── Exports/
│   └── PembayaranExport.php        # Export Excel
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── WargaController.php
│   │   │   ├── TagihanController.php
│   │   │   ├── PembayaranController.php
│   │   │   ├── UserController.php
│   │   │   └── LaporanController.php
│   │   └── Warga/
│   │       ├── DashboardController.php
│   │       ├── TagihanWargaController.php
│   │       └── RiwayatController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php      # Proteksi role admin/warga
│   └── Requests/                   # Form Request Validation
│       ├── LoginRequest.php
│       ├── Warga/
│       ├── Tagihan/
│       ├── Pembayaran/
│       └── User/
├── Models/
│   ├── User.php
│   ├── Warga.php
│   ├── Tagihan.php
│   └── Pembayaran.php
database/
├── migrations/                     # 4 migration files
└── seeders/                        # UserSeeder, WargaSeeder, TagihanSeeder, PembayaranSeeder
resources/views/
├── layouts/app.blade.php           # Layout utama
├── auth/login.blade.php
├── admin/
│   ├── dashboard/
│   ├── warga/     (index, create, edit, show)
│   ├── tagihan/   (index, create, edit, show)
│   ├── pembayaran/(index, create, edit, show)
│   ├── user/      (index, create, edit)
│   └── laporan/   (index, pembayaran, tagihan, warga, pdf)
└── warga/
    ├── dashboard/
    ├── tagihan/
    └── pembayaran/
routes/web.php
```

---

## ✅ Fitur

### Admin / Bendahara
- [x] Login & Logout
- [x] Dashboard (statistik + tabel terbaru)
- [x] CRUD Data Warga (beserta akun login)
- [x] CRUD Data Tagihan (per bulan/tahun)
- [x] CRUD Data Pembayaran
- [x] CRUD Data User
- [x] Laporan Pembayaran (filter bulan/tahun)
- [x] Laporan Tagihan
- [x] Laporan Warga
- [x] Export PDF (DomPDF)
- [x] Export Excel (Maatwebsite)

### Warga
- [x] Login & Logout
- [x] Dashboard (statistik personal)
- [x] Lihat Tagihan Saya
- [x] Riwayat Pembayaran

---

## 🗄️ ERD Singkat

```
users ──< warga >──< pembayaran >── tagihan
│                         │
│ id, name, email,        │ id, warga_id,
│ password, role          │ tagihan_id,
│                         │ tanggal_bayar,
└── hasOne: warga         │ jumlah_bayar,
                          │ status
```

---

## 📦 Package yang Digunakan

| Package                  | Fungsi              |
|--------------------------|---------------------|
| laravel/framework ^10    | Core framework      |
| barryvdh/laravel-dompdf  | Export PDF          |
| maatwebsite/excel ^3.1   | Export Excel        |

---

## ⚙️ Konfigurasi Tambahan (jika diperlukan)

Publish config DomPDF:
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

Publish config Excel:
```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
```

---

## 📝 Catatan

- Sistem ini dibangun untuk studi kasus nyata pengelolaan iuran sampah desa.
- Middleware `role` melindungi seluruh route admin dan warga.
- Form Request Validation memastikan data selalu bersih sebelum masuk Controller.
- Eloquent Relationship digunakan penuh — tidak ada raw query builder.
- Bootstrap 5 digunakan untuk UI yang ringan dan responsif.
