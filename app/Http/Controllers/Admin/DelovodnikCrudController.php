<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DelovodnikRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DelovodnikCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DelovodnikCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Delovodnik::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/delovodnik');
        CRUD::setEntityNameStrings('delovodnik', 'delovodnik');
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

        $this->crud->setColumnDetails('organizaciona_jedinica_id', [
            'name' => 'organizaciona_jedinica_id',
            'type' => 'select',
            'label' => 'Org.jed.',
            'entity' => 'delovodnikOrganizacioneJedinice',
            'attribute' => 'oznaka',
            'model' => 'App\Models\DelovodnikOrganizacioneJedinice',
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
        CRUD::setValidation(DelovodnikRequest::class);

        CRUD::setFromDb(); // fields

        $this->crud->removeField('organizaciona_jedinica_id', 'update/create/both');
        $this->crud->removeField('brojac', 'update/create/both');

        $this->crud->addField([
            'label' => "Org.jed.",
            'type' => 'select2',
            'name' => 'organizaciona_jedinica_id', // the db column for the foreign key
            'entity' => 'delovodnikOrganizacioneJedinice', // the method that defines the relationship in your Model
            'attribute' => 'oznaka', // foreign key attribute that is shown to user
            'model' => "App\Models\DelovodnikOrganizacioneJedinice" // foreign key model
        ]);




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
