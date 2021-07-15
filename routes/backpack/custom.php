<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::get('admin/register', 'App\Http\Controllers\Admin\Auth\RegisterController')->name('backpack.auth.register');

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('osoba', 'OsobaCrudController');
    Route::crud('osiguranje', 'OsiguranjeCrudController');
    Route::crud('firma', 'FirmaCrudController');
    Route::crud('siprijava', 'SiPrijavaCrudController');
    Route::crud('zvanje', 'ZvanjeCrudController');
    Route::crud('regoblast', 'RegOblastCrudController');
    Route::crud('regpodoblast', 'RegPodOblastCrudController');
    Route::crud('sivrsta', 'SiVrstaCrudController');
    Route::crud('licenca', 'LicencaCrudController');
    Route::crud('zahtevlicenca', 'ZahtevLicencaCrudController');
    Route::crud('delovodnikorganizacionejedinice', 'DelovodnikOrganizacioneJediniceCrudController');
    Route::crud('delovodnik', 'DelovodnikCrudController');
    Route::crud('brojac', 'BrojacCrudController');
    Route::get('home', 'HomeController@dashboard');
    Route::get('pdf', 'PdfController@downloadPDF');
    Route::get('test', 'HomeController@test');
    Route::get('unesi/{action}/{url?}', 'ZahtevController@unesi');
    Route::post('obradizahtevsvecanaforma', 'ZahtevController@obradizahtevsvecanaforma');
    Route::post('preuzimanjesvecanaforma', 'ZahtevController@preuzimanjesvecanaforma');
    Route::post('downloadzip', 'ZahtevController@downloadZip');
    Route::view('/unesinovelicence', 'unesinovelicence');
    Route::post('/unesinovelicence', 'ZahtevController@unesinovelicence');
    Route::view('/unesinoveclanove', 'clanstvo');
    Route::post('/unesinoveclanove', 'ZahtevController@unesinoveclanove');
    Route::get('/licencatip/{id}', 'ZahtevController@getLicencaTip');
    Route::get('/checkzahtev/{licenca}/{jmbg}', 'ZahtevController@checkZahtev');
    Route::get('/checklicencatip', 'ZahtevController@checkLicencaTip');


    Route::crud('prijavasistara', 'PrijavaSiStaraCrudController');
    Route::crud('prijavaclanstvo', 'PrijavaClanstvoCrudController');
    Route::crud('osobasi', 'OsobaSiCrudController');
    Route::crud('status', 'StatusCrudController');
    Route::crud('logstatusgrupa', 'LogStatusGrupaCrudController');
    Route::crud('zahtevtip', 'ZahtevTipCrudController');
    Route::crud('zahtev', 'ZahtevCrudController');
    Route::crud('sikandidat', 'SiKandidatCrudController');
}); // this should be the absolute last line of this file

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace' => '\App\Libraries',
], function () { // custom ajax routes
    Route::get('/getprijavaclan/{id}','Helper@getPrijavaClan');
    Route::get('/checkprijavaclan/','Helper@checkPrijavaClan');

}); // this should be the absolute last line of this file
