<?php


namespace App\Libraries;


use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Registry;
use App\Models\RegistryRequestCategory;
use App\Models\Request;

abstract class RegistryLibrary
{

    public static function getRegistry(Request $request, int $document_category_id): Registry
    {
        // table registry_request_category
        $rdu_count = self::rduCount($document_category_id);

        $zg = $request->osoba->zvanjeId->zvanje_grupa_id;

        $request_category_id = $request->request_category_id;

        if ($rdu_count === 0)
            throw new \Exception("Delovodnik ne poseduje vezu sa kategorijom dokumenta $document_category_id.");

        if ($rdu_count === 1) {
            $registry = Registry::where('status_id', AKTIVAN)
                ->whereHas('requestCategories', function ($q) use ($request, $request_category_id, $document_category_id) {
                    $q
                        ->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                        ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
                })
                ->get();
        } else {
            $registry = Registry::where('status_id', AKTIVAN)
                ->whereHas('registryDepartmentUnit', function ($q) use ($zg) {
                    $q
                        ->where('label', "02-$zg");
                })
                ->whereHas('requestCategories', function ($q) use ($request, $request_category_id, $document_category_id) {
                    $q
                        ->where('registry_request_category.request_category_id', $request_category_id) // prijem u clanstvo, mirovanje clanstva, etc...
                        ->where('registry_request_category.document_category_id', $document_category_id); // odluka, resenje, zahtev, etc...
                })
                ->get();
        }


        if ($registry->isEmpty())
            throw new \Exception("Nije pronađen delovodnik.");

        if ($registry->count() > 1)
            throw new \Exception("Pronađeno više od jednog delovodnika.");

        if ($registry->count() == 1)
            return $registry->first();
    }

    public static function getDocument(Request $request, int $document_category_id): Document
    {
        return $request->documents->where('document_category_id', $document_category_id)->get();

    }

    public static function documentExists(Request $request, int $document_category_id): bool
    {
        return $request->documents->where('document_category_id', $document_category_id)->isNotEmpty();
    }

    /**
     * @throws \Exception
     */
    public static function createDocument(Request $request, array $data): void
    {

        // check if document exists
        if (self::documentExists($request, $data['document_category_id']))
            throw new \Exception("Već postoji dokument.");


        // getting registry
        $registry = self::getRegistry($request, $data['document_category_id']);

        $document = new Document();
        $document->document_category_id = $data['document_category_id'];
        $document->registry_id = $registry->id;
        $document->registry_number = $data['br_resenja'];
        $document->registry_date = $data['datum_resenja'];
        $document->status_id = DOCUMENT_REGISTERED;
        $document->user_id = backpack_user()->id;
        $document->metadata = self::getDocumentMetadata($request, $data);
        $document->note = "Automatski kreiran na osnovu podataka iz excela.";
        $document->valid_from = $data['datum_resenja'];
        $document->document_type_id = 4; // AUTOMATSKI GENERISAN VIRTUAL

        $document->documentable()->associate($request);

        if (!$document->save())
            throw new \Exception("Greška 1 prilikom snimanja dokumenta.");

        $registry->counter++;

        if (!$registry->save())
            throw new \Exception("Greška prilikom snimanja delovodnika.");

        $document->barcode = "$request->id#$document->id#$document->registry_date";

        if (!$document->save())
            throw new \Exception("Greška 2 prilikom snimanja dokumenta.");
    }

    private static function getDocumentMetadata(Request $request, array $data): string
    {
        $title = $request->requestCategory->name;

        return json_encode([
            'title' => $title,
            'author' => "Inženjerska komora Srbije",
            'author_id' => '',
            'description' => "Za osobu: {$request->osoba->ime_roditelj_prezime}, lib: {$request->osoba->lib}",
            'category' => $request->requestCategory->name,
            'created_at' => $data['datum_resenja'],
        ], JSON_UNESCAPED_UNICODE);
    }

    private static function getDocumentCategory(int $document_category_id)
    {
        return DocumentCategory::find($document_category_id);
    }

    private static function rduCount(int $document_category_id): int
    {
        return RegistryRequestCategory::where('document_category_id', $document_category_id)->count();

    }

}
