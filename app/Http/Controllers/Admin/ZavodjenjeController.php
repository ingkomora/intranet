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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ZavodjenjeController extends Controller
{
    protected $result;
    protected $brprijava;
    protected $zavodjenje = [
        'si' => ['document_category_id' => 10, 'registry_type' => 'oblast', 'url' => 'si', 'model' => 'SiPrijava', 'title' => 'Zavođenje prijava za polaganje stručnog ispita'],
        'licence' => ['document_category_id' => 5, 'registry_type' => 'oblast', 'url' => 'licence', 'model' => 'ZahtevLicenca', 'title' => 'Zavođenje zahteva za izdavanje licenci'],
        'clanstvo' => ['document_category_id' => [1 => 1, 2 => 2], 'registry_type' => 'sekcija', 'url' => 'clanstvo', 'model' => 'Request', 'title' => 'Zavođenje zahteva za članstvo'],
        'mirovanjeclanstva' => ['document_category_id' => [3 => 4, 4 => 5], 'registry_type' => 'sekcija', 'url' => 'mirovanjeclanstva', 'model' => 'Request', 'title' => 'Zavođenje zahteva za mirovanje'],
        'sfl' => ['document_category_id' => 6, 'registry_type' => '02', 'url' => 'sfl', 'model' => 'Request', 'title' => 'Zavođenje zahteva za izdavanje svečane forme licence'],
        'resenjeclanstvo' => ['document_category_id' => [12 => 2, 13 => 2], 'registry_type' => 'sekcija', 'url' => 'resenjeclanstvo', 'model' => 'Request', 'title' => 'Zavođenje rešenja o prestanku i brisanju iz članstva'],
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
        $data['result'] = [];
        $log = new Log();
        $log->type = 'INFO';
        $documentsOK = TRUE;
        $requestOK = FALSE;

        $nameSpace = 'App\Models\\';
        $model = $nameSpace . $this->zavodjenje[$type]['model'];
//        echo "$model<br>";
        $ids = array_map('trim', $data['entries']);
        $registry_date = (!empty($data['registry_date'])) ? Carbon::parse($data['registry_date'])->format('Y-m-d') : now()->toDateString();
        $this->brprijava = $ids;
        $requests = $model::whereIn('id', $ids)->orderBy('id', 'asc')->get();
        foreach ($requests as $request) {
            try {
                if (is_array($this->zavodjenje[$type]['document_category_id'])) {
                    $document_category_id = array_keys($this->zavodjenje[$type]['document_category_id'], $request->request_category_id);
                } else {
                    $document_category_id = (array)$this->zavodjenje[$type]['document_category_id'];
                }
                DB::beginTransaction();
                foreach ($document_category_id as $cat) {
                    $resultDocument = $this->registerDocuments($request, $cat, $registry_date, $type);
                    if (isset($result['ERROR']) and $resultDocument['ERROR'][$request->id]['status']) {
                        DB::rollBack();
                        $result['ERROR'][$request->id] = $resultDocument['ERROR'][$request->id]['message'];
                        return $result;
                    }

                    array_push($data['result'], $resultDocument['data']);
                    $documentsOK &= $resultDocument['registerDocumentOK'];
                }

                if ($request->status_id == REQUEST_SUBMITED) {
                    $request->status_id = REQUEST_IN_PROGRESS;
                    if ($request->save()) {
                        $requestOK = TRUE;
                    }
                } else {
                    $requestOK = TRUE;
                }
                if ($documentsOK && $requestOK) {
                    DB::commit();
                } else {
                    DB::rollBack();
                    $result['ERROR'][$request->id] = "Greška 3! ROLLBACK: Nije snimljen";
                }
                $result['category'] = ucfirst($request->requestCategory->name);
                $result[$log->type][$request->id] = "{$request->osoba->ime} {$request->osoba->prezime}";
            } catch (\Exception $e) {
                DB::rollBack();
                $result['ERROR'][$request->id] = "Greška 4! {$e->getMessage()}<br>Greška prilikom zavođenja dokumenta.<br>Kontaktirajte službu za informacione tehnologije";
                return $result;
            }
        }
        $result['pdf'] = $this->zavodneNalepnicePDF($data);

        return $result;

    }

    /**
     * @param \App\Models\Request $request
     * @param $document_category_id
     * @param $registry_date
     * @return array|\Illuminate\Http\RedirectResponse
     */
    protected function registerDocuments(\App\Models\Request $request, $document_category_id, $registry_date, $type)
    {
        $result = array();
        $registryOK = $documentOK = FALSE;

        $documents = $request->documents;
        if ($documents->isNotEmpty()) {
//            $documents->where('document_category_id', $document_category_id)->whereIn('status_id', [DOCUMENT_CREATED, DOCUMENT_REGISTERED]);
            $documents = $documents->filter(function ($value, $key) use ($document_category_id) {
                return $value->document_category_id == $document_category_id and ($value->status_id == DOCUMENT_CREATED or $value->status_id == DOCUMENT_REGISTERED);
            });
            if ($documents->count() > 1) {
                //GRESKA IMA VISE OD JEDNOG ZAHTEVA
//                    echo "ima vise od jednog zahteva";
                $result['ERROR'][$request->id] = "Greška 1! Ima više od jednog dokumenta za kategoriju $document_category_id";
                return $result;
            } else {
                $document = $documents->first();
            }
        } else {
            $document = new Document();
            $document->document_category_id = $document_category_id;
            $document->status_id = DOCUMENT_CREATED;
        }
        if ($document->status_id == DOCUMENT_CREATED and is_null($document->registry_number) and is_null($document->registry_date)) {
            if ($this->zavodjenje[$type]['registry_type'] == 'sekcija') {
                $label = '02-' . $request->osoba->zvanjeId->zvanje_grupa_id;
            } else if ($this->zavodjenje[$type]['registry_type'] == 'oblast') {
//                $label = licenca;
            } else {
                $label = $this->zavodjenje[$type]['registry_type'];
            }
            $registry = Registry::where('status_id', AKTIVAN)
                ->whereHas('registryDepartmentUnit', function ($q) use ($label) {
                    $q->where('label', "$label");
                })
                ->whereHas('requestCategories', function ($q) use ($request, $document_category_id) {
                    $q->where('registry_request_category.request_category_id', $request->request_category_id) // prijem u clanstvo
//                            ->where('registry_request_category.document_category_id', $document_category_id) // odluka
                    ;
                })
                ->get();
            if ($registry->count() != 1) {
                $result['ERROR'][$request->id]['status'] = TRUE;
                $result['ERROR'][$request->id]['message'] = "Greška 2! \nGreška prilikom odabira skraćenog delovodnika. Kontaktirajte službu za informacione tehnologije";
                return $result;
            } else {
                $registry = $registry->first();
            }
            if ($document->status_id == DOCUMENT_CREATED and empty($document->registry_number) and empty($document->registry_date)) {
                $registry->counter++;
            }
            if ($registry->save()) {
                $registryOK = TRUE;
            }
            $regnum = $registry->registryDepartmentUnit->label . "-" . $registry->base_number . "/" . date("Y", strtotime($registry_date)) . "-" . $registry->counter;
            $document->document_type_id = 1;
            $document->registry_id = $registry->id;
            $document->registry_number = $regnum;
            $document->registry_date = $registry_date;
            $document->status_id = DOCUMENT_REGISTERED;
            $document->user_id = backpack_user()->id;
            $document->valid_from = $registry_date;
            if ($document->save()) {
                $document->barcode = empty($document->barcode) ? "{$request->id}#{$document->id}#{$document->registry_number}#{$document->registry_date}" : $document->barcode;
                $document->documentable()->associate($request);
                $document->metadata = json_encode([
                    "title" => "{$document->documentCategory->name} za $request->request_category_id #{$request->id}",
                    "author" => $request->osoba->ime_roditelj_prezime,
                    "author_id" => $request->osoba->lib,
                    "description" => "",
                    "category" => ucfirst($request->requestCategory->name),
                    "created_at" => $registry_date,
                ], JSON_UNESCAPED_UNICODE);
                $document->save();
                $documentOK = TRUE;
            }
            if ($registryOK && $documentOK) {
                $result['registerDocumentOK'] = TRUE;
            }
        } else {
            //ako je dokument zaveden onda je sve ok
            $result['registerDocumentOK'] = TRUE;
        }
        $result['data'] = array(
            'category' => $document->documentCategory->name,
            'osoba' => $request->osoba->getImeRoditeljPrezimeAttribute(),
            'id' => $request->id,
            'registry_number' => $document->registry_number,
            'registry_date' => Carbon::parse($document->registry_date)->format('d.m.Y.'),
        );
        return $result;
    }

    /**
     * Generisi PDF.
     *
     * @return string|\Illuminate\Http\Response
     */
    public
    function zavodneNalepnicePDF($data)
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
    public
    function nalepnicePDF()
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
    public
    function prijavaPDF($prijava_id, $type = 'stream')
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
