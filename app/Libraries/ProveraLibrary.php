<?php


namespace App\Libraries;

use App\Models\Clanarina;
use App\Models\Licenca;
use App\Models\Osiguranje;
use App\Models\Osoba;
use App\Models\ZahtevLicenca;
use App\Models\EvidencijaMirovanja;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Sabberworm\CSS\Property\Selector;
use Tesla\JMBG\JMBG;
use Exception;

/**
 * @author Marbo
 * Klasa za razne provere
 * Verzija 1.0, od 20.05.2020.god.
 */
class ProveraLibrary {

    protected $jmbg;
    protected $licenca;
    protected $zahtev;


    /**
     * @param $licenca
     */
    public function statusNoveLicence(ZahtevLicenca $zahtev) {
        if ($zahtev->exists) {
            $this->setZahtev($zahtev);
            $this->setJmbg($zahtev->osoba);
            $jmbg = $zahtev->osoba;

        } else {
            //nema zahteva ili nije validan
            return false;
        }
        $this->setLicenca($zahtev->licenca);
        if ($this->checkOsiguranje()) {
            $osiguranje = true;
        } else {
            $osiguranje = false;
        }
        $message = "";
        $message .= "<br>JMBG: $this->jmbg";
        $message .= "<br>LICENCA: $zahtev->licenca_broj";
        if ($this->checkClan($jmbg)) {
            $message .= "<br>OSOBA JE ČLAN KOMORE...";
//            AKO JE CLAN
            if ($this->checkMirovanje($jmbg)) {
                $message .= "<br>U MIROVANJU...";
//              AKO JE U MIROVANJU
                if ($osiguranje) {
                    $message .= "<br>ALI IMA OSIGURANJE (AKTIVNA)";
//                 ALI IMA OSIGURANJE
                    $result = true;
                } else {
                    $message .= "<br>... I NEMA OSIGURANJE (NEAKTIVNA)";
//            ... I NEMA OSIGURANJE
                    $result = false;
                }
            } else {
                $message .= "<br>... NIJE U MIROVANJU...";
//              AKO NIJE U MIROVANJU
                if ($this->checkClanarina($jmbg)) {
                    $message .= "<br>... ALI PLATIO JE CLANARINU (AKTIVNA)";
//            ... ALI PLATIO JE CLANARINU
                    $result = true;
                } else {
                    $message .= "<br>... NIJE PLATIO CLANARINU...";
//              AKO NIJE PLATIO CLANARINU
                    if ($osiguranje) {
                        $message .= "<br>... ALI IMA OSIGURANJE (AKTIVNA)";
//              ALI IMA OSIGURANJE
                        $result = true;
                    } else {
                        $message .= "<br>... I NEMA OSIGURANJE (NEAKTIVNA)";
//            ... I NEMA OSIGURANJE
                        $result = false;
                    }
                }
            }
        } else {
            $message .= "<br>OSOBA NIJE ČLAN KOMORE...";
//            AKO NIJE CLAN
            if ($osiguranje) {
                $message .= "<br>... ALI IMA OSIGURANJE (AKTIVNA)";
//              AKO IMA OSIGURANJE
                $result = true;
            } else {
                $message .= "<br>... I NEMA OSIGURANJE (NEAKTIVNA)";
//            ... I NEMA OSIGURANJE
                $result = false;
            }
        }
        //        $message .= $message;
        return $result;
    }

    /**
     * @param $licenca
     */
    public function statusLicence(Licenca $licenca) {
        if ($licenca->exists) {
            $this->setLicenca($licenca);
            $this->setJmbg($licenca->osoba);
            $jmbg = $licenca->osoba;
        } else {
            //nema licence ili nije validan
            return false;
        }
//        $this->setZahtev($licenca->zahtevId);
        if ($this->checkOsiguranje()) {
            $osiguranje = true;
        } else {
            $osiguranje = false;
        }
        $message = "";
        $message .= "<br>JMBG: $this->jmbg";
        if ($this->checkClan($jmbg)) {
            $message .= "<br>OSOBA JE ČLAN KOMORE...";
//            AKO JE CLAN
            if ($this->checkMirovanje($jmbg)) {
                $message .= "<br>U MIROVANJU...";
//              AKO JE U MIROVANJU
                if ($osiguranje) {
                    $message .= "<br>ALI IMA OSIGURANJE (AKTIVNA)";
//                 ALI IMA OSIGURANJE
//            dd($message);
                    $result = true;
                } else {
                    $message .= "<br>... I NEMA OSIGURANJE (NEAKTIVNA)";
//            ... I NEMA OSIGURANJE
                    $result = false;
                }
            } else {
                $message .= "<br>... NIJE U MIROVANJU...";
//              AKO NIJE U MIROVANJU
                if ($this->checkClanarina($jmbg)) {
                    $message .= "<br>... ALI PLATIO JE CLANARINU (AKTIVNA)";
//            ... ALI PLATIO JE CLANARINU
                    $result = true;
                } else {
                    $message .= "<br>... NIJE PLATIO CLANARINU...";
//              AKO NIJE PLATIO CLANARINU
                    if ($osiguranje) {
                        $message .= "<br>... ALI IMA OSIGURANJE (AKTIVNA)";
//              ALI IMA OSIGURANJE
                        $result = true;
                    } else {
                        $message .= "<br>... I NEMA OSIGURANJE (NEAKTIVNA)";
//            ... I NEMA OSIGURANJE
                        $result = false;
                    }
                }
            }
        } else {
            $message .= "<br>OSOBA NIJE ČLAN KOMORE...";
//            AKO NIJE CLAN
            if ($osiguranje) {
                $message .= "<br>... ALI IMA OSIGURANJE (AKTIVNA)";
//              AKO IMA OSIGURANJE
                $result = true;
            } else {
                $message .= "<br>... I NEMA OSIGURANJE (NEAKTIVNA)";
//            ... I NEMA OSIGURANJE
                $result = false;
            }
        }
//        echo $message;

        return $result;
    }

