<?php


namespace App\Libraries;


use App\Models\SiPrijava;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


/**
 * --------------------------------------------------------------------
 * Logic that is not implemented
 * --------------------------------------------------------------------
 * Finding (or creating) and updating ZahtevLicenca that is or has to be
 * associated with SiPrijava.
 *
 * Problem with workflow:
 * When employee performs licence insertion using `unesinovelicence`,
 * operation will try to find suitable ZahtevLicenca. However, it won't
 * be able to find ZahtevLicenca with status 53 (zavrsen) or 54 (otkazan),
 * so it will create new one.
 *
 * That's the reason to not implement update of associate ZahtevLicenca...
 */

/**
 * Class SiprijavaLibrary
 * @package App\Libraries
 */
abstract class SiprijavaLibrary
{

    private static $fields = [];
    private static $document_category_id;
    private static $registry_type = 'si';


    /*
    |--------------------------------------------------------------------------
    | ACTION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * This method is an action method
     * @throws \Exception
     */
    public function azurirajRezultatIspita(array $data): array
    {

        self::$document_category_id = 21; // Potvrda o položenom stručnom ispitu

        // Set class property fields to suit excel column names
        self::setFields([
            'jmbg' => 'osoba_id',
            'prijava' => 'si_prijava_id',
            'br_potvrde' => 'broj_dokumenta',
            'datum_potvrde' => 'datum_dokumenta',
            'uspeh_id' => 'uspeh_id',
            'rok' => 'rok',
            'datum_polaganja' => 'datum_polaganja',
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


                // get prijava model
                $prijava = self::getPrijava($filtered_row['si_prijava_id']);


                // updating SiPrijava model
                self::update($prijava, $filtered_row);


                // create document
                RegistryLibrary::createDocument($prijava, $filtered_row['document_category_id'], $filtered_row['datum_dokumenta'], $filtered_row['broj_dokumenta'], self::$registry_type);


                $result['success'][$prijava->id] = "Uspešno završeno ažuriranje prijave.";
                DB::commit();

            } catch (\Exception $e) {

                $result['error'][$filtered_row['si_prijava_id']] = $e->getMessage();
                DB::rollBack();
            }
        }

        return $result;
    }



    /*
    |--------------------------------------------------------------------------
    | GETTERS
    |--------------------------------------------------------------------------
    */
    /**
     * @param int $id
     * @return Collection|null
     */
    private static function getPrijava(int $id): ?SiPrijava
    {
        $prijava = SiPrijava::find($id);
        if (!$prijava)
            throw new \Exception("Nije pronađena prijava.");

        return $prijava;
    }

    /**
     * @param string $jmbg
     * @return Collection|null
     */
    private static function getPrijave(string $jmbg): ?Collection
    {
        $prijave = SiPrijava::where('osoba_id', $jmbg)->where('status_prijave', '<>', REQUEST_CANCELED)->get();

        if (!$prijave)
            throw new \Exception("Nije pronađena ni jedna prijava.");

        return $prijave;
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

    /**
     * @param mixed $document_category_id
     */
    private static function setDocumentCategoryId(int $document_category_id): void
    {
        self::$document_category_id = $document_category_id;
    }


    /*
    |--------------------------------------------------------------------------
    | LOCAL METHODS
    |--------------------------------------------------------------------------
    */
    /**
     * Updating SiPrijava model
     * @param SiPrijava $prijava
     * @param array $data
     * @throws \Exception
     */
    private static function update(SiPrijava $prijava, array $data): void
    {

        // updating prijava model
        $prijava->status_prijave = REQUEST_FINISHED;
        $prijava->uspeh_id = $data['uspeh_id'];
        $prijava->rok = $data['rok'];
        $prijava->datum_polaganja = $data['datum_polaganja'];

        if ($prijava->isDirty())
            $prijava->updated_at = now();


        if (!$prijava->save())
            throw new \Exception("Greška prilikom ažuriranja prijave.");

    }



    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

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
}
