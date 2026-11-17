<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Kebijakan password kuat: min 8, huruf besar+kecil, angka, simbol.
        // Tidak memakai uncompromised() agar tidak bergantung pada koneksi eksternal HIBP di shared hosting.
        Password::defaults(function () {
            return Password::min(8)->letters()->mixedCase()->numbers()->symbols();
        });

        // Paksa semua URL memakai HTTPS jika di production atau diakses via HTTPS / proxy HTTPS (cegah Mixed Content)
        if ($this->app->isProduction() 
            || (request()->isSecure()) 
            || (request()->server('HTTP_X_FORWARDED_PROTO') === 'https') 
            || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
            URL::forceScheme('https');
        }
    }
}
