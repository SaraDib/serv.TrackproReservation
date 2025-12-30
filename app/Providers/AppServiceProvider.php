<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('api', function (Request $request) {
            if ($request->user()) {
                return [Limit::perMinute(120)->by((string) $request->user()->id)];
            }
            return [Limit::perMinute(60)->by((string) $request->ip())];
        });

        RateLimiter::for('login', function (Request $request) {
            return [Limit::perMinute(10)->by((string) $request->ip())];
        });

        RateLimiter::for('logs', function (Request $request) {
            return [Limit::perMinute(30)->by((string) $request->ip())];
        });
    }
}
