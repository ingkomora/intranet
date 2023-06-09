<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\RegistarDelationOperation;
use App\Http\Controllers\Admin\Operations\UpdateZalbaStatusOperation;
use App\Http\Requests\RequestRequest;
use App\Models\Document;
use App\Models\RequestCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use UpdateZalbaStatusOperation;
    use RegistarDelationOperation;

    protected $fields_definition_operation_array = [
        'id' => [
            'name' => 'id',
//            'type' =>'hidden',
            'attributes' => ['readonly' => 'readonly']
        ],
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'ajax' => TRUE,
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
            'attributes' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
        ],
        'status' => [
            'name' => 'status_id',
            'type' => 'select_from_array',
            'options' => [
                41 => 'Žalba (41)',
                43 => 'Poništen (43)',
//                50 => 'Kreiran (50)',
//                51 => 'Podnet (51)',
                52 => 'U obradi (52)',
                53 => 'Završen (53)',
                54 => 'Otkazan (54)',
//                58 => 'Storniran (58)',
                99 => 'Žalba u MGSI (99)',
                100 => 'Odustao od žalbe (100)',
                200 => 'Oglasna tabla (200)',
            ],
        ]
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {

        CRUD::setModel(\App\Models\Request::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/request');
        CRUD::setEntityNameStrings('zahtev', 'zahtevi');

        $this->crud->denyAccess(['updatezalbastatus']);
        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update', 'updatezalbastatus']);
        }
        if (backpack_user()->hasRole('sluzba_pravna')) {
            $this->crud->allowAccess(['update']);
        }
        if (backpack_user()->hasPermissionTo('azuriraj status zalbe')) {
            $this->crud->allowAccess(['updatezalbastatus']);
        }
        CRUD::set('show.setFromDb', FALSE);

        $this->crud->enableDetailsRow();
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
        $this->crud->addColumns([
            'id',
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'osoba_id',
            'osoba' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime',
                'attribute' => 'full_name',
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
            'requestable' => [
                'name' => 'requestable',
                'type' => 'relationship',
                'attribute' => 'id',
            ],
            'documents' => [
                'name' => 'documents',
                'type' => 'relationship',
                'attribute' => 'category_type_name_status_registry_date',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],
        ]);

        $this->crud->modifyColumn('id', [
            'name' => 'id',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
//                    dd($searchTerm);
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
                    $query->whereIn('id', $searchTermArray);
                } else {
                    $query->orWhere('id', 'ilike', $searchTerm . '%');
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

        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
//                    dd($column);
                    $query->orWhereHas('osoba', function ($q) use ($searchTermArray) {
                        $q->whereIn('id', $searchTermArray)
                            ->orderBy('id');
                    });
                } else if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('osoba.licence', function ($q) use ($column, $searchTerm) {
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

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'clan',
            'label' => 'Članstvo'
        ], function () {
            return [
                -1 => 'Funkcioner',
                0 => 'Nije član',
                1 => 'Član',
                100 => 'Članstvo na čekanju',
                10 => 'Priprema se brisanje iz članstva'
            ];
        }, function ($value) {
            $this->crud->addClause('whereHas', 'osoba', function ($q) use ($value) {
                $q->where('clan', $value);
            });
        });

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'platili',
            'label' => 'Platili članarinu'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereHas', 'clanarine', function ($query) {
                    $query->where('rokzanaplatu', '>=', 'now()');
                }); // apply the "active" eloquent scope
            });
        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'nisuplatili',
            'label' => 'Nisu platili članarinu'
        ],
            FALSE,
            function () {
                // if the filter is active
                // apply the "active" eloquent scope
                $this->crud->addClause('whereHas', 'clanarine', function ($query) {
                    $query->where('rokzanaplatu', '<', 'now()')
                        ->whereRaw('iznoszanaplatu = iznosuplate + pretplata');
                });
                $this->crud->addClause('whereDoesntHave', 'clanarine', function ($query) {
                    $query->where('rokzanaplatu', '>=', 'now()');
                });

            });

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], function () {
            return $this->crud->getModel()::existingStatuses();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_id', $value);
            }
        );

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'requestCategory',
            'type' => 'dropdown',
            'label' => 'Kategorija zahteva'
        ], function () {
            return RequestCategory::all()->pluck('name', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'request_category_id', $value);
            }
        );

        $this->crud->addFilter([
            'name' => 'documents',
            'type' => 'simple',
            'label' => 'Dokumenta'
        ],
            FALSE,
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'documents', function ($q) {
//                        $q->whereNotNull('registry_number');
//                        $q->whereNull('registry_number');
                });
            });

        if (backpack_user()->hasRole('admin')) {

            $this->crud->addFilter([
                'name' => 'hasNotGetLicence',
                'type' => 'simple',
                'label' => 'Nije preuzeo licencu'
            ],
                FALSE,
                function ($value) { // if the filter is active
                    $this->crud->addClause('whereHas', 'osoba', function ($q) {
                        $q->whereDoesntHave('licence', function ($q) {
                            $q->where('preuzeta', 1);
                        });
                    });
                });

            // dropdown filter
            $this->crud->addFilter([
                'name' => 'clanarina',
                'type' => 'select2_multiple',
                'label' => 'Plaćena članarina za godinu:'
            ],
                function () {
                    return \DB::table('requests')
                        ->select('id', 'note')
                        ->distinct('note')
                        ->orderBy('note', 'DESC')
                        ->pluck('note', 'note')
                        ->toArray();
                },
                function ($values) { // if the filter is active
                    $this->crud->addClause('whereIn', 'note', json_decode($values));
                });

        }
    }

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->addColumns([
            'id',
            'osoba_id',
            'osoba' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime',
                'attribute' => 'full_name',
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
                'limit' => 500,
            ],
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'requestable' => [
                'name' => 'requestable',
                'type' => 'relationship',
                'attribute' => 'id',
            ],
            'documents' => [
                'name' => 'documents',
                'type' => 'relationship',
                'attribute' => 'category_type_name_status_registry_number_registry_date',
                'limit' => 500,
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
                'limit' => 500
            ],
        ]);

        $this->crud->setColumnDetails('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'target' => '_blank',
                'class' => 'btn btn-sm btn-outline-info',
            ]
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
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(RequestRequest::class);

        $this->crud->addFields([
            /*'id' =>[
                'name'=>'id',
                'attributes' => ['disabled' => 'disabled', 'readonly' => 'readonly'],
            ],*/
            'osoba_id' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime (licence)',
                'attribute' => 'ime_prezime_licence',
                'ajax' => TRUE
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
            'status' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],

        ]);


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

        $request = $this->crud->getCurrentEntry();

