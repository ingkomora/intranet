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
//        todo: ne radi prilikom unosa nove licence
        Osoba::saved(function ($osoba){
            $lib = new LibLibrary();
            $lib->dodeliJedinstveniLib($osoba->id, backpack_user()->id);
        });

        config(['app.status_model', App\Models\Status::class]);
    }
}
