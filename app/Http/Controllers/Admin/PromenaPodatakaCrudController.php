<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PromenaPodatakaEmailRequest;
use App\Models\Licenca;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaPromenaPodatakaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PromenaPodatakaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use Operations\PromenaPodatakaEmailOperation;
    use Operations\PromenaPodatakaObradaBulkOperation;

    protected
        $columns_definition_array = [
        'id',
        'licni_podaci' => [
            'label' => 'LIČNI PODACI',
            'name' => 'licni_podaci',
        ],
        'osoba' => [
            'name' => 'osoba',
//            'name' => 'licenca',
//            'name' => 'licenca.osobaId',
            'type' => 'select',
//            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'entity' => 'licenca',
            'attribute' => 'ime_prezime_jmbg',
//            'attribute' => 'ime_prezime_licence',
            'model' => 'App\Models\Licenca',
        ], // virtual column
        /*'ime'=>[
            'name'=> 'ime'
        ],
        'prezime'=>[
            'name'=> 'prezime'
        ],*/
        'brlic' => [
            'name' => 'brlic',
            'label' => 'Broj licence'
        ],
        'adresa' => [
            'name' => 'adresa',
            'label' => 'Adresa prebivališta'
        ],
        'mesto' => [
            'name' => 'mesto',
            'label' => 'Mesto prebivališta'
        ],
        'pbroj' => [
            'name' => 'pbroj',
            'label' => 'Poštanski broj mesta prebivališta'
        ],
//        'topstina_id',
        'opstina' => [
            'name' => 'opstina',
            'type' => 'relationship',
            'attribute' => 'ime',
            'ajax' => TRUE,
            'label' => 'Opština',
        ],
        'tel' => [
            'name' => 'tel',
            'label' => 'Telefon'
        ],
        'mob' => [
            'name' => 'mob',
            'label' => 'Mobilni'
        ],
        'email',
        'firma_podaci' => [
            'label' => 'PODACI O FIRMI',
            'name' => 'firma_podaci',
        ],
        'nazivfirm' => [
            'name' => 'nazivfirm',
            'label' => 'Naziv'
        ],
        'adresafirm' => [
            'name' => 'adresafirm',
            'label' => 'Adresa'
        ],
        'mestofirm' => [
            'name' => 'mestofirm',
            'label' => 'Mesto'
        ],
        'opstinafirm' => [
            'name' => 'opstinafirm',
            'label' => 'Opština'
        ],
        'mbfirm' => [
            'name' => 'mbfirm',
            'label' => 'Matični broj'
        ],
        'pibfirm' => [
            'name' => 'pibfirm',
            'label' => 'PIB'
        ],
        'emailfirm' => [
            'name' => 'emailfirm',
            'label' => 'Email'
        ],
        'telfirm' => [
            'name' => 'telfirm',
            'label' => 'Telefon'
        ],
        'wwwfirm' => [
            'name' => 'wwwfirm',
            'label' => 'www'
        ],
        'zahtev_podaci' => [
            'label' => 'PODACI O ZAHTEVU',
            'name' => 'zahtev_podaci',
        ],
        'obradjen' => [
            'name' => 'obradjen',
            'label' => 'Status zahteva',
            'type' => 'closure',
        ],
        'ipaddress' => [
            'name' => 'ipaddress',
            'label' => 'IP adresa'
        ],
        'datumprijema' => [
            'name' => 'datumprijema',
            'label' => 'Datum prijema',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'datumobrade' => [
            'name' => 'datumobrade',
            'label' => 'Datum obrade',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ],
        // todo: privremeno, dok regionalci pozivaju telefonom ljude koji nemaju email
        $remove_columns_list_definition_array = [
//        'id',
        'licni_podaci',
//        'osoba',
        'ime',
        'prezime',
//        'brlic',
        'adresa',
        'mesto',
        'pbroj',
        'topstina_id',
        'opstina',
//        'tel',
//        'mob',
//        'email',
        'firma_podaci',
        'nazivfirm',
        'mestofirm',
        'opstinafirm',
        'emailfirm',
        'telfirm',
        'wwwfirm',
        'zahtev_podaci',
        'ipaddress',
        'datumprijema',
        'datumobrade',
//        'obradjen',
        'mbfirm',
        'pibfirm',
        'adresafirm',
//        'napomena',
//        'created_at',
//        'updated_at'
    ],

        $fields_definition_array = [
        'id' =>
            [
                'name' => 'id',
                'attributes' => ['readonly' => 'readonly'],
            ],
        'osoba' => [
            'name' => 'licenca.osobaId',
            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
            'attributes' => ['disabled' => 'disabled'],
            'ajax' => TRUE,
        ], // virtual field,
        /* 'ime' =>
             [
                 'name' => 'ime',
                 'attributes' => ['disabled' => 'disabled'],
             ],
         'prezime' =>
             [
                 'name' => 'prezime',
                 'attributes' => ['disabled' => 'disabled'],
             ],*/
        'brlic' =>
            [
                'name' => 'brlic',
                'type' => 'relationship',
                'label' => 'Broj licence',
                'attribute' => 'osobaId.licence_array',
                'entity' => 'licenca',
                'attributes' => ['disabled' => 'disabled'],
                'ajax' => TRUE,
            ],
        'adresa' => [
            'name' => 'adresa',
            'label' => 'Adresa prebivališta'
        ],
        'mesto' => [
            'name' => 'mesto',
            'label' => 'Mesto prebivališta'
        ],
        'pbroj' => [
            'name' => 'pbroj',
            'label' => 'Poštanski broj mesta prebivališta'
        ],
//        'topstina_id',
        'opstina' => [
            'name' => 'opstina',
            'type' => 'relationship',
            'attribute' => 'ime',
//            'ajax' => TRUE,
            'label' => 'Opština',
            'placeholder' => 'Odaberite opštinu prebivališta',
            'hint' => 'Odaberite jednu od ponuđenih opcija za opštinu prebivališta.',
        ],
        'tel' => [
            'name' => 'tel',
            'label' => 'Telefon'
        ],
        'mob' => [
            'name' => 'mob',
            'label' => 'Mobilni'
        ],
        'email',
        'obradjen' => [
            'name' => 'obradjen',
            'label' => 'Status zahteva',
            'type' => 'select_from_array',
        ],
        'nazivfirm' => [
            'name' => 'nazivfirm',
            'label' => 'Naziv firme'
        ],
        'mestofirm' => [
            'name' => 'mestofirm',
            'label' => 'Mesto firme'
        ],
        'opstinafirm' => [
            'name' => 'opstinafirm',
            'label' => 'Opština firme'
        ],
        'emailfirm' => [
            'name' => 'emailfirm',
            'label' => 'Email firme'
        ],
        'telfirm' => [
            'name' => 'telfirm',
            'label' => 'Telefon firme'
        ],
        'wwwfirm' => [
            'name' => 'wwwfirm',
            'label' => 'Www'
        ],
        'ipaddress',
        'datumprijema' => [
            'name' => 'datumprijema',
            'label' => 'Datum prijema',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'datumobrade' => [
            'name' => 'datumobrade',
            'label' => 'Datum obrade',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'mbfirm' => [
            'name' => 'mbfirm',
            'label' => 'MB'
        ],
        'pibfirm' => [
            'name' => 'pibfirm',
            'label' => 'PIB'
        ],
        'adresafirm' => [
            'name' => 'adresafirm',
            'label' => 'Adresa firme'
        ],
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss',
            'attributes' => [
                'readonly' => 'readonly'
            ]
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ],
        $remove_fields_definition_array = [
//        'id',
//        'osoba',
        'ime',
        'prezime',
//        'brlic',
        'adresa',
        'mesto',
        'pbroj',
        'topstina_id',
        'tel',
        'mob',
//        'email',
        'nazivfirm',
        'mestofirm',
        'opstinafirm',
        'emailfirm',
        'telfirm',
        'wwwfirm',
        'ipaddress',
        'datumprijema',
        'datumobrade',
        'obradjen',
        'mbfirm',
        'pibfirm',
        'adresafirm',
//        'napomena',
        'created_at',
        'updated_at',
    ];


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\PromenaPodataka::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/clanstvo/promenapodataka');
        CRUD::setEntityNameStrings('promena podataka', 'Promena podataka');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess('update');
        }
//        TODO: da bi se prikazala checkbox kolona za bulk action mora u setup-u da se definisu kolone, u suprotnom nece da prikaze kolonu sa chechbox-ovima
        $this->crud->setColumns($this->columns_definition_array);

        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('licenca.osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('licenca.osobaId', function ($q) use ($column, $searchTerm) {
                        $q
                            ->where('ime', 'ilike', $searchTerm . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm . '%')
                            ->orWhere('id', 'ilike', $searchTerm . '%');
//                        });
                    });
                }
            }
        ]);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeColumns($this->remove_columns_list_definition_array);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */

