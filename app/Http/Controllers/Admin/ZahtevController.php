<?php

namespace App\Http\Controllers\Admin;

use App\Imports\MirovanjeImport;
use App\Models\EvidencijaMirovanja;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Mixed_;
use \Tesla\JMBG\JMBG;
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
use Illuminate\Http\UploadedFile;
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
//            $licencaO = Licenca::find($licenca);
            $licencaO = Licenca::where('id', $licenca)->first();
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
                $data->podOblastVisible = $licencaTip->podOblast->visible;
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

//                $this->log($zahtev, $status_grupa, "$naziv zahtev: $zahtev->id, status: " . REQUEST_SUBMITED);
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

//            UNET JE JMBG PROVERITI DA LI POSTOJI U BAZI
            if (!empty($licenca['jmbg'])) {
                $is_osoba = $this->getOsoba($licenca['jmbg']);

                if ($is_osoba == FALSE) {
                    $messageLicencaNOK .= ', Za uneti jmbg ne postoji osoba u bazi';
                    $countNOK++;
                    continue;
                }
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
            } else if (!empty($licenca['tip'])) {
                $broj = $licenca['tip'];
                $tip = 'tip_licence';
                $jmbg = $licenca['jmbg'];
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

            // da li postoji licenca
//            $licenca_exist = Licenca::find($licenca['broj']);
            $licenca_exist = Licenca::where('id', $licenca['broj'])->first();
            if (!empty($licenca_exist)) {
                $messageLicencaNOK .= ' Licenca broj ' . $licenca['broj'] . ' se vec nalazi u Registru!';
                $countNOK++;
                continue;
            }

            if (!is_null($licenca['jmbg'])) {
                if (!$this->checkOsoba(trim($licenca['jmbg']))) {
                    $falseJMBG[$licenca['jmbg']] = 'Osoba sa jmbg: ' . $licenca['jmbg'] . ' ne postoji u bazi!';
                    $messageLicencaNOK .= ' Osoba sa jmbg: ' . $licenca['jmbg'] . ' ne postoji u bazi!';
                    $countNOK++;
                    continue;
//                return Redirect::back()->withErrors(['Osoba ne postoji!']);
                } else {
                    $osoba = Osoba::find($licenca['jmbg']);
                    if (is_null($osoba->lib)) {
                        $lib = new LibLibrary();
                        $lib->dodeliJedinstveniLib($osoba->id, Auth::user()->id);
                        $this->logOsoba($osoba, LICENCE, "Ažurirana osoba: $osoba->ime $osoba->prezime($osoba->id), lib: $osoba->lib, status: $osoba->clan");
                    }
                }
            } else {
                continue;
            }

            // da li ima i koji je zahtev za licencu
            $respZ = $this->getZahtevLicenca($broj, $tip, $jmbg);

            if ($respZ->status) {
                // AZURIRAJ ZAHTEV
                $respZ = $this->azurirajZahtevLicenca($respZ->zahtev, $licenca);
            } else {
                // KREIRAJ ZAHTEV
                $respZ = $this->kreirajZahtevLicenca($licenca);
            }

            if ($respZ->status) {
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
        $messageLicencaOK = ($countOK > 0) ? "$messageLicencaOK . Uspešno sačuvano u bazi($countOK)" : "";
        $messageLicencaNOK = ($countNOK > 0) ? "$messageLicencaNOK . Nije sačuvano u bazi($countNOK)" : "";

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

    public function getOsoba($jmbg)
    {
        $osoba = Osoba::find($jmbg);
        if (!is_null($osoba)) {
            return $osoba->full_name;
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
    private function getZahtevLicenca($broj, $tip = 'broj_licence', $jmbg = ''): \stdClass
    {
        $response = new \stdClass();
        $response->status = FALSE;

        switch ($tip) {
            case 'broj_zahteva':
                $zahtevi = ZahtevLicenca::where('id', $broj)->whereNotIn('status', [REQUEST_FINISHED, REQUEST_CANCELED])->get();
                break;
            case 'broj_licence':
                $zahtevi = ZahtevLicenca::where('licenca_broj', $broj)->whereNotIn('status', [REQUEST_FINISHED, REQUEST_CANCELED])->get();
                break;
            case 'broj_prijave':
                $zahtevi = ZahtevLicenca::where('si_prijava_id', $broj)->whereNotIn('status', [REQUEST_FINISHED, REQUEST_CANCELED])->get();
                break;
            case 'tip_licence':
                $zahtevi = ZahtevLicenca::where('licencatip', $broj)->where('osoba', $jmbg)->whereNotIn('status', [REQUEST_FINISHED, REQUEST_CANCELED])->get();
                break;
        }

        if ($zahtevi->isNotEmpty()) {

            if ($zahtevi->count() > 1) {
                // IMA VIŠE ZAHTEVA

                // da li u kolekciji postoji neki zahtev sa statusom 22 (automatski)
                if ($zahtevi->contains('status', ZAHTEV_LICENCA_AUTOMATSKI)) {

                    $zahtev_automatski = $zahtevi->where('status', ZAHTEV_LICENCA_AUTOMATSKI)->first();

                    // svi ostali zahtevi se otkazuju
                    $zahtevi->each(function ($model) use ($zahtev_automatski) {
                        if ($model->id != $zahtev_automatski->id) {
                            $model->status = REQUEST_CANCELED;
                            $model->save();
                        }
                    });
                    $response->status = TRUE;
                    $response->zahtev = $zahtev_automatski;
                    $response->message = "Pronadjen zahtev broj " . $response->zahtev->id;

                } else {
                    // zahtevi sa drugim statusima (koji nisu 22)
                    $zahtev = $zahtevi->first();

                    $zahtevi->each(function ($model) use ($zahtev) {
                        if ($model->id != $zahtev->id) {
                            $model->status = REQUEST_CANCELED;
                            $model->save();
                        }
                    });
                    $response->status = TRUE;
                    $response->zahtev = $zahtev;
                    $response->message = "Pronadjen zahtev broj " . $response->zahtev->id;

                }

            } else {
                // IMA JEDAN ZAHTEV
                $response->status = TRUE;
                $response->zahtev = $zahtevi->first();
                $response->message = "Pronadjen zahtev broj " . $response->zahtev->id;
            }
        } else {
            // NEMA ZAHTEVA
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

        // LicencaTip model na osnovu odabranog tipa licence iz formulara
        $tipLicence = LicencaTip::find($licenca['tip']);

        if (is_null($tipLicence)) {
            $response->zahtev = $zahtev;
            $response->message = "neispravan tip licence: " . $licenca['tip'];
            $response->status = FALSE;
            return $response;
        }

        // Osoba model na osnovu unetog jmbg iz formulara
        $osoba = Osoba::find($licenca['jmbg']);

        // postavljanje vrednosti
        $zahtev->osoba = $licenca['jmbg'];
        $zahtev->licenca_broj = $licenca['broj'];
        $zahtev->licenca_broj_resenja = $licenca['broj_resenja'];
        $zahtev->licenca_datum_resenja = Carbon::parse($licenca['datum_resenja'])->format('Y-m-d');
        $zahtev->licencatip = $tipLicence->id;
        $zahtev->vrsta_posla_id = $tipLicence->sekcija;
        $zahtev->reg_oblast_id = $tipLicence->podOblast->oblast_id;
        $zahtev->reg_pod_oblast_id = $tipLicence->pod_oblast_id;
        if (empty($zahtev->zvanje_id)) {
            $zahtev->zvanje_id = $osoba->zvanje;
        }
        $zahtev->status = REQUEST_FINISHED;
        $zahtev->prijem = Carbon::parse($licenca['datum_prijema'])->format('Y-m-d');
        $zahtev->datum = date("Y-m-d");

        $document = $zahtev->documents->where('document_category_id', 5); // zahtev za izdavanje licence
        if ($document->count() > 1) {
            $response->message = "Postoji {$document->count()} dokumenata za zahtev broj $zahtev->id";
            $response->status = FALSE;
        } else if ($document->count() == 1) {
            $document = $document->first();
        } else { // nema dokument
            $document = new Document();
        }

        $document->document_type_id = 1; // zahtev
        $document->document_category_id = 5; // zahtev za izdavanje licence
        $document->registry_date = $zahtev->prijem;
        $document->status_id = DOCUMENT_REGISTERED;

        $label = '02-' . $osoba->zvanjeId->zvanje_grupa_id;

        // trazimo skraceni delovodnik
        $registry = Registry::where('status_id', AKTIVAN)
            ->whereHas('registryDepartmentUnit', function ($q) use ($label) {
                $q->where('label', "$label");
            })
            ->whereHas('requestCategories', function ($q) use ($zahtev, $document) {
                $q->where('registry_request_category.request_category_id', $zahtev->request_category_id); // zahtev za izdavanje licence
            })
            ->get();


        if (empty($registry)) {
            // nema
            $response->message = "Nije pronadjen delovodnik prilikom kreiranja zahteva broj $zahtev->id";
            $response->status = FALSE;
        } else if ($registry->count() > 1) {
            // ima vise od 1
            $response->message = "Postoji {$registry->count()} pronadjenih delovodnika prilikom kreiranja zahteva broj $zahtev->id";
            $response->status = FALSE;
        } else {
            // ima tacno 1
            $registry = $registry->first();
        }

        if (empty($document->registry_number)) {
            $registry->counter++;
            $document->registry_id = $registry->id;
            $document->registry_number = $registry->registryDepartmentUnit->label . "-" . $registry->base_number . "/" . date("Y", strtotime($zahtev->prijem)) . "-" . $registry->counter;
        }
        $document->user_id = backpack_user()->id;
        $document->valid_from = $zahtev->prijem;


        if ($document->save()) {
//            dd($document);
            $document->barcode = empty($document->barcode) ? "{$zahtev->id}#{$document->id}#{$document->registry_number}#{$document->registry_date}" : $document->barcode;
            $document->documentable()->associate($zahtev);
            $document->metadata = json_encode([
                "title" => "Zahtev za izdavanje licence za Sticanje licence #{$zahtev->id}",
                "author" => $osoba->ime_roditelj_prezime,
                "author_id" => $osoba->lib,
                "description" => "",
                "dopuna" => '',
                "category" => 'Sticanje licence',
                "created_at" => $document->registry_date,
            ], JSON_UNESCAPED_UNICODE);
            $document->save();

            if ($registry->isDirty()) $registry->save();
        }

        if ($zahtev->isDirty()) {
            $zahtev->save();
            $response->message = "Ažuriran zahtev: $zahtev->id, status: $zahtev->id";
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
        $zahtev->request_category_id = 7; //todo add constant
        $response = $this->azurirajZahtevLicenca($zahtev, $licenca);
        $response->message = "Kreiran zahtev: $zahtev->id, status: " . REQUEST_SUBMITED;
        return $response;
    }

    /**
     * @param $broj_licence
     * @return \stdClass
     */
    private function getLicenca($broj_licence)
    {
        $response = new \stdClass();
//        $licenca = Licenca::find($broj_licence);
        $licenca = Licenca::where('id', $broj_licence)->first();
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

        $zahtev->status = REQUEST_FINISHED;

        $zahtev->save();
        $this->log($zahtev, LICENCE, "Ažuriran datum prijema zahteva: $zahtev->prijem, status: " . REQUEST_FINISHED);

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
    public function getLicencaTip($tip)
    {
        $tip = substr($tip, 0, 3);
        $licencaTip = LicencaTip::where("id", 'LIKE', $tip . '%')->get()->pluck('tip_naziv', 'id')->toArray();
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
        $jmbgs = [
            '0104986782826', '0110974715090', '0301983330061', '0303988715223', '0405983727816', '0407988710035', '0408992793413', '0412991710120', '0504986715086', '0504991789314', '0507991715212', '0511990183753', '0612965910048', '0612988795092', '0612994800030', '0806979754110', '0806986895021', '0809984170045', '0906987733526', '1006967170021', '1009982380007', '1105989170015', '1105993710229', '1108983710211', '1112972710020', '1302986715193', '1304976742012', '1305974757523', '1305983775011', '1305991773664', '1410974724119', '1603984715021', '1608977731331', '1701992810639', '1806992735013', '1809980820014', '1903985740015', '1908985383923', '1910990785031', '2004964722229', '2009991730030', '2103965744119', '2105979850097', '2106992715030', '2106993755028', '2107985770030', '2204965734416', '2211984382128', '2212987100049', '2302993100009', '2309983122149', '2312988787832', '2403987787834', '2501990715110', '2602966710117', '2604993710342', '2609980710043', '2610994715186', '2804973720022', '2807984793934', '2905969710358', '2909986730058', '3007985150023'
        ];
//        dd('alo');
        $osobe = Osoba::
//        whereIn('id', $jmbgs)
//        $requests = \App\Models\Request::where('request_category_id', 3)
//            ->where('note', 'SFL_20211130')
//            ->where('status_id', KREIRAN)
        whereNull('ulica')
            ->get();
//        dd($osobe->pluck('id')->toArray());
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
            $osoba->posta_opstina_id = $osoba->prebivalisteopstinaid;
            $osoba->posta_pb = $osoba->prebivalistebroj;
            $osoba->posta_drzava = $osoba->prebivalistedrzava;
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

    public function clanstvo($action, $save = '')
    {
        DB::disableQueryLog();
//        za one koji nisu clanovi
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '6000');

        $this->counter = 0;
        $this->error = 0;
        $this->ok = 0;

//        CLANARINA
//        $this->clanarina();
//        $this->prijavaSi();
//        $this->prijaveClanstvo();//1                  OK ! ponoviti zbog zavodnih brojeva
//        $this->preziveli();                             //ONI KOJI SU TREBALI DA BUDU OBRISANI A NISU
//        $this->osobeClanarinaNotMembership();//2      OK
//        $this->osobeClanarinaNotMembershipFix();//2.25  OK
//        $this->osobeClanarinaMembership();//2.5       OK
//        $this->osobaNotClanNotRequest();//3           OK
//        $this->osobaNotClanNapomenaRequest();//4 nije gotovo
//        $this->osobaClanNapomenaRequest();//5           ...
//        $this->osobaClanRequestResenje();//6           samo clan = 0 i napomena Usled neplacanja clanarine
//        $this->osobaObrisanaAktivnaRequest();//7

//        $this->prekiniClanstvo;//pojedinacno dok se ne sredi sve
//        $this->osobaUpisiNapomenuBrisanje();//pojedinacno dok se ne sredi sve
//        $this->nereseniAktivni();//pojedinacno dok se ne sredi sve
//        $this->copyZahteviLicenceRequest();//pojedinacno dok se ne sredi sve
//        $this->zahtevLicencaObrisiDuplikate();

//        $this->count();
//        potrebno je svima iz stavke 6 kreirati odgovarajuca dokumenta i podesiti statuse zahteva i membershipa


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
         * 5: oni koji su se ponovo uclanili treba da se updateuje membershipp ended_at updatuje request i da se otvori novi membership za
         *
         * 6: svi koji imaju resenje za brisanje
         *
         *
         */

        echo '<style>
                        html, body {
                            background-color: #fff;
                            color: #636b6f;
                            font-family: "Nunito", sans-serif;
                            font-weight: 200;
                            font-size: 16px;
                            height: 100vh;
                        }
                        a{
                            display: block;
                            margin: 5px;
                        }
                        ol{
                        width: 400px;
                        }

                    </style>
                <ol>';

        echo '<li><a href="/admin/clanstvo/clanarina">clanarina</a></li>';
        echo '<li><a href="/admin/clanstvo/prijavaSi">prijavaSi</a></li>';
        echo '<li><a href="/admin/clanstvo/prijaveClanstvo">prijaveClanstvo</a></li>';
        echo '<li><a href="/admin/clanstvo/preziveli">preziveli</a></li>';
        echo '<li><a href="/admin/clanstvo/osobeClanarinaNotMembership">osobeClanarinaNotMembership</a></li>';
        echo '<li><a href="/admin/clanstvo/osobeClanarinaNotMembershipFix">osobeClanarinaNotMembershipFix</a></li>';
        echo '<li><a href="/admin/clanstvo/osobeClanarinaMembership">osobeClanarinaMembership</a></li>';
        echo '<li><a href="/admin/clanstvo/osobaNotClanNotRequest">osobaNotClanNotRequest</a></li>';
        echo '<li><a href="/admin/clanstvo/osobaNotClanNapomenaRequest">osobaNotClanNapomenaRequest</a></li>';
        echo '<li><a href="/admin/clanstvo/osobaClanNapomenaRequest">osobaClanNapomenaRequest</a></li>';
        echo '<li><a href="/admin/clanstvo/osobaClanRequestResenje">osobaClanRequestResenje</a></li>';
        echo '<li><a href="/admin/clanstvo/osobaObrisanaAktivnaRequest">osobaObrisanaAktivnaRequest</a>';
        echo '<li><a href="/admin/clanstvo/osobeZaBrisanjeClanstvo">osobeZaBrisanjeClanstvo</a></li>';
        echo '<li><a href="/admin/clanstvo/prekiniClanstvo">prekiniClanstvo</a></li>';
        echo '<li><a href="/admin/clanstvo/osobaUpisiNapomenuBrisanje">osobaUpisiNapomenuBrisanje</a></li>';
        echo '<li><a href="/admin/clanstvo/nereseniAktivni">nereseniAktivni</a></li>';
        echo '<li><a href="/admin/clanstvo/copyZahteviLicenceRequest">copyZahteviLicenceRequest</a></li>';
        echo '<li><a href="/admin/clanstvo/kreirajDokumenteZaZahteveZaMirovanje">kreirajDokumenteZaZahteveZaMirovanje</a></li>';
        echo '<li><a href="/admin/clanstvo/kreirajDokumenteZaStareZavedeneZahteve">kreirajDokumenteZaStareZavedeneZahteve</a></li>';
        echo '<li><a href="/admin/clanstvo/osobeKojeNisuPreuzeleLicencu">osobeKojeNisuPreuzeleLicencu</a></li>';
        echo '<li><a href="/admin/clanstvo/nevazeceLicence">nevazeceLicence</a></li>';
        echo '<li><a href="/admin/clanstvo/addZvanjeIdToZahtevLicenca">addZvanjeIdToZahtevLicenca</a></li>';
        echo '<li><a href="/admin/clanstvo/insertDatumRodjenjaFromJmbg">insertDatumRodjenjaFromJmbg</a></li>';
        echo '<li><a href="/admin/clanstvo/updateMirovanjaFromExcel">updateMirovanjaFromExcel</a></li>';
        echo '<li><a href="tel:38163247700">zovi Bojana</a></li>';

        echo '</ol>';

        switch ($action) {
            case 'clanarina':
                $this->clanarina($save);
                break;
            case 'prijavaSi':
                $this->prijavaSi($save);
                break;
            case 'prijaveClanstvo':
                $this->prijaveClanstvo($save);
                break;
            case 'preziveli':
                $this->preziveli($save);
                break;
            case 'osobeClanarinaNotMembership':
                $this->osobeClanarinaNotMembership($save);
                break;
            case 'osobeClanarinaNotMembershipFix':
                $this->osobeClanarinaNotMembershipFix();
                break;
            case 'osobeClanarinaMembership':
                $this->osobeClanarinaMembership($save);
                break;
            case 'osobaNotClanNotRequest':
                $this->osobaNotClanNotRequest($save);
                break;
            case 'osobaNotClanNapomenaRequest':
                $this->osobaNotClanNapomenaRequest($save);
                break;
            case 'osobaClanNapomenaRequest':
                $this->osobaClanNapomenaRequest($save);
                break;
            case 'osobaClanRequestResenje':
                $this->osobaClanRequestResenje($save);
                break;
            case 'osobaObrisanaAktivnaRequest':
                $this->osobaObrisanaAktivnaRequest($save);
                break;
            case 'osobeZaBrisanjeClanstvo':
                $this->osobeZaBrisanjeClanstvo($save);
                break;
            case 'prekiniClanstvo':
                $this->prekiniClanstvo($save);
                break;
            case 'osobaUpisiNapomenuBrisanje':
                $this->osobaUpisiNapomenuBrisanje($save);
                break;
            case 'nereseniAktivni':
                $this->nereseniAktivni($save);
                break;
            case 'copyZahteviLicenceRequest':
                $this->copyZahteviLicenceRequest($save);
                break;
            case 'kreirajDokumenteZaZahteveZaMirovanje':
                $this->kreirajDokumenteZaZahteveZaMirovanje($save);
                break;
            case 'kreirajDokumenteZaStareZavedeneZahteve':
                $this->kreirajDokumenteZaStareZavedeneZahteve($save);
                break;
            case 'osobeKojeNisuPreuzeleLicencu':
                $this->osobeKojeNisuPreuzeleLicencu();
                break;
            case 'nevazeceLicence':
                $this->nevazeceLicence();
                break;
            case 'addZvanjeIdToZahtevLicenca':
                $this->addZvanjeIdToZahtevLicenca();
                break;
            case 'insertDatumRodjenjaFromJmbg':
                $this->insertDatumRodjenjaFromJmbg();
                break;
            case 'updateMirovanjaFromExcel':
                $this->updateMirovanjaFromExcel();
                break;
            default:
//                $this->count();
                break;
        }

    }

    private function outputHtmlTable($array)
    {
        $result = '';
        $result .= "<table style='box-sizing: border-box' cellspacing=0>";
        $header = TRUE;
        foreach ($array as $row) {
            if ($header) {
                $result .= "<tr>";
                foreach ($row as $head => $col) {
                    $result .= "<th style='border:  solid 1px gray; margin: 0; padding: 3px;'>$head</th>";
                }
                $result .= "</tr>";
            }
            $result .= "<tr>";
            foreach ($row as $head => $col) {
                $result .= "<td style='border:  solid 1px gray; margin: 0; padding: 3px;'>$col</td>";
            }
            $result .= "</tr>";
            $header = FALSE;
        }
        $result .= "</table>";
        return $result;
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

    private function count()
    {
        $query = \App\Models\osoba::
//            whereHas('izmirenaClanarina')
//        whereHas('izmirenaClanarinaSa2020') //preziveli
//        whereHas('izmirenaClanarinaSa2021') //
        whereHas('dugujeClanarinuZa2021') // koji duguju samo za 2021 godinu
        ->distinct('id')
            ->where('clan', 1)
//            ->whereNotNull('napomena')

            /*->whereDoesntHave('requests', function ($q) {
                $q->whereHas('requestCategory', function ($q) {
                    $q->where('request_category_type_id', 2)
                    ;
                });
            })*/
            ->whereDoesntHave('requests', function ($q) {
                $q
                    ->where('request_category_id', 4)
//                    ->where('status_id', REQUEST_FINISHED)
//                    ->where('note', 'Staro mirovanje')
                ;
            })//            ->toSql()
        ;

        dd($query->count());
//        dd($query);
    }

    private function preziveli($save = '')
    {
        $osobaOK = FALSE;
        $print = [];

        echo "<h1>PREŽIVELI ($save)</h1>";

        $odustaliOdZalbe = [234, 482, 1547, 1615, 2032, 2256, 2282, 2301, 2344, 2846, 2901, 2953, 3118, 3580, 4358, 4632, 4677, 4939, 5430, 5480, 5550, 5607, 5818, 5842, 5863, 5975, 6101, 6241, 6442, 6817, 7549, 7569, 7642, 8125, 8351, 8680, 8701, 8714, 9025, 9073, 9520, 9971];

        /*$osobeImport = $this->getExcel();
        foreach ($osobeImport->toArray() as $key => $value) {
            $osobeBrisanje[$value['jmbg']] = $value;
        }*/
//        dd($osobeBrisanje);

//                dd($osobeBrisanje->pluck('jmbg')->toArray());

        $this->counter = 0;
//        echo "ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO CLAN 1<BR>";
        echo "ONI KOJI SU TREBALI DA BUDU OBRISANI A NISU<BR>";


        $query = \App\Models\Osoba::
        where('clan', 1)
//            ->whereHas('izmirenaClanarina')
//            ->whereHas('neIzmirenaClanarinaDo2021')
            ->whereHas('neIzmirenaClanarinaDo2022')
//            ->whereDoesntHave('neIzmirenaClanarinaDo2021')
//        ->whereHas('izmirenaClanarinaSa2021')
            ->distinct('id')
//            ->whereNotNull('napomena')

//            ->whereHas('licence')
//            ->whereDoesntHave('licence')

            /* ->whereDoesntHave('requests', function ($q) {
                 $q->whereHas('requestCategory', function ($q) {
                     $q->where('request_category_type_id', 2);
                 });
             })*/
            ->whereDoesntHave('requests', function ($q) {
                $q
                    ->where('request_category_id', 4)
//                    ->where('status_id', REQUEST_FINISHED)
//                    ->where('note', 'Staro mirovanje')
                ;
            })
            ->orderBy('id')
            ->chunkById(100, function ($osobe) use (/*$osobeBrisanje,*/ &$print, $save) {
                $jmbgs = '';
                foreach ($osobe as $osoba) {
                    $requests = $osoba->requests->where('request_category_id', 1);
                    $requestsStr = implode(',', $requests->pluck('status_id')->toArray());
//                    if (isset($osobeBrisanje[$osoba->id])) {
//                        $osobaExcel = $osobeBrisanje[$osoba->id];
//                    } else {
                    $osobaExcel = FALSE;
//                    }
//                    $clanarine = $osoba->poslednjeDveClanarine->pluck('iznoszanaplatu', 'rokzanaplatu')->toArray();
                    $clanarine = $osoba->poslednjeDveClanarine->toArray();
                    if (isset($clanarine[1])) {
                        $clanarine0 = array_splice($clanarine[0], 1, 5);
                        $clanarine1 = array_splice($clanarine[1], 1, 5);
                        $clanarineStr = http_build_query($clanarine0, '', '; ') . "<br>" . http_build_query($clanarine1, '', '; ');
//                        $clanarineStr = NULL;
                    } else {
                        $clanarine0 = array_splice($clanarine[0], 1, 5);
                        $clanarineStr = http_build_query($clanarine0, '', '; ');
//                        $clanarineStr = NULL;
                    }
//                    dd($clanarine0);
//                    $clanarineStr = http_build_query($clanarine, '', '; ');

//                    if (!empty($clanarineStr) //samo provera jedna ili dve clanarine
//                        AND substr($clanarine0['rokzanaplatu'],0,4) == '2021'
//                    ) {
                    $jmbgs .= "'$osoba->id', ";
//                        $jmbgs .= "$osoba->id<br>";
                    $allRequests = implode(',', $osoba->requests->pluck('id')->toArray());
                    $memberships = implode(',', $osoba->memberships->pluck('id')->toArray());
                    $printRow = [];
                    $printRow['count'] = ++$this->counter;
                    $printRow['JMBG'] = $osoba->id;
                    $printRow['član'] = $osoba->clan;
//                    $printRow['REQUEST'] = "<strong>$request->id</strong>($request->request_category_id)";
//                    $printRow['DOC'] = implode(',', $request->documents->pluck('document_category_id')->toArray());
                    $printRow['ALLREQ'] = $allRequests;
                    $printRow['Članarine'] = $clanarineStr;
                    $printRow['napomena'] = $osoba->napomena;
                    $printRow['MEMB'] = $memberships;
                    $printRow['excel'] = $osobaExcel;
//                    $printRow['Status'] = "{$request->status->naziv}, $request->updated_at";

                    if ($save == 'save') {
                        if ($osoba->save()) {
                            $osobaOK = TRUE;
                        }
                        $printRow['Osoba SAVED'] = $osobaOK;
                    }


                    $print[] = $printRow;
//                    }
//                    echo " $this->counter";
                }
//                echo "$jmbgs";
                echo "<br>$jmbgs<br>";
            })
//        ->limit(100)
//            ->get()
//            ->pluck('osoba_id')
//            ->toArray()
//                        ->toSql()
        ;

        echo $this->outputHtmlTable($print);
//        dd($this->counter);
//        dd($query->count());
        dd($query);
    }

    private function osobeClanarinaNotMembership($save = '')
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
            ->chunkById(1000, function ($osobe) use ($save) {
                foreach ($osobe as $osoba) {
                    $clanarina = $osoba->prvaClanarina()->get()->toArray()[0];
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
                    if ($save == 'save') {
                        $result = $this->copyArray($data);
                    }

                    /*$this->counter++;
                    if ($result) {
                        $this->ok++;
                    } else {
                        $this->error++;
                        break;
                    }*/
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
        dd($query);
    }

    private function osobeClanarinaNotMembershipFix()
    {

        $query = Membership::where('note', 'Osobe iz clanarinaod2006 kojih nema u tabeli memberships.')
            ->orderBy('id')
            ->chunkById(100, function ($memberships) {
                foreach ($memberships as $membership) {
                    $clanarina = $membership->osoba->prvaClanarina()->get()->toArray()[0];
                    $request = $membership->requests->where('request_category_id', 1)->first();
//                    dd($request->documents[0]->id);
                    $document = $request->documents->where('document_category_id', 18)->first();

                    $newupdated_at = Carbon::parse($clanarina['rokzanaplatu'])->format("Y-m-d H:i:s");
                    $newdocregistry_number = preg_replace("/\/(\d+)-/", "/" . date('Y', strtotime($clanarina['rokzanaplatu'])) . "-", $document->registry_number);
                    $newdocmetadata = preg_replace("/\"created_at\":\"(\d+-\d+-\d+)\"/", "\"created_at\":\"" . $clanarina['rokzanaplatu'] . "\"", $document->metadata);

                    DB::beginTransaction();

                    try {

                        $membership->started_at = $clanarina['rokzanaplatu'];
                        $membership->created_at = $membership->updated_at = $newupdated_at;
                        if ($membership->save()) {
                            $membershipOK = TRUE;
                            echo "<BR>Model Membership $membership->id " . (($membership->wasRecentlyCreated) ? " created" : " updated");
                        }

                        $request->created_at = $request->updated_at = $newupdated_at;
                        if ($request->save()) {
                            $requestOK = TRUE;
                            echo "<BR>Model Request $request->id " . (($request->wasRecentlyCreated) ? " created" : " updated");
                        }

                        $document->created_at = $document->updated_at = $newupdated_at;
                        $document->valid_from = $document->registry_date = $clanarina['rokzanaplatu'];
                        $document->registry_number = $newdocregistry_number;
                        $document->metadata = $newdocmetadata;

                        if ($document->save()) {
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
                    }

                    echo "<BR>FIRST:{$clanarina['rokzanaplatu']} , MEMB: $membership->started_at, REQ: $request->created_at, DOC: $document->registry_number, NEW: $newdocregistry_number NEWMETA: $newdocmetadata";

                    $this->counter++;
                    if ($created) {
                        $this->ok++;
                    } else {
                        $this->error++;
                    }
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
//                if ($this->counter == 100)
//                    dd('stop');
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
        dd($query);
    }

    private function osobeClanarinaMembership()
    {
//dd();
        $query = Osoba::whereHas('clanarine')
            ->where('clan', 1)
            ->whereHas('memberships')
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
//                    $result = $this->copyArray($data);

                    /*                    $this->counter++;
                                        if ($result) {
                                            $this->ok++;
                                        } else {
                                            $this->error++;
                                            break;
                                        }*/
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
        dd($query);
    }

    private function osobaNotClanNotRequest()
    {
//        $osobeNotClan=Osoba::where('clan',0)->get()->count();
//        echo "<br>Osobe koje nisu clanovi: $osobeNotClan"; //9419
//        $osobeNotClanNotRequest=Osoba::where('clan',0)->whereDoesntHave('memberships')->get()->count();
//        echo "<br>Osobe koje nisu clanovi a da nisu u request: $osobeNotClanNotRequest"; //7926
//        $members=Membership::get()->count();
//        echo "<br>Osobe u memberships: $members";


//      zbog update onih 1371 jer se pri kreiranju nije azuriralo ended_at i status_id
        $query = \App\Models\Request::where('request_category_id', 2)
            ->where('note', 'Kreiran automatski za osobu koja je prestala da bude clan (ima clanarinu), ima memebership i ima sve licence sa statusom D.')
            ->orderBy('id')
            ->chunkById(1000, function ($osobe) {
//                foreach ($osobe as $osoba) {
                foreach ($osobe as $req) {
                    $osoba = $req->osoba;


                    /*$query = Osoba::where('clan', 0)
                        ->whereHas('clanarine')
                        ->whereHas('memberships')
                        ->whereHas('requests', function ($q) {
                            $q->where('request_category_id', 2);
                        })
                        ->whereHas('licence')
                        ->orderBy('id')
                        ->chunkById(1000, function ($osobe) {
                            foreach ($osobe as $osoba) {*/


                    $licence = $osoba->licence;
                    $memberships = implode(';', $osoba->memberships->pluck('id')->toArray());
                    $requests = $osoba->requests;
                    $requestsStr = implode(';', $requests->pluck('id')->toArray());
//                    $req = $osoba->requests->where('request_category_id',2)->first();
                    if ($licence->isNotEmpty() and $licence->count() > 0) {
//                        dd($licence->toArray());
                        $licstr = '';
                        $cond = TRUE;
                        foreach ($licence as $licenca) {
                            if ($licenca->status == 'D') {

                                $cond &= TRUE;
                            } else {
                                //                                break;
                                $cond &= FALSE;
                            }

                            $licstr .= " $licenca->id ($licenca->status) | ";
                        }
                        if ($cond) {


//                        var_dump($licence[0]['datumukidanja']);
                            $this->counter++;
                            echo "$this->counter | MEMB: $memberships | REQ: $requestsStr |           $osoba->id | $licstr<br>";
                        }
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
                            "napomena" => str_contains($osoba->napomena, 'Broj rešenja o prestanku članstva') ? $osoba->napomena . "##" . $licence[0]['razlogukidanja'] : $licence[0]['razlogukidanja'],
                        ];
                        $result = $this->updateMCreateRD($data);

                        /*                            if ($result) {
                                                        $this->ok++;
                                                    } else {
                                                        $this->error++;
                                                        break;
                                                    }*/

                    }
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter<br>";
                if ($this->counter == 100) dd('kraj');
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
//        dd($query->count());
        dd($query);
    }

    private function osobaNotClanNapomenaRequest()
    {
//        $osobeNotClan=Osoba::where('clan',0)->get()->count();
//        echo "<br>Osobe koje nisu clanovi: $osobeNotClan"; //9419
//        $osobeNotClanNotRequest=Osoba::where('clan',0)->whereDoesntHave('memberships')->get()->count();
//        echo "<br>Osobe koje nisu clanovi a da nisu u request: $osobeNotClanNotRequest"; //7926
//        $members=Membership::get()->count();
//        echo "<br>Osobe u memberships: $members";
//dd();
        $query = Osoba::where('clan', 0)
            ->where('napomena', 'like', '%Usled neplaćanja članarine%')
            ->whereHas('clanarine')
//            ->whereHas('memberships')
//            ->whereHas('requests')
            ->orderBy('id')
            ->chunkById(1000, function ($osobe) {
                foreach ($osobe as $osoba) {
//                    dd($osoba);
                    $licence = $osoba->licence;
                    $memberships = implode(';', $osoba->memberships->pluck('id')->toArray());
                    $requests = $osoba->requests;
                    $requestsStr = implode(';', $requests->pluck('id')->toArray());
//                    $req = $osoba->requests->where('request_category_id',2)->first();
                    if ($licence->isNotEmpty() and $licence->count() > 0) {
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
                        $this->counter++;
                        echo "$this->counter | MEMB: $memberships | REQ: $requestsStr |           $osoba->id | $licstr<br>";
                        if (!$cond) {
                            continue;
                        } else {
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
                                "napomena" => str_contains($osoba->napomena, 'Broj rešenja o prestanku članstva') ? $osoba->napomena . "##" . $licence[0]['razlogukidanja'] : $licence[0]['razlogukidanja'],
                            ];
//                            $result = $this->updateMCreateRD($data);

                            /*                            if ($result) {
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

    private function osobaClanNapomenaRequest()
    {

        dd("osobaClanNapomenaRequest");
        $query = \App\Models\Request::where('request_category_id', 2)
//                        ->where($request->note, 'Platio')
//                        ->where($request->note == 'Importovan iz stare tabele prijave_clanstvo.')
//                        ->where($request->note == 'Kreiran za osobe iz clanarinaod2006 kojih nema u tabeli memberships.')
//            ->whereHas('memberships')
            ->whereHas('osoba', function ($q) {
                $q->where('clan', 1)
                    ->whereHas('clanarine')
                    ->where('napomena', 'like', '%Usled neplaćanja članarine%');
            })
            ->orderBy('id')
            ->chunkById(1000, function ($requests) {
                foreach ($requests as $request) {
                    $osoba = $request->osoba;
                    $memberships = implode(',', $osoba->memberships->pluck('id')->toArray());
//                    $requests = $osoba->requests;
                    $document = [];
                    $docreqStr = '';

                    $docreqStr .= " <strong>REQ:$request->id</strong>($request->request_category_id),  (DOC:" . implode(',', $request->documents->pluck('document_category_id')->toArray()) . ");";
                    $documents = implode(' # ', $document);
                    $requestsStr = implode(',', $requests->pluck('status_id', 'id')->toArray());
//                    $req = $osoba->requests->where('request_category_id',2)->first();
//                        dd($licence->toArray());
                    preg_match("/Broj rešenja o prestanku članstva\s(.*)\sod.*i broj rešenja o brisanju iz evidencije\s(.*)\sod\s(\d\d\.\d\d\.\d\d\d\d\.)/", $osoba->napomena, $match);
//                    dd($match);
                    $broj_resenja_prestanak = $match[1];
                    $broj_resenja_brisanje = $match[2];
                    $datum_resenja = date('Y-m-d', strtotime($match[3]));
                    $this->counter++;
//                    echo "<BR>$osoba->napomena";
                    echo "$this->counter | $docreqStr | MEMB: $memberships | $osoba->id | broj_resenja_prestanak: $broj_resenja_prestanak | broj_resenja_brisanje: $broj_resenja_brisanje | datum_resenja: $datum_resenja<br>";
                    $data = [
                        "osoba_id" => $osoba->id,
                        "datum_prijema" => $datum_resenja,
                        "request_category_id" => $request->request_category_id,
                        "app_korisnik_id" => 14,
                        "zavodni_broj" => "",
                        "barcode" => NULL,
                        "created_at" => date('Y-m-d H:i:s', strtotime($datum_resenja)),
                        "updated_at" => Carbon::now()->format("Y-m-d H:i:s"),
                        'ended_at' => $datum_resenja,
                        "broj_odluke_uo" => "",
                        "broj_resenja_prestanak" => $broj_resenja_prestanak,
                        "broj_resenja_brisanje" => $broj_resenja_brisanje,
                        "datum_odluke_uo" => $datum_resenja,
                        'status_id_membership' => MEMBERSHIP_ENDED,
                        "status_id" => REQUEST_SUBMITED,
                        "napomena" => str_contains($osoba->napomena, 'Broj rešenja o prestanku članstva') ? $osoba->napomena : "",
                    ];
//                    $result = $this->updateMRCreateD($data);

                    /*                            if ($result) {
                                                    $this->ok++;
                                                } else {
                                                    $this->error++;
                                                    break;
                                                }*/

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

    private function copyZahteviLicenceRequest()
    {
        $query = \App\Models\ZahtevLicenca::whereNotNull('prijem')

//SVI
            /*->whereNotIn('status_id', [41, 43]) //nije zalba ili ponisten
            ->where('note', 'ilike', '%platio%')
            ->whereHas('osoba', function ($q) {
                $q->where('clan', 1);
            })*/
            ->orderBy('id')
            ->chunkById(1000, function ($requests) {
                foreach ($requests as $request) {

                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";

            });
        dd($query);

    }

    private function nereseniAktivni($save = '')
    {
        /*
         * 1. neresene zalbe uplatio
         *
         * 2. preziveli: oni koji su trebali da budu izbrisani a nisu a platili
         *
         * 3. resene zalbe(usvojene), ponisteni, clanovi a nisu uplatili?
         *
         * 4. da li ima zalbi koje su odbijene
         */
        $naslov = TRUE;
        $print = [];
        $query = \App\Models\Request::where('request_category_id', 2)
//POJEDINACNO
//            ->where('status_id', 41)
            ->whereHas('osoba', function ($q) {
                $q
                    ->where('clan', 0)
//                    ->whereHas('poslednjaPlacenaClanarina')
                    ->whereHas('poslednjaPlacenaClanarinaDatumUplate')
//                    ->whereHas('clanarinaDatumAzuriranjaAdmin')
                    ->whereHas('izmirenaClanarina');
            })
//SVI
            /*->whereNotIn('status_id', [41, 43]) //nije zalba ili ponisten
            ->where('note', 'ilike', '%platio%')
            ->whereHas('osoba', function ($q) {
                $q->where('clan', 1);
            })*/
            ->orderBy('id')
            ->chunkById(1000, function ($requests) use ($naslov, &$print) {
                foreach ($requests as $request) {
                    $osoba = $request->osoba;
                    if ($naslov) {
                        echo "<h2>OSOBE ČLAN $osoba->clan, REQ STATUS: {$request->status->naziv}</h2>";
                        $naslov = FALSE;
                    }
                    $clanarine = $osoba->poslednjeDveClanarine->pluck('iznoszanaplatu', 'rokzanaplatu')->toArray();
                    $clanarinPlacena = $osoba->poslednjaPlacenaClanarina->pluck('iznoszanaplatu', 'rokzanaplatu')->toArray();
                    $aktivan = $osoba->izmirenaClanarina;
                    $clanarineStr = http_build_query($clanarine, '', '; ');
                    $clanarinPlacenaStr = http_build_query($clanarinPlacena, '', '; ');
//                    dd($clanarineStr);
                    $allRequests = implode(',', $osoba->requests->pluck('id')->toArray());
                    $memberships = implode(',', $osoba->memberships->pluck('id')->toArray());
//                    $requests = $osoba->requests;

                    $printRow = [];
                    $printRow['count'] = ++$this->counter;
                    $printRow['JMBG'] = $osoba->id;
                    $printRow['član'] = $osoba->clan;
                    $printRow['REQUEST'] = "<strong>$request->id</strong>($request->request_category_id)";
                    $printRow['DOC'] = implode(',', $request->documents->pluck('document_category_id')->toArray());
                    $printRow['Članarine 2'] = $clanarineStr;
                    $printRow['Članarina poslednja placena'] = $clanarinPlacenaStr;
                    $printRow['ALL REQ'] = $allRequests;
                    $printRow['postojeca napomena'] = $osoba->napomena;
                    $printRow['MEMB'] = $memberships;


//                    $reqStr .= " datum uplate: {$osoba->poslednjaPlacenaClanarinaDatumUplate->toArray()[0]['datumuplate']}";

                    $print[] = $printRow;
                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
//                    if ($this->counter == 1000) dd('kraj');
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
        echo $this->outputHtmlTable($print);
//        dd($query->count());
        dd($print);
    }

    private function getExcel($path = 'public/clanstvo_brisanje.xlsx')
    {
        $filename = str_replace("public/", "", $path);
        $file = new UploadedFile(base_path($path), $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', NULL, TRUE);
        if (!is_null($file)) {
//            UNOS LICENCI IZ EXCEL DATOTEKE
            $import = new ExcelImport();
            try {
                $collection = ($import->toCollection($file));
//                dd($collection);
                $imported = $collection[0]//                    ->take(10)
                ;

            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                dd($failures);
            }
        }
        return $imported;
    }

    private function osobaUpisiNapomenuBrisanje($save = '')
    {
        $osobaOK = FALSE;
        $print = [];
        $osobeImport = $this->getExcel('public/clanstvo_brisanje_2021.xlsx');
//        $osobeImport = $this->getExcel();

        $odustaliOdZalbe = [234, 482, 1547, 1615, 2032, 2256, 2282, 2301, 2344, 2846, 2901, 2953, 3118, 3580, 4358, 4632, 4677, 4939, 5430, 5480, 5550, 5607, 5818, 5842, 5863, 5975, 6101, 6241, 6442, 6817, 7549, 7569, 7642, 8125, 8351, 8680, 8701, 8714, 9025, 9073, 9520, 9971];

        foreach ($osobeImport->toArray() as $key => $value) {
            $osobeBrisanje[$value['jmbg']] = $value;
            $osobeBrisanjeIds[] = $value['jmbg'];
        }
//        dd($osobeBrisanje);

//                dd($osobeBrisanje->pluck('jmbg')->toArray());

        $osobeDuplicate = DB::table('requests')
            ->where('request_category_id', 2)
            ->groupBy('osoba_id')
            ->having(DB::raw('count(osoba_id)'), 2)
            ->pluck('osoba_id');
//        dd($osobeDuplicate);
        $this->counter = 0;
//        echo "ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO CLAN 1<BR>";
//        echo "ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO CLAN 0<BR>";

        $query = \App\Models\Request::
        where('request_category_id', 2)

//            ->distinct('osoba_id')
            ->whereNotIn('status_id', [ZALBA, REQUEST_BOARD])
//            ->whereIn('id', $odustaliOdZalbe) //ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO
//            ->where('status_id','<>', 41)   // ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO
            ->whereHas('osoba', function ($q) use ($osobeDuplicate, $osobeBrisanjeIds) {
                $q
//                    ->where('clan', 0)      // ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO CLAN 0
//                    ->where('clan', 1)    //ODUSTALI OD ZALBE SA STATUSOM ZALBA_ODUSTAO CLAN 1
                    ->where('clan', 10)    //Priprema se za brisanje
//                    ->where('napomena', 'ILIKE', 'Usled neplaćanja članarine')
//                    ->where('napomena', 'ILIKE', '%Usled neplaćanja članarine.')
//                    ->whereNotIn('osoba_id', $osobeDuplicate)
//                    ->whereIn('osoba_id', $osobeBrisanjeIds)
                    /*->whereIn('id', [
                        "0810964710092"
                    ])*/
                ;
            })
            /*$query = \App\Models\osoba::where('napomena', 'ILIKE', 'Usled neplaćanja članarine')
                ->whereNotIn('id', $osobeDuplicate)
                ->whereHas('requests', function ($q) {
                    $q->where('request_category_id', 2)
    //            ->where('status_id', REQUEST_IN_PROGRESS)
                    ;
                })*/
            ->orderBy('id')
            ->chunkById(1000, function ($requests) use ($osobeBrisanje, &$print, $save) {
//                foreach ($osobe as $osoba) {
                $jmbgs = '';
                foreach ($requests as $request) {
                    $osoba = $request->osoba;
                    $requests = $osoba->requests->where('request_category_id', 1);
                    $requestsStr = implode(',', $requests->pluck('status_id')->toArray());
                    $allRequests = implode(',', $osoba->requests->pluck('id')->toArray());
                    $memberships = implode(',', $osoba->memberships->pluck('id')->toArray());
                    $printRow = [];
                    $napomena = '';
                    if (array_key_exists($osoba->id, $osobeBrisanje)) {
//                        dd($osobeBrisanje);
                        $osobaExcel = $osobeBrisanje[$osoba->id];
                        if ($osobaExcel['raditi_resenje'] <> 2) {
                            $napomena = "Broj rešenja o prestanku članstva 02-6/2022-{$osobaExcel['r_br']} od {$osobaExcel['datum_resenja']} i broj rešenja o brisanju iz evidencije 02-7/2022-{$osobaExcel['r_br']} od {$osobaExcel['datum_resenja']}";
                            if (!empty($osoba->napomena)) {
                                if ($osoba->napomena == "Usled neplaćanja članarine.") {
                                    $osoba->napomena = "$napomena $osoba->napomena.";
                                } else if (strstr($osoba->napomena, "Usled neplaćanja članarine")) {
                                    if (strstr($osoba->napomena, $napomena)) {
//                                        dd($napomena);
                                        //nema promene
                                    } else {
                                        $osoba->napomena = "$napomena $osoba->napomena.";
                                    }
                                } else {
                                    $osoba->napomena = $napomena . "Usled neplaćanja članarine.##" . $osoba->napomena;
                                }
                            } else {
                                $osoba->napomena = "$napomena Usled neplaćanja članarine.";
                            }
                        }
                        $jmbgs .= "'$osoba->id', ";

                        $printRow['count'] = ++$this->counter;
                        $printRow['JMBG'] = $osoba->id;
                        $printRow['član'] = $osoba->clan;
                        $printRow['REQUEST'] = "<strong>$request->id</strong>($request->request_category_id)";
                        $printRow['DOC'] = implode(',', $request->documents->pluck('document_category_id')->toArray());
                        $printRow['ALLREQ'] = $allRequests;
                        $printRow['napomena'] = $osoba->napomena;
                        $printRow['MEMB'] = $memberships;
                        $printRow['Status'] = "{$request->status->naziv}, $request->updated_at";

                        if ($save == 'save') {
                            if ($osoba->save()) {
                                $osobaOK = TRUE;
                            }
                            $printRow['Osoba SAVED'] = $osobaOK;
                        }
                    }
                    $print[] = $printRow;
                }
                echo "<br>$jmbgs<br>";
            })
//        limit(2)
//            ->get()
//            ->pluck('osoba_id')
//            ->toArray()
//            ->toSql()
        ;
        echo $this->outputHtmlTable($print);

        dd($this->counter);
//        dd($query->count());
//        dd($query);

    }

    public function osobeZaBrisanjeClanstvo()
    {
        $print = [];
        $this->counter = 1;
        $start = microtime(TRUE);

        $query = Osoba::where('clan', 1)
            ->whereNotIn('id', ['0909946710340']) // podneo je zahtev za prestanak clanstva, pa ga izbacujem da mu ne bismo poslali obavestenje
            ->distinct('id')
            ->whereHas('dugujeClanarinuZa2021')
            ->whereDoesntHave('requests', function ($q) {
                $q
                    ->where('request_category_id', 4) // mirovanje
                    ->orWhere('status_id', 41) // zalba
                    ->orWhere('note', 'Žalba kod drugostepenog organa (MGSI)');
            })
            ->whereHas('licence', function ($q) {
                $q->where('status', '<>', 'D');
//                $q->where('status', '=', 'D');
            })
            ->chunkById(1000, function ($osobe) use (&$print) {
                $ids = '';
                foreach ($osobe as $osoba) {
                    $condition = TRUE;

//                    NISU PREUZELI NI JEDNU LICENCU
//                    start
                    /*foreach ($osoba->licence as $licenca) {
                        if ($licenca->status <> 'D') {
                            $condition &= FALSE;
                            break;
                        }
                    }*/
//                    end
//dd($osoba->poslednjeDveClanarine->toArray());
                    $clanarine = $osoba->poslednjeDveClanarine->toArray();
                    /*foreach ($clanarine as $clanarina) {
                        if ($clanarina['iznosuplate'] == "0.00") {
                            $condition = FALSE;
                        }
                    }*/

//                    $clanarine = $osoba->poslednjeDveClanarine->pluck('iznoszanaplatu', 'rokzanaplatu')->toArray();
                    $clanarineStr = http_build_query($clanarine, '', '; ');
//                    dd($clanarineStr);
                    $clanarineStr = str_replace(["0%5B", "1%5B", "%5D"], "", $clanarineStr);


                    if ($condition) {
                        $ids .= "'$osoba->id', ";
//                        $ids .= "$osoba->id<br>";
                        $row['counter'] = $this->counter++;
                        $row['osoba'] = $osoba->id;
                        $row['clanarine'] = $clanarineStr;
                        $row['licence'] = $osoba->licence_statusi;
                        $print[] = $row;
                    }

                }
                echo "$ids<br><br>";
                echo "<br>Chunk: $this->counter, " . $this->convert(memory_get_usage(TRUE)) . "<br>";

//                dd('stop');
            });

        echo $this->outputHtmlTable($print);

        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        dd($query);
    }


    private function prekiniClanstvo($save = '')
    {
        $errorStr = '';
        echo "<h2>prekiniClanstvo ($save)</h2>";
        $start = microtime(TRUE);

        $query = \App\Models\Request::where('request_category_id', 2)
            ->where('note', 'Neizmirena clanarina 2021')
//            ->where('status_id', '<>', REQUEST_FINISHED)
//            ->where('id', 5226)
//POJEDINACNO

            /*->whereDoesntHave('osoba', function ($q) {
//            ->whereHas('osoba', function ($q) {
                $q->whereIn('id', [
                    '2511965710258'
                ]);
            })*/
//SVI
//            ->whereIn('status_id', [PONISTEN])
            ->whereIn('status_id', [ZALBA, REQUEST_BOARD, PONISTEN])
//            ->where('note', 'ilike', '%platio%')
//            ->whereDate('updated_at', '<', '2022-03-16 00:00:00')
            /*            ->whereHas('osoba', function ($q) {
                            $q->where('clan', 10);
            //                $q->whereIn('id', [
            //                    '1306976370010'
            //                ]);
                        })*/
            ->orderBy('id')
            ->chunkById(1000, function ($requests) use ($save, &$errorStr) {
                foreach ($requests as $request) {
                    $osoba = $request->osoba;
                    $memberships = implode(',', $osoba->memberships->pluck('status_id', 'id')->toArray());
//                    $requests = $osoba->requests;
                    $document = [];
                    $docreqStr = '';

                    $docreqStr .= " <strong>REQ:$request->id</strong>($request->request_category_id),  (DOC:" . implode(',', $request->documents->pluck('document_category_id')->toArray()) . ");";
                    $documents = implode(' # ', $document);
                    $requestsStr = implode(',', $requests->pluck('status_id', 'id')->toArray());
//                    $req = $osoba->requests->where('request_category_id',2)->first();
//                        dd($licence->toArray());

                    preg_match("/Broj rešenja o prestanku članstva\s(.*)\sod.*i broj rešenja o brisanju iz evidencije\s(.*)\sod\s(\d\d\.\d\d\.\d\d\d\d\.)/", $osoba->napomena, $match);
                    $this->counter++;
                    if (!empty($match)) {
//                        samo za one koji imaju napomenu sa brojem resenje
//                        dd($request);

//                    dd($match);
                        $broj_resenja_prestanak = $match[1];
                        $broj_resenja_brisanje = $match[2];
                        $datum_resenja = date('Y-m-d', strtotime($match[3]));
                        $datum_resenjaPlusOneMonth = Carbon::parse($datum_resenja)->addMonth()->format("Y-m-d");
                        $datum_resenja = Carbon::parse($datum_resenja)->format("Y-m-d");
                        $this->ok++;
//                    echo "<BR>$osoba->napomena";

                        echo "$this->counter | $docreqStr | MEMB: $memberships | $osoba->id | broj_resenja_prestanak: $broj_resenja_prestanak | broj_resenja_brisanje: $broj_resenja_brisanje | datum_resenja: $datum_resenja | $request->note<br>";
                        $data = [
                            "osoba_id" => $osoba->id,
                            "datum_prijema" => $datum_resenja,
                            "request_category_id" => $request->request_category_id,
                            "app_korisnik_id" => 14,
                            "zavodni_broj" => "",
                            "barcode" => NULL,
                            "created_at" => date('Y-m-d H:i:s', strtotime($datum_resenja)),
                            "updated_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            'ended_at' => $datum_resenjaPlusOneMonth,
                            "broj_odluke_uo" => "",
                            "broj_resenja_prestanak" => $broj_resenja_prestanak,
                            "broj_resenja_brisanje" => $broj_resenja_brisanje,
                            "datum_odluke_uo" => $datum_resenja,
                            'status_id_membership' => MEMBERSHIP_ENDED,
                            "status_id" => REQUEST_FINISHED,
                            "napomena" => str_contains($osoba->napomena, 'Broj rešenja o prestanku članstva') ? $osoba->napomena : "",
                        ];
                        if ($save == 'save') {
                            $result = $this->updateMRCreateD($data);
                        }

                    } else {
                        echo "$this->counter | $docreqStr | MEMB: $memberships | $osoba->id | $request->note<br>";
                        $this->error++;
                        $errorStr .= "'$osoba->id',";
                    }
                }
                echo "<br>Chunk: $this->counter, " . $this->convert(memory_get_usage(FALSE));
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter<br>";
            })
//        limit(2)
//            ->pluck('osoba')->toArray()
//            ->get()
        ;
        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br><br>Greska, nisu azurirani: $errorStr<br>";
//        dd($query->count());
        dd($query);
    }


    private function osobaClanRequestResenje()
    {
        $query = \App\Models\Request::where('request_category_id', 2)
            ->whereNotIn('status_id', [41, 43]) //nije zalba ili ponisten
            ->where('note', 'ilike', '%platio%')
            ->whereHas('osoba', function ($q) {
                $q->where('clan', 1); //uradjeno
            })
            ->orderBy('id')
            ->chunkById(1000, function ($requests) {
                foreach ($requests as $request) {
                    $osoba = $request->osoba;

                    if (!str_contains($osoba->napomena, 'Usled neplaćanja članarine')) {
                        try {
                            $osoba->clan = 0;
                            $osoba->napomena = empty($osoba->napomena) ? "Usled neplaćanja članarine" : $osoba->napomena . "##Usled neplaćanja članarine";
//                            $osoba->save();

                        } catch (\Exception $e) {
                            echo "<br>$osoba->id, " . $e->getMessage();
                            $errorInfo = $e->getMessage();
                        }
                        $this->counter++;
                        echo "$this->counter | REQ: $request->id | $osoba->id | $osoba->clan | $osoba->napomena<br>";
                    }

                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter";
//                    if ($this->counter == 1000) dd('kraj');
            });
        dd($query);
    }

    private function osobaObrisanaAktivnaRequest()
    {
        $query = \App\Models\Request::where('request_category_id', 2)
            ->whereNotIn('status_id', [41, 43]) //nije zalba ili ponisten
            ->where('note', 'ilike', '%platio%')
            ->whereHas('osoba', function ($q) {
                $q
//->where('clan', 0)
                    /*->whereHas('licence', function ($q) {
                        $q->where('status', 'A');
                    })*/
                    /*->whereHas('clanarine', function ($q) {
                        $q->where('napomena', 'ilike', '%ponovo%');
                    })*/
                ;
            })
            ->orderBy('id')
            ->chunkById(1000, function ($requests) {
                foreach ($requests as $request) {
                    $osoba = $request->osoba;
                    $prvaClanarina = $osoba->prvaClanarina()->get()->toArray()[0];
                    $poslednjaClanarina = $osoba->poslednjaClanarina()->get()->toArray()[0];
//dd($prvaClanarina);
//                    if (!str_contains($osoba->napomena, 'Usled neplaćanja članarine')) {
                    try {
//                            $osoba->clan = 0;
//                            $osoba->napomena = empty($osoba->napomena) ? "Usled neplaćanja članarine" : $osoba->napomena . "##Usled neplaćanja članarine";
//                            $osoba->save();

                    } catch (\Exception $e) {
                        echo "<br>$osoba->id, " . $e->getMessage();
                        $errorInfo = $e->getMessage();
                    }

                    $prvaPart = substr($prvaClanarina['rokzanaplatu'], 5, 5);
                    $poslednjaPart = substr($poslednjaClanarina['rokzanaplatu'], 5, 5);

                    if ($prvaPart != $poslednjaPart) {
                        $this->counter++;

                        echo "$this->counter | REQ: $request->id | $osoba->id | $osoba->clan | $osoba->napomena | {$prvaClanarina['rokzanaplatu']}| {$poslednjaClanarina['rokzanaplatu']}<br>";
                    }
                }

//                }
                echo "<br>Kreirano: $this->ok od $this->counter";
                echo "<br>Nije kreirano: $this->error od $this->counter<br>";
//                    if ($this->counter == 1000) dd('kraj');
            });
        dd($query);
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
            'request_category_id' => 2,
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
//            'started_at' => 'datum_odluke_uo',
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
//            osobaNotClanNapomenaRequest
            $request = \App\Models\Request::where('osoba_id', $data['osoba_id'])->where('request_category_id', 2)->latest()->first();
            foreach ($requestsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'request_category_id') {
                    $requestData[$newKey] = $oldKey;
                } else if ($newKey == 'status_id') {
                    $requestData[$newKey] = array_search($oldData['status_id'], $oldKey);
                } else if ($newKey == 'note') {
                    $requestData[$newKey] = /*!empty($request->note) ? "$request->note##Kreiran automatski za osobu koja je prestala da bude član (ima članarinu) i ima memebership." :*/
                        "Kreiran automatski za osobu koja je prestala da bude član (ima članarinu) i ima memebership.";
                } else {
                    $requestData[$newKey] = $oldData[$oldKey];
                }
            }
            $request->fill($requestData);

            echo "<BR>";
            echo "<BR>REQUEST $request->id";
            echo "<PRE>";
            print_r($request->toArray());
            echo "</PRE>";
            /*echo "<BR>REQUEST UPDATE $request->id";
            echo "<PRE>";
            print_r($requestData);
            echo "</PRE>";*/

//            osobaNotClanNotRequest
//            $request = \App\Models\Request::firstOrNew($requestData);

            /*if ($request->save()) {
                $request->requestable()->associate($membership);
                $request->save();
                $requestOK = TRUE;
                echo "<BR>Model Request $request->id " . (($request->wasRecentlyCreated) ? " created" : " updated");
            }*/

//      DOCUMENTS KAD JE ZAHTEV
            $documentMapPrijaveClanstvo['zahtev_za_brisanje_iz_clanstva'] = [
//      DOCUMENT KAD JE ZAHTEV
                'document_category_id' => 2,
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
                'document_category_id' => 12,
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
                'document_category_id' => 13,
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
            foreach ($documentMapPrijaveClanstvo as $key => $documentMap) {
                $zg = $request->osoba->zvanjeId->zvanje_grupa_id;
                $reg = Registry::where('base_number', 0)->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                    $q->where('label', "02-$zg");
                })->get()[0];
                $reg->counter++;
//                $reg->save();
                foreach ($documentMap as $newKey => $oldKey) {
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
                        $documentOdlukaData[$newKey] = "Automatski kreiran na osnovu zahteva retroaktivno.";
                    } else {
                        $documentOdlukaData[$newKey] = $oldData[$oldKey];
                    }
                }
//                $document = Document::where();
                $document = Document::firstOrNew($documentOdlukaData);
                echo "<BR>";
                echo "<BR>DOCUMENT id: $document->id" . strtoupper($key);
                echo "<PRE>";
                print_r($document->toArray());
                echo "</PRE>";
                /*echo "<BR>DOCUMENT UPDATE " . strtoupper($key);
                echo "<PRE>";
                print_r($documentOdlukaData);
                echo "</PRE>";*/

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
//                    $empty=isEmpty($oldData[$oldKey]);
                    $membershipData[$newKey] = !empty($membership->note) ? (!empty($oldData[$oldKey]) ? $membership->note . "##ended_at:" . $oldData[$oldKey] : $membership->note) : "ended_at:" . $oldData[$oldKey];
                } else {
                    $membershipData[$newKey] = $oldData[$oldKey];
                }
            }
            echo "<BR>";
//            var_dump($empty);
            echo "<BR>MEMBERSHIP $membership->id";
            echo "<PRE>";
            print_r($membership->toArray());
            echo "</PRE>";
            /*echo "<PRE>";
            print_r($membershipData);
            echo "</PRE>";*/

            $membership->fill($membershipData);
            /*if ($membership->save()) {
                $membershipOK = TRUE;
                echo "<BR>Model Membership $membership->id " . (($membership->wasRecentlyCreated) ? " created" : " updated");
            }*/

            if ($membershipOK && $requestOK) {
//            if ($membershipOK && $requestOK && $documentOK) {
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

    private function updateZlCreateD(array $data)
    {
        //1. update zahtevLicenca
        //2. create documents
        DB::beginTransaction();

        $created = $zahtevLicencaOK = $documentOK = $registryOK = FALSE;

        $zahtevLicencaMap = [
//  ZahtevLicenca => $data
            'status' => 'status_id',
            'updated_at' => 'updated_at',
        ];

        /*echo "<BR>";
        echo "<BR>OLD DATA";
        echo "<PRE>";
        print_r($data);
        echo "</PRE>";*/

//        try {

        $zahtevLicenca = \App\Models\SiPrijava::find($data['id']);
//        ZAHTEV LICENCA

        /*echo "<BR>";
        echo "<BR>ZahtevLicenca id: $zahtevLicenca->id, status: $zahtevLicenca->status" . strtoupper($key??'');
        echo "<PRE>";
        print_r($zahtevLicenca->toArray());
        echo "</PRE>";*/

//            $licenca = $zahtevLicenca->licenca;
//        dd($licenca);
        /*if (!empty($licenca)) {
            // ima licencu izdatu po ovom zahtevu
            if ($zahtevLicenca->status <> REQUEST_FINISHED) $zahtevLicenca->status == REQUEST_FINISHED;
        } else {
            // nema licencu izdatu po ovom zahtevu
            // da li ima licencu sa tipom licence iz ovog zahteva ?
            $licenca = Licenca::where('osoba', $osoba->id)
                ->where('licencatip', $zahtevLicenca->licencatip)
                ->whereNull('zahtev')
                ->get();

            if (!empty($licenca)) {
                if ($licenca->count() != 1) {
                    // TODO: problem!!!
                } else {
                    $licenca = $licenca->first();
                    $licenca->zahtev = $zahtevLicenca->id;
                    if ($zahtevLicenca->status <> REQUEST_FINISHED) $zahtevLicenca->status = REQUEST_FINISHED;
                    $licenca->save();

                }
            }
            if ($zahtevLicenca->status == REQUEST_FINISHED) {
                // kako je zavrsen a nema licencu ???
                // TODO: problem!!!
            }
//            dd($osoba->licence[0]->tipLicence);

        }
        if ($zahtevLicenca->save()) {
            $zahtevLicencaOK = TRUE;
            echo "<BR>Model ZahtevLicenca $zahtevLicenca->id: " . (($zahtevLicenca->wasRecentlyCreated) ? " created" : " updated");
        }*/

//        dd('stop');

//      DOCUMENTS
        /*foreach ($zahtevLicencaMap as $newKey => $oldKey) {
            if ($newKey == 'status') {
                $newData[$newKey] = $data[$oldKey][$zahtevLicenca->status];
            } else {
                $requestData[$newKey] = $data[$oldKey];
            }
        }*/

        $documentMapZahtevLicenca = [
            'document_category_id' => 10,    // prijava za si
            'document_type_id' => 1,         // original
            'registry_id' => 104,            // prijave za polaganje si
            'registry_number' => $data['zavodni_broj'],
            'registry_date' => $data['prijem'],
            'barcode' => 'barcode',
            'status_id' => $data['status_id'][$zahtevLicenca->status_prijave],
            'user_id' => 'app_korisnik_id',
            'metadata' => [
                "title" => "Prijava za polaganje stručnog ispita #$zahtevLicenca->id",
                "author" => $zahtevLicenca->osoba->ime_roditelj_prezime,
                "author_id" => $zahtevLicenca->osoba->lib,
                "description" => "",
                "category" => "Prijava za polaganje stručnog ispita",
                "created_at" => Carbon::parse($data['created_at'])->format('Y-m-d'),
            ],
            'note' => 'napomena',
//            'documentable_id' => '',  // request.id
//            'documentable_type' => '',    //\App\Models\Membership
            'created_at' => 'prijem',
            'updated_at' => 'prijem',
            'valid_from' => 'prijem',
        ];
//            dd($documentMapZahtevLicenca);
//            dd($data);

        foreach ($documentMapZahtevLicenca as $newKey => $oldKey) {
//            dd($newKey, $oldKey);
            // modifikacija vrednosti
            if ($newKey == 'document_category_id' or $newKey == 'document_type_id' or $newKey == 'registry_id' or $newKey == 'path') {
                $documentOdlukaData[$newKey] = $oldKey;
            } else if ($newKey == 'status_id') {
                $documentOdlukaData[$newKey] = $oldKey;
            } else if ($newKey == 'registry_date') {
                $documentOdlukaData[$newKey] = $oldKey;
            } else if ($newKey == 'registry_number') {
                $documentOdlukaData[$newKey] = $oldKey;
            } else if ($newKey == 'metadata') {
                $documentOdlukaData[$newKey] = json_encode($oldKey, JSON_UNESCAPED_UNICODE);
            } else if ($newKey == 'note') {
                $documentOdlukaData[$newKey] = "Automatski kreiran na osnovu prijave retroaktivno.";
            } else {
                $documentOdlukaData[$newKey] = $data[$oldKey];
            }
        }

        $document = Document::firstOrNew($documentOdlukaData);

//        echo "<BR>";
//        echo "<BR>DOCUMENT id: $document->id" . strtoupper($key ?? '');
//        echo "<PRE>";
//        print_r($document->toArray());
//        echo "</PRE>";
//        echo "<BR>DOCUMENT UPDATE " . strtoupper($key ?? '');
//            echo "<PRE>";
//            print_r($documentOdlukaData);
//            echo "</PRE>";

        if ($document->save()) {
            $document->documentable()->associate($zahtevLicenca);

            if ($zahtevLicenca->status_prijave < REQUEST_IN_PROGRESS) {
                $zahtevLicenca->status_prijave = REQUEST_IN_PROGRESS;
            }

            if (empty($zahtevLicenca->barcode)) {
                $zahtevLicenca->barcode = "$zahtevLicenca->id#$document->id#{$data['prijem']}";
            }
            $document->barcode = $zahtevLicenca->barcode;

            if ($zahtevLicenca->save()) {
                $zahtevLicencaOK = TRUE;
            }
            $document->save();
            $documentOK = TRUE;
            echo "<BR>Model Document $document->id " . (($document->wasRecentlyCreated) ? " created" : " updated");
        }

        $registry = Registry::find(104);
        $registry->counter++;
        if ($registry->save()) {
            $registryOK = TRUE;
        }

        if ($zahtevLicencaOK && $documentOK && $registryOK) {
            DB::commit();
            $created = TRUE;
        } else {
            DB::rollBack();
            echo "DB ROLLBACK";
            $created = FALSE;
        }

        /*} catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            echo "Exception ! <br>DB ROLLBACK<br>" . $e->getMessage();
            $errorInfo = $e->getMessage();
        }*/
//        dd('kraj');

        return $created;
    }

    private function updateMRCreateD(array $data)
    {
        //1. create requests
        //2. create documents
        //3. update membership

        DB::beginTransaction();

        $created = FALSE;

        $membershipOK = FALSE;
        $requestOK = FALSE;
        $documentOK = FALSE;
        $osobaOK = FALSE;

        $membershipData = []; //reset array


        $requestsMap = [
//  REQUEST => $data
            'osoba_id' => 'osoba_id',
            'request_category_id' => 'request_category_id',
            'status_id' => 'status_id',
            'note' => 'napomena',
//            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];
        $membershipsMapPrijaveClanstvo = [
//  MEMBERSHIP => $data
            'osoba_id' => 'osoba_id',
//            'started_at' => 'datum_odluke_uo',
            'ended_at' => 'ended_at',
            'updated_at' => 'updated_at',
            'note' => 'napomena',
            'status_id' => 'status_id_membership',
        ];

        $oldData = $data;

        /*echo "<BR>";
        echo "<BR>OLD DATA";
        echo "<PRE>";
        print_r($oldData);
        echo "</PRE>";*/
        try {
//OBAVEZNO VRATITI NA STARTED
            $membership = Membership::where('osoba_id', $data['osoba_id'])->where('status_id', MEMBERSHIP_ENDED)->latest()->first();
//            $membership = Membership::where('osoba_id', $data['osoba_id'])->where('status_id', MEMBERSHIP_STARTED)->latest()->first();
            $osoba = $membership->osoba;
//        REQUESTS
//            osobaNotClanNapomenaRequest
            $request = \App\Models\Request::where('osoba_id', $data['osoba_id'])->where('request_category_id', 2)->latest()->first();
            foreach ($requestsMap as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'note') {
                    $requestData[$newKey] = /*!empty($request->note) ? "$request->note##Kreiran automatski za osobu koja je prestala da bude član (ima članarinu) i ima memebership." :*/
                        "Kreiran automatski za osobu koja je prestala da bude član (ima članarinu) i ima memebership.";
                } else {
                    $requestData[$newKey] = $oldData[$oldKey];
                }
            }
            $request->fill($requestData);

            echo "<BR>";
            /*echo "<BR>REQUEST $request->id";
            echo "<PRE>";
            print_r($request->toArray());
            echo "</PRE>";*/
            echo "<BR>REQUEST UPDATE $request->id";
            echo "<PRE>";
            print_r($requestData);
            echo "</PRE>";

            if ($request->save()) {
                $request->requestable()->associate($membership);
                $request->save();
                $requestOK = TRUE;
                echo "<BR>Model Request $request->id " . (($request->wasRecentlyCreated) ? " created" : " updated");
            }

//      DOCUMENTS
//      DOCUMENTS KAD JE ZAHTEV
            $documentMapPrijaveClanstvo['zahtev_za_brisanje_iz_clanstva'] = [
                'document_category_id' => 2,
                'document_type_id' => 4,
                'registry_id' => 1,
                'registry_number' => '',
                'registry_date' => 'datum_prijema',
                'status_id' => DOCUMENT_REGISTERED,
                'barcode' => 'barcode',
                'user_id' => 'app_korisnik_id',
                'metadata' => [
                    "title" => "Zahtev za brisanje iz članstva u IKS #$request->id",
                    "author" => 'Inženjerska komora Srbije',
                    "author_id" => '',
                    "description" => "Za osobu: " . $request->osoba->ime_roditelj_prezime . ", id: " . $request->osoba->lib,
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
                'document_category_id' => 12,
                'document_type_id' => 1,
                'registry_id' => 27,
                'registry_number' => 'broj_resenja_prestanak',
                'registry_date' => 'datum_odluke_uo',

                'status_id' => DOCUMENT_REGISTERED,
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
                'created_at' => 'datum_odluke_uo',
                'updated_at' => 'updated_at',
                'valid_from' => 'ended_at',

            ];
//      DOCUMENT KAD JE RESENJE O BRISANJU IZ EVIDENCIJE
            $documentMapPrijaveClanstvo['Resenje_o_brisanju_iz_evidencije'] = [
                'document_category_id' => 13,
                'document_type_id' => 1,
                'registry_id' => 28,
                'registry_number' => 'broj_resenja_brisanje',
                'registry_date' => 'datum_odluke_uo',

                'status_id' => DOCUMENT_REGISTERED,
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
                'created_at' => 'datum_odluke_uo',
                'updated_at' => 'updated_at',
                'valid_from' => 'ended_at',

            ];
            foreach ($documentMapPrijaveClanstvo as $key => $documentMap) {
                $zg = $request->osoba->zvanjeId->zvanje_grupa_id;
                if ($key == 'zahtev_za_brisanje_iz_clanstva') {
                    $reg = Registry::where('status_id', AKTIVAN)
                        ->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                            $q->where('label', "02-$zg");
                        })
                        ->whereHas('requestCategories', function ($q) use ($request, $documentMap, $oldData) {
                            $q->where('registry_request_category.request_category_id', $oldData['request_category_id'])
                                ->where('registry_request_category.document_category_id', $documentMap['document_category_id']);
                        })
                        ->first();
                } else {
                    $reg = Registry::where('status_id', AKTIVAN)
                        ->whereHas('requestCategories', function ($q) use ($request, $documentMap, $oldData) {
                            $q->where('registry_request_category.request_category_id', $oldData['request_category_id'])
                                ->where('registry_request_category.document_category_id', $documentMap['document_category_id']);
                        })
                        ->first();
                }
                $reg->counter++;
                $reg->save();
                foreach ($documentMap as $newKey => $oldKey) {
                    //modifikacija vrednosti
                    if ($newKey == 'document_category_id' or $newKey == 'document_type_id' or $newKey == 'path') {
                        $documentOdlukaData[$newKey] = $oldKey;
                    } else if ($newKey == 'status_id') {
                        $documentOdlukaData[$newKey] = $oldKey;
                    } else if ($newKey == 'registry_id') {
                        $documentOdlukaData[$newKey] = $reg->id;
                    } else if ($newKey == 'registry_number') {
                        if ($key == 'zahtev_za_brisanje_iz_clanstva') {
                            $documentOdlukaData[$newKey] = "{$reg->registryDepartmentUnit->label}-$reg->base_number/2022-$reg->counter";
                        } else {
                            $documentOdlukaData[$newKey] = $oldData[$oldKey];
                        }
                    } else if ($newKey == 'metadata') {
                        $documentOdlukaData[$newKey] = json_encode($oldKey, JSON_UNESCAPED_UNICODE);
                    } else if ($newKey == 'note') {
                        $documentOdlukaData[$newKey] = "Automatski kreiran na osnovu zahteva retroaktivno.";
                    } else {
                        $documentOdlukaData[$newKey] = $oldData[$oldKey];
                    }
                }
//                $document = Document::where();
                $document = Document::firstOrNew($documentOdlukaData);
                /*echo "<BR>";
                echo "<BR>DOCUMENT id: $document->id" . strtoupper($key);
                echo "<PRE>";
                print_r($document->toArray());
                echo "</PRE>";*/
                echo "<BR>DOCUMENT UPDATE " . strtoupper($key);
                echo "<PRE>";
                print_r($documentOdlukaData);
                echo "</PRE>";

                if ($document->save()) {
                    $document->documentable()->associate($request);
                    $document->save();
                    $documentOK = TRUE;
                    echo "<BR>Model Document $document->id " . (($document->wasRecentlyCreated) ? " created" : " updated");
                }
            }
//        MEMBERSHIPS
            foreach ($membershipsMapPrijaveClanstvo as $newKey => $oldKey) {
                //modifikacija vrednosti
                if ($newKey == 'note') {
//                    $empty=isEmpty($oldData[$oldKey]);
                    $membershipData[$newKey] = !empty($membership->note) ? (!empty($oldData[$oldKey]) ? $membership->note . "##ended_at:" . $oldData[$oldKey] : $membership->note) : "ended_at:" . $oldData[$oldKey];
                } else {
                    $membershipData[$newKey] = $oldData[$oldKey];
                }
            }
            echo "<BR>";
            echo "<BR>MEMBERSHIP $membership->id";
            echo "<PRE>";
//            print_r($membership->toArray());
            echo "</PRE>";
            echo "<PRE>";
            print_r($membershipData);
            echo "</PRE>";

            $membership->fill($membershipData);
            if ($membership->save()) {
                $membershipOK = TRUE;
                echo "<BR>Model Membership $membership->id " . (($membership->wasRecentlyCreated) ? " created" : " updated");
            }

            $osoba->clan = 0;
            $osoba->updated_at = Carbon::now()->format("Y-m-d H:i:s");
            echo "<BR>";
            echo "<BR>OSOBA $osoba->id";
            echo "<PRE>";
//            print_r($osoba->toArray());
            echo "</PRE>";


            if ($osoba->save()) {
                $osobaOK = TRUE;
                echo "<BR>Model Osoba $osoba->id " . (($membership->wasRecentlyCreated) ? " created" : " updated");
            }

            if ($membershipOK && $requestOK && $documentOK && $osobaOK) {
                DB::commit();
                $created = TRUE;
            } else {
                DB::rollBack();
                echo "DB ROLLBACK";
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

    private function createD(array $data): array
    {

        $to_create = FALSE;
        $to_register = FALSE;
        $to_associate = FALSE;
        $result = [];

        $request = \App\Models\Request::where('id', $data['zahtev'])->where('request_category_id', 4)->first(); // request_category_id = 4 Zahtevi za mirovanje clanstva
        if (empty($request)) {
            $result[$data['zahtev']]['status'] = "problem";
            $result[$data['zahtev']]['message'] = "There is no request with number {$data['zahtev']} in database";
            return $result;
        }
        $document = $request->documents->where('document_category_id', 3); // document_category_id -> zahtev za mirovanje clanstva
//        dd($document);
        if ($document->count() > 1) {
            // ima vise dokumenata kategorije zahtev za mirovanje clanstva po ovom zahtevu
            $result[$data['zahtev']]['status'] = "problem";
            $result[$data['zahtev']]['message'] = "Request $request->id has more than one document for category \"Zahtev za mirovanje clanstva\"";
            return $result;
        }
        if ($document->count() == 1) {
            $document = $document->first();
            // vec ima dokument kategorije zahtev za mirovanje clanstva po ovom zahtevu
            if (empty($document->registry_number) or empty($document->registry_date) or empty($document->valid_from) or empty($document->documentable_id)) {
                $result[$data['zahtev']]['action'] = "Update";
                // azurirati dokument
                if (empty($document->registry_number) or empty($document->registry_date) or empty($document->valid_from)) {
                    // zavedi
                    $to_register = TRUE;
                }
                if (empty($document->documentable_id)) {
                    // asociraj
                    $to_associate = TRUE;
                }
            }
        } else {
            // kreiraj novi dokument
            $to_create = TRUE;
            $to_associate = TRUE;
        }
//        dd($result);
//        dd($data);

        try {

            $zg = $request->osoba->zvanjeId->zvanje_grupa_id;
            $registry = Registry::where('base_number', 0)
                ->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                    $q->where('label', "02-$zg");
                })->get()[0];

            if ($to_create) {
                $document = new Document();

                $document->document_category_id = 3; // document_category_id -> zahtev za mirovanje clanstva
                $document->registry_id = $registry->id;
                $document->registry_number = $data['zavodni_broj_zahteva'];
                $document->status_id = 57; // zaveden
                $document->registry_date = Carbon::parse($data['datum_prijema_zahteva'])->format('Y-m-d');
                $document->metadata = json_encode([
                    "Title" => "Zahtev za mirovanje članstva za Mirovanje članstva #$request->id",
                    "Author" => $request->osoba->ime_roditelj_prezime,
                    "Author_id" => $request->osoba->lib,
                    "Description" => "",
                    "Dopuna" => "",
                    "category" => "Mirovanje članstva",
                    "created_at" => Carbon::parse($data['datum_prijema_zahteva'])->format('Y-m-d'),
                ], JSON_UNESCAPED_UNICODE);
                $document->user_id = backpack_user()->id;
                $document->valid_from = Carbon::parse($data['datum_prijema_zahteva'])->format('Y-m-d');
                $document->note = "Automatski kreiran na osnovu podataka iz evidencije koju je vodila služba matičnih sekcija";
                $document->document_type_id = 1;

                $result[$data['zahtev']]['action'] = "Create";
                empty($result[$data['zahtev']]['message']) ? $result[$data['zahtev']]['message'] = "Initializing new document model | Registering new document model | Associating new document model" : $result[$data['zahtev']]['action'] .= " | Initializing new document model | Registering new document model | Associating new document model";
            }

            if ($to_register) {
                $document->registry_id = $registry->id;
                $document->registry_number = $data['zavodni_broj_zahteva'];
                $document->registry_date = Carbon::parse($data['datum_prijema_zahteva'])->format('Y-m-d');
                $document->status_id = 57; // zaveden
//                $document->barcode = ""; // barcode sadrzi id dokumenta, mora da se kreira nakon snimanja
                $document->user_id = backpack_user()->id;
                $document->valid_from = Carbon::parse($data['datum_prijema_zahteva'])->format('Y-m-d');
                $document->note = "Automatski zaveden na osnovu podataka iz evidencije koju je vodila služba matičnih sekcija";

                $result[$data['zahtev']]['action'] = "Update";
                empty($result[$data['zahtev']]['message']) ? $result[$data['zahtev']]['message'] = "Registering existing document" : $result[$data['zahtev']]['message'] .= " | Registering existing document";
            }

            /*if ($document->save()) {

                if ($to_associate) {
                    $document->documentable()->associate($request);

                    $result[$data['zahtev']]['action'] = "Associate";
                    empty($result[$data['zahtev']]['message']) ? $result[$data['zahtev']]['message'] = "Associating existing document with related request" : $result[$data['zahtev']]['message'] .= " | Associating existing document with related request";
                }

                if ($document->save()) {
                    empty($document->barcode) ? $document->barcode = "$request->id#$document->id#$document->registry_number#$document->registry_date" : $document->barcode;
                    $document->save();
                    $result[$data['zahtev']]['status'] = 'success';
                }
            }*/

            $result[$data['zahtev']]['document'] = $document->toArray();

        } catch (\Exception $e) {
            $result[$data['zahtev']]['status'] = 'error';
            empty($result[$data['zahtev']]['message']) ? $result[$data['zahtev']]['message'] = $e->getMessage() : $result[$data['zahtev']]['message'] .= " | {$e->getMessage()}";
        }
//        dd('stop');
        ksort($result[$data['zahtev']], SORT_STRING);
        return $result;
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

    public function zahtevLicencaObrisiDuplikate()
    {
        $prijave = DB::table('tzahtev')
            ->where('status', '<>', REQUEST_FINISHED)
            ->groupBy('si_prijava_id')
            ->having(DB::raw('count(si_prijava_id)'), '>', 1)
            ->pluck('si_prijava_id');
//        dd($prijave);

        $zahtevi = \App\Models\ZahtevLicenca::whereIn('si_prijava_id', $prijave)
            /*->whereDoesntHave('tipLicence', function ($q) {
                $q
                    ->whereIn('si_prijava_id', [24538, 24406, 23937, 24301, 24394, 24401, 24406, 24431, 24472, 24524, 24598, 24767, 24777, 24881, 24944, 25046, 25056, 25145])
                    ->where('licencatip', '453B');
            })*/
            ->whereNotIn('si_prijava_id', [24538, 24406, 23937, 24301, 24394, 24401, 24406, 24431, 24472, 24524, 24598, 24767, 24777, 24881, 24944, 25046, 25056, 25145])
            ->where('licencatip', 'not like', '381%')
            ->orderBy('si_prijava_id')
            ->orderBy('licencatip')
            ->orderBy('datum', 'desc')
            ->get();
//        dd($zahtevi->where('si_prijava_id', 21477));
        $counter = 1;
        $output = "
                <table style='width: 500px'>
                    <tr>
                        <td>#</td>
                        <td>Zahtev</td>
                        <td>Prijava</td>
                        <td>Osoba</td>
                        <td>Tip</td>
                        <td>Status</td>
                        <td>Datum</td>
                        <td>Check</td>
                    </tr>";
        foreach ($zahtevi as $zahtev) {
            if ($counter % 2 != 0) {
                $zahtev->status = 22; // ZAHTEV_LICENCA_AUTOMATSKI
            } else {
                $zahtev->status = REQUEST_CANCELED;
            }
            /*if ($zahtev->save()) {
                $zahtev->check = 'OK';
            } else {
                $zahtev->check = 'NOK';
            }*/
            $output .= "
                    <tr>
                        <td>$counter</td>
                        <td>$zahtev->id</td>
                        <td>$zahtev->si_prijava_id</td>
                        <td>$zahtev->osoba</td>
                        <td>$zahtev->licencatip</td>
                        <td>$zahtev->status</td>
                        <td>$zahtev->datum</td>
                        <td>$zahtev->check</td>
                    </tr>
            ";

            $counter++;
        }
        $output .= "</table>";
        echo $output;
        dd();

    }

    private function kreirajDokumenteZaZahteveZaMirovanje($save = '')
    {
        $result = [];
        $zahtevi = '';
        $this->counter = 1;
        $start = microtime(TRUE);

        $mirovanjaImport = $this->getExcel('public/20220407_mirovanja.xlsx');
        foreach ($mirovanjaImport->toArray() as $key => $value) {
            $requests[$value['zahtev']] = $value;
        }

        foreach ($requests as $data_mirovanje) {

            if ($save == 'save') {
                $result_temp = $this->createD($data_mirovanje);
                $result[$data_mirovanje['zahtev']] = $result_temp[$data_mirovanje['zahtev']];
                array_push($result);
            }
            $zahtevi .= "{$data_mirovanje['zahtev']}, ";
            $this->counter++;
        }

        $stop = microtime(TRUE) - $start;
        echo "<br>Ukupno obradjeno: " . $this->counter;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop;
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        echo "<br>Zahtevi: $zahtevi<br><br>";

        echo "<pre>";
        print_r($result);
        echo "</pre>";
    }

    private function kreirajDokumenteZaStareZavedeneZahteve($save = '')
    {
        $print = [];
        $this->counter = 1;
        $start = microtime(TRUE);

        $query = SiPrijava::whereNotNull('datum_prijema')
//            ->where('status', 19)    // Zastareo
//            ->where('status', 20)    // Odbijen
//            ->where('status', 22)    // Automatski
//            ->where('status', 50)    // Kreiran R
//            ->where('status', 51)    // Podnet
//            ->where('status', 52)    // U obradi
//            ->where('status', 53)    // Završen R
//            ->where('status', 54)    // Otkazan

            ->whereDoesntHave('documents')

            //        nemaju licencu izdatu po tom zahtevu
//        1. Create Document 'Zahtev'
//            ->whereDoesntHave('licenca')
            /*->where(function ($q) {
                $q
//                    ->where('status', REQUEST_CANCELED)
                    ->whereNull('licenca_broj')
                    ->orWhereNull('licenca_broj_resenja')
                    ->orWhereNull('licenca_datum_resenja');
            })*/

//        imaju licencu po starom
//        1. Create Document 'Zahtev'
//        2. Create Document 'Odluka'
            /*->whereHas('licenca')
            ->where(function ($q) {
                $q
//                    ->where('status', REQUEST_CANCELED)
                    ->whereNull('licenca_broj')
                    ->orWhereNull('licenca_broj_resenja')
                    ->orWhereNull('licenca_datum_resenja');
            })*/

//
//        imaju licencu po novom, doneto resenje o sticanju licence
//        1. Create Document 'Zahtev'
//        2. Create Document 'Rešenje'
            /*->whereHas('licenca')
            ->where(function ($q) {
                $q
//                    ->where('status', REQUEST_CANCELED)
                    ->whereNotNull('licenca_broj')
                    ->orWhereNotNull('licenca_broj_resenja')
                    ->orWhereNotNull('licenca_datum_resenja');
            })*/


//            ->limit(50)
//            ->get()
            ->chunkById(1000, function ($zahtevi) use (&$print, $save) {
                $ids = '';
                foreach ($zahtevi as $zahtev) {
                    $ids .= "$zahtev->id, ";

                    $row['counter'] = $this->counter++;
                    $row['id'] = $zahtev->id;
                    $row['osoba'] = $zahtev->osoba_id;
                    $row['status'] = $zahtev->status_prijave;
                    $row['prijem'] = $zahtev->datum_prijema;

                    $print[] = $row;

                    $data = [
                        "action" => "D", // LSRMD
                        "id" => $zahtev->id,
                        "osoba_id" => $zahtev->osoba_id,
                        "prijem" => $zahtev->datum_prijema,
                        "request_category_id" => $zahtev->request_category_id,
                        "app_korisnik_id" => 14,
                        "barcode" => $zahtev->barcode,
                        "created_at" => $zahtev->created_at,
                        "updated_at" => $zahtev->updated_at,
                        "zavodni_broj" => $zahtev->zavodni_broj,
                        "status_id" => [50 => 57, 51 => 57, 52 => 57, 53 => 57, 54 => 58],
                        "napomena" => 'Automatski kreiran na osnovu podataka iz si_prijava',
                    ];
                    if ($save == 'save') {
                        $result = $this->updateZlCreateD($data);
                    }

                }
//                echo "$ids<br><br>";
                echo "<br>Chunk: $this->counter, " . $this->convert(memory_get_usage(TRUE)) . "<br>";

//                dd('stop');
            })//            ->count()
        ;

        echo $this->outputHtmlTable($print);
        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        dd($query);
    }


    public function osobeKojeNisuPreuzeleLicencu()
    {
        $print = [];
        $this->counter = 1;
        $start = microtime(TRUE);

        $query = Osoba::whereHas('licence', function ($q) {
            $q
                ->where('preuzeta', 0)
                ->where('status', '<>', 'D');
        })
            ->chunkById(1000, function ($osobe) use (&$print) {
                $ids = '';
                foreach ($osobe as $osoba) {
                    $condition = TRUE;

//                    NISU PREUZELI NI JEDNU LICENCU
//                    start
                    /*foreach ($osoba->licence as $licenca) {
                        if ($licenca->preuzeta == 1) {
                            $condition &= FALSE;
                            break;
                        }
                    }*/
//                    end

                    if ($condition) {
                        $ids .= "'$osoba->id', ";
//                        $ids .= "$osoba->id<br>";
                        $row['counter'] = $this->counter++;
                        $row['osoba'] = $osoba->id;
                        $row['licence'] = $osoba->licence_statusi_preuzete;
                        $print[] = $row;
                    }

                }
                echo "$ids<br><br>";
                echo "<br>Chunk: $this->counter, " . $this->convert(memory_get_usage(TRUE)) . "<br>";

//                dd('stop');
            });

        echo $this->outputHtmlTable($print);

        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        dd($query);
    }

    public function nevazeceLicence()
    {
        $print = [];
        $this->counter = 1;
        $start = microtime(TRUE);

        $query = Osoba::whereHas('licence', function ($q) {
            $q->where('status', '<>', 'D');
        })
            ->select('id', 'ime', 'roditelj', 'prezime')
            ->chunkById(1000, function ($osobe) use (&$print) {
                $ids = '';
                foreach ($osobe as $osoba) {
//                    dd($osoba);
                    $print[$osoba->id]['counter'] = $this->counter++;
                    $print[$osoba->id]['jmbg'] = $osoba->id;
                    $print[$osoba->id]['osoba'] = $osoba->ime_roditelj_prezime;

                    $tipovi = $osoba->licence->pluck('licencatip')->toArray();

                    $vrste_licenci = LicencaTip::whereIn('id', $tipovi)->pluck('idn')->toArray();

                    $unique = array_unique($vrste_licenci);
                    $duplicates = array_values(array_diff_assoc($vrste_licenci, $unique));
//                    dd($vrste_licenci);
                    $licence = Licenca::where('osoba', $osoba->id)
                        ->where('status', '<>', 'D')
                        ->whereHas('tipLicence', function ($q) use ($duplicates) {
                            $q->whereIn('idn', $duplicates);
                        })
                        ->select('id', 'status', 'licencatip')
                        ->orderBy('id')
                        ->get();

                    foreach ($licence as $licenca) {
                        if (!isset($print[$osoba->id]['licence'])) {
                            $print[$osoba->id]['licence'] = "$licenca->id ($licenca->status)";
                        } else {
                            $print[$osoba->id]['licence'] .= " | $licenca->id ($licenca->status)";
                        }
                        $ids .= "$licenca->id, ";
                    }
                }
                echo "$ids<br><br>";

            });

        dd($print);
        echo $this->outputHtmlTable($print);

        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        dd($query);
    }

    public function insertDatumRodjenjaFromJmbg()
    {
        $print = [];
        $this->counter = 1;
        $osobaSave = FALSE;
        $start = microtime(TRUE);
//        dd(JMBG::for('9999999999999')->isValid());
        $query = Osoba::whereNull('datumrodjenja')
//            ->whereIn('id', ['0202952710364', '0202951780032'])
            ->select('id', 'datumrodjenja')
            ->chunkById(1000, function ($osobe) use (&$print, $osobaSave) {
                $ids = '';

                foreach ($osobe as $osoba) {

                    $print[$osoba->id]['counter'] = $this->counter++;
                    $print[$osoba->id]['osoba'] = $osoba->id;
                    $print[$osoba->id]['datum_rodjenja'] = $osoba->datumrodjenja;
                    $print[$osoba->id]['year'] = '';
                    $print[$osoba->id]['jmbg_correct'] = '';
                    $print[$osoba->id]['napomena'] = '';

                    if (JMBG::for($osoba->id)->isValid()) {
                        $dan_rodjenja = substr($osoba->id, 0, 2);
                        $mesec_rodjenja = substr($osoba->id, 2, 2);
                        $godina_rodjenja = '1' . substr($osoba->id, 4, 3);
                        $datum_rodjenja = $godina_rodjenja . '-' . $mesec_rodjenja . '-' . $dan_rodjenja;

                        if ($mesec_rodjenja === '02') { // februar
                            $print[$osoba->id]['napomena'] = '02 | ';
                            if (date('L', strtotime($datum_rodjenja)) and preg_match("/^[0-9]{4}-(02)-(0[1-9]|1[0-9]|2[0-9])$/", $datum_rodjenja)) {
//                            prestupna
                                $osobaSave = TRUE;
                                $osoba->datumrodjenja = $datum_rodjenja;
                                $print[$osoba->id]['jmbg_correct'] = 'true';
                                $print[$osoba->id]['year'] = 'Leap';
                                $print[$osoba->id]['datum_rodjenja'] = $osoba->datumrodjenja;
                                $print[$osoba->id]['napomena'] .= 'leap';
                                $print[$osoba->id]['osobaSave'] = $osobaSave;
                            } else if (!date('L', strtotime($datum_rodjenja)) and preg_match("/^[0-9]{4}-(02)-(0[1-9]|1[0-9]|2[0-8])$/", $datum_rodjenja)) {
//                            nije prestupna
                                $osobaSave = TRUE;
                                $osoba->datumrodjenja = $datum_rodjenja;
                                $print[$osoba->id]['jmbg_correct'] = 'true';
                                $print[$osoba->id]['year'] = 'Not leap';
                                $print[$osoba->id]['datum_rodjenja'] = $osoba->datumrodjenja;
                                $print[$osoba->id]['napomena'] .= 'not leap';
                                $print[$osoba->id]['osobaSave'] = $osobaSave;
                            } else {
                                $print[$osoba->id]['napomena'] .= 'not leap nor regular';
                                $print[$osoba->id]['osobaSave'] = $osobaSave;
                            }

                        } else {
//                            nije februar
                            $print[$osoba->id]['napomena'] = '!02 | ';
                            if (preg_match("/^[0-9]{4}-(0[13456789]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $datum_rodjenja)) {
                                $osobaSave = TRUE;
                                $print[$osoba->id]['jmbg_correct'] = 'true';
                                $osoba->datumrodjenja = $datum_rodjenja;
                                $print[$osoba->id]['datum_rodjenja'] = $osoba->datumrodjenja;
                                $print[$osoba->id]['osoba'] = $osoba->id;
                                $print[$osoba->id]['napomena'] .= 'pattern';
                                $print[$osoba->id]['osobaSave'] = $osobaSave;
                            } else {
                                $print[$osoba->id]['jmbg_correct'] = 'false';
                                $print[$osoba->id]['napomena'] .= 'not pattern';
                                $print[$osoba->id]['osobaSave'] = $osobaSave;
                            }
                        }
                    } else { // is not valid jmbg by the definition of isValid method from class JMBG
                        $osobaSave = FALSE;
                        $print[$osoba->id]['jmbg_correct'] = 'false';
                        $print[$osoba->id]['napomena'] = '!isValid';
                        $print[$osoba->id]['osobaSave'] = $osobaSave;
                    }
                    if ($osobaSave) {
                        try {
                            $osoba->save();
                            $print[$osoba->id]['model_persisted'] = "<font color='#006400'>true</font>";
                        } catch (\Exception $e) {
                            $print[$osoba->id]['model_persisted'] = "<font color='#8b0000'>false: $osoba->id | {$e->getMessage()}</font>";

                        }
                    }
                }
                echo "$this->counter<br><br>";

            });
        $output = [];
        foreach ($print as $osoba => $item) {
            if ($item['osobaSave']) $output[$osoba] = $item;
        }
        echo $this->outputHtmlTable($output);
//        dd('stop');

        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        dd('the end');
    }

    public function addZvanjeIdToZahtevLicenca()
    {
        $print = [];
        $problem = '';
        $this->counter = 1;
        $start = microtime(TRUE);


        $query = ZahtevLicenca::whereNull('zvanje_id')
            ->chunkById(1000, function ($zahtevi) use (&$print, $problem) {
                $ids = '';
                foreach ($zahtevi as $zahtev) {
                    $zahtev->zvanje_id = $zahtev->osobaId->zvanje;
                    $ids .= "$zahtev->osoba, ";
                    $print['counter'] = ++$this->counter;
                    $print['zahtev'] = $zahtev->id;
                    $print['osoba'] = $zahtev->osoba;
                    $print['zvanje'] = $zahtev->zvanje_id;
                    if ($zahtev->save()) {
                        $print['saved'] = "<span style='color: darkgreen'>OK</span>";
                    } else {
                        $print['saved'] = "<span style='color: darkred'>NOT OK</span>";
                        $problem .= "$zahtev->id, ";
                    }

                }
                echo "$ids<br><br>";
            });
        echo "NISU AZURIRANI: $problem<br><br>";

//        echo $this->outputHtmlTable($print);

        $stop = microtime(TRUE) - $start;
        echo "<br>Vreme izvrsavanja (sec): " . (int)$stop . "<BR>";
        echo "<br>Total memory: " . $this->convert(memory_get_usage(FALSE)) . "<br>";
        dd('kraj');
    }

    public function updateMirovanjaFromExcel()
    {
        $info = [];
        $import = new ExcelImport();

        $collection = ($import->toCollection(public_path() . '/mirovanja.xlsx'));
        $mirovanja = $collection[0]->toArray();

        foreach ($mirovanja as $mirovanje_excel) {

            try {
                DB::beginTransaction();

                // check if osoba exist
                if (!$this->checkOsoba($mirovanje_excel['osoba_id'])) throw new \Exception("Osoba {$mirovanje_excel['osoba_id']} nije pronadjena u bazi.");


                // reformat date fields
                foreach ($mirovanje_excel as $key => $item) {
                    if (
                        $key == 'datum_prijema_zahteva' or
                        $key == 'pocetak' or
                        $key == 'zavrsetak' or
                        $key == 'datum_sednice' or
                        $key == 'datum_odluke'
                    ) $mirovanje_excel[$key] = $this->carbonizeDateDB($item);
                }

                // getting mirovanje by osoba_id and datumpocetka
                $mirovanje = $this->getMirovanjeByExcel($mirovanje_excel);

                if ($mirovanje->isEmpty()) throw new \Exception("Nema mirovanja u bazi, a ima u excelu");
                if ($mirovanje->count() > 1) throw new \Exception("Ima vise od 1 mirovanja u bazi");

                if ($mirovanje->count() == 1) $mirovanje = $mirovanje->first();

                // update mirovanje model
                $mirovanje->datumkraja = $mirovanje_excel['zavrsetak'];
                $mirovanje->brresenja = $mirovanje_excel['odluka_broj'];
                $mirovanje->razlog_mirovanja_usled = $mirovanje_excel['razlog_mirovanja'];
                $mirovanje->prilozio_dokumentaciju = $mirovanje_excel['dokumentacija'];
                if (!empty($mirovanje_excel['napomena'])) $mirovanje->mirovanje_do = $mirovanje_excel['napomena'];

                if (!$mirovanje->save()) throw new \Exception("Greska prilikom snimanja mirovanja");

                // associate mirovanje with request
                $request = $this->getRequestFromExcel($mirovanje_excel['zahtev']);
                $request->requestable()->associate($mirovanje);
                $request->status_id = REQUEST_FINISHED;

                if (!$request->save()) throw new \Exception("Greska prilikom asociranja mirovanja sa zahtevom");

                // getting registry
                $registry = $this->getRegistry($request, 16); // resenje o mirovanju
                if ($registry->isEmpty()) throw new \Exception("Nije pronadjen delovodnik");
                if ($registry->count() > 1) throw new \Exception("Pronadjeno je vise od 1 delovodnika");
                if ($registry->count() == 1) $registry = $registry->first();

                // document
                if ($request->documents->where('document_category_id', 16)->isNotEmpty()) throw new \Exception("Vec postoji dokument");

                $document = new Document();
                $document->document_category_id = 16; // Rešenje o mirovanju
                $document->registry_id = $registry->id;
                $document->registry_number = $mirovanje_excel['odluka_broj'];
                $document->registry_date = $mirovanje_excel['pocetak'];
                $document->status_id = 57; // Zaveden
//                $document->path = null;
//                $document->location = null;
                $document->location = null;
                $document->user_id = backpack_user()->id;
                $document->metadata = json_encode([
                    'title' => "Rešenje o mirovanju članstva",
                    'author' => "Inženjerska komora Srbije",
                    'author_id' => "",
                    'description' => "Za osobu: {$request->osoba->ime_roditelj_prezime}, id: {$request->osoba->lib}",
                    'category' => "Mirovanje članstva",
                    'created_at' => $mirovanje_excel['pocetak'],
                ], JSON_UNESCAPED_UNICODE);
                $document->note = "Automatski kreiran na osnovu podataka iz excela.";
                $document->valid_from = $mirovanje_excel['pocetak'];
                $document->document_type_id = 4; // AUTOMATSKI GENERISAN VIRTUAL

                $document->documentable()->associate($request);

                if (!$document->save()) throw new \Exception("Greska prilikom snimanja dokumenta");

                $registry->counter++;

                if (!$registry->save()) throw new \Exception("Greska prilikom snimanja delovodnika");

                $document->barcode = "$request->id#$document->id#$mirovanje->datumpocetka";
                $document->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $info[$mirovanje_excel['zahtev']] = $e->getMessage();
            }
        }
        dd($info, 'kraj');
    }

    /**
     * @param array $mirovanje_excel
     * @return mixed
     */
    private function getMirovanjeByExcel(array $mirovanje_excel)
    {
        return EvidencijaMirovanja::where('osoba', $mirovanje_excel['osoba_id'])
            ->where('datumpocetka', $mirovanje_excel['pocetak'])
            ->get();
    }

    private function getRequestFromExcel(int $request_id)
    {
        return \App\Models\Request::find($request_id);
    }

    private function getRegistry(\App\Models\Request $request, int $document_category_id) // todo: omoguciti opstu implementaciju (metod treba da nadje registry za bilo koji zahtev i kategoriju dokumenta)
    {
        $zg = $request->osoba->zvanjeId->zvanje_grupa_id;
        $request_category_id = $request->request_category_id;

        return Registry::where('status_id', AKTIVAN)
            ->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                $q->where('label', "02-$zg");
            })
            ->whereHas('requestCategories', function ($q) use ($request, $request_category_id, $document_category_id) {
                $q->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
            })
            ->get();

//        if ($registry->isEmpty()) return new \Exception("Error, there is no registry found..");
//        if ($registry->count() == 1) return $registry->first();
//        if ($registry->count() > 1) return new \Exception("Error, there is more than 1 registry found for this params.");
    }

    /**
     * @param string $date
     * @return string
     * Set date format to Y-m-d, prepare for db
     */
    private function carbonizeDateDB(string $date): string
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    /**
     * @param $size
     * @return string
     */
    protected function convert($size)
    {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    /**
     * @param bool $start
     * @return int
     */
    public function measureTime($start = FALSE)
    {
        if ($start === FALSE) {
            //start
            return (int)microtime(TRUE);
        } else {
            //stop
            return (int)microtime(TRUE) - (int)$start;
        }
    }

    public function secondsToTime($seconds)
    {
        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a dana, %h sati, %i minuta i %s sekundi');
    }

    public function modulo10($number)
    {
        $digits = array(0, 9, 4, 6, 8, 2, 7, 1, 3, 5);
        $next = 0;
        for ($i = 0; $i < strlen($number); $i++) {
            $next = $digits[($next + substr($number, $i, 1)) % 10];
        }
        dd((10 - $next) % 10);
        echo (10 - $next) % 10;
    }
}
