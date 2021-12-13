<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PrijavaRequest;
use App\Http\Requests\SiPrijavaRequest;
use App\Models\RegOblast;
use App\Models\RegPodoblast;
use App\Models\Sekcija;
use App\Models\SiPrijava;
use App\Models\SiVrsta;
use App\Models\VrstaPosla;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SiPrijavaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    protected
        $column_deffinition_array = [
        'id' => [
            'name' => 'id',
            'label' => 'Broj prijave',
        ],
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
        ],
        'vrsta_posla_id' => [
            'name' => 'vrstaPosla',
            'label' => 'Vrsta posla',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_oblast_id' => [
            'name' => 'regOblast',
            'label' => 'Oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_pod_oblast_id' => [
            'name' => 'regPodOblast',
            'label' => 'Uža oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'zvanje_id' => [
            'name' => 'zvanje',
            'label' => 'Zvanje',
            'type' => 'relationship',
            'attribute' => 'skrnaziv',
        ],
        'si_vrsta_id' => [
            'name' => 'siVrsta',
            'label' => 'Vrsta ispita',
            'type' => 'relationship',
            'attributes' => 'naziv',
        ],
        'status_prijave' => [
            'name' => 'status',
            'label' => 'Status',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'strucni_rad' => [
            'name' => 'strucni_rad',
            'label' => 'Stručni rad',
            'type' => 'select_from_array',
            'options' => [
                0 => 'Rad izrađuje sa mentorom',
                1 => 'Ima stručni rad',
            ]
        ],
        'barcode' => [
            'name' => 'strucni_rad',
        ],
        'datum_prijema' => [
            'name' => 'datum_prijema',
            'label' => 'Datum prijema',
            'type' => 'date',
            'format' => 'DD.MM.Y.',
        ],
        'app_korisnik_id' => [
            'name' => 'user',
            'label' => 'Zaveo korisnik',
            'type' => 'relationship',
            'attributes' => 'id',
        ],
        'zavodni_broj' => [
            'name' => 'zavodni_broj',
            'label' => 'Zavodni broj',
        ],
        'tema' => [
            'name' => 'tema',
        ],
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažurirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
        ],
    ],
        $field_deffinition_array = [
        'id' => [
            'name' => 'id',
            'label' => 'Broj prijave',
            'attributes' => [
                'readonly' => 'readonly'
            ]
        ],
        'osoba_id' => [
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime (jmbg)',
            'attribute' => 'ime_prezime_jmbg',
            'ajax' => TRUE,
        ],
        'vrsta_posla_id' => [
            'name' => 'vrstaPosla',
            'label' => 'Vesta posla',
            'attribute' => 'naziv',
        ],
        'reg_oblast_id' => [
            'name' => 'regOblast',
            'label' => 'Oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'reg_pod_oblast_id' => [
            'name' => 'regPodOblast',
            'label' => 'Uža oblast',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'zvanje_id' => [
            'name' => 'zvanje',
            'label' => 'Zvanje',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'si_vrsta_id' => [
            'name' => 'siVrsta',
            'label' => 'Vrsta ispita',
            'type' => 'relationship',
            'attribute' => 'naziv',
        ],
        'status_prijave' => [
            'name' => 'status_prijave',
            'label' => 'Status',
            'type' => 'select2',
            'entity' => 'status',
            'attribute' => 'naziv',
        ],
        'strucni_rad' => [
            'name' => 'strucni_rad',
            'label' => 'Stručni rad',
            'type' => 'select_from_array',
            'options' => [
                0 => 'Rad izrađuje sa mentorom',
                1 => 'Ima stručni rad',
            ]
        ],
        'barcode' => [
            'name' => 'strucni_rad',
        ],
        'datum_prijema' => [
            'name' => 'datum_prijema',
            'label' => 'Datum prijema',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr-latn'
            ],
        ],
        'app_korisnik_id' => [
            'name' => 'user',
            'label' => 'Zaveo korisnik',
            'type' => 'relationship',
            'attribute' => 'name',
        ],
        'zavodni_broj' => [
            'name' => 'zavodni_broj',
            'label' => 'Zavodni broj',
        ],
        'tema' => [
            'name' => 'tema',
        ],
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
            'attributes' => [
                'readonly' => 'readonly'
            ]
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažurirana',
            'type' => 'datetime',
            'format' => 'DD.MM.Y. HH:mm:ss',
            'attributes' => [
                'readonly' => 'readonly'
            ]
        ],
    ];

    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\SiPrijava');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/siprijava');
        CRUD::setEntityNameStrings('siprijava', 'Prijave Stručni ispit');
