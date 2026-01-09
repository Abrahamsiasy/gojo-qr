<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;

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
        // Schedule cleanup of temporary QR code files every hour
        Schedule::command('qr:cleanup-temp --hours=1')
            ->hourly()
            ->withoutOverlapping()
            ->onOneServer(); // Only run on one server in multi-server setups
    }
}
