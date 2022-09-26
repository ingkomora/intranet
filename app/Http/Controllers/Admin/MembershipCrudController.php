<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MembershipRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Carbon\Carbon;

/**
 * Class MembershipCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MembershipCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Membership::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/membership');
        CRUD::setEntityNameStrings('članstvo', 'Evidencija članstva');

        $this->crud->set('show.setFromDb', FALSE);

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
        CRUD::column('id');
        CRUD::column('osoba_id');
        $this->crud->addColumn([
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime',
            'attribute' => 'full_name',
        ]);
        CRUD::column('status_id')->type('select_from_array')->options([10 => 'Aktivno', 11 => 'Neaktivno', 12 => 'U mirovanju']);
        CRUD::column('started_at')->type('date')->format('DD.MM.YYYY');
        CRUD::column('ended_at')->type('date')->format('DD.MM.YYYY');
//        CRUD::column('note');
//        CRUD::column('created_at');
//        CRUD::column('updated_at');

        $this->crud->modifyColumn('status_id', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case 10:
                            return 'border border-success text-success px-2 rounded';
                        case 11:
                            return 'border border-secondary text-dark px-2 rounded';
                        case 12:
                            return 'border border-warning text-dark px-2 rounded';
                    }
                }
            ]
        ]);


        $this->crud->setColumnDetails('osoba', [
            'searchLogic' => function ($query, $column, $searchTerm) {
                if (strstr($searchTerm, ",")) {
                    $searchTerm = trim($searchTerm, " ,.;");
                    $searchTerm = explode(",", $searchTerm);
                    $searchTermArray = array_map('trim', $searchTerm);
//                    dd($column);
                    $query->orWhereHas('osoba', function ($q) use ($searchTermArray) {
                        $q->whereIn('id', $searchTermArray)
                            ->orderBy('id');
                    });
                } else if (strstr($searchTerm, " ")) {
                    $searchTerm = explode(" ", $searchTerm);
                    $query->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                        $q->where('ime', 'ilike', $searchTerm[0] . '%')
                            ->where('prezime', 'ilike', $searchTerm[1] . '%');
                    });
                } else {
                    $query->orWhereHas('osoba.licence', function ($q) use ($column, $searchTerm) {
                        $q
                            ->where('id', 'ilike', $searchTerm . '%');
                    })
                        ->orWhereHas('osoba', function ($q) use ($column, $searchTerm) {
                            $q
                                ->where('id', 'ilike', $searchTerm . '%')
                                ->orWhere('ime', 'ilike', $searchTerm . '%')
                                ->orWhere('prezime', 'ilike', $searchTerm . '%');
                        });
                }
            }
        ]);

        // simple filter
        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Aktivna članstva'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('active'); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'mirovanje',
            'label' => 'U mirovanju'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('where', 'status_id', 12); // apply the "active" eloquent scope
            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'imezahtevzamirovanje',
            'label' => 'Bez zahteva za mirovanje'
        ],
            FALSE,
            function () { // if the filter is active
                // todo: ovi zahtevi za mirovanje nisu asocirani za clanstvo
//                $this->crud->addClause('whereDoesntHave', 'requests', function ($q) {
//                    $q
//                        ->where('request_category_id', 4)
//                        ->where('status_id', 52);
//                }); // apply the "active" eloquent scope

                $this->crud->addClause('whereDoesntHave', 'zahtevZaMirovanje', function ($q) {
                    $q->where('status_id', 52);
                });

            });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'bezfunkcionera',
            'label' => 'Bez funkcionera'
        ],
            FALSE,
            function () { // if the filter is active
                $this->crud->addClause('whereHas', 'osoba', function ($q) {
                    $q
                        ->whereDoesntHave('aktivniClanoviVeca');
                }); // apply the "active" eloquent scope
            });

//        Nema potrebe jer ima date range filter
//        $this->crud->addFilter([
//            'type' => 'simple',
//            'name' => 'nisuplatili',
//            'label' => 'Duguje na današnji dan'
//        ],
//            FALSE,
//            function () {
//                // if the filter is active
//                // apply the "active" eloquent scope
//                $this->crud->addClause('whereHas', 'clanarine', function ($query) {
//                    $query->where('rokzanaplatu', '<', 'now()')
//                        ->whereRaw('iznoszanaplatu = iznosuplate + pretplata');
//                });
//                $this->crud->addClause('whereDoesntHave', 'clanarine', function ($query) {
//                    $query->where('rokzanaplatu', '>=', 'now()');
//                });
//            });

        // daterange filter
        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'from_to',
            'label' => 'Duguje'
        ],
            FALSE,
            function ($value) { // if the filter is active, apply these constraints
                $dates = json_decode($value);
//            dd($dates);
                $this->crud->addClause('whereExists', function ($query) use ($dates) {
                    $query
                        ->select('c1.rokzanaplatu')
                        ->from('tclanarinaod2006 as c1')
                        ->where('c1.rokzanaplatu', function ($query) {
                            $query
                                ->select('c2.rokzanaplatu')
                                ->from('tclanarinaod2006 as c2')
                                ->whereColumn('c1.osoba', 'c2.osoba')
                                ->orderByDesc('c2.rokzanaplatu')
                                ->limit(1);
                        })
                        ->where('c1.rokzanaplatu', '>=', $dates->from)
                        ->whereColumn('c1.osoba', 'memberships.osoba_id')
                        ->where('status_id', 10);
                });

                $this->crud->addClause('whereExists', function ($query) use ($dates) {
                    $query
                        ->select('c1.rokzanaplatu')
                        ->from('tclanarinaod2006 as c1')
                        ->where('c1.rokzanaplatu', function ($query) {
                            $query
                                ->select('c2.rokzanaplatu')
                                ->from('tclanarinaod2006 as c2')
                                ->whereColumn('c1.osoba', 'c2.osoba')
                                ->orderByDesc('c2.rokzanaplatu')
                                ->limit(1);
                        })
                        ->where('c1.rokzanaplatu', '<=', $dates->to)
                        ->whereColumn('c1.osoba', 'memberships.osoba_id')
                        ->where('status_id', 10);
                });

            });


    }

    public function setupShowOperation()
    {
        $membership = $this->crud->getEntry(\Request::segment(3));
        if ($membership->status_id == 12) {
            $mirovanje = $membership->aktivnaMirovanja()->first();

            $mirovanje->datumkraja = $mirovanje->datumkraja == '2999-12-31' ? '<em>na neodređeno vreme</em>' : Carbon::parse($mirovanje->datumkraja)->format('d.m.Y');

            $content = "
            <table class='table'>
                <thead>
                    <tr>
                        <th class='bg-info text'>OSOBA</th>
                    </tr>
                    <tr>
                        <th>Ime (roditelj) prezime</th>
                        <th>Zvanje</th>
                        <th>Prebivalište</th>
                        <th>Kontakt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{$membership->osoba->ime_prezime_licence}</td>
                        <td>{$membership->osoba->zvanjeId->skrnaziv}</td>
                        <td>{$membership->osoba->prebivalistemesto}</td>
                        <td>
                            <a href='mailto: {$membership->osoba->kontaktemail}'>{$membership->osoba->kontaktemail}</a>
                            <br>
                            <a href='tel: {$membership->osoba->mobilnitel}'>{$membership->osoba->mobilnitel}</a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class='table'>
                <thead>
                    <tr>
                        <th class='bg-warning text-dark'>MIROVANJE</th>
                    </tr>
                    <tr>
                        <th>Datum početka</th>
                        <th>Datum završetka</th>
                        <th>Datum prestanka na lični zahtev</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>" . Carbon::parse($mirovanje->datumpocetka)->format('d.m.Y') . "</td>
                        <td>" . $mirovanje->datumkraja . "</td>
                        <td>" . Carbon::parse($mirovanje->datumprestanka)->format('d.m.Y') . "</td>
                    </tr>
                </tbody>
            </table>
            ";

            Widget::add()
                ->to('before_content')
                ->type('alert')
                ->class('alert border border-warning text-center text-dark mb-2 col-8')
                ->heading('Dodatne informacije')
                ->content($content);

        }

        CRUD::column('id');
        CRUD::column('osoba_id');
        $this->crud->addColumn([
            'name' => 'osoba',
            'type' => 'relationship',
            'label' => 'Ime prezime',
            'attribute' => 'full_name',
        ]);
        CRUD::column('status_id')->type('select_from_array')->options([10 => 'Aktivno', 11 => 'Neaktivno', 12 => 'U mirovanju']);
        CRUD::column('started_at')->type('date')->format('DD.MM.YYYY');
        CRUD::column('ended_at')->type('date')->format('DD.MM.YYYY');
        CRUD::column('note')->limit(500);
        CRUD::column('created_at')->type('datetime')->format('DD.MM.Y H:m:s');
        CRUD::column('updated_at')->type('datetime')->format('DD.MM.Y H:m:s');

        $this->crud->setColumnDetails('osoba', [
            'wrapper' => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('osoba/' . $related_key . '/show');
                },
                'class' => 'btn btn-sm btn-outline-info mr-1',
            ],
        ]);

        $this->crud->modifyColumn('status_id', [
            'wrapper' => [
                'class' => function ($crud, $column, $entry, $related_key) {
                    switch ($entry->status_id) {
                        case 10:
                            return 'border border-success text-success px-2 rounded';
                        case 11:
                            return 'border border-secondary text-dark px-2 rounded';
                        case 12:
                            return 'border border-warning text-dark px-2 rounded';
                    }
                }
            ]
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MembershipRequest::class);

        CRUD::field('id')->attributes(['readonly' => 'readonly'])->size(4);
        CRUD::field('osoba_id')->ajax(TRUE)->attribute('ime_prezime_licence')->size(8);
        CRUD::field('status_id')->type('select_from_array')->options([10 => 'Aktivno', 11 => 'Neaktivno', 12 => 'U mirovanju'])->size(4);
        CRUD::field('started_at')->type('date')->format('DD.MM.YYYY')->size(4);
        CRUD::field('ended_at')->type('date')->format('DD.MM.YYYY')->size(4);
        CRUD::field('note');
//        CRUD::field('created_at');
//        CRUD::field('updated_at');

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
        $this->data['crud'] = $this->crud;
        return view('crud::osoba_clanarina_details_row', $this->data);
    }

    /*
 * Fetch operations
 * start
 */
    public function fetchOsoba()
    {
        return $this->fetch([
            'model' => \App\Models\Osoba::class, // required
            'searchable_attributes' => [],
            'paginate' => 10, // items to show per page
            'query' => function ($model) {
                $searchTerm = request()->input('q') ?? FALSE;
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
}
