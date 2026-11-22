<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        // Separate, named limits. A plain `throttle:5,1` on several routes shares ONE
        // counter per IP, so 5 contact messages from an office would block staff login.
        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(5)->by('contact|'.$request->ip()));

        // Login: per email + IP (slows password guessing on one account) and per IP overall.
        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute(5)->by('login|'.Str::lower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perMinute(20)->by('login-ip|'.$request->ip()),
        ]);
    }
}
