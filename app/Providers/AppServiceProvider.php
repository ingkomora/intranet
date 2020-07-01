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
