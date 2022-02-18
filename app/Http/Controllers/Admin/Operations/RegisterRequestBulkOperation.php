<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Admin\ZavodjenjeController;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
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
            'uses' => $controller . '@registerrequestbulk',
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
            if (backpack_user()->hasRole('admin') OR backpack_user()->hasPermissionTo('zavedi')) {
                $this->crud->enableBulkActions();
                $this->crud->addButtonFromView('top', 'registerrequestbulkbutton', 'registerrequestbulkbutton', 'end');
            }
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return array
     */
    public function registerrequestbulk()
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
