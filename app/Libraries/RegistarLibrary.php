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
    private static $document_category_id;


    /**
     * This method is an action method
     * @throws \Exception
     */
    public static function brisanjeUsledSmrti(array $data): array
    {
        self::$document_category_id = 11; // Rešenja o brisanju podataka upisanih u Registar (usled smrti)

        // Set class property fields to suit excel column names
        self::setFields([
            'jmbg' => 'osoba_id',
            'zahtev' => 'request_id',
            'br_resenja' => 'broj_dokumenta',
            'datum_resenja' => 'datum_dokumenta',
        ]);

        self::setDocumentCategoryId(self::$document_category_id);

        // creating necessary data array for program execution from excel data
        $data = self::adjustExcelData($data, self::$fields);


        foreach ($data as $row) {

            $filtered_row = self::filterData($row);
            $filtered_row['document_category_id'] = self::$document_category_id;


            try {
                DB::beginTransaction();

                // check if osoba exist
                if (!OsobaLibrary::osobaExists($filtered_row['osoba_id']))
                    throw new \Exception("Osoba sa jmbg {$filtered_row['osoba_id']} nije pronađena u bazi.");


                // updating licence model
                self::deactivateLicence($filtered_row['osoba_id'], $filtered_row['datum_dokumenta'], $filtered_row['broj_dokumenta']);

                // getting request model
                $request = self::getRequest($filtered_row['request_id']);
                $request->status_id = REQUEST_FINISHED;

                // TODO: Upisati inzenjere u registar tabelu
                // associate registar with request
                // $request->requestable()->associate($registar);

                if (!$request->save())
                    throw new \Exception("Greška prilikom ažuriranja zahteva.");
                // create document resenje o brisanju podataka iz Registra
                RegistryLibrary::createDocument($request, $filtered_row['document_category_id'], $filtered_row['datum_dokumenta'], $filtered_row['broj_dokumenta']);


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
     * @throws \Exception
     */
    private static function getRequest(int $request_id): ?Request
    {
        $request = Request::where('id', $request_id)->where('status_id', REQUEST_IN_PROGRESS)->get();


        if ($request->isEmpty())
            throw new \Exception("Zahtev nije pronađen.");

        return $request->first();
    }

    private static function getLicence(string $jmbg): ?Collection
    {
        return Licenca::where('osoba', $jmbg)->where('status', '<>', 'D')->get();
    }

    /**
     * @param mixed $document_category_id
     */
    private static function setDocumentCategoryId(int $document_category_id): void
    {
        self::$document_category_id = $document_category_id;
    }

    /**
     * Set class property fields to suit excel column names
     * @param array $fields
     */
    private static function setFields(array $fields): void
    {
        self::$fields = $fields;
    }

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
     * @throws \Exception
     */
    private static function deactivateLicence(string $jmbg, string $datum_dokumenta, string $broj_dokumenta, string $uzrok = 'usled smrti'): void
    {

        // getting persons licences
        $licence = self::getLicence($jmbg);


        if ($licence->isEmpty())
            throw new \Exception("Nema evidentiranih licenci u bazi.");

        $datum_dokumenta_string = Carbon::parse($datum_dokumenta)->format('d.m.Y.');

        // updating licenca model
        foreach ($licence as $licenca) {

            $licenca->status = 'D';
            $licenca->datumukidanja = $datum_dokumenta;
            if (empty($licenca->razlogukidanja)) {
                $licenca->razlogukidanja = "Licenca deaktivirana na osnovu Rešenja o brisanju podataka upisanih u Registar broj $broj_dokumenta od $datum_dokumenta_string godine $uzrok.";
            } else {
                $licenca->razlogukidanja = "$licenca->razlogukidanja##Licenca deaktivirana na osnovu Rešenja o brisanju podataka upisanih u Registar broj $broj_dokumenta od $datum_dokumenta_string godine $uzrok.";
            }
            if (!$licenca->save())
                throw new \Exception("Greška prilikom ažuriranja licence u bazi.");

        }

    }

    /**
     * Method creates array of data with corresponding keys suitable for program execution
     * @param array $data
     * @param array $mappedFields
     * @return array
     */
    private static function adjustExcelData(array $data, array $mappedFields): array
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
