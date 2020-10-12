<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FirmaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FirmaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class FirmaCrudController extends CrudController {
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;


    public function setup() {
        $this->crud->setModel('App\Models\Firma');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/firma');
        $this->crud->setEntityNameStrings('firma', 'firme');

        $this->crud->setColumns(['mb', 'pib', 'naziv', 'drzava', 'mesto', 'pb', 'adresa', 'opstina', 'fax', 'telefon', 'email', 'web', 'created_at', 'updated_at']);

        $this->crud->enableExportButtons();

    }

    protected function setupListOperation() {

        $this->crud->setColumnDetails('opstina', [
            'name' => 'opstina',
            'type' => 'select',
            'label' => 'Opština',
            'entity' => 'opstina',
            'attribute' => 'ime',
            'model' => 'App\Models\Opstina',
        ]);
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
//        $this->crud->setFromDb();
    }

    protected function setupCreateOperation() {
        $this->crud->setValidation(FirmaRequest::class);

        $this->crud->field('mb');
        $this->crud->field('pib');
        $this->crud->field('naziv');
        $this->crud->field('drzava');
        $this->crud->field('mesto');
        $this->crud->field('pb');
        $this->crud->field('adresa');
        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'opstina',
            'label' => 'Opština'
        ]);
        $this->crud->field('fax');
        $this->crud->field('telefon');
        $this->crud->field('email');
        $this->crud->field('web');
    }

    protected function setupUpdateOperation() {
        $this->setupCreateOperation();
    }
}
