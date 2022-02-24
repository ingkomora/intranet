<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegistryRequest;
use App\Models\RequestCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RegistryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RegistryCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Registry::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/registry');
        CRUD::setEntityNameStrings('registry', 'registries');

        CRUD::set('show.setFromDb', FALSE);
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
        CRUD::column('registryDepartmentUnit')->attribute('label')->label('rdu');
        CRUD::column('base_number');
        CRUD::column('requestCategories')->limit(500);
        CRUD::column('counter');
        CRUD::column('subject')->limit(500);
//        CRUD::column('copy');
//        CRUD::column('sub_base_number');
        CRUD::column('status_id');
//        CRUD::column('created_at');
//        CRUD::column('updated_at');

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'requestCategory',
            'label' => 'RequestCategory'
        ], function () {
            return RequestCategory::all()->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('whereHas', 'requestCategories', function ($q) use ($value){
                $q->where('request_category_id', $value);
            });
        });

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    protected function setupShowOperation()
    {
        CRUD::column('id');
        CRUD::column('subject');
        CRUD::column('registryDepartmentUnit')->attribute('name_label');
        CRUD::column('requestCategories');
        CRUD::column('base_number');
        CRUD::column('counter');
        CRUD::column('copy');
        CRUD::column('sub_base_number');
        CRUD::column('status')->attribute('naziv');
        CRUD::column('created_at')->type('datetime')->format('DD.MM.Y. HH:mm:ss');
        CRUD::column('updated_at')->type('datetime')->format('DD.MM.Y. HH:mm:ss');

        CRUD::modifyColumn('registryDepartmentUnit', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('registry-department-unit/' . $related_key . '/show');
                },
                'target' => '_blank',
                'class' => 'btn btn-sm btn-outline-info mr-1',

            ],
        ]);

        CRUD::modifyColumn('requestCategories', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('request-category/' . $related_key . '/show');
                },
                'target' => '_blank',
                'class' => 'btn btn-sm btn-outline-info mr-1',

            ],
        ]);

        CRUD::modifyColumn('status', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('status/' . $related_key . '/show');
                },
                'target' => '_blank',
                'class' => 'btn btn-sm btn-outline-info mr-1',

            ],
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
        CRUD::setValidation(RegistryRequest::class);

//        CRUD::field('id');
        CRUD::field('base_number');
        CRUD::field('copy');
        CRUD::field('subject');
        CRUD::field('sub_base_number');
        CRUD::field('registryDepartmentUnit');
        CRUD::field('requestCategories')/*->type('relationship')->pivot(TRUE)->pivotFields(['documentCategories'=> 'docCat'])*/
        ;
        CRUD::field('counter');
        CRUD::field('status')->attribute('naziv');
//        CRUD::field('created_at');
//        CRUD::field('updated_at');

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
