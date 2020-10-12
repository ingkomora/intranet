<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PrijavaClanstvoRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaClanstvoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PrijavaClanstvoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\PrijavaClanstvo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/prijavaclanstvo');
        CRUD::setEntityNameStrings('prijavaclanstvo', 'prijava_clanstvos');

        $this->crud->setColumns(['id', 'osoba_id', 'datum_prijema', 'zavodni_broj', 'barcode', 'broj_odluke_uo', 'status_id', 'napomena']);

        $this->crud->setColumnDetails('osoba_id', [
            'name' => 'osoba_id',
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'osoba',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
        ]);

        $this->crud->setColumnDetails('status_id', [
            'name' => 'status_id',
            'type' => 'select',
            'label' => 'Status',
            'entity' => 'status',
            'attribute' => 'naziv_id',
            'model' => 'App\Models\Status',
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
        CRUD::setFromDb(); // columns

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
        CRUD::setValidation(PrijavaClanstvoRequest::class);

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
