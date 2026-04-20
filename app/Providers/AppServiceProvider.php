<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // [GOD-TIER FIX] Import the Schema facade

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
        // [GOD-TIER FIX] Shrink default string length to bypass WAMP index limits
        Schema::defaultStringLength(191);
    }
}