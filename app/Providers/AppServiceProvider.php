<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
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
        // add Str::currency macro
        Str::macro('currency', function ($price) {
            return number_format($price, 0, '.', '.');
        });

         App::setLocale(Session::get('locale', config('app.locale')));
    }
}
