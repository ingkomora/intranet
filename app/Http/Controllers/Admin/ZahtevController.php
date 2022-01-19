<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ExcelImport;
use App\Libraries\Helper;
use App\Libraries\LibLibrary;
use App\Libraries\ProveraLibrary;
use App\Models\Clanarina;
use App\Models\ClanarinaOld;
use App\Models\Document;
use App\Models\LicencaTip;
use App\Models\Log;
use App\Models\LogOsoba;
use App\Models\Membership;
use App\Models\Osoba;
use App\Models\PrijavaClanstvo;
use App\Models\PrijavaSiStara;
use App\Models\Registry;
use App\Models\RegistryDepartmentUnit;
use App\Models\SiPrijava;
use App\Models\ZahtevLicenca;
use Barryvdh\DomPDF\Facade as PDF;
use DateTime;
use Doctrine\DBAL\Schema\AbstractAsset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use App\Models\Licenca;
use Prologue\Alerts\Facades\Alert;
use Session;
use function PHPUnit\Framework\isEmpty;


class ZahtevController extends Controller
{
    protected $data = []; // the information we send to the view
    protected $h;
    protected $counter;
    protected $ok;
    protected $error;

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

        $file = $request->file('upload');

        $validated = $request->validate([
            'file' => 'required_without_all:licence,jmbgs',
            'licence' => 'required_without_all:file,jmbgs',
            'jmbgs' => 'required_without_all:file,licence',
            'datum' => 'required_without:file',
        ]);
        $request->datum = Carbon::parse($request->datum)->format('Y-m-d');
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
        $flagOK = FALSE;
        $flagNOK = FALSE;
        $count = 0;
        $countOK = 0;
        $countNOK = 0;
        $dataPrint = ['dataOK' => [], 'dataNOK' => []];
        foreach ($licence as $licenca => $datumstampe) {
            $count++;
            $licenca = $this->h->cirUTF_to_iso88592(mb_strtoupper(str_replace(' ', '', $licenca)));
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
//                $licencaTip = $this->h->getLicencaTipFromLicencaBroj($licencaO);
                $licencaTip = $licencaO->tipLicence;
                $data->osobaImeRPrezime = $this->h->iso88592_to_cirUTF($osoba->ime . " " . $osoba->roditelj . ". " . $osoba->prezime);
                $data->zvanje = $this->h->iso88592_to_cirUTF($osoba->zvanjeId->naziv);
                $data->zvanjeskr = $this->h->iso88592_to_cirUTF($osoba->zvanjeId->skrnaziv);
                $data->licenca = $this->h->iso88592_to_cirUTF(mb_strtoupper($licenca));
//                $data->licencaTip = $licencaO->tipLicence->id;
                $data->licencaTip = $licencaTip->id;
//                $gen = $licencaO->tipLicence->generacija;
                $gen = $licencaTip->generacija;
//                $licencaTipNaziv = $licencaO->tipLicence->naziv;
                $licencaTipNaziv = $licencaTip->naziv;
                $temp = '';
                $nazivArr = array_filter(array_keys(PROFESIONALNI_NAZIV[$gen]), function ($value) use ($licencaTipNaziv, $temp) {
                    return strpos($licencaTipNaziv, $value) !== FALSE;
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
                $data->gen = $gen;
                switch ($gen) {
                    case  1:
//                        $data->nazivLicence = 'ималац лиценце ' . mb_strtolower($this->h->iso88592_to_cirUTF(str_replace($naziv, $nazivPadez, $licencaO->tipLicence->naziv)));
                        $data->nazivLicence = 'ималац лиценце ' . mb_strtolower($this->h->iso88592_to_cirUTF(str_replace($naziv, $nazivPadez, $licencaTip->naziv)));
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
                $dataZip['status'] = TRUE;
                $dataZip['filename'] = $filename;
                $dataZip['action'] = 'obradizahtevsvecanaforma';
//            return response()->download(public_path($filename), $filename)->deleteFileAfterSend(true);
//                return redirect('admin/unesi/obradizahtevsvecanaforma/' . $filename)->with('status', true)->with('message', "Generisane svečane forme za unete brojeve licenci ($countOK)" . $messageInfo)->withInput();
                return view(backpack_view('unesi'), $dataZip);
            }
        } else if ($countOK == 1) {
            //download one file
            //TODO: lokacija sa koje se fajl preuzima
            return response()->download(public_path("storage/pdf/$data->filename.pdf"))->deleteFileAfterSend(TRUE);
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

//        Alert::error($messageLicencaNOK);
//        Alert::success($messageLicencaOK);
//        return view(backpack_view('obradizahtevsvecanaforma'), $this->data)->with('alert', Alert::all());
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
        return response()->download(public_path($request->zipfile), $request->zipfile)->deleteFileAfterSend(TRUE);
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
                        $error = FALSE;
                        foreach ($values as $value) {
                            $zahtev = ZahtevLicenca::where(['licenca_broj' => $value['broj']])->first();
//                            dd($zahtev);
                            if (!is_null($zahtev)) {
                                if ($zahtev->osoba !== $value['jmbg']) {
                                    $error = TRUE;
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
        $flagOK = FALSE;
        $flagNOTOK = FALSE;
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
                    $flagOK = TRUE;
                    $countOK++;
                } else {
                    $messageLicencaNOK .= $respL->licenca->id . " " . $respL->licenca->status . " (" . explode(" ", trim($respL->message))[0] . "), ";
                    $flagNOTOK = TRUE;
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
        $dt = DateTime::createFromFormat("Y-m-d", $date);
        return $dt !== FALSE && !array_sum($dt::getLastErrors());
    }

    private function checkOsoba($jmbg)
    {
        $osoba = Osoba::find($jmbg);
        if (!is_null($osoba)) {
            return TRUE;
        } else {
            return FALSE;
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
                $response->status = FALSE;
                $response->message = "IMA VIŠE ZAHTEVA ZA ISTI BROJ: $broj";
            } else if (count($zahtev) == 1) {
//                IMA JEDAN ZAHTEV
                $response->status = TRUE;
                $response->zahtev = $zahtev[0];
                $response->message = "Pronadjen zahtev: " . $response->zahtev->id;
            }
        } else {
//                NEMA ZAHTEVA
            $response->status = FALSE;
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
            $response->status = FALSE;
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
        $zahtev->datum = date("Y-m-d");
        if ($zahtev->isDirty()) {
            $zahtev->save();
            $response->message = "Ažuriran zahtev: $zahtev->id, status: " . ZAHTEV_LICENCA_GENERISAN;
            $response->status = TRUE;
        } else {
            $response->message = "Nema promena";
            $response->status = TRUE;
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
            $response->status = TRUE;
            $response->message = "Pronadjena licenca";
            $response->licenca = $licenca;
        } else {
//                NEMA LICENCE
            $response->status = FALSE;
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
        $response->status = TRUE;
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
                        $error = FALSE;
                        foreach ($values as $value) {
                            $zahtev = PrijavaClanstvo::find($value['broj']);
                            if (is_null($zahtev)) {
                                $error = TRUE;
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
            $resp->status = FALSE;
            return $resp;
        }
        if ($prijava_clan->status_id === PRIJAVA_CLAN_PRIHVACENA) {
            $resp->message = "Prijava je već obrađena";
            $resp->status = FALSE;
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

            $prijava_clan->datum_prijema = Carbon::parse($prijava['datum-prijema'])->format('Y-m-d');
            $prijava_clan->zavodni_broj = $prijava['zavodni-broj'];
            $prijava_clan->datum_odluke_uo = Carbon::parse($prijava['datum-resenja'])->format('Y-m-d');
            $prijava_clan->broj_odluke_uo = $prijava['broj-resenja'];
            $prijava_clan->status_id = PRIJAVA_CLAN_PRIHVACENA;
            $prijava_clan->save();
            $this->log($prijava_clan, CLANSTVO, "Kreirana prijava za clanstvo osoba: $osoba->ime $osoba->prezime($osoba->id), status: " . PRIJAVA_CLAN_PRIHVACENA);
            $clanarina = DB::table('tclanarinaod2006')->updateOrInsert(
                ['osoba' => $osoba->id, 'rokzanaplatu' => Carbon::parse($prijava['datum-resenja'])->format('Y-m-d')],
                [
                    'iznoszanaplatu' => 7500,
                    'created_at' => now()
                ]
            );
            $osoba->clan = 1;
            $osoba->save();
            $resp->message = "Članstvo prema prijavi broj $prijava_clan->id za osobu $osoba->ime_prezime_jmbg je odobreno";
            $resp->status = TRUE;
        } else {
            $resp->message = "Osoba koja želi da postane član Komore nema ni jednu licencu upisanu u Registar . Unesite licence prema Rešenjima u bazu . ";
            $resp->status = FALSE;
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
            return json_encode(TRUE);
        } else {
            $licTip3 = strtoupper(substr($request->input('licence.*.broj')[0], 0, 3));
            $licencaTip = LicencaTip::where("id", $licTip3)->pluck('naziv', 'id')->toArray();
            if ($licencaTip) {
                return json_encode(TRUE);
            } else {
                return json_encode(FALSE);
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

        return json_encode(TRUE);
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

    public function splitAddress()
    {
        $saved = 0;
        $jmbgs = ['0601986805064',
            '2803983730070',
            '0204992722228',
            '0709988737525',
            '0106989850000',
            '0210963715247',
            '2608988915136',
            '0411993715204',
            '1002944715009',
            '2112978743315',
            '1108987895011',
            '0309988381501',
            '1102954710093',
            '2611991710317',
            '1402995710212',
            '0606972710003',
            '0409986790018',
            '0904960783928',
            '2510984799417',
            '0801990788963',
            '0711981740045',
            '1807986710269',
            '0609994723213',
            '2511964730034',
            '2811988805039',
            '0804976786019',
            '2107986710082',
            '0806986895021',
            '3110977710155',
            '2202992775016',
            '2706991815640',
            '0603992735038',
            '2002988783447',
            '2510991725070'];
        $osobe = Osoba::whereIn('id', $jmbgs)
//        $requests = \App\Models\Request::where('request_category_id', 3)
//            ->where('note', 'SFL_20211130')
//            ->where('status_id', KREIRAN)
            ->get();
//        foreach ($requests as $request) {
        foreach ($osobe as $osoba) {
//            $osoba = $request->osoba;
            $original = $osoba->prebivalisteadresa;
            if (preg_match('/^((?:\d+)?\.?[a-zA-ZčČćĆžŽšŠđĐ.\-\s]+)(?:\s|,|,\s|\s,|br.|,\sbr.\s)(\d+)\/?([a-zA-Z]+)?\/?(\d+)?(?:(?:\s|,)?br\.\s?(\d+)|(?:\s|,)?broj\s+(\d+))?(?:.\sulaz\s?\d+,?)?(?:(?:\s|,|,\s|\s,)?st\.\s?(\d+)|(?:\s|,|,\s|\s,)?stan\s+(\d+))?.*$/', $original, $m)) {
                $full[] = $m;

                $osoba->ulica = (!empty($m[1])) ? $m[1] : NULL;
                $osoba->broj = (!empty($m[2])) ? $m[2] : NULL;
                $osoba->podbroj = (!empty($m[3])) ? $m[3] : NULL;
                $osoba->stan = (!empty($m[4])) ? $m[4] : ((!empty($m[8])) ? $m[8] : NULL);
            } else {
                $osoba->ulica = $original;
                $osoba->broj = NULL;
                $osoba->podbroj = NULL;
                $osoba->stan = NULL;
//                echo "<br>!!!!!!$original";
            }
            if (!empty($osoba->lib)) {
                $osoba->save();
            }
            if ($osoba->wasChanged()) {
                $saved++;
            }
//                echo "<br>$osoba->ulica $osoba->broj$osoba->podbroj $osoba->stan";
//                echo "<br>$original";
        }
//        dd($full);
        echo "<br>saved: $saved";
    }

    public function joinAddress()
    {
        $saved = 0;
//        $requests = \App\Models\Request::where('request_category_id', 2)->where('status_id', OBRADJEN)->get();
        $requests = \App\Models\Request::where('request_category_id', 2)->where('note', 'Platio 2017')->where('status_id', '<>', PROBLEM)->get();
//        dd($requests);
        foreach ($requests as $request) {
            $osoba = $request->osoba;

            if (!empty($request->osoba->ulica)) {
                $new = $request->osoba->ulica;
                $new .= (!is_null($request->osoba->broj)) ? " " . $request->osoba->broj : "";
                $new .= (!is_null($request->osoba->podbroj)) ? $request->osoba->podbroj : "";
                $new .= (!is_null($request->osoba->stan)) ? "/" . $request->osoba->stan : "";
                $new .= (!is_null($request->osoba->sprat)) ? ", " . $request->osoba->sprat . ". sprat" : "";
                $osoba->prebivalisteadresa = $new;
//                echo "<br>$osoba->prebivalisteadresa";

                $osoba->save();
            }
            if ($osoba->wasChanged()) {
                $saved++;
            }
//                echo "<br>$original";
        }
//        dd($full);
        echo "<br>saved: $saved";
//        echo "<br>" . count($saved);
//        var_dump($full);

//        dd($request->osoba->prebivalisteadresa);
    }

    public function registries()
    {
//        $reg = RegistryDepartmentUnit::with('childrenRegistryDepartmentUnits')->first();
//        $reg = RegistryDepartmentUnit::with('allChildrenRegistryDepartmentUnits')->find(1);
        $reg = RegistryDepartmentUnit::whereNull('parent_id')
            ->with('allChildrenRegistryDepartmentUnits')
            ->get();
//        $children = $reg->childrenRegistryDepartmentUnits->pluck('label','id')->toArray();
//        $children = $reg->allChildrenRegistryDepartmentUnits->pluck('label','id')->toArray();
        dd($reg);
//        dd($children);
    }

    public function clanstvo($function = '', $one = '', $two = '')
    {
        DB::disableQueryLog();
//        za one koji nisu clanovi
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '600');

        $this->counter = 0;

//        CLANARINA
//        $this->clanarina();
//        $this->prijavaSi();
//        $this->prijaveClanstvo();//1
//        $this->osobeClanarinaNotMembership();//2
        $this->osobaNotClanNotRequest();//3
//        $this->deleteModel();
//        $this->compareModelProperties('documents', 'prijave_clanstvo');
//        $this->compareModelProperties('tzahtev', 'tlicenca');

        /*
         * zahtevi za prijem u clanstvo => importovani u request: 28591 - 29906 (1314 zahteva)
         * documents vezani za zahteve za prijem u clanstvo: 1 - 2528 (1214 x 2, jer nije kreirano 100 dokumenata za zahteve koji imaju status 50)
         * membership za zahteve za prijem u clanstvo: 1-1125 (1125 ima zahteva sa statusom 53)
         *
         * Kreiran za osobe iz clanarinaod2006 kojih nema u tabeli memberships. M:19295 R:28590 D:20697
         *
         * Requestovi za brisanje iz clanstva 1-10323
         * Requestovi za SFL 10324 - 10420
         *
         * Za brisanje M:19296 R:29907 D:20698 (19296 ne postoji?) OK
         *
         * Importovano novo zahtevi za prijem u clanstvo => nisu importovani u request: 29907 - 29980  M19377,R29980,D20997 OK
         *
         *
         */

    }

    private function prijaveClanstvo()
    {
        $prijavaSiImport = PrijavaClanstvo::
//        where('status_id', PRIJAVA_CLAN_PRIHVACENA)->
        chunkById(1000, function ($zahtevi) {
            foreach ($zahtevi as $zahtev) {
//                dd($zahtev);
                $result = $this->copyModelPrijavaClanstvo($zahtev);

                $this->counter++;
                if ($result) {
                    $this->ok++;
                } else {
                    $this->error++;
                }
            }
            echo "<br>Kreirano: $this->ok od $this->counter";
            echo "<br>Nije kreirano: $this->error od $this->counter";
        })
//        limit(2)
//            ->pluck('osoba')->toArray()
            ->get();
    }

    private function osobaNotClanNotRequest()
    {
//        $osobeNotClan=Osoba::where('clan',0)->get()->count();
//        echo "<br>Osobe koje nisu clanovi: $osobeNotClan"; //9419
//        $osobeNotClanNotRequest=Osoba::where('clan',0)->whereDoesntHave('memberships')->get()->count();
//        echo "<br>Osobe koje nisu clanovi a da nisu u request: $osobeNotClanNotRequest"; //7926
//        $members=Membership::get()->count();
//        echo "<br>Osobe u memberships: $members";
//dd();
        $query = Osoba::where('clan', 0)
            ->whereHas('clanarine')
            ->whereHas('memberships')
            ->whereDoesntHave('requests', function ($q) {
                $q->where('request_category_id', 2);
            })
            ->whereHas('licence', function ($q) {
                $q
//                    ->where('status', 'D')
//                    ->where('status','A')
//                ->where('status','<>','A')
//                ->where('status','<>','N')
//                        ->whereNull('razlogukidanja')//reseno
//                        ->orWhereNull('datumukidanja')//reseno
                    ->where('status', '<>', 'H');
            })
            ->orderBy('id')
            ->chunkById(1000, function ($osobe) {
                foreach ($osobe as $osoba) {
//                    dd($osoba);
                    $licence = $osoba->licence;
//                    $req = $osoba->requests->where('request_category_id',2)->first();
                    if ($licence->isNotEmpty() and $licence->count() > 0) {
                        $this->counter++;
//                        dd($licence->toArray());
                        $cond = TRUE;
                        $licstr = '';
                        foreach ($licence as $licenca) {
                            if ($licenca->status == 'D') {

                                $cond &= TRUE;
                            } else {
//                                break;
                                $cond &= FALSE;
                            }

                            $licstr .= " $licenca->id ($licenca->status) | ";
                        }
//                        echo "$this->counter | {$req->id} | {$req->status->naziv} | $cond | $osoba->id | $licstr<br>";
                        echo "$this->counter | $cond | $osoba->id | $licstr<br>";
                        if (!$cond) {
                            continue;
                        } else {
                            $this->ok++;
                            $data = [
                                "osoba_id" => $osoba->id,
                                "datum_prijema" => $licence[0]['datumukidanja'],
                                "app_korisnik_id" => 14,
                                "zavodni_broj" => "",
                                "barcode" => NULL,
                                "created_at" => date('Y-m-d H:i:s', strtotime($licence[0]['datumukidanja'])),
                                "updated_at" => Carbon::now()->format("Y-m-d H:i:s"),
                                "broj_odluke_uo" => "",
                                "datum_odluke_uo" => $licence[0]['datumukidanja'],
                                "status_id" => 13,
                                "napomena" =>str_contains($osoba->napomena,'Broj rešenja o prestanku članstva')? $osoba->napomena . "##" . $licence[0]['razlogukidanja'] : $licence[0]['razlogukidanja'],
                            ];
//                            $result = $this->updateMCreateRD($data);

                            /*if ($result) {
                                $this->ok++;
                            } else {
                                $this->error++;
                                break;
                            }*/
                        }

                    }
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
//                    if ($this->counter == 1000) dd('kraj');
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
//        dd($query->count());
        dd($query);
    }

    private function osobeClanarinaNotMembership()
    {
//        $clanarine=Clanarina::distinct('osoba')->get()->count();
//        echo "Distinct osobe u clanarini: $clanarine";
//        $osobeBezMembership=Osoba::whereHas('clanarine')->whereDoesntHave('memberships')->get()->count();
//        echo "<br>Osobe sa clanarinom a da nisu u memberships: $osobeBezMembership";
//        $members=Membership::get()->count();
//        echo "<br>Osobe u memberships: $members";
//dd();
        $query = Osoba::whereHas('clanarine')
            ->whereDoesntHave('memberships')
            ->orderBy('id')
            ->chunkById(1000, function ($osobe) {
                foreach ($osobe as $osoba) {
                    $clanarina = $osoba->poslednjaClanarina()->get()->toArray()[0];
                    $data = [
                        "osoba_id" => $osoba->id,
                        "datum_prijema" => $clanarina['rokzanaplatu'],
                        "app_korisnik_id" => 14,
                        "zavodni_broj" => "",
                        "barcode" => NULL,
                        "created_at" => date('Y-m-d H:i:s', strtotime($clanarina['rokzanaplatu'])),
                        "updated_at" => Carbon::now()->format("Y-m-d H:i:s"),
                        "broj_odluke_uo" => "",
                        "datum_odluke_uo" => $clanarina['rokzanaplatu'],
                        "status_id" => 13,
                        "napomena" => NULL,
                    ];
                    $result = $this->copyArray($data);

                    $this->counter++;
                    if ($result) {
                        $this->ok++;
                    } else {
                        $this->error++;
                        break;
                    }
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
            ->get();
        dd($query - count());
    }

    private function compareModelProperties($tableNameOne, $tableNameTwo)
    {

//        UPOREDJIVANJE KOLONA
        $columnsOne = Schema::getColumnListing($tableNameOne); // users table
        $columnsTwo = Schema::getColumnListing($tableNameTwo); // users table
        $one_two = array_diff($columnsOne, $columnsTwo);
        $two_one = array_diff($columnsTwo, $columnsOne);
        $columns = [];
        $remember = [];
        $foreach = count($columnsOne) >= count($columnsTwo) ? $columnsOne : $columnsTwo;
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>$tableNameOne</th><th>$tableNameTwo</th></tr>";
        for ($i = 0; $i < count($foreach); $i++) {
            $like = [];
            $color = "#" . $this->random_color();

            echo "<tr>";
            if (!in_array($columnsOne[$i], $one_two)) {
                echo "<td bgcolor='#adff2f'>$columnsOne[$i]</td>";
            } else {
                if (array_key_exists($i, $columnsTwo)) {
                    $val = $this->checkIfStrInArr($columnsOne[$i], $columnsTwo);
                    $like[$i]['keys'] = $val;
                    $like[$i]['color'] = $color;
                    if (is_array($val)) {
                        foreach ($val as $item) {
                            $remember[$item] = $color;
                        }
                    }
                    if ($like[$i]['keys'] !== FALSE) {
                        echo "<td bgcolor='$color'>$columnsOne[$i]</td>";
                    } else {
                        echo "<td>$columnsOne[$i]</td>";
                    }
                } else {
                    $like[$i]['keys'] = FALSE;
                    $like[$i]['color'] = '#FFFFFF';
                    echo "<td>$columnsOne[$i]</td>";
                }
            }
            if (array_key_exists($i, $columnsTwo)) {
                if (!in_array($columnsTwo[$i], $two_one)) {
                    echo "<td bgcolor='#adff2f'>$columnsTwo[$i]</td>";
                } else {
                    if (array_key_exists($i, $remember)) {
                        echo "<td bgcolor='" . $remember[$i] . "'>$columnsTwo[$i]</td>";
                    } else {
                        echo "<td>$columnsTwo[$i]</td>";
                    }
                }
            } else {
                echo "<td>/</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        echo "<br>Kolone koje postoje i u tabeli $tableNameOne i u tabeli $tableNameTwo su označene <span style='padding: 5px; background-color: #adff2f'>zelenom</span> bojom";

    }

    private function replicateModel($modelNameFrom, $modelNameTo, $id)
    {
        $nameSpace = 'App\Models\\';
//        $modelFrom = OsobaSi::findOrFail($id);
        $modelFrom = app($nameSpace . ucfirst($modelNameFrom))->findOrFail($id);
//        $modelFrom = ucfirst($modelNameFrom)::findOrFail($id);
        // replicate (duplicate) the data
        $$modelNameTo = $modelFrom->replicate();

        // make into array for mass assign.
        //make sure you activate $guarded in your Staff model
        $$modelNameTo = $$modelNameTo->toArray();
        $$modelNameTo['id'] = $id;
//        unset($$modelNameTo['datumrodjenja']);
        try {
            $modelTo = app($nameSpace . ucfirst($modelNameTo))::firstOrFail($$modelNameTo);
//            $modelTo = app($nameSpace . ucfirst($modelNameTo))::firstOrCreate($$modelNameTo);
            if ($modelTo->wasRecentlyCreated) {
                $message = 'Replicated!';
            } else {
                $message = 'Model already exists!';
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        return $message;
    }

    private function updateMCreateRD(array $data)
    {
        //1. create requests
        //2. create documents
        //3. update membership

        DB::beginTransaction();

        $created = FALSE;

        $membershipOK = FALSE;
        $requestOK = FALSE;
        $documentOK = FALSE;

        $membershipData = []; //reset array

        $requestsMapPrijaveClanstvo = [
//  REQUEST
            'osoba_id' => 'osoba_id',
            'request_category_id' => 1,
            'status_id' => [50 => 10, 51 => 11, 52 => 12, 53 => 13, 54 => 14],
            'note' => 'napomena',
//            'requestable_id' => '',   //membership.id
//            'requestable_type' => '', //\App\Models\Membership
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
        $membershipsMapPrijaveClanstvo = [
//  MEMBERSHIP
            'osoba_id' => 'osoba_id',
            'started_at' => 'datum_odluke_uo',
            'ended_at' => 'datum_odluke_uo',
            'updated_at' => 'updated_at',
            'note' => 'napomena',
            'status_id' => MEMBERSHIP_ENDED,
        ];

        $oldData = $data;
        echo "<BR>";
        echo "<BR>OLD DATA";
        echo "<PRE>";
        print_r($oldData);
        echo "</PRE>";

        try {

            $membership = Membership::where('osoba_id', $data['osoba_id'])->where('status_id', MEMBERSHIP_STARTED)->latest()->first();
//        REQUESTS
            foreach ($requestsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'request_category_id') {
                    $requestData[$newKey] = $oldKey;
                } else if ($newKey == 'status_id') {
                    $requestData[$newKey] = array_search($oldData['status_id'], $oldKey);
                } else if ($newKey == 'note') {
                    $requestData[$newKey] = isEmpty($oldData[$oldKey]) ? "Kreiran za osobe iz clanarinaod2006 kojih nema u tabeli memberships." : "##Kreiran za osobe iz clanarinaod2006 kojih nema u tabeli memberships.";
                } else {
                    $requestData[$newKey] = $oldData[$oldKey];
                }
            }
            echo "<BR>";
            echo "<BR>REQUEST";
            echo "<PRE>";
            print_r($requestData);
            echo "</PRE>";
            $request = \App\Models\Request::firstOrNew($requestData);
            /*            if ($request->save()) {
                            $request->requestable()->associate($membership);
                            $request->save();
                            $requestOK = TRUE;
                            echo "<BR>Model Request $request->id " . (($request->wasRecentlyCreated) ? " created" : " updated");
                        }*/

//      DOCUMENTS KAD JE ZAHTEV
            $documentMapPrijaveClanstvo['zahtev_za_brisanje_iz_clanstva'] = [
//      DOCUMENT KAD JE ZAHTEV
                'document_category_id' => 1,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => '',
                'registry_date' => 'datum_prijema',
                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => [
                    "title" => "Zahtev za brisanje iz članstva u IKS #$request->id",
                    "author" => $request->osoba->ime_roditelj_prezime,
                    "author_id" => $request->osoba->lib,
                    "description" => "",
                    "category" => "Članstvo",
                    "created_at" => $oldData['datum_odluke_uo'],
                ],
                'note' => 'napomena',
                //            'documentable_id' => '',  // request.id
                //            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_prijema',
                'updated_at' => 'updated_at',
                'valid_from' => 'datum_prijema',
            ];
//      DOCUMENT KAD JE RESENJE O PRESTANKU CLANSTVA
            $documentMapPrijaveClanstvo['Resenje_o_prestanku_clanstva'] = [
                'document_category_id' => 3,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => '',
                'registry_date' => 'datum_odluke_uo',

                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => [
                    "title" => "Rešenje o prestanku članstva u IKS #$request->id",
                    "author" => 'Inženjerska komora Srbije',
                    "author_id" => '',
                    "description" => "Za osobu: " . $request->osoba->ime_roditelj_prezime . ", id: " . $request->osoba->lib,
                    "category" => "Članstvo",
                    "created_at" => $oldData['datum_odluke_uo'],
                ],
                'note' => 'napomena',
//            'documentable_id' => '',  // request.id
//            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_odluke_uo',
                'updated_at' => 'updated_at',
                'valid_from' => 'datum_odluke_uo',

            ];
//      DOCUMENT KAD JE RESENJE O BRISANJU IZ EVIDENCIJE
            $documentMapPrijaveClanstvo['Resenje_o_brisanju_iz_evidencije'] = [
                'document_category_id' => 3,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => '',
                'registry_date' => 'datum_odluke_uo',

                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => [
                    "title" => "Rešenje o brisanju iz evidencije IKS #$request->id",
                    "author" => 'Inženjerska komora Srbije',
                    "author_id" => '',
                    "description" => "Za osobu: " . $request->osoba->ime_roditelj_prezime . ", id: " . $request->osoba->lib,
                    "category" => "Članstvo",
                    "created_at" => $oldData['datum_odluke_uo'],
                ],
                'note' => 'napomena',
//            'documentable_id' => '',  // request.id
//            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_odluke_uo',
                'updated_at' => 'updated_at',
                'valid_from' => 'datum_odluke_uo',

            ];
            foreach ($documentMapPrijaveClanstvo as $key => $document) {
                $zg = $request->osoba->zvanjeId->zvanje_grupa_id;
                $reg = Registry::where('base_number', 0)->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                    $q->where('label', "02-$zg");
                })->get()[0];
                $reg->counter++;
//                    $reg->save();
                foreach ($document as $newKey => $oldKey) {
                    //modifikacija vrednosti
                    if ($newKey == 'document_category_id' or $newKey == 'document_type_id' or $newKey == 'registry_id' or $newKey == 'path') {
                        $documentOdlukaData[$newKey] = $oldKey;
                    } else if ($newKey == 'status_id') {
                        $documentOdlukaData[$newKey] = array_search($oldData['status_id'], $oldKey);
                    } else if ($newKey == 'registry_id') {
                        $documentOdlukaData[$newKey] = $reg->id;
                    } else if ($newKey == 'registry_number') {
                        $documentOdlukaData[$newKey] = $reg->registryDepartmentUnit->label . "-" . $reg->base_number . "/" . date("Y", strtotime($oldData['datum_odluke_uo'])) . "-" . $reg->counter;
                    } else if ($newKey == 'metadata') {
                        $documentOdlukaData[$newKey] = json_encode($oldKey, JSON_UNESCAPED_UNICODE);
                    } else if ($newKey == 'note') {
                        $documentOdlukaData[$newKey] = isEmpty($oldData[$oldKey]) ? "Automatski kreiran na osnovu odluke UO o izdavanju licenci." : "##Automatski kreiran na osnovu odluke UO o izdavanju licenci.";
                    } else {
                        $documentOdlukaData[$newKey] = $oldData[$oldKey];
                    }
                }
                echo "<BR>";
                echo "<BR>DOCUMENT " . strtoupper($key);
                echo "<PRE>";
                print_r($documentOdlukaData);
                echo "</PRE>";

                $document = Document::firstOrNew($documentOdlukaData);
                /*if ($document->save()) {
                    $document->documentable()->associate($request);
                    $document->save();
                    $documentOK = TRUE;
                    echo "<BR>Model Document $document->id " . (($document->wasRecentlyCreated) ? " created" : " updated");
                }*/
            }
//        MEMBERSHIPS
            foreach ($membershipsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'status_id') {
                    $membershipData[$newKey] = $oldKey;
                } else if ($newKey == 'note') {
                    $membershipData[$newKey] = !isEmpty($membership->note) ? (!isEmpty($oldData[$oldKey]) ? $membership->note . "##" . $oldData[$oldKey] : $membership->note) : $oldData[$oldKey];
                } else {
                    $membershipData[$newKey] = $oldData[$oldKey];
                }
            }
            echo "<BR>";
            echo "<BR>MEMBERSHIP";
            echo "<PRE>";
            print_r($membershipData);
            echo "</PRE>";

            /*            if ($membership->save()) {
                            $membershipOK = TRUE;
                            echo "<BR>Model Membership $membership->id " . (($membership->wasRecentlyCreated) ? " created" : " updated");
                        }*/


            if ($membershipOK && $requestOK && $documentOK) {
                DB::commit();
                $created = TRUE;
            } else {
                DB::rollBack();
                $created = FALSE;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            echo "<br>" . $e->getMessage();
            $errorInfo = $e->getMessage();
        }
        dd('kraj');

        return $created;
//        }); //transaction
    }

    private function copyArray(array $modelFrom)
    {
        $nameSpace = 'App\Models\\';
//        DB::transaction(function () use ($modelFrom){
        DB::beginTransaction();

        $created = FALSE;

        $membershipOK = FALSE;
        $requestOK = FALSE;
        $documentOK = FALSE;

        $membershipData = []; //reset array

        $requestsMapPrijaveClanstvo = [
//  REQUEST
            'osoba_id' => 'osoba_id',
            'request_category_id' => 1,
            'status_id' => [50 => 10, 51 => 11, 52 => 12, 53 => 13, 54 => 14],
            'note' => 'napomena',
//            'requestable_id' => '',   //membership.id
//            'requestable_type' => '', //\App\Models\Membership
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
        $membershipsMapPrijaveClanstvo = [
//  MEMBERSHIP
            'osoba_id' => 'osoba_id',
            'started_at' => 'datum_odluke_uo',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'note' => 'napomena',
            'status_id' => MEMBERSHIP_STARTED, // WHERE 'status_prijave' = 13,
        ];

        $oldData = $modelFrom;
        /*echo "<BR>";
        echo "<BR>OLD DATA";
        echo "<PRE>";
        print_r($oldData);
        echo "</PRE>";*/

        try {
//        MEMBERSHIPS
            foreach ($membershipsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'status_id') {
                    $membershipData[$newKey] = $oldKey;
                } else if ($newKey == 'note') {
                    $membershipData[$newKey] = isEmpty($oldData[$oldKey]) ? "Osobe iz clanarinaod2006 kojih nema u tabeli memberships." : "##Osobe iz clanarinaod2006 kojih nema u tabeli memberships.";
                } else {
                    $membershipData[$newKey] = $oldData[$oldKey];
                }
            }
            /*echo "<BR>";
            echo "<BR>MEMBERSHIP";
            echo "<PRE>";
            print_r($membershipData);
            echo "</PRE>";*/

            $membership = Membership::firstOrNew($membershipData);
            if ($membership->save()) {
                $membershipOK = TRUE;
                echo "<BR>Model Membership $membership->id " . (($membership->wasRecentlyCreated) ? " created" : " updated");
            }

//        REQUESTS
            foreach ($requestsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'request_category_id') {
                    $requestData[$newKey] = $oldKey;
                } else if ($newKey == 'status_id') {
                    $requestData[$newKey] = array_search($oldData['status_id'], $oldKey);
                } else if ($newKey == 'note') {
                    $requestData[$newKey] = isEmpty($oldData[$oldKey]) ? "Kreiran za osobe iz clanarinaod2006 kojih nema u tabeli memberships." : "##Kreiran za osobe iz clanarinaod2006 kojih nema u tabeli memberships.";
                } else {
                    $requestData[$newKey] = $oldData[$oldKey];
                }
            }
            /*echo "<BR>";
            echo "<BR>REQUEST";
            echo "<PRE>";
            print_r($requestData);
            echo "</PRE>";*/
            $request = \App\Models\Request::firstOrNew($requestData);
            if ($request->save()) {
                $request->requestable()->associate($membership);
                $request->save();
                $requestOK = TRUE;
                echo "<BR>Model Request $request->id " . (($request->wasRecentlyCreated) ? " created" : " updated");
            }

//      DOCUMENTS KAD JE ZAHTEV
            $documentZahtevMapPrijaveClanstvo = [
//      DOCUMENT KAD JE ZAHTEV
                'document_category_id' => 1,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => '',
                'registry_date' => 'datum_prijema',
                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => [
                    "title" => "Zahtev za prijem u članstvo u IKS #$request->id",
                    "author" => $request->osoba->ime_roditelj_prezime,
                    "author_id" => $request->osoba->lib,
                    "description" => "",
                    "category" => "Prijava clanstvo",
                    "created_at" => $oldData['datum_odluke_uo'],
                ],
                'note' => 'napomena',
                //            'documentable_id' => '',  // request.id
                //            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_prijema',
                'updated_at' => 'updated_at',
                'valid_from' => 'datum_prijema',
            ];
//      DOCUMENT KAD JE ODLUKA
            $documentOdlukaMapPrijaveClanstvo = [
                'document_category_id' => 4,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => '',
                'registry_date' => 'datum_odluke_uo',

                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => [
                    "title" => "Odluka o prijemu u članstvo u IKS #$request->id",
                    "author" => 'Inženjerska komora Srbije',
                    "author_id" => '',
                    "description" => "Za osobu: " . $request->osoba->ime_roditelj_prezime . ", id: " . $request->osoba->lib,
                    "category" => "Prijava clanstvo",
                    "created_at" => $oldData['datum_odluke_uo'],
                ],
                'note' => 'napomena',
//            'documentable_id' => '',  // request.id
//            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_odluke_uo',
                'updated_at' => 'updated_at',
                'valid_from' => 'datum_odluke_uo',

            ];

            foreach ($documentOdlukaMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                $zg = $request->osoba->zvanjeId->zvanje_grupa_id;
                $reg = Registry::where('base_number', 0)->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                    $q->where('label', "02-$zg");
                })->get()[0];
                if ($newKey == 'document_category_id' or $newKey == 'document_type_id' or $newKey == 'registry_id' or $newKey == 'path') {
                    $documentOdlukaData[$newKey] = $oldKey;
                } else if ($newKey == 'status_id') {
                    $documentOdlukaData[$newKey] = array_search($oldData['status_id'], $oldKey);
                } else if ($newKey == 'registry_id') {
                    $documentOdlukaData[$newKey] = $reg->id;
                } else if ($newKey == 'registry_number') {
                    $documentOdlukaData[$newKey] = $reg->registryDepartmentUnit->label . "-" . $reg->base_number . "/" . date("Y", strtotime($oldData['datum_odluke_uo'])) . "-" . $request->requestable_id;
                    //todo counter!!!!
                } else if ($newKey == 'metadata') {
                    $documentOdlukaData[$newKey] = json_encode($oldKey, JSON_UNESCAPED_UNICODE);
                } else if ($newKey == 'note') {
                    $documentOdlukaData[$newKey] = isEmpty($oldData[$oldKey]) ? "Automatski kreiran na osnovu odluke UO o izdavanju licenci." : "##Automatski kreiran na osnovu odluke UO o izdavanju licenci.";
                } else {
                    $documentOdlukaData[$newKey] = $oldData[$oldKey];
                }
            }
            /*echo "<BR>";
            echo "<BR>DOKUMENT ODLUKA";
            echo "<PRE>";
            print_r($documentOdlukaData);
            echo "</PRE>";*/

            $document = Document::firstOrNew($documentOdlukaData);
            if ($document->save()) {
                $document->documentable()->associate($request);
                $document->save();
                $documentOK = TRUE;
                echo "<BR>Model Document $document->id " . (($document->wasRecentlyCreated) ? " created" : " updated");
            }

            if ($membershipOK && $requestOK && $documentOK) {
                DB::commit();
                $created = TRUE;
            } else {
                DB::rollBack();
                $created = FALSE;
            }

        } catch (\Exception $e) {
            DB::rollBack();
            echo "<br>" . $e->getMessage();
            $errorInfo = $e->getMessage();
        }
//        dd('kraj');

        return $created;
//        }); //transaction
    }

    private function copyModelPrijavaClanstvo(PrijavaClanstvo $modelFrom)
    {
        $nameSpace = 'App\Models\\';
        $newData = []; //reset array

        $requestsMapPrijaveClanstvo = [
//  REQUEST
            'id' => 'id',
            'osoba_id' => 'osoba_id',
            'request_category_id' => 1,
            'status_id' => [50 => 10, 51 => 11, 52 => 12, 53 => 13, 54 => 14],
            'note' => 'napomena',
//            'requestable_id' => '',   //membership.id
//            'requestable_type' => '', //\App\Models\Membership
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
        $documentMapPrijaveClanstvo = [
            'zahtev' => [
//  DOCUMENT
//      KAD JE ZAHTEV
                'document_category_id' => 1,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => 'zavodni_broj',
                'registry_date' => 'datum_prijema',
                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => '{
                               "title":"Zahtev za prijem u clanstvo u IKS #id",
                               "author":"Ime i prezime",
                               "description":"",
                               "category":"Prijava clanstvo",
                               "created_at":"created_at",
                            }',
                'note' => 'napomena',
//            'documentable_id' => '',  // request.id
//            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_prijema',
                'updated_at' => 'datum_prijema',
                'valid_from' => 'datum_prijema',

            ],
            'odluka' => [
//      KAD JE ODLUKA
                'document_category_id' => 4,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => 'broj_odluke_uo',
                'registry_date' => 'datum_odluke_uo',

                'status_id' => [56 => 11, 57 => 13, 58 => 14],
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => '{
                               "title":"Odluka o prijemu u clanstvo u IKS broj: $registry_number, od: $registry_date",
                               "author":"Ime i prezime",
                               "description":"",
                               "category":"Prijava clanstvo",
                               "created_at":"created_at",
                            }',
                'note' => 'napomena',
//            'documentable_id' => '',  // request.id
//            'documentable_type' => '',    //\App\Models\Membership
                'created_at' => 'datum_odluke_uo',
                'updated_at' => 'datum_odluke_uo',
                'valid_from' => 'datum_odluke_uo',

            ]
        ];
        $membershipsMapPrijaveClanstvo = [
//  MEMBERSHIP
            'osoba_id' => 'osoba_id',
            'started_at' => 'datum_odluke_uo',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'note' => 'napomena',
            'status_id' => MEMBERSHIP_STARTED, // WHERE 'status_prijave' = 13,
        ];

        $oldData = $modelFrom->toArray();

        if ($oldData['status_id'] == PRIJAVA_CLAN_PRIHVACENA) {
//        MEMBERSHIPS
            foreach ($membershipsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'status_id') {
                    $newData[$newKey] = $oldKey;
                } else if ($newKey == 'note') {
                    $newData[$newKey] = isEmpty($oldData[$oldKey]) ? "Kreiran na osnovu podataka iz stare tabele prijave_clanstvo." : "##Kreiran na osnovu podataka iz stare tabele prijave_clanstvo.";
                } else {
                    $newData[$newKey] = $oldData[$oldKey];
                }
            }
            /*            echo "<BR>";
                        echo "<BR>MEMBERSHIP";
                        echo "<PRE>";
                        print_r($newData);
                        echo "</PRE>";*/

            $membership = Membership::updateOrCreate($newData);
            $membership->save();
            if ($membership->wasRecentlyCreated) {
                echo "Model Membership $membership->id  created";
                $created = TRUE;
            } else {
                $created = FALSE;
                echo "Model Membership $membership->id updated";
            }
            $newData = []; //reset array
        }

//        REQUESTS
        foreach ($requestsMapPrijaveClanstvo as $newKey => $oldKey) {
            //modifikacija vrednosti
            if ($newKey == 'request_category_id') {
                $newData[$newKey] = $oldKey;
            } else if ($newKey == 'status_id') {
                $newData[$newKey] = array_search($oldData['status_id'], $oldKey);
            } else if ($newKey == 'note') {
                $newData[$newKey] = isEmpty($oldData[$oldKey]) ? "Importovan iz stare tabele prijave_clanstvo." : "##Importovan iz stare tabele prijave_clanstvo.";
            } else if ($newKey == 'id') {
                $newId[$newKey] = $oldData[$oldKey];
            } else {
                $newData[$newKey] = $oldData[$oldKey];
            }
        }
        /*        echo "<BR>";
                echo "<BR>REQUEST";
                echo "<PRE>";
                print_r($newData);
                echo "</PRE>";*/

        $request = \App\Models\Request::updateOrCreate($newId, $newData);
        if ($oldData['status_id'] == PRIJAVA_CLAN_PRIHVACENA) {
            $request->requestable()->associate($membership);
        }
        $request->save();
        if ($request->wasRecentlyCreated) {
            echo "Model Request id $request->id  created";
            $created = TRUE;
        } else {
            $created = FALSE;
            echo "Model Request id $request->id updated";
        }
        $newId = []; //reset array
        $newData = []; //reset array

        if ($oldData['status_id'] != PRIJAVA_CLAN_KREIRANA) {

            $metadataZahtev = [
                "title" => "Zahtev za prijem u članstvo u IKS #$request->id",
                "author" => $request->osoba->ime_roditelj_prezime,
                "author_id" => $request->osoba->lib,
                "description" => "",
                "category" => "Prijava clanstvo",
                "created_at" => $oldData['datum_odluke_uo'],
            ];
            $metadataOdluka = [
                "title" => "Odluka o prijemu u članstvo u IKS #$request->id",
                "author" => $request->osoba->ime_roditelj_prezime,
                "author_id" => $request->osoba->lib,
                "description" => "",
                "category" => "Prijava clanstvo",
                "created_at" => $oldData['datum_odluke_uo'],
            ];
//        DOCUMENTS
            foreach ($documentMapPrijaveClanstvo as $key => $document) {
                foreach ($document as $newKey => $oldKey) {
                    //modifikacija vrednosti
                    if ($newKey == 'document_category_id' or $newKey == 'document_type_id' or $newKey == 'registry_id' or $newKey == 'path') {
                        $newData[$newKey] = $oldKey;
                    } else if ($newKey == 'status_id') {
                        $newData[$newKey] = array_search($oldData['status_id'], $oldKey);
                    } else if ($newKey == 'metadata') {
                        if ($key == 'zahtev') {
                            $newData[$newKey] = json_encode($metadataZahtev, JSON_UNESCAPED_UNICODE);
                        } else {
                            $newData[$newKey] = json_encode($metadataOdluka, JSON_UNESCAPED_UNICODE);
                        }
                    } else if ($newKey == 'note') {
                        $newData[$newKey] = isEmpty($oldData[$oldKey]) ? "Automatski kreiran na osnovu podataka iz stare tabele prijave_clanstvo." : "##Automatski kreiran na osnovu podataka iz stare tabele prijave_clanstvo.";
                    } else {
                        $newData[$newKey] = $oldData[$oldKey];
                    }
                }
                /*            echo "<BR>";
                            echo "<BR>DOCUMENT " . strtoupper($key);
                            echo "<PRE>";
                            print_r($newData);
                            echo "</PRE>";*/

                $document = Document::updateOrCreate($newData);
//dd($document);
                $document->documentable()->associate($request);
                $document->save();
                if ($document->wasRecentlyCreated) {
                    echo "Model Document id $document->id  created";
                    $created = TRUE;
                } else {
                    $created = FALSE;
                    echo "Model Document id $document->id updated";
                }
                $newData = []; //reset array
            }
        }
//        dd('kraj');


        return $created;
    }

    private function copyModel(PrijavaSiStara $modelFrom)
    {
        $nameSpace = 'App\Models\\';
        $fieldMap = [
            'id' => 'id',
            'osoba_id' => 'osoba',
            'reg_oblast_id' => 'oblast',
            'si_vrsta_id' => 'strucniispit',
            'created_at' => 'datum',
            'updated_at' => 'datum',
            'status_prijave' => 'status',
            'tema' => 'tema',
            'datum_prijema' => 'prijem',
            'app_korisnik_id' => 'prijem_user',
            'zavodni_broj' => 'zavodnibroj',
            'reg_pod_oblast_id' => 'reg_pod_oblast_id',
            'vrsta_posla_id' => 'vrsta_posla_id',
            'zvanje_id' => ''
        ];

        $oldData = $modelFrom->toArray();

        foreach ($fieldMap as $newKey => $oldKey) {
            //modifikacija vrednosti
            if ($newKey == 'zvanje_id') {
                $newData[$newKey] = $modelFrom->osobaId->zvanje;
            } else if ($newKey == 'created_at' or $newKey == 'updated_at') {
                $newData[$newKey] = date('Y-m-d H:i:s', strtotime($oldData[$oldKey]));
            } else if ($newKey == 'datum_prijema') {
                $newData[$newKey] = date('Y-m-d', strtotime($oldData[$oldKey]));
            } else if ($newKey == 'status_prijave') {
                switch ($oldData[$oldKey]) {
                    case 'K':
                        $newData[$newKey] = PRIJAVA_OTKAZANA;
                        break;
                    case NULL:
                    case '':
//                        dd($oldData[$oldKey]);
                        $newData[$newKey] = PRIJAVA_OTKAZANA;
                        break;
                    case 'P':
                        $newData[$newKey] = PRIJAVA_ZAVEDENA;
                        break;

                }
            } else {
                $newData[$newKey] = $oldData[$oldKey];
            }
        }

        /*        echo "<PRE>";
                print_r($oldData);
                echo "</PRE>";
                echo "<BR>";
                echo "<PRE>";
                print_r($newData);
                echo "</PRE>";
                echo "<BR>";*/

//        dd();
        $modelTo = new SiPrijava($newData);
        $modelTo->save();
        if ($modelTo->wasRecentlyCreated) {
//            echo "<br>Model $modelTo->id  created";
            $created = TRUE;
        } else {
            $created = FALSE;
//            echo "<br>Model $modelTo->id not created";
        }
        return $created;
    }

    private function prijavaSi()
    {
        $prijavaSiImport = PrijavaSiStara::
//        $prijavaSiImport = DB::table('tzahtevsi')
//                whereRaw("osoba NOT IN (SELECT distinct id FROM tosoba)")
//                    ->whereNull('status')
//                    ->orderBy('id')
        chunkById(1000, function ($zahtevi) {
            //                dd($zahtevi);
            //                $arr = $osobe->pluck('osoba')->toArray();
            foreach ($zahtevi as $zahtev) {
//                            $message[$zahtev] = $this->replicateModel('osobaSi', 'osoba', $zahtev);

                $result = $this->copyModel($zahtev);
                /*                if (!empty($temp['Copied'])) {
                                    $copied[] = $temp['Copied'];
                                }
                                if (!empty($temp['Exist'])) {
                                    $exist[] = $temp['Exist'];
                                }*/

                $this->counter++;
                if ($result) {
                    $this->ok++;
                } else {
                    $this->error++;
                }
            }
            echo "<br>Kreirano: $this->ok od $this->counter";
            echo "<br>Nije kreirano: $this->error od $this->counter";
//            echo "<br>Copied: " . count($copied) . ", Exists: " . count($exist);
//            $message['copied'] = implode(";", $copied);
//            $message['exist'] = implode(";", $exist);
            /*            echo "<PRE>";
                        print_r($message);
                        echo "</PRE>";
                        echo "<BR>";*/
        })
//        limit(2)
//            ->pluck('osoba')->toArray()
            ->get();


        /*//        SVE PRIJAVE
                $prijaveSiImport = PrijavaSiStara::all();
        //        dd($prijaveSiImport->take(10));
                foreach ($prijaveSiImport
        //                     ->slice(5000,100)
                         as $model) {
                    $temp = $this->copyModel($model);
                    $copied[] = $temp['Copied'];
                    $exist[] = $temp['Exist'];
                    $this->counter++;
                }
                $message['copied'] = implode(";", $copied);
                $message['exist'] = implode(";", $exist);
                dd($message);*/
    }

    private function clanarina()
    {
        //        $licence = Licenca::all()->count();
//        $licenceClanarinaOld = ClanarinaOld::distinct('brlicence')->get()->count();
        $licenceClanarinaOld = ClanarinaOld::whereHas('licenca.osobaId', function ($q) {
            $q->distinct('id');
        })->get()->count();

        $osobeClanarinaOldCol = Osoba::select('id', 'ime', 'prezime')
            ->whereHas('licence.clanarineold', function ($q) {
                $q->distinct('brlicence');
            })
            //            ->limit(10)
            //            ->get(['id','ime','prezime'])
            ->get()
            ->makeHidden(['ime_prezime_jmbg', 'full_name', 'ime_prezime_licence'])
            //            ->toArray()
            //            ->count()
        ;

        $osobeClanarinaCol = Osoba::select('id', 'ime', 'prezime')
            ->whereHas('clanarine', function ($q) {
                $q->distinct('osoba');
            })
            //            ->limit(10)
            //            ->get(['id','ime','prezime'])
            ->get()
            ->makeHidden(['ime_prezime_jmbg', 'full_name', 'ime_prezime_licence'])
            //            ->toArray()
            //            ->count()
        ;

        //        dd($osobeClanarinaOld->toSql());
        $osobeClanarina = $osobeClanarinaCol->toArray();
        $osobeClanarinaOld = $osobeClanarinaOldCol->toArray();
        $osobeSamoUClanarina = $osobeClanarinaCol->diff($osobeClanarinaOldCol)->toArray();
        $osobeSamoUClanarinaOld = $osobeClanarinaOldCol->diff($osobeClanarinaCol)->toArray();
        //        dd($osobeSamoUClanarinaOld);
    }

    private function deleteModel()
    {
//        DELETE RECORDS FROM DB
        try {
            $result = PrijavaSiStara::
            whereRaw("osoba NOT IN (SELECT distinct id FROM tosoba)")
                ->whereNull('status')
                ->orderBy('id')
                ->delete();
            return "Deleted: " . $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }


    private function random_color_part(int $from, int $to)
    {
        return str_pad(dechex(mt_rand($from, $to)), 2, '0', STR_PAD_LEFT);
    }

    private function random_color()
    {
        return $this->random_color_part(100, 220) . $this->random_color_part(100, 220) . $this->random_color_part(100, 220);
    }

    /**
     * @param string $str
     * @param array $arr
     * @return false|array
     */
    private function checkIfStrInArr(string $str, array $arr)
    {
        foreach ($arr as $key => $item) {
            if (strstr($str, $item)) {
//        echo "1=>2 $str | $item<br>";
                $keys[] = $key;
            } else if (strstr($item, $str)) {
//        echo "2=>1 $str | $item<br>";
                $keys[] = $key;
            }
        }
        if (!isset($keys) or count($keys) == 0)
            $keys = FALSE;
        return $keys;
    }


}
