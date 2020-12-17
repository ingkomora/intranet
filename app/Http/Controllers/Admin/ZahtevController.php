<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ExcelImport;
use App\Libraries\Helper;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;


class ZahtevController extends Controller {
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware(backpack_middleware());
    }

    public function unesi($action, $url = '') {

        $data['action'] = $action;
        $data['url'] = $url;

//        return view('unesi', $data);
        return view(backpack_view('unesi'), $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function obradizahtevsvecanaforma(Request $request) {
        $file = $request->file('upload');

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
            $rows = explode("\r\n", $request->request->get('licence'));
            foreach ($rows as $row) {
                $rowexpl = explode(",", $row);
                $licence[$rowexpl[0]] = $rowexpl[1];
            }
//            dd($licence);
            /*            $validated = $request->validate([
                            'licence' => [
                                function ($attribute, $values, $fail) {
                                    $error = false;
                                    foreach ($values as $value) {
                                        $zahtev = \App\Models\Zahtev::where(['licenca_broj' => $value['broj']])->first();
                                        if (!is_null($zahtev)) {
                                            if ($zahtev->osoba !== $value['jmbg']) {
                                                $error = true;
                                                $fail('JMBG: <strong>' . $value['jmbg'] . '</strong> se ne slaže sa brojem licence, proverite unos.');
                                            }
                                        }
                                    }
                                }
                            ],
                        ]);*/
//        dd($validated);
        }
        $messageLicencaOK = 'Licence: ';
        $messageLicencaNOK = 'Licence: ';
        $messageZahtevOK = 'Zahtevi: ';
        $messageZahtevNOK = 'Zahtevi: ';
        $flagOK = false;
        $flagNOTOK = false;
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
            $licencaO = \App\Models\Licenca::find($licenca);
//dd($licencaO);
            if (!is_null($licencaO)) {
                $osoba = $licencaO->osobaId;
                $h = new Helper();

                $data = new \stdClass();
                $ss = mb_strtoupper(mb_substr($osoba->roditelj, 0, 2));
                if ($ss == "LJ" || $ss == "NJ") {
//                if (mb_strtoupper(mb_substr($osoba->roditelj, 0, 2) == "LJ") || mb_strtoupper(mb_substr($osoba->roditelj, 0, 2) == "NJ")) {
                    $osoba->roditelj = mb_ucfirst(mb_substr($osoba->roditelj, 0, 2));
                } else {
                    $osoba->roditelj = mb_ucfirst(mb_substr($osoba->roditelj, 0, 1));
                }
                $data->osobaImeRPrezime = $h->iso88592_to_cirUTF($osoba->ime . " " . $osoba->roditelj . ". " . $osoba->prezime);
                $data->zvanje = $h->iso88592_to_cirUTF($osoba->zvanjeId->naziv);
                $data->zvanjeskr = $h->iso88592_to_cirUTF($osoba->zvanjeId->skrnaziv);
                $data->licenca = $h->iso88592_to_cirUTF(mb_strtoupper($licenca));
                $data->licencaTip = $licencaO->tipLicence->id;
                $data->vrstaLicenceNaslov = $h->iso88592_to_cirUTF(mb_strtoupper($licencaO->tipLicence->vrstaLicence->naziv_genitiv));
                $data->vrstaLicence = $h->iso88592_to_cirUTF(mb_strtolower($licencaO->tipLicence->vrstaLicence->naziv_genitiv));
                if ($data->licencaTip == '381') {
                    $data->nazivLicence = mb_strtolower($h->iso88592_to_cirUTF(str_replace('Odgovorni inženjer', 'Odgovornog inženjera', $licencaO->tipLicence->naziv)));
                } else if ($data->licencaTip == '381E') {
                    $data->nazivLicence = mb_strtolower($h->iso88592_to_cirUTF(str_replace('Odgovorni projektant za energetsku efikasnost zgrada (oznaka EE 12-01)', 'Odgovornog projektanta za energetsku efikasnost zgrada', $licencaO->tipLicence->naziv)));
                } else {
                    if (is_null($licencaO->tipLicence->oznaka)) {
                        $data->nazivLicence = mb_strtolower($h->iso88592_to_cirUTF(str_replace($licencaO->tipLicence->vrstaLicence->naziv, $licencaO->tipLicence->vrstaLicence->naziv_genitiv, $licencaO->tipLicence->naziv)));
                    } else {
                        $data->nazivLicence = "";
                    }
                }
                $data->strucnaOblast = $h->iso88592_to_cirUTF(mb_strtolower($licencaO->tipLicence->tipLicenceReg->podOblast->regOblast->naziv));
                $data->uzaStrucnaOblastId = $licencaO->tipLicence->tipLicenceReg->podOblast->id;
                $data->uzaStrucnaOblast = $h->iso88592_to_cirUTF(mb_strtolower($licencaO->tipLicence->tipLicenceReg->podOblast->naziv));
                $data->datumStampe = date('d.m.Y.', strtotime($datumstampe));
                $data->filename = $osoba->ime . "_" . $osoba->roditelj . "_" . $osoba->prezime . "_" . $licenca;

                /*                $data->osobaImeRPrezime = "[Име Средње слово, Презиме]";
                                $data->zvanje = "[звање]";
                                $data->zvanjeskr = "[звање]";
                                $data->licenca = "[број лиценце]";
                                $data->vrstaLicenceNaslov = "ОДГОВОРНОГ [врста лиценце]";
                                $data->vrstaLicence = mb_strtolower("ОДГОВОРНОГ [ПРОЈЕКТАНТА, ИЗВОЂАЧА РАДОВА, УРБАНИСТУ, ПЛАНЕРА]");
                                $data->strucnaOblast = "[назив стручне области]";
                                $data->uzaStrucnaOblast = "[назив уже стручне области]";
                                $data->datumStampe = "[датум издавања]";*/

//                dd($data);
//              TODO  poziv funkcije
//                $output = new OutputController();
//                echo $output->downloadPDF($data, 'svecanaforma');
//                  poziv direktno - aktuelno
                $pdf = PDF::loadView('svecanaforma', (array)$data);
//                $pdf = PDF::loadView('svecanaforma-datum', (array)$data);
                Storage::put("public/pdf/$data->filename.pdf", $pdf->output());
//                  PREVIEW
//                return $pdf->stream("$data->filename.pdf");
//                return $pdf->download("$data->filename.pdf");


                $countOK++;

                $dataPrint['dataOK'][] = $data;
            } else {
                $countNOK++;
                $dataPrint['dataNOK'][] = $licenca;
//                echo "<br>$count. $licenca NOK";
            }
//                var_dump($dataPrint);
//            break;
        }

        usort($dataPrint['dataOK'], function ($a, $b) {
            return $a->osobaImeRPrezime <=> $b->osobaImeRPrezime;
        });

        $ime = array_column($dataPrint['dataOK'], 'osobaImeRPrezime');
//dd($ime);
        if ($ime != array_unique($ime)) {
            echo 'There are duplicates in osobaImeRPrezime';
        }
        $lic = array_column($dataPrint['dataOK'], 'licenca');
        if ($lic != array_unique($lic)) {
            echo 'There are duplicates in licenca';
        }

        if ($ime != array_unique($ime) && $lic != array_unique($lic)) {
            echo 'There are duplicates in both osobaImeRPrezime and licenca';
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

        echo "<br>OK: $countOK";
        echo "<br>NOT OK: $countNOK";

        dd($countOK);
        $path = storage_path('app/public/pdf');

        if ($countOK > 1) {
            $pdfFiles = Storage::files($path);
            //zip files
            $zip_file = 'zahtevi.zip';
            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($files as $name => $file) {
                // We're skipping all subfolders
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();

                    // extracting filename with substr/strlen
                    $relativePath = 'pdf/' . substr($filePath, strlen($path) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
//            Storage::delete($pdfFiles);
//            return response()->download($zip_file);
        } else if ($countOK == 1) {

            //download one file
        } else {
            //nema fajlova za download
            dd("kraj nema nista");

        }

        $messageLicencaOK .= "su uspešno sačuvane u bazi ($countOK)";
        $messageLicencaNOK .= "nisu sačuvane u bazi ($countNOK)";

        info($messageLicencaOK);
        $flagNOK ? toastr()->error($messageLicencaNOK) : toastr()->warning("Nema grešaka");
        $flagOK ? toastr()->success($messageLicencaOK) : toastr()->warning("Nema kreiranih licenci");
//        return redirect('/unesinovelicence')->with('message', $messageLicencaOK)->withInput();
        return view(backpack_view('obradizahtevsvecanaforma'), $this->data);

    }

    /**
     * @param Request $request
     */
    public function preuzimanjesvecanaforma(Request $request) {
        $result = new \stdClass();
        $licence = str_replace(" ", "", $request->request->get('licence'));
        $licence = explode("\r\n", $licence);
        dd($licence);
        foreach ($licence as $licenca_broj) {

            $licenca = \App\Models\Licenca::with("osobaId")->find($licenca_broj)->get();
            dd($licenca);
            if (!is_null($licenca)) {

                $osoba = $licenca->osobaId;
            }


        }
        dd($result);

    }

    public function in_array_recursive($needle, Array $haystack, string $attribute) {
//        echo "<br>test";
//        var_dump($haystack);
        if (!empty($haystack) AND is_array($haystack)) {
            foreach ($haystack as $key => $item) {
                if (is_array($item)) {
//                    echo "<br>item is array";
                    $this->in_array_recursive($needle, $item, $attribute);
                } else {
//                    if ($attribute == $key) {
//                    echo strcmp(mb_strtolower($item), mb_strtolower($needle));
                    if (strcmp(mb_strtolower($item), mb_strtolower($needle)) == 0) {
//                        var_dump($item);
                        echo "<br>item $item = needle $needle";
                        return true;
                    }
//                    }
                }
            }
        }
        return false;
    }
}
