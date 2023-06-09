<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LicencaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LicencaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LicencaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Licenca::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/licenca');
        CRUD::setEntityNameStrings('licenca', 'licence');

        $this->crud->setColumns(['id', 'licencatip', 'osoba', 'zahtev', 'datumuo', 'status', 'broj_resenja', 'created_at', 'updated_at']);

        $this->crud->setColumnDetails('osoba', [
            'name' => 'osoba',
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'osobaId',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
        ]);

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create']);
        }

        if (backpack_user()->hasRole('sluzba_rk')) {
            $this->crud->denyAccess(['update']);
        }

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
        CRUD::column('id');
        $this->crud->setColumnDetails('id', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
//                    dd($column);
                    $query->whereIn('id', $searchTermArray)->orderBy('id');
                }
            }
        ]);


        $this->crud->setColumnDetails('osoba', [
            'name' => 'osoba',
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'osobaId',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm . '%')
                            ->orWhere('id', 'ilike', $searchTerm . '%');
                    });
                }
            }
        ]);
//        CRUD::column('osoba');
        CRUD::column('licencatip');
        CRUD::column('datum');
        CRUD::column('zahtev');
        CRUD::column('datumuo');
        CRUD::column('datumobjave');
        CRUD::column('status');
        CRUD::column('datumukidanja');
        CRUD::column('razlogukidanja');
        CRUD::column('preuzeta');
        CRUD::column('mirovanje');
        CRUD::column('prva');
        CRUD::column('prijava_clan_id');
        CRUD::column('broj_resenja');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::column('napomena');
        CRUD::column('status_old');

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
        CRUD::setValidation(LicencaRequest::class);

        CRUD::field('osoba');
        CRUD::field('datum');
        CRUD::field('zahtev');
        CRUD::field('datumuo');
        CRUD::field('datumobjave');
        CRUD::field('status');
        CRUD::field('datumukidanja');
        CRUD::field('razlogukidanja');
        CRUD::field('preuzeta');
        CRUD::field('mirovanje');
        CRUD::field('prva');
//        CRUD::field('prijava_clan_id');
        CRUD::field('broj_resenja');
        CRUD::field('licencatip');
        CRUD::field('napomena');
//        CRUD::field('status_old');

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
}
