<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Document;
use App\Models\PromenaPodataka;
use App\Models\Request;
use App\Models\RequestExternal;
use App\Models\SiPrijava;
use App\Models\ZahtevLicenca;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use function Composer\Autoload\includeFile;

trait DocumentCancelationBulkOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDocumentCancelationBulkRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/document-cancelation', [
            'as' => $routeName . '.document-cancelation',
            'uses' => $controller . '@cancel',
            'operation' => 'documentcancelation',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupDocumentCancelationBulkDefaults()
    {
        $this->crud->allowAccess(['documentcancelation']);

        $this->crud->operation('list', function () {
            if (backpack_user()->hasPermissionTo('document-cancelation')) {
                $this->crud->enableBulkActions();
                $this->crud->addButtonFromView('top', 'bulk.documentCancelation', 'bulk.documentCancelation', 'end');
            }
        });
    }


    /**
     * @return array
     */
    public function cancel(): array
    {
        $this->crud->hasAccessOrFail('documentcancelation');

        $data = $this->crud->getRequest()->all();


        $entries = $data['entries'];
        if (isset($data['cancelRequest']))
            $cancel_request = (int)$data['cancelRequest'] == 1;
        else
            $cancel_request = FALSE;

        $result = [];


        foreach ($entries as $entry) {


            try {
                DB::beginTransaction();

                $document = Document::find($entry);
                $request = $this->getRequest($document->documentable->id);

                // should we cancel documents for FINISHED requests
                /* if ($request->{$this->getStatusColumnName($request)} == REQUEST_FINISHED)
                      throw new \Exception("Status za $request->id je \"ZAVRŠEN\" pa nije moguće stornirati pripradajuće dokumente");*/

                // does request has to be canceled
                if ($cancel_request)
                    if (!$this->cancelRequest($request))
                        throw new \Exception("Status za $request->id nije ažuriran");

                // set document status to canceled
                $document->status_id = DOCUMENT_CANCELED;
                if (!$document->save())
                    throw new \Exception("Dokument $document->id nije storniran");

                DB::commit();
                $result['info'][] = $request->id;

            } catch (\Exception $e) {
                DB::rollBack();
                $result['error'][$document->id] = $e->getMessage();
            }
        }

        return $result;
    }


    /*
    |--------------------------------------------------------------------------
    | LOCAL METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * @param int $id
     * @return Model
     */
    private function getRequest(int $id): Model
    {

        switch (\Request::segment(2)) {
            case 'request':
                $request = Request::find($id);
                break;
            case 'request-external':
                $request = RequestExternal::find($id);
                break;
            case 'si':
                $request = SiPrijava::find($id);
                break;
            case 'zahtevlicenca':
                $request = ZahtevLicenca::find($id);
                break;
            case 'promenapodataka':
                $request = PromenaPodataka::find($id);
                break;
        }

        return $request;
    }

    /**
     * @param Model $model
     * @return Model
     */
    private function getStatusColumnName(Model $model): string
    {
        switch ($model->request_category_id) {

            case 1:  //Prijem u članstvo
            case 2:  //Prekid članstva (usled neplaćanja članarine)
            case 3:  //Svečana forma licence
            case 4:  //Mirovanje članstva
            case 5:  //Prekid mirovanja članstva
            case 8:  //Uverenje o podacima upisanim u Registar
            case 9:  //Promena podataka upisanih u Registar
            case 10: //Promena ličnih podataka
            case 11: //Brisanje podataka upisanih u Registar (usled smrti)
            case 12: //Prekid članstva (usled smrti)
            case 13: //Prekid članstva (na lični zahtev)
            case 14: //Brisanje podataka upisanih u Registar (na lični zahtev)
                $status_col_name = 'status_id';
                break;
            case 6:  //Polaganje stručnog ispita i dobijanje licence
                $status_col_name = 'status_prijave';
                break;
            case 7:  //Sticanje licence
                $status_col_name = 'status';
                break;
            case 15: //IKS Mobnet
                $status_col_name = 'status_id';
                break;
        }

        return $status_col_name;

    }


    /**
     * @param Model $model
     * @return bool
     * @throws \Exception
     */
    private function cancelRequest(Model $model): bool
    {
        $status_col_name = $this->getStatusColumnName($model);
        $model->{$status_col_name} = REQUEST_CANCELED;


        return $model->save();

    }
}
