<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;     // maksimal percobaan login
    private const DECAY_SECONDS = 60;   // waktu blokir (detik)

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Registrasi warga baru. Role selalu 'warga' (tidak bisa dipilih dari form
     * sehingga mencegah privilege escalation / mass assignment).
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => strip_tags($data['name']),
                'email'    => strtolower($data['email']),
                'password' => $data['password'], // di-hash otomatis (cast 'hashed' → bcrypt)
                'role'     => 'warga',
            ]);
            $user->warga()->create([
                'nama'   => strip_tags($data['name']),
                'alamat' => strip_tags($data['alamat']),
                'no_hp'  => $data['no_hp'],
            ]);
            return $user;
        });

        ActivityLog::record('register', "Registrasi akun warga baru: {$user->email}", $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('warga.dashboard')->with('success', 'Registrasi berhasil. Selamat datang!');
    }

    /**
     * Proses login dengan rate limiting (5x / menit per email+IP).
     */
    public function login(LoginRequest $request)
    {
        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            ActivityLog::record('login_blocked', "Login diblokir sementara untuk {$request->input('email')}", null);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
            ActivityLog::record('login_failed', "Login gagal untuk {$request->input('email')}", null);
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate(); // cegah session fixation

        $user = Auth::user();
        ActivityLog::record('login', 'Login berhasil');

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'warga.dashboard');
    }

    public function logout(Request $request)
    {
        ActivityLog::record('logout', 'Logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
