<?php


namespace App\Libraries;

use App\Models\Osoba;
use App\Models\Lib;
use App\Models\LogLib;
use Illuminate\Support\Facades\DB;
use Tesla\JMBG\JMBG;
use Exception;

//require_once('novisajt/classes/Base.php');
//require_once('novisajt/classes/jmbg.class.php');

/**
 * @author MishaA
 * @author Marbo
 * Klasa za dodeljivanje LIB broja clanu Komore
 * Verzija 1.1, od 22.05.2013.god.
 * Verzija 2.0 od 01.04.2020.god.
 */
class LibLibrary {
    var $LOG_ERROR = 'E';
    var $LOG_INFO = 'I';
    var $LOG_WARNING = 'W';
    var $userid;
    var $error;

    /** Interfejsna metoda klase Lib
     * @param instanca db klase Base
     * @param maticni broj $jmbg
     * @return string|NULL ukliko je doslo do greske u setovanju, u promenljivoj error cuva se tekst greske koji je takodje otisao i tu tlib_log
     */
    public function dodeliJedinstveniLib($jmbg, $userid) {

        $this->userid = $userid;
        $this->error = "";
        /*kontrola $jmbg koriscenjem eksternog modula - klase jmbg.class.php*/
        $jmbgO = new JMBG($jmbg);
        if (!($jmbgO->isValid())) {
            //log za gresku JMBG nije validan prema pravilima formiranja JMBG-a
            $this->log_lib($userid, $this->LOG_ERROR, "JMBG $jmbg nije validan prema pravilima formiranja JMBG-a");
            return null;
        }
//        1. zapocni transakciju nad bazom podataka i zaključaj pravo čitanja tabele tlib za sve korisnike
        DB::beginTransaction();

//        2. utvrdi da li osoba sa jmbg ima vec setovan jmbg i ako ima setovanje nece biti ostvareno
        $libPostojeci = $this->getLib($jmbg);
//        dd($libPostojeci);
        /*        if ($libPostojeci == -1) {
                    DB::rollBack();
                    return null;
                }*/

        if (!$libPostojeci) {

//            3. utvrdi mmdd iz maticnog broja
            $ddmm = $this->getMM($jmbg);

//            4. utvrdi sss iz tabele tlib - sss je sekvencni broj koji se vodi za svaki mmdd i dodaj broju vodece nule do pozicije 3
            $sss = $this->getNextSSS($ddmm);
            if (!$sss) {
                DB::rollback();
                return null;
            }
//            5. utvrdi pol iz jmbg  (definisano specifikacijom nacina formiranja lib-a)
            $pol = $this->getPol($jmbg);
//            6. utvrti godinu rodjenja iz jmbg-a*/
            $gg = $this->getGG($jmbg);
//            7. utvrdi LIB broj  (definisano specifikacijom nacina formiranja lib-a)
            $lib = $this->makeLib($ddmm, $gg, $pol, $sss);
//            8. azuriraj LIB broj clana
            try {
                Osoba::where('id', $jmbg)->update(['lib' => $lib]);
//            $q = "UPDATE tosoba SET lib='$lib' WHERE id='$jmbg'";
            } catch (\Exception $e) {
                DB::rollback();
                //log za gresku u setovanju lib-a za osobu sa jmbg-om
                $this->log_lib($userid, $this->LOG_ERROR, "Greska u setovanju lib-a za $jmbg u tabeli tosoba, polje lib: " . $e->getMessage());
                return null;
            }
            //log za upravo azurirani lib
            if (!$this->log_lib($userid, $this->LOG_INFO, "Za osobu $jmbg dodeljen lib: $lib")) {
                DB::rollback();
                return null;
            }
//            9. Zavri transakciju nad bazom i otključaj pravo čitanja tabele tlib
            DB::commit();
//            10. Vrati rezultat - generisani LIB
            return $lib;
        } else {
            //log za pokusaj setovanja liba kada vec postoji
            DB::rollback();
            if (!$this->log_lib($userid, $this->LOG_WARNING, "Za osobu $jmbg vec dodeljen lib: $libPostojeci")) {
                return null;
            }

            return $libPostojeci;
        }
    }

    /**Formira Lib sa kontrolnim brojem na osnovu definisanog pravila
     * @param ddmm - podatak prema specifikaciji LIB-a clana Komore $ddmm
     * @param gg - podatak prema specifikaciji LIB-a clana Komore $gg
     * @param pol - podatak prema specifikaciji LIB-a clana Komore $pol
     * @param sss - podatak prema specifikaciji LIB-a clana Komore $sss
     * @return string
     */
    private function makeLib($ddmm, $gg, $pol, $sss) {
        //MMPGGSSSDDK
        $mm = substr($ddmm, 2, 2);
        $dd = substr($ddmm, 0, 2);
        $libBezKontrolne = $mm . $pol . $gg . $sss . $dd;
        $k = $this->izracunajKontrolniBroj($libBezKontrolne);
        return $libBezKontrolne . $k;
    }

