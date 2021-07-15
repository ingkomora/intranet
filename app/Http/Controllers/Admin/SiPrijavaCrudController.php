<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PrijavaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class SiPrijavaCrudController extends CrudController {
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup() {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        CRUD::setModel('App\Models\SiPrijava');
        CRUD::setRoute(config('backpack.base.route_prefix') . '/siprijava');
        CRUD::setEntityNameStrings('siprijava', 'Prijave Stručni ispit');
        CRUD::setTitle('some string', 'create'); // set the Title for the create action
        CRUD::setHeading('some string', 'create'); // set the Heading for the create action
        CRUD::setSubheading('some string', 'create');
        CRUD::enableDetailsRow();
        CRUD::enableExportButtons();

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        $this->crud->setColumns(['id', 'osoba', 'reg_oblast_id', 'reg_pod_oblast_id', 'zvanje_id', 'si_vrsta_id', 'status_prijave', 'strucni_rad', 'barcode']);


        $this->crud->setColumnDetails('reg_oblast_id', [
            'name' => 'reg_oblast_id',
            'type' => 'select',
            'label' => 'Strucna oblast',
            'entity' => 'regOblast',
            'attribute' => 'naziv',
            'model' => 'App\Models\RegOblast',
        ]);
        $this->crud->setColumnDetails('reg_pod_oblast_id', [
            'name' => 'reg_pod_oblast_id',
            'type' => 'select',
            'label' => 'Uza Strucna oblast',
            'entity' => 'regPodOblast',
            'attribute' => 'naziv',
            'model' => 'App\Models\RegPodoblast',
        ]);

        $this->crud->setColumnDetails('zvanje_id', [
            'name' => 'zvanje_id',
            'type' => 'select',
            'label' => 'Zvanje',
            'entity' => 'zvanje',
            'attribute' => 'naziv',
            'model' => 'App\Models\Zvanje',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('zvanje', function ($q) use ($column, $searchTerm) {
                    $q->where('naziv', 'ilike', '%' . $searchTerm . '%');
                });
            }

        ]);
        $this->crud->setColumnDetails('si_vrsta_id', [
            'name' => 'si_vrsta_id',
            'type' => 'select',
            'label' => 'Vrsta SI',
            'entity' => 'siVrsta',
            'attribute' => 'naziv',
            'model' => 'App\Models\SiVrsta',
        ]);
    }

    protected function setupListOperation() {
        $this->crud->setColumnDetails('osoba', [
            'name' => 'osoba',
            'type' => 'select',
            'label' => 'Osoba',
            'entity' => 'osoba',
//            'attribute' => 'ime',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm[1] . '%')
                            ->orWhere('ime', 'ilike', $searchTerm[1] . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm[0] . '%');
                    });
                } else {
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', '%' . $searchTerm . '%')
                            ->orWhere('prezime', 'ilike', '%' . $searchTerm . '%');
                    });
                }
            }

        ]);
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        $this->crud->setFromDb();
    }

    protected function setupCreateOperation() {
//        $this->crud->setValidation(PrijavaRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation() {
        $this->setupCreateOperation();
    }
}