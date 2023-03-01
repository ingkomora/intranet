<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Libraries\RegistryLibrary;
use App\Models\Membership;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Trait for creating requests for stopping membership due to non-payment of membership fees
 * Trait CreateRequestStopMembershipBulkOperation
 * @package App\Http\Controllers\Admin\Operations
 */
trait CreateRequestStopMembershipBulkOperation
{

    private $request_cancelation_membership = 2; // Prekid članstva (usled neplaćanja članarine)
    private $document_request_cancelation_membership = 2; // Zahtev za prekid članstva (usled neplaćanja članarine)

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupCreateRequestStopMembershipBulkRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/create-request-stop-membership', [
            'as' => $routeName . '.createRequestStopMembership',
            'uses' => $controller . '@createRequestStopMembership',
            'operation' => 'createrequeststopmembership',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupCreateRequestStopMembershipBulkDefaults()
    {
        $this->crud->allowAccess(['createrequeststopmembership']);

        $this->crud->operation('list', function () {
            if (backpack_user()->hasPermissionTo('update-memberships-evidence')) {
                $this->crud->enableBulkActions();
                $this->crud->addButtonFromView('top', 'bulk.createRequestStopMembership', 'bulk.createRequestStopMembership', 'end');
            }
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return array
     */
    public function createRequestStopMembership()
    {
        $this->crud->hasAccessOrFail('createrequeststopmembership');

        // creating collection from entries
        $entries = collect($this->crud->getRequest()->input('entries'));

        // variables
        $result = [];
        $memberships = $entries->map(fn($entry) => Membership::find($entry));

        // main
        foreach ($memberships as $membership) {
            try {
                DB::beginTransaction();

                // only active membership
                if ($membership->status_id != MEMBERSHIP_STARTED)
                    throw new \Exception("Zahtev nije kreiran jer članstvo nije aktivno.");


                // check if request has been already created and not finished
                if ($this->doesRequestExist($membership))
                    throw new \Exception("Zahtev nije kreiran jer već postoji ili nije završen.");


                // retrieving request (get or create)
                $request = $this->getRequest($membership);


                // create document
                $this->createDocument($request, $document_request_cancelation_membership);


                $result['affected'][] = $membership->id;
//                $result['affected'][$membership->id]['request_id'] = $request->id;
//                $result['affected'][$membership->id]['message'] = "Zahtev je uspešno kreiran";
                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                $result['not_affected'][] = $membership->id;
//                $result['not_affected'][$membership->id][] = $e->getMessage();
            }

        }

        return $result;
    }


    private function doesRequestExist(Model $membership): bool
    {
        $request = Request::where('request_category_id', $this->request_cancelation_membership) // Prekid članstva (usled neplaćanja članarine)
        ->where('osoba_id', $membership->osoba_id)
            ->where('requestable_type', 'App\Models\Membership')
            ->where('requestable_id', $membership->id)
            ->whereIn('status_id', [REQUEST_CREATED, REQUEST_SUBMITED, REQUEST_IN_PROGRESS])
            ->first();

        return !is_null($request);
    }

    private function getRequest(Model $membership): Request
    {
        // trying to retrieve request
        $request = $this->doesRequestExist($membership);

        // creating request if there is no one
        if (is_null($request))
            $request = $this->createRequest($membership);

        return $request;
    }


    private function createRequest(Model $membership): Request
    {
        $request = new Request();
        $request->osoba_id = $membership->osoba_id;
        $request->request_category_id = $this->request_cancelation_membership; // Prekid članstva (usled neplaćanja članarine)
        $request->status_id = REQUEST_SUBMITED;
        $request->note = 'Request was created using bulk operation from MembershipCrudController.';

        // associate request with belonging membership
        $request->requestable()->associate($membership);
        $request->save();

        return $request;

    }

    /**
     * @throws \Exception
     */
    private function createDocument(Request $request, int $document_category_id): bool
    {

        if (!RegistryLibrary::documentExists($request, $document_category_id)) {

            $broj_dokumenta = RegistryLibrary::getRegistryNumber($request, $document_category_id);
            $datum_dokumenta = Carbon::parse($request->created_at)->format('Y-m-d');

            RegistryLibrary::createDocument($request, $document_category_id, $datum_dokumenta, $broj_dokumenta);

        } else {
            return FALSE;
        }
    }
}
