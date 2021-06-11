<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OsobaSiRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OsobaSiCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OsobaSiCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\OsobaSi::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/osobasi');
        CRUD::setEntityNameStrings('osobasi', 'Osobe stručni ispit');

        $this->crud->setColumns(['id', 'ime', 'prezime', 'zvanjeId', 'opstinaId', 'mobilnitel', 'kontaktemail', 'firmanaziv']);

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

        $this->crud->setColumnDetails('zvanjeId', [
            'name' => 'zvanjeId',
            'type' => 'select',
            'label' => 'Zvanje',
            'entity' => 'zvanjeId',
            'attribute' => 'skrnaziv',
            'model' => 'App\Models\Zvanje',
        ]);

        $this->crud->setColumnDetails('opstinaId', [
            'name' => 'opstinaId',
            'type' => 'select',
            'label' => 'Opština',
            'entity' => 'opstinaId',
            'attribute' => 'ime',
            'model' => 'App\Models\Opstina',
        ]);

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
        CRUD::setValidation(OsobaSiRequest::class);

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
