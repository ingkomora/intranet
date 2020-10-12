<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StatusRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StatusCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StatusCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Status::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/status');
        CRUD::setEntityNameStrings('status', 'Statusi');

        $this->crud->setColumns(['id', 'naziv', 'log_status_grupa_id', 'napomena', 'const']);


        $this->crud->setColumnDetails('log_status_grupa_id', [
            'name' => 'log_status_grupa_id',
            'type' => 'select',
            'label' => 'Status',
            'entity' => 'statusGrupa',
            'attribute' => 'naziv_id',
            'model' => 'App\Models\LogStatusGrupa',
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
        CRUD::column('naziv');
        CRUD::column('log_status_grupa_id');
        CRUD::column('napomena');
        CRUD::column('const');

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'log_status_grupa_id',
            'label' => 'Status grupa'
        ], function () {
            return \App\Models\LogStatusGrupa::all()->keyBy('id')->pluck('naziv', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'log_status_grupa_id', $value);
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
        CRUD::setValidation(StatusRequest::class);

        CRUD::field('naziv');
//        CRUD::field('log_status_grupa_id');
        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'statusGrupa',
            'label' => 'Status grupa'
        ]);
        CRUD::field('napomena');
        CRUD::field('const');

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
