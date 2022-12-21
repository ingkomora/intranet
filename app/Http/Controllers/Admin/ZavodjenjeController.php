<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentCategoryType;
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
use function Composer\Autoload\includeFile;

class ZavodjenjeController extends Controller
{
    protected $result;
    protected $brprijava;
    protected $zavodjenje = [
        // document_category_id' => [<document_category_id> => <request_category_id>, ...]
        'si' => ['document_category_id' => [10 => 6, 26 => 6], 'registry_type' => 'oblastsi', 'url' => 'si', 'statusRel' => 'status', 'statusCol' => 'status_prijave', 'model' => 'SiPrijava', 'title' => 'Zavođenje prijava za polaganje stručnog ispita'],
        'licence' => ['document_category_id' => [5, 25], 'registry_type' => 'oblast', 'url' => 'licence', 'statusRel' => 'statusId', 'statusCol' => 'status', 'model' => 'ZahtevLicenca', 'title' => 'Zavođenje zahteva za izdavanje licenci'],
        'clanstvo' => ['document_category_id' => [1 => 1, 27 => 1, 2 => 2, 28 => 2, 37 => 12, 39 => 12, 38 => 13, 40 => 13], 'registry_type' => 'sekcija', 'url' => 'clanstvo', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje zahteva za članstvo'],
        'mirovanjeclanstva' => ['document_category_id' => [3 => 4, 4 => 5, 29 => 4, 30 => 5], 'registry_type' => 'sekcija', 'url' => 'mirovanjeclanstva', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje zahteva za mirovanje'],
        'sfl' => ['document_category_id' => 6, 'registry_type' => '02', 'url' => 'sfl', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje zahteva za izdavanje svečane forme licence'],
        'resenjeclanstvo' => ['document_category_id' => [12 => 2, 13 => 2], 'registry_type' => 'sekcija', 'url' => 'resenjeclanstvo', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje rešenja o prestanku i brisanju iz članstva'],
        'registrydataupdate' => ['document_category_id' => [7 => 9], 'registry_type' => '01', 'url' => 'registrydataupdate', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje zahteva za promenu podataka upisanih u Registar'],
        'registryuverenje' => ['document_category_id' => [9 => 8], 'registry_type' => '01', 'url' => 'registryuverenje', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje zahteva za izdavanje uverenja o podacima upisanim u Registar'],
        'registrydatadelete' => ['document_category_id' => [36 => 11, 43 => 11, 41 => 14, 44 => 14], 'registry_type' => '01', 'url' => 'registrydatadelete', 'statusRel' => 'status', 'statusCol' => 'status_id', 'model' => 'Request', 'title' => 'Zavođenje zahteva za brisanje podataka upisanih u Registar'],
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
            if ($prijava->status_prijave == REQUEST_SUBMITED) {
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
                $prijava->status_prijave = REQUEST_IN_PROGRESS;
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
     * ZAVODJENJE ZAHTEVA ZA DOBIJANJE LICENCI
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeLicence(Request $request)
    {
        $request->prijave = trim($request->prijave);
        $this->brprijava = array_map('intval', explode("\r\n", $request->prijave));
        $prijave = SiPrijava::whereIn('id', $this->brprijava)->orderBy('id', 'asc')->get();
        foreach ($prijave as $prijava) {
//            ZAVEDI SAMO PRIJAVE KOJE SU GENERISANE
            if ($prijava->status_prijave == REQUEST_SUBMITED) {
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
                $prijava->status_prijave = REQUEST_IN_PROGRESS;
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
        $prilog = [];
        $data['result'] = [];
        $log = new Log();
        $log->type = 'INFO';
        $documentsOK = TRUE;
        $requestOK = FALSE;
        $nameSpace = 'App\Models\\';
        $model = $nameSpace . $this->zavodjenje[$type]['model'];
        $requestStatusRelationName = $this->zavodjenje[$type]['statusRel'];
        $requestStatusColumnName = $this->zavodjenje[$type]['statusCol'];
        $ids = array_map('trim', $data['entries']);
        $registry_date = (!empty($data['registry_date'])) ? Carbon::parse($data['registry_date'])->format('Y-m-d') : now()->toDateString();
        $this->brprijava = $ids;
        $requests = $model::whereIn('id', $ids)->orderBy('id', 'asc')->get();
        if (isset($data['prilog_text'])) {
            if (count($ids) > 1) {
                $result['ERROR'][1] = "Greška 1! Zavođenje dopune je moguće samo za jedan izabrani zahtev";
                return $result;
            } else {
                if (isset($data['prilog'])) {
                    $prilog['text'] = $data['prilog_text'];
                }
            }
        }
        foreach ($requests as $request) {
            try {
                $existingDocuments = $request->documents;
                if (in_array($request->{$requestStatusColumnName}, [REQUEST_BOARD, ZALBA, PONISTEN, ZALBA_ODUSTAO, ZALBA_MGSI])) {
                    // da moze da se zavede
                } else if ($request->{$requestStatusColumnName} < REQUEST_SUBMITED) {
                    $result['ERROR'][1] = "Greška 2! Zahtev $request->id ima status " . $request->{$requestStatusRelationName}->naziv . ", Zavođenje je moguće samo za zahteve koji su podneti!";
                    return $result;
                } else if ($request->{$requestStatusColumnName} > REQUEST_IN_PROGRESS) {
                    $result['ERROR'][1] = "Greška 2.5! Zahtev $request->id ima status " . $request->{$requestStatusRelationName}->naziv . ", Zavođenje je moguće samo za zahteve koji su u obradi!";
                    return $result;
                }
                if (is_array($this->zavodjenje[$type]['document_category_id'])) {
                    $document_category_ids = array_keys($this->zavodjenje[$type]['document_category_id'], $request->request_category_id);
                    if (empty($document_category_ids)) {
                        $document_category_ids = $this->zavodjenje[$type]['document_category_id'];
                    }
                } else {
                    $document_category_ids = (array)$this->zavodjenje[$type]['document_category_id'];
                }
                if (isset($data['prilog_text'])) {
//                        ZAVEDI SAMO DOPUNU
                    if (strlen($data['prilog_text']) > 90) {
                        $result['ERROR'][1] = "Greška 4! Naziv dopune ne sme imati više od 90 karaktera!";
                        return $result;
                    }
                    $categoryDopuna = DocumentCategory::whereIn('id', $document_category_ids)->where('document_category_type_id', 11)->pluck('id')->toArray();
                    $temp = array_filter($document_category_ids, function ($id) use ($categoryDopuna) {
                        return in_array($id, $categoryDopuna);
                    });
                    $document_category_id = reset($temp);
                } else if (isset($data['prilog'])) {
                    if (!in_array($request->{$requestStatusColumnName}, [REQUEST_IN_PROGRESS, REQUEST_FINISHED, REQUEST_CANCELED, REQUEST_BOARD, ZALBA, PONISTEN, ZALBA_ODUSTAO, ZALBA_MGSI])
                    ) {

                        $result['ERROR'][1] = "Greška 3! Zavođenje dopune je moguće samo za zahtev koji je prethodno zaveden!";
                        return $result;
                    }
                    $categoryDopuna = DocumentCategory::whereIn('id', $document_category_ids)->where('document_category_type_id', 11)->pluck('id')->toArray();
                    $temp = array_filter($document_category_ids, function ($id) use ($categoryDopuna) {
                        return in_array($id, $categoryDopuna);
                    });
                    $document_category_id = reset($temp);
                } else {
//                    ZAVEDI ILI ODSTAMPAJ SAMO ZAHTEV
                    $category = DocumentCategory::whereIn('id', $document_category_ids)->where('document_category_type_id', '<>', 11)->pluck('id')->toArray();
                    $temp = array_filter($document_category_ids, function ($id) use ($category) {
                        return in_array($id, $category);
                    });
                    $document_category_id = reset($temp);
                }
                $documents = [];
                if ($existingDocuments->isNotEmpty()) {
                    if (isset($prilog['text'])) {
                        $filteredExistingDocuments = $existingDocuments->filter(function ($document, $key) use ($document_category_id) {
                            return $document->document_category_id == $document_category_id and ($document->status_id == DOCUMENT_CREATED or $document->status_id == DOCUMENT_REGISTERED) and $document->documentCategory->document_category_type_id <> 11; // ako je setovan prilog text onda se zavodi nova dopuna
                        });
                    } else if (isset($data['prilog'])) {
                        $filteredExistingDocuments = $existingDocuments->filter(function ($document, $key) use ($document_category_id) {
                            return $document->document_category_id == $document_category_id and ($document->status_id == DOCUMENT_CREATED or $document->status_id == DOCUMENT_REGISTERED or $document->status_id == DOCUMENT_CANCELED) and $document->documentCategory->document_category_type_id == 11; // ako je setovan prilog text onda se zavodi nova dopuna
                        });
                    } else {
                        $filteredExistingDocuments = $existingDocuments->filter(function ($document, $key) use ($document_category_id) {
                            return $document->document_category_id == $document_category_id and ($document->status_id == DOCUMENT_CREATED or $document->status_id == DOCUMENT_REGISTERED or $document->status_id == DOCUMENT_CANCELED);
                        });
                    }
                    if (($filteredExistingDocuments)->count() > 1) {

                        $dopunaCategory = DocumentCategory::where('document_category_type_id', 11)->pluck('id')->toArray();
                        if (!in_array($document_category_id, $dopunaCategory)) {
                            //GRESKA IMA VISE OD JEDNOG ZAHTEVA ALI MOZE VISE PRILOGA
//                    echo "ima vise od jednog zahteva";
                            $result['ERROR'][$request->id] = "Greška 5! Ima više od jednog dokumenta za kategoriju $document_category_id. Kontaktirajte službu za informacione tehnologije";
                            return $result;
                        } else {
                            foreach ($filteredExistingDocuments as $document) {
                                $documents[] = $document;
                            }
                        }
                    } else if ($filteredExistingDocuments->count() > 0) {
                        $document = $filteredExistingDocuments->first();
                        $documents[] = $document;
                    } else {
                        $document = new Document();
                        $document->document_category_id = $document_category_id;
                        $document->status_id = DOCUMENT_CREATED;
                        $documents[] = $document;
                    }
                } else {
                    if ($request->{$requestStatusColumnName} >= REQUEST_IN_PROGRESS or in_array($request->{$requestStatusColumnName}, [REQUEST_BOARD, ZALBA, PONISTEN, ZALBA_ODUSTAO, ZALBA_MGSI])) {
                        $result['ERROR'][1] = "Greška 9! Zahtev $request->id ima status \"" . $request->{$requestStatusRelationName}->naziv . "\", a nema evidentiranih dokumenata! Kontaktirajte službu za informacione tehnologije";
                        return $result;
                    } else {
                        $document = new Document();
                        $document->document_category_id = $document_category_id;
                        $document->status_id = DOCUMENT_CREATED;
                        $documents[] = $document;
                    }
                }
                DB::beginTransaction();
//                dd($documents);
                foreach ($documents as $document) {
//                dd($document);
                    $resultDocument = $this->registerDocument($request, $document, $registry_date, $type, $prilog);
                    if (isset($result['ERROR'])) {
                        if ($resultDocument['ERROR'][$request->id]['status']) {
                            DB::rollBack();
                            $result['ERROR'][$request->id] = $resultDocument['ERROR'][$request->id]['message'];
                            return $result;
                        }
                    }
                    array_push($data['result'], $resultDocument['data']);
                    $documentsOK &= $resultDocument['registerDocumentOK'];
                }
                if ($request->{$requestStatusColumnName} == REQUEST_SUBMITED) {
                    $request->{$requestStatusColumnName} = REQUEST_IN_PROGRESS;
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
                    $result['ERROR'][$request->id] = "Greška 7! ROLLBACK: Nije snimljen";
                }
                $result['category'] = ucfirst($resultDocument['requestCategory']);
                $result[$log->type][$request->id] = $resultDocument['osoba']->getImeRoditeljPrezimeAttribute();
            } catch
            (\Exception $e) {
                DB::rollBack();
                $result['ERROR'][$request->id] = "Greška 8! {$e->getMessage()}<br>Greška prilikom zavođenja dokumenta.<br>Kontaktirajte službu za informacione tehnologije";
                return $result;
            }
        }
        $result['pdf'] = $this->zavodneNalepnicePDF($data);
        return $result;
    }

    /**
     * @param $request
     * @param Document $document
     * @param $registry_date
     * @param $type
     * @param $prilog
     * @return array|\Illuminate\Http\RedirectResponse
     */
    protected function registerDocument($request, $document, $registry_date, $type, $prilog)
    {
        $registerDocument = FALSE;
        $result = array();
        $registryOK = $documentOK = FALSE;
        if ($type == 'licence') {
            $osoba = $request->osobaId;
        } else {
            $osoba = $request->osoba;
        }
        if ($request->{$this->zavodjenje[$type]['statusCol']} == REQUEST_IN_PROGRESS or in_array($request->{$this->zavodjenje[$type]['statusCol']}, [REQUEST_BOARD, ZALBA, PONISTEN, ZALBA_ODUSTAO, ZALBA_MGSI])) {
            if ($document->documentCategory->document_category_type_id == 11 and isset($prilog['text'])) {
                $registerDocument = TRUE;
            }
        }
        if ($request->{$this->zavodjenje[$type]['statusCol']} == REQUEST_SUBMITED and $document->status_id == DOCUMENT_CREATED and is_null($document->registry_number) and is_null($document->registry_date)) {
            $registerDocument = TRUE;
        }
        if ($registerDocument) {
            if ($this->zavodjenje[$type]['registry_type'] == 'sekcija') {
                $label = '02-' . $osoba->zvanjeId->zvanje_grupa_id;
            } else if ($this->zavodjenje[$type]['registry_type'] == 'oblast') {
                if ($request->vrsta_posla_id == 5) {
                    $label = '02-E';
                } else {
                    $label = '02-' . $request->reg_oblast_id;
                }
            } else if ($this->zavodjenje[$type]['registry_type'] == 'oblastsi') {
                if ($request->vrsta_posla_id == 5) {
                    $label = '09-E';
                } else {
                    $label = '09-' . $request->reg_oblast_id;
                }
            } else {
                $label = $this->zavodjenje[$type]['registry_type'];
            }
            $registry = Registry::where('status_id', AKTIVAN)
                ->whereHas('registryDepartmentUnit', function ($q) use ($label) {
                    $q->where('label', "$label");
                })
                ->whereHas('requestCategories', function ($q) use ($request, $document) {
                    $q->where('registry_request_category.request_category_id', $request->request_category_id) // prijem u clanstvo
                    ->where('registry_request_category.document_category_id', $document->document_category_id) // odluka
                    ;
                })
                ->get();
//            dd($registry);
            if ($registry->count() != 1) {
                $result['ERROR'][$request->id]['status'] = TRUE;
                $result['ERROR'][$request->id]['message'] = "Greška 6! \nGreška prilikom odabira skraćenog delovodnika. Kontaktirajte službu za informacione tehnologije";
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
                    "title" => "{$document->documentCategory->name} za {$request->requestCategory->name} #{$request->id}",
                    "author" => $osoba->ime_roditelj_prezime,
                    "author_id" => $osoba->lib,
                    "description" => "",
                    "dopuna" => isset($prilog['text']) ? $prilog['text'] : '',
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
        $metadata = json_decode($document->metadata);
        $dopuna = $metadata->dopuna ?? '';
        $result['data'] = array(
            'category' => $document->documentCategory->name,
            'osoba' => $osoba->getImeRoditeljPrezimeAttribute(),
            'id' => $request->id,
            'registry_number' => $document->registry_number,
            'registry_date' => Carbon::parse($document->registry_date)->format('d.m.Y.'),
//            'prilog' => $prilog['text'],
            'prilog' => $dopuna ?? '/',
            'prilogilidopuna' => $document->documentCategory->document_category_type_id == 11 ? 'Dopuna' : 'Prilog'
        );
        $result['osoba'] = $osoba;
        $result['requestCategory'] = $request->requestCategory->name;

        return $result;
    }

    /**
     * Generisi PDF.
     *
     * @return string|\Illuminate\Http\Response
     */
    public function zavodneNalepnicePDF($data)
    {
        $data['oblast'] = '';
        $filename = date("Ymd") . "_test";
//        dd($data);
//        $pdf = PDF::loadView('nalepniceRamipa89x43', $data);
        $customPaper = array(0, 0, 143, 284.2); // 10cm x 5cm rolna
        $pdf = PDF::loadView('citizen100x50', $data)->setPaper($customPaper, 'landscape');
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
