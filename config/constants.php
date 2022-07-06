<?php

//$statusi = app()->call(config('app.status_model'));
//    dd($statusi);
//foreach ($statusi as $status) {
//}

//OPŠTI STATUSI
define('NEAKTIVAN', 0);
define('AKTIVAN', 1);
define('KREIRAN', 35);
define('OBRADJEN', 36);
define('PROBLEM', 37);
define('ZAVRSEN', 38);
define('OTKAZAN', 39);
define('_NEAKTIVAN', 40);
define('ZALBA', 41);        //PRIVREMEN
define('OBAVESTEN', 42);    //PRIVREMEN
define('PONISTEN', 43);     //PRIVREMEN
define('ZALBA_ODUSTAO', 100);     //PRIVREMEN
define('REQUEST_BOARD', 200);     //PRIVREMEN

//PRIJAVE ZA STRUCNI ISPIT
//AZURIRANO U KODU => REQUEST
define('PRIJAVA_KREIRANA', 2);      //50 REQUEST_CREATED
define('PRIJAVA_GENERISANA', 3);    //51 REQUEST_SUBMITED
define('PRIJAVA_ZAVEDENA', 4);      //52 REQUEST_IN_PROGRESS
define('PRIJAVA_OTKLJUCANA', 5);
define('PRIJAVA_AZURIRANA', 6);
define('PRIJAVA_ZAKLJUCANA', 7);
define('PRIJAVA_ZAVRSENA', 8);      //53 REQUEST_FINISHED
define('PRIJAVA_OTKAZANA', 9);      //54 REQUEST_CANCELED

//MEMBERSHIPS                             | PRIJAVE ZA PRIJEM U CLANSTVO
const PRIJAVA_CLAN_KREIRANA = 10;    //|
const PRIJAVA_CLAN_GENERISANA = 11;  //|
const PRIJAVA_CLAN_ZAVEDENA = 12;    //| => REQUESTS
const PRIJAVA_CLAN_PRIHVACENA = 13;  //|
const PRIJAVA_CLAN_ODBIJENA = 14;    //|

const MEMBERSHIP_STARTED = 10;      //|
const MEMBERSHIP_ENDED = 11;        //| => NOVI STATUSI
const MEMBERSHIP_SUSPENDED = 12;    //|
const MEMBERSHIP_PROBLEM = 13;      //|

// STATUSI ZAHTEVA ZA IZDAVANJE LICENCE
//AZURIRANO U KODU => REQUEST
define("ZAHTEV_LICENCA_KREIRAN", 15);//50 REQUEST_CREATED
define("ZAHTEV_LICENCA_GENERISAN", 16);//51 REQUEST_SUBMITED
define("ZAHTEV_LICENCA_PRIMLJEN", 17);//52 REQUEST_IN_PROGRESS
define("ZAHTEV_LICENCA_ZAVRSEN", 18);//53 REQUEST_FINISHED
define("ZAHTEV_LICENCA_ZASTAREO", 19);//19
define("ZAHTEV_LICENCA_ODBIJEN", 20);//20
define("ZAHTEV_LICENCA_OTKLJUCAN", 21);//21
define("ZAHTEV_LICENCA_ZAKLJUCAN", 22);//22

//STATUS LICENCE
define('LICENCA_AKTIVNA', 'A'); //20
define('LICENCA_NEAKTIVNA', 'N'); //21
define('LICENCA_DEAKTIVIRANA', 'D'); //22
define('LICENCA_NEAKTIVIRANA', 'H'); //23

//STATUS VRSTA_DOKUMENTA
define('DOKUMENT_ORIGINAL', 24);
define('DOKUMENT_FOTOKOPIJA', 25);
define('DOKUMENT_DIGITALNA_KOPIJA', 26);

//ZAHTEVI ZA MIROVANJE
define('MIROVANJE_ZAHTEV_KREIRAN', 27);
define('MIROVANJE_ZAHTEV_GENERISAN', 28);
define('MIROVANJE_ZAHTEV_PRIMLJEN', 29);
define('MIROVANJE_ZAHTEV_OBRADJEN', 30);
define('MIROVANJE_ZAHTEV_ODOBREN', 31);
//MIROVANJE
define('MIROVANJE_AKTIVNO', 32);
define('MIROVANJE_ISTEKLO', 33);
define('MIROVANJE_PREKINUTO', 34);

//UNIVERZALNI ZAHTEVI (SVECANA FORMA, ...)
//define('ZAHTEV_KREIRAN', 27);//todo 2
//define('ZAHTEV_GENERISAN', 28); //todo 3
//define('ZAHTEV_ZAVRSEN', 29); //todo 8
//define('ZAHTEV_ODBIJEN', 30);
//define('ZAHTEV_ZASTAREO', 31);

