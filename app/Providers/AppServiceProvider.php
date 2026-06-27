<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Frontend page rate limiter — 60 req/min per IP
        RateLimiter::for('web-pages', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Booking page rate limiter — stricter, 30 req/min per IP
        // This page was responsible for ~52 GiB of bot bandwidth
        RateLimiter::for('web-booking', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // AJAX endpoints rate limiter — 60 req/min per IP
        // Higher headroom so real users can click through calendar dates quickly
        RateLimiter::for('web-ajax', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // Contact form POST rate limiter — 5 submissions/hour per IP
        // Covers /contact/submit and /contact_lawyer to prevent email bombing
        RateLimiter::for('web-contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });

        // Appointment booking POST rate limiter — 20 attempts/hour per IP
        // Covers /book-an-appointment/storepaid and /stripe (high-value actions)
        RateLimiter::for('web-booking-post', function (Request $request) {
            return Limit::perHour(20)->by($request->ip());
        });

        // Promo code check rate limiter — 20 attempts/hour per IP (separate bucket so
        // checking a code doesn't consume the booking quota)
        RateLimiter::for('web-promo', function (Request $request) {
            return Limit::perHour(20)->by($request->ip());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
