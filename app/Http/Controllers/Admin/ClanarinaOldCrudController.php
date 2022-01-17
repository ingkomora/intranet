<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClanarinaOldRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClanarinaOldCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClanarinaOldCrudController extends CrudController
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
        CRUD::setModel(\App\Models\ClanarinaOld::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/clanarina-old');
        CRUD::setEntityNameStrings('clanarina old', 'clanarina olds');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('brlicence');
        CRUD::column('azurirao_korisnik');
        CRUD::column('azurirao_admin');
        CRUD::column('rokzanaplatu');
        CRUD::column('iznoszanaplatu');
        CRUD::column('iznosuplate');
        CRUD::column('pretplata');
        CRUD::column('napomena');
        CRUD::column('potvrdaposlata');
        CRUD::column('datumslanjapotvrde');
        CRUD::column('datumazuriranja');
        CRUD::column('datumazuriranja_admin');

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
        CRUD::setValidation(ClanarinaOldRequest::class);

        CRUD::field('brlicence');
        CRUD::field('azurirao_korisnik');
        CRUD::field('azurirao_admin');
        CRUD::field('rokzanaplatu');
        CRUD::field('iznoszanaplatu');
        CRUD::field('iznosuplate');
        CRUD::field('pretplata');
        CRUD::field('napomena');
        CRUD::field('potvrdaposlata');
        CRUD::field('datumslanjapotvrde');
        CRUD::field('datumazuriranja');
        CRUD::field('datumazuriranja_admin');

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
