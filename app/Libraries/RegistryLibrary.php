<?php


namespace App\Libraries;


use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Registry;
use App\Models\RegistryRequestCategory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RegistryLibrary
 * @package App\Libraries
 */
abstract class RegistryLibrary
{

    /**
     * @var
     */
    private static $label;


    /*
    |--------------------------------------------------------------------------
    | PUBLIC METHODS
    |--------------------------------------------------------------------------
    */
    /**
     * @param Model $model
     * @param int $document_category_id
     * @param int|null $year
     * @return Registry
     * @throws \Exception
     */
    public static function getRegistry(Model $model, int $document_category_id, int $year = null): Registry
    {

        // table registry_request_category
        $rdu_count = self::rduCount($document_category_id, $year);


        if ($rdu_count === 0)
            throw new \Exception("Delovodnik ne poseduje vezu sa kategorijom dokumenta $document_category_id.");

        $registry = self::findRegistry($model, $document_category_id, $year);


        if ($registry->isEmpty())
            throw new \Exception("Nije pronađen delovodnik.");

        if ($registry->count() > 1)
            throw new \Exception("Pronađeno više od jednog delovodnika.");

        if ($registry->count() == 1)
            return $registry->first();
    }

    /**
     * @param Model $model
     * @param int $document_category_id
     * @return Document
     */
    public static function getDocument(Model $model, int $document_category_id): Document
    {
        return $model->documents->where('document_category_id', $document_category_id)->get();

    }

    /**
     * @param Model $model
     * @param int $document_category_id
     * @return bool
     */
    public static function documentExists(Model $model, int $document_category_id): bool
    {
        return $model->documents->where('document_category_id', $document_category_id)->where('status_id', '<>', DOCUMENT_CANCELED)->isNotEmpty();
    }

