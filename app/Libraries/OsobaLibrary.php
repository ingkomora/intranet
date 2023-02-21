<?php


namespace App\Libraries;


use App\Models\Osoba;
use App\Models\Sekcija;
use Illuminate\Database\Eloquent\Model;

abstract class OsobaLibrary
{

    public static function osobaExists(string $jmbg): bool
    {
        $osoba = Osoba::find($jmbg);
        if (is_null($osoba))
            return FALSE;

        return TRUE;

    }

    public static function getOsoba(string $jmbg)
    {
        $osoba = Osoba::find($jmbg);
        if (!is_null($osoba))
            return $osoba;

        return null;
    }


    /**
     * @param Model $model
     * @return Osoba
     */
    public static function getOsobaFromRelatedModel(Model $model): Osoba
    {
        return isset($model->osoba_id) ? $model->osoba : $model->osobaId;
    }

    /**
     * @param Model $model
     * @return Sekcija
     */
    public static function getZvanjeGrupa(Model $model): Sekcija
    {
        if (isset($model->osoba_id))
            $zg = $model->osoba->zvanjeId->sekcija;
        else
            $zg = $model->osobaId->zvanjeId->sekcija;

        return $zg;
    }

}
