<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\UpdateLicencaStatusOperation;
use App\Http\Requests\OsobaRequest;
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
        'rodjenjedan' => [
            'name' => 'rodjenjedan',
            'label' => 'Dan',
        ],
        'rodjenjemesec' => [
            'name' => 'rodjenjemesec',
            'label' => 'Mesec',
        ],
        'rodjenjegodina' => [
            'name' => 'rodjenjegodina',
            'label' => 'Godina',
        ],
        'rodjenjeopstinaid' => [
            'name' => 'opstinaId',
            'label' => 'Opština (relacija)',
            'type' => 'relationship',
            'attribute' => 'ime',
        ],
        'rodjenjeinodrzava' => [
            'name' => 'rodjenjeinodrzava',
            'label' => 'Država',
        ],
        'rodjenjeinomesto' => [
            'name' => 'rodjenjeinomesto',
            'label' => 'Mesto',
        ],
        'datumrodjenja' => [
            'name' => 'datumrodjenja',
            'label' => 'Datum',
            'type' => 'date',
            'format' => 'DD.MM.Y.'
        ],

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
        'prebivalistedrzava' => [
            'name' => 'prebivalistedrzava',
            'label' => 'Država',
        ],
        'prebivalistebroj' => [
            'name' => 'prebivalistebroj',
            'label' => 'Poštanski broj',
        ],
        'prebivalistemesto' => [
            'name' => 'prebivalistemesto',
            'label' => 'Mesto',
        ],
        'prebivalisteopstinaid' => [
            'name' => 'opstinaId',
            'label' => 'Opština (relacija)',
            'type' => 'relationship',
            'attribute' => 'ime',
        ],
        'prebivalisteopstina' => [
            'name' => 'prebivalisteopstina',
            'label' => 'Opština',
        ],
        'prebivalisteadresa' => [
            'name' => 'prebivalisteadresa',
            'label' => 'Adresa',
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

//        firma
        'firma' => [
            'label' => 'PODACI O FIRMI',
            'name' => 'firma',
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
            'name' => 'firmaemail',
            'label' => 'Telefon',
            'type' => 'phone',
        ],
        'firmaemail' => [
            'name' => 'firmaemail',
            'label' => 'Mejl',
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
        'firma_mb' => [
            'name' => 'firma',
            'type' => 'relationship',
            'attribute' => 'naziv_mb',
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
        /*        'osiguranjasection' => [
                    'label' => 'PODACI O OSIGURANJU',
                    'name' => 'osiguranjasection',
                ],
                'osiguranjetip' => [
                    'name' => 'osiguranjetip',
                    'label' => 'Tip osiguranja',
                    'type' => 'select2',
                    'model' => 'App\Models\Osiguranje',
                    'attribute', 'osiguranjeTip.naziv'
                ],
                'osiguranja' => [
                    'name' => 'osiguranja.firmaOsiguravajucaKuca',
                    'label' => 'Osiguravajuća kuća',
                    'attribute', 'naziv'
                ],*/


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
        'lozinka',
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
            'label' => 'Prikaži mejl adresu',
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
            'label' => 'Dozvoli prijem obaveštenja na mejl',
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

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
            $this->crud->addClause('whereRaw', 'length(id) = 13');
        }

        if (backpack_user()->hasRole('sluzba_maticne_sekcije')){
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
            'licni_podaci', 'rodjenje', 'adrese', 'prebivaliste', 'posta', 'firma', 'obrazovanje', 'osiguranjasection', 'dipl', 'mr', 'funkcije', 'portal', 'razno',
//            end

            'devojackoprezime',
            'prezime_staro',
            /*'zvanje' => [
                'name' => 'zvanjeId',
                'type' => 'relationship',
                'label' => 'Zvanje',
            ],*/
            'titula' => [
                'name' => 'titula',
                'type' => 'select',
                'entity' => 'titulaId',
                'model' => 'App\Models\Titula',
                'attribute' => 'naziv',
            ],
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

//        adrese
//        prebivaliste
            'prebivalisteopstinaid' => [
                'name' => 'opstinaId',
                'type' => 'relationship',
                'attribute' => 'ime',
            ],
            'prebivalistebroj',
            'prebivalistemesto',
            'prebivalisteopstina',
            'prebivalisteadresa',
            'prebivalistedrzava',
//        posta
            'ulica',
            'broj',
            'podbroj',
            'sprat',
            'stan',

//        firma
            'firmanaziv',
            'firmamesto',
            'firmaopstina',
            'firmaweb',
            'firmatel',
            'firmaemail',
            'firmaopstinaid' => [
                'name' => 'opstinaId',
                'type' => 'relationship',
                'attribute' => 'ime',
            ],
            'firmafax',
            'firma_mb' => [
                'name' => 'firma',
                'type' => 'relationship',
                'attribute' => 'naziv_mb',
            ],

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
//    'napomena',
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

        $this->crud->setColumnDetails('idn', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
//                    $searchTermArray = array_map('trim', $searchTerm);
                    $searchTermArray = array_map(function($item) {
                        return trim($item, ' \'",.;');
                    }, $searchTerm);
                    $query->whereIn('id', $searchTermArray)->orderBy('id');
                } else if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->where('ime', 'ilike', $searchTerm[0] . '%')
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
//        studije
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

//        todo: link
        /*        $this->crud->modifyColumn('firmaweb', [
                    'wrapper' => [
                        'href' => function ($entry) {
                            dd($entry->id);
                            return '//' . $entry->firmaweb;
                        },
                        'class' => 'btn btn-sm btn-outline-info mr-1',
                        'target' => '_blank',
                    ]
                ]);*/
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
        ]);
        $this->crud->field('lib')->tab('Lični podaci')->attributes(['readonly' => 'readonly']);;
        $this->crud->field('ime')->tab('Lični podaci');
        $this->crud->field('prezime')->tab('Lični podaci');
        $this->crud->field('roditelj')->tab('Lični podaci');
        $this->crud->field('devojackoprezime')->label('Devojačko prezime')->tab('Lični podaci');
        $this->crud->field('zvanjeId')->label('Zvanje')->tab('Lični podaci');
        $this->crud->field('rodjenjemesto')->label('Mesto rođenja')->tab('Lični podaci');
        $this->crud->field('rodjenjeopstina')->label('Opština rođenja')->tab('Lični podaci');
        $this->crud->field('rodjenjedrzava')->label('Država rođenja')->tab('Lični podaci');
        $this->crud->field('kontakttel')->label('Broj telefona')->tab('Lični podaci');
        $this->crud->field('mobilnitel')->label('Broj mobilnog telefona')->tab('Lični podaci');
        $this->crud->field('kontaktemail')->label('Mail')->tab('Lični podaci')->type('email');

//        prebivaliste
        $this->crud->field('prebivalistebroj')->label('Poštanski broj')->tab('Podaci o prebivalištu');
        $this->crud->field('prebivalistemesto')->label('Mesto')->tab('Podaci o prebivalištu');
        $this->crud->field('opstinaId')->label('Opština')->tab('Podaci o prebivalištu');
        $this->crud->field('prebivalisteadresa')->label('Adresa')->tab('Podaci o prebivalištu');
        $this->crud->field('ulica')->tab('Podaci o prebivalištu');
        $this->crud->field('broj')->tab('Podaci o prebivalištu');
        $this->crud->field('podbroj')->tab('Podaci o prebivalištu');
        $this->crud->field('sprat')->tab('Podaci o prebivalištu');
        $this->crud->field('stan')->tab('Podaci o prebivalištu');

//        firma
        $this->crud->field('firmanaziv')->label('Naziv firme ako nema MB')->tab('Podaci o firmi')->attributes(['readonly' => 'readonly']);
        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'firma',
            'label' => 'Firma po matičnom broju (pretraži po MB ili nazivu)',
            'tab' => 'Podaci o firmi',
            'ajax' => TRUE,
            'inline_create' => TRUE
        ]);
        $this->crud->field('firma')->tab('Podaci o firmi');

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
        $this->crud->field('diplfakultet')->label('Naziv fakulteta')->tab('Obrazovanje');
        $this->crud->field('diplmesto')->label('Mesto')->tab('Obrazovanje');
        $this->crud->field('dipldrzava')->label('Država')->tab('Obrazovanje');
        $this->crud->field('diplodsek')->label('Odsek')->tab('Obrazovanje');
        $this->crud->field('diplsmer')->label('Smer')->tab('Obrazovanje');
        $this->crud->field('diplgodina')->label('Godina završetka')->tab('Obrazovanje');
        $this->crud->field('diplbroj')->label('Broj diplome')->tab('Obrazovanje');
        $this->crud->addField([
            'name' => 'separatorMS',
            'type' => 'custom_html',
            'tab' => 'Obrazovanje',
            'value' => '<div class="p-3 text-center" style="background-color: rgba(124,105,239,0.2)"><h4>MASTER STUDIJE</h4></div>'
        ]);
        $this->crud->field('mrfakultet')->label('Naziv fakulteta')->tab('Obrazovanje');
        $this->crud->field('mrmesto')->label('Mesto')->tab('Obrazovanje');
        $this->crud->field('mrdrzava')->label('Država')->tab('Obrazovanje');
        $this->crud->field('mrodsek')->label('Odsek')->tab('Obrazovanje');
        $this->crud->field('mrsmer')->label('Smer')->tab('Obrazovanje');
        $this->crud->field('mrgodina')->label('Godina završetka')->tab('Obrazovanje');
        $this->crud->field('mrbroj')->label('Broj diplome')->tab('Obrazovanje');

