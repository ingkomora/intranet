<?php

return [
    'model_defaults' => [
        'namespace' => 'App\Models',
        'base_class_name' => \Illuminate\Database\Eloquent\Model::class,
        'output_path' => '/var/www/intranet2.iks/app/Models',
        'no_timestamps' => false,
        'date_format' => null,
        'connection' => null,
        'backup' => null,
    ]
];

/*
 * models from database iks DEV

//php artisan krlove:generate:model LicencaTip --table-name=tlicencatip
//php artisan krlove:generate:model Licenca --table-name=tlicenca
//php artisan krlove:generate:model Region --table-name=tregion
//php artisan krlove:generate:model Opstina --table-name=topstina
//php artisan krlove:generate:model Mesto --table-name=tmesto
//php artisan krlove:generate:model RegLicencaTip --table-name=treg_licencatip
//php artisan krlove:generate:model RegOblast --table-name=treg_oblast
//php artisan krlove:generate:model RegPodoblast --table-name=treg_podoblast
//php artisan krlove:generate:model RegPodregistar --table-name=treg_podregistar
//php artisan krlove:generate:model RegSekcija --table-name=treg_sekcija
//php artisan krlove:generate:model LicencaVrsta --table-name=tsekcija
//php artisan krlove:generate:model SiRokgrupa --table-name=tsi_rokgrupa
//php artisan krlove:generate:model SiRok --table-name=tsi_rok
//php artisan krlove:generate:model SiObavestenjetip --table-name=tsi_obavestenjetip
//php artisan krlove:generate:model SiObavestenjegrupa --table-name=tsi_obavestenjegrupa
//php artisan krlove:generate:model SiMestorodjenja --table-name=tsi_mestorodjenja
//php artisan krlove:generate:model SiObavestenje --table-name=tsi_obavestenje
//php artisan krlove:generate:model SiUspeh --table-name=tsi_uspeh
//php artisan krlove:generate:model SiStruka --table-name=tstrucniispitstruka
//php artisan krlove:generate:model SiVrsta --table-name=tvrstasi
//php artisan krlove:generate:model SiKandidatKomisijaclan --table-name=tsi_kandidat_komisijaclan
//php artisan krlove:generate:model SiKandidatstatus --table-name=tsi_kandidatstatus
//php artisan krlove:generate:model SiKomisijaAppkorisnik --table-name=tsi_komisija_appkorisnik
//php artisan krlove:generate:model SiKomisijafunkcija --table-name=tsi_komisijafunkcija
//php artisan krlove:generate:model SiKomisijaoblast --table-name=tsi_komisijaoblast
//php artisan krlove:generate:model SiKomisijaclan --table-name=tsi_komisijaclan
//php artisan krlove:generate:model SiKomisijasaziv --table-name=tsi_komisijasaziv
//php artisan krlove:generate:model SiKomisijaKomisijaclan --table-name=tsi_komisija_komisijaclan
//php artisan krlove:generate:model SiKomisija --table-name=tsi_komisija
//php artisan krlove:generate:model SiKandidat --table-name=tsi_kandidat
//php artisan krlove:generate:model Zahtev --table-name=tzahtev
//php artisan krlove:generate:model Zahtevpregled --table-name=tzahtevpregled
//php artisan krlove:generate:model Titula --table-name=ttitula
//php artisan krlove:generate:model Zvanje --table-name=tzvanje
//php artisan krlove:generate:model Sekcija --table-name=tzvanje_grupa
//php artisan krlove:generate:model SIZvanje --table-name=tzvanjesi
//php artisan krlove:generate:model Osoba --table-name=tosoba
//php artisan krlove:generate:model SiOsoba --table-name=tosobasi
//php artisan krlove:generate:model FunkcijaTip --table-name=tfunkcija_tip
//php artisan krlove:generate:model Funkcija --table-name=tfunkcija
//php artisan krlove:generate:model Funkcioner --table-name=tfunkcioner
//php artisan krlove:generate:model FunkcionerMandatTip --table-name=tfunkcioner_mandat_tip
//php artisan krlove:generate:model FunkcionerMandat --table-name=tfunkcioner_mandat

//php artisan krlove:generate:model Kurs --table-name=tpuokurs
//php artisan krlove:generate:model KursOblast --table-name=tpuooblast
//php artisan krlove:generate:model KursPitanje --table-name=tpuopitanje
//php artisan krlove:generate:model KursPrijava --table-name=tpuoprijava
//php artisan krlove:generate:model Fakultet --table-name=tfakultet
//php artisan krlove:generate:model FakultetSmer --table-name=tsmer
//php artisan krlove:generate:model Meni --table-name=tmeni
//php artisan krlove:generate:model MeniGrupa --table-name=tmeni_grupa
//php artisan krlove:generate:model MeniPozicija --table-name=tmeni_pozicija

//php artisan krlove:generate:model SiZahtev --table-name=tzahtev //stari zahtevi za SI
//php artisan krlove:generate:model SiPrijava --table-name=tsi_prijave

//php artisan krlove:generate:model Status --table-name=statusi
//php artisan krlove:generate:model ClanPrijava --table-name=prijave_clanstvo
//php artisan krlove:generate:model LicencaClanPrijava --table-name=licenca_prijava_clan

//php artisan krlove:generate:model AppKorisnik --table-name=tappkorisnik
//php artisan krlove:generate:model Clanarina --table-name=tclanarinaod2006

//php artisan krlove:generate:model Log --table-name=logovi
//php artisan krlove:generate:model LogOsoba --table-name=logovi_osoba
//php artisan krlove:generate:model LogStatusGrupa --table-name=logovi_statusi_grupe

//php artisan krlove:generate:model Event --table-name=events
//php artisan krlove:generate:model EventGrupa --table-name=events_grupe
//php artisan krlove:generate:model Lib --table-name=tlib
//php artisan krlove:generate:model LogLib --table-name=tlog_lib
//php artisan krlove:generate:model OsobaAngazovanje --table-name=osobe_angazovanje

//php artisan krlove:generate:model EvidencijaMirovanja --table-name=tevidencijamirovanja

//php artisan krlove:generate:model Firma --table-name=firme
//php artisan krlove:generate:model Osiguranje --table-name=osiguranja
//php artisan krlove:generate:model OsiguranjeOsoba --table-name=osiguranje_osoba
//php artisan krlove:generate:model OsiguranjeTip --table-name=osiguranje_tip
//php artisan krlove:generate:model OsiguranjePolisaPokrice --table-name=osiguranja_polise_pokrica
//php artisan krlove:generate:model RegistryDepartmentUnit --table-name=registry_department_units
//php artisan krlove:generate:model Registry --table-name=registries
//php artisan krlove:generate:model VrstaPosla --table-name=vrste_poslova

//php artisan krlove:generate:model VrstaPlana --table-name=tvrstaplana
//php artisan krlove:generate:model Uloga --table-name=tuloga
//php artisan krlove:generate:model SiInfo --table-name=tstrucniispit
//php artisan krlove:generate:model Referenca --table-name=treferenca
//php artisan krlove:generate:model PodOblastVrstaPosla --table-name=pod_oblast_vrsta_posla

//php artisan krlove:generate:model Request --table-name=requests
php artisan krlove:generate:model RequestCategoryType --table-name=request_category_types
php artisan krlove:generate:model RequestCategory --table-name=request_categories

php artisan krlove:generate:model Document --table-name=documents
php artisan krlove:generate:model DocumentType --table-name=document_types
php artisan krlove:generate:model ClanarinaOld --table-name=tclanarina

********************************************************
   K A S N I J E
********************************************************
 nejasno cemu ovo sluzi
php artisan krlove:generate:model  Strucniispit --table-name=tstrucniispit

tclanarina_intervaliobracuna
tclanarinaod2006_korekcijabackup
tclanarinaod2006_upravljanjekorekcijama
tclanovikomisijeuslovnazapisnik
tclanovikomisijezapisnik
tclanovikomisijezapisnik_mgsi
telektronskisertifikat
tevidencijaprekida
tiksmobnetbrojevi
tiksmobnetlog
tiksmobnetzahtev
tiksmobnetzahtevstatus
tiksmobnetzahtevtip
tkruzniemail
tkruzniemailslanje
tpotvrdalicno
tprijavapromenapodataka
tracunar
tregkanc
tregkanc_opstine_obavestenja
ttabele
ttasks
tvesti



 */
