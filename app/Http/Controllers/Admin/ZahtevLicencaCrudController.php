<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ZahtevLicencaRequest;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use Operations\RegisterRequestBulkOperation;

    protected $allowRegister = FALSE;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(ZahtevLicenca::class);

        $segment = \Request::segment(2);

        switch ($segment) {
            case 'zahtevlicenca':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/zahtevlicenca');
//                CRUD::addClause('where', 'request_category_id', 7);
//                $this->requestCategoryType = 1;
//                $this->requestCategory = [10];
                break;
            case 'registerrequestlicence':
                CRUD::setEntityNameStrings('zahtev', 'zahtevi za izdavanje licence');
                CRUD::setRoute(config('backpack.base.route_prefix') . '/registerrequestlicence');
//                CRUD::addClause('whereIn', 'request_category_id', [1, 2]);
//                $this->requestCategoryType = 1;
//                $this->requestCategory = [1, 2];
                $this->allowRegister = TRUE;
                break;
        }

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }
// NE RADI KAD JE ADMIN
        if ((backpack_user()->hasRole('admin') OR backpack_user()->hasPermissionTo('zavedi')) and $this->allowRegister) {
            $this->crud->allowAccess(['registerrequestbulk']);
        }
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
        CRUD::column('id');
        CRUD::column('osobaId')->label('Osoba')->attribute('ime_prezime_jmbg');
        CRUD::column('tipLicence')->label('Oznaka (tip)')->attribute('oznaka_tip');
//        CRUD::column('strucniispit');
//        CRUD::column('referenca1');
//        CRUD::column('referenca2');
//        CRUD::column('pecat');
        CRUD::column('datum')->type('date')->format('DD.MM.Y.');
        CRUD::column('statusId')->attribute('naziv')->label('Status');
//        CRUD::column('razlog');
        CRUD::column('prijem')->type('date')->format('DD.MM.Y.');
//        CRUD::column('preporuka2');
//        CRUD::column('preporuka1');
//        CRUD::column('mestopreuzimanja');
//        CRUD::column('status_pregleda');
//        CRUD::column('datum_statusa_pregleda');
//        CRUD::column('prijava_clan_id');
//        CRUD::column('licenca_broj');
//        CRUD::column('licenca_broj_resenja');
//        CRUD::column('licenca_datum_resenja')->type('date')->format('DD.MM.Y.');
        CRUD::column('documents')->type('relationship')->attribute('category_type_name_status_registry_number');

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

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
            ]
        ]);

        $this->crud->modifyColumn('statusId', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    if ($entry->status == REQUEST_SUBMITED) {
                        return 'text-success';
                    }
                }
            ]
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

        if ($this->allowRegister) {
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
        CRUD::column('tipLicence')->label('Licenca')->attribute('tip_naziv_oznaka_gen')->limit(500);
        CRUD::column('licenca');
        CRUD::column('licenca_broj_resenja');
        CRUD::column('licenca_datum_resenja')->type('date')->format('DD.MM.Y.');
        CRUD::column('documents')->type('relationship')->attribute('category_type_name_status_registry_number');
        CRUD::column('datum')->type('date')->format('DD.MM.Y.');
        CRUD::column('prijem')->type('date')->format('DD.MM.Y.');
        CRUD::column('statusId')->attribute('naziv')->label('Status');
//        CRUD::column('prijava_clan_id');
        CRUD::column('razlog');
        CRUD::column('strucniispit');
//        CRUD::column('preporuka2');
//        CRUD::column('preporuka1');
//        CRUD::column('mestopreuzimanja');
//        CRUD::column('status_pregleda');
//        CRUD::column('datum_statusa_pregleda');
        CRUD::column('referenca1');
        CRUD::column('referenca2');
//        CRUD::column('pecat');

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
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

        $this->crud->setColumnDetails('documents', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('document/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
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
        CRUD::setValidation(ZahtevLicencaRequest::class);

        CRUD::field('osobaId')->ajax(TRUE)->attribute('ime_prezime_jmbg');
        CRUD::field('licencatip')->attribute('gen_tip_naziv');
        CRUD::field('licenca_broj');
        CRUD::field('licenca_broj_resenja');
        CRUD::field('licenca_datum_resenja')->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);
/*        CRUD::field('documents')
            ->type('relationship')
            ->ajax(TRUE)
            ->attribute('category_type_name_status_registry_number')
        ;*/
        CRUD::field('datum')->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);
        CRUD::field('statusId')->attribute('naziv');
        CRUD::field('prijem')->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);
        CRUD::field('strucniispit');
        CRUD::field('referenca1');
        CRUD::field('referenca2');
        CRUD::field('pecat');

        CRUD::field('razlog');
        CRUD::field('preporuka2');
        CRUD::field('preporuka1');
        CRUD::field('mestopreuzimanja');
        CRUD::field('status_pregleda');
        CRUD::field('datum_statusa_pregleda')->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);


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
}
