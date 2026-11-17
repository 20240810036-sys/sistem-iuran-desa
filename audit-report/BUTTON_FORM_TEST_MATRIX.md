# BUTTON AND FORM TEST MATRIX — SISTEM IURAN SAMPAH DESA

Dokumen ini memverifikasi fungsionalitas seluruh form, input field, tombol aksi, dan elemen interaktif yang ada di dalam aplikasi.

---

## 1. Modul Autentikasi

| Halaman | Komponen / Tombol | Aksi yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Login** | Form Input Email & Password | Menerima kredensial pengguna | Kredensial divalidasi server-side | **PASS** |
| **Login** | Tombol "Masuk" (`type=submit`) | Mengirimkan form via POST dengan CSRF Token | Autentikasi berhasil, sesi diregenerasi | **PASS** |
| **Login** | Link "Daftar Warga Baru" | Mengarahkan ke halaman registrasi | Membuka route `/register` | **PASS** |
| **Register** | Form Input Lengkap (Nama, Email, Alamat, No HP, Password, Konfirmasi) | Menerima data registrasi warga | Validasi format email, nomor HP, & password kuat | **PASS** |
| **Register** | Tombol "Daftar Sekarang" | Mendaftarkan akun role warga | Akun tersimpan dengan role `warga` ter-hardcode | **PASS** |
| **Navbar** | Tombol "Logout" | Menghapus sesi pengguna | Sesi hangus, tercatat di `activity_logs`, kembali ke `/login` | **PASS** |

---

## 2. Modul Admin (Tata Kelola Data)

| Halaman | Komponen / Tombol | Aksi yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Data Warga** | Tombol "Tambah Warga" | Membuka form tambah warga | Membuka route `admin.warga.create` | **PASS** |
| **Data Warga** | Form Simpan Warga | Menyimpan data warga & relasi user | Data tersimpan di tabel `users` dan `warga` | **PASS** |
| **Data Warga** | Tombol Edit & Form Update | Memperbarui data alamat/nama/no_hp | Data terupdate tanpa merusak relasi | **PASS** |
| **Data Warga** | Tombol Hapus (Delete Modal) | Menghapus warga beserta tagihannya | Foreign key cascade menghapus data terkait | **PASS** |
| **Tagihan** | Tombol "Generate Tagihan" | Membuat tagihan periode bulan/tahun | Berhasil generate tagihan per warga | **PASS** |
| **Tagihan** | Form Edit & Hapus Tagihan | Mengubah nominal atau menghapus | Berjalan normal sesuai validasi | **PASS** |
| **Pembayaran** | Tombol "Catat Pembayaran" | Mencatat pembayaran manual (tunai) | Status langsung tercatat `lunas` | **PASS** |
| **Verifikasi** | Tombol "Setujui" (`POST`) | Menyetujui bukti transfer/QRIS warga | Status berubah jadi `lunas`, catatan dihapus | **PASS** |
| **Verifikasi** | Modal "Tolak" + Catatan | Menolak bukti bayar dengan alasan | Status berubah jadi `ditolak`, pesan tampil di warga | **PASS** |
| **Kanal Bayar** | Tombol Toggle Aktif/Nonaktif | Mengubah status aktif rekening/QRIS | Status boolean `is_active` berubah seketika | **PASS** |
| **Kelola User** | Form Ganti Password User | Memperbarui password terenkripsi | Password baru di-hash ulang dengan Bcrypt | **PASS** |
| **Laporan** | Tombol "Download PDF" | Menghasilkan berkas PDF rekapitulasi | Berkas PDF ter-render via DomPDF | **PASS** |
| **Laporan** | Tombol "Export Excel" | Menghasilkan spreadsheet `.xlsx` | Berkas terunduh via Laravel Excel | **PASS** |

---

## 3. Modul Warga (Mandiri)

| Halaman | Komponen / Tombol | Aksi yang Diharapkan | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **Dashboard** | Kartu Ringkasan Tagihan | Menampilkan jumlah tagihan tertunggak | Angka sinkron dengan query database | **PASS** |
| **Tagihan Saya** | Tombol "Bayar Sekarang" | Membuka detail instruksi pembayaran | Menampilkan rincian nominal dan pilihan kanal | **PASS** |
| **Detail Tagihan** | Pilihan Kanal (Bank / QRIS / E-Wallet / Tunai) | Mengganti tampilan rekening/QRIS dinamis | JavaScript mengaktifkan panel yang sesuai | **PASS** |
| **Detail Tagihan** | Tombol "Unduh QRIS" / "Cetak" | Membuka atau mencetak gambar QRIS | QR code terbuka dengan resolusi jelas | **PASS** |
| **Detail Tagihan** | Form File Upload Bukti (`enctype="multipart/form-data"`) | Mengunggah bukti foto struk/transfer | Bukti tersimpan ke `storage/app/public/proofs/` | **PASS** |
| **Riwayat** | Tabel Riwayat Pembayaran | Menampilkan daftar pembayaran lunas | Tabel ter-render lengkap dengan tanggal bayar | **PASS** |
