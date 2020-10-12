<?php

//OSNOVNI STATUSI
define('NEAKTIVAN', 0);
define('AKTIVAN', 1);

//PRIJAVE ZA STRUCNI ISPIT
define('PRIJAVA_KREIRANA', 2);
define('PRIJAVA_GENERISANA', 3);
define('PRIJAVA_ZAVEDENA', 4);
define('PRIJAVA_OTKLJUCANA', 5);
define('PRIJAVA_AZURIRANA', 6);
define('PRIJAVA_ZAKLJUCANA', 7);
define('PRIJAVA_ZAVRSENA', 8);
define('PRIJAVA_OTKAZANA', 9);

//PRIJAVE ZA PRIJEM U CLANSTVO
define('PRIJAVA_CLAN_KREIRANA', 10);
define('PRIJAVA_CLAN_GENERISANA', 11);
define('PRIJAVA_CLAN_ZAVEDENA', 12);
define('PRIJAVA_CLAN_PRIHVACENA', 13);
define('PRIJAVA_CLAN_ODBIJENA', 14);

//ZAHTEV ZA LICENCU
define('ZAHTEV_LICENCA_KREIRAN',15);
define('ZAHTEV_LICENCA_GENERISAN',16);
define('ZAHTEV_LICENCA_ZAVRSEN',17);
define('ZAHTEV_LICENCA_ODBIJEN',18);
define('ZAHTEV_LICENCA_ZASTAREO',19);

//STATUS LICENCE
define('LICENCA_AKTIVNA','A'); //20
define('LICENCA_NEAKTIVNA','N'); //21
define('LICENCA_DEAKTIVIRANA','D'); //22
define('LICENCA_NEAKTIVIRANA','H'); //23

//STATUS DOKUMENTA
define('DOKUMENT_ORIGINAL', 24);
define('DOKUMENT_FOTOKOPIJA', 25);
define('DOKUMENT_DIGITALNA_KOPIJA', 26);

//UNIVERZALNI ZAHTEVI (SVECANA FORMA, ...)
define('ZAHTEV_KREIRAN',27);
define('ZAHTEV_GENERISAN',28);
define('ZAHTEV_ZAVRSEN',29);
define('ZAHTEV_ODBIJEN',30);
define('ZAHTEV_ZASTAREO',31);

//ZAPOSLENJE
//sad je zaposlen 3

//GRUPE STATUSA
define('OPSTA',1);
define('STRUCNI_ISPIT',2);
define('CLANSTVO',3);
define('LICENCE',4);
define('REGISTAR',5);
define('DOKUMENTA',6);
define('OSIGURANJE',7);

define('VRSTA_LICENCE_SVE', 0);
define('VRSTA_LICENCE_PLANIRANJE', 1);
define('VRSTA_LICENCE_URBANIZAM', 2);
define('VRSTA_LICENCE_PROJEKTOVANJE', 3);
define('VRSTA_LICENCE_IZVODJENJE', 4);
define('VRSTA_LICENCE_PROJEKTOVANJE_IZVODJENJE', 5);

//VRSTA LICENCE
define('PLANERI',1);
define('URBANISTI',2);
define('PROJENTANTI',3);
define('IZVODJACI',4);

//POKRICE POLISE
define('PROJEKTOVANJE_IZVODENJE_URBANIZAM_PLANIRANJE',0);
define('PLANIRANJE',1);
define('URBANIZAM',2);
define('PROJEKTOVANJE',3);
define('IZVODENJE_RADOVA',4);
define('PROJEKTOVANJE_IZVODENJE',5);
define('PROJEKTOVANJE_URBANIZAM_PLANIRANJE',6);
define('NEMA_POKRICE',99);

//OSIGURANJE IKS
define('OSIGURANJE_IKS',1);