<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OsiguranjeRequest;
use App\Http\Requests\OsobaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


/**
 * Class OsiguranjeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class OsiguranjeCrudController extends CrudController {
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    public function setup() {
        $this->crud->setModel('App\Models\Osiguranje');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/osiguranje');
        $this->crud->setEntityNameStrings('osiguranje', 'osiguranja');

        $this->crud->setColumns(['id','osiguravajuca_kuca_mb', 'ugovarac_osiguranja_mb', 'polisa_broj', 'polisaPokrice', 'polisa_pokrice_id', 'polisa_datum_zavrsetka', 'statusPolise', 'statusDokumenta', 'napomena']);

        $this->crud->enableDetailsRow();
        $this->crud->enableExportButtons();

/*        $this->crud->modifyColumn('polisa_pokrice_id', [
            'name' => 'polisa_pokrice_id',
            'type' => 'select2',
            'label' => 'Pokriæe polise',
            'entity' => 'polisaPokrice',
//            'attribute' => 'naziv',
//            'model' => 'App\Models\OsiguranjePolisaPokrice',
        ]);*/


        /*        $this->crud->addField([
                    'type' => 'relationship_count',
                    'name' => 'osobe',
                    'label' => 'Osobe',
        //            'ajax' => true,
        //            'attribute' => 'ime_prezime_jmbg'
        //            'attribute' => 'id'  //accessor u Osoba modelu
                ]);*/


    }

    protected function setupListOperation() {

        $this->crud->setColumnDetails('statusPolise',[
            'name' => 'statusPolise',
            'type' => 'select',
            'label' => 'Status polise',
            'entity' => 'statusPolise',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
        ]);

        $this->crud->setColumnDetails('statusDokumenta',[
            'name' => 'statusDokumenta',
            'type' => 'select',
            'label' => 'Status dokumenta',
            'entity' => 'statusDokumenta',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
        ]);

        $this->crud->setColumnDetails('polisaPokrice',[
            'name' => 'polisaPokrice',
            'type' => 'select',
            'label' => 'Pokriæe polise',
            'entity' => 'polisaPokrice',
            'attribute' => 'naziv',
            'model' => 'App\Models\OsiguranjePolisaPokrice',
        ]);


        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'status_polise_id',
            'label' => 'Status polise'
        ], function () {
            return [
                0 => 'Neaktivna',
                1 => 'Aktivna'
            ]; // the simple filter has no values, just the "Draft" label specified above
        }, function ($value) { // if the filter is active (the GET parameter "draft" exits)
            $this->crud->addClause('where', 'status_polise_id', $value);
        }
        );

        // TODO: remove setFromDb() and manually define Columns, maybe Filters
//        $this->crud->setFromDb();
    }

    protected function setupCreateOperation() {
//        $this->crud->setColumns(['osiguravajuca_kuca_mb', 'ugovarac_osiguranja_mb', 'polisa_broj', 'polisaPokrice', 'polisa_pokrice_id', 'polisa_datum_zavrsetka', 'status_polise_id', 'statusDokumenta', 'napomena']);

        $this->crud->setValidation(OsiguranjeRequest::class);

        /*        $this->crud->addField([
        //        $this->crud->setColumnDetails('polisa_pokrice_id',[
                    'name' => 'polisaPokrice',
                    'type' => 'relationship',
                    'label' => 'Pokriæe polise',
                    'attribute' => 'naziv',
                ]);*/

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'osobe',
            'label' => 'Osobe',
            'ajax' => true,
//            'attribute' => 'ime_prezime_jmbg'  //accessor u Osoba modelu
            'attribute' => 'id'  //accessor u Osoba modelu
        ]);
        $this->crud->addField([
            'name' => 'polisaPokrice',
            'type' => 'relationship',
            'label' => 'Pokriæe polise',
            'attribute' => 'naziv',
        ]);
        $this->crud->addField([
            'name' => 'statusDokumenta',
            'type' => 'relationship',
            'label' => 'Status dokumenta',
            'attribute' => 'naziv',
        ]);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation() {
        $this->setupCreateOperation();
    }

    public function fetchOsobe() {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
            'searchable_attributes' => ['id'],
            'paginate' => 10, // items to show per page
//            'query' => function($model) {
//                return $model->active();
//            } // to filter the results that are returned
        ]);

    }

    protected function showDetailsRow($id) {
//        $this->crud->hasAccessOrFail('details_row');//???

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
//dd($this->data['entry']->osobe);
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
//        return view('crud.details_row', $this->data);
        return view('crud::osiguranje_details_row', $this->data);
    }
}
