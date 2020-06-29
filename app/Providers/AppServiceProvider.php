<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // aggiunto per forzare i link a https, solo in production
        if (env('APP_ENV') !== 'local') {
            $url->forseScheme('https');
        }
    }
}
