<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ZahtevLicencaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ZahtevCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ZahtevLicencaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\ZahtevLicenca::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/zahtev');
        CRUD::setEntityNameStrings('zahtevlicenca', 'Zahtevi Licence');
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
        CRUD::column('osoba');
        CRUD::column('licencatip');
//        CRUD::column('strucniispit');
//        CRUD::column('referenca1');
//        CRUD::column('referenca2');
//        CRUD::column('pecat');
        CRUD::column('datum');
        CRUD::column('status');
//        CRUD::column('razlog');
        CRUD::column('prijem');
//        CRUD::column('preporuka2');
//        CRUD::column('preporuka1');
//        CRUD::column('mestopreuzimanja');
        CRUD::column('status_pregleda');
        CRUD::column('datum_statusa_pregleda');
        CRUD::column('prijava_clan_id');
        CRUD::column('licenca_broj');
        CRUD::column('licenca_broj_resenja');
        CRUD::column('licenca_datum_resenja');
        CRUD::column('created_at');
        CRUD::column('updated_at');

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
        CRUD::setValidation(ZahtevLicencaRequest::class);

        CRUD::field('osoba');
        CRUD::field('licencatip');
        CRUD::field('strucniispit');
        CRUD::field('referenca1');
        CRUD::field('referenca2');
        CRUD::field('pecat');
        CRUD::field('datum');
        CRUD::field('status');
        CRUD::field('razlog');
        CRUD::field('prijem');
        CRUD::field('preporuka2');
        CRUD::field('preporuka1');
        CRUD::field('mestopreuzimanja');
        CRUD::field('status_pregleda');
        CRUD::field('datum_statusa_pregleda');
        CRUD::field('prijava_clan_id');
        CRUD::field('licenca_broj');
        CRUD::field('licenca_broj_resenja');
        CRUD::field('licenca_datum_resenja');

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
