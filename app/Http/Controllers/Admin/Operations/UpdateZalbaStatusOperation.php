<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Requests\UpdateZalbaStatusRequest;
use App\Libraries\Helper;
use App\Models\Log;
use App\Models\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

trait UpdateZalbaStatusOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupUpdateLicencaStatusRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/updatezalbastatus', [
            'as' => $routeName . '.getUpdateZalbaStatus',
            'uses' => $controller . '@getUpdateZalbaStatusForm',
            'operation' => 'updatezalbastatus',
        ]);
        Route::post($segment . '/{id}/updatezalbastatus', [
            'as' => $routeName . '.postUpdateZalbaStatus',
            'uses' => $controller . '@postUpdateZalbaStatusForm',
            'operation' => 'updatezalbastatus',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupUpdateZalbaStatusDefaults()
    {
        $this->crud->allowAccess(['updatezalbastatus']);

        $this->crud->operation('updatezalbastatus', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            if (backpack_user()->hasPermissionTo('azuriraj status zalbe')) {
                $this->crud->addButtonFromView('line', 'updateZalbaStatus', 'updateZalbaStatus', 'end');
            }
        });
    }

    public function getUpdateZalbaStatusForm(int $id): string
    {
        $this->crud->hasAccessOrFail('updatezalbastatus');

        if (backpack_user()->hasPermissionTo('azuriraj status zalbe')) {
            $this->crud->addButtonFromView('line', 'updateZalbaStatus', 'updateZalbaStatus', 'end');
        }

        /**
         * Define Columns that are visible in MembershipApproving operation
         */
        $this->crud->addFields($this->fields_definition_operation_array);

        $this->crud->removeSaveActions(['save_and_edit', 'save_and_new', 'save_and_back']);
        $this->crud->addSaveAction([
            'name' => 'update_zalba_status',
            'redirect' => function ($crud, $request, $itemId) {
                return $crud->route;
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Snimi status žalbe', // override text appearing on the button
            // You can also provide translatable texts, for example:
            // 'button_text' => trans('backpack::crud.save_action_one'),
            'visible' => function ($crud) {
                return TRUE;
            }, // customize when this save action is visible for the current operation
            'referrer_url' => function ($crud, $request, $itemId) {
                return $crud->route;
            }, // override http_referrer_url
            'order' => 1, // change the order save actions are in
        ]);
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());
        // get the info for that entry
        $this->data['entry'] = $this->crud->getCurrentEntry();
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = 'Update zalba status ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        return view('vendor.backpack.crud.operations.updatezalbastatusform', $this->data);
    }

    public function postUpdateZalbaStatusForm(UpdateZalbaStatusRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->crud->hasAccessOrFail('updatezalbastatus');
//dd($request->all());
        $validated = $request->validated();
        $request = Request::find($validated['id']);
        $osoba = $request->osoba;
        try {
            // samo request koji ima status_id = REQUEST_IN_PROGRESS
            if ($request->status_id == REQUEST_CREATED or $request->status_id == REQUEST_SUBMITED) {
                \Alert::error("<b>GREŠKA 1!</b><br><br><b>Nije moguće ažurirati status zahteva.</b><br>Zahtev $request->id nema odgovarajući status.")->flash();
                return \Redirect::to($this->crud->route);
            }

            $log = new Log();
            $log_data = [];
            $log_data['osoba'] = $osoba->ime_prezime_lib;
            $log_data['app'] = $this->crud->getOperation();

            $request->status_id = $validated['status_id'];
            $request->save();

            $log->naziv = json_encode($log_data, JSON_UNESCAPED_UNICODE);
            $log->log_status_grupa_id = REQUESTS;
            $log->type = "INFO";
            $log->loggable()->associate($request);
            $log->save();

            // show a success message
            \Alert::success('Moderation saved for this entry.')->flash();
        } catch
        (\Exception $e) {
            \Alert::error("<b>GREŠKA!</b><br><br>Status nije ažuriran. {$e->getMessage()}")->flash();
        }
        return \Redirect::to($this->crud->route);
    }
}
