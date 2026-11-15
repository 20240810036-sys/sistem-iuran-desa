<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\WargaController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Warga\DashboardController as WargaDashboard;
use App\Http\Controllers\Warga\TagihanWargaController;
use App\Http\Controllers\Warga\RiwayatController;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:20,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Warga CRUD
    Route::resource('warga', WargaController::class);

    // Tagihan CRUD
    Route::resource('tagihan', TagihanController::class);

    // Pembayaran CRUD + Verifikasi
    Route::resource('pembayaran', PembayaranController::class);
    Route::get('/pembayaran-verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::post('/pembayaran/{pembayaran}/setujui', [PembayaranController::class, 'setujui'])->name('pembayaran.setujui');
    Route::post('/pembayaran/{pembayaran}/tolak', [PembayaranController::class, 'tolak'])->name('pembayaran.tolak');

    // Pengaturan Pembayaran
    Route::resource('payment-setting', PaymentSettingController::class);
    Route::post('/payment-setting/{paymentSetting}/toggle', [PaymentSettingController::class, 'toggleActive'])
         ->name('payment-setting.toggle');

    // User CRUD
    Route::resource('user', UserController::class);

    // Log Aktivitas (audit trail)
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

    // Laporan
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/pembayaran', [LaporanController::class, 'pembayaran'])->name('pembayaran');
        Route::get('/tagihan', [LaporanController::class, 'tagihan'])->name('tagihan');
        Route::get('/warga', [LaporanController::class, 'warga'])->name('warga');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export.excel');
    });
});

// Warga Routes
Route::middleware(['auth', 'role:warga'])->prefix('warga')->name('warga.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [WargaDashboard::class, 'index'])->name('dashboard');

    // Tagihan
    Route::get('/tagihan', [TagihanWargaController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/{pembayaran}', [TagihanWargaController::class, 'show'])->name('tagihan.show');
    Route::post('/tagihan/{pembayaran}/upload-bukti', [TagihanWargaController::class, 'uploadBukti'])->name('tagihan.upload-bukti');

    // Riwayat Pembayaran
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});
