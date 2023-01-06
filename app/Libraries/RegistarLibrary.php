<?php


namespace App\Libraries;


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
     * @throws \Exception
     */
    public static function brisanjeUsledSmrti(array $data): array
    {
        self::setDocumentCategoryId(11); // Resenje o brisanju iz Registra usled smrti


        foreach ($data as $row) {

            $filtered_row = self::filterData($row);
            $filtered_row['document_category_id'] = self::$document_category_id;


            try {
                DB::beginTransaction();

                // check if osoba exist
                if (!OsobaLibrary::osobaExists($filtered_row['jmbg']))
                    throw new \Exception("Osoba sa jmbg {$filtered_row['jmbg']} nije pronađena u bazi.");


                // updating licence model
                self::deactivateLicence($filtered_row['osoba_id'], $filtered_row['datum_dokumenta'], $filtered_row['broj_dokumenta']);

                // getting request model
                $request = self::getRequest($filtered_row['zahtev']);
                $request->status_id = REQUEST_FINISHED;

                // TODO: Upisati inzenjere u registar tabelu
                // associate registar with request
                // $request->requestable()->associate($registar);

                if (!$request->save())
                    throw new \Exception("Greška prilikom ažuriranja zahteva.");

                // create document resenje o brisanju podataka iz Registra
                RegistryLibrary::createDocument($request, $filtered_row);


                $result['success'][$request->id] = "Uspešno završena akcija.";
                DB::commit();

            } catch (\Exception $e) {

                $result['error'][$filtered_row['zahtev']] = $e->getMessage();
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
        $request = \App\Models\Request::where('id', $request_id)->where('status_id', REQUEST_IN_PROGRESS)->get();


        if ($request->isEmpty())
            throw new \Exception("Zahtev nije pronađen.");

        return $request->first();
    }

    private static function getLicence(string $jmbg): ?Collection
    {
        return \App\Models\Licenca::where('osoba', $jmbg)->where('status', '<>', 'D')->get();
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
        foreach ($data as $key => $field) {
            if (in_array($key, self::$fields)) {

                if ($key == 'datum_resenja')
                    $field = Carbon::parse($field)->format('Y-m-d');

                $result[$key] = $field;

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

        // updating licenca model
        foreach ($licence as $licenca) {

            $licenca->status = 'D';
            $licenca->datumukidanja = $datum_dokumenta;
            $licenca->razlogukidanja = "Licenca deaktivirana na osnovu rešenja broj $broj_dokumenta od $datum_dokumenta_string godine $uzrok.";
            if (!$licenca->save())
                throw new \Exception("Greška prilikom ažuriranja licence u bazi.");

        }

    }


}
