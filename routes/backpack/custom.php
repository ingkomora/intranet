<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

use App\Imports\ExcelImport;

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
    Route::crud('brojac', 'BrojacCrudController');
    Route::get('home', 'HomeController@dashboard');
//    Route::get('pdf', 'PdfController@downloadPDF');
    Route::get('test', 'HomeController@test');
    Route::get('unesi/{action}/{url?}', 'ZahtevController@unesi')->name('unesi');
    Route::post('obradizahtevsvecanaforma', 'ZahtevController@obradizahtevsvecanaforma');
    Route::post('downloadzip', 'ZahtevController@downloadZip');
    Route::view('/unesinovelicence', 'unesinovelicence');
    Route::post('/unesinovelicence', 'ZahtevController@unesinovelicence');
    Route::view('/unesinoveclanove', 'clanstvo');
    Route::post('/unesinoveclanove', 'ZahtevController@unesinoveclanove');
    Route::get('/licencatip/{id}', 'ZahtevController@getLicencaTip');
    Route::get('/checkzahtev/{licenca}/{jmbg}', 'ZahtevController@checkZahtev');
    Route::get('/checklicencatip', 'ZahtevController@checkLicencaTip');

    Route::get('/splitaddress', 'ZahtevController@splitAddress');
    Route::get('/joinaddress', 'ZahtevController@joinAddress');
    Route::get('/registries', 'ZahtevController@registries');
    Route::get('/clanstvo/{action}/{save?}', 'ZahtevController@clanstvo');
    Route::get('/clanstvo', 'ZahtevController@clanstvo');
/*    Route::view('clanstvo/obradamirovanja', 'obradamirovanja');
    Route::post('clanstvo/obradamirovanja', 'ZahtevController@obradamirovanja');
    Route::get('/clanstvo/mirovanja/import', 'ZahtevController@import');
    Route::crud('clanstvo/mirovanja', 'MirovanjeCrudController');
    Route::view('clanstvo/mirovanja/administracijamirovanja', 'vendor.backpack.crud.administracijamirovanja');
    Route::get('/generateWordDocument', 'ZahtevController@generateWordDocument');*/

    Route::get('/prijava/si/{prijava_id}/{type?}', 'ZavodjenjeController@prijavaPDF');
//    Route::get('/prijava/si/{prijava_id}', 'ZavodjenjeController@prijavaPDF');

    Route::crud('prijavaclanstvo', 'PrijavaClanstvoCrudController');
    Route::crud('status', 'StatusCrudController');
    Route::crud('logstatusgrupa', 'LogStatusGrupaCrudController');
    Route::crud('zahtevtip', 'ZahtevTipCrudController');
//    Route::crud('sikandidat', 'SiKandidatCrudController');
    Route::crud('clanstvo/promenapodataka', 'PromenaPodatakaCrudController');


});

// this should be the absolute last line of this file
Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom ajax routes
    Route::get('/getprijavaclan/{id}', 'Helper@getPrijavaClan');
    Route::get('/checkprijavaclan/', 'Helper@checkPrijavaClan');

    Route::crud('funkcioner', 'FunkcionerCrudController');
    Route::crud('funkcioner-mandat', 'FunkcionerMandatCrudController');
    Route::crud('funkcioner-mandat-tip', 'FunkcionerMandatTipCrudController');
    Route::crud('funkcija', 'FunkcijaCrudController');
    Route::crud('funkcija-tip', 'FunkcijaTipCrudController');
    Route::crud('request-category-type', 'RequestCategoryTypeCrudController');
    Route::crud('request-category', 'RequestCategoryCrudController');
    Route::crud('request', 'RequestCrudController');
    Route::crud('requestmembership', 'RequestMembershipCrudController');
    Route::crud('osoba-edit', 'OsobaEditCrudController');
    Route::crud('registry-department-unit', 'RegistryDepartmentUnitCrudController');
    Route::crud('registry', 'RegistryCrudController');
    Route::crud('document-category', 'DocumentCategoryCrudController');
    Route::crud('document', 'DocumentCrudController');
    Route::crud('membership', 'MembershipCrudController');
    Route::crud('clanarina', 'ClanarinaCrudController');
    Route::crud('clanarina-old', 'ClanarinaOldCrudController');
    //ZAVOĐENJE
    Route::post('/zavodjenje/zavedi/si', 'ZavodjenjeController@store');
    Route::post('/zavodjenje/zavedi/licence', 'ZavodjenjeController@storeLicence');
    Route::get('/zavodjenje/zavedi/{type}', 'ZavodjenjeController@show');
    Route::post('/zavodjenje/zavedi/{type}', 'ZavodjenjeController@zavedi');

    Route::crud('registerrequestpromenapodataka', 'RegisterRequestCrudController');
    Route::crud('registerrequestclanstvo', 'RegisterRequestCrudController');
    Route::crud('registerrequestmirovanjeclanstva', 'RegisterRequestCrudController');
    Route::crud('registerrequestlicence', 'ZahtevLicencaCrudController');
    Route::crud('registerrequestsi', 'SiPrijavaCrudController');
    Route::crud('registerrequestregistar', 'RegisterRequestCrudController');
    Route::crud('registerrequestsfl', 'RegisterRequestCrudController');
    Route::crud('registerrequestresenjeclanstvo', 'RegisterRequestCrudController');
    Route::crud('registry-request-category', 'RegistryRequestCategoryCrudController');
    Route::crud('document-category-type', 'DocumentCategoryTypeCrudController');
}); // this should be the absolute last line of this file
