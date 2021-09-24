<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RegOblastRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RegOblastCrudController
 * @package App\Http\Controllers\Admin
// * @property-read CrudPanel $crud
 */
class RegOblastCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel('App\Models\RegOblast');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/regoblast');
        CRUD::setEntityNameStrings('regoblast', 'reg_oblasti');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create','delete','update']);
        }

        $this->crud->enableExportButtons();
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        CRUD::setFromDb();
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(RegOblastRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        CRUD::setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
