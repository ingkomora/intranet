<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Log;
use App\Models\Registry;
use App\Models\SiPrijava;
use App\Models\SiStruka;
use App\Models\User;
use DNS1D;
use Illuminate\Support\Facades\Cookie;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ZavodjenjeController extends Controller
{
    protected $result;
    protected $brprijava;
    protected $zavodjenje = [
        'si' => ['url' => 'si', 'model' => 'SiPrijava', 'title' => 'Zavođenje prijava za polaganje stručnog ispita'],
        'licence' => ['url' => 'licence', 'model' => 'ZahtevLicenca', 'title' => 'Zavođenje zahteva za izdavanje licenci'],
//        'clanstvo' => ['url' => 'clanstvo', 'model' => 'PrijavaClanstvo', 'title' => 'Zavođenje prijava za prijem u članstvo'],
        'clanstvo' => ['url' => 'clanstvo', 'model' => 'Request', 'title' => 'Zavođenje prijava za prijem u članstvo'],
        'sfl' => ['url' => 'sfl', 'model' => 'Request', 'title' => 'Zavođenje zahteva za izdavanje svečane forme licence'],
    ];

    public function show($type)
    {
        return view("zavodjenje", $this->zavodjenje[$type]);
    }


    /**
     * ZAVODJENJE PRIJAVA ZA STRUCNI ISPIT
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->prijave = trim($request->prijave);
        $this->brprijava = array_map('intval', explode("\r\n", $request->prijave));
        $prijave = SiPrijava::whereIn('id', $this->brprijava)->orderBy('id', 'asc')->get();
        foreach ($prijave as $prijava) {
//            ZAVEDI SAMO PRIJAVE KOJE SU GENERISANE
            if ($prijava->status_prijave == PRIJAVA_GENERISANA) {
                if ($prijava->reg_oblast_id == 'E') {

                    $prijava->reg_pod_oblast_id = $prijava->zvanje->reg_oblast_id;
                    $zavodnibroj = SiStruka::where('reg_oblast_id', $prijava->reg_pod_oblast_id)->first();
                } else {
                    $zavodnibroj = SiStruka::where('reg_oblast_id', $prijava->reg_oblast_id)->first();
                }
                $zavodnibroj->zavodnibrojbrojcanik++;
                $zavodnibroj->save();

                $osoba = $prijava->osoba;
//        generisi zavodni broj i datum prijema
                $prijava->zavodni_broj = $zavodnibroj->zavodnibrojprefiks . $zavodnibroj->zavodnibrojbrojcanik;
                $prijava->datum_prijema = (!empty($request->datum_prijema)) ? Carbon::parse($request->datum_prijema)->format('Y-m-d') : now()->toDateString();
                $prijava->app_korisnik_id = backpack_user()->id;
//            STATUS PRIJAVE ZAVEDENA = 3
                $prijava->status_prijave = PRIJAVA_ZAVEDENA;
                $prijava->save();
            }

//        kreiraj niz sa svim podacima za nalepnice
//            $this->result[$prijava->id]['oblast'] = $zavodnibroj->naziv;
//            $this->result[$prijava->id]['osoba'] = $osoba->ime . " " . $osoba->prezime;
//            $this->result[$prijava->id]['zavodni_broj'] = $prijava->zavodni_broj;
//            $this->result[$prijava->id]['datum_prijema'] = $prijava->datum_prijema;
//            echo "<BR>$prijava->id | $prijava->zavodni_broj";
        }
//        dd('GOTOVO!');
//        return redirect("/nalepnicePDF");
        return $this->nalepnicePDF();
    }


    /**
     * ZAVODJENJE SVEGA PO NOVOM SISTEMU
     * TODO PRIJAVE SI DA SE PREBACE
     * @param $type
     * @return array|\Illuminate\Http\Response
     */
    public function zavedi($type, array $data)
    {
        $result = [];
        $log = new Log();
        $log->type = 'INFO';

        $nameSpace = 'App\Models\\';
        $model = $nameSpace . $this->zavodjenje[$type]['model'];
//        echo "$model<br>";
        $ids = array_map('trim', $data['entries']);
        $registry_date = (!empty($data['registry_date'])) ? Carbon::parse($data['registry_date'])->format('Y-m-d') : now()->toDateString();
        $this->brprijava = $ids;
        $prijave = $model::whereIn('id', $ids)->orderBy('id', 'asc')->get();
        foreach ($prijave as $prijava) {
            $documents = $prijava->documents->where('document_category_id', 1)->whereIn('status_id', [DOCUMENT_CREATED, DOCUMENT_REGISTERED]);
//                    dd($documents);
            if ($documents->isNotEmpty()) {
                if ($documents->count() > 1) {
                    //GRESKA IMA VISE OD JEDNOG ZAHTEVA
//                    echo "ima vise od jednog zahteva";
                    return FALSE;
                }
                foreach ($documents as $doc) {
//                    echo $document->id;
                    $document = $doc;
                }
            }
            if ($prijava->status_id == REQUEST_SUBMITED) {
                if ($documents->isEmpty()) {
                    $document = new Document();
                    $document->document_category_id = 1;
                    $document->documentable()->associate($prijava);
                }
                if (is_null($document->registry_number) and is_null($document->registry_date)) {
                    $zg = $prijava->osoba->zvanjeId->zvanje_grupa_id;
                    $reg = Registry::whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                        $q->where('label', "02-$zg");
                    })->whereHas('requestCategories', function ($q) use ($prijava) {
                        $q->where('registry_request_category.request_category_id', 1);
                    })->get()[0];
                    $reg->counter++;
                    $reg->save();
                    $regnum = $reg->registryDepartmentUnit->label . "-" . $reg->base_number . "/" . date("Y", strtotime($registry_date)) . "-" . $reg->counter;

                    $document->document_type_id = 1;
                    $document->registry_id = $reg->id;
                    $document->registry_number = $regnum;
                    $document->registry_date = $registry_date;
                    $document->status_id = DOCUMENT_REGISTERED;

                    $document->save();
                }
            }

//            echo("<br>$regnum");
            $result['category'] = ucfirst($prijava->requestCategory->name);
            $result[$log->type][$prijava->id] = "{$prijava->osoba->ime} {$prijava->osoba->prezime}";

            $prijava->status_id = REQUEST_IN_PROGRESS;
            $prijava->save();

            $data['result'][] = array(
                $prijava->id,
                $prijava->osoba->ime . " " . $prijava->osoba->prezime,
                $document->registry_number,
                $document->registry_date,
            );
        }