//        if($request->status->naziv);
//        dd($request->status->naziv);
        $this->crud->addField([
            'name' => 'created_at',
            'label' => 'Kreiran',
            'attributes' => ['disabled' => 'disabled'],
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD.MM.YYYY. HH:mm:ss',
                'language' => 'sr_latin'
            ],
        ]);

        $this->crud->addField([
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'attributes' => ['disabled' => 'disabled'],
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD.MM.YYYY. HH:mm:ss',
                'language' => 'sr_latin'
            ],
        ]);

        //      TODO ako zahtev ima status koji samo neko sme da menja onda je disabled (univerzalno a ne samo pravnici)
        if (!in_array($this->crud->getCurrentEntry()->status_id, [ZALBA, ZALBA_MGSI, PONISTEN])) {
            $this->crud->modifyField('status', [
                'options' => (function ($query) {
                    return $query->orderBy('id')->where('log_status_grupa_id', REQUESTS)->get(); // samo grupa statusa "Zahtevi"
                }),
            ]);
        } else {
            $this->crud->modifyField('status', [
                /*'options' => (function ($query) {
                    return $query->orderBy('id')->where('log_status_grupa_id', REQUESTS)->get(); // samo grupa statusa "Zahtevi"
                }),*/
                'type' => 'relationship',
//                'attribute'=>'naziv',
                'attributes' => ['disabled' => 'disabled']
            ]);
        }
    }

    protected function showDetailsRow($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id)->osoba;
//        dd($this->data['entry']);
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_clanarina_details_row', $this->data);
    }

    public function fetchOsoba()
    {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
            'searchable_attributes' => [],
//            'searchable_attributes' => ['id', 'ime', 'prezime'],
//            'routeSegment' => 'mb', // falls back to the key of this array if not specified ("category")
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%')
//                                ->orWhere('ime', 'ilike', $searchTerm[1] . '%')
//                                ->orWhere('prezime', 'ilike', $searchTerm[0] . '%')
                        ->whereHas('licence', function ($query) use ($model) {
                            $query->where('status', '<>', 'D');
                        });
                } else {
                    return $model->whereHas('licence', function ($q) use ($searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    })->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%')
                        ->orWhere('id', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);
    }


}
