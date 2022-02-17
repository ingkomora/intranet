<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DocumentRequest;
use App\Models\DocumentCategory;
use App\Models\DocumentCategoryType;
use App\Models\Status;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DocumentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DocumentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Document::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/document');
        CRUD::setEntityNameStrings('document', 'documents');

        $this->crud->set('show.setFromDb', FALSE);


        if (backpack_user()->hasRole('admin')) {
            $this->crud->allowAccess(['create', 'delete', 'update']);
        }

        if (backpack_user()->hasRole('sluzba_maticne_sekcije')) {
            $this->crud->allowAccess(['update']);
        }

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
            'document_category_id' => [
                'name' => 'documentCategory',
                'type' => 'relationship',
                'label' => 'Kategorija',
            ],
            'documentable_id' => [
                'name' => 'documentable_id',
                'type' => 'text',
                'label' => 'Broj zahteva'
            ],
            'status_id' => [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            /*            'user_id' => [
                            'name' => 'user',
                            'label' => 'Zaveo',
                            'type' => 'relationship',
                            'attribute' => 'name',
                        ],*/
            'registry_number',
            'registry_date' => [
                'name' => 'registry_date',
                'label' => 'Zavedeno',
                'type' => 'date',
                'format' => 'DD.MM.Y.'
            ],
//            'documentable_type',
            /*            'document_type_id' => [
                            'name' => 'documentType',
                            'label' => 'Tip dokumenta',
                            'type' => 'relationship',
                        ],*/
        ]);

        $this->crud->modifyColumn('status', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status_id == DOCUMENT_REGISTERED) {
                        return 'text-success';
                    }
                }
            ]
        ]);

        $this->crud->setColumnDetails('documentable_id', [
            'searchLogic' => function ($query, $column, $searchTerm) {

                $query->orWhereHas('documentable.osoba', function ($q) use ($column, $searchTerm) {
                    $q->where('id', 'ilike', $searchTerm . '%');
                })
                    ->orWhereHas('documentable', function ($q) use ($column, $searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    });
            }
        ]);

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'documentCategoryType',
            'label' => 'Tip kategorije'
        ],
            function () {
                return DocumentCategoryType::orderBy('id')->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('whereHas', 'documentCategory', function ($q) use ($value) {
                    $q->where('document_category_type_id', $value);
                });
            }
        );

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'documentCategory',
            'label' => 'Kategorija'
        ],
            function () {
                return DocumentCategory::orderBy('id')->pluck('name', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'document_category_id', $value);
            }
        );

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'status',
            'label' => 'Status'
        ],
            function () {
                return Status::where('log_status_grupa_id', DOCUMENTS)->orderBy('id')->pluck('naziv', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'status_id', $value);
            }
        );

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
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
            'document_category_id' => [
                'name' => 'documentCategory',
                'type' => 'relationship',
                'label' => 'Kategorija',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return backpack_url('document-category/' . $related_key . '/show');
                    },
                    'target' => '_blank',
                    'class' => 'btn btn-sm btn-outline-info mr-1',
                ],
            ],
            'registry_id' => [
                'name' => 'registry',
                'type' => 'relationship',
                'label' => 'Delovodnik',
                'attribute' => 'base_number_subject',
                'limit'=>500,
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return backpack_url('registry/' . $related_key . '/show');
                    },
                    'target' => '_blank',
                    'class' => 'btn btn-sm btn-outline-info mr-1',
                ],
            ],
            'status_id' => [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'relationship',
                'attribute' => 'naziv',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return backpack_url('status/' . $related_key . '/show');
                    },
                    'target' => '_blank',
                    'class' => 'btn btn-sm btn-outline-info mr-1',
                ],
            ],
            'user_id' => [
                'name' => 'user',
                'label' => 'Zaveo korisnik',
                'type' => 'relationship',
                'attribute' => 'name',
            ],
            'registry_number',
            'registry_date' => [
                'name' => 'registry_date',
                'label' => 'Zaveden',
                'type' => 'date',
                'format' => 'DD.MM.Y.'
            ],
            'document_type_id' => [
                'name' => 'documentType',
                'type' => 'relationship',
                'label' => 'Tip dokumenta',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $related_key) {
                        return backpack_url('document-type/' . $related_key . '/show');
                    },
                    'target' => '_blank',
                    'class' => 'btn btn-sm btn-outline-info mr-1',
                ],
            ],
            'path',
            'location',
            'documentable_type' => [
                'name' => 'documentable_type',
                'label' => 'Model',
//                'type'=> 'function_model',
//                'function_name'=> 'relatedModel',
            ],
            'documentable_id' => [
                'name' => 'documentable_id',
//                'label' => 'Documentable id',
                'type' => 'text',
//                'function_name'=> 'relatedModel',
            ],
            'barcode',
            'metadata' => [
                'name' => 'metadata',
                'type' => 'model_function',
                'function_name' => 'metadataFormating'
            ],
            'note',
        ]);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
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
        CRUD::setValidation(DocumentRequest::class);

        $this->crud->addFields([
//            'id',

            'document_category_id' => [
                'name' => 'documentCategory',
                'type' => 'relationship',
//                'attribute' => 'id_subject',
            ],
            'document_type_id' => [
                'name' => 'documentType',
                'type' => 'relationship',
//                'attribute' => 'id_subject',
            ],
            'registry_id' => [
                'name' => 'registry',
                'type' => 'relationship',
                'attribute' => 'id_subject',
            ],
            'status_id' => [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            //            'user_id',
            'registry_number',
            'registry_date' => [
                'name' => 'registry_date',
                'label' => 'datum zavođenja',
                'type' => 'date_picker',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'dd.mm.yyyy.',
                    'language' => 'rs-latin'
                ],
            ],
//            'path',
//            'location',
//            'barcode',
//            'metadata',
//            'note',
            /*'documentable' => [
                'name' => 'documentable',
                'type' => 'relationship',
//                'model' => '\App\Models\Request', //todo ne moze ovako
                'model' => '\App\Models\ZahtevLicenca',
                'attribute' => 'id',
                'ajax' => TRUE
            ],*/
//            'created_at',
//            'updated_at',
        ]);

        $this->crud->modifyField('status', [
            'options' => (function ($query) {
                return $query->orderBy('id')->where('log_status_grupa_id', 12)->get(); // samo grupa statusa "DOCUMENTS"
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

    /*
     * Fetch operations
     * start
     */
    public function fetchDocumentable()
    {
        return $this->fetch([
            'model' => \App\Models\Request::class, // required  TODO mora da se izabere u zavisnosti morph tabele
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                return $model->where('id', 'ilike', $searchTerm . '%');
            } // to filter the results that are returned
        ]);
    }

}
