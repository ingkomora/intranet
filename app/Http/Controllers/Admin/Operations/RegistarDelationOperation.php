<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Requests\RegistarDelationRequest;
use App\Http\Requests\RequestMembershipApprovingRequest;
use App\Libraries\LibLibrary;
use App\Libraries\RegistarLibrary;
use App\Mail\Memberships\AdminReportEmail;
use App\Mail\Memberships\ConfirmationEmail;
use App\Models\Clanarina;
use App\Models\ClanarinaOld;
use App\Models\Document;
use App\Models\Log;
use App\Models\Membership;
use App\Models\Registry;
use App\Models\Request;
use CRUD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use function Composer\Autoload\includeFile;

trait RegistarDelationOperation
{

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRegistarDelationRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/registar-delation', [
            'as' => $routeName . '.getRegistarDelation',
            'uses' => $controller . '@getRegistarDelationForm',
            'operation' => 'registardelation',
        ]);
        Route::post($segment . '/{id}/registar-delation', [
            'as' => $routeName . '.postRegistarDelation',
            'uses' => $controller . '@postRegistarDelationForm',
            'operation' => 'registardelation',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRegistarDelationDefaults()
    {

        $this->crud->allowAccess(['registardelation']);

        $this->crud->operation('registardelation', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            if ($this->crud->getModel()->translationEnabled()) {
                $this->crud->addField([
                    'name' => 'locale',
                    'type' => 'hidden',
                    'value' => request()->input('locale') ?? app()->getLocale(),
                ]);
            }
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation(['list', 'show'], function () {
            if (backpack_user()->hasPermissionTo('registar-deletion'))
                $this->crud->addButtonFromView('line', 'registardelationbutton', 'registardelationbutton', 'end');
        });

        $this->crud->enableGroupedErrors();
        $this->crud->enableInlineErrors();
    }

    /**
     * Show the view for performing the operation.
     *
     * @param int $id
     * @return string
     */
    public function getRegistarDelationForm(int $id): string
    {
        $this->crud->hasAccessOrFail('registardelation');
        CRUD::setValidation(RegistarDelationRequest::class);

        if (!backpack_user()->hasPermissionTo('registar-deletion')) {
            $this->crud->denyAccess(['registardelation']);
        }

        /**
         * Define Columns that are visible in MembershipApproving operation
         */
        $this->crud->addFields(static::getFields());


        $this->crud->removeSaveActions(['save_and_edit', 'save_and_new', 'save_and_back', 'save_and_preview']);

        $this->crud->addSaveAction([
            'name' => 'execute',
            'redirect' => function ($crud, $request, $itemId) {
                return $crud->route;
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Execute', // override text appearing on the button
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
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());
        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['title'] = 'Obrada - ' . $this->crud->entity_name;
        $this->data['id'] = $id;

        return view('vendor.backpack.crud.operations.registardelationform', $this->data);
    }


    /**
     * @param RegistarDelationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function postRegistarDelationForm(RegistarDelationRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->crud->hasAccessOrFail('registardelation');

        $validated = $request->validated();

        $result = RegistarLibrary::brisanjeLicno($validated);

        // Notify user about action result
        $this->sendResultToMail($result, $validated['id']);


        if (isset($result['error']))
            // show a error message
            \Alert::error("{$result['error']}")->flash();

        if (isset($result['success']))
            // show a success message
            \Alert::success("{$result['success']}")->flash();


        return \Redirect::to($this->crud->route);

    }


    /**
     * @return array
     */
    private static function getFields(): array
    {
        return [
            'id' => [
                'name' => 'id',
//            'type' =>'hidden',
                'attributes' => ['readonly' => 'readonly']
            ],
            'osoba_id' => [
                'name' => 'osoba',
                'type' => 'relationship',
                'ajax' => TRUE,
                'label' => 'Ime prezime (jmbg)',
                'attribute' => 'ime_prezime_jmbg',
                'attributes' => ['readonly' => 'readonly', 'disabled' => 'disabled'],
            ],
            [
                'name' => 'resenje_broj',
                'type' => 'text',
                'label' => 'Broj rešenja',
            ],
            [
                'name' => 'resenje_datum',
                'type' => 'date_picker',
                'label' => 'Datum rešenja',
                'date_picker_options' => [
                    'todayBtn' => 'linked',
                    'format' => 'dd.mm.yyyy',
                ],
            ],
        ];
    }


    /**
     * @param array $result
     * @param int $request_id
     * @param array $to
     */
    private function sendResultToMail(array $result, int $request_id, array $to = []): void
    {

        $body = '';
        $subject = "Brisanje podataka upisanih u Registar po zahtevu $request_id";
        if (count($to) === 0)
            $to[] = backpack_user()->email;


        if (isset($result['error'])) {
            $body .= "==================== ERRORS =======================\n\n";
            $body .= $result['error'];
        }

        if (isset($result['success'])) {
            $body .= "==================== SUCCESS =======================\n\n";
            $body .= $result['success'];
        }


        // send raw mail
        Mail::raw(
            $body,
            function ($message) use ($to, $subject) {
                $message
                    ->to($to)
                    //->cc([])
                    ->subject($subject);
            }
        );
    }
}
