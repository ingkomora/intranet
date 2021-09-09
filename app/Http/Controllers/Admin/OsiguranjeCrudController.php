<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OsiguranjeRequest;
use App\Models\Firma;
use App\Models\Osiguranje;
use App\Models\OsiguranjeTip;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\PermissionManager\app\Models\Role;
use FontLib\Table\Type\name;


/**
 * Class OsiguranjeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class OsiguranjeCrudController extends CrudController
{

//if(backpack_user()->can('edit articles'))
//backpack_user()->hasPermissionTo('edit')
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    protected
        $column_definition_array = [
        'id',
        'osiguravajuca_kuca_mb' => [
            'name' => 'osiguravajuca_kuca_mb',
            'label' => 'Osiguravajuća kuća (mb)',
            'type' => 'relationship',
            'entity' => 'firmaOsiguravajucaKuca',
            'attribute' => 'naziv_mb',
            'model' => 'App\Models\Firma',
        ],
        'ugovarac_osiguranja_mb' => [
            'name' => 'ugovarac_osiguranja_mb',
            'type' => 'relationship',
            'label' => 'Ugovarač osiguranja firma (mb)',
            'entity' => 'firmaUgovarac',
            'attribute' => 'naziv_mb',
            'model' => 'App\Models\Firma',
        ],
        'ugovarac_osiguranja_osoba_id' => [
            'name' => 'ugovarac_osiguranja_osoba_id',
            'label' => 'Ugovarač individualnog osiguranja',
            'type' => 'relationship',
            'entity' => 'osobaUgovarac',
            'model' => 'App\Models\Osoba',
            'attribute' => 'ime_prezime_jmbg'
        ],
        'korisnici_osiguranja' => [
            'name' => 'osobe',
            'label' => 'Korisnici osiguranja',
            'type' => 'relationship',
            'entity' => 'osobe',
            'model' => 'App\Models\Osoba',
            'attribute' => 'ime_prezime_licence',
        ],
        'polisa_broj' => [
            'name' => 'polisa_broj',
            'label' => 'Broj polise'
        ],
        'status_polise_id' => [
            'name' => 'status_polise_id',
            'label' => 'Status polise',
            'type' => 'boolean',
//             optionally override the Yes/No texts
            'options' => [0 => 'Neaktivna', 1 => 'Aktivna'],
        ],
        'polisa_predmet' => [
            'name' => 'polisa_predmet',
            'label' => 'Predmet polise',
        ],
        'polisa_pokrice_id' => [
            'name' => 'polisa_pokrice_id',
            'type' => 'relationship',
            'label' => 'Pokriće polise',
            'entity' => 'polisaPokrice',
//            'attribute' => 'naziv_id',
            'attribute' => 'naziv',
            'model' => 'App\Models\OsiguranjePolisaPokrice',
        ],
        'osiguranje_vrsta',
        'osiguranje_tip_id' => [
            'name' => 'osiguranje_tip_id',
            'type' => 'relationship',
            'label' => 'Tip osiguranja',
            'entity' => 'osiguranjeTip',
//            'attribute' => 'osiguranje_tip_naziv_id',
            'attribute' => 'naziv',
            'model' => 'App\Models\OsiguranjeTip',
        ],
        'polisa_iskljucenost' => [
            'name' => 'polisa_iskljucenost',
            'label' => 'Isključenost polise',
        ],
        'polisa_teritorijalni_limit' => [
            'name' => 'polisa_teritorijalni_limit',
            'label' => 'Teritorijalni limit polise',
        ],
        'polisa_datum_izdavanja' => [
            'name' => 'polisa_datum_izdavanja',
            'label' => 'Datum izdavanje polise',
            'type' => 'date',
            'format' => 'DD.MM.YYYY.'
        ],
        'polisa_datum_pocetka' => [
            'name' => 'polisa_datum_pocetka',
            'label' => 'Datum početka',
            'type' => 'date',
            'format' => 'DD.MM.YYYY.'
        ],
        'polisa_datum_zavrsetka' => [
            'name' => 'polisa_datum_zavrsetka',
            'label' => 'Datum završetka',
            'type' => 'date',
            'format' => 'DD.MM.YYYY.'
        ],
        'status_dokumenta_id' => [
            'name' => 'status_dokumenta_id',
            'label' => 'Status dokumenta',
            'type' => 'relationship',
            'entity' => 'statusDokumenta',
            'model' => 'App\Models\Status',
            'attribute' => 'naziv'
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
            'attributes' => ['readonly' => 'readonly'],
        ],
        'osiguranje_vrsta' => [
            'name' => 'osiguranje_vrsta',
            'default' => 'Osiguranje od profesionalne odgovornosti',
            'attributes' => ['readonly' => 'readonly'],
        ],
        'osiguranje_tip_id' => [
            'name' => 'osiguranje_tip_id',
            'type' => 'select',
            'label' => 'Tip osiguranja',
            'entity' => 'osiguranjeTip',
            'attribute' => 'naziv',
            'model' => 'App\Models\OsiguranjeTip',
            'default' => OSIGURANJE_KOLEKTIVNO,
        ],
        'osiguravajuca_kuca_mb' => [
            'type' => 'relationship',
            'name' => 'FirmaOsiguravajucaKuca',
            'label' => 'Osiguravajuća kuća',
            'attribute' => 'naziv_mb',
            'ajax' => TRUE,
            'inline_create' => [
                'entity' => 'firma'
            ],
            'placeholder' => 'Odaberite osiguravajuću kuću',
            'hint' => 'Pretraži po nazivu ili mb. Ovo polje je obavezno ukoliko je tip osiguranja IKS ili kolektivno!',
        ],
        'ugovarac_osiguranja_mb' => [
            'type' => 'relationship',
            'name' => 'FirmaUgovarac',
            'label' => 'Ugovarač osiguranja firma (pretraži po nazivu ili mb)',
            'attribute' => 'naziv_mb',
            'ajax' => TRUE,
            'inline_create' => [
                'entity' => 'firma'
            ],
            'placeholder' => 'Odaberite osiguravajuću kuću',
            'hint' => 'Pretraži po nazivu ili mb. Ovo polje je obavezno ukoliko je tip osiguranja individualno!',
        ],
        'ugovarac_osiguranja_osoba_id' => [
            'type' => 'relationship',
            'name' => 'osobaUgovarac',
            'label' => 'Ugovarač osiguranja osoba (pretraži po imenu i prezimenu, broju licence ili jmbg)',
            'attribute' => 'ime_prezime_licence',
            'ajax' => TRUE,
            'placeholder' => 'Odaberite osobu',
            'hint' => 'Ukoliko je individualno osiguranje, odaberite osobu koja je ugovarač osiguranja',
        ],
        'korisnici_osiguranja' => [
            'type' => 'relationship',
            'name' => 'osobe',
            'label' => 'Korisnici osiguranja',
            'ajax' => TRUE,
            'attribute' => 'ime_prezime_licence',  //accessor u Osoba modelu
            'pivot' => TRUE,
            'placeholder' => 'Odaberite osobe',
            'hint' => 'Osobe koje su osigurane ovom polisom',
        ],
        'polisa_broj',
        'polisa_predmet',
        'polisa_pokrice_id' => [
            'type' => 'select2',
            'name' => 'polisaPokrice',
            'label' => 'Pokriće polise',
            'attribute' => 'naziv',
            'ajax' => TRUE,
            'default' => PROJEKTOVANJE,
        ],
        'polisa_iskljucenost' => [
            'name' => 'polisa_iskljucenost',
            'label' => 'Polisa isključenost',
        ],
        'polisa_teritorijalni_limit' => [
            'name' => 'polisa_teritorijalni_limit',
            'default' => 'Republika Srbija',
        ],
        'polisa_datum_izdavanja' => [
            'name' => 'polisa_datum_izdavanja',
            'label' => 'Datum izdavanja',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'rs-latin'
            ],
        ],
        'polisa_datum_pocetka' => [
            'name' => 'polisa_datum_pocetka',
            'label' => 'Datum početka važenja polise',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'rs-latin'
            ],
        ],
        'polisa_datum_zavrsetka' => [
            'name' => 'polisa_datum_zavrsetka',
            'label' => 'Datum isteka polise',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'rs-latin'
            ],
        ],
        'status_polise_id' => [
            'name' => 'status_polise_id',
            'type' => 'select',
            'label' => 'Status polise',
            'entity' => 'statusPolise',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
            'default' => NEAKTIVAN,
        ],
        'status_dokumenta_id' => [
            'label' => 'Status dokumenta',
            'type' => 'select2',
            'name' => 'status_dokumenta_id',
            'entity' => 'statusDokumenta',
            'attribute' => 'naziv',
            'default' => DOKUMENT_ORIGINAL
        ],
        'napomena',
        'created_at' =>
            [
                'name' => 'created_at',
                'label' => 'Kreiran',
                'attributes' => ['disabled' => 'disabled'],
                'type' => 'datetime_picker',
                'datetime_picker_options' => [
                    'format' => 'DD.MM.YYYY. HH:mm:ss',
                    'language' => 'sr_latin'
                ],
            ],
        'updated_at' =>
            [
                'name' => 'updated_at',
                'label' => 'Ažuriran',
                'attributes' => ['disabled' => 'disabled'],
                'type' => 'datetime_picker',
                'datetime_picker_options' => [
                    'format' => 'DD.MM.YYYY. HH:mm:ss',
                    'language' => 'sr_latin'
                ],
            ],
    ];

    public function setup()
    {
        $this->crud->setModel('App\Models\Osiguranje');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/osiguranje');
        $this->crud->setEntityNameStrings('osiguranje', 'osiguranja');

        $this->crud->setColumns($this->column_definition_array);

        $this->crud->enableDetailsRow();
        $this->crud->enableExportButtons();

        /*$permissionNames = backpack_user()->getPermissionNames(); // collection of name strings
        $permissions = backpack_user()->permissions; // collection of permission objects
        $this->crud->denyAccess('all');
        if (backpack_user()->hasPermissionTo('update')) {

        }*/

        // Wrap Column Text in an HTML Element
        $this->crud->setColumnDetails('status_polise_id', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status_polise_id === 1) {
                        return 'bg-success text-white px-2 rounded';
                    }
                    return 'bg-secondary text-dark px-2 rounded';
                },
            ]
        ]);

        $this->crud->setColumnDetails('osiguravajuca_kuca_mb', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('firmaOsiguravajucaKuca', function ($q) use ($column, $searchTerm) {
                        $q->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('firmaOsiguravajucaKuca', function ($q) use ($column, $searchTerm) {
                        $q->where('mb', 'ilike', $searchTerm . '%')
                            ->orWhere('naziv', 'ilike', '%' . $searchTerm . '%');
                    });
                }
            }
        ]);

        $this->crud->setColumnDetails('ugovarac_osiguranja_mb', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('firmaUgovarac', function ($q) use ($column, $searchTerm) {
                        $q->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('firmaUgovarac', function ($q) use ($column, $searchTerm) {
                        $q->where('mb', 'ilike', $searchTerm . '%')
                            ->orWhere('naziv', 'ilike', '%' . $searchTerm . '%');
                    });
                }
            }
        ]);

        $this->crud->setColumnDetails('ugovarac_osiguranja_osoba_id', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osobaUgovarac', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('osobaUgovarac', function ($q) use ($column, $searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%')
                            ->orWhere('ime', 'ilike', $searchTerm . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm . '%');
                    });
                }
            }
        ]);

    }

    protected function setupListOperation()
    {
        $this->crud->removeColumns([
//            'id',
            'osiguranje_vrsta',
//            'osiguranje_tip_id',
//            'osiguravajuca_kuca_mb',
//            'ugovarac_osiguranja_mb',
//            'ugovarac_osiguranja_osoba_id',
            'osobe', // korisnici osiguranja
//            'polisa_broj',
            'polisa_predmet',
//            'polisa_pokrice_id',
            'polisa_iskljucenost',
            'polisa_teritorijalni_limit',
            'polisa_datum_izdavanja',
            'polisa_datum_pocetka',
            'polisa_datum_zavrsetka',
//            'status_polise_id',
            'status_dokumenta_id',
            'napomena',
            'created_at',
            'updated_at',
        ]);

        /*
         * Define filters
         * start
         */
        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'osiguranje_tip_id',
            'label' => 'Tip osiguranja',
        ], function () {
            return OsiguranjeTip::all()->pluck('naziv', 'id')->toArray();
        }
        );

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'status_polise_id',
            'label' => 'Status polise'
        ], function () {
            return [
                NEAKTIVAN => 'Neaktivna',
                AKTIVAN => 'Aktivna'
            ]; // the simple filter has no values, just the "Draft" label specified above
        }, function ($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'status_polise_id', $value);
        }
        );

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'firmaOsiguravajucaKuca',
            'label' => 'Osiguravajuća kuća'
        ],
            function () {
                $q = "
                    SELECT f.mb, f.naziv
                    FROM firme f, osiguranja os
                    WHERE f.mb = os.osiguravajuca_kuca_mb;
                    ";
                return collect(\DB::select($q))->pluck('naziv', 'mb')->toArray();
            },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'osiguravajuca_kuca_mb', $value);
            }
        );

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'firmaUgovarac',
            'label' => 'Ugovarač osiguranja (firma)'
        ],
            function () {
                $q = "
                    SELECT f.mb, f.naziv
                    FROM firme f, osiguranja os
                    WHERE f.mb = os.ugovarac_osiguranja_mb;
                    ";
                return collect(\DB::select($q))->pluck('naziv', 'mb')->toArray();
            },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'ugovarac_osiguranja_mb', $value);
            }
        );

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'ugovarac_osiguranja_osoba_id',
            'label' => 'Individualno osiguranje',
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('ugovarac_osiguranja_osoba_id', '<>', NULL);
            }
        );
        /*
         * end
         * Define filters
         */
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(OsiguranjeRequest::class);

        $this->crud->addFields($this->field_definition_array);

        $this->crud->removeField([
            'id',
            'created_at',
            'updated_at',
        ]);

        $this->crud->modifyField('status_dokumenta_id', [
            'options' => (function ($query) {
                return $query->where('log_status_grupa_id', DOKUMENTA)->get();
            }),
        ]);
        $this->crud->modifyField('status_polise_id', [
            'name' => 'status_polise_id',
            'options' => (function ($query) {
                return $query->where('log_status_grupa_id', 1)->get();
            }),
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function fetchOsobe()
    {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
//            'searchable_attributes' => ['id', 'ime', 'prezime'],
            'searchable_attributes' => [],
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
                        ->orWhere('prezime', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);

    }

    public function fetchFirmaOsiguravajucaKuca()
    {
        return $this->fetch([
            'model' => \App\Models\Firma::class, // required
//            'searchable_attributes' => ['mb', 'naziv'],
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return
                        $model->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                } else {
                    return $model->where('mb', 'ilike', $searchTerm . '%')
                        ->orWhere('naziv', 'ilike', '%' . $searchTerm . '%');

                }
            } // to filter the results that are returned
        ]);
    }

    public function fetchFirmaUgovarac()
    {
        return $this->fetch([
            'model' => \App\Models\Firma::class, // required
//            'searchable_attributes' => ['mb'],
            'searchable_attributes' => [],
//            'routeSegment' => 'mb', // falls back to the key of this array if not specified ("category")
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return
                        $model->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                            ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                } else {
                    return $model->where('mb', 'ilike', $searchTerm . '%')
                        ->orWhere('naziv', 'ilike', '%' . $searchTerm . '%');

                }
            } // to filter the results that are returned
        ]);
    }

    public function fetchOsobaUgovarac()
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

    protected function showDetailsRow($id)
    {
//        $this->crud->hasAccessOrFail('details_row');//???

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
//dd($this->data['entry']->osobe);
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
//        return view('crud.details_row', $this->data);
        return view('crud::osiguranje_details_row', $this->data);
    }
}
