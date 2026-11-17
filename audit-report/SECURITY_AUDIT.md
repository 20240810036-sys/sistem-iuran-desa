# SECURITY AUDIT REPORT — SISTEM IURAN SAMPAH DESA

Laporan audit keamanan ini disusun berdasarkan standar penilaian **Mata Kuliah Cyber Security**, menguji aspek defensif aplikasi web secara ketat dengan pendekatan *white-box code review* dan *automated unit testing*.

---

## 1. Klasifikasi Tingkat Risiko (Risk Matrix)

| Kategori | Definisi |
| :--- | :--- |
| **CRITICAL** | Kerentanan fatal yang memungkinkan eksekusi kode acak, pengambilalihan kontrol server, atau kebocoran kredensial massal. |
| **HIGH** | Celah otorisasi seperti IDOR, bypass autentikasi, privilege escalation, atau injeksi SQL. |
| **MEDIUM** | Kelemahan keamanan kondisional, XSS tersimpan, atau ketiadaan proteksi brute-force. |
| **LOW / INFO** | Hardening header keamanan, penyempurnaan konfigurasi cookie sesi, atau audit jejak log. |

---

## 2. Rincian Temuan & Status Mitigasi Keamanan

### A. Autentikasi & Penyimpanan Password
* **Standar Evaluasi:** Password tidak boleh plaintext, wajib hashing aman (Bcrypt/Argon2), dan password lemah harus ditolak.
* **Hasil Audit:**
  * Password disimpan menggunakan `Hash::make()` dengan algoritma **Bcrypt** (cost factor 12).
  * Diperkuat dengan aturan `Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()` di `AppServiceProvider` dan `RegisterRequest`.
  * Akun seeder tidak menggunakan hardcoded plain secret, melainkan membaca variabel `SEED_ADMIN_PASSWORD` / `SEED_WARGA_PASSWORD` dari environment.
* **Status:** **PASS / MITIGATED (Tingkat Risiko Awal: HIGH → Diselesaikan)**

### B. Proteksi Brute-Force & Rate Limiting
* **Standar Evaluasi:** Pembatasan percobaan login pada endpoint publik.
* **Hasil Audit:**
  * Endpoint `POST /login` diproteksi middleware `throttle:20,1` serta implementasi `RateLimiter::tooManyAttempts($throttleKey, 5)`.
  * Jika user salah memasukkan password 5 kali berturut-turut, sistem memblokir request selama 60 detik dan mencatat insiden ke tabel `activity_logs` dengan aksi `login_blocked`.
* **Status:** **PASS / MITIGATED (Tingkat Risiko Awal: MEDIUM → Diselesaikan)**

### C. Manajemen Sesi & Keamanan Cookie
* **Standar Evaluasi:** Session ID harus diregenerasi saat login; cookie wajib `HttpOnly` dan `Secure`.
* **Hasil Audit:**
  * `AuthController@login` menjalankan `request()->session()->regenerate()` untuk mencegah serangan *Session Fixation*.
  * Konfigurasi `.env` dan `config/session.php` mengaktifkan `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, dan `SameSite=Lax`.
  * Saat logout, `request()->session()->invalidate()` dan `regenerateToken()` dipanggil secara menyeluruh.
* **Status:** **PASS / SECURED (Tingkat Risiko Awal: MEDIUM → Diselesaikan)**

### D. Kontrol Akses & Pencegahan IDOR (Insecure Direct Object Reference)
* **Standar Evaluasi:** Pengguna biasa tidak boleh membuka halaman admin atau memanipulasi data warga lain.
* **Hasil Audit:**
  * Middleware `RoleMiddleware` (`role:admin` / `role:warga`) membatasi akses URL di level kernel route.
  * Pada modul detail tagihan warga (`TagihanWargaController`), sistem memvalidasi kepemilikan tagihan:
    ```php
    $warga = Auth::user()->warga;
    abort_if(!$warga || $pembayaran->warga_id !== $warga->id, 403);
    ```
    Mencegah manipulasi parameter ID tagihan antar warga di browser.
  * Form registrasi meng-hardcode role `warga` di backend, menggagalkan manipulasi request payload untuk menjadi admin.
* **Status:** **PASS / PROTECTED (Tingkat Risiko Awal: HIGH → Diselesaikan)**

### E. Pencegahan Injeksi SQL (SQL Injection)
* **Standar Evaluasi:** Input form dan parameter URL tidak boleh merusak query database.
* **Hasil Audit:**
  * Seluruh manipulasi data menggunakan Laravel Eloquent ORM dan query builder dengan PDO Parameter Binding otomatis.
  * Pencarian di `ActivityLogController` menggunakan binding aman:
    `->where('description', 'like', '%' . $s . '%')`.
  * Uji penetrasi login dengan payload `' OR '1'='1' --` ditolak aman dan gagal login.