//        todo: testirati da li radi pretraga sa licencom sa kojom nije podneo zahtev
        $this->crud->setColumnDetails('brlic', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('licenca', function ($q) use ($column, $searchTerm) {
                    $q->where('id', 'ilike', $searchTerm . '%');
                });
            }
        ]);

        $this->crud->setColumnDetails('obradjen', [
            'name' => 'obradjen',
            'function' => function ($entry) {
                switch ($entry->obradjen) {
                    case 0:
                        return 'Neobrađen';
                    case 1:
                        return 'Obrađen';
                    case 2:
                        return 'Duplikat';
                    case 3:
                        return 'Email';
                    case 4:
                        return 'Otkazan';
                    case 5:
                        return 'Noviji';
                    case 6:
                        return 'Potpis';
                    case 7:
                    case 16:
                    case 32:
                    case 33:
                    case 34:
                    case 35:
                    case 36:
                    case 37:
                    case 38:
                    case 39:
                    case 40:
                    case 41:
                    case 42:
                        return 'Email-neobrađen';
                    case 102:
                    case 116:
                    case 132:
                    case 133:
                    case 134:
                    case 135:
                    case 136:
                    case 137:
                    case 138:
                    case 139:
                    case 140:
                    case 141:
                    case 142:
                        return 'Email-obrađen';
                    case 202:
                    case 216:
                    case 232:
                    case 233:
                    case 234:
                    case 235:
                    case 236:
                    case 237:
                    case 238:
                    case 239:
                    case 240:
                    case 241:
                    case 242:
                        return 'Email-Problem';
                    case 300:
                        return 'Bulk-neobradjen';
                }
            },
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->obradjen) {
                        case 0:
                        case backpack_user()->id:
                            return 'bg-warning px-2 rounded';
                        case 1:
                            return 'bg-success text-white px-2 rounded';
                        case backpack_user()->id + 100:
                            return 'bg-info text-white px-2 rounded';
                        case backpack_user()->id + 200:
                            return 'bg-danger text-white px-2 rounded';
                    }
                }
            ]
        ]);

        /*
         * Define filters
         * start
         */
        if (!backpack_user()->hasRole('admin')) {
            $this->crud->addFilter([
                'type' => 'simple',
                'name' => 'active',
                'label' => backpack_user()->name
            ],
                FALSE,
                function () { // if the filter is active
                    $this->crud->query->whereIn('obradjen', [backpack_user()->id, backpack_user()->id + 100, backpack_user()->id + 200]); // apply the "active" eloquent scope
                });
        }

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'neobradjeni',
            'label' => 'Nebrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'obradjeni',
            'label' => 'Obrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id + 100); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'problematicni',
            'label' => 'Problematični'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id + 200); // apply the "active" eloquent scope
            });

        if (backpack_user()->hasRole('admin')) {
            $this->crud->addFilter([
                'name' => 'obradjen',
                'type' => 'select2_multiple',
                'label' => 'Status',
                'ajax' => TRUE,
            ], function () {
                return [
                    0 => '0 - Neobrađen',
                    1 => '1 - Obrađen',
                    2 => '2 - Duplikat',
                    3 => '3 - Email',
                    4 => '4 - Otkazan',
                    5 => '5 - Noviji',
                    6 => '6 - Potpis',
                    7 => '7 - Email-neobrađen',
                    16 => '16 - Email-neobrađen',
                    32 => '32 - Email-neobrađen',
                    33 => '33 - Email-neobrađen',
                    34 => '34 - Email-neobrađen',
                    35 => '35 - Email-neobrađen',
                    36 => '36 - Email-neobrađen',
                    37 => '37 - Email-neobrađen',
                    38 => '38 - Email-neobrađen',
                    39 => '39 - Email-neobrađen',
                    40 => '40 - Email-neobrađen',
                    41 => '41 - Email-neobrađen',
                    42 => '42 - Email-neobrađen',
                    116 => '116 - Email-obrađen',
                    132 => '132 - Email-obrađen',
                    133 => '133 - Email-obrađen',
                    134 => '134 - Email-obrađen',
                    135 => '135 - Email-obrađen',
                    136 => '136 - Email-obrađen',
                    137 => '137 - Email-obrađen',
                    138 => '138 - Email-obrađen',
                    139 => '139 - Email-obrađen',
                    140 => '140 - Email-obrađen',
                    141 => '141 - Email-obrađen',
                    142 => '142 - Email-obrađen',
                    216 => '216 - Email-Problem',
                    232 => '232 - Email-Problem',
                    233 => '233 - Email-Problem',
                    234 => '234 - Email-Problem',
                    235 => '235 - Email-Problem',
                    236 => '236 - Email-Problem',
                    237 => '237 - Email-Problem',
                    238 => '238 - Email-Problem',
                    239 => '239 - Email-Problem',
                    240 => '240 - Email-Problem',
                    241 => '241 - Email-Problem',
                    242 => '242 - Email-Problem',
                    300 => '300 - Bulk-neobradjen',
                ];
            }, function ($values) { // if the filter is active
                $this->crud->addClause('whereIn', 'obradjen', json_decode($values));
            });
        }

        // daterange filter
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'created_at',
            'label' => 'Period'
        ],
            FALSE,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'created_at', '>=', $dates->from);
                $this->crud->addClause('where', 'created_at', '<=', $dates->to);
            });

        /*
        * end
        * Define filters
        */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PromenaPodatakaEmailRequest::class);
        $this->crud->addFields($this->fields_definition_array);
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();

        $this->crud->modifyField('obradjen', [
            'options' => [
                0 => '(0) Neobrađen',
                1 => '(1) Obrađen',
                2 => '(2) Duplikat',
                3 => '(3) Email',
                4 => '(4) Otkazan',
                5 => '(5) Noviji',
                6 => '(6) Potpis',
                7 => '(7) Email-neobrađen',
                16 => '(16) Email-neobrađen Tijana',
                32 => '(32) Email-neobrađen Nada',
                33 => '(33) Email-neobrađen Ljilja',
                34 => '(34) Email-neobrađen Miljan',
                35 => '(35) Email-neobrađen Jasmina',
                36 => '(36) Email-neobrađen Milorad',
                37 => '(37) Email-neobrađen Milena',
                38 => '(38) Email-neobrađen Mirjana',
                39 => '(39) Email-neobrađen Aca',
                40 => '(40) Email-neobrađen Biserka',
                41 => '(41) Email-neobrađen Edisa',
                42 => '(42) Email-neobrađen Aleksandra',
                116 => '(116) Email-obrađen Tijana',
                132 => '(132) Email-obrađen Nada',
                133 => '(133) Email-obrađen Ljilja',
                134 => '(134) Email-obrađen Miljan',
                135 => '(135) Email-obrađen Jasmina',
                136 => '(136) Email-obrađen Milorad',
                137 => '(137) Email-obrađen Milena',
                138 => '(138) Email-obrađen Mirjana',
                139 => '(139) Email-obrađen Aca',
                140 => '(140) Email-obrađen Biserka',
                141 => '(141) Email-obrađen Edisa',
                142 => '(142) Email-obrađen Aleksandra',
                216 => '(216) Email-Problem Tijana',
                232 => '(232) Email-Problem Nada',
                233 => '(233) Email-Problem Ljilja',
                234 => '(234) Email-Problem Miljan',
                235 => '(235) Email-Problem Jasmina',
                236 => '(236) Email-Problem Milorad',
                237 => '(237) Email-Problem Milena',
                238 => '(238) Email-Problem Mirjana',
                239 => '(239) Email-Problem Aca',
                240 => '(240) Email-Problem Biserka',
                241 => '(241) Email-Problem Edisa',
                242 => '(242) Email-Problem Aleksandra',
                300 => '(300) Bulk-neobradjen',
            ]
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->setColumns($this->columns_definition_array);

        /*
         * virtual columns
         * for separating purpose
         * start
         */
        $this->crud->modifyColumn('licni_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="lpseparator"></div>
                        <script>
                            var row = document.getElementById("lpseparator").parentNode.parentNode.parentNode;
                            row.style.cssText = "background-color: rgba(124,105,239,0.2)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('firma_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="fpseparator"></div>
                        <script>
                            var row = document.getElementById("fpseparator").parentNode.parentNode.parentNode;
                            row.style.cssText = "background-color: rgba(124,105,239,0.2)";
                        </script>
                        '
        ]);

        $this->crud->modifyColumn('zahtev_podaci', [
            'type' => 'custom_html',
            'value' => '<div id="zpseparator"></div>
                        <script>
                            var row = document.getElementById("zpseparator").parentNode.parentNode.parentNode;
                            row.style.cssText = "background-color: rgba(124,105,239,0.2)";
                        </script>
                        '
        ]);
        /*
         * end
         * virtual columns
         * for separating purpose
         */

        $this->crud->modifyColumn('brlic', [
            'name' => 'brlic',
            'label' => 'Licence',
            'type' => 'select',
            'entity' => 'licenca.osobaId',
            'attribute' => 'licence_array',
            'model' => 'App\Models\Licenca',
        ]);

        $this->crud->setColumnDetails('obradjen', [
            'name' => 'obradjen',
            'function' => function ($entry) {
                switch ($entry->obradjen) {
                    case 0:
                        return 'Neobrađen';
                    case 1:
                        return 'Obrađen';
                    case 2:
                        return 'Duplikat';
                    case 3:
                        return 'Email';
                    case 4:
                        return 'Otkazan';
                    case 5:
                        return 'Noviji';
                    case 6:
                        return 'Potpis';
                    case 7:
                    case 16:
                    case 32:
                    case 33:
                    case 34:
                    case 35:
                    case 36:
                    case 37:
                    case 38:
                    case 39:
                    case 40:
                    case 41:
                    case 42:
                        return 'Email-neobrađen';
                    case 102:
                    case 116:
                    case 132:
                    case 133:
                    case 134:
                    case 135:
                    case 136:
                    case 137:
                    case 138:
                    case 139:
                    case 140:
                    case 141:
                    case 142:
                        return 'Email-obrađen';
                    case 202:
                    case 216:
                    case 232:
                    case 233:
                    case 234:
                    case 235:
                    case 236:
                    case 237:
                    case 238:
                    case 239:
                    case 240:
                    case 241:
                    case 242:
                        return 'Email-Problem';
                }
            },
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->obradjen) {
                        case 0:
                        case backpack_user()->id:
                            return 'bg-warning px-2 rounded';
                        case 1:
                            return 'bg-success text-white px-2 rounded';
                        case backpack_user()->id + 100:
                            return 'bg-info text-white px-2 rounded';
                        case backpack_user()->id + 200:
                            return 'bg-danger text-white px-2 rounded';
                    }
                }
            ]
        ]);

    }
}
