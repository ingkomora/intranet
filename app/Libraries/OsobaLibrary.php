<?php


namespace App\Libraries;


use App\Models\Osoba;

abstract class OsobaLibrary
{

    public static function osobaExists($jmbg): bool
    {
        $osoba = Osoba::find($jmbg);
        if (is_null($osoba))
            return FALSE;

        return TRUE;

    }

    public static function getOsoba($jmbg)
    {
        $osoba = Osoba::find($jmbg);
        if (!is_null($osoba))
            return $osoba;

        return null;
    }

}
