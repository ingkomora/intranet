<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Log;
use App\Models\Osoba;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

trait UnlockMembershipFeeRegistrationOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupUnlockMembershipFeeRegistrationRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/{id}/unlockmembershipfeeregistration', [
            'as' => $routeName . '.unlockMembershipFeeRegistration',
            'uses' => $controller . '@unlockMembershipFeeRegistration',
            'operation' => 'unlockmembershipfeeregistration',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupUnlockMembershipFeeRegistrationDefaults()
    {
        $this->crud->allowAccess(['unlockmembershipfeeregistration']);

        $this->crud->operation('unlockmembershipfeeregistration', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            if (backpack_user()->hasPermissionTo('otkljucaj clanarinu')) {
                $this->crud->addButtonFromView('line', 'unlockMembershipFeeRegistration', 'unlockMembershipFeeRegistration', 'end');
            }
        });
    }

    public function unlockMembershipFeeRegistration($id)
    {
        $result = new \stdClass();
        $this->crud->hasAccessOrFail('unlockmembershipfeeregistration');
        $osoba = Osoba::find($id);
        if ($osoba->clan <> MEMBER_TO_DELETE) {
            \Alert::error("<b>GREŠKA 1!</b><br><br><b>Nije moguće ažurirati status članstva za osobu $osoba->full_name.</b><br>Osoba $osoba->id nema odgovarajući status članstva.")->flash();
            return \Redirect::to($this->crud->route);
        }
        try {
            DB::beginTransaction();

            $log = new Log();
            $log_data = [];
            $log_data['osoba'] = $osoba->ime_prezime_lib;
            $log_data['app'] = $this->crud->getOperation();

            $osoba->clan = MEMBER;
//            $result->osoba = $osoba->full_name;

            if ($osoba->save()) {
                DB::commit();
                // show a success message
                $result->message = "Članarina za osobu $osoba->full_name je sada otključana";

                $log->naziv = json_encode($log_data, JSON_UNESCAPED_UNICODE);
                $log->log_status_grupa_id = CLANSTVO;
                $log->type = "INFO";
                $log->loggable()->associate($osoba);
                $log->save();
            } else {
                DB::rollBack();
                $result->message = "<b>GREŠKA 2!</b><br><br>Članarina za osobu $osoba->full_name nije otključana.";
            }
        } catch
        (\Exception $e) {
            DB::rollBack();
            $result->message = "<b>GREŠKA 3!</b><br><br>Status članstva nije promenjen. {$e->getMessage()}";
        }
//        dd($result);
        return response()->json($result);

    }
}
