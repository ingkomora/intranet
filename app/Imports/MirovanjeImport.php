<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class MirovanjeImport implements ToModel, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;


    /**
     * @param array $rows
     * @return array
     */
    public function ToModel(array $rows): array
    {
        // TODO: Implement toModel() method.
    }

    public function collection(Collection $collection)
    {
        // TODO: Implement collection() method.
    }

    public function model(array $row)
    {
        // TODO: Implement model() method.
    }

    public function rules(): array
    {
        // TODO: Implement rules() method.
    }
}
