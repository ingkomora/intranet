<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FunkcijaTipRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FunkcijaTipCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FunkcijaTipCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    protected
        $column_definition_array = [
        'id',
        'naziv',
//        'naziv_cir',
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ],
        $field_definition_array = [
        'id' => [
            'name' => 'id',
            'attributes' => [
//                'readonly' => 'readonly',
                'disabled' => 'disabled'
            ],
        ],
        'naziv',
        'naziv_cir',
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\FunkcijaTip::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/funkcija-tip');
        CRUD::setEntityNameStrings('tip funkcije', 'tipovi funkcija');

        $this->crud->setColumns($this->column_definition_array);
        $this->crud->addClause('orderBy', 'id');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'update', 'delete']);
        }

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

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
        CRUD::setValidation(FunkcijaTipRequest::class);
        $this->crud->addFields($this->field_definition_array);


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
