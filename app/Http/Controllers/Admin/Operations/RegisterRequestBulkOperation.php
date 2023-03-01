<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Admin\ZavodjenjeController;
use App\Models\Log;
use Illuminate\Support\Facades\Route;
use function Composer\Autoload\includeFile;

trait RegisterRequestBulkOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRegisterRequestBulkRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/registerrequestbulk', [
            'as' => $routeName . '.registerrequestbulk',
            'uses' => $controller . '@registerRequestBulk',
            'operation' => 'registerrequestbulk',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRegisterRequestBulkDefaults()
    {
        $this->crud->allowAccess(['registerrequestbulk']);

        $this->crud->operation('list', function () {
            if (backpack_user()->hasPermissionTo('zavedi')) {
                $this->crud->enableBulkActions();
                $this->crud->addButtonFromView('top', 'bulk.registerRequest', 'bulk.registerRequest', 'end');
            }
        });
    }


    /**
     * @return array
     */
    public function registerRequestBulk(): array
    {
        $this->crud->hasAccessOrFail('registerrequestbulk');
//        $entries = $this->crud->getRequest()->input('entries');

        // declaration
        $data = $this->crud->getRequest()->all();
        $type = str_replace('admin/registerrequest', '', $data['route']);
        $result = [];
        $mail_data = new \stdClass();
        $log = new Log();
        $controller = new ZavodjenjeController();
        $result = $controller->zavedi($type, $data);

        return $result;
    }
}
