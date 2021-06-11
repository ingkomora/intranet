<?php

namespace App\Providers;

use App\Libraries\LibLibrary;
use App\Models\Osoba;
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
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Osoba::saved(function ($osoba){
            $lib = new LibLibrary();
            $lib->dodeliJedinstveniLib($osoba->id, 48);

        });
    }
}
