<?php

namespace App\Providers;

use App\Services\CountryCodeMapper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

         $this->app->bind('App\Services\CountryCodeMapper', function ($app) {
        return new CountryCodeMapper();
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
