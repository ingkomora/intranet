<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('tag', 'TagCrudController');
    Route::crud('osoba', 'OsobaCrudController');
    Route::crud('prijava', 'PrijavaCrudController');
    Route::crud('zvanje', 'ZvanjeCrudController');
    Route::crud('regoblast', 'RegOblastCrudController');
    Route::crud('regpodoblast', 'RegPodOblastCrudController');
    Route::crud('sivrsta', 'SiVrstaCrudController');
    Route::crud('osiguranje', 'OsiguranjeCrudController');
    Route::crud('firma', 'FirmaCrudController');
    Route::crud('licenca', 'LicencaCrudController');
}); // this should be the absolute last line of this file