<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ZahtevLicencaRequest;
use App\Models\Status;
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
                //ZA SADA SE ZAHTEVI NE ZAVODE DOK SE NE PREPRAVI APLIKACIJA ZA PROMENU PODATAKA
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

        if (backpack_user()->hasPermissionTo('zavedi') and $this->allowRegister) {
            $this->crud->denyAccess(['create', 'update']);
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
        CRUD::column('osobaId')->attribute('ime_prezime_jmbg');
        CRUD::column('licencatip');
//        CRUD::column('strucniispit');
//        CRUD::column('referenca1');
//        CRUD::column('referenca2');
//        CRUD::column('pecat');
        CRUD::column('datum')->type('date')->format('DD.MM.Y.');
        CRUD::column('statusId')->attribute('naziv')->label('Status');
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

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-show-entries
     * @return void
     */
    protected function setupShowOperation()
    {

        CRUD::column('id');
        CRUD::column('osobaId')->attribute('ime_prezime_jmbg');
        CRUD::column('tipLicence')->attribute('gen_tip_naziv')->limit(500);
        CRUD::column('licenca_broj');
        CRUD::column('licenca_broj_resenja');
        CRUD::column('licenca_datum_resenja')->type('date')->format('DD.MM.Y.');
        CRUD::column('documents')->type('relationship')->attribute('category_type_name_status_registry_number');
        CRUD::column('datum')->type('date')->format('DD.MM.Y.');
        CRUD::column('prijem')->type('date')->format('DD.MM.Y.');
        CRUD::column('statusId')->attribute('naziv')->label('Status');
        CRUD::column('razlog');
        CRUD::column('strucniispit');
        CRUD::column('preporuka2');
        CRUD::column('preporuka1');
        CRUD::column('mestopreuzimanja');
        CRUD::column('status_pregleda');
        CRUD::column('datum_statusa_pregleda');
        CRUD::column('prijava_clan_id');
        CRUD::column('referenca1');
        CRUD::column('referenca2');
        CRUD::column('pecat');

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

        CRUD::field('osobaId')
            ->ajax(TRUE)
            ->attribute('ime_prezime_jmbg');
        CRUD::field('licencatip')->attribute('gen_tip_naziv');
        CRUD::field('licenca_broj');
        CRUD::field('licenca_broj_resenja');
        CRUD::field('licenca_datum_resenja')->type('date_picker')
            ->date_picker_options([
                'todayBtn' => 'linked',
                'format' => 'dd.mm.yyyy.',
                'language' => 'sr_latin',
            ]);
        CRUD::field('documents')
            ->type('relationship')
            ->ajax(TRUE)
            ->attribute('category_type_name_status_registry_number');
        CRUD::field('datum')
            ->type('date_picker')
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
                return $query->orderBy('id')->where('log_status_grupa_id', 11)->get(); // samo grupa statusa "Zahtevi"
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
