<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RequestRequest;
use App\Models\Request;
use App\Models\Status;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class RequestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RequestCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Request::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/request');
        CRUD::setEntityNameStrings('zahtev', 'zahtevi');

        if (!backpack_user()->hasRole('admin')) {
            $this->crud->denyAccess(['create', 'delete', 'update']);
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
        $this->crud->addColumns([
            'id',
            'osoba_id' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime (jmbg)',
                'attribute' => 'ime_prezime_jmbg',
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],
        ]);

        /*        $this->crud->modifyColumn('status_id', [
                    'wrapper' => [
                        'class' => function ($crud, $column, $entry, $related_key) {
                            switch ($entry->status_id) {
                                case OBRADJEN:
                                    return 'bg-success text-white px-2 rounded';
                                case PROBLEM:
                                    return 'bg-danger text-white px-2 rounded';
                                case OTKAZAN:
                                    return 'border border-danger text-white px-2 rounded';
                            }
                        }
                    ]
                ]);*/

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Neažurirani'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('where', 'status_id', KREIRAN); // apply the "active" eloquent scope
            });

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'platili',
            'label' => 'Platili članarinu'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereHas', 'clanarine', function ($query) {
                    $query->where('rokzanaplatu', '>=', 'now()');
                }); // apply the "active" eloquent scope
            });
        // simple filter
        /*        $this->crud->addFilter([
                    'type' => 'simple',
                    'name' => 'nisuplatili',
                    'label' => 'Nisu platili članarinu'
                ],
                    FALSE,
                    function () { // if the filter is active
                        $this->crud->addClause('whereHas', 'clanarine', function ($query) {
                            $query->where('rokzanaplatu', '<', 'now()')
                                ->where('iznoszanaplatu', 'iznosuplate + pretplata');
                        }); // apply the "active" eloquent scope
                    });*/

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], function () {
            return $this->crud->getModel()::existingStatuses();
        },
            function ($value) { // if the filter is active
                $this->crud->addClause('where', 'status_id', $value);
            });

        // dropdown filter
        $this->crud->addFilter([
            'name' => 'clanarina',
            'type' => 'select2_multiple',
            'label' => 'Plaćena članarina za godinu:'
        ],
            function () {
                return \DB::table('requests')
                    ->select('id', 'note')
                    ->distinct('note')
                    ->orderBy('note', 'DESC')
                    ->pluck('note', 'note')
                    ->toArray();
            },
            function ($values) { // if the filter is active
                $this->crud->addClause('whereIn', 'note', json_decode($values));
            });
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(RequestRequest::class);

        $this->crud->setFromDb();
/*        $this->crud->addFields([
            'id',
            'osoba_id' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'label' => 'Ime prezime (jmbg)',
                'attribute' => 'ime_prezime_jmbg',
            ],
            'request_category_id' => [
                'name' => 'requestCategory',
                'type' => 'relationship',
                'label' => 'Kategorija zahteva',
            ],
            'status_id' => [
                'name' => 'status',
                'type' => 'relationship',
                'attribute' => 'naziv',
            ],
            'note' => [
                'name' => 'note',
                'label' => 'Napomena',
            ],
            'created_at' => [
                'name' => 'created_at',
                'label' => 'Kreiran',
                'attributes' => ['disabled' => 'disabled'],
                'type' => 'datetime_picker',
                'datetime_picker_options' => [
                    'format' => 'DD.MM.YYYY. HH:mm:ss',
                    'language' => 'sr_latin'
                ],
            ],
            'updated_at' => [
                'name' => 'updated_at',
                'label' => 'Ažuriran',
                'attributes' => ['disabled' => 'disabled'],
                'type' => 'datetime_picker',
                'datetime_picker_options' => [
                    'format' => 'DD.MM.YYYY. HH:mm:ss',
                    'language' => 'sr_latin'
                ],
            ],
        ]);*/

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
        $this->data['entry'] = $this->crud->getEntry($id)->osoba;
//        dd($this->data['entry']);
        $this->data['crud'] = $this->crud;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('crud::osoba_clanarina_details_row', $this->data);
    }
}
