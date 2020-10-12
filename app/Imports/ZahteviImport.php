<?php

namespace App\Imports;

use App\Models\Zahtev;
use App\Models\Licenca;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;


class ZahteviImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure {
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row) {
        $row = array_map('trim', $row);
//        dd($row['licenca']);

        $licenca = Licenca::find($row['licenca']);
        if (!is_null($licenca)) {

//        dd($licenca->osoba);
            return new Zahtev([
//            'osoba_id' => $row['licenca'],
                'osoba_id' => $licenca->osoba,
                'zahtev_tip_id' => $row['tip'],
            ]);
        }
    }

    /*    public function onError(\Throwable $e)
        {
            info("Greska: $e");

            // Handle the exception how you'd like.
        }*/

    public function rules(): array {
        return [
//            'tip' => Rule::notIn([0]),
//            'licenca_broj' => Rule::unique('tzahtev')->where(function ($query) {
//                return $query->whereNotNull('licenca_broj');
//            })
        ];
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures) {
//        dd($failures);
        info("ZahteviImport Greska u validaciji");
        // Handle the failures how you'd like.
    }

    public function onError(\Throwable $e) {
        info("ZahteviImport Greska: $e");

        // Handle the exception how you'd like.
    }

}
