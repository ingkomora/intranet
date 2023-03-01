<?php namespace App\Http\Controllers\Admin\NestedResources;

use App\Http\Controllers\Admin\Operations\DocumentCancelationBulkOperation;
use App\Http\Controllers\Admin\DocumentCrudController;

class SiPrijavaDocumentCrudController extends DocumentCrudController
{
    use DocumentCancelationBulkOperation;

    public function setup()
    {
        parent::setup();

        // get the request_id parameter
        $id = \Route::current()->parameter('si_prijava_id');

        // set a different route for the admin panel buttons
        $this->crud->setRoute("admin/si/" . $id . "/document");


        // show only that request's documents
        $this->crud->addClause('where', function ($q) use ($id) {
            $q
                ->where('documentable_id', $id)
                ->where('documentable_type', "App\\Models\\SiPrijava");
        });
    }
}
