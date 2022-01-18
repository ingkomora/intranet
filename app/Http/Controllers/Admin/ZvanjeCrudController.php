<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ZvanjeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
//use Backpack\CRUD\app\Library\CrudPanel\CrudPanel as CRUD;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;


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

        $this->crud->setColumns(['id','naziv', 'skrnaziv', 'sekcija', 'regSekcija']);

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
        $this->crud->setColumnDetails('sekcija', [
            'name' => 'zvanje_grupa_id',
            'type' => 'select',
            'label' => 'Sekcija',
            'entity' => 'sekcija',
            'attribute' => 'id_naziv',
            'model' => 'App\Models\Sekcija',
            'orderable'  => true,
            'orderLogic' => function ($query, $column, $columnDirection) {
                return $query->leftJoin('tzvanje_grupa', 'tzvanje_grupa.id', '=', 'tzvanje.zvanje_grupa_id')
                    ->orderBy('tzvanje_grupa.id', $columnDirection)->select('tzvanje.*');
            }
        ]);
        $this->crud->setColumnDetails('regSekcija', [
            'name' => 'regSekcija',
            'type' => 'select',
            'label' => 'Reg Sekcija',
            'entity' => 'regSekcija',
            'attribute' => 'id_naziv',
            'model' => 'App\Models\RegSekcija',
            'orderable'  => true,
        ]);

        CRUD::filter('zvanje_grupa_id')
            ->type('dropdown')
            ->label('Sekcija')
            ->values([
                '1'   => 'Arhitekte',
                '2'   => 'Građevinci',
                '3' => 'Test'
            ])
            ->whenActive(function($value) {
                $this->crud->addClause('where', 'zvanje_grupa_id', $value);
            })
            ->apply();

        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        $this->crud->setFromDb();
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ZvanjeRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
