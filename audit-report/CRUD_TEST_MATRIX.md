# CRUD TEST MATRIX — SISTEM IURAN SAMPAH DESA

Matriks ini menguji siklus operasi dasar data (**Create, Read, Update, Delete**) pada setiap modul aplikasi beserta verifikasi integritas data dan hak aksesnya.

---

## 1. Modul Pengguna (User Management)
* **Model:** `App\Models\User` | **Tabel:** `users`

| Operasi | Aktor Diizinkan | Validasi & Integritas Data | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Create** | Admin, Guest (via Register) | Email unique, format email valid, password min 8 karakter + huruf besar + angka + simbol | Data tersimpan, password otomatis di-hash Bcrypt | **PASS** |
| **Read** | Admin | Menampilkan daftar user, pagination 10 item, filter role | Data tampil lengkap tanpa mengekspos hash password | **PASS** |
| **Update** | Admin | Validasi unique email kecuali ID bersangkutan; password opsional diubah | Data terupdate aman | **PASS** |
| **Delete** | Admin | Proteksi pencegahan penghapusan akun admin sendiri | Berhasil dihapus beserta relasi dependensinya | **PASS** |

---

## 2. Modul Data Warga
* **Model:** `App\Models\Warga` | **Tabel:** `warga`

| Operasi | Aktor Diizinkan | Validasi & Integritas Data | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Create** | Admin, Sistem (Auto pada Register) | Nama wajib, alamat wajib, nomor HP valid numerik | Terhubung one-to-one ke `user_id` | **PASS** |
| **Read** | Admin | Menampilkan daftar seluruh warga, pencarian nama/alamat | Tampil dengan pagination dan tombol aksi | **PASS** |
| **Update** | Admin | Pembaruan data profil warga | Nilai field di database tersinkronisasi | **PASS** |
| **Delete** | Admin | Menghapus record warga | Cascading delete ke tabel tagihan & pembayaran terkait | **PASS** |

---

## 3. Modul Tagihan Iuran
* **Model:** `App\Models\Tagihan` | **Tabel:** `tagihan`

| Operasi | Aktor Diizinkan | Validasi & Integritas Data | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Create** | Admin | Bulan (1-12), Tahun (4 digit), Jumlah Iuran (positif) | Otomatis membuat invoice tagihan untuk seluruh warga aktif | **PASS** |
| **Read** | Admin, Warga (Milik Sendiri) | Admin melihat rekap total; Warga melihat status bayar miliknya | Query tersaring berdasarkan otorisasi session | **PASS** |
| **Update** | Admin | Pembaruan nominal iuran atau tahun periode | Data tagihan terbarui | **PASS** |
| **Delete** | Admin | Konfirmasi penghapusan periode tagihan | Invoice terkait ikut terhapus | **PASS** |

---

## 4. Modul Pembayaran & Bukti Bayar
* **Model:** `App\Models\Pembayaran` | **Tabel:** `pembayaran`

| Operasi | Aktor Diizinkan | Validasi & Integritas Data | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Create** | Admin (Manual), Warga (Upload Bukti) | Validasi mime: jpg, jpeg, png, pdf; max: 2048 KB | Berkas disimpan di `storage/app/public/proofs/` dan path tercatat | **PASS** |
| **Read** | Admin, Warga (Milik Sendiri) | Warga hanya dapat melihat tagihan dengan `warga_id` miliknya | Mencegah IDOR via otorisasi controller | **PASS** |
| **Update** | Admin (Verifikasi), Warga (Ganti Bukti) | Admin mengubah status menjadi `lunas` / `ditolak` + catatan verifikasi | Status terupdate dan tercatat di audit trail | **PASS** |
| **Delete** | Admin | Menghapus entri transaksi pembayaran | Berkas fisik bukti dihapus via `Storage::delete` | **PASS** |

---

## 5. Modul Pengaturan Kanal Pembayaran (Payment Settings)
* **Model:** `App\Models\PaymentSetting` | **Tabel:** `payment_settings`

| Operasi | Aktor Diizinkan | Validasi & Integritas Data | Hasil Pengujian | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Create** | Admin | Tipe (`bank`/`ewallet`/`qris`), Provider, No Rekening, Atas Nama | Tersimpan di database dan siap aktif | **PASS** |
| **Read** | Admin, Warga (Hanya yang `is_active = 1`) | Warga hanya disajikan opsi pembayaran yang diaktifkan admin | Filter `active()` berjalan di controller | **PASS** |
| **Update** | Admin | Ubah nomor rekening atau unggah gambar QRIS baru | Berkas gambar QRIS lama digantikan dengan yang baru | **PASS** |
| **Delete** | Admin | Hapus kanal pembayaran | Data terhapus bersih | **PASS** |
