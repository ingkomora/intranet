<?php


namespace App\Libraries;


use App\Models\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

abstract class RegistarLibrary
{
    private static $fields = ['jmbg', 'zahtev', 'br_resenja', 'datum_resenja'];
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
                self::deactivateLicence($filtered_row);

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
     * @param mixed $document_category_id
     */
    private static function setDocumentCategoryId($document_category_id): void
    {
        self::$document_category_id = $document_category_id;
    }


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

    private static function deactivateLicence(array $filtered_row): void
    {

        // getting persons licences
        $licence = self::getLicence($filtered_row['jmbg']);


        if ($licence->isEmpty())
            throw new \Exception("Nema evidentiranih licenci u bazi.");

        // updating licenca model
        foreach ($licence as $licenca) {

            $licenca->status = 'D';
            $licenca->datumukidanja = $filtered_row['datum_resenja'];
            $licenca->razlogukidanja = "Licenca deaktivirana na osnovu rešenja broj {$filtered_row['br_resenja']} od " . Carbon::parse($filtered_row['datum_resenja'])->format('d.m.Y.') . " usled smrti.";
            if (!$licenca->save())
                throw new \Exception("Greška prilikom ažuriranja licence u bazi.");

        }

    }


}
