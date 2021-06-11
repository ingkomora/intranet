<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PDF;
use DNS1D;


class OutputController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function downloadPDF(\stdClass $data, $type = 'svecanaforma') {

//        $data['nav'] = $this->nav;
//        dd($data);

//        $prijava->status_prijave = PRIJAVA_GENERISANA;
//        $prijava->save();
//        setlocale(LC_CTYPE, 'sr_RS.utf8@latin');
        $filename = date("Ymd") . "_" . $data->osobaImeRPrezime;
//        $filename = date("Ymd") . "_" . $prijava->id . "_" . $prijava->osoba->ime . "_" . iconv('UTF-8','ISO-8859-1//TRANSLIT',$prijava->osoba->prezime);
//        dd($filename);
        $pdf = PDF::loadView('svecanaforma', (array)$data);
//        $pdf = PDF::loadView('pdf', compact('prijava'));
//        $this->sendEmail($prijava);
//        $prijava->sendWelcomeEmail($prijava->osoba);
            return $pdf->download("$filename.pdf");
    }


}
