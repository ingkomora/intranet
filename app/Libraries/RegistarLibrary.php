<?php


namespace App\Libraries;


use App\Models\Licenca;
use App\Models\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class RegistarLibrary
 * @package App\Libraries
 *
 * It is mandatory to set class properties in every action method.
 */
abstract class RegistarLibrary
{
    private static $fields = [];
    private static $registry_type = 'registar';


    /*
    |--------------------------------------------------------------------------
    | ACTION METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * @param array $data
     * @return array
     */
    public static function brisanjeUsledSmrtiFromFile(array $data): array
    {
        $document_category_id = 11; // Rešenja o brisanju podataka upisanih u Registar (usled smrti)

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
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public static function brisanjeLicno(array $data): array
    {

        // request categories (request_categories table)
        $request_category_due_death = 11;   // Brisanje podataka upisanih u Registar (usled smrti)
        $request_category_licno = 14;       // Brisanje podataka upisanih u Registar (na lični zahtev)

        // document categories (document_categories table)
        $document_category_due_death = 11;  // Rešenja o brisanju podataka upisanih u Registar (usled smrti)
        $document_category_licno = 42;      // Rešenja o brisanju podataka upisanih u Registar (na lični zahtev)

        // Set class property fields to suit excel column names
        self::setFields([
            'id' => 'request_id',
            'resenje_broj' => 'broj_dokumenta',
            'resenje_datum' => 'datum_dokumenta',
        ]);

        // getting request model
        $request = RequestLibrary::get($data['id'], [REQUEST_IN_PROGRESS]);

        if ($request->request_category_id == $request_category_due_death)
            $document_category_id = $document_category_due_death;
        if ($request->request_category_id == $request_category_licno)
            $document_category_id = $document_category_licno;

        // preparing data format for mapExcelFields method
        $validated[] = $data;


        // creating necessary data array for program execution from excel data
        $filtered_row = self::mapExcelFields($validated, self::$fields);
        $filtered_row[0]['document_category_id'] = $document_category_id;


        try {
            DB::beginTransaction();

            // performing all necessary logic for this action
            self::performAction($request, $filtered_row[0]);


            $result['success'] = "Uspešno završeno brisanje iz Registra.";
            DB::commit();

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            DB::rollBack();
        }

        return $result;
    }



    /*
    |--------------------------------------------------------------------------
    | SETTERS
    |--------------------------------------------------------------------------
    */

    /**
     * Set class property fields to suit excel column names
     * @param array $fields
     */
    private static function setFields(array $fields): void
    {
        self::$fields = $fields;
    }



    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */


    /**
     * @param Request|null $request
     * @param array $data
     * @throws \Exception
     */
    private static function performAction(?Request $request, array $data): void
    {

        // check if osoba exist
        if (!OsobaLibrary::exists($request->osoba_id))
            throw new \Exception("Osoba sa jmbg {$request->osoba_id} nije pronađena u bazi.");

        // check if osoba has licence in Registar
        OsobaLibrary::hasLicence($request->osoba_id);


        // updating licence model
        LicenceLibrary::deactivate($request, $data['datum_dokumenta'], $data['broj_dokumenta']);

        // after successful deactivation updating request status
        $request->status_id = REQUEST_FINISHED;

        if (!$request->save())
            throw new \Exception("Greška prilikom ažuriranja zahteva.");


        // create document resenje o brisanju podataka iz Registra
        RegistryLibrary::createDocument($request, $data['document_category_id'], $data['datum_dokumenta'], $data['broj_dokumenta'], self::$registry_type);

    }


    /**
     * @param array $data
     * @return array
     */
    private static function filterData(array $data): array
    {
        foreach ($data as $field => $value) {

            if (in_array($field, self::$fields)) {

                if (strstr($field, 'datum')) {
                    $value = Carbon::parse($value)->format('Y-m-d');
                }

                $result[$field] = $value;

            }
        }

        return $result;
    }


    /**
     * Method creates array of data with corresponding keys suitable for program execution
     * @param array $data
     * @param array $mappedFields
     * @return array
     */
    private static function mapExcelFields(array $data, array $mappedFields): array
    {

        return array_map(function ($field) use ($mappedFields) {
            foreach ($field as $key => $value) {
                if (key_exists($key, $mappedFields)) {
                    $new_key = $mappedFields[$key];
                    $row[$new_key] = $value;
                }
            }
            return $row;
        }, $data);
    }


}
