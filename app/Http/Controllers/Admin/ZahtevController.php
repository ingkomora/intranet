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

    public function obradizahtevsvecanaforma(Request $request) {
        $file = $request->file('upload');

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
//dd($filename);
        if (!is_null($file)) {
            $import = new ExcelImport();
//            $import->import($file);
            try {
                $collection = ($import->toCollection($file));
                $licence = $collection[0]->pluck('datumstampe', 'licenca')->toArray();
//        dd($licence);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                dd($failures);
            }
        } else {
            $licence = $request->request->get('licence');
            $validated = $request->validate([
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
            ]);
//        dd($validated);
        }
        $messageLicencaOK = 'Licence: ';
        $messageLicencaNOTOK = 'Licence: ';
        $messageZahtevOK = 'Zahtevi: ';
        $messageZahtevNOK = 'Zahtevi: ';
        $flagOK = false;
        $flagNOTOK = false;
        $count = 0;
        $countOK = 0;
        $countNOTOK = 0;
        $dataOK = ['data' => []];
        foreach ($licence as $licenca => $datumstampe) {
            $count++;
//                IMPORTUJU SE SAMO ZAHTEVI KAD VEC POSTOJI OSOBA SA LICENCAMA
//                ZA SADA SU SVE DODATE LICENCE AKTIVNE
//            UBUDUCE CE SE UBACIVATI I LICENCE AUTOMATSKI DOBIJENE NAKON POLOZENOG STRUCNOG ISPITA
//            $provera->getJmbgFromLicenca($licenca['broj']);

            $status_grupa = LICENCE;
//dd($licenca);
            $licencaO = \App\Models\Licenca::find($licenca);
            if (!is_null($licencaO)) {
                $osoba = $licencaO->osobaId;
//                dd($osoba);
                $h = new Helper();

                $data = new \stdClass();
                if (mb_strtoupper(mb_substr($osoba->roditelj, 0, 2) == "LJ") || mb_strtoupper(mb_substr($osoba->roditelj, 0, 2) == "NJ")) {
                    $osoba->roditelj = mb_ucfirst(mb_substr($osoba->roditelj, 0, 2));
                } else {
                    $osoba->roditelj = mb_ucfirst(mb_substr($osoba->roditelj, 0, 1));
                }
                $data->osobaImeRPrezime = $h->iso88592_to_cirUTF($osoba->ime . " " . $osoba->roditelj . ". " . $osoba->prezime);
                $data->zvanje = $h->iso88592_to_cirUTF($osoba->zvanjeId->naziv);
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
                $data->filename = $osoba->ime . "_" . $osoba->prezime . "_" . $licenca;

//                dd($data);
//              TODO  poziv funkcije
//                $output = new OutputController();
//                echo $output->downloadPDF($data, 'svecanaforma');
//                  poziv direktno - aktuelno
                $pdf = PDF::loadView('svecanaforma', (array)$data);
//                $pdf = PDF::loadView('svecanaforma-datum', (array)$data);
//                Storage::put("public/pdf/$data->filename.pdf", $pdf->output());
//                  PREVIEW
                return $pdf->stream("$data->filename.pdf");

                $countOK++;
                echo "<br>$count. $licenca OK";
                echo "<br>trazi se: $data->osobaImeRPrezime, $data->licenca";

                var_dump($dataOK);
                foreach ($dataOK['data'] as $data) {
                    var_dump($data);
                }
                dd($dataOK);

//                if (in_array($data->osobaImeRPrezime, $dataOK) /*AND in_array($data->licenca, $dataOK['data'])*/) {
                /*                if ($this->in_array_r($data->osobaImeRPrezime, $dataOK['data'])) {
                    echo "<br>duplikat: $data->osobaImeRPrezime, $data->licenca";
                } else {
                    $dataOK['data'][] = (array)$data;
                }*/
            } else {
                $countNOTOK++;
                echo "<br>$count. $licenca NOK";
            }
//            break;
        }
//        dd($dataOK);
//        $pdf = PDF::loadView('svecanaforma-report', $dataOK);
//        Storage::put("public/pdf/Report_$filename.pdf", $pdf->output());

//                $this->log($zahtev, $status_grupa, "$naziv zahtev: $zahtev->id, status: " . ZAHTEV_LICENCA_GENERISAN);
//            TODO prikazati status licence u toasteru
        /*            if ($this->kreirajlicencu($osoba)) {
                        $messageLicencaOK .= $licenca['broj'] . ', ';
                        $flagOK = true;
                        $countOK++;

                    } else {
                        $messageLicencaNOTOK .= $licenca['broj'] . ', ';
                        $flagNOTOK = true;
                        $countNOTOK++;
                    }*/

//            TODO TREBA DODATI SLUCAJ KAD NEMA OSOBE A NIJE PODNEO ZAHTEV ZA CLANSTVO TJ. UPIS NOVE OSOBE U REGISTAR ALI TREBALO BI DA JE IMA IZ SI

        echo "<br>OK: $countOK";
        echo "<br>NOT OK: $countNOTOK";
        dd("kraj");
        $messageLicencaOK .= "su uspešno sačuvane u bazi ($countOK)";
        $messageLicencaNOTOK .= "nisu sačuvane u bazi ($countNOTOK)";

        info($messageLicencaOK);
        $flagNOTOK ? toastr()->error($messageLicencaNOTOK) : toastr()->warning("Nema grešaka");
        $flagOK ? toastr()->success($messageLicencaOK) : toastr()->warning("Nema kreiranih licenci");
//        return redirect('/unesinovelicence')->with('message', $messageLicencaOK)->withInput();
        return view(backpack_view('obradizahtevsvecanaforma'), $this->data);

    }

    public function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }
}
