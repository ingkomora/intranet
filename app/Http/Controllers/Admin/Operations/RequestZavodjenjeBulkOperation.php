<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Admin\ZavodjenjeController;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use function Composer\Autoload\includeFile;

trait RequestZavodjenjeBulkOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRequestZavodjenjeBulkRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/requestzavodjenjebulk', [
            'as' => $routeName . '.requestzavodjenjebulk',
            'uses' => $controller . '@requestzavodjenjebulk',
            'operation' => 'requestzavodjenjebulk',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRequestZavodjenjeBulkDefaults()
    {
        $this->crud->allowAccess('requestzavodjenjebulk');

        $this->crud->operation('list', function () {
            if (backpack_user()->hasRole('admin')) {
                $this->crud->enableBulkActions();
                $this->crud->addButtonFromView('top', 'requestzavodjenjebulkbutton', 'requestzavodjenjebulkbutton', 'end');
            }
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return array
     */
    public function requestzavodjenjebulk()
    {
        $this->crud->hasAccessOrFail('requestzavodjenjebulk');
//        $entries = $this->crud->getRequest()->input('entries');
        $data = $this->crud->getRequest()->all();

        // declaration
        $result = [];
        $mail_data = new \stdClass();
        $log = new Log();

        $contoller = new ZavodjenjeController();
        $result = $contoller->zavedi('clanstvo', $data);


        return $result;
    }
}
