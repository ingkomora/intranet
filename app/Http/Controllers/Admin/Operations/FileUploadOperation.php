<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Requests\FileUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use CRUD;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;

trait FileUploadOperation
{

    protected $libraries;
    protected $path;
    protected $class_path;
    protected $methods;
    protected $action;

    /**
     * FileUploadOperation constructor.
     * @throws \ReflectionException
     */
    public function __construct()
    {
        parent::__construct();

        $this->action = \request()->segment(2);

        $this->path = app_path() . "/Libraries/";
        $this->class_path = "App\Libraries\\";

        if (!is_null($this->action)) {

            $this->libraries = $this->getLibraries();

            $this->methods = $this->getMethods($this->getLibrary());
        }

    }


    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupFileUploadRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/file-upload', [
            'as' => $routeName . '.getFileUploadForm',
            'uses' => $controller . '@getFileUploadForm',
            'operation' => 'fileUpload',
        ]);
        Route::post($segment . '/file-upload', [
            'as' => $routeName . '.postFileUpload',
            'uses' => $controller . '@postFileUpload',
            'operation' => 'fileUpload',
        ]);

    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupFileUploadDefaults()
    {
        $this->crud->allowAccess(['fileUpload']);


        $this->crud->operation('fileUpload', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButton('top', 'fileUploadButton', 'view', 'backpack::crud.buttons.fileUploadButton');
        });

    }

    public function getFileUploadForm()
    {
        $this->crud->hasAccessOrFail('fileUpload');
        $this->crud->setValidation(FileUploadRequest::class);


        // removing all fields from crud
        $this->crud->removeAllFields();

        // defining fields that this operation requires
        CRUD::field('file')->type('upload')->upload(TRUE);
        CRUD::field('library')->type('select_from_array')->options($this->getLibrary(TRUE))->size(6);
        CRUD::field('method')->type('select_from_array')->options($this->methods)->size(6)->label('Action');

        // setting up save action for file upload operation
        $this->crud->removeSaveActions(['save_and_edit', 'save_and_new', 'save_and_back', 'save_and_preview']);

        $this->crud->addSaveAction([
            'name' => 'save_and_back_custom',
            'redirect' => redirect()->back(), // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Execute', // override text appearing on the button

        ]);


        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = $this->crud->getTitle();

        return view('vendor.backpack.crud.operations.fileUploadForm', $this->data);
    }


    /**
     * @throws \Exception
     */
    public function postFileUpload(Request $request)
    {
        $this->crud->hasAccessOrFail('fileUpload');

        $file = $request->file('file');
        $library = Str::camel($request->input('library'));
        $method = Str::camel($request->input('method'));

        if (is_null($file))
            throw new \Exception("There is no file in request.");

        $import = new \App\Imports\ExcelImport();


        try {

            $collection = ($import->toCollection($file))->first();

            if (!$collection->first()->has('import'))
                // excel doesnt contain import column
                $data = $collection->toArray();
            else
                // excel has import column
                $data = $collection->where('import', 1)->toArray();

            if (empty($data))
                throw new \Exception("There is no eligible data in excel to be imported.");


            $class = ucfirst($this->class_path . $library);


            // Dynamically calling action and getting result
            $result = $class::$method($data);


            // Notify user about action result
            $this->sendResultToMail($method, $result);


            // show a success message
            \Alert::success('Uspešno završena akcija.')->flash();

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            // show a error message
            \Alert::error($e->failures())->flash();

        }


        return \Redirect::to($this->crud->route);


    }


    private function getLibraries(): array
    {
        $files = File::files($this->path);

        foreach ($files as $file)
            $libraries[Str::kebab($file->getFilenameWithoutExtension())] = $file->getFilenameWithoutExtension();

        return $libraries;
    }

    private function getLibrary($array = FALSE)
    {
        $result = null;

        foreach ($this->libraries as $library) {
            if (strstr($library, ucfirst($this->action)))
                if ($array) {
                    $result[Str::kebab($library)] = $library;
                } else {
                    $result = $library;
                }
        }

        return $result;
    }

    /**
     * @param $library
     * @return array|null
     * @throws \ReflectionException
     */
    private function getMethods($library): ?array
    {
        $result = null;

        if (!class_exists($this->class_path . $library))
            return $result;

        $class = new ReflectionClass($this->class_path . $library);

        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method)
            $result[Str::kebab($method->name)] = $method->name;

        return $result;
    }

    private function sendResultToMail($method, $result, array $to = []): void
    {


        // parsing result
        $subject = ucfirst(strtolower(implode(' ', preg_split('/(?=[A-Z])/', $method))));
        $body = '';
        $errors = isset($result['error']) and count($result['error']) > 0;
        $success = isset($result['success']) and count($result['success']) > 0;

        if ($errors) {

            $body .= "==================== ERRORS =======================\n\n";
            foreach ($result['error'] as $request_id => $value) {
                $body .= "$request_id -> $value\n";
            }
        }

        if ($success) {

            if ($errors)
                $body .= "\n\n==================== SUCCESS =======================\n\n";

            foreach ($result['success'] as $request_id => $value) {
                $body .= "$request_id -> $value\n";
            }
        }


        if (count($to) === 0)
            $to[] = backpack_user()->email;


        // send raw mail
        Mail::raw(
            $body,
            function ($message) use ($to) {
                $message
                    ->to($to)
                    //->cc([])
                    ->subject($subject);
            }
        );
    }


}
