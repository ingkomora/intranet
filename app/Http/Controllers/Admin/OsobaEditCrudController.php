<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\UpdateDataBrisanjeClanstvoOperation;
use App\Http\Requests\OsobaEditRequest;
use App\Models\Request;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OsobaEditCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OsobaEditCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }

    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
//    use UpdateDataBrisanjeClanstvoOperation;

    protected
        $columns_definition_array = [
        'id',
        'osoba' => [
            'name' => 'osoba_id',
            'type' => 'model_function',
            'label' => 'Ime (roditelj) prezime',
            'function_name' => 'getImeRoditeljPrezimeAttribute',
        ],
        'prebivalisteadresa' => [
            'name' => 'prebivalisteadresa',
            'label' => 'Adresa',
        ],
        'ulica' => [
            'name' => 'ulica',
            'label' => 'Ulica',
        ],
        'broj' => [
            'name' => 'broj',
            'label' => 'Broj',
        ],
        'podbroj' => [
            'name' => 'podbroj',
            'label' => 'Podbroj',
        ],
        'sprat' => [
            'name' => 'sprat',
            'label' => 'Sprat',
        ],
        'stan' => [
            'name' => 'stan',
            'label' => 'Stan',
        ],
        'prebivalistebroj' => [
            'name' => 'prebivalistebroj',
            'label' => 'Poštanski broj',
        ],
        'prebivalisteopstina' => [
            'name' => 'prebivalisteopstina',
            'label' => 'Opština',
        ],
        'prebivalisteopstinaid' => [
            'name' => 'prebivalisteopstinaid',
            'type' => 'select',
            'label' => 'Opština (relacija)',
            'entity' => 'opstinaId',
            'attribute' => 'ime',
            'model' => 'App\Models\Opstina',
        ],
        'prebivalistemesto' => [
            'name' => 'prebivalistemesto',
            'label' => 'Mesto',
        ],
        'prebivalistedrzava' => [
            'name' => 'prebivalistedrzava',
            'label' => 'Država',
        ],
        'status_id' => [
            'name' => 'status_id',
            'type' => 'select',
            'entity' => 'requests.status',
            'label' => 'Ažurirani?',
            'model' => 'App\Models\Request',
            'attribute' => 'naziv'
        ]
    ],
        $fields_definition_array = [
        'id' => [
            'name' => 'id',
            'attributes' => [
                'readonly' => 'readonly',
            ]
        ],
        'ime',
        'roditelj',
        'prezime',
        'prebivalisteadresa' => [
            'name' => 'prebivalisteadresa',
            'label' => 'Adresa',
        ],
        'ulica' => [
            'name' => 'ulica',
            'label' => 'Ulica',
        ],
        'broj' => [
            'name' => 'broj',
            'label' => 'Broj',
        ],
        'podbroj' => [
            'name' => 'podbroj',
            'label' => 'Podbroj',
        ],
        'sprat' => [
            'name' => 'sprat',
            'label' => 'Sprat',
        ],
        'stan' => [
            'name' => 'stan',
            'label' => 'Stan',
        ],
        'prebivalistebroj' => [
            'name' => 'prebivalistebroj',
            'label' => 'Poštanski broj',
        ],
        'prebivalisteopstinaid' => [
            'name' => 'prebivalisteopstinaid',
            'type' => 'relationship',
            'label' => 'Opština (relacija)',
            'entity' => 'opstinaId',
            'attribute' => 'ime',
            'placeholder' => 'Odaberite opštinu',
            'hint' => 'Pretražite po opštinu po nazivu',
            'allows_null' => TRUE,
        ],
        'prebivalistemesto' => [
            'name' => 'prebivalistemesto',
            'label' => 'Mesto',
        ],
        'prebivalistedrzava' => [
            'name' => 'prebivalistedrzava',
            'label' => 'Država',
        ],
        'status_id' => [
            'name' => 'status_id',
//            'type' => 'relationship',
            'type' => 'select',
            'label' => 'Ažuriran',
            'entity' => 'requests.status',
            'attribute' => 'naziv',
//            'placeholder' => 'Odaberite opštinu',
//            'hint' => 'Pretražite po opštinu po nazivu',
        ],
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Osoba::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/osoba-edit');
        CRUD::setEntityNameStrings('člana', 'Članovi');

        $this->crud->setColumns($this->columns_definition_array);

        $this->crud->addClause('whereHas', 'requests', function ($query) {
            $query->where('request_category_id', 2);
        });
        $this->crud->limit(1000);

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['delete', 'create']);
        }

        $this->crud->enableDetailsRow();
        $this->crud->enableExportButtons();

    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->setColumns($this->columns_definition_array);

        $this->crud->modifyColumn('id', [
            'name' => 'id',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%')
                        ->whereHas('licence', function ($q) {
                            $q->where('status', '<>', 'D');
                        });
                } else {
                    $query->whereHas('licence', function ($q) use ($searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    })
                        ->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%')
                        ->orWhere('id', 'ilike', $searchTerm . '%');
                }
            }
        ]);

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Neažurirani'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereHas', 'requests', function ($query) {
                    $query->where('status_id', 35);
                }); // apply the "active" eloquent scope
            });

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
        $this->crud->setValidation(OsobaEditRequest::class);

        $this->crud->addFields($this->fields_definition_array);
        $this->crud->modifyField('status_id', [
            'options' => (function ($query) {
                return $query
                    ->where('log_status_grupa_id', PODACI)
                    ->get();
            }), //  you can use this to filter the results show in the select
        ]);

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

    public function update()
    {
        // do something before validation, before save, before everything; for example:
        // $this->crud->addField(['type' => 'hidden', 'name' => 'author_id']);
        // $this->crud->removeField('password_confirmation');

        // Note: By default Backpack ONLY saves the inputs that were added on page using Backpack fields.
        // This is done by stripping the request of all inputs that do NOT match Backpack fields for this
        // particular operation. This is an added security layer, to protect your database from malicious
        // users who could theoretically add inputs using DeveloperTools or JavaScript. If you're not properly
        // using $guarded or $fillable on your model, malicious inputs could get you into trouble.

        // However, if you know you have proper $guarded or $fillable on your model, and you want to manipulate
        // the request directly to add or remove request parameters, you can also do that.
        // We have a config value you can set, either inside your operation in `config/backpack/crud.php` if
        // you want it to apply to all CRUDs, or inside a particular CrudController:
        // $this->crud->setOperationSetting('saveAllInputsExcept', ['_token', '_method', 'http_referrer', 'current_tab', 'save_action']);
        // The above will make Backpack store all inputs EXCEPT for the ones it uses for various features.
        // So you can manipulate the request and add any request variable you'd like.
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');
        // $this->crud->getRequest()->request->add(['author_id'=> backpack_user()->id]);
        // $this->crud->getRequest()->request->remove('password_confirmation');
//        todo: sta ako ima vise rekorda???
//        dd($this->crud->getRequest()->status_id);
        $request = Request::where('osoba_id', $this->crud->getRequest()->id)
            ->where('request_category_id', 2)
            ->first();
        if ($this->crud->getRequest()->status_id == 37) {
            $request->status_id = 37;
        } else {
            $request->status_id = 36;
        }
        $request->save();
        $response = $this->traitUpdate();
        // do something after save
        return $response;
    }

    protected function showDetailsRow($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_clanarina_details_row', $this->data);
    }
}
