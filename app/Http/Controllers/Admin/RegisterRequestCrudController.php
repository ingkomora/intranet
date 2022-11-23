<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegisterRequestRequest;
use App\Http\Requests\RequestRequest;
use App\Http\Requests\TagRequest;
use App\Models\Document;
use App\Models\DocumentCategoryType;
use App\Models\DocumentCategory;
use App\Models\Licenca;
use App\Models\Request;
use App\Models\RequestCategory;
use App\Models\Status;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;

/**
 * Class RequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RegisterRequestCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }

    use Operations\RegisterRequestBulkOperation;

    protected $request_category_type;
    protected $request_category;
    protected $requestable_model;
    protected $status_col_name = 'status_id';

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Request::class);
        $type = \Request::segment(2);
        $allowCreate = FALSE;

        switch ($type) {
            case 'registerrequestpromenapodataka':
                //ZA SADA SE ZAHTEVI NE ZAVODE DOK SE NE PREPRAVI APLIKACIJA ZA PROMENU PODATAKA
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za promenu podataka');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestpromenapodataka');
                CRUD::addClause('where', 'request_category_id', 10);
                $this->request_category_type = 1;
                $this->request_category = [10];
                $this->requestable_model = '\App\Models\Request';
                $this->status_col_name = 'obradjen';
                break;
            case 'registerrequestclanstvo':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za prijem i prekid članstva');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestclanstvo');
                CRUD::addClause('whereIn', 'request_category_id', [1, 2, 12, 13]);
                $this->request_category_type = 1;
                $this->request_category = [1, 2, 12, 13];
                $this->requestable_model = '\App\Models\Membership';
                $allowCreate = TRUE;
                break;
            case 'registerrequestmirovanjeclanstva':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za mirovanje');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestmirovanjeclanstva');
                CRUD::addClause('whereIn', 'request_category_id', [4, 5]);
                $this->request_category_type = 1;
                $this->request_category = [4, 5];
                $this->requestable_model = '\App\Models\Request';
                $allowCreate = TRUE;
                break;
            case 'registerrequestregistar':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje uverenja o upisu u registar');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestregistar');
                CRUD::addClause('whereIn', 'request_category_id', [8, 9]);
                $this->request_category_type = 2;
                $this->request_category = [8, 9];
                $this->requestable_model = '\App\Models\Request';
                $allowCreate = TRUE;
                break;
            case 'registerrequestsfl':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje svečane forme licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestsfl');
                CRUD::addClause('where', 'request_category_id', 3);
                $this->request_category_type = 2;
                $this->request_category = [3];
                $allowCreate = TRUE;
                $this->requestable_model = '\App\Models\ZahtevLicenca';
                break;
//            case 'registerrequestresenjeclanstvo':
//                CRUD::setEntityNameStrings('zahtev', 'Rešenja o prestanku i brisanju iz članstva');
//                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestresenjeclanstvo');
//                CRUD::addClause('whereIn', 'request_category_id', [1, 2]);//todo document_category_id
////                CRUD::addClause('whereHas', 'documents');
//                $this->request_category_type = 1;
//                $this->request_category = [1, 2];
//                $this->requestable_model = '\App\Models\Request';
//                break;
            case 'registerrequestregistrydatadelete':
                CRUD::setEntityNameStrings('zahtev', 'Zahtevi za brisanje podataka upisanih u Registar');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestregistrydatadelete');
                CRUD::addClause('whereIn', 'request_category_id', [11]);
                $this->request_category_type = 2;
                $this->request_category = [11];
                $this->requestable_model = '\App\Models\Request';
                break;
            case 'registerrequestregistrydataupdate':
                CRUD::setEntityNameStrings('zahtev', 'Zahtevi za promenu podataka upisanih u Registar');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestregistrydataupdate');
                CRUD::addClause('whereIn', 'request_category_id', [9]);
                $this->request_category_type = 2;
                $this->request_category = [9];
                $this->requestable_model = '\App\Models\Request';
                break;
            case 'registerrequestregistryuverenje':
                CRUD::setEntityNameStrings('zahtev', 'Zahtevi za izdavanje uverenja o podacima upisanim u Registar');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestregistryuverenje');
                CRUD::addClause('whereIn', 'request_category_id', [8]);
                $this->request_category_type = 2;
                $this->request_category = [8];
                $this->requestable_model = '\App\Models\Request';
                break;
        }

        $this->crud->set('show.setFromDb', FALSE);


        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }

        if (backpack_user()->hasPermissionTo('zavedi') and $allowCreate) {
            $this->crud->allowAccess(['create']);
        }

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
            'osoba_id' => [
                'name' => 'osoba_id',
                'label' => 'jmbg',
            ],
            'osoba' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime',
                'attribute' => 'full_name',
            ],
            'documents' => [
                'name' => 'documents',
                'type' => 'relationship',
                'attribute' => 'category_type_name_status_registry_number',
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
        ]);

        if (\Request::segment(2) == 'registerrequestsfl') {
            CRUD::column('requestable')->attribute('id')->label('Licenca')->afterColumn('requestCategory');
        }

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

        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->{$this->status_col_name}) {
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info mr-1';
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
            ]
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
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], function () {
            return $this->crud->getModel()::existingStatuses();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_id', $value);
            });

        $this->crud->addFilter([
            'name' => 'requestCategory',
            'type' => 'dropdown',
            'label' => 'Kategorija'
        ], function () {
            return RequestCategory::whereIn('id', $this->request_category)->pluck('name', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'request_category_id', $value);
            });

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Za zavođenje'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereNotIn', 'status_id', [REQUEST_CREATED, REQUEST_CANCELED]); // apply the "active" eloquent scope
            });

        /*$this->crud->addFilter([
            'type' => 'simple',
            'name' => 'documents',
            'label' => 'Ima dokumente'
        ],
            FALSE,
            function () { // if the filter is active
                CRUD::addClause('whereHas', 'documents', function ($q) {
                    $q->whereIn('document_category_id', [12, 13]);
                });
            });*/
    }

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-show-entries
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->addColumns([
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
                'attribute' => 'category_type_name_status_registry_number',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],
            'created_at' => [
                'name' => 'created_at',
                'label' => 'Kreiran',
                'type' => 'datetime',

            ],
            'updated_at' => [
                'name' => 'updated_at',
                'label' => 'Ažuriran',
                'type' => 'datetime',

            ],

        ]);

        if (\Request::segment(2) == 'registerrequestsfl') {
            CRUD::addColumn('licenca')->afterColumn('requestCategory');
        }

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
            ]
        ]);

        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info mr-1';
                        default:
                    }
                }
            ]
        ]);

        $this->crud->setColumnDetails('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info',
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

        $this->crud->addFields([
//            'id',
            'osoba_id' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime',
                'hint' => 'Osobu možete tražiti po imenu i/ili prezimenu, jmbg ili broju licence',
                'attribute' => 'ime_prezime_licence',
                'ajax' => TRUE
            ],
            /*'documents' => [
                'name' => 'documents',
                'type' => 'relationship',
                'attribute' => 'category_type_name_status_registry_number',
                'ajax' => TRUE,
            ],*/
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
                'default' => REQUEST_SUBMITED
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],
        ]);

        // 100000103
        if (in_array(3, $this->request_category)) {
            CRUD::field('requestable_type')->type('hidden')->default('App\Models\Licenca');
            CRUD::field('licenca')
                ->hint("Unesite broj licence na koju se zahtev za izradu svečane forme licence odnosi")
                ->before('status')
                ->attribute('id')
                ->ajax(TRUE);
        }

        $this->crud->modifyField('requestCategory', [
            'options' => (function ($query) {
                return $query->orderBy('id')->whereIn('id', $this->request_category)->get();
            }),
        ]);

        $this->crud->modifyField('status', [
            'options' => (function ($query) {
//                return $query->orderBy('id')->where('log_status_grupa_id', REQUESTS)->get(); // samo grupa statusa "Zahtevi"
                return $query->where('id', REQUEST_SUBMITED)->get();
            }),
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
    }

    protected function showDetailsRow($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id)->osoba;
//        dd($this->data['entry']);
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_clanarina_details_row', $this->data);
    }

    public function store()
    {
        DB::beginTransaction();

        $response = $this->traitStore();

        $licenca = Licenca::find($this->crud->getRequest()->input('licenca'));
        $request = Request::find($this->crud->entry->id);
        $request->requestable_id = $licenca->idn;

        if ($request->save()) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        return $response;
    }

    /*
     * Fetch operations
     * start
     */
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

    public function fetchLicenca()
    {
        return $this->fetch([
            'model' => \App\Models\Licenca::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                return $model->where('id', 'ilike', $searchTerm . '%');
            }
        ]);
    }


}
