<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\OsiguranjeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\PermissionManager\app\Models\Role;


/**
 * Class OsiguranjeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class OsiguranjeCrudController extends CrudController
{

//if(backpack_user()->can('edit articles'))
//backpack_user()->hasPermissionTo('edit')
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

//    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
//    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Osiguranje');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/osiguranje');
        $this->crud->setEntityNameStrings('osiguranje', 'osiguranja');

        $this->crud->setColumns(['id', 'firmaOsiguravajucaKuca', 'firmaUgovarac', 'osobaUgovarac', 'polisa_broj', 'polisaPokrice', 'polisa_datum_zavrsetka', 'statusPolise', 'statusDokumenta', 'napomena', 'osiguravajuca_kuca_mb', 'ugovarac_osiguranja_mb']);

        $this->crud->enableDetailsRow();
        $this->crud->enableExportButtons();

        $permissionNames = backpack_user()->getPermissionNames(); // collection of name strings
        $permissions = backpack_user()->permissions; // collection of permission objects
        $this->crud->denyAccess('all');
        if (backpack_user()->hasPermissionTo('update')) {

        }

    }

    protected function setupListOperation()
    {
        $this->crud->setColumnDetails('firmaOsiguravajucaKuca', [
            'name' => 'firmaOsiguravajucaKuca',
            'type' => 'select',
            'label' => 'Osiguravajuća kuća',
            'entity' => 'firmaOsiguravajucaKuca',
            'attribute' => 'naziv_mb',
            'model' => 'App\Models\Firma',
        ]);

        $this->crud->setColumnDetails('firmaUgovarac', [
            'name' => 'firmaUgovarac',
            'type' => 'select',
            'label' => 'Ugovarač osiguranja',
            'entity' => 'firmaUgovarac',
            'attribute' => 'naziv_mb',
            'model' => 'App\Models\Firma',
        ]);

        $this->crud->setColumnDetails('osobaUgovarac', [
            'name' => 'osobaUgovarac',
            'type' => 'select',
            'label' => 'Ugovarač ind. osiguranja',
            'entity' => 'osobaUgovarac',
            'attribute' => 'ime_prezime_jmbg',
            'model' => 'App\Models\Osoba',
        ]);

        $this->crud->setColumnDetails('statusPolise', [
            'name' => 'statusPolise',
            'type' => 'select',
            'label' => 'Status polise',
            'entity' => 'statusPolise',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
        ]);

        $this->crud->setColumnDetails('statusDokumenta', [
            'name' => 'statusDokumenta',
            'type' => 'select',
            'label' => 'Status dokumenta',
            'entity' => 'statusDokumenta',
            'attribute' => 'naziv',
            'model' => 'App\Models\Status',
            //samo od grupe dokumenti
        ]);

        $this->crud->setColumnDetails('polisaPokrice', [
            'name' => 'polisaPokrice',
            'type' => 'select',
            'label' => 'Pokriće polise',
            'entity' => 'polisaPokrice',
            'attribute' => 'id_naziv',
            'model' => 'App\Models\OsiguranjePolisaPokrice',
        ]);
        $this->crud->setColumnDetails('osiguranjeTip', [
            'name' => 'osiguranjeTip',
            'type' => 'select',
            'label' => 'Tip osiguranja',
            'entity' => 'osiguranjeTip',
            'model' => 'App\Models\OsiguranjeTip',
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

    }

    protected function setupCreateOperation()
    {
//        $this->crud->setColumns(['osiguravajuca_kuca_mb', 'ugovarac_osiguranja_mb', 'polisa_broj', 'polisaPokrice', 'polisa_pokrice_id', 'polisa_datum_zavrsetka', 'status_polise_id', 'statusDokumenta', 'napomena']);

        $this->crud->setValidation(OsiguranjeRequest::class);

        $this->crud->field('osiguranje_vrsta');
        $this->crud->field('polisa_broj');

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'osiguranje_tip_id',
            'label' => 'Tip osiguranja',
            'entity' => 'osiguranjeTip',
            'model' => 'App\Models\OsiguranjeTip',
        ]);

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'osobe',
            'label' => 'Osobe',
            'ajax' => true,
            'attribute' => 'ime_prezime_licence',  //accessor u Osoba modelu
            'pivot' => true
        ]);

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'firmaOsiguravajucaKuca',
            'label' => 'Osiguravajuća kuća (pretraži po nazivu ili mb)',
            'ajax' => true,
            'attribute' => 'naziv_mb',  //accessor u Osoba modelu
            'inline_create' =>
                [ // specify the entity in singular
                    'entity' => 'firma', // the entity in singular
                    // OPTIONALS
                    //                'force_select' => true, // should the inline-created entry be immediately selected?
                    //                'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    //                'modal_route' => route('firma-inline-create'), // InlineCreate::getInlineCreateModal()
                    //                'create_route' =>  route('firma-inline-create-save'), // InlineCreate::storeInlineCreate()
                ]
        ]);
//        $this->crud->field('firmaOsiguravajucaKuca');

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'firmaUgovarac',
            'label' => 'Ugovarač osiguranja (pretraži po nazivu ili mb)',
            'ajax' => true,
            'attribute' => 'naziv_mb',  //accessor u Osoba modelu
            'inline_create' =>
                [ // specify the entity in singular
                    'entity' => 'firma', // the entity in singular
                    // OPTIONALS
                    //                'force_select' => true, // should the inline-created entry be immediately selected?
                    //                'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
                    //                'modal_route' => route('firma-inline-create'), // InlineCreate::getInlineCreateModal()
                    //                'create_route' =>  route('firma-inline-create-save'), // InlineCreate::storeInlineCreate()
                ]
        ]);
//        $this->crud->field('firmaUgovarac');

        $this->crud->addField([
            'type' => 'relationship',
            'name' => 'osobaUgovarac',
            'label' => 'Ugovarač osiguranja (pretraži po imenu, prezimenu ili jmbg)',
            'ajax' => true,
            'attribute' => 'ime_prezime_jmbg',
        ]);

        $this->crud->addField([
            'name' => 'polisa_pokrice_id',
            'type' => 'relationship',
            'label' => 'Pokriće polise',
            'attribute' => 'naziv',
            'entity' => 'polisaPokrice',
            'model' => 'App\Models\OsiguranjePolisaPokrice'
        ]);

        $this->crud->field('polisa_predmet');
        $this->crud->field('polisa_iskljucenost')->label('Polisa isključenost');
        $this->crud->field('polisa_teritorijalni_limit');
        $this->crud->field('polisa_datum_izdavanja');
        $this->crud->field('polisa_datum_pocetka')->label('Polisa datum početka');
        $this->crud->field('polisa_datum_zavrsetka')->label('Polisa datum završetka');

        $this->crud->addField([
            'name' => 'status_polise_id',
            'type' => 'select2',
            'label' => 'Status polise',
            'entity' => 'statusPolise',
            'attribute' => 'naziv',
            'default' => '-',
            'options' => (function ($query) {
                return $query->where('log_status_grupa_id', 1)->get();
            }),
        ]);

        $this->crud->addField([
            'label' => 'Status dokumenta',
            'type' => 'select2',
            'name' => 'status_dokumenta_id',
            'entity' => 'statusDokumenta',
            'attribute' => 'naziv',
            'default' => '-',
            'options' => (function ($query) {
                return $query->where('log_status_grupa_id', 6)->get();
            }),
        ]);

        $this->crud->field('napomena');

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function fetchOsobe()
    {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
//            'searchable_attributes' => ['id', 'ime', 'prezime'],
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? false;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%')
//                                ->orWhere('ime', 'ilike', $searchTerm[1] . '%')
//                                ->orWhere('prezime', 'ilike', $searchTerm[0] . '%')
                        ->whereHas('licence', function ($query) use ($model) {
                            $query->where('status', '<>', 'D');
                        });
                } else {
                    return $model->whereHas('licence', function ($q) use ($searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    })->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);

    }

    public function fetchFirmaOsiguravajucaKuca()
    {
        return $this->fetch([
            'model' => \App\Models\Firma::class, // required
//            'searchable_attributes' => ['mb', 'naziv'],
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? false;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                        ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                } else {
                    return $model->where('id', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);
    }

    public function fetchFirmaUgovarac()
    {
        return $this->fetch([
            'model' => \App\Models\Firma::class, // required
//            'searchable_attributes' => ['mb'],
            'searchable_attributes' => [],
//            'routeSegment' => 'mb', // falls back to the key of this array if not specified ("category")
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? false;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('naziv', 'ilike', '%' . $searchTerm[0] . '%')
                        ->where('naziv', 'ilike', '%' . $searchTerm[1] . '%');
                } else {
                    return $model->where('id', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);
    }

    public function fetchOsobaUgovarac()
    {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
            'searchable_attributes' => [],
//            'searchable_attributes' => ['id', 'ime', 'prezime'],
//            'routeSegment' => 'mb', // falls back to the key of this array if not specified ("category")
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? false;
                if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    return $model->where('ime', 'ilike', $searchTerm[0] . '%')
                        ->where('prezime', 'ilike', $searchTerm[1] . '%')
//                                ->orWhere('ime', 'ilike', $searchTerm[1] . '%')
//                                ->orWhere('prezime', 'ilike', $searchTerm[0] . '%')
                        ->whereHas('licence', function ($query) use ($model) {
                            $query->where('status', '<>', 'D');
                        });
                } else {
                    return $model->whereHas('licence', function ($q) use ($searchTerm) {
                        $q->where('id', 'ilike', $searchTerm . '%');
                    })->orWhere('ime', 'ilike', $searchTerm . '%')
                        ->orWhere('prezime', 'ilike', $searchTerm . '%')
                        ->orWhere('id', 'ilike', $searchTerm . '%');
                }
            } // to filter the results that are returned
        ]);
    }

    protected function showDetailsRow($id)
    {
//        $this->crud->hasAccessOrFail('details_row');//???

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
//dd($this->data['entry']->osobe);
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
//        return view('crud.details_row', $this->data);
        return view('crud::osiguranje_details_row', $this->data);
    }
}
