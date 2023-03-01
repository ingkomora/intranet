<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Mail\PromenaPodataka\AdminReportEmail;
use App\Mail\PromenaPodataka\ConfirmationEmail;
use App\Models\Firma;
use App\Models\Log;
use App\Models\Opstina;
use App\Models\PromenaPodataka;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use function Composer\Autoload\includeFile;

trait PromenaPodatakaObradaBulkOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupPromenaPodatakaEmailObradaBulkRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/promenapodatakaobradabulk', [
            'as' => $routeName . '.promenapodatakaobradabulk',
            'uses' => $controller . '@promenapodatakaobradabulk',
            'operation' => 'promenapodatakaobradabulk',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPromenaPodatakaObradaBulkDefaults()
    {
        $this->crud->allowAccess('promenapodatakaobradabulk');

        $this->crud->operation('list', function () {
            if (backpack_user()->hasRole('admin')) {
                $this->crud->enableBulkActions();
                $this->crud->addButtonFromView('top', 'bulk.promenaPodatakaObrada', 'bulk.promenaPodatakaObrada', 'end');
            }
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function promenapodatakaobradabulk()
    {
        $this->crud->hasAccessOrFail('promenapodatakaobradabulk');
        $entries = $this->crud->getRequest()->input('entries');

        // declaration
        $result = [];
        $mail_data = new \stdClass();
        $log = new Log();

        foreach ($entries as $key => $id) {
            $mail_data->fields = [];
            $azurna_polja_u_bazi = TRUE;
            $obradjen = FALSE;
            $firmaOK = FALSE;
            $zahtev = PromenaPodataka::find($id);
            $osoba = $zahtev->licenca->osobaId;

            try {

                DB::beginTransaction();

                if (in_array($zahtev->obradjen, [3, 116, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142])) { // email
                    if ($osoba->kontaktemail != $zahtev->email) {
                        $osoba->kontaktemail = $zahtev->email;
                        $mail_data->fields['Email']['azurirano'] = $osoba->kontaktemail; // polje koje se azurira
                        $azurna_polja_u_bazi = FALSE;
                    } else {
                        $mail_data->fields['Email']['neazurirano'] = $osoba->kontaktemail; // polje koje je vec azurno
                    }
                    if ($osoba->isDirty('kontaktemail')) {
                        $osoba->save();
                    }
                    if ($osoba->wasChanged('kontaktemail') or $azurna_polja_u_bazi) {
                        $zahtev->datumobrade = Carbon::now()->format('Y-m-d H:i:s');
                        if ($zahtev->obradjen !== 3) {
                            $operater = User::find($zahtev->obradjen - 100)->name;
                            $napomena_string = "Zahtev kreirao operater $operater telefonskim pozivom člana.";
                            $zahtev->napomena .= is_null($zahtev->napomena) ? "" : ". " . $napomena_string;
                        }
                        $obradjen = TRUE;

                        $log->type = 'INFO';
                        $result[$log->type][$id]['message'] = 'Email adresa je uspešno ažurirana.';
                    } else {
                        $log->type = 'ERROR';
                        $result[$log->type][$id]['message'] = 'Greška prilikom ažuriranja email adrese.';
                    }

                } else if ($zahtev->obradjen === 0 or $zahtev->obradjen === 300) {
                    foreach ($zahtev->osoba_related_fields as $zahtev_field => $osoba_field) {
//                    if (!empty($zahtev->{$zahtev_field})) {
                        if ($osoba->{$osoba_field} !== $zahtev->{$zahtev_field}) {
                            $osoba->{$osoba_field} = $zahtev->{$zahtev_field};
                            $azurna_polja_u_bazi &= FALSE;
                            if ($zahtev_field == 'topstina_id') {
                                $mail_data->fields[$zahtev->public_fields['topstina_id']]['azurirano'] = $zahtev->opstina->ime; // polja koja se azuriraju
                            } else {
                                $mail_data->fields[$zahtev->public_fields[$zahtev_field]]['azurirano'] = $osoba->{$osoba_field}; // polja koja se azuriraju
                            }
                        } else {
                            $azurna_polja_u_bazi &= TRUE;
                            if ($zahtev_field == 'topstina_id') {
                                $mail_data->fields[$zahtev->public_fields['topstina_id']]['neazurirano'] = $zahtev->opstina->ime; // polja koja se azuriraju
                            } else {
                                $mail_data->fields[$zahtev->public_fields[$zahtev_field]]['neazurirano'] = $osoba->{$osoba_field}; // polja koja se azuriraju
                            }
                        }
//                    }
                    }


                    $zahtev_posta = json_decode($zahtev->posta);

                    $osoba->ulica = !empty($zahtev_posta->ulica) ? $zahtev_posta->ulica : null;
                    $osoba->broj = !empty($zahtev_posta->broj) ? $zahtev_posta->broj : null;
                    $osoba->podbroj = !empty($zahtev_posta->podbroj) ? $zahtev_posta->podbroj : null;
                    $osoba->posta_pb = !empty($zahtev_posta->pb) ? $zahtev_posta->pb : null;
                    $osoba->posta_opstina_id = !empty($zahtev_posta->opstina_id) ? $zahtev_posta->opstina_id : null;
                    $osoba->posta_drzava = !empty($zahtev_posta->drzava) ? $zahtev_posta->drzava : null;

                    if ($osoba->isDirty())
                        $osoba->save();


                    $result['azurna_polja_u_bazi'][$id] = $azurna_polja_u_bazi;

                    //snimanje firme
                    if (!empty($zahtev->mbfirm)) {
                        $firma_create['mb'] = $zahtev->mbfirm;
                        $firma_update = [];
                        foreach ($zahtev->firma_related_fields as $zahtev_field => $firma_field) {
                            if (!empty($zahtev_field)) {
                                $result['firme_polja_az'][$id] = $zahtev_field;
                                if ($zahtev_field == 'opstinafirm') {
                                    $firma_update[$firma_field] = Opstina::where('ime', $zahtev->{$zahtev_field})->value('id');
                                } else {
                                    $firma_update[$firma_field] = $zahtev->{$zahtev_field};
                                }
                            }
                        }

                        try {
//                    $firma = Firma::upsert($firmaArr, ['mb'], ['pib']); // TODO: UPSERT was introduced in PostgreSQL 9.5
                            $firma = Firma::updateOrCreate($firma_create, $firma_update);
                            $log->type = 'INFO';
                            $result[$log->type][$id]['message'] = "Podaci o firmi su uspešno ažurirani.";

                            $log->naziv = $result[$log->type][$id]['message'];
                            $firmaOK = TRUE;
                        } catch (\Exception $e) {
                            $log->type = 'ERROR';
                            $result[$log->type][$id]['message'] = "Greška prilikom ažuriranja firme. Exception " . $e->getMessage();

                            $log->naziv = $result[$log->type][$id]['message'] . ".";
                            $result[$log->type][$id]['message'] = $e->getMessage();
                        }
                    } else {
                        $log->type = 'INFO';
                        $result[$log->type][$id]['message'] = "Nezaposlen ili peznioner.";
                        $firmaOK = TRUE;
                    }

                    if ($firmaOK) {
                        if ($osoba->isDirty()) {
                            $osoba->save();
                        }
                        if ($osoba->wasChanged() or $azurna_polja_u_bazi) {
                            $obradjen = TRUE;

                            $log->type = 'INFO';
                            $result[$log->type][$id]['message'] .= ' Podaci o osobi su uspešno ažurirani.';
                        } else {
                            $log->type = 'ERROR';
                            $result[$log->type][$id]['message'] = 'Greška prilikom ažuriranja osobe.';
                        }
                    }

                }

                if ($obradjen) {
                    $zahtev->datumobrade = Carbon::now()->format('Y-m-d H:i:s');
                    $zahtev->obradjen = 1;
                    $zahtev->save();

                    // zbog starih zahteva, pre nego sto je povezana prijava za requestom
                    $request = $zahtev->request;
                    if (!empty($request)) {
                        $request->status_id = REQUEST_FINISHED;
                        $request->save();
                    }

                    $mail_data->zahtev = $zahtev;
                    $mail_data->osoba = $osoba;

                    try {
                        Mail::to($osoba->kontaktemail ?? '')
                            ->send(new ConfirmationEmail($mail_data));

                        $log->type = 'INFO';
                        $log->naziv .= " Poslat konfirmacioni mejl na: $osoba->kontaktemail, podnosilac: $osoba->full_name";

                    } catch (\Exception $e) {
                        $mail_data->error['message'] = $e->getMessage();
                        Mail::to('izmeneadresa@ingkomora.rs')
                            ->send(new AdminReportEmail($mail_data));

                        $log->type = 'ERROR';
                        $log->naziv .= " Greška prilikom slanja konfirmacionog mejla na: $osoba->kontaktemail, podnosilac: $osoba->full_name";
                    }
                }
                $log->log_status_grupa_id = PODACI;
                $log->loggable()->associate($zahtev);
                $log->save();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
            }
        }

        return $result;
    }
}
