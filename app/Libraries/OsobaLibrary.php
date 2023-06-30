<?php


namespace App\Libraries;


use App\Models\Osoba;
use App\Models\Sekcija;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class OsobaLibrary
{

    private static $fields = [];
//    private static $registry_type = 'registar';

    /*
    |--------------------------------------------------------------------------
    | ACTION METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * @param array $data
     * @return array
     */
    public static function updateEducationData(array $data): array
    {
        dd($data);

        // Set class property fields to suit excel column names
        self::setFields([
            'jmbg' => 'osoba_id',
            'zahtev' => 'request_id',
            'br_resenja' => 'broj_dokumenta',
            'datum_resenja' => 'datum_dokumenta',
        ]);


        // creating necessary data array for program execution from excel data
        $data = self::mapExcelFields($data, self::$fields);


        foreach ($data as $row) {

            $filtered_row = self::filterData($row);
            $filtered_row['document_category_id'] = $document_category_id;


            try {
                DB::beginTransaction();

                // getting request model
                $request = RequestLibrary::get($data['request_id'], [REQUEST_IN_PROGRESS]);


                // performing all necessary logic for this action
                self::performAction($request, $filtered_row);


                $result['success'][$request->id] = "Uspešno završeno brisanje iz Registra usled smrti.";
                DB::commit();

            } catch (\Exception $e) {

                $result['error'][$filtered_row['request_id']] = $e->getMessage();
                DB::rollBack();
            }
        }

        return $result;

    }


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
