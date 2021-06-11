<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PrijavaSiStaraRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaSiStaraCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PrijavaSiStaraCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel("\App\Models\PrijavaSiStara");
        CRUD::setRoute(config('backpack.base.route_prefix') . '/prijavasistara');
        CRUD::setEntityNameStrings('prijavasistara', 'Stare Prijave za stručni ispit');

        $this->crud->setColumns(['id', 'osoba', 'oblast', 'stucniispit', 'datum', 'status', 'razlog', 'tema', 'prijem', 'prijem_user', 'zavodni_broj']);

/*        $this->crud->setColumnDetails('osoba', [
            'name' => 'osoba',
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'osobaId',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\OsobaSi',
        ]);*/
        $this->crud->setColumnDetails('oblast', [
            'name' => 'oblast',
            'type' => 'select',
            'label' => 'Strucna oblast',
            'entity' => 'oblastSiStruka',
            'attribute' => 'naziv',
            'model' => 'App\Models\StrucniIspitStruka',
        ]);
    }


    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // columns

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
        CRUD::setValidation(PrijavaSiStaraRequest::class);

        CRUD::setFromDb(); // fields

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
