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
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'licenca',
            'attribute' => 'ime_prezime_jmbg',
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
        'posta'=> [
            'name' => 'posta',
            'type' => 'model_function',
            'function_name' => 'postaParseJson',
            'limit' => 500,
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
                'name' => 'licenca.osobaRel',
                'type' => 'relationship',
                'label' => 'Broj licence',
                'attribute' => 'licence_array',
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
//        'licenca.osobaRel',
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
        CRUD::setRoute(config('backpack.base.route_prefix') . '/membership/promenapodataka');
        CRUD::setEntityNameStrings('promena podataka', 'Promena podataka');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['update']);
        }

        CRUD::set('show.setFromDb', FALSE);

        $this->crud->enableExportButtons();

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumns($this->columns_definition_array);

        $this->crud->removeColumns([
//        'id',
            'licni_podaci',
//        'osoba',
            'ime',
            'prezime',
//            'licenca.osobaRel',
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
//        'updated_at',
        'posta',
        ]);

        $this->crud->modifyColumn('id', [
            'name' => 'id',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
                    $query->whereIn('id', $searchTermArray);
                } else {
                    $query->orWhere('id', 'ilike', $searchTerm . '%');
                }
            }
        ]);


        $this->crud->setColumnDetails('licenca.osobaRel', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('licenca.osobaRel', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('licenca', function ($q) use ($column, $searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%')
                            ->orWhereHas('osobaRel', function ($q) use ($column, $searchTerm) {
                                $q
                                    ->where('id', 'ilike', $searchTerm . '%')
                                    ->orWhere('ime', 'ilike', $searchTerm . '%')
                                    ->orWhere('prezime', 'ilike', $searchTerm . '%');
                            });
                    });
                }
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
                    default:
                        return 'Nepoznat status';
                }
            },
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->obradjen === 1)
                        return 'btn btn-sm btn-outline-info';
                }
            ]
        ]);

        /*
         * Define filters
         * start
         */

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'neobradjeni',
            'label' => 'Neobrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', NEAKTIVAN); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'obradjeni',
            'label' => 'Obrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', AKTIVAN); // apply the "active" eloquent scope
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
                ];
            }, function ($values) { // if the filter is active
                $this->crud->addClause('whereIn', 'obradjen', json_decode($values));
            });
        }

        // date range filter
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

        $this->crud->setColumnDetails('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info',
            ]
        ]);

        $this->crud->modifyColumn('brlic', [
            'name' => 'brlic',
            'label' => 'Licence',
            'type' => 'select',
            'entity' => 'licenca.osobaRel',
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
                    default:
                        return 'Nepoznat status';
                }
            },
            'wrapper' => [
                'class' => 'btn btn-sm btn-outline-info',
            ]
        ]);

    }
}
