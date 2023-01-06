<?php


namespace App\Libraries;


use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Registry;
use App\Models\RegistryRequestCategory;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

abstract class RegistryLibrary
{

    public static function getRegistry(Request $request, int $document_category_id, int $year = null): Registry
    {
        // table registry_request_category
        $rdu_count = self::rduCount($document_category_id, $year);

        $zg = $request->osoba->zvanjeId->zvanje_grupa_id;

        if ($rdu_count === 0)
            throw new \Exception("Delovodnik ne poseduje vezu sa kategorijom dokumenta $document_category_id.");

        if ($rdu_count === 1) {
            $registry = self::findRegistry($request, $document_category_id, $year);
        } else {
            $registry = self::findRegistry($request, $document_category_id, $year, $zg);
        }


        if ($registry->isEmpty())
            throw new \Exception("Nije pronađen delovodnik.");

        if ($registry->count() > 1)
            throw new \Exception("Pronađeno više od jednog delovodnika.");

        if ($registry->count() == 1)
            return $registry->first();
    }

    private static function findRegistry(Request $request, int $document_category_id, int $year = null, int $zvanje_grupa_id = null): ?Collection
    {
        $request_category_id = $request->request_category_id;

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
                            ->where('label', "02-$zvanje_grupa_id");
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
                            ->where('label', "02-$zvanje_grupa_id");
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

    public static function getDocument(Request $request, int $document_category_id): Document
    {
        return $request->documents->where('document_category_id', $document_category_id)->get();

    }

    public static function documentExists(Request $request, int $document_category_id): bool
    {
        return $request->documents->where('document_category_id', $document_category_id)->where('status_id', '<>', DOCUMENT_CANCELED)->isNotEmpty();
    }

    /**
     * @throws \Exception
     */
    public static function createDocument(Request $request, int $document_category_id, string $datum_dokumenta, string $broj_dokumenta): void
    {

        // check if document exists
        if (self::documentExists($request, $document_category_id))
            throw new \Exception("Već postoji dokument.");

        $registry_year = Carbon::parse($datum_dokumenta)->format('Y');


        // getting registry
        $registry = self::getRegistry($request, $document_category_id, $registry_year);

        $document = new Document();
        $document->document_category_id = $document_category_id;
        $document->registry_id = $registry->id;
        $document->registry_number = $broj_dokumenta; // br_resenja
        $document->registry_date = $datum_dokumenta; // datum_resenja
        $document->status_id = DOCUMENT_REGISTERED;
        $document->user_id = backpack_user()->id;
        $document->metadata = self::getDocumentMetadata($request, $datum_dokumenta);
        $document->note = "Automatski kreiran na osnovu podataka iz excela.";
        $document->valid_from = $datum_dokumenta; // datum_resenja
        $document->document_type_id = 4; // AUTOMATSKI GENERISAN VIRTUAL

        $document->documentable()->associate($request);

        if (!$document->save())
            throw new \Exception("Greška 1 prilikom snimanja dokumenta.");

        $registry->counter++;

        if (!$registry->save())
            throw new \Exception("Greška prilikom snimanja delovodnika.");

        // creating barcode after inserting document record
        $document->barcode = "$request->id#$document->id#$document->registry_number#$document->registry_date";

        if (!$document->save())
            throw new \Exception("Greška 2 prilikom snimanja dokumenta.");
    }

    private static function getDocumentMetadata(Request $request, string $datum_dokumenta): string
    {
        $title = $request->requestCategory->name;

        return json_encode([
            'title' => $title,
            'author' => "Inženjerska komora Srbije",
            'author_id' => '',
            'description' => "Za osobu: {$request->osoba->ime_roditelj_prezime}, lib: {$request->osoba->lib}",
            'category' => $request->requestCategory->name,
            'created_at' => $datum_dokumenta,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Method returns whole registry number for next document
     * @throws \Exception
     */
    public static function getRegistryNumber(Request $request, int $document_category_id): string
    {
        $registry = RegistryLibrary::getRegistry($request, $document_category_id);

        return "{$registry->registryDepartmentUnit->label}-$registry->base_number/$registry->year-" . $registry->counter++;


    }

    private static function getDocumentCategory(int $document_category_id): DocumentCategory
    {
        return DocumentCategory::find($document_category_id);
    }

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

}
