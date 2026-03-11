<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;


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
            return Limit::perMinute(60)->by($request->ip());
        });

        Carbon::setLocale('th');

        Carbon::macro('toThaiDateTime', function () {
            return $this->locale('th')->translatedFormat('j M') . ' ' .
                ($this->year + 543) . ', ' .
                $this->format('H:i') . ' น.';
        });

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }
    }
}
