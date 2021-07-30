<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Libraries\Helper;
use http\Env\Response;
use Illuminate\Support\Facades\Route;

trait UpdateLicencaStatusOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupUpdateLicencaStatusRoutes($segment, $routeName, $controller)
    {
        Route::post($segment.'/{id}/updatelicencastatus', [
            'as'        => $routeName.'.updatelicencastatus',
            'uses'      => $controller.'@updatelicencastatus',
            'operation' => 'updatelicencastatus',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupUpdateLicencaStatusDefaults()
    {
        $this->crud->allowAccess('updatelicencastatus');

        $this->crud->operation('updatelicencastatus', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'updatelicencastatus', 'view', 'crud::buttons.updatelicencastatus');

//            $this->crud->addButton('line', 'updatelicencastatus', 'view', 'crud::buttons.updatelicencastatus');
            $this->crud->addButtonFromView('line', 'updatelicencastatus', 'updatelicencastatus', 'end');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function updatelicencastatus($id)
    {
        $this->crud->hasAccessOrFail('updatelicencastatus');
        // prepare the fields you need to show
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? 'updatelicencastatus '.$this->crud->entity_name;
        $h = new Helper();
        $result = $h->azurirajLicenceOsobe($id);

//dd($result);
        return response()->json($result);
//        return response()->json($this->data['entry']->toArray());
        // load the view
//        return view("crud::operations.updatelicencastatus", $this->data);
    }
}