//        funkcije
        $this->crud->addFields([
            'funkcija_id' => [
                'name' => 'funkcija_id',
                'label' => 'Funkcija id',
                'tab' => 'Podaci o statusu u IKS',
                'attributes' => ['readonly' => 'readonly'],
            ],
            'clanskupstine' => [
                'name' => 'clanskupstine',
                'label' => 'Član Skupštine',
                'type' => 'select_from_array',
                'options' => [0 => 'Ne', 1 => 'Da'],
                'tab' => 'Podaci o statusu u IKS',
//                'attributes' => ['readonly' => 'readonly'],
            ],
            'clan' => [
                'name' => 'clan',
                'label' => 'Članstvo',
                'type' => 'select_from_array',
                'options' => [-1 => 'Funkcioner', 0 => 'Nije član', 1 => 'Član', 100 => 'Na čekanju', 10 => 'Priprema se brisanje iz članstva'],
                'tab' => 'Podaci o statusu u IKS',
//                'attributes' => ['readonly' => 'readonly'],
            ],
        ]);

//        portal
        $this->crud->field('lozinka')->tab('Portal');
        $this->crud->field('biografija')->tab('Portal');
        $this->crud->field('licniweb')->label('Web')->tab('Portal');
        $this->crud->addField([
            'name' => 'adresaprikazi',
            'label' => 'Prikaži adresu prebivališta',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da', 2 => 'Prikaži samo grad i opštinu'],
        ]);
        $this->crud->addField([
            'name' => 'telefonprikazi',
            'label' => 'Prikaži broj telefona',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'mobilniprikazi',
            'label' => 'Prikaži broj mobilnog telefona',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'faxprikazi',
            'label' => 'Prikaži broj faksa',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'mailprikazi',
            'label' => 'Prikaži mejl adresu',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'prikazisliku',
            'label' => 'Prikaži fotografiju',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'dozvolareklamnimail',
            'label' => 'Dozvoli prijem obaveštenja na mejl',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'imalp',
            'label' => 'Poseduje ličnu prezentaciju',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'zaposlen',
            'label' => 'Status radnog odnosa',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => [0 => 'Ne', 1 => 'Da', 3 => 'Penzioner'],
        ]);

//        razno
        $this->crud->field('napomena')->tab('Razno');
        $this->crud->field('vrsta_poslova')->tab('Razno');
        $this->crud->addField([
            'name' => 'st_drzavljanstvoscg',
            'label' => 'Državljanstvo SCG',
            'tab' => 'Portal',
            'type' => 'select_from_array',
            'options' => ['N' => 'Ne', 'D' => 'Da'],
        ]);
        $this->crud->addField([
            'name' => 'temp_dms_password',
            'tab' => 'Razno',
            'attributes' => ['readonly' => 'readonly'],
        ]);
        $this->crud->addField([
            'name' => 'primary_serial',
            'tab' => 'Razno',
            'attributes' => ['readonly' => 'readonly'],
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
            'model' => \App\Models\Firma::class, // required
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
