<?php


namespace App\Libraries;


use App\Models\Licenca;
use App\Models\LicencaTip;
use App\Models\Osoba;
use App\Models\PrijavaClanstvo;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class Helper
 * @package App\Libraries
 */
class Helper
{

    protected $o;       //osobe
    protected $l;       //licence
    protected $a;       //aktivne licence
    protected $n;       //neaktivne licence
    protected $a2n;     //broj aktivnih -> neaktivne
    protected $a2nl;    //aktivnih -> neaktivne
    protected $n2a;     //broj neaktivnih -> aktivne
    protected $n2al;    //neaktivne ->aktivne
    protected $h;       //broj neaktiviranih/na cekanju
    protected $hl;      //neaktivirane
    protected $h2n;     //broj neaktiviranih -> neaktivne
    protected $h2nl;    //neaktivirane -> neaktivne
    protected $h2a;     //broj neaktiviranih -> aktivne
    protected $h2al;    //neaktivirane -> aktivne
    protected $err;    //greske -> licenca(opis)

    /**
     * @param $text
     * @return string
     */
    public function iso88592_to_cirUTF($text)
    {
        $map = array(
            'A' => 'А', 'a' => 'а',
            'B' => 'Б', 'b' => 'б',
            'V' => 'В', 'v' => 'в',
            'G' => 'Г', 'g' => 'г',
            'D' => 'Д', 'd' => 'д',
            'Đ' => 'Ђ', 'đ' => 'ђ',
            'E' => 'Е', 'e' => 'е',
            'Ž' => 'Ж', 'ž' => 'ж',
            'Z' => 'З', 'z' => 'з',
            'I' => 'И', 'i' => 'и',
            'J' => 'Ј', 'j' => 'ј',
            'K' => 'К', 'k' => 'к',
            'L' => 'Л', 'l' => 'л',
            'LJ' => 'Љ', 'lj' => 'љ',
            'Lj' => 'Љ',
            'M' => 'М', 'm' => 'м',
            'N' => 'Н', 'n' => 'н',
            'NJ' => 'Њ', 'nj' => 'њ',
            'Nj' => 'Њ',
            'O' => 'О', 'o' => 'о',
            'P' => 'П', 'p' => 'п',
            'R' => 'Р', 'r' => 'р',
            'S' => 'С', 's' => 'с',
            'T' => 'Т', 't' => 'т',
            'Ć' => 'Ћ', 'ć' => 'ћ',
            'U' => 'У', 'u' => 'у',
            'F' => 'Ф', 'f' => 'ф',
            'H' => 'Х', 'h' => 'х',
            'C' => 'Ц', 'c' => 'ц',
            'Č' => 'Ч', 'č' => 'ч',
            'Dž' => 'Џ', 'dž' => 'џ',
            'Š' => 'Ш', 'š' => 'ш'
        );

        //echo iconv("cp1251", "UTF-8", strtr($text, $map));
        return strtr($text, $map);
    }

    /**
     * @param $text
     * @return string
     */
    public function cirUTF_to_iso88592($text)
    {
        $map = array(
            'А' => 'A', 'а' => 'a',
            'Б' => 'B', 'б' => 'b',
            'В' => 'V', 'в' => 'v',
            'Г' => 'G', 'г' => 'g',
            'Д' => 'D', 'д' => 'd',
            'Ђ' => 'Đ', 'ђ' => 'đ',
            'Е' => 'E', 'е' => 'e',
            'Ж' => 'Ž', 'ж' => 'ž',
            'З' => 'Z', 'з' => 'z',
            'И' => 'I', 'и' => 'i',
            'Ј' => 'J', 'ј' => 'j',
            'К' => 'K', 'к' => 'k',
            'Л' => 'L', 'л' => 'l',
            'Љ' => 'LJ', 'љ' => 'lj',
            'М' => 'M', 'м' => 'm',
            'Н' => 'N', 'н' => 'n',
            'Њ' => 'NJ', 'њ' => 'nj',
            'О' => 'O', 'о' => 'o',
            'П' => 'P', 'п' => 'p',
            'Р' => 'R', 'р' => 'r',
            'С' => 'S', 'с' => 's',
            'Т' => 'T', 'т' => 't',
            'Ћ' => 'Ć', 'ћ' => 'ć',
            'У' => 'U', 'у' => 'u',
            'Ф' => 'F', 'ф' => 'f',
            'Х' => 'H', 'х' => 'h',
            'Ц' => 'C', 'ц' => 'c',
            'Ч' => 'Č', 'ч' => 'č',
            'Џ' => 'Dž', 'џ' => 'dž',
            'Ш' => 'Š', 'ш' => 'š'
        );

        //echo iconv("cp1251", "UTF-8", strtr($text, $map));
        return strtr($text, $map);
    }

