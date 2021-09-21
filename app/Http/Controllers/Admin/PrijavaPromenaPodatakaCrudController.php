<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PromenaPodatakaEmailRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PrijavaPromenaPodatakaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PrijavaPromenaPodatakaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use Operations\PromenaPodatakaEmailOperation;


    protected
        $columns_definition_array = [
        'id',
        'osoba', // virtual column
//        'ime',
//        'prezime',
        'brlic' => [
            'name' => 'brlic',
            'label' => 'Broj licence'
        ],
        'adresa',
        'mesto',
        'pbroj',
        'topstina_id',
        'tel' => [
            'name' => 'tel',
            'label' => 'Telefon'
        ],
        'mob' => [
            'name' => 'mob',
            'label' => 'Mobilni'
        ],
        'email',
        'obradjen' => [
            'name' => 'obradjen',
            'label' => 'Status zahteva',
            'type' => 'closure',
        ],
        'nazivfirm',
        'mestofirm',
        'opstinafirm',
        'emailfirm',
        'telfirm',
        'wwwfirm',
        'ipaddress',
        'datumprijema' => [
            'name' => 'datumprijema',
            'label' => 'Datum prijema',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'datumobrade' => [
            'name' => 'datumobrade',
            'label' => 'Datum obrade',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'mbfirm',
        'pibfirm',
        'adresafirm',
        'napomena',
        'created_at' => [
            'name' => 'created_at',
            'label' => 'Kreiran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
        'updated_at' => [
            'name' => 'updated_at',
            'label' => 'Ažuriran',
            'type' => 'datetime',
            'format' => 'DD.MM.YYYY. HH:mm:ss'
        ],
    ],
        // todo: privremeno, dok regionalci pozivaju telefonom ljude koji nemaju email
        $remove_columns_definition_array = [
        //            'id',
        'ime',
        'prezime',
//            'brlic',
        'adresa',
        'mesto',
        'pbroj',
        'topstina_id',
        'tel',
        'mob',
//        'email',
        'nazivfirm',
        'mestofirm',
        'opstinafirm',
        'emailfirm',
        'telfirm',
        'wwwfirm',
        'ipaddress',
//            'datumprijema',
//            'datumobrade',
//            'obradjen',
        'mbfirm',
        'pibfirm',
        'adresafirm',
//            'napomena',
        'created_at',
        'updated_at'
    ],
        $fields_definition_array = [
        'id' =>
            [
                'name' => 'id',
                'attributes' => ['readonly' => 'readonly'],
            ],
        'ime' =>
            [
                'name' => 'ime',
                'attributes' => ['disabled' => 'disabled'],
            ],
        'prezime' =>
            [
                'name' => 'prezime',
                'attributes' => ['disabled' => 'disabled'],
            ],
        'brlic' =>
            [
                'name' => 'brlic',
                'attributes' => ['disabled' => 'disabled'],
            ],
        'email',
        'napomena',
    ];

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\PrijavaPromenaPodataka::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/clanstvo/promenapodataka');
        CRUD::setEntityNameStrings('promena podataka', 'Promena podataka');

//        $this->crud->addClause('where', 'obradjen', backpack_user()->id);

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
        $this->crud->removeColumns($this->remove_columns_definition_array);

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */

        $this->crud->setColumnDetails('osoba', [
            'name' => 'licenca',
            'type' => 'select',
            'label' => 'Ime prezime (jmbg)',
            'entity' => 'licenca',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Licenca',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('licenca.osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('licenca.osobaId', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm . '%')
                            ->orWhere('prezime', 'ilike', $searchTerm . '%')
                            ->orWhere('id', 'ilike', $searchTerm . '%');
                    });
                }
            }
        ]);

//        todo: testirati da li radi pretraga sa licencom sa kojom nije podneo zahtev
        $this->crud->setColumnDetails('brlic', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('licenca', function ($q) use ($column, $searchTerm) {
                    $q->where('id', 'ilike', $searchTerm . '%');
                });
            }
        ]);

        $this->crud->setColumnDetails('obradjen', [
            'name' => 'obradjen',
            'function' => function ($entry) {
                switch ($entry->obradjen) {
                    case 0:
                        return 'Neobrađen';
                    case 1:
                        return 'Obrađen';
                    case 2:
                        return 'Duplikat';
                    case 3:
                        return 'Email';
                    case 4:
                        return 'Otkazan';
                    case 5:
                        return 'Noviji';
                    case 6:
                        return 'Potpis';
                    case 7:
                    case 16:
                    case 32:
                    case 33:
                    case 34:
                    case 35:
                    case 36:
                    case 37:
                    case 38:
                    case 39:
                    case 40:
                    case 41:
                    case 42:
                        return 'Email-neobrađen';
                    case 102:
                    case 116:
                    case 132:
                    case 133:
                    case 134:
                    case 135:
                    case 136:
                    case 137:
                    case 138:
                    case 139:
                    case 140:
                    case 141:
                    case 142:
                        return 'Email-obrađen';
                    case 202:
                    case 216:
                    case 232:
                    case 233:
                    case 234:
                    case 235:
                    case 236:
                    case 237:
                    case 238:
                    case 239:
                    case 240:
                    case 241:
                    case 242:
                        return 'Email-Problem';
                }
            },
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->obradjen) {
                        case 0:
                        case backpack_user()->id:
                            return 'bg-warning px-2 rounded';
                        case 1:
                            return 'bg-success text-white px-2 rounded';
                        case backpack_user()->id + 100:
                            return 'bg-info text-white px-2 rounded';
                        case backpack_user()->id + 200:
                            return 'bg-danger text-white px-2 rounded';
                    }
                }
            ]
        ]);

        /*
         * Define filters
         * start
         */

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => backpack_user()->name
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->whereIn('obradjen', [backpack_user()->id, backpack_user()->id + 100, backpack_user()->id + 200]); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'neobradjeni',
            'label' => 'Nebrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'obradjeni',
            'label' => 'Obrađeni zahtevi'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id + 100); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'problematicni',
            'label' => 'Problematični'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->query->where('obradjen', backpack_user()->id + 200); // apply the "active" eloquent scope
            });
        /*
        * end
        * Define filters
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
        CRUD::setValidation(PromenaPodatakaEmailRequest::class);


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