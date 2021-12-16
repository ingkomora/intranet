<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StatusRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

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

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
        }

        $this->crud->setColumns([
            'id',
            'naziv',
            'log_status_grupa_id' => [
                'name' => 'log_status_grupa_id',
                'type' => 'select',
                'entity' => 'StatusGrupa',
                'label' => 'Status grupa',
                'attribute' => 'naziv',
            ],
            'napomena',
            'const'
        ]);

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

        $this->crud->modifyColumn('log_status_grupa_id', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('logstatusgrupa/' . $related_key . '/show');
                },
            ],
        ]);

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'log_status_grupa_id',
            'label' => 'Status grupa'
        ], function () {
            return \App\Models\LogStatusGrupa::all()->keyBy('id')->pluck('naziv', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'log_status_grupa_id', $value);
        });

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

        $this->crud->addFields([
            'id' => [
                'name' => 'id',
                'attributes' => [
                    'readonly' => 'readonly',
                ]
            ],
            'naziv',
            'log_status_grupa_id' => [
                'name' => 'statusGrupa',
                'type' => 'relationship',
                'label' => 'Status grupa',
                'attribute' => 'naziv',
                'ajax' => TRUE,
                'data_source' => backpack_url('monster/fetch/contact-number'),
                'inline_create' => [
                    'entity' => 'statusGrupa',
                    'force_select' => TRUE,
//                    'modal_route' => route('logstatusgrupa-inline-create'),
//                    'create_route' => route('logstatusgrupa-inline-create-save'),
                ],
            ],
            'napomena',
            'const'
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

    protected function fetchStatusGrupa()
    {
        return $this->fetch(\App\Models\LogStatusGrupa::class);
    }
}
