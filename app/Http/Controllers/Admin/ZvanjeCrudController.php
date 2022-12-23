<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ZvanjeRequest;
use App\Models\Sekcija;
use Backpack\CRUD\app\Http\Controllers\CrudController;

//use Backpack\CRUD\app\Library\CrudPanel\CrudPanel as CRUD;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use phpDocumentor\Reflection\Types\This;


/**
 * Class ZvanjeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ZvanjeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Zvanje');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/zvanje');
        $this->crud->setEntityNameStrings('zvanje', 'zvanja');

        CRUD::set('show.setFromDb', FALSE);


        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess('create');
        }

        if (backpack_user()->hasRole('sluzba_rk')) {
            $this->crud->denyAccess(['update']);
        }

        $this->crud->enableDetailsRow();
        $this->crud->enableExportButtons();
    }

    protected function setupListOperation()
    {
        CRUD::setColumns([
            'id',
            'status' => [
                'name' => 'status',
                'attribute' => 'naziv',
                'wrapper' => [
                    'class' => function ($crud, $column, $entry, $related_key) {
                        switch ($related_key) {
                            case AKTIVAN:
                                return 'btn btn-sm btn-outline-success';
                            case NEAKTIVAN:
                                return 'btn btn-sm btn-outline-secondary';
                        }
                    },
                ]
            ],
            'naziv',
//            'naziven',
//            'skrnaziven',
            'skrnaziv' => ['name' => 'skrnaziv', 'label' => 'Skraćeno'],
            'sekcija' => ['name' => 'sekcija', 'attribute' => 'naziv'],
//            'reg_sekcija_id',
//            'reg_oblast_id',
        ]);


        // simple filter
        $this->crud->addFilter([
            'name' => 'activeFilter',
            'type' => 'simple',
            'label' => 'Aktivna'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('where', 'status_id', AKTIVAN); // apply the "active" eloquent scope
            });

        // select2 filter
        $this->crud->addFilter([
            'name' => 'sekcijaFilter',
            'type' => 'select2',
            'label' => 'Sekcija'
        ], function () {
            return Sekcija::pluck('naziv', 'id')->toArray();
        }, function ($value) { // if the filter is active
            $this->crud->addClause('whereHas', 'sekcija', function ($q) use ($value) {
                $q->where('id', $value);
            });
        });

    }

    protected function setupShowOperation()
    {
        CRUD::setColumns([
            'id',
            'status' => [
                'name' => 'status',
                'attribute' => 'naziv',
                'wrapper' => [
                    'class' => function ($crud, $column, $entry, $related_key) {
                        switch ($related_key) {
                            case AKTIVAN:
                                return 'btn btn-sm btn-outline-success';
                            case NEAKTIVAN:
                                return 'btn btn-sm btn-outline-secondary';
                        }
                    },
                ]
            ],
            'naziv' => ['name' => 'naziv', 'limit' => 500],
            'skrnaziv' => ['name' => 'skrnaziv', 'label' => 'Skraćeni naziv'],
            'naziven' => ['name' => 'naziven', 'label' => 'Naziv na enleskom'],
            'skrnaziven' => ['name' => 'skrnaziven', 'label' => 'Skraćeni naziv na engleskom'],
            'sekcija' => ['name' => 'sekcija', 'attribute' => 'naziv', 'label' => 'Grupa zvanja', 'limit' => 500],
            'regSekcija' => ['name' => 'regSekcija', 'attribute' => 'naziv', 'label' => 'Sekcija', 'limit' => 500],
            'oblast' => ['name' => 'oblast', 'attribute' => 'naziv', 'limit' => 500],
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ZvanjeRequest::class);

        CRUD::field('naziv')->size(3);
        CRUD::field('skrnaziv')->size(3);
        CRUD::field('naziven')->size(3);
        CRUD::field('skrnaziven')->size(3);
        CRUD::field('sekcija')->size(4);
        CRUD::field('regSekcija')->size(4);
        CRUD::field('oblast')->size(4);
        CRUD::field('status_id')->type('select_from_array')->options([0 => 'Neaktivan', 1 => 'Aktivan']);


    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