    /**
     * @param $licenca
     */
    public function getJmbgFromLicenca($licenca) {
        $this->setLicenca($licenca);
        $this->jmbg = Licenca::find($licenca)->osoba;
        return $this->jmbg;
    }

    /**
     * @param $licenca
     */
    public function getJmbgFromZahtev($zahtev) {
        $this->setZahtev($zahtev);
        $this->jmbg = ZahtevLicenca::find($zahtev)->osoba;
        return $this->jmbg;
    }

    public function checkClan($jmbg) {

        $clan = Osoba::find($jmbg)->clan;
//        $clan = DB::table('tosoba')->where('id',$jmbg)->value('clan');
//        var_dump($clan);
        if ($clan == 1) {
            $result = true;
        } else {
            $result = false;
        }
        unset($clan);
        return $result;
    }

    public function checkMirovanje($jmbg) {

        $mirovanje = EvidencijaMirovanja::whereNull('datumprestanka')->whereRaw('datumkraja >= now()::date')->where('osoba', $jmbg)->first();

        if ($mirovanje) {
            $result = true;
        } else {
            $result = false;
        }
        unset($mirovanje);
        return $result;
    }

    public function checkClanarina($jmbg) {

        $clanarina = DB::table('tclanarinaod2006')
            ->join('tlicenca', 'tlicenca.osoba', '=', 'tclanarinaod2006.osoba')
            ->distinct()
            ->select('tclanarinaod2006.osoba')
            ->whereRaw('tclanarinaod2006.rokzanaplatu >= (now())::date')
            ->where('tlicenca.status', '<>', 'D')
            ->where('tclanarinaod2006.osoba', $jmbg)
            ->get();
        if ($clanarina->isNotEmpty()) {
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    public function checkOsiguranje() {

//        DA LI LICENCA IMA AKTIVAN STATUS PREMA POKRICU
        $osiguranja = Osiguranje::where('id','<>', 1)
            ->whereHas('osobe', function (Builder $query) {
            $query->where('id', $this->jmbg);
        })->get();
//        $osiguranje = $osoba->osiguranjaOsobe;
//                    var_dump($this->licenca->tipLicence->sekcija);

        if ($osiguranja->isNotEmpty()) {
            //da li ga ima u tabeli osiguranje_osoba
//            echo "<br>Osoba: $osoba->id";
            foreach ($osiguranja as $osiguranje) {

                //datum zavrsetka  veci od danas
                if ($osiguranje->validnaPolisa()) {
                    //da li je aktivna polisa

//                    echo "<br>pokrice: $osiguranje->polisa_pokrice_id,  " . $osiguranje->polisaPokrice->naziv . ", Osiguranje: $osiguranje->polisa_broj, " . $osiguranje->firmaUgovarac->naziv;

//                    VRSTA LICENCE I OSIGURANJE POLISA POKRICE
                    $vrsta = array(
                        1 => array(0, 1, 6),
                        2 => array(0, 2, 6),
                        3 => array(0, 3, 5, 6),
                        4 => array(0, 4, 5),
                    );
//                    dd($this->licenca);
                    if (is_null($this->licenca)) {
                        if (is_null($this->zahtev->tipLicence)) {
                            return false;
                        }
                        $sekcija = $this->zahtev->tipLicence->sekcija;
                    } else {
                        if (is_null($this->licenca->tipLicence)) {
                            return false;
                        }
                        $sekcija = $this->licenca->tipLicence->sekcija;
                    }
                    if (in_array($osiguranje->polisa_pokrice_id, $vrsta[$sekcija])) {
                        //polisa pokrice tj. koje licence su pokrivene ovom polisom
                        return true;
                    }
                }
            }
        }
//        dd($osoba->osiguranja);
//        dd($osiguranje->validnaPolisa());
        return false;
    }

    /**
     * @param $licenca
     */
    protected function setLicenca($licenca) {
        $this->licenca = $licenca;
    }

    /**
     * @param $zahtev
     */
    protected function setZahtev($zahtev) {
        $this->zahtev = $zahtev;
    }

    /**
     * @param $jmbg
     */
    protected function setJmbg($jmbg) {
        $this->jmbg = $jmbg;
    }
}

