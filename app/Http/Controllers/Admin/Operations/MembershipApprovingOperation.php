<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Requests\RequestMembershipApprovingRequest;
use App\Libraries\LibLibrary;
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

trait MembershipApprovingOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupMembershipApprovingRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/membershipapproving', [
            'as' => $routeName . '.getMembershipApproving',
            'uses' => $controller . '@getMembershipApprovingForm',
            'operation' => 'membershipapproving',
        ]);
        Route::post($segment . '/{id}/membershipapproving', [
            'as' => $routeName . '.postMembershipApprovingForm',
            'uses' => $controller . '@postMembershipApprovingForm',
            'operation' => 'membershipapproving',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupMembershipApprovingDefaults()
    {
        $this->crud->allowAccess('membershipapproving');

        $this->crud->operation('membershipapproving', function () {
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
            $this->crud->addButtonFromView('line', 'membershipapprovingbutton', 'membershipapprovingbutton', 'end');
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
    public function getMembershipApprovingForm(int $id): string
    {
        $this->crud->hasAccessOrFail('membershipapproving');
        CRUD::setValidation(RequestMembershipApprovingRequest::class);

        if (!backpack_user()->hasRole('admin') && !backpack_user()->hasPermissionTo('odobri clana')) {
            $this->crud->denyAccess(['create', 'delete', 'update', 'membershipapproving']);
        }

        /**
         * Define Columns that are visible in MembershipApproving operation
         */
        $this->crud->addFields($this->fields_definition_operation_array);


        $this->crud->removeSaveActions(['save_and_edit', 'save_and_new', 'save_and_back']);

        $this->crud->addSaveAction([
            'name' => 'add_member',
            'redirect' => function ($crud, $request, $itemId) {
                return $crud->route;
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Unesi člana', // override text appearing on the button
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

        return view('vendor.backpack.crud.operations.membershipapprovingform', $this->data);
    }


    public function postMembershipApprovingForm(RequestMembershipApprovingRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->crud->hasAccessOrFail('membershipapproving');
        $validated = $request->validated();

        $request = Request::find($validated['id']);

        // samo request koji ima status_id = REQUEST_IN_PROGRESS
        if ($request->status_id != REQUEST_IN_PROGRESS) {
            \Alert::error("<b>GREŠKA 1!</b><br><br><b>Nije moguće odobriti članstvo.</b><br>Zahtev $request->id nema odgovarajući status.")->flash();
            return \Redirect::to($this->crud->route);
        }

        $odluka_datum = Carbon::parse($request->odluka_datum)->format('Y-m-d');

        $osoba = $request->osoba;

        // da li ima licence upisane u registar
        $osobaLicence = $osoba->licence;
        if ($osobaLicence->isEmpty()) {
            \Alert::error("<b>GREŠKA 2!</b><br><br><b>Nije moguće odobriti članstvo.</b><br>Osoba {$osoba->ime_roditelj_prezime} nema ni jednu evidentiranu licencu.<br>Unesite licence.")->flash();
            return \Redirect::to($this->crud->route);
        }


        $zg = $osoba->zvanjeId->zvanje_grupa_id;
        $registry = Registry::whereHas('registryDepartmentUnit', function ($q) use ($zg) {
            $q->where('label', "02-$zg");
        })
            ->whereHas('requestCategories', function ($q) use ($request) {
                $q->where('registry_request_category.request_category_id', 1);
            })
            ->get()[0];
        $registry->counter++;
        $registry_num = $registry->registryDepartmentUnit->label . "-" . $registry->base_number . "/" . date("Y", strtotime($odluka_datum)) . "-" . $registry->counter;

        $log = new Log();
        $log_data = [];
        $log_data['osoba'] = $osoba->ime_prezime_lib;
        $log_data['app'] = $this->crud->getOperation();

        $membershipOK = $requestOK = $documentOK = $registryOK = $osobaOK = $clanarinaOK = FALSE;
        $clanarinaOldOK = TRUE;
        $create_membership = TRUE;

        $message = '';

        // ukoliko je tosoba.clan = 1 ne bi smelo da uopste ima ovaj zahtev
        if ($osoba->clan == 1) {
            \Alert::error("<b>GREŠKA 3!</b><br><br>Članstvo po zahtevu broj {$request->id} ne može biti kreirano jer je clan = 1.<br><br>Prenesite ovu poruku administratoru.")->flash();
            return \Redirect::to($this->crud->route);
        }
        $existingMemberships = $osoba->memberships;
        if ($existingMemberships->isNotEmpty()) {
            // provera rekorda u memberships tabeli
            foreach ($existingMemberships as $existingMembership) {
                // membership active (correct)
                if ($existingMembership->status_id == MEMBERSHIP_STARTED) {
                    $create_membership = FALSE;
                    if (empty($existingMembership->ended_at)) {
//                    CLAN NIJE JEDAN ALI POSTOJI AKTIVAN MEMBERSHIP PA JE TO PROBLEM
                        $message = "<b>GREŠKA 4!</b><br><br>Membership po zahtevu broj {$request->id} je već kreiran, pa članstvo ne može biti odobreno jer je clan = $osoba->clan.<br><br>Prenesite ovu poruku administratoru.";
                    } else if (!empty($existingMembership->ended_at)) {
                        // membership (incorrect)
                        // show a error message
                        $message = "<b>GREŠKA 5!</b><br><br>Članstvo po zahtevu broj {$request->id} je već započeto ali ima datum prestanka " . Carbon::parse($existingMembership->ended_at)->format('d.m.Y.') . "<br><br>Obratite se administratoru.";
                    }
                    // membership (incorrect)
                } elseif ($existingMembership->status_id == MEMBERSHIP_ENDED and empty($existingMembership->ended_at)) {
                    // show a error message
                    $create_membership = FALSE;
                    $message = "<b>GREŠKA 6!</b><br><br>Članstvo po zahtevu broj {$request->id} je prekinuto ali nema datum prestanka.<br><br>Obratite se administratoru.";
                    //ako smo dosli dovde sa create_membership=TRUE, onda je membership status MEMBERSHIP_ENDED a ended_at nije empty
                } elseif ($existingMembership->status_id == MEMBERSHIP_SUSPENDED) {
                    $create_membership = FALSE;
                    $message = "<b>GREŠKA 7!</b><br><br>Članstvo po zahtevu broj {$request->id} je u statusu mirovanja.<br><br>Obratite se administratoru.";
                } elseif ($existingMembership->status_id == MEMBERSHIP_PROBLEM) {
                    $create_membership = FALSE;
                    $message = "<b>GREŠKA 8!</b><br><br>Članstvo po zahtevu broj {$request->id} ima problem.<br><br>Obratite se administratoru.";
                } else if ($request->requestable and $request->requestable->id == $existingMembership->id) {
                    $create_membership = FALSE;
                    $message = "<b>GREŠKA 9!</b><br><br>Membership po zahtevu broj {$request->id} već postoji.<br><br>Obratite se administratoru.";
                }

                if (!$create_membership) {
                    \Alert::error($message)->flash();
                    return \Redirect::to($this->crud->route);
                }
            }
        }

        // dokumenta kategorije odluka u vezi sa ovim zahtevom
        $documents = Document::where('documentable_id', $request->id)->where('document_category_id', 18)->get();
        if ($documents->count() > 0) {
//        greska ne moze da ima vise od 1 odluke za prijem u clanstvo
            if ($documents->count() > 1) {
                \Alert::error("<b>GREŠKA!</b><br><br><b>Nije moguće odobriti članstvo.</b><br>Postoji više odluka za prijem u članstvo po zahtevu {$request->id}.<br><br>Obratite se administratoru.")->flash();
                return \Redirect::to($this->crud->route);
            } else {
                $document = $documents[0];
            }
        } else {
            $document = new Document();
        }

        DB::beginTransaction();

        try {
            /*
             * proveri  membership OK
             * * * * * * * * * * * *
             * proveri  request mora da bude INPROGRESS 52 OK
             * * * * * * * * * * * *
             * proveri  documents:
             *          mora da ima zahtev koji mora da ima status REGISTERED 57
             * I FAZA dok ne napravimo generisanje odluke|resenja
             * ne sme da ima Odluka o prijemu u članstvo (document_category_id=18)
             * II FAZA kad se napravi generisanje odluke|resenja
             * mora da ima Odluka o prijemu u članstvo (document_category_id=18, status_id)
             * * * * * * * * * * * *
             * clanarina
             */

//        MEMBERSHIP:
            $membership = new Membership();
            $membership->osoba_id = $osoba->id;
            $membership->status_id = MEMBERSHIP_STARTED;
            $membership->started_at = $odluka_datum;
            $membership->note = "Odobreno članstvo iz aplikacije.";
            if ($membership->save()) {
                $membershipOK = TRUE;
                $log_data['membership'] = "Membership {$membership->id} created";
            }


//        REQUEST:      update status_id, requestable associate membership
            $request->status_id = REQUEST_FINISHED;
            $request->note = empty($request->note) ? 'Odobreno članstvo iz aplikacije' : "{$request->note}##Odobreno članstvo iz aplikacije";

            if ($request->save()) {
                $request->requestable()->associate($membership);
                $request->save();
                $requestOK = TRUE;
                $log_data['request'] = "Request {$request->id} updated";
            }


            // DOCUMENT:
            $document->document_category_id = 18; // odluka o prijemu u clanstvo
            $document->registry_id = $registry->id;
            $document->registry_number = $registry_num;
            $document->registry_date = $odluka_datum;
            $document->status_id = DOCUMENT_REGISTERED;
            $document->user_id = backpack_user()->id;
            $document->metadata = json_encode([
                "title" => "Odluka o prijemu u članstvo u IKS #{$request->id}",
                "author" => 'Inženjerska komora Srbije',
                "author_id" => '',
                "description" => "Za osobu: {$osoba->ime_roditelj_prezime}, lib: {$osoba->lib}",
                "category" => "Prijava clanstvo",
                "created_at" => $odluka_datum,
            ], JSON_UNESCAPED_UNICODE);
            $document->valid_from = $odluka_datum;
            $document->document_type_id = 1; // original

            if ($document->save()) {
                $document->barcode = "{$request->id}#{$document->id}#{$document->registry_number}#{$document->registry_date}";
                $document->documentable()->associate($request);
                $document->save();
                $documentOK = TRUE;

                $log_data['document'] = "Document {$document->id}, {$document->documentCategory->name}, registry number {$document->registry_number} date {$document->registry_date}, created";
            }

//        REGISTRY
            if ($registry->save()) $registryOK = TRUE;

//        CLANARINA
            $clanarina = Clanarina::firstOrNew(
                ['osoba' => $osoba->id, 'rokzanaplatu' => $odluka_datum],
                ['iznoszanaplatu' => 9500] // todo: dinamicki iz baze tclanarina_intervaliobracuna
            );

            if ($clanarina->save()) {
                $clanarinaOK = TRUE;

                $log_data['clanarina'] = $clanarina->wasRecentlyCreated ? "Kreirana " : "Ažurirana " . "članarina.";

                foreach ($osobaLicence as $licenca) {
                    $clanarina_old = ClanarinaOld::where('brlicence', $licenca->id)->first();
                    if (!$clanarina_old) {
                        $clanarina_old = ClanarinaOld::firstOrNew(
                            ['brlicence' => $licenca->id, 'rokzanaplatu' => $odluka_datum],
                            ['datumazuriranja_admin' => now()]
                        );
                        if (!$clanarina_old->save()) $clanarinaOldOK &= FALSE;
                    }

                }
            }

//        OSOBA: update clan = 1
            $osoba->clan = AKTIVAN;
            if ($osoba->save()) {

                $lib = new LibLibrary();
                $lib->dodeliJedinstveniLib($osoba->id, backpack_user()->id);

                $osobaOK = TRUE;
            }


            if ($membershipOK && $requestOK && $documentOK && $registryOK && $clanarinaOK && $clanarinaOldOK && $osobaOK) {

                $log->naziv = json_encode($log_data);
                $log->log_status_grupa_id = CLANSTVO;
                $log->type = "INFO";
                $log->loggable()->associate($request);

                DB::commit();

                // sending mail to new member
                try {
//            Mail::to($request->osoba->kontaktemail ?? '')
                    Mail::to(backpack_user()->email)
                        ->send(new ConfirmationEmail($request));
                } catch (\Exception $e) {
                    DB::rollBack();
                    Mail::to(backpack_user()->email)
                        ->send(new AdminReportEmail($request));
                }

                // show a success message
                \Alert::success("Članstvo osobe {$osoba->ime_roditelj_prezime} je aktivirano.")->flash();

            } else {
                DB::rollBack();

                \Alert::error("<b>GREŠKA!</b><br><br>Članstvo nije odobreno.")->flash();
            }

        } catch (\Exception $e) {
            DB::rollBack();

            \Alert::error("<b>GREŠKA!</b><br><br>Članstvo nije odobreno. {$e->getMessage()}")->flash();
        }

        return \Redirect::to($this->crud->route);

    }
}
