<?php

namespace App\Providers;
use App\Models\Secret;
use App\Observers\SecretObserver;

use Illuminate\Support\ServiceProvider;

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
        // Register the SecretObserver to handle file deletion
        Secret::observe(SecretObserver::class);
    }
}