* **Status:** **PASS / RESISTANT (Tingkat Risiko Awal: CRITICAL → Diselesaikan)**

### F. Pencegahan Cross-Site Scripting (XSS)
* **Standar Evaluasi:** Data input pengguna harus di-escape sebelum dicetak ke browser.
* **Hasil Audit:**
  * Seluruh view Blade memanfaatkan konstruksi `{{ $variable }}` yang mengeksekusi `htmlspecialchars()` dengan encoding `UTF-8`.
  * Data masukan nama warga seperti `<script>alert(1)</script>` ter-escape menjadi `&lt;script&gt;alert(1)&lt;/script&gt;` di layar.
* **Status:** **PASS / RESISTANT (Tingkat Risiko Awal: HIGH → Diselesaikan)**

### G. Proteksi Cross-Site Request Forgery (CSRF)
* **Standar Evaluasi:** Setiap aksi mutasi data via POST/PUT/DELETE wajib menyertakan token autentik.
* **Hasil Audit:**
  * Seluruh form HTML Blade menyertakan direktif `@csrf`.
  * Middleware `VerifyCsrfToken` aktif secara global. Request POST tanpa token valid menghasilkan error HTTP 419 (Page Expired).
* **Status:** **PASS / ACTIVE (Tingkat Risiko Awal: MEDIUM → Diselesaikan)**

### H. HTTP Security Headers
* **Standar Evaluasi:** Header browser modern untuk mencegah framing, sniffing tipe konten, dan pembatasan origin.
* **Hasil Audit:**
  Middleware kustom `SecurityHeaders` menyuntikkan header pada setiap response HTTP:
  * `X-Frame-Options: DENY` (Anti Clickjacking)
  * `X-Content-Type-Options: nosniff` (Anti MIME Sniffing)
  * `Referrer-Policy: strict-origin-when-cross-origin`
  * `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  * `Content-Security-Policy: default-src 'self' ...`
  * `Strict-Transport-Security: max-age=31536000; includeSubDomains` (HSTS aktif pada protokol HTTPS)
  * Header `X-Powered-By` dihapus dari server response untuk meminimalisasi fingerprinting teknologi.
* **Status:** **PASS / HARDENED (Tingkat Risiko Awal: LOW → Diselesaikan)**

### I. Jejak Audit (Audit Trail & Activity Log)
* **Standar Evaluasi:** Setiap aktivitas penting sistem wajib tercatat demi akuntabilitas forensik.
* **Hasil Audit:**
  * Tersedia tabel `activity_logs` yang menyimpan `user_id`, nama aksi, deskripsi aktivitas, alamat IP pengguna, dan User-Agent browser.
  * Mencatat aktivitas login berhasil, login diblokir (brute-force), logout, perubahan status verifikasi, dan aksi CRUD penting.
  * Halaman visual audit log hanya dapat diakses oleh Administrator desa.
* **Status:** **PASS / IMPLEMENTED**

---

## 3. Ringkasan Kepatuhan Kriteria Dosen

| Kriteria PDF Dosen | Kepatuhan Sistem | Bukti Implementasi |
| :--- | :---: | :--- |
| **Password Hashing** | 100% | Bcrypt hash (`$2y$12$...`) + Strong password rule |
| **SQLi & XSS Prevention** | 100% | Eloquent Parameter Binding + Blade Escaping |
| **Session HttpOnly & Secure** | 100% | `.env` session driver & flag secure cookie |
| **Role-Based Access Control** | 100% | `RoleMiddleware` (Admin vs Warga) |
| **Pencegahan IDOR** | 100% | Pengecekan `warga_id` pada detail tagihan |
| **Rate Limiting** | 100% | Throttling 5 attempt/menit + Log blokir |
| **Security Headers** | 100% | Middleware `SecurityHeaders` lengkap |
| **Log Aktivitas** | 100% | Model `ActivityLog` & menu admin log aktivitas |
| **HTTPS & FQDN** | 100% | `https://iuransampahdesa12.free.nf` (SSL Valid) |
| **Zero Vulnerabilities** | 100% | Upgrade ke Laravel 12 modern (`composer audit` bersih) |
