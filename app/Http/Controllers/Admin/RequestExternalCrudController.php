<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegisterRequestExternalRequest;
use App\Models\Document;
use App\Models\RequestCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RequestExternalCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestExternalCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    use Operations\RegisterRequestBulkOperation;
    use Operations\DocumentCancelationBulkOperation;


    protected $request_category_id;
    protected $requestable_model;
    protected $status_col_name = 'status_id';
    protected $allow_create = FALSE;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {

        CRUD::setModel(\App\Models\RequestExternal::class);
        $type = \Request::segment(2);

        switch ($type) {
            case 'request-external':
                $this->request_category_id = [15];
                $this->requestable_model = '\App\Models\RequestExternal';

                $this->crud->operation('list', function () {
                    $this->crud->disableBulkActions();
                    $this->crud->denyAccess(['documentcancelation', 'registerrequestbulk']);
                });

                CRUD::setRoute(config('backpack.base.route_prefix') . '/request-external');
                CRUD::setEntityNameStrings('eksterni zahtev', 'Eksterni zahtevi');
                CRUD::addClause('where', 'request_category_id', $this->request_category_id);
                break;
            case 'registerrequestiksmobnet':
                $this->request_category_id = [15];
                $this->requestable_model = '\App\Models\RequestExternal';
                $this->allow_create = TRUE;

                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestiksmobnet');
                CRUD::setEntityNameStrings('eksterni zahtev', 'Zahtevi za IKS Mobnet usluge');
                CRUD::addClause('where', 'request_category_id', $this->request_category_id);

                $this->crud->denyAccess(['fileUpload', 'registerrequestbulk', 'documentcancelation']);

                $this->crud->operation('list', function () {
                    if (backpack_user()->hasPermissionTo('zavedi')) {
                        $this->crud->enableBulkActions();
                        $this->crud->addButtonFromView('top', 'bulk.registerRequest', 'bulk.registerRequest', 'end');
                    }
                    $this->crud->allowAccess(['registerrequestbulk']);
                });

                break;
        }

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }

        $this->crud->set('show.setFromDb', FALSE);


        $this->crud->addButtonFromView('line', 'requestExternalDocuments', 'requestExternalDocuments', 'end');

//        $this->crud->enableDetailsRow();
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
            'requestCategory' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'attribute' => 'name',
                'label' => 'Kategorija',
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
                    switch ($entry->{$this->status_col_name}) {
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info mr-1';
                        case REQUEST_FINISHED:
                            return 'btn btn-sm btn-outline-success text-dark';
                        case REQUEST_CANCELED:
                            return 'btn btn-sm btn-outline-danger mr-1';
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
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'requestCategory',
            'type' => 'dropdown',
            'label' => 'Kategorija'
        ], function () {
            return RequestCategory::all()->pluck('name', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'request_category_id', $value);
            }
        );

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

    }

    protected function setupShowOperation()
    {
        $this->crud->addColumns([
            'id',
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija',
            ],
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
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
            ],
            'created_at' => [
                'name' => 'created_at',
                'label' => 'Kreiran',
                'type' => 'datetime',
                'format' => 'DD.MM.Y. HH:mm:ss',
            ],
            'updated_at' => [
                'name' => 'updated_at',
                'label' => 'Ažuriran',
                'type' => 'datetime',
                'format' => 'DD.MM.Y. HH:mm:ss',

            ],

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

        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info mr-1';
                        case REQUEST_FINISHED:
                            return 'btn btn-sm btn-outline-success text-dark';
                        case REQUEST_CANCELED:
                            return 'btn btn-sm btn-outline-danger mr-1';
                        default:
                    }
                }
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

        CRUD::setValidation(RegisterRequestExternalRequest::class);

        $this->crud->addFields([
//            'id',

            /*'documents' => [
                'name' => 'documents',
                'type' => 'relationship',
                'attribute' => 'category_type_name_status_registry_number',
                'ajax' => TRUE,
            ],*/
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija',
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

        $this->crud->modifyField('requestCategory', [
            'options' => (function ($query) {
                return $query->orderBy('id')->whereIn('id', $this->request_category_id)->get();
            }),
        ]);

        $this->crud->modifyField('status', [
            'options' => (function ($query) {
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
}