    /**
     * @param $needle
     * @param array $haystack
     * @param string $attribute
     * @return bool
     */
    public function in_array_recursive($needle, array $haystack, string $attribute)
    {
//        echo "<br>test";
//        var_dump($haystack);
        if (!empty($haystack) and is_array($haystack)) {
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

    /**
     * @param $id
     * @return false|string
     */
    public function getPrijavaClan($id)
    {
        $result = new \stdClass();
        $result->status = false;
        $prijavaClan = PrijavaClanstvo::find($id);
//        $prijavaClan = PrijavaClanstvo::where('id', $id)->whereBetween('status_id', [PRIJAVA_CLAN_GENERISANA, PRIJAVA_CLAN_ZAVEDENA])->first();
//        dd($prijavaClan);
        if (is_null($prijavaClan)) {
            $result->message = "Prijava $id ne postoji";
        } else {
            if ($prijavaClan->status_id === PRIJAVA_CLAN_KREIRANA) {
                $result->message = "Prijava $id još nije obrađena, (status: " . $prijavaClan->status->naziv . "), kontaktirajte SIT";
            } else if ($prijavaClan->status_id === PRIJAVA_CLAN_PRIHVACENA) {
                $result->message = "Prijava $id je već obrađena, (odluka $prijavaClan->broj_odluke_uo od $prijavaClan->datum_odluke_uo, zavodni broj $prijavaClan->zavodni_broj)";
            } else {
                $lic = $this->getLicenceOsobe($prijavaClan->osoba->id, 'string');
                if ($lic) {
                    $result->message = "Licence: $lic, ";
                    $result->status = $prijavaClan->status_id;
                } else {
                    $result->message = "Osoba: " . $prijavaClan->osoba->ime . " " . $prijavaClan->osoba->prezime . "($prijavaClan->osoba_id) nema unete licence, ";

                }
//        TODO treba da se obradjuju samo zavedene a to kad se napravi zavodjenje
//                dd($prijavaClan->status_id);
                $result->jmbg = $prijavaClan->osoba_id;
                $result->ime = $prijavaClan->osoba->ime . " " . $prijavaClan->osoba->prezime;
                $result->message .= "Prijava $id ima status: $prijavaClan->status_id -" . $prijavaClan->status->naziv;
            }
        }
//                    dd($result->message);
//        return $result;
        return json_encode($result);
    }

    /**
     * @param $jmbg
     * @param string $return
     * @return false|\stdClass|string
     */
    public function getLicenceOsobe($jmbg, $return = 'all')
    {
        $result = new \stdClass();
        $licence = Licenca::join('tlicencatip', 'tlicenca.licencatip', '=', 'tlicencatip.id')
            ->where('tlicenca.osoba', $jmbg)
            ->whereIn('tlicenca.status', ['A', 'N'])
            ->get(['tlicenca.*', 'tlicencatip.oznaka', 'tlicencatip.naziv']);
        if ($licence->isEmpty()) {
            $result = false;
        } else {
            switch ($return) {
                case 'all':
                    //VRACA KOLEKCIJU SVIH LICENCI
                    $result = $licence;
                    break;
                case 'array':
                    //VRACA NIZ BROJEVA LICENCI
                    $result = $licence->pluck('id')->toArray();
                    break;
                case 'string':
                    //VRACA BROJEVE LICENCI U STRINGU
                    $result = implode(",", $licence->pluck('id')->toArray());
                    break;
            }
        }
        return $result;
    }

    /**
     * @param $jmbg
     * @return \stdClass
     */
    public function azurirajLicenceOsobe($jmbg)
    {
        $result = new \stdClass();
        $result->osiguranje = new \stdClass();
        $result->licence = new \stdClass();
        $osoba = Osoba::with('osiguranja')
            ->find($jmbg)
//            ->with('licence')
//            ->get()
        ;
        $licence = Licenca::where('osoba', $jmbg)->where('status', '<>', 'D')->get();
        $br = "\n";
//        $br = "<br>";
        $result->osiguranje->ugovarac = '';
        $result->osiguranje->vrsta = '';
        $result->osiguranje->datum_isteka_polise = '';
        foreach ($osoba->osiguranja as $osiguranje) {
            $result->osiguranje->ugovarac .= $osiguranje->firmaUgovarac->naziv ?? $osiguranje->osobaUgovarac->full_name . ', ';
            $result->osiguranje->vrsta .= $osiguranje->osiguranjeTip->naziv . ', ';
            $result->osiguranje->datum_isteka_polise .= $osiguranje->polisa_datum_zavrsetka . ', ';
        };

        $this->o = 0;
        $this->l = 0;
        $this->a = 0;
        $this->n = 0;
        $this->a2n = 0;
        $this->n2a = 0;
        $this->a2nl = '';
        $this->n2al = '';

        $result->osoba = "$osoba->id, $osoba->ime $osoba->prezime";

        foreach ($licence as $licenca) {
            $this->o++;
            $this->proveriLicencu($licenca);
        }

        if ($this->l > 0) {
            $result->licence->azuriranih = "$this->l / $this->o";
            $result->licence->aktiviranih = $this->n2al;
            $result->licence->neaktiviranih = $this->a2nl;
        } else {
            $result->licence->azuriranih = "Nema ažuriranih licenci";
        }

        return $result;
    }

    /**
     * @param Licenca $licenca
     */
    protected function proveriLicencu(Licenca $licenca)
    {
        if ($licenca->status == LICENCA_NEAKTIVIRANA) {
            $now = Carbon::now()->toDateString();
            if (!is_null($licenca->datumobjave)) {
                $datumobjave = Carbon::parse($licenca->datumobjave)->toDateString();
                if ($datumobjave <= $now and strlen($licenca->id) == 9) {
//            POSTAVLJA SE NEAKTIVNI STATUS RADI DALJE PROVERE JER JE PROSLO 5 DANA OD SLANJA RESENJA
                    $licenca->status = LICENCA_NEAKTIVNA;
                    $licenca->preuzeta = 1;
                    $this->h2n++;
                    $this->h2nl .= "$licenca->id, ";
                }
            } else {
                $this->err .= "Za licencu $licenca->id nije setovan datum objave";
            }
        }
        if ($licenca->status == LICENCA_NEAKTIVIRANA) {
//            NEMA PROVERE AKO LICENCA NIJE ODOBRENA (clan nije platio)/ NEAKTIVIRANA (overena ali nije proslo 5 dana od slanja)
            $this->h++;
            $this->hl .= "$licenca->id, ";
        } else {
//            PROVERA LICENCE
            $licenca->status = $this->proveriStatusLicence($licenca);

            if ($licenca->status == LICENCA_AKTIVNA) {
                $this->a++;
            } else {
                $this->n++;
            }

            $original = $licenca->getOriginal('status');
            $licenca->save();
            if ($licenca->wasChanged('status')) {
//            dd($licenca->status . "Original: " . $orig);
                $this->l++;
                if ($original == 'A' and $licenca->status == 'N') {
                    $this->a2n++;
                    $this->a2nl .= "$licenca->id, ";
                } else if ($original == 'N' and $licenca->status == 'A') {
                    $this->n2a++;
                    $this->n2al .= "$licenca->id, ";
                } else if ($original == 'H' and $licenca->status == 'A') {
                    $this->h2a++;
                    $this->h2al .= "$licenca->id, ";
                }
            }

        }
    }

    public function getLicencaTipFromLicencaBroj(Licenca $licenca)
    {
        $licTip4 = strtoupper(substr(trim($licenca->id), 0, 4));
        if ($licenca->datumuo >= '2021-01-21') {
            $gen = 3;
        } else {
            if (strstr($licTip4, '381')) {
                $licTip4 = '381';
                $gen = 1;
            } else {
                $gen = 2;
            }
        }
        $licencaTipO = LicencaTip::find($licTip4);
        if (!empty($licencaTipO)) {
            $licencaTip = $licencaTipO->where('idn', $licencaTipO->idn)->where('generacija', $gen)->first();
//            dd($licencaTip);
            if (!empty($licencaTip)) {
                return $licencaTip;
            }
        }
        if (empty($licencaTipO)) {
            $licTip3 = strtoupper(substr(trim($licenca->id), 0, 3));
            $licencaTip = LicencaTip::where("id", $licTip3)->first();
            if (!is_null($licencaTip)) {
                return $licencaTip;
            } else {
//                return false;
                dd($licenca);
            }
        }
    }

    /**
     * @param Licenca $licenca
     * @return string
     */
    public function proveriStatusLicence(Licenca $licenca)
    {
        $provera = new ProveraLibrary();
        if ($provera->statusLicence($licenca)) {
            return LICENCA_AKTIVNA;
        } else {
            return LICENCA_NEAKTIVNA;
        }
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
    public function measureTime($start = false)
    {
        if ($start === false) {
            //start
            return (int)microtime(true);
        } else {
            //stop
            return (int)microtime(true) - (int)$start;
        }
    }


}
