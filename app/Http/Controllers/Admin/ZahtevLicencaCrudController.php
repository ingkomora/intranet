<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ZahtevLicencaRequest;
use App\Models\Document;
use App\Models\LicencaTip;
use App\Models\RegOblast;
use App\Models\RegPodoblast;
use App\Models\Status;
use App\Models\VrstaPosla;
use App\Models\ZahtevLicenca;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ZahtevCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ZahtevLicencaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use Operations\RegisterRequestBulkOperation;

    protected $allow_register = FALSE;
    protected $segment;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ZahtevLicenca::class);

        $this->segment = \Request::segment(2);

        switch ($this->segment) {
            case 'zahtevlicenca':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/zahtevlicenca');
                break;
            case 'registerrequestlicence':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestlicence');
                $this->allow_register = TRUE;
                break;
        }

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }

        if (backpack_user()->hasPermissionTo('zavedi') and $this->allow_register) {
            $this->crud->allowAccess(['create']);
        }

        CRUD::enableDetailsRow();
        CRUD::enableExportButtons();

        $this->crud->set('show.setFromDb', FALSE);

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        if ($this->segment == 'zahtevlicenca') {
            $this->crud->denyAccess(['registerrequestbulk']);
            $this->crud->disableBulkActions();
        }

        CRUD::column('id');
        CRUD::column('statusId')->attribute('naziv')->label('Status');
        CRUD::column('osoba')->label('jmbg');
        CRUD::column('osobaId')->label('Ime i prezime')->attribute('full_name');
        CRUD::column('tipLicence')->label('Oznaka (tip)')->attribute('oznaka_tip');
        CRUD::column('documents')->type('relationship')->attribute('category_type_name_status_registry_date');
