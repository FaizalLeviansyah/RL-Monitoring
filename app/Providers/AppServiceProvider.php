<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Gate; // <--- PENTING
use App\Models\User; // <--- PENTING

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Konfigurasi Token Beda Database (Yang kemarin)
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // --- GATE: Izin Khusus Super Admin ---
        Gate::define('super_admin', function (User $user) {
            // User boleh lewat jika jabatannya 'Super Admin'
            return $user->position && $user->position->position_name === 'Super Admin';
        });
    }
}
