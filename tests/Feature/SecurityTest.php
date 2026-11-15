<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Self-testing keamanan otomatis.
 * Jalankan: php artisan test --filter=SecurityTest
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private const STRONG = 'Rahasia#2024';

    private function makeWarga(string $email): User
    {
        $u = User::create(['name' => 'Warga', 'email' => $email, 'password' => self::STRONG, 'role' => 'warga']);
        $u->warga()->create(['nama' => 'Warga', 'alamat' => 'RT 01', 'no_hp' => '081234567890']);
        return $u;
    }

    private function registerPayload(array $override = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso', 'email' => 'budi@test.id', 'alamat' => 'RT 02 RW 01',
            'no_hp' => '081234567890', 'password' => self::STRONG, 'password_confirmation' => self::STRONG,
        ], $override);
    }

    /** 1. Password disimpan dengan bcrypt, bukan plaintext */
    public function test_password_is_hashed_with_bcrypt(): void
    {
        $this->post('/register', $this->registerPayload())->assertRedirect(route('warga.dashboard'));
        $user = User::where('email', 'budi@test.id')->first();
        $this->assertNotEquals(self::STRONG, $user->password);
        $this->assertStringStartsWith('$2y$', $user->password);
        $this->assertSame('warga', $user->role);
    }

    /** 2. Password lemah ditolak saat registrasi */
    public function test_weak_password_rejected(): void
    {
        foreach (['123456', 'password', 'abcdefgh', 'Abcdefgh1'] as $weak) {
            $this->post('/register', $this->registerPayload(['password' => $weak, 'password_confirmation' => $weak]))
                ->assertSessionHasErrors('password');
        }
        $this->assertDatabaseCount('users', 0);
    }

    /** 3. Registrasi tidak bisa menyisipkan role admin (mass assignment / privilege escalation) */
    public function test_register_cannot_escalate_to_admin(): void
    {
        $this->post('/register', $this->registerPayload(['role' => 'admin']));
        $this->assertSame('warga', User::where('email', 'budi@test.id')->value('role'));
    }

    /** 4. Rate limiting login: percobaan ke-6 diblokir */
    public function test_login_rate_limited(): void
    {
        $this->makeWarga('w@test.id');
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'w@test.id', 'password' => 'salah']);
        }
        $this->post('/login', ['email' => 'w@test.id', 'password' => self::STRONG])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseHas('activity_logs', ['action' => 'login_blocked']);
    }

    /** 5. SQL Injection pada form login tidak berhasil */
    public function test_sql_injection_login_fails(): void
    {
        $this->makeWarga('w@test.id');
        $this->post('/login', ['email' => "' OR '1'='1' -- ", 'password' => "' OR '1'='1"])
            ->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'w@test.id', 'password' => "' OR 1=1 -- "]);
        $this->assertGuest();
    }

    /** 6. XSS: input script di-escape saat ditampilkan */
    public function test_xss_payload_is_escaped(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'a@test.id', 'password' => self::STRONG, 'role' => 'admin']);
        $u = $this->makeWarga('x@test.id');
        $u->warga->update(['nama' => '<script>alert(1)</script>']);

        $this->actingAs($admin)->get(route('admin.warga.index'))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    /** 7. Kontrol akses: warga tidak bisa membuka halaman admin */
    public function test_warga_cannot_access_admin_pages(): void
    {
        $w = $this->makeWarga('w@test.id');
        foreach (['/admin/dashboard', '/admin/user', '/admin/warga', '/admin/laporan', '/admin/activity-log'] as $url) {
            $this->actingAs($w)->get($url)->assertForbidden();
        }
    }

    /** 8. Guest diarahkan ke login */
    public function test_guest_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/warga/dashboard')->assertRedirect('/login');
    }

    /** 9. IDOR: warga tidak bisa melihat tagihan milik warga lain */
    public function test_idor_other_warga_tagihan_forbidden(): void
    {
        $a = $this->makeWarga('a@test.id');
        $b = $this->makeWarga('b@test.id');
        $tagihan = Tagihan::create(['bulan' => 1, 'tahun' => 2026, 'jumlah_iuran' => 15000]);
        $milikB = Pembayaran::create([
            'warga_id' => $b->warga->id, 'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => now(), 'jumlah_bayar' => 15000, 'status' => 'belum_lunas',
        ]);

        $this->actingAs($a)->get(route('warga.tagihan.show', $milikB))->assertForbidden();
        $this->actingAs($b)->get(route('warga.tagihan.show', $milikB))->assertOk();
    }

    /** 10. CSRF: request POST tanpa token ditolak (419) */
    public function test_csrf_protection_active(): void
    {
        // Laravel menonaktifkan CSRF saat unit test, jadi middleware diuji langsung
        $middleware = new class($this->app, $this->app['encrypter']) extends \App\Http\Middleware\VerifyCsrfToken {
            protected function runningUnitTests() { return false; }
        };
        $request = \Illuminate\Http\Request::create('/login', 'POST', ['email' => 'x@test.id']);
        $request->setLaravelSession($this->app['session.store']);

        $this->expectException(\Illuminate\Session\TokenMismatchException::class); // → HTTP 419
        $middleware->handle($request, fn () => response('lolos'));
    }

    /** 11. Security headers terpasang */
    public function test_security_headers_present(): void
    {
        $this->get('/login')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy')
            ->assertHeader('Content-Security-Policy');
    }

    /** 12. Logout menghapus sesi & tercatat di log */
    public function test_logout_invalidates_session(): void
    {
        $w = $this->makeWarga('w@test.id');
        $this->post('/login', ['email' => 'w@test.id', 'password' => self::STRONG])->assertRedirect(route('warga.dashboard'));
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->get('/warga/dashboard')->assertRedirect('/login');
        $this->assertSame(1, ActivityLog::where('action', 'logout')->where('user_id', $w->id)->count());
    }
}