//        CRUD::setTitle('some string', 'create'); // set the Title for the create action
//        CRUD::setHeading('some string', 'create'); // set the Heading for the create action
//        CRUD::setSubheading('some string', 'create');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete']);
        }
        CRUD::enableExportButtons();
//        CRUD::enableDetailsRow();
    }

    protected function setupListOperation()
    {
        $this->crud->setColumns($this->column_deffinition_array);

        $this->crud->removeColumns(['strucni_rad', 'user', 'barcode', 'created_at', 'updated_at']);

        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    });
                }
            }
        ]);


        /*
         *  Filter definition section
         */
        // select2_multiple filter
        $this->crud->addFilter([
            'name' => 'vrsta_posla',
            'type' => 'select2_multiple',
            'label' => 'Vrsta posla'
        ], function () {
            return VrstaPosla::all()->pluck('naziv', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'vrsta_posla_id', json_decode($values));
        });

        $this->crud->addFilter([
            'name' => 'oblast',
            'type' => 'select2_multiple',
            'label' => 'Stručna oblast'
        ], function () {
            return RegOblast::all()->pluck('naziv', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'reg_oblast_id', json_decode($values));
        });

        $this->crud->addFilter([
            'name' => 'podoblast',
            'type' => 'select2_multiple',
            'label' => 'Uža stručna oblast'
        ], function () {
            return RegPodoblast::all()->pluck('naziv', 'id')->toArray();
        }, function ($values) { // if the filter is active
            $this->crud->addClause('whereIn', 'reg_pod_oblast_id', json_decode($values));
        });

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], function () {
            return SiPrijava::existingStatuses();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_prijave', $value);
            });

        $this->crud->addFilter([
            'name' => 'vrsta_ispita',
            'type' => 'dropdown',
            'label' => 'Vrsta ispita'
        ], function () {
            return SiVrsta::all()->pluck('naziv', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'si_vrsta_id', $value);
            });

        $this->crud->addFilter([
            'name' => 'sekcija',
            'type' => 'dropdown',
            'label' => 'Grupa zvanja'
        ], function () {
            return Sekcija::orderBy('id')->pluck('naziv', 'id')->toArray();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('whereHas', 'zvanje', function ($q) use ($value) {
                    $q->where('zvanje_grupa_id', $value);
                });
            });

        // daterange filter
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'from_to',
            'label' => 'Rok za prijavu'
        ],
            FALSE,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
                $this->crud->addClause('where', 'datum_prijema', '>=', date('Y-m-d', strtotime($dates->from)));
                $this->crud->addClause('where', 'datum_prijema', '<=', date('Y-m-d', strtotime($dates->to)));
            });


    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', FALSE);

        $this->crud->setColumns($this->column_deffinition_array);

        $this->crud->setColumnDetails('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
            ],
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(SiPrijavaRequest::class);
        $this->crud->addFields($this->field_deffinition_array);

        $this->crud->modifyField('status_prijave', [
            'options' => (function ($query) {
                return $query->where('log_status_grupa_id', STRUCNI_ISPIT)->get();
            }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

/*    protected function showDetailsRow($id)
    {
        $this->crud->hasAccessOrFail('details_row');

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_details_row', $this->data);
    }*/
}
