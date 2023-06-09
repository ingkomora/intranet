<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\PromenaPodatakaEmailOperation;
use App\Http\Controllers\Admin\Operations\UpdateDataBrisanjeClanstvoOperation;
use App\Http\Requests\OsobaEditRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OsobaEditCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OsobaEditCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /*    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
            update as traitUpdate;
        }*/

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
//    use UpdateDataBrisanjeClanstvoOperation;

    protected
        $columns_definition_array = [
        'id',
        'osoba' => [
            'name' => 'osoba_id',
            'type' => 'model_function',
            'label' => 'Ime (roditelj) prezime',
            'function_name' => 'getImeRoditeljPrezimeAttribute',
        ],
        'prebivalisteadresa' => [
            'name' => 'prebivalisteadresa',
            'label' => 'Adresa',
        ],
        'ulica' => [
            'name' => 'ulica',
            'label' => 'Ulica',
        ],
        'broj' => [
            'name' => 'broj',
            'label' => 'Broj',
        ],
        'podbroj' => [
            'name' => 'podbroj',
            'label' => 'Podbroj',
        ],
        'sprat' => [
            'name' => 'sprat',
            'label' => 'Sprat',
        ],
        'stan' => [
            'name' => 'stan',
            'label' => 'Stan',
        ],
        'prebivalistebroj' => [
            'name' => 'prebivalistebroj',
            'label' => 'Poštanski broj',
        ],
        'prebivalisteopstina' => [
            'name' => 'prebivalisteopstina',
            'label' => 'Opština',
        ],
        'prebivalisteopstinaid' => [
            'name' => 'prebivalisteopstinaid',
            'type' => 'select',
            'label' => 'Opština (relacija)',
            'entity' => 'opstinaId',
            'attribute' => 'ime',
            'model' => 'App\Models\Opstina',
        ],
        'prebivalistemesto' => [
            'name' => 'prebivalistemesto',
            'label' => 'Mesto',
        ],
        'prebivalistedrzava' => [
            'name' => 'prebivalistedrzava',
            'label' => 'Država',
        ],
//        'status_id' => [
//            'name' => 'status_id',
//            'type' => 'select',
//            'entity' => 'requests.status',
//            'label' => 'Status',
//            'model' => 'App\Models\Request',
//            'attribute' => 'naziv'
//        ]
    ],
        $fields_definition_array = [
        'id' => [
            'name' => 'id',
            'attributes' => [
                'readonly' => 'readonly',
            ]
        ],
        'ime',
        'roditelj',
        'prezime',
        'prebivalisteadresa' => [
            'name' => 'prebivalisteadresa',
            'label' => 'Adresa',
        ],
        'ulica' => [
            'name' => 'ulica',
            'label' => 'Ulica',
        ],
        'broj' => [
            'name' => 'broj',
            'label' => 'Broj',
        ],
        'podbroj' => [
            'name' => 'podbroj',
            'label' => 'Podbroj',
        ],
        'sprat' => [
            'name' => 'sprat',
            'label' => 'Sprat',
        ],
        'stan' => [
            'name' => 'stan',
            'label' => 'Stan',
        ],
        'prebivalistebroj' => [
            'name' => 'prebivalistebroj',
            'label' => 'Poštanski broj',
        ],
        'prebivalisteopstinaid' => [
            'name' => 'prebivalisteopstinaid',
            'type' => 'relationship',
            'label' => 'Opština (relacija)',
            'entity' => 'opstinaId',
            'attribute' => 'ime',
            'placeholder' => 'Odaberite opštinu',
            'hint' => 'Pretražite po opštinu po nazivu',
            'allows_null' => TRUE,
        ],
        'prebivalistemesto' => [
            'name' => 'prebivalistemesto',
            'label' => 'Mesto',
        ],
        'prebivalistedrzava' => [
            'name' => 'prebivalistedrzava',
            'label' => 'Država',
        ],
        /*        'status_id' => [
                    'name' => 'status_id',
                    'type' => 'select',
                    'label' => 'Status',
                    'entity' => 'requests.status',
                    'attribute' => 'naziv',
                    'default' => OBRADJEN, // TODO: da postavi vrednost iz baze, a ne prvi status za opstu kategoriju
                ],*/
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Osoba::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/osoba-edit');
        CRUD::setEntityNameStrings('člana', 'Ažuriranje adresa');

        $this->crud->setColumns($this->columns_definition_array);

        $this->crud->addClause('whereHas', 'requests', function ($query) {
            $query->where('request_category_id', 2);
        });

//        $this->crud->addClause('where', 'clan', 10); // privremeni status za clanove kojima se sprema brisanje
        $this->crud->addClause('orderBy', 'ime');

//        if (!backpack_user()->hasRole(['admin'])) {
//            $this->crud->denyAccess(['create']);
//        } else if (!backpack_user()->hasPermissionTo('update addresses')) {
//            $this->crud->denyAccess(['create']);
//        }

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

        $this->crud->modifyColumn('id', [
            'name' => 'id',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%')
                        ->whereHas('licence', function ($q) {
                            $q->where('status', '<>', 'D');
                        });
                } else {
                    $query->whereHas('licence', function ($q) use ($searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    })
                        ->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%')
                        ->orWhere('id', 'ilike', $searchTerm . '%');
                }
            }
        ]);

        /*$this->crud->modifyColumn('status_id', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($related_key) {
                        case OBRADJEN:
                            return 'bg-success text-white px-2 rounded';
                        case PROBLEM:
                            return 'bg-danger text-white px-2 rounded';
                    }
                }
            ]
        ]);*/


