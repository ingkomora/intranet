<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestCategoryRequest;
use App\Models\RequestCategory;
use App\Models\RequestCategoryType;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RequestCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestCategoryCrudController extends CrudController
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
        CRUD::setModel(\App\Models\RequestCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/request-category');
        CRUD::setEntityNameStrings('request category', 'request categories');

        CRUD::set('show.setFromDb', FALSE);

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
        CRUD::column('id');
        CRUD::column('name')->limit(100);
        CRUD::column('note');
        CRUD::column('requestCategoryType')->type('relationship');
        CRUD::column('status_id')->type('relationship')->attribute('naziv');

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'requestcategorytype',
            'label' => 'Tip kategorije'
        ], function () {
            return RequestCategoryType::orderBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) {
            $this->crud->addClause('where', 'request_category_type_id', $value);
        });

    }

    /**
     * Define what happens when the Show operation is loaded.
     *
     * @return void
     */
    protected function setupShowOperation()
    {
        CRUD::column('id');
        CRUD::column('name')->limit(100);
        CRUD::column('note');
        CRUD::column('requestCategoryType')->type('relationship');
        CRUD::column('status_id')->type('relationship')->attribute('naziv');

        $this->crud->setColumnDetails('requestCategoryType', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('request-category-type/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
                'target' => '_blank',
            ]
        ]);
        $this->crud->setColumnDetails('status_id', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('status/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
                'target' => '_blank',
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
        CRUD::setValidation(RequestCategoryRequest::class);

        $this->crud->addFields([
//            'id',
            'name',
            'request_category_type_id' => [
                'name' => 'requestCategoryType',
                'type' => 'relationship',
                'label' => 'Tip kategorija zahteva',
            ],
            //TODO samo OPSTI statusi !!!
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],

        ]);

        $this->crud->modifyField('status', [
            'options' => (function ($query) {
                return $query->orderBy('id')->where('log_status_grupa_id', 1)->get(); // samo grupa statusa "Zahtevi"
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