//        CRUD::column('strucniispit');
//        CRUD::column('referenca1');
//        CRUD::column('referenca2');
//        CRUD::column('pecat');
//        CRUD::column('datum')->type('date')->format('DD.MM.Y.');
//        CRUD::column('razlog');
//        CRUD::column('prijem')->type('date')->format('DD.MM.Y.');
//        CRUD::column('preporuka2');
//        CRUD::column('preporuka1');
//        CRUD::column('mestopreuzimanja');
//        CRUD::column('status_pregleda');
//        CRUD::column('datum_statusa_pregleda');
//        CRUD::column('prijava_clan_id');
//        CRUD::column('licenca_broj');
//        CRUD::column('licenca_broj_resenja');
//        CRUD::column('licenca_datum_resenja')->type('date')->format('DD.MM.Y.');

        $this->crud->modifyColumn('id', [
            'name' => 'id',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
                    $query->whereIn('id', $searchTermArray);
                } else {
                    $query->orWhere('id', 'ilike', $searchTerm . '%');
                }
            }
        ]);

        $this->crud->setColumnDetails('osobaId', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('osobaId.licence', function ($q) use ($column, $searchTerm) {
                        $q
                            ->where('id', 'ilike', $searchTerm . '%');
                    })
                        ->orWhereHas('osobaId', function ($q) use ($column, $searchTerm) {
                            $q
                                ->where('id', 'ilike', $searchTerm . '%')
                                ->orWhere('ime', 'ilike', $searchTerm . '%')
                                ->orWhere('prezime', 'ilike', $searchTerm . '%');
                        });
                }
            }
        ]);

        $this->crud->setColumnDetails('statusId', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($related_key) {
                        case REQUEST_CREATED:
                        case REQUEST_SUBMITED:
                        default:
                            return 'btn btn-sm btn-outline-secondary';
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info';
                        case REQUEST_FINISHED:
                            return 'btn btn-sm btn-outline-success';
                        case REQUEST_CANCELED:
                        case REQUEST_PROBLEM:
                            return 'btn btn-sm btn-outline-danger';
                        case PRIJAVA_OTKLJUCANA:
                            return 'btn btn-sm btn-outline-warning';
                    }
                },
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

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'statusId',
            'label' => 'Status'
        ],
            function () {
                return Status::where('log_status_grupa_id', REQUESTS)->orderBy('id')->pluck('naziv', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'status', $value);
            }
        );

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'vrstaPosla',
            'label' => 'Vrsta stručnih poslova'
        ],
            function () {
                return VrstaPosla::orderBy('id')->pluck('naziv', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'vrsta_posla_id', $value);
            }
        );

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'oblast',
            'label' => 'Stručna oblast'
        ],
            function () {
                return RegOblast::orderBy('id')->pluck('naziv', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'reg_oblast_id', $value);
            }
        );

        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'podOblast',
            'label' => 'Uža stručna oblast'
        ],
            function () {
                return RegPodoblast::orderBy('id')->pluck('naziv', 'id')->toArray();
            },
            function ($value) { // if the filter is active
                CRUD::addClause('where', 'reg_pod_oblast_id', $value);
            }
        );

        if ($this->allow_register) {
            $this->crud->addFilter([
                'type' => 'simple',
                'name' => 'active',
                'label' => 'Za zavođenje'
            ],
                FALSE,
                function () { // if the filter is active
                    $this->crud->addClause('where', 'status', REQUEST_SUBMITED); // apply the "active" eloquent scope
                });
        }

    }

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-show-entries
     * @return void
     */
    protected function setupShowOperation()
    {

        CRUD::column('id');
        CRUD::column('osobaId')->label('Osoba')->attribute('ime_prezime_jmbg');
        CRUD::column('tipLicence')->label('Tip licence')->attribute('tip_naziv_oznaka_gen')->limit(500);
        CRUD::column('licenca')->attribute('id');
        CRUD::column('licenca_broj_resenja');
        CRUD::column('licenca_datum_resenja')->type('date')->format('DD.MM.Y.');
        CRUD::column('siPrijava')->attribute('id')->label('Stručni ispit');
        CRUD::column('documents')->type('relationship')->attribute('category_type_name_status_registry_number_registry_date')->limit(500);
        CRUD::column('datum')->type('date')->format('DD.MM.Y.');
//        CRUD::column('prijem')->type('date')->format('DD.MM.Y.');
        CRUD::column('statusId')->attribute('naziv')->label('Status');
//        CRUD::column('prijava_clan_id');
//        CRUD::column('razlog');
//        CRUD::column('strucniispit');
//        CRUD::column('preporuka2');
//        CRUD::column('preporuka1');
//        CRUD::column('mestopreuzimanja');
//        CRUD::column('status_pregleda');
//        CRUD::column('datum_statusa_pregleda');
        CRUD::column('reference')->attribute('data_reference_to_array');
//        CRUD::column('referenca2');
//        CRUD::column('pecat');

        $this->crud->setColumnDetails('reference', [
            'wrapper' => [
//                'href' => function ($crud, $column, $entry, $related_key) {
//                    return backpack_url('document/' . $related_key . '/show');
//                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
            ]
        ]);

        $this->crud->setColumnDetails('osobaId', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info',
            ]
        ]);

        $this->crud->setColumnDetails('licenca', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('licenca/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info',
                'target' => '_blank',
            ]
        ]);

        $this->crud->setColumnDetails('siPrijava', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('siprijava/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info',
                'target' => '_blank',
            ]
        ]);

        $this->crud->setColumnDetails('statusId', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($related_key) {
                        case REQUEST_CREATED:
                        case REQUEST_SUBMITED:
                        default:
                            return 'btn btn-sm btn-outline-secondary';
                        case REQUEST_IN_PROGRESS:
                            return 'btn btn-sm btn-outline-info';
                        case REQUEST_FINISHED:
                            return 'btn btn-sm btn-outline-success';
                        case REQUEST_CANCELED:
                        case REQUEST_PROBLEM:
                            return 'btn btn-sm btn-outline-danger';
                        case PRIJAVA_OTKLJUCANA:
                            return 'btn btn-sm btn-outline-warning';
                    }
                },
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


    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ZahtevLicencaRequest::class);

        CRUD::field('osobaId')->label('Ime prezime')->size(4)->ajax(TRUE)
            ->hint('Osobu možete tražiti po imenu i/ili prezimenu, jmbg ili broju licence')
            ->attribute('ime_prezime_jmbg');
        CRUD::field('zvanje')->size(4)->attribute('naziv');
        CRUD::field('statusId')->label('Status')->size(4)->attribute('naziv');
        CRUD::field('tipLicence')->label('Tip licence')->size(3)->attribute('gen_tip_oznaka');
        CRUD::field('licenca_broj')->size(3);
        CRUD::field('licenca_broj_resenja')->label('Licenca broj rešenja')->size(3);
        CRUD::field('licenca_datum_resenja')->label('Licenca datum rešenja')->size(3)->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);
        CRUD::field('vrstaPosla')->attribute('naziv')->size(4);
        CRUD::field('regOblast')->attribute('naziv')->size(4);
        CRUD::field('regPodoblast')->attribute('naziv')->size(4);
//        CRUD::field('documents')
//            ->type('relationship')
//            ->ajax(TRUE)
//            ->attribute('category_type_name_status_registry_number')
//            ->attributes(['disabled' => 'disabled']);
//        CRUD::field('prijem')
//            ->type('date_picker')
//            ->date_picker_options([
//                'todayBtn' => 'linked',
//                'format' => 'dd.mm.yyyy.',
//                'language' => 'sr_latin',
//            ]);
//        CRUD::field('strucniispit');
//        CRUD::field('referenca1')->size(3);
//        CRUD::field('referenca2')->size(3);
//        CRUD::field('reference')->size(3);
//        CRUD::field('preporuka1')->size(3);
//        CRUD::field('preporuka2')->size(3);
        CRUD::field('datum')->label('Datum kreiranja zahteva')->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);
//        CRUD::field('pecat');
//        CRUD::field('razlog');
//        CRUD::field('mestopreuzimanja');
//        CRUD::field('status_pregleda');
//        CRUD::field('datum_statusa_pregleda')->type('date_picker')
//            ->date_picker_options([
//                'todayBtn' => 'linked',
//                'format' => 'dd.mm.yyyy.',
//                'language' => 'sr_latin',
//            ]);


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
        $this->crud->modifyField('statusId', [
            'options' => (function ($query) {
                return $query->orderBy('id')->where('log_status_grupa_id', REQUESTS)->get(); // samo grupa statusa "Zahtevi"
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
        $this->data['entry'] = $this->crud->getEntry($id)->osobaId;
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_clanarina_details_row', $this->data);
    }
}
