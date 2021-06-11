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
//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
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
        $this->crud->setModel('App\Models\PrijavaClanstvo');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/prijavaclanstvo');
        $this->crud->setEntityNameStrings('prijavaclanstvo', 'prijave clanstvo');
//        $this->crud->addClause('with','zahteviLicence.licenca');
        $this->crud->enableDetailsRow();

        $this->crud->setColumns(['id', 'osoba_id', 'datum_prijema', 'zavodni_broj', 'broj_odluke_uo', 'status_id', 'created_at', 'updated_at']);
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
/*        $this->crud->setColumnDetails('osoba', [
            'name' => 'osoba',
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'osoba',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
        ]);*/

/*        $this->crud->setColumnDetails('status_id', [
            'name' => 'status_id',
            'type' => 'select',
            'label' => 'Status',
            'entity' => 'status',
            'attribute' => 'naziv_id',
            'model' => 'App\Models\Status',
//            'searchLogic' => false,
        ]);*/

//        CRUD::setFromDb(); // columns
        CRUD::column('id');
        /*        $this->crud->addColumn([
            'label' => 'Osoba',
            'type' => 'model_function',
            'function_name' => 'getImePrezimeJmbgAttribute',
            'name' => 'osoba_id',
            'entity' => 'osoba',
//            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                    $q->where('id', 'like', '%'.$searchTerm.'%')
//                        ->orWhere('ime', 'like', '%'.$searchTerm.'%')
                        ->orWhere('prezime', 'like', '%'.$searchTerm.'%');
                });
//                $query->orWhere('id', 'like', '%'.$searchTerm.'%');
            }
        ]);*/
        CRUD::column('osoba_id');
        CRUD::column('datum_prijema');
        CRUD::column('zavodni_broj');
        CRUD::column('broj_odluke_uo');
        CRUD::column('status_id');
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

    protected function showDetailsRow($id)
    {
//        $this->crud->hasAccessOrFail('details_row');//???

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
//dd($this->data['entry']->with('zahteviLicence.licenca')->get());
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
//        return view('crud.details_row', $this->data);
        return view('crud::prijavaclanstvo_details_row', $this->data);
    }
}