    /**
     * @param Model $model
     * @param int $document_category_id
     * @param string $datum_dokumenta
     * @param string $broj_dokumenta
     * @param string $registry_type
     * @throws \Exception
     */
    public static function createDocument(Model $model, int $document_category_id, string $datum_dokumenta, string $broj_dokumenta, string $registry_type): void
    {
        // setting class label field
        self::setLabel($registry_type, $model);

        // check if document exists
        if (self::documentExists($model, $document_category_id))
            throw new \Exception("Već postoji dokument.");

        $registry_year = Carbon::parse($datum_dokumenta)->format('Y');


        // getting registry
        $registry = self::getRegistry($model, $document_category_id, $registry_year);

        $document = new Document();
        $document->document_category_id = $document_category_id;
        $document->registry_id = $registry->id;
        $document->registry_number = $broj_dokumenta; // br_resenja
        $document->registry_date = $datum_dokumenta; // datum_resenja
        $document->status_id = DOCUMENT_REGISTERED;
        $document->user_id = backpack_user()->id;
        $document->metadata = self::getDocumentMetadata($model, $datum_dokumenta);
        $document->note = "Automatski kreiran na osnovu podataka iz excela.";
        $document->valid_from = $datum_dokumenta; // datum_resenja
        $document->document_type_id = 4; // AUTOMATSKI GENERISAN VIRTUAL

        $document->documentable()->associate($model);

        if (!$document->save())
            throw new \Exception("Greška 1 prilikom snimanja dokumenta.");

        $registry->counter++;

        if (!$registry->save())
            throw new \Exception("Greška prilikom snimanja delovodnika.");

        // creating barcode after inserting document record
        $document->barcode = "$model->id#$document->id#$document->registry_number#$document->registry_date";

        if (!$document->save())
            throw new \Exception("Greška 2 prilikom snimanja dokumenta.");
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */
    /**
     * @param Model $model
     * @param int $document_category_id
     * @param int|null $year
     * @return Collection|null
     */
    private static function findRegistry(Model $model, int $document_category_id, int $year = null): ?Collection
    {

        $request_category_id = $model->request_category_id;
        $zvanje_grupa_id = OsobaLibrary::getSekcija($model)->id;


        if (is_null($zvanje_grupa_id)) {
            // if zvanje_grupa_id is not provided

            if (is_null($year)) {
                // find active registry

                $registry = Registry::where('status_id', AKTIVAN)
                    ->whereHas('requestCategories', function ($q) use ($request_category_id, $document_category_id) {
                        $q
                            ->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                            ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
                    })
                    ->get();

            } else {
                // find registry by year

                $registry = Registry::where('year', $year)
                    ->whereHas('requestCategories', function ($q) use ($request_category_id, $document_category_id) {
                        $q
                            ->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                            ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
                    })
                    ->get();

            }

        } else {
            // if zvanje_grupa_id is provided

            if (is_null($year)) {
                // find active registry

                $registry = Registry::where('status_id', AKTIVAN)
                    ->whereHas('registryDepartmentUnit', function ($q) use ($zvanje_grupa_id) {
                        $q
                            ->where('label', self::$label);
                    })
                    ->whereHas('requestCategories', function ($q) use ($request_category_id, $document_category_id) {
                        $q
                            ->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                            ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
                    })
                    ->get();
            } else {
                // find registry by year

                $registry = Registry::where('year', $year)
                    ->whereHas('registryDepartmentUnit', function ($q) use ($zvanje_grupa_id) {
                        $q
                            ->where('label', self::$label);
                    })
                    ->whereHas('requestCategories', function ($q) use ($request_category_id, $document_category_id) {
                        $q
                            ->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                            ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
                    })
                    ->get();
            }

        }


        return $registry;
    }


    /**
     * @param Model $model
     * @param string $datum_dokumenta
     * @return string
     */
    private static function getDocumentMetadata(Model $model, string $datum_dokumenta): string
    {
        $osoba = OsobaLibrary::getOsobaFromRelatedModel($model);
        $title = $model->requestCategory->name;

        return json_encode([
            'title' => $title,
            'author' => "Inženjerska komora Srbije",
            'author_id' => '',
            'description' => "Za osobu: {$osoba->ime_roditelj_prezime}, lib: {$osoba->lib}",
            'category' => $title,
            'created_at' => $datum_dokumenta,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Method returns whole registry number for next document
     * @throws \Exception
     */
    public static function getRegistryNumber(Model $model, int $document_category_id): string
    {
        $registry = RegistryLibrary::getRegistry($model, $document_category_id);

        return "{$registry->registryDepartmentUnit->label}-$registry->base_number/$registry->year-" . $registry->counter++;


    }


    /**
     * @param int $document_category_id
     * @return DocumentCategory
     */
    private static function getDocumentCategory(int $document_category_id): DocumentCategory
    {
        return DocumentCategory::find($document_category_id);
    }

    /**
     * @param int $document_category_id
     * @param int $registry_year
     * @return int
     */
    private static function rduCount(int $document_category_id, int $registry_year): int
    {
        if (is_null($registry_year)) {
            $rrc = RegistryRequestCategory::where('document_category_id', $document_category_id)
                ->whereHas('registry', function ($q) {
                    $q->where('status_id', AKTIVAN);
                })
                ->count();
        } else {
            $rrc = RegistryRequestCategory::where('document_category_id', $document_category_id)
                ->whereHas('registry', function ($q) use ($registry_year) {
                    $q->where('year', $registry_year);
                })
                ->count();
        }

        return $rrc;

    }

    /**
     * @param string $registry_type
     * @param Model $model
     * @throws \Exception
     */
    private static function setLabel(string $registry_type, Model $model): void
    {

        if (!$registry_type)
            throw new \Exception("Greška. Nedostaje parametar za određivanje oznake organizacione jedinice.");


        $zg = OsobaLibrary::getSekcija($model)->id;

        switch ($registry_type) {

            case 'registar':
                self::$label = '01';
                break;

            case 'sekcija':
                self::$label = '02-' . $zg;
                break;

            case 'licence':
                if (!isset($model->vrsta_posla_id))
                    throw new \Exception("Greška. Model nema polje vrsta_posla_id.");

                if (!isset($model->reg_oblast_id))
                    throw new \Exception("Greška. Model nema polje reg_oblast_id.");

                self::$label = $model->vrsta_posla_id == 5 ? '02-E' : '02-' . $model->reg_oblast_id;
                break;

            case 'si':
                if (!isset($model->vrsta_posla_id))
                    throw new \Exception("Greška. Model nema polje vrsta_posla_id.");

                if (!isset($model->reg_oblast_id))
                    throw new \Exception("Greška. Model nema polje reg_oblast_id.");

                self::$label = $model->vrsta_posla_id == 5 ? '09-E' : '09-' . $model->reg_oblast_id;
                break;

            default:
                throw new \Exception("Greška prilikom definisanja oznake organizacione jedinice.");
        }

    }

}
