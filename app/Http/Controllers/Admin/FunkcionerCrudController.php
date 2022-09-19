<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FunkcionerRequest;
use App\Models\Funkcija;
use App\Models\FunkcijaTip;
use App\Models\FunkcionerMandat;
use App\Models\FunkcionerMandatTip;
use App\Models\Region;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FunkcionerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FunkcionerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    protected
        $column_definition_array = [
        'id',
        'osoba_id' => [
            'name' => 'osoba_id',
            'label' => 'JMBG',
        ],
        'osoba' => [
            'name' => 'osoba',
            'type' => 'select',
            'label' => 'Ime prezime',
            'entity' => 'osoba',
            'attribute' => 'ime_roditelj_prezime',
            'model' => 'App\Models\Osoba',
        ],
        'mandat_id' => [
            'name' => 'mandat_id',
            'label' => 'Mandat',
            'type' => 'select',
            'entity' => 'funkcionerMandat',
            'attribute' => 'naziv',
            'model' => 'App\Models\FunkcionerMandat',
        ],
        'funkcija_id' => [
            'name' => 'funkcija_id',
            'label' => 'Funkcija',
            'type' => 'select',
            'entity' => 'funkcija',
            'attribute' => 'naziv',
            'model' => 'App\Models\Funkcija',
        ],
        'region_id' => [
            'name' => 'region_id',
            'label' => 'Region',
            'type' => 'select',
            'entity' => 'region',
            'attribute' => 'naziv',
            'model' => 'App\Models\Region',
        ],
        'zvanje_grupa_id' => [
            'name' => 'zvanje_grupa_id',
            'label' => 'Grupa zvanja',
            'type' => 'select',
            'entity' => 'Sekcija',
            'attribute' => 'naziv',
            'model' => 'App\Models\Sekcija',
        ],
        'e-mail' => [
            'name' => 'e-mail',
            'label' => 'E-mail',
            'type' => 'relationship',
            'entity' => 'osoba',
            'attribute' => 'kontaktemail',
        ],
        'adresa' => [
            'name' => 'adresa',
            'label' => 'Adresa',
            'type' => 'relationship',
            'entity' => 'osoba',
            'attribute' => 'full_address',
        ],
        'status_id' => [
            'name' => 'status_id',
            'label' => 'Status',
            'type' => 'select',
            'entity' => 'Status',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
        ],
        'foto',
        'cv',
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
                'readonly' => 'readonly',
                'disabled' => 'disabled'
            ],
        ],
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Osoba',
            'attribute' => 'ime_prezime_jmbg',
            'ajax' => TRUE,
            'placeholder' => 'Odaberite osobu',
            'hint' => 'Pretraži po imenu, prezimenu ili jmbg.',
        ],
        'mandat_id' => [
            'name' => 'funkcionerMandat',
            'type' => 'relationship',
            'label' => 'Mandat',
//            'ajax' => TRUE,
            'attribute' => 'naziv',
//            'attribute' => 'naziv_datum_od', // todo: ne radi sa accessorom
            'inline_create' => [
                'modal_class' => 'modal-dialog modal-xl'
            ],
            'placeholder' => 'Odaberite mandat',
            'hint' => 'Odaberite postojeći mandat ili dodajte novi.',
        ],
        'funkcija_id' => [
            'name' => 'funkcija',
            'label' => 'Funkcija',
            'type' => 'relationship',
            'inline_create' => [
                'modal_class' => 'modal-dialog modal-xl'
            ],
            'placeholder' => 'Odaberite funkciju',
            'hint' => 'Odaberite postojeću funkciju ili dodajte novu.',
            'allows_null' => TRUE,
        ],
        'region_id' => [
            'name' => 'region_id',
            'label' => 'Region',
            'type' => 'relationship',
            'entity' => 'region',
            'attribute' => 'naziv',
            'placeholder' => 'Odaberite region',
            'hint' => 'Odaberite region.',
            'allows_null' => TRUE,
        ],
        'zvanje_grupa_id' => [
            'name' => 'zvanje_grupa_id',
            'label' => 'Grupa zvanja',
            'type' => 'relationship',
            'entity' => 'Sekcija',
            'attribute' => 'naziv',
            'model' => 'App\Models\Sekcija',
            'placeholder' => 'Odaberite grupu zvanja',
            'hint' => 'Odaberite grupu zvanja.',
        ],
        'status_id' => [
            'name' => 'status_id',
            'label' => 'Status',
            'type' => 'relationship',
            'entity' => 'Status',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
            'placeholder' => 'Odaberite status',
            'hint' => 'Odaberite status.',
            'default' => NEAKTIVAN,
        ],
        'foto' => [
            'name' => 'foto',
            'hint' => 'Unesite naziv fajla i ekstenziju.<br>Primer: predsednicaIKS_mijajlovic.jpg',
        ],
        'cv' => [
            'name' => 'cv',
            'hint' => 'Unesite naziv fajla i ekstenziju.<br>Primer: cv_predsednicaIKS_mijajlovic.pdf',
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
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Funkcioner::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/funkcioner');
        CRUD::setEntityNameStrings('funkcionera', 'funkcioneri');

        $this->crud->setColumns($this->column_definition_array);
        $this->crud->addClause('orderBy', 'id');

        $this->crud->enableDetailsRow();
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
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
        $this->crud->removeColumns([
//            'id',
//            'osoba_id',
//            'mandat_id',
//            'funkcija_id',
//            'region_id',
//            'zvanje_grupa_id',
//            'status_id',
            'foto',
            'cv',
            'napomena',
            'created_at',
            'updated_at',
        ]);

        $this->crud->setColumnDetails('osoba_id', [
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

        $this->crud->setColumnDetails('status_id', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case 0:
                            return 'bg-secondary text-dark px-2 rounded';
                        case 1:
                            return 'bg-success text-white px-2 rounded';
                    }
                },
            ]
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
            return $this->crud->query->whereHas('funkcionerMandat', function ($q) use ($value) {
                $q->where('mandat_tip_id', $value);
            });
        }
        );

        $this->crud->addFilter([
            'name' => 'mandat_id',
            'type' => 'select2_multiple',
            'label' => 'Mandat '
        ], function () {
            return FunkcionerMandat::all()->pluck('naziv', 'id')->toArray(); // the simple filter has no values, just the "Draft" label specified above
        }, function ($values) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'mandat_id', json_decode($values));
        }
        );

        $this->crud->addFilter([
            'name' => 'funkcija_tip_id',
            'type' => 'dropdown',
            'label' => 'Funkcija'
        ], function () {
            return FunkcijaTip::orderBy('id')->pluck('naziv', 'id')->toArray(); // the simple filter has no values, just the "Draft" label specified above
        }, function ($value) { // if the filter is active (the GET parameter "draft" exits)
            return $this->crud->query->whereHas('funkcija', function ($q) use ($value) {
                $q->where('funkcija_tip_id', $value);
            });
        }
        );

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'aktivno',
            'label' => 'Aktivni',
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('status_id', 1);
            }
        );

        $this->crud->addFilter(
            [
                'name' => 'region_id',
                'type' => 'select2_multiple',
                'label' => 'Region '
            ],
            function () {
                return Region::where('id', '<>', 0)->pluck('naziv', 'id')->toArray();
            },
            function ($values) { // if the filter is active
                $this->crud->addClause('whereIn', 'region_id', json_decode($values));
            }
        );

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
        CRUD::setValidation(FunkcionerRequest::class);

        $this->crud->addFields($this->field_definition_array);
        $this->crud->removeFields(['id', 'created_at', 'updated_at']);

        $this->crud->modifyField('region_id', [
            'name' => 'region_id',
            'options' => function ($query) {
                return $query->where('id', '<>', 0)->orderBy('id');
            },
        ]);

        $this->crud->modifyField('zvanje_grupa_id', [
            'name' => 'zvanje_grupa_id',
            'options' => function ($query) {
                return $query->orderBy('id');
            },
        ]);

        $this->crud->modifyField('status_id', [
            'name' => 'status_id',
            'options' => function ($query) {
                return $query->where('log_status_grupa_id', OPSTA)->orderBy('id');
            },
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

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        $this->crud->setColumnDetails('mandat_id', [
            'wrapper' => [
                // 'element' => 'a', // the element will default to "a" so you can skip it here
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('funkcioner-mandat/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info m-1',
                'target' => '_blank',
            ]
        ]);

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
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%');
                } else {
                    return $model->where('id', 'ilike', $searchTerm . '%')
                        ->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);
    }

    public function fetchFunkcionerMandat()
    {
        return $this->fetch([
            'model' => \App\Models\FunkcionerMandat::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->orWhere(function ($query) use ($searchTerm) {
                        $query->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                    })
                        ->orWhere(function ($query) use ($searchTerm) {
                            $query->where('naziv_full', 'ilike', '%' . $searchTerm[0] . '%')
                                ->where('naziv_full', 'ilike', '%' . $searchTerm[1] . '%');
                        });
                } else {
                    return $model->orWhere('naziv', 'ilike', '%' . $searchTerm . '%')
                        ->orWhere('naziv_full', 'ilike', '%' . $searchTerm . '%');
                }
            }
            // to filter the results that are returned
        ]);
    }

    public function fetchFunkcija()
    {
        return $this->fetch([
            'model' => \App\Models\Funkcija::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->orWhere(function ($query) use ($searchTerm) {
                        $query->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                    })
                        ->orWhere(function ($query) use ($searchTerm) {
                            $query->where('naziv_full', 'ilike', '%' . $searchTerm[0] . '%')
                                ->where('naziv_full', 'ilike', '%' . $searchTerm[1] . '%');
                        });
                } else {
                    return $model->orWhere('naziv', 'ilike', '%' . $searchTerm . '%')
                        ->orWhere('naziv_full', 'ilike', '%' . $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);
    }

    /*
     * end
     * Fetch operations
     */

    protected function showDetailsRow($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id)->osoba;
        $this->data['crud'] = $this->crud;
        return view('crud::osoba_details_row', $this->data);
    }
}