//ZAPOSLENJE
//sad je zaposlen 3

//GRUPE STATUSA
define('OPSTA', 1);
define('STRUCNI_ISPIT', 2);
define('CLANSTVO', 3);
define('LICENCE', 4);
define('REGISTAR', 5);
define('VRSTA_DOKUMENTA', 6);
define('MIROVANJA', 7);
define('OSIGURANJE', 8);
define('PODACI', 9);
define('OSOBE', 10);
define('REQUESTS', 11);
define('DOCUMENTS', 12);

define('VRSTA_LICENCE_SVE', 0);
define('VRSTA_LICENCE_PLANIRANJE', 1);
define('VRSTA_LICENCE_URBANIZAM', 2);
define('VRSTA_LICENCE_PROJEKTOVANJE', 3);
define('VRSTA_LICENCE_IZVODJENJE', 4);
define('VRSTA_LICENCE_PROJEKTOVANJE_IZVODJENJE', 5);

//VRSTA LICENCE
define('PLANERI', 1);
define('URBANISTI', 2);
define('PROJENTANTI', 3);
define('IZVODJACI', 4);

//POKRICE POLISE
define('PLANIRANJE', 1);
define('URBANIZAM', 2);
define('PROJEKTOVANJE', 3);
define('IZVODENJE_RADOVA', 4);
define('PROJEKTOVANJE_IZVODENJE', 5);
define('PROJEKTOVANJE_URBANIZAM_PLANIRANJE', 6);
define('PROJEKTOVANJE_IZVODENJE_URBANIZAM_PLANIRANJE', 7);
define('NEMA_POKRICE', 99);

//OSIGURANJE
define('OSIGURANJE_IKS', 1);
define('OSIGURANJE_KOLEKTIVNO', 2);
define('OSIGURANJE_INDIVIDUALNO', 3);

//OSOBE  U KOM JE SVOJSTVU OSOBA U BAZI CLAN=?
//define('CLAN', 44);
//define('NIJE_CLAN', 45);
//define('KANDIDAT_LICENCA', 46);
//define('KANDIDAT_SI', 47);
//define('LICENCIRAN', 48);
//define('OSTALI', 49);
//OSOBE  U KOM JE SVOJSTVU OSOBA U BAZI CLAN=?
define('MEMBER', 1);
define('NOT_MEMBER', 0);
define('MEMBER_ON_HOLD', 100);
define('MEMBER_TO_DELETE', 10);
define('KANDIDAT_LICENCA', 46);
define('KANDIDAT_SI', 47);
define('LICENCIRAN', 48);
define('OSTALI', 49);

//REQUESTS
define('REQUEST_CREATED', 50);
define('REQUEST_SUBMITED', 51);
define('REQUEST_IN_PROGRESS', 52);
define('REQUEST_FINISHED', 53);
define('REQUEST_CANCELED', 54);
define('REQUEST_PROBLEM', 55);

//DOCUMENTS
define('DOCUMENT_CREATED', 56);
define('DOCUMENT_REGISTERED', 57);
define('DOCUMENT_CANCELED', 58);

//TODO: VADITI IZ BAZE
define('ARHITEKTE', array(1, 15, 30, 33, 34, 35));
define('PEJZ_ARHITEKTE', array(6, 58));

const PROFESIONALNI_NAZIV = [
    1 => ['Odgovorni planer' => 'Odgovornog planera',
        'Odgovorni urbanista' => 'Odgovornog urbanistu',
        'Odgovorni projektant' => 'Odgovornog projektanta',
        'Odgovorni izvođač' => 'Odgovornog izvođača',
        'Odgovorni izvođač radova' => 'Odgovornog izvođača radova',
        'Odgovorni inženjer' => 'Odgovornog inženjera'],
    2 => ['Odgovorni prostorni planer' => 'Odgovornog prostornog planera',
        'Odgovorni urbanista' => 'Odgovornog urbanistu',
        'Odgovorni projektant' => 'Odgovornog projektanta',
        'Odgovorni izvođač radova' => 'Odgovornog izvođača radova'],
    3 => ['Prostorni planer' => 'Prostornog planera',
        'Urbanista' => 'Urbanistu',
        'Arhitekta urbanista' => 'Arhitektu urbanistu',
        'Inženjer' => 'Inženjera',
        'Pejzažni arhitekta' => 'Pejzažnog arhitektu',
        'Arhitekta' => 'Arhitektu',
        'Izvođač radova' => 'Izvođača radova']
];


//  MEJLOVI OD ZNAČAJA
const EMAIL_RACUNOVODSTVO = 'racunovodstvo@ingkomora.rs';
