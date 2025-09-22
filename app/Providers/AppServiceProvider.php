<?php

namespace App\Providers;

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
        // \App\Tenant::getTenants();
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
