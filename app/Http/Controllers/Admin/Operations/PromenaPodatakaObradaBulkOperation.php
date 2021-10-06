<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Firma;
use App\Models\Opstina;
use App\Models\PromenaPodataka;
use App\Models\User;
use Carbon\Carbon;
use Cassandra\Exception\TruncateException;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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
            $this->crud->enableBulkActions();
            $this->crud->addButtonFromView('top', 'promenapodatakaobradabulkbutton', 'promenapodatakaobradabulkbutton', 'end');
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
        $result = [];
        foreach ($entries as $key => $id) {
            $obradjen = FALSE;
            $zahtev = PromenaPodataka::find($id);
            $osoba = $zahtev->licenca->osobaId;
            if (in_array($zahtev->obradjen, [3, 116, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142])) { // email
                $osoba->kontaktemail = $zahtev->email;
                if ($osoba->isDirty('kontaktemail')) {
                    $osoba->save();
                }
                if ($osoba->wasChanged('kontaktemail')) {
                    $zahtev->datumobrade = Carbon::now()->format('Y-m-d H:i:s');
                    if ($zahtev->obradjen !== 3) {
                        $operater = User::find($zahtev->obradjen - 100)->name;
                        $zahtev->napomena .= "Zahtev kreirao operater $operater telefonskim pozivom člana.";
                    }
                    $obradjen = TRUE;
                    $result['ok'][] = $id;
                } else {
                    $result['nok'][] = $id;
                    $result['message'][] = 'zahtev';
                }
            } else if ($zahtev->obradjen === 0 or $zahtev->obradjen === 300) {
                $azurna_polja_u_bazi = TRUE;
                foreach ($zahtev->osoba_related_fields as $zahtev_field => $osoba_field) {
                    if (!empty($zahtev_field)) {
                        if ($osoba->{$osoba_field} != $zahtev->{$zahtev_field}) {
                            $osoba->{$osoba_field} = $zahtev->{$zahtev_field};
                            $azurna_polja_u_bazi &= FALSE;
                        } else {
                            $azurna_polja_u_bazi &= TRUE;
                        }
                    }
                }
                $result['azurna_polja_u_bazi'][$id] = $azurna_polja_u_bazi;
                if ($osoba->isDirty()) {
                    $osoba->save();
                }
                if ($osoba->wasChanged() or $azurna_polja_u_bazi == TRUE) {
                    $obradjen = TRUE;
                    $result['ok'][] = $id;
                } else {
                    $result['nok'][] = $id;
                    $result['message'][] = 'osoba';
                }

                if (!empty($osoba->firma_mb)) {
                    $firmaArr['mb'] = $osoba->firma_mb;
                    $firmaArrUpdate = [];
                    foreach ($zahtev->firma_related_fields as $zahtev_field => $firma_field) {
                        if (!empty($zahtev_field)) {
                            $result['firme_polja_az'][$id] = $zahtev_field;
                            if ($zahtev_field == 'opstinafirm') {
                                $zahtev_field = Opstina::where('ime', $zahtev->{$firma_field})->get('id');
                            }
                            $firmaArr[$firma_field] = $zahtev->{$zahtev_field};
                            if ($zahtev_field != 'mbfirm') {
                                $firmaArrUpdate[] = $firma_field;
                            }
                        }
                    }
                    $firma = Firma::upsert($firmaArr, ['mb'], $firmaArrUpdate);
                    $result['firma'] = $firma;
                }
            }
            if ($obradjen) {
                $zahtev->datumobrade = Carbon::now()->format('Y-m-d H:i:s');
                $zahtev->obradjen = 1;
                $zahtev->save();
            }
        }
        return $result;
    }
}