//        dd();
        $result['pdf'] = $this->zavodneNalepnicePDF($data);

//        return TRUE;
        return $result;

    }

    /**
     * Generisi PDF.
     *
     * @return string|\Illuminate\Http\Response
     */
    public function zavodneNalepnicePDF($data)
    {
        $prijave = \App\Models\Request::whereIn('id', $this->brprijava)->orderBy('id', 'asc')->get();

        /*        if ($this->result['oblast'] == 'Energetska efikasnost') {
                    $data['oblast'] = $this->result['oblast'];
                } else {
                    $data['oblast'] = $this->result['oblast'] . " - " . $this->result['podoblast'];
                }*/
        $data['oblast'] = '';
        $filename = date("Ymd") . "_test";
//        dd($data);
        $pdf = PDF::loadView('nalepniceRamipa89x43', $data);
//        return $pdf->stream("$filename.pdf");
//        return $pdf->download("$filename.pdf");


        $path = public_path('download/');
        $pdf->save($path . '/' . "$filename.pdf");

        $pdfpath = '/download/' . "$filename.pdf";
//        return response()->download($pdf);
        return $pdfpath;
    }

    /**
     * Generisi PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function nalepnicePDF()
    {
        $prijave = SiPrijava::whereIn('id', $this->brprijava)->orderBy('id', 'asc')->get();
        $data['result'] = $prijave->map(function ($prijava) {
            $this->result['oblast'] = $prijava->regOblast->naziv;
            $this->result['podoblast'] = $prijava->regPodOblast->naziv;
            return array(
                $prijava->id,
                $prijava->osoba->ime . " " . $prijava->osoba->prezime,
                $prijava->zavodni_broj,
                $prijava->datum_prijema,
            );
        });
        if ($this->result['oblast'] == 'Energetska efikasnost') {
            $data['oblast'] = $this->result['oblast'];
        } else {
            $data['oblast'] = $this->result['oblast'] . " - " . $this->result['podoblast'];
        }
        $filename = date("Ymd") . "_" . strtoupper(preg_replace("/\s-\s|\s/", "_", $data['oblast']));
        $pdf = PDF::loadView('nalepniceRamipa89x43', $data);
//        return $pdf->stream("$filename.pdf");
        return $pdf->download("$filename.pdf");
    }

    /**
     * @param $prijava_id
     */
    public function prijavaPDF($prijava_id, $type = 'stream')
    {
        $prijava = SiPrijava::findOrFail($prijava_id);
        $zahtev = $prijava->zahtevLicenca;

        $filename = date("Ymd") . "_" . $prijava->id;
        $data['barcode1'] = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($prijava->barcode, "C128", 1, 30) . '" alt="barcode"   />';
        $data['prijava'] = $prijava;
        $data['barcode2'] = '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($zahtev->barcode, "C128", 1, 30) . '" alt="barcode"   />';
        $data['zahtev'] = $zahtev;
        $refs = $data['prijava']->reference;
        for ($i = 0; $i < count($refs); $i++) {
            if (!empty($refs[$i]['investitor']) and strstr($refs[$i]['investitor'], "##")) {
                $investitor = explode("##", $refs[$i]['investitor']);
                $refs[$i]->investitor = trim($investitor[1]);
            }
            if (!empty($refs[$i]['firma']) and strstr($refs[$i]['firma'], "##")) {
                $firma = explode("##", $refs[$i]['firma']);
                $refs[$i]->firma = trim($firma[1]);
            }
            if (!empty($refs[$i]['lokacijaadresa']) and strstr($refs[$i]['lokacijaadresa'], "##")) {
                $lokacijaadresa = explode("##", $refs[$i]['lokacijaadresa']);
                $refs[$i]->lokacijaadresa = trim($lokacijaadresa[1]) . " (" . trim($lokacijaadresa[0]) . ")";
            }
            if (!empty($refs[$i]['odgprojektant']) and strstr($refs[$i]['odgprojektant'], "##")) {
                $odgprojektant = explode("##", $refs[$i]['odgprojektant']);
                $refs[$i]['odgovornolicestranaclicenca'] = trim($odgprojektant[0]);
                $refs[$i]['odgovornolicestranac'] = trim($odgprojektant[1]);
            } else {
                $refs[$i]['odgovornolicestranaclicenca'] = '';
                $refs[$i]['odgovornolicestranac'] = $refs[$i]['odgprojektant'];
            }
        }
        $data['prijava']->reference = $refs;
        $data['vrsta_licence'] = $prijava->zahtevLicenca->tipLicence->oznaka_naziv;

        $pdf = PDF::loadView('pdf.pdf', $data);
        if ($type == 'download') {
            return $pdf->download("$filename.pdf");
        } else {
            return $pdf->stream("$filename.pdf");
        }
    }
}
