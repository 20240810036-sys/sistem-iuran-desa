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
        // uncompromised() memeriksa ke database kebocoran HaveIBeenPwned (khusus production).
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->mixedCase()->numbers()->symbols();
            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });

        // Paksa semua URL memakai HTTPS di production
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
