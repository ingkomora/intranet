<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class ExcelImport implements WithMultipleSheets
{
    use Importable
//        , WithConditionalSheets
        ;

    public $sheets;

    public function rules(): array {
        return [

        ];
    }

    public function sheets(): array
    {
        $this->sheets = [
//            new OsobeImport(),
            new ZahteviLicenceImport(),
//            new FirmeImport(),
        ];
        return $this->sheets;
    }

/*    public function conditionalSheets(): array
    {
        return [
            'SF' => new ZahteviImport(),
//            'OSOBE' => new OsobeImport(),
//            'FIRME' => new FirmeImport(),
//            'OSIGURANJA' => new OsiguranjaImport(),
//            'OSIGURANJE_OSOBA' => new OsiguranjaImport(),
        ];
    }*/
}