//        // simple filter
//        $this->crud->addFilter([
//            'type' => 'simple',
//            'name' => 'platili',
//            'label' => 'Platili članarinu'
//        ],
//            FALSE,
//            function () { // if the filter is active
//                $this->crud->addClause('whereHas', 'clanarine', function ($query) {
//                    $query->where('rokzanaplatu', '>=', 'now()');
//                }); // apply the "active" eloquent scope
//            });
//
//        $this->crud->addFilter([
//            'type' => 'simple',
//            'name' => 'nisuplatili',
//            'label' => 'Nisu platili članarinu'
//        ],
//            FALSE,
//            function () { // if the filter is active
//                $this->crud->addClause('whereHas', 'clanarine', function ($query) {
//                    $query->where('rokzanaplatu', '<', 'now()')->whereRaw('iznoszanaplatu > iznosuplate + pretplata');
//                }); // apply the "active" eloquent scope
//            });
//
//        // simple filter
//        $this->crud->addFilter([
//            'type' => 'simple',
//            'name' => 'nemaAdresu',
//            'label' => 'Nema adresu'
//        ],
//            FALSE,
//            function () { // if the filter is active
//                $this->crud->addClause('where', 'prebivalisteadresa', '=', '/');
////                $this->crud->addClause('orWhere','prebivalistebroj', '=', '/');
//            });
//
//
//        // simple filter
//        $this->crud->addFilter([
//            'type' => 'simple',
//            'name' => 'nemalib',
//            'label' => 'Nema LIB'
//        ],
//            FALSE,
//            function () { // if the filter is active
//                $this->crud->addClause('whereNull', 'lib');
//            });

        // dropdown filter
        /*        $this->crud->addFilter([
                    'name' => 'status',
                    'type' => 'dropdown',
                    'label' => 'Status'
                ], function () {
                    return Request::existingStatuses();
                },
                    function ($value) { // if the filter is active
                        $this->crud->addClause('whereHas', 'requests', function ($q) use ($value) {
                            $q->where('status_id', $value);
                        });
                    });*/

        // dropdown filter
        /*        $this->crud->addFilter([
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
                        $this->crud->addClause('whereHas', 'requests', function ($q) use ($values) {
                            $q->whereIn('note', json_decode($values));
                        });
                    });*/

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Aktivna članstva'
        ],
            FALSE,
            function () { // if the filter is active
//                $this->crud->addClause('active'); // apply the "active" eloquent scope
                $this->crud->addClause('whereHas', 'memberships', function ($q) {
                    $q->active();
                });
            });

//        $this->crud->addFilter([
//            'type' => 'simple',
//            'name' => 'mirovanje',
//            'label' => 'U mirovanju'
//        ],
//            FALSE,
//            function () { // if the filter is active
////                $this->crud->addClause('where', 'status_id', 12); // apply the "active" eloquent scope
//                $this->crud->addClause('whereHas', 'suspendedMembership');
//            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'imazahtevzamirovanje',
            'label' => 'Bez zahteva za mirovanje'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereDoesntHave', 'zahtevZaMirovanje', function ($q) {
                    $q->where('status_id', REQUEST_IN_PROGRESS);
                });

            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'bezfunkcionera',
            'label' => 'Bez funkcionera'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereDoesntHave', 'aktivniClanoviVeca'); // apply the "active" eloquent scope
            });

        // daterange filter
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'from_to',
            'label' => 'Duguje'
        ],
            FALSE,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
//            dd($dates);
                $this->crud->addClause('whereExists', function ($query) use ($dates) {
                    $query
                        ->select('c1.rokzanaplatu')
                        ->from('tclanarinaod2006 as c1')
                        ->where('c1.rokzanaplatu', function ($query) {
                            $query
                                ->select('c2.rokzanaplatu')
                                ->from('tclanarinaod2006 as c2')
                                ->whereColumn('c1.osoba', 'c2.osoba')
                                ->orderByDesc('c2.rokzanaplatu')
                                ->limit(1);
                        })
                        ->where('c1.rokzanaplatu', '>=', $dates->from)
                        ->whereColumn('c1.osoba', 'tosoba.id');
                });

                $this->crud->addClause('whereExists', function ($query) use ($dates) {
                    $query
                        ->select('c1.rokzanaplatu')
                        ->from('tclanarinaod2006 as c1')
                        ->where('c1.rokzanaplatu', function ($query) {
                            $query
                                ->select('c2.rokzanaplatu')
                                ->from('tclanarinaod2006 as c2')
                                ->whereColumn('c1.osoba', 'c2.osoba')
                                ->orderByDesc('c2.rokzanaplatu')
                                ->limit(1);
                        })
                        ->where('c1.rokzanaplatu', '<=', $dates->to)
                        ->whereColumn('c1.osoba', 'tosoba.id');
                });

            });

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
        $this->crud->setValidation(OsobaEditRequest::class);

        $this->crud->addFields($this->fields_definition_array);
        /*$this->crud->modifyField('status_id', [
            'options' => (function ($query) {
                return $query
                    ->where('log_status_grupa_id', OPSTA)
                    ->get();
            }), //  you can use this to filter the results show in the select
        ]);*/

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

    /*    public function update()
        {
    //        todo: sta ako ima vise rekorda???
    //        dd($this->crud->getRequest()->status_id);
            $request = Request::where('osoba_id', $this->crud->getRequest()->id)
                ->where('request_category_id', 2)
                ->first();

    //        dd($request);
            $request->status_id = $this->crud->getRequest()->status_id;
            if ($this->crud->getRequest()->status_id == KREIRAN) {
                $request->status_id = OBRADJEN;
            }
            $request->save();
            $response = $this->traitUpdate();
            // do something after save
            return $response;
        }*/

    protected function showDetailsRow($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_clanarina_details_row', $this->data);
    }
}
