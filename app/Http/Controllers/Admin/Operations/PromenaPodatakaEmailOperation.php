<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Requests\PromenaPodatakaEmailRequest;
use App\Models\Mirovanje;
use App\Models\PrijavaPromenaPodataka;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\MirovanjeObradaRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use function PHPUnit\Framework\isEmpty;

trait PromenaPodatakaEmailOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupPromenaPodatakaEmailRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/promenapodatakaemail', [
            'as' => $routeName . '.getPromenaPodatakaEmail',
            'uses' => $controller . '@getPromenaPodatakaEmailForm',
            'operation' => 'promenapodatakaemail',
        ]);
        Route::put($segment . '/{id}/promenapodatakaemail', [
            'as' => $routeName . '.postPromenaPodatakaEmail',
            'uses' => $controller . '@postPromenaPodatakaEmailForm',
            'operation' => 'promenapodatakaemail',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPromenaPodatakaEmailDefaults()
    {
        $this->crud->allowAccess('promenapodatakaemail');

        $this->crud->operation('promenapodatakaemail', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            if ($this->crud->getModel()->translationEnabled()) {
                $this->crud->addField([
                    'name' => 'locale',
                    'type' => 'hidden',
                    'value' => request()->input('locale') ?? app()->getLocale(),
                ]);
            }
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButtonFromView('line', 'promenapodatakaemailbutton', 'promenapodatakaemailbutton', 'end');
        });

        $this->crud->enableGroupedErrors();
        $this->crud->enableInlineErrors();
    }

    /**
     * Define what happens when the PromenaPodatakaEmail operation is loaded.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|void
     */
    protected function getPromenaPodatakaEmailForm($id)
    {
        $this->crud->hasAccessOrFail('promenapodatakaemail');
        CRUD::setValidation(PromenaPodatakaEmailRequest::class);

        /**
         * Define Columns that are visible in PromenaPodatakaEmail operation
         */
        $this->crud->addFields($this->fields_definition_array);
        $this->crud->removeFields($this->remove_fields_definition_array);

        /*$this->crud->addSaveAction([
            'name' => 'save_and_back',
            'button_text' => 'Snimi i generiši predlog rešenja', // override text appearing on the button
        ]);*/

        $this->crud->removeSaveAction('save_and_edit');

        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());
        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = 'Obrada - ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        return view('vendor.backpack.crud.operations.promenapodatakaemailform', $this->data);
    }

    public function postPromenaPodatakaEmailForm(PromenaPodatakaEmailRequest $request, PrijavaPromenaPodataka $promenaPodataka)
    {
        $this->crud->hasAccessOrFail('promenapodatakaemail');

        $validated = $request->validated();

        $zahtev = PrijavaPromenaPodataka::find($request->id);

        $zahtev->datumobrade = Carbon::now()->format('Y-m-d H:i:s');
        $zahtev->napomena = $validated['napomena'];
        if (empty($validated['email'])) {
            $zahtev->obradjen = backpack_user()->id + 200;
        } else {
            $zahtev->obradjen = backpack_user()->id + 100;
            $zahtev->email = $validated['email'];
        }
        $zahtev->save();

        // show a success message
        \Alert::success('Zahtev je uspešno obrađen.')->flash();

        return \Redirect::to($this->crud->route);
    }
}
