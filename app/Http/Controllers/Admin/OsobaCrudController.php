<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\UpdateLicencaStatusOperation;
use App\Http\Requests\OsobaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class OsobaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class OsobaCrudController extends CrudController
{

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use UpdateLicencaStatusOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;


    public function setup()
    {
        $this->crud->setModel('App\Models\Osoba');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/osoba');
        $this->crud->setEntityNameStrings('osoba', 'osobe');
        $this->crud->setColumns(['id', 'ime_prezime_roditelj', 'zvanjeId', 'lib', 'clan', 'kontaktemail', 'mobilnitel', 'firmanaziv', 'opstinaId', 'firma_mb', 'firma', 'created_at', 'updated_at']);

//        prikazuje samo osobe sa maticnim brojem od 13 karaktera
        $this->crud->addClause('whereRaw', 'length(id) = 13');

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
        $request = $this->crud->getRequest();
        if (!$request->has('order')) {
            $request->merge(['order' => [
                [
                    'column' => 'id',
                    'dir' => 'asc'
                ]
            ]]);
        }

        $this->crud->setColumnDetails('id', [
            'name' => 'id',
            'label' => 'JMBG',
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%');
                } else {
                    $query->where('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%')
                        ->orWhere('id', 'ilike', $searchTerm . '%');
                }
            },
        ]);

        $this->crud->setColumnDetails('ime_prezime_roditelj', [
            'name' => 'ime_roditelj_prezime',
            'type' => 'text',
            'label' => 'Ime prezime (roditelj)',
        ]);

        $this->crud->setColumnDetails('zvanjeId', [
            'name' => 'zvanjeId',
            'type' => 'select',
            'label' => 'Zvanje',
            'entity' => 'zvanjeId',
            'attribute' => 'skrnaziv',
            'model' => 'App\Models\Zvanje',
        ]);

        $this->crud->setColumnDetails('firma', [
            'name' => 'firma',
            'type' => 'select',
            'label' => 'Firma po MB',
            'entity' => 'firma',
            'attribute' => 'naziv_mb',
            'model' => 'App\Models\Firma',
        ]);

        $this->crud->setColumnDetails('opstinaId', [
            'name' => 'opstinaId',
            'type' => 'select',
            'label' => 'Opština',
            'entity' => 'opstinaId',
            'attribute' => 'ime',
            'model' => 'App\Models\Opstina',
        ]);

        $this->crud->setColumnDetails('clan', [
            'name' => 'clan',
            'label' => 'Članstvo',
            'type' => 'select_from_array',
            'options' => [-1 => 'Funkcioner', 0 => 'Nije član', 1 => 'Član'],
        ]);

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'clan',
            'label' => 'Član'
        ], function () {
            return [
                0 => 'Nije član',
                1 => 'Član je'
            ];
        }, function ($value) {
            $this->crud->addClause('where', 'clan', $value);
        });

        /*$this->crud->addFilter([
            'type' => 'simple',
            'name' => 'firma_mb',
            'label' => 'MB firme'
        ],
            false,
            function () {
                $this->crud->addClause('where', 'firma_mb', '<>', 'NULL');
                $this->crud->addClause('where', 'firma_mb', '<>', 0);
            }
        );*/

        /*        $this->crud->addFilter([
                    'type' => 'text',
                    'name' => 'ime',
                    'label' => 'Ime'
                ],
                    false,
                    function ($value) {
                        $this->crud->addClause('where', 'ime', 'ILIKE', "%$value%");
                    }
                );*/

        /*        $this->crud->addFilter([
                    'type' => 'text',
                    'name' => 'prezime',
                    'label' => 'Prezime'
                ],
                    false,
                    function ($value) {
                        $this->crud->addClause('where', 'prezime', 'LIKE', "%$value%");
                    }
                );*/

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'lib',
            'label' => 'Nema LIB'
        ],
            false,
            function () {
                $this->crud->addClause('where', 'lib', NULL);
            }
        );

        /*$this->crud->addFilter([
            'type' => 'date',
            'name' => 'updated_at',
            'label' => 'Ažuriran'
        ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $this->crud->addClause('where', 'updated_at', $value);
            });*/
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(OsobaRequest::class);

        $this->crud->addField([
            'name' => 'id',
            'label' => 'JMBG',
            'tab' => 'Licni podaci'
        ], 'create');
        $this->setupUpdateOperation();
    }

    protected function setupUpdateOperation()
    {
        //TODO samo admin da moze da menja
        $this->crud->addField([
            'name' => 'id',
            'label' => 'JMBG',
            'attributes' => ['readonly' => 'readonly'],
            'tab' => 'Licni podaci'
        ], 'update');
        $this->crud->field('ime')->tab('Licni podaci');
        $this->crud->field('prezime')->tab('Licni podaci');
        $this->crud->field('roditelj')->tab('Licni podaci');
        $this->crud->field('devojackoprezime')->tab('Licni podaci');
        $this->crud->field('zvanjeId')->tab('Licni podaci');
        $this->crud->field('rodjenjemesto')->tab('Licni podaci');
        $this->crud->field('rodjenjeopstina')->tab('Licni podaci');
        $this->crud->field('rodjenjedrzava')->tab('Licni podaci');
        $this->crud->field('kontakttel')->tab('Licni podaci');
        $this->crud->field('mobilnitel')->tab('Licni podaci');
        $this->crud->field('kontaktemail')->tab('Licni podaci');
        $this->crud->field('prebivalistebroj')->tab('Podaci o prebivalistu');
        $this->crud->field('prebivalistemesto')->tab('Podaci o prebivalistu');
        $this->crud->field('opstinaId')->label('Opština prebivališta')->tab('Podaci o prebivalistu');
        $this->crud->field('prebivalisteadresa')->tab('Podaci o prebivalistu');
        $this->crud->field('firmanaziv')->label('Naziv firme ako nema MB')->tab('Podaci o firmi')->attributes(['readonly' => 'readonly']);
        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'firma',
            'label' => 'Firma po maticnom broju (pretrazi po MB ili Nazivu)',
//            'attribute' => 'naziv_mb',
//            'tab' => 'firma',
            'ajax' => true,
            'inline_create' => true
            /*                [ // specify the entity in singular
                            'entity' => 'firma', // the entity in singular
                            // OPTIONALS
            //                'force_select' => true, // should the inline-created entry be immediately selected?
            //                'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
            //                'modal_route' => route('firma-inline-create'), // InlineCreate::getInlineCreateModal()
            //                'create_route' =>  route('firma-inline-create-save'), // InlineCreate::storeInlineCreate()
                        ]*/
        ]);
        $this->crud->field('firma')->tab('Podaci o firmi');

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'osiguranja',
        ]);
        $this->crud->field('osiguranja')->tab('Osiguranje');
        $this->crud->field('diplfakultet')->tab('Osnovne akademske / master studije');
        $this->crud->field('diplmesto')->tab('Osnovne akademske / master studije');
        $this->crud->field('dipldrzava')->tab('Osnovne akademske / master studije');
        $this->crud->field('diplodsek')->tab('Osnovne akademske / master studije');
        $this->crud->field('diplsmer')->tab('Osnovne akademske / master studije');
        $this->crud->field('diplgodina')->tab('Osnovne akademske / master studije');
        $this->crud->field('diplbroj')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrfakultet')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrmesto')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrdrzava')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrodsek')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrsmer')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrgodina')->tab('Osnovne akademske / master studije');
        $this->crud->field('mrbroj')->tab('Osnovne akademske / master studije');
        $this->crud->field('bolonja')->tab('Osnovne akademske / master studije');

    }

    public function fetchFirma()
    {
        return $this->fetch([
            'model' => \App\Models\Firma::class, // required
//            'searchable_attributes' => ['mb'],
            'searchable_attributes' => ['mb', 'naziv'],
            'paginate' => 10, // items to show per page
//            'query' => function($model) {
//                return $model->active();
//            } // to filter the results that are returned
        ]);

    }

    protected function showDetailsRow($id)
    {
//        $this->crud->hasAccessOrFail('details_row');//???

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
//dd($this->data['entry']->clanarine->count());
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
//        return view('crud.details_row', $this->data);
        return view('crud::osoba_details_row', $this->data);
    }
}
