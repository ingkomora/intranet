<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegisterRequestRequest;
use App\Models\RequestCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestMembershipCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    use Operations\MembershipApprovingOperation;


    protected
        $columns_definition_array = [
        'id',
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
        ],
        'request_category_id' => [
            'name' => 'requestCategory',
            'type' => 'relationship',
            'label' => 'Kategorija zahteva',
        ],
        'status_id' => [
            'name' => 'status',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'documents' => [
            'name' => 'documents',
            'type' => 'relationship',
            'label' => 'Dokumenta',
            'attribute' => 'category_type_name_status_registry_number',
        ],
    ],
        $fields_definition_array = [
        'id' => [
            'name' => 'id',
            'attributes' => ['readonly' => 'readonly']
        ],
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'ajax' => TRUE,
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
        ],
        'request_category_id' => [
            'name' => 'requestCategory',
            'type' => 'relationship',
            'label' => 'Kategorija zahteva',
        ],
        'status_id' => [
            'name' => 'status',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'note' => [
            'name' => 'note',
            'label' => 'Napomena',
        ],
        'requestable' => [
            'name' => 'requestable',
//            'label' => 'requestable_id',
            'type' => 'select2',
            'model' => '\App\Models\Request',
            'attribute' => 'id'
        ],

        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'attributes' => ['disabled' => 'disabled'],
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD.MM.YYYY. HH:mm:ss',
                'language' => 'sr_latin'
            ],
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'attributes' => ['disabled' => 'disabled'],
            'type' => 'datetime_picker',
            'datetime_picker_options' => [
                'format' => 'DD.MM.YYYY. HH:mm:ss',
                'language' => 'sr_latin'
            ],
        ],
    ],
        $fields_definition_operation_array = [
        'id' => [
            'name' => 'id',
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
        'odluka_br' => [
            'name' => 'odluka_br',
            'label' => 'Broj odluke',
            'type' => 'text',
            'hint' => 'Broj odluke o prijemu u članstvo',
        ],
        'odluka_datum' => [
            'name' => 'odluka_datum',
            'label' => 'Datum odluke',
            'hint' => 'Datum odluke o prijemu u članstvo',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr-latin',
            ]

        ],
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Request::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/requestmembership');
        CRUD::setEntityNameStrings('zahtev', 'zahtevi');


        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update', 'membershipapproving']);
        }
        if (backpack_user()->hasPermissionTo('odobri clanstvo')) {
            $this->crud->allowAccess(['membershipapproving']);
        }

        $this->crud->set('show.setFromDb', FALSE);

        $this->crud->addClause('whereHas', 'requestCategory', function ($q) {
            $q->where('request_category_type_id', 1);
        }); // samo zahtevi tipa kategorije clanstvo

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
        $this->crud->setColumns($this->columns_definition_array);

        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
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


        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-outline-success px-2 py-0 rounded';
                        default:
                    }
                }
            ]
        ]);

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
                'target' => '_blank',
            ]
        ]);

        $this->crud->addFilter([
            'name' => 'request_category',
            'type' => 'dropdown',
            'label' => 'Kategorija zahteva'
        ], function () {
            return RequestCategory::orderBy('id')->get()->pluck('name', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'request_category_id', $value);
            });

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'toprocess',
            'label' => 'Za obradu'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('where', 'status_id', REQUEST_IN_PROGRESS); // apply the "active" eloquent scope
            });

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], function () {
            return $this->crud->getModel()::existingStatuses(1); // samo statusi zahteva za prijem u clanstvo
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_id', $value);
            });

    }

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-show-entries
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->setColumns($this->columns_definition_array);

//        na definisane colone dodajemo jos ove
        $this->crud->addColumns([
            'requestable' => [
                'name' => 'requestable',
//                'label' => 'Broj iz modela',
                'type' => 'relationship',
            ],
            'documents' => [
                'name' => 'documents',
                'type' => 'relationship',
                'label' => 'Dokumenta',
                'attribute' => 'category_type_name_status_registry_number',
            ],

        ]);

        $this->crud->modifyColumn('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info px-2',
            ]
        ]);

        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-outline-success px-2 py-0 rounded';
                        default:
                    }
                }
            ]
        ]);

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
                'target' => '_blank',
            ]
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
        CRUD::setValidation(RegisterRequestRequest::class);

        $this->crud->addFields($this->fields_definition_array);
        $this->crud->removeFields(['id', 'requestCategory', 'created_at', 'updated_at']);

//        nece modifyField, zato sam ga obrisao pa ponovo dodao sa novom definicijom
        $this->crud->addField([
            'name' => 'requestCategory',
            'type' => 'relationship',
            'options' => (function ($query) {
                return $query->where('request_category_type_id', 1)->get(); // samo zahtevi tipa kategorije clanstvo
            }),
        ])->afterField('osoba');

        $this->crud->modifyField('status', [
            'options' => (function ($query) {
                return $query->orderBy('id')->where('log_status_grupa_id', 11)->get(); // samo grupa statusa "Zahtevi"
            }),
        ]);

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
