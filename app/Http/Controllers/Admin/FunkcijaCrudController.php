<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FunkcijaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FunkcijaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FunkcijaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    protected
        $column_definition_array = [
        'id',
        'naziv',
        'naziv_full' => [
            'name' => 'naziv_full',
            'label' => 'Pun naziv',
        ],
        'naziv_cir' => [
            'name' => 'naziv_cir',
            'label' => 'Naziv ćirilicom',
        ],
        'naziv_full_cir' => [
            'name' => 'naziv_full_cir',
            'label' => 'Pun naziv ćirilicom',
        ],
        'funkcija_tip_id' => [
//            'name' => 'funkcija_tip_id',
            'name' => 'funkcijaTip',
//            'type' => 'select',
            'type' => 'relationship',
            'label' => 'Tip funkcije',
//            'entity' => 'funkcijaTip',
//            'attribute' => 'naziv',
//            'model' => 'App\Models\FunkcijaTip',
        ],
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
        'naziv_full',
        'naziv_cir',
        'naziv_full_cir',
        'funkcija_tip_id',
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
        CRUD::setModel(\App\Models\Funkcija::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/funkcija');
        CRUD::setEntityNameStrings('funkciju', 'funkcije');

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
        $this->crud->removeColumns(['naziv_cir', 'naziv_full_cir', 'created_at', 'updated_at']);

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(FunkcijaRequest::class);
        $this->crud->addFields($this->field_definition_array);
        $this->crud->removeFields(['id', 'created_at', 'updated_at']);

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
