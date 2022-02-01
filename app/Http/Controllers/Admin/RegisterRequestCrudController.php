<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegisterRequestRequest;
use App\Http\Requests\RequestRequest;
use App\Models\Document;
use App\Models\DocumentCategoryType;
use App\Models\DocumentCategory;
use App\Models\Request;
use App\Models\Status;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

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

    use Operations\RegisterRequestBulkOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Request::class);
        $type = \Request::segment(2);

        switch ($type) {
            case 'registerrequestclanstvo':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za prijem i prekid članstva');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestclanstvo');
                CRUD::addClause('whereIn', 'request_category_id', [1, 2]);
                break;
            case 'registerrequestmirovanjeclanstva':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za mirovanje');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestmirovanjeclanstva');
                CRUD::addClause('whereIn', 'request_category_id', [4, 5]);
                break;
            /*case 'registerrequestlicence':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestlicence');
                CRUD::addClause('where', 'request_category_id', 8); //ubaciti kategorije za licence
                break;*/
            case 'registerrequestuverenjeregistar':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje uverenja o upisu u registar');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestuverenjeregistar');
                CRUD::addClause('where', 'request_category_id', 3);
                break;
            case 'registerrequestsfl':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje svečane forme licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestsfl');
                CRUD::addClause('where', 'request_category_id', 3);
                break;
            case 'registerrequestresenjeclanstvo':
                CRUD::setEntityNameStrings('zahtev', 'Rešenja o prestanku i brisanju iz članstva');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestresenjeclanstvo');
                CRUD::addClause('where', 'request_category_id', 2);//todo document_category_id
//                CRUD::addClause('whereHas', 'documents');
//todo document_category_id
                break;
        }

        $this->crud->set('show.setFromDb', FALSE);


        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }

        if (backpack_user()->hasPermissionTo('zavedi')) {
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


        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
//                        case OBRADJEN:
//                            return 'bg-success text-white px-2 rounded';
//                        case PROBLEM:
//                            return 'bg-danger text-white px-2 rounded';
//                        case OTKAZAN:
//                            return 'border border-danger text-white px-2 rounded';
//                        case REQUEST_CREATED:
//                            return 'border border-info text-white px-2 rounded';
                        case REQUEST_SUBMITED:
                            return 'text-success';
//                        case REQUEST_IN_PROGRESS:
//                            return 'border border-warning text-white px-2 rounded';
//                        case REQUEST_FINISHED:
//                            return 'border border-success text-white px-2 rounded';
//                        case REQUEST_PROBLEM:
//                            return 'border border-danger text-white px-2 rounded';
//                        case REQUEST_CANCELED:
//                            return 'border border-info text-white px-2 rounded';
                    }
                }
            ]
        ]);

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info m-1',
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
            return $this->crud->getModel()::existingStatuses(1);
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_id', $value);
            });

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Za zavođenje'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('where', 'status_id', REQUEST_SUBMITED); // apply the "active" eloquent scope
            });
        /*        $this->crud->addFilter([
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
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
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

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info',
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
                'label' => 'Ime prezime (licence)',
                'attribute' => 'ime_prezime_licence',
                'ajax' => TRUE
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
            //TODO samo OPSTI statusi !!!
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],

        ]);

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


}
