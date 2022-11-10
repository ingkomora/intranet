<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\UnlockMembershipFeeRegistrationOperation;
use App\Http\Controllers\Admin\Operations\UpdateLicencaStatusOperation;
use App\Http\Requests\OsobaRequest;
use App\Models\Firma;
use App\Models\Sekcija;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OsobaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OsobaCrudController extends CrudController
{

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use UpdateLicencaStatusOperation;
    use UnlockMembershipFeeRegistrationOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    protected $columns_definition_array = [
        'idn',

//        licni
        'licni_podaci' => [
            'label' => 'LIČNI PODACI',
            'name' => 'licni_podaci',
            'type' => 'custom_html',
        ],
        'id' => [
            'name' => 'id',
            'label' => 'jmbg',
        ],
        'osoba' => [
            'name' => 'osoba',
            'type' => 'model_function',
            'label' => 'Ime (roditelj) prezime',
            'function_name' => 'getImeRoditeljPrezimeAttribute',
        ],
        'zvanje' => [
            'name' => 'zvanjeId',
            'type' => 'relationship',
            'label' => 'Zvanje',
            'limit' => 200,
        ],
        'lib',
//        'ime',
//        'prezime',
//        'roditelj',
        'devojackoprezime' => [
            'label' => 'Devojačko prezime',
            'name' => 'devojackoprezime',
        ],
        'prezime_staro' => [
            'label' => 'Staro prezime',
            'name' => 'prezime_staro',
        ],
        'titula' => [
            'name' => 'titula',
            'type' => 'select',
            'entity' => 'titulaId',
            'model' => 'App\Models\Titula',
            'attribute' => 'naziv',
        ],
        'kontakttel' => [
            'name' => 'kontakttel',
            'type' => 'phone',
        ],
        'mobilnitel' => [
            'name' => 'mobilnitel',
            'type' => 'phone',
        ],
        'kontaktfax' => [
            'name' => 'kontaktfax',
            'type' => 'phone',
        ],
        'kontaktemail' => [
            'name' => 'kontaktemail',
            'type' => 'email',
        ],
        'pol' => [
            'name' => 'pol',
            'label' => 'Pol',
            'type' => 'select_from_array',
            'options' => [0 => '-', 1 => 'Muški', 2 => 'Ženski'],
        ],

//        rodjenje
        'rodjenje' => [
            'label' => 'PODACI O ROĐENJU',
            'name' => 'rodjenje',
        ],
        'rodjenjemesto' => [
            'name' => 'rodjenjemesto',
            'label' => 'Mesto',
        ],
        'rodjenjeopstina' => [
            'name' => 'rodjenjeopstina',
            'label' => 'Opština',
        ],
        'rodjenjedrzava' => [
            'name' => 'rodjenjedrzava',
            'label' => 'Država',
        ],
//        'rodjenjedan' => [
//            'name' => 'rodjenjedan',
//            'label' => 'Dan',
//        ],
//        'rodjenjemesec' => [
//            'name' => 'rodjenjemesec',
//            'label' => 'Mesec',
//        ],
//        'rodjenjegodina' => [
//            'name' => 'rodjenjegodina',
//            'label' => 'Godina',
//        ],
        'spojen_datum_rodjenja' => [
            'name' => 'datum_rodjenja',
            'label' => 'Datum rođenja (spojen)',
            'type' => 'model_function',
            'function_name' => 'getSpojenDatumRodjenjaAttribute',
        ],
// ne moze zato sto kad je null u bazi auto castuje u danasnji datum, uradjeno preko custom model funkcije
//        'datumrodjenja' => [
//            'name' => 'datumrodjenja',
//            'label' => 'Datum',
//            'type' => 'date',
//            'format' => 'DD.MM.Y.',
//        ],
        'datumrodjenja' => [
            'name' => 'datumrodjenja',
            'label' => 'Datum rođenja',
            'type' => 'model_function',
            'function_name' => 'getDatumRodjenja',
        ],
        'rodjenjeopstinaid' => [  // columns with same relationship name
            'name' => 'opstinaId',
            'label' => 'Opština (relacija)',
            'type' => 'relationship',
            'attribute' => 'ime',
        ],
//        'rodjenjeinodrzava' => [
//            'name' => 'rodjenjeinodrzava',
//            'label' => 'Država',
//        ],
//        'rodjenjeinomesto' => [
//            'name' => 'rodjenjeinomesto',
//            'label' => 'Mesto',
//        ],

//        adrese
        'adrese' => [
            'label' => 'PODACI O ADRESAMA',
            'name' => 'adrese',
        ],
//        prebivaliste
        'prebivaliste' => [
            'label' => 'ADRESA PREBIVALIŠTA',
            'name' => 'prebivaliste',
        ],
        'prebivalisteadresa' => [
            'name' => 'prebivalisteadresa',
            'label' => 'Adresa',
        ],
        'prebivalistemesto' => [
            'name' => 'prebivalistemesto',
            'label' => 'Mesto',
        ],
        'prebivalistebroj' => [
            'name' => 'prebivalistebroj',
            'label' => 'Poštanski broj',
        ],
        'prebivalisteopstinaid' => [ // columns with same relationship name
            'name' => 'opstinaId',
            'label' => 'Opština (relacija)',
            'type' => 'relationship',
            'attribute' => 'ime',
            'key' => 'prebivalisteopstinaid',
        ],
        'prebivalisteopstina' => [
            'name' => 'prebivalisteopstina',
            'label' => 'Opština',
        ],
        'prebivalistedrzava' => [
            'name' => 'prebivalistedrzava',
            'label' => 'Država',
        ],
        'posta' => [
            'label' => 'ADRESA ZA DOSTAVLJANJE POŠTE',
            'name' => 'posta',
        ],
        'ulica',
        'broj',
        'podbroj',
        'sprat',
        'stan',
        'posta_pb' => [
            'name' => 'posta_pb',
            'label' => 'Poštanski broj',
        ],
        'postaOpstinaId' => [
            'name' => 'postaOpstinaId',
            'label' => 'Opština (relacija)',
            'type' => 'relationship',
            'attribute' => 'ime',
        ],
        'posta_drzava' => [
            'name' => 'posta_drzava',
            'label' => 'Država',
        ],

//        firma
        'firma_sep' => [
            'label' => 'PODACI O FIRMI',
            'name' => 'firma_sep',
        ],
        // iz tabele firme
        'firma_novo' => [
            'label' => 'PODACI IZ TABELE FIRME',
            'name' => 'firma_novo',
        ],
        'firma_mb' => [
            'name' => 'firma',
            'type' => 'relationship',
            'key' => 'firma_mb',
            'attribute' => 'naziv_pib_mb_adresa_mesto',
            'limit' => 500,
        ],
        // iz tabele tosoba
        'firma_staro' => [
            'label' => 'PODACI IZ TABELE OSOBE',
            'name' => 'firma_staro',
        ],
        'firmanaziv' => [
            'name' => 'firmanaziv',
            'label' => 'Naziv',
        ],
        'firmamesto' => [
            'name' => 'firmamesto',
            'label' => 'Mesto',
        ],
        'firmaopstina' => [
            'name' => 'firmaopstina',
            'label' => 'Opština',
        ],
        'firmaweb' => [
            'name' => 'firmaweb',
            'label' => 'Internet prezentacija',
        ],
        'firmatel' => [
            'name' => 'firmatel',
            'label' => 'Telefon',
            'type' => 'phone',
        ],
        'firmaemail' => [
            'name' => 'firmaemail',
            'label' => 'imejl',
            'type' => 'email',
        ],
        'firmaopstinaid' => [
            'name' => 'opstinaId',
            'label' => 'Opština',
            'type' => 'relationship',
            'attribute' => 'ime',
        ],
        'firmafax' => [
            'name' => 'firmafax',
            'label' => 'Faks',
            'type' => 'phone',
        ],

//        obrazovanje
        'obrazovanje' => [
            'label' => 'PODACI O OBRAZOVANJU',
            'name' => 'obrazovanje',
        ],
        'bolonja' => [
            'name' => 'bolonja',
            'label' => 'Bolonja',
            'type' => 'select_from_array',
            'options' => [0 => 'Pre bolonje', 1 => 'Po bolonji'],
        ],
//        dipl
        'dipl' => [
            'label' => 'OSNOVNE STUDIJE',
            'name' => 'dipl',
        ],
        'diplfakultet' => [
            'name' => 'diplfakultet',
            'label' => 'Naziv fakulteta',
        ],
        'diplmesto' => [
            'name' => 'diplmesto',
            'label' => 'Mesto fakulteta',
        ],
        'dipldrzava' => [
            'name' => 'dipldrzava',
            'label' => 'Država fakulteta',
        ],
        'diplodsek' => [
            'name' => 'diplodsek',
            'label' => 'Odsek',
        ],
        'diplsmer' => [
            'name' => 'diplsmer',
            'label' => 'Smer',
        ],
        'diplgodina' => [
            'name' => 'diplgodina',
            'label' => 'Godina završetka',
        ],
        'diplbroj' => [
            'name' => 'diplbroj',
            'label' => 'Broj diplome',
        ],
        'diplfakultetid' => [
            'name' => 'diplfakultetid',
            'label' => 'Fakultet (relacija)',
        ],
        'diplsmerid' => [
            'name' => 'diplsmerid',
            'label' => 'Smer (relacija)',
        ],
        'diplunetfakultet' => [
            'name' => 'diplunetfakultet',
            'label' => 'Unet fakultet',
        ],
        'diplunetsmer' => [
            'name' => 'diplunetsmer',
            'label' => 'Unet smer',
        ],

//        mr
        'mr' => [
            'label' => 'MASTER STUDIJE',
            'name' => 'mr',
        ],
        'mrfakultet' => [
            'name' => 'mrfakultet',
            'label' => 'Naziv fakulteta',
        ],
        'mrmesto' => [
            'name' => 'mrmesto',
            'label' => 'Mesto fakulteta',
        ],
        'mrdrzava' => [
            'name' => 'mrdrzava',
            'label' => 'Država fakulteta',
        ],
        'mrodsek' => [
            'name' => 'mrodsek',
            'label' => 'Odsek',
        ],
        'mrsmer' => [
            'name' => 'mrsmer',
            'label' => 'Smer',
        ],
        'mrgodina' => [
            'name' => 'mrgodina',
            'label' => 'Godina završetka',
        ],
        'mrbroj' => [
            'name' => 'mrbroj',
            'label' => 'Broj diplome',
        ],

//        dr
        'drfakultet',
        'drmesto',
        'drdrzava',
        'drodsek',
        'drsmer',
        'drgodina',
        'drbroj',

//        spec
        'specfakultetid',
        'specunetfakultet',
        'specsmerid',
        'specunetsmer',
        'specgodina',

//        mag
        'magfakultetid',
        'magunetfakultet',
        'magsmerid',
        'magunetsmer',

//        doc
        'docfakultetid',
        'docunetfakultet',
        'docsmerid',
        'docunetsmer',

//        osiguranja
        'osiguranjasection' => [
            'label' => 'PODACI O OSIGURANJU',
            'name' => 'osiguranjasection',
        ],

        'osiguranja_data' => [
            'name' => 'osiguranja_data',
            'label' => 'Osiguranja',
        ],


//        funkcije
        'funkcije' => [
            'label' => 'SVOJSTVO U KOMORI',
            'name' => 'funkcije',
        ],
        'funkcija_id' => [
            'name' => 'funkcija_id',
            'label' => 'Funkcija id',
        ],
        'clanskupstine' => [
            'name' => 'clanskupstine',
            'label' => 'Član Skupštine',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'clan' => [
            'name' => 'clan',
            'label' => 'Članstvo',
            'type' => 'select_from_array',
            'options' => [-1 => 'Funkcioner', 0 => 'Nije član', 1 => 'Član', 100 => 'Na čekanju', 10 => 'Priprema se brisanje iz članstva'],
        ],

//        portal
        'portal' => [
            'label' => 'PORTAL',
            'name' => 'portal',
        ],
        'username' => [
            'name' => 'korisnik',
            'label' => 'Korisničko ime',
            'type' => 'relationship',
            'attribute' => 'username',
        ],
//        'lozinka', // postoji u bazi ali null
        'biografija',
        'licniweb',
        'adresaprikazi' => [
            'name' => 'adresaprikazi',
            'label' => 'Prikaži adresu prebivališta',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da', 2 => 'Prikaži samo grad i opštinu'],
        ],
        'telefonprikazi' => [
            'name' => 'telefonprikazi',
            'label' => 'Prikaži broj telefona',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'mobilniprikazi' => [
            'name' => 'mobilniprikazi',
            'label' => 'Prikaži broj mobilnog telefona',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'faxprikazi' => [
            'name' => 'faxprikazi',
            'label' => 'Prikaži broj faksa',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'mailprikazi' => [
            'name' => 'mailprikazi',
            'label' => 'Prikaži imejl adresu',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'prikazisliku' => [
            'name' => 'prikazisliku',
            'label' => 'Prikaži fotografiju',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'dozvolareklamnimail' => [
            'name' => 'dozvolareklamnimail',
            'label' => 'Dozvoli prijem obaveštenja na imejl',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'imalp' => [
            'name' => 'imalp',
            'label' => 'Poseduje ličnu prezentaciju',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ],
        'zaposlen' => [
            'name' => 'zaposlen',
            'label' => 'Status radnog odnosa',
            'type' => 'select_from_array',
            'options' => [0 => 'Nezaposlen', 1 => 'Zaposlen', 3 => 'Penzioner'],
        ],
        'godine_radnog_iskustva',

//        razno
        'razno' => [
            'label' => 'RAZNO',
            'name' => 'razno',
        ],
        'napomena',
        'vrsta_poslova',
        'st_drzavljanstvoscg' => [
            'name' => 'st_drzavljanstvoscg',
            'label' => 'Državljanstvo SCG',
            'type' => 'select_from_array',
            'options' => ['N' => 'Ne', 'D' => 'Da'],
        ],
        'temp_dms_password',
        'primary_serial',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.Y HH:mm:ss'
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.Y HH:mm:ss'
        ],
    ];

    public function setup()
    {
        $this->crud->setModel('App\Models\Osoba');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/osoba');
        $this->crud->setEntityNameStrings('osoba', 'osobe');

//        prikazuje samo osobe sa maticnim brojem od 13 karaktera

        $this->crud->denyAccess(['create', 'update', 'delete']);

        if (!backpack_user()->hasRole(['admin'])) {
            $this->crud->addClause('whereRaw', "length(id) = 13");
        }
        if (backpack_user()->hasPermissionTo('azuriraj osobu')) {
            $this->crud->allowAccess(['update']);
        }

        $this->crud->set('show.setFromDb', FALSE);

        $this->crud->enableDetailsRow();
        $this->crud->enableExportButtons();
    }

    protected function setupListOperation()
    {
        $this->crud->addColumns($this->columns_definition_array);
        $this->crud->removeColumns([
//            separatori
//            start
            'licni_podaci', 'rodjenje', 'adrese', 'prebivaliste', 'posta', 'firma_sep', 'firma_staro', 'firma_novo', 'obrazovanje', 'dipl', 'mr', 'osiguranjasection', 'funkcije', 'portal', 'razno',
//            end

            'devojackoprezime',
            'prezime_staro',
//            'zvanje',
            'titula',
            'kontakttel',
            'mobilnitel',
            'kontaktfax',
            'kontaktemail',
            'pol',

//        rodjenje
            'rodjenjemesto',
            'rodjenjeopstina',
            'rodjenjedrzava',
            'rodjenjedan',
            'rodjenjemesec',
            'rodjenjegodina',
            'rodjenjeopstinaid',
            'rodjenjeinodrzava',
            'rodjenjeinomesto',
            'datumrodjenja',
            'datum_rodjenja',
            'spojen_datum_rodjenja',

//        adrese
//        prebivaliste
            'prebivalisteopstinaid',
            'prebivalistebroj',
            'prebivalistemesto',
            'prebivalisteopstina',
            'prebivalisteadresa',
            'prebivalistedrzava',
            'opstinaId',
//        posta
            'ulica',
            'broj',
            'podbroj',
            'sprat',
            'stan',
            'posta_pb',
            'posta_drzava',
            'postaOpstinaId',

//        firma
            'firmanaziv',
            'firmamesto',
            'firmaopstina',
            'firmaweb',
            'firmatel',
            'firmaemail',
            'firmaopstinaid',
            'firmafax',
            'firma_mb',
            'firma',

            'osiguranja_data',

//        obrazovanje
//        dipl
            'diplfakultet',
            'diplmesto',
            'dipldrzava',
            'diplodsek',
            'diplsmer',
            'diplgodina',
            'diplbroj',
            'diplfakultetid',
            'diplsmerid',
            'diplunetfakultet',
            'diplunetsmer',

//        mr
            'mrfakultet',
            'mrmesto',
            'mrdrzava',
            'mrodsek',
            'mrsmer',
            'mrgodina',
            'mrbroj',

//        dr
            'drfakultet',
            'drmesto',
            'drdrzava',
            'drodsek',
            'drsmer',
            'drgodina',
            'drbroj',

//        spec
            'specfakultetid',
            'specunetfakultet',
            'specsmerid',
            'specunetsmer',
            'specgodina',

//        mag
            'magfakultetid',
            'magunetfakultet',
            'magsmerid',
            'magunetsmer',

//        doc
            'docfakultetid',
            'docunetfakultet',
            'docsmerid',
            'docunetsmer',

//        osiguranje
            'osiguranjetip',

//        funkcija
            'funkcija_id',
            'clanskupstine',
            'clan',

//        portal
            'korisnik',
            'lozinka',
            'biografija',
            'licniweb',
            'adresaprikazi',
            'telefonprikazi',
            'mobilniprikazi',
            'faxprikazi',
            'mailprikazi',
            'prikazisliku',
            'dozvolareklamnimail',
            'imalp',
            'zaposlen',
            'godine_radnog_iskustva',
            'temp_dms_password',
            'primary_serial',

//        razno
            'napomena',
            'vrsta_poslova',
            'bolonja',
            'st_drzavljanstvoscg',
            'created_at',
            'updated_at',
        ]);
        /*        $request = $this->crud->getRequest();
                if (!$request->has('order')) {
                    $request->merge(['order' => ['column' => 'id', 'dir' => 'asc']]);
                }*/

        $this->crud->setColumnDetails('id', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = str_replace(["\"", "'"], "", $searchTerm);
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
//                    $searchTermArray = array_map('trim', $searchTerm);
                    $searchTermArray = array_map(function ($item) {
                        return trim($item, ' \'",.;');
                    }, $searchTerm);
//                    dd($searchTermArray);
                    $query->orWhereIn('id', $searchTermArray);
                } else if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhere('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%');
                } else {
                    $query->where('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%')
                        ->orWhere('id', 'ilike', $searchTerm . '%')
                        ->orWhereHas('licence', function ($q) use ($column, $searchTerm) {
                            $q->where('id', 'ilike', $searchTerm . '%');
                        });
                }
            },
        ]);

        $this->crud->modifyColumn('zvanjeId', [
            'attribute' => 'skrnaziv'
        ]);


        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'licences',
            'label' => 'Licencirani'
        ],
            FALSE,
            function () {
                $this->crud->addClause('whereHas', 'licence');
            }
        );

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'clan',
            'label' => 'Članstvo'
        ], function () {
            return [
                1 => 'Član je',
                0 => 'Nije član',
                100 => 'Članstvo na čekanju',
                10 => 'Priprema se brisanje iz članstva',
            ];
        }, function ($value) {
            $this->crud->addClause('where', 'clan', $value);
        });

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'sekcija',
            'label' => 'Struka'
        ], function () {
            return Sekcija::orderBy('id')->pluck('naziv', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('whereHas', 'zvanjeId', function ($q) use ($value) {
                $q->where('zvanje_grupa_id', $value);
            });
        });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'firma_mb',
            'label' => 'MB firme'
        ],
            FALSE,
            function () {
                $this->crud->addClause('where', 'firma_mb', '<>', 'NULL');
                $this->crud->addClause('where', 'firma_mb', '<>', 0);
            }
        );

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'lib',
            'label' => 'Nema LIB'
        ],
            FALSE,
            function () {
                $this->crud->addClause('where', 'lib', NULL);
            }
        );

    }

    protected function setupShowOperation()
    {
        $this->crud->addColumns($this->columns_definition_array);
        $this->crud->removeColumns([
            //        dr
            'drfakultet',
            'drmesto',
            'drdrzava',
            'drodsek',
            'drsmer',
            'drgodina',
            'drbroj',

//        spec
            'specfakultetid',
            'specunetfakultet',
            'specsmerid',
            'specunetsmer',
            'specgodina',

//        mag
            'magfakultetid',
            'magunetfakultet',
            'magsmerid',
            'magunetsmer',

//        doc
            'docfakultetid',
            'docunetfakultet',
            'docsmerid',
            'docunetsmer',
        ]);

        $this->crud->modifyColumn('licni_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="lpseparator"></div>
                        <script>
                            var row = document.getElementById("lpseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("lpseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);
//        firma
        $this->crud->modifyColumn('firma_sep', [
            'type' => 'custom_html',
            'value' => '<div id="firmaseparator"></div>
                        <script>
                            var row = document.getElementById("firmaseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("firmaseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);
//        start
        $this->crud->modifyColumn('firma_staro', [
            'type' => 'custom_html',
            'value' => '<div id="firmastaroseparator"></div>
                        <script>
                            var row = document.getElementById("firmastaroseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("firmastaroseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                        </script>
                        '
        ]);
        $this->crud->modifyColumn('firma_novo', [
            'type' => 'custom_html',
            'value' => '<div id="firmanovoseparator"></div>
                        <script>
                            var row = document.getElementById("firmanovoseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("firmanovoseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                        </script>
                        '
        ]);
//        end

//        obrazovanje
//        start
        $this->crud->modifyColumn('dipl', [
            'type' => 'custom_html',
            'value' => '<div id="diplstudijeseparator"></div>
                        <script>
//                            var row = document.getElementById("diplstudijeseparator").parentNode.parentNode.parentNode;
//                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                            var row = document.getElementById("diplstudijeseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("diplstudijeseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                        </script>
                        '
        ]);
        $this->crud->modifyColumn('mr', [
            'type' => 'custom_html',
            'value' => '<div id="mrstudijeseparator"></div>
                        <script>
                            var row = document.getElementById("mrstudijeseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("mrstudijeseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                        </script>
                        '
        ]);
//        end

//        osiguranja
        $this->crud->modifyColumn('osiguranjasection', [
            'type' => 'custom_html',
            'value' => '<div id="osiguranjaseparator"></div>
                        <script>
                            var row = document.getElementById("osiguranjaseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("osiguranjaseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);
        $this->crud->modifyColumn('osiguranja_data', [
            'type' => 'model_function',
            'function_name' => 'getOsiguranjaData',
        ]);


//        rodjenje
        $this->crud->modifyColumn('rodjenje', [
            'type' => 'custom_html',
            'value' => '<div id="rseparator"></div>
                        <script>
                            var row = document.getElementById("rseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("rseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);

//        adrese
//        start
        $this->crud->modifyColumn('adrese', [
            'type' => 'custom_html',
            'value' => '<div id="adreseseparator"></div>
                        <script>
                            var row = document.getElementById("adreseseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("adreseseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);
        $this->crud->modifyColumn('prebivaliste', [
            'type' => 'custom_html',
            'value' => '<div id="prebivalisteseparator"></div>
                        <script>
                            var row = document.getElementById("prebivalisteseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("prebivalisteseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                        </script>
                        '
        ]);
        $this->crud->modifyColumn('posta', [
            'type' => 'custom_html',
            'value' => '<div id="postaseparator"></div>
                        <script>
                            var row = document.getElementById("postaseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("postaseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.08)";
                        </script>
                        '
        ]);
//        end

//        firma
        $this->crud->modifyColumn('firma', [
            'type' => 'custom_html',
            'value' => '<div id="fseparator"></div>
                        <script>
                            var row = document.getElementById("fseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("fseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('obrazovanje', [
            'type' => 'custom_html',
            'value' => '<div id="oseparator"></div>
                        <script>
                            var row = document.getElementById("oseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("oseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('funkcije', [
            'type' => 'custom_html',
            'value' => '<div id="funkseparator"></div>
                        <script>
                            var row = document.getElementById("funkseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("funkseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('portal', [
            'type' => 'custom_html',
            'value' => '<div id="portalseparator"></div>
                        <script>
                            var row = document.getElementById("portalseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("portalseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('razno', [
            'type' => 'custom_html',
            'value' => '<div id="rzseparator"></div>
                        <script>
                            var row = document.getElementById("rzseparator").parentNode.parentNode.parentNode.children[0];
                            var col = document.getElementById("rzseparator").parentNode.parentNode;
                            col.remove();
                            row.setAttribute("colspan", 2);
                            row.setAttribute("class", "text-center");
                            row.style.cssText = "background-color: rgba(124,105,239,0.3)";
                        </script>
                        '
        ]);


        $this->crud->setColumnDetails('zvanjeId', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('zvanje/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
                'target' => '_blank',
            ]
        ]);

    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(OsobaRequest::class);

        $this->setupUpdateOperation();
    }

    protected function setupUpdateOperation()
    {
        $this->crud->setValidation(OsobaRequest::class);

//        licni podaci
        $this->crud->addField([
            'name' => 'id',
            'label' => 'JMBG',
            'attributes' => ['readonly' => 'readonly'],
            'tab' => 'Lični podaci',
            'wrapper' => ['class' => 'col-4'],
        ]);
        CRUD::field('datumrodjenja')->type('date_picker')->label('Datum rođenja')->date_picker_options([
            'todayBtn' => TRUE,
            'format' => 'dd.mm.yyyy.',
            'language' => 'sr-Latn',
        ])->size(4)->tab('Lični podaci');
        $this->crud->field('lib')->size(4)->tab('Lični podaci')->attributes(['readonly' => 'readonly']);;
        $this->crud->field('ime')->size(3)->tab('Lični podaci');
        $this->crud->field('prezime')->size(3)->tab('Lični podaci');
        $this->crud->field('roditelj')->size(3)->tab('Lični podaci');
        $this->crud->field('devojackoprezime')->size(3)->label('Devojačko prezime')->tab('Lični podaci');
        $this->crud->field('zvanjeId')->label('Zvanje')->tab('Lični podaci');

        $this->crud->field('rodjenjemesto')->size(3)->label('Mesto rođenja')->tab('Lični podaci');
        $this->crud->field('rodjenjeopstina')->size(3)->label('Opština rođenja')->tab('Lični podaci');
        $this->crud->field('rodjenjedrzava')->size(3)->label('Država rođenja')->tab('Lični podaci');
        $this->crud->addField([
            'name' => 'st_drzavljanstvoscg',
            'label' => 'Državljanstvo SCG',
            'tab' => 'Lični podaci',
            'type' => 'select_from_array',
            'options' => ['N' => 'Ne', 'D' => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->field('kontakttel')->size(4)->label('Broj telefona')->tab('Lični podaci');
        $this->crud->field('mobilnitel')->size(4)->label('Broj mobilnog telefona')->tab('Lični podaci');
        $this->crud->field('kontaktemail')->size(4)->label('imeil')->tab('Lični podaci')->type('email');

//        prebivaliste
        $this->crud->field('prebivalisteadresa')->size(6)->label('Adresa')->tab('Podaci o prebivalištu');
        $this->crud->field('prebivalistemesto')->size(6)->label('Mesto')->tab('Podaci o prebivalištu');
        $this->crud->field('prebivalistebroj')->size(4)->label('Poštanski broj')->tab('Podaci o prebivalištu');
        $this->crud->field('opstinaId')->size(4)->label('Opština')->tab('Podaci o prebivalištu');
        $this->crud->field('prebivalistedrzava')->size(4)->label('Država')->tab('Podaci o prebivalištu');

        // adresa za dostavu poste
        $this->crud->field('ulica')->size(5)->tab('Adresa za dostavu pošte');
        $this->crud->field('broj')->size(3)->hint('***Upisati "bb" ukoliko nema broj')->tab('Adresa za dostavu pošte');
        $this->crud->field('podbroj')->size(2)->tab('Adresa za dostavu pošte');
        $this->crud->field('sprat')->size(1)->tab('Adresa za dostavu pošte');
        $this->crud->field('stan')->size(1)->tab('Adresa za dostavu pošte');
        $this->crud->field('posta_pb')->size(4)->type('number')->label('Poštanski broj')->tab('Adresa za dostavu pošte');
        $this->crud->field('postaOpstinaId')->size(4)->label('Opština')->tab('Adresa za dostavu pošte');
        $this->crud->field('posta_drzava')->size(4)->label('Država')->tab('Adresa za dostavu pošte');

//        firma
        $this->crud->field('firmanaziv')->label('Naziv firme ako nema MB')->attributes(['readonly' => 'readonly'])->tab('Podaci o firmi');
//        $this->crud->field('firmaopstinaid')->label('Opština firme')->attributes(['readonly' => 'readonly'])->tab('Podaci o firmi');
//        $this->crud->field('firmafax')->label('Fax firme')->tab('Podaci o firmi');
        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'firma',
            'attribute' => 'naziv_mb',
            'label' => 'Firma po matičnom broju (pretraži po MB ili nazivu)',
            'tab' => 'Podaci o firmi',
            'ajax' => TRUE,
            'inline_create' => TRUE
        ]);
//        $this->crud->field('firma')->tab('Podaci o firmi');

//        osiguranje
        //todo: ne radi, same name "osiguranja"
        /*        $this->crud->addField([
                    'name' => 'osiguranja',
                    'label' => 'Ugovarač osiguranja',
                    'type' => 'relationship',
                    'attribute' => 'firmaUgovarac.naziv',
                    'tab' => 'Podaci o osiguranju',
                ]);
                $this->crud->addField([
                    'name' => 'osiguranja1',
                    'label' => 'Ugovarač osiguranja (individualno)',
                    'type' => 'select2',
                    'entity' => 'osiguranja',
                    'attribute' => 'osobaUgovarac.id',
                    'tab' => 'Podaci o osiguranju',
                ]);
                $this->crud->addField([
                    'name' => 'osiguranja2',
                    'label' => 'Osiguravajuća kuca',
                    'type' => 'select2',
                    'model' => 'App\Models\Osiguranje',
                    'entity' => 'osiguranja',
        //            'entity' => 'osiguranja.firmaOsiguravajucaKuca',
        //            'attribute' => 'firmaOsiguravajucaKuca.naziv',
                    'attribute' => 'naziv',
        //            'attribute' => 'id',
                    'tab' => 'Podaci o osiguranju',
                    'options' => function ($q) {
                        return $q->whereHas('firmaOsiguravajucaKuca')->get();
                    }
                ]);
                $this->crud->addField([
                    'name' => 'polisaPokrice',
                    'label' => 'Pokriće polise',
                    'entity' => 'osiguranja',
                    'attribute' => 'polisaPokrice.naziv',
                    'tab' => 'Podaci o osiguranju',
                ]);*/

//        obrazovanje
        $this->crud->addField([
            'name' => 'bolonja',
            'label' => 'Da li su studije završene po bolonji?',
            'tab' => 'Obrazovanje',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'separatorOS',
            'type' => 'custom_html',
            'tab' => 'Obrazovanje',
            'value' => '<div class="p-3 text-center" style="background-color: rgba(124,105,239,0.2)"><h4>OSNOVNE STUDIJE</h4></div>'
        ]);
        $this->crud->field('diplfakultet')->size(4)->label('Naziv fakulteta')->tab('Obrazovanje');
        $this->crud->field('diplmesto')->size(4)->label('Mesto')->tab('Obrazovanje');
        $this->crud->field('dipldrzava')->size(4)->label('Država')->tab('Obrazovanje');
        $this->crud->field('diplodsek')->size(4)->label('Odsek')->tab('Obrazovanje');
        $this->crud->field('diplsmer')->size(4)->label('Smer')->tab('Obrazovanje');
        $this->crud->field('diplgodina')->size(2)->label('Godina završetka')->tab('Obrazovanje');
        $this->crud->field('diplbroj')->size(2)->label('Broj diplome')->tab('Obrazovanje');
        $this->crud->addField([
            'name' => 'separatorMS',
            'type' => 'custom_html',
            'tab' => 'Obrazovanje',
            'value' => '<div class="p-3 text-center" style="background-color: rgba(124,105,239,0.2)"><h4>MASTER STUDIJE</h4></div>'
        ]);
        $this->crud->field('mrfakultet')->size(4)->label('Naziv fakulteta')->tab('Obrazovanje');
        $this->crud->field('mrmesto')->size(4)->label('Mesto')->tab('Obrazovanje');
        $this->crud->field('mrdrzava')->size(4)->label('Država')->tab('Obrazovanje');
        $this->crud->field('mrodsek')->size(4)->label('Odsek')->tab('Obrazovanje');
        $this->crud->field('mrsmer')->size(4)->label('Smer')->tab('Obrazovanje');
        $this->crud->field('mrgodina')->size(2)->label('Godina završetka')->tab('Obrazovanje');
        $this->crud->field('mrbroj')->size(2)->label('Broj diplome')->tab('Obrazovanje');

//        funkcije
        $this->crud->addFields([
            'funkcija_id' => [
                'name' => 'funkcija_id',
                'label' => 'Funkcija id',
                'tab' => 'Podaci o statusu u IKS',
                'attributes' => ['readonly' => 'readonly'],
                'wrapper' => ['class' => 'col-4'],
            ],
            'clanskupstine' => [
                'name' => 'clanskupstine',
                'label' => 'Član Skupštine',
                'type' => 'select_from_array',
                'options' => [0 => 'Ne', 1 => 'Da'],
                'tab' => 'Podaci o statusu u IKS',
//                'attributes' => ['readonly' => 'readonly'],
                'wrapper' => ['class' => 'col-4'],
            ],
            'clan' => [
                'name' => 'clan',
                'label' => 'Članstvo',
                'type' => 'select_from_array',
                'options' => [-1 => 'Funkcioner', 0 => 'Nije član', 1 => 'Član', 100 => 'Na čekanju', 10 => 'Priprema se brisanje iz članstva'],
                'tab' => 'Podaci o statusu u IKS',
//                'attributes' => ['readonly' => 'readonly'],
                'wrapper' => ['class' => 'col-4'],
            ],
        ]);

//        portal
        $this->crud->field('licniweb')->size(3)->label('Web')->tab('Portal');
        $this->crud->addField([
            'name' => 'adresaprikazi',
            'label' => 'Prikaži adresu prebivališta',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da', 2 => 'Prikaži samo grad i opštinu'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'telefonprikazi',
            'label' => 'Prikaži broj telefona',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'mobilniprikazi',
            'label' => 'Prikaži broj mobilnog telefona',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'faxprikazi',
            'label' => 'Prikaži broj faksa',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'mailprikazi',
            'label' => 'Prikaži imejl adresu',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'prikazisliku',
            'label' => 'Prikaži fotografiju',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'dozvolareklamnimail',
            'label' => 'Dozvoli prijem obaveštenja na imejl',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3'],
        ]);
        $this->crud->addField([
            'name' => 'imalp',
            'label' => 'Poseduje ličnu prezentaciju',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
            'wrapper' => ['class' => 'col-3 mt-2'],
        ]);
        $this->crud->addField([
            'name' => 'zaposlen',
            'label' => 'Status radnog odnosa',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da', 3 => 'Penzioner'],
            'wrapper' => ['class' => 'col-3 mt-2'],
        ]);
        $this->crud->field('lozinka')->attributes(['disabled' => 'disabled'])->tab('Portal');
        $this->crud->field('biografija')->tab('Portal');

//        razno
        $this->crud->field('napomena')->size(6)->tab('Razno');
        $this->crud->field('vrsta_poslova')->size(6)->tab('Razno');
        $this->crud->addField([
            'name' => 'temp_dms_password',
            'tab' => 'Razno',
            'attributes' => ['readonly' => 'readonly'],
            'wrapper' => ['class' => 'col-6'],
        ]);
        $this->crud->addField([
            'name' => 'primary_serial',
            'tab' => 'Razno',
            'attributes' => ['readonly' => 'readonly'],
            'wrapper' => ['class' => 'col-6'],
        ]);
        /*        $this->crud->addField([   // DateTime
                    'name' => 'created_at',
                    'label' => 'Kreiran',
                    'type' => 'datetime_picker',

                    // optional:
                    'datetime_picker_options' => [
                        'format' => 'DD.MM.YYYY. HH:mm:ss',
                        'language' => 'sr_latin',
                    ],
                    'tab' => 'Razno',
                ]);
                $this->crud->addField([   // DateTime
                    'name' => 'updated_at',
                    'label' => 'Ažuriran',
                    'type' => 'datetime_picker',

                    // optional:
                    'datetime_picker_options' => [
                        'format' => 'DD.MM.YYYY. HH:mm:ss',
                        'language' => 'sr_latin',
                    ],
                    'tab' => 'Razno',
                ]);*/

    }

    public function fetchFirma()
    {
        return $this->fetch([
            'model' => Firma::class, // required
//            'searchable_attributes' => ['mb'],
            'searchable_attributes' => ['mb', 'naziv'],
            'paginate' => 10, // items to show per page
//            'query' => function($model) {
//                return $model->active();
//            } // to filter the results that are returned
        ]);

    }

    protected function showDetailsRow($id)
    {
//        $this->crud->hasAccessOrFail('details_row');//???

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
//dd($this->data['entry']->clanarine->count());
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
//        return view('crud.details_row', $this->data);
        return view('crud::osoba_details_row', $this->data);
    }
}
