<?php


namespace App\Libraries;


use App\Models\Osoba;
use App\Models\Sekcija;
use Illuminate\Database\Eloquent\Model;

abstract class OsobaLibrary
{


    /**
     * @param string $jmbg
     * @return Osoba|null
     */
    public static function get(string $jmbg): ?Osoba
    {
        return Osoba::find($jmbg);
    }


    /**
     * @param string $jmbg
     * @return bool
     */
    public static function exists(string $jmbg): bool
    {
        return !is_null(self::get($jmbg));
    }

    /**
     * @param string $jmbg
     * @return bool
     * Method returns bool for licence existence.
     * If osoba doesnt exist it returns FALSE.
     */
    public static function hasLicence(string $jmbg): bool
    {
        $osoba = self::get($jmbg);

        if ($osoba)
            // if osoba exists return true|false from model relationship
            return $osoba->licence->whereIn('status', ['A', 'N'])->isNotEmpty();
        else
            // if osoba doesn't exists
            return FALSE;
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
    public static function getSekcija(Model $model): Sekcija
    {
        if (isset($model->osoba_id))
            $zg = $model->osoba->zvanjeId->sekcija;
        else
            $zg = $model->osobaId->zvanjeId->sekcija;

        return $zg;
    }

}
