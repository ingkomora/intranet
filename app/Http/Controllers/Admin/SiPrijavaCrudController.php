<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\FileUploadOperation;
use App\Http\Controllers\Admin\Operations\RegisterRequestBulkOperation;
use App\Http\Requests\SiPrijavaRequest;
use App\Models\Document;
use App\Models\RegOblast;
use App\Models\RegPodoblast;
use App\Models\Sekcija;
use App\Models\SiPrijava;
use App\Models\SiVrsta;
use App\Models\VrstaPosla;
use App\Models\ZahtevLicenca;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class PrijavaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SiPrijavaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use FileUploadOperation;
    use RegisterRequestBulkOperation;


    protected
        $column_definition_array_admin = [
        'id' => [
            'name' => 'id',
            'label' => 'Broj prijave',
        ],
        'status_prijave' => [
            'name' => 'status',
            'label' => 'Status',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'osoba_id' => ['name' => 'osoba_id', 'label' => 'jmbg'],
        'osoba' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime',
            'attribute' => 'full_name',
        ],
        'documents' => [
            'name' => 'documents',
            'type' => 'relationship',
            'ajax' => TRUE,
//            'attribute' => 'id',
            'attribute' => 'category_type_name_status_registry_date',
//            'attributes' => ['disabled' => 'disabled',],
        ],
        'vrsta_posla_id' => [
            'name' => 'vrstaPosla',
            'label' => 'Vrsta posla',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_oblast_id' => [
            'name' => 'regOblast',
            'label' => 'Oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_pod_oblast_id' => [
            'name' => 'regPodOblast',
            'label' => 'Uža oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'zvanje_id' => [
            'name' => 'zvanje',
            'label' => 'Zvanje',
            'type' => 'relationship',
            'attribute' => 'skrnaziv',
        ],
        'si_vrsta_id' => [
            'name' => 'siVrsta',
            'label' => 'Vrsta ispita',
            'type' => 'relationship',
            'attributes' => 'naziv',
        ],
        'uspeh_id' => [
            'name' => 'uspeh',
            'label' => 'Uspeh',
            'type' => 'relationship',
        ],
        'rok',
        'datum_polaganja' => [
            'name' => 'datum_polaganja',
            'label' => 'Datum polaganja',
            'type' => 'date',
            'format' => 'DD.MM.Y.',
        ],
        'strucni_rad' => [
            'name' => 'strucni_rad',
            'label' => 'Stručni rad',
            'type' => 'select_from_array',
            'options' => [
                0 => 'Rad izrađuje sa mentorom',
                1 => 'Ima stručni rad',
            ]
        ],
        'barcode' => [
            'name' => 'barcode',
        ],
        'datum_prijema' => [
            'name' => 'datum_prijema',
            'label' => 'Datum prijema',
            'type' => 'date',
            'format' => 'DD.MM.Y.',
        ],
        'app_korisnik_id' => [
            'name' => 'user',
            'label' => 'Zaveo korisnik',
            'type' => 'relationship',
            'attributes' => 'id',
        ],
        'zavodni_broj' => [
            'name' => 'zavodni_broj',
            'label' => 'Zavodni broj',
        ],
        'tema' => [
            'name' => 'tema',
        ],
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažurirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
        ],
    ],
        $column_definition_array = [
        'id' => [
            'name' => 'id',
            'label' => 'Broj prijave',
        ],
        'status_prijave' => [
            'name' => 'status',
            'label' => 'Status',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'osoba_id' => ['name' => 'osoba_id', 'label' => 'jmbg'],
        'osoba' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime',
            'attribute' => 'full_name',
        ],
        'documents' => [
            'name' => 'documents',
            'type' => 'relationship',
            'ajax' => TRUE,
            'attribute' => 'category_type_name_status_registry_date',
        ],
        'vrsta_posla_id' => [
            'name' => 'vrstaPosla',
            'label' => 'Vrsta posla',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_oblast_id' => [
            'name' => 'regOblast',
            'label' => 'Oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_pod_oblast_id' => [
            'name' => 'regPodOblast',
            'label' => 'Uža oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'zvanje_id' => [
            'name' => 'zvanje',
            'label' => 'Zvanje',
            'type' => 'relationship',
            'attribute' => 'skrnaziv',
        ],
        'si_vrsta_id' => [
            'name' => 'siVrsta',
            'label' => 'Vrsta ispita',
            'type' => 'relationship',
            'attributes' => 'naziv',
        ],
        'uspeh_id' => [
            'name' => 'uspeh',
            'label' => 'Uspeh',
            'type' => 'relationship',
        ],
        'rok',
        'datum_polaganja' => [
            'name' => 'datum_polaganja',
            'label' => 'Datum polaganja',
            'type' => 'date',
            'format' => 'DD.MM.Y.',
        ],
        'strucni_rad' => [
            'name' => 'strucni_rad',
            'label' => 'Stručni rad',
            'type' => 'select_from_array',
            'options' => [
                0 => 'Rad izrađuje sa mentorom',
                1 => 'Ima stručni rad',
            ]
        ],
        /*'barcode' => [
            'name' => 'barcode',
        ],
        'datum_prijema' => [
            'name' => 'datum_prijema',
            'label' => 'Datum prijema',
            'type' => 'date',
            'format' => 'DD.MM.Y.',
        ],
        'app_korisnik_id' => [
            'name' => 'user',
            'label' => 'Zaveo korisnik',
            'type' => 'relationship',
            'attributes' => 'id',
        ],
        'zavodni_broj' => [
            'name' => 'zavodni_broj',
            'label' => 'Zavodni broj',
        ],*/
        'tema' => [
            'name' => 'tema',
        ],
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažurirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
        ],
    ],

        $field_definition_array = [
        'id' => [
            'name' => 'id',
            'label' => 'Broj prijave',
            'attributes' => ['readonly' => 'readonly',],
            'wrapper' => ['class' => 'col-md-6 my-3',],
        ],
        'barcode' => [
            'name' => 'barcode',
            'wrapper' => ['class' => 'col-md-6 my-3',],
        ],
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
            'ajax' => TRUE,
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'zvanje_id' => [
            'name' => 'zvanje',
            'label' => 'Zvanje',
            'type' => 'relationship',
            'attribute' => 'naziv',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'si_vrsta_id' => [
            'name' => 'siVrsta',
            'label' => 'Vrsta ispita',
            'type' => 'relationship',
            'attribute' => 'naziv',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'status_prijave' => [
            'name' => 'status_prijave',
            'label' => 'Status',
            'type' => 'select2',
            'entity' => 'status',
            'attribute' => 'naziv',
            'wrapper' => ['class' => 'col-md-6 my-3',],
        ],
        'uspeh_id' => [
            'name' => 'uspeh',
            'label' => 'Uspeh',
            'type' => 'relationship',
            'wrapper' => ['class' => 'col-md-6 my-3',],
        ],
        'rok',
        'datum_polaganja' => [
            'name' => 'datum_polaganja',
            'label' => 'Datum polaganja',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
//                'language' => 'sr-latn'
            ],
        ],
        'vrsta_posla_id' => [
            'name' => 'vrstaPosla',
            'label' => 'Vrsta posla',
            'attribute' => 'naziv',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'reg_oblast_id' => [
            'name' => 'regOblast',
            'label' => 'Oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'reg_pod_oblast_id' => [
            'name' => 'regPodOblast',
            'label' => 'Uža oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'strucni_rad' => [
            'name' => 'strucni_rad',
            'label' => 'Stručni rad',
            'type' => 'select_from_array',
            'options' => [
                0 => 'Rad izrađuje sa mentorom',
                1 => 'Ima stručni rad',
            ],
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'tema' => [
            'name' => 'tema',
            'wrapper' => ['class' => 'col-md-8 my-3',],
        ],
        // todo: omoguciti posle update-a na backpack 5 pro
//        'documents' => [
//            'name' => 'documents',
//            'type' => 'relationship',
//            'ajax' => TRUE,
//            'attribute' => 'category_type_name_status_registry_date',
//            'attributes' => ['disabled' => 'disabled',],
//        ],
        'datum_prijema' => [
            'name' => 'datum_prijema',
            'label' => 'Datum prijema',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
//                'language' => 'sr-latn'
            ],
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'app_korisnik_id' => [
            'name' => 'user',
            'label' => 'Zaveo korisnik',
            'type' => 'relationship',
            'attribute' => 'name',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'zavodni_broj' => [
            'name' => 'zavodni_broj',
            'label' => 'Zavodni broj',
            'wrapper' => ['class' => 'col-md-4 my-3',],
        ],
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreirana',
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD.MM.YYYY HH:mm',
            ],
            'allows_null' => TRUE,
            'attributes' => ['readonly' => 'readonly'],
            'wrapper' => ['class' => 'col-md-6 my-3',],
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažurirana',
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD.MM.YYYY HH:mm',
            ],
            'allows_null' => TRUE,
            'attributes' => ['readonly' => 'readonly'],
            'wrapper' => ['class' => 'col-md-6 my-3',],
        ],
    ],
        $allowRegister = FALSE;

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\SiPrijava');

        $segment = \Request::segment(2);

        switch ($segment) {
            case 'si':
                CRUD::setRoute(config('backpack.base.route_prefix') . '/si');
                CRUD::setEntityNameStrings('siprijava', 'Prijave Stručni ispit');

                $this->crud->denyAccess(['documentcancelation', 'registerrequestbulk']);

//                CRUD::addClause('where', 'request_category_id', 7);
//                $this->requestCategoryType = 1;
//                $this->requestCategory = [10];
                break;
            case 'registerrequestsi':
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestsi');
                CRUD::setEntityNameStrings('siprijava', 'Zavodjenje Prijave SI');

                $this->allowRegister = TRUE;

                $this->crud->denyAccess(['fileUpload', 'documentcancelation']);


                // CRUD::addClause('whereIn', 'request_category_id', [1, 2]);
                // $this->requestCategoryType = 1;
                // $this->requestCategory = [1, 2];
                break;
        }


        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }

        $this->crud->addButtonFromView('line', 'siPrijavaDocuments', 'siPrijavaDocuments', 'end');


        CRUD::enableExportButtons();
        CRUD::enableDetailsRow();

        CRUD::set('show.setFromDb', FALSE);

    }

    protected function setupListOperation()
    {
        if (backpack_user()->hasRole('admin')) {
            $this->crud->addColumns($this->column_definition_array_admin);
        } else {
            $this->crud->addColumns($this->column_definition_array);
        }
        $this->crud->removeColumns(['tema', 'strucni_rad', 'user', 'barcode', 'created_at', 'updated_at', 'zahtev', 'datum_prijema', 'zavodni_broj']);


        $this->crud->setColumnDetails('id', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
//                    dd($column);$column
                    $query->whereIn('id', $searchTermArray)->orderBy('id');
                } else {
                    $query->orWhere('id', 'ilike', $searchTerm . '%');
                }
            }
        ]);

        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query
                        ->orWhereHas('osoba.licence', function ($q) use ($column, $searchTerm) {
                            $q
                                ->where('id', 'ilike', $searchTerm . '%');
                        })
                        ->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                            $q
                                ->where('id', 'ilike', $searchTerm . '%')
                                ->orWhere('ime', 'ilike', $searchTerm . '%')
                                ->orWhere('prezime', 'ilike', $searchTerm . '%');
                        });

                }
            }
        ]);

        $this->crud->setColumnDetails('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($related_key) {
                        case REQUEST_CREATED:
                        case REQUEST_SUBMITED:
                        default:
                            return 'btn btn-sm btn-outline-secondary';
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info';
                        case REQUEST_FINISHED:
                            return 'btn btn-sm btn-outline-success';
                        case REQUEST_CANCELED:
                        case REQUEST_PROBLEM:
                            return 'btn btn-sm btn-outline-danger';
                        case PRIJAVA_OTKLJUCANA:
                            return 'btn btn-sm btn-outline-warning';
                    }
                },
            ]
        ]);

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => function ($crud, $column, $entry, $related_key) {
                    $document = Document::find($related_key);
                    switch ($document->status_id) {
                        case DOCUMENT_CREATED:
                        default:
                            return 'btn btn-sm btn-outline-secondary text-dark';
                        case DOCUMENT_REGISTERED:
                            return 'btn btn-sm btn-outline-success text-dark';
                        case DOCUMENT_CANCELED:
                            return 'btn btn-sm btn-outline-danger text-dark';
                    }
                },
                'target' => '_blank',
            ],
        ]);


        /*
         *  Filter definition section
         */
        // select2_multiple filter
        $this->crud->addFilter([
            'name' => 'rok_filter',
            'type' => 'select2_multiple',
            'label' => 'Rok'
        ], function () {
            return SiPrijava::select('rok')->distinct('rok')->pluck('rok', 'rok')->toArray();

        }, function ($values) { // if the filter is active

            $this->crud->addClause('whereIn', 'rok', json_decode($values));
        });


        // select2_multiple filter
        $this->crud->addFilter([
            'name' => 'vrsta_posla',
            'type' => 'select2_multiple',
            'label' => 'Vrsta posla'
        ], function () {
            return VrstaPosla::all()->pluck('naziv', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'vrsta_posla_id', json_decode($values));
        });

        $this->crud->addFilter([
            'name' => 'oblast',
            'type' => 'select2_multiple',
            'label' => 'Stručna oblast'
        ], function () {
            return RegOblast::all()->pluck('naziv', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'reg_oblast_id', json_decode($values));
        });

        $this->crud->addFilter([
            'name' => 'podoblast',
            'type' => 'select2_multiple',
            'label' => 'Uža stručna oblast'
        ], function () {
            return RegPodoblast::all()->pluck('naziv', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'reg_pod_oblast_id', json_decode($values));
        });

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], function () {
            return SiPrijava::existingStatuses();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_prijave', $value);
            });

        $this->crud->addFilter([
            'name' => 'vrsta_ispita',
            'type' => 'dropdown',
            'label' => 'Vrsta ispita'
        ], function () {
            return SiVrsta::all()->pluck('naziv', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'si_vrsta_id', $value);
            });

        $this->crud->addFilter([
            'name' => 'sekcija',
            'type' => 'dropdown',
            'label' => 'Struka'
        ], function () {
            return Sekcija::orderBy('id')->pluck('naziv', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'zvanje', function ($q) use ($value) {
                    $q->where('zvanje_grupa_id', $value);
                });
            });

        // daterange filter
        /*        $this->crud->addFilter([
                    'type' => 'date_range',
                    'name' => 'from_to',
                    'label' => 'Rok za prijavu'
                ],
                    FALSE,
                    function ($value) { // if the filter is active, apply these constraints
                        $dates = json_decode($value);
                        $this->crud->addClause('where', 'datum_prijema', '>=', date('Y-m-d', strtotime($dates->from)));
                        $this->crud->addClause('where', 'datum_prijema', '<=', date('Y-m-d', strtotime($dates->to)));
                    });*/

        if ($this->allowRegister) {
            $this->crud->addFilter([
                'type' => 'simple',
                'name' => 'active',
                'label' => 'Za zavođenje'
            ],
                FALSE,
                function () { // if the filter is active
                    $this->crud->addClause('where', 'status_prijave', REQUEST_SUBMITED); // apply the "active" eloquent scope
                });
        }

        if (backpack_user()->hasRole('admin') and !$this->allowRegister) {
            $this->crud->addFilter([
                'type' => 'simple',
                'name' => 'zl',
                'label' => 'Nema zahtev'
            ],
                FALSE,
                function () { // if the filter is active
                    $this->crud->addClause('whereDoesntHave', 'zahtevLicenca'); // apply the "active" eloquent scope
                });
        }

        if (backpack_user()->hasRole('admin') and !$this->allowRegister) {
            $this->crud->addFilter([
                'type' => 'simple',
                'name' => 'licenciran',
                'label' => 'Licencirani'
            ],
                FALSE,
                function () { // if the filter is active
                    $this->crud->addClause('whereHas', 'osoba', function ($q) {
                        $q->whereHas('licence');
                    }); // apply the "active" eloquent scope
                });
        }
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', FALSE);

        $si_prijava = $this->crud->getEntry(\Request::segment(3));
        $osoba = $si_prijava->osoba;

        Widget::add([
            'type' => 'view',
            'view' => 'vendor.backpack.base.widgets.view-kandidat',
            'osoba' => $osoba,
        ]);

        if ($si_prijava->reference->isNotEmpty()) {
            Widget::add([
                'type' => 'view-reference',
                'wrapper' => ['class' => 'col-md-12 p-0'], // optional
                'content' => [
                    'header' => 'REFERENCE',
                    'body' => $si_prijava->reference,
                ]
            ]);
        }

        if (backpack_user()->hasRole(['admin'])) {
            $this->crud->setColumns($this->column_definition_array_admin);
        } else {
            $this->crud->setColumns($this->column_definition_array);
        }

        CRUD::column('zahtev')
            ->type('model_function')
            ->function_name('getZahtevLicencaStatusAttribute')
            ->label('Zahtev za licencu')
            ->before('reference');

        /*        CRUD::column('reference')
                    ->attribute('data_reference_to_array')
                    ->limit(500)
                    ->before('created_at')
                    ->wrapper([
                        'href' => function ($crud, $column, $entry, $related_key) {
                            $odgovorno_lice_licenca = Referenca::find($related_key)->odgovorno_lice_licenca_id;
                            $odgovorno_lice = Licenca::find($odgovorno_lice_licenca)->osoba;
                            return backpack_url('osoba/' . $odgovorno_lice . '/show');
                        },
                        'class' => 'btn btn-sm btn-outline-info mr-1',
                        'target' => '_blank',
                    ]);*/

        CRUD::column('documents')
            ->type('relationship')
            ->attribute('category_type_name_status_registry_number_registry_date')
            ->limit(500)
            ->before('status');

        $this->crud->modifyColumn('vrstaPosla', ['limit' => 500,]);
        $this->crud->modifyColumn('regOblast', ['limit' => 500,]);
        $this->crud->modifyColumn('regPodOblast', ['limit' => 500,]);

        $this->crud->setColumnDetails('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
                'target' => '_blank',
            ],
        ]);

        $this->crud->setColumnDetails('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($related_key) {
                        case REQUEST_CREATED:
                        case REQUEST_SUBMITED:
                        default:
                            return 'btn btn-sm btn-outline-secondary';
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info';
                        case REQUEST_FINISHED:
                            return 'btn btn-sm btn-outline-success';
                        case REQUEST_CANCELED:
                        case REQUEST_PROBLEM:
                            return 'btn btn-sm btn-outline-danger';
                        case PRIJAVA_OTKLJUCANA:
                            return 'btn btn-sm btn-outline-warning';
                    }
                },
            ]
        ]);

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => function ($crud, $column, $entry, $related_key) {
                    $document = Document::find($related_key);
                    switch ($document->status_id) {
                        case DOCUMENT_CREATED:
                        default:
                            return 'btn btn-sm btn-outline-secondary text-dark';
                        case DOCUMENT_REGISTERED:
                            return 'btn btn-sm btn-outline-success text-dark';
                        case DOCUMENT_CANCELED:
                            return 'btn btn-sm btn-outline-danger text-dark';
                    }
                },
                'target' => '_blank',
            ],
        ]);

        $this->crud->setColumnDetails('zahtev', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    if (!is_null($entry->zahtevLicenca)) {
                        $zl = $entry->zahtevLicenca->id;
                        return backpack_url('zahtevlicenca/' . $zl . '/show');
                    }
                },
                'target' => '_blank',
                'class' => function ($crud, $column, $entry, $related_key) {
                    $zahtev = ZahtevLicenca::where('si_prijava_id', $entry->id)->first();
                    if (!is_null($zahtev)) {
                        switch ($zahtev->status) {
                            case REQUEST_CREATED:
                            case REQUEST_SUBMITED:
                            case REQUEST_IN_PROGRESS:
                            default:
                                return 'btn btn-sm btn-outline-secondary';
                            case REQUEST_FINISHED:
                                return 'btn btn-sm btn-outline-success';
                            case REQUEST_CANCELED:
                            case REQUEST_PROBLEM:
                                return 'btn btn-sm btn-outline-danger';
                            case PRIJAVA_OTKLJUCANA:
                            case ZAHTEV_LICENCA_ZAKLJUCAN:
                                return 'btn btn-sm btn-outline-warning';
                        }
                    } else {
                        return 'text-danger';
                    }
                },
            ],
        ]);

    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(SiPrijavaRequest::class);

        $this->crud->addFields($this->field_definition_array);

        $this->crud->modifyField('status_prijave', [
            'options' => (function ($query) {
                return $query->where('log_status_grupa_id', REQUESTS)->get();
            }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function showDetailsRow($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id)->osoba;
        $this->data['crud'] = $this->crud;
        return view('crud::osoba_details_row', $this->data);
    }

    public function fetchDocuments()
    {
        return $this->fetch([
            'model' => \App\Models\Document::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                return $model->where('id', 'ilike', $searchTerm . '%');
            } // to filter the results that are returned
        ]);
    }
}
