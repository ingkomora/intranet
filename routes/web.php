<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Mail\PromenaPodataka\AdminReportEmail;
use App\Mail\PromenaPodataka\ConfirmationEmail;
use Illuminate\Support\Facades\Mail;

Route::get('/', 'Admin\HomeController@dashboard');
//Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
});
/*Auth::routes(['verify' => TRUE]);

Route::get('test', function () {

    $jmbgs = [
        '0905967800050' => 'azzurro@eunet.rs', '0106953860050' => 'baner@panet.rs', '1909972270026' => 'bobjanns@yahoo.com', '0103957770049' => 'dragan957@gmail.com', '2810965850014' => 'dragansa@telekom.rs', '2804975860077' => 'dsvet@yubc.net', '2004955110008' => 'dtdsu@yunord.net', '2004960710269' => 'ferbild@eunet.rs', '0601952715309' => 'geotehnika@beotel.rs', '1006949780068' => 'gp_inpro@ptt.rs', '1511951782821' => 'gradisca@eunet.rs', '2707960800099' => 'hidrodtd@eunet.rs', '2601968815033' => 'hidrodtd@eunet.rs', '0301979740034' => 'inkoprojekt@gmail.com', '2102961751017' => 'ipmostovi@beotel.rs', '0707965715459' => 'irenazunic@yahoo.com', '0709978815064' => 'kosanoviczeljka@open.telekom.rs', '2903948810090' => 'kosanoviczeljka@open.telekom.rs', '2408973785032' => 'ktgmbm@yahoo.com', '2312960177656' => 'lidija_pecinar@yahoo.com', '2903948710398' => 'lmb@eunet.rs', '2012963782816' => 'mancocacak@gmail.com', '2309960122656' => 'nacapet@verat.net', '1307947715137' => 'office@gzv.rs', '2108950715262' => 'office@srbijaprojekt.co.rs', '0610945710279' => 'omnipro@eunet.rs', '2310957740029' => 'priprema@planum.rs', '0902970782846' => 'ramit@eunet.rs', '2005956885042' => 'rgpvrd@eunet.rs', '0310937715055' => 'rialto@sbb.rs', '2706955710190' => 'sabona@bitsyu.net', '0801957715020' => 'sabona@bitsyu.net', '1101962130016' => 'serbiansoul@hotmail.com', '0501948153954' => 'signal@eunet.rs', '2709966715266' => 'simicm@beotel.rs', '0204966710106' => 'simicm@beotel.rs', '2002949850056' => 'sming@ptt.rs', '0807959762016' => 'sule1@ptt.rs', '0802972710201' => 'tago@beotel.rs', '0409962761017' => 'ter-mont@eunet.rs', '2703952772013' => 'tihos@ptt.rs', '0105966715118' => 'zagorkapetrovic@yahoo.com', '1001977762022' => 'zapispro@hotmail.com'
    ];

//    $osobe = \App\Models\Osoba::whereIn('id', $jmbgs)->get();

//    foreach ($osobe as $osoba) {

//    iterate through array of jmbg=>emails because there is no emails in DB for these persons
    dd("STOP da sistem ne bi ponovo poslao poslate imejlove");
    foreach ($jmbgs as $jmbg=>$email) {
    $osoba = \App\Models\Osoba::find($jmbg);
        $osoba->kontaktemail = $email;
        $body = "Poštovani $osoba->ime_prezime_licence,\n\nVaša imejl adresa $osoba->kontaktemail u bazi Inženjerske komore Srbije nije jedinstvena, što znači da je bar još jedna osoba koristi.\n\nObzirom da zbog prelaska na novi informacioni sistem, nije moguće da više članova koristi istu imejl adresu, pokušali smo da Vas kontaktiramo telefonskim putem. Pošto nismo uspeli da uspostavimo kontakt sa Vama, ovim putem Vas molimo da nam dostavite novu ličnu imejl adresu, koristeći opciju \"Reply to\" ili pozivom na broj šefa Službe za informacione tehnologije IKS: 066 820 6 820.\n\nNapominjemo da je zbog predstojećih izbora u Inženjerskoj komori Srbije neophodno da nam dostavite novu imejl adresu najkasnije sutra, 03.05.2023. godine, do kraja dana, kako biste ostvarili svoje pravo da birate članove veća matičnih sekcija.\n\nČlanovi koji u predviđenom roku ne budu dostavili novu imejl adresu, neće imati imejl adresu u novom informacionom sistemu IKS. Ovo za posledicu ima da takvi članovi neće moći da dobijaju obaveštenja, kao ni da tokom izbornog postupka kandiduju i glasaju (moći će samo da budu birani).\n\nZa sve dodatne informacije možete nas kontaktirati na 066 820 6 820.\n\n\nStručna služba za informacione tehnologije\nInženjerske komore Srbije";
        try {
            Mail::raw($body, function ($message) use ($osoba) {
                $message
                    ->from('ikszahtevi@ingkomora.rs')
                    ->to($osoba->kontaktemail)
//                    ->to('bojan.maravic@ingkomora.rs')
//                    ->to('milan.simic@ingkomora.rs')
                    ->bcc(['ikszahtevi@ingkomora.rs'])
                    ->subject("Promena duplikata imejl adresa u sistemu IKS");
            });

        } catch (\Exception $e) {
            $error = $e->getMessage();
            Mail::raw("Osobi $osoba->ime_prezime_licence ($osoba->id) je poslat mejl koji nije isporučen. Sadržaj mejla je bio:\n\n\"$body\n\"\n\nError:\n$error", function ($message) use ($osoba) {
                $message
                    ->to(['ikszahtevi@ingkomora.rs'])
                    ->subject("ADMIN: Promena duplikata imejl adresa u sistemu IKS");
            });

        }
    }
});*/
