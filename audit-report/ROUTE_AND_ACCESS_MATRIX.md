# ROUTE AND ACCESS MATRIX — SISTEM IURAN SAMPAH DESA

Matriks otorisasi ini mendokumentasikan setiap route yang terdaftar di aplikasi dan hasil pengujian hak akses berdasarkan 3 kategori pengguna: **Guest**, **Warga**, dan **Admin**.

---

## 1. Matriks Route Publik & Autentikasi

| Method | URI Route | Nama Route | Handler Controller | Guest | Warga | Admin | Status Proteksi |
| :--- | :--- | :--- | :--- | :---: | :---: | :---: | :--- |
| `GET` | `/` | - | Redirect ke login | 302 | 302 | 302 | **PASS** |
| `GET` | `/login` | `login` | `AuthController@showLogin` | 200 | 302 (Ke dashboard) | 302 (Ke dashboard) | **PASS** (Protected via `guest`) |
| `POST` | `/login` | `login.post` | `AuthController@login` | 200/302 | 403/Redirect | 403/Redirect | **PASS** (Throttled: 5/min) |
| `GET` | `/register` | `register` | `AuthController@showRegister` | 200 | 302 | 302 | **PASS** (Protected via `guest`) |
| `POST` | `/register` | `register.post` | `AuthController@register` | 302 | 302 | 302 | **PASS** (Hardcoded role `warga`) |
| `POST` | `/logout` | `logout` | `AuthController@logout` | 302 | 302 | 302 | **PASS** (Protected via `auth`) |

---

## 2. Matriks Route Khusus Admin (`prefix: admin`, `role: admin`)

| Method | URI Route | Nama Route | Handler Controller | Guest | Warga | Admin |
| :--- | :--- | :--- | :--- | :---: | :---: | :---: |
| `GET` | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | 302 | 403 | **200** |
| `GET` | `/admin/warga` | `admin.warga.index` | `Admin\WargaController@index` | 302 | 403 | **200** |
| `POST` | `/admin/warga` | `admin.warga.store` | `Admin\WargaController@store` | 302 | 403 | **302** |
| `GET` | `/admin/tagihan` | `admin.tagihan.index` | `Admin\TagihanController@index` | 302 | 403 | **200** |
| `POST` | `/admin/tagihan` | `admin.tagihan.store` | `Admin\TagihanController@store` | 302 | 403 | **302** |
| `GET` | `/admin/pembayaran` | `admin.pembayaran.index` | `Admin\PembayaranController@index` | 302 | 403 | **200** |
| `POST` | `/admin/pembayaran` | `admin.pembayaran.store` | `Admin\PembayaranController@store` | 302 | 403 | **302** |
| `GET` | `/admin/pembayaran-verifikasi` | `admin.pembayaran.verifikasi` | `Admin\PembayaranController@verifikasi` | 302 | 403 | **200** |
| `POST` | `/admin/pembayaran/{id}/setujui` | `admin.pembayaran.setujui` | `Admin\PembayaranController@setujui` | 302 | 403 | **302** |
| `POST` | `/admin/pembayaran/{id}/tolak` | `admin.pembayaran.tolak` | `Admin\PembayaranController@tolak` | 302 | 403 | **302** |
| `GET` | `/admin/payment-setting` | `admin.payment-setting.index` | `Admin\PaymentSettingController@index` | 302 | 403 | **200** |
| `POST` | `/admin/payment-setting/{id}/toggle` | `admin.payment-setting.toggle` | `Admin\PaymentSettingController@toggleActive` | 302 | 403 | **302** |
| `GET` | `/admin/user` | `admin.user.index` | `Admin\UserController@index` | 302 | 403 | **200** |
| `GET` | `/admin/activity-log` | `admin.activity-log.index` | `Admin\ActivityLogController@index` | 302 | 403 | **200** |
| `GET` | `/admin/laporan` | `admin.laporan.index` | `Admin\LaporanController@index` | 302 | 403 | **200** |
| `GET` | `/admin/laporan/export-pdf` | `admin.laporan.export.pdf` | `Admin\LaporanController@exportPdf` | 302 | 403 | **200** |
| `GET` | `/admin/laporan/export-excel` | `admin.laporan.export.excel` | `Admin\LaporanController@exportExcel` | 302 | 403 | **200** |

*Keterangan Hasil:* Warga yang mencoba membuka URL admin mendapatkan respons **HTTP 403 Forbidden** secara konsisten.

---

## 3. Matriks Route Khusus Warga (`prefix: warga`, `role: warga`)

| Method | URI Route | Nama Route | Handler Controller | Guest | Warga | Admin |
| :--- | :--- | :--- | :--- | :---: | :---: | :---: |
| `GET` | `/warga/dashboard` | `warga.dashboard` | `Warga\DashboardController@index` | 302 | **200** | 403 |
| `GET` | `/warga/tagihan` | `warga.tagihan.index` | `Warga\TagihanWargaController@index` | 302 | **200** | 403 |
| `GET` | `/warga/tagihan/{id}` | `warga.tagihan.show` | `Warga\TagihanWargaController@show` | 302 | **200** (Jika milik sendiri) | 403 |
| `POST` | `/warga/tagihan/{id}/upload-bukti` | `warga.tagihan.upload-bukti` | `Warga\TagihanWargaController@uploadBukti` | 302 | **302** (Jika valid) | 403 |
| `GET` | `/warga/riwayat` | `warga.riwayat.index` | `Warga\RiwayatController@index` | 302 | **200** | 403 |

*Uji Pencegahan IDOR:* Ketika Warga A mencoba mengakses `GET /warga/tagihan/{id}` milik Warga B, controller memanggil `abort_if($pembayaran->warga_id !== $warga->id, 403)` dan server mengembalikan **HTTP 403 Forbidden**.

---

## 4. Matriks Route Aset Berkas (`storage`)

| Method | URI Route | Nama Route | Keterangan & Proteksi | Status |
| :--- | :--- | :--- | :--- | :---: |
| `GET` | `/storage/{path}` | `storage.local` | Melayani berkas publik (`qris/` dan `proofs/`) dari `storage/app/public/`. Mengembalikan 404 jika file tidak ditemukan. | **PASS** |
