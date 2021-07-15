<?php


namespace App\Libraries;


use App\Models\Licenca;
use App\Models\PrijavaClanstvo;
use Illuminate\Http\Request;

class Helper {
    public function iso88592_to_cirUTF($text) {
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

    public function in_array_recursive($needle, array $haystack, string $attribute) {
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

    public function getPrijavaClan($id) {
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
                    $result->message = "Nema licence, ";

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

    public function getLicenceOsobe($jmbg, $return = 'all') {
        $result = new \stdClass();
        $licence = Licenca::join('tlicencatip', 'tlicenca.licencatip', '=', 'tlicencatip.id')
            ->where('tlicenca.osoba', $jmbg)
            ->whereIn('tlicenca.status', ['A', 'N'])
            ->get(['tlicenca.*', 'tlicencatip.oznaka', 'tlicencatip.naziv']);
        dd($licence);
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

}