    /**Izracunava kontrolni broj LIB-a
     * @param libBezKontrolne - LIB clana Komore bez kontrolnog broja libBezKontrolne
     * @return string
     */
    private function izracunajKontrolniBroj($libBezKontrolne) {
        //L = 11 - (( 7*(A+Đ) + 6*(B+E) + 5*(V+) + 4*(G+Z) + 3*(D+I)) % 11)
        ////MMPGGSSSDD
        ////ABVGDĐEZIL
        $a = (int)substr($libBezKontrolne, 0, 1);
        $b = (int)substr($libBezKontrolne, 1, 1);
        $v = (int)substr($libBezKontrolne, 2, 1);
        $g = (int)substr($libBezKontrolne, 3, 1);
        $d = (int)substr($libBezKontrolne, 4, 1);
        $dj = (int)substr($libBezKontrolne, 5, 1);
        $e = (int)substr($libBezKontrolne, 6, 1);
        $zz = (int)substr($libBezKontrolne, 7, 1);
        $z = (int)substr($libBezKontrolne, 8, 1);
        $i = (int)substr($libBezKontrolne, 9, 1);
        $l = 11 - ((7 * ($a + $dj) + 6 * ($b + $e) + 5 * ($v + $zz) + 4 * ($g + $z) + 3 * ($d + $i)) % 11);
        //ako je kontrolna cifra između 1 i 9, ostaje ista (L = K), a  ako je kontrolna cifra veća od 9, postaje nula (L = 0)
        if ($l > 9)
            $l = 0;
        return $l;
    }

    /**Izvlaci podatak gg prema specifikaciji
     * @param maticni broj $jmbg
     * @return string
     */
    private function getGG($jmbg) {
        return substr($jmbg, 5, 2);
    }


    /**Izvlaci podatak pol prema specifikaciji
     * @param jmbg - maticni broj
     * @return string
     */
    private function getPol($jmbg) {
        $jmbgJedinstveniBroj = substr($jmbg, 9, 3);
        if ((int)$jmbgJedinstveniBroj < 500)
            return "0";
        else
            return "5";
    }

    /**Izvlaci podatak mm prema specifikaciji
     * @param jmbg - maticni broj
     * @return string
     */
    private function getMM($jmbg) {
        return substr($jmbg, 0, 4);
    }

    /**Generise podatak sss prema specifikaciji
     * @param db - instanca klase Base
     * @param ddmm - podatak prema specifikaciji LIB-a clanova Komore $ddmm
     * @return string|NULL ukoliko je doslo do greske
     */
    private function getNextSSS($ddmm) {
        try {
            Lib::where('ddmm', $ddmm)->update(['sss' => DB::raw('sss+1')]);
//        $q = "UPDATE tlib SET sss=sss+1 WHERE ddmm='$ddmm'";
        } catch (\Exception $e) {
            //log za gresku u setovanju novog sss broja
            $this->log_lib($this->userid, $this->LOG_ERROR, "Za ddmm - $ddmm nije moguce azurirati sss, greska: " . $e->getMessage());
            return null;
        }
        try {
            $sss = Lib::where('ddmm', $ddmm)->select('sss')->first()->toArray()['sss'];
        } catch (\Exception $e) {
            //log za gresku u citanju sss broja
            $this->log_lib($this->userid, $this->LOG_ERROR, "Nije moguce procitati sss za ddmm - $ddmm, greska: " . $e->getMessage());
            return null;
        }
//        $row = $db->getRow();
//        return str_pad((int)$row[0], 3, "0", STR_PAD_LEFT);
        return str_pad((int)$sss, 3, "0", STR_PAD_LEFT);
    }

    /**Izvlaci postojeci lib broj za clana sa jmbg
     * @param db - Instanca klase Base
     * @param jmbg - maticni broj jmbg
     * @return LibLibrary broj clana Komore|NULL ukoliko lib nije setovan|-1 ukoliko je doslo do greske
     */
    public function getLib($jmbg) {
        try {
            $lib = Osoba::where('id', $jmbg)->whereNotNull('lib')->firstOrFail();
        } catch (\Exception $e) {
            $this->log_lib($this->userid, $this->LOG_WARNING, "Ne postoji lib za $jmbg u tabeli tosoba");
//            dd($e->getMessage());
            return false;
        }
        return $lib->lib;
    }

    /**
     * @param $db - Instanca klase Base $db
     * @param $userid - identifikator korisnika sistema IKS
     * @param $level - nivo LOG-a (LOG_ERROR/LOG_INFO/LOG_WARNING)
     * @param $text - text log-a
     * @return NULL ukoliko nije azuriran LOG| 1 ukoliko jeste
     */
    private function log_lib($userid, $level, $text) {
        if ($level == $this->LOG_ERROR)
            $this->error = $text;
        try {
            $logLib = new LogLib();
            $logLib->date = now();
            $logLib->userid = $userid;
            $logLib->level = $level;
            $logLib->text = $text;
            $logLib->save();
        } catch (Exception $e) {
            dd($e->getMessage());
            $this->error = "Greska u insertovanju LOG-a: " . $e->getMessage();
            return null;
        }
        return true;
    }
}

