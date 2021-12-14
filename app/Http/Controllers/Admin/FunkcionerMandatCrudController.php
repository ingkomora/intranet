<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FunkcionerMandatRequest;
use App\Models\Funkcioner;
use App\Models\FunkcionerMandat;
use App\Models\FunkcionerMandatTip;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FunkcionerMandatCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FunkcionerMandatCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;


    protected
        $column_definition_array = [
        'id',
        'naziv',
        'naziv_full' => [
            'name' => 'naziv_full',
            'label' => 'Pun naziv',
        ],
        'naziv_cir' => [
            'name' => 'naziv_cir',
            'label' => 'Naziv ćirilicom',
        ],
        'naziv_full_cir' => [
            'name' => 'naziv_full_cir',
            'label' => 'Pun naziv ćirilicom',
        ],
        'datum_od' => [
            'name' => 'datum_od',
            'label' => 'Datum početka',
            'type' => 'date',
            'format' => 'DD.MM.YYYY.'
        ],
        'datum_do' => [
            'name' => 'datum_do',
            'label' => 'Datum završetka',
            'type' => 'date',
            'format' => 'DD.MM.YYYY.'
        ],
        'status_id' => [
            'name' => 'status_id',
            'label' => 'Status',
            'type' => 'select',
            'entity' => 'Status',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
        ],
        'funkcioneri' => [
            'type' => 'relationship',
            'name' => 'funkcioneri',
            'label' => 'Funkcioneri',
            'ajax' => TRUE,
            'attribute' => 'osoba.ime_prezime_licence',  //accessor u Osoba modelu
            'placeholder' => 'Odaberite osobe',
            'hint' => 'Da biste dodali funkcionera, pretražite osobe po imenu i prezimenu ili po broju licence',
        ],
        'mandat_tip_id' => [
            'name' => 'mandat_tip_id',
            'label' => 'Tip mandata',
            'type' => 'select',
            'entity' => 'funkcionerMandatTip',
            'attribute' => 'naziv',
            'model' => 'App\Models\FunkcionerMandatTip',
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
        $field_definition_array = [
        'id' => [
            'name' => 'id',
            'attributes' => [
//                'readonly' => 'readonly',
                'disabled' => 'disabled'
            ],
        ],
        'naziv',
        'naziv_full',
        'naziv_cir',
        'naziv_full_cir',
        'mandat_tip_id' => [
            'name' => 'funkcionerMandatTip',
            'type' => 'relationship',
            'label' => 'Tip mandata',
            'inline_create' => [
                'modal_class' => 'modal-dialog modal-xl'
            ],
            'placeholder' => 'Odaberite tip mandata',
            'hint' => 'Odaberite postojeći tip mandata ili dodajte novi.',
        ],
        'funkcioneri' => [
            'type' => 'relationship',
            'name' => 'funkcioneri',
            'label' => 'Funkcioneri',
            'ajax' => TRUE,
            'attribute' => 'ime_prezime_licence',  //accessor u Osoba modelu
            'placeholder' => 'Odaberite osobe',
            'hint' => 'Da biste dodali funkcionera, pretražite osobe po imenu i prezimenu ili po broju licence',
        ],
        'datum_od' => [
            'name' => 'datum_od',
            'label' => 'Datum početka',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'rs-latin'
            ],
        ],
        'datum_do' => [
            'name' => 'datum_do',
            'label' => 'Datum završetka',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'rs-latin'
            ],
        ],
        'napomena' => [
            'name' => 'napomena',
            'hint' => 'Polje u koje se upisuje odluka o konstituisanju, razrešenju, isteku mandata...',
        ],
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss',
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss',
        ],
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\FunkcionerMandat::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/funkcioner-mandat');
        CRUD::setEntityNameStrings('mandat', 'mandati');

        $this->crud->setColumns($this->column_definition_array);
        $this->crud->addClause('orderBy', 'id');

        $this->crud->setColumnDetails('naziv', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhere(function ($query) use ($searchTerm) {
                        $query->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                    })
                        ->orWhere(function ($query) use ($searchTerm) {
                            $query->where('naziv_full', 'ilike', '%' . $searchTerm[0] . '%')
                                ->where('naziv_full', 'ilike', '%' . $searchTerm[1] . '%');
                        });
                } else {
                    $query->orWhere('naziv', 'ilike', '%' . $searchTerm . '%')
                        ->orWhere('naziv_full', 'ilike', '%' . $searchTerm . '%');
                }
            }
        ]);

        $this->crud->enableExportButtons();


        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeColumns([
//            'id',
//            'naziv',
//            'naziv_full',
            'naziv_cir',
            'naziv_full_cir',
//            'datum_od',
//            'datum_do',
//            'mandat_tip_id',
//            'napomena',
            'created_at',
            'updated_at',
        ]);

        /*
         * Define filters
         * start
         */
        $this->crud->addFilter([
            'name' => 'mandat_tip_id',
            'type' => 'dropdown',
            'label' => 'Organ ili telo'
        ], function () {
            return FunkcionerMandatTip::all()->pluck('naziv', 'id')->toArray(); // the simple filter has no values, just the "Draft" label specified above
        }, function ($value) { // if the filter is active (the GET parameter "draft" exits)
            return $this->crud->query->whereHas('funkcionerMandatTip', function ($q) use ($value) {
                $q->where('mandat_tip_id', $value);
            });
        }
        );
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
        CRUD::setValidation(FunkcionerMandatRequest::class);

        $this->crud->addFields($this->field_definition_array);
        $this->crud->removeFields(['id', 'created_at', 'updated_at']);

    }

    public function store()
    {
        dd('store');
        // do something before validation, before save, before everything
//        $response = $this->traitStore();
        // do something after save
//        return $response;
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

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */

    protected function setupShowOperation()
    {
        $this->crud->modifyColumn('funkcioneri', [
            'name' => 'aktivniFunkcioneri'
        ]);

        $this->crud->setColumnDetails('funkcioneri', [
            'wrapper' => [
                // 'element' => 'a', // the element will default to "a" so you can skip it here
                'href' => function ($crud, $column, $entry, $related_key) {
                    $funkcioner = Funkcioner::find($related_key);
                    return backpack_url('osoba/' . $funkcioner->osoba->lib . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info m-1',
                'target' => '_blank',
            ],
            'function' => function ($entry) {
                return $entry->funkcioneri->where('status_id', AKTIVAN);
            }
        ]);

        $this->crud->setColumnDetails('mandat_tip_id', [
            'attribute' => 'naziv_full',
            'wrapper' => [
                // 'element' => 'a', // the element will default to "a" so you can skip it here
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('funkcioner-mandat-tip/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info m-1',
                'target' => '_blank',
            ],
        ]);
    }

    /*
     * Fetch operations
     * start
     */
    public function fetchFunkcioneri()
    {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model
//                        pretraga po imenu i prezimenu za osobe bez deaktiviranih licenci
                        ->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%')
                        ->whereHas('licence', function ($query) use ($model) {
                            $query
                                ->where('status', '<>', 'D');
                        });
                } else {
                    return $model
                        ->whereHas('licence', function ($q) use ($searchTerm) {
                            $q->where('id', 'ilike', $searchTerm . '%')
                                ->where('status', '<>', 'D');
                        })
                        ->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);

    }

    public function fetchFunkcionerMandatTip()
    {
        return $this->fetch([
            'model' => \App\Models\FunkcionerMandatTip::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                        ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%')
                        ->orWhere('naziv_full', 'ilike', '%' . $searchTerm[0] . '%')
                        ->orWhere('naziv_full', 'ilike', '%' . $searchTerm[1] . '%');
                } else {
                    return $model->where('naziv', 'ilike', '%' . $searchTerm . '%')
                        ->orWhere('naziv_full', 'ilike', '%' . $searchTerm . '%');
                }
            }
            // to filter the results that are returned
        ]);
    }
    /*
     * end
     * Fetch operations
     */
}
