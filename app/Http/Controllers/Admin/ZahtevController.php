<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ExcelImport;
use App\Libraries\Helper;
use App\Libraries\LibLibrary;
use App\Libraries\ProveraLibrary;
use App\Models\LicencaTip;
use App\Models\Log;
use App\Models\LogOsoba;
use App\Models\Osoba;
use App\Models\PrijavaClanstvo;
use App\Models\SiPrijava;
use App\Models\ZahtevLicenca;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use Doctrine\DBAL\Schema\AbstractAsset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use App\Models\Licenca;
use Session;


class ZahtevController extends Controller
{
    protected $data = []; // the information we send to the view
    protected $h;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->h = new Helper();
        $this->middleware(backpack_middleware());
    }

    public function unesi($action, $url = '')
    {
        if (Session::get('message') !== NULL) {
            $data['message'] = Session::get('message');
        }
        if (Session::get('status') !== NULL) {
            $data['status'] = Session::get('status');
        }
        $data['action'] = $action;
        $data['url'] = $url;

//        return view('unesi', $data);
        return view(backpack_view('unesi'), $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function obradizahtevsvecanaforma(Request $request)
    {
//        TODO U TABELU

        $file = $request->file('upload');

        $validated = $request->validate([
            'file' => 'required_without_all:licence,jmbgs',
            'licence' => 'required_without_all:file,jmbgs',
            'jmbgs' => 'required_without_all:file,licence',
            'datum' => 'required_without:file',
        ]);
        if (!is_null($file)) {
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $import = new ExcelImport();
//            $import->import($file);
            try {
                $collection = ($import->toCollection($file));
                $licence = $collection[0]->where('import', 1)->pluck('datumstampe', 'licenca')->toArray();
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                dd($failures);
            }
        } else {
            $rows = explode("\r\n", $request->licence);
            foreach ($rows as $row) {
                if (empty($request->datum)) {
                    $rowexpl = explode(",", $row);
                    $rowexpl[0] = trim($rowexpl[0]);
                    $rowexpl[1] = trim($rowexpl[1]);
                    $licence[$rowexpl[0]] = $rowexpl[1];
                } else {
                    $row = trim($row);
                    $request->datum = trim($request->datum);
                    $licence[$row] = $request->datum;
                }
            }
//            dd($licence);
        }
        $messageLicencaOK = 'Licence: ';
        $messageLicencaNOK = 'Licence: ';
        $messageZahtevOK = 'Zahtevi: ';
        $messageZahtevNOK = 'Zahtevi: ';
        $messageInfo = '';
        $flagOK = false;
        $flagNOK = false;
        $count = 0;
        $countOK = 0;
        $countNOK = 0;
        $dataPrint = ['dataOK' => [], 'dataNOK' => []];
        foreach ($licence as $licenca => $datumstampe) {
            $count++;
//                IMPORTUJU SE SAMO ZAHTEVI KAD VEC POSTOJI OSOBA SA LICENCAMA
//                ZA SADA SU SVE DODATE LICENCE AKTIVNE
//            UBUDUCE CE SE UBACIVATI I LICENCE AUTOMATSKI DOBIJENE NAKON POLOZENOG STRUCNOG ISPITA
//            $provera->getJmbgFromLicenca($licenca['broj']);

//dd($licenca);
            $status_grupa = LICENCE;
            $licencaO = Licenca::find($licenca);
//dd($licencaO->tipLicence->vrstaLicence->naziv);
            if (!is_null($licencaO)) {
                $osoba = $licencaO->osobaId;

                $data = new \stdClass();
                $ss = mb_strtoupper(mb_substr($osoba->roditelj, 0, 2));
                if ($ss == "LJ" || $ss == "NJ") {
//                if (mb_strtoupper(mb_substr($osoba->roditelj, 0, 2) == "LJ") || mb_strtoupper(mb_substr($osoba->roditelj, 0, 2) == "NJ")) {
                    $osoba->roditelj = mb_ucfirst(mb_substr($osoba->roditelj, 0, 2));
                } else {
                    $osoba->roditelj = mb_ucfirst(mb_substr($osoba->roditelj, 0, 1));
                }
                $data->osobaImeRPrezime = $this->h->iso88592_to_cirUTF($osoba->ime . " " . $osoba->roditelj . ". " . $osoba->prezime);
                $data->zvanje = $this->h->iso88592_to_cirUTF($osoba->zvanjeId->naziv);
                $data->zvanjeskr = $this->h->iso88592_to_cirUTF($osoba->zvanjeId->skrnaziv);
                $data->licenca = $this->h->iso88592_to_cirUTF(mb_strtoupper($licenca));
                $data->licencaTip = $licencaO->tipLicence->id;
                $gen = $licencaO->tipLicence->generacija;
                $licencaTipNaziv = $licencaO->tipLicence->naziv;
                $temp = '';
                $nazivArr = array_filter(array_keys(PROFESIONALNI_NAZIV[$gen]), function ($value) use ($licencaTipNaziv, $temp) {
                    return strpos($licencaTipNaziv, $value) !== false;
                });
                if ($nazivArr) {
                    $lenArr = array_map('strlen', $nazivArr);
                    $naziv = $nazivArr[array_search(max($lenArr), $lenArr)];
                } else {
                    $errormsg = "Nije pronadjen ispravan naziv tipa licence za broj licence: $licenca";
                    toastr()->error($errormsg);
                    return redirect("unesi/obradizahtevsvecanaforma/")
                        ->withErrors($errormsg)
                        ->withInput();
                }
//                dd($naziv);
//                $naziv = $nazivArr[0];
                $nazivPadez = PROFESIONALNI_NAZIV[$gen][$naziv];
                $data->vrstaLicenceNaslov = $this->h->iso88592_to_cirUTF(mb_strtoupper($nazivPadez));
                switch ($gen) {
                    case  1:
                        $data->nazivLicence = 'ималац лиценце ' . mb_strtolower($this->h->iso88592_to_cirUTF(str_replace($naziv, $nazivPadez, $licencaO->tipLicence->naziv)));
                        $data->nazivLicence = str_replace('урбанисту', 'урбанисте', $data->nazivLicence);
                        $data->nazivLicence = str_replace('архитекту', 'архитекте', $data->nazivLicence);
                        break;
                    case  2:
                        $data->vrstaLicence = 'ималац лиценце ' . $this->h->iso88592_to_cirUTF(mb_strtolower($nazivPadez));
                        $data->vrstaLicence = str_replace('урбанисту', 'урбанисте', $data->vrstaLicence);
                        $data->vrstaLicence = str_replace('архитекту', 'архитекте', $data->vrstaLicence);
                        break;
                    case  3:
                        $data->vrstaLicence = 'лиценцирани ' . $this->h->iso88592_to_cirUTF(mb_strtolower($naziv));
                        $data->vrstaPoslaGen = "за обављање стручних послова " . $this->h->iso88592_to_cirUTF($licencaO->tipLicence->vrstaPosla->naziv_gen);
                        break;
                    default:
                        break;
                }
                $data->strucnaOblast = $this->h->iso88592_to_cirUTF(mb_strtolower($licencaO->tipLicence->podOblast->regOblast->naziv));
                $data->uzaStrucnaOblastId = $licencaO->tipLicence->podOblast->id;
                $data->uzaStrucnaOblast = $this->h->iso88592_to_cirUTF(mb_strtolower($licencaO->tipLicence->podOblast->naziv));
                $data->brojResenja = $licencaO->broj_resenja;
                $data->datumResenja = date('d.m.Y.', strtotime($licencaO->datumuo));
                $data->datumStampe = date('d.m.Y.', strtotime($datumstampe));
                $data->filename = $osoba->ime . "_" . $osoba->roditelj . "_" . $osoba->prezime . "_" . $licenca;
                $data->filename = str_replace(" ", "_", $data->filename);

                $pdf = PDF::loadView('svecanaforma', (array)$data);
//                $pdf = PDF::loadView('svecanaforma-datum', (array)$data);
//                LIVE
                Storage::put("public/pdf/$data->filename.pdf", $pdf->output());
//                  PREVIEW
//                return $pdf->stream("$data->filename.pdf");

                $countOK++;

                $dataPrint['dataOK'][] = $data;
            } else {
                $countNOK++;
                $dataPrint['dataNOK'][] = $licenca;
//                echo "<br>$count. $licenca NOK";
            }
//                var_dump($dataPrint);
//            break;
        } //end foreach

        usort($dataPrint['dataOK'], function ($a, $b) {
            return $a->osobaImeRPrezime <=> $b->osobaImeRPrezime;
        });

        $ime = array_column($dataPrint['dataOK'], 'osobaImeRPrezime');
//dd($ime);
        if ($ime != array_unique($ime)) {
            $messageInfo .= '<br>There are duplicates in osobaImeRPrezime';
        }
        $lic = array_column($dataPrint['dataOK'], 'licenca');
        if ($lic != array_unique($lic)) {
            $messageInfo .= '<br>There are duplicates in licenca';
        }

        if ($ime != array_unique($ime) && $lic != array_unique($lic)) {
            $messageInfo .= '<br>There are duplicates in both osobaImeRPrezime and licenca';
        }

//        dd(array_column($dataPrint['dataOK'],'osobaImeRPrezime'));
//        dd($dataPrint['dataOK']);
//        dd($dataPrint);
//        $pdf = PDF::loadView('svecanaforma-report', $dataPrint);
//        return $pdf->stream("Report_$filename.pdf");

//        Storage::put("public/pdf/Report_$filename.pdf", $pdf->output());

//                $this->log($zahtev, $status_grupa, "$naziv zahtev: $zahtev->id, status: " . ZAHTEV_LICENCA_GENERISAN);
//            TODO prikazati status licence u toasteru
        /*            if ($this->kreirajlicencu($osoba)) {
                        $messageLicencaOK .= $licenca['broj'] . ', ';
                        $flagOK = true;
                        $countOK++;

                    } else {
                        $messageLicencaNOK .= $licenca['broj'] . ', ';
                        $flagNOK = true;
                        $countNOK++;
                    }*/

//            TODO TREBA DODATI SLUCAJ KAD NEMA OSOBE A NIJE PODNEO ZAHTEV ZA CLANSTVO TJ. UPIS NOVE OSOBE U REGISTAR ALI TREBALO BI DA JE IMA IZ SI

//        echo "<br>OK: $countOK";
//        echo "<br>NOT OK: $countNOK";
//        dd($dataPrint['dataNOK']);

        $path = 'public/pdf';
//        $temp = 'temp/';

        if ($countOK > 1) {
            $pdfFiles = Storage::files($path);
            //zip files
            $timestamp = date('Ymd');
            $unique = uniqid();
            $filename = $timestamp . "_SF_$unique.zip";
            $zip = new \ZipArchive();
            if ($zip->open($filename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                foreach ($pdfFiles as $file) {
//                    $file = Storage::get($name);
                    $name = basename($file);
//                    TODO: lokacija snimanja fajla
                    $zip->addFile(public_path('storage/pdf/' . $name), $name);
//                    $zip->addFile(public_path($temp . $name), $name);
                }
//            dd($zip);
                $zip->close();
                Storage::delete($pdfFiles);

                $dataZip['message'] = "Generisane svečane forme za unete brojeve licenci ($countOK)" . $messageInfo;
                $dataZip['status'] = true;
                $dataZip['filename'] = $filename;
                $dataZip['action'] = 'obradizahtevsvecanaforma';
//            return response()->download(public_path($filename), $filename)->deleteFileAfterSend(true);
//                return redirect('admin/unesi/obradizahtevsvecanaforma/' . $filename)->with('status', true)->with('message', "Generisane svečane forme za unete brojeve licenci ($countOK)" . $messageInfo)->withInput();
                return view(backpack_view('unesi'), $dataZip);
            }
        } else if ($countOK == 1) {
            //download one file
            //TODO: lokacija sa koje se fajl preuzima
            return response()->download(public_path("storage/pdf/$data->filename.pdf"))->deleteFileAfterSend(true);
//            return response()->download(public_path($temp . $data->filename . "pdf"))->deleteFileAfterSend(true);
        } else {
            //nema fajlova za download
            dd("kraj nema nista");

        }

        $messageLicencaOK .= "su uspešno sačuvane u bazi($countOK)";
        $messageLicencaNOK .= "nisu sačuvane u bazi($countNOK)";

        info($messageLicencaOK);
        $flagNOK ? toastr()->error($messageLicencaNOK) : toastr()->warning("Nema grešaka");
        $flagOK ? toastr()->success($messageLicencaOK) : toastr()->warning("Nema kreiranih licenci");
//        return redirect('/unesinovelicence')->with('message', $messageLicencaOK)->withInput();
        return view(backpack_view('obradizahtevsvecanaforma'), $this->data);

    }

    public function createZip($zipfile)
    {
        return $zipfile;
    }

    public function downloadZip(Request $request)
    {
//        dd($request->zipfile);
        ob_end_clean();
        //TODO: lokacija sa koje se fajl preuzima
        return response()->download(public_path($request->zipfile), $request->zipfile)->deleteFileAfterSend(true);
//        return response()->download(public_path('temp/' . $request->zipfile), $request->zipfile)->deleteFileAfterSend(true);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function unesinovelicence(Request $request)
    {
        $file = $request->file('upload');
        if (!is_null($file)) {
//            UNOS LICENCI IZ EXCEL DATOTEKE
            $import = new ExcelImport();
            try {
                $collection = ($import->toCollection($file));
//        dd($collection);
                $licence = $collection[0]->where('import', 1)->toArray();
//            dd($licence);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                dd($failures);
            }
        } else {
//            UNOS LICENCI IZ POLJA ZA UNOS
            $licence = $request->get('licence');
            $validated = $request->validate([
                'licence' => [
                    function ($attribute, $values, $fail) {
                        $error = false;
                        foreach ($values as $value) {
                            $zahtev = ZahtevLicenca::where(['licenca_broj' => $value['broj']])->first();
//                            dd($zahtev);
                            if (!is_null($zahtev)) {
                                if ($zahtev->osoba !== $value['jmbg']) {
                                    $error = true;
                                    $fail('JMBG: ' . $value['jmbg'] . ' se ne slaže sa brojem licence, proverite unos.');
                                }
                            }
                        }
                    }
                ]
            ]);
        }
        $messageLicencaOK = 'Licence: ';
        $messageLicencaNOK = 'Licence: ';
        $flagOK = false;
        $flagNOTOK = false;
        $countOK = 0;
        $countNOK = 0;
        $falseJMBG = [];
//        LICENCE SPREMNE ZA UNOS U BAZU
        foreach ($licence as $licenca) {
//                IMPORTUJU SE SAMO ZAHTEVI KAD VEC POSTOJI OSOBA SA LICENCAMA
//                ZA SADA SU SVE DODATE LICENCE AKTIVNE
//            UBUDUCE CE SE UBACIVATI I LICENCE AUTOMATSKI DOBIJENE NAKON POLOZENOG STRUCNOG ISPITA
//            $provera->getJmbgFromLicenca($licenca['broj']);

            $licenca['broj'] = strtoupper(trim($licenca['broj']));

            $licenca['datum_resenja'] = Carbon::parse($licenca['datum_resenja'])->format('Y-m-d');
            $licenca['datum_prijema'] = Carbon::parse($licenca['datum_prijema'])->format('Y-m-d');
            // VALIDACIJA DATUMA
            if (!$this->checkDate($licenca['datum_resenja'])) {
                $falseJMBG[$licenca['broj']] = 'Neispravan datum resenja za broj licence: ' . $licenca['broj'];
                $messageLicencaNOK .= ', Neispravan datum resenja za broj licence: ' . $licenca['broj'];
                $countNOK++;
                continue;
            }
            if (!$this->checkDate($licenca['datum_prijema'])) {
                $falseJMBG[$licenca['broj']] = 'Neispravan datum prijema za broj licence: ' . $licenca['broj'];
                $messageLicencaNOK .= ', Neispravan datum prijema za broj licence: ' . $licenca['broj'];
                $countNOK++;
                continue;
            }


//            PRONADJI JMBG NA OSNOVU BROJA ZAHTEVA ILI BROJA PRIJAVE
            if (!empty($licenca['broj_zahteva'])) {
                $licenca['jmbg'] = $this->getJMBG($licenca['broj_zahteva'], 'zahtev');
                if (is_null($licenca['jmbg'])) {
                    $falseJMBG[$licenca['broj_zahteva']] = 'Broj zahteva: ' . $licenca['broj_zahteva'] . ' ne postoji u bazi!';
                    $messageLicencaNOK .= ' Broj zahteva: ' . $licenca['broj_zahteva'] . ' ne postoji u bazi!';
                    $countNOK++;
                } else {
                    $broj = $licenca['broj_zahteva'];
                    $tip = 'broj_zahteva';
                }
            } else if (!empty($licenca['broj_prijave'])) {
                $licenca['jmbg'] = $this->getJMBG($licenca['broj_prijave'], 'siprijava');
                if (is_null($licenca['jmbg'])) {
                    $falseJMBG[$licenca['broj_prijave']] = 'Broj prijave: ' . $licenca['broj_prijave'] . ' ne postoji u bazi!';
                    $messageLicencaNOK .= ' Broj prijave: ' . $licenca['broj_prijave'] . ' ne postoji u bazi!';
                    $countNOK++;
                } else {
                    $broj = $licenca['broj_prijave'];
                    $tip = 'broj_prijave';
                }
            } else if (!empty($licenca['jmbg'])) {
                $broj = $licenca['broj'];
                $tip = 'broj_licence';
            }
//            dd($licenca['jmbg']);
            if (!is_null($licenca['jmbg'])) {
                if (!$this->checkOsoba(trim($licenca['jmbg']))) {
                    $falseJMBG[$licenca['jmbg']] = 'Osoba sa jmbg: ' . $licenca['jmbg'] . ' ne postoji u bazi!';
                    $messageLicencaNOK .= ' Osoba sa jmbg: ' . $licenca['jmbg'] . ' ne postoji u bazi!';
                    $countNOK++;
                    continue;
//                return Redirect::back()->withErrors(['Osoba ne postoji!']);
                }
            } else {
                continue;
            }

            $respZ = $this->getZahtevLicenca($broj, $tip);

            if ($respZ->status) {
                if ($respZ->zahtev->status <= ZAHTEV_LICENCA_PRIMLJEN) {
//                  AZURIRAJ ZAHTEV
                    $respZ = $this->azurirajZahtevLicenca($respZ->zahtev, $licenca);
                } else {
//                  ZAHTEV JE VEC OBRADJEN
                    $messageLicencaNOK .= " Zahtev za broj licence: " . $licenca['broj'] . ' je ' . strtoupper($respZ->zahtev->status) . ",";
                    $countNOK++;
                }
            } else {
//              KREIRAJ ZAHTEV
                $respZ = $this->kreirajZahtevLicenca($licenca);
            }
//            TODO prikazati status licence u toasteru i zasto explode!!!
            if ($respZ->status) {
//            dd($respZ);
                $this->log($respZ->zahtev, LICENCE, $respZ->message);
                $respL = $this->getLicenca($respZ->zahtev->licenca_broj);
                if ($respZ->status) {
                    if ($respL->status) {
                        $respL = $this->azurirajLicencu($respL->licenca, $respZ->zahtev);
                    } else {
                        $respL = $this->kreirajLicencu($respZ->zahtev);
                    }
                }
                if ($respL->status) {
                    $messageLicencaOK .= $respL->licenca->id . " " . $respL->licenca->status . " (" . explode(" ", trim($respL->message))[0] . "), ";
                    $flagOK = true;
                    $countOK++;
                } else {
                    $messageLicencaNOK .= $respL->licenca->id . " " . $respL->licenca->status . " (" . explode(" ", trim($respL->message))[0] . "), ";
                    $flagNOTOK = true;
                    $countNOK++;
                }
                $result[$licenca['broj']] = [
                    'status' => $respL->status,
                    'messageL' => $respL->licenca->id . " " . $respL->licenca->status . " ($respL->message), ",
                    'messageZ' => $respZ->message,
                ];
                $this->logOsoba($respL->licenca, LICENCE, $respL->message);
            } else {
                $countNOK++;
                $messageLicencaNOK .= ', Zahtev broj: ' . $respZ->zahtev->id . " status: " . $respZ->zahtev->status . " ($respZ->message), ";
            }

//            TODO TREBA DODATI SLUCAJ KAD NEMA OSOBE A NIJE PODNEO ZAHTEV ZA CLANSTVO TJ. UPIS NOVE OSOBE U REGISTAR ALI TREBALO BI DA JE IMA IZ SI
        } //end foreach
        $result['falseJMBG'] = array_keys($falseJMBG);
//        dd($result);
        $messageLicencaOK .= ". Uspešno sačuvano u bazi($countOK)";
        $messageLicencaNOK .= " . Nije sačuvano u bazi($countNOK)";

        info($messageLicencaOK);
//        $flagNOTOK ? toastr()->error($messageLicencaNOK) : toastr()->warning("Nema grešaka");
//        $flagOK ? toastr()->success($messageLicencaOK) : toastr()->warning("Nema kreiranih licenci");
        return redirect('/admin/unesinovelicence')->with('message', $messageLicencaOK)->with('messageNOK', $messageLicencaNOK)->withInput();
    }

    private function checkDate($date)
    {
        $dt = DateTime::createFromFormat("Y - m - d", $date);
        return $dt !== false && !array_sum($dt::getLastErrors());
    }

    private function checkOsoba($jmbg)
    {
        $osoba = Osoba::find($jmbg);
        if (!is_null($osoba)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $broj
     * @param $tip
     * @return null
     */
    private function getJMBG($broj, $tip)
    {
        $jmbg = NULL;
        switch ($tip) {
            case 'zahtev':
//            dd($broj);
                $o = ZahtevLicenca::find($broj);
                if (!is_null($o)) {
                    $jmbg = $o->osoba;
                }
                break;
            case 'siprijava':
                $o = SiPrijava::find($broj);
                if (!is_null($o)) {
                    $jmbg = $o->osoba_id;
                }
                break;
        }
        return $jmbg;
    }

    /**
     * @param $broj
     * @param string $tip
     * @return \stdClass
     */
    private function getZahtevLicenca($broj, $tip = 'broj_licence')
    {
        $response = new \stdClass();
        switch ($tip) {
            case 'broj_zahteva':
                $zahtev = ZahtevLicenca::where('id', $broj)->get();
                break;
            case 'broj_licence':
                $zahtev = ZahtevLicenca::where('licenca_broj', $broj)->get();
                break;
            case 'broj_prijave':
                $zahtev = ZahtevLicenca::where('si_prijava_id', $broj)->get();
                break;
        }
        if (!$zahtev->isEmpty()) {
            if (count($zahtev) > 1) {
//                IMA VIŠE ZAHTEVA ZA ISTI BROJ LICENCE
                $response->status = false;
                $response->message = "IMA VIŠE ZAHTEVA ZA ISTI BROJ: $broj";
            } else if (count($zahtev) == 1) {
//                IMA JEDAN ZAHTEV
                $response->status = true;
                $response->zahtev = $zahtev[0];
                $response->message = "Pronadjen zahtev: " . $response->zahtev->id;
            }
        } else {
//                NEMA ZAHTEVA
            $response->status = false;
            $response->broj = $broj;
            $response->zahtev = NULL;
        }
        return $response;
    }

    /**
     * @param ZahtevLicenca $zahtev
     * @param $licenca
     * @return \stdClass
     */
    private function azurirajZahtevLicenca(ZahtevLicenca $zahtev, $licenca)
    {
        $response = new \stdClass();
        $tipLicence = LicencaTip::find($licenca['tip']);
        if (is_null($tipLicence)) {
            $response->zahtev = $zahtev;
            $response->message = "neispravan tip licence: " . $licenca['tip'];
            $response->status = false;
            return $response;
        }
        $zahtev->osoba = $licenca['jmbg'];
        $zahtev->licenca_broj = $licenca['broj'];
        $zahtev->licenca_broj_resenja = $licenca['broj_resenja'];
        $zahtev->licenca_datum_resenja = Carbon::parse($licenca['datum_resenja'])->format('Y-m-d');
        $zahtev->licencatip = $tipLicence->id;
        $zahtev->vrsta_posla_id = $tipLicence->sekcija;
        $zahtev->reg_oblast_id = $tipLicence->podOblast->oblast_id;
        $zahtev->reg_pod_oblast_id = $tipLicence->pod_oblast_id;
        //todo treba zahtev status da bude zavrsen ako je kreirana licenca => azurira se prilikom azuriranja licence
        $zahtev->status = ZAHTEV_LICENCA_GENERISAN;
        $zahtev->prijem = Carbon::parse($licenca['datum_prijema'])->format('Y-m-d');
        $zahtev->datum = date("Y - m - d");
        if ($zahtev->isDirty()) {
            $zahtev->save();
            $response->message = "Ažuriran zahtev: $zahtev->id, status: " . ZAHTEV_LICENCA_GENERISAN;
            $response->status = true;
        } else {
            $response->message = "Nema promena";
            $response->status = true;
        }
        $response->zahtev = $zahtev;
        return $response;
    }

    /**
     * @param $licenca
     * @return \stdClass
     */
    private function kreirajZahtevLicenca($licenca)
    {
        $response = new \stdClass();

        $zahtev = new ZahtevLicenca();
        $response = $this->azurirajZahtevLicenca($zahtev, $licenca);
        $response->message = "Kreiran zahtev: $zahtev->id, status: " . ZAHTEV_LICENCA_GENERISAN;
        return $response;
    }

    /**
     * @param $broj_licence
     * @return \stdClass
     */
    private function getLicenca($broj_licence)
    {
        $response = new \stdClass();
        $licenca = Licenca::find($broj_licence);
        if (!is_null($licenca)) {
//                IMA JEDAN ZAHTEV
            $response->status = true;
            $response->message = "Pronadjena licenca";
            $response->licenca = $licenca;
        } else {
//                NEMA LICENCE
            $response->status = false;
            $response->message = "Nije Pronadjena licenca";
            $response->licenca = NULL;
//            dd($response);
        }
//        dd($response);
        return $response;
    }

    /**
     * @param Licenca $licenca
     * @param ZahtevLicenca $zahtev
     * @return \stdClass
     */
    private function azurirajLicencu(Licenca $licenca, ZahtevLicenca $zahtev)
    {
        $response = new \stdClass();
        $licenca->id = $zahtev->licenca_broj;
        $licenca->licencatip = $zahtev->licencatip;
        $licenca->osoba = $zahtev->osoba;
        $licenca->datum = $zahtev->datum;
        $licenca->zahtev = $zahtev->id;
        $licenca->datumuo = $zahtev->licenca_datum_resenja;
        $licenca->datumobjave = $zahtev->licenca_datum_resenja;

        $licenca->preuzeta = 1;
//            dd(Licenca::where('osoba', $zahtev->osoba)->where('prva', 1)->get()->isEmpty());
        if (Licenca::where('osoba', $zahtev->osoba)->where('prva', 1)->get()->isEmpty()) {
            $licenca->prva = 1;
            // default je 0 ako nije prva
        }
        $licenca->broj_resenja = $zahtev['licenca_broj_resenja'];
//dd($licenca->wasRecentlyCreated);
        $licenca->save();
        $licenca->status = $this->h->proveriStatusLicence($licenca);
        $licenca->save();
        $response->licenca = $licenca;
        $response->message = "Ažurirana licenca: $licenca->id($licenca->status, $licenca->osoba->id), status: $licenca->status, ažuriran status zahteva $zahtev->id: $zahtev->status";
        $response->status = true;
        return $response;
    }

    /**
     * @param ZahtevLicenca $zahtev
     * @return \stdClass
     */
    private function kreirajLicencu(ZahtevLicenca $zahtev)
    {
//dd($zahtev);
        $licenca = new Licenca();
        $response = $this->azurirajLicencu($licenca, $zahtev);

        $zahtev->status = ZAHTEV_LICENCA_ZAVRSEN;

        $zahtev->save();
        $this->log($zahtev, LICENCE, "Ažuriran datum prijema zahteva: $zahtev->prijem, status: " . ZAHTEV_LICENCA_ZAVRSEN);

        $response->message = "Kreirana licenca: $licenca->id($licenca->status, $licenca->osoba->id), status: $licenca->status, ažuriran status zahteva $zahtev->id: $zahtev->status";
        return $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function unesinoveclanove(Request $request)
    {
        $messageOK = "";
        $messageNOK = "";
        $file = $request->file('upload');
        if (!is_null($file)) {
            $import = new ExcelImport();
            $collection = ($import->toCollection($file));
            $prijave = $collection[2]->where('import', 1);
//            dd($prijave);
        } else {
            $prijave = $request->get('prijave');
            $validated = $request->validate([
                'prijave' => [
                    function ($attribute, $values, $fail) {
                        $error = false;
                        foreach ($values as $value) {
                            $zahtev = PrijavaClanstvo::find($value['broj']);
                            if (is_null($zahtev)) {
                                $error = true;
                                $fail('Prijava: ' . $value['broj'] . ' ne postoji u bazi, proverite unos.');
                            }
                        }
                    }
                ],
                'prijave.*.datum-resenja' => 'date_format:d.m.Y.',
                'prijave.*.datum-prijema' => 'date_format:d.m.Y.',
                'prijave.*.broj-resenja' => 'unique:prijave_clanstvo,broj_odluke_uo',
                'prijave.*.zavodni-broj' => 'unique:prijave_clanstvo,zavodni_broj',
            ]);
        }
        foreach ($prijave as $prijava) {
            $resp = $this->odobrinovogclana($prijava);
            if ($resp->status) {
                $messageOK .= $prijava['broj'] . " ($resp->message), ";
            } else {
                $messageNOK .= $prijava['broj'] . " ($resp->message), ";
            }
        }
        if (!empty($messageOK)) {
            $messageOK = trim($messageOK, ", ");
            $messageOK = 'Uspešno uneti novi članovi: ' . $messageOK;
        }
        if (!empty($messageNOK)) {
            $messageNOK = trim($messageNOK, ", ");
            $messageNOK = 'Neuspešno: ' . $messageNOK;
        }
//        toastr()->success($messageOK);
//        toastr()->warning($messageNOTOK);
        session()->flashInput($request->input());
        return view('clanstvo')->with("message", $messageOK)->with('errormessage', $messageNOK);


    }


    public function odobrinovogclana($prijava)
    {
        $resp = new \stdClass();
        $prijava_clan = PrijavaClanstvo::find($prijava['broj']);

        if ($prijava_clan->status_id === PRIJAVA_CLAN_KREIRANA) {
            $resp->message = "Status prijave: " . $prijava['broj'] . " " . $prijava_clan->status->naziv . ", kontaktirajte SIT";
            $resp->status = false;
            return $resp;
        }
        if ($prijava_clan->status_id === PRIJAVA_CLAN_PRIHVACENA) {
            $resp->message = "Prijava je već obrađena";
            $resp->status = false;
            return $resp;
        }
        // TODO napraviti elektronsko zavodjenje

        $zahtevi = $prijava_clan->zahtevi;
        $osoba = $prijava_clan->osoba;

        $osobaLicence = $osoba->licence;
        if (!$osobaLicence->isEmpty()) {
            $lib = new LibLibrary();
            $lib->dodeliJedinstveniLib($osoba->id, Auth::user()->id);
            $this->logOsoba($osoba, CLANSTVO, "Ažurirana osoba: $osoba->ime $osoba->prezime($osoba->id), lib: $osoba->lib, status: $osoba->clan");

            $prijava_clan->datum_prijema = Carbon::parse($prijava['datum - prijema'])->format('Y-m-d');
            $prijava_clan->zavodni_broj = $prijava['zavodni - broj'];
            $prijava_clan->datum_odluke_uo = Carbon::parse($prijava['datum - resenja'])->format('Y-m-d');
            $prijava_clan->broj_odluke_uo = $prijava['broj - resenja'];
            $prijava_clan->status_id = PRIJAVA_CLAN_PRIHVACENA;
            $prijava_clan->save();
            $this->log($prijava_clan, CLANSTVO, "Kreirana prijava za clanstvo osoba: $osoba->ime $osoba->prezime($osoba->id), status: " . PRIJAVA_CLAN_PRIHVACENA);
            $clanarina = DB::table('tclanarinaod2006')->updateOrInsert(
                ['osoba' => $osoba->id, 'rokzanaplatu' => Carbon::parse($prijava['datum - resenja'])->format('Y-m-d')],
                [
                    'iznoszanaplatu' => 7500,
                    'created_at' => now()
                ]
            );
            $osoba->clan = 1;
            $osoba->save();
            $resp->message = "Članstvo prema prijavi broj $prijava_clan->id za osobu $osoba->ime_prezime_jmbg je odobreno";
            $resp->status = true;
        } else {
            $resp->message = "Osoba koja želi da postane član Komore nema ni jednu licencu upisanu u Registar . Unesite licence prema Rešenjima u bazu . ";
            $resp->status = false;
        }
        return $resp;
    }

    private function getOsobaLicence($osoba)
    {

    }

    /*
     * funkcija koja se poziva iz bladea AJAX
     */
    public function checkLicencaTip(Request $request)
    {
        $licTip4 = strtoupper(substr(trim($request->input('licence.*.broj')[0]), 0, 4));
        $licencaTip = LicencaTip::where("id", $licTip4)->pluck('naziv', 'id')->toArray();
        if ($licencaTip) {
            return json_encode(true);
        } else {
            $licTip3 = strtoupper(substr($request->input('licence.*.broj')[0], 0, 3));
            $licencaTip = LicencaTip::where("id", $licTip3)->pluck('naziv', 'id')->toArray();
            if ($licencaTip) {
                return json_encode(true);
            } else {
                return json_encode(false);
            }
        }
    }

    /*
     * funkcija koja se poziva iz bladea AJAX
     */
    public function getLicencaTip($id)
    {
        $id = substr($id, 0, 3);
        $licencaTip = LicencaTip::where("id", 'LIKE', $id . '%')->get()->pluck('tip_naziv', 'id')->toArray();
        return json_encode($licencaTip);
    }


    /*
     * funkcija koja se poziva iz bladea AJAX
     */
    public function checkZahtev($licenca, $jmbg)
    {
//        dd($licenca . $jmbg);

        return json_encode(true);
    }

//    TODO dodati ovo u helper ili LOG klasu
    private function log($object, $statusGrupa, $naziv, $napomena = '')
    {
//        dd($object->id);
        $log = Log::firstOrNew(['naziv' => $naziv, 'loggable_id' => $object->id]);
        $log->naziv = $naziv;
        $log->napomena = $napomena;
        $log->log_status_grupa_id = $statusGrupa;
        $log->loggable()->associate($object);
        $log->save();
    }

    private function logOsoba($object, $statusGrupa, $naziv, $napomena = '')
    {
        $log = LogOsoba::firstOrNew(['naziv' => $naziv, 'loggable_id' => $object->id]);
        $log->naziv = $naziv;
        $log->napomena = $napomena;
        $log->log_status_grupa_id = $statusGrupa;
        $log->loggable()->associate($object);
        $log->save();
    }

}
