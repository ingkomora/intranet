<?php


namespace App\Libraries;


class Helper {
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